<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('Please login first to access this page.'); window.location.href = 'index.html';</script>";
    exit();
}

require_once './php_files/db_connect.php';

$student_id = $_SESSION['student_id'] ?? '';
$student_name = $_SESSION['student_name'] ?? '';
$student_email = $_SESSION['student_email'] ?? '';
$student_phone = $_SESSION['student_phone'] ?? '';
$student_program = $_SESSION['student_program'] ?? '';

// Check if already submitted
$check_sql = "SELECT ITP_Application_ID FROM itp_applications WHERE Student_ID = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $student_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "<script>alert('You have already submitted your ITP application.'); window.location.href='main.php';</script>";
    exit();
}
$check_stmt->close();

// Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $reason = $_POST['application_reason'] ?? '';

    $stmt = $conn->prepare("INSERT INTO itp_applications (Student_ID, Preferred_Start_Date, Preferred_End_Date, Application_Reason) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $student_id, $start_date, $end_date, $reason);
        if ($stmt->execute()) {
            echo "<script>alert('Industrial Training application submitted successfully.'); window.location.href='main.php';</script>";
        } else {
            echo "<script>alert('Submission failed. Please try again.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Industrial Training</title>
    <link rel="stylesheet" href="./student_css/main.css">
    <link rel="stylesheet" href="./student_css/stuApplication.css">
    <script src="./student_script/main.js" defer></script>
</head>
<body>
<header>
    <h1>MMU ITMS SYSTEM</h1>
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

    <!-- Form Content -->
    <div class="content" id="content">
        <form class="registration-form" method="POST" action="php_files/submit_itp_application.php">
            <!-- Pre-filled Info -->
            <input type="text" value="<?php echo $student_name; ?>" readonly placeholder="Full Name">
            <input type="text" value="<?php echo $student_id; ?>" readonly placeholder="Student ID">
            <input type="email" value="<?php echo $student_email; ?>" readonly placeholder="Email">
            <input type="tel" value="<?php echo $student_phone; ?>" readonly placeholder="Phone">
            <input type="text" value="<?php echo $student_program; ?>" readonly placeholder="Program">

            <!-- Application Form -->
            <label for="start_date">Preferred Start Date:</label>
            <input type="date" name="start_date" id="start_date" required>

            <label for="end_date">Preferred End Date:</label>
            <input type="date" name="end_date" id="end_date" required>

            <button type="submit">Submit ITP Application</button>
        </form>
    </div>
</div>
</body>
</html>
