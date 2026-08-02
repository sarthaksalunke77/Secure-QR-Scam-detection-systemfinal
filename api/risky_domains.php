<?php
require_once 'db.php';

header('Content-Type: application/json');

$days = isset($_GET['days']) ? $_GET['days'] : '7';

try {
    $whereClause = "risk_level IN ('DANGEROUS', 'SUSPICIOUS', 'WARNING')";
    $params = [];

    if ($days !== 'all') {
        $whereClause .= " AND timestamp >= datetime('now', ?)";
        $params[] = '-' . (int)$days . ' days';
    }

    // Group by original_payload and count
    $query = "
        SELECT 
            original_payload, 
            risk_level,
            COUNT(*) as count
        FROM scan_sessions
        WHERE $whereClause
        GROUP BY original_payload, risk_level
        ORDER BY count DESC
        LIMIT 5
    ";

    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $domains = [];
    foreach ($results as $row) {
        $urlStr = $row['original_payload'];
        $domain = $urlStr;
        
        // Very simple URL parser for domains
        if (preg_match('/^(?:https?:\/\/)?(?:www\.)?([^\/]+)/i', $urlStr, $matches)) {
            $domain = $matches[1];
        }

        $level = $row['risk_level'] === 'DANGEROUS' ? 'Dangerous' : 'Suspicious';
        
        if (!isset($domains[$domain])) {
            $domains[$domain] = [
                'domain' => $domain,
                'count' => 0,
                'level' => $level,
                'color' => $level === 'Dangerous' ? 'bg-red-500' : 'bg-orange-400'
            ];
        }
        $domains[$domain]['count'] += $row['count'];
        
        // Upgrade level if a dangerous scan is found for the same domain
        if ($level === 'Dangerous') {
            $domains[$domain]['level'] = 'Dangerous';
            $domains[$domain]['color'] = 'bg-red-500';
        }
    }

    // Sort by count desc and take top 3
    usort($domains, function($a, $b) {
        return $b['count'] - $a['count'];
    });
    $domains = array_slice($domains, 0, 3);

    echo json_encode($domains);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
