<?php

declare(strict_types=1);

require_once __DIR__ . '/init.php';

function save_uploaded_image(string $field, string $subdir): ?string
{
    if (empty($_FILES[$field]) || !is_array($_FILES[$field])) {
        return null;
    }

    $file = $_FILES[$field];

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('فشل رفع الملف');
    }

    $tmp = (string)($file['tmp_name'] ?? '');

    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new RuntimeException('ملف غير صالح');
    }

    $size = (int)($file['size'] ?? 0);
    if ($size <= 0 || $size > 5 * 1024 * 1024) {
        throw new RuntimeException('حجم الصورة غير مسموح');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmp);

    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        default => '',
    };

    if ($ext === '') {
        throw new RuntimeException('نوع الصورة غير مدعوم');
    }

    $safeSubdir = preg_replace('/[^a-z0-9_-]/i', '', $subdir) ?: 'general';

    $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $safeSubdir;
    if (!is_dir($baseDir)) {
        if (!mkdir($baseDir, 0775, true) && !is_dir($baseDir)) {
            throw new RuntimeException('تعذر إنشاء مجلد الرفع');
        }
    }

    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $dest = $baseDir . DIRECTORY_SEPARATOR . $name;

    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('تعذر حفظ الصورة');
    }

    return 'uploads/' . $safeSubdir . '/' . $name;
}
