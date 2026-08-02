<?php include 'includes/header.php'; ?>

<div class="max-w-3xl mx-auto animate-in fade-in zoom-in duration-500">
    <div class="text-center mb-10">
        <h1 class="text-4xl font-bold text-white mb-4">URL & UPI Check</h1>
        <p class="text-gray-400">Manually inspect a suspicious link or UPI ID through our massive 6-engine analysis architecture.</p>
    </div>

    <div class="glass-panel rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        <!-- Animated grid background -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: radial-gradient(#3b82f6 1px, transparent 1px); background-size: 30px 30px;"></div>
        
        <div class="relative z-10">
            <!-- Tabs -->
            <div class="flex border-b border-gray-700 mb-8" id="tabs">
                <button id="tab-url" class="flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-cyber-primary border-cyber-primary bg-blue-900/10">
                    <i class="ph ph-globe text-xl"></i> URL Check
                </button>
                <button id="tab-upi" class="flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-gray-400 border-transparent hover:text-gray-200 hover:bg-gray-800/30">
                    <i class="ph ph-device-mobile text-xl"></i> UPI ID Check
                </button>
            </div>

            <form id="check-form" class="space-y-6">
                <div>
                    <label id="input-label" class="block text-sm font-medium text-gray-300 mb-2 uppercase tracking-wider">
                        Enter Suspicious URL
                    </label>
                    <div class="relative">
                        <input
                            type="text"
                            id="payload-input"
                            placeholder="e.g. https://free-gift-claim.example.com/login"
                            class="w-full bg-gray-900 border-2 border-gray-700 focus:border-cyber-primary rounded-xl px-4 py-4 text-white text-lg placeholder-gray-600 focus:outline-none focus:ring-0 transition-colors pl-12"
                            autofocus
                        />
                        <i class="ph ph-magnifying-glass text-2xl text-gray-500 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    </div>
                </div>

                <div id="error-msg" class="hidden p-4 bg-red-900/20 border border-red-500/50 rounded-lg flex items-start gap-3">
                    <i class="ph ph-warning-circle text-red-500 text-2xl flex-shrink-0"></i>
                    <p class="text-red-200" id="error-text"></p>
                </div>

                <button
                    type="submit"
                    id="submit-btn"
                    class="w-full flex items-center justify-center gap-3 px-6 py-4 rounded-xl font-bold text-lg transition-all bg-cyber-primary hover:bg-blue-600 text-white shadow-[0_0_20px_rgba(59,130,246,0.4)] disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <i id="btn-icon" class="ph ph-magnifying-glass text-2xl"></i>
                    <span id="btn-text">Check URL Security</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let activeTab = 'url';
        
        const tabUrl = document.getElementById('tab-url');
        const tabUpi = document.getElementById('tab-upi');
        const inputLabel = document.getElementById('input-label');
        const payloadInput = document.getElementById('payload-input');
        const submitBtn = document.getElementById('submit-btn');
        const btnIcon = document.getElementById('btn-icon');
        const btnText = document.getElementById('btn-text');
        const errorMsg = document.getElementById('error-msg');
        const errorText = document.getElementById('error-text');
        const form = document.getElementById('check-form');

        function switchTab(tab) {
            activeTab = tab;
            payloadInput.value = '';
            errorMsg.classList.add('hidden');
            
            if (tab === 'url') {
                tabUrl.className = 'flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-cyber-primary border-cyber-primary bg-blue-900/10';
                tabUpi.className = 'flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-gray-400 border-transparent hover:text-gray-200 hover:bg-gray-800/30';
                inputLabel.innerText = 'Enter Suspicious URL';
                payloadInput.placeholder = 'e.g. https://free-gift-claim.example.com/login';
                submitBtn.className = 'w-full flex items-center justify-center gap-3 px-6 py-4 rounded-xl font-bold text-lg transition-all bg-cyber-primary hover:bg-blue-600 text-white shadow-[0_0_20px_rgba(59,130,246,0.4)] disabled:opacity-50 disabled:cursor-not-allowed';
                btnText.innerText = 'Check URL Security';
            } else {
                tabUpi.className = 'flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-cyber-neon border-cyber-neon bg-cyan-900/10';
                tabUrl.className = 'flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 text-gray-400 border-transparent hover:text-gray-200 hover:bg-gray-800/30';
                inputLabel.innerText = 'Enter Suspicious UPI ID';
                payloadInput.placeholder = 'e.g. 9423663923@ibl';
                submitBtn.className = 'w-full flex items-center justify-center gap-3 px-6 py-4 rounded-xl font-bold text-lg transition-all bg-cyber-neon hover:bg-cyan-500 text-black shadow-[0_0_20px_rgba(0,255,255,0.4)] disabled:opacity-50 disabled:cursor-not-allowed';
                btnText.innerText = 'Check UPI ID Structure';
            }
        }

        tabUrl.addEventListener('click', () => switchTab('url'));
        tabUpi.addEventListener('click', () => switchTab('upi'));

        payloadInput.addEventListener('input', () => {
            submitBtn.disabled = payloadInput.value.trim().length === 0;
        });
        
        // Initial state
        submitBtn.disabled = true;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = payloadInput.value.trim();
            if (!payload) return;

            errorMsg.classList.add('hidden');
            submitBtn.disabled = true;
            btnIcon.className = 'ph ph-spinner-gap text-2xl animate-spin';
            btnText.innerText = 'Executing Analysis...';

            try {
                const forcedType = activeTab === 'upi' ? 'upi_id_only' : 'url';
                
                const response = await fetch('api/scan.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        payload: payload,
                        input_type: 'manual_string',
                        forced_type: forcedType
                    })
                });
                
                const data = await response.json();
                
                if (data.scan_id) {
                    window.location.href = `report.php?id=${data.scan_id}`;
                } else {
                    errorMsg.classList.remove('hidden');
                    errorText.innerText = data.error || 'Failed to analyze. Server returned an error.';
                }
            } catch (err) {
                console.error(err);
                errorMsg.classList.remove('hidden');
                errorText.innerText = 'Failed to analyze. Backend server might be down.';
            } finally {
                submitBtn.disabled = false;
                btnIcon.className = 'ph ph-magnifying-glass text-2xl';
                btnText.innerText = activeTab === 'url' ? 'Check URL Security' : 'Check UPI ID Structure';
            }
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
