const express = require('express');
const router = express.Router();
const db = require('../database/db');
const { classifyPayload } = require('../services/classifier');
const { analyzeUrl } = require('../services/analyzer');
const { checkDomain } = require('../services/domainChecker');
const { checkSSL } = require('../services/sslChecker');
const { checkThreatIntel } = require('../services/threatIntel');
const { checkRedirects } = require('../services/redirectChecker');
const { v4: uuidv4 } = require('uuid');
const { calculateRisk } = require('../services/riskEngine');

// Endpoint: Single QR Scan Analysis
router.post('/scan', async (req, res) => {
    try {
        const { payload, qr_image = null, input_type = 'live', forced_type = null } = req.body;
        if (!payload) return res.status(400).json({ error: 'Payload is required' });

        const result = await processSinglePayload(payload, qr_image, input_type, null, forced_type);
        res.json(result);
    } catch (error) {
        console.error('Scan API Error:', error);
        res.status(500).json({ error: 'Internal server error' });
    }
});

// Helper function to process a single payload
async function processSinglePayload(payload, qr_image = null, input_type = 'manual', batch_id = null, forced_type = null) {
    const startTime = Date.now();
    
    let payloadClass;
    if (forced_type) {
        if (forced_type === 'upi_id_only') {
            payloadClass = { type: forced_type, data: { vpa: payload.trim() } };
        } else if (forced_type === 'url') {
            payloadClass = { type: forced_type, data: { url: payload.trim() } };
        } else {
            payloadClass = { type: forced_type, data: payload };
        }
    } else {
        payloadClass = classifyPayload(payload);
    }
    
    let analysisResult = null;
    let domainCheck = null;
    let sslCheck = null;
    let threatIntelResult = null;
    let redirectCheck = null;
    let finalUrl = payload;

    if (payloadClass.type === 'url') {
        const initialUrl = payloadClass.data.url;
        
        // Engine 1 & 2: Initial URL
        analysisResult = await analyzeUrl(initialUrl);
        
        if (!analysisResult.indicators.some(i => i.id === 'UNSAFE_DESTINATION')) {
            // Engine 6: Redirect Check
            redirectCheck = await checkRedirects(initialUrl);
            
            // Final URL Re-analysis
            finalUrl = redirectCheck.finalUrl || initialUrl;
            
            // If redirected, re-run lexical on final URL and merge indicators
            if (redirectCheck.status !== 'NO_REDIRECT' && finalUrl !== initialUrl) {
                const finalAnalysis = await analyzeUrl(finalUrl);
                analysisResult.indicators.push(...finalAnalysis.indicators);
                analysisResult.domain = finalAnalysis.domain; // Use final domain for next checks
            }

            // Engine 3: Domain Check (on final domain)
            domainCheck = await checkDomain(analysisResult.domain);

            // Engine 4: SSL Check (on final url)
            sslCheck = await checkSSL(finalUrl);

            // Engine 5: Threat Intel (on final domain/url)
            threatIntelResult = await checkThreatIntel(analysisResult.domain, finalUrl);
        }
    }

    // Engine 7 & 8 & 9: Risk Aggregation, Trust Score, Verdict, Confidence
    const scoring = calculateRisk(payloadClass, analysisResult, domainCheck, sslCheck, threatIntelResult, redirectCheck);

    const detailsJson = JSON.stringify({
        payloadClass, analysisResult, domainCheck, sslCheck, threatIntelResult, redirectCheck, scoring
    });

    return new Promise((resolve, reject) => {
        db.run(
            `INSERT INTO scan_sessions (input_type, payload_type, original_payload, final_url, risk_score, trust_score, risk_level, confidence, qr_image, details_json, batch_id) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
            [input_type, payloadClass.type, payload, finalUrl, scoring.riskScore, scoring.trustScore, scoring.verdict, scoring.confidence, qr_image, detailsJson, batch_id],
            function(err) {
                if (err) return reject(err);
                const scan_id = this.lastID;
                
                if (scoring.evidence && scoring.evidence.length > 0) {
                    const stmt = db.prepare(`INSERT INTO threat_indicators (scan_id, indicator_type, severity, description) VALUES (?, ?, ?, ?)`);
                    scoring.evidence.forEach(ind => {
                        stmt.run([scan_id, ind.id, ind.severity, ind.description]);
                    });
                    stmt.finalize();
                }

                resolve({
                    analysisId: scan_id,
                    timestamp: new Date().toISOString(),
                    originalUrl: payload,
                    payloadClass: payloadClass,
                    normalizedUrl: analysisResult ? analysisResult.normalizedUrl : payload,
                    finalUrl: finalUrl,
                    hostname: analysisResult ? analysisResult.domain : null,
                    analysis: {
                        urlLexical: analysisResult,
                        domainCheck,
                        sslCheck,
                        threatIntel: threatIntelResult,
                        redirectCheck
                    },
                    scoring,
                    processing: {
                        status: "COMPLETED",
                        durationMs: Date.now() - startTime,
                        errors: []
                    },
                    qr_image
                });
            }
        );
    });
}

// Background processing function with Bounded Concurrency
async function processBatchAsync(batchId, payloads) {
    db.run(`UPDATE batch_jobs SET status = 'PROCESSING' WHERE batch_id = ?`, [batchId]);
    
    let processed = 0, safe = 0, susp = 0, dang = 0, failed = 0;
    const CHUNK_SIZE = 10; // Process 10 items concurrently
    
    for (let i = 0; i < payloads.length; i += CHUNK_SIZE) {
        const chunk = payloads.slice(i, i + CHUNK_SIZE);
        
        await Promise.all(chunk.map(async (item) => {
            try {
                let payloadStr = typeof item === 'string' ? item : item.payload;
                let qrImg = typeof item === 'string' ? null : item.qrImageDataUrl;
                
                const res = await processSinglePayload(payloadStr, qrImg, 'bulk', batchId);
                
                if (res.scoring.verdict === 'SAFE') safe++;
                else if (res.scoring.verdict === 'WARNING') susp++;
                else if (res.scoring.verdict === 'DANGEROUS') dang++;
                processed++;
                
            } catch (e) {
                console.error("Batch processing error on item:", e);
                failed++;
                processed++;
            }
        }));
        
        // Update database progress incrementally
        db.run(
            `UPDATE batch_jobs SET processed_items = ?, safe_count = ?, suspicious_count = ?, dangerous_count = ?, failed_count = ? WHERE batch_id = ?`,
            [processed, safe, susp, dang, failed, batchId]
        );
    }
    
    db.run(`UPDATE batch_jobs SET status = 'COMPLETED' WHERE batch_id = ?`, [batchId]);
}

// Endpoint: Create Batch Job
router.post('/batches', (req, res) => {
    const { payloads } = req.body;
    if (!payloads || !Array.isArray(payloads) || payloads.length === 0) {
        return res.status(400).json({ error: 'Payloads array is required' });
    }

    const batchId = uuidv4();
    const totalItems = payloads.length;

    db.run(
        `INSERT INTO batch_jobs (batch_id, status, total_items) VALUES (?, 'QUEUED', ?)`,
        [batchId, totalItems],
        (err) => {
            if (err) return res.status(500).json({ error: 'Failed to create batch job' });
            
            // Start background processing immediately, DO NOT AWAIT
            processBatchAsync(batchId, payloads).catch(e => console.error("Batch error:", e));
            
            res.json({ batchId, status: 'QUEUED', totalItems });
        }
    );
});

// Endpoint: Get Batch Progress
router.get('/batches/:batchId', (req, res) => {
    const { batchId } = req.params;
    db.get(`SELECT * FROM batch_jobs WHERE batch_id = ?`, [batchId], (err, row) => {
        if (err || !row) return res.status(404).json({ error: 'Batch not found' });
        res.json(row);
    });
});

// Endpoint: Get Batch Results (Paginated)
router.get('/batches/:batchId/results', (req, res) => {
    const { batchId } = req.params;
    db.all(`SELECT * FROM scan_sessions WHERE batch_id = ? ORDER BY scan_id ASC`, [batchId], (err, rows) => {
        if (err) return res.status(500).json({ error: 'Database error' });
        
        // Convert to format UI expects
        const items = rows.map((r, idx) => {
            const details = r.details_json ? JSON.parse(r.details_json) : {};
            // Backward compatibility for old UI format if needed
            return {
                id: idx,
                payload: r.original_payload,
                score: r.risk_score,
                verdict: r.risk_level,
                details: { ...details, qr_image: r.qr_image }
            };
        });
        
        res.json({ items });
    });
});

// Keep legacy /bulk working for backward compatibility, but it just waits for the new process (not recommended for 1000+)
router.post('/bulk', async (req, res) => {
    return res.status(400).json({ error: 'Legacy /bulk is disabled for large files. Use /batches job API.' });
});

// Endpoint: Log User Action (Block/Continue/Sandbox)
router.post('/action', (req, res) => {
    const { scan_id, action } = req.body;
    if (!scan_id || !action) return res.status(400).json({ error: 'Missing parameters' });

    db.run(`INSERT INTO action_logs (scan_id, action) VALUES (?, ?)`, [scan_id, action], (err) => {
        if (err) return res.status(500).json({ error: 'Database error' });
        res.json({ success: true });
    });
});

// Endpoint: Get Scan History with Pagination and Search
router.get('/history', (req, res) => {
    const page = parseInt(req.query.page) || 1;
    const limit = parseInt(req.query.limit) || 50;
    const offset = (page - 1) * limit;
    const search = req.query.search ? `%${req.query.search}%` : null;

    let query = `SELECT * FROM scan_sessions`;
    let countQuery = `SELECT COUNT(*) as total FROM scan_sessions`;
    let params = [];
    
    if (search) {
        query += ` WHERE original_payload LIKE ? OR final_url LIKE ?`;
        countQuery += ` WHERE original_payload LIKE ? OR final_url LIKE ?`;
        params = [search, search];
    }
    
    query += ` ORDER BY timestamp DESC LIMIT ? OFFSET ?`;
    
    db.get(countQuery, params, (err, countRow) => {
        if (err) return res.status(500).json({ error: 'Database error' });
        const total = countRow.total;
        
        db.all(query, [...params, limit, offset], (err, rows) => {
            if (err) return res.status(500).json({ error: 'Database error' });
            res.json({ total, page, limit, data: rows });
        });
    });
});

// Endpoint: Get Global History Stats
router.get('/history/stats', (req, res) => {
    db.get(`
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN risk_level = 'SAFE' THEN 1 ELSE 0 END) as safe,
            SUM(CASE WHEN risk_level = 'SUSPICIOUS' OR risk_level = 'WARNING' THEN 1 ELSE 0 END) as suspicious,
            SUM(CASE WHEN risk_level = 'DANGEROUS' THEN 1 ELSE 0 END) as dangerous
        FROM scan_sessions
    `, [], (err, row) => {
        if (err) return res.status(500).json({ error: 'Database error' });
        res.json({
            total: row.total || 0,
            safe: row.safe || 0,
            suspicious: row.suspicious || 0,
            dangerous: row.dangerous || 0
        });
    });
});

// Endpoint: Analyze SSL directly
router.post('/analyze-ssl', async (req, res) => {
    try {
        const { url } = req.body;
        if (!url) return res.status(400).json({ error: 'URL is required' });
        const result = await checkSSL(url);
        res.json(result);
    } catch (e) {
        res.status(500).json({ error: 'Internal error checking SSL' });
    }
});

module.exports = router;
