<?php
// Check the correct path for bootstrap icons on jsdelivr
$baseUrl = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/';
$files = ['bootstrap-icons.css', 'bootstrap-icons.min.css', 'font/bootstrap-icons.woff2', 'font/bootstrap-icons.woff', 'font/bootstrap-icons.ttf', 'font/bootstrap-icons.eot', 'font/bootstrap-icons.svg'];

foreach ($files as $file) {
    $url = $baseUrl . $file;
    $headers = @get_headers($url, 1);
    echo "$file: " . ($headers ? $headers[0] : 'Not found') . "\n";
}