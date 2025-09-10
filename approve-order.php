<?php
session_start();
require_once 'lib/data.php';
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: pending-orders.php');
    exit;
}

$id = (int) $_GET['id'];

$products = loadData('products.json');
$pending_orders = loadData('pending_orders.json');
$transactions = loadData('transactions.json');

$foundIndex = null;
foreach ($pending_orders as $index => $order) {
    if ($order['id'] === $id) {
        $foundIndex = $index;
        break;
    }
}

if ($foundIndex === null) {
    header('Location: pending-orders.php');
    exit;
}

$order = $pending_orders[$foundIndex];

// Kurangi stok produk
foreach ($order['items'] as $item) {
    foreach ($products as &$p) {
        if ($p['id'] === $item['produk_id']) {
            $p['stok'] -= $item['jumlah'];
            if ($p['stok'] < 0) $p['stok'] = 0;
        }
    }
}
unset($p);

// Simpan transaksi
foreach ($order['items'] as $item) {
    $harga_satuan = 0;
    foreach ($products as $p) {
        if ($p['id'] === $item['produk_id']) {
            $harga_satuan = $p['harga'];
            if (!empty($item['susu'])) {
                $harga_satuan += 1000;
            }
        }
    }
    $transactions[] = [
        'id' => time() + rand(0,999),
        'produk_id' => $item['produk_id'],
        'jumlah' => $item['jumlah'],
        'susu' => !empty($item['susu']),
        'harga_total' => $item['jumlah'] * $harga_satuan,
        'tanggal' => date('Y-m-d H:i:s')
    ];
}

// Update file produk dan hapus order pending
saveData('products.json', $products);
unset($pending_orders[$foundIndex]);
$pending_orders = array_values($pending_orders); // reset indeks
saveData('pending_orders.json', $pending_orders);
saveData('transactions.json', $transactions);

header('Location: pending-orders.php');
exit;
