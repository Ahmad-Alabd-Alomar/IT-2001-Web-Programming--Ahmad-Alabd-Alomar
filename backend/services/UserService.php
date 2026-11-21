<?php

use Firebase\JWT\JWT;

require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../middleware/Logger.php';

class UserService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new UserDao());
    }

    public function register(array $data): array
    {
        $fullName = trim($data['full_name'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if ($fullName === '' || $email === '' || $password === '') {
            throw new Exception("Full name, email, and password are required.", 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Please provide a valid email address.", 400);
        }

        if (strlen($password) < 6) {
            throw new Exception("Password must be at least 6 characters long.", 400);
        }

        if ($this->dao->get_by_email($email)) {
            throw new Exception("An account with this email already exists.", 409);
        }

        $created = $this->dao->add([
            'full_name' => $fullName,
            'email'     => $email,
            'password'  => password_hash($password, PASSWORD_BCRYPT),
            'role'      => 'user'
        ]);

        $safeUser = $this->sanitize_user($created);
        $token = $this->generate_token($safeUser);

        Logger::info("POST /auth/register 201 user={$safeUser['id']} role={$safeUser['role']} Successful registration");

        return [
            'user'  => $safeUser,
            'token' => $token
        ];
    }

    public function login(array $data): array
    {
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if ($email === '' || $password === '') {
            Logger::error("POST /auth/login 400 user=guest role=none Missing email or password");
            throw new Exception("Email and password are required.", 400);
        }

        $user = $this->dao->get_by_email($email);

        if (!$user || !password_verify($password, $user['password'])) {
            Logger::error("POST /auth/login 401 user=guest role=none Failed login attempt for email={$email}");
            throw new Exception("Invalid email or password.", 401);
        }

        $safeUser = $this->sanitize_user($user);
        $token = $this->generate_token($safeUser);

        Logger::info("POST /auth/login 200 user={$safeUser['id']} role={$safeUser['role']} Successful login");

        return [
            'user'  => $safeUser,
            'token' => $token
        ];
    }

    public function get_all_users(): array
    {
        $users = $this->dao->get_all();
        return array_map([$this, 'sanitize_user'], $users);
    }

    public function get_user_by_id($id): array
    {
        $user = $this->get_by_id($id);
        return $this->sanitize_user($user);
    }

    public function update_user($id, array $data, $currentUser): array
    {
        $existing = $this->get_by_id($id);

        if ((int)$currentUser->id !== (int)$id && $currentUser->role !== 'admin') {
            throw new Exception("Forbidden: you can only update your own profile.", 403);
        }

        $payload = [];

        if (isset($data['full_name']) && trim($data['full_name']) !== '') {
            $payload['full_name'] = trim($data['full_name']);
        }

        if (isset($data['email']) && trim($data['email']) !== '') {
            $email = strtolower(trim($data['email']));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Please provide a valid email address.", 400);
            }
            $emailOwner = $this->dao->get_by_email($email);
            if ($emailOwner && (int)$emailOwner['id'] !== (int)$id) {
                throw new Exception("Email is already in use by another account.", 409);
            }
            $payload['email'] = $email;
        }

        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                throw new Exception("Password must be at least 6 characters long.", 400);
            }
            $payload['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        // Only admins may change user roles
        if (isset($data['role']) && $currentUser->role === 'admin') {
            if (!in_array($data['role'], ['admin', 'user'], true)) {
                throw new Exception("Invalid role specified.", 400);
            }
            $payload['role'] = $data['role'];
        }

        if (empty($payload)) {
            return $this->sanitize_user($existing);
        }

        $updated = $this->dao->update($id, $payload);
        return $this->sanitize_user($updated);
    }

    private function generate_token(array $user): string
    {
        $now = time();
        $payload = [
            'iat'       => $now,
            'exp'       => $now + Config::JWT_EXPIRATION(),
            'id'        => (int)$user['id'],
            'email'     => $user['email'],
            'full_name' => $user['full_name'],
            'role'      => $user['role']
        ];

        return JWT::encode($payload, Config::JWT_SECRET(), 'HS256');
    }

    private function sanitize_user(array $user): array
    {
        unset($user['password']);
        return $user;
    }
}