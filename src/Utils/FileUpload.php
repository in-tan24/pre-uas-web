<?php
declare(strict_types=1);

namespace App\Utils;

final class FileUpload
{
    /** @return array{ok: bool, path?: string, error?: string} */
    public static function save(string $field, string $subdir, array $allowedExt, int $maxBytes): array
    {
        if (!isset($_FILES[$field]) || !is_array($_FILES[$field])) {
            return ['ok' => false, 'error' => 'File tidak ditemukan.'];
        }

        $f = $_FILES[$field];
        if (($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload gagal.'];
        }

        $size = (int)($f['size'] ?? 0);
        if ($size <= 0 || $size > $maxBytes) {
            return ['ok' => false, 'error' => 'Ukuran file tidak valid.'];
        }

        $original = (string)($f['name'] ?? '');
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if ($ext === '' || !in_array($ext, $allowedExt, true)) {
            return ['ok' => false, 'error' => 'Tipe file tidak diizinkan.'];
        }

        $tmp = (string)($f['tmp_name'] ?? '');
        $mime = self::detectMime($tmp);
        if (!self::isAllowedMime($ext, $mime) && !self::validateContentSignature($ext, $tmp)) {
            $shownMime = $mime !== '' ? $mime : '(unknown)';
            return ['ok' => false, 'error' => 'MIME file tidak valid: ' . $shownMime];
        }

        $root = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads';
        $targetDir = $root . DIRECTORY_SEPARATOR . $subdir;
        if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
            return ['ok' => false, 'error' => 'Gagal membuat folder upload.'];
        }

        $safeName = bin2hex(random_bytes(12)) . '.' . $ext;
        $targetPath = $targetDir . DIRECTORY_SEPARATOR . $safeName;
        if (!is_uploaded_file($tmp) || !move_uploaded_file($tmp, $targetPath)) {
            return ['ok' => false, 'error' => 'Gagal menyimpan file.'];
        }

        $publicPath = '/uploads/' . trim($subdir, '/') . '/' . $safeName;
        return ['ok' => true, 'path' => $publicPath];
    }

    private static function detectMime(string $tmpPath): string
    {
        if ($tmpPath === '' || !is_file($tmpPath)) {
            return '';
        }
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        if (!$fi) {
            return '';
        }
        $mime = (string)finfo_file($fi, $tmpPath);
        finfo_close($fi);
        return $mime;
    }

    private static function isAllowedMime(string $ext, string $mime): bool
    {
        $map = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'jpg' => ['image/jpeg', 'image/jpg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/jpg', 'image/pjpeg'],
            'png' => ['image/png', 'image/x-png'],
        ];
        if (!isset($map[$ext])) {
            return false;
        }
        return in_array($mime, $map[$ext], true);
    }

    private static function validateContentSignature(string $ext, string $tmpPath): bool
    {
        if ($tmpPath === '' || !is_file($tmpPath)) {
            return false;
        }

        $ext = strtolower($ext);

        // Strong validation for images
        if (in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            $info = @getimagesize($tmpPath);
            if (!is_array($info) || empty($info['mime'])) {
                return false;
            }
            $m = (string)$info['mime'];
            if ($ext === 'png') {
                return $m === 'image/png';
            }
            return $m === 'image/jpeg';
        }

        // Basic signature checks for document types
        $fh = fopen($tmpPath, 'rb');
        if ($fh === false) {
            return false;
        }
        $head = fread($fh, 8);
        fclose($fh);
        if (!is_string($head)) {
            return false;
        }

        if ($ext === 'pdf') {
            return strncmp($head, '%PDF', 4) === 0;
        }
        if ($ext === 'doc') {
            // OLE Compound File signature: D0 CF 11 E0 A1 B1 1A E1
            return bin2hex($head) === 'd0cf11e0a1b11ae1';
        }
        if ($ext === 'docx') {
            // ZIP signature: PK..
            return strncmp($head, "PK\x03\x04", 4) === 0 || strncmp($head, "PK\x05\x06", 4) === 0 || strncmp($head, "PK\x07\x08", 4) === 0;
        }

        return false;
    }
}
