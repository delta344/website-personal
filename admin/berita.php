<?php
require_once '../config/database.php';
cekAkses(['admin']);

// Proses Tambah Berita
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan'])) {
    $judul = escape($_POST['judul']);
    $isi = escape($_POST['isi']);
    $kategori = escape($_POST['kategori']);
    
    // Handle upload gambar
    $foto = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Buat nama file unik
            $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target_path = "../assets/uploads/berita/" . $new_filename;
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                $foto = "assets/uploads/berita/" . $new_filename;
            } else {
                $error = "Gagal mengupload gambar!";
            }
        } else {
            $error = "Format gambar tidak didukung! (JPG, PNG, GIF, WEBP)";
        }
    }
    
    if (empty($error)) {
        $query = "INSERT INTO berita (judul, isi, kategori, foto) 
                  VALUES ('$judul', '$isi', '$kategori', '$foto')";
        
        if (query($query)) {
            echo "<script>alert('Berita berhasil ditambahkan!'); window.location.href='berita.php';</script>";
            exit();
        } else {
            $error = "Gagal menyimpan berita!";
        }
    }
}

// Proses Edit Berita
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_data = fetch(query("SELECT * FROM berita WHERE id = $edit_id"));
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
        $judul = escape($_POST['judul']);
        $isi = escape($_POST['isi']);
        $kategori = escape($_POST['kategori']);
        $foto_lama = $edit_data['foto'];
        
        // Handle upload gambar baru
        $foto = $foto_lama;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['foto']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = time() . '_' . rand(1000, 9999) . '.' . $ext;
                $target_path = "../assets/uploads/berita/" . $new_filename;
                
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
                    $foto = "assets/uploads/berita/" . $new_filename;
                    // Hapus foto lama jika ada
                    if ($foto_lama && file_exists("../" . $foto_lama)) {
                        unlink("../" . $foto_lama);
                    }
                }
            }
        }
        
        $query = "UPDATE berita SET 
                  judul = '$judul', 
                  isi = '$isi', 
                  kategori = '$kategori', 
                  foto = '$foto' 
                  WHERE id = $edit_id";
        
        if (query($query)) {
            echo "<script>alert('Berita berhasil diupdate!'); window.location.href='berita.php';</script>";
            exit();
        }
    }
}

// Proses Hapus Berita
if (isset($_GET['hapus'])) {
    $hapus_id = (int)$_GET['hapus'];
    // Ambil nama foto untuk dihapus
    $foto_data = fetch(query("SELECT foto FROM berita WHERE id = $hapus_id"));
    if ($foto_data['foto'] && file_exists("../" . $foto_data['foto'])) {
        unlink("../" . $foto_data['foto']);
    }
    query("DELETE FROM berita WHERE id = $hapus_id");
    echo "<script>alert('Berita dihapus!'); window.location.href='berita.php';</script>";
    exit();
}

// Ambil data berita
$berita = fetchAll(query("SELECT * FROM berita ORDER BY tgl_post DESC"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Berita - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, 
        .form-group select, 
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group textarea {
            height: 200px;
        }
        
        .preview-image {
            margin-top: 10px;
            max-width: 200px;
        }
        
        .preview-image img {
            width: 100%;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .data-table img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .btn-submit {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Manajemen Berita & Artikel</h1>
            
            <!-- Form Tambah/Edit Berita -->
            <div class="form-container">
                <h2><?= isset($edit_data) ? 'Edit Berita' : 'Tambah Berita Baru' ?></h2>
                
                <?php if (isset($error)): ?>
                    <div style="background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 15px; color: #721c24;">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Judul Berita</label>
                        <input type="text" name="judul" required value="<?= isset($edit_data) ? htmlspecialchars($edit_data['judul']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" required>
                            <option value="pengumuman" <?= isset($edit_data) && $edit_data['kategori'] == 'pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                            <option value="artikel" <?= isset($edit_data) && $edit_data['kategori'] == 'artikel' ? 'selected' : '' ?>>Artikel</option>
                            <option value="kegiatan" <?= isset($edit_data) && $edit_data['kategori'] == 'kegiatan' ? 'selected' : '' ?>>Kegiatan</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar/Foto</label>
                        <input type="file" name="foto" accept="image/*" onchange="previewImage(this)">
                        <?php if (isset($edit_data) && $edit_data['foto']): ?>
                            <div class="preview-image">
                                <img src="../<?= $edit_data['foto'] ?>" alt="Current Image">
                                <p style="font-size: 12px; color: #7f8c8d;">Gambar saat ini</p>
                            </div>
                        <?php endif; ?>
                        <div id="preview"></div>
                        <small style="color: #7f8c8d;">Format: JPG, PNG, GIF, WEBP. Maksimal 2MB</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Isi Berita</label>
                        <textarea name="isi" required><?= isset($edit_data) ? htmlspecialchars($edit_data['isi']) : '' ?></textarea>
                    </div>
                    
                    <?php if (isset($edit_data)): ?>
                        <input type="hidden" name="edit_id" value="<?= $edit_data['id'] ?>">
                        <button type="submit" name="update" class="btn-submit">Update Berita</button>
                        <a href="berita.php" class="btn-delete" style="display: inline-block;">Batal</a>
                    <?php else: ?>
                        <button type="submit" name="simpan" class="btn-submit">Simpan Berita</button>
                    <?php endif; ?>
                </form>
            </div>
            
            <!-- Daftar Berita -->
            <h2>Daftar Berita</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Views</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($berita as $b): ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td>
                            <?php if ($b['foto']): ?>
                                <img src="../<?= $b['foto'] ?>" alt="<?= htmlspecialchars($b['judul']) ?>">
                            <?php else: ?>
                                <span style="color: #95a5a6;">No Image</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($b['judul']) ?></td>
                        <td><?= ucfirst($b['kategori']) ?></td>
                        <td><?= date('d/m/Y', strtotime($b['tgl_post'])) ?></td>
                        <td><?= $b['views'] ?></td>
                        <td>
                            <a href="berita.php?edit=<?= $b['id'] ?>" class="btn-edit">Edit</a>
                            <a href="berita.php?hapus=<?= $b['id'] ?>" class="btn-delete" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        function previewImage(input) {
            var preview = document.getElementById('preview');
            preview.innerHTML = '';
            
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '200px';
                    img.style.marginTop = '10px';
                    img.style.borderRadius = '4px';
                    img.style.border = '1px solid #ddd';
                    preview.appendChild(img);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>