<?php

declare(strict_types=1);

namespace Nicastore\Models;

use Nicastore\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password', 'role', 'email_verified_at', 'remember_token', 'remember_token_hash', 'remember_token_expires'];
    protected array $hidden = ['password', 'remember_token'];
    
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }
    
    public function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);
        $data['role'] = $data['role'] ?? 'customer';
        return $this->create($data);
    }
    
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    public function updatePassword(int $userId, string $newPassword): int
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->update($userId, ['password' => $hash]);
    }
    
    /**
     * Store a remember token (hashed) for the user
     */
    public function setRememberToken(int $userId, string $token): int
    {
        $hash = password_hash($token, PASSWORD_BCRYPT, ['cost' => 12]);
        $expires = date('Y-m-d H:i:s', time() + 30 * 24 * 60 * 60); // 30 days
        return $this->update($userId, [
            'remember_token_hash' => $hash,
            'remember_token_expires' => $expires
        ]);
    }
    
    /**
     * Verify a remember token against stored hash
     */
    public function verifyRememberToken(int $userId, string $token): bool
    {
        $user = $this->find($userId);
        if (!$user || empty($user['remember_token_hash'])) {
            return false;
        }
        
        // Check expiration
        if (!empty($user['remember_token_expires'])) {
            $expires = strtotime($user['remember_token_expires']);
            if ($expires < time()) {
                return false; // Token expired
            }
        }
        
        return password_verify($token, $user['remember_token_hash']);
    }
    
    /**
     * Invalidate remember token (set to NULL)
     */
    public function invalidateRememberToken(int $userId): int
    {
        return $this->update($userId, [
            'remember_token_hash' => null,
            'remember_token_expires' => null
        ]);
    }
    
    /**
     * Rotate remember token (generate new one after successful auth)
     */
    public function rotateRememberToken(int $userId): ?string
    {
        $newToken = bin2hex(random_bytes(32));
        $this->setRememberToken($userId, $newToken);
        return $newToken;
    }
    
    public function getCustomers(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, ['id', 'name', 'email', 'role', 'created_at']);
    }
    
    public function getAdmins(): array
    {
        return $this->where('role', 'admin');
    }
}