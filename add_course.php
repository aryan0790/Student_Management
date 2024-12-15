<?php
// add_course.php
require '../php/db_connection.php';
session_start(); // Start the session

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = trim($_POST['course_name']);
    $course_description = trim($_POST['course_description']);

    // Server-side validation
    if (empty($course_name) || empty($course_description)) {
        $message = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO courses (course_name, course_description) VALUES (:course_name, :course_description)");
            $stmt->execute(['course_name' => $course_name, 'course_description' => $course_description]);

            // Add notification only if the user is logged in
            if (isset($_SESSION['user_id'])) {
                addNotification($pdo, 'Course Added', "Course $course_name has been added successfully.", $_SESSION['user_id']);
            }

            header("Location: ../php/admin_dashboard.php?success=Course+added+successfully");
            exit();
        } catch (Exception $e) {
            $message = "Failed to add course: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Function to add a notification
function addNotification($pdo, $title, $message, $created_by) {
    $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, created_at) VALUES (:title, :message, :created_by, NOW())");
    $stmt->execute(['title' => $title, 'message' => $message, 'created_by' => $created_by]);
}
?>

<!-- Add Course Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>
<body>
    <div class="container">
        <h1>Add Course</h1>
        <?php if ($message): ?>
            <p class="alert alert-danger"><?= $message; ?></p>
        <?php endif; ?>
        <form method="POST" class="form">
            <label>Course Name:</label>
            <input type="text" name="course_name" required><br>

            <label>Course Description:</label>
            <textarea name="course_description" required></textarea><br>

            <button type="submit">Add Course</button>
        </form>
    </div>
</body>
</html>
