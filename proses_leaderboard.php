<?php
session_start();

// 1. Periksa apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?status=harus_login");
    exit;
}

// 2. Periksa apakah aksi adalah 'reset'
if (isset($_GET['action']) && $_GET['action'] == 'reset') {

    include 'koneksi.php'; // 3. Sertakan koneksi database

    // 4. Jalankan query untuk mengosongkan tabel 'scores'
    // TRUNCATE TABLE lebih efisien dan mereset auto_increment (jika ada)
    $sql = "TRUNCATE TABLE scores";

    if ($conn->query($sql) === TRUE) {
        // Jika berhasil, redirect kembali ke admin dengan status sukses
        $conn->close();
        header("Location: admin.php?status=leaderboard_reset_sukses");
        exit();
    } else {
        // Jika gagal, redirect kembali dengan status error
        $error_message = $conn->error;
        $conn->close();
        header("Location: admin.php?status=leaderboard_reset_gagal&error=" . urlencode($error_message));
        exit();
    }

    $conn->close();

} else {
    // Jika aksi tidak valid, redirect kembali ke admin
    header("Location: admin.php");
    exit();
}
?>