<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || !isset($_SESSION['supervisor_name'])) {
    header("Location: index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$supervisor_id = $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];

// Check if supervisor is a committee member (based on Committee_ID)
$sql = "SELECT Committee_ID FROM supervisor WHERE Supervisor_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$_SESSION['is_committee'] = !empty($row['Committee_ID']) ? 1 : 0;

// Fetch logbook entries of supervised students
$sql = "SELECT s.Student_ID, s.student_name, l.logbook_id, l.logbook_date, l.supervisor_viewed
        FROM student s
        JOIN logbook l ON s.Student_ID = l.student_id
        WHERE s.Supervisor_ID = ?
        ORDER BY l.logbook_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Review Logbooks</title>
  <link rel="stylesheet" href="supervisor_css/supLogs.css">
  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("collapsed");
    }
    function goToLog(logbookId) {
      window.location.href = 'supViewLog.php?logbook_id=' + logbookId;
    }
  </script>
</head>
<body>
  <header>
    <h1>Welcome, <?php echo htmlspecialchars($supervisor_name); ?>!</h1>
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
          <li><a href="supEvaluationPage.php">Evaluate Students</a></li>

          <?php if (!empty($_SESSION['is_committee'])): ?>
            <li><a href="comAnnouncements.php">Announcements</a></li>
            <li><a href="comApplication.php">Student Applications</a></li>
            <li><a href="comSupervisorList.php">List of Supervisors</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <div class="logbook-section">
        <h2>Student Logbook Entries</h2>
        <?php if (count($logs) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Student ID</th>
              <th>Student Name</th>
              <th>Date</th>
              <th>Viewed</th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; foreach ($logs as $log): ?>
            <tr onclick="goToLog('<?php echo $log['logbook_id']; ?>')">
              <td><?php echo $i++; ?></td>
              <td><?php echo htmlspecialchars($log['Student_ID']); ?></td>
              <td><?php echo htmlspecialchars($log['student_name']); ?></td>
              <td><?php echo htmlspecialchars($log['logbook_date']); ?></td>
              <td><?php echo $log['supervisor_viewed'] ? '✅Yes' : '❌No'; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
          <p>No logbook entries found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>