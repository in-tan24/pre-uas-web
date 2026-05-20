<?php
declare(strict_types=1);

namespace App\Models;

final class Faculty
{
    /** @return array<int, array<string,mixed>> */
    public static function all(): array
    {
        $stmt = db()->query('SELECT * FROM faculties ORDER BY faculty_name ASC');
        return $stmt->fetchAll() ?: [];
    }

    public static function create(string $name, ?string $description): int
    {
        $stmt = db()->prepare('
            INSERT INTO faculties (faculty_name, description)
            VALUES (:name, :description)
        ');
        $stmt->execute([
            'name' => $name,
            'description' => $description,
        ]);
        return (int)db()->lastInsertId();
    }
}

