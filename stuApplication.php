<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    echo "<script>alert('Please login first to access this page.'); window.location.href = 'index.html';</script>";
    exit();
}

require_once './php_files/db_connect.php'; // Make sure this path is correct

// Auto-fill session data safely
$student_id = $_SESSION['student_id'] ?? '';
$student_name = $_SESSION['student_name'] ?? '';
$student_email = $_SESSION['student_email'] ?? '';
$student_phone = $_SESSION['student_phone'] ?? '';
$student_program = $_SESSION['student_program'] ?? '';

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name = $_POST['company_name'] ?? '';
    $company_address = $_POST['company_address'] ?? '';
    $company_state = $_POST['company_state'] ?? '';
    $company_contact_name = $_POST['company_contact_name'] ?? '';
    $company_designation = $_POST['company_designation'] ?? '';
    $company_phone = $_POST['company_phone'] ?? '';
    $company_email = $_POST['company_email'] ?? '';
    $company_website = $_POST['company_website'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $allowance = $_POST['allowance'] ?? '';
    $job_description = $_POST['job_description'] ?? '';

    $sql = "INSERT INTO applications 
            (Student_ID, Internship_Start_Date, Internship_End_Date, Allowance_Amount, Job_Description,
             Company_Name, Company_Address, Company_State, Company_Contact_Name, Company_Designation,
             Company_Phone, Company_Email, Company_Website)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            "sssssssssssss",
            $student_id,
            $start_date,
            $end_date,
            $allowance,
            $job_description,
            $company_name,
            $company_address,
            $company_state,
            $company_contact_name,
            $company_designation,
            $company_phone,
            $company_email,
            $company_website
        );

        if ($stmt->execute()) {
            echo "<script>alert('Application submitted successfully!'); window.location.href='main.php';</script>";
        } else {
            echo "<script>alert('Error submitting application. Please try again.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Database error.');</script>";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application for Industrial Training</title>
    <link rel="stylesheet" href="./student_css/main.css">
    <link rel="stylesheet" href="./student_css/stuApplication.css">
    <script src="./student_script/main.js" defer></script>
</head>
<body>
<header>
    <h1>MMU ITMS SYSTEM</h1>
</header>
<div class="main-layout">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">&#60; Hide</button>
        <div class="sidebar-content">
            <h3>Student Pages</h3>
            <ul class="sidebar-nav">
                <li><a href="main.php">Dashboard</a></li>
                <li><a href="stuAnnouncement.html">Announcements</a></li>
                <li><a href="stuProfile.html">Profile</a></li>
                <li><a href="stuApplication.php">Application For ITP</a></li>
                <li><a href="stuCompList.html">Company Listings</a></li>
                <li><a href="stuLogBooks.html">Logbook Management</a></li>
                <li><a href="stuGuidelines.html">Guidelines for ITP</a></li>
            </ul>
        </div>
    </div>

    <!-- Form Content -->
    <div class="content" id="content">
        <form class="registration-form" method="POST" action="stuApplication.php">
            <!-- Pre-filled Student Info -->
            <input type="text" name="student_name" value="<?php echo $student_name; ?>" readonly placeholder="Full Name">
            <input type="text" name="student_id" value="<?php echo $student_id; ?>" readonly placeholder="Student ID">
            <input type="email" name="student_email" value="<?php echo $student_email; ?>" readonly placeholder="Email">
            <input type="tel" name="student_phone" value="<?php echo $student_phone; ?>" readonly placeholder="Phone">
            <input type="text" name="student_program" value="<?php echo $student_program; ?>" readonly placeholder="Program">

            <!-- Internship Fields -->
            <input type="date" name="start_date" required placeholder="Internship Start Date">
            <input type="date" name="end_date" required placeholder="Internship End Date">
            <input type="number" name="allowance" step="0.01" placeholder="Allowance (optional)">
            <textarea name="job_description" placeholder="Job Description" rows="3"></textarea>

            <!-- Company Info -->
            <input type="text" name="company_name" required placeholder="Company Name">
            <textarea name="company_address" required placeholder="Company Address" rows="2"></textarea>
            <input type="text" name="company_state" required placeholder="Company State">
            <input type="text" name="company_contact_name" required placeholder="Contact Person Name">
            <input type="text" name="company_designation" placeholder="Designation">
            <input type="tel" name="company_phone" placeholder="Phone Number">
            <input type="email" name="company_email" placeholder="Email">
            <input type="url" name="company_website" placeholder="Website">

            <button type="submit">Submit Application</button>
        </form>
    </div>
</div>
</body>
</html>


    

