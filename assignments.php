<?php
// assignments.php
session_start();
require '../php/db_connection.php'; // Your PDO connection file

// Check user role; only allow admin access
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.html"); // Redirect to home if not admin
    exit;
}

// Define which section to display
$section = isset($_GET['section']) ? $_GET['section'] : 'assignments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Assignments</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css"> <!-- Include your CSS file -->
</head>
<body>
    <div class="container">
        <h1>Assignments Management</h1>
        <nav>
            <ul>
                <li><a href="assignments.php?section=assignments">Manage Assignments</a></li>
                <!-- Add more sections as needed -->
            </ul>
        </nav>

        <div class="content">
            <?php
            // Include the appropriate section based on the URL parameter
            if ($section === 'assignments') {
                include '../admin_dashboard_sections/assignments_section.php';
            }
            ?>
        </div>
    </div>
</body>
</html>
