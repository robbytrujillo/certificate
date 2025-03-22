<?php
require('fpdf/fpdf.php');

// Koneksi database
$conn = new mysqli("localhost", "root", "", "sertifikat");

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil query dari URL
$query = isset($_GET['query']) ? $conn->real_escape_string($_GET['query']) : '';

// Perbaiki query untuk mencari berdasarkan NIP atau nama
$sql = "SELECT p.nip, p.nama_pegawai, s.file_template 
        FROM pegawai p 
        JOIN sertifikat s ON p.id = s.pegawai_id 
        WHERE p.nip = '$query' OR p.nama_pegawai LIKE '%$query%'";

$result = $conn->query($sql);

// Cek apakah data ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Buat PDF
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->Image('templates/' . $row['file_template'], 0, 0, 297, 210);

    // Set font dan warna teks
    $pdf->SetFont('Arial', 'B', 28); // Ukuran font diperbesar agar proporsional
    $pdf->SetTextColor(0, 0, 0); // Warna hitam

    // Menentukan posisi teks Nama Peserta lebih presisi
    $pdf->SetXY(0, 92); // Ubah nilai vertikal agar pas di bawah "Diberikan kepada:"
    $pdf->Cell(297, 15, strtoupper($row['nama_pegawai']), 0, 1, 'C'); // Posisi tengah, tinggi diperbesar

    // Output PDF dengan nama file yang sesuai
    $pdf->Output("D", "Sertifikat_" . $row['nama_pegawai'] . ".pdf");

} else {
    echo "❌ Data tidak ditemukan!";
}

// Tutup koneksi database
$conn->close();
?>
