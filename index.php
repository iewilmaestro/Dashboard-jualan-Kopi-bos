<?php
session_start();
require_once 'lib/data.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $users = loadData('users.json');
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            // Login sukses
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            header('Location: dashboard.php');
            exit;
        }
    }
	
    $error = "Username atau password salah!";
}
?>

<form method="post">
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
</form>
