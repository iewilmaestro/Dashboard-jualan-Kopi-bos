<?php
session_start();
require_once 'lib/data.php';
require_once 'layout.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$products = loadData('products.json');
$pending_orders = loadData('pending_orders.json');

function getProductName($id, $products) {
    foreach ($products as $p) {
        if ($p['id'] === $id) return $p['nama'];
    }
    return 'Produk tidak ditemukan';
}

// Mulai isi konten halaman
ob_start(); // tampung output HTML di buffer
?>

<h2>Daftar Order Pending</h2>
<a href="dashboard.php" class="back-link">← Kembali</a><br><br>

<?php if (count($pending_orders) === 0): ?>
    <p>Tidak ada order pending.</p>
<?php else: ?>
    <?php foreach ($pending_orders as $order): ?>
        <div style="border:1px solid #ccc; margin-bottom:15px; padding:10px;">
            <strong>Order ID:</strong> <?= $order['id'] ?><br>
            <strong>Tanggal:</strong> <?= $order['tanggal'] ?><br>
            <ul>
                <?php 
                $total = 0;
                foreach ($order['items'] as $item):
                    foreach ($products as $p) {
                        if ($p['id'] === $item['produk_id']) {
                            $harga_satuan = $p['harga'];
                            if (!empty($item['susu'])) {
                                $harga_satuan += 1000;
                            }
                            $subtotal = $harga_satuan * $item['jumlah'];
                            $total += $subtotal;
                            $nama_produk = $p['nama'];
                        }
                    }
                ?>
                    <li>
                        <?= htmlspecialchars($nama_produk) ?> - <?= $item['jumlah'] ?> pcs 
                        <?= !empty($item['susu']) ? '(pakai susu)' : '' ?> 
                        - Rp <?= number_format($subtotal,0,',','.') ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <strong>Total Harga:</strong> Rp <?= number_format($total,0,',','.') ?><br><br>

            <a href="approve-order.php?id=<?= $order['id'] ?>" class="approve" onclick="return confirm('Setujui order ini?')">Approve</a>  
            <a href="cancel-order.php?id=<?= $order['id'] ?>" class="cancel" onclick="return confirm('Batalkan order ini?')">Cancel</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
// Ambil isi buffer dan simpan di variabel
$content = ob_get_clean();

// Tampilkan halaman lengkap dengan layout
layout('Pending Orders', $content);
