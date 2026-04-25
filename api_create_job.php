<?php
session_start();
require_once 'database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    echo json_encode(['success' => false]);
    exit();
}

$stmt = $conn->prepare("INSERT INTO jobs (employer_id, title, description, skills_required, salary_min, salary_max, microdistrict, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$success = $stmt->execute([$_SESSION['user_id'], $_POST['title'], $_POST['description'], $_POST['skills'], $_POST['salary_min'], $_POST['salary_max'], $_POST['district'], $_POST['type']]);

echo json_encode(['success' => $success]);
?>