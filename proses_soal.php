<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?status=harus_login");
    exit;
}

include 'koneksi.php';

$action = $_REQUEST['action'] ?? '';

// --- FUNGSI CREATE (TAMBAH) ---
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    $sql = "INSERT INTO questions (question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) { die("Error preparing statement: " . $conn->error); }
    $stmt->bind_param("ssssss", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer);
    if (!$stmt->execute()) { echo "Error: " . $stmt->error; }
    $stmt->close();

// --- FUNGSI UPDATE (EDIT) ---
} elseif ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];

    $sql = "UPDATE questions SET question_text = ?, option_a = ?, option_b = ?, option_c = ?, option_d = ?, correct_answer = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) { die("Error preparing statement: " . $conn->error); }
    $stmt->bind_param("ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $id);
    if (!$stmt->execute()) { echo "Error: " . $stmt->error; }
    $stmt->close();

// --- FUNGSI DELETE (HAPUS) ---
} elseif ($action == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM questions WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) { die("Error preparing statement: " . $conn->error); }
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) { echo "Error: " . $stmt->error; }
    $stmt->close();
}

$conn->close();

header("Location: admin.php");
exit();
?>