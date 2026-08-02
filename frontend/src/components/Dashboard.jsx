import React, { useState, useEffect } from 'react';
import { ShieldCheck, ShieldAlert, AlertTriangle, ScanLine } from 'lucide-react';
import axios from 'axios';
import { PieChart, Pie, Cell, ResponsiveContainer, Tooltip } from 'recharts';

export default function Dashboard() {
  const [stats, setStats] = useState({
    total: 0,
    safe: 0,
    suspicious: 0,
    dangerous: 0
  });

  useEffect(() => {
    // In a real app, this would be a dedicated analytics endpoint.
    // For prototype, we aggregate from history.
    axios.get('http://localhost:3000/api/history/stats')
      .then(res => {
        setStats({
          total: res.data.total,
          safe: res.data.safe,
          suspicious: res.data.suspicious,
          dangerous: res.data.dangerous,
        });
      })
      .catch(console.error);
  }, []);

  const data = [
    { name: 'Safe', value: stats.safe, color: '#10b981' },
    { name: 'Suspicious', value: stats.suspicious, color: '#f59e0b' },
    { name: 'Dangerous', value: stats.dangerous, color: '#ef4444' },
  ];

  return (
    <div className="space-y-8 animate-in fade-in duration-500">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-white mb-2">Threat Intelligence Dashboard</h1>
        <p className="text-gray-400">System-wide overview of scanned QR codes and intercepted threats.</p>
      </div>

      {/* KPI Cards */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div className="glass-panel p-6 rounded-xl border-l-4 border-l-cyber-primary">
          <div className="flex justify-between items-start">
            <div>
              <p className="text-gray-400 text-sm font-semibold uppercase">Total Scans</p>
              <h3 className="text-4xl font-black text-white mt-2">{stats.total}</h3>
            </div>
            <ScanLine className="text-cyber-primary w-8 h-8 opacity-50" />
          </div>
        </div>
        
        <div className="glass-panel p-6 rounded-xl border-l-4 border-l-cyber-safe">
          <div className="flex justify-between items-start">
            <div>
              <p className="text-gray-400 text-sm font-semibold uppercase">Safe URLs</p>
              <h3 className="text-4xl font-black text-white mt-2">{stats.safe}</h3>
            </div>
            <ShieldCheck className="text-cyber-safe w-8 h-8 opacity-50" />
          </div>
        </div>

        <div className="glass-panel p-6 rounded-2xl relative overflow-hidden group">
            <div className="absolute inset-0 bg-cyber-warning/5 group-hover:bg-cyber-warning/10 transition-colors"></div>
            <div className="relative z-10">
              <div className="flex justify-between items-start mb-4">
                <div className="p-3 bg-cyber-warning/20 rounded-xl">
                  <AlertTriangle className="w-6 h-6 text-cyber-warning" />
                </div>
                <span className="text-xs font-bold text-cyber-warning bg-cyber-warning/10 px-2 py-1 rounded-full uppercase tracking-wider">Warning</span>
              </div>
              <h3 className="text-4xl font-black text-white mb-1 font-mono tracking-tighter">{stats.suspicious}</h3>
              <p className="text-sm text-gray-400 font-medium uppercase tracking-wider">Requires Review</p>
            </div>
          </div>

        <div className="glass-panel p-6 rounded-xl border-l-4 border-l-cyber-danger">
          <div className="flex justify-between items-start">
            <div>
              <p className="text-gray-400 text-sm font-semibold uppercase">Threats Blocked</p>
              <h3 className="text-4xl font-black text-white mt-2">{stats.dangerous}</h3>
            </div>
            <ShieldAlert className="text-cyber-danger w-8 h-8 opacity-50" />
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div className="glass-panel p-6 rounded-xl h-80 flex flex-col">
          <h2 className="text-lg font-bold text-white mb-4">Risk Distribution</h2>
          <div className="flex-grow">
            <ResponsiveContainer width="100%" height="100%">
              <PieChart>
                <Pie
                  data={data}
                  cx="50%"
                  cy="50%"
                  innerRadius={60}
                  outerRadius={80}
                  paddingAngle={5}
                  dataKey="value"
                >
                  {data.map((entry, index) => (
                    <Cell key={`cell-${index}`} fill={entry.color} />
                  ))}
                </Pie>
                <Tooltip 
                  contentStyle={{ backgroundColor: '#151b2b', borderColor: '#232c45', color: '#fff' }}
                  itemStyle={{ color: '#fff' }}
                />
              </PieChart>
            </ResponsiveContainer>
          </div>
        </div>
        
        <div className="glass-panel p-6 rounded-xl flex items-center justify-center text-center">
            <div>
                <h2 className="text-2xl font-bold text-white mb-4">Ready to scan?</h2>
                <p className="text-gray-400 mb-6">Head over to the Secure Scanner to intercept malicious QR codes before they execute.</p>
                <a href="/scan" className="inline-block px-6 py-3 bg-cyber-primary text-white font-bold rounded-lg hover:bg-blue-600 transition-colors shadow-[0_0_15px_rgba(59,130,246,0.3)]">
                    Open Scanner
                </a>
            </div>
        </div>
      </div>
    </div>
  );
}
