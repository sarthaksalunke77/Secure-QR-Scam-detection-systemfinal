import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Globe, Smartphone, Search, AlertCircle, Loader } from 'lucide-react';
import axios from 'axios';

export default function ManualCheck() {
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState('url'); // 'url' or 'upi'
  const [payload, setPayload] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!payload.trim()) return;

    setLoading(true);
    setError(null);

    try {
      // Force type so backend doesn't misclassify injections
      const forcedType = activeTab === 'upi' ? 'upi_id_only' : 'url';
      
      const response = await axios.post('http://localhost:3000/api/scan', {
        payload: payload.trim(),
        input_type: 'manual_string',
        forced_type: forcedType
      });
      
      setLoading(false);
      navigate(`/report/${response.data.analysisId || response.data.scan_id}`, { state: { report: response.data } });
    } catch (err) {
      console.error(err);
      setError('Failed to analyze. Backend server might be down or returned an error.');
      setLoading(false);
    }
  };

  return (
    <div className="max-w-3xl mx-auto animate-in fade-in zoom-in duration-500">
      <div className="text-center mb-10">
        <h1 className="text-4xl font-bold text-white mb-4">URL & UPI Check</h1>
        <p className="text-gray-400">Manually inspect a suspicious link or UPI ID through our massive 6-engine analysis architecture.</p>
      </div>

      <div className="glass-panel rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        {/* Animated grid background */}
        <div className="absolute inset-0 opacity-10 pointer-events-none" style={{ backgroundImage: 'radial-gradient(#3b82f6 1px, transparent 1px)', backgroundSize: '30px 30px' }}></div>
        
        <div className="relative z-10">
          {/* Tabs */}
          <div className="flex border-b border-gray-700 mb-8">
            <button
              className={`flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 ${
                activeTab === 'url' ? 'text-cyber-primary border-cyber-primary bg-blue-900/10' : 'text-gray-400 border-transparent hover:text-gray-200 hover:bg-gray-800/30'
              }`}
              onClick={() => { setActiveTab('url'); setPayload(''); setError(null); }}
            >
              <Globe className="w-5 h-5" /> URL Check
            </button>
            <button
              className={`flex-1 py-4 flex items-center justify-center gap-2 font-bold text-lg transition-colors border-b-2 ${
                activeTab === 'upi' ? 'text-cyber-neon border-cyber-neon bg-cyan-900/10' : 'text-gray-400 border-transparent hover:text-gray-200 hover:bg-gray-800/30'
              }`}
              onClick={() => { setActiveTab('upi'); setPayload(''); setError(null); }}
            >
              <Smartphone className="w-5 h-5" /> UPI ID Check
            </button>
          </div>

          <form onSubmit={handleSubmit} className="space-y-6">
            <div>
              <label className="block text-sm font-medium text-gray-300 mb-2 uppercase tracking-wider">
                {activeTab === 'url' ? 'Enter Suspicious URL' : 'Enter Suspicious UPI ID'}
              </label>
              <div className="relative">
                <input
                  type="text"
                  value={payload}
                  onChange={(e) => setPayload(e.target.value)}
                  placeholder={activeTab === 'url' ? 'e.g. https://free-gift-claim.example.com/login' : 'e.g. 9423663923@ibl'}
                  className="w-full bg-gray-900 border-2 border-gray-700 focus:border-cyber-primary rounded-xl px-4 py-4 text-white text-lg placeholder-gray-600 focus:outline-none focus:ring-0 transition-colors pl-12"
                  autoFocus
                />
                <Search className="w-6 h-6 text-gray-500 absolute left-4 top-1/2 -translate-y-1/2" />
              </div>
            </div>

            {error && (
              <div className="p-4 bg-red-900/20 border border-red-500/50 rounded-lg flex items-start gap-3">
                <AlertCircle className="w-6 h-6 text-red-500 flex-shrink-0" />
                <p className="text-red-200">{error}</p>
              </div>
            )}

            <button
              type="submit"
              disabled={loading || !payload.trim()}
              className={`w-full flex items-center justify-center gap-3 px-6 py-4 rounded-xl font-bold text-lg transition-all ${
                loading || !payload.trim() 
                ? 'bg-gray-800 text-gray-500 cursor-not-allowed border border-gray-700' 
                : (activeTab === 'url' ? 'bg-cyber-primary hover:bg-blue-600 text-white shadow-[0_0_20px_rgba(59,130,246,0.4)]' : 'bg-cyber-neon hover:bg-cyan-500 text-black shadow-[0_0_20px_rgba(0,255,255,0.4)]')
              }`}
            >
              {loading ? (
                <><Loader className="w-6 h-6 animate-spin" /> Executing 6-Engine Analysis...</>
              ) : (
                <><Search className="w-6 h-6" /> {activeTab === 'url' ? 'Check URL Security' : 'Check UPI ID Structure'}</>
              )}
            </button>
          </form>

        </div>
      </div>
    </div>
  );
}
