<?php
$urls = [
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
    'https://unpkg.com/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
    'https://unpkg.com/bootstrap-icons@1.11.3/font/bootstrap-icons.css',
];

foreach ($urls as $url) {
    $headers = @get_headers($url, 1);
    $status = $headers ? $headers[0] : 'Not found';
    echo "$url: $status\n";
}