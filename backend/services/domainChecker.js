const dns = require('dns').promises;

async function checkDomain(hostname) {
    let result = {
        status: "NOT_CHECKED",
        hostname: hostname,
        syntaxValid: false,
        dnsResolved: false,
        domainExists: false,
        registrationDate: "Unavailable",
        domainAgeDays: "Unavailable",
        expiryDate: "Unavailable",
        registrar: "Unavailable",
        newlyRegistered: false,
        punycodeDetected: false,
        rawIpHost: false,
        suspiciousSubdomains: false,
        brandImpersonation: "Not Checked",
        checkedAt: new Date().toISOString(),
        provider: "Native DNS",
        error: null
    };

    try {
        if (!hostname) {
            result.error = "No hostname provided";
            return result;
        }

        // 1. Domain Syntax & Raw IP
        const isIp = /^(\d{1,3}\.){3}\d{1,3}$/.test(hostname);
        result.rawIpHost = isIp;
        // Punycode is often xn-- but we do a simple check
        result.punycodeDetected = hostname.includes('xn--');
        
        // Basic syntax valid
        result.syntaxValid = isIp || /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(hostname) || result.punycodeDetected;

        // Subdomain depth
        const parts = hostname.split('.');
        if (!isIp && parts.length > 3) {
            result.suspiciousSubdomains = true;
        }

        if (!result.syntaxValid) {
            result.status = "INVALID_SYNTAX";
            return result;
        }

        if (isIp) {
            result.status = "RAW_IP";
            return result;
        }

        // 2. DNS Resolution
        try {
            const addresses = await dns.resolve(hostname);
            if (addresses && addresses.length > 0) {
                result.dnsResolved = true;
                result.domainExists = true;
                result.status = "RESOLVED";
            }
        } catch (dnsErr) {
            result.dnsResolved = false;
            result.domainExists = false;
            result.status = "NOT_RESOLVED";
            result.error = dnsErr.code;
        }

    } catch (e) {
        result.status = "ERROR";
        result.error = e.message;
    }

    return result;
}

module.exports = {
    checkDomain
};
