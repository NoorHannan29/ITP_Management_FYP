<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || !isset($_SESSION['supervisor_name'])) {
    header("Location: ../index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$logbook_id = $_GET['logbook_id'] ?? null;
if (!$logbook_id) {
    echo "Invalid logbook ID.";
    exit();
}

// Handle mark as read
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_read'])) {
    $logbook_id_post = $_POST['logbook_id'];
    $stmt = $conn->prepare("UPDATE logbook SET supervisor_viewed = 1 WHERE logbook_id = ?");
    $stmt->bind_param("i", $logbook_id_post);
    $stmt->execute();
    $stmt->close();
    header("Location: supViewLog.php?logbook_id=$logbook_id_post&success=1");
    exit();
}

// Fetch logbook data
$stmt = $conn->prepare("
    SELECT l.logbook_id, l.student_id, l.logbook_date, l.week_number, l.report_period, 
           l.logbook_file_path, l.supervisor_viewed, s.student_name
    FROM logbook l
    JOIN student s ON l.student_id = s.Student_ID
    WHERE l.logbook_id = ?
");
$stmt->bind_param("i", $logbook_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Logbook not found.";
    exit();
}

$log = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Logbook</title>
    <link rel="stylesheet" href="supervisor_css/supViewLog.css">
</head>
<body>
<header>
    <h1>Viewing Logbook</h1>
    <button onclick="window.location.href='php_files/logout.php'">Log Out</button>
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
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">Logbook marked as read successfully.</div>
        <?php endif; ?>

        <div class="logbox">
            <h2>Student Name: <?= htmlspecialchars($log['student_name']) ?></h2>
            <p><strong>Student ID:</strong> <?= htmlspecialchars($log['student_id']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($log['logbook_date']) ?></p>
            <p><strong>Week Number:</strong> <?= htmlspecialchars($log['week_number']) ?></p>
            <p><strong>Report Period:</strong> <?= htmlspecialchars($log['report_period']) ?></p>
            <p><strong>Status:</strong> <?= $log['supervisor_viewed'] ? 'Viewed' : 'Not Viewed' ?></p>

            <div class="log-content">
                <h3>Logbook File</h3>
                <?php if (!empty($log['logbook_file_path'])): ?>
                    <a href="../<?= htmlspecialchars($log['logbook_file_path']) ?>" target="_blank" class="download-btn">
                        📄 Download PDF
                    </a>
                <?php else: ?>
                    <p style="color:red;">No file uploaded.</p>
                <?php endif; ?>
            </div>

            <div class="button-row" style="margin-top: 20px;">
                <button onclick="window.location.href='supLogs.php'">Go Back</button>
                <?php if ($log['supervisor_viewed'] == 0): ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="logbook_id" value="<?= $log['logbook_id'] ?>">
                        <button type="submit" name="mark_read">Mark as Read</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("collapsed");
    }
</script>
</body>
</html>

                    