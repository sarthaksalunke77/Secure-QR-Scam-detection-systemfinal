<?php
require 'api/db.php';
$stmt = $db->query('SELECT original_payload, risk_score, risk_level, JSON_EXTRACT(details_json, "$.scoring.evidence") as ev FROM scan_sessions ORDER BY scan_id DESC LIMIT 10');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
