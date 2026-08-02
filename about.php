<?php 
// about.php
include 'includes/light-header.php'; 
?>
<div class="p-8 max-w-4xl">
    <div class="mb-8">
        <h1 class="text-2xl font-black text-gray-800 tracking-tight">About FraudEye</h1>
        <p class="text-sm text-gray-500 mt-1">Information about the system and its creators.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 flex flex-col md:flex-row gap-10">
        
        <!-- Logo / Icon -->
        <div class="flex-shrink-0">
            <div class="w-32 h-32 rounded-2xl bg-[#0f152a] flex items-center justify-center text-white shadow-xl shadow-indigo-500/10 border border-gray-800">
                <i class="ph-fill ph-shield-check text-6xl text-white"></i>
            </div>
        </div>

        <!-- Info -->
        <div class="flex-1">
            <h2 class="text-2xl font-black text-gray-800 tracking-tight m-0">FraudEye System <span class="text-sm px-2.5 py-1 bg-indigo-100 text-indigo-700 rounded-full font-bold align-middle ml-2">v1.0</span></h2>
            <p class="text-xs text-gray-400 font-bold tracking-wide mt-2 uppercase">Secure QR Scam Detection System</p>
            
            <div class="mt-6 text-sm text-gray-600 leading-relaxed space-y-4">
                <p>
                    <strong>FraudEye</strong> is a robust, intelligent URL and QR Code scanning system designed to detect phishing attempts, malicious domains, and suspicious redirects in real-time.
                </p>
                <p>
                    Built with a powerful Trust Score engine, it analyzes numerous data points such as domain age, SSL validity, known threat databases, and heuristic red flags to instantly classify threats into <span class="text-green-600 font-bold">Safe</span>, <span class="text-orange-500 font-bold">Suspicious</span>, or <span class="text-red-500 font-bold">Dangerous</span>.
                </p>
            </div>

            <hr class="my-8 border-gray-100">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Developed By</h4>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <i class="ph-fill ph-user text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800 m-0">Sarthak Salunke</p>
                            <p class="text-xs text-gray-500 m-0">Lead Developer / Admin</p>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Technologies</h4>
                    <p class="text-sm font-medium text-gray-700 m-0">
                        PHP 8, MySQL, Tailwind CSS, Python (Engine), Chart.js
                    </p>
                </div>
            </div>

            <div class="mt-8 p-4 bg-gray-50 rounded-lg border border-gray-100 text-xs text-gray-500 text-center">
                &copy; <?php echo date('Y'); ?> FraudEye Security. All rights reserved.
            </div>
        </div>
    </div>
</div>
<?php include 'includes/light-footer.php'; ?>
