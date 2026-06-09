<?php

declare(strict_types=1);

final class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findById(int $id): ?UserDTO
    {
        $stmt = $this->db->prepare('SELECT id, email, role FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new UserDTO((int) $row['id'], $row['email'], $row['role']);
    }

    public function findByEmail(string $email): ?UserDTO
    {
        $stmt = $this->db->prepare('SELECT id, email, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        if (!$row) return null;
        return new UserDTO((int) $row['id'], $row['email'], $row['role']);
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function getPasswordHash(string $email): ?string
    {
        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ? $row['password_hash'] : null;
    }

    public function create(string $email, string $passwordHash, string $role): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (email, password_hash, role) VALUES (?, ?, ?)');
        $stmt->execute([$email, $passwordHash, $role]);
        return (int) $this->db->lastInsertId();
    }
}
