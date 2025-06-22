<?php
session_start();
require_once("php_files/db_connect.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: index.html");
    exit();
}

$studentID = $_SESSION['student_id'];
$query = "SELECT * FROM logbook WHERE Student_ID = ? ORDER BY Logbook_Date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LOG BOOK PAGE</title>
    <link rel="stylesheet" href="./student_css/main.css">
    <link rel="stylesheet" href="./student_css/stuLogbook.css">
    <script src="./student_script/stuLogBooks.js" defer></script>
</head>
<body>
<header><h1>LOG BOOK PAGE</h1></header>

<div class="main-layout">
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">&#60; Hide</button>
        <div class="sidebar-content">
            <h3>Student Pages</h3>
            <ul class="sidebar-nav">
                <li><a href="main.php">Dashboard</a></li>
                <li><a href="stuAnnouncement.html">Announcements</a></li>
                <li><a href="stuProfile.html">Profile</a></li>
                <li><a href="stuApplication.php">Application For ITP</a></li>
                <li><a href="stuCompList.html">Company Listings</a></li>
                <li><a href="stuLogBooks.php">Logbook Management</a></li>
                <li><a href="stuGuidelines.html">Guidelines for ITP</a></li>
            </ul>
        </div>
    </div>

    <div class="content">
        <div class="logbook-container">
            <div class="logbook-header">
                <h2>Your Log Books</h2>
                <button onclick="window.location.href='stuCreateLogbook.php'" class="add-logbook-button">+ Add New Logbook</button>
            </div>

            <div class="logbook-body">
            <?php if ($result->num_rows > 0): ?>
                <table class="logbook-table">
                <thead>
                    <tr>
                    <th>No.</th>
                    <th>Date</th>
                    <th>Report Period</th>
                    <th>Status</th>
                    <th>Report File</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 1;
                    while ($row = $result->fetch_assoc()): 
                        $fullTask = $row['Tasks_Done'] ?? 'No content';
                        $taskPreview = strlen($fullTask) > 80 
                                        ? substr($fullTask, 0, 80) . '...'
                                        : $fullTask;
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlspecialchars($row['Logbook_Date']); ?></td>
                        <td><?php echo htmlspecialchars($row['Report_Period']); ?></td>
                        <td>
                        <?php if ($row['Supervisor_Viewed']): ?>
                            <span class="status-viewed">Viewed by Supervisor ✅</span>
                        <?php else: ?>
                            <span class="status-pending">Not Viewed ❌</span>
                        <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= htmlspecialchars($row['Logbook_File_Path']) ?>" target="_blank" class="download-btn">
                        📄 View PDF
                        </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                </table>
            <?php else: ?>
                <p>No logbook entries found.</p>
            <?php endif; ?>
            </div>

        </div>
    </div>
</div>

</body>
</html>
