<div align="center">

# PROJECT REPORT ON
# “FraudEye: Secure QR & Scam Detection System”

<br><br>

**Submitted by**

**Sarthak Salunke**

<br><br>

**Under Guidance of**

**[Guide Name]**

<br><br>

**School of Engineering and Technology (SOET)**<br>
**MGM University, Chh.Sambhajinagar**<br>
**Academic Year 2025-2026**

</div>

<div style="page-break-after: always;"></div>

<div align="center">

**School of Engineering and Technology**<br>
**MGM University, Chh. Sambhajinagar**

### **CERTIFICATE**

</div>

This is to certify that **Sarthak Salunke** has successfully completed Project work on **“FraudEye: Secure QR & Scam Detection System”** under the guidance of **[Guide Name]** and submitted the same during the academic year 2025-2026 towards the partial fulfillment of degree of B.Tech from School of Engineering and Technology, MGM University, Chh.Sambhajinagar.

<br><br><br>

**[Guide Name]** &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; **[HOD Name]**<br>
**Project Guide** &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; **HOD**

<br><br>

**Date: [Insert Date]**

<div style="page-break-after: always;"></div>

### **ACKNOWLEDGEMENT**

I express my deepest gratitude to all those who have contributed to the successful completion of my project titled **"FraudEye: Secure QR & Scam Detection System"**.

I sincerely thank **Dr. Parminder Kaur**, Director, **School of Engineering and Technology, MGM University**, Chhatrapati Sambhajinagar, for providing excellent facilities and a supportive environment.

I express my heartfelt appreciation to **[HOD Name]**, Head of the Department, for their constant motivation and valuable guidance throughout this project.

I am specially grateful to my project guide, **[Guide Name]**, for their exceptional supervision, expert technical insights, valuable feedback, and encouragement at every stage of developing this cybersecurity solution.

I extend my thanks to all faculty members for their valuable suggestions and support.

I am grateful to MGM University for fostering an environment that encourages innovation and technical excellence.

<br><br>
**Sarthak Salunke**

<div style="page-break-after: always;"></div>

### **Abstract**

FraudEye is a web-based cybersecurity application designed to automate and streamline the detection of modern web payloads, specifically focusing on QR codes, URLs, and UPI links. With the widespread adoption of QR codes, attackers have increasingly utilized them to mask malicious URLs and fraudulent UPI payment requests (Quishing). FraudEye addresses these challenges by providing a fully digital, pre-execution analysis platform. 

Users can upload QR codes, use their device's camera, or manually input links. The backend Risk Engine then performs deep forensic analysis, including DNS resolution, SSL certificate validation, redirect tracing, and heuristic brand impersonation checks. The system leverages HTML, Tailwind CSS, JavaScript (jsQR) on the frontend, and a robust PHP backend with a localized SQLite database for data persistence. 

Key features include a Trust Score calculation algorithm that outputs a clear Risk Level (Safe, Caution, Suspicious, Dangerous), an Enterprise Bulk Scanning module, and an Administrative Threat Dashboard. By automating the threat analysis process, FraudEye completely transforms the reactive approach of traditional antivirus software into a proactive, fast, and accessible digital defense environment.

<div style="page-break-after: always;"></div>

### **Abbreviations**

**API:** Application Programming Interface.<br>
**CORS:** Cross-Origin Resource Sharing.<br>
**CRUD:** Create, Read, Update, Delete.<br>
**CSS:** Cascading Style Sheets.<br>
**DOM:** Document Object Model.<br>
**HTML:** Hyper Text Markup Language.<br>
**JS/ES6+:** JavaScript/ECMA Script 2015+.<br>
**PHP:** PHP: Hypertext Preprocessor.<br>
**QR:** Quick Response.<br>
**SQL:** Structured Query Language.<br>
**SSL/TLS:** Secure Sockets Layer / Transport Layer Security.<br>
**UPI:** Unified Payments Interface.<br>
**URI/URL:** Uniform Resource Identifier / Uniform Resource Locator.<br>
**VPA:** Virtual Payment Address.

<div style="page-break-after: always;"></div>

### **TABLE OF CONTENTS**

