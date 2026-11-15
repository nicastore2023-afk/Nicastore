<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;

class Product extends Model
{
    protected string $table = 'products';
    protected array $fillable = [
        'category_id', 'name', 'slug', 'description', 'short_description',
        'price', 'sale_price', 'sku', 'stock', 'image', 'images',
        'is_active', 'is_featured'
    ];
    
    public function getActive(int $page = 1, int $perPage = 12, array $filters = []): array
    {
        $where = ['is_active = 1'];
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $where[] = 'category_id = ?';
            $params[] = $filters['category_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $where[] = '(COALESCE(sale_price, price)) >= ?';
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $where[] = '(COALESCE(sale_price, price)) <= ?';
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['in_stock'])) {
            $where[] = 'stock > 0';
        }
        
        if (!empty($filters['on_sale'])) {
            $where[] = 'sale_price IS NOT NULL AND sale_price < price';
        }
        
        if (!empty($filters['featured'])) {
            $where[] = 'is_featured = 1';
        }
        
        $whereClause = implode(' AND ', $where);
        
        // Validate sort parameter against whitelist
        $sortWhitelist = [
            'id DESC' => 'id DESC',
            'id ASC' => 'id ASC',
            'name ASC' => 'name ASC',
            'name DESC' => 'name DESC',
            'price_asc' => 'COALESCE(sale_price, price) ASC',
            'price_desc' => 'COALESCE(sale_price, price) DESC',
            'newest' => 'created_at DESC',
            'oldest' => 'created_at ASC',
            'popular' => 'is_featured DESC, id DESC',
        ];
        
        $sortKey = $filters['sort'] ?? 'id DESC';
        $orderBy = $sortWhitelist[$sortKey] ?? 'id DESC';
        
        $offset = ($page - 1) * $perPage;
        $cols = '*';
        
        $total = (int)$this->query(
            "SELECT COUNT(*) as count FROM {$this->table} WHERE {$whereClause}",
            $params
        )->fetch()['count'];
        
        $data = $this->query(
            "SELECT {$cols} FROM {$this->table} WHERE {$whereClause} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        )->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    public function getFeatured(int $limit = 8): array
    {
        return $this->query(
            "SELECT * FROM products WHERE is_active = 1 AND is_featured = 1 ORDER BY id DESC LIMIT ?",
            [$limit]
        )->fetchAll();
    }
    
    public function findBySlug(string $slug): ?array
    {
        return $this->findBy('slug', $slug);
    }
    
    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        return $this->query(
            "SELECT * FROM products WHERE category_id = ? AND id != ? AND is_active = 1 ORDER BY RANDOM() LIMIT ?",
            [$categoryId, $productId, $limit]
        )->fetchAll();
    }
    
    public function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        $words = array_filter(explode(' ', $query));
        
        if (empty($words)) {
            return [
                'data' => [],
                'total' => 0,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => 0,
                'from' => 0,
                'to' => 0
            ];
        }
        
        // Build relevance scoring and where conditions
        $relevanceCases = [];
        $whereConditions = [];
        $params = [];
        
        foreach ($words as $word) {
            $term = "%{$word}%";
            
            // Relevance scoring: name=3, short_description=2, description=1
            $relevanceCases[] = "(CASE WHEN name LIKE ? THEN 3 ELSE 0 END) + (CASE WHEN short_description LIKE ? THEN 2 ELSE 0 END) + (CASE WHEN description LIKE ? THEN 1 ELSE 0 END)";
            
            // Where condition
            $whereConditions[] = '(name LIKE ? OR description LIKE ? OR short_description LIKE ?)';
            
            // Add params for relevance (3) + where (3) = 6 per word
            $params[] = $term; // name for relevance
            $params[] = $term; // short_description for relevance
            $params[] = $term; // description for relevance
            $params[] = $term; // name for where
            $params[] = $term; // description for where
            $params[] = $term; // short_description for where
        }
        
        $relevanceSql = implode(' + ', $relevanceCases);
        $whereSql = implode(' AND ', $whereConditions);
        
        // Count total
        $countParams = [];
        foreach ($words as $word) {
            $term = "%{$word}%";
            $countParams[] = $term;
            $countParams[] = $term;
            $countParams[] = $term;
        }
        
        $total = (int)$this->query(
            "SELECT COUNT(*) as count FROM products WHERE is_active = 1 AND ({$whereSql})",
            $countParams
        )->fetch()['count'];
        
        // Search with relevance
        $params = array_merge($params, [$perPage, $offset]);
        $data = $this->query(
            "SELECT *, ({$relevanceSql}) as relevance 
             FROM products 
             WHERE is_active = 1 
             AND ({$whereSql})
             ORDER BY relevance DESC, is_featured DESC, id DESC 
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
        
        return [
            'data' => $data,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => (int)ceil($total / $perPage),
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ];
    }
    
    public function getByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        return $this->whereIn('id', $ids);
    }
    
    public function decreaseStock(int $productId, int $quantity): int
    {
        return $this->query(
            "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?",
            [$quantity, $productId, $quantity]
        )->rowCount();
    }
    
    public function increaseStock(int $productId, int $quantity): int
    {
        return $this->query(
            "UPDATE products SET stock = stock + ? WHERE id = ?",
            [$quantity, $productId]
        )->rowCount();
    }
    
    public function getPrice(float $price, ?float $salePrice = null): float
    {
        return $salePrice !== null && $salePrice < $price ? $salePrice : $price;
    }
    
    public function formatPrice(float $price): string
    {
        $symbol = AppConfig::currencySymbol();
        $locale = AppConfig::locale();
        
        return number_format($price, 2, ',', '.') . ' ' . $symbol;
    }
    
    public function getMinMaxPrice(): array
    {
        $result = $this->query(
            "SELECT MIN(COALESCE(sale_price, price)) as min_price, MAX(COALESCE(sale_price, price)) as max_price 
             FROM products WHERE is_active = 1"
        )->fetch();
        
        return [
            'min' => (float)($result['min_price'] ?? 0),
            'max' => (float)($result['max_price'] ?? 0)
        ];
    }
}