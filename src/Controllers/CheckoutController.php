<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\Cart;
use Nicastore\Models\Order;
use Nicastore\Core\Session;
use Nicastore\Config\AppConfig;

class CheckoutController extends Controller
{
    private Cart $cartModel;
    private Order $orderModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        
        if (!Session::isLoggedIn()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Debes iniciar sesión para finalizar la compra']);
            $this->redirect(AppConfig::url('/login'));
        }
    }
    
    public function index(): void
    {
        $items = $this->cartModel->getItems();
        
        if (empty($items)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Tu carrito está vacío']);
            $this->redirect(AppConfig::url('/carrito'));
        }
        
        $errors = $this->cartModel->validateStock();
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->redirect(AppConfig::url('/carrito'));
        }
        
        $subtotal = $this->cartModel->getSubtotal();
        $coupon = Session::get('applied_coupon');
        $discount = $coupon['discount'] ?? 0;
        
        $settings = new \Nicastore\Models\Setting();
        $shippingCost = (float)$settings->get('shipping_cost', 5.00);
        $freeShippingThreshold = (float)$settings->get('free_shipping_threshold', 50);
        $taxRate = (float)$settings->get('tax_rate', 15);
        
        $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCost;
        $tax = $subtotal * $taxRate / 100;
        $total = $subtotal + $shipping + $tax - $discount;
        
        $user = Session::getUser();
        
        $this->view('checkout/index', [
            'title' => 'Finalizar compra',
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'discount' => $discount,
            'coupon' => $coupon,
            'total' => $total,
            'user' => $user,
            'step' => 1
        ]);
    }
    
    public function step1(): void
    {
        $items = $this->cartModel->getItems();
        
        if (empty($items)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Tu carrito está vacío']);
            $this->redirect(AppConfig::url('/carrito'));
        }
        
        $errors = $this->cartModel->validateStock();
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->redirect(AppConfig::url('/carrito'));
        }
        
        $data = [
            'shipping_name' => trim($_POST['shipping_name'] ?? ''),
            'shipping_email' => trim($_POST['shipping_email'] ?? ''),
            'shipping_phone' => trim($_POST['shipping_phone'] ?? ''),
            'shipping_address' => trim($_POST['shipping_address'] ?? ''),
            'shipping_city' => trim($_POST['shipping_city'] ?? ''),
            'shipping_state' => trim($_POST['shipping_state'] ?? ''),
            'shipping_zip' => trim($_POST['shipping_zip'] ?? ''),
            'shipping_country' => trim($_POST['shipping_country'] ?? 'Nicaragua'),
            'notes' => trim($_POST['notes'] ?? ''),
        ];
        
        $validationErrors = [];
        
        if (empty($data['shipping_name'])) {
            $validationErrors[] = 'El nombre es requerido';
        }
        
        if (empty($data['shipping_email']) || !filter_var($data['shipping_email'], FILTER_VALIDATE_EMAIL)) {
            $validationErrors[] = 'Email inválido';
        }
        
        if (empty($data['shipping_address'])) {
            $validationErrors[] = 'La dirección es requerida';
        }
        
        if (empty($data['shipping_city'])) {
            $validationErrors[] = 'La ciudad es requerida';
        }
        
        if (empty($data['shipping_zip'])) {
            $validationErrors[] = 'El código postal es requerido';
        }
        
        if (!empty($validationErrors)) {
            $subtotal = $this->cartModel->getSubtotal();
            $coupon = Session::get('applied_coupon');
            $discount = $coupon['discount'] ?? 0;
            
            $settings = new \Nicastore\Models\Setting();
            $shippingCost = (float)$settings->get('shipping_cost', 5.00);
            $freeShippingThreshold = (float)$settings->get('free_shipping_threshold', 50);
            $taxRate = (float)$settings->get('tax_rate', 15);
            
            $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCost;
            $tax = $subtotal * $taxRate / 100;
            $total = $subtotal + $shipping + $tax - $discount;
            
            $user = Session::getUser();
            
            $this->view('checkout/index', [
                'title' => 'Finalizar compra',
                'items' => $this->cartModel->getItems(),
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'discount' => $discount,
                'coupon' => Session::get('applied_coupon'),
                'total' => $total,
                'user' => $user,
                'step' => 1,
                'errors' => $validationErrors,
                'formData' => $data
            ]);
            return;
        }
        
        // Store shipping data in session
        Session::set('checkout_shipping', $data);
        
        // Redirect to step 2
        $this->redirect(AppConfig::url('/checkout/paso2'));
    }
    
    public function step2(): void
    {
        $shippingData = Session::get('checkout_shipping');
        
        if (!$shippingData) {
            $this->redirect(AppConfig::url('/checkout'));
        }
        
        $items = $this->cartModel->getItems();
        
        if (empty($items)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Tu carrito está vacío']);
            $this->redirect(AppConfig::url('/carrito'));
        }
        
        $subtotal = $this->cartModel->getSubtotal();
        $coupon = Session::get('applied_coupon');
        $discount = $coupon['discount'] ?? 0;
        
        $settings = new \Nicastore\Models\Setting();
        $shippingCost = (float)$settings->get('shipping_cost', 5.00);
        $freeShippingThreshold = (float)$settings->get('free_shipping_threshold', 50);
        $taxRate = (float)$settings->get('tax_rate', 15);
        
        $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCost;
        $tax = $subtotal * $taxRate / 100;
        $total = $subtotal + $shipping + $tax - $discount;
        
        $user = Session::getUser();
        
        // Handle POST (payment method selection)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentMethod = $_POST['payment_method'] ?? '';
            
            if (empty($paymentMethod)) {
                $validationErrors[] = 'Debe seleccionar un método de pago';
            } else {
                Session::set('checkout_payment', ['method' => $paymentMethod]);
                $this->redirect(AppConfig::url('/checkout/confirmar'));
                return;
            }
        }
        
        $this->view('checkout/step2', [
            'title' => 'Método de Pago',
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'discount' => $discount,
            'coupon' => Session::get('applied_coupon'),
            'total' => $total,
            'user' => $user,
            'shippingData' => $shippingData,
            'step' => 2
        ]);
    }
    
    public function confirm(): void
    {
        $shippingData = Session::get('checkout_shipping');
        $paymentData = Session::get('checkout_payment');
        
        if (!$shippingData || !$paymentData) {
            $this->redirect(AppConfig::url('/checkout'));
        }
        
        $items = $this->cartModel->getItems();
        
        if (empty($items)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Tu carrito está vacío']);
            $this->redirect(AppConfig::url('/carrito'));
        }
        
        $subtotal = $this->cartModel->getSubtotal();
        $coupon = Session::get('applied_coupon');
        $discount = $coupon['discount'] ?? 0;
        
        $settings = new \Nicastore\Models\Setting();
        $shippingCost = (float)$settings->get('shipping_cost', 5.00);
        $freeShippingThreshold = (float)$settings->get('free_shipping_threshold', 50);
        $taxRate = (float)$settings->get('tax_rate', 15);
        
        $shipping = $subtotal >= $freeShippingThreshold ? 0 : $shippingCost;
        $tax = $subtotal * $taxRate / 100;
        $total = $subtotal + $shipping + $tax - $discount;
        
        $this->view('checkout/confirm', [
            'title' => 'Confirmar Pedido',
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'tax' => $tax,
            'discount' => $discount,
            'coupon' => $coupon,
            'total' => $total,
            'shippingData' => $shippingData,
            'paymentData' => Session::get('checkout_payment')
        ]);
    }
    
    public function confirmPost(): void
    {
        $shippingData = Session::get('checkout_shipping');
        $paymentData = Session::get('checkout_payment');
        
        if (!$shippingData || !$paymentData) {
            $this->redirect(AppConfig::url('/checkout'));
        }
        
        $items = $this->cartModel->getItems();
        
        if (empty($items)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Tu carrito está vacío']);
            $this->redirect(AppConfig::url('/carrito'));
        }
        
        $errors = $this->cartModel->validateStock();
        if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->redirect(AppConfig::url('/carrito'));
        }
        
        // Create order with all data
        $orderData = array_merge($shippingData, $paymentData, [
            'coupon_code' => Session::get('applied_coupon')['code'] ?? null
        ]);
        
        try {
            $orderId = $this->orderModel->createFromCart($orderData);
            
            Session::remove('applied_coupon');
            Session::remove('checkout_shipping');
            Session::remove('checkout_payment');
            
            Session::flash('flash', ['type' => 'success', 'message' => '¡Pedido realizado con éxito! Número de pedido: #' . $orderId]);
            $this->redirect(AppConfig::url('/pedido/' . $orderId));
        } catch (\Exception $e) {
            Session::flash('flash', ['type' => 'error', 'message' => $e->getMessage()]);
            $this->back();
        }
    }
    
    public function success(int $id): void
    {
        $order = $this->orderModel->findById($id);
        
        if (!$order || $order['user_id'] != Session::getUserId()) {
            $this->redirect(AppConfig::url('/'));
        }
        
        $items = $this->orderModel->getItems($id);
        
        $this->view('checkout/success', [
            'title' => 'Pedido confirmado',
            'order' => $order,
            'items' => $items
        ]);
    }
}