| Sr. No | Contents | Page No. |
| :--- | :--- | :--- |
| | **Abstract** | I |
| | **Abbreviations** | II |
| | **List of Figures** | III |
| | **List of Screenshots** | IV |
| | **List of Tables** | V |
| **1.** | **Introduction :** | **1** |
| | 1.1 Introduction | 1 |
| | 1.2 Problem Statement | 1 |
| | 1.3 Objectives | 2 |
| | 1.4 Scope of the Project | 2 |
| **2.** | **Literature Review :** | **3** |
| | 2.1 Review of Existing Systems / Related Work | 3 |
| | 2.2 Knowledge Gap | 4 |
| | 2.3 Summary of Findings | 4 |
| **3.** | **System Analysis :** | **5** |
| | 3.1 Requirement Analysis | 5 |
| | 3.2 Feasibility Study | 6 |
| | 3.3 System Specifications | 7 |
| **4.** | **System Design :** | **8** |
| | 4.1 System Architecture | 8 |
| | 4.2 Data Flow Diagrams / UML Diagrams | 9 |
| | 4.3 ER Diagram / Database Design | 10 |
| **5.** | **Implementation :** | **11** |
| | 5.1 Module-wise Description | 11 |
| | 5.2 Technologies Used | 12 |
| | 5.3 Core Logic & Scoring | 13 |
| | 5.4 Screenshots and Outputs | 14 |
| **6.** | **Testing & Validation :** | **18** |
| | 6.1 Types of Testing | 18 |
| | 6.2 Test Cases and Results | 19 |
| **7.** | **Results & Discussion :** | **20** |
| | 7.1 Performance Evaluation | 20 |
| | 7.2 Comparison with Existing Systems | 21 |
| | 7.3 Key Observations | 21 |
| **8.** | **Conclusion & Future Scope :** | **22** |
| | 8.1 Summary of Work | 22 |
| | 8.2 Project Limitations | 22 |
| | 8.3 Future Improvements | 23 |
| **9.** | **References / Bibliography** | **24** |
| **10.**| **Appendices** | **25** |

<div style="page-break-after: always;"></div>

### **Chapter 1: Introduction**

#### **1.1 Introduction**
The core requirement for modern cybersecurity is proactive threat detection. With the widespread adoption of QR codes for payments, menus, and website navigation, attackers have increasingly utilized them to mask malicious URLs and fraudulent UPI payment requests (Quishing). FraudEye is designed to solve these challenges through a comprehensive digital analysis platform. The system is a full-stack web application with a modern, user-friendly interface that intercepts, decodes, and analyzes QR codes, URLs, and UPI links in real-time, ensuring users are protected from phishing campaigns and credential harvesting before they compromise their devices.

#### **1.2 Problem Statement**
Traditional methods of verifying the authenticity of a QR code or a URL rely heavily on human intuition or post-infection antivirus scans, which are slow and error-prone. The current ecosystem poses severe cybersecurity difficulties. Attackers create look-alike domains (e.g., `paytm-secure.example`) and embed them in QR codes. When scanned by a standard smartphone camera, the user is immediately redirected to a malicious site. There is a critical lack of integrated, pre-execution analysis tools available to the average user that can instantly decode a QR code, trace its hidden redirects, verify the SSL certificate, and flag brand impersonation without exposing the user's device to the threat.

#### **1.3 Objectives**
The core objective of this project is to deliver a comprehensive, responsive, web-based scam detection system. The user interface allows individuals to manually input URLs, upload QR code images, or use their webcam to scan codes safely. The backend engine performs deep forensic analysis, including DNS resolution, SSL certificate validation, redirect tracing, and heuristic brand impersonation checks. A central objective is to calculate a mathematical "Trust Score" (0–100) and provide a clear Risk Level (Safe, Caution, Suspicious, Dangerous) so users can make immediate, informed decisions.

#### **1.4 Scope of the Project**
FraudEye is designed to automate the threat analysis of modern web payloads. The project includes functionalities found in the Customer Scanner Portal, the Enterprise Bulk Scanning module, and the Administrative Threat Dashboard. The system supports full CRUD operations on historical scan records, maintains an audit trail in a localized SQLite database, and supports automated report generation. It focuses specifically on URLs, Plain Text payloads, and UPI (Unified Payments Interface) URIs.

<div style="page-break-after: always;"></div>

### **Chapter 2: Literature Review**

