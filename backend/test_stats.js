const db = require('./database/db');

db.get(`
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN risk_level = 'SAFE' THEN 1 ELSE 0 END) as safe,
        SUM(CASE WHEN risk_level = 'SUSPICIOUS' OR risk_level = 'WARNING' THEN 1 ELSE 0 END) as suspicious,
        SUM(CASE WHEN risk_level = 'DANGEROUS' THEN 1 ELSE 0 END) as dangerous
    FROM scan_sessions
`, [], (err, row) => {
    if (err) console.error(err);
    console.log(row);
});
