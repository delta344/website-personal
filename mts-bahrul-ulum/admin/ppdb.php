<?php
require_once '../config/database.php';
cekAkses(['admin', 'panitia']);

// Handle Update Status
if (isset($_GET['update_status'])) {
    $id = (int)$_GET['update_status'];
    $status = escape($_GET['status']);
    query("UPDATE ppdb SET status = '$status' WHERE id = $id");
    alert("Status pendaftar diubah menjadi $status");
    redirect("ppdb.php");
}

// Handle Delete
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    query("DELETE FROM ppdb WHERE id = $id");
    alert("Data pendaftar dihapus");
    redirect("ppdb.php");
}

// Handle Export Excel
if (isset($_GET['export'])) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=pendaftar_ppdb.xls");
    
    $data = fetchAll(query("SELECT * FROM ppdb ORDER BY tgl_daftar DESC"));
    echo "<table border='1'>";
    echo "<tr><th>No Pendaftaran</th><th>Nama</th><th>NISN</th><th>Tempat Lahir</th><th>Tgl Lahir</th><th>Alamat</th><th>No HP</th><th>Asal Sekolah</th><th>Status</th><th>Tgl Daftar</th></tr>";
    foreach ($data as $row) {
        echo "<tr>";
        echo "<td>{$row['no_pendaftaran']}</td>";
        echo "<td>{$row['nama_lengkap']}</td>";
        echo "<td>{$row['nisn']}</td>";
        echo "<td>{$row['tempat_lahir']}</td>";
        echo "<td>{$row['tanggal_lahir']}</td>";
        echo "<td>{$row['alamat']}</td>";
        echo "<td>{$row['no_hp']}</td>";
        echo "<td>{$row['asal_sekolah']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "<td>{$row['tgl_daftar']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    exit();
}

// Pencarian
$search = '';
if (isset($_GET['search'])) {
    $search = escape($_GET['search']);
    $where = "WHERE nama_lengkap LIKE '%$search%' OR no_pendaftaran LIKE '%$search%' OR nisn LIKE '%$search%'";
} else {
    $where = "";
}

// Pagination
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$total_rows = mysqli_fetch_assoc(query("SELECT COUNT(*) as total FROM ppdb $where"))['total'];
$total_pages = ceil($total_rows / $limit);

$pendaftar = fetchAll(query("SELECT * FROM ppdb $where ORDER BY tgl_daftar DESC LIMIT $offset, $limit"));

// Statistik
$statistik = fetchAll(query("SELECT status, COUNT(*) as jumlah FROM ppdb GROUP BY status"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen PPDB</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-container">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <h1>Manajemen PPDB</h1>
            
            <!-- Statistik -->
            <div class="stats-grid">
                <?php foreach ($statistik as $s): ?>
                <div class="stat-card">
                    <h3><?= ucfirst($s['status']) ?></h3>
                    <p><?= $s['jumlah'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Export & Search -->
            <div style="margin-bottom: 20px;">
                <a href="?export=1" class="btn" style="background:#27ae60">📊 Export Excel</a>
                <form method="GET" style="display: inline-block; float: right;">
                    <input type="text" name="search" placeholder="Cari nama/NISN/no pendaftaran" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">Cari</button>
                </form>
            </div>
            
            <!-- Tabel Pendaftar -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No Pendaftaran</th>
                        <th>Nama</th>
                        <th>NISN</th>
                        <th>Asal Sekolah</th>
                        <th>No HP</th>
                        <th>Status</th>
                        <th>Tgl Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendaftar as $p): ?>
                    <tr>
                        <td><?= $p['no_pendaftaran'] ?></td>
                        <td><?= htmlspecialchars($p['nama_lengkap']) ?></td>
                        <td><?= $p['nisn'] ?></td>
                        <td><?= $p['asal_sekolah'] ?></td>
                        <td><?= $p['no_hp'] ?></td>
                        <td>
                            <select onchange="updateStatus(<?= $p['id'] ?>, this.value)" style="padding:5px">
                                <option value="pending" <?= $p['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="verifikasi" <?= $p['status'] == 'verifikasi' ? 'selected' : '' ?>>Verifikasi</option>
                                <option value="lulus" <?= $p['status'] == 'lulus' ? 'selected' : '' ?>>Lulus</option>
                                <option value="tidak_lulus" <?= $p['status'] == 'tidak_lulus' ? 'selected' : '' ?>>Tidak Lulus</option>
                            </select>
                        </td>
                        <td><?= $p['tgl_daftar'] ?></td>
                        <td>
                            <a href="ppdb-detail.php?id=<?= $p['id'] ?>">Detail</a>
                            <a href="?hapus=<?= $p['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" style="padding:8px 12px; background:<?= $i == $page ? '#3498db' : '#ecf0f1' ?>; color:<?= $i == $page ? 'white' : 'black' ?>; margin:2px; text-decoration:none">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    
    <script>
    function updateStatus(id, status) {
        if (confirm('Ubah status pendaftar?')) {
            window.location.href = '?update_status=' + id + '&status=' + status;
        }
    }
    </script>
</body>
</html>