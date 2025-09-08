<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // secure hash
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    // Check for existing email
    $check = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered. Try logging in.'); window.location.href='../frontend/login.html';</script>";
    } else {
        // Insert new user
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please log in.'); window.location.href='../frontend/login.html';</script>";
        } else {
            echo "<script>alert('Error while registering.'); window.location.href='../frontend/register.html';</script>";
        }
    }

    $stmt->close();
    $conn->close();
}
?>
