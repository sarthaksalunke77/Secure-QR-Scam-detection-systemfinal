<?php include 'includes/header.php'; ?>

<div class="max-w-5xl mx-auto">

    <!-- Hero Section -->
    <div class="text-center mb-12">
        <div class="inline-flex items-center gap-3 mb-4">
            <div class="w-14 h-14 rounded-full bg-blue-600/20 flex items-center justify-center border border-blue-500/30">
                <i class="ph ph-shield-warning text-blue-400 text-3xl"></i>
            </div>
        </div>
        <h1 class="text-4xl font-bold text-white mb-4">Cyber <span class="text-cyber-primary">Awareness</span> Guide</h1>
        <p class="text-gray-400 max-w-2xl mx-auto text-lg">Learn how to identify fake websites, protect your personal data, and stay safe from cybercriminals online.</p>
    </div>

    <!-- Warning Banner -->
    <div class="mb-10 p-5 rounded-2xl border border-red-500/30 bg-red-900/10 flex items-start gap-4">
        <i class="ph ph-warning text-red-400 text-3xl flex-shrink-0 mt-1"></i>
        <div>
            <h3 class="text-red-400 font-bold text-lg mb-1">⚠️ Stay Alert: Cybercrime is Rising</h3>
            <p class="text-gray-300 text-sm">Cybercriminals create fake websites that look almost identical to genuine ones. Their goal is to steal your <strong class="text-white">banking details, passwords, OTPs, card information,</strong> and <strong class="text-white">personal data</strong>. This guide teaches you how to spot them before it's too late.</p>
        </div>
    </div>

    <!-- Steps Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">

        <!-- Step 1 -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-blue-500/50 transition-all group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-blue-600/20 flex items-center justify-center text-blue-400 font-bold text-lg border border-blue-500/30 flex-shrink-0">1</div>
                <h2 class="text-white font-bold text-lg">Check the Website URL</h2>
            </div>
            <p class="text-gray-400 text-sm mb-4">Look for correct spelling of the domain. Small differences can indicate fraud.</p>
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

        <!-- Step 2 -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-green-500/50 transition-all group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-600/20 flex items-center justify-center text-green-400 font-bold text-lg border border-green-500/30 flex-shrink-0">2</div>
                <h2 class="text-white font-bold text-lg">Verify HTTPS</h2>
            </div>
            <p class="text-gray-400 text-sm mb-4">Secure websites use <code class="text-green-400">https://</code> and show a 🔒 padlock icon in your browser.</p>
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
                <p class="text-yellow-400 text-xs mt-2">⚠️ Note: HTTPS does NOT guarantee a site is legitimate — scammers can also use HTTPS.</p>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-yellow-500/50 transition-all group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-yellow-600/20 flex items-center justify-center text-yellow-400 font-bold text-lg border border-yellow-500/30 flex-shrink-0">3</div>
                <h2 class="text-white font-bold text-lg">Search for Reviews</h2>
            </div>
            <p class="text-gray-400 text-sm mb-4">Before trusting a website, search its name followed by "reviews".</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-2">
                <p class="text-sm text-gray-300">Example search:</p>
                <code class="block text-yellow-400 bg-yellow-900/20 px-3 py-2 rounded-lg text-sm font-mono">"ABC Store Reviews"</code>
                <ul class="text-xs text-gray-400 mt-3 space-y-1 list-none">
                    <li class="flex items-center gap-2"><i class="ph ph-star text-yellow-400"></i> Customer experiences & ratings</li>
                    <li class="flex items-center gap-2"><i class="ph ph-megaphone-simple text-red-400"></i> Scam reports & complaints</li>
                    <li class="flex items-center gap-2"><i class="ph ph-x-circle text-red-400"></i> Avoid if many users report fraud</li>
                </ul>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-purple-500/50 transition-all group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-purple-600/20 flex items-center justify-center text-purple-400 font-bold text-lg border border-purple-500/30 flex-shrink-0">4</div>
                <h2 class="text-white font-bold text-lg">Check Contact Information</h2>
            </div>
            <p class="text-gray-400 text-sm mb-4">A legitimate website should always provide real contact details.</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-2">
                <ul class="text-sm text-gray-300 space-y-2">
                    <li class="flex items-center gap-3"><i class="ph ph-phone text-purple-400"></i> Phone Number</li>
                    <li class="flex items-center gap-3"><i class="ph ph-envelope text-purple-400"></i> Email Address</li>
                    <li class="flex items-center gap-3"><i class="ph ph-map-pin text-purple-400"></i> Physical Address</li>
                    <li class="flex items-center gap-3"><i class="ph ph-headset text-purple-400"></i> Customer Support</li>
                </ul>
                <p class="text-red-400 text-xs mt-3">❌ If these are missing or suspicious — avoid the site!</p>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-cyan-500/50 transition-all group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-cyan-600/20 flex items-center justify-center text-cyan-400 font-bold text-lg border border-cyan-500/30 flex-shrink-0">5</div>
                <h2 class="text-white font-bold text-lg">Verify Domain via WHOIS</h2>
            </div>
            <p class="text-gray-400 text-sm mb-4">Check when the domain was registered. A very new domain for a "famous company" is a red flag.</p>
            <div class="bg-gray-900/60 rounded-xl p-4 space-y-2">
                <p class="text-xs text-gray-400 mb-2">WHOIS lookup reveals:</p>
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

        <!-- Step 6 -->
        <div class="glass-panel rounded-2xl p-6 border border-cyber-border hover:border-orange-500/50 transition-all group">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-orange-600/20 flex items-center justify-center text-orange-400 font-bold text-lg border border-orange-500/30 flex-shrink-0">6</div>
                <h2 class="text-white font-bold text-lg">Use Online Security Scanners</h2>
            </div>
            <p class="text-gray-400 text-sm mb-4">Use trusted tools to scan websites for malware, phishing, and SSL issues.</p>
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
    <div class="glass-panel rounded-2xl p-8 mb-10 border border-cyber-border">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="ph ph-book-open text-cyber-primary"></i>
            Key Cyber Security Concepts
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
                    <tr>
                        <td class="py-3 pr-6 text-white font-semibold">Phishing</td>
                        <td class="py-3 text-gray-300">Fake websites or messages used to steal sensitive information</td>
                    </tr>
                    <tr>
                        <td class="py-3 pr-6 text-white font-semibold">HTTPS</td>
                        <td class="py-3 text-gray-300">Secure encrypted communication between browser and website</td>
                    </tr>
                    <tr>
                        <td class="py-3 pr-6 text-white font-semibold">SSL Certificate</td>
                        <td class="py-3 text-gray-300">Encrypts data exchanged with a website to prevent interception</td>
                    </tr>
                    <tr>
                        <td class="py-3 pr-6 text-white font-semibold">WHOIS</td>
                        <td class="py-3 text-gray-300">Shows domain registration details like owner and creation date</td>
                    </tr>
                    <tr>
                        <td class="py-3 pr-6 text-white font-semibold">Virus Scan</td>
                        <td class="py-3 text-gray-300">Detects malicious code or content within websites or files</td>
                    </tr>
                    <tr>
                        <td class="py-3 pr-6 text-white font-semibold">Quishing</td>
                        <td class="py-3 text-gray-300">Phishing attacks carried out via malicious QR codes</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Advantages & Limitations -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="glass-panel rounded-2xl p-6 border border-green-500/20">
            <h3 class="text-green-400 font-bold text-lg mb-4 flex items-center gap-2">
                <i class="ph ph-check-circle"></i> Advantages
            </h3>
            <ul class="space-y-2 text-sm text-gray-300">
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Protects personal & financial information</li>
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Prevents online banking fraud</li>
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Reduces the risk of identity theft</li>
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Helps detect fake shopping websites</li>
                <li class="flex items-start gap-2"><i class="ph ph-check text-green-400 mt-0.5 flex-shrink-0"></i> Improves safe internet browsing habits</li>
            </ul>
        </div>
        <div class="glass-panel rounded-2xl p-6 border border-red-500/20">
            <h3 class="text-red-400 font-bold text-lg mb-4 flex items-center gap-2">
                <i class="ph ph-x-circle"></i> Limitations
            </h3>
            <ul class="space-y-2 text-sm text-gray-300">
                <li class="flex items-start gap-2"><i class="ph ph-x text-red-400 mt-0.5 flex-shrink-0"></i> HTTPS alone does not guarantee legitimacy</li>
                <li class="flex items-start gap-2"><i class="ph ph-x text-red-400 mt-0.5 flex-shrink-0"></i> Some scam websites may have valid SSL certificates</li>
                <li class="flex items-start gap-2"><i class="ph ph-x text-red-400 mt-0.5 flex-shrink-0"></i> Fake reviews can sometimes be posted online</li>
                <li class="flex items-start gap-2"><i class="ph ph-x text-red-400 mt-0.5 flex-shrink-0"></i> WHOIS info may be hidden by privacy protection</li>
            </ul>
        </div>
    </div>

    <!-- Practical Applications -->
    <div class="glass-panel rounded-2xl p-8 mb-10 border border-cyber-border">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="ph ph-lightbulb text-yellow-400"></i> Practical Applications
        </h2>
        <p class="text-gray-400 text-sm mb-5">Apply these practices when you are:</p>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50">
                <i class="ph ph-shopping-cart text-cyber-primary text-2xl"></i>
                <span class="text-sm text-gray-300">Shopping Online</span>
            </div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50">
                <i class="ph ph-bank text-cyber-primary text-2xl"></i>
                <span class="text-sm text-gray-300">Internet Banking</span>
            </div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50">
                <i class="ph ph-sign-in text-cyber-primary text-2xl"></i>
                <span class="text-sm text-gray-300">Logging Into Websites</span>
            </div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50">
                <i class="ph ph-download-simple text-cyber-primary text-2xl"></i>
                <span class="text-sm text-gray-300">Downloading Software</span>
            </div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50">
                <i class="ph ph-globe text-cyber-primary text-2xl"></i>
                <span class="text-sm text-gray-300">Visiting Unknown Sites</span>
            </div>
            <div class="flex items-center gap-3 bg-gray-800/50 rounded-xl p-3 border border-gray-700/50">
                <i class="ph ph-envelope-open text-cyber-primary text-2xl"></i>
                <span class="text-sm text-gray-300">Links via Email/SMS</span>
            </div>
        </div>
    </div>

    <!-- Viva Questions -->
    <div class="glass-panel rounded-2xl p-8 mb-10 border border-yellow-500/20">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="ph ph-question text-yellow-400"></i> Interview / Viva Questions
        </h2>
        <ol class="space-y-3 text-sm text-gray-300 list-none">
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30">
                <span class="text-yellow-400 font-bold flex-shrink-0">Q1.</span> What is a phishing website?
            </li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30">
                <span class="text-yellow-400 font-bold flex-shrink-0">Q2.</span> Why is HTTPS important?
            </li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30">
                <span class="text-yellow-400 font-bold flex-shrink-0">Q3.</span> What is an SSL certificate?
            </li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30">
                <span class="text-yellow-400 font-bold flex-shrink-0">Q4.</span> What information can WHOIS provide?
            </li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30">
                <span class="text-yellow-400 font-bold flex-shrink-0">Q5.</span> How can VirusTotal help identify malicious websites?
            </li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30">
                <span class="text-yellow-400 font-bold flex-shrink-0">Q6.</span> Why should you check website reviews?
            </li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30">
                <span class="text-yellow-400 font-bold flex-shrink-0">Q7.</span> Can a website with HTTPS still be fraudulent? Explain.
            </li>
            <li class="flex items-start gap-3 p-3 bg-gray-800/40 rounded-xl border border-gray-700/30">
                <span class="text-yellow-400 font-bold flex-shrink-0">Q8.</span> What are common signs of a fake website?
            </li>
        </ol>
    </div>

    <!-- Key Takeaways -->
    <div class="glass-panel rounded-2xl p-8 mb-10 border border-cyber-primary/30 bg-blue-900/10">
        <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
            <i class="ph ph-flag-checkered text-cyber-primary"></i> Key Takeaways
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20">
                <i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i>
                <p class="text-sm text-gray-300">Fake websites are a common method used by cybercriminals.</p>
            </div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20">
                <i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i>
                <p class="text-sm text-gray-300">Always inspect the website URL carefully for spelling differences.</p>
            </div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20">
                <i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i>
                <p class="text-sm text-gray-300">Check for HTTPS and a valid SSL certificate.</p>
            </div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20">
                <i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i>
                <p class="text-sm text-gray-300">Look for genuine reviews and complete contact information.</p>
            </div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20">
                <i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i>
                <p class="text-sm text-gray-300">Use tools like WHOIS lookup and VirusTotal for verification.</p>
            </div>
            <div class="flex items-start gap-3 p-3 bg-blue-900/20 rounded-xl border border-blue-500/20">
                <i class="ph ph-check-circle text-blue-400 text-xl flex-shrink-0 mt-0.5"></i>
                <p class="text-sm text-gray-300">Never share sensitive info before verifying a site's authenticity.</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="text-center mb-6">
        <p class="text-gray-400 text-sm mb-4">Think a URL or QR code might be suspicious? Let FraudEye analyze it for you!</p>
        <div class="flex justify-center gap-4 flex-wrap">
            <a href="scanner.php" class="inline-flex items-center gap-2 px-6 py-3 bg-cyber-primary hover:bg-blue-500 text-white font-semibold rounded-xl transition-all shadow-lg">
                <i class="ph ph-scan"></i> Scan QR Code
            </a>
            <a href="check.php" class="inline-flex items-center gap-2 px-6 py-3 border border-cyber-primary text-cyber-primary hover:bg-blue-900/30 font-semibold rounded-xl transition-all">
                <i class="ph ph-link"></i> Check a URL
            </a>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>
