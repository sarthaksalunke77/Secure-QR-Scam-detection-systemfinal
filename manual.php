<?php 
// manual.php
include 'includes/header.php'; 

// Determine which tab is active by default based on URL query param
$type = isset($_GET['type']) && $_GET['type'] === 'upi' ? 'upi' : 'url';
?>

<div class="max-w-3xl mx-auto">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-white mb-4">URL & UPI Check</h1>
        <p class="text-gray-400">Manually inspect a suspicious link or UPI ID through our massive 6-engine analysis architecture.</p>
    </div>

    <div class="glass-panel rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        <!-- Animated grid background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="relative z-10">
            <!-- Tabs -->
            <div class="flex border-b border-gray-800 mb-8">
                <button id="tab-url" onclick="switchTab('url')" class="w-1/2 flex items-center justify-center gap-2 py-4 font-bold transition-all <?php echo $type === 'url' ? 'text-blue-500 border-b-2 border-blue-500' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-300'; ?>">
                    <i class="ph ph-globe text-lg"></i> URL Check
                </button>
                <button id="tab-upi" onclick="switchTab('upi')" class="w-1/2 flex items-center justify-center gap-2 py-4 font-bold transition-all <?php echo $type === 'upi' ? 'text-blue-500 border-b-2 border-blue-500' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-300'; ?>">
                    <i class="ph ph-device-mobile text-lg"></i> UPI ID Check
                </button>
            </div>

            <!-- URL Check Form -->
            <div id="form-url" class="<?php echo $type === 'url' ? 'block' : 'hidden'; ?> animate-fade-in">
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Enter Suspicious URL(s)</label>
                <div class="relative mb-6">
                    <div class="absolute top-4 left-0 pl-4 flex items-start pointer-events-none">
                        <i class="ph ph-magnifying-glass text-gray-500 text-xl"></i>
                    </div>
                    <textarea id="url-input" rows="3" class="w-full bg-[#0f172a] border border-gray-700 text-white rounded-xl pl-12 pr-4 py-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-gray-600 resize-y" placeholder="e.g. https://sbi-verify.example.com&#10;You can enter up to 10 URLs separated by commas or newlines." autocomplete="off"></textarea>
                </div>
                <button id="btn-check-url" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="ph ph-magnifying-glass"></i> Check URL Security
                </button>
            </div>

            <!-- UPI Check Form -->
            <div id="form-upi" class="<?php echo $type === 'upi' ? 'block' : 'hidden'; ?> animate-fade-in">
                <label class="block text-xs font-bold text-gray-300 uppercase tracking-wider mb-2">Enter Suspicious UPI ID</label>
                <div class="relative mb-6">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="ph ph-magnifying-glass text-gray-500 text-xl"></i>
                    </div>
                    <input type="text" id="upi-input" class="w-full bg-[#0f172a] border border-gray-700 text-white rounded-xl pl-12 pr-4 py-4 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors placeholder-gray-600" placeholder="e.g. 9876543210@ybl" autocomplete="off">
                </div>
                <button id="btn-check-upi" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-xl transition-colors flex items-center justify-center gap-2">
                    <i class="ph ph-magnifying-glass"></i> Check UPI Security
                </button>
            </div>

            <!-- Error Message -->
            <div id="error-msg" class="hidden mt-6 p-4 bg-red-900/20 border border-red-500/50 rounded-lg flex items-start gap-3">
                <i class="ph ph-warning-circle text-red-500 text-2xl flex-shrink-0"></i>
                <p class="text-red-200" id="error-text"></p>
            </div>

            <!-- Loading State -->
            <div id="loading" class="hidden mt-8 flex flex-col items-center justify-center">
                <i class="ph ph-spinner-gap text-blue-500 text-4xl animate-spin mb-4"></i>
                <p class="text-blue-400 animate-pulse" id="loading-text">Analyzing payload against global threat databases...</p>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        const urlTab = document.getElementById('tab-url');
        const upiTab = document.getElementById('tab-upi');
        const urlForm = document.getElementById('form-url');
        const upiForm = document.getElementById('form-upi');
        const errorMsg = document.getElementById('error-msg');
        
        // Hide error when switching tabs
        errorMsg.classList.add('hidden');

        if (tab === 'url') {
            urlTab.className = "w-1/2 flex items-center justify-center gap-2 py-4 font-bold transition-all text-blue-500 border-b-2 border-blue-500";
            upiTab.className = "w-1/2 flex items-center justify-center gap-2 py-4 font-bold transition-all text-gray-500 border-b-2 border-transparent hover:text-gray-300";
            urlForm.classList.remove('hidden');
            urlForm.classList.add('block');
            upiForm.classList.add('hidden');
            upiForm.classList.remove('block');
        } else {
            upiTab.className = "w-1/2 flex items-center justify-center gap-2 py-4 font-bold transition-all text-blue-500 border-b-2 border-blue-500";
            urlTab.className = "w-1/2 flex items-center justify-center gap-2 py-4 font-bold transition-all text-gray-500 border-b-2 border-transparent hover:text-gray-300";
            upiForm.classList.remove('hidden');
            upiForm.classList.add('block');
            urlForm.classList.add('hidden');
            urlForm.classList.remove('block');
        }
    }

    async function processPayload(payload, inputType) {
        const loading = document.getElementById('loading');
        const errorMsg = document.getElementById('error-msg');
        const errorText = document.getElementById('error-text');
        const formUrl = document.getElementById('form-url');
        const formUpi = document.getElementById('form-upi');

        // Hide forms and show loading
        formUrl.classList.add('hidden');
        formUpi.classList.add('hidden');
        errorMsg.classList.add('hidden');
        loading.classList.remove('hidden');

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
                showError(data.error || 'Failed to analyze ' + inputType.toUpperCase());
            }
        } catch (err) {
            console.error(err);
            showError('Failed to connect to analysis engine. Backend server might be down.');
        }

        function showError(msg) {
            loading.classList.add('hidden');
            // Show the correct form again
            if (inputType === 'manual_url') {
                formUrl.classList.remove('hidden');
            } else {
                formUpi.classList.remove('hidden');
            }
            errorMsg.classList.remove('hidden');
            errorText.innerText = msg;
        }
    }

    document.getElementById('btn-check-url').addEventListener('click', () => {
        const val = document.getElementById('url-input').value.trim();
        if(!val) {
            alert('Please enter a URL to check.');
            return;
        }
        
        // Split input by newlines, commas, or spaces
        const urls = val.split(/[\r\n,\s]+/).map(u => u.trim()).filter(u => u.length > 0);
        
        if (urls.length > 1) {
            // Multiple URLs detected! Forward to the Bulk Analyzer automatically
            sessionStorage.setItem('bulk_text_payloads', JSON.stringify(urls));
            window.location.href = 'bulk.php?autostart=1';
            return;
        }
        
        // Single URL: normal flow
        processPayload(urls[0], 'manual_url');
    });

    document.getElementById('btn-check-upi').addEventListener('click', () => {
        const val = document.getElementById('upi-input').value.trim();
        if(!val) {
            alert('Please enter a UPI ID to check.');
            return;
        }
        processPayload(val, 'manual_upi');
    });
</script>

<?php include 'includes/footer.php'; ?>
