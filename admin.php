<?php


$host = "localhost";
$user = "root";
$pass = "";
$db = "sertifikat";
$conn = new mysqli($host, $user, $pass, $db);

$username = "admin";
// $password = password_hash("admin123", PASSWORD_BCRYPT); // Hash password
$password = "admin123"; // Hash password

$sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
$conn->query($sql);

// echo "Admin berhasil ditambahkan!";

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Tambah Pegawai
if (isset($_POST['add_pegawai'])) {
    $nip = $_POST['nip'];
    $nama = $_POST['nama_pegawai'];
    $jabatan = $_POST['jabatan'];
    $unit = $_POST['unit'];

    $sql = "INSERT INTO pegawai (nip, nama_pegawai, jabatan, unit) VALUES ('$nip', '$nama', '$jabatan', '$unit')";
    $conn->query($sql);
}

// Hapus Pegawai
if (isset($_GET['delete_pegawai'])) {
    $id = $_GET['delete_pegawai'];
    $conn->query("DELETE FROM pegawai WHERE id=$id");
}

// Tambah Sertifikat
if (isset($_POST['add_sertifikat'])) {
    $pegawai_id = $_POST['pegawai_id'];
    $file_name = $_FILES['file_template']['name'];
    $file_tmp = $_FILES['file_template']['tmp_name'];
    $upload_dir = "templates/";

    if (move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
        $sql = "INSERT INTO sertifikat (pegawai_id, file_template) VALUES ('$pegawai_id', '$file_name')";
        $conn->query($sql);
    }
}

// Hapus Sertifikat
if (isset($_GET['delete_sertifikat'])) {
    $id = $_GET['delete_sertifikat'];
    $conn->query("DELETE FROM sertifikat WHERE id=$id");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - e-Sertifikat</title>
    <link rel="icon" type="image/x-icon" href="assets/images/ihbs-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        body {
            background: #f8f9fa;
        }
        .logo {
            width: 120px;
            height: auto;
        }
        .card-header {
            background: #218838;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <!-- HEADER -->
    <div class="text-center mb-4">
        <img src="assets/images/certificate-logo.png" alt="Logo" class="logo " style="width: 150px">
        <h2 class="mt-2">Admin Panel - e-Sertifikat</h2>
    </div>

    <!-- CARD PEGAWAI -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-users"></i> Manajemen Peserta Lomba</div>
        <div class="card-body">
            <form method="POST" class="mb-3">
                <div class="form-row">
                    <div class="col"><input type="text" name="nip" class="form-control" placeholder="NIP" required></div>
                    <div class="col"><input type="text" name="nama_pegawai" class="form-control" placeholder="Nama Pegawai" required></div>
                    <div class="col"><input type="text" name="jabatan" class="form-control" placeholder="Jabatan" required></div>
                    <div class="col"><input type="text" name="unit" class="form-control" placeholder="Unit" required></div>
                    <div class="col"><button type="submit" name="add_pegawai" class="btn btn-success rounded-pill"><i class="fas fa-plus"></i> Tambah</button></div>
                </div>
            </form>
            <table class="table table-bordered">
                <tr class="bg-success text-white"><th>ID</th><th>NIP</th><th>Nama</th><th>Jabatan</th><th>Unit</th><th>Aksi</th></tr>
                <?php
                $result = $conn->query("SELECT * FROM pegawai");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nip']}</td>
                            <td>{$row['nama_pegawai']}</td>
                            <td>{$row['jabatan']}</td>
                            <td>{$row['unit']}</td>
                            <td><a href='?delete_pegawai={$row['id']}' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></a></td>
                          </tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- CARD SERTIFIKAT -->
    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-file-alt"></i> Manajemen Sertifikat</div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" class="mb-3">
                <div class="form-row">
                    <div class="col">
                        <select name="pegawai_id" class="form-control" required>
                            <option value="">Pilih Pegawai</option>
                            <?php
                            $pegawai = $conn->query("SELECT * FROM pegawai");
                            while ($row = $pegawai->fetch_assoc()) {
                                echo "<option value='{$row['id']}'>{$row['nama_pegawai']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col"><input type="file" name="file_template" class="form-control" required></div>
                    <div class="col"><button type="submit" name="add_sertifikat" class="btn btn-success rounded-pill"><i class="fas fa-upload"></i> Upload</button></div>
                </div>
            </form>
            <table class="table table-bordered">
                <tr class="bg-success text-white"><th>ID</th><th>Nama Peserta</th><th>File Template</th><th>Aksi</th></tr>
                <?php
                $result = $conn->query("SELECT s.id, p.nama_pegawai, s.file_template 
                                        FROM sertifikat s 
                                        JOIN pegawai p ON s.pegawai_id = p.id");
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['nama_pegawai']}</td>
                            <td><a href='templates/{$row['file_template']}' target='_blank'>{$row['file_template']}</a></td>
                            <td><a href='?delete_sertifikat={$row['id']}' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></a></td>
                          </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>

</body>
</html>
