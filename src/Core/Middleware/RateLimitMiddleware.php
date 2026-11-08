<?php

declare(strict_types=1);

namespace Nicastore\Core\Middleware;

use Nicastore\Core\Session;

class RateLimitMiddleware
{
    private int $maxAttempts;
    private int $windowMinutes;
    private string $action;
    
    public function __construct(int $maxAttempts = 5, int $windowMinutes = 15, string $action = 'login')
    {
        $this->maxAttempts = $maxAttempts;
        $this->windowMinutes = $windowMinutes;
        $this->action = $action;
    }
    
    public function handle(): bool
    {
        $identifier = $this->getIdentifier();
        $key = "ratelimit_{$this->action}_{$identifier}";
        
        $attempts = Session::get($key, [
            'count' => 0,
            'window_start' => time()
        ]);
        
        $now = time();
        $windowStart = $attempts['window_start'];
        $windowEnd = $windowStart + ($this->windowMinutes * 60);
        
        // Reset window if expired
        if ($now > $windowEnd) {
            $attempts = ['count' => 0, 'window_start' => $now];
        }
        
        if ($attempts['count'] >= $this->maxAttempts) {
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
        
        $attempts['count']++;
        Session::set($key, $attempts);
        
        return true;
    }
    
    public static function recordFailure(string $action, string $identifier): void
    {
        $key = "ratelimit_{$action}_{$identifier}";
        $attempts = Session::get($key, ['count' => 0, 'window_start' => time()]);
        $attempts['count']++;
        Session::set($key, $attempts);
    }
    
    public static function clear(string $action, string $identifier): void
    {
        $key = "ratelimit_{$action}_{$identifier}";
        Session::remove($key);
    }
    
    public static function getIdentifier(): string
    {
        // Use IP + User Agent for more granular limiting
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 50);
        return hash('sha256', $ip . '|' . $ua);
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