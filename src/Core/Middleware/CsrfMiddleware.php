<?php

declare(strict_types=1);

namespace Nicastore\Core\Middleware;

use Nicastore\Core\Session;
use Nicastore\Config\AppConfig;

class CsrfMiddleware
{
    public static function handle(): bool
    {
        $tokenName = AppConfig::csrfTokenName();
        $token = $_POST[$tokenName] ?? $_GET[$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = Session::get('csrf_token');
        
        if (empty($token) || !hash_equals($sessionToken, $token)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Token CSRF inválido'
            ]);
            exit;
        }
        return true;
    }
}