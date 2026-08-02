/**
 * Threat Intelligence Module
 */

const axios = require('axios');

async function checkThreatIntel(domain, url) {
    let result = {
        status: "NOT_CHECKED",
        checked: false,
        providers: [],
        malicious: false,
        phishing: false,
        malware: false,
        suspicious: false,
        blacklistMatch: false,
        detections: 0,
        checkedAt: new Date().toISOString(),
        error: null
    };

    try {
        const apiKey = process.env.GOOGLE_SAFE_BROWSING_API_KEY;

        if (!apiKey) {
            result.status = "NOT_CHECKED";
            result.checked = false;
            result.error = "No threat intelligence API keys configured.";
            return result;
        }

        result.providers.push("Google Safe Browsing");

        const requestBody = {
            client: {
                clientId: "fraudi-eye",
                clientVersion: "1.0.0"
            },
            threatInfo: {
                threatTypes: ["MALWARE", "SOCIAL_ENGINEERING", "UNWANTED_SOFTWARE", "POTENTIALLY_HARMFUL_APPLICATION"],
                platformTypes: ["ANY_PLATFORM"],
                threatEntryTypes: ["URL"],
                threatEntries: [
                    { url: url },
                    { url: domain }
                ]
            }
        };

        const response = await axios.post(`https://safebrowsing.googleapis.com/v4/threatMatches:find?key=${apiKey}`, requestBody);
        
        if (response.data && response.data.matches && response.data.matches.length > 0) {
            result.status = "MALICIOUS";
            result.checked = true;
            result.malicious = true;
            
            const match = response.data.matches[0];
            if (match.threatType === "SOCIAL_ENGINEERING") {
                result.phishing = true;
            } else {
                result.malware = true;
            }
            
            result.blacklistMatch = true;
            result.detections = response.data.matches.length;
        } else {
            result.status = "CLEAN";
            result.checked = true;
            result.detections = 0;
        }

    } catch (e) {
        result.status = "API_ERROR";
        result.error = e.message;
    }

    return result;
}

module.exports = {
    checkThreatIntel
};
