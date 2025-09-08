<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'freelancer') {
    header("Location: login.html");
    exit();
}

include '../backend/db.php';

$name = $_SESSION['name'];
$search = $_GET['search'] ?? '';
$min_budget = $_GET['min_budget'] ?? '';
$max_budget = $_GET['max_budget'] ?? '';

$query = "SELECT * FROM jobs WHERE 1=1";
$params = [];
$types = '';

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR skills LIKE ?)";
    $search_term = "%" . $search . "%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}
if (!empty($min_budget)) {
    $query .= " AND budget >= ?";
    $params[] = $min_budget;
    $types .= 'd';
}
if (!empty($max_budget)) {
    $query .= " AND budget <= ?";
    $params[] = $max_budget;
    $types .= 'd';
}

$query .= " ORDER BY id DESC";
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>🔍 Browse Jobs - Freelance Connect</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
  <style>
    * { box-sizing: border-box; }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #1a1a1a, #004d00);
      margin: 0;
      padding: 0;
      color: #e0e0e0;
    }
    .navbar {
      background: #000;
      padding: 20px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }
    .navbar h1 {
      color: #00ff88;
      font-size: 24px;
    }
    .navbar .right {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
    }
    .navbar .right span {
      margin-right: 15px;
      font-size: 14px;
      color: #ccc;
    }
    .navbar .right a {
      color: #00ff88;
      margin-left: 20px;
      text-decoration: none;
      font-weight: 500;
    }
    .navbar .right a.logout {
      background-color: #ff3333;
      color: white;
      padding: 8px 16px;
      border-radius: 6px;
      font-weight: bold;
    }

    .container {
      max-width: 1000px;
      margin: 40px auto;
      padding: 0 20px;
    }
    h2 {
      text-align: center;
      color: #00ff88;
      margin-bottom: 30px;
    }

    form.search-filter {
      background: #111;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 30px;
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      justify-content: center;
    }
    form.search-filter input {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #444;
      font-size: 15px;
      background-color: #222;
      color: #00ff88;
    }
    form.search-filter button {
      padding: 10px 20px;
      background: #00cc66;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
    }

    .job-card {
      background: #fff;
      color: #000;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 12px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.2);
    }
    .job-card h3 {
      color: #006644;
      margin-bottom: 10px;
    }
    .job-card p {
      margin: 5px 0;
      font-size: 14px;
    }
    .badge {
      display: inline-block;
      background: #ccffcc;
      padding: 4px 10px;
      border-radius: 6px;
      font-size: 13px;
      color: #006600;
      margin-right: 5px;
    }
    .apply-btn {
      margin-top: 15px;
      display: inline-block;
      background: #006633;
      color: white;
      padding: 10px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
    }

    @media (max-width: 768px) {
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
        margin: 5px 0 0 0;
      }
      form.search-filter {
        flex-direction: column;
        align-items: stretch;
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
      <a href="../frontend/my_applications.php">My Applications</a>
      <a href="../backend/logout.php" class="logout">Logout</a>
    </div>
  </div>

  <div class="container">
    <h2>🔍 Browse Available Jobs</h2>

    <form class="search-filter" method="get">
      <input type="text" name="search" placeholder="Search title or skills..." value="<?php echo htmlspecialchars($search); ?>">
      <input type="number" name="min_budget" placeholder="Min Budget" value="<?php echo htmlspecialchars($min_budget); ?>">
      <input type="number" name="max_budget" placeholder="Max Budget" value="<?php echo htmlspecialchars($max_budget); ?>">
      <button type="submit">🔎 Filter</button>
    </form>

    <?php if ($result->num_rows === 0): ?>
      <p style="text-align:center; color:#ccc;">No jobs found.</p>
    <?php else: ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="job-card">
          <h3>🧾 <?php echo htmlspecialchars($row['title']); ?></h3>
          <p>✍️ <strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
          <p>🛠️ <strong>Skills:</strong>
            <?php
              $skills = explode(',', $row['skills']);
              foreach ($skills as $skill) {
                echo '<span class="badge">' . trim(htmlspecialchars($skill)) . '</span>';
              }
            ?>
          </p>
          <p>💸 <strong>Budget:</strong> ₹<?php echo number_format($row['budget']); ?></p>
          <p>📅 <strong>Deadline:</strong> <?php echo htmlspecialchars($row['deadline']); ?></p>
          <a href="../frontend/apply_job.php?id=<?php echo $row['id']; ?>" class="apply-btn">🚀 Apply Now</a>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>
  </div>

</body>
</html>
