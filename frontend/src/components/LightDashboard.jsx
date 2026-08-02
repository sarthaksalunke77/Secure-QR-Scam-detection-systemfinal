import React from 'react';
import { 
  ShieldCheck, AlertTriangle, ShieldAlert, ScanLine, 
  LayoutDashboard, Scan, UploadCloud, History, 
  Bell, FileText, Download, Settings, Info,
  Search, User, ChevronDown, CheckCircle2, Shield,
  ArrowUpRight, BarChart2, Activity, Zap
} from 'lucide-react';
import { 
  LineChart, Line, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer,
  PieChart, Pie, Cell, Legend
} from 'recharts';

// Dummy data for charts
const scanSummaryData = [
  { name: '27 May', safe: 110, suspicious: 50, dangerous: 20 },
  { name: '28 May', safe: 140, suspicious: 50, dangerous: 22 },
  { name: '29 May', safe: 115, suspicious: 70, dangerous: 18 },
  { name: '30 May', safe: 135, suspicious: 50, dangerous: 20 },
  { name: '31 May', safe: 160, suspicious: 65, dangerous: 25 },
  { name: '01 Jun', safe: 135, suspicious: 55, dangerous: 20 },
  { name: '02 Jun', safe: 160, suspicious: 50, dangerous: 22 },
];

const trustScoreData = [
  { name: 'Safe (70-100)', value: 842, color: '#22c55e' },
  { name: 'Suspicious (40-69)', value: 278, color: '#f59e0b' },
  { name: 'Dangerous (0-39)', value: 128, color: '#ef4444' },
];

const recentScans = [
  { id: 1, url: 'paytm.com', score: '85/100', level: 'Safe', time: '2 min ago' },
  { id: 2, url: 'https://amazon.in', score: '78/100', level: 'Safe', time: '5 min ago' },
  { id: 3, url: 'http://bit.ly/xyz123', score: '35/100', level: 'Dangerous', time: '10 min ago' },
  { id: 4, url: 'https://free-gift.com', score: '20/100', level: 'Dangerous', time: '15 min ago' },
  { id: 5, url: 'upi://pay?pa=xyz@upi', score: '60/100', level: 'Suspicious', time: '18 min ago' },
];

const riskyDomains = [
  { domain: 'free-gift.com', level: 'Dangerous', width: '90%', color: 'bg-red-500' },
  { domain: 'bit.ly', level: 'Dangerous', width: '85%', color: 'bg-red-500' },
  { domain: 'claim-prize.in', level: 'Suspicious', width: '60%', color: 'bg-orange-400' },
  { domain: 'tinyurl.com', level: 'Suspicious', width: '55%', color: 'bg-orange-400' },
  { domain: 'secure-login.net', level: 'Suspicious', width: '55%', color: 'bg-orange-400' },
];

const recentAlerts = [
  { id: 1, title: 'Malicious URL Detected', url: 'http://free-gift.com/win', time: '2 min ago', type: 'danger' },
  { id: 2, title: 'Phishing Attempt', url: 'http://secure-login.net/verify', time: '12 min ago', type: 'danger' },
  { id: 3, title: 'Suspicious Redirect', url: 'http://bit.ly/xyz123', time: '18 min ago', type: 'warning' },
];

