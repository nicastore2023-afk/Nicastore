<?php

declare(strict_types=1);

namespace Nicastore\Core\Middleware;

use Nicastore\Core\Session;
use Nicastore\Config\AppConfig;

class AuthMiddleware
{
    public static function handle(): bool
    {
        if (!Session::isLoggedIn()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Debes iniciar sesión para acceder a esta página']);
            header('Location: ' . AppConfig::url('/login'));
            exit;
        }
        return true;
    }
}