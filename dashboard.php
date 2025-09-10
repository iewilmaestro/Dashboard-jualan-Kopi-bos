<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}
$role = $_SESSION['role'] ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Penjual</title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f4f6f8;
      color: #333;
    }
    header {
      background-color: #34495e;
      color: #ecf0f1;
      padding: 15px 20px;
      text-align: center;
      font-size: 1.5em;
      font-weight: bold;
    }
    .container {
      display: flex;
      min-height: calc(100vh - 90px);
    }
    nav.sidebar {
      width: 220px;
      background-color: #2c3e50;
      color: #ecf0f1;
      padding-top: 20px;
      flex-shrink: 0;
    }
    nav.sidebar ul {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }
    nav.sidebar ul li {
      padding: 12px 20px;
    }
    nav.sidebar ul li a {
      color: #ecf0f1;
      text-decoration: none;
      display: block;
      transition: background-color 0.3s ease;
      border-radius: 4px;
    }
    nav.sidebar ul li a:hover,
    nav.sidebar ul li a.active {
      background-color: #1abc9c;
      color: white;
    }
    main.content {
      flex-grow: 1;
      padding: 25px 30px;
      background-color: white;
      box-shadow: 0 0 8px rgba(0,0,0,0.05);
      overflow-y: auto;
    }
    footer {
      background-color: #34495e;
      color: #ecf0f1;
      padding: 15px 20px;
      text-align: center;
      font-size: 0.9em;
    }
  </style>
</head>
<body>
  <header>
    Dashboard Penjual
  </header>
  <div class="container">
    <nav class="sidebar">
      <ul>
        <?php if ($role === 'kasir'): ?>
          <li><a href="tambah-order.php">Tambah Order</a></li>
          <li><a href="pending-orders.php">Pending Order</a></li>
          <li><a href="riwayat-transaksi.php">Laporan Penjualan</a></li>
          <li><a href="change-password.php">Ganti Password</a></li>
          <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
          <li><a href="tambah-produk.php">Tambah Produk</a></li>
          <li><a href="tambah-order.php">Tambah Order</a></li>
          <li><a href="pending-orders.php">Pending Order</a></li>
          <li><a href="riwayat-transaksi.php">Laporan Penjualan</a></li>
          <li><a href="tambah-stok.php">Update Stok</a></li>
          <li><a href="register.php">Tambah User</a></li>
          <li><a href="change-password.php">Ganti Password</a></li>
          <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
      </ul>
    </nav>
    <main class="content">
      <h2>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
      <p>Role Anda: <strong><?= htmlspecialchars($role) ?></strong></p>
      <!-- Konten dashboard lainnya bisa kamu tambahkan di sini -->
    </main>
  </div>
  <footer>
    &copy; <?= date('Y') ?> iewilofficial. All rights reserved.
  </footer>
</body>
</html>
