<?php
session_start();
require_once 'database.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false]);
    exit();
}

$stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
$success = $stmt->execute([$_POST['status'], $_POST['app_id']]);
echo json_encode(['success' => $success]);
?>