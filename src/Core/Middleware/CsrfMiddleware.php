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
        
        // Only accept token from POST body or X-CSRF-TOKEN header
        // Do NOT accept from GET query string for security
        $token = $_POST[$tokenName] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        $sessionToken = Session::get('csrf_token');
        
        if (empty($token) || !hash_equals($sessionToken, $token)) {
            // Check if AJAX request
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            $acceptsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
            
            http_response_code(403);
            
            if ($isAjax || $acceptsJson) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Token CSRF inválido o expirado. Recarga la página e inténtalo de nuevo.'
                ]);
            } else {
                // For regular form submissions, show error page
                echo '<!DOCTYPE html>
<html lang="es"><head><meta charset="UTF-8"><title>Error CSRF</title>
<style>body{font-family:sans-serif;max-width:600px;margin:3rem auto;padding:1rem;text-align:center}
.error{color:#dc2626}.btn{display:inline-block;margin-top:1rem;padding:0.75rem 1.5rem;background:#2563eb;color:#fff;text-decoration:none;border-radius:4px}</style></head>
<body><h1 class="error">⛔ Token CSRF inválido</h1>
<p>El token de seguridad ha expirado o es inválido. Esto puede ocurrir si la sesión expiró o la página estuvo abierta mucho tiempo.</p>
<a href="javascript:history.back()" class="btn">Volver atrás</a>
<a href="' . AppConfig::url('/') . '" class="btn" style="background:#6b7280">Ir al inicio</a></body></html>';
            }
            exit;
        }
        return true;
    }
    
    /**
     * Get current CSRF token for use in JavaScript
     */
    public static function getToken(): string
    {
        return Session::get('csrf_token', '');
    }
    
    /**
     * Get token name for use in forms
     */
    public static function getTokenName(): string
    {
        return AppConfig::csrfTokenName();
    }
}