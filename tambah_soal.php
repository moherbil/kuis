<?php
session_start();

// Periksa apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?status=harus_login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Soal Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Warna latar belakang seperti admin.php */
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-top: 2rem;
            margin-bottom: 2rem;
            max-width: 700px; /* Batasi lebar container agar form tidak terlalu lebar */
        }
        .card-header {
            background-color: #0d6efd; /* Biru Bootstrap */
            color: white;
        }
         .btn-success {
            background-color: #198754; /* Hijau Bootstrap */
            border-color: #198754;
        }
        .btn-success:hover {
            background-color: #157347;
            border-color: #146c43;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="h3 mb-4 text-primary"><i class="bi bi-plus-circle-fill"></i> Tambah Soal Baru</h1>

        <div class="card">
            <div class="card-header">
                Formulir Input Soal
            </div>
            <div class="card-body">
                <form action="proses_soal.php" method="POST">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-3">
                        <label for="question_text" class="form-label">Teks Pertanyaan:</label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="option_a" class="form-label">Pilihan A:</label>
                        <input type="text" class="form-control" id="option_a" name="option_a" required>
                    </div>

                    <div class="mb-3">
                        <label for="option_b" class="form-label">Pilihan B:</label>
                        <input type="text" class="form-control" id="option_b" name="option_b" required>
                    </div>

                    <div class="mb-3">
                        <label for="option_c" class="form-label">Pilihan C:</label>
                        <input type="text" class="form-control" id="option_c" name="option_c" required>
                    </div>

                    <div class="mb-3">
                        <label for="option_d" class="form-label">Pilihan D:</label>
                        <input type="text" class="form-control" id="option_d" name="option_d" required>
                    </div>

                    <div class="mb-3">
                        <label for="correct_answer" class="form-label">Jawaban Benar:</label>
                        <input type="text" class="form-control" id="correct_answer" name="correct_answer" required>
                        <div class="form-text">
                            Tulis teks jawaban yang benar. Pastikan sama persis dengan salah satu pilihan di atas.
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                         <a href="admin.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Admin</a>
                         <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>