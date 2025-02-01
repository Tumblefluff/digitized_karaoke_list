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

// Fetch song to verify it exists
$stmt = $pdo->prepare("SELECT * FROM songs WHERE id = ?");
$stmt->execute([$song_id]);
$song = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$song) {
    header("Location: index.php?error=SongNotFound");
    exit;
}

// Delete the song
$stmt = $pdo->prepare("DELETE FROM songs WHERE id = ?");
$stmt->execute([$song_id]);

header("Location: index.php?success=SongDeleted");
exit;
