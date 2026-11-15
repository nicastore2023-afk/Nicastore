<?php

declare(strict_types=1);

namespace Nicastore\Controllers;

use Nicastore\Core\Controller;
use Nicastore\Models\User;
use Nicastore\Core\Session;
use Nicastore\Config\AppConfig;
use Nicastore\Core\Middleware\RateLimitMiddleware;

class AuthController extends Controller
{
    private User $userModel;
    
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }
    
    public function showLogin(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect(AppConfig::url('/'));
        }
        $this->view('auth/login', ['title' => 'Iniciar Sesión']);
    }
    
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        $identifier = RateLimitMiddleware::getIdentifier();
        
        if (empty($email) || empty($password)) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Email y contraseña son requeridos']);
            $this->back();
        }
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user || !$this->userModel->verifyPassword($password, $user['password'])) {
            Session::flash('flash', ['type' => 'error', 'message' => 'Credenciales inválidas']);
            $this->back();
        }
        
        // Clear rate limit on successful login
        RateLimitMiddleware::clear('login', $identifier);
        
        Session::regenerate();
        Session::setUser([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);
        
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->setRememberToken($user['id'], $token);
            setcookie('remember_token', $token, time() + 60 * 60 * 24 * 30, '/', '', false, true);
        }
        
        $cart = new \Nicastore\Models\Cart();
        $cart->mergeGuestCart($user['id']);
        
        Session::flash('flash', ['type' => 'success', 'message' => 'Bienvenido, ' . $user['name']]);
        $this->redirect(AppConfig::url('/'));
    }
    
    public function showRegister(): void
    {
        if (Session::isLoggedIn()) {
            $this->redirect(AppConfig::url('/'));
        }
        $this->view('auth/register', ['title' => 'Registrarse']);
    }
    
    public function register(): void
    {
        $identifier = RateLimitMiddleware::getIdentifier();
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'El nombre es requerido';
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        // Stronger password requirements
        $passwordErrors = $this->validatePasswordStrength($password);
        if (!empty($passwordErrors)) {
            $errors = array_merge($errors, $passwordErrors);
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Las contraseñas no coinciden';
        }
        
        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'El email ya está registrado';
        }
        
if (!empty($errors)) {
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        $userId = $this->userModel->createUser([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
        
        file_put_contents('C:/xampp/htdocs/PROTOTIPO/register_debug.log', "After createUser, userId: $userId\n", FILE_APPEND);
        
$user = $this->userModel->find($userId);

        Session::regenerate();
        Session::setUser([
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);
        
        $cart = new \Nicastore\Models\Cart();
        $cart->mergeGuestCart($user['id']);
        
        Session::flash('flash', ['type' => 'success', 'message' => 'Cuenta creada exitosamente']);
        $this->redirect(AppConfig::url('/'));
    }
    
    /**
     * Validate password strength
     * @return string[]
     */
    private function validatePasswordStrength(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'La contraseña debe tener al menos 8 caracteres';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una mayúscula';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos una minúscula';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'La contraseña debe contener al menos un número';
        }
        
        // Special character is recommended but not required
        // if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        //     $errors[] = 'La contraseña debe contener al menos un carácter especial';
        // }
        
        // Check for common weak passwords
        $commonPasswords = ['password', '123456', 'qwerty', 'admin', 'nicastore', 'password123'];
        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'La contraseña es demasiado común, elige una más segura';
        }
        
        return $errors;
    }
    
    public function logout(): void
    {
        $token = $_COOKIE['remember_token'] ?? null;
        $userId = Session::getUserId();
        
        if ($token && $userId) {
            // Verify token belongs to this user before invalidating
            if ($this->userModel->verifyRememberToken($userId, $token)) {
                $this->userModel->invalidateRememberToken($userId);
            }
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        Session::logout();
        $this->redirect(AppConfig::url('/'));
    }
    
    public function checkRemember(): void
    {
        $token = $_COOKIE['remember_token'] ?? null;
        
        if ($token && !Session::isLoggedIn()) {
            // Find user by token (we need to check all users since token is hashed)
            // More efficient: add a selector column, but for now check all
            $users = $this->userModel->query(
                "SELECT * FROM users WHERE remember_token_hash IS NOT NULL AND remember_token_expires > datetime('now')"
            )->fetchAll();
            
            foreach ($users as $user) {
                if ($this->userModel->verifyRememberToken($user['id'], $token)) {
                    // Token valid - ROTATE it for security
                    $newToken = $this->userModel->rotateRememberToken($user['id']);
                    if ($newToken) {
                        // Update cookie with new token
                        setcookie('remember_token', $newToken, time() + 60 * 60 * 24 * 30, '/', '', false, true);
                    }
                    
                    Session::regenerate();
                    Session::setUser([
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]);
                    
                    $cart = new \Nicastore\Models\Cart();
                    $cart->mergeGuestCart($user['id']);
                    break; // Only one valid token should match
                }
            }
        }
    }
}