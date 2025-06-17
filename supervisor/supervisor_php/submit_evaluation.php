<?php
session_start();
require_once '../../php_files/db_connect.php';

if (!isset($_SESSION['supervisor_id'])) {
    header("Location: ../index.html?error=1");
    exit();
}

$supervisor_id = $_SESSION['supervisor_id'];

// Get and sanitize inputs
$student_id = $_POST['student_id'];
$report_quality = $_POST['report_quality'];
$skill_application = $_POST['skill_application'];
$timely_reporting = $_POST['timely_reporting'];
$company_score = $_POST['company_score'];
$company_presentation = $_POST['company_presentation'];
$faculty_presentation = $_POST['faculty_presentation'];
$comments = $_POST['comments'];

// Determine PASS/FAIL
$status = (
    $report_quality >= 1 && $skill_application >= 1 && $timely_reporting >= 1 &&
    $company_score >= 10 &&
    $company_presentation >= 2 && $faculty_presentation >= 2
) ? 'PASS' : 'FAIL';

// Insert evaluation
$sql = "INSERT INTO evaluations 
    (Student_ID, Supervisor_ID, Report_Quality, Skill_Application, Timely_Reporting, 
     Company_Supervisor_Score, Company_Presentation_Score, Faculty_Presentation_Score,
     Comments, Status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("siiiiiiiss", $student_id, $supervisor_id, $report_quality, $skill_application,
    $timely_reporting, $company_score, $company_presentation, $faculty_presentation,
    $comments, $status);

if ($stmt->execute()) {
    header("Location: ../supEvaluationPage.php?success=1");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
