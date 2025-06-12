<?php
header("Content-Type: application/json"); 

require_once '../../php_files/db_connect.php';

// Read JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($input['appID']) || !isset($input['reason'])) {
    echo json_encode([
        "success" => false,
        "message" => "Missing application ID or denial reason"
    ]);
    exit();
}

$appID = $input['appID'];
$reason = trim($input['reason']);

// Update the application
$sql = "UPDATE applications 
        SET Application_Status = 'Denied', Remarks = ? 
        WHERE Application_ID = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "SQL Error: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("si", $reason, $appID);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Application denied successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Failed to deny application: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
