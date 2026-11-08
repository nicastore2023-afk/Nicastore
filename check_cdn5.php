<?php
$baseUrls = [
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/'
];

$files = ['bootstrap-icons.min.css', 'font/bootstrap-icons.woff2'];

foreach ($baseUrls as $baseUrl) {
    foreach (['bootstrap-icons.min.css', 'font/bootstrap-icons.woff2'] as $file) {
        $url = $baseUrl . $file;
        $headers = @get_headers($url, 1);
        $status = $headers ? $headers[0] : 'Not found';
        if (strpos($status, '200') !== false) {
            echo "FOUND: $baseUrl$file\n";
            exit;
        }
    }
}
echo "Done\n";