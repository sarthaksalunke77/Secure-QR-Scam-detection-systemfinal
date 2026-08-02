/**
 * Deterministic Risk Scoring and Verdict Engine
 * Calculates a 0-100 risk score and determines the exact verdict.
 */

function calculateRisk(payloadClass, analysis, domainCheck, sslCheck, threatIntel, redirectCheck) {
    let totalRisk = 0;
    const evidenceMap = new Map(); // Use Map to prevent double counting identical evidence IDs

    function addEvidence(id, source, severity, riskContribution, description) {
        if (!evidenceMap.has(id)) {
            evidenceMap.set(id, { id, source: [source], severity, riskContribution, description });
            totalRisk += riskContribution;
        } else {
            // Correlation: Evidence exists, just append the source if new
            const existing = evidenceMap.get(id);
            if (!existing.source.includes(source)) {
                existing.source.push(source);
            }
        }
    }

    let checksCompleted = 0;
    let totalPossibleChecks = 5;

    if (payloadClass.type === 'url') {
        // 1. URL Analysis (completed if exists)
        if (analysis && analysis.indicators) {
            checksCompleted++;
            analysis.indicators.forEach(ind => {
                addEvidence(ind.id, 'URL_ANALYSIS', ind.severity, ind.riskContribution, ind.description);
            });
        }

        // 2. Domain Check
        if (domainCheck) {
            if (domainCheck.status !== 'NOT_CHECKED' && domainCheck.status !== 'ERROR') checksCompleted++;
            
            if (domainCheck.status === 'NOT_RESOLVED') {
                addEvidence('DOMAIN_NO_RESOLVE', 'DOMAIN_CHECK', 'high', 25, 'Domain does not resolve via DNS.');
            } else if (domainCheck.status === 'INVALID_SYNTAX' && !evidenceMap.has('MALFORMED_URL')) {
                // Only add INVALID_SYNTAX if it wasn't already caught by the URL parser as MALFORMED
                addEvidence('DOMAIN_INVALID_SYNTAX', 'DOMAIN_CHECK', 'high', 35, 'Domain syntax is invalid.');
            }
            if (domainCheck.newlyRegistered) {
                addEvidence('DOMAIN_NEWLY_REGISTERED', 'DOMAIN_CHECK', 'medium', 15, 'Very newly registered domain.');
            }
            if (domainCheck.punycodeDetected) {
                // Matches ID from URL_ANALYSIS to prevent double counting
                addEvidence('PUNYCODE_HOST', 'DOMAIN_CHECK', 'medium', 10, 'Punycode domain detected.');
            }
            if (domainCheck.rawIpHost) {
                addEvidence('RAW_IP_HOST', 'DOMAIN_CHECK', 'medium', 15, 'Raw IP host used instead of domain name.');
            }
        } else {
            addEvidence('DOMAIN_NOT_CHECKED', 'DOMAIN_CHECK', 'info', 0, 'Domain check was unavailable.');
        }

        // 3. SSL/TLS Check
        if (sslCheck) {
            if (sslCheck.checked) checksCompleted++;
            
            if (sslCheck.status === 'HTTP_ONLY') {
                addEvidence('HTTP_ONLY', 'SSL_CHECK', 'low', 8, 'Connection is not encrypted (HTTP).');
            } else if (sslCheck.status === 'EXPIRED') {
                addEvidence('SSL_EXPIRED', 'SSL_CHECK', 'high', 20, 'SSL certificate has expired.');
            } else if (sslCheck.status === 'HOSTNAME_MISMATCH') {
                addEvidence('SSL_HOSTNAME_MISMATCH', 'SSL_CHECK', 'high', 25, 'SSL certificate hostname mismatch.');
            } else if (sslCheck.status === 'SELF_SIGNED') {
                addEvidence('SSL_SELF_SIGNED', 'SSL_CHECK', 'medium', 15, 'SSL certificate is self-signed.');
            } else if (sslCheck.status === 'NO_CERTIFICATE') {
                addEvidence('SSL_NO_CERTIFICATE', 'SSL_CHECK', 'high', 20, 'No certificate found on expected HTTPS port.');
            }
            // Technical errors like TLS_ERROR or TIMEOUT do NOT add risk points.
            // They just reduce confidence since the check didn't complete successfully.
        }

        // 4. Threat Intelligence
        if (threatIntel) {
            if (threatIntel.status !== 'NOT_CHECKED' && threatIntel.status !== 'API_ERROR' && threatIntel.status !== 'RATE_LIMITED') {
                checksCompleted++;
            }

            if (threatIntel.status === 'MALICIOUS') {
                addEvidence('THREAT_MALICIOUS', 'THREAT_INTEL', 'critical', 80, 'Confirmed malicious by threat intelligence.');
            } else if (threatIntel.status === 'SUSPICIOUS') {
                addEvidence('THREAT_SUSPICIOUS', 'THREAT_INTEL', 'high', 30, 'Suspicious provider result.');
            }
            
            if (threatIntel.blacklistMatch) {
                addEvidence('THREAT_BLACKLIST', 'THREAT_INTEL', 'critical', 65, 'Matches a known threat blacklist.');
            }
            if (threatIntel.detections > 1) {
                addEvidence('THREAT_MULTIPLE', 'THREAT_INTEL', 'high', 20, 'Multiple threat engines flagged this URL.');
            }
        }

        // 5. Redirect Check
        if (redirectCheck) {
            if (redirectCheck.checked) checksCompleted++;

            if (redirectCheck.crossDomainRedirect) {
                addEvidence('CROSS_DOMAIN_REDIRECT', 'REDIRECT_CHECK', 'low', 8, 'Redirects to a different domain.');
            }
            if (redirectCheck.httpsDowngrade) {
                addEvidence('HTTPS_DOWNGRADE', 'REDIRECT_CHECK', 'high', 20, 'HTTPS to HTTP downgrade during redirect.');
            }
            if (redirectCheck.excessiveRedirects) {
                addEvidence('EXCESSIVE_REDIRECTS', 'REDIRECT_CHECK', 'medium', 15, 'Excessive redirects detected (possibly hiding final payload).');
            }
            if (redirectCheck.redirectLoop) {
                addEvidence('REDIRECT_LOOP', 'REDIRECT_CHECK', 'medium', 15, 'Redirect loop detected.');
            }
        }
    } else if (payloadClass.type === 'upi') {
        checksCompleted = 5; // UPI skips external checks but has full static confidence
        const d = payloadClass.data;
        if (d.error) {
            addEvidence('UPI_FORMAT_ERROR', 'UPI_ANALYSIS', 'high', 80, 'Malformed UPI URI.');
        } else {
            if (!d.pa) addEvidence('UPI_MISSING_PA', 'UPI_ANALYSIS', 'high', 50, 'Missing Payee Address (pa).');
            else if (!/^[a-zA-Z0-9.\-_]+@[a-zA-Z]+$/.test(d.pa)) addEvidence('UPI_INVALID_PA', 'UPI_ANALYSIS', 'high', 40, 'Malformed Payee Address (VPA).');

            if (!d.pn) addEvidence('UPI_MISSING_PN', 'UPI_ANALYSIS', 'medium', 20, 'Payee Name (pn) is missing.');
            
            if (d.am) {
                const amNum = parseFloat(d.am);
                if (isNaN(amNum) || amNum < 0) {
                    addEvidence('UPI_INVALID_AMOUNT', 'UPI_ANALYSIS', 'high', 50, 'Invalid or negative amount requested.');
                } else if (amNum > 100000) {
                    addEvidence('UPI_LARGE_AMOUNT', 'UPI_ANALYSIS', 'medium', 15, 'Suspiciously large transaction amount requested.');
                }
            }
            
            if (d.url) addEvidence('UPI_EXTERNAL_URL', 'UPI_ANALYSIS', 'medium', 30, 'Contains external redirect URL.');
        }
    } else if (payloadClass.type === 'upi_id_only') {
        checksCompleted = 5; // Static analysis completed
        const vpa = payloadClass.data.vpa;
        
        if (!vpa.includes('@')) {
            addEvidence('UPI_MISSING_AT', 'UPI_ANALYSIS', 'high', 50, 'Missing @ symbol in UPI ID.');
        } else {
            const atCount = (vpa.match(/@/g) || []).length;
            if (atCount > 1) {
                addEvidence('UPI_MULTIPLE_AT', 'UPI_ANALYSIS', 'critical', 80, 'Multiple @ symbols detected (Possible Injection).');
            }
            
            const parts = vpa.split('@');
            if (parts.length >= 2) {
                const localPart = parts[0];
                const handle = parts[parts.length - 1];
                
                if (!localPart) addEvidence('UPI_MISSING_LOCAL', 'UPI_ANALYSIS', 'high', 40, 'Missing local part before @.');
                if (!handle) addEvidence('UPI_MISSING_HANDLE', 'UPI_ANALYSIS', 'high', 40, 'Missing bank handle after @.');
                
                // Suspicious characters and encoding
                if (/[%<>'"+=]/.test(vpa)) {
                    addEvidence('UPI_SUSPICIOUS_CHARS', 'UPI_ANALYSIS', 'critical', 70, 'Suspicious characters or encoding detected (SQLi/XSS risk).');
                }
            }
        }
    } else {
        checksCompleted = 5;
        addEvidence('PLAIN_TEXT', 'TEXT_ANALYSIS', 'info', 5, 'Payload is plain text. Ensure it does not contain deceptive instructions.');
    }

    // Evaluate explicit fraud signals
    let hasFraudEvidence = false;
    for (let id of evidenceMap.keys()) {
        if (id.startsWith('THREAT_') || id.startsWith('BRAND_IMPERSONATION') || id === 'BRAND_TYPOSQUATTING' || id === 'UNSAFE_DESTINATION') {
            hasFraudEvidence = true;
            break;
        }
    }

    // Exact required clamping and Trust Calculation
    let riskScore = Math.max(0, Math.min(100, totalRisk));
    
    // User requirement: DANGEROUS only when actual fraud/phishing evidence exists
    // Technical errors reduce Confidence but cannot create fake Dangerous scores.
    if (!hasFraudEvidence && riskScore > 60) {
        riskScore = 60; // Cap at max Warning level
    }

    const trustScore = 100 - riskScore;

    // Strict 0-30, 31-60, 61-100 logic
    let verdict = 'SAFE';
    if (riskScore >= 61) {
        verdict = 'DANGEROUS';
    } else if (riskScore >= 31) {
        verdict = 'WARNING';
    }

    // Confidence Calculation
    const confidence = Math.round((checksCompleted / totalPossibleChecks) * 100);

    return {
        riskScore,
        trustScore,
        confidence,
        verdict,
        evidence: Array.from(evidenceMap.values())
    };
}

module.exports = {
    calculateRisk
};
