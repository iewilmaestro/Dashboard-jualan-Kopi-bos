<?php
session_start();
require_once 'lib/data.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$products = loadData('products.json');

// Proses form penambahan stok
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['tambah'] as $id => $tambahStok) {
        foreach ($products as &$p) {
            if ($p['id'] == $id) {
                $p['stok'] += max(0, (int)$tambahStok);
                break;
            }
        }
    }
    unset($p); // lepas reference
    saveData('products.json', $products);
    header('Location: tambah-stok.php?success=1');
    exit;
}

// Ambil daftar produk hampir habis
$hampir_habis = array_filter($products, fn($p) => $p['stok'] < 5);
?>

<h2>Tambah Stok Produk</h2>
<a href="dashboard.php">← Kembali</a><br><br>

<?php if (isset($_GET['success'])): ?>
<p style="color:green;">Stok berhasil ditambahkan.</p>
<?php endif; ?>

<!-- Notifikasi stok hampir habis -->
<?php if (count($hampir_habis) > 0): ?>
<div style="border: 1px solid red; padding: 10px; background: #ffe0e0; margin-bottom: 15px;">
  <strong>⚠️ Stok Hampir Habis:</strong>
  <ul>
    <?php foreach ($hampir_habis as $p): ?>
      <li><?= htmlspecialchars($p['nama']) ?> — stok: <?= $p['stok'] ?></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<!-- Pencarian -->
<input type="text" id="cari" placeholder="Cari produk..." style="margin-bottom: 10px; padding: 5px; width: 250px;">

<form method="post">
  <table border="1" cellpadding="5" cellspacing="0" id="tabel-produk">
    <thead>
      <tr>
        <th>Nama Produk</th>
        <th>Harga (Rp)</th>
        <th>Stok Saat Ini</th>
        <th>Status</th>
        <th>Tambah Stok</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td><?= htmlspecialchars($p['nama']) ?></td>
        <td><?= number_format($p['harga'], 0, ',', '.') ?></td>
        <td><?= $p['stok'] ?></td>
        <td>
          <?php if ($p['stok'] < 5): ?>
            <span style="color: red; font-weight: bold;">⚠️ Hampir habis</span>
          <?php else: ?>
            <span style="color: green; font-weight: bold;">✅ Aman</span>
          <?php endif; ?>
        </td>
        <td>
          <input type="number" name="tambah[<?= $p['id'] ?>]" value="0" min="0" style="width: 80px;">
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table><br>
  <button type="submit">Tambah Stok</button>
</form>

<script>
// Pencarian baris produk
document.getElementById('cari').addEventListener('input', function () {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll('#tabel-produk tbody tr');
  rows.forEach(row => {
    const nama = row.children[0].textContent.toLowerCase();
    row.style.display = nama.includes(filter) ? '' : 'none';
  });
});
</script>
