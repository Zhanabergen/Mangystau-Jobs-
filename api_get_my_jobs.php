<?php
session_start();
require_once 'database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    echo json_encode([]);
    exit();
}

$stmt = $conn->prepare("SELECT *, (SELECT COUNT(*) FROM applications WHERE job_id = jobs.id) as app_count FROM jobs WHERE employer_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>