import React, { useState, useRef, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import jsQR from 'jsqr';
import { Camera, Upload, AlertCircle, Loader, VideoOff, RefreshCw } from 'lucide-react';
import axios from 'axios';

export default function Scanner() {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [cameraActive, setCameraActive] = useState(false);
  const [scanning, setScanning] = useState(false);
  const [diagnosticMsg, setDiagnosticMsg] = useState('');
  
  const videoRef = useRef(null);
  const canvasRef = useRef(null);
  const streamRef = useRef(null);
  const requestRef = useRef(null);
  const fileInputRef = useRef(null);
  
  const lastScannedRef = useRef({ payload: null, timestamp: 0 });
  
  const navigate = useNavigate();

  useEffect(() => {
    return () => {
      stopCamera();
    };
  }, []);

  const updateDiagnostic = (msg) => {
    setDiagnosticMsg(msg);
  };

  const startCamera = async () => {
    setError(null);
    if (!window.isSecureContext) {
      setError("Camera access requires HTTPS or localhost.");
      return;
    }
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      setError("Camera API is not supported in this browser.");
      return;
    }

    try {
      updateDiagnostic('Requesting camera permission...');
      const stream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: { ideal: "environment" } },
        audio: false
      });
      
      streamRef.current = stream;
      if (videoRef.current) {
        videoRef.current.srcObject = stream;
        videoRef.current.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
        videoRef.current.muted = true;
        
        videoRef.current.onloadedmetadata = () => {
          updateDiagnostic('Stream received, starting video...');
          videoRef.current.play();
          setCameraActive(true);
          setScanning(true);
          requestRef.current = requestAnimationFrame(scanLoop);
        };
      }
    } catch (err) {
      console.error(err);
      if (err.name === 'NotAllowedError') setError('Camera permission denied.');
      else if (err.name === 'NotFoundError') setError('No camera found on this device.');
      else setError(`Camera error: ${err.message}`);
    }
  };

  const stopCamera = () => {
    if (streamRef.current) {
      streamRef.current.getTracks().forEach(track => track.stop());
      streamRef.current = null;
    }
    if (videoRef.current) {
      videoRef.current.srcObject = null;
    }
    if (requestRef.current) {
      cancelAnimationFrame(requestRef.current);
    }
    setCameraActive(false);
    setScanning(false);
    updateDiagnostic('');
  };

  const scanLoop = () => {
    if (!videoRef.current || !canvasRef.current || !scanning) return;

    if (videoRef.current.readyState === videoRef.current.HAVE_ENOUGH_DATA) {
      const canvas = canvasRef.current;
      const video = videoRef.current;
      const ctx = canvas.getContext("2d", { willReadFrequently: true });
      
      canvas.height = video.videoHeight;
      canvas.width = video.videoWidth;
      ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
      
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      const code = jsQR(imageData.data, imageData.width, imageData.height, {
        inversionAttempts: "dontInvert",
      });
      
      if (code && code.data) {
        const now = Date.now();
        const lastScan = lastScannedRef.current;
        // Prevent duplicate scans within 3 seconds
        if (lastScan.payload !== code.data || (now - lastScan.timestamp > 3000)) {
          lastScannedRef.current = { payload: code.data, timestamp: now };
          updateDiagnostic('QR Detected! Extracting payload...');
          
          // Pause scanning while processing
          setScanning(false);
          stopCamera();
          processPayload(code.data, 'live');
          return; 
        }
      }
    }
    if (scanning) {
      requestRef.current = requestAnimationFrame(scanLoop);
    }
  };

  const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;

    stopCamera();
    setError(null);
    setLoading(true);

    const reader = new FileReader();
    reader.onload = (event) => {
      const img = new Image();
      img.onload = () => {
        const canvas = document.createElement('canvas');
        canvas.width = img.width;
        canvas.height = img.height;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, img.width, img.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        
        const code = jsQR(imageData.data, imageData.width, imageData.height);
        if (code) {
          processPayload(code.data, 'upload');
        } else {
          setError('No QR code found in the image. Please try a clearer image.');
          setLoading(false);
        }
      };
      img.src = event.target.result;
    };
    reader.readAsDataURL(file);
  };

  const processPayload = async (payload, inputType) => {
    try {
      setLoading(true);
      updateDiagnostic('Sending payload to analysis engine...');
      const response = await axios.post('http://localhost:3000/api/scan', {
        payload,
        input_type: inputType
      });
      
      setLoading(false);
      navigate(`/report/${response.data.scan_id}`, { state: { report: response.data } });
    } catch (err) {
      console.error(err);
      setError('Failed to analyze QR code. Backend server might be down.');
      setLoading(false);
    }
  };

  return (
    <div className="max-w-2xl mx-auto">
      <div className="text-center mb-10">
        <h1 className="text-4xl font-bold text-white mb-4">Secure QR Scanner</h1>
        <p className="text-gray-400">Scan or upload a QR code. We will intercept and analyze the destination before you open it.</p>
      </div>

      <div className="glass-panel rounded-2xl p-8 shadow-2xl relative overflow-hidden">
        {/* Animated grid background */}
        <div className="absolute inset-0 opacity-10 pointer-events-none" style={{ backgroundImage: 'radial-gradient(#3b82f6 1px, transparent 1px)', backgroundSize: '30px 30px' }}></div>
        
        <div className="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10 mb-6">
          {!cameraActive ? (
            <button onClick={startCamera} className="flex flex-col items-center justify-center p-8 border-2 border-dashed border-cyber-border rounded-xl hover:border-cyber-primary hover:bg-blue-900/20 transition-all group">
              <Camera className="w-16 h-16 text-gray-500 group-hover:text-cyber-primary mb-4 transition-colors" />
              <span className="text-lg font-semibold text-white">Start Camera</span>
              <span className="text-sm text-gray-400 mt-2">Requires permissions</span>
            </button>
          ) : (
            <button onClick={stopCamera} className="flex flex-col items-center justify-center p-8 border-2 border-solid border-cyber-danger rounded-xl hover:bg-red-900/20 transition-all group">
              <VideoOff className="w-16 h-16 text-cyber-danger mb-4 transition-colors" />
              <span className="text-lg font-semibold text-red-400">Stop Camera</span>
            </button>
          )}

          <button 
            onClick={() => fileInputRef.current?.click()}
            className="flex flex-col items-center justify-center p-8 border-2 border-dashed border-cyber-border rounded-xl hover:border-cyber-primary hover:bg-blue-900/20 transition-all group"
          >
            <Upload className="w-16 h-16 text-gray-500 group-hover:text-cyber-primary mb-4 transition-colors" />
            <span className="text-lg font-semibold text-white">Upload Image</span>
            <span className="text-sm text-gray-400 mt-2">PNG, JPG, WEBP</span>
          </button>
          <input 
            type="file" 
            ref={fileInputRef} 
            onChange={handleImageUpload} 
            accept="image/*" 
            className="hidden" 
          />
        </div>

        {/* Camera Viewport */}
        {cameraActive && (
          <div className="relative rounded-xl overflow-hidden border-2 border-cyber-primary mb-6 bg-black flex justify-center items-center">
            <video ref={videoRef} className="w-full max-h-[400px] object-cover" />
            <canvas ref={canvasRef} className="hidden" />
            
            {/* Scanner overlay */}
            <div className="absolute inset-0 pointer-events-none border-[3px] border-transparent">
               <div className="w-full h-full relative">
                  <div className="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-cyber-neon m-4"></div>
                  <div className="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-cyber-neon m-4"></div>
                  <div className="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-cyber-neon m-4"></div>
                  <div className="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-cyber-neon m-4"></div>
                  {scanning && <div className="absolute top-1/2 left-4 right-4 h-0.5 bg-cyber-neon shadow-[0_0_10px_#0ff] animate-[scan_2s_ease-in-out_infinite]"></div>}
               </div>
            </div>
          </div>
        )}

        {diagnosticMsg && !error && !loading && (
           <p className="text-cyber-safe text-sm text-center mb-4">{diagnosticMsg}</p>
        )}

        {error && (
          <div className="mb-6 p-4 bg-red-900/20 border border-red-500/50 rounded-lg flex items-start gap-3">
            <AlertCircle className="w-6 h-6 text-red-500 flex-shrink-0" />
            <p className="text-red-200">{error}</p>
          </div>
        )}

        {loading && (
          <div className="mt-8 flex flex-col items-center justify-center">
            <Loader className="w-10 h-10 text-cyber-primary animate-spin mb-4" />
            <p className="text-cyber-neon animate-pulse">{diagnosticMsg || 'Analyzing payload & querying Threat Intelligence...'}</p>
          </div>
        )}
      </div>
    </div>
  );
}
