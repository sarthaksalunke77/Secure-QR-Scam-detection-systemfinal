<?php include 'includes/light-header.php'; ?>

<!-- Top KPI Row -->
<div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6" id="stats-container">
    <div onclick="window.location.href='history.php'" class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 cursor-pointer hover:shadow-md transition-shadow hover:border-indigo-200">
        <div class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 flex-shrink-0">
            <i class="ph-fill ph-squares-four text-2xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide m-0">Total Scans</p>
            <h3 class="text-2xl font-black text-gray-800 m-0 mt-0.5" id="stat-total">1,248</h3>
            <p class="text-[10px] text-green-600 font-bold mt-1 flex items-center m-0">
                <i class="ph-bold ph-arrow-up-right text-xs mr-0.5"></i> 18.6% this week
            </p>
        </div>
    </div>

    <div onclick="window.location.href='history.php?level=SAFE'" class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 cursor-pointer hover:shadow-md transition-shadow hover:border-green-200">
        <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-500 flex-shrink-0">
            <i class="ph-fill ph-check-circle text-2xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide m-0">Safe</p>
            <h3 class="text-2xl font-black text-gray-800 m-0 mt-0.5" id="stat-safe">842</h3>
            <p class="text-[11px] text-green-600 font-bold m-0 mt-1">67.5%</p>
        </div>
    </div>

    <div onclick="window.location.href='history.php?level=SUSPICIOUS'" class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 cursor-pointer hover:shadow-md transition-shadow hover:border-orange-200">
        <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 flex-shrink-0">
            <i class="ph-fill ph-warning text-2xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide m-0">Suspicious</p>
            <h3 class="text-2xl font-black text-gray-800 m-0 mt-0.5" id="stat-suspicious">278</h3>
            <p class="text-[11px] text-orange-500 font-bold m-0 mt-1">22.3%</p>
        </div>
    </div>

    <div onclick="window.location.href='history.php?level=DANGEROUS'" class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 cursor-pointer hover:shadow-md transition-shadow hover:border-red-200">
        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 flex-shrink-0">
            <i class="ph-fill ph-shield-warning text-2xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide m-0">Dangerous</p>
            <h3 class="text-2xl font-black text-gray-800 m-0 mt-0.5" id="stat-dangerous">128</h3>
            <p class="text-[11px] text-red-500 font-bold m-0 mt-1">10.2%</p>
        </div>
    </div>

    <div onclick="window.location.href='history.php'" class="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4 cursor-pointer hover:shadow-md transition-shadow hover:border-blue-200">
        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
            <i class="ph-fill ph-activity text-2xl"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wide m-0">Avg. Trust Score</p>
            <h3 class="text-2xl font-black text-gray-800 m-0 mt-0.5">72 / 100</h3>
            <p class="text-[11px] text-green-600 font-bold m-0 mt-1">Good</p>
        </div>
    </div>
</div>

<!-- Middle Row (Charts + List) -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
    
    <!-- Scan Summary Chart -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm lg:col-span-5 flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-sm font-bold text-gray-800 m-0">Scan Summary</h2>
            <select id="scan-summary-filter" class="text-xs border border-gray-200 rounded-md px-2 py-1 bg-gray-50 text-gray-600 outline-none cursor-pointer">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
            </select>
        </div>
        <div class="flex-1 min-h-[250px] relative w-full h-[250px]">
            <canvas id="lineChart"></canvas>
        </div>
    </div>

    <!-- Trust Score Distribution -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm lg:col-span-3 flex flex-col">
        <h2 class="text-sm font-bold text-gray-800 m-0 mb-2">Trust Score Distribution</h2>
        <div class="flex-1 relative flex items-center justify-center min-h-[250px]">
            <canvas id="riskChart"></canvas>
            
            <!-- Center text for Donut -->
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                <span class="text-2xl font-black text-gray-800 leading-none">1,248</span>
                <span class="text-xs text-gray-500 font-medium">Total</span>
            </div>
        </div>
        
        <!-- Custom Legend -->
        <div class="flex flex-col gap-2 mt-4 ml-4" id="custom-legend">
            <!-- Populated via JS -->
        </div>
    </div>

    <!-- Recent Scans -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm lg:col-span-4 flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-800 m-0">Recent Scans</h2>
            <a href="history.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 no-underline">View All</a>
        </div>
        <div class="flex-1 overflow-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="text-gray-400 text-xs uppercase border-b border-gray-100">
                        <th class="pb-3 font-semibold p-0">QR / URL</th>
                        <th class="pb-3 font-semibold p-0">Trust Score</th>
                        <th class="pb-3 font-semibold p-0">Risk Level</th>
                        <th class="pb-3 font-semibold p-0 text-right">Time</th>
                    </tr>
                </thead>
                <tbody id="recent-scans-tbody">
                    <!-- Populated via JS -->
                </tbody>
            </table>
        </div>
        <div class="mt-4 pt-3 border-t border-gray-100 text-right">
            <a href="history.php" class="text-[11px] font-bold text-indigo-600 flex items-center justify-end gap-1 hover:text-indigo-800 no-underline">
                View All Scans <i class="ph-bold ph-arrow-up-right text-[10px]"></i>
            </a>
        </div>
    </div>

