<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || !isset($_SESSION['supervisor_name'])) {
    header("Location: index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$supervisor_name = $_SESSION['supervisor_name'];

// Fetch announcements (you can filter by role/audience later)
$sql = "SELECT Announcement_ID, Title, Timestamp, Content FROM announcements ORDER BY Timestamp DESC";
$result = $conn->query($sql);

$announcements = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $announcements[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Committee Announcements</title>
  <link rel="stylesheet" href="supervisor_css/supITPStudents.css">
  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
    }
  </script>
</head>
<body>
  <header>
    <h1>Announcement Page</h1>
    <button onclick="window.location.href='php_files/logout.php'" style="padding: 10px 20px; border-radius: 10px; background-color: white; border: none; font-weight: bold; cursor:pointer;">Log Out</button>
  </header>

  <div class="main-layout">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
      <div class="sidebar-content">
        <ul class="sidebar-nav">
          <li><a href="supITPStudents.php">Dashboard</a></li>
          <li><a href="supLogs.php">Review Logbooks</a></li>
          <li><a href="supEvaluate.html">Evaluate Students</a></li>

          <?php if (!empty($_SESSION['is_committee'])): ?>
            <li><a href="comAnnouncements.php">Announcements</a></li>
            <li><a href="comApplication.php">Student Applictions</a></li>
            <li><a href="comSupervisorList.php">List of Supervisors</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>


    <!-- Main Content -->
    <div class="main-content">
      <div class="alert-box">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <h3>List of Announcements</h3>
          <button onclick="window.location.href='draftAnnouncement.php'" style="padding: 10px 20px; border-radius: 10px; background-color: white; border: none; font-weight: bold; cursor:pointer;">+ Draft New Announcement</button>
        </div>

        <?php if (count($announcements) > 0): ?>
          <table>
          <thead>
            <tr>
              <th style="width: 10%;">ID</th>
              <th style="width: 25%;">Title</th>
              <th style="width: 20%;">Date</th>
              <th style="width: 45%;">Preview</th>
            </tr>
          </thead>
            <tbody>
              <?php foreach ($announcements as $row): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['Announcement_ID']); ?></td>
                  <td><?php echo htmlspecialchars($row['Title']); ?></td>
                  <td><?php echo htmlspecialchars($row['Timestamp']); ?></td>
                  <td><?php echo htmlspecialchars(mb_strimwidth($row['Content'], 0, 80, '...')); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No announcements found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
