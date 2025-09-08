<?php
session_start();
include '../backend/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    http_response_code(403);
    echo "Access Denied.";
    exit();
}

if (!isset($_GET['id'])) {
    echo "Application ID missing.";
    exit();
}

$application_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT resume FROM applications WHERE id = ?");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Application not found.";
    exit();
}

$row = $result->fetch_assoc();
$resume_file = $row['resume'];
$resume_path = '../resumes/' . $resume_file;

if (!file_exists($resume_path)) {
    echo "Resume file not found.";
    exit();
}

header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($resume_path) . '"');
header('Content-Length: ' . filesize($resume_path));
readfile($resume_path);
exit();
?>
