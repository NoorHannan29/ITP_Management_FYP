<?php
session_start(); // Start the session

require_once '../../php_files/db_connect.php'; // Correct path to db_connect

$userId = $_POST['userId'];
$passwordInput = $_POST['password'];

$sql = "SELECT * FROM supervisor WHERE Supervisor_ID = ? AND password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $userId, $passwordInput);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Store data in session
    $_SESSION['supervisor_id'] = $user['Supervisor_ID'];
    $_SESSION['supervisor_name'] = $user['Supervisor_Name'];
    $_SESSION['supervisor_email'] = $user['Supervisor_Email'];
    $_SESSION['supervisor_phone'] = $user['Supervisor_Phone'];
    
    // Redirect to supervisor dashboard
    header("Location: ../supITPStudents.php");
    exit();
} else {
    // Redirect back with error message
    header("Location: ../supLogin.html?error=1");
    exit();
}

$conn->close();
?>
