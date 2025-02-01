<?php
require_once __DIR__ . '/../karaoke-cfg/db.php';

// Handle search and sorting
$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$sort = isset($_GET["sort"]) ? $_GET["sort"] : "artist"; // Default sorting by artist

// Validate sort parameter
$validSortColumns = ["artist", "title"];
if (!in_array($sort, $validSortColumns)) {
    $sort = "artist";
}

$query = "SELECT title, artist FROM songs";
$params = [];

if ($search !== "") {
    $query .= " WHERE title LIKE ? OR artist LIKE ?";
    $params = ["%$search%", "%$search%"];
}

$query .= " ORDER BY $sort ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karaoke Song List</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <style>
        /* Dark mode styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #222;
            color: #ddd;
            text-align: center;
            padding: 20px;
        }
        input, select, button {
            padding: 10px;
            margin: 10px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #333;
            color: #ddd;
        }
        button {
            cursor: pointer;
            font-weight: bold;
        }
        table {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #333;
            color: #ddd;
        }
        th, td {
            border: 1px solid #444;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #444;
            cursor: pointer;
        }
        th a {
            color: #ddd;
            text-decoration: none;
        }
        th a:hover {
            text-decoration: underline;
        }
        tr:nth-child(even) {
            background-color: #292929;
        }
    </style>
</head>
<body>
    <h1>Karaoke Song List</h1>

    <form method="GET" action="index.php">
        <input type="text" name="search" placeholder="Search by song title or artist" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <table>
        <tr>
            <th><a href="?search=<?php echo urlencode($search); ?>&sort=title">Title</a></th>
            <th><a href="?search=<?php echo urlencode($search); ?>&sort=artist">Artist</a></th>
        </tr>
        <?php if (empty($songs)): ?>
            <tr><td colspan="2">No songs found.</td></tr>
        <?php else: ?>
            <?php foreach ($songs as $song): ?>
                <tr>
                    <td><?php echo htmlspecialchars($song["title"]); ?></td>
                    <td><?php echo htmlspecialchars($song["artist"]); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>
</body>
</html>
