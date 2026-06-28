<?php

// ====================== DATA DUMMY ======================
$stats = [
    [
        'label' => 'Total Pelanggan',
        'value' => '186',
        'note' => 'Semua pelanggan',
        'icon' => 'users',
        'color' => 'blue',
    ],
    [
        'label' => 'Pelanggan Baru (Bulan Ini)',
        'value' => '12',
        'note' => 'Bulan ini',
        'icon' => 'user-plus',
        'color' => 'green',
    ],
    [
        'label' => 'Total Transaksi (Bulan Ini)',
        'value' => '32',
        'note' => 'Transaksi',
        'icon' => 'cart',
        'color' => 'orange',
    ],
    [
        'label' => 'Total Pembelian (Bulan Ini)',
        'value' => 'Rp 24.750.000',
        'note' => 'Bulan ini',
        'icon' => 'wallet',
        'color' => 'purple',
    ],
];

// Daftar pelanggan. avatarColor mengacu pada class warna lingkaran inisial.
$customers = [
    ['no' => 1, 'nama' => 'Budi Santoso',  'telepon' => '0812-3456-7890', 'transaksi' => 15, 'pembelian' => 12450000, 'avatarColor' => 'blue'],
    ['no' => 2, 'nama' => 'Andi Wijaya',   'telepon' => '0813-2222-3333', 'transaksi' => 9,  'pembelian' => 8250000,  'avatarColor' => 'green'],
    ['no' => 3, 'nama' => 'Rian Pratama',  'telepon' => '0857-1111-2222', 'transaksi' => 7,  'pembelian' => 5600000,  'avatarColor' => 'purple'],
    ['no' => 4, 'nama' => 'Dewi Lestari',  'telepon' => '0812-9876-5432', 'transaksi' => 6,  'pembelian' => 4980000,  'avatarColor' => 'orange'],
    ['no' => 5, 'nama' => 'Agus Setiawan', 'telepon' => '0821-7654-3210', 'transaksi' => 5,  'pembelian' => 3430000,  'avatarColor' => 'teal'],
    ['no' => 6, 'nama' => 'Yudi Saputra',  'telepon' => '0813-5555-6666', 'transaksi' => 4,  'pembelian' => 2150000,  'avatarColor' => 'red'],
    ['no' => 7, 'nama' => 'Maya Rahayu',   'telepon' => '0882-1234-5678', 'transaksi' => 3,  'pembelian' => 1890000,  'avatarColor' => 'blue'],
    ['no' => 8, 'nama' => 'Fajar Hidayat', 'telepon' => '0812-2222-4444', 'transaksi' => 2,  'pembelian' => 1200000,  'avatarColor' => 'gray'],
];

// Pagination dummy
$currentPage = 1;
$totalPages = 24;
$totalData = 186;
$shownFrom = 1;
$shownTo = 8;

// Helper format Rupiah
function rp($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// Ambil inisial dari nama (maks. 2 huruf)
function initials($nama) {
    $parts = explode(' ', trim($nama));
    $first = mb_substr($parts[0], 0, 1);
    $second = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '';
    return mb_strtoupper($first . $second);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pelanggan - Sparepart Store</title>
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
            <a href="pelanggan.php" class="nav-item active"><span class="nav-icon">👥</span> Pelanggan</a>
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
                <h1>Pelanggan</h1>
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
                $icons = ['users' => '👥', 'user-plus' => '👤➕', 'cart' => '🛒', 'wallet' => '👛'];
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

            <!-- DAFTAR PELANGGAN -->
            <div class="card">
                <div class="card-header">
                    <h2>Daftar Pelanggan</h2>
                    <div class="toolbar no-margin">
                        <div class="toolbar-search">
                            <span class="toolbar-icon">🔍</span>
                            <input type="text" placeholder="Cari pelanggan...">
                        </div>
                        <button class="btn-primary">+ Pelanggan Baru</button>
                    </div>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Pelanggan</th>
                            <th>No. Telepon</th>
                            <th>Total Transaksi</th>
                            <th>Total Pembelian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $c): ?>
                        <tr>
                            <td><?= $c['no'] ?></td>
                            <td>
                                <div class="customer-cell">
                                    <div class="avatar-circle avatar-<?= $c['avatarColor'] ?>"><?= initials($c['nama']) ?></div>
                                    <strong><?= htmlspecialchars($c['nama']) ?></strong>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($c['telepon']) ?></td>
                            <td><?= $c['transaksi'] ?></td>
                            <td><?= rp($c['pembelian']) ?></td>
                            <td>
                                <div class="action-icons">
                                    <button class="icon-btn" title="Edit">✏️</button>
                                    <button class="icon-btn icon-btn-danger" title="Hapus">🗑️</button>
                                </div>
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