#### **2.1 Review of Existing System / Related Work**
**2.1.1 Review of the Existing System (Manual Verification & Native Scanners)**
The current operational framework for average users involves scanning QR codes directly with native camera apps. This methodology is inherently insecure, as native cameras automatically execute the embedded URL redirect without performing comprehensive background checks. Financial operations and credential logins lack control and transparency when accessed via these links, significantly increasing the risk of human error and phishing attacks. While enterprise solutions like VirusTotal exist, they require manual URL extraction and do not specialize in analyzing UPI payment string formats or Indian-centric brand impersonation (like SBI or Paytm). 

**2.1.2 Related Work Review - Automated Threat Intelligence Solutions**
The review of existing systems confirms that proactive digital interception is an industry standard in enterprise cybersecurity. These systems universally feature integrated web and network monitoring alongside robust SSL and DNS checks. The proposed FraudEye system directly applies these successful industry concepts by incorporating a dedicated customer self-scanning web portal and facilitating modern payload checks through redirect tracing and API integrations. This strategic alignment ensures that FraudEye provides faster analysis, significantly improved accuracy, and a seamless digital experience that meets contemporary cybersecurity performance expectations.

#### **2.2 Knowledge Gap**
The transition from reactive antivirus scanning to proactive QR interception is driven by a critical knowledge gap rooted in fundamental deficiencies in the manual process across four key areas: data handling, speed, scalability, and user engagement. Validating a URL requires a user to manually check WHOIS records, SSL issuers, and follow HTTP chains—a tediously time-consuming process. The reliance on post-execution antivirus severely limits operational transparency. The proposed FraudEye system directly bridges this gap through complete digital automation, ensuring robust data consistency, enabling instant threat intelligence processing, and fundamentally enhancing security through automated reporting features.

#### **2.3 Summary of Findings**
FraudEye effectively bridges the operational and technological gaps identified in manual URL verification. Through complete backend automation, the system effectively reduces analysis time and eliminates common human errors in identifying phishing sites, ensuring faster, more accurate service delivery. Operationally, FraudEye promotes a secure digital environment by supporting safe QR decoding and centralizing threat data management. These enhancements provide significant benefits to the user base, including personalized features like scan history and analytics, all while guaranteeing a significantly faster pre-execution environment compared to the traditional native scanners.

<div style="page-break-after: always;"></div>

### **Chapter 3: System Analysis**

#### **3.1 Requirement Analysis**
**3.1.1 Functional Requirements:**
The project delivers an end-to-end digital cybersecurity system, making the operational workflow smooth from scanning payloads to advanced threat intelligence. It includes real-time QR code decoding via device cameras, manual URL analysis, and bulk URL processing. Vital data governance is ensured through a Risk Engine that calculates trust scores based on SSL checks, Redirect loops, and Threat Intelligence blacklists. The system maintains a historical dashboard (`index.php`) and a detailed transaction history (`history.php`) for auditing purposes.

**3.1.2 Non-Functional Requirements:**
The application architecture was developed with a strong focus on core quality attributes, prioritizing robust performance and security. Performance is ensured through the implementation of fast asynchronous API calls (`fetch`) and efficient Document Object Model (DOM) updates. For security, the system enforces rigorous backend validation in PHP to prevent Server-Side Request Forgery (SSRF) when tracing redirects. Furthermore, the overall design emphasizes usability, utilizing Tailwind CSS for a fully responsive experience, guaranteeing a seamless and optimal experience across all device types, including desktop, tablet, and mobile platforms.

#### **3.2 Feasibility Study**
**3.2.1 Technical Feasibility:**
The project demonstrates strong technical and economic feasibility. Technically, the system is built upon a mature web stack utilizing HTML5, Tailwind CSS, JavaScript (jsQR), PHP 8.0, and SQLite 3. This eliminates the need for complex, heavy database server setups while maintaining the robust processing power required for network-level socket connections (SSL checking) and cURL requests (Redirect tracking). Economically, the solution is highly attractive, requiring very low development and maintenance costs with no anticipated recurring server expenses for the prototype phase.

**3.2.2 Operational Feasibility:**
Our comprehensive digital solution is built around a user-friendly, mobile-responsive design to guarantee high user acceptance and a superior scanning experience. Operationally, it ensures smooth threat-hunting workflows by providing color-coded, easy-to-read security reports (Green for Safe, Red for Dangerous) rather than complex technical logs, making it accessible to non-technical users. It meticulously records all scan transactions for future reference and administrative oversight.

#### **3.3 System Specifications**
**3.3.1 Hardware Requirements:**
• Standard desktop/laptop or smartphone with at least 4GB RAM.
• Webcam/Smartphone Camera required for live QR scanning.

