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
                <button class="log-btn add-btn" onclick="openLogbookPopup()">+ Add Entry</button>
            </div>

            <div class="logbook-body">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="log-entry">
                            <strong><?php echo htmlspecialchars($row['Logbook_Date']); ?></strong><br>
                            <?php echo nl2br(htmlspecialchars($row['Logbook_Content'] ?? 'No content yet.')); ?>
                            <small><?php echo $row['Supervisor_Viewed'] ? 'Viewed by supervisor' : 'Not yet viewed'; ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No logbook entries found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Popup -->
<div class="popup-overlay" id="popupOverlay" style="display:none;">
    <div class="popup-form">
        <h3>New Logbook Entry</h3>
        <form action="./php_files/add_logbook.php" method="POST">
            <label>Date:</label>
            <input type="date" name="log_date" value="<?php echo date('Y-m-d'); ?>" required>
            <label>Content:</label>
            <textarea name="logbook_content" rows="6" required></textarea>
            <br>
            <button type="submit" class="log-btn">Submit</button>
            <button type="button" class="log-btn" onclick="closeLogbookPopup()">Cancel</button>
        </form>
    </div>
</div>
</body>
</html>
