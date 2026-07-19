<?php
require_once '../config/database.php';
cekAkses(['admin']);

// Handle Create/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $username = escape($_POST['username']);
    $role = escape($_POST['role']);
    
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $password_field = ", password = '$password'";
    } else {
        $password_field = "";
    }
    
    if (isset($_POST['edit_id']) && $_POST['edit_id'] > 0) {
        $id = (int)$_POST['edit_id'];
        query("UPDATE users SET username='$username', role='$role' $password_field WHERE id=$id");
    } else {
        query("INSERT INTO users (username, password, role) VALUES ('$username', MD5('123456'), '$role')");
    }
    alert("User berhasil disimpan");
    redirect("users.php");
}

// Handle Delete
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    if ($id != $_SESSION['user_id']) {
        query("DELETE FROM users WHERE id = $id");
        alert("User dihapus");
    } else {
        alert("Tidak bisa menghapus akun sendiri!");
    }
    redirect("users.php");
}

$users = fetchAll(query("SELECT * FROM users ORDER BY id DESC"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen User</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Manajemen User</h1>
            
            <div class="form-card">
                <h3>Tambah/Edit User</h3>
                <form method="POST" id="formUser">
                    <input type="hidden" name="edit_id" id="edit_id" value="0">
                    <input type="text" name="username" id="username" placeholder="Username" required>
                    <input type="password" name="password" id="password" placeholder="Password (kosongkan jika tidak diubah)">
                    <select name="role" id="role" required>
                        <option value="siswa">Siswa</option>
                        <option value="panitia">Panitia PPDB</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" name="simpan">Simpan User</button>
                    <button type="button" onclick="resetForm()" style="background:#95a5a6">Batal</button>
                </form>
                <small>Default password untuk user baru: 123456</small>
            </div>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                            <span style="padding:3px 8px; border-radius:3px; background: 
                                <?= $u['role'] == 'admin' ? '#e74c3c' : ($u['role'] == 'panitia' ? '#f39c12' : '#3498db') ?>; color:white">
                                <?= strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td><?= $u['created_at'] ?></td>
                        <td>
                            <a href="#" onclick="editUser(<?= $u['id'] ?>, '<?= addslashes($u['username']) ?>', '<?= $u['role'] ?>')">Edit</a>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            | <a href="?hapus=<?= $u['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    function editUser(id, username, role) {
        document.getElementById('edit_id').value = id;
        document.getElementById('username').value = username;
        document.getElementById('role').value = role;
        document.getElementById('password').placeholder = "Kosongkan jika tidak ingin mengubah password";
    }
    
    function resetForm() {
        document.getElementById('edit_id').value = 0;
        document.getElementById('formUser').reset();
        document.getElementById('password').placeholder = "Password (kosongkan jika tidak diubah)";
    }
    </script>
</body>
</html>