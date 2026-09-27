<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/classes/UserRepository.php';
if (is_logged_in()) redirect('index.php');
$errors = [];
$email = '';
$displayName = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $displayName = trim($_POST['display_name'] ?? '');
    $password = (string)($_POST['password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if (mb_strlen($displayName) < 2 || mb_strlen($displayName) > 80) $errors[] = 'Display name must be between 2 and 80 characters.';
    if (strlen($password) < 10 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/\d/', $password)) $errors[] = 'Password must be at least 10 characters and include upper-case, lower-case and a number.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';
    $repo = new UserRepository();
    if (!$errors && $repo->findByEmail($email)) $errors[] = 'An account already exists for that email.';
    if (!$errors) {
        $id = $repo->create($email, $displayName, $password);
        audit('USER_REGISTER', $id, 'New CookBook account created');
        flash('success', 'Account created. You can now sign in.');
        redirect('login.php');
    }
}
$pageTitle = 'Create account'; require __DIR__ . '/includes/header.php';
?>
<section class="auth-shell container"><div class="auth-card"><span class="eyebrow">Join the community</span><h1>Create your CookBook account</h1><p>Save recipes, follow cooks, join discussions and publish your own recipes.</p>
<?php if ($errors): ?><div class="form-errors"><strong>Please fix the following:</strong><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<form method="post" class="form-stack" novalidate><?= csrf_field() ?>
<label>Display name<input name="display_name" value="<?= e($displayName) ?>" maxlength="80" required autocomplete="name"></label>
<label>Email address<input type="email" name="email" value="<?= e($email) ?>" maxlength="190" required autocomplete="email"></label>
<label>Password<input type="password" name="password" minlength="10" required autocomplete="new-password"><small>10+ characters with upper-case, lower-case and a number.</small></label>
<label>Confirm password<input type="password" name="confirm_password" minlength="10" required autocomplete="new-password"></label>
<button class="button button-primary button-wide">Create account</button></form><p class="form-foot">Already a member? <a href="<?= e(url('login.php')) ?>">Sign in</a>.</p></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
