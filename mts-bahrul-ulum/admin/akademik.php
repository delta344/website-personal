<?php
require_once '../config/database.php';
cekAkses(['admin']);

// Handle Update Kurikulum
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_kurikulum'])) {
    $isi = escape($_POST['isi']);
    $tahun = escape($_POST['tahun_ajaran']);
    
    $check = query("SELECT id FROM akademik WHERE tipe='kurikulum'");
    if (mysqli_num_rows($check) > 0) {
        query("UPDATE akademik SET isi='$isi', tahun_ajaran='$tahun' WHERE tipe='kurikulum'");
    } else {
        query("INSERT INTO akademik (tipe, isi, tahun_ajaran) VALUES ('kurikulum', '$isi', '$tahun')");
    }
    alert("Kurikulum berhasil diupdate");
    redirect("akademik.php");
}

// Handle Upload Kalender
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_kalender'])) {
    $judul = escape($_POST['judul']);
    
    if ($_FILES['file_kalender']['name']) {
        $target = "../assets/uploads/kalender/" . time() . "_" . basename($_FILES['file_kalender']['name']);
        if (move_uploaded_file($_FILES['file_kalender']['tmp_name'], $target)) {
            $file = str_replace("../", "", $target);
            $check = query("SELECT id FROM akademik WHERE tipe='kalender'");
            if (mysqli_num_rows($check) > 0) {
                query("UPDATE akademik SET judul='$judul', file_upload='$file' WHERE tipe='kalender'");
            } else {
                query("INSERT INTO akademik (tipe, judul, file_upload) VALUES ('kalender', '$judul', '$file')");
            }
            alert("Kalender pendidikan berhasil diupload");
        }
    }
    redirect("akademik.php");
}

// Handle CRUD Pengajar
if (isset($_GET['hapus_pengajar'])) {
    $id = (int)$_GET['hapus_pengajar'];
    query("DELETE FROM pengajar WHERE id = $id");
    alert("Pengajar dihapus");
    redirect("akademik.php");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_pengajar'])) {
    $nama = escape($_POST['nama']);
    $nip = escape($_POST['nip']);
    $jabatan = escape($_POST['jabatan']);
    $mapel = escape($_POST['mapel']);
    
    $foto = '';
    if ($_FILES['foto']['name']) {
        $target = "../assets/uploads/pengajar/" . time() . "_" . basename($_FILES['foto']['name']);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $foto = ", foto = '" . str_replace("../", "", $target) . "'";
        }
    }
    
    if (isset($_POST['edit_id']) && $_POST['edit_id'] > 0) {
        $id = (int)$_POST['edit_id'];
        query("UPDATE pengajar SET nama='$nama', nip='$nip', jabatan='$jabatan', mapel='$mapel' $foto WHERE id=$id");
    } else {
        query("INSERT INTO pengajar (nama, nip, jabatan, mapel $foto) VALUES ('$nama', '$nip', '$jabatan', '$mapel')");
    }
    alert("Data pengajar disimpan");
    redirect("akademik.php");
}

// Ambil data
$kurikulum = fetch(query("SELECT * FROM akademik WHERE tipe='kurikulum'"));
$kalender = fetch(query("SELECT * FROM akademik WHERE tipe='kalender'"));
$pengajar = fetchAll(query("SELECT * FROM pengajar ORDER BY id DESC"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Akademik</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Manajemen Akademik</h1>
            
            <!-- Kurikulum -->
            <div class="form-card">
                <h3>Kurikulum Sekolah</h3>
                <form method="POST">
                    <textarea name="isi" rows="8" required><?= htmlspecialchars($kurikulum['isi'] ?? '') ?></textarea>
                    <input type="text" name="tahun_ajaran" placeholder="Tahun Ajaran (contoh: 2024/2025)" value="<?= $kurikulum['tahun_ajaran'] ?? '' ?>">
                    <button type="submit" name="update_kurikulum">Update Kurikulum</button>
                </form>
            </div>
            
            <!-- Kalender Pendidikan -->
            <div class="form-card">
                <h3>Kalender Pendidikan</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="judul" placeholder="Judul Kalender" required>
                    <input type="file" name="file_kalender" accept=".pdf,.jpg,.png" required>
                    <?php if (!empty($kalender['file_upload'])): ?>
                        <p>File saat ini: <a href="../<?= $kalender['file_upload'] ?>" target="_blank">Lihat Kalender</a></p>
                    <?php endif; ?>
                    <button type="submit" name="upload_kalender">Upload Kalender</button>
                </form>
            </div>
            
            <!-- Tenaga Pengajar -->
            <div class="form-card">
                <h3>Tambah/Edit Tenaga Pengajar</h3>
                <form method="POST" enctype="multipart/form-data" id="formPengajar">
                    <input type="hidden" name="edit_id" id="edit_id" value="0">
                    <input type="text" name="nama" id="nama" placeholder="Nama Lengkap" required>
                    <input type="text" name="nip" id="nip" placeholder="NIP">
                    <input type="text" name="jabatan" id="jabatan" placeholder="Jabatan (contoh: Kepala Sekolah, Guru Mapel)">
                    <input type="text" name="mapel" id="mapel" placeholder="Mata Pelajaran">
                    <input type="file" name="foto" accept="image/*">
                    <button type="submit" name="simpan_pengajar">Simpan Pengajar</button>
                    <button type="button" onclick="resetForm()" style="background:#95a5a6">Batal</button>
                </form>
            </div>
            
            <!-- Daftar Pengajar -->
            <table class="data-table">
                <thead>
                    <tr><th>Foto</th><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Mapel</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pengajar as $p): ?>
                    <tr>
                        <td>
                            <?php if ($p['foto']): ?>
                                <img src="../<?= $p['foto'] ?>" width="50" height="50" style="object-fit:cover">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['nama']) ?></td>
                        <td><?= $p['nip'] ?></td>
                        <td><?= $p['jabatan'] ?></td>
                        <td><?= $p['mapel'] ?></td>
                        <td>
                            <a href="#" onclick="editPengajar(<?= $p['id'] ?>, '<?= addslashes($p['nama']) ?>', '<?= $p['nip'] ?>', '<?= addslashes($p['jabatan']) ?>', '<?= addslashes($p['mapel']) ?>')">Edit</a>
                            <a href="?hapus_pengajar=<?= $p['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    function editPengajar(id, nama, nip, jabatan, mapel) {
        document.getElementById('edit_id').value = id;
        document.getElementById('nama').value = nama;
        document.getElementById('nip').value = nip;
        document.getElementById('jabatan').value = jabatan;
        document.getElementById('mapel').value = mapel;
    }
    
    function resetForm() {
        document.getElementById('edit_id').value = 0;
        document.getElementById('formPengajar').reset();
    }
    </script>
</body>
</html>