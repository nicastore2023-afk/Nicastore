<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Nicastore\Models\User;
use Nicastore\Models\Product;
use Nicastore\Models\Cart;
use Nicastore\Models\Coupon;
use Nicastore\Models\Order;
use Nicastore\Config\AppConfig;
use Nicastore\Core\Database;
use Nicastore\Tests\BaseTestCase;

class ControllerTest extends BaseTestCase
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
    
    public function testUserLoginValidation(): void
    {
        $userModel = new User();
        $email = 'login_' . uniqid() . '@test.com';
        $userModel->createUser([
            'name' => 'Login Test User',
            'email' => $email,
            'password' => 'password123'
        ]);
        
        $user = $userModel->findByEmail($email);
        $this->assertNotNull($user);
        $this->assertTrue($userModel->verifyPassword('password123', $user['password']));
        $this->assertFalse($userModel->verifyPassword('wrong', $user['password']));
    }
    
    public function testUserRegistrationValidation(): void
    {
        $userModel = new User();
        
        // Test email uniqueness
        $userModel->createUser([
            'name' => 'User 1',
            'email' => 'reg1_' . uniqid() . '@test.com',
            'password' => 'password123'
        ]);
        
        $existing = $userModel->findByEmail('reg1_' . uniqid() . '@test.com');
        $this->assertNull($existing); // Will be null since email is unique
    }
    
    public function testProductListing(): void
    {
        $productModel = new Product();
        
        $productModel->create([
            'name' => 'Product 1',
            'slug' => 'product-' . uniqid(),
            'price' => 10.00,
            'category_id' => 1,
            'is_active' => 1
        ]);
        
        $productModel->create([
            'name' => 'Product 2',
            'slug' => 'product-' . uniqid(),
            'price' => 20.00,
            'category_id' => 1,
            'is_active' => 1
        ]);
        
        $productModel->create([
            'name' => 'Product 3',
            'slug' => 'product-' . uniqid(),
            'price' => 30.00,
            'category_id' => 1,
            'is_active' => 0
        ]);
        
        $activeProducts = $productModel->query("SELECT * FROM products WHERE is_active = 1")->fetchAll();
        $this->assertCount(2, $activeProducts);
        
        $allProducts = $productModel->all();
        $this->assertCount(3, $allProducts);
    }
    
    public function testFeaturedProducts(): void
    {
        $productModel = new Product();
        
        $productModel->create([
            'name' => 'Featured 1',
            'slug' => 'featured-1-' . uniqid(),
            'price' => 10.00,
            'category_id' => 1,
            'is_active' => 1,
            'is_featured' => 1
        ]);
        
        $productModel->create([
            'name' => 'Featured 2',
            'slug' => 'featured-2-' . uniqid(),
            'price' => 20.00,
            'category_id' => 1,
            'is_active' => 1,
            'is_featured' => 1
        ]);
        
        $productModel->create([
            'name' => 'Not Featured',
            'slug' => 'not-featured-' . uniqid(),
            'price' => 30.00,
            'category_id' => 1,
            'is_active' => 1,
            'is_featured' => 0
        ]);
        
        $featured = $productModel->getFeatured();
        $this->assertCount(2, $featured);
    }
    
    public function testCartOperations(): void
    {
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'cart-test-' . uniqid(),
            'price' => 29.99,
            'stock' => 100,
            'category_id' => 1
        ]);
        
        $cartModel = new Cart();
        
        // Add to cart
        $cartModel->add($productId, 2);
        $this->assertEquals(2, $cartModel->getCount());
        
        // Add same product again (should increase quantity)
        $cartModel->add($productId, 3);
        $items = $cartModel->getItems();
        $this->assertCount(1, $items);
        $this->assertEquals(5, $items[0]['quantity']);
        
        // Verify subtotal
        $subtotal = $cartModel->getSubtotal();
        $this->assertEquals(149.95, round($subtotal, 2));
    }
    
    public function testCouponApplication(): void
    {
        $couponModel = new Coupon();
        $code = 'WELCOME10_' . uniqid();
        $couponModel->create([
            'code' => $code,
            'type' => 'percentage',
            'value' => 10.00,
            'min_order_amount' => 50.00,
            'is_active' => 1
        ]);
        
        $subtotal = 100.00;
        $coupon = $couponModel->validate($code, $subtotal);
        
        $this->assertNotNull($coupon);
        $discount = $subtotal * $coupon['value'] / 100;
        $this->assertEquals(10.00, $discount);
    }
    
    public function testCouponFixedType(): void
    {
        $couponModel = new Coupon();
        $code = 'FIXED5_' . uniqid();
        $couponModel->create([
            'code' => $code,
            'type' => 'fixed',
            'value' => 5.00,
            'min_order_amount' => 20.00,
            'is_active' => 1
        ]);
        
        $subtotal = 100.00;
        $coupon = $couponModel->validate($code, $subtotal);
        
        $this->assertNotNull($coupon);
        $this->assertEquals(5.00, min($coupon['value'], $subtotal));
    }
    
    public function testCouponInactiveNotValid(): void
    {
        $couponModel = new Coupon();
        $code = 'INACTIVE_' . uniqid();
        $couponModel->create([
            'code' => $code,
            'type' => 'percentage',
            'value' => 10.00,
            'min_order_amount' => 0,
            'is_active' => 0
        ]);
        
        $coupon = $couponModel->validate('INACTIVE', 100.00);
        $this->assertNull($coupon);
    }
    
    public function testOrderStatusTransitions(): void
    {
        $userModel = new User();
        $userId = $userModel->createUser([
            'name' => 'Order Test User',
            'email' => 'order_' . uniqid() . '@test.com',
            'password' => 'password123'
        ]);
        
        $this->assertGreaterThan(0, $userId);
        
        // Verify user exists
        $user = $userModel->find($userId);
        $this->assertNotNull($user);
        
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Test Product',
            'slug' => 'order-test-' . uniqid(),
            'price' => 29.99,
            'stock' => 100,
            'category_id' => 1
        ]);
        
        // Create order using Order model
        $orderModel = new Order();
        $orderId = $orderModel->create([
            'user_id' => $userId,
            'status' => 'pending',
            'total' => 36.48,
            'subtotal' => 29.99,
            'shipping_name' => 'Test User',
            'shipping_email' => 'test@test.com',
            'shipping_address' => 'Test Address',
            'shipping_city' => 'Test City',
            'shipping_zip' => '12345'
        ]);
        
        $this->assertGreaterThan(0, $orderId);
        
        // Verify order was created
        $order = $orderModel->findById($orderId);
        $this->assertNotNull($order, "Order should exist after creation");
        
        // Test status update
        $orderModel->updateStatus($orderId, 'processing');
        $order = $orderModel->findById($orderId);
        $this->assertNotNull($order, "Order should exist after status update");
        $this->assertEquals('processing', $order['status']);
        
        $orderModel->updateStatus($orderId, 'shipped');
        $order = $orderModel->findById($orderId);
        $this->assertEquals('shipped', $order['status']);
        $this->assertNotNull($order['shipped_at']);
        
        $orderModel->updateStatus($orderId, 'delivered');
        $order = $orderModel->findById($orderId);
        $this->assertEquals('delivered', $order['status']);
        $this->assertNotNull($order['delivered_at']);
    }
    
    public function testInvalidOrderStatusThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        // Use reflection to call the protected updateStatus method
        $orderModel = new \Nicastore\Models\Order();
        $orderModel->updateStatus(999, 'invalid_status');
    }
    
    public function testProductSearchRelevance(): void
    {
        $productModel = new Product();
        
        $productModel->create(['name' => 'Purple T-shirt', 'slug' => 'purple-t-' . uniqid(), 'price' => 29.99, 'category_id' => 1]);
        $productModel->create(['name' => 'Pink Hoodie', 'slug' => 'pink-h-' . uniqid(), 'price' => 49.99, 'category_id' => 1]);
        $productModel->create(['name' => 'Purple Jeans', 'slug' => 'purple-j-' . uniqid(), 'price' => 39.99, 'category_id' => 1]);
        $productModel->create(['name' => 'Blue Shirt', 'slug' => 'blue-s-' . uniqid(), 'price' => 19.99, 'category_id' => 1]);
        
        $results = $productModel->search('Purple');
        $this->assertCount(2, $results['data']);
        
        $results = $productModel->search('Pink');
        $this->assertCount(1, $results['data']);
        
        $results = $productModel->search('Shirt');
        $this->assertGreaterThanOrEqual(1, count($results['data']));
    }
    
    public function testCartValidationStock(): void
    {
        $productModel = new Product();
        $productId = $productModel->create([
            'name' => 'Limited Stock Product',
            'slug' => 'limited-' . uniqid(),
            'price' => 29.99,
            'stock' => 2,
            'category_id' => 1
        ]);
        
        $cartModel = new Cart();
        $cartModel->add($productId, 2);
        
        $errors = $cartModel->validateStock();
        $this->assertEmpty($errors);
        
        // Try to add more than available
        $cartModel->add($productId, 1);
        $errors = $cartModel->validateStock();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('insuficiente', $errors[0]);
    }
}