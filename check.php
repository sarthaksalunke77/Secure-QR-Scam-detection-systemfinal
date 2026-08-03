<?php include 'includes/header.php'; ?>

<!-- Include libraries for PDF, ZIP, and QR processing -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<div class="max-w-4xl mx-auto animate-in fade-in zoom-in duration-500">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-white mb-4">Live URL & UPI Analysis</h1>
        <p class="text-gray-400 max-w-2xl mx-auto">Inspect websites, UPI IDs, QR code images, PDFs, or ZIP archives in real-time through our multi-engine security architecture.</p>
    </div>

    <!-- Main Scanner Box -->
    <div class="glass-panel rounded-2xl p-8 shadow-2xl relative overflow-hidden border border-cyber-border">
        <!-- Animated grid background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="relative z-10" id="main-content">
            <!-- Tabs -->
            <div class="flex border-b border-gray-700 mb-8" id="tabs">
                <button id="tab-text" class="flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-cyber-primary border-cyber-primary bg-blue-900/10">
                    <i class="ph ph-keyboard text-xl"></i> Manual Input Check
                </button>
                <button id="tab-file" class="flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-gray-400 border-transparent hover:text-gray-200 hover:bg-gray-800/30">
                    <i class="ph ph-file-arrow-up text-xl"></i> Upload QR File (Image/PDF/ZIP)
                </button>
            </div>

            <!-- Panel 1: Text Form -->
            <div id="panel-text" class="space-y-6">
                <!-- Inner sub-tabs (URL / UPI) -->
                <div class="flex gap-4 p-1.5 bg-gray-900/80 rounded-xl border border-gray-800">
                    <button id="btn-sub-url" class="flex-1 py-2 rounded-lg font-bold text-sm bg-cyber-primary text-white transition-all">URL Website Check</button>
                    <button id="btn-sub-upi" class="flex-1 py-2 rounded-lg font-bold text-sm text-gray-400 hover:text-gray-200 transition-all">UPI ID Check</button>
                </div>

                <form id="check-form" class="space-y-6">
                    <div>
                        <label id="input-label" class="block text-xs font-semibold text-gray-400 mb-2 uppercase tracking-wider">
                            Enter Suspicious URL
                        </label>
                        <div class="relative">
                            <input
                                type="text"
                                id="payload-input"
                                placeholder="e.g. https://free-gift-claim.example.com/login"
                                class="w-full bg-gray-990 border-2 border-gray-800 focus:border-cyber-primary rounded-xl px-4 py-4 text-white text-lg placeholder-gray-600 focus:outline-none focus:ring-0 transition-colors pl-12"
                                autofocus
                            />
                            <i class="ph ph-globe text-2xl text-gray-500 absolute left-4 top-1/2 -translate-y-1/2" id="input-icon"></i>
                        </div>
                    </div>

                    <button
                        type="submit"
                        id="submit-btn"
                        class="w-full flex items-center justify-center gap-3 px-6 py-4 rounded-xl font-bold text-lg transition-all bg-cyber-primary hover:bg-blue-600 text-white shadow-[0_0_20px_rgba(59,130,246,0.4)] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <i class="ph ph-shield-check text-2xl"></i>
                        <span>Start Security Check</span>
                    </button>
                </form>
            </div>

            <!-- Panel 2: File Form -->
            <div id="panel-file" class="hidden space-y-6">
                <div class="flex flex-col items-center justify-center p-10 border-2 border-dashed border-cyber-border rounded-xl hover:border-cyber-primary hover:bg-blue-900/15 transition-all cursor-pointer group" onclick="document.getElementById('file-upload').click()">
                    <i class="ph ph-cloud-arrow-up text-gray-500 text-7xl group-hover:text-cyber-primary mb-4 transition-colors"></i>
                    <span class="text-xl font-bold text-white mb-2">Upload Files</span>
                    <span class="text-sm text-gray-400 text-center">Drag and drop or browse to upload.<br>Supports <strong class="text-white">QR Code Images (PNG/JPG/WEBP), PDFs, or ZIP archives</strong>.</span>
                </div>
                <input type="file" id="file-upload" accept="image/*,application/pdf,application/zip,application/x-zip-compressed" class="hidden" />
            </div>

            <!-- Local Error display -->
            <div id="error-msg" class="hidden mt-6 p-4 bg-red-900/20 border border-red-500/50 rounded-lg flex items-start gap-3">
                <i class="ph ph-warning-circle text-red-500 text-2xl flex-shrink-0"></i>
                <p class="text-red-200" id="error-text"></p>
            </div>
        </div>

        <!-- Dynamic Live Scanning Dashboard Loader -->
        <div id="scanning-loader" class="hidden relative z-10 py-12 flex flex-col items-center justify-center text-center">
            <!-- Hexagonal scanning ring -->
            <div class="relative w-28 h-28 mb-8 flex items-center justify-center">
                <div class="absolute inset-0 rounded-full border-4 border-dashed border-cyber-primary animate-spin duration-[8s]"></div>
                <div class="absolute inset-2 rounded-full border-4 border-solid border-cyber-neon/40 animate-ping duration-[2s]"></div>
                <div class="w-16 h-16 rounded-full bg-cyber-primary/20 border-2 border-cyber-primary flex items-center justify-center">
                    <i class="ph ph-shield-check text-cyber-neon text-4xl animate-pulse"></i>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-white mb-2" id="loader-title">Initiating Analysis Engine...</h3>
            <p class="text-cyber-neon font-semibold text-sm tracking-wider uppercase mb-8" id="loader-subtitle">Loading Modules</p>

            <!-- Diagnostic messages -->
            <div class="w-full max-w-md bg-gray-900/80 rounded-2xl border border-gray-800 p-6 text-left space-y-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-400 font-medium">Scanning Node</span>
                    <span class="text-white font-semibold font-mono" id="node-ip">127.0.0.1</span>
                </div>
                <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                    <div id="scan-progress-bar" class="h-full bg-cyber-primary w-0 transition-all duration-300"></div>
                </div>
                <div class="space-y-2 text-xs font-mono text-gray-500" id="loader-logs">
                    <!-- Diagnostic logs fill dynamically -->
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    document.addEventListener('DOMContentLoaded', () => {
        let activeTab = 'text';
        let subTab = 'url';

        const tabText = document.getElementById('tab-text');
        const tabFile = document.getElementById('tab-file');
        const panelText = document.getElementById('panel-text');
        const panelFile = document.getElementById('panel-file');

        const btnSubUrl = document.getElementById('btn-sub-url');
        const btnSubUpi = document.getElementById('btn-sub-upi');
        const inputLabel = document.getElementById('input-label');
        const payloadInput = document.getElementById('payload-input');
        const inputIcon = document.getElementById('input-icon');
        const btnText = document.querySelector('#submit-btn span');
        const errorMsg = document.getElementById('error-msg');
        const errorText = document.getElementById('error-text');
        const form = document.getElementById('check-form');
        const mainContent = document.getElementById('main-content');
        const scanningLoader = document.getElementById('scanning-loader');

        // Tab Switching
        tabText.addEventListener('click', () => {
            activeTab = 'text';
            tabText.className = 'flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-cyber-primary border-cyber-primary bg-blue-900/10';
            tabFile.className = 'flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-gray-400 border-transparent hover:text-gray-200 hover:bg-gray-800/30';
            panelText.classList.remove('hidden');
            panelFile.classList.add('hidden');
            errorMsg.classList.add('hidden');
        });

        tabFile.addEventListener('click', () => {
            activeTab = 'file';
            tabFile.className = 'flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-cyber-neon border-cyber-neon bg-cyan-900/10';
            tabText.className = 'flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-gray-400 border-transparent hover:text-gray-200 hover:bg-gray-800/30';
            panelFile.classList.remove('hidden');
            panelText.classList.add('hidden');
            errorMsg.classList.add('hidden');
        });

        // Sub-Tab Switching
        btnSubUrl.addEventListener('click', () => {
            subTab = 'url';
            btnSubUrl.className = 'flex-1 py-2 rounded-lg font-bold text-sm bg-cyber-primary text-white transition-all';
            btnSubUpi.className = 'flex-1 py-2 rounded-lg font-bold text-sm text-gray-400 hover:text-gray-200 transition-all';
            inputLabel.innerText = 'Enter Suspicious URL';
            payloadInput.placeholder = 'e.g. https://free-gift-claim.example.com/login';
            inputIcon.className = 'ph ph-globe text-2xl text-gray-500 absolute left-4 top-1/2 -translate-y-1/2';
        });

        btnSubUpi.addEventListener('click', () => {
            subTab = 'upi';
            btnSubUpi.className = 'flex-1 py-2 rounded-lg font-bold text-sm bg-cyber-neon text-black transition-all';
            btnSubUrl.className = 'flex-1 py-2 rounded-lg font-bold text-sm text-gray-400 hover:text-gray-200 transition-all';
            inputLabel.innerText = 'Enter Suspicious UPI ID';
            payloadInput.placeholder = 'e.g. 9423663923@ibl';
            inputIcon.className = 'ph ph-device-mobile text-2xl text-gray-500 absolute left-4 top-1/2 -translate-y-1/2';
        });

        // Form Submit
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const val = payloadInput.value.trim();
            if (!val) return;

            errorMsg.classList.add('hidden');
            const forcedType = subTab === 'upi' ? 'upi_id_only' : 'url';
            await runAnalysisPipeline(val, 'manual_string', forcedType);
        });

        // File upload handling
        const fileUpload = document.getElementById('file-upload');
        fileUpload.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            errorMsg.classList.add('hidden');
            
            try {
                // Initialize scan loading view
                mainContent.classList.add('hidden');
                scanningLoader.classList.remove('hidden');
                logMsg('Reading file structure...');

                const ext = file.name.split('.').pop().toLowerCase();

                if (ext === 'pdf') {
                    logMsg('PDF document detected. Spawning PDF.js rendering worker...');
                    const reader = new FileReader();
                    reader.onload = async function() {
                        try {
                            const buffer = this.result;
                            const pdf = await pdfjsLib.getDocument({data: buffer}).promise;
                            logMsg(`PDF loaded. Total Pages: ${pdf.numPages}. Analyzing page contents...`);
                            
                            for (let i = 1; i <= pdf.numPages; i++) {
                                logMsg(`Scanning Page ${i} of ${pdf.numPages} for QR codes...`);
                                const page = await pdf.getPage(i);
                                const viewport = page.getViewport({scale: 1.5});
                                const canvas = document.createElement('canvas');
                                const ctx = canvas.getContext('2d');
                                canvas.width = viewport.width;
                                canvas.height = viewport.height;
                                await page.render({canvasContext: ctx, viewport: viewport}).promise;

                                const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                                const code = jsQR(imgData.data, imgData.width, imgData.height, { inversionAttempts: "attemptBoth" });
                                if (code && code.data) {
                                    logMsg(`QR Code successfully decoded from page ${i}!`);
                                    await runAnalysisPipeline(code.data, 'pdf_upload');
                                    return;
                                }
                            }
                            throw new Error('No QR code found inside PDF pages.');
                        } catch (err) {
                            showFileError(err.message);
                        }
                    };
                    reader.readAsArrayBuffer(file);

                } else if (ext === 'zip') {
                    logMsg('ZIP Archive detected. Initializing JSZip extractor...');
                    const reader = new FileReader();
                    reader.onload = async function() {
                        try {
                            const zip = await JSZip.loadAsync(this.result);
                            const files = Object.keys(zip.files).filter(name => /\.(png|jpe?g|webp)$/i.test(name));
                            logMsg(`ZIP structure parsed. Found ${files.length} images inside archive.`);

                            if (files.length === 0) throw new Error('No valid images (PNG/JPG/WEBP) found in ZIP.');

                            for (const name of files) {
                                logMsg(`Extracting and scanning ${name}...`);
                                const base64 = await zip.files[name].async('base64');
                                const dataUrl = 'data:image/' + name.split('.').pop() + ';base64,' + base64;
                                
                                const decodedPayload = await scanDataUrl(dataUrl);
                                if (decodedPayload) {
                                    logMsg(`QR Code successfully decoded from archive file ${name}!`);
                                    await runAnalysisPipeline(decodedPayload, 'zip_upload');
                                    return;
                                }
                            }
                            throw new Error('No valid QR codes found inside the archived images.');
                        } catch (err) {
                            showFileError(err.message);
                        }
                    };
                    reader.readAsArrayBuffer(file);

                } else {
                    // Image upload
                    logMsg('Image file detected. Loading image canvas parser...');
                    const reader = new FileReader();
                    reader.onload = async function() {
                        const payload = await scanDataUrl(this.result);
                        if (payload) {
                            logMsg('QR code successfully decoded from image.');
                            await runAnalysisPipeline(payload, 'upload');
                        } else {
                            showFileError('No QR code found in this image. Please try a clearer image.');
                        }
                    };
                    reader.readAsDataURL(file);
                }

            } catch (err) {
                showFileError(err.message);
            }
        });

        // Scan helper
        function scanDataUrl(dataUrl) {
            return new Promise((resolve) => {
                const img = new Image();
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imgData.data, imgData.width, imgData.height, { inversionAttempts: "attemptBoth" });
                    resolve(code ? code.data : null);
                };
                img.onerror = () => resolve(null);
                img.src = dataUrl;
            });
        }

        function showFileError(msg) {
            mainContent.classList.remove('hidden');
            scanningLoader.classList.add('hidden');
            errorMsg.classList.remove('hidden');
            errorText.innerText = msg;
            fileUpload.value = '';
        }

        // Live Log Output
        function logMsg(msg) {
            const logs = document.getElementById('loader-logs');
            const div = document.createElement('div');
            div.className = 'flex justify-between items-center text-gray-400';
            div.innerHTML = `<span>&gt; ${msg}</span><span class="text-cyber-neon">OK</span>`;
            logs.appendChild(div);
            logs.scrollTop = logs.scrollHeight;
        }

        // Pipeline Orchestrator
        async function runAnalysisPipeline(payload, inputType, forcedType = 'url') {
            mainContent.classList.add('hidden');
            scanningLoader.classList.remove('hidden');
            
            const logs = document.getElementById('loader-logs');
            logs.innerHTML = '';
            
            const bar = document.getElementById('scan-progress-bar');
            const title = document.getElementById('loader-title');
            const subtitle = document.getElementById('loader-subtitle');

            const steps = [
                { percent: 10, title: 'Extracting Payload', subtitle: 'Analyzing Classification Structure' },
                { percent: 25, title: 'Querying DNS Records', subtitle: 'Resolving A/AAAA/MX/TXT records' },
                { percent: 40, title: 'Resolving WHOIS Metadata', subtitle: 'Fetching domain age & registrar' },
                { percent: 55, title: 'Tracing Redirect Chain', subtitle: 'Following HTTP response locations' },
                { percent: 70, title: 'Inspecting SSL/TLS Cert', subtitle: 'Reading OpenSSL socket x509 details' },
                { percent: 85, title: 'Consulting Threat Intelligence', subtitle: 'Querying GSB, VT, AbuseIPDB & local lists' },
                { percent: 95, title: 'Finalizing Trust Score', subtitle: 'Compiling security evidence matrix' }
            ];

            // Run artificial delays for visualization
            for (const step of steps) {
                bar.style.width = `${step.percent}%`;
                title.innerText = step.title;
                subtitle.innerText = step.subtitle;
                logMsg(step.subtitle);
                await new Promise(r => setTimeout(r, 600));
            }

            try {
                const response = await fetch('api/scan.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        payload: payload,
                        input_type: inputType,
                        forced_type: forcedType
                    })
                });

                const data = await response.json();
                if (data.scan_id) {
                    bar.style.width = '100%';
                    title.innerText = 'Analysis Complete!';
                    subtitle.innerText = 'Redirecting to Dashboard';
                    await new Promise(r => setTimeout(r, 400));
                    window.location.href = `report.php?id=${data.scan_id}`;
                } else {
                    showFileError(data.error || 'Failed to execute security scan.');
                }
            } catch (err) {
                console.error(err);
                showFileError('Security scan failed. Connection was closed or server is offline.');
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
