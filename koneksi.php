<?php
// --- KONFIGURASI DATABASE ---
$db_host = 'localhost'; // Alamat server database, biasanya 'localhost'
$db_user = 'root';      // Username database Anda (default XAMPP biasanya 'root')
$db_pass = '';          // Password database Anda (default XAMPP biasanya kosong)
$db_name = 'db_kuis';   // Nama database yang sudah kita buat sebelumnya

// --- MEMBUAT KONEKSI DATABASE ---
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// --- MEMERIKSA KONEKSI ---
if ($conn->connect_error) {
    die("Koneksi Database Gagal: " . $conn->connect_error);
}

// Mengatur set karakter koneksi ke utf8mb4.
$conn->set_charset("utf8mb4");

?>