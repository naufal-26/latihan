<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: pembelian.php'); exit; }

// Ambil data pembelian
$stmt = $pdo->prepare("
    SELECT pb.*, s.nama_supplier, COALESCE(u.nama,'-') AS nama_user
    FROM pembelian pb
    LEFT JOIN supplier s ON s.id = pb.supplier_id
    LEFT JOIN users u ON u.id = pb.user_id
    WHERE pb.id = ?
");
$stmt->execute([$id]);
$trx = $stmt->fetch();

if (!$trx) { header('Location: pembelian.php'); exit; }

// Ambil detail produk
$stmt = $pdo->prepare("
    SELECT pd.qty, pd.harga_satuan, pd.subtotal, pr.nama_produk, pr.icon
    FROM pembelian_detail pd
    JOIN produk pr ON pr.id = pd.produk_id
    WHERE pd.pembelian_id = ?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();

function rp($n){ return 'Rp '.number_format($n,0,',','.'); }

$bulanIndo = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
              '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
function tglIndo($tgl, $bln){
    [$y,$m,$d] = explode('-',$tgl);
    return (int)$d.' '.($bln[$m]??$m).' '.$y;
}

function statusBadge($s){
    $map=['Selesai'=>'badge-success','Proses'=>'badge-blue','Dibatalkan'=>'badge-red'];
    return $map[$s]??'badge-gray';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Pembelian - Sparepart Store</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="form-transaksi-page">
<div class="layout">
  <aside class="sidebar">
    <div class="brand"><div class="brand-icon">⚙️</div><div class="brand-text"><span class="brand-title">SPAREPART</span><span class="brand-sub">STORE</span></div></div>
    <nav class="nav">
      <a href="index.php" class="nav-item"><span class="nav-icon">▦</span> Dashboard</a>
      <a href="penjualan.php" class="nav-item"><span class="nav-icon">🛒</span> Penjualan</a>
      <a href="produk.php" class="nav-item"><span class="nav-icon">📦</span> Produk</a>
      <a href="stok.php" class="nav-item"><span class="nav-icon">🗳️</span> Stok</a>
      <a href="pembelian.php" class="nav-item active"><span class="nav-icon">📋</span> Pembelian</a>
      <a href="pelanggan.php" class="nav-item"><span class="nav-icon">👥</span> Pelanggan</a>
      <a href="laporan.php" class="nav-item"><span class="nav-icon">📊</span> Laporan</a>
      <a href="pengaturan.php" class="nav-item"><span class="nav-icon">⚙️</span> Pengaturan</a>
    </nav>
    <div class="sidebar-footer"><div class="footer-icon">🔧</div><strong>Toko Sparepart</strong><p>Solusi terbaik untuk kendaraan Anda.</p></div>
  </aside>

  <div class="main">
    <header class="topbar">
      <div class="topbar-left"><span class="hamburger">☰</span><h1>Detail Pembelian</h1></div>
      <div class="topbar-right">
        <span class="bell">🔔<span class="dot"></span></span>
        <div class="avatar"><?php $__n=$_SESSION["user_nama"]??"AD"; $__p=explode(" ",$__n); echo mb_strtoupper(mb_substr($__p[0],0,1).(isset($__p[1])?mb_substr($__p[1],0,1):"")); ?></div>
        <div class="admin-dropdown">
          <span class="admin-name" onclick="toggleAdminMenu()"><?= htmlspecialchars($_SESSION['user_nama'] ?? 'Admin') ?> ▾</span>
          <div class="admin-menu" id="adminMenu">
            <a href="pengaturan.php">⚙️ Pengaturan</a>
            <a href="logout.php">🚪 Keluar</a>
          </div>
        </div>
      </div>
    </header>

    <div class="content">
      <a href="pembelian.php" class="form-back-btn">← Kembali ke Pembelian</a>

      <!-- INFO PEMBELIAN -->
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
          <h2>Informasi Pembelian</h2>
          <span class="badge <?= statusBadge($trx['status']) ?>"><?= htmlspecialchars($trx['status']) ?></span>
        </div>

        <div class="form-row" style="padding:0 0 8px;">
          <div class="form-group">
            <label style="color:#6b7280;font-size:12.5px;">No. Invoice</label>
            <p style="font-size:15px;font-weight:700;color:#111827;margin:4px 0 0;"><?= htmlspecialchars($trx['no_invoice'] ?? '-') ?></p>
          </div>
          <div class="form-group">
            <label style="color:#6b7280;font-size:12.5px;">Tanggal</label>
            <p style="font-size:15px;font-weight:600;color:#111827;margin:4px 0 0;"><?= tglIndo($trx['tanggal'], $bulanIndo) ?></p>
          </div>
        </div>

        <div class="form-row" style="margin-top:16px;">
          <div class="form-group">
            <label style="color:#6b7280;font-size:12.5px;">Supplier</label>
            <p style="font-size:14px;color:#374151;margin:4px 0 0;">
              <strong><?= htmlspecialchars($trx['nama_supplier'] ?? '-') ?></strong>
            </p>
          </div>
          <div class="form-group">
            <label style="color:#6b7280;font-size:12.5px;">Dibuat Oleh</label>
            <p style="font-size:14px;color:#374151;margin:4px 0 0;"><?= htmlspecialchars($trx['nama_user']) ?></p>
          </div>
        </div>

        <?php if ($trx['status'] === 'Proses'): ?>
        <div class="info-box" style="margin-top:16px;">
          <span class="info-icon">ⓘ</span>
          <span>Status <strong>Proses</strong> — stok produk belum bertambah. Stok akan bertambah otomatis setelah status diubah ke Selesai.</span>
        </div>
        <?php endif; ?>
      </div>

      <!-- DETAIL PRODUK -->
      <div class="card">
        <div class="card-header"><h2>Daftar Produk Dibeli</h2></div>

        <table class="data-table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Produk</th>
              <th>Harga Beli</th>
              <th>Qty</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($items)): ?>
            <tr><td colspan="5" class="text-muted" style="text-align:center;padding:20px;">Tidak ada detail produk.</td></tr>
            <?php endif; ?>
            <?php foreach ($items as $i => $item): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td>
                <div class="product-cell">
                  <div class="stock-icon"><?= $item['icon'] ?></div>
                  <div class="stock-info"><strong><?= htmlspecialchars($item['nama_produk']) ?></strong></div>
                </div>
              </td>
              <td><?= rp($item['harga_satuan']) ?></td>
              <td><?= $item['qty'] ?></td>
              <td><?= rp($item['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- TOTAL -->
        <div class="total-box" style="margin-top:20px;">
          <div class="total-row grand"><span>TOTAL PEMBELIAN</span><span><?= rp($trx['total']) ?></span></div>
        </div>

        <!-- TOMBOL -->
        <div class="form-actions" style="margin-top:20px;">
          <a href="pembelian.php" class="btn-outline" style="padding:11px 20px;border-radius:9px;font-size:13.5px;font-weight:600;color:#374151;text-decoration:none;">← Kembali</a>
          <button onclick="window.print()" class="btn-primary" style="padding:11px 24px;">🖨️ Cetak</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function toggleAdminMenu(){document.getElementById("adminMenu").classList.toggle("open");}
document.addEventListener("click",function(e){var m=document.getElementById("adminMenu");if(m&&!e.target.closest(".admin-dropdown"))m.classList.remove("open");});
</script>

<style>
@media print {
  .sidebar, .topbar, .form-back-btn, .form-actions { display: none !important; }
  .main { margin: 0; }
  .content { padding: 0; }
  .card { box-shadow: none; border: 1px solid #e5e7eb; }
}
</style>
</body>
</html>
