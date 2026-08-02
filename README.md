# FraudEye: Secure QR & Scam Detection System

FraudEye is a comprehensive, local PHP-based cybersecurity web application designed to analyze URLs, QR codes, and UPI payment links in real-time. It protects users from phishing campaigns, brand impersonation, malicious downloads, and fraudulent payment requests by meticulously analyzing the structural and network properties of any given link.

## 🚀 Core Features

*   **Manual URL Scanning**: Users can paste any suspicious link into the engine for an instant, deep security analysis.
*   **QR Code Analysis**: Upload an image or use your device's camera to safely intercept and parse embedded URLs, plain text, and UPI links *before* they execute on your device.
*   **Enterprise Bulk Scanning**: Scan up to 10 URLs simultaneously in parallel, bypassing the single-scan limit for maximum efficiency.
*   **Trust Score Algorithm**: A rigorously tested scoring engine that rates payloads from 0 to 100, assigning clear risk levels: SAFE, CAUTION, SUSPICIOUS, or DANGEROUS.
*   **Dashboard & Historical Auditing**: A central hub that aggregates scan statistics (Total Scans, Malicious URLs, Safe Links) and maintains a permanent historical audit trail in an SQLite database.

## ⚙️ How It Works (The Risk Engine)

At the heart of FraudEye is the `RiskEngine.php`, which operates as a centralized orchestrator triggering a cascade of specialized security checkers:

1.  **Redirect Checker**: Follows the HTTP request chain up to 10 times to expose the true final destination, flagging HTTPS downgrades and infinite loops.
2.  **SSL/TLS Checker**: Inspects the website's SSL certificate for expiration, issuer validity, and hostname mismatches.
3.  **Domain Checker**: Analyzes the domain structure for newly registered domains, Raw IP abuse, and Punycode (homograph attacks).
4.  **Threat Intelligence**: Acts as a blacklist validator, cross-referencing the domain against known databases for confirmed malware or phishing.
5.  **Brand Impersonation**: Uses heuristics to scan the domain string for attempts to mimic trusted brands (e.g., `paytm-secure-login.example`).
6.  **UPI Deep Inspection**: Verifies VPA formats, missing parameters, and flags suspiciously large payment requests.

## 🛠️ Technology Stack

*   **Frontend**: HTML5, Vanilla JavaScript (ES6+), Tailwind CSS (for modern UI styling), and `jsQR` (for client-side QR decoding).
*   **Backend**: PHP 8.0+ 
*   **Database**: SQLite 3 (Zero-configuration file-based database)
*   **Server Stack**: Apache HTTP Server (via XAMPP)

## 📦 Installation & Setup

1.  **Prerequisites**: Install [XAMPP](https://www.apachefriends.org/index.html) (with PHP 8+).
2.  **Clone the Repository**:
    ```bash
    git clone https://github.com/sarthaksalunke77/Secure-QR-Scam-detection-system.git
    ```
3.  **Move to Web Root**: Place the cloned project folder inside your XAMPP web root directory (`C:\xampp\htdocs\`).
    *(Alternatively, you can create a Directory Junction/Symlink from `htdocs` to your project folder).*
4.  **Start the Server**: Open the XAMPP Control Panel and start **Apache**. (MySQL is not required as the project uses SQLite).
5.  **Run the Application**: Open your web browser and navigate to:
    ```
    http://localhost/Secure-QR-Scam-detection-system/index.php
    ```

## 🛡️ License & Usage

This project was built for educational and cybersecurity research purposes. The threat indicators and scores generated are intended to provide guidance and should be used as part of a broader security posture.
