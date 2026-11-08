<?php

declare(strict_types=1);

namespace Nicastore\Core;

use Nicastore\Config\AppConfig;
use Nicastore\Models\Setting;

abstract class Controller
{
    protected array $data = [];
    protected string $layout = 'main';
    
    public function __construct()
    {
        $this->data['app_name'] = AppConfig::get('APP_NAME', 'Nicastore');
        $this->data['csrf_token'] = $this->generateCsrfToken();
        $this->data['flash'] = Session::flash('flash');
        $this->data['user'] = Session::getUser();
        $this->data['settings'] = (new Setting())->getAll();
    }
    
    protected function view(string $view, array $data = [], string $layout = null): void
    {
        $this->data = array_merge($this->data, $data);
        $layout = $layout ?? $this->layout;
        
        extract($this->data);
        
        $viewPath = __DIR__ . "/../Views/{$view}.php";
        $layoutPath = __DIR__ . "/../Views/layouts/{$layout}.php";
        
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: {$viewPath}");
        }
        
        if ($layout && file_exists($layoutPath)) {
            ob_start();
            require $viewPath;
            $content = ob_get_clean();
            require $layoutPath;
        } else {
            require $viewPath;
        }
    }
    
    protected function redirect(string $url, int $statusCode = 302): void
    {
        // Ensure session data is written before redirect
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        
        // Force session cookie to be sent with new session ID
        $params = session_get_cookie_params();
        $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 120);
        $secure = filter_var($_ENV['SESSION_SECURE'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
        $httpOnly = filter_var($_ENV['SESSION_HTTP_ONLY'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
        $sameSite = $secure ? 'None' : 'Lax';
        
        $cookieString = sprintf(
            '%s=%s; expires=%s; path=%s; domain=%s; secure=%s; httponly=%s; samesite=%s',
            session_name(),
            session_id(),
            gmdate('D, d-M-Y H:i:s T', time() + $lifetime * 60),
            $params['path'],
            $params['domain'] ?: '',
            $secure ? 'true' : 'false',
            $httpOnly ? 'true' : 'false',
            $sameSite
        );
        
        // Debug
        error_log("REDIRECT DEBUG: session_id = " . session_id() . ", cookie = $cookieString");
        
        header("Set-Cookie: $cookieString", false);
        header("Location: {$url}", true, $statusCode);
        exit;
    }
    
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? AppConfig::url('/');
        $this->redirect($referer);
    }
    
    protected function validateCsrf(): bool
    {
        $tokenName = AppConfig::csrfTokenName();
        $token = $_POST[$tokenName] ?? $_GET[$tokenName] ?? '';
        $sessionToken = Session::get('csrf_token');
        
        return !empty($token) && hash_equals($sessionToken, $token);
    }
    
    private function generateCsrfToken(): string
    {
        $tokenName = AppConfig::csrfTokenName();
        if (!Session::has('csrf_token')) {
            Session::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return Session::get('csrf_token');
    }
    
    protected function json(mixed $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}