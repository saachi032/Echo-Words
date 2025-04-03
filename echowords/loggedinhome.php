<?php
session_start();

// Database connection
$host = "localhost";
$user = "root"; // Change if needed
$pass = ""; // Change if needed
$dbname = "echowords"; // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM books");
$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}

$recommendations = $conn->query("SELECT * FROM books ORDER BY hits DESC LIMIT 3");
$recommended_books = [];
while ($row = $recommendations->fetch_assoc()) {
    $recommended_books[] = $row;
}

function getBookCover($title) {
    $filename = "covers/" . str_replace(" ", "_", $title) . ".jpg"; 
    return file_exists($filename) ? $filename : "covers/default.jpg"; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EchoWords - Audiobooks</title>
    <script>
        function updateHits(bookTitle) {
            fetch("update_hits.php?title=" + encodeURIComponent(bookTitle))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("hits-" + bookTitle.replace(/\s+/g, '_')).innerText = data.hits + " Hits";
                    }
                });
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #1e3c72, #2a5298, #6e8efb, #a777e3);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        header {
            width: 95%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 50, 0.9);
            padding: 20px 50px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 40px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        nav {
            flex-grow: 1;
            display: flex;
            justify-content: right;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 25px;
            padding: 0;
            margin: 0;
        }
        nav ul li {
            display: inline-block;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s ease;
        }
        nav ul li a:hover {
            color: #ffcc00;
        }
        
        /* SEARCH SECTION ADDED */
        .search-section {
            text-align: center;
            margin-top: 140px;
        }
        .search-section input {
            width: 50%;
            padding: 15px;
            font-size: 20px;
            border: none;
            border-radius: 50px;
            outline: none;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
        }
        .search-section button {
            padding: 15px 30px;
            background-color: #ffcc00;
            color: black;
            border: none;
            cursor: pointer;
            font-size: 20px;
            margin-left: 10px;
            border-radius: 50px;
            transition: background 0.3s ease;
        }
        .search-section button:hover {
            background: #ffaa00;
        }

        main {
            max-width: 1200px;
            margin-top: 50px;
            padding: 40px 20px;
            text-align: center;
        }

        .book-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
        }
        .book {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            width: 150px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .book img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .book:hover {
            transform: scale(1.1);
        }
        .hits-counter {
            margin-top: 10px;
            color: #ffcc00;
        }
        footer {
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 50, 0.9);
            margin-top: 50px;
            width: 100%;
        }
        .logout-btn {
            padding: 10px 20px;
            background: #ff4444;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        .logout-btn:hover {
            background: #cc0000;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">EchoWords</div>
        <nav>
            <ul>
                <li><a href="loggedinhome.php">Home</a></li>
                <li><a href="myacc.php">My Account</a></li>
                <?php if (isset($_SESSION["user"])): ?>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- SEARCH SECTION ADDED -->
    <section class="search-section">
        <input type="text" placeholder="Search for books...">
        <button>Search</button>
    </section>

    <main>
        <section class="continue-reading">
            <h2>Available Books</h2>
            <div class="book-list">
                <?php foreach ($books as $book): ?>
                    <div class="book">
                        <a href="<?= htmlspecialchars($book["file_path"]) ?>" target="_blank">
                            <img src="<?= getBookCover($book["title"]) ?>" 
                                 alt="<?= htmlspecialchars($book["title"]) ?>">
                        </a>
                        <p><?= htmlspecialchars($book["title"]) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="recommendations">
            <h2>This Week's Recommendations</h2>
            <div class="book-list">
                <?php foreach ($recommended_books as $book): ?>
                    <div class="book">
                        <a href="<?= htmlspecialchars($book["file_path"]) ?>" target="_blank">
                            <img src="<?= getBookCover($book["title"]) ?>" 
                                 alt="<?= htmlspecialchars($book["title"]) ?>">
                        </a>
                        <p><?= htmlspecialchars($book["title"]) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>
