<?php
/**
 * Bibiheybet.com - Database Seed Script
 * 
 * Admin istifadəçini yaradır.
 * İstifadə: php database/seed.php
 * 
 * Qeyd: Bu script-i yalnız bir dəfə çalışdırın.
 * Əvvəlcə database/schema.sql-i MySQL-ə import edin.
 */

// Konfiqurasiyanı yüklə
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';

echo "Bibiheybet - Database Seed\n";
echo "==========================\n\n";

try {
    $db = bb_get_db();
    
    // Admin istifadəçi seed
    $username = 'ekosafari';
    $password = 'ParolYanlisdirSifreDogrudur';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    // Əvvəlcə mövcud admin yoxla
    $stmt = $db->prepare("SELECT id FROM admins WHERE username = :username");
    $stmt->execute([':username' => $username]);
    
    if ($stmt->fetch()) {
        echo "[!] Admin '{$username}' artıq mövcuddur. Keçilir.\n";
    } else {
        $stmt = $db->prepare("INSERT INTO admins (username, password_hash, created_at) VALUES (:username, :password_hash, NOW())");
        $stmt->execute([
            ':username' => $username,
            ':password_hash' => $passwordHash,
        ]);
        echo "[+] Admin '{$username}' uğurla yaradıldı.\n";
    }
    
    echo "\nSeed tamamlandı!\n";
    
} catch (PDOException $e) {
    echo "[XƏTA] Database xətası: " . $e->getMessage() . "\n";
    exit(1);
}
