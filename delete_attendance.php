<?php
require '../php/db_connection.php';
session_start(); // Start the session

// Handle Delete Attendance
if (isset($_GET['delete_id'])) {
    $attendance_id = $_GET['delete_id'];

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Fetch attendance record to include student name in the notification
        $stmt = $pdo->prepare("SELECT s.first_name, s.last_name, c.course_name FROM attendance a 
                               JOIN students s ON a.student_id = s.student_id 
                               JOIN courses c ON a.course_id = c.course_id 
                               WHERE a.attendance_id = ?");
        $stmt->execute([$attendance_id]);
        $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$attendance) {
            throw new Exception('Attendance record not found');
        }

        // Delete the attendance record
        $stmt = $pdo->prepare("DELETE FROM attendance WHERE attendance_id = ?");
        $stmt->execute([$attendance_id]);

        // Add notification if user is logged in
        if (isset($_SESSION['user_id'])) {
            $message = "Attendance for student {$attendance['first_name']} {$attendance['last_name']} in course {$attendance['course_name']} was deleted.";
            addNotification($pdo, 'Attendance Deleted', $message, $_SESSION['user_id']);
        }

        // Commit transaction
        $pdo->commit();

        // Redirect with success message
        header('Location: ../php/admin_dashboard.php?success=Attendance+deleted');
        exit();
    } catch (Exception $e) {
        // Rollback transaction in case of error
        $pdo->rollBack();

        // Redirect with error message
        $errorMessage = "Failed to delete attendance: " . htmlspecialchars($e->getMessage());
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
