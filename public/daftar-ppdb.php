<?php
require_once '../config/database.php';

global $conn; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validasi server-side
    $errors = [];
    
    $nama = escape($_POST['nama']);
    $nisn = escape($_POST['nisn']);
    $tempat_lahir = escape($_POST['tempat_lahir']);
    $tanggal_lahir = escape($_POST['tanggal_lahir']);
    $alamat = escape($_POST['alamat']);
    $no_hp = escape($_POST['no_hp']);
    $asal_sekolah = escape($_POST['asal_sekolah']);
    
    // Validasi input tidak kosong
    if (empty($nama)) $errors[] = "Nama lengkap harus diisi";
    if (empty($tempat_lahir)) $errors[] = "Tempat lahir harus diisi";
    if (empty($tanggal_lahir)) $errors[] = "Tanggal lahir harus diisi";
    if (empty($alamat)) $errors[] = "Alamat harus diisi";
    if (empty($no_hp)) $errors[] = "Nomor HP harus diisi";
    if (empty($asal_sekolah)) $errors[] = "Asal sekolah harus diisi";
    
    // Validasi nomor HP
    if (!empty($no_hp) && !preg_match('/^[0-9]{10,13}$/', $no_hp)) {
        $errors[] = "Nomor HP harus 10-13 digit angka";
    }
    
    // Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        // Generate no pendaftaran unik
        $no_pendaftaran = "PPDB-" . date("Ymd") . "-" . rand(1000, 9999);
        
        $query = "INSERT INTO ppdb (no_pendaftaran, nama_lengkap, nisn, tempat_lahir, tanggal_lahir, alamat, no_hp, asal_sekolah, status) 
                  VALUES ('$no_pendaftaran', '$nama', '$nisn', '$tempat_lahir', '$tanggal_lahir', '$alamat', '$no_hp', '$asal_sekolah', 'pending')";
        
        // PERBAIKAN: mysqli_error($conn) BUKAN mysqli_error('$conn')
        $result = mysqli_query($conn, $query);

           if ($result) {
            $success = "Pendaftaran berhasil! No Pendaftaran Anda: <strong>$no_pendaftaran</strong><br>Silakan simpan nomor pendaftaran ini untuk cek status kelulusan.";
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($conn); // <-- Perbaikan di sini
        }

    } else {
        $error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>PPDB Online - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/script.js"></script>
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input, 
        .form-group textarea, 
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-group input:focus, 
        .form-group textarea:focus {
            outline: none;
            border-color: #4CAF50;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background: #45a049;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        .info-box {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #2196F3;
        }
        .required:after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Penerimaan Peserta Didik Baru</h1>
        <p>MTs Bahrul Ulum NW Telage Bagek - Tahun Ajaran <?= date('Y') . '/' . (date('Y')+1) ?></p>
    </div>
    
    <div class="nav">
        <a href="index.php">Beranda</a>
        <a href="profil.php">Profil</a>
        <a href="akademik.php">Akademik</a>
        <a href="kesiswaan.php">Kesiswaan</a>
        <a href="ppdb.php">PPDB</a>
        <a href="berita.php">Berita</a>
        <a href="kontak.php">Kontak</a>
        <a href="cek-status.php">Cek Status</a>
    </div>
    
    <div class="container">
        <div class="form-container">
            <h2 style="text-align: center; margin-bottom: 30px;">Formulir Pendaftaran Online</h2>
            
            <?php if (isset($success)): ?>
                <div class="alert-success">
                    <?= $success ?>
                    <br><br>
                    <a href="cek-status.php" style="color: #155724; font-weight: bold;">Cek Status Pendaftaran →</a>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <div class="info-box">
                <strong>📝 Informasi Penting:</strong><br>
                - Isi formulir dengan data yang benar<br>
                - Simpan nomor pendaftaran yang akan diberikan<br>
                - Gunakan nomor pendaftaran untuk cek status kelulusan
            </div>
            
            <form method="POST" onsubmit="return validateForm()">
                <div class="form-group">
                    <label class="required">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label>NISN</label>
                    <input type="text" name="nisn" value="<?= isset($_POST['nisn']) ? htmlspecialchars($_POST['nisn']) : '' ?>">
                    <small style="color: #666;">Diisi jika sudah memiliki NISN</small>
                </div>
                
                <div class="form-group">
                    <label class="required">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="<?= isset($_POST['tempat_lahir']) ? htmlspecialchars($_POST['tempat_lahir']) : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="required">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="<?= isset($_POST['tanggal_lahir']) ? $_POST['tanggal_lahir'] : '' ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="required">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" required><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : '' ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="required">Nomor WhatsApp/HP</label>
                    <input type="tel" name="no_hp" id="no_hp" value="<?= isset($_POST['no_hp']) ? $_POST['no_hp'] : '' ?>" required>
                    <small style="color: #666;">Contoh: 081234567890</small>
                </div>
                
                <div class="form-group">
                    <label class="required">Asal Sekolah</label>
                    <input type="text" name="asal_sekolah" value="<?= isset($_POST['asal_sekolah']) ? htmlspecialchars($_POST['asal_sekolah']) : '' ?>" required>
                </div>
                
                <button type="submit">Daftar Sekarang</button>
            </form>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; <?= date('Y') ?> MTs Bahrul Ulum NW Telage Bagek. All Rights Reserved.</p>
    </div>
    
    <script>
        function validateForm() {
            const nama = document.getElementById('nama');
            if (nama.value.trim() === '') {
                alert('Nama lengkap harus diisi');
                nama.focus();
                return false;
            }
            
            const nohp = document.getElementById('no_hp');
            const hpRegex = /^[0-9]{10,13}$/;
            if (!hpRegex.test(nohp.value)) {
                alert('Nomor HP harus 10-13 digit angka');
                nohp.focus();
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>