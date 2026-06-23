<?php
/**
 * Dashboard Toko Sparepart
 * File: index.php
 * Semua data di bawah ini bersifat statis (dummy) — silakan ganti
 * dengan query database (MySQL/PDO) sesuai kebutuhan.
 */

// ====================== DATA DUMMY ======================
$stats = [
    [
        'label' => 'Total Penjualan',
        'value' => 'Rp 24.750.000',
        'change' => '+12.5%',
        'changeType' => 'up',
        'note' => 'dari bulan lalu',
        'icon' => 'cart',
        'color' => 'blue',
    ],
    [
        'label' => 'Total Produk',
        'value' => '328',
        'change' => '0%',
        'changeType' => 'flat',
        'note' => 'dari bulan lalu',
        'icon' => 'box',
        'color' => 'green',
    ],
    [
        'label' => 'Stok Tersedia',
        'value' => '1.245',
        'change' => '+8.3%',
        'changeType' => 'up',
        'note' => 'dari bulan lalu',
        'icon' => 'layers',
        'color' => 'orange',
    ],
    [
        'label' => 'Total Pelanggan',
        'value' => '186',
        'change' => '+5.4%',
        'changeType' => 'up',
        'note' => 'dari bulan lalu',
        'icon' => 'users',
        'color' => 'purple',
    ],
];

// Data grafik penjualan 30 hari terakhir (label tanggal => nilai dalam juta)
$salesChart = [
    '20 Apr' => 1.8, '22 Apr' => 2.1, '24 Apr' => 2.4, '25 Apr' => 2.6,
    '27 Apr' => 3.1, '29 Apr' => 3.6, '30 Apr' => 3.9, '1 Mei' => 4.0,
    '3 Mei' => 4.3, '5 Mei' => 5.2, '5 Mei' => 8.2, '7 Mei' => 6.7,
    '8 Mei' => 5.4, '10 Mei' => 4.8, '12 Mei' => 4.7, '13 Mei' => 5.0,
    '15 Mei' => 5.6, '16 Mei' => 5.1, '18 Mei' => 4.6, '19 Mei' => 5.0,
    '20 Mei' => 3.6,
];

$recentSales = [
    ['invoice' => 'INV/2025/05/0021', 'pelanggan' => 'Budi Santoso', 'tanggal' => '20 Mei 2025', 'total' => 750000, 'status' => 'Selesai'],
    ['invoice' => 'INV/2025/05/0020', 'pelanggan' => 'Andi Wijaya', 'tanggal' => '20 Mei 2025', 'total' => 1250000, 'status' => 'Selesai'],
    ['invoice' => 'INV/2025/05/0019', 'pelanggan' => 'Rian Pratama', 'tanggal' => '19 Mei 2025', 'total' => 560000, 'status' => 'Selesai'],
    ['invoice' => 'INV/2025/05/0018', 'pelanggan' => 'Dewi Lestari', 'tanggal' => '19 Mei 2025', 'total' => 980000, 'status' => 'Selesai'],
    ['invoice' => 'INV/2025/05/0017', 'pelanggan' => 'Agus Setiawan', 'tanggal' => '18 Mei 2025', 'total' => 430000, 'status' => 'Selesai'],
];

$lowStock = [
    ['nama' => 'Busi NGK CPR8E', 'kategori' => 'Busi', 'stok' => 3, 'icon' => '🔧'],
    ['nama' => 'Kampas Rem Depan', 'kategori' => 'Rem', 'stok' => 5, 'icon' => '🛑'],
    ['nama' => 'V-Belt Yamaha NMAX', 'kategori' => 'CVT', 'stok' => 7, 'icon' => '⚙️'],
    ['nama' => 'Filter Oli Honda Beat', 'kategori' => 'Oli', 'stok' => 8, 'icon' => '🧴'],
    ['nama' => 'Aki GS GTZ5S', 'kategori' => 'Aki', 'stok' => 9, 'icon' => '🔋'],
];

$purchaseSummary = [
    ['label' => 'Total Pembelian', 'value' => 'Rp 18.250.000', 'icon' => 'bag', 'color' => 'blue'],
    ['label' => 'Jumlah Transaksi', 'value' => '32', 'icon' => 'doc', 'color' => 'green'],
    ['label' => 'Rata-rata per Transaksi', 'value' => 'Rp 570.312', 'icon' => 'chart', 'color' => 'orange'],
];

