<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || empty($_SESSION['is_committee'])) {
    header("Location: index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$supervisor_name = $_SESSION['supervisor_name'];

// Fetch applications with status not yet approved
$sql = "SELECT s.Student_ID, s.student_name, a.Submitted_At, a.Application_Status, a.Application_ID
        FROM applications a
        JOIN student s ON s.Student_ID = a.Student_ID
        WHERE a.Application_Status = 'Pending'";

$result = $conn->query($sql);

$pending_apps = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pending_apps[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Committee - Pending Applications</title>
  <link rel="stylesheet" href="supervisor_css/supITPStudents.css">
  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
    }
  </script>
</head>
<body>
  <header>
    <h1>Welcome, <?php echo htmlspecialchars($supervisor_name); ?> (Committee)</h1>
    <button onclick="window.location.href='../php_files/logout.php'" style="padding: 10px 20px; border-radius: 10px; background-color: white; border: none; font-weight: bold; cursor:pointer;">Log Out</button>
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
      <div class="grid-section">
        <div class="alert-box">
          <h3>Pending Student Applications</h3>
          <?php if (count($pending_apps) > 0): ?>
            <table>
              <thead>
                <tr>
                  <th>Student ID</th>
                  <th>Name</th>
                  <th>Application Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pending_apps as $app): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($app['Student_ID']); ?></td>
                    <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($app['Submitted_At']); ?></td>
                    <td><?php echo htmlspecialchars($app['Application_Status']); ?></td>
                    <td>
                      <a href="comApplicationApproval.php?application_id=<?php echo urlencode($app['Application_ID']); ?>" 
                         style="padding: 6px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                        Review
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No pending applications found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
