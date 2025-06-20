<?php
session_start();
require_once("db_connect.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit();
}

$studentID = $_SESSION['student_id'];
$logbookDate = $_POST['logbook_date'];
$weekNumber = $_POST['week_number'];
$reportPeriod = $_POST['report_period'];
$companyName = $_POST['company_name'];
$trainingPeriod = $_POST['training_period'];
$companySupervisor = $_POST['company_supervisor'];
$facultySupervisor = $_POST['faculty_supervisor'];
$tasksDone = $_POST['tasks_done'];
$reflections = $_POST['reflections'];
$supervisorRemarks = $_POST['supervisor_remarks'] ?? null;

// Insert query
$sql = "INSERT INTO logbook (
    Student_ID, Logbook_Date, Week_Number, Report_Period, Company_Name,
    Training_Period, Company_Supervisor_Name, Faculty_Supervisor_Name,
    Tasks_Done, Reflections, Supervisor_Remarks, Supervisor_Signed, Supervisor_Viewed
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ssissssssss",
    $studentID,
    $logbookDate,
    $weekNumber,
    $reportPeriod,
    $companyName,
    $trainingPeriod,
    $companySupervisor,
    $facultySupervisor,
    $tasksDone,
    $reflections,
    $supervisorRemarks
);

if ($stmt->execute()) {
    header("Location: ../stuLogBooks.php?success=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}
?>
