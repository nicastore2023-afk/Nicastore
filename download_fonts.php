<?php
$files = [
    'bootstrap-icons.min.css' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'bootstrap-icons.css' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
    'bootstrap-icons.woff2' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2',
    'bootstrap-icons.woff' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff',
    'bootstrap-icons.ttf' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.ttf',
];

$targetDir = 'C:/xampp/htdocs/PROTOTIPO/public/fonts/bootstrap-icons/';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

foreach ($files as $name => $url) {
    echo "Downloading $name...\n";
    $content = @file_get_contents($url);
    if ($content !== false) {
        file_put_contents('C:/xampp/htdocs/PROTOTIPO/public/fonts/bootstrap-icons/' . $name, $content);
        echo "Downloaded: $name\n";
    } else {
        echo "Failed: $name\n";
    }
}

echo "Done!\n";