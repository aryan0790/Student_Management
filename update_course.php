<?php
// update_course.php
session_start();
require '../php/db_connection.php';

$message = '';

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Fetch course details
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = :course_id");
    $stmt->execute(['course_id' => $course_id]);
    $course = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate inputs
        $course_name = trim(htmlspecialchars($_POST['course_name']));
        $course_description = trim(htmlspecialchars($_POST['course_description']));

        $stmt = $pdo->prepare("UPDATE courses SET course_name = :course_name, course_description = :course_description WHERE course_id = :course_id");
        $stmt->execute([
            'course_name' => $course_name,
            'course_description' => $course_description,
            'course_id' => $course_id,
        ]);

        // Notification logic
        if (isset($_SESSION['user_id'])) {
            $notification_title = "Course Updated";
            $notification_message = "Course {$course_name} updated successfully.";
            $created_by = $_SESSION['user_id'];

            $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, related_entity, related_id, created_at) VALUES (?, ?, ?, 'course', ? , NOW())");
            $stmt->execute([$notification_title, $notification_message, $created_by, $course_id]);
        }

        header("Location: ../php/admin_dashboard.php?success=Course+updated+successfully");
        exit();
    }
} else {
    header("Location: ../php/admin_dashboard.php?error=Invalid+course+ID");
    exit();
}
?>

<!-- Update Course Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Course</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>
<body>
    <div class="container">
        <h1>Update Course</h1>
        <form method="POST" class="form">
            <label>Course Name:</label>
            <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required><br>

            <label>Course Description:</label>
            <textarea name="course_description"><?php echo htmlspecialchars($course['course_description']); ?></textarea><br>

            <button type="submit">Update Course</button>
        </form>
    </div>
</body>
</html>
