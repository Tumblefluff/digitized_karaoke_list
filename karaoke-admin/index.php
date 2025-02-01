<?php
session_start();
require_once __DIR__ . '/../karaoke-cfg/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

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
	<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Admin Dashboard</h2>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</p>
    
    <h3>Song List</h3>
    <table border="1">
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

    <h3>Add New Song</h3>
    <form action="add-song.php" method="POST">
        <label>Title:</label>
        <input type="text" name="title" required>
        <br>
        <label>Artist:</label>
        <input type="text" name="artist" required>
        <br>
        <button type="submit">Add Song</button>
    </form>

    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
