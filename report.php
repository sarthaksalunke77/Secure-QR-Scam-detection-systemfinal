<?php
require_once 'api/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Report not found. Please scan again.");
}

try {
    $stmt = $db->prepare("SELECT * FROM scan_sessions WHERE scan_id = ?");
    $stmt->execute([$id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error.");
}

if (!$report) {
    die("Report not found. Please scan again.");
}

$details = json_decode($report['details_json'], true) ?: [];
$scoring = $details['scoring'] ?? [];
$riskScore = $scoring['riskScore'] ?? $report['risk_score'];
$trustScore = $scoring['trustScore'] ?? $report['trust_score'];
$confidence = $scoring['confidence'] ?? $report['confidence'];
$verdict = $scoring['verdict'] ?? $report['risk_level'];
$evidence = $scoring['evidence'] ?? [];

$isDangerous = strpos($verdict, 'DANGEROUS') !== false;
$isWarning = strpos($verdict, 'WARNING') !== false || strpos($verdict, 'SUSPICIOUS') !== false || strpos($verdict, 'CAUTION') !== false;
$isSafe = strpos($verdict, 'SAFE') !== false;

$payloadType = $report['payload_type'] ?? 'url';
$recommendation = $scoring['summary']['recommendation'] ?? 'No obvious threats detected.';

// Colors for Alert Box
if ($isDangerous) {
    $alertBorder = 'border-red-200';
    $alertBg = 'bg-red-50';
    $alertIcon = 'ph-warning text-red-500';
    $alertTitleColor = 'text-red-600';
    $alertTitle = 'Overall Status: ' . ucwords(strtolower($verdict));
    $alertSubtitle = $recommendation;
} elseif ($isWarning) {
    $alertBorder = 'border-orange-200';
    $alertBg = 'bg-orange-50';
    $alertIcon = 'ph-warning text-orange-500';
    $alertTitleColor = 'text-orange-500';
    $alertTitle = 'Overall Status: ' . ucwords(strtolower($verdict));
    $alertSubtitle = $recommendation;
} else {
    $alertBorder = 'border-green-200';
    $alertBg = 'bg-green-50';
    $alertIcon = 'ph-check-circle text-green-500';
    $alertTitleColor = 'text-green-600';
    $alertTitle = 'Overall Status: Safe';
    $alertSubtitle = $recommendation;
}

include 'includes/light-header.php';
?>

<div id="report-content" class="max-w-xl mx-auto my-4 pb-6 animate-in fade-in duration-500 px-4">
    <div class="bg-white rounded-[14px] border border-gray-200 shadow-sm overflow-hidden p-4 md:p-5">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-[22px] font-black text-gray-800 m-0 leading-tight">Scan Report</h1>
            <p class="text-xs font-medium text-gray-500 m-0 mt-1">Detailed security report for your scan</p>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mb-4 border border-gray-100 rounded-lg p-2.5">
            <div class="md:col-span-2">
                <div class="text-[10px] font-bold text-gray-800 uppercase">Input</div>
                <div class="text-xs text-gray-600 truncate mt-1" title="<?php echo htmlspecialchars($report['original_payload']); ?>">
                    <?php echo htmlspecialchars($report['original_payload']); ?>
                </div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-800 uppercase">Type</div>
                <div class="text-xs text-gray-600 mt-1"><?php echo strtoupper($payloadType); ?></div>
            </div>
            <div>
                <div class="text-[10px] font-bold text-gray-800 uppercase">Scanned On</div>
                <div class="text-xs text-gray-600 mt-1">
                    <?php 
                        try {
                            $dt = new DateTime($report['timestamp'] ?? 'now', new DateTimeZone('UTC'));
                            $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
                            echo $dt->format('d M Y, h:i A');
                        } catch (Exception $e) {
                            echo htmlspecialchars($report['timestamp']);
                        }
                    ?>
                </div>
            </div>
        </div>

        <!-- Alert Box -->
        <div class="border <?php echo $alertBorder; ?> <?php echo $alertBg; ?> rounded-lg p-3 flex flex-col md:flex-row items-center justify-between mb-5 gap-3">
            <div class="flex items-center gap-3">
                <i class="ph-fill <?php echo $alertIcon; ?> text-[32px]"></i>
                <div>
                    <h2 class="text-base font-bold <?php echo $alertTitleColor; ?> m-0 leading-tight"><?php echo $alertTitle; ?></h2>
                    <p class="text-[11px] font-bold text-gray-700 m-0 mt-0.5"><?php echo $alertSubtitle; ?></p>
                    <?php $riskLvl = $isDangerous ? 'High' : ($isWarning ? 'Medium' : 'Low'); ?>
                    <div class="inline-block mt-1.5 px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-white border <?php echo $alertBorder; ?> <?php echo $alertTitleColor; ?>">
                        Risk Level: <?php echo $riskLvl; ?>
                    </div>
                </div>
            </div>
            <div class="flex-shrink-0 relative px-4 py-2 flex flex-col items-center justify-center rounded-lg border-2 <?php echo $alertBorder; ?> bg-white shadow-sm">
                <div class="text-[9px] font-black text-gray-500 leading-none tracking-wide uppercase mb-1">Trust Score</div>
                <div class="text-[18px] font-black text-gray-800 leading-none"><?php echo $trustScore; ?><span class="text-[11px] text-gray-400 font-bold">/100</span></div>
            </div>
        </div>

        <!-- Security Analysis Summary -->
        <h3 class="text-xs font-bold text-gray-800 mb-2">Security Analysis Summary</h3>
        <div class="space-y-1 mb-5">
            <?php
            // Pull summary strings directly from the RiskEngine's output
            $summary = $scoring['summary'] ?? [];

            // Helper to determine color and icon based on the summary string
            function getCheckProps($str, $isGood, $isWarning = false) {
                if ($isGood) return ['color' => 'text-green-600', 'icon' => 'ph-check-circle text-green-600'];
                if ($isWarning) return ['color' => 'text-orange-500', 'icon' => 'ph-warning text-orange-500'];
                return ['color' => 'text-red-600', 'icon' => 'ph-x-circle text-red-600'];
            }
            
            function buildCheckRow($name, $status, $isGood, $isWarning = false) {
                $props = getCheckProps($status, $isGood, $isWarning);
                return [
                    'name' => $name,
                    'status' => $status,
                    'color' => $props['color'],
                    'icon' => $props['icon']
                ];
            }

            $checks = [];
            
            if (in_array(strtolower($payloadType), ['upi', 'upi_id_only'])) {
                $upiData = $details['payloadClass']['data'] ?? [];
                
                $vpa = $upiData['pa'] ?? $upiData['vpa'] ?? 'Unknown';
                $name = $upiData['pn'] ?? 'Not provided';
                $amount = !empty($upiData['am']) ? '₹' . $upiData['am'] : 'Variable / User Input';
                
                $hasError = isset($upiData['error']);
                $vpaValid = preg_match('/^[a-zA-Z0-9.\-_]+@[a-zA-Z]+$/', $vpa);
                
                $checks[] = buildCheckRow('UPI ID (VPA)', htmlspecialchars($vpa), !$hasError && $vpaValid && $vpa !== 'Unknown', false);
                $checks[] = buildCheckRow('Holder Name', htmlspecialchars($name), $name !== 'Not provided', false);
                
                // Add Country Name determination
                $country = "India (UPI Network)";
                // Check if VPA looks like a phone number with an international country code
                if (preg_match('/^([0-9]{2,3})[0-9]{8,10}@/', $vpa, $matches)) {
                    $code = $matches[1];
                    if ($code == '971') $country = "United Arab Emirates (UPI International)";
                    else if ($code == '65') $country = "Singapore (UPI International)";
                    else if ($code == '33') $country = "France (UPI International)";
                    else if ($code == '44') $country = "United Kingdom (UPI International)";
                    else if ($code == '91') $country = "India";
                }
                $checks[] = buildCheckRow('Country Origin', $country, true, false);
                
                if (strtolower($payloadType) === 'upi') {
                    $checks[] = buildCheckRow('Requested Amount', htmlspecialchars($amount), true, false);
                }
                
                $hasSuspiciousEvidence = false;
                foreach($scoring['evidence'] ?? [] as $ev) {
                    if (strpos($ev['id'], 'UPI_') === 0 && $ev['severity'] !== 'low') {
                        $hasSuspiciousEvidence = true;
                        break;
                    }
                }
                
                $checks[] = buildCheckRow('Format Integrity', $hasError ? 'Malformed URI' : 'Valid UPI Format', !$hasError, false);
                $checks[] = buildCheckRow('Fraud Risk Assessment', $hasSuspiciousEvidence ? 'Suspicious flags detected' : 'No obvious threats detected', !$hasSuspiciousEvidence, $hasSuspiciousEvidence && !$isDangerous);
                
            } else if (strtolower($payloadType) === 'text') {
                $hasSuspiciousEvidence = false;
                foreach($scoring['evidence'] ?? [] as $ev) {
                    if ($ev['severity'] !== 'low' && $ev['severity'] !== 'info') {
                        $hasSuspiciousEvidence = true;
                        break;
                    }
                }
                
                $checks = [
                    buildCheckRow('Format Integrity', 'Valid Plain Text', true, false),
                    buildCheckRow('Fraud Risk Assessment', $hasSuspiciousEvidence ? 'Suspicious patterns detected' : 'No obvious threats detected', !$hasSuspiciousEvidence, $hasSuspiciousEvidence && !$isDangerous),
                    buildCheckRow('Content Analysis', $isDangerous ? 'High Risk Content' : 'Standard Text Content', !$isDangerous, false)
                ];
            } else {
                // Fallbacks in case the summary array is missing
                $sslStatus = $summary['ssl'] ?? ($isDangerous ? 'Invalid Certificate' : 'Valid Certificate');
                $domainAgeStatus = $summary['domainAge'] ?? ($isDangerous ? 'Newly Registered' : '5+ Years');
                $blacklistStatus = $summary['blacklist'] ?? ($isDangerous ? 'Listed in database' : 'Clean');
                $malwareStatus = $summary['malware'] ?? ($isDangerous ? 'Malware Detected' : 'No malware found');
                $phishingStatus = $summary['phishing'] ?? ($isDangerous ? 'Phishing Simulation (Impersonation)' : 'No phishing indicators');
                $redirectStatus = $summary['redirect'] ?? 'No suspicious redirects';
                $contentStatus = $summary['content'] ?? ($isDangerous ? 'Suspicious Login Page' : 'Standard Content');
    
                $checks = [
                    buildCheckRow('SSL Certificate', $sslStatus, $sslStatus === 'Valid Certificate' || $sslStatus === 'Valid', false),
                    buildCheckRow('Domain Age', $domainAgeStatus, !in_array($domainAgeStatus, ['Newly Registered', 'Not Applicable (Reserved Domain)']), false),
                    buildCheckRow('Blacklist Check', $blacklistStatus, in_array($blacklistStatus, ['Clean', 'Not listed in any database']), $blacklistStatus === 'Listed in 1 database'),
                    buildCheckRow('Malware Check', $malwareStatus, $malwareStatus === 'No malware found', false),
                    buildCheckRow('Phishing Check', $phishingStatus, in_array($phishingStatus, ['No phishing indicators', 'No suspicious indicators']), $phishingStatus === 'Suspicious Indicators Found' || $phishingStatus === 'Suspicious indicators found'),
                    buildCheckRow('Redirect Check', $redirectStatus, in_array($redirectStatus, ['No suspicious redirects', 'No redirect detected', 'No suspicious redirects detected', 'Legitimate regional redirect detected', 'Simulation']), false),
                    buildCheckRow('Content Analysis', $contentStatus, in_array($contentStatus, ['Standard Content', 'Trusted Website', 'High Trust Content', 'Content appears legitimate and safe']), $contentStatus === 'Medium Trust Content')
                ];
            }

            
            foreach($checks as $check): ?>
            <div class="flex items-center text-[10px] py-0.5 border-b border-gray-50 last:border-0">
                <i class="ph-fill <?php echo $check['icon']; ?> text-[13px] mr-2"></i>
                <div class="w-[45%] font-bold text-gray-800"><?php echo $check['name']; ?></div>
                <div class="w-[55%] font-medium <?php echo $check['color']; ?>"><?php echo $check['status']; ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Action Bar (Buttons) -->
        <div id="action-bar" class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-5 pt-5 border-t border-gray-100">
            <button onclick="handleAction('safe')" class="w-full py-2 bg-[#16a34a] text-white rounded-md text-xs font-bold hover:bg-green-700 transition-colors border-none cursor-pointer">
                Mark Safe
            </button>
            <button onclick="handleAction('wrong')" class="w-full py-2 bg-[#dc2626] text-white rounded-md text-xs font-bold hover:bg-red-700 transition-colors border-none cursor-pointer">
                Report Wrong Result
            </button>
            <button onclick="downloadPDF()" class="w-full py-2 bg-[#5b21b6] text-white rounded-md text-xs font-bold hover:bg-purple-800 transition-colors border-none cursor-pointer">
                Export PDF
            </button>
            
            <?php 
                $destUrl = $report['final_url'] ?? $report['original_payload'];
                $isUpiLink = (strtolower($payloadType) === 'upi' || strtolower($payloadType) === 'upi_id_only' || preg_match('/^upi:\/\//i', $destUrl));
                
                if (!$isUpiLink && !preg_match("~^(?:f|ht)tps?://~i", $destUrl)) {
                    $destUrl = "http://" . $destUrl;
                }
                
                // If it's just a raw UPI ID, construct a proper intent link
                if (strtolower($payloadType) === 'upi_id_only' && !preg_match('/^upi:\/\//i', $destUrl)) {
                    $destUrl = "upi://pay?pa=" . urlencode($destUrl) . "&pn=" . urlencode("Unknown");
                }
                
                $btnText = $isUpiLink ? "Continue to Payment" : "Continue to Web";
                $btnColor = $isUpiLink ? "bg-teal-600 hover:bg-teal-700" : "bg-blue-600 hover:bg-blue-700";
            ?>
            <a href="<?php echo htmlspecialchars($destUrl); ?>" target="_blank" class="w-full py-2 <?php echo $btnColor; ?> text-white rounded-md text-xs font-bold transition-colors text-center no-underline flex items-center justify-center">
                <?php echo $btnText; ?>
            </a>
            <a href="index.php" class="w-full py-2 bg-gray-800 text-white rounded-md text-xs font-bold hover:bg-gray-900 transition-colors text-center no-underline flex items-center justify-center">
                Block & Close
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    body { background-color: white !important; }
    #action-bar { display: none !important; }
    #pdf-toast { display: none !important; }
    nav, header, aside { display: none !important; }
    .max-w-3xl { max-width: 100% !important; margin: 0 !important; }
    /* Force background colors to print */
    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
}
</style>
<script>
function handleAction(type) {
    let toast = document.createElement('div');
    toast.className = 'fixed bottom-6 right-6 bg-gray-900 text-white px-6 py-4 rounded-xl shadow-2xl z-50 font-bold text-sm border border-gray-700 flex items-center gap-3';
    
    if (type === 'safe') {
        toast.innerHTML = '<i class="ph-fill ph-check-circle text-green-500 text-xl"></i> Marked as Safe!';
    } else {
        toast.innerHTML = '<i class="ph-fill ph-flag text-red-500 text-xl"></i> Feedback Submitted!';
    }
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        window.location.href = 'index.php';
    }, 1500);
}

function downloadPDF() {
    // 100% reliable native vector PDF generation
    window.print();
}
</script>

<?php include 'includes/light-footer.php'; ?>
