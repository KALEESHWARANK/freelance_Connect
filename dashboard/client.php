<?php
session_start();

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['client', 'freelancer'])) {
    header("Location: ../frontend/login.html");
    exit();
}

$role = $_SESSION['role'];
$name = isset($_SESSION['name']) ? $_SESSION['name'] : 'User';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo ucfirst($role); ?> Dashboard - Freelance Connect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #0f0f0f, #003300);
      color: #e0ffe0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      overflow-x: hidden;
    }

    .background {
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      width: 100%;
      background-image: repeating-linear-gradient(
        135deg,
        rgba(0, 255, 0, 0.1) 0,
        rgba(0, 255, 0, 0.1) 1px,
        transparent 1px,
        transparent 20px
      );
      animation: move 8s linear infinite;
      z-index: -1;
      filter: blur(0.4px);
      opacity: 0.85;
    }

    @keyframes move {
      0% {
        background-position: 0 0;
      }
      100% {
        background-position: 200px 200px;
      }
    }

    .navbar {
      background: #000;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      box-shadow: 0 2px 10px rgba(0, 255, 0, 0.3);
    }

    .navbar h1 {
      color: #00ff88;
      font-size: 24px;
    }

    .navbar .right {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 15px;
      margin-top: 10px;
    }

    .navbar .right a {
      color: #e0ffe0;
      text-decoration: none;
      font-weight: 500;
    }

    .navbar .right a.logout {
      background-color: #00ff88;
      color: #000;
      padding: 8px 16px;
      border-radius: 6px;
    }

    .hero {
      flex: 1;
      padding: 60px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .hero-content {
      max-width: 600px;
      flex: 1 1 400px;
    }

    .hero-content h2 {
      font-size: 40px;
      margin-bottom: 10px;
      color: #00ff88;
    }

    .hero-content p {
      font-size: 18px;
      margin-bottom: 30px;
      color: #c6ffc6;
    }

    .welcome-message {
      font-size: 30px;
      font-weight: 700;
      margin-bottom: 25px;
      color: #e0ffe0;
    }

    .actions a {
      display: block;
      background: #000;
      color: #00ff88;
      margin-bottom: 15px;
      padding: 12px;
      border: 2px solid #00ff88;
      border-radius: 10px;
      font-weight: bold;
      text-align: center;
      text-decoration: none;
      width: 100%;
      max-width: 300px;
      transition: 0.3s;
    }

    .actions a:hover {
      background: #003300;
      box-shadow: 0 0 10px #00ff88;
    }

    .hero img {
      border-radius: 20px;
      box-shadow: 0 0 20px rgba(0, 255, 136, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      image-rendering: smooth;
      width: 100%;
      max-width: 400px;
      margin-top: 40px;
    }

    .hero img:hover {
      transform: scale(1.03);
      box-shadow: 0 0 30px rgba(0, 255, 136, 0.4);
    }

    footer {
      background: #001100;
      text-align: center;
      padding: 20px;
      color: #00ff88;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .hero {
        flex-direction: column;
        align-items: flex-start;
      }

      .hero-content h2 {
        font-size: 30px;
      }

      .welcome-message {
        font-size: 24px;
      }

      .navbar {
        flex-direction: column;
        align-items: flex-start;
      }

      .navbar .right {
        margin-top: 10px;
        flex-direction: column;
        align-items: flex-start;
      }
    }

    @media (max-width: 480px) {
      .hero-content h2 {
        font-size: 24px;
      }

      .hero-content p {
        font-size: 16px;
      }

      .welcome-message {
        font-size: 20px;
      }

      .actions a {
        width: 100%;
      }

      .navbar h1 {
        font-size: 20px;
      }
    }
  </style>
</head>

<body>
  <div class="background"></div>
  <div class="navbar">
    <h1>Freelance Connect</h1>
    <div class="right">
      <a href="#about">About</a>
      <a href="#features">Features</a>
      <a href="#contact">Contact Us</a>
      <a href="../backend/logout.php" class="logout">Logout</a>
    </div>
  </div>

  <div class="hero">
    <div class="hero-content">
      <div class="welcome-message">👋 Welcome, <?php echo htmlspecialchars($name); ?>! 🎉</div>
      <h2><?php echo ucfirst($role); ?> Dashboard</h2>
      <p>
        <?php
        if ($role === 'client') {
          echo "Manage job postings, review applicants, and track hiring process with ease.";
        } else {
          echo "Discover freelance opportunities, track your applications, and connect with potential clients.";
        }
        ?>
      </p>
      <div class="actions">
        <?php if ($role === 'client'): ?>
          <a href="../frontend/post_job.php">➕ Post a Job</a>
          <a href="../frontend/view_jobs.php">📄 My Job Listings</a>
          <a href="../frontend/view_applicants.php">📥 Applications Received</a>
        <?php else: ?>
          <a href="browse_jobs.php">🔍 Find Freelance Jobs</a>
          <a href="my_applications.php">📂 Track My Applications</a>
        <?php endif; ?>
      </div>
    </div>
    <img src="../assets/hero.png" alt="Dashboard Hero" />
  </div>

  <footer>
    © 2025 Freelance Connect. All rights reserved.
  </footer>
</body>
</html>
