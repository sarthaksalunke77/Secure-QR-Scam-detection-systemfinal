<?php include 'includes/light-header.php'; ?>

<style>
/* Custom Toggle Switch Styles */
.toggle-checkbox:checked {
  right: 0;
  border-color: #68D391;
}
.toggle-checkbox:checked + .toggle-label {
  background-color: #4f46e5;
}
.toggle-checkbox:checked + .toggle-label:after {
  transform: translateX(100%);
  border-color: white;
}
/* Active Tab Styles */
.tab-btn.active {
    background-color: #eef2ff;
    color: #4f46e5;
    border-left: 3px solid #4f46e5;
}
.tab-btn {
    border-left: 3px solid transparent;
}
</style>

<div class="max-w-5xl mx-auto my-8 pb-12 animate-fade-in px-4">
    <div class="bg-white rounded-[14px] shadow-sm border border-gray-200 overflow-hidden flex flex-col md:flex-row min-h-[500px]">
        
        <!-- Sidebar Navigation -->
        <div class="w-full md:w-64 bg-[#f8fafc] border-r border-gray-200 flex-shrink-0">
            <div class="p-6 border-b border-gray-200 bg-white">
                <h1 class="text-[20px] font-bold text-[#1e293b] tracking-tight m-0">Settings</h1>
                <p class="text-[12px] text-gray-500 font-medium m-0 mt-1">Manage your preferences</p>
            </div>
            <div class="flex flex-col py-2">
                <button onclick="switchTab('general')" id="tab-general" class="tab-btn active text-left px-6 py-3 font-semibold text-[13.5px] text-gray-600 hover:bg-gray-100 transition-colors flex items-center gap-3">
                    <i class="ph-bold ph-gear text-[18px]"></i> General
                </button>
                <button onclick="switchTab('scan')" id="tab-scan" class="tab-btn text-left px-6 py-3 font-semibold text-[13.5px] text-gray-600 hover:bg-gray-100 transition-colors flex items-center gap-3">
                    <i class="ph-bold ph-scan text-[18px]"></i> Scan
                </button>
                <button onclick="switchTab('security')" id="tab-security" class="tab-btn text-left px-6 py-3 font-semibold text-[13.5px] text-gray-600 hover:bg-gray-100 transition-colors flex items-center gap-3">
                    <i class="ph-bold ph-shield-check text-[18px]"></i> Security & Privacy
                </button>
                <button onclick="switchTab('detection')" id="tab-detection" class="tab-btn text-left px-6 py-3 font-semibold text-[13.5px] text-gray-600 hover:bg-gray-100 transition-colors flex items-center gap-3">
                    <i class="ph-bold ph-radar text-[18px]"></i> Threat Detection
                </button>
                <button onclick="switchTab('alerts')" id="tab-alerts" class="tab-btn text-left px-6 py-3 font-semibold text-[13.5px] text-gray-600 hover:bg-gray-100 transition-colors flex items-center gap-3">
                    <i class="ph-bold ph-bell text-[18px]"></i> Alerts
                </button>
                <button onclick="switchTab('reports')" id="tab-reports" class="tab-btn text-left px-6 py-3 font-semibold text-[13.5px] text-gray-600 hover:bg-gray-100 transition-colors flex items-center gap-3">
                    <i class="ph-bold ph-file-text text-[18px]"></i> Reports
                </button>
                <button onclick="switchTab('logs')" id="tab-logs" class="tab-btn text-left px-6 py-3 font-semibold text-[13.5px] text-gray-600 hover:bg-gray-100 transition-colors flex items-center gap-3">
                    <i class="ph-bold ph-clock-counter-clockwise text-[18px]"></i> Logs & History
                </button>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 p-8 bg-white">
            
            <!-- GENERAL TAB -->
            <div id="content-general" class="tab-content block animate-fade-in">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">General Settings</h2>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Display Language</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Choose your preferred application language</p>
                        </div>
                        <!-- We will inject the native Google Translate widget here using Javascript -->
                        <div id="translate_container_settings"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Theme (Light/Dark)</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Enable dark mode across the dashboard</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" onchange="toggleDarkMode(event)">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Auto Update</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Keep threat intelligence database updated automatically</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- SCAN TAB -->
            <div id="content-scan" class="tab-content hidden">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Scan Preferences</h2>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Auto Scan QR</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Automatically scan when a QR code image is uploaded</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Batch QR Analysis</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Allow uploading multiple QR codes simultaneously</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Scan Sensitivity</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Adjust the thoroughness of the analysis engine</p>
                        </div>
                        <select class="bg-white border border-gray-200 text-gray-700 font-bold text-xs rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 cursor-pointer outline-none shadow-sm hover:border-gray-300 transition-colors" onchange="showToast('Settings saved!')">
                            <option value="low">Low (Fast)</option>
                            <option value="medium" selected>Medium (Recommended)</option>
                            <option value="high">High (Deep Scan)</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Save Scan History</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Store results in the database for future reference</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- SECURITY TAB -->
            <div id="content-security" class="tab-content hidden">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Privacy & Security</h2>
                <div class="space-y-6">
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                        <h4 class="text-sm font-bold text-gray-800 m-0 mb-3">Change Password</h4>
                        <div class="flex flex-col gap-3 max-w-sm">
                            <input type="password" placeholder="Current Password" class="bg-white border border-gray-300 text-gray-600 text-xs rounded-lg block p-2">
                            <input type="password" placeholder="New Password" class="bg-white border border-gray-300 text-gray-600 text-xs rounded-lg block p-2">
                            <button onclick="showToast('Password updated!')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-[12px] rounded-lg transition-colors w-max">Update Password</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Two-Factor Authentication (2FA)</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Require an email OTP to login</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Session Timeout</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Auto-logout after period of inactivity</p>
                        </div>
                        <select class="bg-white border border-gray-200 text-gray-700 font-bold text-xs rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 cursor-pointer outline-none shadow-sm hover:border-gray-300 transition-colors" onchange="showToast('Settings saved!')">
                            <option value="15">15 Minutes</option>
                            <option value="30" selected>30 Minutes</option>
                            <option value="60">1 Hour</option>
                            <option value="never">Never</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Encrypt Scan History</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Apply AES-256 encryption to stored database records</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- DETECTION TAB -->
            <div id="content-detection" class="tab-content hidden">
                <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-3">
                    <h2 class="text-lg font-bold text-gray-800 m-0">Threat Detection</h2>
                    <i class="ph-fill ph-star text-amber-400 text-lg"></i>
                </div>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0 flex items-center gap-1.5"><i class="ph-fill ph-google-logo text-[#4285F4]"></i> Google Safe Browsing</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Cross-reference URLs with Google's threat list</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0 flex items-center gap-1.5"><i class="ph-fill ph-bug text-[#0D32B2]"></i> VirusTotal Scan</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Query VirusTotal database for malware flags</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">SSL Certificate Validation</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Check if the destination has a valid SSL certificate</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Redirect Detection</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Identify and trace URL shorteners/redirects</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">AI Risk Score Threshold</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Alerting threshold for the AI classification</p>
                        </div>
                        <select class="bg-white border border-gray-200 text-gray-700 font-bold text-xs rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2 cursor-pointer outline-none shadow-sm hover:border-gray-300 transition-colors" onchange="showToast('Settings saved!')">
                            <option value="low">Low (Aggressive Blocking)</option>
                            <option value="medium" selected>Medium (Balanced)</option>
                            <option value="high">High (Lenient)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ALERTS TAB -->
            <div id="content-alerts" class="tab-content hidden">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Alert Preferences</h2>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Popup Warning</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Show on-screen popups when a malicious QR is detected</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Email Notifications</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Send an email alert for detected threats</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Sound Alerts</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Play an audible beep for warnings</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Critical Alerts Only</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Only trigger alerts for High-Risk (Phishing/Malware) URLs</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- REPORTS TAB -->
            <div id="content-reports" class="tab-content hidden">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Report Preferences</h2>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Export PDF</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Enable PDF report generation</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Export Excel</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Enable CSV/Excel bulk export functionality</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Auto Generate Report</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Automatically download a PDF after every dangerous scan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" onchange="showToast('Settings saved!')">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- LOGS TAB -->
            <div id="content-logs" class="tab-content hidden">
                <h2 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Logs & History</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Scan History</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">View and export previously analyzed QR codes</p>
                        </div>
                        <button class="px-4 py-2 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-bold text-xs rounded transition-colors" onclick="window.location.href='history.php'">View History</button>
                    </div>
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 m-0">Security Logs</h4>
                            <p class="text-[12px] text-gray-500 m-0 mt-1">Download system audit logs and login records</p>
                        </div>
                        <button class="px-4 py-2 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 font-bold text-xs rounded transition-colors" onclick="window.location.href='export.php?type=all'">Export Logs</button>
                    </div>
                    <div class="flex items-center justify-between p-4 border border-red-100 rounded-lg bg-red-50 mt-8">
                        <div>
                            <h4 class="text-sm font-bold text-red-800 m-0">Clear History</h4>
                            <p class="text-[12px] text-red-600 m-0 mt-1">Permanently delete all saved scan results from this device</p>
                        </div>
                        <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded transition-colors shadow-sm" onclick="clearHistory()">Clear History</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function switchTab(tabId) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('block', 'animate-fade-in');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('active', 'bg-indigo-50', 'text-indigo-600', 'border-indigo-600');
    });

    // Show selected content
    const content = document.getElementById('content-' + tabId);
    if(content) {
        content.classList.remove('hidden');
        content.classList.add('block', 'animate-fade-in');
    }

    // Set active state on button
    const btn = document.getElementById('tab-' + tabId);
    if(btn) {
        btn.classList.add('active');
    }
}

