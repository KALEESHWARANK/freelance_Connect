<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: login.html");
    exit();
}

include '../backend/db.php';

$freelancer_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

if (!isset($_GET['id'])) {
    echo "❌ Invalid request.";
    exit();
}

$job_id = intval($_GET['id']);

// Fetch job details
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();

if (!$job) {
    echo "❌ Job not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];
    $skills = $_POST['skills'];

    // Handle resume upload
    $resume_name = "";
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            echo "<script>alert('❌ Only PDF files are allowed.');</script>";
            exit();
        } else {
            $resume_name = uniqid("resume_") . ".pdf";
            move_uploaded_file($_FILES['resume']['tmp_name'], "../resumes/" . $resume_name);
        }
    }

    // Prevent duplicate applications
    $check = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND freelancer_id = ?");
    $check->bind_param("ii", $job_id, $freelancer_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('❗ You have already applied for this job.');</script>";
    } else {
        $insert = $conn->prepare("INSERT INTO applications (job_id, freelancer_id, message, skills, resume) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("iisss", $job_id, $freelancer_id, $message, $skills, $resume_name);

        if ($insert->execute()) {
            echo "<script>alert('🎉 Application submitted successfully!'); window.location.href='my_applications.php';</script>";
        } else {
            echo "<script>alert('❌ Failed to submit application');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>📩 Apply Job - Freelance Connect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #000000, #006400); /* Black to Dark Green */
      margin: 0;
      padding: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
    }

    .navbar {
      background: #000;
      padding: 20px 40px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }

    .navbar h1 {
      color: #00FF00;
      margin: 0;
    }

    .navbar .right {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
    }

    .navbar .right span {
      color: #90ee90;
      margin-right: 20px;
      font-weight: 500;
    }

    .navbar .right a {
      color: #00FF00;
      margin-left: 15px;
      text-decoration: none;
      font-weight: 500;
      padding: 6px 10px;
      transition: background 0.3s ease;
    }

    .navbar .right a:hover {
      background-color: #004d00;
      border-radius: 5px;
    }

    .navbar .right a.logout {
      background-color: #228B22;
      color: white;
      padding: 8px 16px;
      border-radius: 6px;
    }

    .container {
      background: #f0fff0;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
      padding: 30px 25px;
      margin: 40px auto;
      width: 100%;
      max-width: 500px;
    }

    h2 {
      text-align: center;
      color: #006400;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
      color: #004d00;
    }

    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      font-size: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }

    button {
      margin-top: 20px;
      background: #006400;
      color: white;
      padding: 12px;
      font-size: 15px;
      border: none;
      border-radius: 8px;
      width: 100%;
      cursor: pointer;
    }

    button:hover {
      background: #004d00;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar .right {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar .right a {
        margin: 5px 0;
      }

      .container {
        width: 90%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <h1>Freelance Connect</h1>
    <div class="right">
      <span>👋 Welcome, <?php echo htmlspecialchars($name); ?></span>
      <a href="../dashboard/freelancer.php">Dashboard</a>
      <a href="browse_jobs.php">Browse Jobs</a>
      <a href="my_applications.php">My Applications</a>
      <a href="../backend/logout.php" class="logout">Logout</a>
    </div>
  </div>

  <div class="container">
    <h2>📩 Apply for: <?php echo htmlspecialchars($job['title']); ?></h2>

    <form method="post" enctype="multipart/form-data">
      <label>Your Message</label>
      <textarea name="message" rows="5" required></textarea>

      <label>Your Relevant Skills (comma-separated)</label>
      <input type="text" name="skills" required>

      <label>Upload Resume (PDF only)</label>
      <input type="file" name="resume" accept=".pdf" required>

      <button type="submit">🚀 Submit Application</button>
    </form>
  </div>

</body>
</html>
