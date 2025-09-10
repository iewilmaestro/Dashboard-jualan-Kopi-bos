<?php
session_start();
require_once 'lib/data.php';
require_once 'layout.php';
if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

$users = loadData('users.json');
$error = '';
$success = '';

$currentUsername = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPassword = $_POST['old_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Cari user yang login
    $userIndex = null;
    foreach ($users as $i => $user) {
        if ($user['username'] === $currentUsername) {
            $userIndex = $i;
            break;
        }
    }

    if ($userIndex === null) {
        $error = "User tidak ditemukan.";
    } elseif ($users[$userIndex]['password'] !== $oldPassword) {
        // Untuk sekarang masih password plain text, jadi cek langsung
        $error = "Password lama salah.";
    } elseif (strlen($newPassword) < 4) {
        $error = "Password baru minimal 4 karakter.";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Password konfirmasi tidak cocok.";
    } else {
        // Update password
        $users[$userIndex]['password'] = $newPassword;
        saveData('users.json', $users);
        $success = "Password berhasil diubah.";
    }
}
ob_start();
?>

<h2>Ganti Password</h2>
<a href="dashboard.php" class="back-link">← Kembali</a><br><br>

<?php if ($error !== ''): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success !== ''): ?>
    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="post">
    <label>Password Lama:</label><br>
    <input type="password" name="old_password" required><br><br>

    <label>Password Baru:</label><br>
    <input type="password" name="new_password" required><br><br>

    <label>Konfirmasi Password Baru:</label><br>
    <input type="password" name="confirm_password" required><br><br>

    <button type="submit">Ubah Password</button>
</form>
<?php
// Ambil isi buffer dan simpan di variabel
$content = ob_get_clean();

// Tampilkan halaman lengkap dengan layout
layout('Ganti Password', $content);
