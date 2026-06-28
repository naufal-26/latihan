<?php

// ====================== DATA DUMMY ======================
$stats = [
    [
        'label' => 'Total Pembelian',
        'value' => 'Rp 18.250.000',
        'note' => 'Bulan ini',
        'icon' => 'cart',
        'color' => 'blue',
    ],
    [
        'label' => 'Jumlah Transaksi',
        'value' => '32',
        'note' => 'Bulan ini',
        'icon' => 'doc',
        'color' => 'green',
    ],
    [
        'label' => 'Rata-rata per Transaksi',
        'value' => 'Rp 570.312',
        'note' => 'Bulan ini',
        'icon' => 'wallet',
        'color' => 'orange',
    ],
    [
        'label' => 'Total Supplier',
        'value' => '12',
        'note' => 'Terdaftar',
        'icon' => 'users',
        'color' => 'purple',
    ],
];

// Daftar pembelian
$purchaseList = [
    ['invoice' => 'PB/2025/05/0012', 'tanggal' => '20 Mei 2025', 'supplier' => 'Maju Jaya Motor',   'total' => 2750000, 'status' => 'Selesai'],
    ['invoice' => 'PB/2025/05/0011', 'tanggal' => '19 Mei 2025', 'supplier' => 'Sinar Abadi Parts',  'total' => 1980000, 'status' => 'Selesai'],
    ['invoice' => 'PB/2025/05/0010', 'tanggal' => '18 Mei 2025', 'supplier' => 'Duta Sparepart',     'total' => 3450000, 'status' => 'Selesai'],
    ['invoice' => 'PB/2025/05/0009', 'tanggal' => '17 Mei 2025', 'supplier' => 'Berkat Mandiri',     'total' => 2120000, 'status' => 'Proses'],
    ['invoice' => 'PB/2025/05/0008', 'tanggal' => '16 Mei 2025', 'supplier' => 'Jaya Motorindo',     'total' => 4250000, 'status' => 'Proses'],
    ['invoice' => 'PB/2025/05/0007', 'tanggal' => '15 Mei 2025', 'supplier' => 'Toko Sumber Rejeki', 'total' => 1700000, 'status' => 'Dibatalkan'],
    ['invoice' => 'PB/2025/05/0006', 'tanggal' => '14 Mei 2025', 'supplier' => 'Anugrah Parts',      'total' => 2000000, 'status' => 'Selesai'],
];

// Pagination dummy
$currentPage = 1;
$totalPages = 5;
$totalData = 32;
$shownFrom = 1;
$shownTo = 7;

// Helper format Rupiah
function rp($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// Badge warna berdasarkan status pembelian
function statusBadgeClass($status) {
    switch ($status) {
        case 'Selesai':    return 'badge-success';
        case 'Proses':     return 'badge-blue';
        case 'Dibatalkan': return 'badge-red';
        default: return 'badge-gray';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembelian - Sparepart Store</title>
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
            <a href="pembelian.php" class="nav-item active"><span class="nav-icon">📋</span> Pembelian</a>
            <a href="pelanggan.php" class="nav-item"><span class="nav-icon">👥</span> Pelanggan</a>
            <a href="laporan.php" class="nav-item"><span class="nav-icon">📊</span> Laporan</a>
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
                <h1>Pembelian</h1>
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
                $icons = ['cart' => '🛒', 'doc' => '📥', 'wallet' => '👛', 'users' => '👥'];
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

            <!-- DAFTAR PEMBELIAN -->
            <div class="card">
                <div class="card-header">
                    <h2>Daftar Pembelian</h2>
                </div>

                <div class="toolbar">
                    <div class="toolbar-field">
                        <span>01/05/2025 - 31/05/2025</span>
                        <span class="toolbar-icon">📅</span>
                    </div>
                    <select class="toolbar-select">
                        <option>Semua Supplier</option>
                        <option>Maju Jaya Motor</option>
                        <option>Sinar Abadi Parts</option>
                        <option>Duta Sparepart</option>
                        <option>Berkat Mandiri</option>
                    </select>
                    <div class="toolbar-search">
                        <span class="toolbar-icon">🔍</span>
                        <input type="text" placeholder="Cari invoice / supplier...">
                    </div>
                    <button class="btn-primary">+ Pembelian Baru</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($purchaseList as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['invoice']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal']) ?></td>
                            <td><?= htmlspecialchars($row['supplier']) ?></td>
                            <td><?= rp($row['total']) ?></td>
                            <td><span class="badge <?= statusBadgeClass($row['status']) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                            <td>
                                <button class="icon-btn" title="Opsi">⋮</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="table-footer">
                    <span>Menampilkan <?= $shownFrom ?> - <?= $shownTo ?> dari <?= $totalData ?> data</span>
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
