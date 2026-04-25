<?php
session_start();
require_once 'database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seeker') {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit();
}

$job_id = $_POST['job_id'] ?? 0;
$seeker_id = $_SESSION['user_id'];

$check = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND seeker_id = ?");
$check->execute([$job_id, $seeker_id]);
if($check->rowCount() > 0) {
    echo json_encode(['success' => false, 'error' => 'Already applied']);
    exit();
}

// AI Score calculation
$stmt = $conn->prepare("SELECT skills FROM users WHERE id = ?");
$stmt->execute([$seeker_id]);
$seeker = $stmt->fetch();

$stmt = $conn->prepare("SELECT skills_required FROM jobs WHERE id = ?");
$stmt->execute([$job_id]);
$job = $stmt->fetch();

$ai_score = 0;
if($seeker && $job) {
    $seeker_skills = explode(',', strtolower($seeker['skills'] ?? ''));
    $job_skills = explode(',', strtolower($job['skills_required'] ?? ''));
    $common = array_intersect($seeker_skills, $job_skills);
    $ai_score = count($job_skills) > 0 ? round((count($common) / count($job_skills)) * 100) : 50;
}

$stmt = $conn->prepare("INSERT INTO applications (job_id, seeker_id, ai_score) VALUES (?, ?, ?)");
$success = $stmt->execute([$job_id, $seeker_id, $ai_score]);

echo json_encode(['success' => $success, 'ai_score' => $ai_score]);
?>