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
        $query = "UPDATE users SET username='$username', role='$role' $password_field WHERE id=$id";
        if (query($query)) {
            alert("User berhasil diupdate");
        }
    } else {
        $default_password = md5('123456');
        $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$default_password', '$role')";
        if (query($query)) {
            alert("User baru berhasil ditambahkan! Default password: 123456");
        }
    }
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

// Handle Reset Password
if (isset($_GET['reset_password'])) {
    $id = (int)$_GET['reset_password'];
    $new_password = md5('123456');
    if (query("UPDATE users SET password = '$new_password' WHERE id = $id")) {
        alert("Password telah direset menjadi: 123456");
    }
    redirect("users.php");
}

// Handle Change Password sendiri
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $old_password = md5($_POST['old_password']);
    $new_password = md5($_POST['new_password']);
    $user_id = $_SESSION['user_id'];
    
    $check = query("SELECT id FROM users WHERE id = $user_id AND password = '$old_password'");
    if (mysqli_num_rows($check) > 0) {
        query("UPDATE users SET password = '$new_password' WHERE id = $user_id");
        alert("Password berhasil diubah!");
    } else {
        alert("Password lama salah!");
    }
    redirect("users.php");
}

$users = fetchAll(query("SELECT * FROM users ORDER BY id DESC"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen User - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .password-info {
            background: #e8f4f8;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            font-size: 12px;
        }
        .btn-reset {
            background: #f39c12;
            color: white;
            padding: 3px 8px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
        }
        .btn-change {
            background: #3498db;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>👥 Manajemen User</h1>
            
            <!-- Form Ganti Password untuk User Login -->
            <div class="form-card">
                <h3>🔐 Ganti Password Saya</h3>
                <form method="POST" onsubmit="return confirm('Yakin ingin mengganti password?')">
                    <div class="form-group">
                        <label>Password Lama</label>
                        <input type="password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn-change">Ganti Password</button>
                </form>
            </div>
            
            <!-- Form Tambah/Edit User -->
            <div class="form-card">
                <h3>➕ Tambah/Edit User</h3>
                <form method="POST" id="formUser">
                    <input type="hidden" name="edit_id" id="edit_id" value="0">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="username" placeholder="Username" required>
                    </div>
                    <div class="form-group">
                        <label>Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" id="password" placeholder="Isi untuk mengubah password">
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" id="role" required>
                            <option value="siswa">Siswa</option>
                            <option value="panitia">Panitia PPDB</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="simpan">Simpan User</button>
                    <button type="button" onclick="resetForm()" style="background:#95a5a6">Batal</button>
                </form>
                <div class="password-info">
                    💡 <strong>Informasi:</strong> User baru akan memiliki password default: <strong>123456</strong>
                </div>
            </div>
            
            <!-- Tabel Daftar User -->
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
                            <a href="#" onclick="editUser(<?= $u['id'] ?>, '<?= addslashes($u['username']) ?>', '<?= $u['role'] ?>')">✏️ Edit</a>
                            | <a href="?reset_password=<?= $u['id'] ?>" onclick="return confirm('Reset password user ini menjadi 123456?')" class="btn-reset">🔑 Reset Password</a>
                            <?php if ($u['id'] != $_SESSION['user_id']): ?>
                            | <a href="?hapus=<?= $u['id'] ?>" onclick="return confirm('Yakin hapus user ini?')">🗑️ Hapus</a>
                            <?php else: ?>
                            | <span style="color:#999">(Akun Anda)</span>
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
        document.getElementById('password').placeholder = "Isi jika ingin mengubah password";
        document.getElementById('password').value = "";
    }
    
    function resetForm() {
        document.getElementById('edit_id').value = 0;
        document.getElementById('formUser').reset();
        document.getElementById('password').placeholder = "Password (kosongkan jika tidak diubah)";
    }
    </script>
</body>
</html>