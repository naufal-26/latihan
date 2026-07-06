<?php
/**
 * generate_password_hash.php
 *
 * Alat bantu sekali pakai untuk membuat hash password yang valid,
 * supaya bisa ditempel ke kolom `password` pada tabel `users` di database.sql.
 *
 * CARA PAKAI:
 * 1. Taruh file ini di folder dashboard/, lalu akses lewat browser:
 *    http://localhost/toko_sparepart1/dashboard/generate_password_hash.php?password=admin123
 * 2. Salin hasil hash yang muncul, lalu tempel ke kolom `password`
 *    pada baris INSERT INTO users (...) di database.sql / phpMyAdmin.
 * 3. SETELAH SELESAI DIGUNAKAN, HAPUS FILE INI dari server (jangan
 *    dibiarkan menumpuk di server produksi karena bisa disalahgunakan
 *    untuk menebak-nebak hash dari password apa saja).
 */

$password = $_GET['password'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Generate Password Hash</title>
<style>
    body { font-family: -apple-system, Arial, sans-serif; max-width: 640px; margin: 60px auto; color: #1f2937; padding: 0 20px; }
    form { display: flex; gap: 10px; margin-bottom: 20px; }
    input[type="text"] { flex: 1; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; }
    button { padding: 10px 18px; background: #2563eb; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-size: 14px; }
    .result { background: #f3f4f6; padding: 16px; border-radius: 10px; word-break: break-all; font-family: monospace; font-size: 13px; margin-top: 10px; }
    .warn { background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: 8px; font-size: 13.5px; margin-top: 30px; }
</style>
</head>
<body>
    <h2>🔑 Generate Password Hash</h2>
    <p>Masukkan password yang ingin di-hash (contoh: <code>admin123</code>):</p>

    <form method="get">
        <input type="text" name="password" placeholder="Masukkan password..." value="<?= htmlspecialchars($password ?? '') ?>">
        <button type="submit">Generate</button>
    </form>

    <?php if ($password): ?>
        <p><strong>Password asli:</strong> <?= htmlspecialchars($password) ?></p>
        <p><strong>Hash (tempel ini ke kolom `password`):</strong></p>
        <div class="result"><?= htmlspecialchars(password_hash($password, PASSWORD_DEFAULT)) ?></div>
    <?php endif; ?>

    <div class="warn">
        ⚠️ Hapus file ini dari server setelah selesai dipakai — jangan dibiarkan
        bisa diakses publik, karena siapa pun yang menemukannya bisa membuat
        hash dari password tebakan mereka sendiri.
    </div>
</body>
</html>
