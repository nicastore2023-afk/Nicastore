<?php
$url = 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css';
$css = file_get_contents($url);
if ($css === false) {
    die("Failed to download CSS\n");
}

// Replace CDN URLs with local paths
$css = str_replace('url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/', 'url("../fonts/bootstrap-icons/', $css);
$css = str_replace("url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/", "url('../fonts/bootstrap-icons/", $css);

file_put_contents('C:/xampp/htdocs/PROTOTIPO/public/css/bootstrap-icons/bootstrap-icons.min.css', $css);
echo "CSS downloaded and modified\n";

// Download font files
$fontUrls = [
    'bootstrap-icons.woff2' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.woff2',
    'bootstrap-icons.woff' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.woff',
    'bootstrap-icons.ttf' => 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.ttf',
];

mkdir('C:/xampp/htdocs/PROTOTIPO/public/fonts/bootstrap-icons', true);

foreach ($fontUrls as $name => $url) {
    $content = file_get_contents($url);
    if ($content !== false) {
        file_put_contents("C:/xampp/htdocs/PROTOTIPO/public/fonts/bootstrap-icons/$name", $content);
        echo "Downloaded $name\n";
    } else {
        echo "Failed to download $name\n";
    }
}

echo "Done!\n";