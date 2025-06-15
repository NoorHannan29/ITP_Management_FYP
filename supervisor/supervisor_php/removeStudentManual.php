<?php
require_once '../../php_files/db_connect.php';

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    $sql = "UPDATE student SET Supervisor_ID = NULL WHERE Student_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $student_id);

    if ($stmt->execute()) {
        header("Location: ../comStudentAssignment.php?supervisor_id=" . urlencode($_GET['supervisor_id']) . "&status=removed");
        exit();
    } else {
        echo "Error removing student.";
    }
} else {
    echo "No student ID provided.";
}
?>
