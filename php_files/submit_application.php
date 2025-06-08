<?php
session_start();
require_once 'db_connect.php'; // adjust path if needed

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: ../index.html");
    exit();
}

// Sanitize & retrieve values from form
$student_id = $_SESSION['student_id'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$company_name = $_POST['company_name'];
$company_address = $_POST['company_address'];
$company_state = $_POST['company_state'];
$contact_name = $_POST['company_contact'];
$designation = $_POST['company_designation'] ?? null;
$phone = $_POST['company_phone'] ?? null;
$email = $_POST['company_email'] ?? null;
$website = $_POST['company_website'] ?? null;
$job_description = $_POST['job_description'] ?? null;
$allowance = $_POST['allowance'] !== "" ? $_POST['allowance'] : null;

// Insert query
$sql = "INSERT INTO applications (
    Student_ID,
    Internship_Start_Date,
    Internship_End_Date,
    Allowance_Amount,
    Job_Description,
    Company_Name,
    Company_Address,
    Company_State,
    Company_Contact_Name,
    Company_Designation,
    Company_Phone,
    Company_Email,
    Company_Website
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "sssssssssssss",
    $student_id,
    $start_date,
    $end_date,
    $allowance,
    $job_description,
    $company_name,
    $company_address,
    $company_state,
    $contact_name,
    $designation,
    $phone,
    $email,
    $website
);

if ($stmt->execute()) {
    echo "<script>alert('Application submitted successfully!'); window.location.href = 'main.php';</script>";
} else {
    echo "<script>alert('Error: " . addslashes($stmt->error) . "'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
