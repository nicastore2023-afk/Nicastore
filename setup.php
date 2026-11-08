<?php

declare(strict_types=1);

/**
 * Nicastore Setup Script
 * 
 * This script sets up the Nicastore online store:
 * 1. Installs Composer dependencies
 * 2. Creates the SQLite database
 * 3. Runs database migrations
 * 4. Seeds sample data
 */

echo "=====================================\n";
echo "   Nicastore Setup Wizard\n";
echo "=====================================\n\n";

// Check if Composer is installed
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "Installing Composer dependencies...\n";
    
    $composerPath = __DIR__ . '/composer.phar';
    if (!file_exists($composerPath)) {
        echo "Downloading Composer...\n";
        copy('https://getcomposer.org/installer', 'composer-setup.php');
        exec('php composer-setup.php');
        unlink('composer-setup.php');
    }
    
    echo "Running: php composer.phar install\n";
    passthru('php composer.phar install');
    echo "\n";
}

// Check if autoload exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "ERROR: Failed to install Composer dependencies.\n";
    echo "Please run: composer install\n";
    exit(1);
}

echo "✓ Composer dependencies installed\n\n";

// Create uploads directory
$uploadDirs = [
    __DIR__ . '/public/images/products/',
    __DIR__ . '/public/images/categories/',
];

foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "✓ Created directory: {$dir}\n";
    }
}

// Run migrations
echo "\nRunning database migrations...\n";
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Config/Database.php';

use Nicastore\Config\DatabaseConfig;

try {
    $pdo = DatabaseConfig::getConnection();
    
    $schema = file_get_contents(__DIR__ . '/database/schema.sql');
    if ($schema) {
        $pdo->exec($schema);
        echo "✓ Database schema created\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// Ask if user wants to seed sample data
echo "\nDo you want to insert sample data? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (strtolower(trim($line)) === 'y' || strtolower(trim($line)) === 'yes') {
    echo "\nInserting sample data...\n";
    require_once __DIR__ . '/database/seed.php';
}

echo "\n=====================================\n";
echo "   Setup Complete!\n";
echo "=====================================\n\n";

echo "To start the development server, run:\n";
echo "  php -S localhost:8000 -t public\n\n";

echo "Test accounts:\n";
echo "  Admin: admin@nicastore.com / password123\n";
echo "  Customer: customer@nicastore.com / password123\n\n";

echo "Next steps:\n";
echo "  1. Run 'composer install' if not already done\n";
echo "  2. Start the server: php -S localhost:8000 -t public\n";
echo "  3. Visit: http://localhost:8000\n";
echo "  4. Run tests: ./vendor/bin/phpunit\n\n";