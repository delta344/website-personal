<?php
// public/cek-status.php
require_once '../config/database.php';

$status = null;
$data_pendaftar = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $no_pendaftaran = escape($_POST['no_pendaftaran']);
    
    $query = "SELECT * FROM ppdb WHERE no_pendaftaran = '$no_pendaftaran'";
    $result = query($query);
    
    if (mysqli_num_rows($result) > 0) {
        $data_pendaftar = mysqli_fetch_assoc($result);
        $status = $data_pendaftar['status'];
    } else {
        $error = "Nomor pendaftaran tidak ditemukan!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cek Status PPDB - MTs Bahrul Ulum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="header">
        <h1>Cek Status Pendaftaran</h1>
    </div>
    
    <div class="nav">
        <a href="index.php">Beranda</a>
        <a href="ppdb.php">PPDB</a>
        <a href="cek-status.php">Cek Status</a>
        <a href="login.php">Login Admin</a>
    </div>
    
    <div class="container">
        <div class="form-container" style="max-width: 500px; margin: 0 auto;">
            <h2>Cek Status Kelulusan</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Nomor Pendaftaran:</label>
                    <input type="text" name="no_pendaftaran" placeholder="Contoh: PPDB-20240115-1234" required>
                </div>
                <button type="submit">Cek Status</button>
            </form>
            
            <?php if ($data_pendaftar): ?>
            <div class="result-box" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 5px;">
                <h3>Hasil Pengecekan:</h3>
                <p><strong>No Pendaftaran:</strong> <?= $data_pendaftar['no_pendaftaran'] ?></p>
                <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data_pendaftar['nama_lengkap']) ?></p>
                <p><strong>Status:</strong> 
                    <?php
                    $status_badge = '';
                    switch($data_pendaftar['status']) {
                        case 'pending':
                            $status_badge = '<span style="background: orange; color: white; padding: 5px 10px; border-radius: 3px;">Pending - Sedang Diproses</span>';
                            break;
                        case 'verifikasi':
                            $status_badge = '<span style="background: blue; color: white; padding: 5px 10px; border-radius: 3px;">Verifikasi - Berkas Diperiksa</span>';
                            break;
                        case 'lulus':
                            $status_badge = '<span style="background: green; color: white; padding: 5px 10px; border-radius: 3px;">LULUS - Selamat! Silakan daftar ulang</span>';
                            break;
                        case 'tidak_lulus':
                            $status_badge = '<span style="background: red; color: white; padding: 5px 10px; border-radius: 3px;">Tidak Lulus</span>';
                            break;
                    }
                    echo $status_badge;
                    ?>
                </p>
                <p><strong>Tanggal Daftar:</strong> <?= $data_pendaftar['tgl_daftar'] ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; <?= date('Y') ?> MTs Bahrul Ulum NW Telage Bagek</p>
    </div>
</body>
</html>