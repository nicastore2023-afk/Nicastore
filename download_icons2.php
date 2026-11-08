<?php
// Try to download from jsdelivr with correct path
$baseUrl = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/';
$files = ['bootstrap-icons.woff2', 'bootstrap-icons.woff', 'bootstrap-icons.ttf', 'bootstrap-icons.css'];

$targetDir = 'C:/xampp/htdocs/PROTOTIPO/public/fonts/bootstrap-icons/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

foreach ($files as $file) {
    $url = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/' . $file;
    echo "Trying: $url\n";
    $content = @file_get_contents($url);
    if ($content !== false) {
        file_put_contents('C:/xampp/htdocs/PROTOTIPO/public/fonts/bootstrap-icons/' . basename($file), $content);
        echo "Downloaded: $file\n";
    } else {
        echo "Failed: $file\n";
    }
}

echo "Done!\n";