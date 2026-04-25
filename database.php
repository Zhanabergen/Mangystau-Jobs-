<?php
$host = 'localhost';
$dbname = 'mangystau_jobs';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Create tables
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    role ENUM('seeker', 'employer') NOT NULL,
    microdistrict VARCHAR(50),
    skills TEXT,
    experience INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    skills_required TEXT,
    salary_min INT,
    salary_max INT,
    microdistrict VARCHAR(50),
    type ENUM('full', 'part', 'freelance') DEFAULT 'full',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    seeker_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    ai_score INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, seeker_id)
);
";
$conn->exec($sql);
?>