<?php
$conn = new mysqli("localhost", "root", "", "sertifikat");

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT p.nip, p.nama_pegawai, p.jabatan, p.unit, s.file_template 
            FROM pegawai p 
            JOIN sertifikat s ON p.id = s.pegawai_id 
            WHERE p.nip = '$query' OR p.nama_pegawai LIKE '%$query%'";
    $result = $conn->query($sql);
} else {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Sertifikat | e-Sertifikat</title>
    <link rel="icon" type="image/x-icon" href="assets/images/ihbs-logo.png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <br><br><br><br>
    <div class="container text-center mt-5">
        <img src="assets/images/certificate-logo.png" alt="Logo" class="logo " style="width: 250px">
        <!-- <p class="lead">Cari sertifikat Anda dengan NIP atau Nama</p> -->
        
        <form action="" method="GET" class="search-box mt-3">
            <input type="text" name="query" class="form-control" placeholder="Masukkan NIP atau Nama Peserta" required>
            <button type="submit" class="btn btn-sm btn-success">🔍 Cari</button>
        </form>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="result-box mt-4">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="card border-0 shadow-md">
                        <img src="templates/<?= $row['file_template']; ?>" class="card-img-top" alt="Sertifikat">
                        <div class="card-body">
                            <h5 class="card-title"><?= $row['nama_pegawai']; ?></h5>
                            <p class="card-text"><?= $row['jabatan']; ?> - <?= $row['unit']; ?></p>
                            <a href="generate_pdf.php?query=<?= $query; ?>" target="_blank" class="btn btn-info rounded-pill">Download PDF</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php elseif ($result): ?>
            <p class="text-danger mt-4">Sertifikat tidak ditemukan!</p>
        <?php endif; ?>
    </div>
    <?php include "footer.php"; ?>
</body>
</html>
