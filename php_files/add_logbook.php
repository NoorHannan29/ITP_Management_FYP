<?php
session_start();
require_once("db_connect.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit();
}

$studentID = $_SESSION['student_id'];
$logDate = $_POST['logbook_date'];
$weekNumber = $_POST['week_number'];
$reportPeriod = $_POST['report_period'];

// Handle file upload
$uploadDir = "../uploads/logbooks/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = pathinfo($_FILES['logbook_file']['name'], PATHINFO_EXTENSION);
$newFilename = "Logbook_" . $studentID . "_Week" . $weekNumber . "_" . date("YmdHis") . "." . $ext;
$targetFile = $uploadDir . $newFilename;

if (move_uploaded_file($_FILES['logbook_file']['tmp_name'], $targetFile)) {
    $relativePath = "uploads/logbooks/" . $newFilename;

    $stmt = $conn->prepare("INSERT INTO logbook 
        (Student_ID, Logbook_Date, Week_Number, Report_Period, Supervisor_Viewed, Logbook_File_Path) 
        VALUES (?, ?, ?, ?, 0, ?)");

    $stmt->bind_param("ssiss", $studentID, $logDate, $weekNumber, $reportPeriod, $relativePath);
    $stmt->execute();
    $stmt->close();

    header("Location: ../stuLogBooks.php?success=1");
    exit();
} else {
    echo "<script>alert('Failed to upload logbook.'); window.history.back();</script>";
}
?>
