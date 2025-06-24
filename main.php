<?php
session_start();

if (!isset($_SESSION['student_id']) || !isset($_SESSION['student_name'])) {
    header("Location: index.html?error=1");
    exit();
}

require_once './php_files/db_connect.php';

$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['student_name'];
$student_program = $_SESSION['student_program'];

// --- ALERTS ---
$alerts = [];

$appl_sql = "SELECT 1 FROM applications WHERE Student_ID = ?";
$appl_stmt = $conn->prepare($appl_sql);
$appl_stmt->bind_param("s", $student_id);
$appl_stmt->execute();
$appl_stmt->store_result();
if ($appl_stmt->num_rows === 0) {
    $alerts[] = "You have not applied for your Industrial Training yet.";
}
$appl_stmt->close();

$logbook_sql = "SELECT 1 FROM logbook WHERE Student_ID = ? AND Logbook_Date >= CURDATE() - INTERVAL 7 DAY";
$logbook_stmt = $conn->prepare($logbook_sql);
$logbook_stmt->bind_param("s", $student_id);
$logbook_stmt->execute();
$logbook_stmt->store_result();
if ($logbook_stmt->num_rows === 0) {
    $alerts[] = "You have not submitted any logbook entry in the last 7 days.";
}
$logbook_stmt->close();

$announcements = [];
$ann_sql = "SELECT Announcement_ID, Title, Timestamp FROM announcements ORDER BY Timestamp DESC LIMIT 3";
$ann_result = $conn->query($ann_sql);
if ($ann_result && $ann_result->num_rows > 0) {
    while ($row = $ann_result->fetch_assoc()) {
        $announcements[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Student Main Menu</title>
  <link rel="stylesheet" href="./student_css/main.css">
  <script src="./student_script/main.js" type="text/javascript"></script>
</head>
<body>
  <header>
    <h1>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h1>
    <h1>Main Menu</h1>
    <button onclick="window.location.href='php_files/logout.php'" style="padding: 10px 20px; border-radius: 10px; background-color: white; border: none; font-weight: bold; cursor:pointer;">Log Out</button>
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
                <li><a href="stuLogBooks.html">Logbook Management</a></li>
                <li><a href="stuGuidelines.html">Guidelines for ITP</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <!-- Profile Section -->
      <div class="profile-section">
        <div class="profile-photo">Photo</div>
        <div class="profile-info">
          <div class="info-box"><?php echo htmlspecialchars($student_name); ?></div>
          <div class="info-box"><?php echo htmlspecialchars($student_program); ?></div>
        </div>
      </div>

      <!-- Alerts and Announcements -->
      <div class="grid-section">
        <div class="alert-box">
          <h3>Alerts</h3>
          <?php if (count($alerts) > 0): ?>
              <ul>
                  <?php foreach ($alerts as $alert): ?>
                      <li><?php echo htmlspecialchars($alert); ?></li>
                  <?php endforeach; ?>
              </ul>
          <?php else: ?>
              <p>No alerts at this time.</p>
          <?php endif; ?>
        </div>

        <div class="announcement-box">
          <h3>Announcements</h3>
          <?php if (count($announcements) > 0): ?>
              <ul>
                  <?php foreach ($announcements as $ann): ?>
                      <li>
                        <strong>
                          <a href="stuAnnouncementDisplay.html?id=<?php echo urlencode($ann['Announcement_ID']); ?>">
                            <?php echo htmlspecialchars($ann['Title']); ?>
                          </a>
                        </strong><br>
                        <small><?php echo htmlspecialchars(date("M d, Y", strtotime($ann['Timestamp']))); ?></small><br>
                    </li>
                  <?php endforeach; ?>
              </ul>
          <?php else: ?>
              <p>No announcements available.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
