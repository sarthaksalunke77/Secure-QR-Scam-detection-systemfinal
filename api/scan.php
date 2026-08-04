<?php
error_reporting(0); // Suppress warnings from polluting JSON output
ini_set('display_errors', 0);
require_once 'db.php';
header('Content-Type: application/json');

// Read JSON payload
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['payload'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Payload is required']);
    exit;
}

$payload = $data['payload'];
$inputType = $data['input_type'] ?? 'live';
$qrImage = $data['qr_image'] ?? null;

require_once 'services/RiskEngine.php';

$engine = new RiskEngine();
$result = $engine->processPayload($payload, $qrImage, ['input_type' => $inputType]);

$detailsJson = json_encode($result);
$payloadType = $result['payloadClass']['type'];
$finalUrl = ($result['redirectCheck'] ?? [])['finalUrl'] ?? $payload;
$riskScore = $result['scoring']['riskScore'];
$trustScore = $result['scoring']['trustScore'];
$riskLevel = $result['scoring']['verdict'];
$confidence = $result['scoring']['confidence'];

try {
    $stmt = $db->prepare("
        INSERT INTO scan_sessions (input_type, payload_type, original_payload, final_url, risk_score, trust_score, risk_level, confidence, qr_image, details_json) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $inputType, $payloadType, $payload, $finalUrl, $riskScore, $trustScore, $riskLevel, $confidence, $qrImage, $detailsJson
    ]);

    $scanId = $db->lastInsertId();

    $response = $result;
    $response['scan_id'] = $scanId;
    $response['timestamp'] = date('Y-m-d\TH:i:s\Z');
    $response['originalUrl'] = $payload;
    $response['finalUrl'] = $finalUrl;
    
    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
