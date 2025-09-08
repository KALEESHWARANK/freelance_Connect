<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.html");
    exit();
}

include '../backend/db.php';

$job_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$client_id = $_SESSION['user_id'];

// Fetch job details
$query = $conn->prepare("SELECT * FROM jobs WHERE id = ? AND client_id = ?");
$query->bind_param("ii", $job_id, $client_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('⚠️ Job not found or access denied.'); window.location.href='view_jobs.php';</script>";
    exit();
}

$job = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $skills = $_POST['skills'];
    $budget = $_POST['budget'];
    $deadline = $_POST['deadline'];

    $update = $conn->prepare("UPDATE jobs SET title=?, description=?, skills=?, budget=?, deadline=? WHERE id=? AND client_id=?");
    $update->bind_param("sssdsii", $title, $desc, $skills, $budget, $deadline, $job_id, $client_id);

    if ($update->execute()) {
        echo "<script>alert('✅ Job updated successfully!'); window.location.href='view_jobs.php';</script>";
    } else {
        echo "<script>alert('❌ Failed to update job.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>✏️ Edit Job - Freelance Connect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #fb923c, #f43f5e);
      padding: 0;
      min-height: 100vh;
    }

    .navbar {
      background: white;
      width: 100%;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .navbar h1 { color: #f97316; }
    .navbar .right a {
      color: #1f2937;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
    }
    .navbar .right a.logout {
      background-color: #ef4444;
      color: white;
      padding: 8px 16px;
      border-radius: 6px;
    }

    .form-container {
      max-width: 600px;
      margin: 50px auto;
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #f43f5e;
      margin-bottom: 25px;
    }
    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      margin-top: 5px;
      border-radius: 8px;
    }
    button {
      background: #f43f5e;
      color: white;
      padding: 12px 20px;
      border: none;
      margin-top: 25px;
      width: 100%;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
    }
    button:hover {
      background: #fb923c;
    }

    @media (max-width: 600px) {
      .navbar { flex-direction: column; align-items: flex-start; }
      .navbar .right { margin-top: 10px; }
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h1>Freelance Connect</h1>
    <div class="right">
      <span>👋 Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
      <a href="../dashboard/client.php">Dashboard</a>
      <a href="../frontend/view_jobs.php">My Jobs</a>
      <a href="../backend/logout.php" class="logout">Logout</a>
    </div>
  </div>

  <div class="form-container">
    <h2>✏️ Edit Job</h2>
    <form method="POST">
      <label>Job Title</label>
      <input type="text" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required>

      <label>Description</label>
      <textarea name="description" rows="5" required><?php echo htmlspecialchars($job['description']); ?></textarea>

      <label>Skills Required (comma separated)</label>
      <input type="text" name="skills" value="<?php echo htmlspecialchars($job['skills']); ?>" required>

      <label>Budget (INR)</label>
      <input type="number" name="budget" value="<?php echo $job['budget']; ?>" required>

      <label>Deadline</label>
      <input type="date" name="deadline" value="<?php echo $job['deadline']; ?>" required>

      <button type="submit">💾 Update Job</button>
    </form>
  </div>
</body>
</html>
