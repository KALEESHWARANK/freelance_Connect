<?php
session_start();

// ✅ Login check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../login.html");
    exit();
}

include '../../backend/db.php';

$freelancer_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Check application ID
if (!isset($_GET['id'])) {
    echo "❌ Invalid Request.";
    exit();
}

$application_id = (int)$_GET['id'];

// Fetch application
$sql = "SELECT * FROM applications WHERE id = ? AND freelancer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $application_id, $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();
$app = $result->fetch_assoc();

if (!$app) {
    echo "❌ Application not found.";
    exit();
}

// ✅ Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link = trim($_POST['work_link']);

    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        echo "<script>alert('❌ Invalid URL. Please enter a valid link.');</script>";
    } else {
        $update = $conn->prepare("UPDATE applications SET submitted_link = ? WHERE id = ?");
        $update->bind_param("si", $link, $application_id);
        if ($update->execute()) {
            echo "<script>alert('✅ Work submitted successfully!'); window.location.href = '../my_applications.php';</script>";
        } else {
            echo "<script>alert('❌ Failed to submit work.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🚀 Submit Work - Freelance Connect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #fb923c, #f43f5e);
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 500px;
      background: white;
      margin: 80px auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #f43f5e;
    }
    label {
      display: block;
      margin-top: 20px;
      font-weight: bold;
    }
    input[type="url"] {
      width: 100%;
      padding: 10px;
      font-size: 15px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      margin-top: 20px;
      background: #10b981;
      color: white;
      border: none;
      padding: 12px;
      width: 100%;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background: #059669;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>🚀 Submit Your Work</h2>
    <form method="POST">
      <label for="work_link">Project/Drive/GitHub Link</label>
      <input type="url" id="work_link" name="work_link" required placeholder="https://yourworklink.com" />
      <button type="submit">✅ Submit</button>
    </form>
  </div>
</body>
</html>
