<?php
// layout.php
function layout($title, $content) {
	
    $role = $_SESSION['role'] ?? '';
	
	// badge pending order
	$pending_orders = loadData('pending_orders.json');
	$jml_pending = count($pending_orders);
	
	// badge stok tipis
	$products = loadData('products.json');
	$jml_stok_tipis = 0;
	foreach ($products as $p) {
		if ($p['stok'] < 5) {
			$jml_stok_tipis++;
		}
	}
	
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title><?= htmlspecialchars($title) ?></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
		<div class="page-wrapper">
        <header>
			<img src="logo.png" alt="Logo" class="logo" />
			<span class="header-title">Dashboard</span>
		</header>

        <div class="container">
            <nav class="sidebar">
                <ul>
                    <?php if ($role === 'kasir'): ?>
						<li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="tambah-order.php">Tambah Order</a></li>
                        <li>
							<a href="pending-orders.php">Pending Order
							<?php if ($jml_pending > 0): ?>
							  <span class="badge badge-warning"><?= $jml_pending ?></span>
							<?php endif; ?>
							</a>
						</li>
                        <li><a href="riwayat-transaksi.php">Laporan Penjualan</a></li>
                        <li><a href="change-password.php">Ganti Password</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
						<li><a href="dashboard.php">Dashboard</a></li>
                        <li><a href="tambah-produk.php">Tambah Produk</a></li>
                        <li><a href="tambah-order.php">Tambah Order</a></li>
                        <li>
							<a href="pending-orders.php">Pending Order
							<?php if ($jml_pending > 0): ?>
							  <span class="badge badge-warning"><?= $jml_pending ?></span>
							<?php endif; ?>
							</a>
						</li>
                        <li><a href="riwayat-transaksi.php">Laporan Penjualan</a></li>
						<li>
						  <a href="tambah-stok.php">
							Update Stok
							<?php if ($jml_stok_tipis > 0): ?>
							  <span class="badge badge-warning"><?= $jml_stok_tipis ?></span>
							<?php endif; ?>
						  </a>
						</li>
                        <li><a href="register.php">Tambah User</a></li>
                        <li><a href="change-password.php">Ganti Password</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <main class="content">
                <?= $content ?>
            </main>
        </div>
        <footer>
            &copy; <?= date('Y') ?> Dashboard Penjual. All rights reserved.
        </footer>
		</div>
    </body>
    </html>
    <?php
}
