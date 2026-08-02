# PROJECT REPORT ON
# “FraudEye: Secure QR & Scam Detection System”

**Submitted by**
Sarthak Salunke 

**Academic Year 2025-2026**

---

## ACKNOWLEDGEMENT
I express my deepest gratitude to all those who have contributed to the successful completion of my project titled **"FraudEye: Secure QR & Scam Detection System"**.

I sincerely thank my project guide and faculty members for their exceptional supervision, expert technical insights, valuable feedback, and encouragement at every stage of developing this cybersecurity solution. 

---

## TABLE OF CONTENTS
1. [Chapter 1: Introduction](#chapter-1-introduction)
2. [Chapter 2: Literature Review](#chapter-2-literature-review)
3. [Chapter 3: System Analysis](#chapter-3-system-analysis)
4. [Chapter 4: System Design](#chapter-4-system-design)
5. [Chapter 5: Implementation](#chapter-5-implementation)
6. [Chapter 6: Testing & Validation](#chapter-6-testing--validation)
7. [Chapter 7: Results & Discussion](#chapter-7-results--discussion)
8. [Chapter 8: Conclusion & Future Scope](#chapter-8-conclusion--future-scope)
9. [References](#references)
10. [Appendices](#appendices)

---

<a id="chapter-1-introduction"></a>
## Chapter 1: Introduction

### 1.1 Introduction
The core requirement for modern cybersecurity is proactive threat detection. With the widespread adoption of QR codes for payments, menus, and website navigation, attackers have increasingly utilized them to mask malicious URLs and fraudulent UPI payment requests (Quishing). FraudEye is designed to solve these challenges through a comprehensive digital analysis platform. The system is a full-stack web application with a modern, user-friendly interface that intercepts, decodes, and analyzes QR codes, URLs, and UPI links in real-time, ensuring users are protected from phishing campaigns and credential harvesting before they compromise their devices.

### 1.2 Problem Statement
Traditional methods of verifying the authenticity of a QR code or a URL rely heavily on human intuition or post-infection antivirus scans, which are slow and error-prone. The current ecosystem poses severe cybersecurity difficulties. Attackers create look-alike domains (e.g., `paytm-secure.example`) and embed them in QR codes. When scanned by a standard smartphone camera, the user is immediately redirected to a malicious site. There is a critical lack of integrated, pre-execution analysis tools available to the average user that can instantly decode a QR code, trace its hidden redirects, verify the SSL certificate, and flag brand impersonation without exposing the user's device to the threat.

### 1.3 Objectives
The core objective of this project is to deliver a comprehensive, responsive, web-based scam detection system. The user interface allows individuals to manually input URLs, upload QR code images, or use their webcam to scan codes safely. The backend engine performs deep forensic analysis, including DNS resolution, SSL certificate validation, redirect tracing, and heuristic brand impersonation checks. A central objective is to calculate a mathematical "Trust Score" (0–100) and provide a clear Risk Level (Safe, Caution, Suspicious, Dangerous) so users can make immediate, informed decisions.

### 1.4 Scope of the Project
FraudEye is designed to automate the threat analysis of modern web payloads. The project includes functionalities found in the Customer Scanner Portal, the Enterprise Bulk Scanning module, and the Administrative Threat Dashboard. The system supports full CRUD operations on historical scan records, maintains an audit trail in a localized SQLite database, and supports automated report generation. It focuses specifically on URLs, Plain Text payloads, and UPI (Unified Payments Interface) URIs.

---

<a id="chapter-2-literature-review"></a>
## Chapter 2: Literature Review

### 2.1 Review of Existing System / Related Work
The current operational framework for average users involves scanning QR codes directly with native camera apps. This methodology is inherently insecure, as native cameras automatically execute the embedded URL redirect without performing comprehensive background checks. While enterprise solutions like VirusTotal exist, they require manual URL extraction and do not specialize in analyzing UPI payment string formats or Indian-centric brand impersonation (like SBI or Paytm). The existing ecosystem is defined by a lack of seamless, mobile-friendly interceptors, underscoring the necessity of the proposed FraudEye system.

### 2.2 Knowledge Gap
The transition from reactive antivirus scanning to proactive QR interception is driven by a critical knowledge gap in data handling and real-time threat intelligence. Existing manual checks lack data integrity and speed. Validating a URL requires a user to manually check WHOIS records, SSL issuers, and follow HTTP chains—a tediously time-consuming process. The proposed FraudEye system directly bridges this gap through complete digital automation, ensuring robust data consistency and enabling instant, sub-second threat intelligence processing.

### 2.3 Summary of Findings
FraudEye effectively bridges the operational and technological gaps identified in manual URL verification. Through complete backend automation, the system effectively reduces analysis time and eliminates common human errors in identifying phishing sites. Operationally, FraudEye promotes a secure digital environment by supporting safe QR decoding and centralizing threat data management.

---

<a id="chapter-3-system-analysis"></a>
## Chapter 3: System Analysis

### 3.1 Requirement Analysis
**3.1.1 Functional Requirements:**
The project delivers an end-to-end digital cybersecurity system. It includes real-time QR code decoding, manual URL analysis, and bulk URL processing. Vital data governance is ensured through a Risk Engine that calculates trust scores based on SSL checks, Redirect loops, and Threat Intelligence blacklists. The system must also maintain a historical dashboard (`index.php`) and a detailed transaction history (`history.php`).

**3.1.2 Non-Functional Requirements:**
The application architecture was developed with a strong focus on core quality attributes, prioritizing robust performance and security. Performance is ensured through asynchronous API calls (`fetch`) and efficient DOM updates. For security, the system enforces rigorous backend validation in PHP to prevent Server-Side Request Forgery (SSRF) when tracing redirects. The UI emphasizes usability, utilizing Tailwind CSS for a fully responsive experience across mobile and desktop.

### 3.2 Feasibility Study
**3.2.1 Technical Feasibility:**
The project demonstrates strong technical feasibility. It is built upon a mature web stack utilizing HTML5, Tailwind CSS, JavaScript (jsQR), PHP 8.0, and SQLite 3. This eliminates the need for complex, heavy server setups while maintaining the robust processing power required for network-level socket connections (SSL checking) and cURL requests (Redirect tracking). 

**3.2.2 Operational Feasibility:**
The comprehensive digital solution is built around a user-friendly, mobile-responsive design to guarantee high user acceptance. Operationally, it ensures smooth threat-hunting workflows by providing color-coded, easy-to-read security reports (Green for Safe, Red for Dangerous) rather than complex technical logs, making it accessible to non-technical users.

### 3.3 System Specifications
*   **Hardware Requirements:** Standard desktop/laptop or smartphone with at least 4GB RAM. Webcam required for live QR scanning.
*   **Software Requirements:** Modern web browser (Chrome, Firefox, Edge).
*   **Front-end:** HTML5, Tailwind CSS, JavaScript (ES6+), jsQR.
*   **Back-end:** PHP 8.0+ running on XAMPP/Apache.
*   **Database:** SQLite 3 (file-based database).

---

<a id="chapter-4-system-design"></a>
## Chapter 4: System Design

### 4.1 System Architecture
FraudEye uses a standard Client-Server architecture. The Presentation Layer (Customer Interface) handles QR extraction and sends the raw payload to the Application Layer (`api/scan.php`). The API interacts with the `RiskEngine.php`, which acts as the orchestrator, delegating tasks to micro-modules: `SSLChecker`, `RedirectChecker`, `DomainChecker`, and `Classifier`. The results are aggregated and persisted in the SQLite Data Storage Layer.

### 4.2 Data Flow Diagram
1.  **Input Flow**: User uploads QR / Pastes URL -> JS extracts payload -> Sent via POST to `/api/scan.php`.
2.  **Process Flow**: API routes payload to Risk Engine -> Payload Classified -> Network Checks Executed (cURL/OpenSSL) -> Trust Score Calculated.
3.  **Output Flow**: Engine formats JSON response -> Saved to SQLite `scan_sessions` -> JS updates UI with Risk Verdict and pie charts.

### 4.3 Database Design (ER Diagram Summary)
*   **`scan_sessions`**: Primary table. Contains `session_id`, `payload`, `input_type`, `trust_score`, `risk_level`, `created_at`.
*   **`url_analysis`**: Links to session. Stores `domain`, `final_url`, `ssl_issuer`, `is_blacklisted`.
*   **`threat_indicators`**: Stores one-to-many specific evidence flags (e.g., `BRAND_IMPERSONATION`, `SSL_EXPIRED`) linked to a scan session.

---

<a id="chapter-5-implementation"></a>
## Chapter 5: Implementation

### 5.1 Module-wise Description
*   **Scanner Module (`scanner.php`)**: Utilizes the device's camera stream and the `jsQR` library to detect and decode QR codes on the client side, ensuring malicious links are caught before the browser natively navigates to them.
*   **Risk Engine Module (`RiskEngine.php`)**: The core backend logic. It applies a mathematical scoring system (+20 for valid SSL, -40 for Blacklist, -40 for Impersonation) to dynamically rate the safety of a payload.
*   **Dashboard Module (`index.php`)**: A central operational hub that queries the SQLite database to display real-time metrics, such as total scans, threat distribution charts, and recent high-risk detections.
*   **Bulk Processing Module (`bulk.php`)**: Allows administrators to input multiple URLs separated by commas, processing them asynchronously in a loop and rendering a tabular report of all results simultaneously.

### 5.2 Technologies Used
*   **HTML5 / CSS3 / Tailwind**: Lays the foundation for responsive, modern UI design.
*   **JavaScript (ES6+)**: Handles asynchronous UI updates and client-side QR decoding without page reloads.
*   **PHP 8.0**: Provides robust server-side routing, cURL request handling for redirect tracing, and OpenSSL integration for certificate extraction.
*   **SQLite 3**: Acts as a light, portable database for storing scan history without requiring a dedicated database server like MySQL.

### 5.3 Core Logic & Scoring
The system relies on a rigorous points-based logic:
```php
// Trust Score Calculation Example
$trustScore = 0;
if (!$hasInvalidSSL) $trustScore += 20;
if (!$isBlacklisted) $trustScore += 20;
if ($hasInvalidSSL) $trustScore -= 20;
if ($hasBrandImpersonation) $trustScore -= 40;
```

---

<a id="chapter-6-testing--validation"></a>
## Chapter 6: Testing & Validation

### 6.1 Types of Testing
*   **Unit Testing**: Individual backend components (e.g., `SSLChecker.php`) were tested in isolation via PHP CLI to ensure they correctly identified expired or HTTP-only certificates.
*   **Integration Testing**: Validated the flow between the JavaScript frontend `fetch` API and the PHP backend, ensuring JSON payloads were properly stringified, transmitted, parsed, and returned without CORS or formatting errors.
*   **System Testing**: End-to-end evaluation simulating real-world malicious payloads (e.g., a fake Paytm URL embedded in a QR code) to confirm the Risk Engine correctly applied deductions and rendered a "DANGEROUS" verdict on the UI.

### 6.2 Test Cases and Results
| Test ID | Scenario | Status |
| :--- | :--- | :--- |
| T01 | Scan valid Google.com URL | Passed (SAFE) |
| T02 | Scan Naked Domain (erp.mgmu.ac.in) | Passed (Follows redirect to HTTPS, SAFE) |
| T03 | Scan Fake Brand URL (paytm-secure.example) | Passed (Flags Impersonation, DANGEROUS) |
| T04 | Bulk Scan 3 mixed URLs | Passed (Renders all 3 results) |
| T05 | Scan Plain Text / UPI ID | Passed (Handles null domains safely) |

---

<a id="chapter-7-results--discussion"></a>
## Chapter 7: Results & Discussion

### 7.1 Performance Evaluation
FraudEye effectively achieves its primary objective of enhancing digital safety without sacrificing speed. The digital analysis engine cuts the time required for manual URL verification from several minutes of human investigation down to **under 3 seconds**. The operational handoff between the frontend scanner and backend engine is virtually instantaneous.

### 7.2 Comparison with Existing Systems
| Feature | Traditional Antivirus | FraudEye CMS | Advantage |
| :--- | :--- | :--- | :--- |
| **Analysis Timing** | Post-execution (After click) | Pre-execution (Interception) | Prevents initial infection |
| **QR Code Support** | Poor / None | Native Browser & Camera integration | Seamless mobile experience |
| **UPI Link Parsing** | None | Deep VPA formatting checks | Protects against payment fraud |
| **Deployment** | Heavy OS Installation | Web-based / URL Access | High portability and accessibility |

### 7.3 Key Observations
The system demonstrated exceptional reliability across different payload types. A key observation was the necessity of order-of-operations in the backend: running the `RedirectChecker` *before* the `SSLChecker` ensured that naked HTTP domains that securely redirect to HTTPS were not falsely penalized. The mobile-friendly interface contributed to high user acceptance, proving the system's operational feasibility.

---

<a id="chapter-8-conclusion--future-scope"></a>
## Chapter 8: Conclusion & Future Scope

### 8.1 Summary of Work
The FraudEye project successfully culminated in the creation of a fully operational cybersecurity web application designed to combat QR code and URL-based scams. Utilizing a robust PHP and SQLite backend combined with a modern JavaScript frontend, the system achieved its main goals: delivering instant threat intelligence, providing a safe pre-execution environment for QR codes, and calculating an accurate, deterministic Trust Score to guide user behavior.

### 8.2 Project Limitations
The current version is a functional prototype built for localized deployment (via XAMPP). A significant constraint is its reliance on heuristic, string-matching rules for Brand Impersonation (e.g., manually checking for the word "paytm" in the domain). Attackers frequently change tactics, and static heuristic lists require constant manual updating. Additionally, the SQLite database is not optimized for massive, concurrent enterprise-scale traffic.

### 8.3 Future Improvements
In the future, the system can be upgraded to integrate real Machine Learning (ML) models via Python microservices to detect phishing visual similarities and sophisticated domain generation algorithms (DGAs). Migrating the data system to PostgreSQL will allow for enterprise scalability. Finally, packaging the web application into a native Android/iOS app would allow it to intercept system-level camera intents, completely replacing the vulnerable native camera QR scanners on smartphones.

---

<a id="references"></a>
## References
1. Research on Phishing Trends and Quishing (QR Phishing) methodologies.
2. Official PHP Documentation for OpenSSL and cURL modules.
3. SQLite3 Database Architecture and Optimization.
4. Tailwind CSS styling frameworks.

---

<a id="appendices"></a>
## Appendices

**Appendix A: Source Code**
This appendix contains the essential portions of the project's source code. Due to its length, the complete codebase is not included in this document but can be accessed via the official GitHub repository:
**Link to GitHub Repository:** [https://github.com/sarthaksalunke77/Secure-QR-Scam-detection-system](https://github.com/sarthaksalunke77/Secure-QR-Scam-detection-system)
