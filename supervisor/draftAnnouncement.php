<?php
session_start();

if (!isset($_SESSION['supervisor_id']) || !isset($_SESSION['supervisor_name']) || $_SESSION['is_committee'] != 1) {
    header("Location: index.html?error=unauthorized");
    exit();
}

require_once '../php_files/db_connect.php';

$success = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $poster = $_SESSION['supervisor_name'];
    $timestamp = date('Y-m-d H:i:s');

    if (empty($title) || empty($content)) {
        $error = "Please fill in both title and content.";
    } else {
        $stmt = $conn->prepare("INSERT INTO announcements (Title, Content, Poster_Name, Timestamp) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $title, $content, $poster, $timestamp);
        
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Database error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Draft New Announcement</title>
    <link rel="stylesheet" href="supervisor_css/draftAnnouncement.css">
</head>
<body>
    <?php if ($success): ?>
        <div id="popup" class="popup-success">
            Announcement successfully posted. Redirecting, please wait...
        </div>
        <script>
            // Show popup for 2.5 seconds then redirect
            setTimeout(() => {
                document.getElementById("popup").style.opacity = '0';
            }, 2000);

            setTimeout(() => {
                window.location.href = "comAnnouncements.php";
            }, 2500);
        </script>
    <?php endif; ?>

    <div class="form-container">
        <h2>Draft New Announcement</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="title">Announcement Title</label>
            <input type="text" id="title" name="title" required>

            <label for="content">Content</label>
            <textarea id="content" name="content" rows="6" required></textarea>

            <button type="submit">Post Announcement</button>
        </form>
    </div>
</body>

</html>
