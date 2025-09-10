<?php
session_start();
require_once 'lib/data.php';
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $stok = (int) ($_POST['stok'] ?? 0);
    $harga = (int) ($_POST['harga'] ?? 0);

    if ($nama === '' || $stok < 0 || $harga < 0) {
        $error = "Input tidak valid.";
    } else {
        $products = loadData('products.json');
        $newId = count($products) > 0 ? max(array_column($products, 'id')) + 1 : 1;
        $products[] = [
            "id" => $newId,
            "nama" => $nama,
            "stok" => $stok,
            "harga" => $harga
        ];
        saveData('products.json', $products);
        header('Location: dashboard.php');
        exit;
    }
}
?>

<h2>Tambah Produk Baru</h2>
<a href="dashboard.php">← Kembali</a><br><br>
<?php if ($error): ?>
<p style="color:red;"><?= $error ?></p>
<?php endif; ?>

<form method="post">
  Nama Produk:<br>
  <input type="text" name="nama" required><br><br>
  Stok:<br>
  <input type="number" name="stok" min="0" required><br><br>
  Harga (Rp):<br>
  <input type="number" name="harga" min="0" required><br><br>
  <button type="submit">Simpan Produk</button>
</form>
