<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\Product;
use Nicastore\Config\AppConfig;

class CompareController extends Controller
{
    private Product $productModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
    }
    
    public function index(): void
    {
        $ids = [];
        $compareCookie = $_COOKIE['compare_products'] ?? '';
        
        if ($compareCookie) {
            $ids = array_filter(explode(',', $compareCookie), fn($id) => is_numeric($id));
            $ids = array_slice($ids, 0, 3); // Max 3 products
        }
        
        $products = [];
        if (!empty($ids)) {
            $products = $this->productModel->getByIds($ids);
        }
        
        $this->view('products/compare', [
            'title' => 'Comparar Productos',
            'products' => $products,
            'compareIds' => $ids,
        ]);
    }
    
    public function add(int $id): void
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }
        
        $ids = [];
        $compareCookie = $_COOKIE['compare_products'] ?? '';
        
        if ($compareCookie) {
            $ids = array_filter(explode(',', $compareCookie), fn($id) => is_numeric($id));
        }
        
        // Remove if already exists
        $ids = array_values(array_filter($ids, fn($compareId) => (int)$compareId !== $id));
        
        // Add to front
        array_unshift($ids, $id);
        
        // Keep only last 3
        $ids = array_slice($ids, 0, 3);
        
        setcookie('compare_products', implode(',', $ids), time() + 86400 * 30, '/', '', false, true);
        
        $this->json([
            'success' => true,
            'message' => 'Producto añadido a comparación',
            'count' => count($ids)
        ]);
    }
    
    public function remove(int $id): void
    {
        $ids = [];
        $compareCookie = $_COOKIE['compare_products'] ?? '';
        
        if ($compareCookie) {
            $ids = array_filter(explode(',', $compareCookie), fn($id) => is_numeric($id));
        }
        
        $ids = array_values(array_filter($ids, fn($compareId) => (int)$compareId !== $id));
        
        setcookie('compare_products', implode(',', $ids), time() + 86400 * 30, '/', '', false, true);
        
        $this->json([
            'success' => true,
            'message' => 'Producto eliminado de comparación',
            'count' => count($ids)
        ]);
    }
    
    public function clear(): void
    {
        setcookie('compare_products', '', time() - 3600, '/', '', false, true);
        
        $this->json([
            'success' => true,
            'message' => 'Comparación limpiada',
            'count' => 0
        ]);
    }
}