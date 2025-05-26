<?php
session_start();
// Embed status login pemain untuk dibaca JavaScript
$is_player_logged_in = isset($_SESSION['player_logged_in']) && $_SESSION['player_logged_in'] === true;
$player_display_name = $is_player_logged_in ? htmlspecialchars($_SESSION['player_display_name']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuis AJAX PHP</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; color: #333; display: flex; justify-content: center; align-items: flex-start; min-height: 100vh; }
        .quiz-container { background-color: #fff; padding: 30px 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 650px; width: 100%; margin: auto; text-align: center; min-height: 400px; display: flex; flex-direction: column; justify-content: center; transition: all 0.3s ease; }
        h1, h2 { color: #0056b3; text-align: center; margin-bottom: 25px; }
        .question-area { text-align: left; margin-bottom: 30px; }
        .question { font-size: 1.3em; font-weight: 600; margin-bottom: 30px; line-height: 1.5; }
        .options label { display: block; margin-bottom: 12px; background-color: #e9ecef; padding: 15px; border-radius: 8px; cursor: pointer; transition: background-color 0.3s, box-shadow 0.3s; border: 1px solid #dee2e6; }
        .options label:hover { background-color: #d8dfe5; }
        .options label.selected { background-color: #cce5ff; border-color: #b8daff; }
        input[type="radio"] { margin-right: 15px; transform: scale(1.2); }
        button { display: block; width: 100%; padding: 15px; background: linear-gradient(90deg, #28a745, #218838); color: white; border: none; border-radius: 8px; font-size: 1.2em; font-weight: bold; cursor: pointer; margin-top: 30px; transition: transform 0.2s, box-shadow 0.2s, background 0.3s; box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3); }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4); }
        button:disabled { background: #aaa; cursor: not-allowed; transform: none; box-shadow: none; }
        .start-button, .restart-button { background: linear-gradient(90deg, #007bff, #0056b3); box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3); }
        .start-button:hover, .restart-button:hover { background: linear-gradient(90deg, #0069d9, #004a99); }
        .result { text-align: center; font-size: 1.6em; font-weight: bold; margin-bottom: 20px; }
        .no-questions, .loading, .error-msg { text-align: center; color: #dc3545; font-weight: bold; font-size: 1.1em; }
        .loading { color: #555; }
        .timer { position: fixed; top: 20px; right: 20px; background-color: #dc3545; color: white; padding: 10px 20px; border-radius: 25px; font-size: 1.2em; font-weight: bold; box-shadow: 0 2px 8px rgba(0,0,0,0.25); z-index: 1000; transition: all 0.5s; opacity: 0; transform: translateY(-20px); }
        .timer.show { opacity: 1; transform: translateY(0); }
        .timer.low-time { background-color: #ffc107; color: black; animation: pulse 1s infinite; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
        .time-up-msg { color: #dc3545; font-weight: bold; margin-bottom: 15px; }
        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        #player-status { position: fixed; top: 20px; left: 20px; background-color: #fff; padding: 8px 15px; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-size: 0.9em; z-index: 1000;}
        #player-status a { color: #007bff; text-decoration: none; margin: 0 5px; }
        #player-status a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div id="player-status">Memuat...</div>
    <div id="timer" class="timer">--:--</div>
    <div class="quiz-container" id="quiz-container">
        <h1>Kuis Interaktif PHP + AJAX</h1>
        <p>Selamat datang! Kuis akan dimuat tanpa refresh halaman.</p>
        <button id="start-button" class="start-button">Mulai Kuis!</button>
    </div>

<script>
    const quizContainer = document.getElementById('quiz-container');
    const timerElement = document.getElementById('timer');
    const playerStatusElement = document.getElementById('player-status');
    const apiUrl = 'api_kuis.php';
    let countdownInterval;

    // Ambil data PHP untuk status login
    const isPlayerLoggedIn = <?php echo json_encode($is_player_logged_in); ?>;
    const playerDisplayName = <?php echo json_encode($player_display_name); ?>;

    function showMessage(message, type = 'loading') {
        quizContainer.innerHTML = `<h2 class="${type}">${message}</h2>`;
        timerElement.classList.remove('show');
        clearInterval(countdownInterval);
    }

    async function fetchApi(url, options = {}) {
        try {
            const response = await fetch(url, options);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const result = await response.json();
            if (result.status !== 'ok') throw new Error(result.message || 'Respons API tidak valid.');
            return result.data;
        } catch (error) {
            showMessage(`Terjadi kesalahan: ${error.message}`, 'error-msg');
            return null;
        }
    }

    async function startQuiz() {
        showMessage('Memulai kuis...');
        const data = await fetchApi(`${apiUrl}?action=start_quiz`);
        if (data) updateUI(data);
    }

    async function submitAnswer(answer = null, timeUp = false) {
        showMessage('Memproses jawaban...');
        const formData = new FormData();
        formData.append('action', 'submit_answer');
        if (answer !== null) formData.append('answer', answer);
        if (timeUp) formData.append('time_up', 'true');
        const data = await fetchApi(apiUrl, { method: 'POST', body: formData });
        if (data) updateUI(data);
    }

    function updateUI(data) {
        quizContainer.innerHTML = '';
        if (!data) { showMessage('Gagal memuat data kuis.', 'error-msg'); return; }

        if (data.quiz_finished) {
            clearInterval(countdownInterval);
            timerElement.classList.remove('show');
            let resultHTML = `
                <div class="fade-in">
                    <h2>Kuis Selesai! 🎉</h2>
                    ${data.time_up ? '<p class="time-up-msg">Waktu Anda Telah Habis!</p>' : ''}
                    <p class="result">Skor Akhir Anda: ${data.score} dari ${data.total_questions}</p>
                    ${isPlayerLoggedIn ? '<p>Skor Anda telah disimpan!</p>' : '<p>Login untuk menyimpan skor Anda!</p>'}
                    <button id="restart-button" class="restart-button">Ulangi Kuis</button>
                </div>`;
            quizContainer.innerHTML = resultHTML;
            document.getElementById('restart-button').addEventListener('click', startQuiz);
        } else {
            timerElement.classList.add('show');
            let questionHTML = `
                <div class="fade-in">
                    <h2>Pertanyaan ${data.current_question_index + 1} dari ${data.total_questions}</h2>
                    <div class="question-area">
                        <p class="question">${escapeHtml(data.question_text)}</p>
                        <form id="quizForm">
                            <div class="options">
                                ${data.options.map(option => `
                                    <label>
                                        <input type="radio" name="answer" value="${escapeHtml(option)}" required>
                                        ${escapeHtml(option)}
                                    </label>
                                `).join('')}
                            </div>
                            <button id="submit-button" type="submit">Jawab & Lanjut</button>
                        </form>
                    </div>
                </div>`;
            quizContainer.innerHTML = questionHTML;
            document.getElementById('quizForm').addEventListener('submit', (e) => {
                e.preventDefault();
                const selectedAnswer = document.querySelector('input[name="answer"]:checked');
                submitAnswer(selectedAnswer ? selectedAnswer.value : null);
            });
            document.querySelectorAll('input[name="answer"]').forEach(radio => {
                radio.addEventListener('change', () => {
                    document.querySelectorAll('.options label').forEach(label => label.classList.remove('selected'));
                    if(radio.checked) radio.closest('label').classList.add('selected');
                });
            });
            startJsTimer(data.remaining_time);
        }
    }

    function startJsTimer(duration) {
        clearInterval(countdownInterval);
        let timer = duration;
        let minutes, seconds;
        function updateDisplay() {
            minutes = parseInt(timer / 60, 10); seconds = parseInt(timer % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes; seconds = seconds < 10 ? "0" + seconds : seconds;
            timerElement.textContent = minutes + ":" + seconds;
            timerElement.classList.remove('low-time');
            if (timer < 60) { timerElement.classList.add('low-time'); }
        }
        updateDisplay();
        countdownInterval = setInterval(() => {
            if (--timer < 0) {
                clearInterval(countdownInterval); timerElement.textContent = "WAKTU HABIS!"; submitAnswer(null, true);
            } else { updateDisplay(); }
        }, 1000);
    }

    function escapeHtml(text) {
        if (typeof text !== 'string') return '';
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function updatePlayerStatusDisplay() {
        if (isPlayerLoggedIn) {
            playerStatusElement.innerHTML = `Selamat datang, <strong>${playerDisplayName}</strong>! | <a href='leaderboard.php'>Papan Peringkat</a> | <a href='logout_player.php'>Logout</a>`;
        } else {
            playerStatusElement.innerHTML = `<a href='login_player.php'>Login</a> | <a href='register.php'>Daftar</a> | <a href='leaderboard.php'>Papan Peringkat</a>`;
        }
    }

    document.getElementById('start-button').addEventListener('click', startQuiz);
    updatePlayerStatusDisplay(); // Panggil saat halaman dimuat
</script>
</body>
</html>