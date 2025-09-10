<?php
session_start();
require_once 'lib/data.php';
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$transactions = loadData('transactions.json');
$products = loadData('products.json');

function getProductName($id, $products) {
    foreach ($products as $p) {
        if ($p['id'] === $id) return $p['nama'];
    }
    return 'Produk tidak ditemukan';
}

// Ambil filter dari query params
$filter_day = $_GET['filter_day'] ?? '';
$filter_month = $_GET['filter_month'] ?? '';

$filteredTransactions = [];

// Filter transaksi berdasarkan filter_day dan filter_month
foreach ($transactions as $t) {
    $t_date = date('Y-m-d', strtotime($t['tanggal']));
    $t_month = date('Y-m', strtotime($t['tanggal']));

    if ($filter_day && $t_date !== $filter_day) continue;
    if ($filter_month && $t_month !== $filter_month) continue;

    $filteredTransactions[] = $t;
}

// Hitung total pemasukan dari transaksi yang sudah difilter
$totalPemasukan = 0;
foreach ($filteredTransactions as $t) {
    $totalPemasukan += $t['harga_total'];
}

?>

<h2>Riwayat Transaksi</h2>
<a href="dashboard.php">← Kembali</a><br><br>

<form method="get" style="margin-bottom:20px;">
  <label>
    Filter Hari: 
    <input type="date" name="filter_day" value="<?= htmlspecialchars($filter_day) ?>">
  </label>
  &nbsp;&nbsp;
  <label>
    Filter Bulan: 
    <input type="month" name="filter_month" value="<?= htmlspecialchars($filter_month) ?>">
  </label>
  &nbsp;&nbsp;
  <button type="submit">Filter</button>
  &nbsp;&nbsp;
  <a href="riwayat-transaksi.php">Reset</a>
</form>

<h3>Total Pemasukan: Rp <?= number_format($totalPemasukan,0,',','.') ?></h3>

<?php if (count($filteredTransactions) === 0): ?>
<p>Tidak ada transaksi pada periode ini.</p>
<?php else: ?>
<table border="1" cellpadding="5" cellspacing="0">
  <tr>
    <th>ID Transaksi</th>
    <th>Produk</th>
    <th>Jumlah</th>
    <th>Pakai Susu</th>
    <th>Total Harga (Rp)</th>
    <th>Tanggal</th>
  </tr>
  <?php foreach ($filteredTransactions as $t): ?>
  <tr>
    <td><?= $t['id'] ?></td>
    <td><?= htmlspecialchars(getProductName($t['produk_id'], $products)) ?></td>
    <td><?= $t['jumlah'] ?></td>
    <td><?= $t['susu'] ? 'Ya' : 'Tidak' ?></td>
    <td><?= number_format($t['harga_total'],0,',','.') ?></td>
    <td><?= $t['tanggal'] ?></td>
  </tr>
  <?php endforeach; ?>
</table>
<?php endif; ?>
