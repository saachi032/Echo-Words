<?php
include "db.php";

if (isset($_GET["title"])) {
    $title = urldecode($_GET["title"]);

    // Update hits count
    $stmt = $conn->prepare("UPDATE books SET hits = hits + 1 WHERE title = ?");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the homepage
    header("Location: loggedinhome.php");
    exit();
}
?>
