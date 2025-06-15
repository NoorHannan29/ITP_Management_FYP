<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || !isset($_SESSION['supervisor_name'])) {
    header("Location: ../index.html?error=1");
    exit();
}

require_once '../php_files/db_connect.php';

$supervisor_id = $_SESSION['supervisor_id'];
$supervisor_name = $_SESSION['supervisor_name'];

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

// Get logbook + student info
$stmt = $conn->prepare("
    SELECT l.logbook_id, l.student_id, l.logbook_date, l.logbook_content, l.supervisor_viewed,
           s.student_name
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
                <li><a href="supEvaluate.html">Evaluate Students</a></li>

                <?php if (!empty($_SESSION['is_committee'])): ?>
                    <li><a href="comAnnouncements.php">Announcements</a></li>
                    <li><a href="comApplication.php">Student Applications</a></li>
                    <li><a href="comSupervisorList.html">List of Supervisors</a></li>
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
            <p><strong>Status:</strong> <?= $log['supervisor_viewed'] ? 'Viewed' : 'Not Viewed' ?></p>

            <div class="log-content">
                <h3>Logbook Content</h3>
                <p><?= nl2br(htmlspecialchars($log['logbook_content'])) ?></p>
            </div>
            <div class="button-row">
                <div class="go-back-container">
                    <button onclick="history.back()">Go Back</button>
                        <?php if ($log['supervisor_viewed'] == 0): ?>
                        <button style="float:right;" type="submit" name="mark_read" >Mark as Read</button>
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
                    