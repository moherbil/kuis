<?php
session_start();
include 'koneksi.php';

$error_message = '';

if (isset($_SESSION['player_logged_in']) && $_SESSION['player_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Username dan password tidak boleh kosong!";
    } else {
        $sql = "SELECT id, username, password_hash, display_name FROM players WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $player = $result->fetch_assoc();
            if (password_verify($password, $player['password_hash'])) {
                $_SESSION['player_logged_in'] = true;
                $_SESSION['player_id'] = $player['id'];
                $_SESSION['player_username'] = $player['username'];
                $_SESSION['player_display_name'] = $player['display_name'];
                header("Location: index.php");
                exit;
            } else { $error_message = "Username atau password salah."; }
        } else { $error_message = "Username atau password salah."; }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Pemain Kuis</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        h1 { color: #17a2b8; margin-bottom: 30px; }
        label { display: block; text-align: left; margin-bottom: 5px; color: #555; font-weight: bold; }
        input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 12px; background-color: #17a2b8; color: white; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer; transition: background-color 0.3s; }
        button:hover { background-color: #138496; }
        .error { color: #dc3545; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .link { margin-top: 15px; display: inline-block; margin: 15px 5px 0 5px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Login Pemain</h1>
        <?php if (!empty($error_message)) : ?> <div class="error"><?php echo $error_message; ?></div> <?php endif; ?>
        <form action="login_player.php" method="POST">
            <div> <label for="username">Username:</label> <input type="text" id="username" name="username" required> </div>
            <div> <label for="password">Password:</label> <input type="password" id="password" name="password" required> </div>
            <div> <button type="submit">Login</button> </div>
        </form>
        <a href="register.php" class="link">Belum punya akun? Daftar</a> |
        <a href="index.php" class="link">Kembali ke Kuis</a>
    </div>
</body>
</html>