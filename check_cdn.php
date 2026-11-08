<?php
// Check what files exist in the bootstrap-icons font directory on CDN
$baseUrl = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/';
$files = ['bootstrap-icons.woff2', 'bootstrap-icons.woff', 'bootstrap-icons.ttf', 'bootstrap-icons.eot', 'bootstrap-icons.svg'];

foreach ($files as $file) {
    $url = $baseUrl . $file;
    $headers = @get_headers($url, 1);
    echo "$file: " . ($headers ? $headers[0] : 'Not found') . "\n";
}