<?php
require_once '../config/database.php';
cekAkses(['admin']);

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipe = escape($_POST['tipe']);
    $konten = escape($_POST['konten']);
    
    // Upload foto jika ada
    $foto = '';
    if ($_FILES['foto']['name']) {
        $target = "../assets/uploads/profil/" . time() . "_" . basename($_FILES['foto']['name']);
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $foto = ", foto = '" . str_replace("../", "", $target) . "'";
        }
    }
    
    $query = "UPDATE profil SET konten = '$konten' $foto WHERE tipe = '$tipe'";
    if (query($query)) {
        alert("Profil berhasil diupdate");
        redirect("profil.php");
    }
}

// Ambil data profil
$profil = [];
$result = query("SELECT * FROM profil");
while ($row = fetch($result)) {
    $profil[$row['tipe']] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Profil</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Manajemen Profil Sekolah</h1>
            
            <!-- Form Visi -->
            <div class="form-card">
                <h3>Visi Sekolah</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe" value="visi">
                    <textarea name="konten" rows="5" required><?= htmlspecialchars($profil['visi']['konten'] ?? '') ?></textarea>
                    <button type="submit">Update Visi</button>
                </form>
            </div>
            
            <!-- Form Misi -->
            <div class="form-card">
                <h3>Misi Sekolah</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe" value="misi">
                    <textarea name="konten" rows="10" required><?= htmlspecialchars($profil['misi']['konten'] ?? '') ?></textarea>
                    <button type="submit">Update Misi</button>
                </form>
            </div>
            
            <!-- Form Sejarah -->
            <div class="form-card">
                <h3>Sejarah Sekolah</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe" value="sejarah">
                    <textarea name="konten" rows="10" required><?= htmlspecialchars($profil['sejarah']['konten'] ?? '') ?></textarea>
                    <button type="submit">Update Sejarah</button>
                </form>
            </div>
            
            <!-- Form Fasilitas -->
            <div class="form-card">
                <h3>Fasilitas Sekolah</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe" value="fasilitas">
                    <textarea name="konten" rows="10" placeholder="Contoh:&#10;- Ruang Kelas ber-AC&#10;- Laboratorium Komputer&#10;- Perpustakaan Digital" required><?= htmlspecialchars($profil['fasilitas']['konten'] ?? '') ?></textarea>
                    <button type="submit">Update Fasilitas</button>
                </form>
            </div>
            
            <!-- Form Struktur Organisasi -->
            <div class="form-card">
                <h3>Struktur Organisasi</h3>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tipe" value="struktur">
                    <input type="file" name="foto" accept="image/*">
                    <?php if (!empty($profil['struktur']['foto'])): ?>
                        <img src="../<?= $profil['struktur']['foto'] ?>" width="200">
                    <?php endif; ?>
                    <button type="submit">Update Struktur Organisasi</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>