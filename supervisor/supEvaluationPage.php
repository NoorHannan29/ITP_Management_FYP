<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || !isset($_SESSION['supervisor_name'])) {
    header("Location: index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$supervisor_id = $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];

// Fetch students under this supervisor
$sql = "SELECT s.Student_ID, s.student_name
        FROM student s
        WHERE s.Supervisor_ID = ?
        AND s.Student_ID NOT IN (SELECT Student_ID FROM evaluations)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supervisor_id);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Supervisor Evaluation</title>
  <link rel="stylesheet" href="supervisor_css/supITPStudents.css"> 
  <link rel="stylesheet" href="supervisor_css/supEvaluationPage.css">
  <script src="supervisor_script/supEvaluationPage.js"></script>  
</head>
<body>
  <header>
    <h1>Evaluate Students – <?php echo htmlspecialchars($supervisor_name); ?></h1>
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
            <li><a href="comITPApplication.php">ITP Applications</a></li>
            <li><a href="comApplication.php">Placement Applications</a></li>
            <li><a href="comSupervisorList.php">List of Supervisors</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>


    <div class="main-content">
      <div class="alert-box">
        <h3>Overall Assessment Form</h3>

        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
          <div class="popup-overlay" id="successPopup">
            <div class="popup-box">
              <p>✅ Evaluation submitted successfully!</p>
              <button onclick="document.getElementById('successPopup').style.display='none'">OK</button>
            </div>
          </div>
        <?php endif; ?>

        <?php if (count($students) > 0): ?>
        <form method="post" action="supervisor_php/submit_evaluation.php" id="evaluationForm">

          <label for="student_id">Select Student:</label>
          <select name="student_id" required>
            <option value="">-- Select --</option>
            <?php foreach ($students as $s): ?>
              <option value="<?php echo $s['Student_ID']; ?>">
                <?php echo htmlspecialchars($s['Student_ID'] . " - " . $s['student_name']); ?>
              </option>
            <?php endforeach; ?>
          </select>

          <h4>A. Report and Professionalism (1–5)</h4>
          <div class="radio-section">
            <label>Report Quality:</label>
            <div class="radio-group">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <label><input type="radio" name="report_quality" value="<?php echo $i; ?>" required> <?php echo $i; ?></label>
              <?php endfor; ?>
            </div>

            <label>Skill Application:</label>
            <div class="radio-group">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <label><input type="radio" name="skill_application" value="<?php echo $i; ?>" required> <?php echo $i; ?></label>
              <?php endfor; ?>
            </div>

            <label>Timely Reporting:</label>
            <div class="radio-group">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <label><input type="radio" name="timely_reporting" value="<?php echo $i; ?>" required> <?php echo $i; ?></label>
              <?php endfor; ?>
            </div>
          </div>

          <h4>B. Company Supervisor Assessment (0–20)</h4>
          <label for="company_score">Company Score:</label>
          <div class="slider-container">
            <input type="range" id="company_score" name="company_score" min="0" max="20" value="10" oninput="updateSliderLabel(this.value)">
            <span id="score_value">10</span>
          </div>
          <div id="score_description">Decent</div>

          <h4>C. Presentation Assessment</h4>
          <div class="radio-section">
            <label>Company Presentation (0–5):</label>
            <div class="radio-group">
              <?php for ($i = 0; $i <= 5; $i++): ?>
                <label><input type="radio" name="company_presentation" value="<?php echo $i; ?>" required> <?php echo $i; ?></label>
              <?php endfor; ?>
            </div>

            <label>Faculty Presentation (0–5):</label>
            <div class="radio-group">
              <?php for ($i = 0; $i <= 5; $i++): ?>
                <label><input type="radio" name="faculty_presentation" value="<?php echo $i; ?>" required> <?php echo $i; ?></label>
              <?php endfor; ?>
            </div>

            <div class="total-display">
              Total Presentation Score: <span id="presentation_total">0</span> / 10
            </div>
          </div>

          <label>Comments:</label>
          <textarea name="comments" rows="4" cols="50" placeholder="Optional feedback..."></textarea>

          <br><br>
          <button type="submit">Submit Evaluation</button>
        </form>

        <?php else: ?>
          <p class="no-students">❗ You have no students pending an evaluation</p>
          <button onclick="window.location.href='supITPStudents.php'" class="back-button">Back to Dashboard</button>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>

</html>
