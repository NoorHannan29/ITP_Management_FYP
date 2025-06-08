<?php
session_start();
require_once("db_connect.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: index.html");
    exit();
}

$studentID = $_SESSION['student_id'];
$logDate = $_POST['log_date'];
$logbookContent = $_POST['logbook_content'];

$stmt = $conn->prepare("INSERT INTO logbook (Student_ID, Logbook_Date, Supervisor_Viewed, Logbook_Content) VALUES (?, ?, 0, ?)");
$stmt->bind_param("sss", $studentID, $logDate, $logbookContent);
$stmt->execute();

header("Location: ../stuLogBooks.php");
exit();
