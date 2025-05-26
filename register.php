<?php
include 'koneksi.php';
session_start();

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $display_name = trim($_POST['display_name']) ?: $username; // Gunakan username jika nama tampilan kosong

    // Validasi dasar
    if (empty($username) || empty($password) || empty($password_confirm)) {
        $error_message = "Semua field (kecuali Nama Tampilan) wajib diisi!";
    } elseif ($password !== $password_confirm) {
        $error_message = "Konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error_message = "Password minimal harus 6 karakter!";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
         $error_message = "Username hanya boleh berisi huruf, angka, dan underscore (_)!";
    } else {
        // Cek apakah username sudah ada
        $sql_check = "SELECT id FROM players WHERE username = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "Username sudah digunakan, silakan pilih yang lain.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Simpan ke database
            $sql_insert = "INSERT INTO players (username, password_hash, display_name) VALUES (?, ?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("sss", $username, $hashed_password, $display_name);

            if ($stmt_insert->execute()) {
                $success_message = "Registrasi berhasil! Silakan <a href='login_player.php'>login</a>.";
            } else {
                $error_message = "Registrasi gagal. Silakan coba lagi. " . $stmt_insert->error;
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Pemain Kuis</title>
    <style>
        body { font-family: sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .box { background-color: #fff; padding: 30px 40px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 350px; text-align: center; }
        h1 { color: #28a745; margin-bottom: 25px; }
        label { display: block; text-align: left; margin-bottom: 5px; color: #555; font-weight: bold; }
        input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 12px; background-color: #28a745; color: white; border: none; border-radius: 4px; font-size: 1.1em; cursor: pointer; transition: background-color 0.3s; }
        button:hover { background-color: #218838; }
        .error { color: #dc3545; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        .link { margin-top: 15px; display: inline-block; margin: 15px 5px 0 5px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Registrasi Pemain</h1>
        <?php if (!empty($error_message)) : ?> <div class="error"><?php echo $error_message; ?></div> <?php endif; ?>
        <?php if (!empty($success_message)) : ?> <div class="success"><?php echo $success_message; ?></div> <?php else: ?>
        <form action="register.php" method="POST">
            <div> <label for="username">Username:</label> <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required> </div>
            <div> <label for="display_name">Nama Tampilan (Opsional):</label> <input type="text" id="display_name" name="display_name" value="<?php echo isset($_POST['display_name']) ? htmlspecialchars($_POST['display_name']) : ''; ?>"> </div>
            <div> <label for="password">Password:</label> <input type="password" id="password" name="password" required> </div>
            <div> <label for="password_confirm">Konfirmasi Password:</label> <input type="password" id="password_confirm" name="password_confirm" required> </div>
            <div> <button type="submit">Daftar</button> </div>
        </form>
        <?php endif; ?>
        <a href="login_player.php" class="link">Sudah punya akun? Login</a> |
        <a href="index.php" class="link">Kembali ke Kuis</a>
    </div>
</body>
</html>