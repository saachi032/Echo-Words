

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["signup"])) {
    $fname = $_POST["first_name"];
    $lname = $_POST["last_name"];
    $email = $_POST["email"];
    $phno = $_POST["phone"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location='signup.php';</script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $query = "INSERT INTO user (fname, lname, email, phno, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $fname, $lname, $email, $phno, $hashed_password);
    
    if ($stmt->execute()) {
        echo "<script>alert('Signup successful! Please login.'); window.location='login.php';</script>";
    } else {
        die("Error: " . $stmt->error); // Show exact SQL error
    }

    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - EchoWords</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
        
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
        .signup-container {
            display: flex;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            width: 60%;
            max-width: 800px;
            text-align: center;
        }
        .signup-left {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .signup-left h2 {
            color: #ffcc00;
            font-size: 26px;
            font-weight: 600;
            text-align: left;
        }
        .signup-right {
            flex: 1.5;
            padding: 20px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
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
            margin-top: 5px;
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
        .login-link {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-left">
            <h2>Discover your next must-read</h2>
        </div>
        <div class="signup-right">
            <h2>Create Account</h2>
            <form action="signup.php" method="POST">
                <label for="first-name">First Name</label>
                <input type="text" id="first-name" name="first_name" placeholder="First Name" required>

                <label for="last-name">Last Name</label>
                <input type="text" id="last-name" name="last_name" placeholder="Last Name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Email" required>

                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Password" required>

                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required>

                <button type="submit" name="signup">Create Account</button>
            </form>
            <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
</body>
</html>
