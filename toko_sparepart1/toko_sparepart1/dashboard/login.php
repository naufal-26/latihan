<?php
/**
 * Halaman Login
 * File: login.php
 *
 * LOGIC AUTENTIKASI:
 * Kredensial diverifikasi terhadap data/settings.json — file yang sama
 * dipakai oleh pengaturan.php (field 'akun.email' dan 'password_hash').
 * Jadi kalau Anda ubah email/password lewat halaman Pengaturan, login
 * di sini otomatis ikut berubah juga.
 *
 * Kalau nanti sudah pakai database, ganti blok "AMBIL DATA AKUN" dan
 * "PROSES LOGIN" di bawah dengan query ke tabel `users`.
 */

session_start();

$dataFile = __DIR__ . '/data/settings.json';

// ---------- Nilai default kalau settings.json belum ada ----------
function defaultAuth() {
    return [
        'email'         => 'admin@tokosparepart.com',
        // Password default: "admin123" (di-hash, jangan simpan plain text)
        'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
    ];
}

// ---------- AMBIL DATA AKUN ----------
function loadAuthData($file) {
    $default = defaultAuth();
    if (!file_exists($file)) {
        return $default;
    }
    $json = json_decode(file_get_contents($file), true);
    if (!is_array($json)) {
        return $default;
    }
    return [
        'email'         => $json['akun']['email'] ?? $default['email'],
        'password_hash' => $json['password_hash'] ?? $default['password_hash'],
    ];
}

$auth = loadAuthData($dataFile);

// ---------- Kalau sudah login, langsung lempar ke dashboard ----------
if (!empty($_SESSION['is_logged_in'])) {
    header('Location: index.php');
    exit;
}

// ---------- Kalau ada cookie "Ingat Saya" yang valid, login otomatis ----------
if (empty($_SESSION['is_logged_in']) && !empty($_COOKIE['remember_email']) && !empty($_COOKIE['remember_token'])) {
    // TODO (database): cocokkan token ini dengan token yang disimpan di tabel users, JANGAN simpan token statis seperti contoh di bawah
    $expectedToken = hash('sha256', $auth['email'] . $auth['password_hash']);
    if ($_COOKIE['remember_email'] === $auth['email'] && hash_equals($expectedToken, $_COOKIE['remember_token'])) {
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_email']   = $auth['email'];
        header('Location: index.php');
        exit;
    }
}

$error = '';

// ====================== PROSES LOGIN (POST) ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Tombol "Login sebagai Admin" mengisi otomatis kredensial admin default
    // lewat JavaScript lalu submit form yang sama — jadi diproses sebagai login biasa.
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($email === '' || $password === '') {
        $error = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // TODO (database): SELECT password_hash FROM users WHERE email = ?
        if (strcasecmp($email, $auth['email']) === 0 && password_verify($password, $auth['password_hash'])) {

            // Login sukses
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_email']  = $auth['email'];

            if ($remember) {
                // Cookie "Ingat saya" berlaku 30 hari.
                // TODO (database): idealnya token ini acak & disimpan di tabel users, bukan diturunkan dari hash password seperti contoh ini.
                $token = hash('sha256', $auth['email'] . $auth['password_hash']);
                setcookie('remember_email', $auth['email'], time() + 60 * 60 * 24 * 30, '/');
                setcookie('remember_token', $token, time() + 60 * 60 * 24 * 30, '/');
            }

            header('Location: index.php');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Sparepart Store</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

<div class="auth-page">

    <div class="auth-wrapper">

        <!-- ====================== PANEL KIRI ====================== -->
        <div class="auth-left">
            <div class="auth-logo">
                <div class="auth-logo-icon">⚙️</div>
                <div class="brand-text">
                    <span class="brand-title">SPAREPART</span>
                    <span class="brand-sub">STORE</span>
                </div>
            </div>

            <div class="auth-heading">
                <h1>Kelola toko sparepart<br>lebih mudah</h1>
                <p>Sistem manajemen toko sparepart yang simpel, cepat dan terorganisir.</p>
            </div>

            <!-- Ilustrasi dekoratif (CSS/emoji, tanpa perlu file gambar eksternal) -->
            <div class="auth-illustration">
                <span class="illu-icon illu-1">🛞</span>
                <span class="illu-icon illu-2">🔧</span>
                <span class="illu-icon illu-3">⚙️</span>
                <span class="illu-icon illu-4">🔩</span>
            </div>
        </div>

        <!-- ====================== PANEL KANAN (FORM LOGIN) ====================== -->
        <div class="auth-right">
            <h2 class="auth-title">Selamat Datang!</h2>
            <p class="auth-subtitle">Login untuk melanjutkan ke dashboard.</p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="post" class="auth-form" id="loginForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-icon-field">
                        <span class="input-icon">✉️</span>
                        <input type="email" id="email" name="email" placeholder="Masukkan email Anda"
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-field">
                        <span class="input-icon">🔒</span>
                        <input type="password" id="password" name="password" placeholder="Masukkan password Anda" required>
                        <span class="eye-icon" data-target="password">👁️</span>
                    </div>
                </div>

                <div class="auth-row">
                    <label class="checkbox-row">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="link">Lupa password?</a>
                </div>

                <button type="submit" class="btn-primary btn-block">Login</button>

                <div class="auth-divider"><span>atau</span></div>

                <button type="button" class="btn-outline btn-block" id="btnLoginAdmin">
                    🛡️ Login sebagai Admin
                </button>
            </form>
        </div>

    </div>

    <p class="auth-copyright">© <?= date('Y') ?> Sparepart Store. Semua hak dilindungi.</p>
</div>

<script>
// Toggle tampil/sembunyikan password
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

// "Login sebagai Admin": isi kredensial admin default lalu submit form yang sama.
// Catatan: ini hanya jalan pintas demo. Untuk produksi sungguhan, sebaiknya
// dihapus atau diganti dengan mekanisme SSO/role-based login yang lebih aman,
// karena kredensial default ini terlihat di source code halaman.
document.getElementById('btnLoginAdmin').addEventListener('click', function () {
    document.getElementById('email').value = 'admin@tokosparepart.com';
    document.getElementById('password').value = 'admin123';
    document.getElementById('loginForm').submit();
});
</script>

</body>
</html>
