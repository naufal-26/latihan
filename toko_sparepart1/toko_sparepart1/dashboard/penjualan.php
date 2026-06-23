<?php
/**
 * Halaman Penjualan
 * File: penjualan.php
 * Data di bawah ini bersifat statis (dummy) — ganti dengan query
 * database (MySQL/PDO) sesuai kebutuhan aplikasi Anda.
 */

// ====================== DATA DUMMY ======================
$stats = [
    [
        'label' => 'Total Penjualan',
        'value' => 'Rp 24.750.000',
        'change' => '+12.5%',
        'changeType' => 'up',
        'note' => 'dari periode lalu',
        'icon' => 'cart',
        'color' => 'blue',
    ],
    [
        'label' => 'Jumlah Transaksi',
        'value' => '32',
        'change' => '+8.6%',
        'changeType' => 'up',
        'note' => 'dari periode lalu',
        'icon' => 'trend',
        'color' => 'green',
    ],
    [
        'label' => 'Rata-rata Transaksi',
        'value' => 'Rp 773.438',
        'change' => '+3.2%',
        'changeType' => 'up',
        'note' => 'dari periode lalu',
        'icon' => 'doc',
        'color' => 'orange',
    ],
    [
        'label' => 'Total Diskon',
        'value' => 'Rp 1.250.000',
        'change' => '+5.1%',
        'changeType' => 'up',
        'note' => 'dari periode lalu',
        'icon' => 'tag',
        'color' => 'purple',
    ],
];

// Data grafik penjualan 30 hari terakhir (nilai dalam juta rupiah)
$salesChart = [
    '20 Apr' => 1.8, '22 Apr' => 2.1, '24 Apr' => 2.4, '25 Apr' => 2.6,
    '27 Apr' => 3.1, '29 Apr' => 3.6, '30 Apr' => 3.9, '1 Mei' => 4.0,
    '3 Mei' => 4.3, '5 Mei' => 5.2, '5 Mei' => 8.2, '7 Mei' => 6.7,
    '8 Mei' => 5.4, '10 Mei' => 4.8, '12 Mei' => 4.7, '13 Mei' => 5.0,
    '15 Mei' => 5.6, '16 Mei' => 5.1, '18 Mei' => 4.6, '19 Mei' => 5.0,
    '20 Mei' => 3.6,
];

// Penjualan berdasarkan metode pembayaran (untuk donut chart)
$paymentMethods = [
    ['label' => 'Tunai',              'value' => 12450000, 'percent' => 50.3, 'color' => '#2563eb'],
    ['label' => 'Transfer',           'value' => 8250000,  'percent' => 33.3, 'color' => '#22c55e'],
    ['label' => 'QRIS',               'value' => 3350000,  'percent' => 13.5, 'color' => '#f59e0b'],
    ['label' => 'Kartu Debit/Kredit', 'value' => 700000,   'percent' => 2.9,  'color' => '#8b5cf6'],
];

// Daftar penjualan (tabel)
$salesList = [
    ['invoice' => 'INV/2025/05/0021', 'tanggal' => '20 Mei 2025', 'pelanggan' => 'Budi Santoso',   'kasir' => 'Admin',   'total' => 750000,  'metode' => 'Tunai',    'status' => 'Selesai'],
    ['invoice' => 'INV/2025/05/0020', 'tanggal' => '20 Mei 2025', 'pelanggan' => 'Andi Wijaya',    'kasir' => 'Admin',   'total' => 1250000, 'metode' => 'Transfer', 'status' => 'Selesai'],
    ['invoice' => 'INV/2025/05/0019', 'tanggal' => '20 Mei 2025', 'pelanggan' => 'Rian Pratama',   'kasir' => 'Kasir 1', 'total' => 560000,  'metode' => 'QRIS',     'status' => 'Selesai'],
    ['invoice' => 'INV/2025/05/0018', 'tanggal' => '20 Mei 2025', 'pelanggan' => 'Dewi Lestari',   'kasir' => 'Kasir 1', 'total' => 980000,  'metode' => 'Tunai',    'status' => 'Selesai'],
    ['invoice' => 'INV/2025/05/0017', 'tanggal' => '19 Mei 2025', 'pelanggan' => 'Agus Setiawan',  'kasir' => 'Admin',   'total' => 430000,  'metode' => 'Transfer', 'status' => 'Selesai'],
];

// Ringkasan hari ini
$todaySummary = [
    ['label' => 'Total Penjualan',     'value' => 'Rp 2.850.000', 'icon' => 'cart',  'color' => 'blue'],
    ['label' => 'Jumlah Transaksi',    'value' => '5',            'icon' => 'trend', 'color' => 'green'],
    ['label' => 'Rata-rata Transaksi', 'value' => 'Rp 570.000',   'icon' => 'doc',   'color' => 'orange'],
    ['label' => 'Total Diskon',        'value' => 'Rp 150.000',   'icon' => 'tag',   'color' => 'purple'],
];

// Pagination dummy
$currentPage = 1;
$totalPages = 7;
$totalData = 32;
$shownFrom = 1;
$shownTo = 5;

