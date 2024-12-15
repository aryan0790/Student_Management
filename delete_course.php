<?php
// delete_course.php
session_start();
require '../php/db_connection.php';

if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    // Start a transaction
    $pdo->beginTransaction();

    try {
        // Fetch course details for notification
        $stmt = $pdo->prepare("SELECT course_name FROM courses WHERE course_id = :course_id");
        $stmt->execute(['course_id' => $course_id]);
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete students associated with the course
        $stmt = $pdo->prepare("DELETE FROM students WHERE course_id = :course_id");
        $stmt->execute(['course_id' => $course_id]);

        // Delete the course
        $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = :course_id");
        $stmt->execute(['course_id' => $course_id]);

        // Notification logic
        if (isset($_SESSION['user_id'])) {
            $notification_title = "Course Deleted";
            $notification_message = "Course {$course['course_name']} deleted successfully.";
            $created_by = $_SESSION['user_id'];

            $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, related_entity, related_id, created_at) VALUES (?, ?, ?, 'course', ?, NOW())");
            $stmt->execute([$notification_title, $notification_message, $created_by, $course_id]);
        }

        // Commit the transaction
        $pdo->commit();

        header("Location: ../php/admin_dashboard.php?success=Course+deleted+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $pdo->rollBack();
        header("Location: ../php/admin_dashboard.php?error=Failed+to+delete+course");
        exit();
    }
} else {
    echo "Invalid course ID!";
    exit();
}
?>
