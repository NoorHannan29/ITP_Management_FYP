<?php
session_start();
if (!isset($_SESSION['supervisor_id']) || !$_SESSION['is_committee']) {
    header("Location: index.html?error=unauthorized");
    exit();
}

require_once '../php_files/db_connect.php';

// Fetch all supervisors
$sql = "SELECT 
            s.Supervisor_ID, 
            s.Supervisor_Name, 
            s.Supervisor_Email, 
            s.Preferred_Specialization, 
            s.Committee_ID,
            s.Committee_Role,
            COUNT(st.Student_ID) AS Student_Count
        FROM supervisor s
        LEFT JOIN student st ON s.Supervisor_ID = st.Supervisor_ID
        GROUP BY s.Supervisor_ID";

$result = $conn->query($sql);

$supervisors = [];
while ($row = $result->fetch_assoc()) {
    $supervisors[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Supervisor List</title>
  <link rel="stylesheet" href="supervisor_css/comSupervisorList.css">
</head>
<body>
  <header>
    <h1>Supervisor List</h1>
    <button onclick="window.location.href='php_files/logout.php'" style="float:right; margin-right:20px;">Log Out</button>
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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h2 style="margin: 0;">List of Supervisors</h2>
      <button class="add-btn" onclick="location.href='comAddSupervisor.html'">+ Add New Supervisor</button>
    </div>


      <?php if (count($supervisors) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Preferred Specialization</th>
              <th>No. of Students</th>
              <th>Committee Role</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($supervisors as $row): ?>
              <tr onclick="goToSupStudent('<?php echo $row['Supervisor_ID']; ?>')">
                <td><?php echo htmlspecialchars($row['Supervisor_ID']); ?></td>
                <td><?php echo htmlspecialchars($row['Supervisor_Name']); ?></td>
                <td><?php echo htmlspecialchars($row['Supervisor_Email']); ?></td>
                <td><?php echo htmlspecialchars($row['Preferred_Specialization']); ?></td>
                <td><?php echo $row['Student_Count']; ?></td>
                <td><?php echo !empty($row['Committee_ID']) ? htmlspecialchars($row['Committee_Role']) : '-'; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No supervisors found.</p>
      <?php endif; ?>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      document.getElementById("sidebar").classList.toggle("collapsed");
    }

  function goToSupStudent(supervisorId) {
    window.location.href = 'comStudentAssignment.php?supervisor_id=' + supervisorId;
  }
  </script>
</body>
</html>

