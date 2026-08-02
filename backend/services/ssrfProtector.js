const dns = require('dns');
const { promisify } = require('util');
const ipaddr = require('ipaddr.js');

const lookup = promisify(dns.lookup);

/**
 * Validates a URL to prevent SSRF attacks.
 * 1. Checks protocol (only http/https)
 * 2. Resolves DNS
 * 3. Rejects private, loopback, and link-local IP addresses.
 */
async function isSafeUrl(urlString) {
    try {
        const url = new URL(urlString);
        
        if (url.protocol !== 'http:' && url.protocol !== 'https:') {
            return false;
        }

        const hostname = url.hostname;
        
        // Resolve DNS
        const { address } = await lookup(hostname);
        
        // Parse IP
        const ip = ipaddr.parse(address);
        const range = ip.range();

        // Block internal network ranges
        const blockedRanges = [
            'private', 
            'loopback', 
            'linkLocal', 
            'unspecified', 
            'multicast', 
            'broadcast',
            'carrierGradeNat',
            'reserved'
        ];

        if (blockedRanges.includes(range)) {
            return false;
        }

        // Specifically block cloud metadata IP
        if (address === '169.254.169.254') {
            return false;
        }

        return true;
    } catch (error) {
        // If DNS fails or URL is malformed, block it.
        return false;
    }
}

module.exports = {
    isSafeUrl
};
