<?php
/**
 * notif.php
 * Komponen notifikasi stok — di-include di setiap topbar halaman.
 * Mengambil produk dengan stok habis/menipis dari database.
 *
 * Cara pakai: pastikan $pdo sudah tersedia (sudah require db.php)
 * lalu include file ini di dalam <div class="topbar-right">
 */

// Produk stok habis (stok = 0)
$stmtHabis = $pdo->query("
    SELECT pr.nama_produk, pr.stok, pr.icon, k.nama_kategori
    FROM produk pr
    LEFT JOIN kategori k ON k.id = pr.kategori_id
    WHERE pr.stok = 0
    ORDER BY pr.nama_produk ASC
    LIMIT 10
");
$produkHabis = $stmtHabis->fetchAll();

// Produk stok menipis (stok > 0 dan stok <= stok_minimum)
$stmtTipis = $pdo->query("
    SELECT pr.nama_produk, pr.stok, pr.icon, k.nama_kategori
    FROM produk pr
    LEFT JOIN kategori k ON k.id = pr.kategori_id
    WHERE pr.stok > 0 AND pr.stok <= pr.stok_minimum
    ORDER BY pr.stok ASC
    LIMIT 10
");
$produkTipis = $stmtTipis->fetchAll();

$totalNotif = count($produkHabis) + count($produkTipis);
?>

<div class="bell-wrapper" onclick="toggleNotif(event)">
    <span style="font-size:18px;color:#6b7280;">🔔</span>
    <?php if ($totalNotif > 0): ?>
    <span class="bell-badge"><?= $totalNotif > 99 ? '99+' : $totalNotif ?></span>
    <?php endif; ?>

    <div class="notif-panel" id="notifPanel" onclick="event.stopPropagation()">
        <div class="notif-header">
            Notifikasi Stok
            <span><?= $totalNotif ?> peringatan</span>
        </div>

        <div class="notif-list">
            <?php if ($totalNotif === 0): ?>
            <div style="padding:24px;text-align:center;color:#9ca3af;font-size:13px;">
                ✅ Semua stok dalam kondisi normal
            </div>
            <?php endif; ?>

            <?php foreach ($produkHabis as $p): ?>
            <a href="stok.php" class="notif-item">
                <div class="notif-icon-wrap">
                    <?= $p['icon'] ?? '📦' ?>
                </div>
                <div class="notif-info">
                    <strong><?= htmlspecialchars($p['nama_produk']) ?></strong>
                    <span><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></span>
                </div>
                <span class="notif-stok">Habis</span>
            </a>
            <?php endforeach; ?>

            <?php foreach ($produkTipis as $p): ?>
            <a href="stok.php" class="notif-item">
                <div class="notif-icon-wrap orange">
                    <?= $p['icon'] ?? '📦' ?>
                </div>
                <div class="notif-info">
                    <strong><?= htmlspecialchars($p['nama_produk']) ?></strong>
                    <span><?= htmlspecialchars($p['nama_kategori'] ?? '-') ?></span>
                </div>
                <span class="notif-stok orange"><?= $p['stok'] ?> stok</span>
            </a>
            <?php endforeach; ?>
        </div>

        <?php if ($totalNotif > 0): ?>
        <div class="notif-footer">
            <a href="stok.php">Lihat semua stok →</a>
        </div>
        <?php endif; ?>
    </div>
</div>
