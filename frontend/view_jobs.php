<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: login.html");
    exit();
}
include '../backend/db.php';

$client_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch applications for jobs posted by this client
$query = "
    SELECT a.id AS app_id, a.message, a.skills AS app_skills, 
           u.name AS freelancer_name, u.email, 
           j.title AS job_title
    FROM applications a
    JOIN users u ON a.freelancer_id = u.id
    JOIN jobs j ON a.job_id = j.id
    WHERE j.client_id = ?
    ORDER BY a.id DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📥 Applications Received - Freelance Connect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #000000, #064e3b);
      min-height: 100vh;
      margin: 0;
      padding: 0;
      color: #e5e5e5;
    }

    .navbar {
      background: #000000;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 10px rgba(0,0,0,0.3);
      flex-wrap: wrap;
    }

    .navbar h1 {
      color: #10b981;
      margin: 0;
    }

    .navbar .right {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
    }

    .navbar .right span {
      margin-right: 20px;
      font-weight: bold;
      color: #f0fdf4;
    }

    .navbar .right a {
      color: #f0fdf4;
      margin-left: 15px;
      text-decoration: none;
      font-weight: 600;
      padding: 6px 12px;
      border-radius: 6px;
      transition: background 0.3s;
    }

    .navbar .right a:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .navbar .right a.logout {
      background-color: #dc2626;
      color: white;
    }

    .container {
      padding: 40px 20px;
      max-width: 1000px;
      margin: auto;
    }

    h2 {
      text-align: center;
      color: #10b981;
      margin-bottom: 30px;
      font-size: 24px;
    }

    .card {
      background: #111827;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }

    .card h3 {
      margin-bottom: 10px;
      color: #22c55e;
    }

    .card p {
      margin: 6px 0;
      font-size: 14px;
      color: #ccc;
    }

    .badge {
      display: inline-block;
      background: #064e3b;
      color: #a7f3d0;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 13px;
      margin-right: 6px;
      margin-top: 4px;
    }

    @media (max-width: 600px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }
      .navbar .right {
        margin-top: 10px;
        flex-direction: column;
        align-items: flex-start;
      }
      .navbar .right a {
        margin: 5px 0;
      }
    }
  </style>
</head>
<body>

  <div class="navbar">
    <h1>Freelance Connect</h1>
    <div class="right">
      <span>👋 Welcome, <?php echo htmlspecialchars($name); ?></span>
      <a href="../dashboard/client.php">Dashboard</a>
      <a href="../frontend/view_jobs.php">My Jobs</a>
      <a href="../backend/logout.php" class="logout">Logout</a>
    </div>
  </div>

  <div class="container">
    <h2>📥 Applications Received</h2>
    <?php if ($result->num_rows === 0): ?>
      <p style="text-align:center; color: #999;">No applications received yet.</p>
    <?php else: ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
          <h3>📝 <?php echo htmlspecialchars($row['job_title']); ?></h3>
          <p>👤 <strong>Freelancer:</strong> <?php echo htmlspecialchars($row['freelancer_name']); ?> (<?php echo htmlspecialchars($row['email']); ?>)</p>
          <p>📄 <strong>Message:</strong> <?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
          <p>🛠️ <strong>Skills:</strong>
            <?php
              $skills = explode(',', $row['app_skills']);
              foreach ($skills as $skill) {
                echo '<span class="badge">' . trim(htmlspecialchars($skill)) . '</span>';
              }
            ?>
          </p>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

</body>
</html>
