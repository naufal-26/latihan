<?php
/**
 * auth_check.php
 *
 * File ini BUKAN halaman tersendiri — ini "penjaga" yang ditaruh di baris
 * paling atas setiap halaman dashboard (index.php, penjualan.php, dst).
 * Kalau session belum login, user otomatis dilempar ke login.php.
 *
 * Cara pakai — tambahkan baris ini sebagai baris PERTAMA pada file PHP
 * yang ingin dilindungi (sebelum HTML apa pun dicetak):
 *
 *     <?php
 *     require_once __DIR__ . '/auth_check.php';
 *     ?>
 *     <!DOCTYPE html> ...
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['is_logged_in'])) {
    header('Location: login.php');
    exit;
}
