<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookName = $_POST["book_name"];
    $author = $_POST["author"];
    
    $targetDir = "uploads/requests/";
    $fileName = basename($_FILES["cover"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName;
    $uploadOk = 1;
    $email = $_SESSION["email"]; // add this line

    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileType, $allowedTypes)) {
        die("Only JPG, JPEG, PNG & GIF files are allowed.");
    }

    if (move_uploaded_file($_FILES["cover"]["tmp_name"], $targetFile)) {
        // Save to database
        $conn = new mysqli("localhost", "root", "", "echowords");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        
        // Modify the INSERT query
        $stmt = $conn->prepare("INSERT INTO bookreq (book_name, author, cover_path, email) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $bookName, $author, $targetFile, $email);
        $stmt->execute();
        $stmt->close();
        $conn->close();

        // Success popup
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Request Submitted</title>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                    background-color: rgba(0,0,0,0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .popup {
                    background-color: #fff;
                    padding: 40px;
                    border-radius: 10px;
                    text-align: center;
                    box-shadow: 0 0 15px rgba(0,0,0,0.3);
                }
                .popup h2 {
                    color: #333;
                }
                .popup button {
                    margin-top: 20px;
                    padding: 10px 20px;
                    background: #1e3c72;
                    color: white;
                    border: none;
                    border-radius: 5px;
                    cursor: pointer;
                    font-weight: bold;
                }
                .popup button:hover {
                    background: #6e8efb;
                }
            </style>
        </head>
        <body>
            <div class="popup">
                <h2>Request Submitted!</h2>
                <p>Your book request has been submitted successfully.</p>
                <button onclick="window.location.href = \'loggedinhome.php\'">Close</button>
            </div>
        </body>
        </html>';
        exit;
    } else {
        echo "File upload failed.";
    }
} else {
    header("Location: request-book.php");
    exit;
}
