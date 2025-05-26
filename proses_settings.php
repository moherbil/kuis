<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quiz_duration'])) {
    $duration_minutes = intval($_POST['quiz_duration']);

    if ($duration_minutes > 0) {
        $duration_seconds = $duration_minutes * 60;

        $sql = "INSERT INTO settings (setting_key, setting_value)
                VALUES ('quiz_duration', ?)
                ON DUPLICATE KEY UPDATE setting_value = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("ss", $duration_seconds, $duration_seconds);

        if (!$stmt->execute()) {
            // Sebaiknya tangani error dengan lebih baik, misal simpan di session
            // dan tampilkan di admin.php, tapi untuk sekarang echo saja
            echo "Error: Gagal menyimpan pengaturan - " . $stmt->error;
            // Jangan langsung redirect jika error, agar bisa dilihat (sementara)
            exit();
        }

        $stmt->close();
    } else {
        echo "Error: Durasi harus berupa angka positif.";
        exit();
    }
} else {
    echo "Error: Permintaan tidak valid.";
    exit();
}

$conn->close();
header("Location: admin.php");
exit();
?>