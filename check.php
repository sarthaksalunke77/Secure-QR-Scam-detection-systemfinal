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

<!-- ===== CYBER AWARENESS SECTION ===== -->
<div class="max-w-5xl mx-auto mt-14">

    <!-- Section Header -->
    <div class="text-center mb-10">
        <div class="inline-flex items-center gap-3 mb-3">
            <div class="w-12 h-12 rounded-full bg-blue-600/20 flex items-center justify-center border border-blue-500/30">
                <i class="ph ph-shield-warning text-blue-400 text-2xl"></i>
            </div>
        </div>
        <h2 class="text-3xl font-bold text-white mb-3">Cyber <span class="text-cyber-primary">Awareness</span> Guide</h2>
        <p class="text-gray-400 max-w-2xl mx-auto">Learn how to identify fake websites and protect your personal data before you analyze any URL or UPI ID.</p>
    </div>

    <!-- Warning Banner -->
    <div class="mb-8 p-5 rounded-2xl border border-red-500/30 bg-red-900/10 flex items-start gap-4">
        <i class="ph ph-warning text-red-400 text-3xl flex-shrink-0 mt-1"></i>
        <div>
            <h3 class="text-red-400 font-bold text-lg mb-1">⚠️ Stay Alert: Cybercrime is Rising</h3>
            <p class="text-gray-300 text-sm">Cybercriminals create fake websites to steal your <strong class="text-white">banking details, passwords, OTPs, card information,</strong> and <strong class="text-white">personal data</strong>. Use the checker above and follow these tips to stay safe.</p>
        </div>
    </div>

    <!-- Steps Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

        <!-- Step 1: Check URL -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-blue-500/50 transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-blue-600/20 flex items-center justify-center text-blue-400 font-bold text-lg border border-blue-500/30 flex-shrink-0">1</div>
                <h3 class="text-white font-bold text-lg">Check the Website URL</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Look carefully for spelling differences — small changes indicate fraud.</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-3">
                    <span class="w-14 text-xs text-green-400 font-bold flex-shrink-0">✅ REAL</span>
                    <code class="text-green-400 bg-green-900/20 px-3 py-1 rounded-lg text-sm font-mono">amazon.in</code>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-14 text-xs text-red-400 font-bold flex-shrink-0">❌ FAKE</span>
                    <code class="text-red-400 bg-red-900/20 px-3 py-1 rounded-lg text-sm font-mono">amaz0n.in</code>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-14 text-xs text-red-400 font-bold flex-shrink-0">❌ FAKE</span>
                    <code class="text-red-400 bg-red-900/20 px-3 py-1 rounded-lg text-sm font-mono">amazonn.in</code>
                </div>
            </div>
        </div>

        <!-- Step 2: HTTPS -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-green-500/50 transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-600/20 flex items-center justify-center text-green-400 font-bold text-lg border border-green-500/30 flex-shrink-0">2</div>
                <h3 class="text-white font-bold text-lg">Verify HTTPS & Padlock</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Secure websites use <code class="text-green-400">https://</code> and show a 🔒 padlock in the browser.</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-3">
                <div class="flex items-center gap-3">
                    <i class="ph ph-lock-simple text-green-400 text-xl flex-shrink-0"></i>
                    <code class="text-green-400 bg-green-900/20 px-3 py-1 rounded-lg text-sm font-mono">https://</code>
                    <span class="text-gray-400 text-xs">Secure ✅</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="ph ph-lock-simple-open text-red-400 text-xl flex-shrink-0"></i>
                    <code class="text-red-400 bg-red-900/20 px-3 py-1 rounded-lg text-sm font-mono">http://</code>
                    <span class="text-gray-400 text-xs">Not Secure ❌</span>
                </div>
                <p class="text-yellow-400 text-xs mt-1">⚠️ HTTPS does NOT guarantee legitimacy — scammers use it too!</p>
            </div>
        </div>

        <!-- Step 3: Reviews -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-yellow-500/50 transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-yellow-600/20 flex items-center justify-center text-yellow-400 font-bold text-lg border border-yellow-500/30 flex-shrink-0">3</div>
                <h3 class="text-white font-bold text-lg">Search for Reviews</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Search the website name followed by "reviews" to check its reputation.</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-2">
                <code class="block text-yellow-400 bg-yellow-900/20 px-3 py-2 rounded-lg text-sm font-mono">"ABC Store Reviews"</code>
                <ul class="text-xs text-gray-400 mt-3 space-y-1">
                    <li class="flex items-center gap-2"><i class="ph ph-star text-yellow-400"></i> Customer ratings & experiences</li>
                    <li class="flex items-center gap-2"><i class="ph ph-megaphone-simple text-red-400"></i> Scam or fraud complaints</li>
                    <li class="flex items-center gap-2"><i class="ph ph-x-circle text-red-400"></i> Avoid sites with many fraud reports</li>
                </ul>
            </div>
        </div>

        <!-- Step 4: Contact Info -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-purple-500/50 transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-purple-600/20 flex items-center justify-center text-purple-400 font-bold text-lg border border-purple-500/30 flex-shrink-0">4</div>
                <h3 class="text-white font-bold text-lg">Check Contact Information</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Legitimate websites always provide real, verifiable contact details.</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-2">
                <ul class="text-sm text-gray-300 space-y-2">
                    <li class="flex items-center gap-3"><i class="ph ph-phone text-purple-400"></i> Phone Number</li>
                    <li class="flex items-center gap-3"><i class="ph ph-envelope text-purple-400"></i> Email Address</li>
                    <li class="flex items-center gap-3"><i class="ph ph-map-pin text-purple-400"></i> Physical Address</li>
                    <li class="flex items-center gap-3"><i class="ph ph-headset text-purple-400"></i> Customer Support</li>
                </ul>
                <p class="text-red-400 text-xs mt-2">❌ Missing or suspicious details = avoid the site!</p>
            </div>
        </div>

        <!-- Step 5: WHOIS -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-cyan-500/50 transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-cyan-600/20 flex items-center justify-center text-cyan-400 font-bold text-lg border border-cyan-500/30 flex-shrink-0">5</div>
                <h3 class="text-white font-bold text-lg">Verify Domain via WHOIS</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">A very recently registered domain claiming to be a famous brand is a major red flag.</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-2">
                <ul class="text-sm text-gray-300 space-y-2">
                    <li class="flex items-center gap-2"><i class="ph ph-user text-cyan-400"></i> Domain Owner (if public)</li>
                    <li class="flex items-center gap-2"><i class="ph ph-calendar text-cyan-400"></i> Registration & Expiry Date</li>
                    <li class="flex items-center gap-2"><i class="ph ph-globe text-cyan-400"></i> Hosting Information</li>
                </ul>
                <a href="https://who.is" target="_blank" class="mt-3 inline-flex items-center gap-2 text-xs text-cyan-400 hover:text-cyan-300 transition-colors">
                    <i class="ph ph-arrow-square-out"></i> Try WHOIS Lookup →
                </a>
            </div>
        </div>

        <!-- Step 6: Security Scanners -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-orange-500/50 transition-all">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-orange-600/20 flex items-center justify-center text-orange-400 font-bold text-lg border border-orange-500/30 flex-shrink-0">6</div>
                <h3 class="text-white font-bold text-lg">Use Online Security Scanners</h3>
            </div>
            <p class="text-gray-400 text-sm mb-4">Trusted tools can scan websites for malware, phishing, and SSL issues.</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-3">
                <a href="https://www.virustotal.com" target="_blank" class="flex items-center justify-between p-3 bg-orange-900/20 rounded-lg border border-orange-500/20 hover:border-orange-400/50 transition-all">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-virus text-orange-400"></i>
                        <span class="text-sm text-white font-semibold">VirusTotal</span>
                    </div>
                    <i class="ph ph-arrow-square-out text-gray-400 text-sm"></i>
                </a>
                <a href="https://www.ssllabs.com/ssltest/" target="_blank" class="flex items-center justify-between p-3 bg-orange-900/20 rounded-lg border border-orange-500/20 hover:border-orange-400/50 transition-all">
                    <div class="flex items-center gap-2">
                        <i class="ph ph-lock-key text-orange-400"></i>
                        <span class="text-sm text-white font-semibold">SSL Certificate Checker</span>
                    </div>
                    <i class="ph ph-arrow-square-out text-gray-400 text-sm"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- Key Concepts Table -->
    <div class="glass-panel rounded-2xl p-8 mb-8 border border-cyber-border">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="ph ph-book-open text-cyber-primary"></i> Key Cyber Security Concepts
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left text-cyber-primary pb-3 pr-6 font-semibold">Concept</th>
                        <th class="text-left text-gray-400 pb-3 font-semibold">Meaning</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    <tr><td class="py-3 pr-6 text-white font-semibold">Phishing</td><td class="py-3 text-gray-300">Fake websites or messages used to steal sensitive information</td></tr>
                    <tr><td class="py-3 pr-6 text-white font-semibold">HTTPS</td><td class="py-3 text-gray-300">Secure encrypted communication between browser and website</td></tr>
                    <tr><td class="py-3 pr-6 text-white font-semibold">SSL Certificate</td><td class="py-3 text-gray-300">Encrypts data exchanged with a website to prevent interception</td></tr>
                    <tr><td class="py-3 pr-6 text-white font-semibold">WHOIS</td><td class="py-3 text-gray-300">Shows domain registration details like owner and creation date</td></tr>
                    <tr><td class="py-3 pr-6 text-white font-semibold">Virus Scan</td><td class="py-3 text-gray-300">Detects malicious code or content within websites or files</td></tr>
                    <tr><td class="py-3 pr-6 text-white font-semibold">Quishing</td><td class="py-3 text-gray-300">Phishing attacks carried out via malicious QR codes</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Advantages & Limitations -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="glass-panel rounded-2xl p-6 border border-green-500/20">
            <h3 class="text-green-400 font-bold text-lg mb-4 flex items-center gap-2"><i class="ph ph-check-circle"></i> Advantages</h3>
            <ul class="space-y-2 text-sm text-gray-300">
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Protects personal & financial information</li>
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Prevents online banking fraud</li>
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Reduces the risk of identity theft</li>
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Helps detect fake shopping websites</li>
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Improves safe internet browsing habits</li>
            </ul>
        </div>
        <div class="glass-panel rounded-2xl p-6 border border-red-500/20">
            <h3 class="text-red-400 font-bold text-lg mb-4 flex items-center gap-2"><i class="ph ph-x-circle"></i> Limitations</h3>
            <ul class="space-y-2 text-sm text-gray-300">
                <li class="flex items-start gap-2"><i class="ph ph-x text-red-400 mt-0.5 flex-shrink-0"></i> HTTPS alone does not guarantee legitimacy</li>
                <li class="flex items-start gap-2"><i class="ph ph-x text-red-400 mt-0.5 flex-shrink-0"></i> Some scam sites may have valid SSL certificates</li>
                <li class="flex items-start gap-2"><i class="ph ph-x text-red-400 mt-0.5 flex-shrink-0"></i> Fake reviews can sometimes be posted online</li>
                <li class="flex items-start gap-2"><i class="ph ph-x text-red-400 mt-0.5 flex-shrink-0"></i> WHOIS info may be hidden by privacy protection</li>
            </ul>
        </div>
    </div>

    <!-- Practical Applications -->
    <div class="glass-panel rounded-2xl p-8 mb-8 border border-cyber-border">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="ph ph-lightbulb text-yellow-400"></i> Practical Applications
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50"><i class="ph ph-shopping-cart text-cyber-primary text-2xl"></i><span class="text-sm text-gray-300">Shopping Online</span></div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50"><i class="ph ph-bank text-cyber-primary text-2xl"></i><span class="text-sm text-gray-300">Internet Banking</span></div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50"><i class="ph ph-sign-in text-cyber-primary text-2xl"></i><span class="text-sm text-gray-300">Logging Into Websites</span></div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50"><i class="ph ph-download-simple text-cyber-primary text-2xl"></i><span class="text-sm text-gray-300">Downloading Software</span></div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50"><i class="ph ph-globe text-cyber-primary text-2xl"></i><span class="text-sm text-gray-300">Visiting Unknown Sites</span></div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50"><i class="ph ph-envelope-open text-cyber-primary text-2xl"></i><span class="text-sm text-gray-300">Links via Email/SMS</span></div>
        </div>
    </div>

    <!-- Viva Questions -->
    <div class="glass-panel rounded-2xl p-8 mb-8 border border-yellow-500/20">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="ph ph-question text-yellow-400"></i> Interview / Viva Questions
        </h2>
        <ol class="space-y-3 text-sm text-gray-300 list-none">
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30"><span class="text-yellow-400 font-bold flex-shrink-0">Q1.</span> What is a phishing website?</li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30"><span class="text-yellow-400 font-bold flex-shrink-0">Q2.</span> Why is HTTPS important?</li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30"><span class="text-yellow-400 font-bold flex-shrink-0">Q3.</span> What is an SSL certificate?</li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30"><span class="text-yellow-400 font-bold flex-shrink-0">Q4.</span> What information can WHOIS provide?</li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30"><span class="text-yellow-400 font-bold flex-shrink-0">Q5.</span> How can VirusTotal help identify malicious websites?</li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30"><span class="text-yellow-400 font-bold flex-shrink-0">Q6.</span> Why should you check website reviews?</li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30"><span class="text-yellow-400 font-bold flex-shrink-0">Q7.</span> Can a website with HTTPS still be fraudulent? Explain.</li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30"><span class="text-yellow-400 font-bold flex-shrink-0">Q8.</span> What are common signs of a fake website?</li>
        </ol>
    </div>

    <!-- Key Takeaways -->
    <div class="glass-panel rounded-2xl p-8 mb-6 border border-cyber-primary/30 bg-blue-900/10">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="ph ph-flag-checkered text-cyber-primary"></i> Key Takeaways
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20"><i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i><p class="text-sm text-gray-300">Fake websites are a common cybercriminal method.</p></div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20"><i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i><p class="text-sm text-gray-300">Always inspect the URL carefully for spelling differences.</p></div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20"><i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i><p class="text-sm text-gray-300">Check for HTTPS and a valid SSL certificate.</p></div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20"><i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i><p class="text-sm text-gray-300">Look for genuine reviews and complete contact info.</p></div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20"><i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i><p class="text-sm text-gray-300">Use WHOIS lookup and VirusTotal for extra verification.</p></div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20"><i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i><p class="text-sm text-gray-300">Never share sensitive info before verifying site authenticity.</p></div>
        </div>
    </div>

</div>
<!-- ===== END CYBER AWARENESS SECTION ===== -->

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
