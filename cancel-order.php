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

$pending_orders = loadData('pending_orders.json');

$foundIndex = null;
foreach ($pending_orders as $index => $order) {
    if ($order['id'] === $id) {
        $foundIndex = $index;
        break;
    }
}

if ($foundIndex !== null) {
    unset($pending_orders[$foundIndex]);
    $pending_orders = array_values($pending_orders);
    saveData('pending_orders.json', $pending_orders);
}

header('Location: pending-orders.php');
exit;
