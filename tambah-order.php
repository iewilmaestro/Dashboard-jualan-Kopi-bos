<?php
session_start();
require_once 'lib/data.php';
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$products = loadData('products.json');
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items = [];
    foreach ($_POST['produk_id'] as $index => $produk_id) {
        $jumlah = (int) ($_POST['jumlah'][$index] ?? 0);
        $pakai_susu = isset($_POST['susu'][$index]) ? true : false;

        if ($jumlah > 0) {
            $items[] = [
                'produk_id' => (int)$produk_id,
                'jumlah' => $jumlah,
                'susu' => $pakai_susu
            ];
        }
    }

    if (count($items) === 0) {
        $error = "Minimal pilih satu produk dengan jumlah lebih dari 0.";
    } else {
        $pending_orders = loadData('pending_orders.json');
        $newId = count($pending_orders) > 0 ? max(array_column($pending_orders, 'id')) + 1 : 1;
        $pending_orders[] = [
            'id' => $newId,
            'tanggal' => date('Y-m-d H:i:s'),
            'items' => $items,
            'status' => 'pending'
        ];
        saveData('pending_orders.json', $pending_orders);
        header('Location: pending-orders.php');
        exit;
    }
}
?>

<h2>Tambah Order Rombongan</h2>
<a href="dashboard.php">← Kembali</a><br><br>

<?php if ($error): ?>
<p style="color:red;"><?= $error ?></p>
<?php endif; ?>

<label for="searchProduk">Cari Produk:</label>
<input type="text" id="searchProduk" placeholder="Ketik nama produk..." style="width: 300px; padding: 5px; margin-bottom: 10px;">

<form method="post" id="formOrder">
  <table border="1" cellpadding="5" cellspacing="0" id="produkTable" style="width: 100%;">
    <thead>
      <tr>
        <th>Produk</th>
        <th>Harga (Rp)</th>
        <th>Jumlah</th>
        <th>Pakai Susu (+Rp 1.000)</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($products as $index => $p): ?>
      <tr class="produk-row">
        <td class="nama-produk"><?= htmlspecialchars($p['nama']) ?></td>
        <td><?= number_format($p['harga'],0,',','.') ?></td>
        <td>
          <input type="hidden" name="produk_id[]" value="<?= $p['id'] ?>">
          <input type="number" name="jumlah[]" min="0" value="0" style="width: 60px;">
        </td>
        <td style="text-align:center;">
          <input type="checkbox" name="susu[<?= $index ?>]" value="1">
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table><br>

  <button type="submit">Simpan Order</button>
</form>

<script>
// Fungsi filter produk berdasarkan input pencarian
document.getElementById('searchProduk').addEventListener('input', function() {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll('#produkTable tbody .produk-row');

  rows.forEach(row => {
    const namaProduk = row.querySelector('.nama-produk').textContent.toLowerCase();
    if (namaProduk.includes(filter)) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});
</script>
