<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/db.php';

$dari   = $_GET['dari']   ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

function rp($n){ return 'Rp ' . number_format($n, 0, ',', '.'); }

// Query data
$stmt = $pdo->prepare("SELECT COALESCE(SUM(total),0) AS total, COUNT(*) AS jumlah FROM penjualan WHERE status='Selesai' AND tanggal BETWEEN ? AND ?");
$stmt->execute([$dari, $sampai]); $penjualan = $stmt->fetch();

$stmt = $pdo->prepare("SELECT COALESCE(SUM(total),0) AS total, COUNT(*) AS jumlah FROM pembelian WHERE status='Selesai' AND tanggal BETWEEN ? AND ?");
$stmt->execute([$dari, $sampai]); $pembelian = $stmt->fetch();

$labaKotor = $penjualan['total'] - $pembelian['total'];

$stmt = $pdo->prepare("SELECT COALESCE(SUM(pd.qty),0) FROM penjualan_detail pd JOIN penjualan p ON p.id=pd.penjualan_id WHERE p.status='Selesai' AND p.tanggal BETWEEN ? AND ?");
$stmt->execute([$dari, $sampai]); $produkTerjual = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM pelanggan WHERE created_at BETWEEN ? AND ?");
$stmt->execute([$dari, $sampai . ' 23:59:59']); $pelangganBaru = (int)$stmt->fetchColumn();

// Detail penjualan per produk
$stmt = $pdo->prepare("
    SELECT pr.nama_produk, SUM(pd.qty) AS total_qty, SUM(pd.subtotal) AS total_nilai
    FROM penjualan_detail pd
    JOIN produk pr ON pr.id = pd.produk_id
    JOIN penjualan p ON p.id = pd.penjualan_id
    WHERE p.status='Selesai' AND p.tanggal BETWEEN ? AND ?
    GROUP BY pr.id, pr.nama_produk
    ORDER BY total_nilai DESC
");
$stmt->execute([$dari, $sampai]);
$detailProduk = $stmt->fetchAll();

// Format tanggal untuk nama file
$periodeFile = date('d-m-Y', strtotime($dari)) . '_sd_' . date('d-m-Y', strtotime($sampai));

// Output sebagai CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="laporan_' . $periodeFile . '.csv"');

$out = fopen('php://output', 'w');

// BOM untuk Excel agar karakter Indonesia terbaca
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

// Header
fputcsv($out, ['LAPORAN TOKO SPAREPART'], ';');
fputcsv($out, ['Periode: ' . date('d/m/Y', strtotime($dari)) . ' - ' . date('d/m/Y', strtotime($sampai))], ';');
fputcsv($out, [''], ';');

// Ringkasan
fputcsv($out, ['=== RINGKASAN ==='], ';');
fputcsv($out, ['No', 'Jenis Laporan', 'Jumlah', 'Keterangan'], ';');
fputcsv($out, [1, 'Penjualan',      rp($penjualan['total']), $penjualan['jumlah'] . ' Transaksi'], ';');
fputcsv($out, [2, 'Pembelian',      rp($pembelian['total']), $pembelian['jumlah'] . ' Transaksi'], ';');
fputcsv($out, [3, 'Laba Kotor',     rp($labaKotor),          'Penjualan - Pembelian'], ';');
fputcsv($out, [4, 'Produk Terjual', $produkTerjual,           'Total unit terjual'], ';');
fputcsv($out, [5, 'Pelanggan Baru', $pelangganBaru,           'Periode ini'], ';');
fputcsv($out, [''], ';');

// Detail produk terjual
fputcsv($out, ['=== DETAIL PRODUK TERJUAL ==='], ';');
fputcsv($out, ['No', 'Nama Produk', 'Qty Terjual', 'Total Nilai'], ';');
foreach ($detailProduk as $i => $p) {
    fputcsv($out, [$i+1, $p['nama_produk'], $p['total_qty'], rp($p['total_nilai'])], ';');
}
fputcsv($out, [''], ';');

// Detail transaksi penjualan
$stmt = $pdo->prepare("
    SELECT p.no_invoice, p.tanggal, COALESCE(c.nama,'Umum') AS pelanggan, p.total, p.metode_pembayaran, p.status
    FROM penjualan p LEFT JOIN pelanggan c ON c.id=p.pelanggan_id
    WHERE p.tanggal BETWEEN ? AND ?
    ORDER BY p.tanggal DESC, p.id DESC
");
$stmt->execute([$dari, $sampai]);
$transaksi = $stmt->fetchAll();

fputcsv($out, ['=== DAFTAR TRANSAKSI PENJUALAN ==='], ';');
fputcsv($out, ['No. Invoice', 'Tanggal', 'Pelanggan', 'Total', 'Metode', 'Status'], ';');
foreach ($transaksi as $t) {
    fputcsv($out, [$t['no_invoice'], $t['tanggal'], $t['pelanggan'], rp($t['total']), $t['metode_pembayaran'], $t['status']], ';');
}

fclose($out);
exit;
