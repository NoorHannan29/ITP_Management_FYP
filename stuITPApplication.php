<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='index.html';</script>";
    exit();
}

require_once './php_files/db_connect.php';

$student_id = $_SESSION['student_id'] ?? '';
$student_name = $_SESSION['student_name'] ?? '';
$student_email = $_SESSION['student_email'] ?? '';
$student_phone = $_SESSION['student_phone'] ?? '';
$student_program = $_SESSION['student_program'] ?? '';

// Restrict access to approved users even on GET
$precheck_sql = "SELECT Status FROM itp_applications WHERE Student_ID = ?";
$precheck_stmt = $conn->prepare($precheck_sql);
$precheck_stmt->bind_param("s", $student_id);
$precheck_stmt->execute();
$precheck_stmt->store_result();
$precheck_stmt->bind_result($status);

if ($precheck_stmt->num_rows > 0) {
    $precheck_stmt->fetch();
    if ($status === "Approved") {
        echo "<script>alert('You have already been approved for ITP. You cannot access this page.'); window.location.href='main.php';</script>";
        exit();
    }
}
$precheck_stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // First, check if student already has an approved ITP application
    $status_check_sql = "SELECT Status FROM itp_applications WHERE Student_ID = ?";
    $status_stmt = $conn->prepare($status_check_sql);
    $status_stmt->bind_param("s", $student_id);
    $status_stmt->execute();
    $status_stmt->store_result();
    $status_stmt->bind_result($status);

    if ($status_stmt->num_rows > 0) {
        $status_stmt->fetch();
        if ($status === "Approved") {
            echo "<script>alert('You have already been approved for ITP. You cannot submit another application.'); window.location.href='main.php';</script>";
            exit();
        } else {
            echo "<script>alert('You have already submitted an ITP application (Status: $status).'); window.location.href='main.php';</script>";
            exit();
        }
    }
    $status_stmt->close();

    // --- Continue to handle new submission ---
    $credit_score = isset($_POST['credit_score']) ? intval($_POST['credit_score']) : null;

    if ($credit_score === null || $credit_score < 0 || $credit_score > 100) {
        echo "<script>alert('Credit Score is required and must be between 0 and 100.'); window.history.back();</script>";
        exit();
    }

    // Optional file upload
    $supporting_file_path = null;

    if (isset($_FILES['supporting_file']) && $_FILES['supporting_file']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $maxSize = 10 * 1024 * 1024;

        $file = $_FILES['supporting_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowedTypes) && $file['size'] <= $maxSize) {
            $uploadDir = "uploads/itp_supporting/";
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = "SUPP_ITP_" . $student_id . "_" . time() . "." . $ext;
            $filePath = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $supporting_file_path = $filePath;
            }
        }
    }

    // Insert into database
    $sql = "INSERT INTO itp_applications (Student_ID, Credit_Score, Supporting_File) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sis", $student_id, $credit_score, $supporting_file_path);
        if ($stmt->execute()) {
            echo "<script>alert('ITP application submitted successfully.'); window.location.href='main.php';</script>";
        } else {
            echo "<script>alert('Failed to submit application.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error.'); window.history.back();</script>";
    }

    $conn->close();
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
                <li><a href="stuITPApplication.php" class="active">Apply for ITP</a></li>
                <li><a href="stuApplication.php">Application For ITP Placement</a></li>
                <li><a href="stuCompList.html">Company Listings</a></li>
                <li><a href="stuLogBooks.php">Logbook Management</a></li>
                <li><a href="stuGuidelines.html">Guidelines for ITP</a></li>
            </ul>
        </div>
    </div>

    <!-- Form Content -->
    <div class="content" id="content">
        <form class="registration-form" method="POST" action="stuITPApplication.php" enctype="multipart/form-data">
            <input type="text" value="<?php echo $student_name; ?>" readonly placeholder="Full Name">
            <input type="text" value="<?php echo $student_id; ?>" readonly placeholder="Student ID">
            <input type="email" value="<?php echo $student_email; ?>" readonly placeholder="Email">
            <input type="tel" value="<?php echo $student_phone; ?>" readonly placeholder="Phone">
            <input type="text" value="<?php echo $student_program; ?>" readonly placeholder="Program">

            <label for="credit_score">Credit Score:</label>
            <input type="number" name="credit_score" id="credit_score" min="0" max="100" required placeholder="e.g. 65">

            <label for="supporting_file">Upload Supporting File (optional):</label>
            <input type="file" name="supporting_file" id="supporting_file" accept=".pdf,.doc,.docx,.jpg,.png">

            <button type="submit">Submit ITP Application</button>
        </form>
    </div>
</div>
</body>
</html>