**3.3.2 Software Requirements:**
• Front-end: HTML5, CSS3 (Tailwind CSS), JavaScript (ES6+), jsQR.
• Back-end: PHP 8.0+ running on XAMPP/Apache.
• Database: SQLite 3 (file-based database for portability).
• Any modern web browser: Chrome, Firefox, Edge, Safari.

<div style="page-break-after: always;"></div>

### **Chapter 4: System Design**

#### **4.1 System Architecture**
FraudEye uses a standard Client-Server architecture. The Presentation Layer (Customer Interface) handles QR extraction and user inputs, sending the raw payload to the Application Layer (`api/scan.php`). The API interacts with the `RiskEngine.php`, which acts as the orchestrator, delegating tasks to specialized micro-modules: `SSLChecker`, `RedirectChecker`, `DomainChecker`, and `Classifier`. The results are aggregated, scored, and finally persisted in the SQLite Data Storage Layer.

#### **4.2 Data Flow Diagram / UML Diagram**
The flow of data through FraudEye is highly structured:
1.  **Input Flow**: User uploads a QR code or pastes a URL -> JavaScript extracts the payload -> Sent via POST request to `/api/scan.php`.
2.  **Process Flow**: API routes the payload to the Risk Engine -> Payload is Classified -> Network Checks are Executed sequentially (cURL for redirects, OpenSSL for certificates) -> Trust Score is mathematically calculated based on heuristic rules.
3.  **Output Flow**: The Risk Engine formats a JSON response -> Data is saved to the SQLite `scan_sessions` table -> JavaScript updates the UI with the final Risk Verdict, threat indicators, and visual charts.

#### **4.3 ER Diagram / Database Design**
The application relies on several linked relational tables in SQLite to maintain its audit trail:
*   **`scan_sessions`**: The primary master table. Contains `session_id`, `payload`, `input_type`, `trust_score`, `risk_level`, and `created_at`.
*   **`url_analysis`**: Links to the `scan_sessions` table. Stores domain-specific data such as `domain`, `final_url`, `ssl_issuer`, and `is_blacklisted`.
*   **`threat_indicators`**: Stores one-to-many specific evidence flags (e.g., `BRAND_IMPERSONATION`, `SSL_EXPIRED`) linked to a single scan session.
*   **`payment_checks`**: Stores extracted metadata specific to UPI payloads (e.g., `VPA`, `amount`, `receiver_name`).

<div style="page-break-after: always;"></div>

### **Chapter 5: Implementation**

#### **5.1 Module-wise Description**
The FraudEye System offers a set of modules that automate the scanning, analysis, monitoring, and reporting processes. 

**Scanner Module (`scanner.php`)**
Utilizes the device's camera stream and the `jsQR` library to detect and decode QR codes entirely on the client side. This ensures malicious links are intercepted and analyzed by the backend before the browser can natively navigate to them, providing a safe pre-execution environment.

**Risk Engine Module (`RiskEngine.php`)**
The core backend logic and operational hub. It acts as the central orchestrator, passing the payload through various filters (`Classifier`, `SSLChecker`, `RedirectChecker`, `DomainChecker`, `ThreatIntel`). It applies a mathematical scoring system (+20 for valid SSL, -40 for Blacklist, -40 for Impersonation) to dynamically rate the safety of a payload and assign a final verdict.

**Dashboard Module (`index.php`) & History (`history.php`)**
A central operational hub that queries the SQLite database to display real-time metrics, such as total scans, threat distribution charts, and recent high-risk detections. The History module provides a complete, filterable record of all past scan transactions for auditing and review.

**Bulk Processing Module (`bulk.php`)**
Allows administrators to input multiple URLs separated by commas, processing them asynchronously in a loop and rendering a tabular report of all results simultaneously, vastly improving efficiency for large-scale checks.

#### **5.2 Technologies Used**
FraudEye uses modern web technologies: 
*   **HTML5 / Tailwind CSS** lays the foundation for web pages and improves looks and responsive design so that the interface works flawlessly across all devices. 
*   **JavaScript (ES6+)** provides logic and interactivity, managing asynchronous API calls (`fetch`), client-side QR decoding with `jsQR`, and dynamic DOM updates. 
*   **PHP 8.0+** powers the backend API and Risk Engine, utilizing modules like `cURL` for network requests and `OpenSSL` for certificate extraction. 
*   **SQLite 3** acts as a light, portable relational database, storing scan histories and session data without the overhead of a complex server setup.

