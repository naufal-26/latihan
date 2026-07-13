<?php
/**
 * lupa_password.php
 * Reset password tanpa email — verifikasi via nama toko + email terdaftar.
 * Cocok untuk sistem lokal (XAMPP) yang tidak punya mail server.
 */

session_start();
require_once __DIR__ . '/db.php';

$step   = 1;      // 1 = form verifikasi, 2 = form password baru, 3 = sukses
$error  = '';
$email  = '';

// Ambil nama toko dari pengaturan
$namaToko = 'Toko Sparepart';
$settingRow = $pdo->query("SELECT nama_toko FROM pengaturan LIMIT 1")->fetch();
if ($settingRow) $namaToko = $settingRow['nama_toko'];

// ====================== STEP 1: Verifikasi email ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === '1') {
    $emailInput    = trim($_POST['email'] ?? '');
    $namaTokoBukti = trim($_POST['nama_toko'] ?? '');

    if ($emailInput === '' || $namaTokoBukti === '') {
        $error = 'Semua field wajib diisi.';
    } elseif (strtolower($namaTokoBukti) !== strtolower($namaToko)) {
        $error = 'Nama toko tidak sesuai.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$emailInput]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Email tidak ditemukan.';
        } else {
            // Simpan token sementara di session (berlaku 10 menit)
            $_SESSION['reset_user_id'] = $user['id'];
            $_SESSION['reset_email']   = $emailInput;
            $_SESSION['reset_expire']  = time() + 600;
            $step  = 2;
            $email = $emailInput;
        }
    }
}

// ====================== STEP 2: Set password baru ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['step']) && $_POST['step'] === '2') {
    $userId  = $_SESSION['reset_user_id'] ?? null;
    $expire  = $_SESSION['reset_expire']  ?? 0;

    if (!$userId || time() > $expire) {
        $error = 'Sesi reset sudah kadaluarsa. Silakan mulai ulang.';
        $step  = 1;
        unset($_SESSION['reset_user_id'], $_SESSION['reset_email'], $_SESSION['reset_expire']);
    } else {
        $pw1 = $_POST['password_baru'] ?? '';
        $pw2 = $_POST['konfirmasi']    ?? '';

        if (strlen($pw1) < 6) {
            $error = 'Password minimal 6 karakter.';
            $step  = 2;
            $email = $_SESSION['reset_email'];
        } elseif ($pw1 !== $pw2) {
            $error = 'Konfirmasi password tidak cocok.';
            $step  = 2;
            $email = $_SESSION['reset_email'];
        } else {
            $hash = password_hash($pw1, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ?, remember_token = NULL WHERE id = ?")
                ->execute([$hash, $userId]);

            unset($_SESSION['reset_user_id'], $_SESSION['reset_email'], $_SESSION['reset_expire']);
            $step = 3;
        }
    }
}

