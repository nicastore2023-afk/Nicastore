<?php
require 'vendor/autoload.php';
use Nicastore\Config\DatabaseConfig;
$pdo = DatabaseConfig::getConnection();
$pdo->exec('DELETE FROM sessions WHERE id LIKE "ratelimit_%"');
echo "Rate limit cleared from database\n";