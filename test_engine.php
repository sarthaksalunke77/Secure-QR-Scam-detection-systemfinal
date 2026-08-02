<?php
require_once 'api/services/RiskEngine.php';
$engine = new RiskEngine();
$payloads = [
    'https://github.com', // Safe
    'http://secure-login-update.example.com', // Warning
    'https://phishingsite.net' // Dangerous
];

foreach ($payloads as $p) {
    echo "=====================================\n";
    echo "Testing: $p\n";
    $result = $engine->processPayload($p);
    echo "Score: " . $result['scoring']['riskScore'] . " / 100\n";
    echo "Verdict: " . $result['scoring']['verdict'] . "\n";
    echo "Evidence Count: " . count($result['scoring']['evidence']) . "\n";
    foreach ($result['scoring']['evidence'] as $ev) {
        echo "- " . $ev['id'] . " (" . $ev['riskContribution'] . "): " . $ev['description'] . "\n";
    }
}
