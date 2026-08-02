import React from 'react';
import { BrowserRouter as Router, Routes, Route, Link, useLocation } from 'react-router-dom';
import { Shield, Scan, History, UploadCloud, Activity } from 'lucide-react';
import Dashboard from './components/Dashboard';
import LightDashboard from './components/LightDashboard';
import Scanner from './components/Scanner';
import Report from './components/Report';
import ScanHistory from './components/History';
import BulkUpload from './components/BulkUpload';
import ManualCheck from './pages/ManualCheck';

function Navigation() {
  const location = useLocation();
  // Hide the global dark nav bar when on the new Light Dashboard
  if (location.pathname === '/') return null;

  return (
    <nav className="glass-panel sticky top-0 z-50 px-6 py-4 mb-8">
      <div className="max-w-7xl mx-auto flex justify-between items-center">
        <div className="flex items-center gap-2">
          <Shield className="text-cyber-primary h-8 w-8" />
          <span className="text-xl font-bold tracking-wider text-white">FRAUD<span className="text-cyber-primary">EYE</span></span>
        </div>
        <div className="flex gap-6">
          <Link to="/dark-dashboard" className="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
            <Activity className="h-4 w-4" /> Dashboard
          </Link>
          <Link to="/scan" className="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
            <Scan className="h-4 w-4" /> Scanner
          </Link>
          <Link to="/check" className="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
            <Scan className="h-4 w-4" /> URL & UPI Check
          </Link>
          <Link to="/bulk" className="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
            <UploadCloud className="h-4 w-4" /> Bulk Analysis
          </Link>
          <Link to="/history" className="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
            <History className="h-4 w-4" /> History
          </Link>
        </div>
      </div>
    </nav>
  );
}

function MainLayout({ children }) {
  const location = useLocation();
  const isLightDashboard = location.pathname === '/';

  // Apply different wrapper classes depending on the route
  return (
    <div className={isLightDashboard ? "min-h-screen bg-[#f3f4f6]" : "min-h-screen bg-cyber-bg text-gray-200"}>
      <Navigation />
      <main className={isLightDashboard ? "" : "max-w-7xl mx-auto px-6 pb-12"}>
        {children}
      </main>
    </div>
  );
}

function App() {
  return (
    <Router>
      <MainLayout>
        <Routes>
          <Route path="/" element={<LightDashboard />} />
          <Route path="/dark-dashboard" element={<Dashboard />} />
          <Route path="/scan" element={<Scanner />} />
          <Route path="/check" element={<ManualCheck />} />
          <Route path="/report/:id" element={<Report />} />
          <Route path="/history" element={<ScanHistory />} />
          <Route path="/bulk" element={<BulkUpload />} />
        </Routes>
      </MainLayout>
    </Router>
  );
}

export default App;
