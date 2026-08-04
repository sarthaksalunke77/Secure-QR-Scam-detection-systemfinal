<?php include 'includes/header.php'; ?>

<!-- Include external libraries for client-side processing -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';</script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<div class="max-w-6xl mx-auto animate-in fade-in duration-500 pb-12">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Enterprise Bulk Analysis</h1>
        <p class="text-gray-400">Upload CSV, Excel, Text, or PDF files containing multiple QR codes or URLs for asynchronous batch processing.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Upload Column -->
        <div class="glass-panel p-8 rounded-xl flex flex-col items-center justify-center text-center h-[fit-content]">
            <i class="ph ph-cloud-arrow-up text-6xl text-cyber-primary mb-4"></i>
            <h2 class="text-xl font-bold text-white mb-2">Upload Files</h2>
            <p class="text-sm text-gray-400 mb-6">Select files to begin bulk processing.</p>
            
            <input 
                type="file" 
                id="file-input"
                multiple 
                class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-900/30 file:text-cyber-primary hover:file:bg-blue-900/50 mb-6" 
            />

            <div id="file-info" class="hidden text-left w-full mb-6 bg-gray-900/50 p-4 rounded text-sm text-gray-300">
                <strong>Selected:</strong> <span id="file-count">0</span> file(s)
                <ul id="file-list" class="mt-2 list-disc list-inside"></ul>
            </div>
            
            <p id="error-msg" class="text-red-400 text-sm mb-4 hidden"></p>

            <button 
                id="start-btn"
                disabled
                class="w-full py-3 bg-cyber-primary text-white font-bold rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-[0_0_15px_rgba(59,130,246,0.3)] flex justify-center items-center gap-2"
            >
                <span id="btn-text">Start Batch Scan</span>
            </button>
        </div>

        <!-- Results Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Loading State -->
            <div id="loading-state" class="hidden glass-panel p-8 rounded-xl flex flex-col items-center justify-center h-full min-h-[300px]">
                <i class="ph ph-spinner-gap text-5xl text-cyber-primary animate-spin mb-4"></i>
                <p id="status-text" class="text-cyber-neon animate-pulse text-lg">VALIDATING</p>
                <p id="progress-text" class="text-gray-400 mt-2">Validating file types...</p>
            </div>

            <!-- Idle State -->
            <div id="idle-state" class="glass-panel p-8 rounded-xl flex flex-col items-center justify-center h-full min-h-[300px] text-gray-500">
                <i class="ph ph-cloud-arrow-up text-5xl mb-4 opacity-50"></i>
                <p id="idle-text">Awaiting file upload for bulk processing.</p>
            </div>

            <!-- Results State -->
            <div id="results-state" class="hidden space-y-6">
                <!-- Summary -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="glass-panel p-4 rounded-lg text-center">
                        <div class="text-xs text-gray-500 uppercase">Processed</div>
                        <div id="res-total" class="text-2xl font-bold text-white">0</div>
                    </div>
                    <div class="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-safe">
                        <div class="text-xs text-gray-500 uppercase">Safe</div>
                        <div id="res-safe" class="text-2xl font-bold text-cyber-safe">0</div>
                    </div>
                    <div class="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-warning">
                        <div class="text-xs text-gray-500 uppercase">Caution</div>
                        <div id="res-warning" class="text-2xl font-bold text-cyber-warning">0</div>
                    </div>
                    <div class="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-danger">
                        <div class="text-xs text-gray-500 uppercase">Dangerous</div>
                        <div id="res-danger" class="text-2xl font-bold text-cyber-danger">0</div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="glass-panel rounded-xl overflow-hidden">
                    <div class="flex justify-between items-center p-4 border-b border-cyber-border bg-gray-900/50">
                        <h3 class="font-bold text-white">Batch Results Preview</h3>
                        <button id="export-btn" class="flex items-center gap-2 px-3 py-1.5 text-sm bg-cyber-primary/20 text-cyber-primary rounded hover:bg-cyber-primary hover:text-white transition-colors border border-cyber-primary/50">
                            <i class="ph ph-file-pdf"></i> Export Report (PDF)
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-gray-900/30 sticky top-0">
                                    <th class="p-3 text-gray-400 font-semibold w-12">QR</th>
                                    <th class="p-3 text-gray-400 font-semibold">Payee Name / URL</th>
                                    <th class="p-3 text-gray-400 font-semibold">UPI ID / Domain</th>
                                    <th class="p-3 text-gray-400 font-semibold text-center">Risk Score</th>
                                    <th class="p-3 text-gray-400 font-semibold text-center">Trust Score</th>
                                    <th class="p-3 text-gray-400 font-semibold text-center">Verdict</th>
                                </tr>
                            </thead>
                            <tbody id="results-tbody">
                                <!-- Populated via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('file-input');
    const fileInfo = document.getElementById('file-info');
    const fileCount = document.getElementById('file-count');
    const fileList = document.getElementById('file-list');
    const startBtn = document.getElementById('start-btn');
    const btnText = document.getElementById('btn-text');
    const errorMsg = document.getElementById('error-msg');
    
    const idleState = document.getElementById('idle-state');
    const idleText = document.getElementById('idle-text');
    const loadingState = document.getElementById('loading-state');
    const statusText = document.getElementById('status-text');
    const progressText = document.getElementById('progress-text');
    const resultsState = document.getElementById('results-state');
    
    const resTotal = document.getElementById('res-total');
    const resSafe = document.getElementById('res-safe');
    const resWarning = document.getElementById('res-warning');
    const resDanger = document.getElementById('res-danger');
    const resultsTbody = document.getElementById('results-tbody');
    const exportBtn = document.getElementById('export-btn');

    let selectedFiles = [];
    let finalResults = [];
    
    // --- NEW: AUTO-START CHECK FOR TEXTAREA INPUT ---
    const autoPayloads = sessionStorage.getItem('bulk_text_payloads');
    if (autoPayloads && new URLSearchParams(window.location.search).get('autostart') === '1') {
        sessionStorage.removeItem('bulk_text_payloads');
        const urls = JSON.parse(autoPayloads);
        
        startBtn.disabled = true;
        btnText.innerHTML = '<i class="ph ph-spinner-gap animate-spin"></i> Processing...';
        idleState.classList.add('hidden');
        resultsState.classList.add('hidden');
        loadingState.classList.remove('hidden');
        errorMsg.classList.add('hidden');
        
        const mappedPayloads = urls.map(u => ({ payload: u, sourceFile: 'Manual Text Input', qrImageDataUrl: null }));
        
        // Use setTimeout to allow DOM to finish loading before kicking off heavy scans
        setTimeout(() => runBulkScan(mappedPayloads), 500);
    }
    // ------------------------------------------------

    fileInput.addEventListener('change', (e) => {
        selectedFiles = Array.from(e.target.files);
        if (selectedFiles.length > 0) {
            fileInfo.classList.remove('hidden');
            fileCount.innerText = selectedFiles.length;
            fileList.innerHTML = selectedFiles.map(f => `<li class="truncate">${f.name} (${(f.size / 1024).toFixed(1)} KB)</li>`).join('');
            startBtn.disabled = false;
            errorMsg.classList.add('hidden');
            resultsState.classList.add('hidden');
            idleState.classList.remove('hidden');
            idleText.innerText = 'Ready to begin batch scanning.';
        } else {
            fileInfo.classList.add('hidden');
            startBtn.disabled = true;
            idleText.innerText = 'Awaiting file upload for bulk processing.';
        }
    });

    async function extractPayloadsFromFile(file) {
        const extractedData = [];
        const urlRegex = /(https?:\/\/[^\s]+|upi:\/\/pay[^\s]+)/gi;

        if (file.name.toLowerCase().endsWith('.pdf')) {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d', { willReadFrequently: true });

            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const viewport = page.getViewport({ scale: 2.0 });
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                
                await page.render({ canvasContext: ctx, viewport: viewport }).promise;
                
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                let qrImageDataUrl = null;
                if (code && code.data) {
                    qrImageDataUrl = canvas.toDataURL('image/png');
                    extractedData.push({ payload: code.data, qrImageDataUrl, sourceFile: file.name });
                }
                
                const textContent = await page.getTextContent();
                let rawText = textContent.items.map(item => item.str).join('\n');
                rawText = rawText.replace(/([:/.?=&\-_])\s*\n\s*/g, '$1');
                rawText = rawText.replace(/\s+/g, ' ');
                
                const matches = rawText.match(urlRegex);
                if (matches) {
                    matches.forEach(m => {
                        if (!extractedData.some(d => d.payload === m)) {
                            extractedData.push({ payload: m, sourceFile: file.name, qrImageDataUrl: null });
                        }
                    });
                }
            }
        } else {
            const text = await file.text();
            const matches = text.match(urlRegex);
            if (matches) {
                matches.forEach(m => {
                    if (!extractedData.some(d => d.payload === m)) {
                        extractedData.push({ payload: m, sourceFile: file.name, qrImageDataUrl: null });
                    }
                });
            } else {
                text.split(/\r?\n/).forEach(line => {
                    if (line.trim() && !line.trim().includes(' ')) {
                        if (!extractedData.some(d => d.payload === line.trim())) {
                            extractedData.push({ payload: line.trim(), sourceFile: file.name, qrImageDataUrl: null });
                        }
                    }
                });
            }
        }
        return extractedData;
    }

    startBtn.addEventListener('click', async () => {
        if (selectedFiles.length === 0) return;
        
        startBtn.disabled = true;
        btnText.innerHTML = '<i class="ph ph-spinner-gap animate-spin"></i> Processing...';
        idleState.classList.add('hidden');
        resultsState.classList.add('hidden');
        loadingState.classList.remove('hidden');
        errorMsg.classList.add('hidden');

        try {
            statusText.innerText = 'PARSING';
            let allPayloads = [];
            for (const file of selectedFiles) {
                progressText.innerText = `Parsing ${file.name}...`;
                const payloads = await extractPayloadsFromFile(file);
                allPayloads = allPayloads.concat(payloads);
            }
            
            await runBulkScan(allPayloads);
        } catch (e) {
            console.error(e);
            errorMsg.innerText = e.message || 'Failed to analyze files.';
            errorMsg.classList.remove('hidden');
            loadingState.classList.add('hidden');
            idleState.classList.remove('hidden');
        } finally {
            startBtn.disabled = false;
            btnText.innerText = 'Start Batch Scan';
        }
    });

    // Extract core logic to a reusable function
    async function runBulkScan(allPayloads) {
        try {
            statusText.innerText = 'QUEUED';
            
            // Remove duplicates
            const uniquePayloads = new Set();
            allPayloads = allPayloads.filter(item => {
                if (uniquePayloads.has(item.payload)) return false;
                uniquePayloads.add(item.payload);
                return true;
            });

            if (allPayloads.length === 0) {
                throw new Error('No URLs or UPI payloads found in the uploaded files.');
            }

            statusText.innerText = 'ANALYZING';
            progressText.innerText = `Queuing ${allPayloads.length} payloads...`;
            
            finalResults = [];
            
            let safeCount = 0;
            let warningCount = 0;
            let dangerCount = 0;

            for (let i = 0; i < allPayloads.length; i++) {
                progressText.innerText = `Analyzing: ${i} / ${allPayloads.length} (Safe: ${safeCount}, Caution: ${warningCount}, Dangerous: ${dangerCount})`;
                const item = allPayloads[i];
                
                try {
                    const res = await fetch('api/scan.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            payload: item.payload,
                            input_type: 'bulk',
                            qr_image: item.qrImageDataUrl
                        })
                    });
                    const data = await res.json();
                    
                    if (data.scan_id) {
                        const verdict = data.scoring.verdict;
                        if (verdict === 'SAFE') safeCount++;
                        else if (verdict === 'LOW_RISK' || verdict === 'CAUTION' || verdict === 'WARNING' || verdict === 'SUSPICIOUS') warningCount++;
                        else dangerCount++;

                        // Map verdict to display-friendly label
                        let displayVerdict;
                        if (verdict === 'SAFE') displayVerdict = 'SAFE';
                        else if (verdict === 'LOW_RISK' || verdict === 'CAUTION') displayVerdict = 'CAUTION';
                        else if (verdict === 'SUSPICIOUS' || verdict === 'WARNING') displayVerdict = 'WARNING';
                        else displayVerdict = 'DANGEROUS';

                        finalResults.push({
                            payload: item.payload,
                            qr: item.qrImageDataUrl,
                            riskScore: data.scoring.riskScore,
                            trustScore: data.scoring.trustScore,
                            verdict: displayVerdict,
                            details: data
                        });
                    }
                } catch (err) {
                    console.error("Scan error on", item.payload, err);
                }
            }
            
            progressText.innerText = `Fetching final results...`;

            resTotal.innerText = finalResults.length;
            resSafe.innerText = safeCount;
            resWarning.innerText = warningCount;
            resDanger.innerText = dangerCount;

            resultsTbody.innerHTML = finalResults.map(r => {
                const d = r.details;
                const isUpi = d.payloadClass?.type === 'upi' || d.payloadClass?.type === 'upi_id_only';
                const upi = d.payloadClass?.data || {};
                const verStatus = upi.verification_status || '';
                let name;
                if (isUpi) {
                    name = upi.pn ? upi.pn : 'Not Available';
                } else {
                    name = 'Web URL';
                }
                const idOrDomain = isUpi && upi.pa ? upi.pa : (d.analysisResult?.domain || r.payload);
                const verBadge = isUpi ? `<span class="text-[10px] text-yellow-400 block">(${verStatus ? 'Unverified' : ''})</span>` : '';
                
                return `
                <tr class="border-t border-cyber-border/50 hover:bg-gray-800/30 transition-colors">
                    <td class="p-2">
                        ${r.qr ? `<img src="${r.qr}" class="w-8 h-8 object-contain bg-white rounded" />` : `<div class="w-8 h-8 bg-gray-800 rounded flex items-center justify-center text-xs text-gray-500">None</div>`}
                    </td>
                    <td class="p-3 text-gray-300 font-semibold text-sm truncate max-w-[200px]" title="${name}">${name}${verBadge}</td>
                    <td class="p-3 text-gray-400 font-mono text-xs truncate max-w-[150px]" title="${idOrDomain}">${idOrDomain}</td>
                    <td class="p-3 font-mono font-bold text-gray-200 text-center">${r.riskScore}<span class="text-gray-600 text-xs font-normal">/100</span></td>
                    <td class="p-3 font-mono font-bold text-gray-200 text-center">${r.trustScore}<span class="text-gray-600 text-xs font-normal">/100</span></td>
                    <td class="p-3 font-semibold text-center">
                        <a href="report.php?id=${d.scan_id}" target="_blank" class="inline-block px-2 py-1 rounded text-xs font-bold uppercase tracking-wider transition-all hover:scale-105 cursor-pointer no-underline ${
                            r.verdict === 'SAFE' ? 'bg-green-500/20 text-green-400 hover:bg-green-500/30' :
                            (r.verdict === 'CAUTION' || r.verdict === 'WARNING') ? 'bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30' :
                            'bg-red-500/20 text-red-400 hover:bg-red-500/30'
                        }">${r.verdict}</a>
                    </td>
                </tr>
                `;
            }).join('');

            loadingState.classList.add('hidden');
            resultsState.classList.remove('hidden');

        } catch (e) {
            throw e; // Pass error up
        }
    }

    exportBtn.addEventListener('click', () => {
        if (!finalResults.length) return;
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let currentY = 15;
        
        doc.setFontSize(16);
        doc.text("FraudEye Enterprise Bulk Analysis Report", 14, currentY);
        currentY += 10;
        
        doc.setFontSize(10);
        doc.text(`Total Scanned: ${finalResults.length} | Safe: ${resSafe.innerText} | Suspicious: ${resWarning.innerText} | Dangerous: ${resDanger.innerText}`, 14, currentY);
        currentY += 10;
        
        const summaryData = finalResults.map(item => {
            const upi = item.details.payloadClass?.data || {};
            const isUpi = item.details.payloadClass?.type === 'upi' || item.details.payloadClass?.type === 'upi_id_only';
            let holderName;
            if (isUpi) {
                holderName = upi.pn ? upi.pn + ' (Unverified - from QR)' : 'Not Available';
            } else {
                holderName = 'Web URL';
            }
            return [
                holderName,
                isUpi && upi.pa ? upi.pa : item.payload,
                item.riskScore,
                item.trustScore,
                item.verdict
            ];
        });

        doc.autoTable({
            startY: currentY,
            head: [['QR Holder Name', 'UPI ID / Domain', 'Risk Score', 'Trust Score', 'Verdict']],
            body: summaryData,
            styles: { fontSize: 8, cellPadding: 2 },
        });

        currentY = doc.lastAutoTable.finalY + 15;

        finalResults.forEach((item, index) => {
            if (currentY > 250) {
                doc.addPage();
                currentY = 20;
            }

            doc.setFontSize(12);
            doc.text(`================================================`, 14, currentY);
            currentY += 6;
            doc.text(`QR RESULT #${index + 1}`, 14, currentY);
            currentY += 6;
            doc.text(`================================================`, 14, currentY);
            currentY += 10;

            if (item.qr) {
                try {
                    doc.addImage(item.qr, 'PNG', 14, currentY, 40, 40);
                    currentY += 45;
                } catch (e) {
                    doc.text("[QR Image Rendering Error]", 14, currentY);
                    currentY += 10;
                }
            }

            doc.setFontSize(10);
            const d = item.details;
            const isUpi = d.payloadClass?.type === 'upi' || d.payloadClass?.type === 'upi_id_only';
            const upi = d.payloadClass?.data || {};
            
            const detailsLines = [];
            if (isUpi) {
                detailsLines.push(`Payee Name (from QR): ${upi.pn || 'Not Provided'}`);
                detailsLines.push(`Verification Status: ${upi.verification_status || 'Unable to Verify'}`);
                detailsLines.push(`UPI ID: ${upi.pa || 'Missing'}`);
                
                let country = "India (UPI Network)";
                if (upi.pa) {
                    const match = upi.pa.match(/^([0-9]{2,3})[0-9]{8,10}@/);
                    if (match) {
                        const code = match[1];
                        if (code === '971') country = "United Arab Emirates (UPI International)";
                        else if (code === '65') country = "Singapore (UPI International)";
                        else if (code === '33') country = "France (UPI International)";
                        else if (code === '44') country = "United Kingdom (UPI International)";
                        else if (code === '91') country = "India";
                    }
                }
                detailsLines.push(`Country Origin: ${country}`);
                
                detailsLines.push(`Amount: ${upi.am || 'Not Specified'}`);
                detailsLines.push(`Currency: ${upi.cu || 'Not Specified'}`);
                detailsLines.push(`Merchant Code: ${upi.mc || 'Not Specified'}`);
                detailsLines.push(`Payment Mode: ${upi.mode || 'Not Specified'}`);
                detailsLines.push(`Purpose: ${upi.purpose || 'Not Specified'}`);
                detailsLines.push('');
            } else {
                detailsLines.push(`Domain/URL: ${d.analysisResult?.domain || item.payload}`);
                detailsLines.push('');
            }

            detailsLines.push(`Risk Score: ${item.riskScore} / 100`);
            detailsLines.push(`Trust Score: ${item.trustScore} / 100`);
            detailsLines.push(`Confidence: ${d.scoring?.confidence || '100'}%`);
            detailsLines.push(`Verdict: ${item.verdict}`);
            detailsLines.push('');
            
            if (isUpi) {
                detailsLines.push(`Merchant Verification: UNVERIFIED`);
                detailsLines.push('');
            }

            detailsLines.push(`Security Indicators:`);
            const indicatorsList = d.scoring?.evidence || [];
            if (indicatorsList && indicatorsList.length > 0) {
                indicatorsList.forEach(ind => {
                    const typeLabel = (ind.id || ind.type || 'UNKNOWN').toUpperCase();
                    detailsLines.push(`- ${typeLabel}: ${ind.description}`);
                });
            } else {
                detailsLines.push(`- None`);
            }
            detailsLines.push('');
            detailsLines.push(`Raw Payload:`);
            
            const splitPayload = doc.splitTextToSize(item.payload, 180);
            detailsLines.push(...splitPayload);

            detailsLines.forEach(line => {
                if (currentY > 280) {
                    doc.addPage();
                    currentY = 20;
                }
                doc.text(line, 14, currentY);
                currentY += 5;
            });

            currentY += 10;
        });

        doc.save('FraudEye_Bulk_Report.pdf');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
