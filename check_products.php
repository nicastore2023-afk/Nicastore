<?php
require 'vendor/autoload.php';
$pdo = new PDO('sqlite:database/nicastore.sqlite');
$count = $pdo->query("SELECT COUNT(*) as count FROM products")->fetch();
echo "Products count: " . $count['count'] . "\n";

$products = $pdo->query("SELECT * FROM products LIMIT 5")->fetchAll();
print_r($products);