// Helper format Rupiah
function rp($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// ====================== BUAT TITIK SVG UNTUK GRAFIK ======================
$values = array_values($salesChart);
$labels = array_keys($salesChart);
$maxVal = 10; // skala sumbu Y tetap 0 - 10 jt seperti pada desain
$chartW = 900;
$chartH = 260;
$pointCount = count($values);
$points = [];
foreach ($values as $i => $v) {
    $x = ($i / ($pointCount - 1)) * $chartW;
    $y = $chartH - ($v / $maxVal) * $chartH;
    $points[] = "$x,$y";
}
$polylinePoints = implode(' ', $points);
$areaPoints = "0,$chartH " . $polylinePoints . " $chartW,$chartH";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Sparepart Store</title>
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
            <a href="index.php" class="nav-item active"><span class="nav-icon">▦</span> Dashboard</a>
            <a href="penjualan.php" class="nav-item"><span class="nav-icon">🛒</span> Penjualan</a>
            <a href="produk.php" class="nav-item"><span class="nav-icon">📦</span> Produk</a>
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
                <h1>Dashboard</h1>
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
                <?php foreach ($stats as $s): ?>
                <div class="stat-card">
                    <div class="stat-icon icon-<?= $s['color'] ?>">
                        <?php
                        $icons = ['cart' => '🛒','box' => '📦','layers' => '🧱','users' => '👥'];
                        echo $icons[$s['icon']] ?? '●';
                        ?>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label"><?= htmlspecialchars($s['label']) ?></span>
                        <span class="stat-value"><?= htmlspecialchars($s['value']) ?></span>
                        <span class="stat-change change-<?= $s['changeType'] ?>">
                            <?= $s['changeType'] === 'up' ? '▲' : ($s['changeType'] === 'down' ? '▼' : '—') ?>
                            <?= htmlspecialchars($s['change']) ?> <span class="stat-note"><?= htmlspecialchars($s['note']) ?></span>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- CHART + SIDE PANEL -->
            <div class="grid-2col">

                <!-- LEFT COLUMN -->
                <div class="col-left">

                    <!-- SALES CHART -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Penjualan</h2>
                            <select class="range-select">
                                <option>30 Hari Terakhir</option>
                                <option>7 Hari Terakhir</option>
                                <option>90 Hari Terakhir</option>
                            </select>
                        </div>

                        <div class="chart-wrapper">
                            <div class="chart-axis">
                                <span>10 jt</span><span>8 jt</span><span>6 jt</span><span>4 jt</span><span>2 jt</span><span>0</span>
                            </div>
                            <svg viewBox="0 0 <?= $chartW ?> <?= $chartH ?>" preserveAspectRatio="none" class="chart-svg">
                                <defs>
                                    <linearGradient id="areaGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.25"/>
                                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0"/>
                                    </linearGradient>
                                </defs>
                                <polygon points="<?= $areaPoints ?>" fill="url(#areaGradient)"/>
                                <polyline points="<?= $polylinePoints ?>" fill="none" stroke="#3b82f6" stroke-width="3"/>
                                <?php foreach ($points as $p): list($px,$py)=explode(',', $p); ?>
                                    <circle cx="<?= $px ?>" cy="<?= $py ?>" r="3.5" fill="#3b82f6"/>
                                <?php endforeach; ?>
                            </svg>
                        </div>
                        <div class="chart-labels">
                            <?php
                            // tampilkan sebagian label saja agar tidak terlalu padat
                            $step = max(1, floor(count($labels) / 7));
                            foreach ($labels as $i => $l) {
                                if ($i % $step === 0 || $i === count($labels) - 1) {
                                    echo '<span>' . htmlspecialchars($l) . '</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>

                    <!-- RECENT SALES TABLE -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Penjualan Terbaru</h2>
                            <a href="#" class="link">Lihat semua</a>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentSales as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['invoice']) ?></td>
                                    <td><?= htmlspecialchars($row['pelanggan']) ?></td>
                                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                    <td><?= rp($row['total']) ?></td>
                                    <td><span class="badge badge-success"><?= htmlspecialchars($row['status']) ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

                <!-- RIGHT COLUMN -->
                <div class="col-right">

                    <!-- LOW STOCK -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Stok Menipis</h2>
                            <a href="#" class="link">Lihat semua</a>
                        </div>
                        <ul class="stock-list">
                            <?php foreach ($lowStock as $item): ?>
                            <li>
                                <div class="stock-icon"><?= $item['icon'] ?></div>
                                <div class="stock-info">
                                    <strong><?= htmlspecialchars($item['nama']) ?></strong>
                                    <span><?= htmlspecialchars($item['kategori']) ?></span>
                                </div>
                                <div class="stock-amount">
                                    <span class="badge badge-warning"><?= $item['stok'] ?></span>
                                    <small>Stok</small>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- PURCHASE SUMMARY -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Ringkasan Pembelian (30 Hari Terakhir)</h2>
                        </div>
                        <div class="summary-grid">
                            <?php
                            $sIcons = ['bag' => '🛍️','doc' => '📄','chart' => '📈'];
                            foreach ($purchaseSummary as $p): ?>
                            <div class="summary-item">
                                <div class="summary-icon icon-<?= $p['color'] ?>"><?= $sIcons[$p['icon']] ?? '●' ?></div>
                                <span class="summary-label"><?= htmlspecialchars($p['label']) ?></span>
                                <span class="summary-value"><?= htmlspecialchars($p['value']) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
