<?php
session_start();

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "echowords";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch username for greeting
$greeting = '';
$username = 'User';
if (isset($_SESSION["user"]) && isset($_SESSION["email"])) {
    $email = $_SESSION["email"];
    $query = "SELECT fname FROM user WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($fname);
    if ($stmt->fetch()) {
        $username = htmlspecialchars($fname);
    }
    $stmt->close();

    date_default_timezone_set('Asia/Kolkata');
    // Determine greeting based on current hour
    $hour = (int)date('H');
    if ($hour >= 0 && $hour < 12) {
        $greeting = "Good Morning";
    } elseif ($hour >= 12 && $hour < 17) {
        $greeting = "Good Afternoon";
    } else {
        $greeting = "Good Evening";
    }
}

// Update hits when a book is opened
if (isset($_POST['file_path'])) {
    $file_path = $conn->real_escape_string($_POST['file_path']);
    $sql = "UPDATE books SET hits = hits + 1 WHERE file_path = '$file_path'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}

// Get all book hit counts
if (isset($_GET['get_hits'])) {
    $result = $conn->query("SELECT file_path, hits FROM books");
    $hits = [];
    while ($row = $result->fetch_assoc()) {
        $hits[$row['file_path']] = $row['hits'];
    }
    echo json_encode($hits);
    exit;
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
    $filename = str_replace(' ', '_', $title);
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '', $filename);
    $path = "covers/" . $filename . ".jpg";
    return file_exists($path) ? $path : "covers/default.jpg";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EchoWords - Audiobooks</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
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
            min-height: 100vh;
            overflow-x: hidden;
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
            font-size: 50px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: white;
            cursor: pointer;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .logo:hover {
            color: #ffcc00;
        }
        .greeting-section {
            font-size: 50px;
            font-weight: 900;
            color: white;
            text-align: center;
            margin-top: 160px;
            margin-bottom: 20px;
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
        .search-section {
            text-align: center;
            margin-top: 20px;
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
            flex: 1 0 auto;
            width: 100%;
        }
        .book-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            width: 100%;
            padding: 0 10px;
            box-sizing: border-box;
        }
        .book {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
            width: 200px;
            text-align: center;
            transition: transform 0.3s ease;
            cursor: pointer;
            flex: 0 0 auto;
            box-sizing: border-box;
        }
        .book img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .book .hits {
            font-size: 14px;
            color: #ffcc00;
            margin-top: 5px;
            transition: color 0.3s ease;
        }
        .book:hover .hits {
            color: #ffaa00;
        }
        .book:hover {
            transform: scale(1.1);
        }
        footer {
            text-align: center;
            padding: 20px;
            background: rgba(0, 0, 50, 0.9);
            width: 100%;
            flex-shrink: 0;
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
        .no-results {
            font-size: 20px;
            margin-top: 20px;
            display: none;
        }
        .pdf-viewer {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #1e1e2f;
            z-index: 2000;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: auto;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .pdf-viewer.active {
            opacity: 1;
        }
        .pdf-header {
            width: 100%;
            background: rgba(0, 0, 50, 0.9);
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: -60px;
            left: 0;
            z-index: 2100;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            transition: top 0.3s ease;
        }
        .pdf-header.active {
            top: 0;
        }
        .pdf-header h2 {
            margin: 0;
            font-size: 24px;
            color: #ffcc00;
            margin-left: 10px;
        }
        .close-btn {
            margin-right: 30px;
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .close-btn:hover {
            color: #ff4444;
        }
        .pdf-controls {
            position: fixed;
            bottom: -60px;
            background: rgba(0, 0, 50, 0.9);
            padding: 10px 70px;
            border-radius: 10px;
            display: flex;
            gap: 5px;
            justify-content: center;
            align-items: center;
            box-shadow: 0px -4px 10px rgba(0, 0, 0, 0.3);
            z-index: 2100;
            transition: bottom 0.3s ease;
            left: 50%;
            transform: translateX(-50%);
            min-width: 600px;
            box-sizing: border-box;
        }
        .pdf-controls.active {
            bottom: 20px;
        }
        .pdf-controls button {
            padding: 10px 15px;
            background: #ffcc00;
            color: black;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            font-size: 16px;
            min-width: 95px;
            text-align: center;
        }
        .pdf-controls button:hover {
            background: #ffaa00;
            transform: scale(1.05);
        }
        .pdf-controls #pageInfo {
            flex: 0 0 150px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .pdf-controls #pageInput {
            flex: 0 0 50px;
            padding: 2px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 3px;
            text-align: center;
            margin: 0 2px;
            width: 50px;
            box-sizing: border-box;
        }
        .pdf-controls #pageTotal {
            flex: 0 0 40px;
            text-align: center;
        }
        .pdf-controls #zoomLevel {
            flex: 0 0 120px;
            text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pdf-controls span {
            color: white;
            font-size: 16px;
        }
        @media (max-width: 768px) {
            .pdf-header h2 {
                font-size: 18px;
            }
            .close-btn {
                font-size: 20px;
            }
            .pdf-controls {
                padding: 8px 15px;
                flex-wrap: wrap;
                justify-content: center;
                min-width: 300px;
            }
            .pdf-controls button {
                padding: 8px 12px;
                font-size: 14px;
                min-width: 60px;
            }
            .pdf-controls #pageInfo {
                flex: 0 0 120px;
                font-size: 14px;
            }
            .pdf-controls #pageInput {
                flex: 0 0 30px;
                font-size: 14px;
                padding: 1px;
                width: 30px;
            }
            .pdf-controls #pageTotal {
                flex: 0 0 30px;
                font-size: 14px;
            }
            .pdf-controls #zoomLevel {
                flex: 0 0 100px;
                font-size: 14px;
            }
            .book {
                width: 120px;
            }
            .greeting-section {
                font-size: 18px;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="loggedinhome.php" class="logo">EchoWords</a>
        <nav>
            <ul>
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

    <?php if ($greeting && $username): ?>
        <section class="greeting-section"><?php echo $greeting . ', ' . $username.'!'; ?></section>
    <?php endif; ?>
    <section class="search-section">
        <input type="text" id="searchInput" placeholder="Search for books..." oninput="searchBooks()">
        <button onclick="searchBooks()">Search</button>
    </section>

    <main>
        <section class="continue-reading">
            <h2>Available Books</h2>
            <div class="book-list" id="bookList">
                <?php foreach ($books as $book): ?>
                    <div class="book" data-file-path="<?= htmlspecialchars($book['file_path']) ?>" onclick="openPDF('<?= htmlspecialchars($book['file_path']) ?>', '<?= htmlspecialchars($book['title']) ?>')">
                        <img src="<?= getBookCover($book['title']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                        <p class="book-title"><?= htmlspecialchars($book['title']) ?></p>
                        <p class="hits" data-file-path="<?= htmlspecialchars($book['file_path']) ?>">Hits: <?= htmlspecialchars($book['hits']) ?></p>
                    </div>
                <?php endforeach; ?>    
            </div>
            <div class="no-results" id="noResults">No results found</div>
        </section>

        <section class="recommendations" id="recommendationsSection">
            <h2>This Week's Recommendations</h2>
            <div class="book-list">
                <?php foreach ($recommended_books as $book): ?>
                    <div class="book" data-file-path="<?= htmlspecialchars($book['file_path']) ?>" onclick="openPDF('<?= htmlspecialchars($book['file_path']) ?>', '<?= htmlspecialchars($book['title']) ?>')">
                        <img src="<?= getBookCover($book['title']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                        <p><?= htmlspecialchars($book['title']) ?></p>
                        <p class="hits" data-file-path="<?= htmlspecialchars($book['file_path']) ?>">Hits: <?= htmlspecialchars($book['hits']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <div class="pdf-viewer" id="pdfViewerContainer">
        <div class="pdf-header" id="pdfHeader">
            <h2 id="pdfTitle">Book Title</h2>
            <button class="close-btn" onclick="closePDF()">×</button>
        </div>
        <canvas id="pdfViewer"></canvas>
        <div class="pdf-controls" id="pdfControls">
            <button onclick="previousPage()">Previous</button>
            <div id="pageInfo">
                Page <input type="number" id="pageInput" min="1"> of <span id="pageTotal">1</span>
            </div>
            <button onclick="nextPage()">Next</button>
            <button onclick="zoomOut()">−</button>
            <button onclick="zoomIn()">+</button>
            <button onclick="resetZoom()">Reset</button>
            <span id="zoomLevel">100%</span>
            <button onclick="downloadPDF()">Download</button>
        </div>
    </div>

    <footer>
        <p>© 2025 EchoWords. All rights reserved.</p>
    </footer>

    <script>
        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        let initialScale = 1.0;
        let canvas = document.getElementById('pdfViewer');
        let ctx = canvas.getContext('2d');
        let currentPDFUrl = '';

        function renderPage(num) {
            pageRendering = true;
            console.log('Rendering page:', num);
            pdfDoc.getPage(num).then(page => {
                const pixelRatio = window.devicePixelRatio || 1;
                let viewport = page.getViewport({ scale: 1 });

                const pdfAspectRatio = viewport.width / viewport.height;
                const maxWidth = window.innerWidth * 0.95;
                const maxHeight = (window.innerHeight - 120) * 0.95;
                const screenAspectRatio = maxWidth / maxHeight;

                let baseScale;
                if (pdfAspectRatio > screenAspectRatio) {
                    baseScale = maxWidth / viewport.width;
                } else {
                    baseScale = maxHeight / viewport.height;
                }

                viewport = page.getViewport({ scale: baseScale * scale * pixelRatio });

                canvas.height = viewport.height;
                canvas.width = viewport.width;
                canvas.style.width = `${viewport.width / pixelRatio}px`;
                canvas.style.height = `${viewport.height / pixelRatio}px`;

                canvas.style.position = 'absolute';
                canvas.style.left = `${(window.innerWidth - (viewport.width / pixelRatio)) / 2}px`;
                canvas.style.top = `60px`;

                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                page.render(renderContext).promise.then(() => {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                    const totalPages = pdfDoc.numPages || '?';
                    document.getElementById('pageInput').value = num;
                    document.getElementById('pageTotal').textContent = totalPages;
                    document.getElementById('zoomLevel').textContent = `${Math.round(scale * 100)}%`;
                    localStorage.setItem('lastPage_' + currentPDFUrl, num);
                }).catch(err => {
                    console.error('Error rendering page:', err);
                    document.getElementById('pageInput').value = num;
                    document.getElementById('pageTotal').textContent = '?';
                });
            }).catch(err => {
                console.error('Error loading page:', err);
                document.getElementById('pageInput').value = num;
                document.getElementById('pageTotal').textContent = '?';
            });
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function openPDF(url, title) {
            currentPDFUrl = url;
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'file_path=' + encodeURIComponent(url)
            }).then(response => response.json())
              .then(data => {
                  if (!data.success) console.error('Failed to update hits:', data.error);
                  updateHits();
              }).catch(error => console.error('Error updating hits:', error));

            pdfjsLib.getDocument(url).promise.then(pdf => {
                pdfDoc = pdf;
                const lastPage = localStorage.getItem('lastPage_' + url);
                pageNum = lastPage && !isNaN(lastPage) && parseInt(lastPage) <= pdf.numPages ? parseInt(lastPage) : 1;
                scale = 1.0;
                initialScale = 1.0;
                document.getElementById('pdfTitle').textContent = title;
                const viewer = document.getElementById('pdfViewerContainer');
                viewer.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                setTimeout(() => {
                    viewer.classList.add('active');
                    document.getElementById('pdfHeader').classList.add('active');
                    document.getElementById('pdfControls').classList.add('active');
                }, 10);
                document.querySelector('header').style.display = 'none';
                document.querySelector('main').style.display = 'none';
                document.querySelector('footer').style.display = 'none';
                renderPage(pageNum);
            }).catch(err => {
                console.error('Error loading PDF:', err);
                alert('Failed to load the PDF. Please try again.');
            });
        }

        function closePDF() {
            const viewer = document.getElementById('pdfViewerContainer');
            viewer.classList.remove('active');
            document.getElementById('pdfHeader').classList.remove('active');
            document.getElementById('pdfControls').classList.remove('active');
            document.body.style.overflow = '';
            setTimeout(() => {
                viewer.style.display = 'none';
                document.querySelector('header').style.display = 'flex';
                document.querySelector('main').style.display = 'block';
                document.querySelector('footer').style.display = 'block';
                pdfDoc = null;
                pageNum = 1;
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }, 300);
        }

        function previousPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        function nextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }

        function goToPage() {
            const input = document.getElementById('pageInput');
            const page = parseInt(input.value);
            const totalPages = pdfDoc.numPages || 0;
            console.log('Attempting to go to page:', page, 'Total pages:', totalPages);
            if (page >= 1 && page <= totalPages && !isNaN(page)) {
                pageNum = page;
                queueRenderPage(pageNum);
                console.log('Navigated to page:', pageNum);
            } else {
                alert('Please enter a valid page number between 1 and ' + totalPages);
                console.log('Invalid page number entered');
                input.value = pageNum;
            }
        }

        function zoomIn() {
            if (scale >= 3.0) return;
            scale *= 1.2;
            queueRenderPage(pageNum);
        }

        function zoomOut() {
            if (scale <= 0.4) return;
            scale /= 1.2;
            queueRenderPage(pageNum);
        }

        function resetZoom() {
            scale = initialScale;
            queueRenderPage(pageNum);
        }

        function downloadPDF() {
            window.open(currentPDFUrl, '_blank');
        }

        function searchBooks() {
            const input = document.getElementById("searchInput").value.toLowerCase();
            const books = document.querySelectorAll("#bookList .book");
            const recommendations = document.getElementById("recommendationsSection");
            const noResults = document.getElementById("noResults");

            let matches = 0;
            books.forEach(book => {
                const title = book.querySelector(".book-title").textContent.toLowerCase();
                if (title.includes(input)) {
                    book.style.display = "block";
                    matches++;
                } else {
                    book.style.display = "none";
                }
            });

            recommendations.style.display = input ? "none" : "block";
            noResults.style.display = matches === 0 && input ? "block" : "none";
        }

        window.addEventListener('resize', () => {
            if (pdfDoc && document.getElementById('pdfViewerContainer').style.display === 'flex') {
                queueRenderPage(pageNum);
            }
        });

        document.addEventListener('keydown', (e) => {
            const viewer = document.getElementById('pdfViewerContainer');
            if (viewer.style.display === 'flex') {
                if (e.key === 'ArrowLeft') previousPage();
                if (e.key === 'ArrowRight') nextPage();
                if (e.key === '+') zoomIn();
                if (e.key === '-') zoomOut();
                if (e.key === 'Escape') closePDF();
                if (e.key === 'Enter') {
                    goToPage();
                }
            }
        });

        const pageInput = document.getElementById('pageInput');
        pageInput.addEventListener('input', () => {
            console.log('Manual input:', pageInput.value);
        });

        function updateHits() {
            fetch(window.location.href + '?get_hits=true')
                .then(response => response.json())
                .then(hits => {
                    console.log('Fetched hits:', hits);
                    const books = document.querySelectorAll('.book');
                    books.forEach(book => {
                        const filePath = book.getAttribute('data-file-path');
                        const hitsElement = book.querySelector('.hits');
                        if (hits[filePath] !== undefined) {
                            const currentHits = parseInt(hitsElement.textContent.match(/\d+/) || 0);
                            const newHits = parseInt(hits[filePath]);
                            if (newHits !== currentHits) {
                                hitsElement.textContent = `Hits: ${newHits}`;
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching hits:', error));
        }

        setInterval(updateHits, 5000);
        window.addEventListener('load', updateHits);
    </script>
</body>
</html>
