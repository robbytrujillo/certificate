<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        // Menampilkan background sertifikat
        $this->Image('sertifikat_template.png', 0, 0, 297, 210); // Sesuaikan ukuran A4 (297x210 mm)
    }
}

// Koneksi ke database
$servername = "localhost";
$username = "root"; // Sesuaikan dengan user database Anda
$password = ""; // Sesuaikan dengan password database Anda
$dbname = "sertifikat";

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data dari parameter URL
$nip = isset($_GET['nip']) ? $conn->real_escape_string($_GET['nip']) : '';

// Perbaikan query SQL
$sql = "SELECT * FROM pegawai WHERE nip = '$nip' OR nama_pegawai LIKE '%$nip%'";
$result = $conn->query($sql);

// Cek apakah query berhasil dijalankan
if (!$result) {
    die("Query gagal: " . $conn->error);
}

// Cek apakah ada data yang ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $pdf = new PDF('L', 'mm', 'A4'); // Mode Landscape (L), ukuran dalam milimeter, A4
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(false);

    // Menampilkan nama peserta
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->SetTextColor(0, 0, 0); // Warna hitam
    $pdf->SetXY(80, 100); // Atur posisi nama
    $pdf->Cell(140, 10, strtoupper($row['nama_pegawai']), 0, 1, 'C'); // Nama ditengah

    // Simpan output PDF
    $pdf->Output("D", "Sertifikat_" . $row['nama_pegawai'] . ".pdf");
} else {
    echo "❌ Data tidak ditemukan untuk NIP/Nama: " . htmlspecialchars($nip);
}

// Tutup koneksi database
$conn->close();
?>
