<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    // Delete all scan related records from all tables
    $db->exec("DELETE FROM action_logs");
    $db->exec("DELETE FROM payment_checks");
    $db->exec("DELETE FROM threat_indicators");
    $db->exec("DELETE FROM url_analysis");
    $db->exec("DELETE FROM scan_sessions");
    
    // Also reset autoincrement sequences to keep IDs clean
    $db->exec("DELETE FROM sqlite_sequence WHERE name IN ('action_logs', 'payment_checks', 'threat_indicators', 'url_analysis', 'scan_sessions')");
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
