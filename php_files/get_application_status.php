<?php
session_start();
require_once("db_connect.php");

header('Content-Type: application/json');

if (!isset($_SESSION['student_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
}

$studentID = trim($_SESSION['student_id']);

$sql = "SELECT * FROM applications WHERE Student_ID = ? ORDER BY Submitted_At DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $studentID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'Application_Status' => $row['Application_Status'],
            'Internship_Start_Date' => $row['Internship_Start_Date'],
            'Internship_End_Date' => $row['Internship_End_Date'],
            'Allowance_Amount' => $row['Allowance_Amount'],
            'Job_Description' => $row['Job_Description'],
            'Company_Name' => $row['Company_Name'],
            'Company_Address' => $row['Company_Address'],
            'Company_State' => $row['Company_State'],
            'Company_Contact_Name' => $row['Company_Contact_Name'],
            'Company_Designation' => $row['Company_Designation'],
            'Company_Phone' => $row['Company_Phone'],
            'Company_Email' => $row['Company_Email'],
            'Company_Website' => $row['Company_Website']
        ]
    ]);
} else {
    echo json_encode(['status' => 'no_application', 'message' => 'No application found.']);
}
?>
