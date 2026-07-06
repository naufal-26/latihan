<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';

// ====================== HANDLE POST: SIMPAN TRANSAKSI ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal     = $_POST['tanggal'] ?? date('Y-m-d');
    $pelangganId = !empty($_POST['pelanggan_id']) ? (int)$_POST['pelanggan_id'] : null;
    $kasirId     = $_SESSION['user_id'];
    $metode      = $_POST['metode_pembayaran'] ?? 'Tunai';
    $diskon      = (float)($_POST['diskon'] ?? 0);

    $produkIds = $_POST['produk_id'] ?? [];
    $qtys      = $_POST['qty'] ?? [];
    $hargas    = $_POST['harga_satuan'] ?? [];

    // Kumpulkan item valid
    $items = [];
    $subtotalTotal = 0;
    $errors = [];

    for ($i = 0; $i < count($produkIds); $i++) {
        if (empty($produkIds[$i])) continue;
        $qty   = (int)($qtys[$i] ?? 0);
        $harga = (float)($hargas[$i] ?? 0);
        if ($qty <= 0) { $errors[] = "Qty item baris " . ($i + 1) . " harus lebih dari 0."; continue; }

        // Cek stok tersedia
        $stokRow = $pdo->prepare("SELECT stok, nama_produk FROM produk WHERE id=?");
        $stokRow->execute([$produkIds[$i]]);
        $pr = $stokRow->fetch();
        if (!$pr) { $errors[] = "Produk tidak ditemukan."; continue; }
        if ($pr['stok'] < $qty) {
            $errors[] = "Stok '{$pr['nama_produk']}' tidak cukup (tersedia: {$pr['stok']}).";
            continue;
        }

        $subtotal = $qty * $harga;
        $subtotalTotal += $subtotal;
        $items[] = ['produk_id' => (int)$produkIds[$i], 'qty' => $qty, 'harga' => $harga, 'subtotal' => $subtotal];
    }

    if (empty($items) && empty($errors)) $errors[] = 'Tambahkan minimal 1 produk ke transaksi.';

    if (empty($errors)) {
        $totalAkhir = max(0, $subtotalTotal - $diskon);

        // Generate no_invoice: INV/YYYY/MM/XXXX
        $prefix = 'INV/' . date('Y/m') . '/';
        $last   = $pdo->query("SELECT no_invoice FROM penjualan WHERE no_invoice LIKE '$prefix%' ORDER BY id DESC LIMIT 1")->fetchColumn();
        $num    = $last ? str_pad((int)substr($last, -4) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        $noInv  = $prefix . $num;

        $pdo->prepare("INSERT INTO penjualan (no_invoice, pelanggan_id, user_id, tanggal, total, diskon, metode_pembayaran, status) VALUES (?,?,?,?,?,?,?,'Selesai')")
            ->execute([$noInv, $pelangganId, $kasirId, $tanggal, $totalAkhir, $diskon, $metode]);
        $penjualanId = $pdo->lastInsertId();

        $stmtD = $pdo->prepare("INSERT INTO penjualan_detail (penjualan_id, produk_id, qty, harga_satuan, subtotal) VALUES (?,?,?,?,?)");
        $stmtS = $pdo->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");
        foreach ($items as $item) {
            $stmtD->execute([$penjualanId, $item['produk_id'], $item['qty'], $item['harga'], $item['subtotal']]);
            $stmtS->execute([$item['qty'], $item['produk_id']]);
        }

        $_SESSION['flash'] = "Transaksi $noInv berhasil disimpan. Total: Rp " . number_format($totalAkhir, 0, ',', '.');
        $_SESSION['flash_type'] = 'success';
        header('Location: penjualan.php');
        exit;
    }
}

