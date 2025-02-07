<?php
session_start();
require_once __DIR__ . '/../karaoke-cfg/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Ensure the username is set before using it
$username = isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : "Admin";

// Fetch all songs from the database
$stmt = $pdo->query("SELECT id, title, artist FROM songs ORDER BY artist, title");
$songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="stylesheet" href="style.css">
</head>
<body id="admin-dashboard">
    <center> <h2>Admin Dashboard</h2> </center>
	<table>
		<tr>
			<th>
				<p><big>Welcome, <?php echo $username; ?>!</big></p>
    		    <a href="change-password.php">Update Password</a> - 
    			<a href="logout.php">Logout</a>
			</th><th>
    			<h3>Add New Song</h3>
    			<form action="add-song.php" method="POST" class="form-container">
        			<label for="title">Title:</label>
			        <input type="text" id="title" name="title" required>
        			<label for="artist">Artist:</label>
        			<input type="text" id="artist" name="artist" required>
		        	<button type="submit">Add Song</button>
			    </form>
        	</th>
		</tr>
	</table>
    <h3>Song List</h3>
    <table>
        <tr>
            <th>Title</th>
            <th>Artist</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($songs as $song): ?>
        <tr>
            <td><?php echo htmlspecialchars($song["title"]); ?></td>
            <td><?php echo htmlspecialchars($song["artist"]); ?></td>
            <td>
                <a href="edit-song.php?id=<?php echo $song['id']; ?>">Edit</a> | 
                <a href="delete-song.php?id=<?php echo $song['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
