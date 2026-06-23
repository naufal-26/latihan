<?php

// ====================== DATA DUMMY ======================
$stats = [
    [
        'label' => 'Total Stok',
        'value' => '1.245',
        'note' => 'Semua produk',
        'icon' => 'box',
        'color' => 'blue',
    ],
    [
        'label' => 'Stok Tersedia',
        'value' => '1.214',
        'note' => 'Siap dijual',
        'icon' => 'check',
        'color' => 'green',
    ],
    [
        'label' => 'Stok Menipis',
        'value' => '23',
        'note' => 'Stok ≤ 5',
        'icon' => 'warning',
        'color' => 'orange',
    ],
    [
        'label' => 'Stok Habis',
        'value' => '8',
        'note' => 'Stok = 0',
        'icon' => 'cross',
        'color' => 'red',
    ],
];

// Daftar stok
// stok_tersedia: jumlah siap jual, stok_menipis / stok_habis: jumlah unit dalam kondisi tsb (jika ada), '-' jika tidak relevan
$stockList = [
    ['no' => 1,  'nama' => 'Busi NGK CPR8E',         'kategori' => 'Busi',  'tersedia' => 25, 'menipis' => '-', 'habis' => '-', 'satuan' => 'Pcs', 'status' => 'Tersedia', 'icon' => '🔧'],
    ['no' => 2,  'nama' => 'Kampas Rem Depan',        'kategori' => 'Rem',   'tersedia' => 12, 'menipis' => '-', 'habis' => '-', 'satuan' => 'Set', 'status' => 'Tersedia', 'icon' => '🛑'],
    ['no' => 3,  'nama' => 'V-Belt Yamaha NMAX',       'kategori' => 'CVT',   'tersedia' => 7,  'menipis' => '-', 'habis' => '-', 'satuan' => 'Pcs', 'status' => 'Tersedia', 'icon' => '⚙️'],
    ['no' => 4,  'nama' => 'Filter Oli Honda Beat',    'kategori' => 'Oli',   'tersedia' => 3,  'menipis' => '-', 'habis' => '-', 'satuan' => 'Pcs', 'status' => 'Menipis',  'icon' => '🧴'],
    ['no' => 5,  'nama' => 'Aki GS GTZ5S',             'kategori' => 'Aki',   'tersedia' => 0,  'menipis' => '-', 'habis' => 1,   'satuan' => 'Pcs', 'status' => 'Habis',    'icon' => '🔋'],
    ['no' => 6,  'nama' => 'Gear Set SSS 428-14T',     'kategori' => 'Gear',  'tersedia' => 15, 'menipis' => '-', 'habis' => '-', 'satuan' => 'Set', 'status' => 'Tersedia', 'icon' => '⚙️'],
    ['no' => 7,  'nama' => 'Oli Federal Matic 10W-30', 'kategori' => 'Oli',   'tersedia' => 20, 'menipis' => '-', 'habis' => '-', 'satuan' => 'Pcs', 'status' => 'Tersedia', 'icon' => '🛢️'],
    ['no' => 8,  'nama' => 'Filter Udara Vario 125',   'kategori' => 'Filter','tersedia' => 9,  'menipis' => '-', 'habis' => '-', 'satuan' => 'Pcs', 'status' => 'Tersedia', 'icon' => '🌀'],
    ['no' => 9,  'nama' => 'Piringan Cakram Depan',    'kategori' => 'Rem',   'tersedia' => 2,  'menipis' => 1,   'habis' => '-', 'satuan' => 'Pcs', 'status' => 'Menipis',  'icon' => '⚪'],
    ['no' => 10, 'nama' => 'Rantai Motor 428-104L',    'kategori' => 'Chain', 'tersedia' => 0,  'menipis' => '-', 'habis' => 2,   'satuan' => 'Pcs', 'status' => 'Habis',    'icon' => '🔗'],
];

// Pagination dummy
$currentPage = 1;
$totalPages = 125;
$totalData = 1245;
$shownFrom = 1;
$shownTo = 10;

// Badge warna berdasarkan status stok
function statusBadgeClass($status) {
    switch ($status) {
        case 'Tersedia': return 'badge-success';
        case 'Menipis':  return 'badge-orange';
        case 'Habis':    return 'badge-red';
        default: return 'badge-gray';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Stok - Sparepart Store</title>
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
            <a href="stok.php" class="nav-item active"><span class="nav-icon">🗳️</span> Stok</a>
            <a href="#" class="nav-item"><span class="nav-icon">📋</span> Pembelian</a>
            <a href="#" class="nav-item"><span class="nav-icon">👥</span> Pelanggan</a>
            <a href="#" class="nav-item"><span class="nav-icon">📊</span> Laporan</a>
            <a href="#" class="nav-item"><span class="nav-icon">⚙️</span> Pengaturan</a>
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
                <h1>Stok</h1>
            </div>
            <div class="topbar-right">
                <span class="bell">🔔<span class="dot"></span></span>
                <div class="avatar">AD</div>
                <span class="admin-name">Admin ▾</span>
            </div>
        </header>

        <div class="content">

            <!-- STAT CARDS -->
            <div class="stats-grid">
                <?php
                $icons = ['box' => '📦', 'check' => '✅', 'warning' => '⚠️', 'cross' => '❌'];
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

            <!-- DAFTAR STOK -->
            <div class="card">
                <div class="card-header">
                    <h2>Daftar Stok</h2>
                    <div class="toolbar toolbar-end no-margin">
                        <div class="toolbar-search">
                            <span class="toolbar-icon">🔍</span>
                            <input type="text" placeholder="Cari produk...">
                        </div>
                        <button class="btn-outline">▾ Filter</button>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Stok Tersedia</th>
                            <th>Stok Menipis</th>
                            <th>Stok Habis</th>
                            <th>Satuan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stockList as $row): ?>
                        <tr>
                            <td><?= $row['no'] ?></td>
                            <td>
                                <div class="product-cell">
                                    <div class="stock-icon"><?= $row['icon'] ?></div>
                                    <div class="stock-info">
                                        <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                        <span><?= htmlspecialchars($row['kategori']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <td><?= $row['tersedia'] ?></td>
                            <td class="text-muted"><?= $row['menipis'] ?></td>
                            <td class="text-muted"><?= $row['habis'] ?></td>
                            <td><?= htmlspecialchars($row['satuan']) ?></td>
                            <td><span class="badge <?= statusBadgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="table-footer">
                    <span>Menampilkan <?= $shownFrom ?> - <?= $shownTo ?> dari <?= number_format($totalData, 0, ',', '.') ?> data</span>
                    <div class="pagination">
                        <span class="page-nav">‹</span>
                        <?php for ($i = 1; $i <= 3; $i++): ?>
                            <span class="page-item <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></span>
                        <?php endfor; ?>
                        <span class="page-dots">...</span>
                        <span class="page-item"><?= $totalPages ?></span>
                        <span class="page-nav">›</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
