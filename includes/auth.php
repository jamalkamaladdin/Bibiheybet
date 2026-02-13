<?php
/**
 * Bibiheybet.com - Autentifikasiya & Session İdarəetmə
 * 
 * Session management, login/logout, CSRF qoruması.
 */

require_once __DIR__ . '/db.php';

/**
 * Session-u başladır (təkrar start-ın qarşısını alır).
 */
function bb_start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        // config.php yüklənib yoxla
        if (!defined('SESSION_NAME')) {
            require_once __DIR__ . '/../config.php';
        }

        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path'     => '/',
            'secure'   => isset($_SERVER['HTTPS']),
            'httponly'  => true,
            'samesite'  => 'Lax',
        ]);
        session_start();
    }
}

/**
 * Admin girişi yoxlayır.
 * 
 * @param string $username İstifadəçi adı
 * @param string $password Parol
 * @return bool Giriş uğurlu olub-olmadığı
 */
function bb_login(string $username, string $password): bool
{
    bb_start_session();

    $db = bb_get_db();
    $stmt = $db->prepare("SELECT id, username, password_hash FROM admins WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Session regenerate (session fixation qoruması)
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['login_time'] = time();

        return true;
    }

    return false;
}

/**
 * Admin çıxışı - session-u tamamilə məhv edir.
 */
function bb_logout(): void
{
    bb_start_session();

    // Session dəyişənlərini təmizlə
    $_SESSION = [];

    // Session cookie-ni sil
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Admin giriş edib-etmədiyini yoxlayır.
 * 
 * @return bool
 */
function bb_is_logged_in(): bool
{
    bb_start_session();

    if (!isset($_SESSION['admin_id'])) {
        return false;
    }

    // Session müddəti yoxla
    if (defined('SESSION_LIFETIME') && isset($_SESSION['login_time'])) {
        if (time() - $_SESSION['login_time'] > SESSION_LIFETIME) {
            bb_logout();
            return false;
        }
    }

    return true;
}

/**
 * Admin girişi tələb edir. Giriş etməyibsə login səhifəsinə yönləndirir.
 * Admin səhifələrinin əvvəlində çağırılmalıdır (middleware).
 */
function bb_require_auth(): void
{
    if (!bb_is_logged_in()) {
        header('Location: ' . ADMIN_PATH . '/login.php');
        exit;
    }
}

/**
 * CSRF token yaradır və session-a yazır.
 * 
 * @return string Hidden input elementi (<input type="hidden" ...>)
 */
function bb_generate_csrf(): string
{
    bb_start_session();

    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();

    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * CSRF token-i doğrulayır.
 * 
 * @param string $token POST-dan gələn token
 * @return bool Token doğrudursa true
 */
function bb_verify_csrf(string $token): bool
{
    bb_start_session();

    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    // Token uyğunluğu yoxla
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }

    // Token müddəti yoxla
    if (defined('CSRF_TOKEN_LIFETIME') && isset($_SESSION['csrf_token_time'])) {
        if (time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_LIFETIME) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
            return false;
        }
    }

    // İstifadə olunmuş token-i sil (bir dəfəlik istifadə)
    unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);

    return true;
}

/**
 * Cari admin ID-ni qaytarır.
 * 
 * @return int|null
 */
function bb_current_admin_id(): ?int
{
    bb_start_session();
    return $_SESSION['admin_id'] ?? null;
}

/**
 * Cari admin istifadəçi adını qaytarır.
 * 
 * @return string|null
 */
function bb_current_admin_username(): ?string
{
    bb_start_session();
    return $_SESSION['admin_username'] ?? null;
}
