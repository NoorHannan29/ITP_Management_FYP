<?php
session_start(); // Start the session

$host = "localhost";
$username = "root";
$password = "";
$database = "itp_system";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_POST['userId'];
$passwordInput = $_POST['password'];

$sql = "SELECT * FROM Student WHERE Student_ID = ? AND Password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $userId, $passwordInput);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Store data in session
    $_SESSION['student_id'] = $user['Student_ID'];
    $_SESSION['student_name'] = $user['Student_Name'];
    $_SESSION['student_program'] = $user['Student_Program'];
    
    // Redirect to main menu
    header("Location: ../main.php");
    exit();
} else {
    echo "Invalid ID or password!";
}

$conn->close();
?>
