<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
$token = (string)($_GET['token'] ?? $_POST['token'] ?? '');
$error = '';
$validRow = null;
if ($token !== '') {
    $stmt = db()->prepare('SELECT * FROM password_resets WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
    $stmt->execute([hash('sha256', $token)]);
    $validRow = $stmt->fetch() ?: null;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (!$validRow) $error = 'This reset link is invalid or expired.';
    elseif (strlen($password) < 10 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) $error = 'Password does not meet the security requirements.';
    elseif ($password !== $confirm) $error = 'Passwords do not match.';
    else {
        db()->beginTransaction();
        try {
            $stmt = db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?'); $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $validRow['user_id']]);
            $stmt = db()->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = ?'); $stmt->execute([$validRow['id']]);
            db()->commit(); audit('PASSWORD_RESET_COMPLETE', (int)$validRow['user_id']); flash('success','Password updated. You can now sign in.'); redirect('login.php');
        } catch (Throwable $e) { db()->rollBack(); $error='Unable to update password.'; }
    }
}
$pageTitle='Choose a new password'; require __DIR__ . '/includes/header.php';
?>
<section class="auth-shell container"><div class="auth-card"><h1>Choose a new password</h1><?php if ($error): ?><div class="form-errors"><?= e($error) ?></div><?php endif; ?><?php if ($validRow): ?><form method="post" class="form-stack"><?= csrf_field() ?><input type="hidden" name="token" value="<?= e($token) ?>"><label>New password<input type="password" name="password" minlength="10" required></label><label>Confirm password<input type="password" name="confirm_password" minlength="10" required></label><button class="button button-primary">Update password</button></form><?php else: ?><div class="form-errors">This reset link is invalid or expired.</div><?php endif; ?></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
