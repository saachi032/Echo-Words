CREATE TABLE bookreq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author VARCHAR(255),
    book_name VARCHAR(255),
    cover_path VARCHAR(255),
    email VARCHAR(255) NULL
);

CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author VARCHAR(255) NULL,
    file_path VARCHAR(255),
    hits INT,
    title VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(255) UNIQUE,
    fname VARCHAR(100),
    lname VARCHAR(100),
    password VARCHAR(255),
    phno VARCHAR(20)
);

INSERT INTO books (title, author, file_path, hits, uploaded_at) VALUES
('Harry Potter and the Philosophers Stone', 'J.K. Rowling', 'book/HarryPotter1.pdf', 0, '2025-04-14 00:34:06'),
('The Alchemist', 'Paulo Coelho', 'book/TheAlchemist.pdf', 0, '2025-04-14 00:34:06'),
('1984', 'George Orwell', 'book/1984.pdf', 0, '2025-04-14 00:34:06'),
('Animal Farm', 'George Orwell', 'book/animalfarm.pdf', 0, '2025-04-14 00:34:06'),
('Verity', 'Colleen Hoover', 'book/verity.pdf', 0, '2025-04-14 00:34:06'),
('The Silent Patient', 'Alex Michaelides', 'book/silentpatient.pdf', 0, '2025-04-14 00:34:06'),
('Ikigai', 'Héctor García', 'book/ikigai.pdf', 0, '2025-04-14 00:34:06');

