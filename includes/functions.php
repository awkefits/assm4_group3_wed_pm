<?php
/** Shared helpers used across views and controllers. */
declare(strict_types=1);
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

function url(string $path = ''): string
{
    return APP_BASE . ($path !== '' ? '/' . ltrim($path, '/') : '');
}

function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function consume_flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    if (!$submitted || !hash_equals($_SESSION['csrf_token'] ?? '', $submitted)) {
        http_response_code(419);
        exit('Security check failed. Please go back and try again.');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    return (current_user()['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('warning', 'Please sign in to continue.');
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        exit('Access denied. Administrator permission is required.');
    }
}

function audit(string $action, ?int $targetId = null, string $details = ''): void
{
    try {
        $stmt = db()->prepare('INSERT INTO audit_logs (user_id, action, target_id, details, ip_address) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            current_user()['id'] ?? null,
            $action,
            $targetId,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'CLI',
        ]);
    } catch (Throwable $e) {
        // Audit failures should not break the user flow in a classroom prototype.
    }
}

function setting(string $key, string $default = ''): string
{
    try {
        $stmt = db()->prepare('SELECT setting_value FROM site_settings WHERE setting_key = ? LIMIT 1');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? (string)$value : $default;
    } catch (Throwable $e) {
        return $default;
    }
}

function recipe_image(?string $path): string
{
    if ($path) {
        return url(ltrim($path, '/'));
    }
    return url('assets/images/recipe-placeholder.svg');
}

function avatar_url(?string $path): string
{
    return $path ? url(ltrim($path, '/')) : url('assets/images/avatar-placeholder.svg');
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'recipe-' . time();
}

function save_upload(array $file, string $folder, array $allowedMime, int $maxBytes = 5242880): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The upload could not be completed.');
    }
    if (($file['size'] ?? 0) > $maxBytes) {
        throw new RuntimeException('The uploaded file is too large.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, $allowedMime, true)) {
        throw new RuntimeException('Unsupported file type.');
    }

    $extensionMap = [
        'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
        'image/gif' => 'gif', 'video/mp4' => 'mp4', 'video/webm' => 'webm',
    ];
    $extension = $extensionMap[$mime] ?? 'bin';
    $safeName = bin2hex(random_bytes(12)) . '.' . $extension;
    $absoluteDir = dirname(__DIR__) . '/uploads/' . trim($folder, '/');
    if (!is_dir($absoluteDir)) {
        mkdir($absoluteDir, 0775, true);
    }
    $absolutePath = $absoluteDir . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
        throw new RuntimeException('The file could not be saved.');
    }
    return 'uploads/' . trim($folder, '/') . '/' . $safeName;
}

function notify(int $userId, string $type, string $message, ?string $link = null): void
{
    $stmt = db()->prepare('INSERT INTO notifications (user_id, type, message, link_url) VALUES (?, ?, ?, ?)');
    $stmt->execute([$userId, $type, $message, $link]);
}

function unread_notification_count(): int
{
    if (!is_logged_in()) return 0;
    $stmt = db()->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->execute([current_user()['id']]);
    return (int)$stmt->fetchColumn();
}

function stars(float $rating): string
{
    $rounded = (int)round($rating);
    return str_repeat('★', $rounded) . str_repeat('☆', max(0, 5 - $rounded));
}