#### **5.3 Core Logic & Scoring**
The major functionalities deal with data validation and risk calculation. The system relies on a rigorous points-based logic to calculate a "Trust Score":
```php
// Trust Score Calculation Logic Example
$trustScore = 0;
if (!$hasInvalidSSL) $trustScore += 20; // Reward for valid SSL
if (!$isBlacklisted) $trustScore += 20; // Reward for clean domain
if ($hasInvalidSSL) $trustScore -= 20;  // Penalty for invalid SSL
if ($hasBrandImpersonation) $trustScore -= 40; // Heavy penalty for phishing
if ($isMalware) $trustScore -= 50; // Critical penalty for malware
```

<div style="page-break-after: always;"></div>

### **Chapter 6: Testing & Validation**

#### **6.1 Types of Testing**
Testing ensures that the FraudEye System is reliable and works properly. Since it is built with PHP and JavaScript, tests focused on both backend security logic and frontend data handling.

**Unit Testing:**
Unit Testing represents the most fundamental layer of the testing strategy, focused entirely on validating the smallest, independent components of the application. For FraudEye, rigorous Unit Testing was applied to key PHP classes. The `SSLChecker` was tested to ensure it correctly identified expired or HTTP-only certificates. The `Classifier` was verified to accurately distinguish between URLs, Plain Text, and UPI strings. By isolating and validating these functions, Unit Testing established a mathematically accurate foundation for the Risk Engine.

**Integration Testing:**
Integration Testing followed the Unit Testing phase and was essential for validating the flow of data and communication between the different modules. This phase confirmed the seamless interaction between the JavaScript frontend `fetch` API and the PHP backend. It ensured that JSON payloads were properly stringified, transmitted, parsed by the Risk Engine, and returned with the correct HTTP headers without CORS or formatting errors, guaranteeing data consistency between the client and server.

**System Testing:**
System Testing is the final and most comprehensive layer of validation, evaluating the integrated FraudEye system as a complete product by simulating real-world scenarios. This high-level test covers the entire end-to-end operational flow, from a user scanning a malicious QR code on their webcam to the final output of the Red "DANGEROUS" verdict on the UI. It verified that the system handles edge cases gracefully, such as infinite redirect loops or naked domains that redirect to HTTPS, without crashing.

#### **6.2 Test Cases and Results**
Manual testing showed positive results across all major features.

| Test ID | Scenario | Status |
| :--- | :--- | :--- |
| T01 | Scan valid, trusted URL (Google.com) | Passed (SAFE) |
| T02 | Scan Naked Domain (erp.mgmu.ac.in) that redirects to HTTPS | Passed (SAFE) |
| T03 | Scan Fake Brand URL (e.g., paytm-secure.example) | Passed (DANGEROUS) |
| T04 | Bulk Scan 3 mixed URLs simultaneously via bulk.php | Passed |
| T05 | Scan Plain Text or UPI ID (Null Domains) | Passed (Handled safely) |

<div style="page-break-after: always;"></div>

### **Chapter 7: Results & Discussion**

#### **7.1 Performance Evaluation**
The FraudEye System effectively achieves its primary objective of enhancing digital security and operational efficiency through significant reductions in analysis time. For Customer Service Speed, the digital, automated scanning model cuts the average URL verification time by approximately 90% (from several minutes of manual investigation down to under 3 seconds). The Operational Handoff is made instantaneous; the use of asynchronous JavaScript allows the frontend to send the payload to the backend Risk Engine and display the verdict almost immediately, thereby establishing a real-time workflow that keeps users safe without causing frustrating delays.

| Operation | Manual Investigation Time | FraudEye Digital Time | Key Gain |
| :--- | :--- | :--- | :--- |
| **URL Analysis** | 2–5 minutes | < 3 seconds | 90% faster for users |
| **QR Decoding** | Native Camera App | Instant via WebRTC | Pre-execution safety |
| **Reporting** | Manual logging | Instant database entry | Major productivity gain |

#### **7.2 Comparison with Existing Systems**
The theoretical foundation for this comparison is rooted in the shift from reactive to proactive cybersecurity. This comparison justifies the implementation of FraudEye by contrasting its performance against traditional Post-Execution Antivirus solutions and Native Smartphone Cameras. The core theory posits that FraudEye offers a superior value proposition by shifting operations to a Pre-Execution, Interception Model.

