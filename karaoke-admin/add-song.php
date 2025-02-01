<?php
session_start();
require_once __DIR__ . '/../karaoke-cfg/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $artist = trim($_POST["artist"]);
    $added_by = $_SESSION["username"]; // Store the username who added the song

    if (!empty($title) && !empty($artist)) {
        $stmt = $pdo->prepare("INSERT INTO songs (title, artist, added_by) VALUES (?, ?, ?)");
        $stmt->execute([$title, $artist, $added_by]);

        header("Location: index.php?success=SongAdded");
        exit;
    } else {
        header("Location: index.php?error=MissingFields");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
