<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\Product;
use Nicastore\Models\Category;
use Nicastore\Config\AppConfig;

class ProductController extends Controller
{
    private Product $productModel;
    private Category $categoryModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    
    public function index(int $page = 1): void
    {
        $perPage = AppConfig::productsPerPage();
        
        // Build filters from GET parameters
        $filters = [
            'category_id' => !empty($_GET['category']) ? (int)$_GET['category'] : null,
            'min_price' => !empty($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price' => !empty($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'in_stock' => !empty($_GET['in_stock']) ? true : null,
            'on_sale' => !empty($_GET['on_sale']) ? true : null,
            'featured' => !empty($_GET['featured']) ? true : null,
            'sort' => $this->getSortOrder($_GET['sort'] ?? ''),
        ];
        
        // Remove null filters
        $filters = array_filter($filters, fn($v) => $v !== null);
        
        $result = $this->productModel->getActive($page, $perPage, $filters);
        $categories = $this->categoryModel->getActive();
        $priceRange = $this->productModel->getMinMaxPrice();
        
        $this->view('products/index', [
            'title' => 'Todos los productos',
            'products' => $result['data'],
            'pagination' => $result,
            'categories' => $categories,
            'filters' => $filters,
            'priceRange' => $priceRange,
            'currentCategory' => $filters['category_id'] ?? null,
        ]);
    }
    
    private function getSortOrder(string $sort): string
    {
        $sortOptions = [
            'price_asc' => 'COALESCE(sale_price, price) ASC',
            'price_desc' => 'COALESCE(sale_price, price) DESC',
            'name_asc' => 'name ASC',
            'name_desc' => 'name DESC',
            'newest' => 'created_at DESC',
            'popular' => 'is_featured DESC, id DESC',
        ];
        
        return $sortOptions[$sort] ?? 'id DESC';
    }
    
    public function show(string $slug): void
    {
        $product = $this->productModel->query("
            SELECT p.*, c.name as category_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.slug = ?
        ", [$slug])->fetch();
        
        if (!$product) {
            $this->redirect(AppConfig::url('/productos'));
        }
        
        // Track recently viewed
        $this->trackRecentlyViewed($product['id']);
        
        $relatedProducts = $this->productModel->getRelated($product['id'], $product['category_id'], 4);
        $categories = $this->categoryModel->getActive();
        
        $this->view('products/show', [
            'title' => $product['name'],
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'categories' => $categories,
            'recentlyViewed' => $this->getRecentlyViewed($product['id']),
        ]);
    }
    
    private function trackRecentlyViewed(int $productId): void
    {
        $recent = json_decode($_COOKIE['recently_viewed'] ?? '[]', true);
        if (!is_array($recent)) $recent = [];
        
        // Remove if already exists
        $recent = array_values(array_filter($recent, fn($id) => $id !== $productId));
        
        // Add to front
        array_unshift($recent, $productId);
        
        // Keep only last 10
        $recent = array_slice($recent, 0, 10);
        
        setcookie('recently_viewed', json_encode($recent), time() + 86400 * 30, '/', '', false, true);
    }
    
    private function getRecentlyViewed(int $excludeId = 0): array
    {
        $recent = json_decode($_COOKIE['recently_viewed'] ?? '[]', true);
        if (!is_array($recent)) return [];
        
        $recent = array_values(array_filter($recent, fn($id) => $id !== $excludeId));
        $recent = array_slice($recent, 0, 4);
        
        if (empty($recent)) return [];
        
        return $this->productModel->getByIds($recent);
    }
    
    public function category(string $slug, int $page = 1): void
    {
        $category = $this->categoryModel->findBySlug($slug);
        
        if (!$category) {
            $this->redirect(AppConfig::url('/productos'));
        }
        
        $perPage = AppConfig::productsPerPage();
        
        // Build filters for category
        $filters = [
            'category_id' => $category['id'],
            'min_price' => !empty($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price' => !empty($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'in_stock' => !empty($_GET['in_stock']) ? true : null,
            'on_sale' => !empty($_GET['on_sale']) ? true : null,
            'sort' => $this->getSortOrder($_GET['sort'] ?? ''),
        ];
        
        $filters = array_filter($filters, fn($v) => $v !== null);
        
        $result = $this->productModel->getActive($page, $perPage, $filters);
        $categories = $this->categoryModel->getActive();
        $priceRange = $this->productModel->getMinMaxPrice();
        
        $this->view('products/category', [
            'title' => $category['name'],
            'category' => $category,
            'products' => $result['data'],
            'pagination' => $result,
            'categories' => $categories,
            'filters' => $filters,
            'priceRange' => $priceRange,
        ]);
    }
}