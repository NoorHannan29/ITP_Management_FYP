<?php
require_once 'db_connect.php';

// Sanitize and assign POST data
$name = $_POST['name'];
$student_id = $_POST['student_id'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$program = $_POST['program'];
$contact = $_POST['contact'];
$specialisation = $_POST['specialisation'];

// Check password match
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}


$stmt = $conn->prepare("INSERT INTO Student 
(Student_ID, Student_Name, Student_email, Student_Phone, Student_Program, Student_Intern_Status, Password, Student_Specialisation) 
VALUES (?, ?, ?, ?, ?, 'Not Started', ?, ?)");

$stmt->bind_param("sssssss", $student_id, $name, $email, $contact, $program, $password, $specialisation);

// Execute and check result
if ($stmt->execute()) {
    echo "Registration successful!";
    header("Location: ../index.html");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
