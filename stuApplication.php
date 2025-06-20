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

$check_sql = "SELECT Application_ID FROM applications WHERE Student_ID = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("s", $student_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    echo "<script>alert('You have already submitted an application. Redirecting to status page.'); window.location.href='stuApplStat.html';</script>";
    exit();
}
$check_stmt->close();

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
    $identityType = $_POST['identity_type'] ?? null;
    $identityValue = $_POST['identity_value'] ?? null;


    $sql = "INSERT INTO applications 
            (Student_ID, Internship_Start_Date, Internship_End_Date, Allowance_Amount, Job_Description,
             Company_Name, Company_Address, Company_State, Company_Contact_Name, Company_Designation,
             Company_Phone, Company_Email, Company_Website, Student_Identity_Type, Student_Identity_Value)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param(
            "sssssssssssssss",
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
            $company_website,
            $identityType,
            $identityValue
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

            <!-- Identity Type Dropdown -->
            <label for="identity_type">Identity Type:</label>
            <select name="identity_type" id="identity_type" required>
            <option value="">-- Select Type --</option>
            <option value="NRIC">NRIC</option>
            <option value="Passport">Passport</option>
            <option value="Other">Other</option>
            </select>

            <!-- NRIC Fields (Shown only when NRIC is selected) -->
            <div id="nric-fields" style="display: none; margin-top: 10px;">
            <label>NRIC:</label>
            <div style="display: flex; gap: 5px; align-items: center;">
                <input type="text" id="nric_part1" maxlength="6" placeholder="xxxxxx" pattern="\d{6}">
                - 
                <input type="text" id="nric_part2" maxlength="2" placeholder="xx" pattern="\d{2}">
                - 
                <input type="text" id="nric_part3" maxlength="4" placeholder="xxxx" pattern="\d{4}">
            </div>
            </div>

            <!-- Standard Identity Value (Hidden when NRIC selected) -->
            <div id="identity-value-wrapper" style="margin-top: 10px;">
            <label for="identity_value">Identity Value:</label>
            <input type="text" name="identity_value" id="identity_value" placeholder="e.g. Passport No" required>
            </div>



            <!-- Internship Fields -->
            <label for="start_date">Internship Start Date:</label>
            <input type="date" name="start_date" required placeholder="Internship Start Date">
            <label for="end_date">Internship End Date:</label>
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
        <script>
        document.addEventListener("DOMContentLoaded", function () {
        const identityType = document.getElementById("identity_type");
        const nricFields = document.getElementById("nric-fields");
        const identityValueWrapper = document.getElementById("identity-value-wrapper");
        const identityValueInput = document.getElementById("identity_value");
        const form = document.querySelector("form");

        function toggleIdentityFields() {
            if (identityType.value === "NRIC") {
            nricFields.style.display = "block";
            identityValueWrapper.style.display = "none";
            identityValueInput.removeAttribute("required");
            } else {
            nricFields.style.display = "none";
            identityValueWrapper.style.display = "block";
            identityValueInput.setAttribute("required", "required");
            }
        }

        identityType.addEventListener("change", toggleIdentityFields);

        form.addEventListener("submit", function () {
            if (identityType.value === "NRIC") {
            const p1 = document.getElementById("nric_part1").value;
            const p2 = document.getElementById("nric_part2").value;
            const p3 = document.getElementById("nric_part3").value;
            const merged = `${p1}-${p2}-${p3}`;
            identityValueInput.value = merged;
            }
        });
        });
        </script>
</div>
</body>
</html>


    

