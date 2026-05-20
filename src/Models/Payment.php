<?php
declare(strict_types=1);

namespace App\Models;

final class Payment
{
    /** @return array<int, array<string,mixed>> */
    public static function forEnrollment(int $enrollmentId): array
    {
        $stmt = db()->prepare('
            SELECT p.*, u.full_name AS verified_by_name
            FROM payments p
            LEFT JOIN users u ON u.id = p.verified_by
            WHERE p.enrollment_id = :eid
            ORDER BY p.id DESC
        ');
        $stmt->execute(['eid' => $enrollmentId]);
        return $stmt->fetchAll() ?: [];
    }

    public static function create(int $enrollmentId, float $amount, string $method, string $receiptPath): int
    {
        $stmt = db()->prepare('
            INSERT INTO payments (enrollment_id, amount, payment_date, payment_method, status, receipt_file)
            VALUES (:eid, :amount, NOW(), :method, :status, :receipt)
        ');
        $stmt->execute([
            'eid' => $enrollmentId,
            'amount' => $amount,
            'method' => $method,
            'status' => 'pending',
            'receipt' => $receiptPath,
        ]);
        return (int)db()->lastInsertId();
    }

    /** @return array<int, array<string,mixed>> */
    public static function listAll(?string $status = null): array
    {
        if ($status) {
            $stmt = db()->prepare('
                SELECT p.*, e.student_id, c.email AS candidate_email
                FROM payments p
                JOIN enrollment_records e ON e.id = p.enrollment_id
                JOIN candidates c ON c.id = e.candidate_id
                WHERE p.status = :status
                ORDER BY p.id DESC
            ');
            $stmt->execute(['status' => $status]);
            return $stmt->fetchAll() ?: [];
        }
        $stmt = db()->query('
            SELECT p.*, e.student_id, c.email AS candidate_email
            FROM payments p
            JOIN enrollment_records e ON e.id = p.enrollment_id
            JOIN candidates c ON c.id = e.candidate_id
            ORDER BY p.id DESC
        ');
        return $stmt->fetchAll() ?: [];
    }

    public static function verify(int $paymentId, string $status, int $verifiedBy): void
    {
        $stmt = db()->prepare('
            UPDATE payments
            SET status = :status, verified_by = :verified_by, verified_at = NOW()
            WHERE id = :id
        ');
        $stmt->execute(['status' => $status, 'verified_by' => $verifiedBy, 'id' => $paymentId]);
    }
}

