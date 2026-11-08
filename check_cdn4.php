<?php
// Try different CDN paths
$baseUrls = [
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/',
];

foreach ($baseUrls as $baseUrl) {
    echo "Trying: $baseUrl\n";
    $files = ['bootstrap-icons.css', 'bootstrap-icons.min.css', 'font/bootstrap-icons.woff2'];
    
    foreach (['bootstrap-icons.min.css', 'font/bootstrap-icons.woff2'] as $file) {
        $url = $baseUrl . $file;
        $headers = @get_headers($url, 1);
        echo "  $file: " . ($headers ? $headers[0] : 'Not found') . "\n";
    }
    echo "\n";
}