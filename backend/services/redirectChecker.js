const axios = require('axios');
const { isSafeUrl } = require('./ssrfProtector');

const MAX_REDIRECTS = 10;

async function checkRedirects(urlString) {
    let result = {
        status: "NOT_CHECKED",
        checked: false,
        initialUrl: urlString,
        redirectCount: 0,
        chain: [],
        finalUrl: urlString,
        finalDomain: null,
        crossDomainRedirect: false,
        httpsDowngrade: false,
        redirectLoop: false,
        excessiveRedirects: false,
        error: null
    };

    try {
        const safe = await isSafeUrl(urlString);
        if (!safe) {
            result.status = "ERROR";
            result.error = "SSRF Protection blocked URL";
            return result;
        }

        const initialUrlParsed = new URL(urlString);
        
        let currentUrl = urlString;
        let redirectCount = 0;
        let chain = [currentUrl];
        
        // Custom agent to prevent auto-redirect so we can track it manually
        // But axios has a `maxRedirects` option, but it doesn't give us the chain easily.
        // We can just use standard axios request interceptor or follow manually.
        
        // Manual following for precise tracking
        while (redirectCount < MAX_REDIRECTS) {
            try {
                const response = await axios.get(currentUrl, {
                    maxRedirects: 0, // Stop automatically following
                    validateStatus: function (status) {
                        return status >= 200 && status < 400; // Resolve on redirects
                    },
                    timeout: 5000
                });

                if (response.status >= 300 && response.status < 400 && response.headers.location) {
                    redirectCount++;
                    let nextUrl = response.headers.location;
                    
                    // Handle relative redirects
                    if (!nextUrl.startsWith('http')) {
                        const currentParsed = new URL(currentUrl);
                        nextUrl = new URL(nextUrl, currentParsed.origin).toString();
                    }
                    
                    if (chain.includes(nextUrl)) {
                        result.redirectLoop = true;
                        result.status = "LOOP_DETECTED";
                        result.checked = true;
                        break;
                    }
                    
                    chain.push(nextUrl);
                    currentUrl = nextUrl;
                } else {
                    // No more redirects
                    result.status = redirectCount > 0 ? "REDIRECTED" : "NO_REDIRECT";
                    result.checked = true;
                    break;
                }
            } catch (err) {
                // Network error on the hop
                result.error = err.message;
                result.status = redirectCount > 0 ? "REDIRECTED" : "NO_REDIRECT";
                result.checked = true;
                break;
            }
        }
        
        if (redirectCount >= MAX_REDIRECTS) {
            result.excessiveRedirects = true;
            result.status = "TOO_MANY_REDIRECTS";
            result.checked = true;
        }
        
        result.redirectCount = redirectCount;
        result.chain = chain;
        result.finalUrl = currentUrl;
        
        if (result.finalUrl) {
            const finalParsed = new URL(result.finalUrl);
            result.finalDomain = finalParsed.hostname;
            
            if (initialUrlParsed.hostname !== finalParsed.hostname) {
                result.crossDomainRedirect = true;
            }
            
            if (initialUrlParsed.protocol === 'https:' && finalParsed.protocol === 'http:') {
                result.httpsDowngrade = true;
            }
        }
        
    } catch (e) {
        result.status = "ERROR";
        result.error = e.message;
    }

    return result;
}

module.exports = {
    checkRedirects
};
