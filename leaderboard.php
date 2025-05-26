<?php
session_start();
include 'koneksi.php';

// Ambil top 10 skor, gabungkan dengan nama pemain
$sql = "SELECT p.display_name, s.score, s.total_questions, s.quiz_date
        FROM scores s
        JOIN players p ON s.player_id = p.id
        ORDER BY s.score DESC, s.quiz_date ASC
        LIMIT 10";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Papan Peringkat Kuis</title>
    <style>
        body { font-family: sans-serif; margin: 20px; background-color: #f8f9fc; }
        .container { max-width: 800px; margin: auto; background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.05); }
        h1 { color: #f6c23e; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { border: 1px solid #e3e6f0; padding: 12px 15px; text-align: left; }
        th { background-color: #f6c23e; color: white; text-transform: uppercase; font-size: 0.9em; border-color: #f6c23e; }
        tr:nth-child(even) { background-color: #f8f9fc; }
        tr:hover { background-color: #f1f1f1; }
        .rank { font-weight: bold; text-align: center; width: 50px; font-size: 1.1em; }
        .rank-1 { color: #ffd700; } /* Emas */
        .rank-2 { color: #c0c0c0; } /* Perak */
        .rank-3 { color: #cd7f32; } /* Perunggu */
        .back-link { margin-top: 25px; display: inline-block; color: #4e73df; text-decoration: none; font-size: 1.1em; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <h1>🏆 Papan Peringkat 🏆</h1>
    <table>
        <thead>
            <tr>
                <th>Peringkat</th>
                <th>Nama Pemain</th>
                <th>Skor</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                $rank = 1;
                while($row = $result->fetch_assoc()) {
                    $rank_class = '';
                    if($rank == 1) $rank_class = 'rank-1';
                    if($rank == 2) $rank_class = 'rank-2';
                    if($rank == 3) $rank_class = 'rank-3';

                    echo "<tr>";
                    echo "<td class='rank " . $rank_class . "'>" . $rank++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['display_name']) . "</td>";
                    echo "<td><strong>" . $row['score'] . "</strong> / " . $row['total_questions'] . "</td>";
                    echo "<td>" . date('d M Y, H:i', strtotime($row['quiz_date'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4' style='text-align:center;'>Belum ada skor yang tercatat. Jadilah yang pertama!</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="index.php" class="back-link">← Kembali ke Kuis</a>
</div>
</body>
</html>
<?php $conn->close(); ?>