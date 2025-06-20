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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Create Logbook Entry</title>
  <link rel="stylesheet" href="./student_css/main.css">
  <link rel="stylesheet" href="student_css/stuLogbookForm.css"> 
  <script src="./student_script/main.js" type="text/javascript"></script>
</head>
<body>
  <header>
    <h1>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h1>
    <h1>Create Logbook Entry</h1>
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
          <li><a href="stuApplication.php">Application For ITP</a></li>
          <li><a href="stuApplStat.html">Your Application Status</a></li>
          <li><a href="stuCompList.html">Company Listings</a></li>
          <li><a href="stuLogBooks.php">Logbook Management</a></li>
          <li><a href="stuGuidelines.html">Guidelines for ITP</a></li>
        </ul>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <div class="form-section">
        <form action="php_files/add_logbook.php" method="post" class="logbook-form">
          <label>Student ID:</label>
          <input type="text" name="student_id" value="<?php echo $student_id; ?>" readonly>

          <label>Student Name:</label>
          <input type="text" value="<?php echo $student_name; ?>" readonly>

          <label for="logbook_date">Logbook Date:</label>
          <input type="date" name="logbook_date" required>

          <label for="week_number">Week Number:</label>
          <input type="number" name="week_number" min="1" required>

          <label for="report_period">Report Period (e.g. June 10 – June 14):</label>
          <input type="text" name="report_period" required>

          <label for="company_name">Company Name:</label>
          <input type="text" name="company_name" required>

          <label for="training_period">Training Period:</label>
          <input type="text" name="training_period" required>

          <label for="company_supervisor">Company Supervisor Name:</label>
          <input type="text" name="company_supervisor" required>

          <label for="faculty_supervisor">Faculty Supervisor Name:</label>
          <input type="text" name="faculty_supervisor" required>

          <label for="tasks_done">Tasks Done:</label>
          <textarea name="tasks_done" rows="5" required></textarea>

          <label for="reflections">Reflections:</label>
          <textarea name="reflections" rows="5" required></textarea>

          <label for="supervisor_remarks">Remarks from Company Supervisor (optional):</label>
          <textarea name="supervisor_remarks" rows="4"></textarea>

          <br>
          <button type="submit" class="submit-button">Submit Logbook Entry</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
