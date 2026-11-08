<?php

declare(strict_types=1);

namespace Nicastore\Tests;

use Nicastore\Core\Database;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected static function setUpDatabase(): void
    {
        // Ensure test database is used
        $_ENV['DB_DATABASE'] = 'database/nicastore_test.sqlite';
        $_ENV['APP_ENV'] = 'testing';
        $_ENV['APP_DEBUG'] = 'false';
        $_ENV['SESSION_SECURE'] = 'false';
        
        // Reset database singleton to use test database
        Database::resetInstance();
    }
    
    protected function setUp(): void
    {
        self::setUpDatabase();
    }
    
    protected function tearDown(): void
    {
        // Clean up if needed
    }
}