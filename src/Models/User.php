<?php
declare(strict_types=1);

namespace App\Models;

final class User
{
    /** @return array<string,mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('
            SELECT u.*, r.role_key, r.role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<string,mixed>|null */
    public static function findByUsername(string $username): ?array
    {
        $stmt = db()->prepare('
            SELECT u.*, r.role_key, r.role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            WHERE u.username = :username
        ');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<int, array<string,mixed>> */
    public static function listAll(): array
    {
        $stmt = db()->query('
            SELECT u.id, u.full_name, u.username, u.email, u.is_active, u.created_at, r.role_key, r.role_name
            FROM users u
            JOIN roles r ON r.id = u.role_id
            ORDER BY u.id DESC
        ');
        return $stmt->fetchAll() ?: [];
    }

    public static function create(int $roleId, string $fullName, string $username, ?string $email, string $passwordHash, bool $isActive): int
    {
        $stmt = db()->prepare('
            INSERT INTO users (role_id, full_name, username, email, password_hash, is_active)
            VALUES (:role_id, :full_name, :username, :email, :password_hash, :is_active)
        ');
        $stmt->execute([
            'role_id' => $roleId,
            'full_name' => $fullName,
            'username' => $username,
            'email' => $email,
            'password_hash' => $passwordHash,
            'is_active' => $isActive ? 1 : 0,
        ]);
        return (int)db()->lastInsertId();
    }
}