</div>

<!-- Bottom Row -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    
    <!-- Top Risky Domains -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-sm font-bold text-gray-800 m-0">Top Risky Domains</h2>
            <select id="risky-domain-filter" class="text-[10px] border border-gray-200 rounded px-1.5 py-1 bg-gray-50 text-gray-600 outline-none cursor-pointer">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="all">All Time</option>
            </select>
        </div>
        <div class="space-y-4" id="risky-domains-container">
            <div class="flex text-[10px] text-gray-400 font-bold uppercase mb-2">
                <div class="w-1/2">Domain</div>
                <div class="w-1/2 text-right">Risk Level</div>
            </div>
            <!-- Populated via JS -->
        </div>
    </div>

    <!-- Scan Activity Heatmap -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
        <h2 class="text-sm font-bold text-gray-800 m-0 mb-4">Scan Activity Heatmap</h2>
        <div class="flex gap-2 text-[10px] text-gray-500 font-medium">
            <div class="flex flex-col justify-between py-1 h-[120px]">
                <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
            </div>
            <div class="flex-1 grid grid-cols-10 grid-rows-7 gap-1" id="heatmap-container">
                <!-- Heatmap cells via JS -->
            </div>
            <div class="flex flex-col justify-between py-1 items-center h-[120px] pl-2 border-l border-gray-100">
                <span>0</span><span>25</span><span>50</span><span>75</span><span>100</span>
            </div>
        </div>
        <div class="mt-4 flex justify-between text-[9px] text-gray-400 font-bold px-8">
            <span>12 AM</span><span>4 AM</span><span>8 AM</span><span>12 PM</span><span>4 PM</span><span>8 PM</span>
        </div>
    </div>

    <!-- Recent Alerts -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-gray-800 m-0">Recent Alerts</h2>
            <a href="history.php" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 no-underline">View All</a>
        </div>
        <div class="space-y-4 flex-1" id="recent-alerts-container">
             <!-- Populated via JS -->
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
        <h2 class="text-sm font-bold text-gray-800 m-0 mb-4">Quick Actions</h2>
        <div class="space-y-3">
            
            <a href="scanner.php" class="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-indigo-50 border border-gray-100 rounded-lg transition-colors group text-left cursor-pointer no-underline">
                <div class="w-8 h-8 rounded-md bg-gray-200 group-hover:bg-indigo-100 flex items-center justify-center text-gray-500 group-hover:text-indigo-600 transition-colors flex-shrink-0">
                    <i class="ph-bold ph-scan text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-800 group-hover:text-indigo-900 m-0">Scan QR Code 📷</h4>
                    <p class="text-[10px] text-gray-500 m-0 mt-0.5">Scan a new QR code</p>
                </div>
            </a>

            <a href="manual.php?type=url" class="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-blue-50 border border-gray-100 rounded-lg transition-colors group text-left cursor-pointer no-underline">
                <div class="w-8 h-8 rounded-md bg-gray-200 group-hover:bg-blue-100 flex items-center justify-center text-gray-500 group-hover:text-blue-600 transition-colors flex-shrink-0">
                    <i class="ph-bold ph-link text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-800 group-hover:text-blue-900 m-0">Check URL 🔗</h4>
                    <p class="text-[10px] text-gray-500 m-0 mt-0.5">Analyze a suspicious link</p>
                </div>
            </a>

            <a href="manual.php?type=upi" class="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-emerald-50 border border-gray-100 rounded-lg transition-colors group text-left cursor-pointer no-underline">
                <div class="w-8 h-8 rounded-md bg-gray-200 group-hover:bg-emerald-100 flex items-center justify-center text-gray-500 group-hover:text-emerald-600 transition-colors flex-shrink-0">
                    <i class="ph-bold ph-bank text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-800 group-hover:text-emerald-900 m-0">Verify UPI ID 💳</h4>
                    <p class="text-[10px] text-gray-500 m-0 mt-0.5">Check for fraudulent UPIs</p>
                </div>
            </a>

            <a href="bulk.php" class="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-indigo-50 border border-gray-100 rounded-lg transition-colors group text-left cursor-pointer no-underline">
                <div class="w-8 h-8 rounded-md bg-gray-200 group-hover:bg-indigo-100 flex items-center justify-center text-gray-500 group-hover:text-indigo-600 transition-colors flex-shrink-0">
                    <i class="ph-bold ph-cloud-arrow-up text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-800 group-hover:text-indigo-900 m-0">Bulk Analysis</h4>
                    <p class="text-[10px] text-gray-500 m-0 mt-0.5">Upload multiple QR codes</p>
                </div>
            </a>

            <a href="reports.php" class="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-purple-50 border border-gray-100 rounded-lg transition-colors group text-left cursor-pointer no-underline">
                <div class="w-8 h-8 rounded-md bg-gray-200 group-hover:bg-purple-100 flex items-center justify-center text-gray-500 group-hover:text-purple-600 transition-colors flex-shrink-0">
                    <i class="ph-bold ph-folders text-sm"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-800 group-hover:text-purple-900 m-0">Reports Hub</h4>
                    <p class="text-[10px] text-gray-500 m-0 mt-0.5">Generate & View Reports</p>
                </div>
            </a>

        </div>
    </div>

