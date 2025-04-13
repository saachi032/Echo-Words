<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Submitted</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: rgba(30, 60, 114, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }

        .popup {
            background: #222;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
            text-align: center;
            max-width: 400px;
        }

        .popup h2 {
            margin-bottom: 20px;
            color: #ffcc00;
        }

        .popup button {
            padding: 10px 20px;
            background-color: #ffcc00;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .popup button:hover {
            background-color: #ffaa00;
        }
    </style>
</head>
<body>
    <div class="popup">
        <h2>Request Submitted!</h2>
        <p>Thank you for requesting a book.</p>
        <button onclick="window.location.href='loggedinhome.php'">Close</button>
    </div>
</body>
</html>