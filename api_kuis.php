<?php
session_start();
include 'koneksi.php';

header('Content-Type: application/json');

// Fungsi untuk mengambil durasi kuis dari database
function get_quiz_duration($conn) {
    $quiz_duration = 300; // Default
    $sql = "SELECT setting_value FROM settings WHERE setting_key = 'quiz_duration' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $quiz_duration = intval($row['setting_value']);
    }
    return $quiz_duration;
}

// Fungsi untuk mengambil semua pertanyaan dari database
function getQuestions($conn) {
    $questions = [];
    $sql = "SELECT id, question_text, option_a, option_b, option_c, option_d, correct_answer FROM questions ORDER BY RAND()";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $questions[] = [
                'question' => $row['question_text'],
                'options' => [$row['option_a'], $row['option_b'], $row['option_c'], $row['option_d']],
                'answer' => $row['correct_answer']
            ];
        }
    }
    return $questions;
}

// Fungsi untuk menyimpan skor
function save_score($conn, $player_id, $score, $total_questions) {
    // Pastikan belum disimpan untuk sesi ini
    if (isset($player_id) && (!isset($_SESSION['score_saved']) || $_SESSION['score_saved'] !== true)) {
        $sql = "INSERT INTO scores (player_id, score, total_questions) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("iii", $player_id, $score, $total_questions);
            $stmt->execute();
            $stmt->close();
            $_SESSION['score_saved'] = true; // Tandai sudah disimpan
            return true;
        }
    }
    return false;
}

// Fungsi untuk Mendapatkan State Kuis Saat Ini
function get_current_state() {
    $questions = $_SESSION['questions'] ?? [];
    $total_questions = count($questions);
    $current_index = $_SESSION['current_question'] ?? 0;
    $end_time = $_SESSION['end_time'] ?? 0;
    $current_time = time();
    $remaining_time = max(0, $end_time - $current_time);

    if ($current_index >= $total_questions) {
        return [
            'quiz_finished' => true,
            'score' => $_SESSION['score'] ?? 0,
            'total_questions' => $total_questions,
            'time_up' => isset($_SESSION['time_up']) && $_SESSION['time_up'] === true,
        ];
    } else {
        $current_question_data = $questions[$current_index];
        $options = $current_question_data['options'];
        shuffle($options);
        return [
            'quiz_finished' => false,
            'current_question_index' => $current_index,
            'total_questions' => $total_questions,
            'question_text' => $current_question_data['question'],
            'options' => $options,
            'score' => $_SESSION['score'] ?? 0,
            'remaining_time' => $remaining_time,
        ];
    }
}

// Menangani Aksi
$action = $_REQUEST['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Aksi tidak valid'];

switch ($action) {
    case 'start_quiz':
        $quiz_duration = get_quiz_duration($conn);
        $_SESSION['questions'] = getQuestions($conn);
        $_SESSION['current_question'] = 0;
        $_SESSION['score'] = 0;
        $_SESSION['start_time'] = time();
        $_SESSION['end_time'] = $_SESSION['start_time'] + $quiz_duration;
        unset($_SESSION['time_up']);
        unset($_SESSION['score_saved']); // Reset flag simpan skor
        $response = ['status' => 'ok', 'action' => 'quiz_started', 'data' => get_current_state()];
        break;

    case 'get_state':
        $response = ['status' => 'ok', 'action' => 'current_state', 'data' => get_current_state()];
        break;

    case 'submit_answer':
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['questions'])) {
            $user_answer = $_POST['answer'] ?? null;
            $is_time_up = ($_POST['time_up'] ?? 'false') === 'true';
            $current_index = $_SESSION['current_question'];
            $questions = $_SESSION['questions'];
            $end_time = $_SESSION['end_time'];
            $current_time = time();
            $is_finished_now = false;

            if ($is_time_up || ($end_time > 0 && $current_time >= $end_time)) {
                $_SESSION['current_question'] = count($questions);
                $_SESSION['time_up'] = true;
                $is_finished_now = true;
            } elseif ($current_index < count($questions)) {
                if ($user_answer !== null && $user_answer == $questions[$current_index]['answer']) {
                    $_SESSION['score']++;
                }
                $_SESSION['current_question']++;
                if ($_SESSION['current_question'] >= count($questions)) {
                    $is_finished_now = true;
                }
            }

            if ($is_finished_now && isset($_SESSION['player_logged_in']) && $_SESSION['player_logged_in'] === true) {
                save_score($conn, $_SESSION['player_id'], $_SESSION['score'], count($questions));
            }

            $response = ['status' => 'ok', 'action' => 'answer_processed', 'data' => get_current_state()];
        } else {
            $response = ['status' => 'error', 'message' => 'Permintaan tidak valid atau kuis belum dimulai.'];
        }
        break;

    default:
        $response = ['status' => 'error', 'message' => 'Aksi tidak dikenal.'];
        break;
}

$conn->close();
echo json_encode($response);
exit();
?>