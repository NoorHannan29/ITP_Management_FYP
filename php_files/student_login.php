<?php
session_start();
$host = "localhost";
$db_user = "root";  // or your DB username
$db_pass = "";      // or your DB password
$db_name = "itp_system";  // change to your DB name

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_POST['userId'];
$password = $_POST['password'];

$sql = "SELECT * FROM student WHERE Student_ID = ? AND Student_Password = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $userId, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $_SESSION['userId'] = $userId;
    echo "<script>
        alert('Welcome!');
        setTimeout(function() {
            window.location.href = '../main.html';}, 100); 
        </script>";
} 
else {
    echo "<script>alert('Invalid ID or Password.'); 
    window.location.href = '../index.html';</script>";
}

$stmt->close();
$conn->close();
?>
