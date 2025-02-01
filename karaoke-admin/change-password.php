<?php
session_start();
require_once __DIR__ . '/../karaoke-cfg/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION["user_id"];
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match.";
    } else {
        // Retrieve current hashed password
        $stmt = $pdo->prepare("SELECT password_hash, username FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($current_password . hash('sha256', hash('sha256', $user['username'])), $user["password_hash"])) {
            $message = "Current password is incorrect.";
        } else {
            // Generate new hashed password
            $salt = hash('sha256', hash('sha256', $user["username"]));
            $new_hashed_password = password_hash($new_password . $salt, PASSWORD_DEFAULT);

            // Update password in the database
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$new_hashed_password, $user_id]);

            $message = "Password successfully updated!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Change Password</h2>

    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <div class="form-container">
	    <form method="POST" action="change-password.php">
    	    <label>Current Password:</label>
        	<input type="password" name="current_password" required><br>

        	<label>New Password:</label>
    	    <input type="password" name="new_password" required><br>

	        <label>Confirm New Password:</label>
        	<input type="password" name="confirm_password" required><br>

    	    <button type="submit">Change Password</button>
	    </form>
	</div>

    <br>
    <a href="index.php">Back to Dashboard</a>
</body>
</html>
