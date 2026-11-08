<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Nicastore\Models\User;
use Nicastore\Models\Product;
use Nicastore\Models\Cart;
use Nicastore\Models\Coupon;
use Nicastore\Models\Order;
use Nicastore\Core\Database;
use Nicastore\Tests\BaseTestCase;

class ModelTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Clean tables using Database singleton
        $pdo = Database::getInstance();
        $pdo->exec("DELETE FROM users");
        $pdo->exec("DELETE FROM products");
        $pdo->exec("DELETE FROM cart_items");
        $pdo->exec("DELETE FROM coupons");
        $pdo->exec("DELETE FROM orders");
        $pdo->exec("DELETE FROM order_items");
    }
    
    public function testUserCreation(): void
    {
        $userModel = new User();
        $email = 'test_' . uniqid() . '@test.com';
        $userId = $userModel->createUser([
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password123'
        ]);
        
        $this->assertGreaterThan(0, $userId);
        
        $user = $userModel->find($userId);
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user['name']);
        $this->assertEquals($email, $user['email']);
    }
    
    public function testUserPasswordsDiffer(): void
    {
        $userModel = new User();
        $userModel->createUser([
            'name' => 'Test User',
            'email' => 'pwd1_' . uniqid() . '@test.com',
            'password' => 'password123'
        ]);
        
        $userModel2 = new User();
        $userModel2->createUser([
            'name' => 'Test User 2',
            'email' => 'pwd2_' . uniqid() . '@test.com',
            'password' => 'password123'
        ]);
        
        $user1 = $userModel->all();
        $user2 = $userModel2->all();
        
        // Different users should have different password hashes
        $this->assertNotEquals($user1[0]['password'], $user2[count($user2)-1]['password']);
    }
    
    public function testUserVerifiesPassword(): void
    {
        $userModel = new User();
        $userId = $userModel->createUser([
            'name' => 'Verify User',
            'email' => 'verify_' . uniqid() . '@test.com',
            'password' => 'password123'
        ]);
        
        $user = $userModel->find($userId);
        
        $this->assertTrue($userModel->verifyPassword('password123', $user['password']));
        $this->assertFalse($userModel->verifyPassword('wrongpassword', $user['password']));
    }
    
    public function testUserFindByEmail(): void
    {
        $userModel = new User();
        $email = 'findby_' . uniqid() . '@test.com';
        $userModel->createUser([
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password123'
        ]);
        
        $user = $userModel->findByEmail($email);
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user['name']);
        
        $nonExistent = $userModel->findByEmail('nonexistent@test.com');
        $this->assertNull($nonExistent);
    }
    
    public function testUserUpdate(): void
    {
        $userModel = new User();
        $email = 'update_' . uniqid() . '@test.com';
        $userId = $userModel->createUser([
            'name' => 'Test User',
            'email' => $email,
            'password' => 'password123'
        ]);
        
        $userModel->update($userId, ['name' => 'Updated Name']);
        
        $user = $userModel->find($userId);
        $this->assertEquals('Updated Name', $user['name']);
    }
    
    public function testProductCreation(): void
    {
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'test-product-' . uniqid(),
            'price' => 29.99,
            'category_id' => 1,
            'stock' => 50
        ]);
        
        $this->assertGreaterThan(0, $productId);
        
        $product = $productModel->find($productId);
        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product['name']);
        $this->assertEquals(29.99, (float)$product['price']);
    }
    
    public function testProductFindBySlug(): void
    {
        $productModel = new Product();
        $slug = 'test-product-' . uniqid();
        $productModel->create([
            'name' => 'Test Product',
            'slug' => $slug,
            'price' => 29.99,
            'category_id' => 1
        ]);
        
        $product = $productModel->findBySlug($slug);
        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product['name']);
        
        $nonExistent = $productModel->findBySlug('non-existent');
        $this->assertNull($nonExistent);
    }
    
    public function testProductSearch(): void
    {
        $productModel = new Product();
        
        $productModel->create(['name' => 'Purple T-shirt', 'slug' => 'purple-t-' . uniqid(), 'price' => 29.99, 'category_id' => 1]);
        $productModel->create(['name' => 'Pink Hoodie', 'slug' => 'pink-h-' . uniqid(), 'price' => 49.99, 'category_id' => 1]);
        $productModel->create(['name' => 'Blue Jeans', 'slug' => 'blue-j-' . uniqid(), 'price' => 39.99, 'category_id' => 1]);
        
        $results = $productModel->search('Purple');
        $this->assertCount(1, $results['data']);
        $this->assertEquals('Purple T-shirt', $results['data'][0]['name']);
    }
    
    public function testProductStockManagement(): void
    {
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'stock-' . uniqid(),
            'price' => 29.99,
            'stock' => 100,
            'category_id' => 1
        ]);
        
        $productModel->decreaseStock($productId, 30);
        $product = $productModel->find($productId);
        $this->assertEquals(70, (int)$product['stock']);
        
        $productModel->increaseStock($productId, 20);
        $product = $productModel->find($productId);
        $this->assertEquals(90, (int)$product['stock']);
    }
    
    public function testCouponValidation(): void
    {
        $couponModel = new Coupon();
        $code = 'VALIDATE_' . uniqid();
        $couponModel->create([
            'code' => $code,
            'type' => 'percentage',
            'value' => 10.00,
            'min_order_amount' => 50.00,
            'is_active' => 1
        ]);
        
        $valid = $couponModel->validate($code, 100.00);
        $this->assertNotNull($valid);
        $this->assertEquals('percentage', $valid['type']);
        
        $invalid = $couponModel->validate('INVALID', 100.00);
        $this->assertNull($invalid);
        
        $belowMinimum = $couponModel->validate($code, 25.00);
        $this->assertNull($belowMinimum);
    }
    
    public function testCouponFindByCode(): void
    {
        $couponModel = new Coupon();
        $code = 'FIND_' . uniqid();
        $couponModel->create([
            'code' => $code,
            'type' => 'percentage',
            'value' => 10.00,
            'is_active' => 1
        ]);
        
        $coupon = $couponModel->findByCode($code);
        $this->assertNotNull($coupon);
        $this->assertEquals(strtoupper($code), $coupon['code']);
        
        $lowerCase = $couponModel->findByCode(strtolower($code));
        $this->assertNotNull($lowerCase);
        $this->assertEquals(strtoupper($code), $coupon['code']);
    }
    
    public function testCartItemOperations(): void
    {
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'cart-op-' . uniqid(),
            'price' => 29.99,
            'stock' => 100,
            'category_id' => 1
        ]);
        
        $cartModel = new Cart();
        
        // Test adding to cart
        $cartModel->add($productId, 2);
        $items = $cartModel->getItems();
        $this->assertCount(1, $items);
        $this->assertEquals(2, $items[0]['quantity']);
        
        // Test cart count
        $this->assertEquals(2, $cartModel->getCount());
        
        // Test subtotal
        $subtotal = $cartModel->getSubtotal();
        $this->assertEquals(59.98, round($subtotal, 2));
    }
    
    public function testCartItemRemoval(): void
    {
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'cart-rem-' . uniqid(),
            'price' => 29.99,
            'stock' => 100,
            'category_id' => 1
        ]);
        
        $cartModel = new Cart();
        $cartModel->add($productId, 2);
        
        $items = $cartModel->getItems();
        $itemId = $items[0]['id'];
        
        $cartModel->remove($itemId);
        $items = $cartModel->getItems();
        $this->assertCount(0, $items);
    }
    
    public function testCartQuantityUpdate(): void
    {
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'cart-qty-' . uniqid(),
            'price' => 29.99,
            'stock' => 100,
            'category_id' => 1
        ]);
        
        $cartModel = new Cart();
        $cartModel->add($productId, 2);
        
        $items = $cartModel->getItems();
        $itemId = $items[0]['id'];
        
        $cartModel->updateQuantity($itemId, 5);
        $items = $cartModel->getItems();
        $this->assertEquals(5, $items[0]['quantity']);
    }
    
    public function testCartClear(): void
    {
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'cart-clr-' . uniqid(),
            'price' => 29.99,
            'stock' => 100,
            'category_id' => 1
        ]);
        
        $cartModel = new Cart();
        $cartModel->add($productId, 2);
        
        $this->assertEquals(2, $cartModel->getCount());
        
        $cartModel->clear();
        $this->assertEquals(0, $cartModel->getCount());
    }
    
    public function testOrderCreation(): void
    {
        $userModel = new User();
        $userId = $userModel->createUser([
            'name' => 'Order User',
            'email' => 'order_' . uniqid() . '@test.com',
            'password' => 'password123'
        ]);
        
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'order-prd-' . uniqid(),
            'price' => 29.99,
            'stock' => 100,
            'category_id' => 1
        ]);
        
        $orderModel = new Order();
        $orderId = $orderModel->create([
            'user_id' => $userId,
            'status' => 'pending',
            'total' => 59.98,
            'subtotal' => 59.98,
            'shipping_name' => 'Test User',
            'shipping_email' => 'test@test.com',
            'shipping_address' => 'Test Address 123',
            'shipping_city' => 'Test City',
            'shipping_zip' => '12345'
        ]);
        
        $this->assertGreaterThan(0, $orderId);
        
        $order = $orderModel->find($orderId);
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order['status']);
    }
}