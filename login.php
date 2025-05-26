<?php
session_start();
include 'koneksi.php';

$error_message = '';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error_message = "Username dan password tidak boleh kosong!";
    } else {
        $sql = "SELECT id, username, password_hash FROM users WHERE username = ?"; // Menggunakan tabel 'users'
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password_hash'])) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    header("Location: admin.php");
                    exit;
                } else {
                    $error_message = "Username atau password salah.";
                }
            } else {
                $error_message = "Username atau password salah.";
            }
            $stmt->close();
        } else {
            $error_message = "Terjadi kesalahan pada server.";
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background-color: #fff; padding: 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        h1 { color: #007bff; margin-bottom: 30px; }
        label { display: block; text-align: left; margin-bottom: 5px; color: #555; font-weight: bold; }
        input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer; transition: background-color 0.3s; }
        button:hover { background-color: #0056b3; }
        .error { color: #dc3545; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Login Admin</h1>
        <?php if (!empty($error_message)) : ?>
            <div class="error"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
</body>
</html>