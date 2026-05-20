<?php
declare(strict_types=1);

namespace App\Models;

final class Document
{
    /** @return array<int, array<string,mixed>> */
    public static function forApplication(int $applicationId): array
    {
        $stmt = db()->prepare('
            SELECT d.*, u.full_name AS verified_by_name
            FROM documents d
            LEFT JOIN users u ON u.id = d.verified_by
            WHERE d.application_id = :aid
            ORDER BY d.id DESC
        ');
        $stmt->execute(['aid' => $applicationId]);
        return $stmt->fetchAll() ?: [];
    }

    public static function create(int $applicationId, string $type, string $path): int
    {
        $stmt = db()->prepare('
            INSERT INTO documents (application_id, document_type, file_path, upload_date, status)
            VALUES (:application_id, :document_type, :file_path, NOW(), :status)
        ');
        $stmt->execute([
            'application_id' => $applicationId,
            'document_type' => $type,
            'file_path' => $path,
            'status' => 'pending',
        ]);
        return (int)db()->lastInsertId();
    }

    /** @return array<string,mixed>|null */
    public static function findByApplicationAndType(int $applicationId, string $type): ?array
    {
        $stmt = db()->prepare('
            SELECT *
            FROM documents
            WHERE application_id = :aid AND document_type = :dtype
            ORDER BY id DESC
            LIMIT 1
        ');
        $stmt->execute(['aid' => $applicationId, 'dtype' => $type]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function replace(int $docId, string $path): void
    {
        $stmt = db()->prepare('
            UPDATE documents
            SET file_path = :file_path, upload_date = NOW(), status = :status, verified_by = NULL, verified_at = NULL, notes = NULL
            WHERE id = :id
        ');
        $stmt->execute([
            'file_path' => $path,
            'status' => 'pending',
            'id' => $docId,
        ]);
    }

    public static function verify(int $id, string $status, ?int $verifiedBy, ?string $notes = null): void
    {
        $stmt = db()->prepare('
            UPDATE documents
            SET status = :status, verified_by = :verified_by, verified_at = NOW(), notes = :notes
            WHERE id = :id
        ');
        $stmt->execute([
            'status' => $status,
            'verified_by' => $verifiedBy,
            'notes' => $notes,
            'id' => $id,
        ]);
    }
}
