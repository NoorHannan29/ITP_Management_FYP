<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['supervisor_id']) || empty($_SESSION['is_committee'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once '../../php_files/db_connect.php';

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['appID'])) {
    echo json_encode(['success' => false, 'message' => 'Missing application ID.']);
    exit();
}

$app_id = $data['appID'];

// Step 1: Check if the application exists and is still pending
$check_sql = "SELECT Status FROM itp_applications WHERE ITP_Application_ID = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $app_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Application not found.']);
    exit();
}

$row = $check_result->fetch_assoc();
if ($row['Status'] !== 'Pending') {
    echo json_encode(['success' => false, 'message' => 'Application already processed.']);
    exit();
}
$check_stmt->close();

// Step 2: Approve the application
$update_sql = "UPDATE itp_applications SET Status = 'Approved', Decision_Date = NOW() WHERE ITP_Application_ID = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("i", $app_id);

if ($update_stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'ITP application approved.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update application.']);
}

$update_stmt->close();
$conn->close();
?>
