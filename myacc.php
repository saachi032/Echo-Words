<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$mysqli = new mysqli("localhost", "root", "", "echowords");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch user details
$userStmt = $mysqli->prepare("SELECT fname, lname, phno FROM user WHERE email = ?");
$userStmt->bind_param("s", $email);
$userStmt->execute();
$userStmt->bind_result($fname, $lname, $phno);
$userStmt->fetch();
$userStmt->close();

// Fetch requested books
$bookStmt = $mysqli->prepare("SELECT book_name, cover_path FROM bookreq WHERE email = ?");
$bookStmt->bind_param("s", $email);
$bookStmt->execute();
$bookResult = $bookStmt->get_result();
$books = [];
while ($row = $bookResult->fetch_assoc()) {
    $books[] = $row;
}
$bookStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - EchoWords</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            padding: 30px 50px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 50px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-decoration: none;
            color: white;
        }
        nav {
            max-width: 50%;
        }
        nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
            padding: 0;
            margin: 0;
        }
        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s ease;
            padding: 10px 20px;
            border-radius: 5px;
        }
        nav ul li a:hover {
            color: #ffcc00;
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
        .account-container {
            width: 80%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.1);
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            margin-top: 150px;
            text-align: center;
        }
        .account-info h2 {
            font-size: 36px;
            font-weight: bold;
            color: #ffcc00;
            margin-bottom: 10px;
        }
        .account-info p {
            font-size: 20px;
            font-weight: 500;
            color: white;
            margin: 8px 0;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .stat-box {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 30%;
        }
        .stat-box h3 {
            font-size: 22px;
            color: #ffcc00;
        }
        .stat-box p {
            font-size: 20px;
            font-weight: bold;
        }
        .book-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }
        .book {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 120px;
        }
        .book img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }
        .chart-container {
            margin-top: 30px;
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <header>
    <a href="loggedinhome.php" class="logo">EchoWords</a>
    <nav>
            <ul>
                <li><a href="myacc.php">My Account</a></li>
                <li><a href="request_book.php">Request Book</a></li>
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="account-container">
        <div class="account-info">
            <h2>My Account</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($fname . " " . $lname); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($phno); ?></p>
        </div>

        <div class="stats">
            <div class="stat-box">
                <h3>Books in Progress</h3>
                <p>3</p>
            </div>
            <div class="stat-box">
                <h3>Completed Books</h3>
                <p>12</p>
            </div>
            <div class="stat-box">
                <h3>Average Reading Time</h3>
                <p>2 hrs/day</p>
            </div>
        </div>

        <h2>Books Requested</h2>
        <div class="book-list">
            <?php if (count($books) === 0): ?>
                <p>No books found.</p>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="book">
                        <img src="<?php echo htmlspecialchars($book['cover_path']); ?>" alt="<?php echo htmlspecialchars($book['book_name']); ?>">
                        <?php echo htmlspecialchars($book['book_name']); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="chart-container">
            <h2>Time Spent Reading (Peak Hours)</h2>
            <canvas id="readingChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('readingChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['6 AM', '9 AM', '12 PM', '3 PM', '6 PM', '9 PM', '12 AM'],
                datasets: [{
                    label: 'Reading Time (hours)',
                    data: [0.5, 1, 1.5, 1, 2, 3, 0.5],
                    backgroundColor: 'rgba(255, 204, 0, 0.2)',
                    borderColor: '#000000',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
