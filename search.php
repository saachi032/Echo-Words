<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EchoWords - Audiobooks</title>
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
            flex-direction: column;
            align-items: center;
        }
        header {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0, 0, 50, 0.9);
            padding: 20px 40px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .logo {
            font-size: 36px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        nav {
            flex-grow: 1;
            display: flex;
            justify-content: center;
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
            transition: color 0.3s ease;
        }
        nav ul li a:hover {
            color: #ffcc00;
        }
        .search-section {
            text-align: center;
            margin-top: 120px;
        }
        .search-section form {
            display: inline-flex;
            align-items: center;
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
        .continue-reading, .recommendations {
            margin-bottom: 50px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
        }
        .book-list {
            display: flex;
            justify-content: center;
            gap: 30px;
        }
        .book {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            flex: 1;
            text-align: center;
            transition: transform 0.3s ease;
            position: relative;
        }
        .book img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .book:hover {
            transform: scale(1.1);
        }
        footer {
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 50, 0.9);
            margin-top: 50px;
            width: 100%;
        }
        .welcome-message {
            margin-top: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">EchoWords</div>
        <nav>
            <ul id="navLinks">
                <li><a href="#">Home</a></li>
                <li><a href="login.html">Login</a></li>
                <li><a href="signup.html">Sign Up</a></li> 
                <li><a href="myacc.html">My Account</a></li> 
                <li id="logoutLink" style="display: none;"><a href="#" onclick="handleLogout()">Logout</a></li>
            </ul>
        </nav>
    </header>
    
    <section class="search-section">
        <form id="searchForm" onsubmit="handleSearch(event)">
            <input type="text" id="searchInput" placeholder="Search for books...">
            <button type="submit">Search</button>
        </form>
    </section>
    
    <main>
        <section class="continue-reading">
            <h2>Continue Reading</h2>
            <div class="book-list">
                <div class="book"><img src="https://m.media-amazon.com/images/I/81YOuOGFCJL.jpg" alt="Harry Potter and Philosopher's Stone ">Harry Potter and Philosopher's Stone</div>
                <div class="book"><img src="https://m.media-amazon.com/images/I/91OINeHnJGL.jpg" alt="Harry Potter and Chamber of Secrets">Harry Potter and Chamber of Secrets</div>
                <div class="book"><img src="https://m.media-amazon.com/images/I/71aFt4+OTOL.jpg" alt="The Alchemist">The Alchemist</div>
            </div>
        </section>
        <section class="recommendations">
            <h2>This Week's Recommendations</h2>
            <div class="book-list">
                <div class="book"><img src="https://m.media-amazon.com/images/I/71aFt4+OTOL.jpg" alt="The Alchemist">The Alchemist</div>
                <div class="book"><img src="https://m.media-amazon.com/images/I/91SZSW8qSsL.jpg" alt="1984">1984</div>
                <div class="book"><img src="https://m.media-amazon.com/images/I/81WcnNQ-TBL.jpg" alt="Big Magic">Big Magic</div>
            </div>
        </section>
    </main>
    <div class="welcome-message" id="welcomeMessage"></div>

    <script>
        // Check login status on page load
        function checkLoginStatus() {
            const isLoggedIn = localStorage.getItem('isLoggedIn') === 'true';
            const navLinks = document.getElementById('navLinks');
            const welcomeMessage = document.getElementById('welcomeMessage');
            const logoutLink = document.getElementById('logoutLink');

            if (isLoggedIn) {
                const currentUser = JSON.parse(localStorage.getItem('currentUser'));
                welcomeMessage.textContent = `Welcome, ${currentUser.firstName}!`;
                // Hide login and signup links, show logout link
                document.querySelectorAll('a[href="login.html"], a[href="signup.html"]').forEach(link => link.style.display = 'none');
                logoutLink.style.display = 'inline-block';
            } else {
                welcomeMessage.textContent = '';
                document.querySelectorAll('a[href="login.html"], a[href="signup.html"]').forEach(link => link.style.display = 'inline-block');
                logoutLink.style.display = 'none';
            }
        }

        // Handle logout
        function handleLogout() {
            localStorage.removeItem('isLoggedIn');
            localStorage.removeItem('currentUser');
            window.location.href = 'home.html';
        }

        // Handle search
        function handleSearch(event) {
            event.preventDefault(); // Prevent form from submitting traditionally

            // Get the search query
            const query = document.getElementById('searchInput').value.trim();

            // Redirect to search page with query parameter
            if (query) {
                window.location.href = `search.html?query=${encodeURIComponent(query)}`;
            }
        }

        // Run login check on page load
        window.onload = checkLoginStatus;
    </script>
    <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'91f562b7ddb3672f',t:'MTc0MTgwNDQ2NS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script>
</body>
</html>