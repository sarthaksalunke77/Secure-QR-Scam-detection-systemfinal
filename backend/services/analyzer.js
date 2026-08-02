// URL parser is now native URL class
const { isSafeUrl } = require('./ssrfProtector');

const SUSPICIOUS_KEYWORDS = [
    'login', 'verify', 'update', 'secure', 'account', 'bank', 
    'wallet', 'payment', 'reward', 'prize', 'urgent', 'kyc', 
    'password', 'otp', 'confirm', 'support', 'service'
];

const PROTECTED_BRANDS = ['paypal', 'google', 'amazon', 'microsoft', 'apple', 'facebook'];

// Levenshtein distance for fuzzy matching
function levenshteinDistance(a, b) {
    const matrix = [];
    let i, j;
    if (a.length === 0) return b.length;
    if (b.length === 0) return a.length;
    for (i = 0; i <= b.length; i++) matrix[i] = [i];
    for (j = 0; j <= a.length; j++) matrix[0][j] = j;
    for (i = 1; i <= b.length; i++) {
        for (j = 1; j <= a.length; j++) {
            if (b.charAt(i - 1) == a.charAt(j - 1)) {
                matrix[i][j] = matrix[i - 1][j - 1];
            } else {
                matrix[i][j] = Math.min(matrix[i - 1][j - 1] + 1, Math.min(matrix[i][j - 1] + 1, matrix[i - 1][j] + 1));
            }
        }
    }
    return matrix[b.length][a.length];
}

/**
 * Engine 1 & 2: URL Normalization and Lexical Analysis
 */
async function analyzeUrl(urlString) {
    const indicators = [];

    try {
        const originalUrl = urlString.trim();
        
        // 1. Normalization
        const parsed = new URL(originalUrl);
        const domain = parsed.hostname.toLowerCase();
        
        const normalizedUrl = parsed.toString();

        // SSRF Check
        const isSafe = await isSafeUrl(normalizedUrl);
        if (!isSafe) {
            indicators.push({ id: 'UNSAFE_DESTINATION', source: ['URL_ANALYSIS'], severity: 'critical', riskContribution: 100, description: 'Destination resolves to an internal or private IP address.' });
            return { originalUrl, normalizedUrl, domain, indicators, parsed: { protocol: parsed.protocol, pathname: parsed.pathname } };
        }

        // 1. IP Address Check
        const isIp = /^(\d{1,3}\.){3}\d{1,3}$/.test(domain);
        if (isIp) {
            indicators.push({ id: 'RAW_IP_HOST', source: ['URL_ANALYSIS'], severity: 'high', riskContribution: 15, description: 'Domain is a raw IP address.' });
        }

        // 2. HTTP vs HTTPS
        if (parsed.protocol === 'http:') {
            indicators.push({ id: 'HTTP_ONLY', source: ['URL_ANALYSIS'], severity: 'low', riskContribution: 8, description: 'Uses unencrypted HTTP protocol.' });
        }

        // 3. Embedded Credentials
        if (parsed.username || parsed.password) {
            indicators.push({ id: 'EMBEDDED_CREDENTIALS', source: ['URL_ANALYSIS'], severity: 'high', riskContribution: 25, description: 'URL contains embedded credentials (e.g. user:pass@).' });
        }

        // 4. Excessive subdomains
        const domainParts = domain.split('.');
        if (domainParts.length > 3 && !isIp) {
            indicators.push({ id: 'EXCESSIVE_SUBDOMAINS', source: ['URL_ANALYSIS'], severity: 'medium', riskContribution: 8, description: 'Multiple subdomains detected.' });
        }

        // 5. Suspicious keywords in URL path
        let keywordCount = 0;
        const fullUrlLower = normalizedUrl.toLowerCase();
        
        // Only check the pathname to prevent false positives from benign query parameters like ?action=login
        const pathLower = parsed.pathname.toLowerCase();
        
        SUSPICIOUS_KEYWORDS.forEach(kw => {
            const kwRegex = new RegExp(`\\b${kw}\\b`, 'i');
            if (kwRegex.test(pathLower)) {
                keywordCount++;
            }
        });
        
        if (keywordCount > 0) {
            indicators.push({ id: 'SUSPICIOUS_KEYWORDS', source: ['URL_ANALYSIS'], severity: 'medium', riskContribution: Math.min(20, keywordCount * 5), description: `Found ${keywordCount} suspicious keyword(s) in URL path/query.` });
        }

        // 6. Length checks
        if (normalizedUrl.length > 150) {
            indicators.push({ id: 'LONG_URL', source: ['URL_ANALYSIS'], severity: 'low', riskContribution: 8, description: 'URL is abnormally long.' });
        }

        // 7. Suspicious characters / Punycode
        if (domain.includes('xn--')) {
            indicators.push({ id: 'PUNYCODE_HOST', source: ['URL_ANALYSIS'], severity: 'high', riskContribution: 12, description: 'Punycode detected (often used for homograph attacks).' });
        }
        
        // 8. Encoding checks
        if (/%25/i.test(normalizedUrl)) {
            indicators.push({ id: 'DOUBLE_ENCODING', source: ['URL_ANALYSIS'], severity: 'medium', riskContribution: 15, description: 'Double URL encoding detected (often bypasses filters).' });
        }

        // 9. Suspicious Port
        if (parsed.port && parsed.port !== '80' && parsed.port !== '443') {
            indicators.push({ id: 'SUSPICIOUS_PORT', source: ['URL_ANALYSIS'], severity: 'low', riskContribution: 8, description: `Non-standard port ${parsed.port} used.` });
        }
        
        // 10. Brand Impersonation
        const domainWithoutTld = domainParts.slice(0, -1).join('.');
        PROTECTED_BRANDS.forEach(brand => {
            if (!domain.includes(brand + '.')) {
                if (fullUrlLower.includes(brand)) {
                    indicators.push({ id: 'BRAND_IMPERSONATION', source: ['URL_ANALYSIS'], severity: 'high', riskContribution: 25, description: `Protected brand "${brand}" appears in URL but not as main domain.` });
                } else {
                    const dist = levenshteinDistance(brand, domainWithoutTld);
                    if (dist > 0 && dist <= 2 && domainWithoutTld.length > 4) {
                        indicators.push({ id: 'BRAND_TYPOSQUATTING', source: ['URL_ANALYSIS'], severity: 'high', riskContribution: 25, description: `Domain closely resembles protected brand "${brand}".` });
                    }
                }
            }
        });

        return {
            originalUrl,
            normalizedUrl,
            domain,
            parsed: { protocol: parsed.protocol, hostname: parsed.hostname, port: parsed.port, pathname: parsed.pathname, query: parsed.search },
            indicators
        };
    } catch (e) {
        console.error("analyzer.js Error on URL:", urlString, e);
        indicators.push({ id: 'MALFORMED_URL', source: ['URL_ANALYSIS'], severity: 'high', riskContribution: 35, description: 'Malformed URL could not be parsed.' });
        return { originalUrl: urlString, normalizedUrl: urlString, domain: 'unknown', indicators, parsed: {} };
    }
}

module.exports = {
    analyzeUrl
};
