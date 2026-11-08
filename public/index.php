<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Nicastore\Config\AppConfig;
use Nicastore\Core\Session;
use Nicastore\Core\Router;
use Nicastore\Controllers\AuthController;
use Nicastore\Controllers\HomeController;
use Nicastore\Controllers\ProductController;
use Nicastore\Controllers\CartController;
use Nicastore\Controllers\CheckoutController;
use Nicastore\Controllers\AccountController;
use Nicastore\Controllers\AdminController;
use Nicastore\Controllers\CompareController;
use Nicastore\Core\Middleware\AuthMiddleware;
use Nicastore\Core\Middleware\AdminMiddleware;
use Nicastore\Core\Middleware\CsrfMiddleware;
use Nicastore\Core\Middleware\RateLimitMiddleware;
use Nicastore\Core\Middleware\SecureHeadersMiddleware;

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Initialize session
Session::init();

// Apply secure headers globally
SecureHeadersMiddleware::handle();

// Check remember me cookie
$authController = new AuthController();
$authController->checkRemember();

// Initialize router
$router = new Router();

// Public routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/productos', [ProductController::class, 'index']);
$router->get('/productos/page/{page}', [ProductController::class, 'index']);
$router->get('/producto/{slug}', [ProductController::class, 'show']);
$router->get('/categoria/{slug}', [ProductController::class, 'category']);
$router->get('/categoria/{slug}/page/{page}', [ProductController::class, 'category']);
$router->get('/buscar', [HomeController::class, 'search']);
$router->get('/carrito', [CartController::class, 'index']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->get('/register', [AuthController::class, 'showRegister']);

// Compare routes
$router->get('/comparar', [CompareController::class, 'index']);
$router->post('/comparar/añadir/{id}', [CompareController::class, 'add'], [CsrfMiddleware::class]);
$router->post('/comparar/eliminar/{id}', [CompareController::class, 'remove'], [CsrfMiddleware::class]);
$router->post('/comparar/limpiar', [CompareController::class, 'clear'], [CsrfMiddleware::class]);

// POST routes with CSRF protection
$router->post('/login', [AuthController::class, 'login'], [fn() => (new \Nicastore\Core\Middleware\RateLimitMiddleware(5, 15, 'login'))->handle()]);
$router->post('/register', [AuthController::class, 'register'], [fn() => (new \Nicastore\Core\Middleware\RateLimitMiddleware(3, 15, 'register'))->handle()]);
$router->post('/logout', [AuthController::class, 'logout']);
$router->post('/carrito/agregar', [CartController::class, 'add'], [CsrfMiddleware::class]);
$router->post('/carrito/actualizar/{itemId}', [CartController::class, 'update'], [CsrfMiddleware::class]);
$router->post('/carrito/eliminar/{itemId}', [CartController::class, 'remove'], [CsrfMiddleware::class]);
$router->post('/carrito/cupon', [CartController::class, 'applyCoupon'], [CsrfMiddleware::class]);
$router->post('/checkout/procesar', [CheckoutController::class, 'process'], [CsrfMiddleware::class, AuthMiddleware::class]);

// Account routes (require auth)
$router->get('/cuenta', [AccountController::class, 'index'], [AuthMiddleware::class]);
$router->get('/cuenta/pedidos', [AccountController::class, 'orders'], [AuthMiddleware::class]);
$router->get('/cuenta/pedido/{id}', [AccountController::class, 'orderDetail'], [AuthMiddleware::class]);
$router->get('/cuenta/perfil', [AccountController::class, 'profile'], [AuthMiddleware::class]);
$router->post('/cuenta/perfil/actualizar', [AccountController::class, 'updateProfile'], [CsrfMiddleware::class, AuthMiddleware::class]);

// Checkout routes
$router->get('/checkout', [CheckoutController::class, 'index'], [AuthMiddleware::class]);
$router->post('/checkout/paso1', [CheckoutController::class, 'step1'], [CsrfMiddleware::class, AuthMiddleware::class]);
$router->get('/checkout/paso2', [CheckoutController::class, 'step2'], [AuthMiddleware::class]);
$router->post('/checkout/paso2', [CheckoutController::class, 'step2'], [CsrfMiddleware::class, AuthMiddleware::class]);
$router->get('/checkout/confirmar', [CheckoutController::class, 'confirm'], [AuthMiddleware::class]);
$router->post('/checkout/confirmar', [CheckoutController::class, 'confirmPost'], [CsrfMiddleware::class, AuthMiddleware::class]);
$router->get('/pedido/{id}', [CheckoutController::class, 'success'], [AuthMiddleware::class]);

// Admin routes
$router->get('/admin', [AdminController::class, 'dashboard'], [AdminMiddleware::class]);
$router->get('/admin/productos', [AdminController::class, 'products'], [AdminMiddleware::class]);
$router->post('/admin/productos/bulk', [AdminController::class, 'bulkProducts'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/productos/nuevo', [AdminController::class, 'createProduct'], [AdminMiddleware::class]);
$router->post('/admin/productos/guardar', [AdminController::class, 'storeProduct'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/productos/editar/{id}', [AdminController::class, 'editProduct'], [AdminMiddleware::class]);
$router->post('/admin/productos/actualizar/{id}', [AdminController::class, 'updateProduct'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/productos/eliminar/{id}', [AdminController::class, 'deleteProduct'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/categorias', [AdminController::class, 'categories'], [AdminMiddleware::class]);
$router->post('/admin/categorias/guardar', [AdminController::class, 'storeCategory'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/categorias/actualizar/{id}', [AdminController::class, 'updateCategory'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/categorias/eliminar/{id}', [AdminController::class, 'deleteCategory'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/pedidos', [AdminController::class, 'orders'], [AdminMiddleware::class]);
$router->post('/admin/pedidos/bulk', [AdminController::class, 'bulkOrders'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/pedidos/{id}', [AdminController::class, 'orderDetail'], [AdminMiddleware::class]);
$router->post('/admin/pedidos/{id}/estado', [AdminController::class, 'updateOrderStatus'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->get('/admin/configuracion', [AdminController::class, 'settings'], [AdminMiddleware::class]);
$router->post('/admin/configuracion/guardar', [AdminController::class, 'updateSettings'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/configuracion/test-email', [AdminController::class, 'testEmail'], [CsrfMiddleware::class, AdminMiddleware::class]);

// User management
$router->get('/admin/usuarios', [AdminController::class, 'users'], [AdminMiddleware::class]);
$router->get('/admin/usuarios/{id}', [AdminController::class, 'userDetail'], [AdminMiddleware::class]);
$router->get('/admin/usuarios/editar/{id}', [AdminController::class, 'editUser'], [AdminMiddleware::class]);
$router->post('/admin/usuarios/actualizar/{id}', [AdminController::class, 'updateUser'], [CsrfMiddleware::class, AdminMiddleware::class]);
$router->post('/admin/usuarios/eliminar/{id}', [AdminController::class, 'deleteUser'], [CsrfMiddleware::class, AdminMiddleware::class]);

// Dispatch
$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

$router->dispatch($method, $uri);