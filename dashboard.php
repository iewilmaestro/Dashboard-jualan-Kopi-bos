<?php
session_start();

require_once 'layout.php';
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
// Inisialisasi array penampung hasil
$result = [];

// Loop transaksi
foreach ($filteredTransactions as $t) {
	$totalPemasukan += $t['harga_total'];
    // Buat key unik berdasarkan produk_id dan status susu (pakai susu atau tidak)
    $key = $t['produk_id'] . '-' . (!empty($t['susu']) ? 'susu' : 'non');

    if (!isset($result[$key])) {
        $result[$key] = [
            'produk_id' => $t['produk_id'],
            'susu' => !empty($t['susu']),
            'jumlah' => 0,
			'harga_total' => 0,
        ];
    }
	$result[$key]['harga_total'] += $t['harga_total'];
    $result[$key]['jumlah'] += $t['jumlah'];
}
ob_start();
?>
<p>Selamat datang, <?=htmlspecialchars($_SESSION["username"]) ?></p>

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
  <a href="dashboard.php" class="reset-link">Reset</a>
</form>

<h3>Total Pemasukan: Rp <?= number_format($totalPemasukan,0,',','.') ?></h3>

<?php if (count($filteredTransactions) === 0): ?>
	<p>Tidak ada transaksi pada periode ini.</p>
<?php else: ?>
	<table border="1" cellpadding="5" cellspacing="0">
	  <tr>
		<th>Produk</th>
		<th>Jumlah</th>
		<th>Total Harga (Rp)</th>
	  </tr>
	  <?php foreach ($result as $r): 
		$nama = getProductName($r['produk_id'], $products);
		$susu = $r['susu'] ? '+ susu' : '';
	  ?>
	  <tr>
		<td><?= htmlspecialchars("$nama $susu") ?></td>
		<td><?= $r['jumlah'] ?></td>
		<td><?= number_format($r['harga_total'],0,',','.') ?></td>
	  </tr>
	  <?php endforeach; ?>
	</table>
<?php endif; ?>

<?php
// Ambil isi buffer dan simpan di variabel
$content = ob_get_clean();

// Tampilkan halaman lengkap dengan layout
layout('Dashboard', $content);
