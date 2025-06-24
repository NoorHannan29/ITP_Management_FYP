<?php
session_start();
require_once("php_files/db_connect.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: index.html");
    exit();
}

$student_id = $_SESSION['student_id'];

if (!isset($_GET['id'])) {
    die("No logbook selected.");
}

$logbook_id = $_GET['id'];

$sql = "SELECT * FROM logbook WHERE Logbook_ID = ? AND Student_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $logbook_id, $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Logbook not found or access denied.");
}

$log = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>View Logbook Entry</title>
  <link rel="stylesheet" href="./student_css/main.css">
  <link rel="stylesheet" href="./student_css/stuLogbook.css">
</head>
<body>
  <header>
    <h1>View Logbook Entry</h1>
    <button onclick="window.location.href='stuLogBooks.php'" style="padding: 10px 20px; border-radius: 10px; background-color: white; border: none; font-weight: bold; cursor:pointer;">← Back to Logbooks</button>
  </header>

  <div class="main-layout">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">&#60; Hide</button>
        <div class="sidebar-content">
            <h3>Student Pages</h3>
            <ul class="sidebar-nav">
                <li><a href="main.php">Dashboard</a></li>
                <li><a href="stuAnnouncement.html">Announcements</a></li>
                <li><a href="stuProfile.html">Profile</a></li>
                <li><a href="stuITPApplication.php">Apply for ITP</a></li>
                <li><a href="stuApplication.php">Application For ITP Placement</a></li>
                <li><a href="stuCompList.html">Company Listings</a></li>
                <li><a href="stuLogBooks.php">Logbook Management</a></li>
                <li><a href="stuGuidelines.html">Guidelines for ITP</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
      <div class="logbook-container">
        <h2>Week <?php echo $log['Week_Number']; ?> – <?php echo htmlspecialchars($log['Logbook_Date']); ?></h2>
        <p><strong>Report Period:</strong> <?php echo htmlspecialchars($log['Report_Period']); ?></p>
        <p><strong>Company:</strong> <?php echo htmlspecialchars($log['Company_Name']); ?></p>
        <p><strong>Training Period:</strong> <?php echo htmlspecialchars($log['Training_Period']); ?></p>
        <p><strong>Company Supervisor:</strong> <?php echo htmlspecialchars($log['Company_Supervisor_Name']); ?></p>
        <p><strong>Faculty Supervisor:</strong> <?php echo htmlspecialchars($log['Faculty_Supervisor_Name']); ?></p>
        <hr>
        <p><strong>Tasks Done:</strong><br><?php echo nl2br(htmlspecialchars($log['Tasks_Done'])); ?></p>
        <p><strong>Reflections:</strong><br><?php echo nl2br(htmlspecialchars($log['Reflections'])); ?></p>
        <p><strong>Supervisor Remarks:</strong><br><?php echo nl2br(htmlspecialchars($log['Supervisor_Remarks'] ?? '—')); ?></p>
        <p><strong>Viewed by Supervisor:</strong> 
            <?php echo $log['Supervisor_Viewed'] ? "<span class='status-viewed'>Yes ✅</span>" : "<span class='status-pending'>No ❌</span>"; ?>
        </p>
      </div>
    </div>
  </div>
</body>
</html>
