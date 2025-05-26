<?php
session_start();

// Periksa login admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?status=harus_login");
    exit;
}

include 'koneksi.php';

// Validasi dan ambil ID soal
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: admin.php?status=id_tidak_valid");
    exit();
}
$id = intval($_GET['id']);

// Ambil data soal dari database
$sql = "SELECT * FROM questions WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah soal ditemukan
if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    // Redirect atau tampilkan pesan error yang lebih baik
    header("Location: admin.php?status=soal_tidak_ditemukan");
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Soal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin-top: 2rem;
            margin-bottom: 2rem;
            max-width: 700px;
        }
        .card-header {
            background-color: #ffc107; /* Kuning Bootstrap (Warning) */
            color: black;
        }
        .btn-warning {
            color: black; /* Agar teks lebih kontras */
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="h3 mb-4 text-warning"><i class="bi bi-pencil-square"></i> Edit Soal (ID: <?php echo $row['id']; ?>)</h1>

        <div class="card">
            <div class="card-header">
                Formulir Edit Soal
            </div>
            <div class="card-body">
                <form action="proses_soal.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

                    <div class="mb-3">
                        <label for="question_text" class="form-label">Teks Pertanyaan:</label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required><?php echo htmlspecialchars($row['question_text']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="option_a" class="form-label">Pilihan A:</label>
                        <input type="text" class="form-control" id="option_a" name="option_a" value="<?php echo htmlspecialchars($row['option_a']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="option_b" class="form-label">Pilihan B:</label>
                        <input type="text" class="form-control" id="option_b" name="option_b" value="<?php echo htmlspecialchars($row['option_b']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="option_c" class="form-label">Pilihan C:</label>
                        <input type="text" class="form-control" id="option_c" name="option_c" value="<?php echo htmlspecialchars($row['option_c']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="option_d" class="form-label">Pilihan D:</label>
                        <input type="text" class="form-control" id="option_d" name="option_d" value="<?php echo htmlspecialchars($row['option_d']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="correct_answer" class="form-label">Jawaban Benar:</label>
                        <input type="text" class="form-control" id="correct_answer" name="correct_answer" value="<?php echo htmlspecialchars($row['correct_answer']); ?>" required>
                         <div class="form-text">
                            Pastikan teks ini sama persis dengan salah satu pilihan di atas.
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                         <a href="admin.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal & Kembali</a>
                         <button type="submit" class="btn btn-warning"><i class="bi bi-save2"></i> Update Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
