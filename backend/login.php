<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Query the user by email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user found
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            // Redirect by role
            if ($user['role'] === 'client') {
                header("Location: ../dashboard/client.php");
                exit();
            } elseif ($user['role'] === 'freelancer') {
                header("Location: ../dashboard/freelancer.php");
                exit();
            } else {
                echo "<script>alert('Invalid user role.'); window.location.href='login.html';</script>";
            }
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href='../frontend/login.html';</script>";
        }
    } else {
        echo "<script>alert('Email not found. Please register.'); window.location.href='../frontend/register.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
