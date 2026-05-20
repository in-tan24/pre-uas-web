<?php
declare(strict_types=1);

namespace App\Models;

final class Program
{
    /** @return array<int, array<string,mixed>> */
    public static function all(): array
    {
        $stmt = db()->query('
            SELECT p.*, f.faculty_name
            FROM programs p
            JOIN faculties f ON f.id = p.faculty_id
            WHERE p.is_active = 1
            ORDER BY f.faculty_name, p.program_name
        ');
        return $stmt->fetchAll() ?: [];
    }

    /** @return array<string,mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM programs WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<int, array<string,mixed>> */
    public static function allWithFaculty(): array
    {
        $stmt = db()->query('
            SELECT p.*, f.faculty_name
            FROM programs p
            JOIN faculties f ON f.id = p.faculty_id
            ORDER BY f.faculty_name, p.program_name
        ');
        return $stmt->fetchAll() ?: [];
    }

    public static function create(int $facultyId, string $name, int $capacity, ?string $requirements, ?string $description, bool $isActive): int
    {
        $stmt = db()->prepare('
            INSERT INTO programs (faculty_id, program_name, capacity, requirements, description, is_active)
            VALUES (:faculty_id, :program_name, :capacity, :requirements, :description, :is_active)
        ');
        $stmt->execute([
            'faculty_id' => $facultyId,
            'program_name' => $name,
            'capacity' => $capacity,
            'requirements' => $requirements,
            'description' => $description,
            'is_active' => $isActive ? 1 : 0,
        ]);
        return (int)db()->lastInsertId();
    }
}
