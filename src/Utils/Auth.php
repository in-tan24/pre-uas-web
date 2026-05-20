<?php
declare(strict_types=1);

namespace App\Utils;

use App\Models\Candidate;
use App\Models\User;

final class Auth
{
    public static function candidate(): ?array
    {
        $id = $_SESSION['candidate_id'] ?? null;
        if (!is_int($id) && !ctype_digit((string)$id)) {
            return null;
        }
        return Candidate::findById((int)$id);
    }

    public static function user(): ?array
    {
        $id = $_SESSION['user_id'] ?? null;
        if (!is_int($id) && !ctype_digit((string)$id)) {
            return null;
        }
        return User::findById((int)$id);
    }

    public static function requireCandidate(): array
    {
        $candidate = self::candidate();
        if ($candidate === null) {
            header('Location: /pre-uas/public/candidate/login');
            exit;
        }
        return $candidate;
    }

    /** @param string[] $roleKeys */
    public static function requireUser(array $roleKeys = []): array
    {
        $user = self::user();
        if ($user === null) {
            header('Location: /pre-uas/public/admin/login');
            exit;
        }
        if ($roleKeys !== [] && (!isset($user['role_key']) || !in_array($user['role_key'], $roleKeys, true))) {
            http_response_code(403);
            View::render('errors/403');
            exit;
        }
        return $user;
    }
}
