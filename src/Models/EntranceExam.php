<?php
declare(strict_types=1);

namespace App\Models;

final class EntranceExam
{
    /** @return array<string,mixed>|null */
    public static function forApplication(int $applicationId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM entrance_exams WHERE application_id = :aid ORDER BY id DESC LIMIT 1');
        $stmt->execute(['aid' => $applicationId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function schedule(int $applicationId, string $examDate, string $location, string $type): int
    {
        $existing = self::forApplication($applicationId);
        if ($existing && in_array((string)$existing['status'], ['scheduled', 'completed'], true)) {
            $stmt = db()->prepare('
                UPDATE entrance_exams
                SET exam_date = :exam_date, exam_location = :exam_location, exam_type = :exam_type, status = :status
                WHERE id = :id
            ');
            $stmt->execute([
                'exam_date' => $examDate,
                'exam_location' => $location !== '' ? $location : null,
                'exam_type' => $type,
                'status' => 'scheduled',
                'id' => (int)$existing['id'],
            ]);
            return (int)$existing['id'];
        }

        $stmt = db()->prepare('
            INSERT INTO entrance_exams (application_id, exam_date, exam_location, exam_type, status)
            VALUES (:aid, :exam_date, :exam_location, :exam_type, :status)
        ');
        $stmt->execute([
            'aid' => $applicationId,
            'exam_date' => $examDate,
            'exam_location' => $location !== '' ? $location : null,
            'exam_type' => $type,
            'status' => 'scheduled',
        ]);
        return (int)db()->lastInsertId();
    }

    public static function setScore(int $examId, float $score, string $status): void
    {
        $stmt = db()->prepare('UPDATE entrance_exams SET score = :score, status = :status WHERE id = :id');
        $stmt->execute(['score' => $score, 'status' => $status, 'id' => $examId]);
    }

    /** @return array<int, array<string,mixed>> */
    public static function listAll(): array
    {
        $stmt = db()->query('
            SELECT e.*, a.id AS application_id, c.email AS candidate_email, p.program_name
            FROM entrance_exams e
            JOIN applications a ON a.id = e.application_id
            JOIN candidates c ON c.id = a.candidate_id
            JOIN programs p ON p.id = a.program_id
            ORDER BY e.exam_date DESC
        ');
        return $stmt->fetchAll() ?: [];
    }
}