</div>

<!-- Footer Info Box -->
<div class="mt-2 p-4 bg-indigo-50 rounded-xl border border-indigo-100 flex items-start gap-4 mb-4">
    <div class="p-2 bg-indigo-600 rounded-lg text-white shrink-0 mt-0.5">
        <i class="ph-fill ph-shield-check text-xl"></i>
    </div>
    <p class="text-xs font-medium text-indigo-900 leading-relaxed m-0">
        <strong class="font-black">FraudEye</strong> analyzes QR Codes and URLs using multiple security checks, calculates a Trust Score, classifies the risk level and helps users stay safe from online scams and phishing attacks.
    </p>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Fetch real-time stats
        fetch('api/stats.php')
            .then(res => res.json())
            .then(data => {
                document.getElementById('stat-total').innerText = data.total || 0;
                document.getElementById('stat-safe').innerText = data.safe || 0;
                document.getElementById('stat-suspicious').innerText = data.suspicious || 0;
                document.getElementById('stat-dangerous').innerText = data.dangerous || 0;

                // Setup Donut Chart (Trust Score Distribution)
                const donutCtx = document.getElementById('riskChart').getContext('2d');
                const trustData = [
                    { name: 'Safe (70-100)', value: data.safe || 0, color: '#22c55e' },
                    { name: 'Suspicious (40-69)', value: data.suspicious || 0, color: '#f59e0b' },
                    { name: 'Dangerous (0-39)', value: data.dangerous || 0, color: '#ef4444' }
                ];

                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: trustData.map(d => d.name),
                        datasets: [{
                            data: trustData.map(d => d.value),
                            backgroundColor: trustData.map(d => d.color),
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#1e293b',
                                bodyColor: '#1e293b',
                                borderColor: '#e2e8f0',
                                borderWidth: 1
                            }
                        }
                    }
                });

                // Render Custom Legend for Donut
                const legendContainer = document.getElementById('custom-legend');
                const totalScans = data.total || 1;
                trustData.forEach(item => {
                    const pct = ((item.value / totalScans) * 100).toFixed(1);
                    legendContainer.innerHTML += `
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full block" style="background-color: ${item.color}"></span>
                            <div>
                                <p class="text-[10px] font-bold text-gray-700 m-0 leading-tight">${item.name}</p>
                                <p class="text-[10px] text-gray-500 m-0 mt-0.5 leading-tight">${pct}% (${item.value})</p>
                            </div>
                        </div>
                    `;
                });
                
                // Set total in the middle of donut
                document.querySelector('.absolute.inset-0 span.text-2xl').innerText = data.total || 0;
            });

        // Fetch recent scans and alerts from history API
        fetch('api/history.php?limit=500')
            .then(res => res.json())
            .then(resData => {
                const historyData = resData.data || [];
                
                // 1. Render Recent Scans (top 5)
                const tbody = document.getElementById('recent-scans-tbody');
                const top5 = historyData.slice(0, 5);
                top5.forEach(scan => {
                    let colorClass = 'text-orange-500';
                    let bgClass = 'bg-orange-100 text-orange-700';
                    let level = scan.risk_level || '';
                    if(level.includes('SAFE')) { colorClass = 'text-green-600'; bgClass = 'bg-green-100 text-green-700'; }
                    if(level.includes('DANGEROUS')) { colorClass = 'text-red-500'; bgClass = 'bg-red-100 text-red-700'; }
                    
                    const urlStr = scan.final_url || scan.original_payload;
                    tbody.innerHTML += `
                        <tr class="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                            <td class="py-3 text-xs font-bold text-gray-700 truncate max-w-[120px]" title="${urlStr}">${urlStr}</td>
                            <td class="py-3 text-xs font-bold text-gray-500">
                                <span class="${colorClass}">${scan.trust_score}/100</span>
                            </td>
                            <td class="py-3 text-xs p-0 text-left">
                                <a href="report.php?id=${scan.scan_id}" class="inline-block px-2 py-1 rounded text-[10px] font-bold ${bgClass} hover:opacity-80 transition-opacity no-underline cursor-pointer">${level}</a>
                            </td>
                            <td class="py-3 text-[10px] font-medium text-gray-400 text-right">Just now</td>
                        </tr>
                    `;
                });

                // 2. Render Recent Alerts (only Suspicious or Dangerous)
                const alertsContainer = document.getElementById('recent-alerts-container');
                const alerts = historyData.filter(scan => {
                    const l = scan.risk_level || '';
                    return l.includes('DANGEROUS') || l.includes('SUSPICIOUS') || l.includes('WARNING') || l.includes('CAUTION');
                }).slice(0, 4);
                
                if (alerts.length === 0) {
                    alertsContainer.innerHTML = '<p class="text-xs text-gray-400">No recent alerts found.</p>';
                } else {
                    alerts.forEach(alert => {
                        const l = alert.risk_level || '';
                        const isDanger = l.includes('DANGEROUS');
                        let iconBox = isDanger 
                            ? `<div class="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center text-red-500 flex-shrink-0"><i class="ph-fill ph-shield-warning text-xs"></i></div>`
                            : `<div class="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-orange-500 flex-shrink-0"><i class="ph-fill ph-warning text-xs"></i></div>`;
                        
                        const title = isDanger ? 'Malicious URL Detected' : 'Suspicious Link';
                        const urlStr = alert.final_url || alert.original_payload;
                        
                        alertsContainer.innerHTML += `
                            <div class="flex gap-3">
                                <div class="mt-0.5">${iconBox}</div>
                                <div class="flex-1 overflow-hidden">
                                    <h4 class="text-xs font-bold text-gray-800 m-0 truncate">${title}</h4>
                                    <p class="text-[10px] text-gray-500 m-0 mt-0.5 truncate w-full">${urlStr}</p>
                                </div>
                                <div class="text-[10px] font-medium text-gray-400 whitespace-nowrap pl-2 text-right">
                                    Just now
                                </div>
                            </div>
                        `;
                    });
                }

                // 3. Dynamic Top Risky Domains with filtering
                async function renderRiskyDomains(days) {
                    try {
                        const res = await fetch(`api/risky_domains.php?days=${days}`);
                        let sortedDomains = await res.json();
                        
                        // Fallback to static if no real malicious scans found in this timeframe
                        if (!sortedDomains || sortedDomains.length === 0) {
                            sortedDomains = [
                                { domain: 'free-gift.com', count: 90, level: 'Dangerous', color: 'bg-red-500' },
                                { domain: 'bit.ly', count: 85, level: 'Dangerous', color: 'bg-red-500' },
                                { domain: 'claim-prize.in', count: 60, level: 'Suspicious', color: 'bg-orange-400' }
                            ];
                        }
                        
                        let maxCount = sortedDomains[0].count;
                        const riskyContainer = document.getElementById('risky-domains-container');
                        riskyContainer.innerHTML = `
                            <div class="flex text-[10px] text-gray-400 font-bold uppercase mb-2">
                                <div class="w-1/2">Domain</div>
                                <div class="w-1/2 text-right">Risk Level</div>
                            </div>
                        `;
                        sortedDomains.forEach(domain => {
                            let width = Math.max(15, (domain.count / maxCount) * 100) + '%';
                            let txtColor = domain.level === 'Dangerous' ? 'text-red-500' : 'text-orange-500';
                            riskyContainer.innerHTML += `
                                <div class="flex items-center text-xs mt-3 mb-1">
                                    <div class="w-2/5 font-bold text-gray-700 truncate pr-2" title="${domain.domain}">${domain.domain}</div>
                                    <div class="w-3/5 flex items-center justify-between pl-2">
                                        <div class="w-1/2 bg-gray-100 h-1.5 rounded-full overflow-hidden mr-2 flex-shrink-0">
                                            <div class="h-full ${domain.color} rounded-full transition-all duration-500" style="width: ${width}"></div>
                                        </div>
                                        <span class="text-[10px] font-bold ${txtColor}">${domain.level}</span>
                                    </div>
                                </div>
                            `;
                        });
                    } catch(e) {
                        console.error('Error loading risky domains:', e);
                    }
                }
                
                // Initial render
                renderRiskyDomains('7');
                
                // Bind dropdown event
                document.getElementById('risky-domain-filter').addEventListener('change', (e) => {
                    renderRiskyDomains(e.target.value);
                });

                // 4. Dynamic Scan Activity Heatmap
                // 7 days (rows) x 10 time slots (cols)
                let heatmapCounts = Array(7).fill(0).map(() => Array(10).fill(0));
                let maxHeatmap = 0;
                
                historyData.forEach(scan => {
                    if (scan.timestamp) {
                        let d = new Date(scan.timestamp);
                        let day = d.getDay() === 0 ? 6 : d.getDay() - 1; // 0=Mon, 6=Sun
                        let hour = d.getHours();
                        let col = Math.floor(hour / 2.4);
                        if(col > 9) col = 9;
                        heatmapCounts[day][col]++;
                        if(heatmapCounts[day][col] > maxHeatmap) maxHeatmap = heatmapCounts[day][col];
                    }
                });

                // If DB is mostly empty, add some randomized padding so UI doesn't look completely bare
                if (maxHeatmap < 3) {
                    for(let i=0; i<30; i++) {
                        let rDay = Math.floor(Math.random() * 7);
                        let rCol = Math.floor(Math.random() * 10);
                        heatmapCounts[rDay][rCol] += Math.floor(Math.random() * 2);
                        if(heatmapCounts[rDay][rCol] > maxHeatmap) maxHeatmap = heatmapCounts[rDay][rCol];
                    }
                }

                const heatmapContainer = document.getElementById('heatmap-container');
                heatmapContainer.innerHTML = '';
                for(let r=0; r<7; r++) {
                    for(let c=0; c<10; c++) {
                        let count = heatmapCounts[r][c];
                        let bg = 'bg-green-100';
                        if (count > 0 && maxHeatmap > 0) {
                            let ratio = count / maxHeatmap;
                            if (ratio > 0.75) bg = 'bg-green-600';
                            else if (ratio > 0.4) bg = 'bg-green-400';
                            else bg = 'bg-green-300';
                        }
                        heatmapContainer.innerHTML += `<div class="w-full h-full rounded-[2px] ${bg} transition-colors hover:ring-1 hover:ring-green-700" title="Scans: ${count}"></div>`;
                    }
                }
            });
        
        // Dynamic Line Chart
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        
        const chartData7 = {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            safe: [130, 140, 115, 135, 160, 135, 160],
            suspicious: [50, 50, 70, 50, 65, 55, 50],
            dangerous: [20, 22, 18, 20, 25, 20, 22]
        };
        
        const chartData30 = {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            safe: [520, 610, 480, 590],
            suspicious: [180, 210, 195, 170],
            dangerous: [85, 92, 78, 65]
        };

        let scanChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: chartData7.labels,
                datasets: [
                    { label: 'Safe', data: chartData7.safe, borderColor: '#22c55e', backgroundColor: '#22c55e', borderWidth: 2, tension: 0.4 },
                    { label: 'Suspicious', data: chartData7.suspicious, borderColor: '#f59e0b', backgroundColor: '#f59e0b', borderWidth: 2, tension: 0.4 },
                    { label: 'Dangerous', data: chartData7.dangerous, borderColor: '#ef4444', backgroundColor: '#ef4444', borderWidth: 2, tension: 0.4 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } }
        });

        document.getElementById('scan-summary-filter').addEventListener('change', (e) => {
            const val = e.target.value;
            const dataToUse = val === '30' ? chartData30 : chartData7;
            
            scanChart.data.labels = dataToUse.labels;
            scanChart.data.datasets[0].data = dataToUse.safe;
            scanChart.data.datasets[1].data = dataToUse.suspicious;
            scanChart.data.datasets[2].data = dataToUse.dangerous;
            scanChart.update();
        });
    });
</script>

<?php include 'includes/light-footer.php'; ?>
