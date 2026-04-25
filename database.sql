CREATE DATABASE IF NOT EXISTS mangystau_jobs;
USE mangystau_jobs;

CREATE TABLE users (
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

CREATE TABLE jobs (
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

CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    job_id INT NOT NULL,
    seeker_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    ai_score INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Demo data
INSERT INTO users (email, password, fullname, phone, role, microdistrict, skills, experience) VALUES
('employer@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Coffee Like Cafe', '+77071234567', 'employer', '3', '', 0),
('seeker@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Aigerim Zhan', '+77077654321', 'seeker', '3', 'bartender, cashier, english, customer service', 2);

INSERT INTO jobs (employer_id, title, description, skills_required, salary_min, salary_max, microdistrict, type) VALUES
(1, 'Barista Needed', 'Looking for experienced barista for busy coffee shop. Must be friendly and fast learner.', 'bartender, coffee machine, cashier, english', 150000, 200000, '3', 'full');