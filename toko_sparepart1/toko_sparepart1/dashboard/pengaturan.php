<?php
/**
 * Halaman Pengaturan
 * File: pengaturan.php
 *
 * LOGIC PENYIMPANAN:
 * Karena belum terhubung database, pengaturan disimpan ke file
 * data/settings.json (otomatis dibuat). Ini hanya solusi sementara
 * agar form benar-benar berfungsi (data tersimpan & bisa diubah).
 *
 * Kalau nanti sudah pakai database, ganti bagian loadSettings() /
 * saveSettings() / blok password dengan query ke tabel `pengaturan`
 * dan `users` (lihat catatan TODO di setiap blok).
 */

session_start();

$dataFile = __DIR__ . '/data/settings.json';

// ---------- Helper: nilai default kalau file belum ada ----------
function defaultSettings() {
    return [
        'akun' => [
            'nama_toko'   => 'Toko Sparepart',
            'email'       => 'admin@tokosparepart.com',
            'nama_admin'  => 'Admin',
            'no_telepon'  => '0812-3456-7890',
            'alamat_toko' => 'Jl. Merdeka No.123, Jakarta Selatan, DKI Jakarta',
        ],
        'umum' => [
            'mata_uang'          => 'Rupiah (IDR)',
            'pajak'              => '11',
            'format_tanggal'     => 'DD/MM/YYYY',
            'stok_minimum_aktif' => true,
        ],
        // Password default: "admin123" (di-hash, jangan simpan plain text)
        'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
    ];
}

// ---------- Helper: baca & tulis file JSON ----------
function loadSettings($file) {
    $default = defaultSettings();
    if (!file_exists($file)) {
        return $default;
    }
    $json = file_get_contents($file);
    $data = json_decode($json, true);
    if (!is_array($data)) {
        return $default;
    }
    // Gabungkan dengan default supaya field yang belum ada tetap terisi
    return array_replace_recursive($default, $data);
}

function saveSettings($file, array $data) {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    return file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX) !== false;
}

$settings = loadSettings($dataFile);

// ====================== PROSES FORM (POST) ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---------- FORM 1: Pengaturan Akun ----------
    if (isset($_POST['simpan_akun'])) {
        $nama_toko   = trim($_POST['nama_toko'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $nama_admin  = trim($_POST['nama_admin'] ?? '');
        $no_telepon  = trim($_POST['no_telepon'] ?? '');
        $alamat_toko = trim($_POST['alamat_toko'] ?? '');

        if ($nama_toko === '' || $nama_admin === '') {
            $_SESSION['flash_error_akun'] = 'Nama Toko dan Nama Admin wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error_akun'] = 'Format email tidak valid.';
        } else {
            // TODO (database): UPDATE toko SET nama_toko=?, email=?, nama_admin=?, no_telepon=?, alamat_toko=? WHERE id=?
            $settings['akun'] = [
                'nama_toko'   => $nama_toko,
                'email'       => $email,
                'nama_admin'  => $nama_admin,
                'no_telepon'  => $no_telepon,
                'alamat_toko' => $alamat_toko,
            ];
            saveSettings($dataFile, $settings);
            $_SESSION['flash_success_akun'] = 'Pengaturan akun berhasil disimpan.';
        }

        header('Location: pengaturan.php#akun');
        exit;
    }

    // ---------- FORM 2: Pengaturan Umum ----------
    if (isset($_POST['simpan_umum'])) {
        $mata_uang      = trim($_POST['mata_uang'] ?? '');
        $pajak          = trim($_POST['pajak'] ?? '0');
        $format_tanggal = trim($_POST['format_tanggal'] ?? '');
        $stok_minimum   = isset($_POST['stok_minimum_aktif']); // checkbox: ada = true, tidak ada = false

        if (!is_numeric($pajak) || $pajak < 0 || $pajak > 100) {
            $_SESSION['flash_error_umum'] = 'Pajak harus berupa angka antara 0 - 100.';
        } else {
            // TODO (database): UPDATE pengaturan SET mata_uang=?, pajak=?, format_tanggal=?, stok_minimum_aktif=? WHERE id=?
            $settings['umum'] = [
                'mata_uang'          => $mata_uang,
                'pajak'              => $pajak,
                'format_tanggal'     => $format_tanggal,
                'stok_minimum_aktif' => $stok_minimum,
            ];
            saveSettings($dataFile, $settings);
            $_SESSION['flash_success_umum'] = 'Pengaturan umum berhasil disimpan.';
        }

        header('Location: pengaturan.php#umum');
        exit;
    }

    // ---------- FORM 3: Keamanan (Ubah Password) ----------
    if (isset($_POST['ubah_password'])) {
        $password_lama       = $_POST['password_lama'] ?? '';
        $password_baru       = $_POST['password_baru'] ?? '';
        $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

        // TODO (database): ganti password_verify() di bawah dengan ambil hash dari tabel users WHERE id = sesi_user_id
        if (!password_verify($password_lama, $settings['password_hash'])) {
            $_SESSION['flash_error_keamanan'] = 'Password lama tidak sesuai.';
        } elseif (strlen($password_baru) < 6) {
            $_SESSION['flash_error_keamanan'] = 'Password baru minimal 6 karakter.';
        } elseif ($password_baru !== $konfirmasi_password) {
            $_SESSION['flash_error_keamanan'] = 'Konfirmasi password tidak cocok dengan password baru.';
        } else {
            // TODO (database): UPDATE users SET password = ? WHERE id = sesi_user_id
            $settings['password_hash'] = password_hash($password_baru, PASSWORD_DEFAULT);
            saveSettings($dataFile, $settings);
            $_SESSION['flash_success_keamanan'] = 'Password berhasil diubah.';
        }

        header('Location: pengaturan.php#keamanan');
        exit;
    }
}

