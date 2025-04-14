<?php
// Simulate book list for guest page
$books = [
    ["title" => "Verity"],
    ["title" => "Harry Potter and the Chamber of Secrets"],
    ["title" => "The Alchemist"]
];

function getBookCover($title) {
  // Replace spaces with underscores
  $filename = str_replace(' ', '_', $title);

  // Remove all characters except letters, numbers, underscores, and dashes
  $filename = preg_replace('/[^A-Za-z0-9_\-]/', '', $filename);

  $path = "covers/" . $filename . ".jpg";

  return file_exists($path) ? $path : "covers/default.jpg";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EchoWords</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #1e3c72, #2a5298, #6e8efb, #a777e3);
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
      max-width: 50%;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      padding: 0;
      margin: 0;
      flex-shrink: 1;
    }

    nav ul li a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      font-size: 16px;
      transition: color 0.3s ease;
      white-space: nowrap;
    }

    nav ul li a:hover {
      color: #ffcc00;
    }

    .search-section {
      text-align: center;
      margin-top: 180px;
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
      padding: 10px 20px;
      text-align: center;
    }

    .recommendations {
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
      flex-wrap: wrap;
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
      min-width: 250px;
      max-width: 300px;
    }

    .book img {
      width: 100%;
      height: auto;
      border-radius: 10px;
    }

    .book:hover {
      transform: scale(1.1);
    }

    .book a {
      display: block;
      text-decoration: none;
      color: white;
      font-size: 18px;
      font-weight: bold;
      margin-top: 10px;
    }

    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      color: black;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
      text-align: center;
      width: 300px;
    }

    .popup:target {
      display: block;
    }

    .popup a {
      display: inline-block;
      margin-top: 15px;
      padding: 10px 20px;
      background: #ffcc00;
      color: black;
      text-decoration: none;
      border-radius: 10px;
      font-weight: bold;
    }

    .no-results {
      color: white;
      font-size: 20px;
      font-weight: bold;
      margin-top: 30px;
    }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        padding: 15px 20px;
      }

      nav {
        max-width: 100%;
      }

      nav ul {
        flex-direction: column;
        gap: 15px;
        margin-top: 15px;
        text-align: center;
      }

      .logo {
        font-size: 32px;
      }

      .search-section input {
        width: 80%;
      }
    }
  </style>
</head>
<body>
  <header>
    <a href="home.php" class="logo">EchoWords</a>
    <nav>
      <ul>
        <!-- <li><a href="#">Home</a></li> -->
        <li><a href="login.php">Login</a></li>
        <li><a href="signup.php">Sign Up</a></li>
      </ul>
    </nav>
  </header>

  <section class="search-section">
    <input type="text" id="searchInput" placeholder="Search for books..." onkeyup="filterBooks()" />
    <button onclick="filterBooks()">Search</button>
  </section>

  <main>
    <section class="recommendations" id="recommendations">
      <h2>This Week's Recommendations</h2>
      <div class="book-list" id="bookList">
        <?php foreach ($books as $book): ?>
        <div class="book" data-title="<?= htmlspecialchars($book['title']) ?>">
          <a href="#popup">
            <img src="<?= getBookCover($book['title']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            <?= htmlspecialchars($book['title']) ?>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
      <div id="noResults" class="no-results" style="display: none;">No results found</div>
    </section>
  </main>

  <div id="popup" class="popup">
    <p>You must log in to access this book.</p>
    <a href="login.php">Login</a>
    <br />
    <a href="#">Close</a>
  </div>

  <script>
    function filterBooks() {
      const input = document.getElementById('searchInput').value.toLowerCase();
      const books = document.querySelectorAll('.book');
      const noResults = document.getElementById('noResults');
      const recommendations = document.getElementById('recommendations');
      let anyVisible = false;

      books.forEach(book => {
        const title = book.getAttribute('data-title').toLowerCase();
        if (title.includes(input)) {
          book.style.display = 'block';
          anyVisible = true;
        } else {
          book.style.display = 'none';
        }
      });

      noResults.style.display = anyVisible ? 'none' : 'block';
      recommendations.querySelector('h2').style.display = input ? 'none' : 'block';
    }
  </script>
</body>
</html>
