<?php
declare(strict_types=1);

/** اختصار لإخراج نص آمن (يمنع XSS) */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

/**
 * رفع صورة بشكل آمن:
 * - يتحقق من نوع الملف الفعلي (finfo) لا من الامتداد فقط
 * - يعيد ترميز الصورة عبر GD (يزيل أي كود مضمّن خبيث ويوحّد الصيغة)
 * - يولّد اسم ملف عشوائي (لا نثق باسم الملف القادم من المستخدم)
 * يُرجع اسم الملف الجديد عند النجاح، أو null عند الفشل.
 */
function handle_image_upload(array $file): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($file['size'] > 4 * 1024 * 1024) { // حد أقصى 4 ميجابايت
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        return null;
    }

    $image = match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($file['tmp_name']),
        'image/png' => @imagecreatefrompng($file['tmp_name']),
        'image/webp' => @imagecreatefromwebp($file['tmp_name']),
        default => null,
    };

    if (!$image) {
        return null;
    }

    // تحجيم لعرض معقول (800px كحد أقصى) للحفاظ على سرعة الموقع
    $width = imagesx($image);
    $height = imagesy($image);
    $maxWidth = 800;
    if ($width > $maxWidth) {
        $newHeight = (int) round($height * ($maxWidth / $width));
        $resized = imagecreatetruecolor($maxWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $resized;
    }

    $filename = bin2hex(random_bytes(16)) . '.jpg';
    $destination = __DIR__ . '/../assets/img/uploads/' . $filename;

    $success = imagejpeg($image, $destination, 85);
    imagedestroy($image);

    return $success ? $filename : null;
}

function delete_uploaded_image(?string $filename): void
{
    if (!$filename) {
        return;
    }
    $path = __DIR__ . '/../assets/img/uploads/' . basename($filename);
    if (is_file($path)) {
        @unlink($path);
    }
}
