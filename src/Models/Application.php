<?php
declare(strict_types=1);

namespace App\Models;

final class Application
{
    /** @return array<string,mixed>|null */
    public static function findByCandidateId(int $candidateId): ?array
    {
        $stmt = db()->prepare('
            SELECT a.*, p.program_name, f.faculty_name
            FROM applications a
            JOIN programs p ON p.id = a.program_id
            JOIN faculties f ON f.id = p.faculty_id
            WHERE a.candidate_id = :cid
            ORDER BY a.id DESC
            LIMIT 1
        ');
        $stmt->execute(['cid' => $candidateId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function createOrUpdate(int $candidateId, int $programId, string $applicationDate): int
    {
        $existing = self::findByCandidateId($candidateId);
        if ($existing && in_array((string)$existing['status'], ['pending', 'revise'], true)) {
            $stmt = db()->prepare('
                UPDATE applications
                SET program_id = :program_id, application_date = :application_date
                WHERE id = :id
            ');
            $stmt->execute([
                'program_id' => $programId,
                'application_date' => $applicationDate,
                'id' => (int)$existing['id'],
            ]);
            return (int)$existing['id'];
        }

        $stmt = db()->prepare('
            INSERT INTO applications (candidate_id, program_id, application_date, status)
            VALUES (:candidate_id, :program_id, :application_date, :status)
        ');
        $stmt->execute([
            'candidate_id' => $candidateId,
            'program_id' => $programId,
            'application_date' => $applicationDate,
            'status' => 'pending',
        ]);
        return (int)db()->lastInsertId();
    }

    public static function submit(int $applicationId): void
    {
        $stmt = db()->prepare('
            UPDATE applications
            SET status = :status, submission_date = NOW()
            WHERE id = :id
        ');
        $stmt->execute(['status' => 'submitted', 'id' => $applicationId]);
    }

    /** @return array<int, array<string,mixed>> */
    public static function listAll(?string $status = null): array
    {
        if ($status) {
            $stmt = db()->prepare('
                SELECT a.*, c.first_name, c.last_name, c.email, p.program_name
                FROM applications a
                JOIN candidates c ON c.id = a.candidate_id
                JOIN programs p ON p.id = a.program_id
                WHERE a.status = :status
                ORDER BY a.id DESC
            ');
            $stmt->execute(['status' => $status]);
            return $stmt->fetchAll() ?: [];
        }

        $stmt = db()->query('
            SELECT a.*, c.first_name, c.last_name, c.email, p.program_name
            FROM applications a
            JOIN candidates c ON c.id = a.candidate_id
            JOIN programs p ON p.id = a.program_id
            ORDER BY a.id DESC
        ');
        return $stmt->fetchAll() ?: [];
    }

    /** @return array<string,mixed>|null */
    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('
            SELECT a.*, c.first_name, c.last_name, c.email AS candidate_email, c.phone, c.status AS candidate_status,
                   p.program_name, f.faculty_name
            FROM applications a
            JOIN candidates c ON c.id = a.candidate_id
            JOIN programs p ON p.id = a.program_id
            JOIN faculties f ON f.id = p.faculty_id
            WHERE a.id = :id
        ');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function setStatus(int $id, string $status, ?string $notes = null): void
    {
        $stmt = db()->prepare('UPDATE applications SET status = :status, review_notes = :notes WHERE id = :id');
        $stmt->execute(['status' => $status, 'notes' => $notes, 'id' => $id]);
    }
}

