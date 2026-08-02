import React, { useState, useEffect } from 'react';
import { UploadCloud, FileSpreadsheet, Loader, ShieldAlert, ShieldCheck } from 'lucide-react';
import axios from 'axios';
import * as pdfjsLib from 'pdfjs-dist';
// Vite specific import for the worker
import pdfWorker from 'pdfjs-dist/build/pdf.worker.mjs?url';
import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';
import jsQR from 'jsqr';

// Configure PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorker;

const STATES = {
  IDLE: 'IDLE',
  FILE_SELECTED: 'FILE_SELECTED',
  VALIDATING: 'VALIDATING',
  PARSING: 'PARSING',
  QUEUED: 'QUEUED',
  ANALYZING: 'ANALYZING',
  COMPLETED: 'COMPLETED'
};

export default function BulkUpload() {
  const [files, setFiles] = useState([]);
  const [status, setStatus] = useState(STATES.IDLE);
  const [results, setResults] = useState(null);
  const [errorMsg, setErrorMsg] = useState('');
  const [progressMsg, setProgressMsg] = useState('');

  const handleFileChange = (e) => {
    const selected = Array.from(e.target.files);
    if (selected.length > 0) {
      setFiles(selected);
      setStatus(STATES.FILE_SELECTED);
      setErrorMsg('');
      setResults(null);
    }
  };

  const extractPayloadsFromFile = async (file) => {
    const extractedData = [];
    const urlRegex = /(https?:\/\/[^\s]+|upi:\/\/pay[^\s]+)/gi;

    if (file.name.toLowerCase().endsWith('.pdf')) {
      const arrayBuffer = await file.arrayBuffer();
      const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
      
      const canvas = document.createElement('canvas');
      const ctx = canvas.getContext('2d', { willReadFrequently: true });

      for (let i = 1; i <= pdf.numPages; i++) {
        const page = await pdf.getPage(i);
        const viewport = page.getViewport({ scale: 2.0 });
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        
        await page.render({
          canvasContext: ctx,
          viewport: viewport
        }).promise;
        
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        
        if (code && code.data) {
          const loc = code.location;
          const minX = Math.min(loc.topLeftCorner.x, loc.bottomLeftCorner.x);
          const minY = Math.min(loc.topLeftCorner.y, loc.topRightCorner.y);
          const maxX = Math.max(loc.topRightCorner.x, loc.bottomRightCorner.x);
          const maxY = Math.max(loc.bottomLeftCorner.y, loc.bottomRightCorner.y);
          
          const width = maxX - minX;
          const height = maxY - minY;
          const padding = 20;
          const cropX = Math.max(0, minX - padding);
          const cropY = Math.max(0, minY - padding);
          const cropW = Math.min(canvas.width - cropX, width + padding * 2);
          const cropH = Math.min(canvas.height - cropY, height + padding * 2);

          const cropCanvas = document.createElement('canvas');
          cropCanvas.width = cropW;
          cropCanvas.height = cropH;
          const cropCtx = cropCanvas.getContext('2d');
          cropCtx.drawImage(canvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);
          
          const qrImageDataUrl = cropCanvas.toDataURL('image/png');

          extractedData.push({
            payload: code.data,
            qrImageDataUrl,
            sourceFile: file.name,
            pageNumber: i
          });
        }
        
        const textContent = await page.getTextContent();
        let rawText = textContent.items.map(item => item.str).join('\n');
        // Reconstruct URLs broken across lines (e.g. after /, ., ?, = etc.)
        rawText = rawText.replace(/([:/.?=&\-_])\s*\n\s*/g, '$1');
        rawText = rawText.replace(/\s+/g, ' ');
        
        const matches = rawText.match(urlRegex);
        if (matches) {
          matches.forEach(m => {
            if (!extractedData.some(d => d.payload === m)) {
              extractedData.push({ payload: m, sourceFile: file.name, pageNumber: i, qrImageDataUrl: null });
            }
          });
        }
      }
    } else {
      const text = await file.text();
      const matches = text.match(urlRegex);
      if (matches) {
        matches.forEach(m => {
          if (!extractedData.some(d => d.payload === m)) {
            extractedData.push({ payload: m, sourceFile: file.name, pageNumber: 1, qrImageDataUrl: null });
          }
        });
      } else {
        text.split(/\r?\n/).forEach(line => {
          if (line.trim() && !line.trim().includes(' ')) {
            if (!extractedData.some(d => d.payload === line.trim())) {
              extractedData.push({ payload: line.trim(), sourceFile: file.name, pageNumber: 1, qrImageDataUrl: null });
            }
          }
        });
      }
    }
    return extractedData;
  };

  const startBulkAnalysis = async () => {
    if (files.length === 0) return;
    
    try {
      setStatus(STATES.VALIDATING);
      setProgressMsg('Validating file types...');
      
      setStatus(STATES.PARSING);
      let allPayloads = [];
      for (const file of files) {
        setProgressMsg(`Parsing ${file.name}...`);
        const payloads = await extractPayloadsFromFile(file);
        allPayloads = allPayloads.concat(payloads);
      }
      
      setStatus(STATES.QUEUED);
      // Remove duplicates by payload
      const uniquePayloads = new Set();
      allPayloads = allPayloads.filter(item => {
        if (uniquePayloads.has(item.payload)) return false;
        uniquePayloads.add(item.payload);
        return true;
      });

      if (allPayloads.length === 0) {
        throw new Error('No URLs or UPI payloads found in the uploaded files.');
      }

      setStatus(STATES.ANALYZING);
      setProgressMsg(`Queuing ${allPayloads.length} payloads...`);

      // 1. Create the Batch Job
      const initRes = await axios.post('http://localhost:3000/api/batches', { payloads: allPayloads });
      const batchId = initRes.data.batchId;

      // 2. Poll for Progress
      let isDone = false;
      while (!isDone) {
        setProgressMsg(`Analyzing ${allPayloads.length} payloads... Please wait.`);
        await new Promise(r => setTimeout(r, 2000)); // Poll every 2 seconds
        
        const progressRes = await axios.get(`http://localhost:3000/api/batches/${batchId}`);
        const job = progressRes.data;
        
        if (job) {
           setProgressMsg(`Analyzing: ${job.processed_items} / ${job.total_items} (Safe: ${job.safe_count}, Suspicious: ${job.suspicious_count}, Dangerous: ${job.dangerous_count})`);
           if (job.status === 'COMPLETED' || job.status === 'FAILED') {
               isDone = true;
           }
        }
      }

      // 3. Fetch Final Results
      setProgressMsg(`Fetching final results...`);
      const resultsRes = await axios.get(`http://localhost:3000/api/batches/${batchId}/results`);
      
      const jobFinal = (await axios.get(`http://localhost:3000/api/batches/${batchId}`)).data;
      
      setResults({
          total: jobFinal.total_items,
          safe: jobFinal.safe_count,
          suspicious: jobFinal.suspicious_count,
          dangerous: jobFinal.dangerous_count,
          items: resultsRes.data.items
      });
      
      setStatus(STATES.COMPLETED);
    } catch (e) {
      console.error(e);
      setErrorMsg(e.message || 'Failed to analyze files. Please ensure the backend is running.');
      setStatus(STATES.FILE_SELECTED);
    }
  };

  const exportPDF = () => {
    if (!results || !results.items) return;
    const doc = new jsPDF();
    let currentY = 15;

    doc.setFontSize(16);
    doc.text("FraudEye Enterprise Bulk Analysis Report", 14, currentY);
    currentY += 10;
    
    doc.setFontSize(10);
    doc.text(`Total Scanned: ${results.total} | Safe: ${results.safe} | Suspicious: ${results.suspicious} | Dangerous: ${results.dangerous}`, 14, currentY);
    currentY += 10;
    
    const summaryData = results.items.map(item => {
      const upi = item.details.payloadClass.data;
      const isUpi = item.details.payloadClass.type === 'upi';
      const rScore = item.details?.scoring?.riskScore !== undefined ? item.details.scoring.riskScore : (item.score !== undefined ? item.score : 'N/A');
      const tScore = item.details?.scoring?.trustScore !== undefined ? item.details.scoring.trustScore : (item.details?.riskReport?.trustScore !== undefined ? item.details.riskReport.trustScore : 'N/A');
      return [
        isUpi && upi.pn ? upi.pn : 'N/A',
        isUpi && upi.pa ? upi.pa : item.payload,
        rScore,
        tScore,
        item.verdict === 'SUSPICIOUS' ? 'WARNING' : item.verdict
      ];
    });

    autoTable(doc, {
      startY: currentY,
      head: [['QR Holder Name', 'UPI ID / Domain', 'Risk Score', 'Trust Score', 'Verdict']],
      body: summaryData,
      styles: { fontSize: 8, cellPadding: 2 },
    });

    currentY = doc.lastAutoTable.finalY + 15;

    results.items.forEach((item, index) => {
      // Page break check
      if (currentY > 250) {
        doc.addPage();
        currentY = 20;
      }

      doc.setFontSize(12);
      doc.text(`================================================`, 14, currentY);
      currentY += 6;
      doc.text(`QR RESULT #${index + 1}`, 14, currentY);
      currentY += 6;
      doc.text(`================================================`, 14, currentY);
      currentY += 10;

      if (item.details.qr_image) {
        try {
          doc.addImage(item.details.qr_image, 'PNG', 14, currentY, 40, 40);
          currentY += 45;
        } catch (e) {
          doc.text("[QR Image Rendering Error]", 14, currentY);
          currentY += 10;
        }
      }

      doc.setFontSize(10);
      const d = item.details;
      const isUpi = d.payloadClass.type === 'upi';
      const upi = d.payloadClass.data || {};
      
      const detailsLines = [];
      if (isUpi) {
        detailsLines.push(`QR Holder Name: ${upi.pn || 'Not Provided'}`);
        detailsLines.push(`Receiver / Payee Name: ${upi.pn || 'Not Provided'}`);
        detailsLines.push(`UPI ID: ${upi.pa || 'Missing'}`);
        detailsLines.push(`Amount: ${upi.am || 'Not Specified'}`);
        detailsLines.push(`Currency: ${upi.cu || 'Not Specified'}`);
        detailsLines.push(`Merchant Code: ${upi.mc || 'Not Specified'}`);
        detailsLines.push(`Payment Mode: ${upi.mode || 'Not Specified'}`);
        detailsLines.push(`Purpose: ${upi.purpose || 'Not Specified'}`);
        detailsLines.push('');
      } else {
        detailsLines.push(`Domain/URL: ${d.analysisResult?.domain || item.payload}`);
        detailsLines.push('');
      }

      const scoreObj = d.scoring || d.riskReport || {};
      const verdict = scoreObj.verdict || scoreObj.riskLevel || item.verdict;
      
      detailsLines.push(`Risk Score: ${scoreObj.riskScore !== undefined ? scoreObj.riskScore : 'N/A'} / 100`);
      detailsLines.push(`Trust Score: ${scoreObj.trustScore !== undefined ? scoreObj.trustScore : 'N/A'} / 100`);
      detailsLines.push(`Confidence: ${scoreObj.confidence !== undefined ? scoreObj.confidence : 'N/A'}%`);
      detailsLines.push(`Verdict: ${verdict === 'SUSPICIOUS' ? 'WARNING' : verdict}`);
      detailsLines.push('');
      
      if (isUpi) {
        detailsLines.push(`Merchant Verification: UNVERIFIED`);
        detailsLines.push('');
      }

      detailsLines.push(`Security Indicators:`);
      const indicatorsList = scoreObj.evidence || scoreObj.indicators;
      if (indicatorsList && indicatorsList.length > 0) {
        indicatorsList.forEach(ind => {
          const typeLabel = (ind.id || ind.type || 'UNKNOWN').toUpperCase();
          detailsLines.push(`- ${typeLabel}: ${ind.description}`);
        });
      } else {
        detailsLines.push(`- None`);
      }
      detailsLines.push('');
      detailsLines.push(`Raw Payload:`);
      // Wrap payload
      const splitPayload = doc.splitTextToSize(item.payload, 180);
      detailsLines.push(...splitPayload);

      detailsLines.forEach(line => {
        if (currentY > 280) {
          doc.addPage();
          currentY = 20;
        }
        doc.text(line, 14, currentY);
        currentY += 5;
      });

      currentY += 10;
    });

    doc.save('FraudEye_Detailed_Batch_Report.pdf');
  };

  return (
    <div className="max-w-6xl mx-auto animate-in fade-in duration-500">
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-white mb-2">Enterprise Bulk Analysis</h1>
        <p className="text-gray-400">Upload CSV, Excel, Text, or PDF files containing multiple QR codes or URLs for asynchronous batch processing.</p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {/* Upload Column */}
        <div className="glass-panel p-8 rounded-xl flex flex-col items-center justify-center text-center">
          <UploadCloud className="w-16 h-16 text-cyber-primary mb-4" />
          <h2 className="text-xl font-bold text-white mb-2">Upload Files</h2>
          <p className="text-sm text-gray-400 mb-6">Select files to begin bulk processing.</p>
          
          <input 
            type="file" 
            multiple 
            onChange={handleFileChange}
            disabled={status !== STATES.IDLE && status !== STATES.FILE_SELECTED && status !== STATES.COMPLETED}
            className="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-900/30 file:text-cyber-primary hover:file:bg-blue-900/50 mb-6" 
          />

          {status === STATES.FILE_SELECTED && (
            <div className="text-left w-full mb-6 bg-gray-900/50 p-4 rounded text-sm text-gray-300">
               <strong>Selected:</strong> {files.length} file(s)
               <ul className="mt-2 list-disc list-inside">
                 {files.map((f, i) => <li key={i} className="truncate">{f.name} ({(f.size / 1024).toFixed(1)} KB)</li>)}
               </ul>
            </div>
          )}
          
          {errorMsg && <p className="text-red-400 text-sm mb-4">{errorMsg}</p>}

          <button 
            onClick={startBulkAnalysis}
            disabled={status !== STATES.FILE_SELECTED}
            className="w-full py-3 bg-cyber-primary text-white font-bold rounded-lg hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-[0_0_15px_rgba(59,130,246,0.3)]"
          >
            {status !== STATES.IDLE && status !== STATES.FILE_SELECTED && status !== STATES.COMPLETED ? (
              <span className="flex justify-center items-center gap-2">
                <Loader className="animate-spin w-5 h-5"/> Processing...
              </span>
            ) : 'Start Batch Scan'}
          </button>
        </div>

        {/* Results Column */}
        <div className="lg:col-span-2 space-y-6">
          {(status === STATES.VALIDATING || status === STATES.PARSING || status === STATES.QUEUED || status === STATES.ANALYZING) && (
            <div className="glass-panel p-8 rounded-xl flex flex-col items-center justify-center h-full">
              <Loader className="w-12 h-12 text-cyber-primary animate-spin mb-4" />
              <p className="text-cyber-neon animate-pulse text-lg">{status}</p>
              <p className="text-gray-400 mt-2">{progressMsg}</p>
            </div>
          )}

          {status === STATES.COMPLETED && results && (
            <>
              {/* Summary */}
              <div className="grid grid-cols-4 gap-4">
                <div className="glass-panel p-4 rounded-lg text-center">
                  <div className="text-xs text-gray-500 uppercase">Processed</div>
                  <div className="text-2xl font-bold text-white">{results.total}</div>
                </div>
                <div className="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-safe">
                  <div className="text-xs text-gray-500 uppercase">Safe</div>
                  <div className="text-2xl font-bold text-cyber-safe">{results.safe}</div>
                </div>
                <div className="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-warning">
                  <div className="text-xs text-gray-500 uppercase">Suspicious</div>
                  <div className="text-2xl font-bold text-cyber-warning">{results.suspicious}</div>
                </div>
                <div className="glass-panel p-4 rounded-lg text-center border-b-2 border-cyber-danger">
                  <div className="text-xs text-gray-500 uppercase">Dangerous</div>
                  <div className="text-2xl font-bold text-cyber-danger">{results.dangerous}</div>
                </div>
              </div>

              {/* Data Table */}
              <div className="glass-panel rounded-xl overflow-hidden">
                <div className="flex justify-between items-center p-4 border-b border-cyber-border bg-gray-900/50">
                  <h3 className="font-bold text-white">Batch Results Preview</h3>
                  <button onClick={exportPDF} className="flex items-center gap-2 px-3 py-1.5 text-sm bg-cyber-primary/20 text-cyber-primary rounded hover:bg-cyber-primary hover:text-white transition-colors border border-cyber-primary/50">
                    <FileSpreadsheet className="w-4 h-4" /> Export Report (PDF)
                  </button>
                </div>
                <div className="overflow-x-auto max-h-[500px] overflow-y-auto">
                  <table className="w-full text-left text-sm">
                    <thead>
                      <tr className="bg-gray-900/30 sticky top-0">
                        <th className="p-3 text-gray-400 font-semibold w-12">QR</th>
                        <th className="p-3 text-gray-400 font-semibold">Holder Name / URL</th>
                        <th className="p-3 text-gray-400 font-semibold">UPI ID / Domain</th>
                        <th className="p-3 text-gray-400 font-semibold text-center">Risk Score</th>
                        <th className="p-3 text-gray-400 font-semibold text-center">Trust Score</th>
                        <th className="p-3 text-gray-400 font-semibold">Verdict</th>
                      </tr>
                    </thead>
                    <tbody>
                      {results.items.map(item => {
                        const d = item.details;
                        const isUpi = d.payloadClass.type === 'upi';
                        const upi = d.payloadClass.data || {};
                        const name = isUpi && upi.pn ? upi.pn : (isUpi ? 'N/A' : 'Web URL');
                        const idOrDomain = isUpi && upi.pa ? upi.pa : (d.analysisResult?.domain || item.payload);

                        return (
                          <tr key={item.id} className="border-t border-cyber-border/50 hover:bg-gray-800/30 transition-colors">
                            <td className="p-2">
                              {d.qr_image ? (
                                <img src={d.qr_image} alt="QR" className="w-8 h-8 object-contain bg-white rounded" />
                              ) : (
                                <div className="w-8 h-8 bg-gray-800 rounded flex items-center justify-center text-xs text-gray-500">None</div>
                              )}
                            </td>
                            <td className="p-3 text-gray-300 font-semibold text-sm truncate max-w-[150px]" title={name}>{name}</td>
                            <td className="p-3 text-gray-400 font-mono text-xs truncate max-w-[150px]" title={idOrDomain}>{idOrDomain}</td>
                            <td className="p-3 font-mono font-bold text-gray-200 text-center">
                              {item.details?.scoring?.riskScore !== undefined ? item.details.scoring.riskScore : (item.score !== undefined ? item.score : 'N/A')}<span className="text-gray-600 text-xs font-normal">/100</span>
                            </td>
                            <td className="p-3 font-mono font-bold text-gray-200 text-center">
                              {item.details?.scoring?.trustScore !== undefined ? item.details.scoring.trustScore : (item.details?.riskReport?.trustScore !== undefined ? item.details.riskReport.trustScore : 'N/A')}<span className="text-gray-600 text-xs font-normal">/100</span>
                            </td>
                            <td className="p-3 font-semibold text-center">
                              <span className={`px-2 py-1 rounded text-xs font-bold uppercase tracking-wider ${
                                item.verdict === 'SAFE' ? 'bg-green-500/20 text-green-400' :
                                (item.verdict === 'WARNING' || item.verdict === 'SUSPICIOUS') ? 'bg-yellow-500/20 text-yellow-400' :
                                'bg-red-500/20 text-red-400'
                              }`}>
                                {item.verdict === 'SUSPICIOUS' ? 'WARNING' : item.verdict}
                              </span>
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                </div>
              </div>
            </>
          )}

          {(status === STATES.IDLE || status === STATES.FILE_SELECTED) && (
             <div className="glass-panel p-8 rounded-xl flex flex-col items-center justify-center h-full text-gray-500">
               <UploadCloud className="w-12 h-12 mb-4 opacity-50" />
               <p>{status === STATES.FILE_SELECTED ? "Ready to begin batch scanning." : "Awaiting file upload for bulk processing."}</p>
             </div>
          )}
        </div>
      </div>
    </div>
  );
}
