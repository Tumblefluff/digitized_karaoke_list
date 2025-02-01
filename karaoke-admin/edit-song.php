<?php
session_start();
require_once __DIR__ . '/../karaoke-cfg/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Ensure an ID was provided
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php?error=InvalidID");
    exit;
}

$song_id = (int)$_GET["id"];

// Fetch song details
$stmt = $pdo->prepare("SELECT * FROM songs WHERE id = ?");
$stmt->execute([$song_id]);
$song = $stmt->fetch(PDO::FETCH_ASSOC);

// If no song is found, redirect back
if (!$song) {
    header("Location: index.php?error=SongNotFound");
    exit;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $artist = trim($_POST["artist"]);

    if (!empty($title) && !empty($artist)) {
        $stmt = $pdo->prepare("UPDATE songs SET title = ?, artist = ? WHERE id = ?");
        $stmt->execute([$title, $artist, $song_id]);

        header("Location: index.php?success=SongUpdated");
        exit;
    } else {
        header("Location: edit-song.php?id=$song_id&error=MissingFields");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Song</title>
</head>
<body>
    <h2>Edit Song</h2>
    <form action="edit-song.php?id=<?php echo $song_id; ?>" method="POST">
        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($song["title"]); ?>" required>
        <br>
        <label>Artist:</label>
        <input type="text" name="artist" value="<?php echo htmlspecialchars($song["artist"]); ?>" required>
        <br>
        <button type="submit">Save Changes</button>
    </form>
    <br>
    <a href="index.php">Back to Admin</a>
</body>
</html>
