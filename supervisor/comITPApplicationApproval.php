<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || empty($_SESSION['is_committee'])) {
    header("Location: index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$supervisor_name = $_SESSION['supervisor_name'];

if (!isset($_GET['itp_id'])) {
    echo "Missing ITP application ID.";
    exit();
}

$itp_id = $_GET['itp_id'];

$sql = "SELECT i.*, s.student_name, s.student_email, s.student_phone, s.student_program, s.student_specialisation
        FROM itp_applications i
        JOIN student s ON s.Student_ID = i.Student_ID
        WHERE i.ITP_Application_ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $itp_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No ITP application found.";
    exit();
}

$data = $result->fetch_assoc();
$conn->close();

$supportingFile = $data['Supporting_File'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="../student_script/main.js" defer></script>
  <script src="./supervisor_script/applicationDenial.js" defer></script>
  <script src="./supervisor_script/submitITPApproval.js" defer></script>
  <link rel="stylesheet" href="../student_css/main.css">
  <link rel="stylesheet" href="./supervisor_css/comApplicationApproval.css">
  <title>ITP APPLICATION APPROVAL</title>
  <script>
    const itpId = <?= json_encode($data['ITP_Application_ID']) ?>;
  </script>
</head>
<body>
  <header>
    <h1 style="float: left;">Committee: <?php echo htmlspecialchars($supervisor_name); ?></h1>
    <h1 style="text-align: right;">ITP APPLICATION REVIEW</h1>
  </header>

  <div class="main-layout">
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

    <div class="approval-content" id="content">
      <div class="form-box">
        <h2>Student ITP Application</h2>
        <div class="form-field"><strong>Name:</strong> <?= htmlspecialchars($data['student_name']) ?></div>
        <div class="form-field"><strong>ID:</strong> <?= htmlspecialchars($data['Student_ID']) ?></div>
        <div class="form-field"><strong>Program:</strong> <?= htmlspecialchars($data['student_program']) ?></div>
        <div class="form-field"><strong>Specialisation:</strong> <?= htmlspecialchars($data['student_specialisation']) ?></div>
        <div class="form-field"><strong>Email:</strong> <?= htmlspecialchars($data['student_email']) ?></div>
        <div class="form-field"><strong>Phone:</strong> <?= htmlspecialchars($data['student_phone']) ?></div>
        <div class="form-field"><strong>Credit Score:</strong> <?= htmlspecialchars($data['Credit_Score']) ?></div>

        <h3>Supporting Document</h3>
        <?php if (!empty($supportingFile)): ?>
          <button onclick="window.open('<?= '../' . htmlspecialchars($supportingFile) ?>', '_blank')">📎 View Supporting File</button>
        <?php else: ?>
          <p>No supporting file provided.</p>
        <?php endif; ?>

        <div class="approval-buttons">
          <button class="approve-btn" onclick="submitITPApproval(<?= $itp_id ?>)">Approve</button>
          <button class="deny-btn" onclick="openDenyPopup()">Deny</button>
        </div>
      </div>
    </div>
  </div>

  <div id="denyPopup" class="popup-overlay">
    <div class="popup-box">
      <h3>Reason for Denial</h3>
      <textarea id="denyReason" placeholder="Enter reason here..."></textarea>
      <div class="popup-actions">
        <button onclick="submitITPDenial()">Submit</button>
        <button onclick="closeDenyPopup()">Cancel</button>
      </div>
    </div>
  </div>
</body>
</html>
