<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request a Book - EchoWords</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298, #6e8efb, #a777e3);
            color: white;
            margin: 0;
            padding: 0;
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
    color: white;
    text-decoration: none;
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
        .form-container {
            margin-top: 200px;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            width: 400px;
            text-align: center;
        }
        .form-container h2 {
            margin-bottom: 30px;
        }
        .form-container input[type="text"],
        .form-container input[type="file"] {
            width: 90%;
            padding: 15px;
            margin: 15px 0;
            border: none;
            border-radius: 10px;
            font-size: 16px;
        }
        .form-container input[type="submit"] {
            background-color: #ffcc00;
            color: black;
            padding: 15px 30px;
            border: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
        }
        .form-container input[type="submit"]:hover {
            background-color: #ffaa00;
        }
    </style>
</head>
<body>
    <header>
        <a href="loggedinhome.php" class="logo">EchoWords</a>
        <nav>
            <ul>
                <!-- <li><a href="loggedinhome.php">Home</a></li> -->
                <li><a href="myacc.php">My Account</a></li>
                <li><a href="request_book.php">Request a Book</a></li>
                <?php if (isset($_SESSION["user"])): ?>
                    <li><a href="logout.php" class="logout-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="form-container">
        <h2>Request a Book</h2>
        <form action="handlereq.php" method="post" enctype="multipart/form-data">
            <input type="text" name="book_name" placeholder="Name of Book" required>
            <input type="text" name="author" placeholder="Author" required>
            <input type="file" name="cover" accept="image/*" required>
            <input type="submit" value="Submit Request">
        </form>
    </div>
</body>
</html>
