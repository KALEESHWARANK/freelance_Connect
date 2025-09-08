<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
  header("Location: login.html");
  exit();
}
$name = $_SESSION['name'];
?>
<head>
  <meta charset="UTF-8">
  <title>➕ Post a Job - Freelance Connect</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #065f46, #111827); /* green + black gradient */
      color: #111827;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
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
    .navbar h1 {
      color: #047857; /* dark green */
    }
    .navbar .right a {
      color: #111827;
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
    .container {
      margin-top: 50px;
      background: #ffffff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      max-width: 450px;
      width: 100%;
    }
    h2 {
      text-align: center;
      color: #047857; /* green heading */
      margin-bottom: 20px;
      font-size: 22px;
    }
    label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
      font-size: 14px;
    }
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    textarea {
      resize: vertical;
      min-height: 80px;
      max-height: 150px;
    }
    button {
      margin-top: 20px;
      background: #047857; /* green button */
      color: white;
      padding: 12px 18px;
      border: none;
      font-size: 16px;
      border-radius: 6px;
      cursor: pointer;
      width: 100%;
      transition: 0.3s;
    }
    button:hover {
      background: #065f46;
    }
    @media (max-width: 520px) {
      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }
      .container {
        padding: 15px;
        max-width: 90%;
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
      <a href="../backend/logout.php" class="logout">Logout</a>
    </div>
  </div>

  <div class="container">
    <h2>➕ Post a New Job</h2>
    <form method="post" action="../backend/post_job_action.php">
      <label>Job Title</label>
      <input type="text" name="title" required>

      <label>Description</label>
      <textarea name="description" rows="4" required></textarea>

      <label>Skills Required (comma separated)</label>
      <input type="text" name="skills" required>

      <label>Budget (INR)</label>
      <input type="number" name="budget" required>

      <label>Deadline</label>
      <input type="date" name="deadline" required>

      <button type="submit">🚀 Post Job</button>
    </form>
  </div>
</body>
</html>