| Feature | Traditional Antivirus / Native Scanner | FraudEye CMS | Advantage |
| :--- | :--- | :--- | :--- |
| **Analysis Timing** | Post-execution (After click) | Pre-execution (Interception) | Faster & prevents initial infection |
| **QR Code Support** | Poor / Automatically executes | Native Browser & Camera integration | Seamless & safe mobile experience |
| **UPI Link Parsing** | None | Deep VPA formatting checks | Protects against payment fraud |
| **Deployment** | Heavy OS Installation | Web-based / URL Access | High portability and accessibility |

#### **7.3 Key Observations**
The key observations derived from the FraudEye System testing confirm the project's success across crucial operational dimensions. Firstly, Workflow Speed Validation confirmed that the system's sub-second redirect tracing and rapid SSL checking translated directly into actual, quantifiable efficiency gains, fully realizing the project's real-time goal. Secondly, the necessity of order-of-operations in the backend was proven: running the `RedirectChecker` before the `SSLChecker` ensured that naked HTTP domains that securely redirect to HTTPS were not falsely penalized. Finally, the mobile-friendly Tailwind interface contributed to high user acceptance, demonstrating the system's comprehensive operational feasibility beyond mere technical soundness.

<div style="page-break-after: always;"></div>

### **Chapter 8: Conclusion & Future Scope**

#### **8.1 Summary of Work**
The FraudEye project successfully culminated in the creation of a fully operational digital cybersecurity prototype specifically designed to solve the significant vulnerabilities of traditional QR code scanning and URL verification. We validated the concept of a strong, proactive security tool built using a localized PHP backend and a responsive JavaScript frontend, utilizing SQLite to ensure data persistence and system portability. By digitalizing core threat intelligence tasks, FraudEye achieved its main goals: it delivered Faster Analysis by cutting verification time down to seconds; it provided a Better Experience for customers through safe, pre-execution QR scanning; and it enabled Smarter Management by providing instant, automated threat reports and a deterministic Trust Score. In essence, FraudEye is a successful technological intervention that fundamentally transforms URL scanning into a fast, highly automated, and user-focused safe environment.

#### **8.2 Project Limitations**
The current version of FraudEye is a functional prototype built primarily to demonstrate core concepts, and as such, it faces inherent limitations that restrict its deployment in a large-scale, enterprise environment. The most significant constraint is its reliance on heuristic, string-matching rules for Brand Impersonation. Attackers frequently change tactics, and static heuristic lists require constant manual updating, which limits scalability. Additionally, because the SQLite database operates as a single file, the system cannot support true high-concurrency enterprise access where data is heavily written by thousands of users simultaneously. Finally, its design currently operates without a distributed server architecture, making it vulnerable to heavy traffic loads.

#### **8.3 Future Improvements**
In the future, improvements can be made to turn FraudEye into a fully deployable, commercial application. Migration of the data system to a server-based relational database like PostgreSQL will allow multiple user access and massive scalability. Security enhancements can be done by integrating real Machine Learning (ML) models via Python microservices to detect phishing visual similarities and sophisticated domain generation algorithms (DGAs), replacing the static heuristic lists. Furthermore, packaging the web application into a native Android/iOS app would allow it to intercept system-level camera intents, completely replacing the vulnerable native camera QR scanners on smartphones, making the system more efficient, user-friendly, and commercially viable.

<div style="page-break-after: always;"></div>

### **References / Bibliography**

1. Research on Phishing Trends and Quishing (QR Phishing) methodologies.
2. Official PHP Documentation for OpenSSL and cURL modules.
3. SQLite3 Database Architecture, Limitations, and Optimization.
4. Tailwind CSS styling frameworks and responsive design principles.
5. OWASP Top 10 Security Risks and Mitigation Strategies.

<div style="page-break-after: always;"></div>

### **Appendices**

**Appendix A: Source Code**

This appendix contains essential portions of the Project's Source code. Due to its length, the complete codebase is not included here but can be accessed via the following link:

**Link to GitHub Repository:** 
[https://github.com/sarthaksalunke77/Secure-QR-Scam-detection-systemfinal](https://github.com/sarthaksalunke77/Secure-QR-Scam-detection-systemfinal)

<br><br><br>
*(End of Report)*
