<?php
// Database credentials
$host = "localhost";
$username = "root";
$password = "";
$database = "itp_system";

// Connect to DB
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize and assign POST data
$name = $_POST['name'];
$student_id = $_POST['student_id'];
$email = $_POST['email'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$program = $_POST['program'];
$contact = $_POST['contact'];

// Check password match
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}


$stmt = $conn->prepare("INSERT INTO Student 
(Student_ID, Student_Name, Student_email, Student_Phone, Student_Program, Student_Intern_Status, Password) 
VALUES (?, ?, ?, ?, ?, 'Not Started', ?)");

$stmt->bind_param("ssssss", $student_id, $name, $email, $contact, $program, $password);

// Execute and check result
if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
