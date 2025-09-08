<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../frontend/login.html");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $skills = $_POST['skills'];
    $budget = $_POST['budget'];
    $deadline = $_POST['deadline'];

    $query = "INSERT INTO jobs (client_id, title, description, skills, budget, deadline)
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    $stmt->bind_param("isssds", $client_id, $title, $desc, $skills, $budget, $deadline);

    if ($stmt->execute()) {
        echo "<script>alert('🎉 Job posted successfully!'); window.location.href='../frontend/view_jobs.php';</script>";
    } else {
        echo "<script>alert('❌ Failed: " . $stmt->error . "'); window.history.back();</script>";
    }
}
?>
