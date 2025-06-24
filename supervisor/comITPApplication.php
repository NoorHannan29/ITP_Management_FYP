<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || empty($_SESSION['is_committee'])) {
    header("Location: index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$supervisor_name = $_SESSION['supervisor_name'];

// Fetch ITP applications with status not yet approved
$sql = "SELECT s.Student_ID, s.student_name, i.Application_Date, i.Status, i.ITP_Application_ID
        FROM itp_applications i
        JOIN student s ON s.Student_ID = i.Student_ID
        WHERE i.Status = 'Pending'";

$result = $conn->query($sql);

$pending_itp_apps = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pending_itp_apps[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Committee - ITP Applications</title>
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
          <li><a href="supEvaluationPage.php">Evaluate Students</a></li>

          <?php if (!empty($_SESSION['is_committee'])): ?>
            <li><a href="comAnnouncements.php">Announcements</a></li>
            <li><a href="comITPApplication.php">ITP Applications</a></li>
            <li><a href="comApplication.php">Placement Applications</a></li>
            <li><a href="comSupervisorList.php">List of Supervisors</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <div class="grid-section">
        <div class="alert-box">
          <h3>Pending ITP Applications</h3>
          <?php if (count($pending_itp_apps) > 0): ?>
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
                <?php foreach ($pending_itp_apps as $app): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($app['Student_ID']); ?></td>
                    <td><?php echo htmlspecialchars($app['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($app['Application_Date']); ?></td>
                    <td><?php echo htmlspecialchars($app['Status']); ?></td>
                    <td>
                      <a href="comITPApplicationApproval.php?itp_id=<?php echo urlencode($app['ITP_Application_ID']); ?>" 
                         style="padding: 6px 12px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                        Review
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No pending ITP applications found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