// ====================== AMBIL PESAN FLASH (lalu hapus dari session) ======================
$flash = [
    'success_akun'     => $_SESSION['flash_success_akun']     ?? null,
    'error_akun'       => $_SESSION['flash_error_akun']       ?? null,
    'success_umum'     => $_SESSION['flash_success_umum']     ?? null,
    'error_umum'       => $_SESSION['flash_error_umum']       ?? null,
    'success_keamanan' => $_SESSION['flash_success_keamanan'] ?? null,
    'error_keamanan'   => $_SESSION['flash_error_keamanan']   ?? null,
];
unset(
    $_SESSION['flash_success_akun'], $_SESSION['flash_error_akun'],
    $_SESSION['flash_success_umum'], $_SESSION['flash_error_umum'],
    $_SESSION['flash_success_keamanan'], $_SESSION['flash_error_keamanan']
);

$akun = $settings['akun'];
$umum = $settings['umum'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengaturan - Sparepart Store</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="layout">

    <!-- ====================== SIDEBAR ====================== -->
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">⚙️</div>
            <div class="brand-text">
                <span class="brand-title">SPAREPART</span>
                <span class="brand-sub">STORE</span>
            </div>
        </div>

        <nav class="nav">
            <a href="index.php" class="nav-item"><span class="nav-icon">▦</span> Dashboard</a>
            <a href="penjualan.php" class="nav-item"><span class="nav-icon">🛒</span> Penjualan</a>
            <a href="produk.php" class="nav-item"><span class="nav-icon">📦</span> Produk</a>
            <a href="stok.php" class="nav-item"><span class="nav-icon">🗳️</span> Stok</a>
            <a href="pembelian.php" class="nav-item"><span class="nav-icon">📋</span> Pembelian</a>
            <a href="pelanggan.php" class="nav-item"><span class="nav-icon">👥</span> Pelanggan</a>
            <a href="laporan.php" class="nav-item"><span class="nav-icon">📊</span> Laporan</a>
            <a href="pengaturan.php" class="nav-item active"><span class="nav-icon">⚙️</span> Pengaturan</a>
        </nav>

        <div class="sidebar-footer">
            <div class="footer-icon">🔧</div>
            <strong>Toko Sparepart</strong>
            <p>Solusi terbaik untuk kendaraan Anda.</p>
        </div>
    </aside>

    <!-- ====================== MAIN ====================== -->
    <div class="main">

        <!-- TOPBAR -->
        <header class="topbar">
            <div class="topbar-left">
                <span class="hamburger">☰</span>
                <h1>Pengaturan</h1>
            </div>
            <div class="topbar-right">
                <span class="bell">🔔<span class="dot"></span></span>
                <div class="avatar">AD</div>
                <span class="admin-name">Admin ▾</span>
            </div>
        </header>

        <div class="content">

            <!-- ====================== PENGATURAN AKUN ====================== -->
            <div class="card settings-card" id="akun">
                <div class="settings-header">
                    <div class="settings-icon icon-blue">🏬</div>
                    <div>
                        <h2>Pengaturan Akun</h2>
                        <p>Kelola informasi akun dan toko Anda.</p>
                    </div>
                </div>

                <?php if ($flash['success_akun']): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($flash['success_akun']) ?></div>
                <?php endif; ?>
                <?php if ($flash['error_akun']): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($flash['error_akun']) ?></div>
                <?php endif; ?>

                <form action="pengaturan.php#akun" method="post" class="settings-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama_toko">Nama Toko</label>
                            <input type="text" id="nama_toko" name="nama_toko" value="<?= htmlspecialchars($akun['nama_toko']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($akun['email']) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nama_admin">Nama Admin</label>
                            <input type="text" id="nama_admin" name="nama_admin" value="<?= htmlspecialchars($akun['nama_admin']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="no_telepon">No. Telepon</label>
                            <input type="text" id="no_telepon" name="no_telepon" value="<?= htmlspecialchars($akun['no_telepon']) ?>">
                        </div>
                    </div>

                    <div class="form-row form-row-full">
                        <div class="form-group">
                            <label for="alamat_toko">Alamat Toko</label>
                            <textarea id="alamat_toko" name="alamat_toko" rows="2"><?= htmlspecialchars($akun['alamat_toko']) ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="simpan_akun" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <!-- ====================== PENGATURAN UMUM ====================== -->
            <div class="card settings-card" id="umum">
                <div class="settings-header">
                    <div class="settings-icon icon-green">⚙️</div>
                    <div>
                        <h2>Pengaturan Umum</h2>
                        <p>Sesuaikan preferensi sistem sesuai kebutuhan.</p>
                    </div>
                </div>

                <?php if ($flash['success_umum']): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($flash['success_umum']) ?></div>
                <?php endif; ?>
                <?php if ($flash['error_umum']): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($flash['error_umum']) ?></div>
                <?php endif; ?>

                <form action="pengaturan.php#umum" method="post" class="settings-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="mata_uang">Mata Uang</label>
                            <select id="mata_uang" name="mata_uang">
                                <option <?= $umum['mata_uang'] === 'Rupiah (IDR)' ? 'selected' : '' ?>>Rupiah (IDR)</option>
                                <option <?= $umum['mata_uang'] === 'US Dollar (USD)' ? 'selected' : '' ?>>US Dollar (USD)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="pajak">Pajak (%)</label>
                            <input type="number" id="pajak" name="pajak" value="<?= htmlspecialchars($umum['pajak']) ?>" min="0" max="100" step="0.1">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="format_tanggal">Format Tanggal</label>
                            <select id="format_tanggal" name="format_tanggal">
                                <option <?= $umum['format_tanggal'] === 'DD/MM/YYYY' ? 'selected' : '' ?>>DD/MM/YYYY</option>
                                <option <?= $umum['format_tanggal'] === 'MM/DD/YYYY' ? 'selected' : '' ?>>MM/DD/YYYY</option>
                                <option <?= $umum['format_tanggal'] === 'YYYY-MM-DD' ? 'selected' : '' ?>>YYYY-MM-DD</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Stok Minimum</label>
                            <div class="toggle-row">
                                <label class="toggle-switch">
                                    <input type="checkbox" name="stok_minimum_aktif" <?= $umum['stok_minimum_aktif'] ? 'checked' : '' ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="toggle-label">Aktifkan peringatan stok minimum</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="simpan_umum" class="btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <!-- ====================== KEAMANAN ====================== -->
            <div class="card settings-card" id="keamanan">
                <div class="settings-header">
                    <div class="settings-icon icon-purple">🔒</div>
                    <div>
                        <h2>Keamanan</h2>
                        <p>Ubah password akun Anda secara berkala untuk keamanan.</p>
                    </div>
                </div>

                <?php if ($flash['success_keamanan']): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($flash['success_keamanan']) ?></div>
                <?php endif; ?>
                <?php if ($flash['error_keamanan']): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($flash['error_keamanan']) ?></div>
                <?php endif; ?>

                <form action="pengaturan.php#keamanan" method="post" class="settings-form">
                    <div class="form-row form-row-3">
                        <div class="form-group">
                            <label for="password_lama">Password Lama</label>
                            <div class="password-field">
                                <input type="password" id="password_lama" name="password_lama" placeholder="Masukkan password lama" required>
                                <span class="eye-icon" data-target="password_lama">👁️</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_baru">Password Baru</label>
                            <div class="password-field">
                                <input type="password" id="password_baru" name="password_baru" placeholder="Masukkan password baru" minlength="6" required>
                                <span class="eye-icon" data-target="password_baru">👁️</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="konfirmasi_password">Konfirmasi Password</label>
                            <div class="password-field">
                                <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Konfirmasi password baru" minlength="6" required>
                                <span class="eye-icon" data-target="konfirmasi_password">👁️</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="ubah_password" class="btn-primary">Ubah Password</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<script>
// Toggle tampil/sembunyikan password saat ikon mata diklik
document.querySelectorAll('.eye-icon').forEach(function (icon) {
    icon.addEventListener('click', function () {
        var input = document.getElementById(icon.getAttribute('data-target'));
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = '🙈';
        } else {
            input.type = 'password';
            icon.textContent = '👁️';
        }
    });
});
</script>

</body>
</html>
