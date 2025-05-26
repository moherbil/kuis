<?php
session_start();
// Periksa login admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php?status=harus_login");
    exit;
}

include 'koneksi.php'; // Sertakan koneksi

// Ambil pengaturan durasi kuis
$current_duration_seconds = 300; // Default 5 menit (300 detik)
$sql_get_duration = "SELECT setting_value FROM settings WHERE setting_key = 'quiz_duration' LIMIT 1";
$result_duration = $conn->query($sql_get_duration);
if ($result_duration && $result_duration->num_rows > 0) {
    $row_duration = $result_duration->fetch_assoc();
    $current_duration_seconds = intval($row_duration['setting_value']);
}
$current_duration_minutes = $current_duration_seconds / 60;

// Ambil daftar soal
$sql = "SELECT id, question_text, correct_answer FROM questions ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Admin - Kelola Soal Kuis</title>
    <style>
        body { background-color: #f8f9fa; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.05); margin-top: 2rem; margin-bottom: 2rem; }
        .table-hover tbody tr:hover { background-color: #f1f1f1; }
        .table th { background-color: #e9ecef; }
    </style>
    <script>
        function confirmDelete(id) {
            if (confirm("Apakah Anda yakin ingin menghapus soal ini?")) {
                window.location.href = 'proses_soal.php?action=delete&id=' + id;
            }
        }
        // Fungsi konfirmasi untuk reset leaderboard
        function confirmResetLeaderboard() {
            if (confirm("APAKAH ANDA YAKIN?\n\nTindakan ini akan MENGHAPUS SEMUA DATA SKOR di papan peringkat secara permanen dan TIDAK BISA DIBATALKAN.\n\nLanjutkan?")) {
                window.location.href = 'proses_leaderboard.php?action=reset';
            }
        }
    </script>
</head>
<body>
<div class="container">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
         <h1 class="h3 mb-0 text-primary"><i class="bi bi-speedometer2"></i> Dashboard Admin Kuis</h1>
         <div class="text-end">
            <span class="text-muted me-3">Selamat datang, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
            <a href="logout.php" class="btn btn-secondary btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
         </div>
    </div>
    <?php if(isset($_GET['status'])): ?>
        <?php if($_GET['status'] == 'leaderboard_reset_sukses'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> Papan peringkat berhasil direset!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif($_GET['status'] == 'leaderboard_reset_gagal'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-x-circle-fill"></i> Gagal mereset papan peringkat!
                <?php if(isset($_GET['error'])) { echo "<br><small>Error: " . htmlspecialchars($_GET['error']) . "</small>"; } ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h2 class="h5 mb-0"><i class="bi bi-gear-fill"></i> Pengaturan Kuis</h2>
        </div>
        <div class="card-body">
            <form action="proses_settings.php" method="POST" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label for="quiz_duration" class="col-form-label">Durasi Kuis (menit):</label>
                </div>
                <div class="col-auto">
                    <input type="number" class="form-control form-control-sm" id="quiz_duration" name="quiz_duration" value="<?php echo $current_duration_minutes; ?>" min="1" required style="width: 100px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save"></i> Simpan Durasi</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
         <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h2 class="h5 mb-0"><i class="bi bi-list-task"></i> Kelola Soal</h2>
            <a href="tambah_soal.php" class="btn btn-success btn-sm"><i class="bi bi-plus-circle"></i> Tambah Soal Baru</a>
        </div>
        <div class="card-body p-0">
             <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Teks Pertanyaan (Awal)</th>
                            <th scope="col">Jawaban Benar</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && $result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td>" . htmlspecialchars(substr($row['question_text'], 0, 100)) . "...</td>"; //
                                echo "<td>" . htmlspecialchars($row['correct_answer']) . "</td>"; //
                                echo "<td class='text-center'>";
                                echo "<a href='edit_soal.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm me-1'><i class='bi bi-pencil-square'></i></a>"; //
                                echo "<a href='#' onclick='confirmDelete(" . $row['id'] . ")' class='btn btn-danger btn-sm'><i class='bi bi-trash'></i></a>"; //
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else { echo "<tr><td colspan='4' class='text-center p-4'>Belum ada soal di database.</td></tr>"; } //
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card mt-4 border-danger">
        <div class="card-header bg-danger text-white">
            <h2 class="h5 mb-0"><i class="bi bi-exclamation-triangle-fill"></i> Zona Berbahaya</h2>
        </div>
        <div class="card-body">
            <p>Tindakan di bawah ini bersifat permanen dan tidak dapat dibatalkan. Lakukan dengan hati-hati.</p>
            <button type="button" class="btn btn-danger" onclick="confirmResetLeaderboard()">
                <i class="bi bi-trash3-fill"></i> Reset Papan Peringkat (Leaderboard)
            </button>
            <div class="form-text text-danger mt-1">
                Ini akan menghapus **SEMUA** data skor pemain yang tersimpan.
            </div>
        </div>
    </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); // Tutup koneksi ?>
