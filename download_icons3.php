<?php
$files = [
    'bootstrap-icons.woff2' => 'https://raw.githubusercontent.com/twbs/bootstrap-icons/v1.11.3/fonts/bootstrap-icons.woff2',
    'bootstrap-icons.woff' => 'https://raw.githubusercontent.com/twbs/bootstrap-icons/v1.11.3/fonts/bootstrap-icons.woff',
    'bootstrap-icons.ttf' => 'https://raw.githubusercontent.com/twbs/bootstrap-icons/v1.11.3/fonts/bootstrap-icons.ttf',
    'bootstrap-icons.css' => 'https://raw.githubusercontent.com/twbs/bootstrap-icons/v1.11.3/font/bootstrap-icons.css',
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