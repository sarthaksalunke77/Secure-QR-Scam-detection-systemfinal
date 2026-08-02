<?php
date_default_timezone_set('Asia/Kolkata');
// db.php
// SQLite database connection

$dbPath = __DIR__ . '/db/fraudeye.db';

try {
    $db = new PDO("sqlite:" . $dbPath);
    // Set errormode to exceptions
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Create tables if not exists (migrated from Node.js db.js)
    
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE,
        password_hash TEXT,
        role TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS batch_jobs (
        batch_id TEXT PRIMARY KEY,
        status TEXT DEFAULT 'QUEUED',
        total_items INTEGER DEFAULT 0,
        processed_items INTEGER DEFAULT 0,
        safe_count INTEGER DEFAULT 0,
        suspicious_count INTEGER DEFAULT 0,
        dangerous_count INTEGER DEFAULT 0,
        failed_count INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS scan_sessions (
        scan_id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        input_type TEXT,
        payload_type TEXT,
        original_payload TEXT,
        final_url TEXT,
        risk_score INTEGER,
        trust_score INTEGER,
        risk_level TEXT,
        confidence TEXT,
        qr_image TEXT,
        details_json TEXT,
        batch_id TEXT,
        FOREIGN KEY (user_id) REFERENCES users(user_id),
        FOREIGN KEY (batch_id) REFERENCES batch_jobs(batch_id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS url_analysis (
        analysis_id INTEGER PRIMARY KEY AUTOINCREMENT,
        scan_id INTEGER,
        domain TEXT,
        ssl_status TEXT,
        ssl_issuer TEXT,
        redirect_count INTEGER,
        suspicious_keywords TEXT,
        threat_intel_result TEXT,
        FOREIGN KEY (scan_id) REFERENCES scan_sessions(scan_id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS threat_indicators (
        indicator_id INTEGER PRIMARY KEY AUTOINCREMENT,
        scan_id INTEGER,
        indicator_type TEXT,
        severity TEXT,
        description TEXT,
        FOREIGN KEY (scan_id) REFERENCES scan_sessions(scan_id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS payment_checks (
        payment_id INTEGER PRIMARY KEY AUTOINCREMENT,
        scan_id INTEGER,
        receiver_name TEXT,
        upi_id TEXT,
        amount REAL,
        merchant_status TEXT,
        FOREIGN KEY (scan_id) REFERENCES scan_sessions(scan_id)
    )");

    $db->exec("CREATE TABLE IF NOT EXISTS action_logs (
        action_id INTEGER PRIMARY KEY AUTOINCREMENT,
        scan_id INTEGER,
        action TEXT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (scan_id) REFERENCES scan_sessions(scan_id)
    )");

} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
