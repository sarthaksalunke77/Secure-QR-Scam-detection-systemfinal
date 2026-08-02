import React, { useState } from 'react';
import { useLocation, useNavigate, useParams } from 'react-router-dom';
import { ShieldAlert, ShieldCheck, AlertTriangle, ExternalLink, ShieldX, TerminalSquare, CheckCircle, XCircle, Info } from 'lucide-react';
import axios from 'axios';

export default function Report() {
  const location = useLocation();
  const navigate = useNavigate();
  const { id } = useParams();
  const report = location.state?.report;
  
  const [actionLoading, setActionLoading] = useState(false);

  if (!report) {
    return <div className="text-center mt-20 text-xl text-gray-400">Report not found. Please scan again.</div>;
  }

  const { originalUrl, hostname, analysis, scoring, qr_image, payloadClass } = report;
  const { riskScore, trustScore, confidence, verdict, evidence } = scoring;
  
  // Provide safe defaults for the analysis sections
  const isUpiCheck = payloadClass && (payloadClass.type === 'upi' || payloadClass.type === 'upi_id_only');
  const upiData = isUpiCheck ? payloadClass.data : null;

  const urlLexical = analysis?.urlLexical || {};
  const domainCheck = analysis?.domainCheck || { status: 'NOT_CHECKED' };
  const sslCheck = analysis?.sslCheck || { status: 'NOT_CHECKED' };
  const threatIntel = analysis?.threatIntel || { status: 'NOT_CHECKED', providers: [] };
  const redirectCheck = analysis?.redirectCheck || { status: 'NOT_CHECKED' };

  // Determine styling based on risk level
  const isDangerous = verdict === 'DANGEROUS';
  const isWarning = verdict === 'WARNING' || verdict === 'SUSPICIOUS';
  const isSafe = verdict === 'SAFE';

  const riskColorClass = isDangerous ? 'text-cyber-danger' : (isWarning ? 'text-cyber-warning' : 'text-cyber-safe');
  const glowClass = isDangerous ? 'glow-danger' : (isWarning ? 'glow-warning' : 'glow-safe');
  const Icon = isDangerous ? ShieldX : (isWarning ? AlertTriangle : ShieldCheck);
  
  const borderTopClass = isDangerous ? 'border-t-cyber-danger' : (isWarning ? 'border-t-cyber-warning' : 'border-t-cyber-safe');

  let recommendation = "Proceed carefully.";
  if (isDangerous) recommendation = "Do not continue. Highly likely to be a scam.";
  else if (isWarning) recommendation = "Verify the source before continuing.";
  else recommendation = "No obvious threats detected.";

  const handleAction = async (actionType) => {
    setActionLoading(true);
    try {
      await axios.post('http://localhost:3000/api/action', {
        scan_id: id,
        action: actionType
      });
      
      if (actionType === 'continue') {
        window.location.href = report.finalUrl || originalUrl;
      } else if (actionType === 'block') {
        navigate('/');
      } else if (actionType === 'sandbox') {
        alert("Sandbox Environment Concept:\n\nRoutes URL through a remote headless browser rendering engine.");
        setActionLoading(false);
      }
    } catch (e) {
      console.error(e);
      setActionLoading(false);
    }
  };

  const getStatusColor = (status) => {
    if (status === 'COMPLETED' || status === 'CLEAN' || status === 'VALID' || status === 'RESOLVED') return 'text-cyber-safe';
    if (status === 'NOT_CHECKED') return 'text-gray-500';
    if (status === 'WARNING' || status === 'SUSPICIOUS' || status === 'RATE_LIMITED') return 'text-cyber-warning';
    return 'text-cyber-danger'; // ERROR, MALICIOUS, INVALID, etc.
  };

  return (
    <div className="max-w-5xl mx-auto space-y-8 animate-in fade-in zoom-in duration-500 pb-12">
      
      <div className="text-center mb-6">
        <h1 className="text-3xl font-bold text-white uppercase tracking-widest">Website Security Analysis</h1>
        <p className="text-gray-400 mt-2 break-all">{originalUrl}</p>
        <p className="text-gray-500 font-mono text-sm">{hostname || 'No Hostname'}</p>
      </div>

      {/* Top Banner (Risk / Trust / Verdict) */}
      <div className={`glass-panel rounded-2xl p-8 flex flex-col md:flex-row items-center justify-between border-t-4 ${borderTopClass} ${glowClass} gap-6`}>
        <div className="flex items-center gap-6 w-full md:w-auto">
          <div className="p-4 rounded-full bg-cyber-bg hidden md:block">
            <Icon className={`w-12 h-12 ${riskColorClass}`} />
          </div>
          <div>
            <div className="text-sm text-gray-400 uppercase tracking-widest font-bold mb-1">Classification</div>
            <h1 className={`text-5xl font-black tracking-tight ${riskColorClass}`}>{verdict}</h1>
            <p className="text-gray-300 mt-2 text-lg">{recommendation}</p>
          </div>
        </div>
        
        <div className="flex gap-8 w-full md:w-auto justify-center md:justify-end">
           <div className="text-center md:text-right">
             <div className="text-sm text-gray-400 uppercase tracking-widest font-bold">Risk Score</div>
             <div className={`text-5xl font-black font-mono ${riskColorClass}`}>
               {riskScore !== undefined ? riskScore : 'N/A'}<span className="text-2xl text-gray-600">/100</span>
             </div>
           </div>
           <div className="text-center md:text-right border-l border-gray-700 pl-8">
             <div className="text-sm text-gray-400 uppercase tracking-widest font-bold">Trust Score</div>
             <div className={`text-5xl font-black font-mono text-white`}>
               {trustScore !== undefined ? trustScore : 'N/A'}<span className="text-2xl text-gray-600">/100</span>
             </div>
           </div>
        </div>
      </div>
      
      {/* Confidence and Analysis Coverage */}
      <div className="flex items-center justify-between bg-gray-900/50 p-4 rounded-xl border border-cyber-border">
         <div className="text-sm text-gray-400 uppercase font-bold tracking-wider">Analysis Confidence</div>
         <div className="w-1/2 bg-gray-800 rounded-full h-4 overflow-hidden relative">
            <div 
              className={`h-full absolute left-0 top-0 transition-all duration-1000 ${confidence >= 80 ? 'bg-cyber-safe' : (confidence >= 50 ? 'bg-cyber-warning' : 'bg-cyber-danger')}`}
              style={{ width: `${confidence || 0}%` }}
            ></div>
         </div>
         <div className="font-mono text-xl text-white font-bold">{confidence || 0}%</div>
      </div>

      {/* Grid of Modules */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {isUpiCheck ? (
          <>
            {/* UPI ID CHECK MODULE */}
            <div className="glass-panel p-6 rounded-xl md:col-span-2">
              <h2 className="text-lg font-bold text-white mb-4 border-b border-gray-700 pb-2">UPI ID ANALYSIS</h2>
              <div className="grid grid-cols-2 md:grid-cols-4 gap-y-4 text-sm">
                <div className="text-gray-500">Payload Type:</div>
                <div className="font-bold text-cyber-neon">{payloadClass.type.toUpperCase()}</div>
                
                <div className="text-gray-500">Raw VPA:</div>
                <div className="text-white truncate" title={upiData.vpa || upiData.pa}>{upiData.vpa || upiData.pa || 'N/A'}</div>
                
                <div className="text-gray-500">Receiver Name (pn):</div>
                <div className="text-white truncate" title={upiData.pn}>{upiData.pn || 'Not Provided'}</div>
                
                <div className="text-gray-500">Requested Amount:</div>
                <div className="text-white">{upiData.am ? `${upiData.am} ${upiData.cu || 'INR'}` : 'Not Specified'}</div>

                <div className="text-gray-500">Transaction Note:</div>
                <div className="text-white truncate" title={upiData.tn}>{upiData.tn || 'Not Provided'}</div>
                
                <div className="text-gray-500">Verification Status:</div>
                <div className="text-yellow-500 font-bold">UNVERIFIED</div>
              </div>
            </div>
          </>
        ) : (
          <>
            {/* DOMAIN CHECK */}
            <div className="glass-panel p-6 rounded-xl">
              <h2 className="text-lg font-bold text-white mb-4 border-b border-gray-700 pb-2">DOMAIN CHECK</h2>
              <div className="grid grid-cols-2 gap-y-3 text-sm">
                <div className="text-gray-500">Status:</div>
                <div className={`font-bold ${getStatusColor(domainCheck.status)}`}>{domainCheck.status}</div>
                
                <div className="text-gray-500">DNS Resolved:</div>
                <div className="text-white">{domainCheck.dnsResolved ? 'Yes' : (domainCheck.status === 'NOT_CHECKED' ? 'Not Checked' : 'No')}</div>
                
                <div className="text-gray-500">Domain Age:</div>
                <div className="text-white">{domainCheck.domainAgeDays}</div>
                
                <div className="text-gray-500">Punycode:</div>
                <div className="text-white">{domainCheck.punycodeDetected ? 'Detected' : 'Not Detected'}</div>
                
                <div className="text-gray-500">Brand Impersonation:</div>
                <div className="text-white">{domainCheck.brandImpersonation}</div>
              </div>
            </div>

        {/* SSL/TLS CHECK */}
        <div className="glass-panel p-6 rounded-xl">
          <h2 className="text-lg font-bold text-white mb-4 border-b border-gray-700 pb-2">SSL/TLS CHECK</h2>
          <div className="grid grid-cols-2 gap-y-3 text-sm">
            <div className="text-gray-500">Status:</div>
            <div className={`font-bold ${getStatusColor(sslCheck.status)}`}>{sslCheck.status}</div>
            
            <div className="text-gray-500">Certificate Present:</div>
            <div className="text-white">{sslCheck.certificatePresent ? 'YES' : (sslCheck.status === 'NOT_CHECKED' ? 'UNKNOWN' : 'NO')}</div>
            
            <div className="text-gray-500">Issuer:</div>
            <div className="text-white truncate" title={sslCheck.issuer || ''}>{sslCheck.issuer || 'Unavailable'}</div>
            
            <div className="text-gray-500">Valid Until:</div>
            <div className="text-white">{sslCheck.validTo ? new Date(sslCheck.validTo).toLocaleDateString() : 'Unavailable'}</div>
            
            <div className="text-gray-500">Hostname Match:</div>
            <div className="text-white">{sslCheck.hostnameMatch === true ? 'YES' : (sslCheck.hostnameMatch === false ? 'NO' : 'UNKNOWN')}</div>
          </div>
        </div>

        {/* THREAT INTELLIGENCE */}
        <div className="glass-panel p-6 rounded-xl">
          <h2 className="text-lg font-bold text-white mb-4 border-b border-gray-700 pb-2">THREAT INTELLIGENCE</h2>
          <div className="grid grid-cols-2 gap-y-3 text-sm">
            <div className="text-gray-500">Status:</div>
            <div className={`font-bold ${getStatusColor(threatIntel.status)}`}>{threatIntel.status}</div>
            
            <div className="text-gray-500">Provider:</div>
            <div className="text-white">{threatIntel.providers?.length > 0 ? threatIntel.providers.join(', ') : 'None Configured'}</div>
            
            <div className="text-gray-500">Detections:</div>
            <div className="text-white">{threatIntel.detections > 0 ? threatIntel.detections : (threatIntel.status === 'NOT_CHECKED' ? 'N/A' : '0')}</div>
          </div>
        </div>

        {/* REDIRECT CHECK */}
        <div className="glass-panel p-6 rounded-xl">
          <h2 className="text-lg font-bold text-white mb-4 border-b border-gray-700 pb-2">REDIRECT CHECK</h2>
          <div className="grid grid-cols-2 gap-y-3 text-sm">
            <div className="text-gray-500">Status:</div>
            <div className={`font-bold ${getStatusColor(redirectCheck.status)}`}>{redirectCheck.status}</div>
            
            <div className="text-gray-500">Redirect Count:</div>
            <div className="text-white">{redirectCheck.redirectCount || 0}</div>
            
            <div className="text-gray-500">Final URL:</div>
            <div className="text-white truncate" title={redirectCheck.finalUrl}>{redirectCheck.finalUrl || 'N/A'}</div>
            
            <div className="text-gray-500">Cross-Domain:</div>
            <div className="text-white">{redirectCheck.crossDomainRedirect ? 'YES' : 'NO'}</div>
            
            <div className="text-gray-500">HTTPS Downgrade:</div>
            <div className="text-white">{redirectCheck.httpsDowngrade ? 'YES' : 'NO'}</div>
          </div>
        </div>
        </>
        )}
      </div>

      {/* SECURITY FINDINGS */}
      <div className="glass-panel p-6 rounded-xl">
        <h2 className="text-xl font-bold text-white mb-4 border-b border-cyber-border pb-2">SECURITY FINDINGS</h2>
        {!evidence || evidence.length === 0 ? (
          <div className="flex flex-col items-center justify-center py-6 text-gray-500">
            {threatIntel.status === 'NOT_CHECKED' ? (
              <>
                <Info className="w-12 h-12 mb-2 opacity-50" />
                <p>Analysis incomplete — Threat Intelligence was not checked.</p>
              </>
            ) : (domainCheck.status === 'NOT_CHECKED' || sslCheck.status === 'NOT_CHECKED' || redirectCheck.status === 'NOT_CHECKED') ? (
              <>
                <Info className="w-12 h-12 mb-2 opacity-50" />
                <p>No suspicious evidence found in completed checks. Analysis is incomplete.</p>
              </>
            ) : (
              <>
                <ShieldCheck className="w-12 h-12 mb-2 opacity-50 text-cyber-safe" />
                <p>No suspicious indicators found across any engine.</p>
              </>
            )}
          </div>
        ) : (
          <ul className="space-y-3">
            {evidence.map((ind, idx) => (
              <li key={idx} className="flex gap-4 items-start p-4 bg-gray-900/50 rounded-lg border border-gray-800">
                {ind.severity === 'critical' ? <ShieldX className="w-6 h-6 text-red-500 flex-shrink-0" /> :
                 ind.severity === 'high' ? <ShieldAlert className="w-6 h-6 text-red-400 flex-shrink-0" /> :
                 ind.severity === 'medium' ? <AlertTriangle className="w-6 h-6 text-yellow-500 flex-shrink-0" /> :
                 <Info className="w-6 h-6 text-blue-400 flex-shrink-0" />}
                <div className="flex-1">
                  <div className="font-bold text-gray-200">
                     {ind.id.replace(/_/g, ' ')}
                     <span className={`ml-3 text-[10px] px-2 py-0.5 rounded-full uppercase tracking-wider ${
                        ind.severity === 'critical' ? 'bg-red-500/20 text-red-400' :
                        ind.severity === 'high' ? 'bg-red-400/20 text-red-300' :
                        ind.severity === 'medium' ? 'bg-yellow-500/20 text-yellow-400' :
                        'bg-blue-500/20 text-blue-400'
                     }`}>{ind.severity} Risk</span>
                  </div>
                  <div className="text-sm text-gray-400 mt-1">{ind.description}</div>
                  <div className="text-xs text-gray-600 mt-2 font-mono">Source: {ind.source.join(', ')} | Penalty: +{ind.riskContribution}</div>
                </div>
              </li>
            ))}
          </ul>
        )}
      </div>

      {/* CHECK COVERAGE */}
      <div className="glass-panel p-6 rounded-xl mt-6">
        <h2 className="text-xl font-bold text-white mb-4 border-b border-cyber-border pb-2">CHECK COVERAGE</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div className="flex justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-800">
                <span className="text-gray-400">URL Analysis</span>
                <span className="font-bold text-cyber-safe">COMPLETED</span>
            </div>
            <div className="flex justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-800">
                <span className="text-gray-400">Domain Check</span>
                <span className={`font-bold ${domainCheck.status === 'NOT_CHECKED' ? 'text-gray-500' : (domainCheck.status === 'ERROR' ? 'text-cyber-danger' : 'text-cyber-safe')}`}>
                  {domainCheck.status === 'NOT_CHECKED' ? 'NOT_CHECKED' : (domainCheck.status === 'ERROR' ? 'ERROR' : 'COMPLETED')}
                </span>
            </div>
            <div className="flex justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-800">
                <span className="text-gray-400">SSL/TLS Check</span>
                <span className={`font-bold ${sslCheck.status === 'NOT_CHECKED' ? 'text-gray-500' : (sslCheck.status === 'ERROR' ? 'text-cyber-danger' : 'text-cyber-safe')}`}>
                  {sslCheck.status === 'NOT_CHECKED' ? 'NOT_CHECKED' : (sslCheck.status === 'ERROR' ? 'ERROR' : 'COMPLETED')}
                </span>
            </div>
            <div className="flex justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-800">
                <span className="text-gray-400">Threat Intelligence</span>
                <span className={`font-bold ${threatIntel.status === 'NOT_CHECKED' ? 'text-gray-500' : (threatIntel.status === 'API_ERROR' ? 'text-cyber-danger' : 'text-cyber-safe')}`}>
                  {threatIntel.status === 'NOT_CHECKED' ? 'NOT_CHECKED' : (threatIntel.status === 'API_ERROR' ? 'ERROR' : 'COMPLETED')}
                </span>
            </div>
            <div className="flex justify-between p-3 bg-gray-900/50 rounded-lg border border-gray-800">
                <span className="text-gray-400">Redirect Check</span>
                <span className={`font-bold ${redirectCheck.status === 'NOT_CHECKED' ? 'text-gray-500' : (redirectCheck.status === 'ERROR' ? 'text-cyber-danger' : 'text-cyber-safe')}`}>
                  {redirectCheck.status === 'NOT_CHECKED' ? 'NOT_CHECKED' : (redirectCheck.status === 'ERROR' ? 'ERROR' : 'COMPLETED')}
                </span>
            </div>
        </div>
      </div>

      {qr_image && (
        <div className="glass-panel p-6 rounded-xl flex justify-center bg-gray-900/50">
          <img src={qr_image} alt="Original QR" className="max-w-[200px] rounded-lg border border-gray-700" />
        </div>
      )}

      {/* Action Bar */}
      <div className="glass-panel p-6 rounded-xl flex flex-col md:flex-row justify-between items-center bg-gray-900/50 gap-4">
        <p className="text-sm text-gray-400">By continuing, you accept the risks associated with this destination.</p>
        <div className="flex flex-wrap gap-4 justify-end">
          <button 
            onClick={() => handleAction('block')}
            disabled={actionLoading}
            className="px-6 py-3 rounded-lg font-bold bg-cyber-bg border border-cyber-border text-gray-300 hover:text-white hover:bg-red-900/30 hover:border-red-500/50 transition-all"
          >
            Block & Close
          </button>
          
          {(isWarning || isDangerous) && (
             <button 
             onClick={() => handleAction('sandbox')}
             disabled={actionLoading}
             className="px-6 py-3 rounded-lg font-bold bg-blue-900/20 border border-blue-500/50 text-blue-400 hover:bg-blue-600 hover:text-white transition-all flex items-center gap-2"
           >
             <TerminalSquare className="w-4 h-4" /> Open in Sandbox
           </button>
          )}

          <button 
            onClick={() => handleAction('continue')}
            disabled={actionLoading}
            className={`px-6 py-3 rounded-lg font-bold flex items-center gap-2 transition-all ${
              isDangerous 
              ? 'bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500 hover:text-white' 
              : 'bg-cyber-primary text-white hover:bg-blue-600 shadow-[0_0_15px_rgba(59,130,246,0.5)]'
            }`}
          >
            {isDangerous ? 'Continue Anyway (Unsafe)' : 'Continue to Destination'} <ExternalLink className="w-4 h-4" />
          </button>
        </div>
      </div>

    </div>
  );
}
