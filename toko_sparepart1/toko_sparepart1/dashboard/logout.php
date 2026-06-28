<?php
/**
 * logout.php
 * Menghapus sesi login & cookie "Ingat saya", lalu kembali ke halaman login.
 */

session_start();

// Hapus semua data session
$_SESSION = [];
session_destroy();

// Hapus cookie "Ingat saya" kalau ada
if (!empty($_COOKIE['remember_email'])) {
    setcookie('remember_email', '', time() - 3600, '/');
}
if (!empty($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

header('Location: login.php');
exit;
