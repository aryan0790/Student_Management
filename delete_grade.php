<?php
require '../php/db_connection.php';
session_start(); // Start session to use session variables

// Handle Delete Grade
if (isset($_GET['delete_id'])) {
    $grade_id = $_GET['delete_id'];

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Fetch student's name for notification before deletion
        $studentQuery = $pdo->prepare("SELECT s.first_name, s.last_name 
                                       FROM students s 
                                       JOIN grades g ON s.student_id = g.student_id 
                                       WHERE g.grade_id = ?");
        $studentQuery->execute([$grade_id]);
        $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

        // Delete the grade record
        $stmt = $pdo->prepare("DELETE FROM grades WHERE grade_id = ?");
        $stmt->execute([$grade_id]);

        if ($student && isset($_SESSION['user_id'])) {
            // Add a notification for the action
            $first_name = $student['first_name'];
            $last_name = $student['last_name'];
            $message = "Grade for $first_name $last_name has been deleted.";
            addNotification($pdo, 'Grade Deleted', $message, $_SESSION['user_id']);
        }

        // Commit the transaction
        $pdo->commit();

        // Redirect with success message
        header("Location: ../php/admin_dashboard.php?success=Grade+deleted+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        $errorMessage = "Failed to delete grade: " . htmlspecialchars($e->getMessage());
        header("Location: ../php/admin_dashboard.php?error=" . urlencode($errorMessage));
        exit();
    }
}

// Function to add a notification
function addNotification($pdo, $title, $message, $created_by) {
    $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, created_at) 
                           VALUES (:title, :message, :created_by, NOW())");
    $stmt->execute([
        'title' => $title,
        'message' => $message,
        'created_by' => $created_by
    ]);
}
?>
