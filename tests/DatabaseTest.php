<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Nicastore\Core\Database;
use Nicastore\Tests\BaseTestCase;

class DatabaseTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clean tables before each test using Database singleton
        $pdo = Database::getInstance();
        $pdo->exec("DELETE FROM users");
        $pdo->exec("DELETE FROM products");
        $pdo->exec("DELETE FROM cart_items");
        $pdo->exec("DELETE FROM coupons");
    }
    
    private function getPdo(): \PDO
    {
        return Database::getInstance();
    }
    
    public function testInsertUser(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $result = $stmt->execute(['Test User', 'test@test.com', 'hashedpass', 'customer']);
        
        $this->assertTrue($result);
        
        $id = (int)$pdo->lastInsertId();
        $this->assertGreaterThan(0, $id);
    }
    
    public function testFindUserById(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute(['Test User', 'test@test.com', 'hashedpass', 'customer']);
        $id = (int)$pdo->lastInsertId();
        
        $user = $pdo->query("SELECT * FROM users WHERE id = {$id}")->fetch();
        
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user['name']);
        $this->assertEquals('test@test.com', $user['email']);
    }
    
    public function testFindUserByEmail(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute(['Test User', 'test@test.com', 'hashedpass', 'customer']);
        
        $user = $pdo->query("SELECT * FROM users WHERE email = 'test@test.com'")->fetch();
        
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user['name']);
    }
    
    public function testUpdateUser(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute(['Test User', 'test@test.com', 'hashedpass', 'customer']);
        $id = (int)$pdo->lastInsertId();
        
        $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->execute(['Updated User', $id]);
        
        $user = $pdo->query("SELECT * FROM users WHERE id = {$id}")->fetch();
        $this->assertEquals('Updated User', $user['name']);
    }
    
    public function testDeleteUser(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute(['Test User', 'test@test.com', 'hashedpass', 'customer']);
        $id = (int)$pdo->lastInsertId();
        
        $pdo->exec("DELETE FROM users WHERE id = {$id}");
        
        $user = $pdo->query("SELECT * FROM users WHERE id = {$id}")->fetch();
        $this->assertFalse($user);
    }
    
    public function testProductInsertAndFind(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, name, slug, price, stock, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $result = $stmt->execute([1, 'Test Product', 'test-product-' . uniqid(), 29.99, 50, 1]);
        
        $this->assertTrue($result);
        
        $product = $pdo->query("SELECT * FROM products WHERE name = 'Test Product'")->fetch();
        $this->assertNotNull($product);
        $this->assertEquals(29.99, (float)$product['price']);
        $this->assertEquals(50, (int)$product['stock']);
    }
    
    public function testProductStockDecrease(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, name, slug, price, stock, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([1, 'Test Product', 'test-product-' . uniqid(), 29.99, 50, 1]);
        $id = (int)$pdo->lastInsertId();
        
        $pdo->exec("UPDATE products SET stock = stock - 5 WHERE id = {$id}");
        
        $product = $pdo->query("SELECT * FROM products WHERE id = {$id}")->fetch();
        $this->assertEquals(45, (int)$product['stock']);
    }
    
    public function testCartItemInsert(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, name, slug, price, stock, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([1, 'Test Product', 'test-product-' . uniqid(), 29.99, 50, 1]);
        $productId = (int)$pdo->lastInsertId();
        
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (product_id, quantity, session_id) 
            VALUES (?, ?, ?)
        ");
        $result = $stmt->execute([$productId, 2, 'test_session_123']);
        
        $this->assertTrue($result);
        
        $cartItem = $pdo->query("SELECT * FROM cart_items WHERE product_id = {$productId}")->fetch();
        $this->assertNotNull($cartItem);
        $this->assertEquals(2, (int)$cartItem['quantity']);
    }
    
    public function testCartQuantityUpdate(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, name, slug, price, stock, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([1, 'Test Product', 'test-product-' . uniqid(), 29.99, 50, 1]);
        $productId = (int)$pdo->lastInsertId();
        
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (product_id, quantity, session_id) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$productId, 2, 'test_session_123']);
        $cartId = (int)$pdo->lastInsertId();
        
        $pdo->exec("UPDATE cart_items SET quantity = 5 WHERE id = {$cartId}");
        
        $cartItem = $pdo->query("SELECT * FROM cart_items WHERE id = {$cartId}")->fetch();
        $this->assertEquals(5, (int)$cartItem['quantity']);
    }
    
    public function testCartItemRemoval(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, name, slug, price, stock, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([1, 'Test Product', 'test-product-' . uniqid(), 29.99, 50, 1]);
        $productId = (int)$pdo->lastInsertId();
        
        $stmt = $pdo->prepare("
            INSERT INTO cart_items (product_id, quantity, session_id) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$productId, 2, 'test_session_123']);
        $cartId = (int)$pdo->lastInsertId();
        
        $pdo->exec("DELETE FROM cart_items WHERE id = {$cartId}");
        
        $cartItem = $pdo->query("SELECT * FROM cart_items WHERE id = {$cartId}")->fetch();
        $this->assertFalse($cartItem);
    }
    
    public function testCouponValidation(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO coupons (code, type, value, min_order_amount, is_active) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(['WELCOME10', 'percentage', 10.00, 50.00, 1]);
        
        $coupon = $pdo->query("SELECT * FROM coupons WHERE code = 'WELCOME10'")->fetch();
        
        $this->assertNotNull($coupon);
        $this->assertEquals('percentage', $coupon['type']);
        $this->assertEquals(10.00, (float)$coupon['value']);
        $this->assertEquals(50.00, (float)$coupon['min_order_amount']);
        $this->assertEquals(1, (int)$coupon['is_active']);
    }
    
    public function testCouponIncrementUsage(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO coupons (code, type, value, is_active, used_count) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute(['TEST10', 'percentage', 10.00, 1, 5]);
        $id = (int)$pdo->lastInsertId();
        
        $pdo->exec("UPDATE coupons SET used_count = used_count + 1 WHERE id = {$id}");
        
        $coupon = $pdo->query("SELECT * FROM coupons WHERE id = {$id}")->fetch();
        $this->assertEquals(6, (int)$coupon['used_count']);
    }
    
    public function testMultipleUsersCanBeInserted(): void
    {
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute(['User 1', 'user1@test.com', 'pass1', 'customer']);
        $stmt->execute(['User 2', 'user2@test.com', 'pass2', 'customer']);
        $stmt->execute(['Admin', 'admin@test.com', 'pass3', 'admin']);
        
        $count = (int)$pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
        $this->assertEquals(3, $count);
    }
    
    public function testProductPagination(): void
    {
        // Insert 25 test products
        $pdo = $this->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO products (category_id, name, slug, price, stock, is_active) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        for ($i = 1; $i <= 25; $i++) {
            $stmt->execute([1, "Product {$i}", "product-{$i}-" . uniqid(), 10.00 + $i, 100, 1]);
        }
        
        $total = (int)$pdo->query("SELECT COUNT(*) as count FROM products")->fetch()['count'];
        $this->assertEquals(25, $total);
        
        // Get page 1 (12 items)
        $page1 = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 12 OFFSET 0")->fetchAll();
        $this->assertCount(12, $page1);
        
        // Get page 2 (12 items)
        $page2 = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 12 OFFSET 12")->fetchAll();
        $this->assertCount(12, $page2);
        
        // Get page 3 (1 item)
        $page3 = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 12 OFFSET 24")->fetchAll();
        $this->assertCount(1, $page3);
    }
    
    public function testUserPasswordHashing(): void
    {
        $pdo = $this->getPdo();
        $password = 'SecurePass123!';
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute(['Test User', 'hash@test.com', $hash, 'customer']);
        
        $user = $pdo->query("SELECT * FROM users WHERE email = 'hash@test.com'")->fetch();
        
        $this->assertTrue(password_verify($password, $user['password']));
    }
}