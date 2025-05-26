<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?status=harus_login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Soal Baru</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        h1 { color: #28a745; }
        form { background-color: #f9f9f9; padding: 20px; border-radius: 5px; max-width: 600px; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input[type="text"], textarea { width: calc(100% - 22px); padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        textarea { height: 80px; resize: vertical; }
        button { padding: 12px 20px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; }
        button:hover { background-color: #218838; }
        .back-link { margin-top: 15px; display: inline-block; }
    </style>
</head>
<body>

    <h1>Tambah Soal Baru</h1>

    <form action="proses_soal.php" method="POST">
        <input type="hidden" name="action" value="add">

        <div>
            <label for="question_text">Teks Pertanyaan:</label>
            <textarea id="question_text" name="question_text" required></textarea>
        </div>
        <div>
            <label for="option_a">Pilihan A:</label>
            <input type="text" id="option_a" name="option_a" required>
        </div>
        <div>
            <label for="option_b">Pilihan B:</label>
            <input type="text" id="option_b" name="option_b" required>
        </div>
        <div>
            <label for="option_c">Pilihan C:</label>
            <input type="text" id="option_c" name="option_c" required>
        </div>
        <div>
            <label for="option_d">Pilihan D:</label>
            <input type="text" id="option_d" name="option_d" required>
        </div>
        <div>
            <label for="correct_answer">Jawaban Benar (Tulis teksnya, harus sama dengan salah satu pilihan):</label>
            <input type="text" id="correct_answer" name="correct_answer" required>
        </div>
        <div>
            <button type="submit">Simpan Soal</button>
        </div>
    </form>

    <a href="admin.php" class="back-link">Kembali ke Daftar Soal</a>

</body>
</html>