export default function LightDashboard() {
  return (
    <div className="flex min-h-screen bg-[#f3f4f6] text-gray-800 font-sans absolute inset-0 z-[100] overflow-hidden">
      {/* Sidebar */}
      <aside className="w-[280px] bg-[#0f152a] text-white flex flex-col hidden md:flex h-full shadow-2xl relative z-10">
        <div className="px-6 py-8 flex items-center gap-4 border-b border-gray-800">
          <div className="p-2 border border-blue-500/30 rounded-lg bg-blue-500/10 flex-shrink-0">
            <Shield className="w-8 h-8 text-white" />
          </div>
          <div>
            <h1 className="text-2xl font-bold tracking-tight">FraudEye</h1>
            <p className="text-[11px] text-gray-400 font-medium tracking-wide">Scan Smart, Stay Safe</p>
          </div>
        </div>

        <nav className="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
          <a href="#" className="flex items-center gap-3 px-4 py-3 bg-indigo-600 text-white rounded-lg font-medium shadow-[0_4px_10px_rgba(79,70,229,0.3)]">
            <LayoutDashboard className="w-5 h-5" /> Dashboard
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium">
            <Scan className="w-5 h-5" /> Scan QR Code
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium">
            <UploadCloud className="w-5 h-5" /> Bulk QR Analysis
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium">
            <History className="w-5 h-5" /> History
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium">
            <Bell className="w-5 h-5" /> Real-time Alerts
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium">
            <FileText className="w-5 h-5" /> Reports
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium">
            <Download className="w-5 h-5" /> Export to Excel
          </a>
          
          <div className="pt-6 pb-2 px-4">
            <div className="border-t border-gray-800"></div>
          </div>

          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium">
            <Settings className="w-5 h-5" /> Settings
          </a>
          <a href="#" className="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-white/5 rounded-lg transition-colors font-medium">
            <Info className="w-5 h-5" /> About
          </a>
        </nav>

        {/* Trust Score Guide */}
        <div className="p-6 bg-[#0a0e1c] border-t border-gray-800">
          <h3 className="text-xs font-bold text-gray-400 mb-4 uppercase tracking-wider">Trust Score Guide</h3>
          <ul className="space-y-3 text-sm font-medium">
            <li className="flex items-center gap-3">
              <span className="w-3 h-3 rounded-full bg-green-500"></span>
              <span className="text-gray-300">Safe (70 - 100)</span>
            </li>
            <li className="flex items-center gap-3">
              <span className="w-3 h-3 rounded-full bg-orange-400"></span>
              <span className="text-gray-300">Suspicious (40 - 69)</span>
            </li>
            <li className="flex items-center gap-3">
              <span className="w-3 h-3 rounded-full bg-red-500"></span>
              <span className="text-gray-300">Dangerous (0 - 39)</span>
            </li>
          </ul>
          
          <div className="mt-8 text-xs text-gray-500">
            © 2025 FraudEye<br />All rights reserved.
          </div>
        </div>
      </aside>

      {/* Main Content */}
      <main className="flex-1 flex flex-col h-full overflow-hidden">
        {/* Top Header */}
        <header className="bg-white px-8 py-5 flex items-center justify-between border-b border-gray-200 z-10 shadow-sm flex-shrink-0">
          <div>
            <h1 className="text-2xl font-black text-[#1e293b] tracking-tight uppercase flex items-center gap-3">
               DASHBOARD – FRAUDEYE
            </h1>
            <p className="text-sm font-medium text-gray-500 mt-1">FraudEye Secure QR Scam Detection System – Dashboard Overview</p>
          </div>
          <div className="flex items-center gap-6">
            <button className="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
              <Bell className="w-6 h-6" />
              <span className="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white box-content"></span>
            </button>
            <div className="flex items-center gap-3 border-l border-gray-200 pl-6 cursor-pointer">
              <div className="w-10 h-10 rounded-full bg-[#1e293b] flex items-center justify-center">
                <User className="w-5 h-5 text-white" />
              </div>
              <div className="hidden md:block text-right">
                <p className="text-sm font-bold text-gray-700">Sarthak Salunke</p>
                <p className="text-xs text-gray-500 font-medium">Admin</p>
              </div>
              <ChevronDown className="w-4 h-4 text-gray-400" />
            </div>
          </div>
        </header>

        {/* Dashboard Content */}
        <div className="flex-1 p-8 overflow-y-auto">
          {/* Top KPI Row */}
          <div className="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
            <div className="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-500 flex-shrink-0">
                <LayoutDashboard className="w-6 h-6" />
              </div>
              <div>
                <p className="text-xs text-gray-500 font-bold uppercase tracking-wide">Total Scans</p>
                <h3 className="text-2xl font-black text-gray-800 mt-0.5">1,248</h3>
                <p className="text-[10px] text-green-600 font-bold mt-1 flex items-center">
                  <ArrowUpRight className="w-3 h-3 mr-0.5" /> 18.6% this week
                </p>
              </div>
            </div>

            <div className="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-500 flex-shrink-0">
                <CheckCircle2 className="w-6 h-6" />
              </div>
              <div>
                <p className="text-xs text-gray-500 font-bold uppercase tracking-wide">Safe</p>
                <h3 className="text-2xl font-black text-gray-800 mt-0.5">842</h3>
                <p className="text-[11px] text-green-600 font-bold mt-1">67.5%</p>
              </div>
            </div>

            <div className="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 flex-shrink-0">
                <AlertTriangle className="w-6 h-6" />
              </div>
              <div>
                <p className="text-xs text-gray-500 font-bold uppercase tracking-wide">Suspicious</p>
                <h3 className="text-2xl font-black text-gray-800 mt-0.5">278</h3>
                <p className="text-[11px] text-orange-500 font-bold mt-1">22.3%</p>
              </div>
            </div>

            <div className="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 flex-shrink-0">
                <ShieldAlert className="w-6 h-6" />
              </div>
              <div>
                <p className="text-xs text-gray-500 font-bold uppercase tracking-wide">Dangerous</p>
                <h3 className="text-2xl font-black text-gray-800 mt-0.5">128</h3>
                <p className="text-[11px] text-red-500 font-bold mt-1">10.2%</p>
              </div>
            </div>

            <div className="bg-white rounded-xl p-5 border border-gray-100 shadow-sm flex items-center gap-4">
              <div className="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
                <Activity className="w-6 h-6" />
              </div>
              <div>
                <p className="text-xs text-gray-500 font-bold uppercase tracking-wide">Avg. Trust Score</p>
                <h3 className="text-2xl font-black text-gray-800 mt-0.5">72 / 100</h3>
                <p className="text-[11px] text-green-600 font-bold mt-1">Good</p>
              </div>
            </div>
          </div>

          {/* Middle Row (Charts + List) */}
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
            
            {/* Scan Summary Chart */}
            <div className="bg-white rounded-xl p-6 border border-gray-100 shadow-sm lg:col-span-5 flex flex-col">
              <div className="flex items-center justify-between mb-6">
                <h2 className="text-sm font-bold text-gray-800">Scan Summary</h2>
                <select className="text-xs border border-gray-200 rounded-md px-2 py-1 bg-gray-50 text-gray-600 outline-none">
                  <option>Last 7 Days</option>
                  <option>Last 30 Days</option>
                </select>
              </div>
              <div className="flex-1 h-64 min-h-[250px]">
                <ResponsiveContainer width="100%" height="100%">
                  <LineChart data={scanSummaryData} margin={{ top: 5, right: 10, left: -20, bottom: 0 }}>
                    <CartesianGrid strokeDasharray="3 3" vertical={false} stroke="#f1f5f9" />
                    <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 10, fill: '#64748b' }} dy={10} />
                    <YAxis axisLine={false} tickLine={false} tick={{ fontSize: 10, fill: '#64748b' }} />
                    <Tooltip 
                      contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 12px rgba(0,0,0,0.1)' }}
                      itemStyle={{ fontSize: '12px', fontWeight: 'bold' }}
                      labelStyle={{ fontSize: '11px', color: '#64748b', marginBottom: '4px' }}
                    />
                    <Legend iconType="circle" wrapperStyle={{ fontSize: '11px', paddingTop: '10px' }} />
                    <Line type="monotone" name="Safe" dataKey="safe" stroke="#22c55e" strokeWidth={2} dot={{ r: 3 }} activeDot={{ r: 5 }} />
                    <Line type="monotone" name="Suspicious" dataKey="suspicious" stroke="#f59e0b" strokeWidth={2} dot={{ r: 3 }} activeDot={{ r: 5 }} />
                    <Line type="monotone" name="Dangerous" dataKey="dangerous" stroke="#ef4444" strokeWidth={2} dot={{ r: 3 }} activeDot={{ r: 5 }} />
                  </LineChart>
                </ResponsiveContainer>
              </div>
            </div>

            {/* Trust Score Distribution */}
            <div className="bg-white rounded-xl p-6 border border-gray-100 shadow-sm lg:col-span-3 flex flex-col">
              <h2 className="text-sm font-bold text-gray-800 mb-2">Trust Score Distribution</h2>
              <div className="flex-1 relative flex items-center justify-center h-64 min-h-[250px]">
                <ResponsiveContainer width="100%" height="100%">
                  <PieChart>
                    <Pie
                      data={trustScoreData}
                      cx="50%"
                      cy="50%"
                      innerRadius={60}
                      outerRadius={80}
                      paddingAngle={2}
                      dataKey="value"
                      stroke="none"
                    >
                      {trustScoreData.map((entry, index) => (
                        <Cell key={`cell-${index}`} fill={entry.color} />
                      ))}
                    </Pie>
                    <Tooltip 
                      contentStyle={{ borderRadius: '8px', border: 'none', boxShadow: '0 4px 12px rgba(0,0,0,0.1)' }}
                      itemStyle={{ fontSize: '12px', fontWeight: 'bold' }}
                    />
                  </PieChart>
                </ResponsiveContainer>
                {/* Center text for Donut */}
                <div className="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                  <span className="text-2xl font-black text-gray-800">1,248</span>
                  <span className="text-xs text-gray-500 font-medium">Total</span>
                </div>
              </div>
              {/* Custom Legend */}
              <div className="flex flex-col gap-2 mt-4 ml-4">
                {trustScoreData.map((item, idx) => (
                  <div key={idx} className="flex items-center gap-2">
                    <span className="w-2.5 h-2.5 rounded-full" style={{ backgroundColor: item.color }}></span>
                    <div>
                      <p className="text-[10px] font-bold text-gray-700">{item.name}</p>
                      <p className="text-[10px] text-gray-500">{((item.value / 1248) * 100).toFixed(1)}% ({item.value})</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Recent Scans */}
            <div className="bg-white rounded-xl p-6 border border-gray-100 shadow-sm lg:col-span-4 flex flex-col">
              <div className="flex items-center justify-between mb-4">
                <h2 className="text-sm font-bold text-gray-800">Recent Scans</h2>
                <a href="#" className="text-xs font-bold text-indigo-600 hover:text-indigo-800">View All</a>
              </div>
              <div className="flex-1 overflow-auto">
                <table className="w-full text-left text-sm">
                  <thead>
                    <tr className="text-gray-400 text-xs uppercase border-b border-gray-100">
                      <th className="pb-3 font-semibold">QR / URL</th>
                      <th className="pb-3 font-semibold">Trust Score</th>
                      <th className="pb-3 font-semibold">Risk Level</th>
                      <th className="pb-3 font-semibold text-right">Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    {recentScans.map((scan) => (
                      <tr key={scan.id} className="border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                        <td className="py-3 text-xs font-bold text-gray-700 truncate max-w-[120px]" title={scan.url}>{scan.url}</td>
                        <td className="py-3 text-xs font-bold text-gray-500">
                          <span className={scan.level === 'Safe' ? 'text-green-600' : scan.level === 'Dangerous' ? 'text-red-500' : 'text-orange-500'}>
                            {scan.score}
                          </span>
                        </td>
                        <td className="py-3 text-xs">
                          <span className={`px-2 py-1 rounded text-[10px] font-bold ${
                            scan.level === 'Safe' ? 'bg-green-100 text-green-700' : 
                            scan.level === 'Dangerous' ? 'bg-red-100 text-red-700' : 
                            'bg-orange-100 text-orange-700'
                          }`}>
                            {scan.level}
                          </span>
                        </td>
                        <td className="py-3 text-[10px] font-medium text-gray-400 text-right">{scan.time}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
              <div className="mt-4 pt-3 border-t border-gray-100 text-right">
                 <a href="#" className="text-[11px] font-bold text-indigo-600 flex items-center justify-end gap-1 hover:text-indigo-800">
                   View All Scans <ArrowUpRight className="w-3 h-3" />
                 </a>
              </div>
            </div>

          </div>

          {/* Bottom Row */}
          <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
            
            {/* Top Risky Domains */}
            <div className="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
              <div className="flex items-center justify-between mb-6">
                <h2 className="text-sm font-bold text-gray-800">Top Risky Domains</h2>
                <select className="text-[10px] border border-gray-200 rounded px-1.5 py-1 bg-gray-50 text-gray-600 outline-none">
                  <option>Last 7 Days</option>
                </select>
              </div>
              <div className="space-y-4">
                <div className="flex text-[10px] text-gray-400 font-bold uppercase mb-2">
                  <div className="w-1/2">Domain</div>
                  <div className="w-1/2 text-right">Risk Level</div>
                </div>
                {riskyDomains.map((domain, idx) => (
                  <div key={idx} className="flex items-center text-xs">
                    <div className="w-2/5 font-bold text-gray-700 truncate pr-2" title={domain.domain}>{domain.domain}</div>
                    <div className="w-3/5 flex items-center justify-between pl-2">
                       <div className="w-1/2 bg-gray-100 h-1.5 rounded-full overflow-hidden mr-2">
                         <div className={`h-full ${domain.color} rounded-full`} style={{ width: domain.width }}></div>
                       </div>
                       <span className={`text-[10px] font-bold ${domain.level === 'Dangerous' ? 'text-red-500' : 'text-orange-500'}`}>
                         {domain.level}
                       </span>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Scan Activity Heatmap */}
            <div className="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
              <h2 className="text-sm font-bold text-gray-800 mb-4">Scan Activity Heatmap</h2>
              <div className="flex gap-2 text-[10px] text-gray-500 font-medium">
                <div className="flex flex-col justify-between py-1 h-[120px]">
                  <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                </div>
                <div className="flex-1 grid grid-cols-10 grid-rows-7 gap-1">
                   {/* Generate 70 fake heatmap boxes */}
                   {Array.from({ length: 70 }).map((_, i) => {
                     const val = Math.random();
                     let bg = 'bg-green-100';
                     if (val > 0.8) bg = 'bg-green-600';
                     else if (val > 0.6) bg = 'bg-green-400';
                     else if (val > 0.3) bg = 'bg-green-200';
                     
                     return <div key={i} className={`w-full h-full rounded-[2px] ${bg}`}></div>
                   })}
                </div>
                <div className="flex flex-col justify-between py-1 items-center h-[120px] pl-2 border-l border-gray-100">
                  <span>0</span><span>25</span><span>50</span><span>75</span><span>100</span>
                </div>
              </div>
              <div className="mt-4 flex justify-between text-[9px] text-gray-400 font-bold px-8">
                 <span>12 AM</span><span>4 AM</span><span>8 AM</span><span>12 PM</span><span>4 PM</span><span>8 PM</span>
              </div>
            </div>

            {/* Recent Alerts */}
            <div className="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
               <div className="flex items-center justify-between mb-4">
                <h2 className="text-sm font-bold text-gray-800">Recent Alerts</h2>
                <a href="#" className="text-xs font-bold text-indigo-600 hover:text-indigo-800">View All</a>
              </div>
              <div className="space-y-4 flex-1">
                {recentAlerts.map((alert) => (
                  <div key={alert.id} className="flex gap-3">
                    <div className="mt-0.5">
                      {alert.type === 'danger' ? (
                        <div className="w-6 h-6 rounded-full bg-red-100 flex items-center justify-center text-red-500">
                           <ShieldAlert className="w-3.5 h-3.5" />
                        </div>
                      ) : (
                        <div className="w-6 h-6 rounded-full bg-orange-100 flex items-center justify-center text-orange-500">
                           <AlertTriangle className="w-3.5 h-3.5" />
                        </div>
                      )}
                    </div>
                    <div className="flex-1">
                      <h4 className="text-xs font-bold text-gray-800">{alert.title}</h4>
                      <p className="text-[10px] text-gray-500 mt-0.5 truncate max-w-[150px]">{alert.url}</p>
                    </div>
                    <div className="text-[10px] font-medium text-gray-400 whitespace-nowrap">
                      {alert.time}
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Quick Actions */}
            <div className="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col">
              <h2 className="text-sm font-bold text-gray-800 mb-4">Quick Actions</h2>
              <div className="space-y-3">
                
                <button className="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-indigo-50 border border-gray-100 rounded-lg transition-colors group text-left">
                  <div className="w-8 h-8 rounded-md bg-gray-200 group-hover:bg-indigo-100 flex items-center justify-center text-gray-500 group-hover:text-indigo-600 transition-colors">
                    <Scan className="w-4 h-4" />
                  </div>
                  <div>
                    <h4 className="text-xs font-bold text-gray-800 group-hover:text-indigo-900">Scan QR Code</h4>
                    <p className="text-[10px] text-gray-500">Scan a new QR code</p>
                  </div>
                </button>

                <button className="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-indigo-50 border border-gray-100 rounded-lg transition-colors group text-left">
                  <div className="w-8 h-8 rounded-md bg-gray-200 group-hover:bg-indigo-100 flex items-center justify-center text-gray-500 group-hover:text-indigo-600 transition-colors">
                    <UploadCloud className="w-4 h-4" />
                  </div>
                  <div>
                    <h4 className="text-xs font-bold text-gray-800 group-hover:text-indigo-900">Bulk QR Analysis</h4>
                    <p className="text-[10px] text-gray-500">Upload multiple QR codes</p>
                  </div>
                </button>

                <button className="w-full flex items-center gap-3 p-3 bg-gray-50 hover:bg-green-50 border border-gray-100 rounded-lg transition-colors group text-left">
                  <div className="w-8 h-8 rounded-md bg-gray-200 group-hover:bg-green-100 flex items-center justify-center text-gray-500 group-hover:text-green-600 transition-colors">
                    <FileText className="w-4 h-4" />
                  </div>
                  <div>
                    <h4 className="text-xs font-bold text-gray-800 group-hover:text-green-900">Export Report</h4>
                    <p className="text-[10px] text-gray-500">Download report as Excel</p>
                  </div>
                </button>

              </div>
            </div>

          </div>

          {/* Footer Info Box */}
          <div className="mt-2 p-4 bg-indigo-50 rounded-xl border border-indigo-100 flex items-start gap-4">
            <div className="p-2 bg-indigo-600 rounded-lg text-white shrink-0 mt-0.5">
              <Shield className="w-5 h-5" />
            </div>
            <p className="text-xs font-medium text-indigo-900 leading-relaxed">
              <strong className="font-black">FraudEye</strong> analyzes QR Codes and URLs using multiple security checks, calculates a Trust Score, classifies the risk level and helps users stay safe from online scams and phishing attacks.
            </p>
          </div>

        </div>
      </main>
    </div>
  );
}
