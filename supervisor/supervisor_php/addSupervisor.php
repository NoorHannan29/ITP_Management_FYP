<?php
require_once '../../php_files/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $specialization = $_POST['specialisation'];
    $phone = $_POST['phone'];
    $isCommittee = isset($_POST['isCommittee']) ? 1 : 0;
    $committeeID = $isCommittee ? $_POST['committee_id'] : null;
    $role = $isCommittee ? $_POST['role'] : null;

    $stmt = $conn->prepare("INSERT INTO supervisor (supervisor_name, supervisor_email, password, Preferred_Specialization, Supervisor_Phone, Committee_ID, Committee_Role) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $email, $password, $specialization, $phone, $committeeID, $role);

    if ($stmt->execute()) {
        header("Location: ../comSupervisorList.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
