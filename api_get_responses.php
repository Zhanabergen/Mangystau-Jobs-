<?php
session_start();
require_once 'database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'employer') {
    echo json_encode([]);
    exit();
}

$stmt = $conn->prepare("
    SELECT a.*, j.title as job_title, u.fullname, u.phone, u.skills, u.experience 
    FROM applications a 
    JOIN jobs j ON a.job_id = j.id 
    JOIN users u ON a.seeker_id = u.id 
    WHERE j.employer_id = ? 
    ORDER BY a.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>