<?php
session_start();
require_once __DIR__ . '/../karaoke-cfg/db.php';
require_once __DIR__ . '/../karaoke-cfg/auth.php';
require_once __DIR__ . '/../karaoke-cfg/veri.php';

$error = "";
$success = "";

if (!isset($_SESSION["new_user"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $code = trim($_POST["code"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($username === $_SESSION["new_user"] && $code === $verification_code) {
        if (!empty($password) && $password === $confirm_password) {
            // Hash and store the new password
            $hashed_password = hashPassword($username, $password);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
            $stmt->execute([$hashed_password, $username]);

            unset($_SESSION["new_user"]); // Remove flag
            $success = "Password set successfully! You can now log in.";
        } else {
            $error = "Passwords do not match or are empty.";
        }
    } else {
        $error = "Invalid verification code or username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Password</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Set Initial Password</h2>
    <?php if ($error): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>
    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
        <a href="login.php">Go to Login</a>
    <?php else: ?>
        <div class="form-container">
	    	<form method="POST">
    	        <label>Username:</label>
        	    <input type="text" name="username" required>
            	<br>
	            <label>Verification Code:</label>
    	        <input type="text" name="code" required>
        	    <br>
            	<label>New Password:</label>
	            <input type="password" name="password" required>
    	        <br>
        	    <label>Confirm Password:</label>
            	<input type="password" name="confirm_password" required>
        	    <br>
    	        <button type="submit">Set Password</button>
	        </form>
		</div>
    <?php endif; ?>
</body>
</html>
