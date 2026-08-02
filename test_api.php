<?php
$data = ['payload' => 'http://secure-login-update.example.com'];
$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
    ]
];
$context  = stream_context_create($options);
$result = file_get_contents('http://localhost:8000/api/scan.php', false, $context);
print_r(json_decode($result, true));
