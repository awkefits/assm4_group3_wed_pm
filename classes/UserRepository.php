<?php
/** Data access object for user and profile operations. */
declare(strict_types=1);
require_once __DIR__ . '/../config/db.php';

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT id, email, display_name, role, bio, profile_image, dietary_preferences, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $email, string $displayName, string $password): int
    {
        $stmt = db()->prepare('INSERT INTO users (email, display_name, password_hash, role) VALUES (?, ?, ?, "user")');
        $stmt->execute([$email, $displayName, password_hash($password, PASSWORD_DEFAULT)]);
        return (int)db()->lastInsertId();
    }

    public function updateProfile(int $id, string $displayName, string $bio, string $dietaryPreferences, ?string $profileImage): void
    {
        if ($profileImage) {
            $stmt = db()->prepare('UPDATE users SET display_name = ?, bio = ?, dietary_preferences = ?, profile_image = ? WHERE id = ?');
            $stmt->execute([$displayName, $bio, $dietaryPreferences, $profileImage, $id]);
            return;
        }
        $stmt = db()->prepare('UPDATE users SET display_name = ?, bio = ?, dietary_preferences = ? WHERE id = ?');
        $stmt->execute([$displayName, $bio, $dietaryPreferences, $id]);
    }
}
