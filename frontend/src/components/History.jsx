import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { ShieldCheck, ShieldAlert, AlertTriangle, Download } from 'lucide-react';

export default function ScanHistory() {
  const [history, setHistory] = useState([]);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);
  const [total, setTotal] = useState(0);
  const [stats, setStats] = useState({ total: 0, safe: 0, suspicious: 0, dangerous: 0 });
  const [search, setSearch] = useState('');
  const limit = 50;

  useEffect(() => {
    fetchHistory();
    fetchStats();
  }, [page, search]);

  const fetchHistory = async () => {
    try {
      setLoading(true);
      const res = await axios.get(`http://localhost:3000/api/history?page=${page}&limit=${limit}&search=${search}`);
      setHistory(res.data.data);
      setTotal(res.data.total);
      setLoading(false);
    } catch (e) {
      console.error(e);
      setLoading(false);
    }
  };

  const fetchStats = async () => {
    try {
      const res = await axios.get('http://localhost:3000/api/history/stats');
      setStats(res.data);
    } catch (e) {
      console.error(e);
    }
  };

  const getRiskIcon = (level) => {
    switch(level) {
      case 'DANGEROUS': return <ShieldAlert className="text-red-500 w-5 h-5" />;
      case 'SUSPICIOUS': return <AlertTriangle className="text-yellow-500 w-5 h-5" />;
      default: return <ShieldCheck className="text-green-500 w-5 h-5" />;
    }
  };

  const exportCSV = () => {
    if (history.length === 0) return;
    const headers = ['ID', 'Date', 'Type', 'Destination', 'Risk Score', 'Trust Score', 'Verdict'];
    const rows = history.map(scan => [
      scan.scan_id,
      `"${new Date(scan.timestamp).toLocaleString()}"`,
      scan.payload_type,
      `"${scan.final_url}"`,
      scan.risk_score,
      scan.trust_score !== null ? scan.trust_score : 'N/A',
      scan.risk_level
    ]);
    
    let csvContent = "data:text/csv;charset=utf-8," 
      + headers.join(",") + "\n"
      + rows.map(e => e.join(",")).join("\n");
      
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "FraudEye_Scan_History.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  return (
    <div className="max-w-6xl mx-auto animate-in fade-in duration-500">
      <div className="flex justify-between items-end mb-8">
        <div>
          <h1 className="text-3xl font-bold text-white mb-2">Scan History</h1>
          <p className="text-gray-400">Permanent record of all security analysis logs.</p>
        </div>
        <button onClick={exportCSV} className="flex items-center gap-2 px-4 py-2 bg-cyber-bg border border-cyber-border rounded hover:border-cyber-primary transition-colors text-sm text-gray-300">
          <Download className="w-4 h-4" /> Export CSV (Current Page)
        </button>
      </div>

      <div className="grid grid-cols-4 gap-4 mb-8">
        <div className="glass-panel p-4 rounded-lg text-center">
          <div className="text-xs text-gray-500 uppercase">Total Analyzed</div>
          <div className="text-2xl font-bold text-white">{stats.total}</div>
        </div>
        <div className="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-safe">
          <div className="text-xs text-gray-500 uppercase">Safe</div>
          <div className="text-2xl font-bold text-cyber-safe">{stats.safe}</div>
        </div>
        <div className="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-warning">
          <div className="text-xs text-gray-500 uppercase">Warning</div>
          <div className="text-2xl font-bold text-cyber-warning">{stats.suspicious}</div>
        </div>
        <div className="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-danger">
          <div className="text-xs text-gray-500 uppercase">Dangerous</div>
          <div className="text-2xl font-bold text-cyber-danger">{stats.dangerous}</div>
        </div>
      </div>

      <div className="mb-4">
        <input 
          type="text" 
          placeholder="Search by URL or payload..." 
          value={search}
          onChange={(e) => { setSearch(e.target.value); setPage(1); }}
          className="w-full bg-gray-900/50 border border-cyber-border text-white text-sm rounded-lg focus:ring-cyber-primary focus:border-cyber-primary block p-2.5"
        />
      </div>

      <div className="glass-panel rounded-xl overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left border-collapse">
            <thead>
              <tr className="bg-gray-900/50 border-b border-cyber-border">
                <th className="p-4 text-sm font-semibold text-gray-400">ID</th>
                <th className="p-4 text-sm font-semibold text-gray-400">Date</th>
                <th className="p-4 text-sm font-semibold text-gray-400">Type</th>
                <th className="p-4 text-sm font-semibold text-gray-400">Destination</th>
                <th className="p-4 text-sm font-semibold text-gray-400">Risk</th>
                <th className="p-4 text-sm font-semibold text-gray-400">Trust</th>
                <th className="p-4 text-sm font-semibold text-gray-400">Verdict</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr><td colSpan="6" className="p-8 text-center text-gray-500">Loading history...</td></tr>
              ) : history.length === 0 ? (
                <tr><td colSpan="6" className="p-8 text-center text-gray-500">No scans found.</td></tr>
              ) : (
                history.map(scan => (
                  <tr key={scan.scan_id} className="border-b border-cyber-border/50 hover:bg-gray-800/30 transition-colors">
                    <td className="p-4 text-gray-500 font-mono text-sm">#{scan.scan_id}</td>
                    <td className="p-4 text-gray-300 text-sm">{new Date(scan.timestamp).toLocaleString()}</td>
                    <td className="p-4 text-cyber-neon text-sm uppercase">{scan.payload_type}</td>
                    <td className="p-4 text-gray-300 max-w-xs truncate" title={scan.original_payload}>{scan.original_payload}</td>
                    <td className="p-4 text-gray-300 font-mono text-center">
                      <span className={scan.risk_score > 60 ? 'text-red-400' : (scan.risk_score > 30 ? 'text-yellow-400' : 'text-green-400')}>{scan.risk_score}</span><span className="text-gray-600 text-xs font-normal">/100</span>
                    </td>
                    <td className="p-4 text-gray-300 font-mono text-center">
                      <span className="text-white">{scan.trust_score}</span><span className="text-gray-600 text-xs font-normal">/100</span>
                    </td>
                    <td className="p-4">
                      <span className={`px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${
                        scan.risk_level === 'SAFE' ? 'bg-green-500/20 text-green-400' :
                        (scan.risk_level === 'WARNING' || scan.risk_level === 'SUSPICIOUS') ? 'bg-yellow-500/20 text-yellow-400' :
                        'bg-red-500/20 text-red-400'
                      }`}>
                        {scan.risk_level === 'SUSPICIOUS' ? 'WARNING' : scan.risk_level}
                      </span>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
        
        {/* Pagination Controls */}
        <div className="flex justify-between items-center p-4 bg-gray-900/50 border-t border-cyber-border">
          <span className="text-sm text-gray-400">
            Showing {Math.min((page - 1) * limit + 1, total)} to {Math.min(page * limit, total)} of {total} records
          </span>
          <div className="flex gap-2">
            <button 
              onClick={() => setPage(p => Math.max(1, p - 1))}
              disabled={page === 1}
              className="px-3 py-1 bg-cyber-bg border border-cyber-border rounded text-sm text-gray-300 hover:border-cyber-primary disabled:opacity-50"
            >
              Previous
            </button>
            <button 
              onClick={() => setPage(p => p + 1)}
              disabled={page * limit >= total}
              className="px-3 py-1 bg-cyber-bg border border-cyber-border rounded text-sm text-gray-300 hover:border-cyber-primary disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
