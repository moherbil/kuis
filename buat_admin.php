<?php
include 'koneksi.php';

// --- ATUR DI SINI ---
$username_baru = 'admin'; // Ganti dengan username yang Anda inginkan
$password_baru = 'password123'; // Ganti dengan password yang kuat!
// --------------------

$hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, password_hash) VALUES (?, ?)"; // Menggunakan tabel 'users'
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("ss", $username_baru, $hashed_password);

if ($stmt->execute()) {
    echo "<h1>Admin Berhasil Dibuat!</h1>";
    echo "<p>Username: " . htmlspecialchars($username_baru) . "</p>";
    echo "<p>Password: " . htmlspecialchars($password_baru) . "</p>";
    echo "<p><strong>Penting:</strong> Hapus file 'buat_admin.php' ini sekarang demi keamanan!</p>";
} else {
    if ($conn->errno == 1062) {
         echo "<h1>Error: Username '" . htmlspecialchars($username_baru) . "' sudah ada!</h1>";
    } else {
        echo "<h1>Error: Gagal membuat admin!</h1>";
        echo "<p>" . $stmt->error . "</p>";
    }
}

$stmt->close();
$conn->close();
?>