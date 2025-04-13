<?php
session_start();
include 'db.php'; // Ensure this file contains proper database connection

// Redirect if user is already logged in
if (isset($_SESSION["user"])) {
    header("Location: loggedinhome.php");
    exit();
}

$error_message = ""; // To store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $query = "SELECT fname, password FROM user WHERE email=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($fname, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Store user session details
            $_SESSION["user"] = $fname;
            $_SESSION["email"] = $email;

            // Redirect to home page
            header("Location: loggedinhome.php");
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "User does not exist.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EchoWords</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #1e3c72, #2a5298, #6e8efb, #a777e3);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            width: 40%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: #ffcc00;
            font-size: 26px;
            font-weight: 600;
        }
        .error-msg {
            color: #ff4444;
            font-size: 14px;
            margin-bottom: 10px;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        label {
            font-size: 14px;
            font-weight: 500;
            text-align: left;
            width: 85%;
            margin-top: 10px;
        }
        input, button {
            width: 85%;
            padding: 10px;
            margin: 5px 0;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }
        input {
            background: rgba(255, 255, 255, 0.9);
            color: black;
        }
        button {
            background: #ffcc00;
            color: black;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #ffaa00;
        }
        .signup-link {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Welcome Back!</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-msg"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <button type="submit" name="login">Login</button>
        </form>
        <p class="signup-link">Don't have an account? <a href="signup.php">Sign up</a></p>
    </div>
</body>
</html>
