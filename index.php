<?php
/**
 * Bibiheybet.com - Root Entry Point
 * 
 * Cloudways NGINX+Apache stack-də NGINX try_files direktivi
 * kök qovluqda index.php axtarır. Bu fayl sorğunu
 * public/index.php-yə yönləndirir.
 * 
 * .htaccess mod_rewrite aktivdirsə, bu fayl istifadə olunmur.
 * NGINX fallback olaraq bu faylı çağıracaq.
 */

// Sorğunu public/index.php-yə yönləndir
// .htaccess parametrlərini simulyasiya et
if (!isset($_GET['lang'])) {
    $_GET['lang'] = 'az';
}
if (!isset($_GET['route'])) {
    $_GET['route'] = $_SERVER['REQUEST_URI'] ?? '';
    
    // Query string-i təmizlə
    if (($pos = strpos($_GET['route'], '?')) !== false) {
        $_GET['route'] = substr($_GET['route'], 0, $pos);
    }
    
    // Əvvəlindəki slash-ı sil
    $_GET['route'] = ltrim($_GET['route'], '/');
    
    // EN/RU dil prefikslərini yoxla
    if (preg_match('#^(en|ru)/(.*)$#', $_GET['route'], $matches)) {
        $_GET['lang'] = $matches[1];
        $_GET['route'] = $matches[2];
    }
}

require __DIR__ . '/public/index.php';
