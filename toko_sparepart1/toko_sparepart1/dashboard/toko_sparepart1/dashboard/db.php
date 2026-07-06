<?php
/**
 * db.php
 * Koneksi database pusat — di-include di semua halaman yang butuh data.
 * Pakai PDO supaya aman dari SQL Injection (prepared statement).
 *
 * Kalau MySQL Anda pakai username/password lain (bukan default XAMPP),
 * ubah nilai DB_USER / DB_PASS di bawah ini.
 */

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'toko_sparepart1');
define('DB_USER', 'root');
define('DB_PASS', '');        // default XAMPP: kosong. Ganti kalau MySQL Anda diberi password.
define('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lempar error kalau query gagal
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // hasil fetch jadi array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Tampilkan pesan ramah, jangan tampilkan detail koneksi ke publik
    die('Koneksi database gagal. Pastikan MySQL (XAMPP) sedang berjalan dan database "toko_sparepart" sudah diimport. Detail teknis: ' . $e->getMessage());
}
