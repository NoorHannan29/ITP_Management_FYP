<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='index.html';</script>";
    exit();
}

require_once './php_files/db_connect.php';

$student_id = $_SESSION['student_id'] ?? '';

// Prevent multiple submissions
$check_sql = "SELECT ITP_Application_ID FROM itp_applications WHERE Student_ID = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $student_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "<script>alert('You have already submitted an ITP application.'); window.location.href='main.php';</script>";
    exit();
}
$check_stmt->close();

// Form values
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$reason = $_POST['application_reason'] ?? '';

// Validation
if (empty($start_date) || empty($end_date) || empty($reason)) {
    echo "<script>alert('All fields are required.'); window.history.back();</script>";
    exit();
}

// Insert into database
$insert_sql = "INSERT INTO itp_applications (Student_ID, Preferred_Start_Date, Preferred_End_Date, Application_Reason) 
               VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($insert_sql);
if ($stmt) {
    $stmt->bind_param("ssss", $student_id, $start_date, $end_date, $reason);

    if ($stmt->execute()) {
        echo "<script>alert('ITP application submitted successfully.'); window.location.href='main.php';</script>";
    } else {
        echo "<script>alert('Submission failed. Please try again.'); window.history.back();</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Database error.'); window.history.back();</script>";
}

$conn->close();
?>
