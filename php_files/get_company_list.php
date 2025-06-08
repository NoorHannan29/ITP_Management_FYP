<?php
require_once 'db_connect.php';

$sql = "SELECT * FROM company_list";
$result = $conn->query($sql);

$company = [];
while ($row = $result->fetch_assoc()) {
    $company[] = $row;
}

echo json_encode($company);
$conn->close();
?>
