<?php
header('Content-Type: application/json');

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$keysFile = __DIR__ . '/keys.json';
$existing = [];

if (file_exists($keysFile)) {
    $existing = json_decode(file_get_contents($keysFile), true) ?: [];
}

if (isset($data['google'])) {
    $existing['google_safe_browsing'] = $data['google'];
}
if (isset($data['virustotal'])) {
    $existing['virustotal'] = $data['virustotal'];
}
if (isset($data['abuseipdb'])) {
    $existing['abuseipdb'] = $data['abuseipdb'];
}

file_put_contents($keysFile, json_encode($existing, JSON_PRETTY_PRINT));

echo json_encode(['success' => true]);
?>
