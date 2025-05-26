<?php
session_start();

// Hapus hanya sesi yang berhubungan dengan pemain
unset($_SESSION['player_logged_in']);
unset($_SESSION['player_id']);
unset($_SESSION['player_username']);
unset($_SESSION['player_display_name']);

// Arahkan kembali ke halaman utama
header("Location: index.php");
exit;
?>