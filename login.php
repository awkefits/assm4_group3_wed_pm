<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/UserRepository.php';
if (is_logged_in()) redirect('index.php');
$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    $repo = new UserRepository();
    $user = $repo->findByEmail($email);
    if ($user && (int)$user['is_active'] === 1 && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int)$user['id'], 'email' => $user['email'], 'display_name' => $user['display_name'], 'role' => $user['role']];
        audit('LOGIN_SUCCESS', (int)$user['id']);
        flash('success', 'Welcome back, ' . $user['display_name'] . '.');
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    }
    audit('LOGIN_FAILED', $user ? (int)$user['id'] : null, 'Email: ' . $email);
    $error = 'Email or password is incorrect, or the account is inactive.';
}
$pageTitle='Sign in'; require __DIR__ . '/includes/header.php';
?>
<section class="auth-shell container"><div class="auth-card"><span class="eyebrow">Welcome back</span><h1>Sign in to CookBook</h1><p>Continue to your saved recipes, creators and cooking activity.</p>
<?php if ($error): ?><div class="form-errors"><?= e($error) ?></div><?php endif; ?>
<form method="post" class="form-stack"><?= csrf_field() ?><label>Email address<input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><div class="form-inline-between"><label class="checkbox"><input type="checkbox" name="remember" value="1"> Remember me on this device</label><a href="<?= e(url('forgot_password.php')) ?>">Forgot password?</a></div><button class="button button-primary button-wide">Sign in</button></form><p class="form-foot">New here? <a href="<?= e(url('register.php')) ?>">Create an account</a>.</p></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