// ====================== DATA UNTUK FORM ======================
$pelangganList = $pdo->query("SELECT id, nama FROM pelanggan ORDER BY nama ASC")->fetchAll();
$produkList    = $pdo->query("SELECT id, nama_produk AS nama, harga_jual AS harga, stok FROM produk WHERE stok > 0 ORDER BY nama_produk ASC")->fetchAll();
$produkJson    = json_encode($produkList, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transaksi Baru - Sparepart Store</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="form-transaksi-page">
<div class="layout">
  <aside class="sidebar">
    <div class="brand"><div class="brand-icon">⚙️</div><div class="brand-text"><span class="brand-title">SPAREPART</span><span class="brand-sub">STORE</span></div></div>
    <nav class="nav">
      <a href="index.php" class="nav-item"><span class="nav-icon">▦</span> Dashboard</a>
      <a href="penjualan.php" class="nav-item active"><span class="nav-icon">🛒</span> Penjualan</a>
      <a href="produk.php" class="nav-item"><span class="nav-icon">📦</span> Produk</a>
      <a href="stok.php" class="nav-item"><span class="nav-icon">🗳️</span> Stok</a>
      <a href="pembelian.php" class="nav-item"><span class="nav-icon">📋</span> Pembelian</a>
      <a href="pelanggan.php" class="nav-item"><span class="nav-icon">👥</span> Pelanggan</a>
      <a href="laporan.php" class="nav-item"><span class="nav-icon">📊</span> Laporan</a>
      <a href="pengaturan.php" class="nav-item"><span class="nav-icon">⚙️</span> Pengaturan</a>
    </nav>
    <div class="sidebar-footer"><div class="footer-icon">🔧</div><strong>Toko Sparepart</strong><p>Solusi terbaik untuk kendaraan Anda.</p></div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-left"><span class="hamburger">☰</span><h1>Transaksi Baru</h1></div>
      <div class="topbar-right">
        <span class="bell">🔔<span class="dot"></span></span>
        <div class="avatar">AD</div><span class="admin-name">Admin ▾</span>
      </div>
    </header>

    <div class="content">
      <a href="penjualan.php" class="form-back-btn">← Kembali ke Penjualan</a>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom:18px;">
          <?php foreach ($errors as $e): ?><div>• <?= htmlspecialchars($e) ?></div><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="post" id="formPenjualan">
        <div class="card" style="margin-bottom:20px;">
          <div class="card-header"><h2>Informasi Transaksi</h2></div>

          <div class="form-row" style="padding:0 0 8px;">
            <div class="form-group">
              <label>Tanggal</label>
              <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="form-group">
              <label>Pelanggan</label>
              <select name="pelanggan_id">
                <option value="">— Tanpa Pelanggan (Umum) —</option>
                <?php foreach ($pelangganList as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nama']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label>Metode Pembayaran</label>
              <select name="metode_pembayaran">
                <option>Tunai</option>
                <option>Transfer</option>
                <option>QRIS</option>
                <option>Kartu Debit/Kredit</option>
              </select>
            </div>
            <div class="form-group">
              <label>Diskon (Rp)</label>
              <input type="number" name="diskon" id="inp-diskon" min="0" value="0" oninput="hitung()">
            </div>
          </div>
        </div>

        <!-- PRODUK -->
        <div class="card">
          <div class="card-header"><h2>Daftar Produk</h2></div>

          <table class="item-table">
            <thead>
              <tr>
                <th>Produk</th>
                <th style="width:80px">Stok</th>
                <th style="width:80px">Qty</th>
                <th style="width:160px">Harga Satuan (Rp)</th>
                <th style="width:160px">Subtotal</th>
                <th style="width:36px"></th>
              </tr>
            </thead>
            <tbody id="item-tbody"></tbody>
          </table>

          <button type="button" class="btn-add-row" onclick="addRow()">+ Tambah Produk</button>

          <div class="total-box">
            <div class="total-row"><span>Subtotal</span><span id="lbl-subtotal">Rp 0</span></div>
            <div class="total-row"><span>Diskon</span><span id="lbl-diskon">Rp 0</span></div>
            <div class="total-row grand"><span>TOTAL</span><span id="lbl-total">Rp 0</span></div>
          </div>

          <div class="form-actions" style="margin-top:20px;">
            <a href="penjualan.php" class="btn-outline" style="padding:11px 20px;border-radius:9px;font-size:13.5px;font-weight:600;color:#374151;text-decoration:none;">Batal</a>
            <button type="submit" class="btn-primary" style="padding:11px 28px;font-size:14px;">💾 Simpan Transaksi</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const produkData = <?= $produkJson ?>;
const produkMap  = {};
produkData.forEach(p => produkMap[p.id] = p);

let rowCount = 0;

function fmt(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

function addRow() {
    rowCount++;
    const opts = produkData.map(p =>
        `<option value="${p.id}" data-harga="${p.harga}" data-stok="${p.stok}">${p.nama} (Stok: ${p.stok})</option>`
    ).join('');

    const tr = document.createElement('tr');
    tr.id = 'row-' + rowCount;
    tr.innerHTML = `
        <td>
            <select name="produk_id[]" onchange="isiHarga(this)" required style="min-width:180px;">
                <option value="">— Pilih Produk —</option>
                ${opts}
            </select>
        </td>
        <td><span class="lbl-stok text-muted">-</span></td>
        <td><input type="number" name="qty[]" class="inp-qty" min="1" value="1" oninput="hitung()" style="width:70px;"></td>
        <td><input type="number" name="harga_satuan[]" class="inp-harga" min="0" value="0" oninput="hitung()" style="width:140px;"></td>
        <td><span class="lbl-sub text-muted">Rp 0</span></td>
        <td><button type="button" onclick="removeRow('row-${rowCount}')" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:16px;">✕</button></td>
    `;
    document.getElementById('item-tbody').appendChild(tr);
    hitung();
}

function removeRow(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
    hitung();
}

function isiHarga(select) {
    const opt = select.options[select.selectedIndex];
    const row = select.closest('tr');
    if (!opt.value) { row.querySelector('.inp-harga').value = 0; row.querySelector('.lbl-stok').textContent = '-'; return; }
    const harga = parseFloat(opt.dataset.harga) || 0;
    const stok  = parseInt(opt.dataset.stok) || 0;
    row.querySelector('.inp-harga').value = harga;
    row.querySelector('.lbl-stok').textContent = stok;
    row.querySelector('.inp-qty').max = stok;
    hitung();
}

function hitung() {
    let subtotal = 0;
    document.querySelectorAll('#item-tbody tr').forEach(tr => {
        const qty   = parseFloat(tr.querySelector('.inp-qty')?.value) || 0;
        const harga = parseFloat(tr.querySelector('.inp-harga')?.value) || 0;
        const sub   = qty * harga;
        subtotal += sub;
        const lbl = tr.querySelector('.lbl-sub');
        if (lbl) lbl.textContent = fmt(sub);
    });
    const diskon = parseFloat(document.getElementById('inp-diskon')?.value) || 0;
    const total  = Math.max(0, subtotal - diskon);
    document.getElementById('lbl-subtotal').textContent = fmt(subtotal);
    document.getElementById('lbl-diskon').textContent   = fmt(diskon);
    document.getElementById('lbl-total').textContent    = fmt(total);
}

// Tambah 1 baris kosong saat halaman dibuka
addRow();
</script>
</body>
</html>
