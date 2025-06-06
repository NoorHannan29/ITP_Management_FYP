<?php
$host = 'localhost';
$db   = 'itp_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM company_list";
$result = $conn->query($sql);

$company = [];
while ($row = $result->fetch_assoc()) {
    $company[] = $row;
}

echo json_encode($company);
$conn->close();
?>
