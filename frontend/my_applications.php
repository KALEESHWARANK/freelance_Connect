<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: ../frontend/login.html");
    exit();
}

include '../backend/db.php';

$freelancer_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

$query = "
    SELECT a.*, j.title, j.description, j.budget, j.deadline
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    WHERE a.freelancer_id = ?
    ORDER BY a.id DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $freelancer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>📂 My Applications - Freelance Connect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #0f172a, #065f46);
      margin: 0;
      padding: 0;
      color: #fff;
    }

    .navbar {
      background: #111827;
      padding: 20px 40px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
    }

    .navbar h1 {
      color: #22c55e;
      font-size: 24px;
    }

    .navbar .right {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
    }

    .navbar .right span {
      color: #e5e7eb;
      margin-right: 20px;
    }

    .navbar .right a {
      color: #22c55e;
      margin-left: 15px;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .navbar .right a.logout {
      background-color: #dc2626;
      color: white;
      padding: 6px 14px;
      border-radius: 6px;
    }

    .navbar .right a:hover {
      color: #16a34a;
    }

    .container {
      padding: 40px 20px;
      max-width: 1000px;
      margin: auto;
    }

    h2 {
      color: #d1fae5;
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
    }

    .card {
      background: #1f2937;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }

    .card h3 {
      color: #22c55e;
      margin-bottom: 10px;
    }

    .card p {
      margin: 5px 0;
      color: #e5e7eb;
    }

    .badge {
      background: #047857;
      padding: 5px 10px;
      border-radius: 6px;
      font-size: 13px;
      color: white;
      margin-right: 6px;
      display: inline-block;
    }

    .btn {
      background: #22c55e;
      color: #000;
      padding: 8px 14px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
      display: inline-block;
      margin-top: 10px;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #16a34a;
    }

    .feedback-box {
      background: #374151;
      padding: 10px;
      border-radius: 6px;
      font-size: 14px;
      color: #d1d5db;
      margin-top: 10px;
    }

    .status-paid {
      color: #22c55e;
      font-weight: bold;
    }

    .status-pending {
      color: #f87171;
      font-weight: bold;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar .right {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        margin-top: 10px;
      }

      .container {
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
    <a href="../frontend/browse_jobs.php">Browse Jobs</a>
    <a href="../frontend/my_applications.php">My Applications</a>
    <a href="../backend/logout.php" class="logout">Logout</a>
  </div>
</div>

<div class="container">
  <h2>📂 My Job Applications</h2>

  <?php while ($row = $result->fetch_assoc()): ?>
    <div class="card">
      <h3>🧾 <?php echo htmlspecialchars($row['title']); ?></h3>
      <p>✍️ <strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
      <p>💸 <strong>Budget:</strong> ₹<?php echo number_format($row['budget']); ?></p>
      <p>📅 <strong>Deadline:</strong> <?php echo htmlspecialchars($row['deadline']); ?></p>
      <p>💬 <strong>Your Message:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
      <p>🛠️ <strong>Your Skills:</strong>
        <?php
          $skills = explode(',', $row['skills']);
          foreach ($skills as $skill) {
            echo '<span class="badge">' . trim(htmlspecialchars($skill)) . '</span>';
          }
        ?>
      </p>
      <p>📌 <strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>

      <?php if (!empty($row['resume'])): ?>
        <a class="btn" href="../resumes/<?php echo htmlspecialchars($row['resume']); ?>" target="_blank">📄 Download Resume</a>
      <?php endif; ?>

      <?php if ($row['status'] === 'Accepted'): ?>
        <?php if (empty($row['submitted_link'])): ?>
          <a class="btn" href="../frontend/freelancer/upload_work.php?id=<?php echo $row['id']; ?>">📤 Upload Work</a>
        <?php else: ?>
          <p>✅ You submitted your work: <a href="<?php echo htmlspecialchars($row['submitted_link']); ?>" target="_blank">View Submission</a></p>
          <a class="btn" href="../frontend/freelancer/upload_work.php?id=<?php echo $row['id']; ?>">✏️ Edit Submission</a>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (!empty($row['feedback'])): ?>
        <div class="feedback-box">🗨️ <strong>Client Feedback:</strong> <?php echo htmlspecialchars($row['feedback']); ?></div>
      <?php endif; ?>

      <?php if (!empty($row['payment_status'])): ?>
        <p>💰 <strong>Payment:</strong>
          <?php if ($row['payment_status'] === 'Paid'): ?>
            <span class="status-paid">Paid</span>
          <?php else: ?>
            <span class="status-pending">Pending</span>
          <?php endif; ?>
        </p>
      <?php endif; ?>
    </div>
  <?php endwhile; ?>
</div>

</body>
</html>
