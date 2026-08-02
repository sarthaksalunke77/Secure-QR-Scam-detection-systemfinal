<?php
require_once 'db.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$offset = ($page - 1) * $limit;

try {
    $whereClause = "WHERE 1=1";
    $params = [];
    
    if (!empty($search)) {
        $whereClause .= " AND (original_payload LIKE ? OR final_url LIKE ? OR scan_id LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $level = isset($_GET['level']) ? strtoupper($_GET['level']) : '';
    if (!empty($level)) {
        if ($level === 'SUSPICIOUS' || $level === 'WARNING') {
            $whereClause .= " AND risk_level IN ('SUSPICIOUS', 'WARNING')";
        } else {
            $whereClause .= " AND risk_level = ?";
            $params[] = $level;
        }
    }

    // Get total count
    $countStmt = $db->prepare("SELECT COUNT(*) FROM scan_sessions $whereClause");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();

    // Get data
    $query = "SELECT * FROM scan_sessions $whereClause ORDER BY timestamp DESC LIMIT ? OFFSET ?";
    $stmt = $db->prepare($query);
    
    // Bind parameters
    $paramIndex = 1;
    if (!empty($search)) {
        $stmt->bindValue($paramIndex++, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue($paramIndex++, "%$search%", PDO::PARAM_STR);
        $stmt->bindValue($paramIndex++, "%$search%", PDO::PARAM_STR);
    }
    if (!empty($level) && $level !== 'SUSPICIOUS' && $level !== 'WARNING') {
        $stmt->bindValue($paramIndex++, $level, PDO::PARAM_STR);
    }
    $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        'data' => $data
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
