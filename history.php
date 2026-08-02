<?php include 'includes/header.php'; ?>

<div class="max-w-6xl mx-auto animate-in fade-in duration-500 pb-12">
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Scan History</h1>
            <p class="text-gray-400">Permanent record of all security analysis logs.</p>
        </div>
        <button id="export-btn" class="flex items-center gap-2 px-4 py-2 bg-cyber-bg border border-cyber-border rounded hover:border-cyber-primary transition-colors text-sm text-gray-300">
            <i class="ph ph-download-simple"></i> Export CSV (All Records)
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-4 gap-4 mb-8">
        <div onclick="window.location.href='history.php'" class="glass-panel p-4 rounded-lg text-center cursor-pointer hover:bg-gray-800/50 transition-colors border-b-2 border-transparent hover:border-gray-500">
            <div class="text-xs text-gray-500 uppercase">Total Analyzed</div>
            <div id="stat-total" class="text-2xl font-bold text-white">0</div>
        </div>
        <div onclick="window.location.href='history.php?level=SAFE'" class="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-safe cursor-pointer hover:bg-gray-800/50 transition-colors">
            <div class="text-xs text-gray-500 uppercase">Safe</div>
            <div id="stat-safe" class="text-2xl font-bold text-cyber-safe">0</div>
        </div>
        <div onclick="window.location.href='history.php?level=WARNING'" class="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-warning cursor-pointer hover:bg-gray-800/50 transition-colors">
            <div class="text-xs text-gray-500 uppercase">Warning</div>
            <div id="stat-suspicious" class="text-2xl font-bold text-cyber-warning">0</div>
        </div>
        <div onclick="window.location.href='history.php?level=DANGEROUS'" class="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-danger cursor-pointer hover:bg-gray-800/50 transition-colors">
            <div class="text-xs text-gray-500 uppercase">Dangerous</div>
            <div id="stat-dangerous" class="text-2xl font-bold text-cyber-danger">0</div>
        </div>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input 
            type="text" 
            id="search-input"
            placeholder="Search by URL or payload..." 
            class="w-full bg-gray-900/50 border border-cyber-border text-white text-sm rounded-lg focus:ring-cyber-primary focus:border-cyber-primary block p-2.5"
        />
    </div>

    <!-- Data Table -->
    <div class="glass-panel rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-900/50 border-b border-cyber-border">
                        <th class="p-4 text-sm font-semibold text-gray-400">ID</th>
                        <th class="p-4 text-sm font-semibold text-gray-400">Date</th>
                        <th class="p-4 text-sm font-semibold text-gray-400">Type</th>
                        <th class="p-4 text-sm font-semibold text-gray-400">Destination</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-center">Risk</th>
                        <th class="p-4 text-sm font-semibold text-gray-400 text-center">Trust</th>
                        <th class="p-4 text-sm font-semibold text-gray-400">Verdict</th>
                    </tr>
                </thead>
                <tbody id="history-tbody">
                    <tr><td colspan="7" class="p-8 text-center text-gray-500">Loading history...</td></tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-between items-center p-4 bg-gray-900/50 border-t border-cyber-border">
            <span id="pagination-info" class="text-sm text-gray-400">
                Showing 0 to 0 of 0 records
            </span>
            <div class="flex gap-2">
                <button 
                    id="prev-btn"
                    disabled
                    class="px-3 py-1 bg-cyber-bg border border-cyber-border rounded text-sm text-gray-300 hover:border-cyber-primary disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Previous
                </button>
                <button 
                    id="next-btn"
                    disabled
                    class="px-3 py-1 bg-cyber-bg border border-cyber-border rounded text-sm text-gray-300 hover:border-cyber-primary disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentPage = 1;
    const limit = 50;
    let totalRecords = 0;
    let currentData = [];

    const searchInput = document.getElementById('search-input');
    const tbody = document.getElementById('history-tbody');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const paginationInfo = document.getElementById('pagination-info');
    const exportBtn = document.getElementById('export-btn');

    async function fetchStats() {
        try {
            const res = await fetch('api/stats.php');
            const stats = await res.json();
            document.getElementById('stat-total').innerText = stats.total || 0;
            document.getElementById('stat-safe').innerText = stats.safe || 0;
            document.getElementById('stat-suspicious').innerText = stats.suspicious || 0;
            document.getElementById('stat-dangerous').innerText = stats.dangerous || 0;
        } catch (e) {
            console.error("Stats Error:", e);
        }
    }

    async function fetchHistory() {
        tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-500">Loading history...</td></tr>';
        const search = encodeURIComponent(searchInput.value.trim());
        const urlParams = new URLSearchParams(window.location.search);
        const level = urlParams.get('level') || '';
        
        try {
            const res = await fetch(`api/history.php?page=${currentPage}&limit=${limit}&search=${search}&level=${encodeURIComponent(level)}`);
            const data = await res.json();
            currentData = data.data;
            totalRecords = data.total;
            
            renderTable();
            updatePagination();
        } catch (e) {
            console.error("History Error:", e);
            tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-red-500">Error loading history</td></tr>';
        }
    }

    function renderTable() {
        if (currentData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-500">No scans found.</td></tr>';
            return;
        }

        tbody.innerHTML = currentData.map(scan => {
            const date = new Date(scan.timestamp).toLocaleString();
            const riskColor = scan.risk_score > 60 ? 'text-red-400' : (scan.risk_score > 30 ? 'text-yellow-400' : 'text-green-400');
            const verdictBadge = scan.risk_level === 'SAFE' ? 'bg-green-500/20 text-green-400' : 
                                ((scan.risk_level === 'WARNING' || scan.risk_level === 'SUSPICIOUS') ? 'bg-yellow-500/20 text-yellow-400' : 
                                'bg-red-500/20 text-red-400');
            const verdictLabel = scan.risk_level === 'SUSPICIOUS' ? 'WARNING' : scan.risk_level;

            return `
                <tr class="border-b border-cyber-border/50 hover:bg-gray-800/30 transition-colors cursor-pointer" onclick="window.location.href='report.php?id=${scan.scan_id}'">
                    <td class="p-4 text-gray-500 font-mono text-sm">#${scan.scan_id}</td>
                    <td class="p-4 text-gray-300 text-sm">${date}</td>
                    <td class="p-4 text-cyber-neon text-sm uppercase">${scan.payload_type}</td>
                    <td class="p-4 text-gray-300 max-w-xs truncate" title="${scan.original_payload}">${scan.original_payload}</td>
                    <td class="p-4 text-gray-300 font-mono text-center">
                        <span class="${riskColor}">${scan.risk_score}</span><span class="text-gray-600 text-xs font-normal">/100</span>
                    </td>
                    <td class="p-4 text-gray-300 font-mono text-center">
                        <span class="text-white">${scan.trust_score}</span><span class="text-gray-600 text-xs font-normal">/100</span>
                    </td>
                    <td class="p-4">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${verdictBadge}">
                            ${verdictLabel}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function updatePagination() {
        const start = Math.min((currentPage - 1) * limit + 1, totalRecords);
        const end = Math.min(currentPage * limit, totalRecords);
        
        if (totalRecords === 0) {
            paginationInfo.innerText = "Showing 0 to 0 of 0 records";
        } else {
            paginationInfo.innerText = `Showing ${start} to ${end} of ${totalRecords} records`;
        }

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage * limit >= totalRecords;
    }

    searchInput.addEventListener('input', () => {
        currentPage = 1;
        fetchHistory();
    });

    prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            fetchHistory();
        }
    });

    nextBtn.addEventListener('click', () => {
        if (currentPage * limit < totalRecords) {
            currentPage++;
            fetchHistory();
        }
    });

    exportBtn.addEventListener('click', async () => {
        if (totalRecords === 0 && currentData.length === 0) return;
        
        exportBtn.disabled = true;
        const originalText = exportBtn.innerHTML;
        exportBtn.innerHTML = '<i class="ph ph-spinner-gap animate-spin"></i> Exporting...';
        
        try {
            const fetchLimit = totalRecords > 0 ? totalRecords : 10000;
            const res = await fetch(`api/history.php?limit=${fetchLimit}&search=${encodeURIComponent(searchInput.value)}`);
            const data = await res.json();
            const exportData = data.data || [];
            
            if (exportData.length === 0) return;
            
            const headers = ['ID', 'Date', 'Type', 'Destination', 'Risk Score', 'Trust Score', 'Verdict'];
            const rows = exportData.map(scan => [
                scan.scan_id,
                `"${new Date(scan.timestamp).toLocaleString()}"`,
                scan.payload_type,
                `"${scan.final_url}"`,
                scan.risk_score,
                scan.trust_score !== null ? scan.trust_score : 'N/A',
                scan.risk_level
            ]);
            
            let csvContent = "data:text/csv;charset=utf-8,\uFEFF" 
                + headers.join(",") + "\n"
                + rows.map(e => e.join(",")).join("\n");
                
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "FraudEye_Scan_History.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        } catch (e) {
            console.error("Export failed", e);
            alert("Failed to export history.");
        } finally {
            exportBtn.disabled = false;
            exportBtn.innerHTML = originalText;
        }
    });

    // Init
    fetchStats();
    fetchHistory();
});
</script>

<?php include 'includes/footer.php'; ?>
