<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: ../frontend/login.html");
    exit();
}

include '../backend/db.php';

$client_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

$sql = "
    SELECT a.*, j.title AS job_title, f.name AS freelancer_name, f.email AS freelancer_email, 
           a.submitted_link, a.feedback, a.payment_status
    FROM applications a
    JOIN jobs j ON a.job_id = j.id
    JOIN users f ON a.freelancer_id = f.id
    WHERE j.client_id = ?
    ORDER BY a.id DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>📥 Applications Received - Freelance Connect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #000000, #064e3b);
      margin: 0;
      padding: 0;
    }
    .navbar {
      background: #000000;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    .navbar h1 {
      color: #10b981;
      margin: 0;
    }
    .navbar .right a {
      margin-left: 20px;
      text-decoration: none;
      font-weight: bold;
      color: #f0fdf4;
    }
    .navbar .right a.logout {
      background-color: #dc2626;
      color: white;
      padding: 8px 16px;
      border-radius: 6px;
    }
    .container {
      padding: 20px;
      max-width: 1000px;
      margin: auto;
    }
    h2 {
      color: #10b981;
      text-align: center;
      margin-bottom: 30px;
    }
    .applicant-card {
      background: #111827;
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.3);
      color: white;
    }
    .applicant-card h3 {
      margin: 0 0 10px;
      color: #22c55e;
    }
    .applicant-card p {
      margin: 8px 0;
    }
    .badge {
      display: inline-block;
      background: #064e3b;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 13px;
      color: #a7f3d0;
      margin-right: 5px;
    }
    .download-btn {
      background: #10b981;
      color: black;
      padding: 6px 10px;
      border-radius: 6px;
      text-decoration: none;
    }
    .btn-accept, .btn-reject {
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      color: white;
      cursor: pointer;
      font-weight: bold;
    }
    .btn-accept {
      background-color: #16a34a;
      margin-right: 10px;
    }
    .btn-reject {
      background-color: #dc2626;
    }
    .status {
      font-weight: bold;
      margin-top: 10px;
    }
    .feedback-form {
      margin-top: 10px;
    }
    .feedback-form textarea {
      width: 100%;
      padding: 8px;
      margin-top: 6px;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    .feedback-form select {
      margin-top: 10px;
      padding: 6px;
      border-radius: 6px;
    }
    .feedback-form button {
      margin-top: 10px;
      padding: 8px 16px;
      background: #10b981;
      color: black;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }
    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }
      .navbar .right {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 10px;
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

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="applicant-card">
          <h3><?php echo htmlspecialchars($row['freelancer_name']); ?> applied for "<?php echo htmlspecialchars($row['job_title']); ?>"</h3>
          <p>📧 Email: <?php echo htmlspecialchars($row['freelancer_email']); ?></p>
          <p>📜 Message: <?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
          <p>🛠️ Skills:
            <?php
              $skills = explode(',', $row['skills']);
              foreach ($skills as $skill) {
                  echo '<span class="badge">' . trim(htmlspecialchars($skill)) . '</span>';
              }
            ?>
          </p>

          <?php if (!empty($row['resume'])): ?>
            <p>📄 Resume: 
              <a class="download-btn" href="../resumes/<?php echo htmlspecialchars($row['resume']); ?>" target="_blank">Download</a>
            </p>
          <?php endif; ?>

          <?php if (!empty($row['submitted_link'])): ?>
            <p>🔗 Submitted Work: 
              <a class="download-btn" href="<?php echo htmlspecialchars($row['submitted_link']); ?>" target="_blank">View Work</a>
            </p>
          <?php endif; ?>

          <p class="status">📌 Status: <strong><?php echo htmlspecialchars($row['status'] ?? 'Pending'); ?></strong></p>

          <?php if ($row['status'] !== 'Accepted' && $row['status'] !== 'Rejected'): ?>
            <form method="POST" action="../backend/update_application_status.php" style="margin-top:10px;">
              <input type="hidden" name="id" value="<?= $row['id'] ?>">
              <input type="hidden" name="email" value="<?= htmlspecialchars($row['freelancer_email']) ?>">
              <button class="btn-accept" name="action" value="accept">✅ Accept</button>
              <button class="btn-reject" name="action" value="reject">❌ Reject</button>
            </form>
          <?php endif; ?>

          <?php if (!empty($row['submitted_link'])): ?>
            <?php if (!empty($row['feedback']) && !empty($row['payment_status'])): ?>
              <p>🚸️ <strong>Feedback:</strong> <?php echo nl2br(htmlspecialchars($row['feedback'])); ?></p>
              <p>💰 <strong>Payment Status:</strong> <?php echo htmlspecialchars($row['payment_status']); ?></p>
            <?php else: ?>
              <form class="feedback-form" action="../backend/submit_feedback.php" method="POST">
                <input type="hidden" name="application_id" value="<?= $row['id'] ?>">
                <label for="feedback">🚸️ Provide Feedback:</label>
                <textarea name="feedback" required><?php echo htmlspecialchars($row['feedback'] ?? ''); ?></textarea>
                <label for="payment_status">💰 Payment Status:</label>
                <select name="payment_status" required>
                  <option value="Pending" <?= ($row['payment_status'] === 'Pending' ? 'selected' : '') ?>>Pending</option>
                  <option value="Paid" <?= ($row['payment_status'] === 'Paid' ? 'selected' : '') ?>>Paid</option>
                </select>
                <button type="submit">Submit Feedback & Payment</button>
              </form>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="color: white; text-align:center;">No applications received yet.</p>
    <?php endif; ?>
  </div>

</body>
</html>
