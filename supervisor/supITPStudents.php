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

// Fetch supervised students
$students = [];
$has_alert = false;

$sql = "SELECT s.Student_ID, s.student_name, s.student_email, s.student_phone, a.Application_Status AS Status
        FROM applications a
        JOIN student s ON s.Student_ID = a.Student_ID
        WHERE s.Supervisor_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $row['Alert'] = ($row['Status'] === 'Pending');
    if ($row['Alert']) $has_alert = true;
    $students[] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Supervisor Dashboard</title>
  <link rel="stylesheet" href="supervisor_css/supITPStudents.css">
  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById("sidebar");
      sidebar.classList.toggle("collapsed");
    }
  </script>
</head>
<body>
  <header>
    <h1>Welcome, <?php echo htmlspecialchars($supervisor_name); ?>!</h1>
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
            <li><a href="comITPApplication.html">Student Applications(TRAINING)</a></li>
            <li><a href="comApplication.php">Student Applications(PLACEMENT)</a></li>
            <li><a href="comSupervisorList.php">List of Supervisors</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>


    <!-- Main Content -->
    <div class="main-content">
      <div class="profile-section">
        <div class="profile-photo">Photo</div>
        <div class="profile-info">
          <div class="info-box"><?php echo htmlspecialchars($supervisor_name); ?></div>
          <div class="info-box"><?php echo $has_alert ? 'You have an action that needs taking' : 'No action needed'; ?></div>
        </div>
      </div>

      <div class="grid-section">
        <div class="alert-box">
          <h3>Your Supervised Students</h3>
          <?php if (count($students) > 0): ?>
              <table>
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Alert</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($students as $stu): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($stu['Student_ID']); ?></td>
                      <td><?php echo htmlspecialchars($stu['student_name']); ?></td>
                      <td><?php echo htmlspecialchars($stu['student_email']); ?></td>
                      <td><?php echo htmlspecialchars($stu['student_phone']); ?></td>
                      <td><?php echo htmlspecialchars($stu['Status']); ?></td>
                      <td><?php echo $stu['Alert'] ? '<strong>Pending Approval</strong>' : 'None'; ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
          <?php else: ?>
              <p>No supervised students found.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
