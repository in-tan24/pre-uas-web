<?php
declare(strict_types=1);

namespace App\Models;

final class OspekSchedule
{
    /** @return array<int, array<string,mixed>> */
    public static function listForProgram(?int $programId): array
    {
        if ($programId === null) {
            $stmt = db()->query('SELECT * FROM ospek_schedules ORDER BY start_at ASC');
            return $stmt->fetchAll() ?: [];
        }
        $stmt = db()->prepare('
            SELECT *
            FROM ospek_schedules
            WHERE program_id IS NULL OR program_id = :pid
            ORDER BY start_at ASC
        ');
        $stmt->execute(['pid' => $programId]);
        return $stmt->fetchAll() ?: [];
    }

    public static function create(?int $programId, string $title, string $startAt, string $endAt, string $location): int
    {
        $stmt = db()->prepare('
            INSERT INTO ospek_schedules (program_id, title, description, start_at, end_at, location)
            VALUES (:pid, :title, :description, :start_at, :end_at, :location)
        ');
        $stmt->execute([
            'pid' => $programId,
            'title' => $title,
            'description' => null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'location' => $location !== '' ? $location : null,
        ]);
        return (int)db()->lastInsertId();
    }
}

