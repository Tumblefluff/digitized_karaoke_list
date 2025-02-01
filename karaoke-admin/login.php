<?php
session_start();
require_once __DIR__ . '/../karaoke-cfg/db.php';
require_once __DIR__ . '/../karaoke-cfg/auth.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (!empty($username) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && !empty($user["password_hash"])) {
            if (verifyPassword($username, $password, $user["password_hash"])) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $username;
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } elseif ($user && empty($user["password_hash"])) {
            // Redirect user to initial password setup
            $_SESSION["new_user"] = $username;
            header("Location: setup-password.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Admin Login</h2>
    <?php if ($error): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>
	<div class="form-container">
	    <h2>Login</h2>
	    <form action="login.php" method="POST">
        	<label>Username:</label>
    	    <input type="text" name="username" required>
	        <br>
        	<label>Password:</label>
    	    <input type="password" name="password" required>
	        <br>
        	<button type="submit">Login</button>
    	</form>
	</div>
</body>
</html>
