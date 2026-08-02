<?php 
require_once 'api/db.php';

// Fetch Real DB Stats for the Hub
$totalScans = 0;
$dangerousLinks = 0;
$avgTrustScore = 0;
$safeUrls = 0;
$suspiciousUrls = 0;

try {
    $stmt = $db->query("SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN risk_level = 'DANGEROUS' THEN 1 ELSE 0 END) as dangerous,
        SUM(CASE WHEN risk_level = 'SUSPICIOUS' OR risk_level = 'WARNING' THEN 1 ELSE 0 END) as suspicious,
        SUM(CASE WHEN risk_level = 'SAFE' THEN 1 ELSE 0 END) as safe,
        AVG(trust_score) as avg_trust
    FROM scan_sessions");
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $totalScans = (int)($stats['total'] ?? 0);
    $dangerousLinks = (int)($stats['dangerous'] ?? 0);
    $suspiciousUrls = (int)($stats['suspicious'] ?? 0);
    $safeUrls = (int)($stats['safe'] ?? 0);
    $avgTrustScore = round((float)($stats['avg_trust'] ?? 0));
} catch (PDOException $e) { }

include 'includes/header.php'; 
?>

<div class="max-w-6xl mx-auto my-8 pb-12 animate-in fade-in zoom-in duration-500 px-4">
    
    <!-- Dashboard Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-black text-white tracking-widest uppercase m-0 flex items-center gap-3">
            <i class="ph-fill ph-folders text-indigo-500"></i> REPORTS HUB
        </h1>
        <p class="text-sm font-medium text-gray-400 m-0 mt-2">Generate and download comprehensive summaries of all FraudEye scan activities.</p>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="glass-panel p-4 rounded-xl border border-gray-800 bg-gray-900/40 hover:border-gray-700 transition-colors">
            <div class="flex items-center gap-3 mb-2">
                <i class="ph-fill ph-file-text text-gray-400 text-lg"></i>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide m-0">Total Reports</h4>
            </div>
            <div class="text-2xl font-black text-white m-0">3</div>
        </div>
        <div class="glass-panel p-4 rounded-xl border border-gray-800 bg-gray-900/40 hover:border-gray-700 transition-colors">
            <div class="flex items-center gap-3 mb-2">
                <i class="ph-fill ph-download text-blue-400 text-lg"></i>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide m-0">Downloads</h4>
            </div>
            <div class="text-2xl font-black text-white m-0">128</div>
        </div>
        <div class="glass-panel p-4 rounded-xl border border-gray-800 bg-gray-900/40 hover:border-gray-700 transition-colors">
            <div class="flex items-center gap-3 mb-2">
                <i class="ph-fill ph-shield-check text-indigo-400 text-lg"></i>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide m-0">Total Scans</h4>
            </div>
            <div class="text-2xl font-black text-white m-0"><?php echo number_format($totalScans); ?></div>
        </div>
        <div class="glass-panel p-4 rounded-xl border border-gray-800 bg-gray-900/40 hover:border-gray-700 transition-colors">
            <div class="flex items-center gap-3 mb-2">
                <i class="ph-fill ph-warning-octagon text-red-500 text-lg"></i>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide m-0">Dangerous Links</h4>
            </div>
            <div class="text-2xl font-black text-white m-0"><?php echo number_format($dangerousLinks); ?></div>
        </div>
        <div class="glass-panel p-4 rounded-xl border border-gray-800 bg-gray-900/40 hover:border-gray-700 transition-colors">
            <div class="flex items-center gap-3 mb-2">
                <i class="ph-fill ph-star text-yellow-400 text-lg"></i>
                <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wide m-0">Avg. Trust Score</h4>
            </div>
            <div class="text-2xl font-black text-white m-0"><?php echo $avgTrustScore; ?>/100</div>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="glass-panel p-4 rounded-xl border border-gray-800 bg-gray-900/60 mb-8 flex flex-col md:flex-row gap-4 items-center justify-between">
        <div class="relative w-full md:w-96">
            <i class="ph-bold ph-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-lg"></i>
            <input type="text" id="search-input" oninput="filterReports()" placeholder="Search Reports..." class="w-full bg-black/50 border border-gray-800 rounded-lg py-2.5 pl-11 pr-4 text-sm text-white focus:outline-none focus:border-indigo-500 transition-colors placeholder-gray-600">
        </div>
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase">Filter:</span>
                <select id="format-filter" onchange="filterReports()" class="bg-black/50 border border-gray-800 rounded-lg py-2 px-3 text-sm text-gray-300 focus:outline-none focus:border-indigo-500 cursor-pointer appearance-none">
                    <option value="all">All Formats</option>
                    <option value="pdf">PDF</option>
                    <option value="xlsx">Excel (XLSX)</option>
                    <option value="csv">CSV</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500 uppercase">Sort:</span>
                <select id="sort-filter" onchange="filterReports()" class="bg-black/50 border border-gray-800 rounded-lg py-2 px-3 text-sm text-gray-300 focus:outline-none focus:border-indigo-500 cursor-pointer appearance-none">
                    <option value="newest">Newest</option>
                    <option value="oldest">Oldest</option>
                    <option value="name">Name (A-Z)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Report Cards Grid -->
    <div class="grid grid-cols-1 gap-6" id="reports-container">
        
        <!-- Weekly Threat Summary (PDF) -->
        <div class="glass-panel rounded-xl border border-gray-800 bg-gray-900/40 overflow-hidden hover:border-indigo-500/30 transition-all duration-300 group shadow-lg shadow-black/20">
            <div class="p-6 border-b border-gray-800/50 flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 flex-shrink-0 mt-1 shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                        <i class="ph-fill ph-shield-check text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white m-0 tracking-wide flex items-center gap-3">
                            Weekly Threat Summary 
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/10 text-green-400 border border-green-500/20 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Ready</span>
                        </h3>
                        <p class="text-sm text-gray-400 m-0 mt-1.5 max-w-2xl leading-relaxed">Comprehensive overview of all security scans, malware detections, and overall threat posture analyzed during the last 7 days.</p>
                        <p class="text-[11px] font-medium text-gray-500 m-0 mt-2">Generated By: System Auto-Scheduler</p>
                    </div>
                </div>
            </div>
            <div class="p-6 bg-black/20 grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Metadata -->
                <div class="col-span-3 space-y-4">
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Generated Date</div>
                        <div class="text-sm font-bold text-gray-300"><?php echo date('d M Y, h:i A'); ?></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Format</div>
                        <div class="flex items-center gap-1.5 text-sm font-bold text-indigo-400">
                            <i class="ph-fill ph-file-pdf"></i> PDF
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">File Size</div>
                        <div class="text-sm font-bold text-gray-300">2.8 MB</div>
                    </div>
                </div>
                <!-- Statistics -->
                <div class="col-span-5 border-l border-gray-800/50 pl-6">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">Report Statistics</div>
                    <div class="grid grid-cols-2 gap-y-3 gap-x-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Total Scans</span>
                            <span class="text-xs font-bold text-white"><?php echo number_format($totalScans); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Safe URLs</span>
                            <span class="text-xs font-bold text-green-400"><?php echo number_format($safeUrls); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Suspicious</span>
                            <span class="text-xs font-bold text-orange-400"><?php echo number_format($suspiciousUrls); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Dangerous</span>
                            <span class="text-xs font-bold text-red-500"><?php echo number_format($dangerousLinks); ?></span>
                        </div>
                    </div>
                </div>
                <!-- Actions -->
                <div class="col-span-4 flex flex-col justify-end gap-2 border-l border-gray-800/50 pl-6">
                    <button onclick="openPreview('Weekly Threat Summary')" class="w-full py-2.5 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-xs font-bold transition-colors border border-gray-700 cursor-pointer flex items-center justify-center gap-2">
                        <i class="ph-bold ph-eye text-sm"></i> Preview Report
                    </button>
                    <button onclick="showComingSoon()" class="w-full py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white rounded-lg text-xs font-bold transition-all shadow-[0_0_20px_rgba(99,102,241,0.2)] hover:shadow-[0_0_25px_rgba(99,102,241,0.4)] border border-indigo-400/50 cursor-pointer flex items-center justify-center gap-2">
                        <i class="ph-bold ph-download-simple text-sm"></i> Download PDF
                    </button>
                    <div class="flex gap-2 mt-1">
                        <button onclick="shareReport('Weekly Threat Summary')" class="flex-1 py-2 bg-transparent hover:bg-gray-800 text-gray-400 hover:text-white rounded-lg text-xs font-bold transition-colors border border-transparent hover:border-gray-700 cursor-pointer flex items-center justify-center gap-2">
                            <i class="ph-bold ph-share-network"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Activity Log (Excel) -->
        <div class="glass-panel rounded-xl border border-gray-800 bg-gray-900/40 overflow-hidden hover:border-green-500/30 transition-all duration-300 group shadow-lg shadow-black/20">
            <div class="p-6 border-b border-gray-800/50 flex items-start justify-between">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center justify-center text-green-400 flex-shrink-0 mt-1 shadow-[0_0_15px_rgba(34,197,94,0.1)]">
                        <i class="ph-fill ph-file-xls text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white m-0 tracking-wide flex items-center gap-3">
                            Monthly Activity Log 
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-500/10 text-green-400 border border-green-500/20 flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-green-400"></span> Ready</span>
                        </h3>
                        <p class="text-sm text-gray-400 m-0 mt-1.5 max-w-2xl leading-relaxed">Raw data export containing every single URL and QR code scan performed within the current calendar month, fully formatted for Excel analysis.</p>
                        <p class="text-[11px] font-medium text-gray-500 m-0 mt-2">Generated By: Admin Request</p>
                    </div>
                </div>
            </div>
            <div class="p-6 bg-black/20 grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Metadata -->
                <div class="col-span-3 space-y-4">
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Generated Date</div>
                        <div class="text-sm font-bold text-gray-300"><?php echo date('d M Y, h:i A'); ?></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Format</div>
                        <div class="flex items-center gap-1.5 text-sm font-bold text-green-400">
                            <i class="ph-fill ph-microsoft-excel-logo"></i> XLSX
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">File Size</div>
                        <div class="text-sm font-bold text-gray-300">14.5 MB</div>
                    </div>
                </div>
                <!-- Statistics -->
                <div class="col-span-5 border-l border-gray-800/50 pl-6">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">Report Statistics</div>
                    <div class="grid grid-cols-2 gap-y-3 gap-x-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Total Scans Included</span>
                            <span class="text-xs font-bold text-white"><?php echo number_format($totalScans); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Average Trust Score</span>
                            <span class="text-xs font-bold text-white"><?php echo $avgTrustScore; ?>/100</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Safe Percentage</span>
                            <span class="text-xs font-bold text-green-400"><?php echo $totalScans > 0 ? round(($safeUrls / $totalScans) * 100) : 0; ?>%</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Most Scanned Category</span>
                            <span class="text-xs font-bold text-gray-300">Technology</span>
                        </div>
                    </div>
                </div>
                <!-- Actions -->
                <div class="col-span-4 flex flex-col justify-end gap-2 border-l border-gray-800/50 pl-6">
                    <button onclick="openPreview('Monthly Activity Log')" class="w-full py-2.5 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-xs font-bold transition-colors border border-gray-700 cursor-pointer flex items-center justify-center gap-2">
                        <i class="ph-bold ph-eye text-sm"></i> Preview Data
                    </button>
                    <button onclick="showComingSoon()" class="w-full py-2.5 bg-gradient-to-r from-emerald-600 to-green-500 hover:from-emerald-500 hover:to-green-400 text-white rounded-lg text-xs font-bold transition-all shadow-[0_0_20px_rgba(34,197,94,0.2)] hover:shadow-[0_0_25px_rgba(34,197,94,0.4)] border border-green-400/50 cursor-pointer flex items-center justify-center gap-2">
                        <i class="ph-bold ph-download-simple text-sm"></i> Download XLSX
                    </button>
                    <div class="flex gap-2 mt-1">
                        <button onclick="shareReport('Monthly Activity Log')" class="flex-1 py-2 bg-transparent hover:bg-gray-800 text-gray-400 hover:text-white rounded-lg text-xs font-bold transition-colors border border-transparent hover:border-gray-700 cursor-pointer flex items-center justify-center gap-2">
                            <i class="ph-bold ph-share-network"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dangerous Links Archive (CSV) -->
        <div id="report-card-dangerous" class="glass-panel rounded-xl border border-red-900/50 bg-gray-900/40 overflow-hidden hover:border-red-500/50 transition-all duration-300 group shadow-lg shadow-black/20">
            <div class="p-6 border-b border-gray-800/50 flex items-start justify-between bg-red-950/10">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500 flex-shrink-0 mt-1 shadow-[0_0_15px_rgba(239,68,68,0.1)]">
                        <i class="ph-fill ph-warning-octagon text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white m-0 tracking-wide flex items-center gap-3">
                            Dangerous Links Archive 
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30 flex items-center gap-1.5 animate-pulse"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Critical Archive</span>
                        </h3>
                        <p class="text-sm text-gray-400 m-0 mt-1.5 max-w-2xl leading-relaxed">A high-priority export containing full indicators of compromise (IOCs) for all scanned URLs definitively flagged as Malicious or Phishing.</p>
                        <p class="text-[11px] font-medium text-gray-500 m-0 mt-2">Generated By: Threat Intel Engine</p>
                    </div>
                </div>
            </div>
            <div class="p-6 bg-black/20 grid grid-cols-1 md:grid-cols-12 gap-6">
                <!-- Metadata -->
                <div class="col-span-3 space-y-4">
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Generated Date</div>
                        <div class="text-sm font-bold text-gray-300"><?php echo date('d M Y, h:i A'); ?></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Format</div>
                        <div class="flex items-center gap-1.5 text-sm font-bold text-gray-300">
                            <i class="ph-fill ph-file-csv"></i> CSV
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">File Size</div>
                        <div class="text-sm font-bold text-gray-300">1.2 MB</div>
                    </div>
                </div>
                <!-- Statistics -->
                <div class="col-span-5 border-l border-gray-800/50 pl-6">
                    <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-3">Threat Statistics</div>
                    <div class="grid grid-cols-2 gap-y-3 gap-x-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Total Dangerous URLs</span>
                            <span class="text-xs font-black text-red-500"><?php echo number_format($dangerousLinks); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Malware URLs</span>
                            <span class="text-xs font-bold text-orange-400"><?php echo floor($dangerousLinks * 0.4); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Phishing URLs</span>
                            <span class="text-xs font-bold text-orange-400"><?php echo ceil($dangerousLinks * 0.6); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">Latest Category</span>
                            <span class="text-xs font-bold text-gray-300">Credential Harvesting</span>
                        </div>
                    </div>
                </div>
                <!-- Actions -->
                <div class="col-span-4 flex flex-col justify-end gap-2 border-l border-gray-800/50 pl-6">
                    <button onclick="openPreview('Dangerous Links Archive')" class="w-full py-2.5 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-xs font-bold transition-colors border border-gray-700 cursor-pointer flex items-center justify-center gap-2">
                        <i class="ph-bold ph-eye text-sm"></i> Preview Indicators
                    </button>
                    <button onclick="downloadActualFile('Dangerous_Links_Archive.csv')" class="w-full py-2.5 bg-gradient-to-r from-red-700 to-red-600 hover:from-red-600 hover:to-red-500 text-white rounded-lg text-xs font-bold transition-all shadow-[0_0_20px_rgba(220,38,38,0.2)] hover:shadow-[0_0_25px_rgba(220,38,38,0.4)] border border-red-500/50 cursor-pointer flex items-center justify-center gap-2">
                        <i class="ph-bold ph-download-simple text-sm"></i> Download CSV
                    </button>
                    <div class="flex gap-2 mt-1">
                        <button onclick="deleteReport('report-card-dangerous')" class="flex-1 py-2 bg-transparent hover:bg-red-900/50 text-red-400 hover:text-red-300 rounded-lg text-xs font-bold transition-colors border border-transparent hover:border-red-800 cursor-pointer flex items-center justify-center gap-2">
                            <i class="ph-bold ph-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Custom Hub Footer -->
    <div class="mt-12 pt-6 border-t border-gray-800 flex flex-col md:flex-row items-center justify-between text-[11px] font-medium text-gray-500">
        <div>
            Last Updated: <?php echo date('d M Y, h:i A'); ?>
        </div>
        <div class="flex gap-6 mt-2 md:mt-0">
            <span class="flex items-center gap-1.5"><i class="ph-bold ph-database"></i> Storage Used: 18.5 MB</span>
            <span class="flex items-center gap-1.5"><i class="ph-bold ph-files"></i> Total Reports Generated: 3</span>
        </div>
    </div>
</div>

<script>
function showComingSoon() {
    let toast = document.getElementById('toast-msg');
    if(!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-msg';
        toast.className = 'fixed bottom-6 right-6 bg-[#0a0e1c] text-white px-6 py-4 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] transform transition-all duration-300 translate-y-20 opacity-0 z-50 border border-gray-700 backdrop-blur-xl';
        document.body.appendChild(toast);
    }
    
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-blue-500/20 border border-blue-500/30 flex items-center justify-center flex-shrink-0">
                <i class="ph-bold ph-info text-blue-400 text-lg"></i> 
            </div>
            <div>
                <h4 class="text-sm font-bold text-white m-0">Coming Soon</h4>
                <p class="text-[10px] text-gray-400 m-0 mt-0.5">This feature is in development.</p>
            </div>
        </div>
    `;
    
    // Force reflow for animation
    void toast.offsetWidth;
    toast.classList.remove('translate-y-20', 'opacity-0');
    
    setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
    }, 3000);
}

function deleteReport(cardId) {
    if (!confirm('Are you sure you want to permanently delete this report template?')) return;
    
    const card = document.getElementById(cardId);
    if (card) {
        card.style.transition = 'all 0.5s ease-out';
        card.style.opacity = '0';
        card.style.transform = 'scale(0.95)';
        
        setTimeout(() => {
            card.style.display = 'none';
        }, 500);
        
        // Save to localStorage so it stays deleted on refresh
        localStorage.setItem(cardId + '_deleted', 'true');
    }
    
    let toast = document.getElementById('toast-msg');
    if(!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-msg';
        toast.className = 'fixed bottom-6 right-6 bg-[#0a0e1c] text-white px-6 py-4 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] transform transition-all duration-300 translate-y-20 opacity-0 z-50 border border-gray-700 backdrop-blur-xl';
        document.body.appendChild(toast);
    }
    
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-red-500/20 border border-red-500/30 flex items-center justify-center flex-shrink-0">
                <i class="ph-bold ph-trash text-red-400 text-lg"></i> 
            </div>
            <div>
                <h4 class="text-sm font-bold text-white m-0">Report Deleted</h4>
                <p class="text-[10px] text-gray-400 m-0 mt-0.5">The report has been successfully removed.</p>
            </div>
        </div>
    `;
    
    void toast.offsetWidth;
    toast.classList.remove('translate-y-20', 'opacity-0');
    
    setTimeout(() => {
        toast.classList.add('translate-y-20', 'opacity-0');
    }, 3000);
}

// Check for deleted reports on load
window.addEventListener('DOMContentLoaded', () => {
    const reportIds = ['report-card-dangerous']; // Add others here if they get IDs
    reportIds.forEach(id => {
        if (localStorage.getItem(id + '_deleted') === 'true') {
            const card = document.getElementById(id);
            if (card) card.style.display = 'none';
        }
    });
});

function filterReports() {
    const search = document.getElementById('search-input').value.toLowerCase();
    const format = document.getElementById('format-filter').value.toLowerCase();
    const sort = document.getElementById('sort-filter').value;
    
    const container = document.getElementById('reports-container');
    if (!container) return;
    
    const cards = Array.from(container.children).filter(el => el.classList.contains('glass-panel'));
    
    cards.forEach(card => {
        // Find title
        const titleEl = card.querySelector('h3');
        const titleText = titleEl ? titleEl.innerText.toLowerCase() : '';
        
        // Find format text
        const cardText = card.innerText.toUpperCase();
        let cardFormat = 'all';
        if (cardText.includes('PDF')) cardFormat = 'pdf';
        if (cardText.includes('CSV')) cardFormat = 'csv';
        if (cardText.includes('XLSX')) cardFormat = 'xlsx';
        
        const matchesSearch = titleText.includes(search) || card.innerText.toLowerCase().includes(search);
        const matchesFormat = format === 'all' || format === cardFormat;
        
        // Don't show if deleted
        const isDeleted = card.id && localStorage.getItem(card.id + '_deleted') === 'true';
        
        if (matchesSearch && matchesFormat && !isDeleted) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Sort logic
    if (sort === 'name') {
        cards.sort((a, b) => {
            const tA = a.querySelector('h3') ? a.querySelector('h3').innerText : '';
            const tB = b.querySelector('h3') ? b.querySelector('h3').innerText : '';
            return tA.localeCompare(tB);
        });
    } else if (sort === 'oldest') {
        // Just reverse default since default is newest
        cards.reverse();
    }
    
    // Re-append sorted cards
    cards.forEach(card => container.appendChild(card));
}

function shareReport(title) {
    const shareText = `Check out the ${title} from FraudEye Security Scanner.`;
    const shareUrl = window.location.href;

    if (navigator.share) {
        navigator.share({
            title: title,
            text: shareText,
            url: shareUrl
        }).catch(console.error);
    } else {
        // Fallback to clipboard
        navigator.clipboard.writeText(`${shareText} ${shareUrl}`).then(() => {
            let toast = document.getElementById('toast-msg');
            if(!toast) {
                toast = document.createElement('div');
                toast.id = 'toast-msg';
                toast.className = 'fixed bottom-6 right-6 bg-[#0a0e1c] text-white px-6 py-4 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] transform transition-all duration-300 translate-y-20 opacity-0 z-50 border border-gray-700 backdrop-blur-xl';
                document.body.appendChild(toast);
            }
            
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 border border-green-500/30 flex items-center justify-center flex-shrink-0">
                        <i class="ph-bold ph-check text-green-400 text-lg"></i> 
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white m-0">Link Copied!</h4>
                        <p class="text-[10px] text-gray-400 m-0 mt-0.5">Share link copied to clipboard.</p>
                    </div>
                </div>
            `;
            
            void toast.offsetWidth;
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        });
    }
}

function openPreview(title) {
    let modal = document.getElementById('preview-modal');
    if(!modal) {
        modal = document.createElement('div');
        modal.id = 'preview-modal';
        modal.className = 'fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300';
        modal.innerHTML = `
            <div class="bg-[#0f152a] w-full max-w-4xl max-h-[85vh] rounded-2xl border border-gray-700 shadow-2xl flex flex-col transform scale-95 transition-transform duration-300" id="preview-content">
                <div class="p-6 border-b border-gray-800 flex justify-between items-center bg-[#0a0e1c] rounded-t-2xl">
                    <h2 class="text-xl font-bold text-white m-0 flex items-center gap-3">
                        <i class="ph-fill ph-file-text text-indigo-400"></i> <span id="preview-title">Report Preview</span>
                    </h2>
                    <button onclick="closePreview()" class="text-gray-400 hover:text-white cursor-pointer bg-transparent border-none">
                        <i class="ph-bold ph-x text-2xl"></i>
                    </button>
                </div>
                <div class="p-8 overflow-y-auto flex-1 bg-[#0f152a] rounded-b-2xl">
                    <div class="glass-panel p-6 rounded-xl border border-gray-800 mb-6">
                        <h3 class="text-white font-bold mb-4">Executive Summary</h3>
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">This generated preview shows the structure of the data that will be exported. The actual export will contain all fully expanded data rows for the selected time period.</p>
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div class="bg-black/30 p-4 rounded-lg border border-gray-800">
                                <div class="text-xs text-gray-500 font-bold mb-1">Total Records</div>
                                <div class="text-xl text-white font-black">4,676</div>
                            </div>
                            <div class="bg-black/30 p-4 rounded-lg border border-gray-800">
                                <div class="text-xs text-gray-500 font-bold mb-1">Threat Level</div>
                                <div class="text-xl text-red-400 font-black">Elevated</div>
                            </div>
                            <div class="bg-black/30 p-4 rounded-lg border border-gray-800">
                                <div class="text-xs text-gray-500 font-bold mb-1">Generation Time</div>
                                <div class="text-xl text-white font-black">0.4s</div>
                            </div>
                        </div>
                    </div>
                    <h3 class="text-white font-bold mb-4">Data Preview (Sample)</h3>
                    <div class="overflow-x-auto rounded-lg border border-gray-800">
                        <table class="w-full text-left text-sm text-gray-400">
                            <thead class="bg-gray-900/50 text-xs uppercase text-gray-500">
                                <tr><th class="px-4 py-3">Timestamp</th><th class="px-4 py-3">URL / Target</th><th class="px-4 py-3">Threat Type</th><th class="px-4 py-3">Verdict</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-800">
                                <tr><td class="px-4 py-3">2026-07-13 10:00</td><td class="px-4 py-3">http://secure-login-update.com</td><td class="px-4 py-3 text-orange-400">Phishing</td><td class="px-4 py-3 text-red-500 font-bold">DANGEROUS</td></tr>
                                <tr><td class="px-4 py-3">2026-07-13 09:45</td><td class="px-4 py-3">https://google.com</td><td class="px-4 py-3">None</td><td class="px-4 py-3 text-green-500 font-bold">SAFE</td></tr>
                                <tr><td class="px-4 py-3">2026-07-13 09:12</td><td class="px-4 py-3">http://bit.ly/xyz123</td><td class="px-4 py-3 text-yellow-400">Suspicious Redirect</td><td class="px-4 py-3 text-orange-400 font-bold">WARNING</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
    
    document.getElementById('preview-title').innerText = title + ' Preview';
    
    // Animate in
    void modal.offsetWidth;
    modal.classList.remove('opacity-0', 'pointer-events-none');
    document.getElementById('preview-content').classList.remove('scale-95');
    document.getElementById('preview-content').classList.add('scale-100');
}

function closePreview() {
    let modal = document.getElementById('preview-modal');
    if(modal) {
        modal.classList.add('opacity-0', 'pointer-events-none');
        document.getElementById('preview-content').classList.remove('scale-100');
        document.getElementById('preview-content').classList.add('scale-95');
    }
}

function downloadActualFile(filename) {
    let toast = document.getElementById('toast-msg');
    if(!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-msg';
        toast.className = 'fixed bottom-6 right-6 bg-[#0a0e1c] text-white px-6 py-4 rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.8)] transform transition-all duration-300 translate-y-20 opacity-0 z-50 border border-gray-700 backdrop-blur-xl';
        document.body.appendChild(toast);
    }
    
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center flex-shrink-0">
                <i class="ph-bold ph-spinner-gap animate-spin text-indigo-400 text-lg"></i> 
            </div>
            <div>
                <h4 class="text-sm font-bold text-white m-0">Generating Report</h4>
                <p class="text-[10px] text-gray-400 m-0 mt-0.5">Building ${filename}...</p>
            </div>
        </div>
    `;
    
    // Force reflow
    void toast.offsetWidth;
    toast.classList.remove('translate-y-20', 'opacity-0');
    
    setTimeout(() => {
        // Determine report type based on requested filename
        let reportType = 'all';
        if (filename.includes('Weekly')) reportType = 'weekly';
        if (filename.includes('Monthly')) reportType = 'monthly';
        if (filename.includes('Dangerous')) reportType = 'dangerous';
        
        // Trigger actual backend export download
        window.location.href = 'export.php?type=' + reportType;
        
        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-green-500/20 border border-green-500/30 flex items-center justify-center flex-shrink-0">
                    <i class="ph-bold ph-check text-green-400 text-lg"></i> 
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white m-0">Download Started</h4>
                    <p class="text-[10px] text-gray-400 m-0 mt-0.5">Your report is downloading securely.</p>
                </div>
            </div>
        `;
        
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3500);
        
    }, 1500);
}
</script>

<?php include 'includes/footer.php'; ?>
