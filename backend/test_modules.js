const { checkThreatIntel } = require('./services/ThreatIntel');
const { calculateRisk } = require('./services/RiskEngine');

async function runTests() {
    console.log("--- Testing API Unavailable Case (No Key) ---");
    process.env.GOOGLE_SAFE_BROWSING_API_KEY = '';
    const tiResult1 = await checkThreatIntel('example.com', 'http://example.com');
    console.log(tiResult1);

    console.log("\n--- Testing Risk Engine with Suspicious Fixture (No Fraud) ---");
    const riskResult1 = calculateRisk(
        { type: 'url' },
        { indicators: [] },
        { status: 'NOT_RESOLVED', dnsResolved: false }, // technical error
        { checked: true, status: 'EXPIRED' }, // technical error
        tiResult1, // NOT_CHECKED
        { checked: true, crossDomainRedirect: false }
    );
    console.log(riskResult1);

    console.log("\n--- Testing Risk Engine with Fraud Evidence ---");
    const riskResult2 = calculateRisk(
        { type: 'url' },
        { indicators: [] },
        { status: 'RESOLVED', dnsResolved: true },
        { checked: true, status: 'VALID' },
        { status: 'MALICIOUS', malicious: true, detections: 1, blacklistMatch: true },
        { checked: true, crossDomainRedirect: false }
    );
    console.log(riskResult2);
}

runTests();
