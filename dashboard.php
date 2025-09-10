<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}
$role = $_SESSION['role'] ?? '';

require_once 'layout.php';

$content = '<p>Selamat datang, '.htmlspecialchars($_SESSION["username"]) .'</p>';
layout('Dashboard', $content);
