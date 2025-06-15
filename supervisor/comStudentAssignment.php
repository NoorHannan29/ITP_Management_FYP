<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || !$_SESSION['is_committee']) {
    header("Location: index.html?error=unauthorized");
    exit();
}

if (!isset($_GET['supervisor_id'])) {
    echo "Supervisor ID is required.";
    exit();
}

$supervisor_id = $_GET['supervisor_id'];

require_once '../php_files/db_connect.php';

// Get Supervisor Info (optional)
$stmt = $conn->prepare("SELECT Supervisor_Name FROM supervisor WHERE Supervisor_ID = ?");
$stmt->bind_param("s", $supervisor_id);
$stmt->execute();
$supervisor_result = $stmt->get_result();
$supervisor_name = $supervisor_result->fetch_assoc()['Supervisor_Name'] ?? 'Unknown';

// Get Students under this Supervisor
$stmt = $conn->prepare("SELECT Student_ID, student_name, student_email, student_phone, student_program 
                        FROM student 
                        WHERE Supervisor_ID = ?");
$stmt->bind_param("s", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Students Under Supervisor</title>
  <link rel="stylesheet" href="supervisor_css/comStudentAssignment.css">
  <script>
    function addStudent(supervisorId) {
      window.location.href = "comAddStudentManual.php?supervisor_id=" + supervisorId;
    }
  </script>
</head>
<body>
  <header>
    <h1>Students Assigned to <?php echo htmlspecialchars($supervisor_name); ?></h1>
    <button onclick="window.location.href='php_files/logout.php'" style="float:right;">Log Out</button>
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
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Student List</h2>
        <button class="add-btn" onclick="addStudent('<?php echo $supervisor_id; ?>')">+ Add Student Manually</button>
      </div>

      <?php if (count($students) > 0): ?>
        <table>
          <thead>
            <tr>
              <th>Student ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Program</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $student): ?>
              <tr>
                <td><?php echo htmlspecialchars($student['Student_ID']); ?></td>
                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                <td><?php echo htmlspecialchars($student['student_email']); ?></td>
                <td><?php echo htmlspecialchars($student['student_phone']); ?></td>
                <td><?php echo htmlspecialchars($student['student_program']); ?></td>
                <td><button class="remove-btn" onclick="showConfirmation('<?php echo $student['Student_ID']; ?>', '<?php echo $supervisor_id; ?>')">Remove</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No students assigned to this supervisor.</p>
      <?php endif; ?>
    </div>
  </div>
  <!-- Confirmation Modal -->
<div id="confirmModal" class="modal" style="display:none;">
  <div class="modal-content">
    <p>Are you sure you want to remove this student from the supervisor?</p>
    <div class="modal-actions">
      <button class="confirm-btn" onclick="confirmRemove()">Yes, Remove</button>
      <button class="cancel-btn" onclick="closeModal()">Cancel</button>
    </div>
  </div>
</div>

<script>
  let pendingStudentId = null;
  let pendingSupervisorId = null;

  function showConfirmation(studentId, supervisorId) {
    pendingStudentId = studentId;
    pendingSupervisorId = supervisorId;
    document.getElementById("confirmModal").style.display = "flex";
  }

  function closeModal() {
    document.getElementById("confirmModal").style.display = "none";
    pendingStudentId = null;
    pendingSupervisorId = null;
  }

  function confirmRemove() {
    if (pendingStudentId && pendingSupervisorId) {
      window.location.href = 'supervisor_php/removeStudentManual.php?student_id=' + pendingStudentId + '&supervisor_id=' + pendingSupervisorId;
    }
  }
</script>

</body>
</html>

