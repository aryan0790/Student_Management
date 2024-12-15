<?php
// delete_student.php
session_start();
require '../php/db_connection.php';

if (isset($_GET['student_id']) && is_numeric($_GET['student_id'])) {
    $student_id = intval($_GET['student_id']); // Sanitize input

    // Start a transaction
    $pdo->beginTransaction();

    try {
        // Fetch user_id associated with the student
        $stmt = $pdo->prepare("SELECT user_id, first_name, last_name FROM students WHERE student_id = :student_id");
        $stmt->execute(['student_id' => $student_id]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            // Delete from users table
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $student['user_id']]);
            
            // Delete from students table
            $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = :student_id");
            $stmt->execute(['student_id' => $student_id]);

            // Notification logic
            if (isset($_SESSION['user_id'])) {
                $notification_title = "Student Deleted";
                $notification_message = "Student {$student['first_name']} {$student['last_name']} deleted successfully.";
                $created_by = $_SESSION['user_id'];

                $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, related_entity, related_id, created_at) VALUES (?, ?, ?, 'student', ?, NOW())");
                $stmt->execute([$notification_title, $notification_message, $created_by, $student_id]);
            }

            // Commit the transaction
            $pdo->commit();

            // Redirect with success message
            header("Location: ../php/admin_dashboard.php?success=Student+deleted+successfully");
            exit();
        } else {
            throw new Exception("Student not found.");
        }
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $pdo->rollBack();
        header("Location: ../php/admin_dashboard.php?error=Failed+to+delete+student");
        exit();
    }
} else {
    echo "Invalid student ID!";
    exit();
}
?>
