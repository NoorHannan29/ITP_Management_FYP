<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || empty($_SESSION['is_committee'])) {
    header("Location: index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$supervisor_name = $_SESSION['supervisor_name'];

// Ensure application_id is provided
if (!isset($_GET['application_id'])) {
    echo "Missing application ID.";
    exit();
}

$app_id = $_GET['application_id'];

// Fetch full application + student data
$sql = "SELECT 
            a.Application_ID, a.Application_Status, a.Remarks, a.Submitted_At,
            a.Internship_Start_Date, a.Internship_End_Date, a.Allowance_Amount, a.Job_Description,
            a.Company_Name, a.Company_Address, a.Company_State,
            a.Company_Contact_Name, a.Company_Designation, a.Company_Phone, a.Company_Email, a.Company_Website,
            s.Student_ID, s.student_name, s.student_email, s.student_phone, s.student_program, s.student_specialisation,
            d.Offer_Letter_Path, d.Undertaking_Letter_Path, d.Insurance_Letter_Path
        FROM applications a
        JOIN student s ON s.Student_ID = a.Student_ID
        LEFT JOIN application_documents d ON a.Application_ID = d.Application_ID
        WHERE a.Application_ID = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $app_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No application found.";
    exit();
}

$data = $result->fetch_assoc();
$conn->close();

$offerPath = $data['Offer_Letter_Path'];
$undertakingPath = $data['Undertaking_Letter_Path'];
$insurancePath = $data['Insurance_Letter_Path'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <script src="../student_script/main.js" defer></script>
  <script src="./supervisor_script/applicationDenial.js" defer></script>
  <script src="./supervisor_script/submitApplication.js" defer></script>
  <link rel="stylesheet" href="../student_css/main.css">
  <link rel="stylesheet" href="./supervisor_css/comApplicationApproval.css">
  <title>APPLICATION APPROVAL</title>
  <script>
    const applicationId = <?= json_encode($data['Application_ID']) ?>;
  </script>
</head>
<body>
  <header>
    <h1 style="float: left;"><?php echo htmlspecialchars($supervisor_name); ?></h1>
    <h1 style="text-align: right;">APPLICATION APPROVAL</h1>
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
            <li><a href="comApplication.php">Student Applictions</a></li>
            <li><a href="comSupervisorList.php">List of Supervisors</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>




    <!-- Main Content -->
    <div class="approval-content" id="content">
      <div class="form-box">
        <h2>Student Application Details</h2>

        <div class="form-field"><strong>Full Name:</strong> <?= htmlspecialchars($data['student_name']) ?></div>
        <div class="form-field"><strong>Student ID:</strong> <?= htmlspecialchars($data['Student_ID']) ?></div>
        <div class="form-field"><strong>Program:</strong> <?= htmlspecialchars($data['student_program']) ?></div>
        <div class="form-field"><strong>specialization:</strong> <?= htmlspecialchars($data['student_specialisation']) ?></div>
        <div class="form-field"><strong>Email:</strong> <?= htmlspecialchars($data['student_email']) ?></div>
        <div class="form-field"><strong>Phone:</strong> <?= htmlspecialchars($data['student_phone']) ?></div>

        <hr>

        <div class="form-field"><strong>Application Submitted:</strong> <?= htmlspecialchars($data['Submitted_At']) ?></div>
        <div class="form-field"><strong>Status:</strong> <?= htmlspecialchars($data['Application_Status']) ?></div>
        <?php if (!empty($data['Remarks'])): ?>
          <div class="form-field"><strong>Remarks:</strong> <?= htmlspecialchars($data['Remarks']) ?></div>
        <?php endif; ?>

        <hr>

        <div class="form-field"><strong>Internship Start:</strong> <?= htmlspecialchars($data['Internship_Start_Date']) ?></div>
        <div class="form-field"><strong>Internship End:</strong> <?= htmlspecialchars($data['Internship_End_Date']) ?></div>
        <div class="form-field"><strong>Allowance:</strong> 
          <?= is_null($data['Allowance_Amount']) ? 'N/A' : 'RM ' . htmlspecialchars($data['Allowance_Amount']) ?>
        </div>
        <div class="form-field"><strong>Job Description:</strong><br><?= nl2br(htmlspecialchars($data['Job_Description'])) ?></div>

        <hr>

        <h3>Company Details</h3>
        <div class="form-field"><strong>Name:</strong> <?= htmlspecialchars($data['Company_Name']) ?></div>
        <div class="form-field"><strong>Address:</strong><br><?= nl2br(htmlspecialchars($data['Company_Address'])) ?></div>
        <div class="form-field"><strong>State:</strong> <?= htmlspecialchars($data['Company_State']) ?></div>
        <div class="form-field"><strong>Contact Person:</strong> <?= htmlspecialchars($data['Company_Contact_Name']) ?></div>
        <div class="form-field"><strong>Designation:</strong> <?= htmlspecialchars($data['Company_Designation']) ?></div>
        <div class="form-field"><strong>Phone:</strong> <?= htmlspecialchars($data['Company_Phone']) ?></div>
        <div class="form-field"><strong>Email:</strong> <?= htmlspecialchars($data['Company_Email']) ?></div>
        <div class="form-field"><strong>Website:</strong> <?= htmlspecialchars($data['Company_Website']) ?></div>

        <hr>
        <h3>Uploaded Documents</h3>
        <div class="form-field">
          <?php if (!empty($offerPath)): ?>
            <button onclick="window.open('<?= htmlspecialchars('../' . $offerPath) ?>', '_blank')" class="download-btn">
              📄 Offer Letter
            </button>
          <?php endif; ?>

          <?php if (!empty($undertakingPath)): ?>
            <button onclick="window.open('<?= htmlspecialchars('../' . $undertakingPath) ?>', '_blank')" class="download-btn">
              📄 Letter of Undertaking
            </button>
          <?php endif; ?>

          <?php if (!empty($insurancePath)): ?>
            <button onclick="window.open('<?= htmlspecialchars('../' . $insurancePath) ?>', '_blank')" class="download-btn">
              📄 Insurance Letter
            </button>
          <?php endif; ?>

          <?php if (empty($offerPath) && empty($undertakingPath) && empty($insurancePath)): ?>
            <p style="color: red;">No uploaded documents found.</p>
          <?php endif; ?>
        </div>
        
        <div class="approval-buttons">
          <button class="approve-btn" onclick="submitApproval(<?= $app_id ?>)">Approve</button>
          <button class="deny-btn" onclick="openDenyPopup()">Deny</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Denial Popup -->
  <div id="denyPopup" class="popup-overlay">
    <div class="popup-box">
      <h3>Reason for Denial</h3>
      <textarea id="denyReason" placeholder="Enter reason here..."></textarea>
      <div class="popup-actions">
        <button onclick="submitDenial()">Submit</button>
        <button onclick="closeDenyPopup()">Cancel</button>
      </div>
    </div>
  </div>
</body>
</html>