// Helper format Rupiah
function rp($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// Badge warna untuk metode pembayaran
function metodeBadgeClass($metode) {
    switch ($metode) {
        case 'Tunai': return 'badge-blue';
        case 'Transfer': return 'badge-green';
        case 'QRIS': return 'badge-purple';
        default: return 'badge-gray';
    }
}

// ====================== TITIK SVG UNTUK GRAFIK GARIS ======================
$values = array_values($salesChart);
$labels = array_keys($salesChart);
$maxVal = 10;
$chartW = 760;
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

// ====================== SEGMEN DONUT CHART ======================
// Lingkar donut menggunakan stroke-dasharray pada <circle>.
$radius = 70;
$circumference = 2 * M_PI * $radius;
$cumulativePercent = 0;
$donutSegments = [];
foreach ($paymentMethods as $pm) {
    $dash = ($pm['percent'] / 100) * $circumference;
    $gap = $circumference - $dash;
    $offset = $circumference * (1 - $cumulativePercent / 100) + ($circumference / 4); // mulai dari atas (jam 12)
    $donutSegments[] = [
        'color' => $pm['color'],
        'dasharray' => $dash . ' ' . $gap,
        'offset' => $offset,
    ];
    $cumulativePercent += $pm['percent'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Penjualan - Sparepart Store</title>
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
            <a href="penjualan.php" class="nav-item active"><span class="nav-icon">🛒</span> Penjualan</a>
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
                <h1>Penjualan</h1>
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
                $icons = ['cart' => '🛒','trend' => '📈','doc' => '📄','tag' => '🏷️'];
                foreach ($stats as $s): ?>
                <div class="stat-card">
                    <div class="stat-icon icon-<?= $s['color'] ?>">
                        <?= $icons[$s['icon']] ?? '●' ?>
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

            <!-- CHART + DONUT -->
            <div class="grid-2col">

                <!-- SALES CHART -->
                <div class="card">
                    <div class="card-header">
                        <h2>Grafik Penjualan</h2>
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
                        $step = max(1, floor(count($labels) / 7));
                        foreach ($labels as $i => $l) {
                            if ($i % $step === 0 || $i === count($labels) - 1) {
                                echo '<span>' . htmlspecialchars($l) . '</span>';
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- DONUT CHART METODE PEMBAYARAN -->
                <div class="card">
                    <div class="card-header">
                        <h2>Penjualan Berdasarkan Metode Pembayaran</h2>
                    </div>
                    <div class="donut-wrapper">
                        <svg viewBox="0 0 180 180" class="donut-svg">
                            <g transform="rotate(-90 90 90)">
                                <?php foreach ($donutSegments as $seg): ?>
                                <circle cx="90" cy="90" r="<?= $radius ?>" fill="none"
                                        stroke="<?= $seg['color'] ?>" stroke-width="34"
                                        stroke-dasharray="<?= $seg['dasharray'] ?>"
                                        stroke-dashoffset="<?= $seg['offset'] ?>"/>
                                <?php endforeach; ?>
                            </g>
                        </svg>
                        <ul class="donut-legend">
                            <?php foreach ($paymentMethods as $pm): ?>
                            <li>
                                <span class="legend-dot" style="background: <?= $pm['color'] ?>"></span>
                                <div class="legend-info">
                                    <strong><?= htmlspecialchars($pm['label']) ?></strong>
                                    <span><?= rp($pm['value']) ?></span>
                                </div>
                                <span class="legend-percent"><?= number_format($pm['percent'], 1) ?>%</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- DAFTAR PENJUALAN + RINGKASAN -->
            <div class="grid-2col" style="margin-top:20px;">

                <!-- TABEL DAFTAR PENJUALAN -->
                <div class="card">
                    <div class="card-header">
                        <h2>Daftar Penjualan</h2>
                        <button class="btn-primary">+ Transaksi Baru</button>
                    </div>

                    <div class="toolbar">
                        <div class="toolbar-field">
                            <span class="toolbar-icon">📅</span>
                            <span>20/05/2025 - 20/05/2025</span>
                        </div>
                        <select class="toolbar-select">
                            <option>Semua Kasir</option>
                            <option>Admin</option>
                            <option>Kasir 1</option>
                        </select>
                        <div class="toolbar-search">
                            <span class="toolbar-icon">🔍</span>
                            <input type="text" placeholder="Cari invoice / pelanggan...">
                        </div>
                    </div>

                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Kasir</th>
                                <th>Total</th>
                                <th>Metode Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($salesList as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['invoice']) ?></td>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td><?= htmlspecialchars($row['pelanggan']) ?></td>
                                <td><?= htmlspecialchars($row['kasir']) ?></td>
                                <td><?= rp($row['total']) ?></td>
                                <td><span class="badge <?= metodeBadgeClass($row['metode']) ?>"><?= htmlspecialchars($row['metode']) ?></span></td>
                                <td><span class="badge badge-success"><?= htmlspecialchars($row['status']) ?></span></td>
                                <td><span class="action-dots">⋮</span></td>
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

                <!-- RINGKASAN HARI INI -->
                <div class="card">
                    <div class="card-header">
                        <h2>Ringkasan Hari Ini</h2>
                    </div>
                    <ul class="today-summary-list">
                        <?php foreach ($todaySummary as $t): ?>
                        <li>
                            <div class="stat-icon small icon-<?= $t['color'] ?>"><?= $icons[$t['icon']] ?? '●' ?></div>
                            <span class="today-label"><?= htmlspecialchars($t['label']) ?></span>
                            <span class="today-value"><?= htmlspecialchars($t['value']) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</div>
</body>
</html>
