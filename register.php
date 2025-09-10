<?php
session_start();
require_once 'lib/data.php';

if (!isset($_SESSION['username'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    echo "Anda tidak memiliki akses ke halaman ini.";
    exit;
}

$users = loadData('users.json');
$error = '';
$success = '';

// Proses hapus user
if (isset($_GET['hapus'])) {
    $hapusUsername = $_GET['hapus'];

    // Jangan hapus diri sendiri
    if ($hapusUsername === $_SESSION['username']) {
        $error = "Anda tidak bisa menghapus user sendiri.";
    } else {
        $found = false;
        foreach ($users as $key => $user) {
            if ($user['username'] === $hapusUsername) {
                unset($users[$key]);
                $found = true;
                break;
            }
        }
        if ($found) {
            // Reindex array
            $users = array_values($users);
            saveData('users.json', $users);
            $success = "User '$hapusUsername' berhasil dihapus.";
        } else {
            $error = "User tidak ditemukan.";
        }
    }
}

// Proses update role user
if (isset($_POST['update_role'])) {
    $updateUsername = $_POST['update_username'] ?? '';
    $newRole = $_POST['new_role'] ?? '';

    if ($updateUsername === '') {
        $error = "Username tidak valid.";
    } elseif (!in_array($newRole, ['admin', 'kasir'])) {
        $error = "Role tidak valid.";
    } else {
        $found = false;
        foreach ($users as &$user) {
            if ($user['username'] === $updateUsername) {
                $user['role'] = $newRole;
                $found = true;
                break;
            }
        }
        unset($user);
        if ($found) {
            saveData('users.json', $users);
            $success = "Role user '$updateUsername' berhasil diupdate.";
        } else {
            $error = "User tidak ditemukan.";
        }
    }
}

// Proses tambah user baru (sama seperti sebelumnya)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_user'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? '';

    if ($username === '' || $password === '' || !in_array($role, ['admin', 'kasir'])) {
        $error = "Semua field harus diisi dengan benar.";
    } else {
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $error = "Username sudah digunakan.";
                break;
            }
        }
        if ($error === '') {
            $users[] = [
                'username' => $username,
                'password' => $password,
                'role' => $role
            ];
            saveData('users.json', $users);
            $success = "User baru berhasil didaftarkan.";
        }
    }
}
?>

<h2>Registrasi User Baru (Admin)</h2>
<a href="dashboard.php">← Kembali</a><br><br>

<?php if ($error !== ''): ?>
    <p style="color: red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<?php if ($success !== ''): ?>
    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<!-- Form Registrasi User -->
<form method="post" style="margin-bottom: 30px;">
    <input type="hidden" name="register_user" value="1">
    <label>Username:</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password:</label><br>
    <input type="password" name="password" required><br><br>

    <label>Role:</label><br>
    <select name="role" required>
        <option value="">-- Pilih Role --</option>
        <option value="admin">Admin</option>
        <option value="kasir">Kasir</option>
    </select><br><br>

    <button type="submit">Daftarkan User</button>
</form>

<h3>Daftar User Terdaftar</h3>
<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>Username</th>
		<th>Password</th>
        <th>Role</th>
        <th>Aksi</th>
    </tr>
    <?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['username']) ?></td>
		<td><?= htmlspecialchars($user['password']) ?></td>
        <td>
            <form method="post" style="margin:0;">
                <input type="hidden" name="update_username" value="<?= htmlspecialchars($user['username']) ?>">
                <select name="new_role" onchange="this.form.submit()">
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="kasir" <?= $user['role'] === 'kasir' ? 'selected' : '' ?>>Kasir</option>
                </select>
                <input type="hidden" name="update_role" value="1">
            </form>
        </td>
        <td>
            <?php if ($user['username'] !== $_SESSION['username']): ?>
                <a href="?hapus=<?= urlencode($user['username']) ?>" onclick="return confirm('Yakin hapus user ini?');">Hapus</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
