<?php

declare(strict_types=1);

namespace Nicastore\Core;

use Nicastore\Core\Database;

abstract class Model
{
    protected string $table;
    protected array $fillable = [];
    protected array $hidden = ['password', 'remember_token'];
    protected array $dates = ['created_at', 'updated_at'];
    
    public function __construct()
    {
        if (empty($this->table)) {
            $className = (new \ReflectionClass($this))->getShortName();
            $this->table = strtolower($className) . 's';
        }
    }
    
    public function find(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
    }
    
    public function findBy(string $column, mixed $value): ?array
    {
        $this->validateColumn($column);
        return Database::fetchOne("SELECT * FROM {$this->table} WHERE {$column} = ?", [$value]);
    }
    
    public function all(array $columns = ['*'], string $orderBy = 'id DESC'): array
    {
        $cols = implode(', ', $columns);
        $orderBy = $this->validateOrderBy($orderBy);
        return Database::fetchAll("SELECT {$cols} FROM {$this->table} ORDER BY {$orderBy}");
    }
    
    public function paginate(int $page = 1, int $perPage = 12, array $columns = ['*'], string $orderBy = 'id DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $cols = implode(', ', $columns);
        $orderBy = $this->validateOrderBy($orderBy);
        
        $total = (int)Database::fetchOne("SELECT COUNT(*) as count FROM {$this->table}")['count'];
        $data = Database::fetchAll(
            "SELECT {$cols} FROM {$this->table} ORDER BY {$orderBy} LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        
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
    
    public function create(array $data): int
    {
        $data = $this->filterFillable($data);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return Database::insert($this->table, $data);
    }
    
    public function update(int $id, array $data): int
    {
        $data = $this->filterFillable($data);
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        return Database::update($this->table, $data, 'id = ?', [$id]);
    }
    
    public function delete(int $id): int
    {
        return Database::delete($this->table, 'id = ?', [$id]);
    }
    
    public function where(string $column, mixed $value, string $operator = '='): array
    {
        $this->validateColumn($column);
        $this->validateOperator($operator);
        return Database::fetchAll(
            "SELECT * FROM {$this->table} WHERE {$column} {$operator} ?",
            [$value]
        );
    }
    
    public function whereIn(string $column, array $values): array
    {
        if (empty($values)) {
            return [];
        }
        $this->validateColumn($column);
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        return Database::fetchAll(
            "SELECT * FROM {$this->table} WHERE {$column} IN ({$placeholders})",
            $values
        );
    }
    
    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->fillable));
    }
    
    protected function hide(array $data): array
    {
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }
        return $data;
    }
    
    public function query(string $sql, array $params = []): \PDOStatement
    {
        return Database::query($sql, $params);
    }
    
    /**
     * Validate column name against allowed columns
     * Uses $fillable + common columns (id, created_at, updated_at)
     */
    protected function validateColumn(string $column): void
    {
        $allowedColumns = array_merge(
            $this->fillable,
            ['id', 'created_at', 'updated_at', 'email_verified_at', 'remember_token']
        );
        
        // Allow snake_case column names
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            throw new \InvalidArgumentException("Invalid column name: {$column}");
        }
        
        if (!in_array($column, $allowedColumns, true)) {
            throw new \InvalidArgumentException("Column not allowed: {$column}");
        }
    }
    
    /**
     * Validate SQL operator
     */
    protected function validateOperator(string $operator): void
    {
        $allowedOperators = ['=', '!=', '<>', '>', '<', '>=', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS', 'IS NOT'];
        
        if (!in_array(strtoupper($operator), $allowedOperators, true)) {
            throw new \InvalidArgumentException("Invalid operator: {$operator}");
        }
    }
    
    /**
     * Validate ORDER BY clause
     * Only allows column names with optional ASC/DESC
     */
    protected function validateOrderBy(string $orderBy): string
    {
        // Default fallback
        $default = 'id DESC';
        
        if (empty(trim($orderBy))) {
            return $default;
        }
        
        // Pattern: column [ASC|DESC] (case insensitive)
        // Allows: "column", "column ASC", "column DESC"
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*\s*(ASC|DESC)?$/i', $orderBy)) {
            return $default;
        }
        
        // Extract column name and validate it
        $parts = preg_split('/\s+/', trim($orderBy));
        $column = $parts[0];
        
        $allowedColumns = array_merge(
            $this->fillable,
            ['id', 'created_at', 'updated_at']
        );
        
        if (!in_array($column, $allowedColumns, true)) {
            return $default;
        }
        
        return strtoupper($orderBy);
    }
}