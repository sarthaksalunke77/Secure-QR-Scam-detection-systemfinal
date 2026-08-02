const sqlite3 = require('sqlite3').verbose();
const path = require('path');

const dbPath = path.resolve(__dirname, 'fraudeye.db');

const db = new sqlite3.Database(dbPath, (err) => {
    if (err) {
        console.error('Could not connect to database', err);
    } else {
        console.log('Connected to SQLite database');
    }
});

// Create tables if they don't exist
db.serialize(() => {
    db.run(`
        CREATE TABLE IF NOT EXISTS users (
            user_id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE,
            password_hash TEXT,
            role TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    `);

    db.run(`
        CREATE TABLE IF NOT EXISTS batch_jobs (
            batch_id TEXT PRIMARY KEY,
            status TEXT DEFAULT 'QUEUED',
            total_items INTEGER DEFAULT 0,
            processed_items INTEGER DEFAULT 0,
            safe_count INTEGER DEFAULT 0,
            suspicious_count INTEGER DEFAULT 0,
            dangerous_count INTEGER DEFAULT 0,
            failed_count INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    `);

    db.run(`
        CREATE TABLE IF NOT EXISTS scan_sessions (
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
        )
    `);

    // Safely attempt to add columns if they don't exist (for older SQLite databases)
    db.run(`ALTER TABLE scan_sessions ADD COLUMN qr_image TEXT`, () => {});
    db.run(`ALTER TABLE scan_sessions ADD COLUMN details_json TEXT`, () => {});
    db.run(`ALTER TABLE scan_sessions ADD COLUMN batch_id TEXT`, () => {});

    db.run(`
        CREATE TABLE IF NOT EXISTS url_analysis (
            analysis_id INTEGER PRIMARY KEY AUTOINCREMENT,
            scan_id INTEGER,
            domain TEXT,
            ssl_status TEXT,
            ssl_issuer TEXT,
            redirect_count INTEGER,
            suspicious_keywords TEXT,
            threat_intel_result TEXT,
            FOREIGN KEY (scan_id) REFERENCES scan_sessions(scan_id)
        )
    `);

    db.run(`
        CREATE TABLE IF NOT EXISTS threat_indicators (
            indicator_id INTEGER PRIMARY KEY AUTOINCREMENT,
            scan_id INTEGER,
            indicator_type TEXT,
            severity TEXT,
            description TEXT,
            FOREIGN KEY (scan_id) REFERENCES scan_sessions(scan_id)
        )
    `);

    db.run(`
        CREATE TABLE IF NOT EXISTS payment_checks (
            payment_id INTEGER PRIMARY KEY AUTOINCREMENT,
            scan_id INTEGER,
            receiver_name TEXT,
            upi_id TEXT,
            amount REAL,
            merchant_status TEXT,
            FOREIGN KEY (scan_id) REFERENCES scan_sessions(scan_id)
        )
    `);

    db.run(`
        CREATE TABLE IF NOT EXISTS action_logs (
            action_id INTEGER PRIMARY KEY AUTOINCREMENT,
            scan_id INTEGER,
            action TEXT,
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (scan_id) REFERENCES scan_sessions(scan_id)
        )
    `);
});

module.exports = db;
