<?php
declare(strict_types=1);

namespace App\Models;

final class EnrollmentRecord
{
    /** @return array<string,mixed>|null */
    public static function forCandidate(int $candidateId): ?array
    {
        $stmt = db()->prepare('
            SELECT e.*, p.program_name
            FROM enrollment_records e
            JOIN programs p ON p.id = e.program_id
            WHERE e.candidate_id = :cid
            ORDER BY e.id DESC
            LIMIT 1
        ');
        $stmt->execute(['cid' => $candidateId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(int $candidateId, int $programId, string $studentId): int
    {
        $stmt = db()->prepare('
            INSERT INTO enrollment_records (candidate_id, program_id, enrollment_date, status, student_id)
            VALUES (:cid, :pid, NOW(), :status, :student_id)
        ');
        $stmt->execute([
            'cid' => $candidateId,
            'pid' => $programId,
            'status' => 'active',
            'student_id' => $studentId,
        ]);
        return (int)db()->lastInsertId();
    }
}

