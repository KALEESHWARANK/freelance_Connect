<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../frontend/login.html");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $application_id = $_POST['application_id'] ?? '';
    $feedback = trim($_POST['feedback'] ?? '');
    $payment_status = $_POST['payment_status'] ?? '';

    if (empty($application_id) || empty($feedback) || empty($payment_status)) {
        echo "<script>alert('❌ All fields are required.'); window.history.back();</script>";
        exit();
    }

    // Sanitize
    $application_id = (int)$application_id;

    $sql = "UPDATE applications SET feedback = ?, payment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $feedback, $payment_status, $application_id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ Feedback and Payment status updated successfully!'); window.location.href = '../frontend/view_applicants.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to update. Please try again.'); window.history.back();</script>";
    }
}
?>
