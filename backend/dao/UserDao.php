<?php
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('users');
    }

    /**
     * Finds a user by their unique email address.
     */
    public function get_by_email($email)
    {
        return $this->query_unique(
            "SELECT * FROM users WHERE email = :email",
            ['email' => $email]
        );
    }

    /**
     * Retrieves users filtered by role (admin or user).
     */
    public function get_by_role($role)
    {
        return $this->query(
            "SELECT id, full_name, email, role, created_at FROM users WHERE role = :role",
            ['role' => $role]
        );
    }
}