// Kalau session masih ada (kembali ke step 2)
if ($step === 1 && !empty($_SESSION['reset_user_id']) && time() <= ($_SESSION['reset_expire'] ?? 0)) {
    $step  = 2;
    $email = $_SESSION['reset_email'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lupa Password - Sparepart Store</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
<div class="auth-page">
  <div class="auth-wrapper" style="max-width:520px;margin:0 auto;">

    <div class="auth-left" style="display:none;"></div>

    <div class="auth-right" style="flex:1;padding:52px 48px;">

      <!-- STEP 1: Verifikasi -->
      <?php if ($step === 1): ?>
        <div style="margin-bottom:24px;">
          <a href="login.php" class="form-back-btn" style="margin-bottom:12px;">← Kembali ke Login</a>
          <h2 class="auth-title">Lupa Password?</h2>
          <p class="auth-subtitle">Masukkan nama toko dan email akun Anda untuk verifikasi.</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:18px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="lupa_password.php" method="post" class="auth-form">
          <input type="hidden" name="step" value="1">
          <div class="form-group">
            <label>Nama Toko</label>
            <div class="input-icon-field">
              <span class="input-icon">🏬</span>
              <input type="text" name="nama_toko" placeholder="Masukkan nama toko Anda" required>
            </div>
            <small style="color:#9ca3af;font-size:12px;margin-top:4px;display:block;">
              Hint: nama toko ada di halaman Pengaturan
            </small>
          </div>
          <div class="form-group" style="margin-top:16px;">
            <label>Email Akun</label>
            <div class="input-icon-field">
              <span class="input-icon">✉️</span>
              <input type="email" name="email" placeholder="Email yang terdaftar" required>
            </div>
          </div>
          <button type="submit" class="btn-primary btn-block" style="margin-top:20px;">Verifikasi →</button>
        </form>

      <!-- STEP 2: Set password baru -->
      <?php elseif ($step === 2): ?>
        <div style="margin-bottom:24px;">
          <h2 class="auth-title">Buat Password Baru</h2>
          <p class="auth-subtitle">Akun: <strong><?= htmlspecialchars($email) ?></strong></p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-error" style="margin-bottom:18px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="lupa_password.php" method="post" class="auth-form">
          <input type="hidden" name="step" value="2">
          <div class="form-group">
            <label>Password Baru</label>
            <div class="input-icon-field">
              <span class="input-icon">🔒</span>
              <input type="password" id="pw1" name="password_baru" placeholder="Minimal 6 karakter" minlength="6" required>
              <span class="eye-icon" data-target="pw1">👁️</span>
            </div>
          </div>
          <div class="form-group" style="margin-top:16px;">
            <label>Konfirmasi Password</label>
            <div class="input-icon-field">
              <span class="input-icon">🔒</span>
              <input type="password" id="pw2" name="konfirmasi" placeholder="Ulangi password baru" minlength="6" required>
              <span class="eye-icon" data-target="pw2">👁️</span>
            </div>
          </div>

          <!-- Indikator kekuatan password -->
          <div style="margin-top:10px;">
            <div style="height:4px;background:#e5e7eb;border-radius:4px;overflow:hidden;">
              <div id="pw-strength-bar" style="height:100%;width:0;border-radius:4px;transition:all 0.3s;"></div>
            </div>
            <small id="pw-strength-text" style="font-size:12px;color:#9ca3af;"></small>
          </div>

          <button type="submit" class="btn-primary btn-block" style="margin-top:20px;">💾 Simpan Password Baru</button>
          <a href="lupa_password.php" style="display:block;text-align:center;margin-top:12px;font-size:13px;color:#6b7280;text-decoration:none;">Mulai Ulang Verifikasi</a>
        </form>

      <!-- STEP 3: Sukses -->
      <?php elseif ($step === 3): ?>
        <div style="text-align:center;padding:20px 0;">
          <div style="font-size:56px;margin-bottom:16px;">✅</div>
          <h2 class="auth-title">Password Berhasil Diubah!</h2>
          <p class="auth-subtitle" style="margin-bottom:28px;">Silakan login menggunakan password baru Anda.</p>
          <a href="login.php" class="btn-primary" style="display:inline-block;text-decoration:none;padding:12px 32px;border-radius:10px;font-size:14px;">
            → Login Sekarang
          </a>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <p class="auth-copyright">© <?= date('Y') ?> Sparepart Store. Semua hak dilindungi.</p>
</div>

<script>
// Toggle show/hide password
document.querySelectorAll('.eye-icon').forEach(icon => {
    icon.addEventListener('click', () => {
        const inp = document.getElementById(icon.dataset.target);
        if (inp) {
            inp.type = inp.type === 'password' ? 'text' : 'password';
            icon.textContent = inp.type === 'password' ? '👁️' : '🙈';
        }
    });
});

// Indikator kekuatan password
const pw1 = document.getElementById('pw1');
const bar = document.getElementById('pw-strength-bar');
const txt = document.getElementById('pw-strength-text');

if (pw1 && bar && txt) {
    pw1.addEventListener('input', () => {
        const v = pw1.value;
        let score = 0;
        if (v.length >= 6)  score++;
        if (v.length >= 10) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;

        const levels = [
            { pct: '20%',  color: '#dc2626', label: 'Sangat Lemah' },
            { pct: '40%',  color: '#f97316', label: 'Lemah' },
            { pct: '60%',  color: '#eab308', label: 'Sedang' },
            { pct: '80%',  color: '#22c55e', label: 'Kuat' },
            { pct: '100%', color: '#16a34a', label: 'Sangat Kuat' },
        ];
        const lvl = levels[Math.min(score, 4)];
        bar.style.width = v.length ? lvl.pct : '0';
        bar.style.background = lvl.color;
        txt.textContent = v.length ? lvl.label : '';
        txt.style.color = lvl.color;
    });
}
</script>

<style>
/* Override auth-wrapper untuk halaman single-panel */
.auth-wrapper {
    max-width: 480px !important;
    border-radius: 20px;
}
.auth-left {
    display: none !important;
}
.auth-right {
    border-radius: 20px !important;
}
</style>
</body>
</html>
