<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FraudEye - Web Security Scanner</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Using Phosphor Icons as a replacement for Lucide -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Tailwind CSS Browser Compiler for new utility classes -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-cyber-bg text-gray-200">
    <nav class="glass-panel sticky top-0 z-50 px-6 py-4 mb-8">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="ph ph-shield-check text-cyber-primary text-3xl"></i>
                <span class="text-xl font-bold tracking-wider text-white">FRAUD<span class="text-cyber-primary">EYE</span></span>
            </div>
            <div class="flex gap-6 items-center">
                <!-- Google Translate Widget (Hidden but active for translations) -->
                <div id="google_translate_element" class="hidden !important"></div>
                <script type="text/javascript">
                    function googleTranslateElementInit() {
                        new google.translate.TranslateElement({
                            pageLanguage: 'en',
                            includedLanguages: 'en,hi,mr',
                            layout: google.translate.TranslateElement.InlineLayout.SIMPLE
                        }, 'google_translate_element');
                    }
                </script>
                <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

                <a href="index.php" class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                    <i class="ph ph-activity"></i> Home
                </a>
                <a href="scanner.php" class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                    <i class="ph ph-scan"></i> Scanner
                </a>
                <a href="check.php" class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                    <i class="ph ph-link"></i> URL & UPI Check
                </a>
                <a href="bulk.php" class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                    <i class="ph ph-cloud-arrow-up"></i> Bulk Analysis
                </a>
                <a href="history.php" class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                    <i class="ph ph-clock-counter-clockwise"></i> History
                </a>
                <a href="awareness.php" class="flex items-center gap-2 text-gray-300 hover:text-white transition-colors">
                    <i class="ph ph-shield-warning"></i> Awareness
                </a>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto px-6 pb-12">
