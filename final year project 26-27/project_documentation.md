# FraudEye: Project Documentation

FraudEye is a robust, localized PHP-based cybersecurity web application designed to analyze URLs, QR codes, and UPI payment links in real-time. It detects malicious intent, phishing campaigns, brand impersonation, and fraudulent payments, presenting the results through an intuitive Dashboard and detailed Scan Reports.

## 1. Core Features
*   **Manual URL Scanning (`manual.php`)**: Users can paste suspicious links. FraudEye parses the URL and runs it through its comprehensive security engine.
*   **QR Code Analysis (`scanner.php`)**: Allows users to upload or use their camera to scan QR codes. It safely intercepts embedded URLs, plain text, and UPI links before they are executed by a device.
*   **Enterprise Bulk Scanning (`bulk.php`)**: Empowers users to scan up to 10 URLs simultaneously, bypassing the single-scan limit and processing them in parallel.
*   **Trust Score & Verdict Engine**: A meticulously crafted scoring algorithm that rates payloads from 0 to 100, assigning risk levels (SAFE, CAUTION, SUSPICIOUS, DANGEROUS).
*   **Dashboard & History (`index.php`, `history.php`)**: A central hub that aggregates scan statistics (Total Scans, Malicious URLs, Safe Links) and logs a complete historical audit trail in an SQLite database.

## 2. Technology Stack
*   **Frontend**: HTML5, Vanilla JavaScript, Tailwind CSS (for modern styling and responsive layout), and `jsQR` (for client-side QR code extraction).
*   **Backend**: PHP (running on XAMPP / Apache).
*   **Database**: SQLite (`db/fraudeye.db`).

## 3. The Backend Architecture (Risk Engine)
The heart of FraudEye is the `RiskEngine.php`, which operates as a centralized orchestrator. When a payload is submitted to `api/scan.php`, the Risk Engine triggers a cascade of specialized security checkers:

1.  **Classifier (`Classifier.php`)**
    Determines the nature of the payload (URL, Plain Text, UPI URI, or naked UPI ID) so the engine knows how to process it.
2.  **Redirect Checker (`RedirectChecker.php`)**
    Follows the HTTP request chain up to 10 times to expose the *true* final destination. It flags suspicious behaviors like HTTPS-to-HTTP downgrades, infinite redirect loops, or excessive cross-domain bouncing.
3.  **SSL/TLS Checker (`SSLChecker.php`)**
    Establishes a secure socket connection to inspect the website's SSL certificate. It verifies the issuer, expiration date, and hostname matches, penalizing self-signed or expired certificates.
4.  **Domain Checker (`DomainChecker.php`)**
    Analyzes the domain structure. It flags Raw IP addresses, newly registered domains, and Punycode (homograph attacks where attackers use look-alike characters).
5.  **Threat Intelligence (`ThreatIntel.php`)**
    Acts as the blacklist validator, cross-referencing the domain against known databases (like Google Web Risk or Phishtank) for confirmed malware or phishing campaigns.
6.  **Brand Impersonation Heuristics**
    Scans the domain string for attempts to mimic trusted brands (e.g., `paytm-secure-login.example` instead of `paytm.com`), which immediately triggers a critical phishing alert.

## 4. Trust Score Algorithm
FraudEye calculates a Trust Score out of 100 based on the gathered evidence:
*   **Base Addition**: A domain can earn points for having a Valid SSL (+20), Domain Age > 5 Years (+15), Clean Blacklist (+20), No Malware (+15), No Phishing Indicators (+20), and No Suspicious Redirects (+10).
*   **Deductions**: Severe penalties are applied for threats. Invalid SSL (-20), Brand Impersonation (-40), Blacklisted (-40), Malware (-50).
*   **Verdict Matrix**:
    *   `81 - 100`: **SAFE** (Low Risk)
    *   `61 - 80`: **CAUTION** (Medium Risk)
    *   `31 - 60`: **SUSPICIOUS** (High Risk)
    *   `0 - 30`: **DANGEROUS** (Critical Risk)

## 5. Database Schema (SQLite)
The application relies on several linked tables to maintain its audit trail:
*   **`scan_sessions`**: The master table recording every scan, storing the original payload, Trust Score, Verdict, and timestamp.
*   **`url_analysis`**: Stores specific URL-related checks like redirect counts and SSL issuers.
*   **`threat_indicators`**: Logs specific evidence flags (e.g., "SSL_EXPIRED", "BRAND_IMPERSONATION") raised by the Risk Engine during a scan.
*   **`payment_checks`**: Stores extracted data specific to UPI payloads (VPA, amount, receiver name).

## 6. Recent Enhancements
Over the course of its development, FraudEye has seen several critical upgrades:
*   Fixed strict string matching on the dashboard to properly display `DANGEROUS (DEMO)` risk levels.
*   Upgraded the `RiskEngine` pipeline to ensure `RedirectChecker` runs *before* `SSLChecker`. This prevents naked domains (like `example.com`) from being falsely flagged as invalid SSL due to defaulting to HTTP before they redirect to HTTPS.
*   Added null-safety to ensure the system gracefully handles Plain Text and UPI QR codes without throwing PHP Fatal Errors.
