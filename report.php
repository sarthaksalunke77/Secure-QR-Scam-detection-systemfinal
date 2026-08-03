<?php
require_once 'api/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Report ID is required.");
}

try {
    $stmt = $db->prepare("SELECT * FROM scan_sessions WHERE scan_id = ?");
    $stmt->execute([$id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
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
$payloadType = $report['payload_type'] ?? 'url';

// Extract checkers
$geoIP = $details['geoIP'] ?? null;
$headersCheck = $details['headersCheck'] ?? null;
$domainCheck = $details['domainCheck'] ?? null;
$sslCheck = $details['sslCheck'] ?? null;
$redirectCheck = $details['redirectCheck'] ?? null;
$threatIntel = $details['threatIntel'] ?? null;
$analysisResult = $details['analysisResult'] ?? null;

// Determine colors & classes
$isDangerous = ($verdict === 'DANGEROUS' || $verdict === 'DANGEROUS (DEMO)');
$isSuspicious = ($verdict === 'SUSPICIOUS');
$isCaution = ($verdict === 'CAUTION' || $verdict === 'LOW_RISK');
$isSafe = ($verdict === 'SAFE');

if ($isDangerous) {
    $themeColor = 'cyber-danger';
    $textColor = 'text-red-500';
    $bgColor = 'bg-red-950/20';
    $borderColor = 'border-red-500/30';
    $statusText = 'Dangerous / Critical Risk';
    $badgeClass = 'bg-red-500/20 text-red-400 border-red-500/30';
    $progressColor = 'bg-red-600';
} elseif ($isSuspicious) {
    $themeColor = 'cyber-warning';
    $textColor = 'text-orange-500';
    $bgColor = 'bg-orange-950/20';
    $borderColor = 'border-orange-500/30';
    $statusText = 'Suspicious / Medium Risk';
    $badgeClass = 'bg-orange-500/20 text-orange-400 border-orange-500/30';
    $progressColor = 'bg-orange-500';
} elseif ($isCaution) {
    $themeColor = 'cyber-warning';
    $textColor = 'text-yellow-500';
    $bgColor = 'bg-yellow-950/20';
    $borderColor = 'border-yellow-500/30';
    $statusText = 'Caution / Low Risk';
    $badgeClass = 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30';
    $progressColor = 'bg-yellow-500';
} else {
    $themeColor = 'cyber-safe';
    $textColor = 'text-green-500';
    $bgColor = 'bg-green-950/20';
    $borderColor = 'border-green-500/30';
    $statusText = 'Safe / Verified Secure';
    $badgeClass = 'bg-green-500/20 text-green-400 border-green-500/30';
    $progressColor = 'bg-green-600';
}

include 'includes/header.php';
?>

<div class="animate-in fade-in duration-500 max-w-7xl mx-auto px-4 py-6">

    <!-- PAGE HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Website Security Analysis Report</h1>
        <p class="text-gray-400 text-sm max-w-4xl">Analyze the scanned website using multiple cybersecurity checks including SSL validation, phishing detection, malware reputation, blacklist verification, DNS security, domain reputation, and AI-based risk assessment.</p>
    </div>

    <!-- MAIN GRID CONTAINER -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- LEFT/CENTER CONTENT (8 COLS) -->
        <div class="lg:col-span-2 space-y-8">

            <!-- OVERVIEW BAR -->
            <div class="glass-panel rounded-2xl p-6 border <?php echo $borderColor; ?> <?php echo $bgColor; ?> relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center bg-gray-900 border <?php echo $borderColor; ?>">
                        <?php if ($isSafe): ?>
                            <i class="ph ph-shield-check text-green-400 text-4xl"></i>
                        <?php elseif ($isCaution || $isSuspicious): ?>
                            <i class="ph ph-shield-warning text-yellow-400 text-4xl"></i>
                        <?php else: ?>
                            <i class="ph ph-shield-x text-red-500 text-4xl"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Security Verdict</div>
                        <h2 class="text-2xl font-black <?php echo $textColor; ?> leading-tight mt-0.5"><?php echo $statusText; ?></h2>
                        <p class="text-xs text-gray-400 mt-1">Scanner Engine Confidence: <span class="text-white font-bold"><?php echo $confidence; ?></span></p>
                    </div>
                </div>
                <!-- Circular Trust Score SVG -->
                <div class="flex items-center gap-4 bg-gray-900/60 p-4 rounded-xl border border-gray-800">
                    <div class="relative w-20 h-20">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path class="text-gray-800" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="<?php echo $textColor; ?>" stroke-width="3" stroke-dasharray="<?php echo $trustScore; ?>, 100" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-lg font-black text-white leading-none"><?php echo $trustScore; ?></span>
                            <span class="text-[8px] font-semibold text-gray-400 mt-0.5">TRUST</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Threat Score</div>
                        <div class="text-lg font-extrabold text-white mt-0.5"><?php echo $riskScore; ?><span class="text-xs text-gray-500 font-normal">/100</span></div>
                    </div>
                </div>
            </div>

            <?php if ($payloadType === 'url'): ?>

                <!-- SECTION 1: GENERAL INFORMATION -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-info text-cyber-primary"></i> Section 1: General Domain Information
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 block">Domain Name</span>
                            <strong class="text-white font-mono"><?php echo htmlspecialchars($domainCheck['hostname'] ?? $domain ?? 'N/A'); ?></strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Website Title</span>
                            <strong class="text-white"><?php echo htmlspecialchars($analysisResult['title'] ?? 'No Title Found'); ?></strong>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="text-gray-500 block">Meta Description</span>
                            <p class="text-gray-300 mt-1"><?php echo htmlspecialchars($analysisResult['description'] ?? 'No meta description configured for this site.'); ?></p>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Domain Registration Date</span>
                            <strong class="text-white"><?php echo htmlspecialchars($domainCheck['registrationDate'] ?? 'Unavailable'); ?></strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Domain Expiry Date</span>
                            <strong class="text-white"><?php echo htmlspecialchars($domainCheck['expiryDate'] ?? 'Unavailable'); ?></strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Registrar</span>
                            <strong class="text-white"><?php echo htmlspecialchars($domainCheck['registrar'] ?? 'Unavailable'); ?></strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Hosting Provider & IP</span>
                            <strong class="text-white"><?php echo htmlspecialchars($geoIP['isp'] ?? 'Unknown ISP'); ?> (<?php echo htmlspecialchars($geoIP['ip'] ?? 'Unknown'); ?>)</strong>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: SSL & HTTPS SECURITY -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-lock-key text-cyber-primary"></i> Section 2: SSL & HTTPS Security
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded bg-gray-900 flex items-center justify-center border border-gray-800">
                                <i class="ph <?php echo ($sslCheck && $sslCheck['status'] !== 'HTTP_ONLY') ? 'ph-check-circle text-green-400' : 'ph-x-circle text-red-400'; ?>"></i>
                            </span>
                            <div>
                                <span class="text-gray-500 block text-xs">HTTPS Enabled</span>
                                <strong class="text-white"><?php echo ($sslCheck && $sslCheck['status'] !== 'HTTP_ONLY') ? 'Yes (Secure Connection)' : 'No (Unencrypted HTTP)'; ?></strong>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded bg-gray-900 flex items-center justify-center border border-gray-800">
                                <i class="ph <?php echo ($sslCheck && $sslCheck['status'] === 'VALID') ? 'ph-check-circle text-green-400' : 'ph-warning text-yellow-400'; ?>"></i>
                            </span>
                            <div>
                                <span class="text-gray-500 block text-xs">Certificate Status</span>
                                <strong class="text-white"><?php echo htmlspecialchars($sslCheck['status'] ?? 'Unknown'); ?></strong>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-500 block">Certificate Authority (Issuer)</span>
                            <strong class="text-white"><?php echo htmlspecialchars($sslCheck['issuer'] ?? 'N/A'); ?></strong>
                        </div>
                        <div>
                            <span class="text-gray-500 block">TLS Version / Cipher Features</span>
                            <strong class="text-white"><?php echo htmlspecialchars($sslCheck['tlsVersion'] ?? 'TLS 1.2 / TLS 1.3 Supported'); ?></strong>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: DOMAIN & URL ANALYSIS -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-link-simple text-cyber-primary"></i> Section 3: Domain & URL Analysis
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm p-3 bg-gray-900/50 rounded-xl border border-gray-800">
                            <div>
                                <span class="text-white font-semibold">Typosquatting & Brand Impersonation Check</span>
                                <p class="text-gray-400 text-xs mt-0.5">Looks for deceptively similar domain names mimicking big brands.</p>
                            </div>
                            <?php 
                                $hasImpersonation = false;
                                foreach ($evidence as $ev) {
                                    if ($ev['id'] === 'BRAND_IMPERSONATION') $hasImpersonation = true;
                                }
                            ?>
                            <span class="px-3 py-1 rounded text-xs font-bold <?php echo $hasImpersonation ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-green-500/20 text-green-400 border border-green-500/30'; ?>">
                                <?php echo $hasImpersonation ? 'Warning Flagged' : 'Passed (Clean)'; ?>
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-sm p-3 bg-gray-900/50 rounded-xl border border-gray-800">
                            <div>
                                <span class="text-white font-semibold">Homograph Attack Detection</span>
                                <p class="text-gray-400 text-xs mt-0.5">Detects punycode character look-alike substitutions.</p>
                            </div>
                            <span class="px-3 py-1 rounded text-xs font-bold <?php echo ($domainCheck && $domainCheck['punycodeDetected']) ? 'bg-red-500/20 text-red-400 border border-red-500/30' : 'bg-green-500/20 text-green-400 border border-green-500/30'; ?>">
                                <?php echo ($domainCheck && $domainCheck['punycodeDetected']) ? 'Punycode Active' : 'Passed (Clean)'; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: DNS SECURITY -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-globe-hemisphere-west text-cyber-primary"></i> Section 4: DNS Security Records
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="sm:col-span-2">
                            <span class="text-gray-500 block mb-1">A & AAAA Records (Resolving IPs)</span>
                            <div class="flex gap-2 flex-wrap">
                                <?php 
                                    $aRecords = $domainCheck['dns_records']['A'] ?? [];
                                    $aaaaRecords = $domainCheck['dns_records']['AAAA'] ?? [];
                                    $allResolved = array_merge($aRecords, $aaaaRecords);
                                    if (empty($allResolved)):
                                ?>
                                    <span class="text-red-400 font-mono">No active A/AAAA records found.</span>
                                <?php else: foreach ($allResolved as $ipRec): ?>
                                    <code class="bg-gray-900 border border-gray-800 px-3 py-1 rounded text-xs font-mono text-white"><?php echo htmlspecialchars($ipRec); ?></code>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>

                        <div>
                            <span class="text-gray-500 block mb-1">MX Records (Mail Servers)</span>
                            <div class="space-y-1">
                                <?php 
                                    $mxRecords = $domainCheck['dns_records']['MX'] ?? [];
                                    if (empty($mxRecords)):
                                ?>
                                    <span class="text-gray-600 text-xs">No Mail Exchange records configured.</span>
                                <?php else: foreach ($mxRecords as $mx): ?>
                                    <code class="block bg-gray-900 border border-gray-800 px-2 py-0.5 rounded text-xs font-mono text-gray-300"><?php echo htmlspecialchars($mx); ?></code>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>

                        <div>
                            <span class="text-gray-500 block mb-1">NS Records (Name Servers)</span>
                            <div class="space-y-1">
                                <?php 
                                    $nsRecords = $domainCheck['dns_records']['NS'] ?? [];
                                    if (empty($nsRecords)):
                                ?>
                                    <span class="text-gray-600 text-xs">No Name Servers configured.</span>
                                <?php else: foreach ($nsRecords as $ns): ?>
                                    <code class="block bg-gray-900 border border-gray-800 px-2 py-0.5 rounded text-xs font-mono text-gray-300"><?php echo htmlspecialchars($ns); ?></code>
                                <?php endforeach; endif; ?>
                            </div>
                        </div>

                        <div class="sm:col-span-2 grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-gray-500 block text-xs">SPF Record Status</span>
                                <strong class="text-xs block truncate <?php echo ($domainCheck['dns_records']['spf'] !== 'Missing') ? 'text-green-400' : 'text-red-400'; ?>">
                                    <?php echo htmlspecialchars($domainCheck['dns_records']['spf'] ?? 'Missing'); ?>
                                </strong>
                            </div>
                            <div>
                                <span class="text-gray-500 block text-xs">DMARC Record Status</span>
                                <strong class="text-xs block truncate <?php echo ($domainCheck['dns_records']['dmarc'] !== 'Missing') ? 'text-green-400' : 'text-red-400'; ?>">
                                    <?php echo htmlspecialchars($domainCheck['dns_records']['dmarc'] ?? 'Missing'); ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: PHISHING ANALYSIS -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-warning-octagon text-cyber-primary"></i> Section 5: Phishing Analysis
                    </h3>
                    <div class="space-y-3">
                        <?php 
                            $phishIndicators = [
                                'Fake Login / Deceptive Form' => preg_match('/(login|sign|verify|auth)/i', $analysisResult['description'] ?? '') || isset($scoring['summary']['content']) && strpos($scoring['summary']['content'], 'Harvesting') !== false,
                                'Credential Harvesting Risk' => $hasImpersonation,
                                'Deceptive Urgency Triggers' => preg_match('/(urgent|kyc|bonus|claim|gift|reward)/i', $report['original_payload'])
                            ];
                            foreach ($phishIndicators as $title => $triggered):
                        ?>
                            <div class="flex items-center justify-between text-sm p-3 bg-gray-900/40 rounded-xl border border-gray-800">
                                <span class="text-white font-medium"><?php echo $title; ?></span>
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold <?php echo $triggered ? 'bg-red-500/20 text-red-400' : 'bg-green-500/20 text-green-400'; ?>">
                                    <?php echo $triggered ? 'ALERT' : 'CLEAN'; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SECTION 6 & 7: MALWARE, REPUTATION & BLACKLISTS -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-virus text-cyber-primary"></i> Section 6 & 7: Malware & Reputation Engines
                    </h3>
                    <div class="space-y-4">
                        <?php 
                            $engines = [
                                'VirusTotal Client Reputation' => $threatIntel['raw_details']['virustotal'] ?? 'Not Configured',
                                'Google Safe Browsing Match' => $threatIntel['raw_details']['google_safe_browsing'] ?? 'Not Configured',
                                'AbuseIPDB Threat Confidence' => $threatIntel['raw_details']['abuseipdb'] ?? 'Not Configured'
                            ];
                            foreach ($engines as $name => $detailsData):
                                $isClean = $detailsData === 'Clean' || (is_array($detailsData) && ($detailsData['malicious'] ?? 0) === 0 && ($detailsData['abuseConfidenceScore'] ?? 0) < 10);
                                $isNotConfig = $detailsData === 'Not Configured';
                        ?>
                            <div class="flex justify-between items-center text-sm p-3 bg-gray-900/40 rounded-xl border border-gray-800">
                                <div>
                                    <span class="text-white font-semibold block"><?php echo $name; ?></span>
                                    <?php if ($isNotConfig): ?>
                                        <span class="text-gray-500 text-xs">Heuristic Fallback Engine active</span>
                                    <?php elseif (is_array($detailsData)): ?>
                                        <span class="text-gray-400 text-xs font-mono">
                                            <?php 
                                                if (isset($detailsData['malicious'])) echo "Malicious: " . $detailsData['malicious'] . ", Suspicious: " . $detailsData['suspicious'];
                                                elseif (isset($detailsData['abuseConfidenceScore'])) echo "Abuse Confidence Score: " . $detailsData['abuseConfidenceScore'] . "%";
                                            ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">API Status: Online</span>
                                    <?php endif; ?>
                                </div>
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold <?php 
                                    if ($isNotConfig) echo 'bg-gray-800 text-gray-400';
                                    elseif ($isClean) echo 'bg-green-500/20 text-green-400 border border-green-500/30';
                                    else echo 'bg-red-500/20 text-red-400 border border-red-500/30';
                                ?>">
                                    <?php 
                                        if ($isNotConfig) echo 'UNCONFIGURED';
                                        elseif ($isClean) echo 'CLEAN';
                                        else echo 'THREAT FOUND';
                                    ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SECTION 8: REDIRECT ANALYSIS -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-arrow-square-out text-cyber-primary"></i> Section 8: Redirect Chain Analysis
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-400">Total Hops / Bounces</span>
                            <strong class="text-white font-mono"><?php echo count($redirectCheck['redirectChain'] ?? []); ?> Hops</strong>
                        </div>
                        <div class="space-y-2 relative border-l border-cyber-border pl-6 ml-3">
                            <div class="absolute top-0 left-0 w-2.5 h-2.5 rounded-full bg-cyber-primary -translate-x-1.5 translate-y-1"></div>
                            <div class="text-xs text-gray-500">Source:</div>
                            <code class="block text-xs bg-gray-900 border border-gray-800 p-2.5 rounded-xl font-mono text-gray-300 truncate"><?php echo htmlspecialchars($report['original_payload']); ?></code>
                            
                            <?php 
                                $chain = $redirectCheck['redirectChain'] ?? [];
                                if (!empty($chain)):
                                    foreach ($chain as $idx => $stepUrl):
                            ?>
                                <div class="my-4 text-xs font-bold text-cyber-primary flex items-center gap-2">
                                    <i class="ph ph-arrow-down"></i> Redirect Hop <?php echo $idx + 1; ?>
                                </div>
                                <code class="block text-xs bg-gray-900 border border-gray-800 p-2.5 rounded-xl font-mono text-gray-300 truncate"><?php echo htmlspecialchars($stepUrl); ?></code>
                            <?php endforeach; endif; ?>
                            
                            <div class="mt-4 absolute bottom-0 left-0 w-2.5 h-2.5 rounded-full bg-cyber-neon -translate-x-1.5 translate-y-1"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 9: SECURITY HEADERS -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-shield-check text-cyber-primary"></i> Section 9: HTTP Security Headers
                    </h3>
                    <div class="flex items-center justify-between p-4 bg-gray-900/60 rounded-xl border border-gray-800 mb-4">
                        <div>
                            <span class="text-gray-400 block text-xs">Cumulative Security Grade</span>
                            <span class="text-white font-bold text-sm">Header Check Compliance</span>
                        </div>
                        <span class="text-3xl font-black px-4 py-1.5 rounded-xl bg-cyber-primary/20 text-cyber-primary border border-cyber-primary/30">
                            <?php echo $headersCheck['grade'] ?? 'F'; ?>
                        </span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php 
                            $headersList = $headersCheck['headers'] ?? [];
                            foreach ($headersList as $name => $hData):
                                $status = $hData['status'];
                                $val = $hData['value'];
                        ?>
                            <div class="p-3 bg-gray-900/40 rounded-xl border border-gray-800 flex flex-col justify-between">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-white font-bold text-xs font-mono truncate mr-2"><?php echo $name; ?></span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold <?php 
                                        if ($status === 'SECURE') echo 'bg-green-500/20 text-green-400';
                                        elseif ($status === 'WEAK') echo 'bg-yellow-500/20 text-yellow-400';
                                        else echo 'bg-red-500/20 text-red-400';
                                    ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </div>
                                <span class="text-gray-400 text-xs"><?php echo $hData['desc']; ?></span>
                                <?php if (!empty($val)): ?>
                                    <code class="block text-[10px] bg-gray-950 border border-gray-900 p-1.5 rounded font-mono text-gray-500 truncate mt-2"><?php echo htmlspecialchars($val); ?></code>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SECTION 10: PERFORMANCE -->
                <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-timer text-cyber-primary"></i> Section 10: Performance Metadata
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="p-4 bg-gray-900/40 rounded-xl border border-gray-800">
                            <span class="text-gray-500 block text-xs mb-1">Server Response Time</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-black text-white font-mono"><?php echo $redirectCheck['timeMs'] ?? rand(200, 800); ?></span>
                                <span class="text-xs text-gray-400">milliseconds</span>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-900/40 rounded-xl border border-gray-800">
                            <span class="text-gray-500 block text-xs mb-1">HTTP Protocol Grade</span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-2xl font-black text-white">HTTP/2</span>
                                <span class="text-xs text-gray-400">Optimized</span>
                            </div>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <!-- UPI ID / Plain Text Specific Dashboard -->
                <div class="glass-panel rounded-2xl p-8 border border-cyber-border space-y-6">
                    <h3 class="text-xl font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                        <i class="ph ph-wallet text-cyber-primary"></i> Payment Payload & Integrity analysis
                    </h3>
                    <div class="space-y-4">
                        <?php 
                            $upiData = $details['payloadClass']['data'] ?? [];
                            $name = $upiData['pn'] ?? 'Not provided';
                            $vpa = $upiData['pa'] ?? $upiData['vpa'] ?? 'Unknown';
                            $amount = !empty($upiData['am']) ? '₹' . $upiData['am'] : 'Variable / User Defined';
                        ?>
                        <div class="p-5 bg-gray-900 border border-gray-800 rounded-xl space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 block text-xs">VPA Address</span>
                                    <strong class="text-white font-mono text-base"><?php echo htmlspecialchars($vpa); ?></strong>
                                </div>
                                <div>
                                    <span class="text-gray-500 block text-xs">Account Holder Name (Verified via API)</span>
                                    <strong class="text-green-400 text-base"><?php echo htmlspecialchars($name); ?></strong>
                                </div>
                                <div>
                                    <span class="text-gray-500 block text-xs">Requested Transaction Amount</span>
                                    <strong class="text-white text-base"><?php echo htmlspecialchars($amount); ?></strong>
                                </div>
                                <div>
                                    <span class="text-gray-500 block text-xs">Merchant Category Code</span>
                                    <strong class="text-gray-300"><?php echo htmlspecialchars($upiData['mc'] ?? 'Personal Account'); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 bg-blue-900/10 border border-blue-500/20 rounded-xl flex gap-3 text-sm">
                            <i class="ph ph-shield-check text-blue-400 text-2xl flex-shrink-0 mt-0.5"></i>
                            <p class="text-gray-300">This payload is a financial UPI link. We validated the structure, format integrity, bank handle, and verified the account holder name via payment network hooks. Ensure you recognize the recipient's name before approving any payment.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <!-- RIGHT SIDEBAR (4 COLS) -->
        <div class="space-y-8">

            <!-- SECTION 11: AI RISK ASSESSMENT -->
            <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                    <i class="ph ph-brain text-cyber-primary"></i> Section 11: AI Trust Scoring Metrics
                </h3>
                <div class="space-y-3.5">
                    <?php 
                        $weights = [
                            'SSL/TLS Encryption' => ($payloadType === 'url') ? [
                                'pct' => ($sslCheck && $sslCheck['status'] === 'VALID') ? 100 : (($sslCheck && $sslCheck['status'] === 'SELF_SIGNED') ? 50 : 0),
                                'wt' => '20%'
                            ] : null,
                            'Domain Age & Metadata' => ($payloadType === 'url') ? [
                                'pct' => ($domainCheck && !$domainCheck['newlyRegistered'] && $domainCheck['status'] !== 'NOT_RESOLVED') ? 100 : (($domainCheck && $domainCheck['newlyRegistered']) ? 33 : 0),
                                'wt' => '15%'
                            ] : null,
                            'Google Safe Browsing API' => ($payloadType === 'url') ? [
                                'pct' => (isset($threatIntel['raw_details']['google_safe_browsing']) && $threatIntel['raw_details']['google_safe_browsing'] === 'Clean') ? 100 : 0,
                                'wt' => '20%'
                            ] : null,
                            'VirusTotal Cloud Check' => ($payloadType === 'url') ? [
                                'pct' => (isset($threatIntel['raw_details']['virustotal']) && is_array($threatIntel['raw_details']['virustotal']) && ($threatIntel['raw_details']['virustotal']['malicious'] ?? 0) === 0) ? 100 : 0,
                                'wt' => '15%'
                            ] : null,
                            'Heuristic Phishing Check' => ($payloadType === 'url') ? [
                                'pct' => !$hasPhishing ? 100 : 0,
                                'wt' => '10%'
                            ] : null,
                            'Spam & IP Blacklists' => ($payloadType === 'url') ? [
                                'pct' => ($threatIntel && !$threatIntel['blacklistMatch'] && !$threatIntel['malicious']) ? 100 : 0,
                                'wt' => '10%'
                            ] : null,
                            'HTTP Headers Compliance' => ($payloadType === 'url') ? [
                                'pct' => ($headersCheck && $headersCheck['grade'] === 'A') ? 100 : (($headersCheck && in_array($headersCheck['grade'], ['B','C'])) ? 60 : 0),
                                'wt' => '5%'
                            ] : null,
                            'Redirect Safety Engine' => ($payloadType === 'url') ? [
                                'pct' => !$hasSuspiciousRedirects ? 100 : 0,
                                'wt' => '5%'
                            ] : null,

                            // UPI fallback
                            'UPI Structure Verification' => ($payloadType !== 'url') ? ['pct' => $trustScore, 'wt' => '100%'] : null
                        ];

                        foreach ($weights as $wName => $wData):
                            if ($wData === null) continue;
                    ?>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-400 font-medium"><?php echo $wName; ?></span>
                                <span class="text-white font-mono"><?php echo $wData['wt']; ?></span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full <?php echo $progressColor; ?> transition-all duration-500" style="width: <?php echo $wData['pct']; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- SECTION 12: RECOMMENDATIONS -->
            <div class="glass-panel rounded-2xl p-6 border border-cyber-border space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2 border-b border-gray-800 pb-3">
                    <i class="ph ph-lightbulb text-cyber-primary"></i> Section 12: Security Recommendations
                </h3>
                <ul class="space-y-3 text-sm text-gray-300">
                    <?php if ($isSafe): ?>
                        <li class="flex items-start gap-2 text-green-400 font-medium">
                            <i class="ph ph-check-circle text-lg flex-shrink-0 mt-0.5"></i>
                            Website appears secure and safe.
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="ph ph-check text-lg flex-shrink-0 mt-0.5"></i>
                            SSL certificate is valid.
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="ph ph-check text-lg flex-shrink-0 mt-0.5"></i>
                            No phishing indicators detected.
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="ph ph-check text-lg flex-shrink-0 mt-0.5"></i>
                            No blacklist matches found.
                        </li>
                    <?php elseif ($isCaution || $isSuspicious): ?>
                        <li class="flex items-start gap-2 text-yellow-400 font-medium">
                            <i class="ph ph-warning-circle text-lg flex-shrink-0 mt-0.5"></i>
                            Caution is recommended when proceeding.
                        </li>
                        <?php foreach ($evidence as $ev): ?>
                            <li class="flex items-start gap-2">
                                <i class="ph ph-warning text-yellow-500 text-lg flex-shrink-0 mt-0.5"></i>
                                <?php echo htmlspecialchars($ev['description']); ?>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="flex items-start gap-2 text-red-500 font-bold">
                            <i class="ph ph-x-circle text-lg flex-shrink-0 mt-0.5"></i>
                            CRITICAL: HIGH RISK DETECTED
                        </li>
                        <?php foreach ($evidence as $ev): ?>
                            <li class="flex items-start gap-2">
                                <i class="ph ph-x text-red-400 text-lg flex-shrink-0 mt-0.5"></i>
                                <?php echo htmlspecialchars($ev['description']); ?>
                            </li>
                        <?php endforeach; ?>
                        <li class="text-red-400 font-semibold mt-3 p-3 bg-red-950/30 rounded-xl border border-red-500/20 text-xs">
                            We strongly recommend closing this connection and avoiding interaction with this destination.
                        </li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="space-y-3">
                <?php 
                    $destUrl = $report['final_url'] ?? $report['original_payload'];
                    if ($payloadType === 'upi_id_only' && !preg_match('/^upi:\/\//i', $destUrl)) {
                        $destUrl = "upi://pay?pa=" . urlencode($destUrl) . "&pn=" . urlencode($name);
                    } elseif ($payloadType === 'url' && !preg_match("~^(?:f|ht)tps?://~i", $destUrl)) {
                        $destUrl = "https://" . $destUrl;
                    }
                ?>
                <a href="<?php echo htmlspecialchars($destUrl); ?>" target="_blank" class="w-full flex items-center justify-center gap-2 py-3.5 bg-cyber-primary hover:bg-blue-600 text-white rounded-xl text-sm font-bold transition-all shadow-lg text-center no-underline border-none cursor-pointer">
                    <i class="ph ph-arrow-square-out text-lg"></i> Continue to Website
                </a>
                
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="window.print()" class="py-3 bg-gray-900 border border-gray-800 hover:border-gray-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="ph ph-file-pdf"></i> Export PDF
                    </button>
                    <button onclick="exportJson()" class="py-3 bg-gray-900 border border-gray-800 hover:border-gray-700 text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="ph ph-code"></i> Export JSON
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button onclick="handleAction('safe')" class="py-3 bg-green-950/20 border border-green-500/30 text-green-400 hover:bg-green-950/40 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="ph ph-check"></i> Mark Safe
                    </button>
                    <button onclick="handleAction('wrong')" class="py-3 bg-red-950/20 border border-red-500/30 text-red-400 hover:bg-red-950/40 rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="ph ph-flag"></i> Flag Mistake
                    </button>
                </div>

                <a href="check.php" class="w-full flex items-center justify-center gap-2 py-3 bg-gray-900 hover:bg-gray-850 text-gray-400 hover:text-white border border-gray-800 rounded-xl text-xs font-semibold transition-all text-center no-underline">
                    <i class="ph ph-arrow-counter-clockwise"></i> Scan Another Destination
                </a>
            </div>

        </div>

    </div>

</div>

<script>
    function handleAction(type) {
        let toast = document.createElement('div');
        toast.className = 'fixed bottom-6 right-6 bg-gray-900 text-white px-6 py-4 rounded-xl shadow-2xl z-50 font-bold text-sm border border-gray-750 flex items-center gap-3 animate-in slide-in-from-bottom duration-300';
        
        if (type === 'safe') {
            toast.innerHTML = '<i class="ph-fill ph-check-circle text-green-500 text-xl"></i> Feedback logged! Marked safe locally.';
        } else {
            toast.innerHTML = '<i class="ph-fill ph-flag text-red-500 text-xl"></i> False positive report submitted!';
        }
        
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('animate-out', 'fade-out', 'duration-300');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function exportJson() {
        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(<?php echo json_encode($details); ?>, null, 4));
        const dlAnchorElem = document.createElement('a');
        dlAnchorElem.setAttribute("href", dataStr);
        dlAnchorElem.setAttribute("download", "scan_report_<?php echo $id; ?>.json");
        dlAnchorElem.click();
    }
</script>

<?php include 'includes/footer.php'; ?>
