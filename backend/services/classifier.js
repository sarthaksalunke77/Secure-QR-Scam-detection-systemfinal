/**
 * Payload Classifier
 * Determines if the extracted QR payload is a URL, a UPI URI, or plain text.
 */
function classifyPayload(payload) {
    if (!payload || typeof payload !== 'string') {
        return { type: 'unknown', data: null };
    }

    const trimmed = payload.trim();

    // Check for UPI
    if (trimmed.toLowerCase().startsWith('upi://pay')) {
        return { type: 'upi', data: parseUpiUri(trimmed) };
    }
    // Check for simple VPA/UPI ID (e.g., user@bank)
    if (/^[a-zA-Z0-9.\-_]+@[a-zA-Z]+$/.test(trimmed)) {
        return { type: 'upi_id_only', data: { vpa: trimmed } };
    }

    // Check for URL
    if (/^https?:\/\//i.test(trimmed) || /^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/.test(trimmed)) {
        let urlData = trimmed;
        // Prepend http if missing to make it parseable as a valid URL for later
        if (!/^https?:\/\//i.test(trimmed)) {
            urlData = 'http://' + trimmed; 
        }
        return { type: 'url', data: { url: urlData } };
    }

    // Default to plain text
    return { type: 'text', data: trimmed };
}

function parseUpiUri(uri) {
    try {
        const urlObj = new URL(uri);
        const params = new URLSearchParams(urlObj.search);
        
        return {
            raw: uri,
            pa: params.get('pa') || null, // payee address (VPA)
            pn: params.get('pn') ? decodeURIComponent(params.get('pn').replace(/\+/g, ' ')) : null, // payee name
            am: params.get('am') || null, // amount
            cu: params.get('cu') || null, // currency
            tn: params.get('tn') ? decodeURIComponent(params.get('tn').replace(/\+/g, ' ')) : null, // transaction note
            mc: params.get('mc') || null, // merchant code
            tr: params.get('tr') || null, // transaction reference
            tid: params.get('tid') || null, // transaction id
            url: params.get('url') ? decodeURIComponent(params.get('url')) : null,
            mam: params.get('mam') || null, // minimum amount
            mode: params.get('mode') || null, // payment mode
            orgid: params.get('orgid') || null,
            purpose: params.get('purpose') || null,
            sign: params.get('sign') || null
        };
    } catch (e) {
        return { raw: uri, error: 'Invalid UPI format' };
    }
}

module.exports = {
    classifyPayload,
    parseUpiUri
};
