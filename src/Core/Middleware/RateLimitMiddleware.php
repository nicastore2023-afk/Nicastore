<?php

declare(strict_types=1);

namespace Nicastore\Core\Middleware;

use Nicastore\Core\Session;
use Nicastore\Config\DatabaseConfig;

class RateLimitMiddleware
{
    private int $maxAttempts;
    private int $windowMinutes;
    private string $action;
    private \PDO $db;
    
    public function __construct(int $maxAttempts = 5, int $windowMinutes = 15, string $action = 'login')
    {
        $this->maxAttempts = $maxAttempts;
        $this->windowMinutes = $windowMinutes;
        $this->action = $action;
        $this->db = DatabaseConfig::getConnection();
    }
    
    public function handle(): bool
    {
        $identifier = $this->getIdentifier();
        $userId = Session::isLoggedIn() ? Session::getUserId() : null;
        
        $this->cleanExpired();
        
        $record = $this->getRecord($identifier, $userId);
        $now = time();
        $windowEnd = $record['window_start'] + ($this->windowMinutes * 60);
        
        // Reset window if expired
        if ($now > $windowEnd) {
            $record = $this->resetWindow($identifier, $userId, $now);
            $windowEnd = $now + ($this->windowMinutes * 60);
        }
        
        if ($record['count'] >= $this->maxAttempts) {
            $retryAfter = $windowEnd - $now;
            http_response_code(429);
            header('Content-Type: application/json');
            header("Retry-After: {$retryAfter}");
            echo json_encode([
                'success' => false,
                'message' => 'Demasiados intentos. Intenta de nuevo en ' . $this->formatTime($retryAfter),
                'retry_after' => $retryAfter
            ]);
            exit;
        }
        
        $this->incrementCount($identifier, $userId, $record['count'] + 1);
        
        // Also store in session for fast path (defense in depth)
        $key = "ratelimit_{$this->action}_{$identifier}";
        Session::set($key, ['count' => $record['count'] + 1, 'window_start' => $record['window_start']]);
        
        return true;
    }
    
    public static function recordFailure(string $action, string $identifier): void
    {
        $userId = Session::isLoggedIn() ? Session::getUserId() : null;
        $db = DatabaseConfig::getConnection();
        
        $stmt = $db->prepare("
            INSERT INTO rate_limits (action, identifier, user_id, count, window_start, expires_at, updated_at)
            VALUES (?, ?, ?, 1, ?, ?, datetime('now'))
            ON CONFLICT(action, identifier) DO UPDATE SET
                count = count + 1,
                expires_at = ?,
                updated_at = datetime('now')
        ");
        $now = time();
        $expiresAt = $now + (15 * 60); // default 15 min window
        $stmt->execute([$action, $identifier, $userId, $now, $expiresAt, $expiresAt]);
        
        // Also update session
        $key = "ratelimit_{$action}_{$identifier}";
        $attempts = Session::get($key, ['count' => 0, 'window_start' => time()]);
        $attempts['count']++;
        Session::set($key, $attempts);
    }
    
    public static function clear(string $action, string $identifier): void
    {
        $userId = Session::isLoggedIn() ? Session::getUserId() : null;
        $db = DatabaseConfig::getConnection();
        
        $stmt = $db->prepare("DELETE FROM rate_limits WHERE action = ? AND identifier = ?" . ($userId ? " AND user_id = ?" : ""));
        $params = [$action, $identifier];
        if ($userId) $params[] = $userId;
        $stmt->execute($params);
        
        $key = "ratelimit_{$action}_{$identifier}";
        Session::remove($key);
    }
    
    public static function getIdentifier(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 50);
        $hash = hash('sha256', $ip . '|' . $ua);
        
        if (Session::isLoggedIn()) {
            return 'user:' . Session::getUserId();
        }
        
        return 'anon:' . $hash;
    }
    
    private function getRecord(string $identifier, ?int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM rate_limits 
            WHERE action = ? AND identifier = ?" . ($userId ? " AND user_id = ?" : "") . "
            LIMIT 1
        ");
        $params = [$this->action, $identifier];
        if ($userId) $params[] = $userId;
        $stmt->execute($params);
        $record = $stmt->fetch();
        
        if (!$record) {
            return [
                'count' => 0,
                'window_start' => time(),
                'expires_at' => time() + ($this->windowMinutes * 60)
            ];
        }
        
        return $record;
    }
    
    private function resetWindow(string $identifier, ?int $userId, int $now): array
    {
        $expiresAt = $now + ($this->windowMinutes * 60);
        
        $stmt = $this->db->prepare("
            UPDATE rate_limits 
            SET count = 0, window_start = ?, expires_at = ?, updated_at = datetime('now')
            WHERE action = ? AND identifier = ?" . ($userId ? " AND user_id = ?" : "")
        );
        $params = [$now, $expiresAt, $this->action, $identifier];
        if ($userId) $params[] = $userId;
        $stmt->execute($params);
        
        return [
            'count' => 0,
            'window_start' => $now,
            'expires_at' => $expiresAt
        ];
    }
    
    private function incrementCount(string $identifier, ?int $userId, int $count): void
    {
        $expiresAt = time() + ($this->windowMinutes * 60);
        
        $stmt = $this->db->prepare("
            INSERT INTO rate_limits (action, identifier, user_id, count, window_start, expires_at, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))
            ON CONFLICT(action, identifier) DO UPDATE SET
                count = ?,
                expires_at = ?,
                updated_at = datetime('now')
        ");
        $params = [$this->action, $identifier, $userId, $count, time(), $expiresAt, $count, $expiresAt];
        $stmt->execute($params);
    }
    
    private function cleanExpired(): void
    {
        // Clean up expired records (older than 1 hour)
        $cutoff = time() - 3600;
        $stmt = $this->db->prepare("DELETE FROM rate_limits WHERE expires_at < ?");
        $stmt->execute([$cutoff]);
    }
    
    private function formatTime(int $seconds): string
    {
        if ($seconds < 60) {
            return "{$seconds} segundos";
        }
        $minutes = ceil($seconds / 60);
        return "{$minutes} minuto" . ($minutes > 1 ? 's' : '');
    }
}