<?php
require_once '../../php_files/db_connect.php';

if (!isset($_GET['student_id']) || !isset($_GET['supervisor_id'])) {
    die("Missing parameters.");
}

$student_id = $_GET['student_id'];
$supervisor_id = $_GET['supervisor_id'];

$stmt = $conn->prepare("UPDATE student SET Supervisor_ID = ? WHERE Student_ID = ?");
$stmt->bind_param("ss", $supervisor_id, $student_id);
$stmt->execute();

header("Location: ../comStudentAssignment.php?supervisor_id=" . urlencode($supervisor_id));
exit();
?>
