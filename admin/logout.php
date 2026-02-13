<?php
/**
 * Bibiheybet.com - Admin Çıxış
 * 
 * Session-u məhv edir və login səhifəsinə yönləndirir.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

bb_logout();
bb_flash('info', 'Uğurla çıxış etdiniz.');
bb_redirect(ADMIN_PATH . '/login.php');
