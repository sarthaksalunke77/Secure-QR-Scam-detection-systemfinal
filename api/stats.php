<?php
require_once 'db.php';

header('Content-Type: application/json');

try {
    // Get total count
    $stmt = $db->query("SELECT COUNT(*) as total FROM scan_sessions");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get safe count
    $stmt = $db->query("SELECT COUNT(*) as safe FROM scan_sessions WHERE risk_level = 'SAFE'");
    $safe = $stmt->fetch(PDO::FETCH_ASSOC)['safe'];

    // Get suspicious count
    $stmt = $db->query("SELECT COUNT(*) as suspicious FROM scan_sessions WHERE risk_level IN ('SUSPICIOUS', 'WARNING')");
    $suspicious = $stmt->fetch(PDO::FETCH_ASSOC)['suspicious'];

    // Get dangerous count
    $stmt = $db->query("SELECT COUNT(*) as dangerous FROM scan_sessions WHERE risk_level = 'DANGEROUS'");
    $dangerous = $stmt->fetch(PDO::FETCH_ASSOC)['dangerous'];

    echo json_encode([
        'total' => (int)$total,
        'safe' => (int)$safe,
        'suspicious' => (int)$suspicious,
        'dangerous' => (int)$dangerous
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
