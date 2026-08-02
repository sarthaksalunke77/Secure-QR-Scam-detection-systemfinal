<?php include 'includes/header.php'; ?>

<div class="max-w-2xl mx-auto">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-white mb-4">Secure QR Scanner</h1>
        <p class="text-gray-400">Scan or upload a QR code. We will intercept and analyze the destination before you open it.</p>
    </div>

    <div class="glass-panel rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        <!-- Animated grid background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10 mb-6" id="controls">
            <button id="btn-start-camera" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-cyber-border rounded-xl hover:border-cyber-primary hover:bg-blue-900/20 transition-all group">
                <i class="ph ph-camera text-gray-500 text-6xl group-hover:text-cyber-primary mb-4 transition-colors"></i>
                <span class="text-lg font-semibold text-white">Start Camera</span>
                <span class="text-sm text-gray-400 mt-2">Requires permissions</span>
            </button>
            <button id="btn-stop-camera" class="hidden flex flex-col items-center justify-center p-8 border-2 border-solid border-cyber-danger rounded-xl hover:bg-red-900/20 transition-all group">
                <i class="ph ph-video-camera-slash text-cyber-danger text-6xl mb-4 transition-colors"></i>
                <span class="text-lg font-semibold text-red-400">Stop Camera</span>
            </button>

            <button onclick="document.getElementById('file-upload').click()" class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-cyber-border rounded-xl hover:border-cyber-primary hover:bg-blue-900/20 transition-all group">
                <i class="ph ph-upload text-gray-500 text-6xl group-hover:text-cyber-primary mb-4 transition-colors"></i>
                <span class="text-lg font-semibold text-white">Upload Image</span>
                <span class="text-sm text-gray-400 mt-2">PNG, JPG, WEBP</span>
            </button>
            <input type="file" id="file-upload" accept="image/*" class="hidden" />
        </div>

        <!-- Camera Viewport -->
        <div id="camera-container" class="hidden relative rounded-xl overflow-hidden border-2 border-cyber-primary mb-6 bg-black flex justify-center items-center">
            <video id="video" class="w-full max-h-[400px] object-cover" playsinline></video>
            <canvas id="canvas" class="hidden"></canvas>
            
            <!-- Scanner overlay -->
            <div class="absolute inset-0 pointer-events-none border-[3px] border-transparent">
                <div class="w-full h-full relative">
                    <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-cyber-neon m-4"></div>
                    <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-cyber-neon m-4"></div>
                    <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-cyber-neon m-4"></div>
                    <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-cyber-neon m-4"></div>
                    <div id="scan-line" class="absolute top-1/2 left-4 right-4 h-0.5 bg-cyber-neon shadow-[0_0_10px_#0ff] animate-[scan_2s_ease-in-out_infinite]"></div>
                </div>
            </div>
        </div>

        <p id="diagnostic-msg" class="text-cyber-safe text-sm text-center mb-4"></p>

        <div id="error-msg" class="hidden mb-6 p-4 bg-red-900/20 border border-red-500/50 rounded-lg flex items-start gap-3">
            <i class="ph ph-warning-circle text-red-500 text-2xl flex-shrink-0"></i>
            <p class="text-red-200" id="error-text"></p>
        </div>

        <div id="loading" class="hidden mt-8 flex flex-col items-center justify-center">
            <i class="ph ph-spinner-gap text-cyber-primary text-4xl animate-spin mb-4"></i>
            <p class="text-cyber-neon animate-pulse" id="loading-text">Analyzing payload & querying Threat Intelligence...</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d', { willReadFrequently: true });
        
        const btnStart = document.getElementById('btn-start-camera');
        const btnStop = document.getElementById('btn-stop-camera');
        const cameraContainer = document.getElementById('camera-container');
        const fileUpload = document.getElementById('file-upload');
        const diagnosticMsg = document.getElementById('diagnostic-msg');
        const errorMsg = document.getElementById('error-msg');
        const errorText = document.getElementById('error-text');
        const loading = document.getElementById('loading');
        const loadingText = document.getElementById('loading-text');

        let stream = null;
        let scanning = false;
        let animationFrameId = null;
        let lastScanned = { payload: null, timestamp: 0 };

        function showError(msg) {
            errorMsg.classList.remove('hidden');
            errorText.innerText = msg;
        }
        function hideError() {
            errorMsg.classList.add('hidden');
        }
        function updateDiagnostic(msg) {
            diagnosticMsg.innerText = msg;
        }

        btnStart.addEventListener('click', async () => {
            hideError();
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showError("Camera API is not supported in this browser.");
                return;
            }

            try {
                updateDiagnostic('Requesting camera permission...');
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: "environment" } },
                    audio: false
                });
                
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    updateDiagnostic('Stream received, starting video...');
                    video.play();
                    
                    btnStart.classList.add('hidden');
                    btnStop.classList.remove('hidden');
                    cameraContainer.classList.remove('hidden');
                    
                    scanning = true;
                    animationFrameId = requestAnimationFrame(scanLoop);
                };
            } catch (err) {
                if (err.name === 'NotAllowedError') showError('Camera permission denied.');
                else if (err.name === 'NotFoundError') showError('No camera found on this device.');
                else showError(`Camera error: ${err.message}`);
            }
        });

        btnStop.addEventListener('click', stopCamera);

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
            video.srcObject = null;
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
            }
            btnStart.classList.remove('hidden');
            btnStop.classList.add('hidden');
            cameraContainer.classList.add('hidden');
            scanning = false;
            updateDiagnostic('');
        }

        function scanLoop() {
            if (video.readyState === video.HAVE_ENOUGH_DATA && scanning) {
                canvas.height = video.videoHeight;
                canvas.width = video.videoWidth;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "attemptBoth",
                });
                
                if (code && code.data) {
                    const now = Date.now();
                    if (lastScanned.payload !== code.data || (now - lastScanned.timestamp > 3000)) {
                        lastScanned = { payload: code.data, timestamp: now };
                        updateDiagnostic('QR Detected! Extracting payload...');
                        
                        scanning = false;
                        stopCamera();
                        processPayload(code.data, 'live');
                        return;
                    }
                }
            }
            if (scanning) {
                animationFrameId = requestAnimationFrame(scanLoop);
            }
        }

        fileUpload.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;

            stopCamera();
            hideError();
            loading.classList.remove('hidden');
            document.getElementById('controls').classList.add('hidden');

            const reader = new FileReader();
            reader.onload = (event) => {
                const img = new Image();
                img.onload = () => {
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0, img.width, img.height);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "attemptBoth",
                    });
                    if (code) {
                        processPayload(code.data, 'upload');
                    } else {
                        showError('No QR code found in the image. Please try a clearer image.');
                        loading.classList.add('hidden');
                        document.getElementById('controls').classList.remove('hidden');
                    }
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });

        async function processPayload(payload, inputType) {
            loading.classList.remove('hidden');
            document.getElementById('controls').classList.add('hidden');
            updateDiagnostic('Sending payload to analysis engine...');
            
            try {
                const response = await fetch('api/scan.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ payload, input_type: inputType })
                });
                const data = await response.json();
                
                if (data.scan_id) {
                    window.location.href = `report.php?id=${data.scan_id}`;
                } else {
                    showError(data.error || 'Failed to analyze QR code.');
                    loading.classList.add('hidden');
                    document.getElementById('controls').classList.remove('hidden');
                }
            } catch (err) {
                console.error(err);
                showError('Failed to analyze QR code. Backend server might be down.');
                loading.classList.add('hidden');
                document.getElementById('controls').classList.remove('hidden');
            }
        }
    });
</script>

<?php include 'includes/footer.php'; ?>
