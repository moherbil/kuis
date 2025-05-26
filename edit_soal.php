<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?status=harus_login");
    exit;
}

include 'koneksi.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM questions WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Soal tidak ditemukan.";
    $stmt->close();
    $conn->close();
    exit();
}

$row = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Soal</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h1 { color: #ffc107; }
        form { background-color: #f9f9f9; padding: 20px; border-radius: 5px; max-width: 600px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], textarea { width: calc(100% - 22px); padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        textarea { height: 80px; resize: vertical; }
        button { padding: 12px 20px; background-color: #ffc107; color: black; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; }
        button:hover { background-color: #e0a800; }
        .back-link { margin-top: 15px; display: inline-block; }
    </style>
</head>
<body>

    <h1>Edit Soal</h1>

    <form action="proses_soal.php" method="POST">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <div>
            <label for="question_text">Teks Pertanyaan:</label>
            <textarea id="question_text" name="question_text" required><?php echo htmlspecialchars($row['question_text']); ?></textarea>
        </div>
        <div>
            <label for="option_a">Pilihan A:</label>
            <input type="text" id="option_a" name="option_a" value="<?php echo htmlspecialchars($row['option_a']); ?>" required>
        </div>
        <div>
            <label for="option_b">Pilihan B:</label>
            <input type="text" id="option_b" name="option_b" value="<?php echo htmlspecialchars($row['option_b']); ?>" required>
        </div>
        <div>
            <label for="option_c">Pilihan C:</label>
            <input type="text" id="option_c" name="option_c" value="<?php echo htmlspecialchars($row['option_c']); ?>" required>
        </div>
        <div>
            <label for="option_d">Pilihan D:</label>
            <input type="text" id="option_d" name="option_d" value="<?php echo htmlspecialchars($row['option_d']); ?>" required>
        </div>
        <div>
            <label for="correct_answer">Jawaban Benar:</label>
            <input type="text" id="correct_answer" name="correct_answer" value="<?php echo htmlspecialchars($row['correct_answer']); ?>" required>
        </div>
        <div>
            <button type="submit">Update Soal</button>
        </div>
    </form>

    <a href="admin.php" class="back-link">Kembali ke Daftar Soal</a>

</body>
</html>