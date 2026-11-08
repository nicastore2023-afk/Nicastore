<?php
require 'vendor/autoload.php';
use Nicastore\Config\DatabaseConfig;
$pdo = DatabaseConfig::getConnection();
$stmt = $pdo->query('SELECT id, name, image, images FROM products WHERE is_active = 1');
foreach($stmt->fetchAll() as $row) {
    echo "ID: {$row['id']} | Name: {$row['name']} | Image: {$row['image']} | Images: {$row['images']}\n";
}