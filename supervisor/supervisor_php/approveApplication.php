<?php
ob_start();
header("Content-Type: application/json");
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

require_once '../../php_files/db_connect.php';

$raw = file_get_contents("php://input");
file_put_contents('approve_debug.txt', "RAW: $raw\n", FILE_APPEND);

$input = json_decode($raw, true);
file_put_contents('approve_debug.txt', print_r($input, true), FILE_APPEND);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["success" => false, "message" => "Invalid JSON: " . json_last_error_msg()]);
    exit();
}

if (!isset($input['appID'])) {
    echo json_encode(["success" => false, "message" => "Missing application ID"]);
    exit();
}

$appID = $input['appID'];

// Step 1: Get student's specialization and ID
$sql = "SELECT a.Student_ID, s.student_specialisation
        FROM applications a 
        JOIN student s ON s.Student_ID = a.Student_ID 
        WHERE a.Application_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Application not found"]);
    exit();
}

$row = $result->fetch_assoc();
$studentID = $row['Student_ID'];
$specialization = $row['student_specialisation'];

$stmt->close();

// Step 2: Find matching supervisors and select one with the fewest students
$sql = "SELECT Supervisor_ID FROM supervisor WHERE LOWER(Preferred_Specialization) = LOWER(?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $specialization);
$stmt->execute();
$supervisors = $stmt->get_result();

if ($supervisors->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "No supervisors match this specialization"]);
    exit();
}

$supervisorOptions = [];
while ($row = $supervisors->fetch_assoc()) {
    $supID = $row['Supervisor_ID'];

    $countSql = "SELECT COUNT(*) AS student_count FROM student WHERE Supervisor_ID = ?";
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param("i", $supID);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $countRow = $countResult->fetch_assoc();

    $supervisorOptions[] = [
        "Supervisor_ID" => $supID,
        "Student_Count" => $countRow['student_count']
    ];

    $countStmt->close();
}
$stmt->close();

// Step 3: Pick supervisor with the fewest students
file_put_contents('approve_debug.txt', print_r($supervisorOptions, true), FILE_APPEND);

usort($supervisorOptions, function($a, $b) {
    return $a['Student_Count'] - $b['Student_Count'];
});

$assignedSupervisorID = $supervisorOptions[0]['Supervisor_ID'];

// Step 4: Approve application and assign supervisor
$updateApplicationSql = "UPDATE applications SET Application_Status = 'Approved' WHERE Application_ID = ?";
$updateAppStmt = $conn->prepare($updateApplicationSql);
$updateAppStmt->bind_param("i", $appID); 
$updateAppStmt->execute();
$updateAppStmt->close();

if (empty($studentID)) {
    echo json_encode(["success" => false, "message" => "Invalid student ID"]);
    exit();
}

$assignSql = "UPDATE student SET Supervisor_ID = ? WHERE Student_ID = ?";
$assignStmt = $conn->prepare($assignSql);
$assignStmt->bind_param("is", $assignedSupervisorID, $studentID); 
$assignStmt->execute();
$assignStmt->close();

// Set student allowance based on application value
$allowanceSql = "SELECT Allowance_Amount FROM applications WHERE Application_ID = ?";
$allowanceStmt = $conn->prepare($allowanceSql);
$allowanceStmt->bind_param("i", $appID);
$allowanceStmt->execute();
$allowanceResult = $allowanceStmt->get_result();

if ($allowanceResult->num_rows > 0) {
    $allowanceRow = $allowanceResult->fetch_assoc();
    $allowance = $allowanceRow['Allowance_Amount'];

    $updateAllowanceSql = "UPDATE student SET student_allowance = ? WHERE Student_ID = ?";
    $updateAllowanceStmt = $conn->prepare($updateAllowanceSql);
    $updateAllowanceStmt->bind_param("ds", $allowance, $studentID); // d = double, s = string
    $updateAllowanceStmt->execute();
    $updateAllowanceStmt->close();
}
$allowanceStmt->close();

//INSERT IDENTITY TYPE
$identitySql = "SELECT Student_Identity_Type, Student_Identity_Value FROM applications WHERE Application_ID = ?";
$identityStmt = $conn->prepare($identitySql);
$identityStmt->bind_param("i", $appID);
$identityStmt->execute();
$identityResult = $identityStmt->get_result();

if ($identityResult->num_rows > 0) {
    $identityRow = $identityResult->fetch_assoc();
    $identityType = $identityRow['Student_Identity_Type'];
    $identityValue = $identityRow['Student_Identity_Value'];

    // Update student table
    $updateIdentitySql = "UPDATE student SET Student_Identity_Type = ?, Student_Identity_Value = ? WHERE Student_ID = ?";
    $updateIdentityStmt = $conn->prepare($updateIdentitySql);
    $updateIdentityStmt->bind_param("sss", $identityType, $identityValue, $studentID);
    $updateIdentityStmt->execute();
    $updateIdentityStmt->close();
}
$identityStmt->close();

$conn->close();

echo json_encode(["success" => true, "message" => "Application approved and supervisor assigned"]);
exit();
