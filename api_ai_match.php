<?php
session_start();
require_once 'database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seeker') {
    echo json_encode([]);
    exit();
}

$stmt = $conn->prepare("SELECT skills, microdistrict, experience FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$seeker = $stmt->fetch();

$stmt = $conn->prepare("SELECT j.*, u.fullname as company FROM jobs j JOIN users u ON j.employer_id = u.id WHERE j.is_active = 1");
$stmt->execute();
$jobs = $stmt->fetchAll();

$seeker_skills = explode(',', strtolower($seeker['skills'] ?? ''));
$recommendations = [];

foreach($jobs as $job) {
    $job_skills = explode(',', strtolower($job['skills_required'] ?? ''));
    $common = array_intersect($seeker_skills, $job_skills);
    $skill_score = count($job_skills) > 0 ? (count($common) / count($job_skills)) * 60 : 30;
    $location_score = ($seeker['microdistrict'] == $job['microdistrict']) ? 30 : 10;
    $exp_score = min(10, ($seeker['experience'] / 5) * 10);
    $total_score = round($skill_score + $location_score + $exp_score);
    
    $recommendations[] = [
        'id' => $job['id'], 'title' => $job['title'], 'company' => $job['company'],
        'salary_min' => $job['salary_min'], 'salary_max' => $job['salary_max'],
        'district' => $job['microdistrict'], 'score' => $total_score
    ];
}

usort($recommendations, function($a, $b) { return $b['score'] <=> $a['score']; });
echo json_encode(array_slice($recommendations, 0, 4));
?>