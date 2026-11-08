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
            $this->userModel->update($user['id'], ['remember_token' => $token]);
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
        file_put_contents('C:/xampp/htdocs/PROTOTIPO/register_debug.log', "REGISTER START\n", FILE_APPEND);
        
        $identifier = RateLimitMiddleware::getIdentifier();
        
        file_put_contents('C:/xampp/htdocs/PROTOTIPO/register_debug.log', "After identifier\n", FILE_APPEND);
        
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        file_put_contents('C:/xampp/htdocs/PROTOTIPO/register_debug.log', "After input parsing\n", FILE_APPEND);
        
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
            file_put_contents('C:/xampp/htdocs/PROTOTIPO/register_debug.log', "VALIDATION ERRORS: " . json_encode($errors) . "\n", FILE_APPEND);
            Session::flash('flash', ['type' => 'error', 'message' => implode('<br>', $errors)]);
            $this->back();
        }
        
        file_put_contents('C:/xampp/htdocs/PROTOTIPO/register_debug.log', "After validation, before clear rate limit\n", FILE_APPEND);
        
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
        
        file_put_contents('C:/xampp/htdocs/PROTOTIPO/register_debug.log', "After setUser: " . json_encode($_SESSION) . "\n", FILE_APPEND);
        
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
        if ($token) {
            $this->userModel->query("UPDATE users SET remember_token = NULL WHERE remember_token = ?", [$token]);
            setcookie('remember_token', '', time() - 3600, '/', '', false, true);
        }
        
        Session::logout();
        Session::flash('flash', ['type' => 'success', 'message' => 'Sesión cerrada correctamente']);
        $this->redirect(AppConfig::url('/'));
    }
    
    public function checkRemember(): void
    {
        $token = $_COOKIE['remember_token'] ?? null;
        
        if ($token && !Session::isLoggedIn()) {
            $user = $this->userModel->query("SELECT * FROM users WHERE remember_token = ?", [$token])->fetch();
            
            if ($user) {
                Session::regenerate();
                Session::setUser([
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]);
                
                $cart = new \Nicastore\Models\Cart();
                $cart->mergeGuestCart($user['id']);
            }
        }
    }
}