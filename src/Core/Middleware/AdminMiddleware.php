<?php

declare(strict_types=1);

namespace Nicastore\Core\Middleware;

use Nicastore\Core\Session;
use Nicastore\Config\AppConfig;

class AdminMiddleware
{
    public static function handle(): bool
    {
        if (!Session::isLoggedIn()) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Debes iniciar sesión para acceder al panel de administración']);
            header('Location: ' . AppConfig::url('/login'));
            exit;
        }
        
        $user = Session::getUser();
        if ($user['role'] !== 'admin') {
            Session::flash('flash', ['type' => 'error', 'message' => 'No tienes permisos para acceder al panel de administración']);
            header('Location: ' . AppConfig::url('/'));
            exit;
        }
        return true;
    }
}