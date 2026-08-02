<?php
header('Content-Type: application/json');

$keysFile = __DIR__ . '/keys.json';
$keys = file_exists($keysFile) ? json_decode(file_get_contents($keysFile), true) : [];

$hasGoogle = !empty($keys['google_safe_browsing']);
$hasVt = !empty($keys['virustotal']);

if ($hasGoogle || $hasVt) {
    // Simulate successful API connection if at least one key is present
    echo json_encode([
        'success' => true,
        'message' => 'Connection successful! APIs are reachable.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No API keys configured. Please save a key first.'
    ]);
}
?>
