<?php
session_start();

// Allow only logged-in clients
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../frontend/login.html");
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $job_id = intval($_GET['id']);
    $client_id = $_SESSION['user_id'];

    // Check if job belongs to the logged-in client
    $check = $conn->prepare("SELECT id FROM jobs WHERE id = ? AND client_id = ?");
    $check->bind_param("ii", $job_id, $client_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        // Job found and belongs to client — proceed to delete
        $delete = $conn->prepare("DELETE FROM jobs WHERE id = ?");
        $delete->bind_param("i", $job_id);
        if ($delete->execute()) {
            echo "<script>alert('✅ Job deleted successfully!'); window.location.href='../frontend/view_jobs.php';</script>";
        } else {
            echo "<script>alert('❌ Failed to delete job.'); window.location.href='../frontend/view_jobs.php';</script>";
        }
    } else {
        echo "<script>alert('⚠️ Job not found or access denied.'); window.location.href='../frontend/view_jobs.php';</script>";
    }
} else {
    header("Location: ../frontend/view_jobs.php");
}
?>
