<?php
session_start();
include 'db.php';

// Get book title from URL
if (!isset($_GET["title"])) {
    die("Book not found.");
}

$title = urldecode($_GET["title"]);

// Fetch book details
$query = "SELECT title, file_path, hits FROM books WHERE title=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $title);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

if (!$book) {
    die("Book not found.");
}

// Update hits counter
$update_hits = $conn->prepare("UPDATE books SET hits = hits + 1 WHERE title=?");
$update_hits->bind_param("s", $title);
$update_hits->execute();

// Full file path
$file_path = "http://localhost/" . $book["file_path"];

// Check if file exists
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $book["file_path"])) {
    die("File not found: " . htmlspecialchars($file_path));
}

// Get file extension
$file_extension = pathinfo($book["file_path"], PATHINFO_EXTENSION);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book["title"]) ?> - EchoWords</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #1e3c72, #2a5298, #6e8efb, #a777e3);
            color: white;
        }
        .container {
            max-width: 800px;
            margin: 60px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            overflow-y: auto;
            max-height: 80vh;
            text-align: center;
        }
        h1 {
            color: #ffcc00;
        }
        .hits {
            margin-top: 10px;
            color: #ffcc00;
        }
        .pdf-container {
            margin-top: 20px;
            width: 100%;
            height: 600px;
            border: none;
        }
        .download {
            margin-top: 20px;
        }
        .download a {
            background: #ffcc00;
            padding: 10px 15px;
            color: black;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
        }
        .download a:hover {
            background: #ffaa00;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($book["title"]) ?></h1>
        <p class="hits"><?= $book["hits"] + 1 ?> views</p>

        <?php if ($file_extension === "pdf"): ?>
            <iframe class="pdf-container" src="<?= htmlspecialchars($file_path) ?>"></iframe>
            <div class="download">
                <a href="<?= htmlspecialchars($file_path) ?>" download>Download PDF</a>
            </div>
        <?php else: ?>
            <p>Book format not supported.</p>
        <?php endif; ?>
    </div>
</body>
</html>
