
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing ID']);
    exit;
}

$announcementId = intval($_GET['id']);

// Replace with your own DB credentials
$conn = new mysqli("localhost", "root", "", "itp_system");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM Announcements WHERE Announcement_ID = ?");
$stmt->bind_param("i", $announcementId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Announcement not found']);
    exit;
}

$data = $result->fetch_assoc();
echo json_encode($data);
?>
