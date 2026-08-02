<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$username = $_SESSION['username'] ?? 'User';
$role = $_SESSION['role'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FraudEye - Dashboard Overview</title>
    <!-- We still include style.css for basic things, but we'll use inline Tailwind classes primarily -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Using Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Tailwind CSS Browser Compiler for new utility classes -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        /* Custom scrollbar for sidebar and main content */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #1e293b;
        }
    </style>
</head>
<body class="bg-[#f3f4f6] text-gray-800 font-sans m-0 p-0 overflow-hidden h-screen flex">
    
    <!-- Sidebar -->
    <aside class="w-[280px] bg-[#0f152a] text-white hidden md:flex flex-col h-full shadow-2xl relative z-20 flex-shrink-0">
        <div class="px-6 py-8 flex items-center gap-4 border-b border-gray-800">
            <div class="p-2 border border-blue-500/30 rounded-lg bg-blue-500/10 flex-shrink-0">
                <i class="ph-fill ph-shield-check text-2xl text-white"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight m-0 leading-none">FraudEye</h1>
                <p class="text-[11px] text-gray-400 font-medium tracking-wide m-0 mt-1">Scan Smart, Stay Safe</p>
            </div>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto sidebar-scroll">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 text-white bg-indigo-600 rounded-lg shadow-md shadow-indigo-600/20 font-bold no-underline">
                <i class="ph ph-squares-four text-xl"></i> Home
            </a>
            <a href="scanner.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-scan text-xl"></i> Scan QR Code
            </a>
            
            <a href="manual.php?type=url" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-link text-xl"></i> Check URL
            </a>

            <a href="manual.php?type=upi" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-bank text-xl"></i> Verify UPI ID
            </a>

            <a href="bulk.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-cloud-arrow-up text-xl"></i> Bulk QR Analysis
            </a>
            <a href="history.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-clock-counter-clockwise text-xl"></i> History
            </a>
            <a href="alerts.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-bell text-xl"></i> Real-time Alerts
            </a>
            <a href="reports.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-file-text text-xl"></i> Reports
            </a>

            <a href="export.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-download-simple text-xl"></i> Export to Excel
            </a>
            
            <div class="pt-6 pb-2 px-4">
                <div class="border-t border-gray-800"></div>
            </div>

            <a href="settings.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-gear text-xl"></i> Settings
            </a>
            <a href="about.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium no-underline">
                <i class="ph ph-info text-xl"></i> About
            </a>
        </nav>

        <!-- Trust Score Guide -->
        <div class="p-6 bg-[#0a0e1c] border-t border-gray-800">
            <h3 class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-wider m-0">Trust Score Guide</h3>
            <ul class="space-y-3 text-sm font-medium m-0 p-0 list-none mt-4">
                <li onclick="window.location.href='history.php?level=SAFE'" class="flex items-center gap-3 cursor-pointer hover:bg-white/5 p-2 -mx-2 rounded transition-colors">
                    <span class="w-3 h-3 rounded-full bg-green-500 block"></span>
                    <span class="text-gray-300">Safe (70 - 100)</span>
                </li>
                <li onclick="window.location.href='history.php?level=WARNING'" class="flex items-center gap-3 cursor-pointer hover:bg-white/5 p-2 -mx-2 rounded transition-colors">
                    <span class="w-3 h-3 rounded-full bg-orange-400 block"></span>
                    <span class="text-gray-300">Suspicious (40 - 69)</span>
                </li>
                <li onclick="window.location.href='history.php?level=DANGEROUS'" class="flex items-center gap-3 cursor-pointer hover:bg-white/5 p-2 -mx-2 rounded transition-colors">
                    <span class="w-3 h-3 rounded-full bg-red-500 block"></span>
                    <span class="text-gray-300">Dangerous (0 - 39)</span>
                </li>
            </ul>
            
            <div class="mt-8 text-xs text-gray-500 leading-tight">
                © 2025 FraudEye<br />All rights reserved.
            </div>
        </div>
    </aside>

    <!-- Main Content Container -->
    <main class="flex-1 flex flex-col h-full overflow-hidden relative">
        <!-- Top Header -->
        <header class="bg-white px-6 py-3 flex items-center justify-between border-b border-gray-200 z-10 shadow-sm flex-shrink-0">
            <div>
                <h1 class="text-xl font-black text-[#1e293b] tracking-tight uppercase flex items-center gap-3 m-0 leading-none">
                    FRAUDEYE
                </h1>
                <p class="text-[11px] font-medium text-gray-500 m-0 mt-1">FraudEye Secure QR Scam Detection System</p>
            </div>
            <div class="flex items-center gap-4">
                
                <?php 
                $currentPage = basename($_SERVER['PHP_SELF']);
                $showTranslate = ($currentPage === 'index.php' || $currentPage === 'settings.php');
                ?>
                <!-- Google Translate Widget -->
                <div id="google_translate_element" class="mr-2 <?php echo $showTranslate ? '' : 'hidden !important'; ?>"></div>
                <script type="text/javascript">
                    function googleTranslateElementInit() {
                        new google.translate.TranslateElement({
                            pageLanguage: 'en',
                            includedLanguages: 'en,hi,mr',
                            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                        }, 'google_translate_element');
                    }
                </script>
                <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
                
                <div class="relative">
                    <button onclick="document.getElementById('notif-dropdown').classList.toggle('hidden')" class="relative p-1.5 text-gray-400 hover:text-gray-600 transition-colors bg-transparent border-none cursor-pointer">
                        <i class="ph-fill ph-bell text-xl"></i>
                        <span class="absolute top-1.5 right-1.5 w-1.5 h-1.5 bg-red-500 rounded-full border border-white box-content block"></span>
                    </button>
                    
                    <!-- Notification Dropdown -->
                    <div id="notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-50">
                        <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <h3 class="text-sm font-bold text-gray-800 m-0">Notifications</h3>
                            <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold">3 New</span>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto">
                            <!-- Item 1 -->
                            <a href="alerts.php" class="flex gap-3 p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors no-underline">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-500 flex-shrink-0 mt-0.5">
                                    <i class="ph-fill ph-shield-warning text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-800 m-0">Dangerous URL Detected</h4>
                                    <p class="text-[10px] text-gray-500 m-0 mt-1 line-clamp-1">Someone scanned free-gift.com which is a known phishing site.</p>
                                    <span class="text-[9px] font-bold text-gray-400 mt-2 block">2 mins ago</span>
                                </div>
                            </a>
                            <!-- Item 2 -->
                            <a href="alerts.php" class="flex gap-3 p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors no-underline">
                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-500 flex-shrink-0 mt-0.5">
                                    <i class="ph-fill ph-warning text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-800 m-0">Suspicious Redirect</h4>
                                    <p class="text-[10px] text-gray-500 m-0 mt-1 line-clamp-1">bit.ly/xyz123 was flagged for suspicious redirects.</p>
                                    <span class="text-[9px] font-bold text-gray-400 mt-2 block">18 mins ago</span>
                                </div>
                            </a>
                            <!-- Item 3 -->
                            <a href="alerts.php" class="flex gap-3 p-4 hover:bg-gray-50 transition-colors no-underline">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 flex-shrink-0 mt-0.5">
                                    <i class="ph-fill ph-bell text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-gray-800 m-0">System Update</h4>
                                    <p class="text-[10px] text-gray-500 m-0 mt-1 line-clamp-1">FraudEye threat database was successfully updated.</p>
                                    <span class="text-[9px] font-bold text-gray-400 mt-2 block">1 hour ago</span>
                                </div>
                            </a>
                        </div>
                        <div class="p-3 border-t border-gray-100 text-center bg-gray-50">
                            <a href="alerts.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 no-underline">View all alerts</a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-4 border-l border-gray-200 pl-4">
                    <div class="text-right flex flex-col items-end">
                        <p class="text-xs font-bold text-gray-700 m-0 leading-tight"><?php echo htmlspecialchars($username); ?></p>
                        <a href="logout.php" class="text-[10px] text-red-500 font-bold hover:text-red-700 no-underline mt-0.5">Logout</a>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Scrollable Page Content -->
        <div class="flex-1 overflow-y-auto p-8 relative">
