const tls = require('tls');
const { isSafeUrl } = require('./ssrfProtector');

/**
 * Connects to the host on port 443 and retrieves SSL certificate details.
 */
async function checkSSL(urlString) {
    let result = {
        checked: false,
        https: false,
        certificatePresent: false,
        authorized: false,
        hostnameMatch: false,
        issuer: null,
        subject: null,
        validFrom: null,
        validTo: null,
        daysRemaining: null,
        expired: false,
        notYetValid: false,
        selfSigned: false,
        errorCode: null,
        errorMessage: null,
        status: "NOT_CHECKED",
        checkedAt: new Date().toISOString()
    };

    try {
        const url = new URL(urlString);
        
        if (url.protocol !== 'https:') {
            result.checked = true;
            result.status = "HTTP_ONLY";
            return result;
        }

        result.https = true;
        const hostname = url.hostname;

        // SSRF Protection
        const safe = await isSafeUrl(urlString);
        if (!safe) {
            result.status = "TLS_ERROR";
            result.errorCode = "SSRF_BLOCKED";
            result.errorMessage = "SSRF Protection blocked connection";
            return result;
        }

        return new Promise((resolve) => {
            const socket = tls.connect({
                host: hostname,
                port: 443,
                servername: hostname,
                rejectUnauthorized: false, // We want to inspect the cert even if invalid
                timeout: 5000
            });

            socket.once('secureConnect', () => {
                result.checked = true;
                const cert = socket.getPeerCertificate();
                
                if (!cert || Object.keys(cert).length === 0) {
                    result.status = "NO_CERTIFICATE";
                    socket.destroy();
                    resolve(result);
                    return;
                }

                result.certificatePresent = true;
                result.authorized = socket.authorized;
                
                result.issuer = cert.issuer ? cert.issuer.O || cert.issuer.CN : "Unknown";
                result.subject = cert.subject ? cert.subject.CN : "Unknown";
                
                result.validFrom = cert.valid_from;
                result.validTo = cert.valid_to;

                // Check Expiry and Validity
                const validToDate = new Date(cert.valid_to);
                const validFromDate = new Date(cert.valid_from);
                const now = new Date();
                
                if (validToDate < now) {
                    result.expired = true;
                } else {
                    const diffTime = Math.abs(validToDate - now);
                    result.daysRemaining = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                }

                if (now < validFromDate) {
                    result.notYetValid = true;
                }

                // Check Hostname Match
                if (tls.checkServerIdentity(hostname, cert) !== undefined) {
                    result.hostnameMatch = false;
                } else {
                    result.hostnameMatch = true;
                }
                
                if (result.issuer === result.subject) {
                    result.selfSigned = true;
                }

                // Determine overall status
                if (result.expired) {
                    result.status = "EXPIRED";
                } else if (!result.hostnameMatch) {
                    result.status = "HOSTNAME_MISMATCH";
                } else if (result.selfSigned) {
                    result.status = "SELF_SIGNED";
                } else if (!result.authorized) {
                    result.status = "INVALID"; // Generic untrusted
                } else {
                    result.status = "VALID";
                }

                socket.destroy();
                resolve(result);
            });

            socket.once('error', (err) => {
                result.checked = true;
                result.status = "TLS_ERROR";
                result.errorCode = err.code || "UNKNOWN";
                result.errorMessage = err.message;
                resolve(result);
            });

            socket.once('timeout', () => {
                socket.destroy();
                result.checked = true;
                result.status = "TLS_ERROR";
                result.errorCode = "TIMEOUT";
                result.errorMessage = "Connection timed out";
                resolve(result);
            });
        });
    } catch (error) {
        result.status = "TLS_ERROR";
        result.errorCode = "CATCH_ERROR";
        result.errorMessage = error.message;
        return result;
    }
}

module.exports = {
    checkSSL
};
