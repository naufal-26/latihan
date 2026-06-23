<?php
/**
 * Halaman Produk
 * File: produk.php
 * Data di bawah ini bersifat statis (dummy) — ganti dengan query
 * database (MySQL/PDO) sesuai kebutuhan aplikasi Anda.
 */

// ====================== DATA DUMMY ======================
$stats = [
    [
        'label' => 'Total Produk',
        'value' => '328',
        'note' => 'Semua produk terdaftar',
        'icon' => 'box',
        'color' => 'blue',
    ],
    [
        'label' => 'Stok Tersedia',
        'value' => '1.245',
        'note' => 'Produk dengan stok > 0',
        'icon' => 'gift',
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
        'note' => 'Produk tanpa stok',
        'icon' => 'box',
        'color' => 'purple',
    ],
];

// Daftar produk
$products = [
    ['no' => 1, 'nama' => 'Busi NGK CPR8E',        'kategori' => 'Busi',   'harga' => 18000,  'stok' => 25, 'status' => 'Tersedia', 'icon' => '🔧'],
    ['no' => 2, 'nama' => 'Kampas Rem Depan',       'kategori' => 'Rem',    'harga' => 45000,  'stok' => 12, 'status' => 'Tersedia', 'icon' => '🛑'],
    ['no' => 3, 'nama' => 'V-Belt Yamaha NMAX',     'kategori' => 'CVT',    'harga' => 65000,  'stok' => 7,  'status' => 'Tersedia', 'icon' => '⚙️'],
    ['no' => 4, 'nama' => 'Filter Oli Honda Beat',  'kategori' => 'Oli',    'harga' => 25000,  'stok' => 3,  'status' => 'Menipis',  'icon' => '🧴'],
    ['no' => 5, 'nama' => 'Aki GS GTZ5S',           'kategori' => 'Aki',    'harga' => 210000, 'stok' => 0,  'status' => 'Habis',    'icon' => '🔋'],
    ['no' => 6, 'nama' => 'Gear Set SSS 428-14T',   'kategori' => 'Gear',   'harga' => 120000, 'stok' => 15, 'status' => 'Tersedia', 'icon' => '⚙️'],
    ['no' => 7, 'nama' => 'Oli Federal Matic 10W-30','kategori' => 'Oli',   'harga' => 55000,  'stok' => 20, 'status' => 'Tersedia', 'icon' => '🛢️'],
    ['no' => 8, 'nama' => 'Filter Udara Vario 125', 'kategori' => 'Filter', 'harga' => 30000,  'stok' => 9,  'status' => 'Tersedia', 'icon' => '🌀'],
];

// Pagination dummy
$currentPage = 1;
$totalPages = 41;
$totalData = 328;
$shownFrom = 1;
$shownTo = 8;

// Helper format Rupiah
function rp($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

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
<title>Produk - Sparepart Store</title>
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
            <a href="produk.php" class="nav-item active"><span class="nav-icon">📦</span> Produk</a>
            <a href="stok.php" class="nav-item"><span class="nav-icon">🗳️</span> Stok</a>
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
                <h1>Produk</h1>
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
                $icons = ['box' => '📦', 'gift' => '🎁', 'warning' => '⚠️'];
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

            <!-- DAFTAR PRODUK -->
            <div class="card">
                <div class="card-header">
                    <h2>Daftar Produk</h2>
                </div>

                <div class="toolbar toolbar-end">
                    <select class="toolbar-select">
                        <option>Semua Kategori</option>
                        <option>Busi</option>
                        <option>Rem</option>
                        <option>CVT</option>
                        <option>Oli</option>
                        <option>Aki</option>
                        <option>Gear</option>
                        <option>Filter</option>
                    </select>
                    <div class="toolbar-search">
                        <span class="toolbar-icon">🔍</span>
                        <input type="text" placeholder="Cari produk...">
                    </div>
                    <button class="btn-primary">+ Tambah Produk</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $p['no'] ?></td>
                            <td>
                                <div class="product-cell">
                                    <div class="stock-icon"><?= $p['icon'] ?></div>
                                    <div class="stock-info">
                                        <strong><?= htmlspecialchars($p['nama']) ?></strong>
                                        <span><?= htmlspecialchars($p['kategori']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($p['kategori']) ?></td>
                            <td><?= rp($p['harga']) ?></td>
                            <td><?= $p['stok'] ?></td>
                            <td><span class="badge <?= statusBadgeClass($p['status']) ?>"><?= htmlspecialchars($p['status']) ?></span></td>
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
