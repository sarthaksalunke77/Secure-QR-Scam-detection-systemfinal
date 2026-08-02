<?php
// alerts.php
include 'includes/light-header.php'; 
?>
<div class="p-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-gray-800 tracking-tight">Real-time Alerts</h1>
            <p class="text-sm text-gray-500 mt-1">Live feed of suspicious and dangerous scans</p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase border-b border-gray-100">
                    <th class="py-4 px-6 font-semibold">Target / URL</th>
                    <th class="py-4 px-6 font-semibold">Trust Score</th>
                    <th class="py-4 px-6 font-semibold">Risk Level</th>
                    <th class="py-4 px-6 font-semibold text-right">Time Detected</th>
                </tr>
            </thead>
            <tbody id="alerts-tbody">
                <tr><td colspan="4" class="p-6 text-center text-gray-500">Loading alerts...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    fetch('api/history.php?limit=50')
        .then(res => res.json())
        .then(resData => {
            const data = resData.data || [];
            const alerts = data.filter(s => s.risk_level === 'DANGEROUS' || s.risk_level === 'SUSPICIOUS');
            const tbody = document.getElementById('alerts-tbody');
            
            if (alerts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="p-6 text-center text-gray-500 font-medium">No alerts found recently! You are safe.</td></tr>';
                return;
            }
            
            tbody.innerHTML = '';
            alerts.forEach(scan => {
                let colorClass = 'text-orange-500';
                let bgClass = 'bg-orange-100 text-orange-700';
                if(scan.risk_level === 'DANGEROUS') { colorClass = 'text-red-500'; bgClass = 'bg-red-100 text-red-700'; }
                
                const urlStr = scan.final_url || scan.original_payload;
                tbody.innerHTML += `
                    <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-6 text-sm font-bold text-gray-700">${urlStr}</td>
                        <td class="py-4 px-6 text-sm font-bold">
                            <span class="${colorClass}">${scan.trust_score} / 100</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold ${bgClass}">${scan.risk_level}</span>
                        </td>
                        <td class="py-4 px-6 text-xs font-medium text-gray-400 text-right">${scan.timestamp}</td>
                    </tr>
                `;
            });
        });
});
</script>
<?php include 'includes/light-footer.php'; ?>
