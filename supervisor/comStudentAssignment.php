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

$stmt = $conn->prepare("SELECT Student_ID, student_name, student_program, Supervisor_ID 
                        FROM student WHERE Supervisor_ID IS NULL");
$stmt->execute();
$unassigned_result = $stmt->get_result();

$unassigned_students = [];
while ($row = $unassigned_result->fetch_assoc()) {
    $unassigned_students[] = $row;
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
  const currentSupervisorId = "<?php echo $supervisor_id; ?>";
  </script>
<script src="supervisor_js/comStudentAssignment.js"></script>

  <script src="supervisor_script/comStudentAssignment.js"></script>
</head>
<body data-supervisor-id="<?php echo $supervisor_id; ?>">
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
  <!-- Confirmat Remove Modal -->
<div id="confirmModal" class="modal" style="display:none;">
  <div class="modal-content">
    <p>Are you sure you want to remove this student from the supervisor?</p>
    <div class="modal-actions">
      <button class="confirm-btn" onclick="confirmRemove()">Yes, Remove</button>
      <button class="cancel-btn" onclick="closeModal()">Cancel</button>
    </div>
  </div>
</div>

<!-- Add Student Modal -->
<div id="addStudentModal" class="modal" style="display: none;">
  <div class="modal-content" style="width: 400px;">
    <h3>Select Student to Assign</h3>
    <select id="studentSelect" onchange="updateStudentDetails()" style="width: 100%; padding: 8px;">
      <option value="">-- Select a student --</option>
      <?php foreach ($unassigned_students as $student): ?>
        <option value="<?php echo $student['Student_ID']; ?>"
                data-name="<?php echo htmlspecialchars($student['student_name']); ?>"
                data-program="<?php echo htmlspecialchars($student['student_program']); ?>"
                data-supervisor="<?php echo $student['Supervisor_ID'] ?? '-'; ?>">
          <?php echo $student['student_name']; ?> (<?php echo $student['Student_ID']; ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <div id="studentDetails" style="margin-top: 20px; display: none;">
      <p><strong>Student ID:</strong> <span id="detailID"></span></p>
      <p><strong>Specialisation:</strong> <span id="detailProgram"></span></p>
      <p><strong>Current Supervisor:</strong> <span id="detailSupervisor"></span></p>
    </div>

    <div class="modal-actions">
      <button class="confirm-btn" onclick="confirmAddStudent()">Assign</button>
      <button class="cancel-btn" onclick="closeAddModal()">Cancel</button>
    </div>
  </div>
</div>

</body>
</html>

