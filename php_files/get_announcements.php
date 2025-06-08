<?php
require_once 'db_connect.php';

$sql = "SELECT * FROM announcements ORDER BY Timestamp DESC";
$result = $conn->query($sql);

$announcements = [];
while ($row = $result->fetch_assoc()) {
    $announcements[] = $row;
}

echo json_encode($announcements);
$conn->close();
?>