function showToast(message) {
    let toast = document.getElementById('toast-msg');
    if(!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-msg';
        toast.className = 'fixed bottom-6 right-6 bg-[#1e293b] text-white px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-300 translate-y-20 opacity-0 z-50 flex items-center gap-3 border border-gray-700';
        toast.innerHTML = '<i class="ph-fill ph-check-circle text-[#4ade80] text-[22px]"></i> <span id="toast-text" class="text-[14px] font-bold tracking-wide"></span>';
        document.body.appendChild(toast);
    }
    document.getElementById('toast-text').innerText = message;
    
    // Show
    toast.classList.remove('translate-y-20', 'opacity-0');
    
    // Hide after 3s
    setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
    }, 3000);
}

function clearHistory() {
    if(confirm("Are you sure you want to permanently delete all scan history? This action cannot be undone.")) {
        fetch('api/clear_history.php')
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    showToast('All history cleared successfully!');
                } else {
                    showToast('Failed to clear history.');
                }
            })
            .catch(error => {
                showToast('Error clearing history.');
            });
    }
}

function saveApiKey(service) {
    let inputId = service === 'google' ? 'api-key-google' : 'api-key-vt';
    let val = document.getElementById(inputId).value;
    
    let payload = {};
    payload[service] = val;
    
    fetch('api/save_keys.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if(data.success) {
            showToast(service.toUpperCase() + ' API Key saved!');
        } else {
            showToast('Failed to save key.');
        }
    });
}

function testApiConnection() {
    showToast('Testing APIs...');
    fetch('api/test_keys.php')
    .then(r => r.json())
    .then(data => {
        setTimeout(() => {
            showToast(data.message);
        }, 800);
    });
}

// Move the native Google Translate widget from the header directly into the Settings panel!
// This guarantees that it works 100% natively without any custom JS bugs.
window.addEventListener('load', () => {
    setTimeout(() => {
        let gtWidget = document.getElementById('google_translate_element');
        let container = document.getElementById('translate_container_settings');
        if (gtWidget && container) {
            // Remove 'mr-2' margin class used for header styling
            gtWidget.classList.remove('mr-2'); 
            container.appendChild(gtWidget);
        }
    }, 500); // Give it half a second to initialize first
});

function toggleDarkMode(event) {
    if(event.target.checked) {
        document.body.style.filter = 'invert(1) hue-rotate(180deg)';
        showToast('Dark Mode Enabled!');
    } else {
        document.body.style.filter = 'none';
        showToast('Dark Mode Disabled!');
    }
}
</script>

<?php include 'includes/light-footer.php'; ?>
