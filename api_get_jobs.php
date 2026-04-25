<?php
session_start();
require_once 'database.php';
header('Content-Type: application/json');

$title = $_GET['title'] ?? '';
$district = $_GET['district'] ?? '';
$type = $_GET['type'] ?? '';

$sql = "SELECT j.*, u.fullname as company_name FROM jobs j JOIN users u ON j.employer_id = u.id WHERE j.is_active = 1";
$params = [];

if($title) { $sql .= " AND (j.title LIKE ? OR j.description LIKE ?)"; $params[] = "%$title%"; $params[] = "%$title%"; }
if($district) { $sql .= " AND j.microdistrict = ?"; $params[] = $district; }
if($type) { $sql .= " AND j.type = ?"; $params[] = $type; }

$sql .= " ORDER BY j.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>