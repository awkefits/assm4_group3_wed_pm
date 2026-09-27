<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/UserRepository.php';
$demoLink = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $repo = new UserRepository();
    $user = $repo->findByEmail($email);
    if ($user) {
        $raw = bin2hex(random_bytes(24));
        $hash = hash('sha256', $raw);
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $stmt = db()->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([(int)$user['id'], $hash, $expires]);
        $demoLink = url('reset_password.php?token=' . urlencode($raw));
        audit('PASSWORD_RESET_REQUEST', (int)$user['id']);
    }
    flash('success', 'If that email exists, a password reset instruction has been created.');
}
$pageTitle='Password recovery'; require __DIR__ . '/includes/header.php';
?>
<section class="auth-shell container"><div class="auth-card"><span class="eyebrow">Account recovery</span><h1>Reset your password</h1><p>Enter the email attached to your account. In production this token would be emailed securely.</p><form method="post" class="form-stack"><?= csrf_field() ?><label>Email address<input type="email" name="email" required></label><button class="button button-primary">Create reset request</button></form><?php if ($demoLink): ?><div class="demo-box"><strong>Local prototype reset link</strong><p>Because XAMPP email is normally not configured, the demo exposes the one-time link here:</p><a href="<?= e($demoLink) ?>">Open secure reset link</a></div><?php endif; ?></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
