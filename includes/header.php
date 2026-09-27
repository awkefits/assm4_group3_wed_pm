<?php
/** Shared page header and navigation. */
declare(strict_types=1);
require_once __DIR__ . '/functions.php';
$pageTitle = $pageTitle ?? APP_NAME;
$user = current_user();
$flashes = consume_flashes();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CookBook community recipe sharing platform">
    <title><?= e($pageTitle) ?> · <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
    <script defer src="<?= e(url('assets/js/app.js')) ?>"></script>
</head>
<body>
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="<?= e(url('index.php')) ?>" aria-label="CookBook home">
            <span class="brand-mark">CB</span>
            <span><strong>CookBook</strong><small>Cook. Share. Discover.</small></span>
        </a>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">☰</button>
        <nav id="primary-nav" class="primary-nav" aria-label="Primary navigation">
            <a href="<?= e(url('browse.php')) ?>">Discover</a>
            <?php if ($user): ?>
                <a href="<?= e(url('recommendations.php')) ?>">For you</a>
                <a href="<?= e(url('recipe_form.php')) ?>">Share recipe</a>
                <a href="<?= e(url('bookmarks.php')) ?>">Saved</a>
                <a href="<?= e(url('notifications.php')) ?>" class="nav-notification">Notifications<?php $count = unread_notification_count(); if ($count): ?><span><?= $count ?></span><?php endif; ?></a>
                <?php if (($user['role'] ?? '') === 'admin'): ?><a href="<?= e(url('admin/index.php')) ?>">Admin</a><?php endif; ?>
                <div class="nav-user">
                    <a href="<?= e(url('profile.php')) ?>"><?= e($user['display_name']) ?></a>
                    <a class="button button-ghost button-small" href="<?= e(url('logout.php')) ?>">Logout</a>
                </div>
            <?php else: ?>
                <a href="<?= e(url('login.php')) ?>">Sign in</a>
                <a class="button button-primary button-small" href="<?= e(url('register.php')) ?>">Join CookBook</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<?php foreach ($flashes as $flash): ?>
<div class="flash flash-<?= e($flash['type']) ?>" role="status"><div class="container"><?= e($flash['message']) ?></div></div>
<?php endforeach; ?>
<main id="main-content">
