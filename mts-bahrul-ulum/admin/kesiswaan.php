<?php
require_once '../config/database.php';
cekAkses(['admin']);

// Handle Delete
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    query("DELETE FROM kesiswaan WHERE id = $id");
    alert("Data dihapus");
    redirect("kesiswaan.php");
}

// Handle Insert/Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $tipe = escape($_POST['tipe']);
    $judul = escape($_POST['judul']);
    $deskripsi = escape($_POST['deskripsi']);
    $tanggal = !empty($_POST['tanggal']) ? escape($_POST['tanggal']) : 'NULL';
    
    $foto = '';
    if ($_FILES['foto']['name']) {
        $folder = ($tipe == 'eskul') ? 'eskul' : (($tipe == 'osis') ? 'osis' : 'prestasi');
        $target = "../assets/uploads/$folder/" . time() . "_" . basename($_FILES['foto']['name']);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $foto = ", foto = '" . str_replace("../", "", $target) . "'";
        }
    }
    
    if (isset($_POST['edit_id']) && $_POST['edit_id'] > 0) {
        $id = (int)$_POST['edit_id'];
        $query = "UPDATE kesiswaan SET tipe='$tipe', judul='$judul', deskripsi='$deskripsi', tanggal=$tanggal $foto WHERE id=$id";
    } else {
        $query = "INSERT INTO kesiswaan (tipe, judul, deskripsi, tanggal $foto) VALUES ('$tipe', '$judul', '$deskripsi', $tanggal)";
    }
    
    if (query($query)) {
        alert("Data berhasil disimpan");
        redirect("kesiswaan.php");
    }
}

// Ambil data berdasarkan tipe
$eskul = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe='eskul' ORDER BY id DESC"));
$osis = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe='osis' ORDER BY id DESC"));
$prestasi = fetchAll(query("SELECT * FROM kesiswaan WHERE tipe='prestasi' ORDER BY tanggal DESC"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Kesiswaan</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Manajemen Kesiswaan</h1>
            
            <!-- Form Tambah/Edit -->
            <div class="form-card">
                <h3>Tambah/Edit Data</h3>
                <form method="POST" enctype="multipart/form-data" id="formKesiswaan">
                    <input type="hidden" name="edit_id" id="edit_id" value="0">
                    <select name="tipe" id="tipe" required>
                        <option value="">Pilih Tipe</option>
                        <option value="eskul">Ekstrakurikuler</option>
                        <option value="osis">Organisasi (OSIS)</option>
                        <option value="prestasi">Prestasi</option>
                    </select>
                    <input type="text" name="judul" id="judul" placeholder="Judul/Nama" required>
                    <textarea name="deskripsi" id="deskripsi" rows="5" placeholder="Deskripsi lengkap"></textarea>
                    <input type="date" name="tanggal" id="tanggal">
                    <input type="file" name="foto" accept="image/*">
                    <button type="submit" name="simpan">Simpan</button>
                    <button type="button" onclick="resetForm()" style="background:#95a5a6">Batal</button>
                </form>
            </div>
            
            <!-- Tabel Ekstrakurikuler -->
            <h2>Ekstrakurikuler</h2>
            <table class="data-table">
                <thead><tr><th>Foto</th><th>Nama Eskul</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($eskul as $e): ?>
                    <tr>
                        <td><?php if($e['foto']) echo "<img src='../{$e['foto']}' width='50'>"; else echo "-"; ?></td>
                        <td><?= htmlspecialchars($e['judul']) ?></td>
                        <td><?= substr(htmlspecialchars($e['deskripsi']), 0, 100) ?>...</td>
                        <td>
                            <a href="#" onclick="editData(<?= $e['id'] ?>, '<?= addslashes($e['tipe']) ?>', '<?= addslashes($e['judul']) ?>', '<?= addslashes($e['deskripsi']) ?>', '<?= $e['tanggal'] ?>')">Edit</a>
                            <a href="?hapus=<?= $e['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Tabel OSIS -->
            <h2>Organisasi (OSIS)</h2>
            <table class="data-table">
                <thead><tr><th>Foto</th><th>Nama Organisasi</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($osis as $o): ?>
                    <tr>
                        <td><?php if($o['foto']) echo "<img src='../{$o['foto']}' width='50'>"; else echo "-"; ?></td>
                        <td><?= htmlspecialchars($o['judul']) ?></td>
                        <td><?= substr(htmlspecialchars($o['deskripsi']), 0, 100) ?>...</td>
                        <td>
                            <a href="#" onclick="editData(<?= $o['id'] ?>, '<?= addslashes($o['tipe']) ?>', '<?= addslashes($o['judul']) ?>', '<?= addslashes($o['deskripsi']) ?>', '<?= $o['tanggal'] ?>')">Edit</a>
                            <a href="?hapus=<?= $o['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Tabel Prestasi -->
            <h2>Prestasi Siswa</h2>
            <table class="data-table">
                <thead><tr><th>Foto</th><th>Judul Prestasi</th><th>Tanggal</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php foreach ($prestasi as $p): ?>
                    <tr>
                        <td><?php if($p['foto']) echo "<img src='../{$p['foto']}' width='50'>"; else echo "-"; ?></td>
                        <td><?= htmlspecialchars($p['judul']) ?></td>
                        <td><?= $p['tanggal'] ?></td>
                        <td><?= substr(htmlspecialchars($p['deskripsi']), 0, 100) ?>...</td>
                        <td>
                            <a href="#" onclick="editData(<?= $p['id'] ?>, '<?= addslashes($p['tipe']) ?>', '<?= addslashes($p['judul']) ?>', '<?= addslashes($p['deskripsi']) ?>', '<?= $p['tanggal'] ?>')">Edit</a>
                            <a href="?hapus=<?= $p['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    function editData(id, tipe, judul, deskripsi, tanggal) {
        document.getElementById('edit_id').value = id;
        document.getElementById('tipe').value = tipe;
        document.getElementById('judul').value = judul;
        document.getElementById('deskripsi').value = deskripsi;
        document.getElementById('tanggal').value = tanggal;
    }
    
    function resetForm() {
        document.getElementById('edit_id').value = 0;
        document.getElementById('formKesiswaan').reset();
    }
    </script>
</body>
</html>