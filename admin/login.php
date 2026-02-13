<?php
/**
 * Bibiheybet.com - Admin Giriş Səhifəsi
 * 
 * Username + password formu, CSRF qorumalı.
 * Öz layout-u var (admin layout istifadə etmir).
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

bb_start_session();

// Artıq giriş edibsə - dashboard-a yönləndir
if (bb_is_logged_in()) {
    bb_redirect(ADMIN_PATH . '/index.php');
}

// POST - login cəhdi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!bb_verify_csrf($csrf)) {
        bb_flash('error', 'Etibarsız sorğu. Yenidən cəhd edin.');
        bb_redirect(ADMIN_PATH . '/login.php');
    }

    if (empty($username) || empty($password)) {
        bb_flash('error', 'İstifadəçi adı və parol daxil edin.');
        bb_redirect(ADMIN_PATH . '/login.php');
    }

    if (bb_login($username, $password)) {
        bb_flash('success', 'Uğurla daxil oldunuz.');
        bb_redirect(ADMIN_PATH . '/index.php');
    }

    bb_flash('error', 'İstifadəçi adı və ya parol yanlışdır.');
    bb_redirect(ADMIN_PATH . '/login.php');
}

// Flash mesajları al
$flashHtml = bb_render_flash();
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş | <?= bb_sanitize(SITE_NAME) ?> Admin</title>
    <link rel="icon" type="image/png" href="/public/assets/img/icon.png">
    <link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body class="bb-login-page">
    <div class="bb-login-wrapper">
        <div class="bb-login-card">
            <div class="bb-login-logo">
                <img src="/public/assets/img/logo.png" alt="<?= bb_sanitize(SITE_NAME) ?>">
            </div>
            <h1 class="bb-login-title">Admin Panel</h1>

            <?= $flashHtml ?>

            <form method="POST" action="" class="bb-login-form">
                <?= bb_generate_csrf() ?>

                <div class="bb-form-group">
                    <label for="username">İstifadəçi adı</label>
                    <input type="text" id="username" name="username" 
                           autocomplete="username" required autofocus
                           placeholder="İstifadəçi adınızı daxil edin">
                </div>

                <div class="bb-form-group">
                    <label for="password">Parol</label>
                    <input type="password" id="password" name="password" 
                           autocomplete="current-password" required
                           placeholder="Parolunuzu daxil edin">
                </div>

                <button type="submit" class="bb-btn bb-btn-primary bb-btn-block">Daxil ol</button>
            </form>
        </div>
        <p class="bb-login-footer">&copy; <?= date('Y') ?> <?= bb_sanitize(SITE_NAME) ?></p>
    </div>
</body>
</html>
