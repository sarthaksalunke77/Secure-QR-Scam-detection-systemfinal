<?php
require_once 'api/db.php';

$type = $_GET['type'] ?? 'all';

$filename = "fraudeye_report_" . date('Y-m-d') . ".csv";
if ($type === 'weekly') $filename = "Weekly_Threat_Summary_" . date('Y-m-d') . ".csv";
if ($type === 'monthly') $filename = "Monthly_Activity_Log_" . date('Y-m-d') . ".csv";
if ($type === 'dangerous') $filename = "Dangerous_Links_Archive_" . date('Y-m-d') . ".csv";

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM for Excel compatibility
fwrite($output, "\xEF\xBB\xBF");

// Output column headings
fputcsv($output, ['ID', 'Original Payload', 'Final URL', 'Risk Level', 'Trust Score', 'Timestamp']);

// Fetch all scans based on type
try {
    $query = "SELECT id, original_payload, final_url, risk_level, trust_score, timestamp FROM scan_sessions ";
    
    if ($type === 'weekly') {
        $query .= "WHERE timestamp >= datetime('now', '-7 days') ";
    } else if ($type === 'monthly') {
        $query .= "WHERE timestamp >= datetime('now', '-1 month') ";
    } else if ($type === 'dangerous') {
        $query .= "WHERE risk_level = 'DANGEROUS' ";
    }
    
    $query .= "ORDER BY timestamp DESC";
    
    // SQLite uses datetime('now', '-7 days'). If using MySQL, it would be DATE_SUB(NOW(), INTERVAL 7 DAY).
    // The previous risk engine used SQLite. Let's make it compatible with both just in case, but standardizing on standard SQL if possible.
    // Assuming SQLite based on previous files, `datetime('now', '-7 days')` is safest. Wait, if it's MySQL, `datetime` might fail.
    // Let's use PHP to calculate the date string and pass it to the query to be 100% database agnostic!
    
    $query = "SELECT scan_id, original_payload, final_url, risk_level, trust_score, timestamp FROM scan_sessions ";
    
    if ($type === 'weekly') {
        $date = date('Y-m-d H:i:s', strtotime('-7 days'));
        $query .= "WHERE timestamp >= '$date' ";
    } else if ($type === 'monthly') {
        $date = date('Y-m-d H:i:s', strtotime('-1 month'));
        $query .= "WHERE timestamp >= '$date' ";
    } else if ($type === 'dangerous') {
        $query .= "WHERE risk_level = 'DANGEROUS' ";
    }
    
    $query .= "ORDER BY timestamp DESC";
    
    $stmt = $db->query($query);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($output, [
            $row['scan_id'],
            $row['original_payload'],
            $row['final_url'],
            $row['risk_level'],
            $row['trust_score'] !== null ? $row['trust_score'] . '%' : 'N/A',
            $row['timestamp']
        ]);
    }
} catch (PDOException $e) {
    // Ignore error in CSV stream
}

fclose($output);
exit();
?>
