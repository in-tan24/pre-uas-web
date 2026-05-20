<?php
declare(strict_types=1);

namespace App\Models;

final class Candidate
{
    /** @return array<string,mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM candidates WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<string,mixed>|null */
    public static function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare('SELECT * FROM candidates WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $firstName, string $lastName, string $email, string $passwordHash): int
    {
        $stmt = db()->prepare('
            INSERT INTO candidates (first_name, last_name, email, password_hash, status)
            VALUES (:first_name, :last_name, :email, :password_hash, :status)
        ');
        $stmt->execute([
            'first_name' => $firstName,
            'last_name' => $lastName !== '' ? $lastName : null,
            'email' => $email,
            'password_hash' => $passwordHash,
            'status' => 'draft',
        ]);
        return (int)db()->lastInsertId();
    }

    public static function updateStatus(int $id, string $status): void
    {
        $stmt = db()->prepare('UPDATE candidates SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
    }
}

