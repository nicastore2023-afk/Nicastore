<?php
// Download bootstrap icons from GitHub release
$version = '1.11.3';
$baseUrl = "https://github.com/twbs/bootstrap-icons/releases/download/v$version/";

$files = [
    'bootstrap-icons.css',
    'fonts/bootstrap-icons.woff2',
    'fonts/bootstrap-icons.woff',
    'fonts/bootstrap-icons.ttf'
];

$targetDir = 'C:/xampp/htdocs/PROTOTIPO/public/fonts/bootstrap-icons/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

foreach ($files as $file) {
    $url = $baseUrl . $file;
    $targetFile = $targetDir . basename($file);
    echo "Downloading $file...\n";
    $content = @file_get_contents($url);
    if ($content !== false) {
        file_put_contents($targetFile, $content);
        echo "Downloaded: $file\n";
    } else {
        echo "Failed: $file\n";
    }
}

echo "Done!\n";