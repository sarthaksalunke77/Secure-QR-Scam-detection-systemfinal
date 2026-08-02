<?php
$html = file_get_contents('http://localhost:8000/report.php?id=3878');
$pos = strpos($html, 'SECURITY INDICATORS (EVIDENCE)');
if ($pos !== false) {
    echo substr($html, $pos, 1000);
}
