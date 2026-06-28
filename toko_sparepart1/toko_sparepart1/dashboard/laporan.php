<?php

// ====================== DATA DUMMY ======================
$periode = '01/05/2025 - 31/05/2025';

$stats = [
    [
        'label' => 'Total Penjualan',
        'value' => 'Rp 24.750.000',
        'note' => '32 Transaksi',
        'icon' => 'cart',
        'color' => 'blue',
    ],
    [
        'label' => 'Total Pembelian',
        'value' => 'Rp 18.250.000',
        'note' => '28 Transaksi',
        'icon' => 'bag',
        'color' => 'green',
    ],
    [
        'label' => 'Laba Kotor',
        'value' => 'Rp 6.500.000',
        'note' => '(Penjualan - Pembelian)',
        'icon' => 'chart',
        'color' => 'orange',
    ],
    [
        'label' => 'Total Produk Terjual',
        'value' => '180',
        'note' => 'Produk',
        'icon' => 'doc',
        'color' => 'purple',
    ],
];

// Ringkasan laporan
$reportSummary = [
    ['no' => 1, 'jenis' => 'Penjualan',      'jumlah' => 'Rp 24.750.000', 'keterangan' => '32 Transaksi'],
    ['no' => 2, 'jenis' => 'Pembelian',      'jumlah' => 'Rp 18.250.000', 'keterangan' => '28 Transaksi'],
    ['no' => 3, 'jenis' => 'Laba Kotor',     'jumlah' => 'Rp 6.500.000',  'keterangan' => 'Selisih Penjualan - Pembelian'],
    ['no' => 4, 'jenis' => 'Produk Terjual', 'jumlah' => '180',           'keterangan' => 'Total unit produk terjual'],
    ['no' => 5, 'jenis' => 'Pelanggan Baru', 'jumlah' => '12',            'keterangan' => 'Pelanggan pada periode ini'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan - Sparepart Store</title>
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
            <a href="laporan.php" class="nav-item active"><span class="nav-icon">📊</span> Laporan</a>
            <a href="pengaturan.php" class="nav-item"><span class="nav-icon">⚙️</span> Pengaturan</a>
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
                <h1>Laporan</h1>
            </div>
            <div class="topbar-right">
                <span class="bell">🔔<span class="dot"></span></span>
                <div class="avatar">AD</div>
                <span class="admin-name">Admin ▾</span>
            </div>
        </header>

        <div class="content">

            <!-- PAGE TOOLBAR: FILTER PERIODE + UNDUH LAPORAN -->
            <div class="page-toolbar">
                <div class="toolbar-field">
                    <span class="toolbar-icon">📅</span>
                    <span><?= htmlspecialchars($periode) ?></span>
                    <span class="caret">▾</span>
                </div>
                <button class="btn-outline btn-download">⬇ Unduh Laporan</button>
            </div>

            <!-- STAT CARDS -->
            <div class="stats-grid">
                <?php
                $icons = ['cart' => '🛒', 'bag' => '🛍️', 'chart' => '📊', 'doc' => '📄'];
                foreach ($stats as $s): ?>
                <div class="stat-card">
                    <div class="stat-icon icon-<?= $s['color'] ?>">
                        <?= $icons[$s['icon']] ?? '●' ?>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label"><?= htmlspecialchars($s['label']) ?></span>
                        <span class="stat-value"><?= htmlspecialchars($s['value']) ?></span>
                        <span class="stat-plain-note"><?= htmlspecialchars($s['note']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- RINGKASAN LAPORAN -->
            <div class="card">
                <div class="card-header">
                    <h2>Ringkasan Laporan</h2>
                </div>

                <table class="data-table table-report">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Jenis Laporan</th>
                            <th>Jumlah</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportSummary as $row): ?>
                        <tr>
                            <td><?= $row['no'] ?></td>
                            <td><strong><?= htmlspecialchars($row['jenis']) ?></strong></td>
                            <td><?= htmlspecialchars($row['jumlah']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($row['keterangan']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="info-box">
                    <span class="info-icon">ⓘ</span>
                    <span>Semua data berdasarkan periode <strong><?= htmlspecialchars($periode) ?></strong></span>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
