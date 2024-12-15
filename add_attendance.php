<?php
require '../php/db_connection.php';
session_start(); // Start the session

// Handle Add Attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_attendance'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Insert the attendance record
        $stmt = $pdo->prepare("INSERT INTO attendance (student_id, course_id, attendance_date, status) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$student_id, $course_id, $attendance_date, $status]);

        // Fetch the student's name for notification purposes
        $studentQuery = $pdo->prepare("SELECT first_name, last_name FROM students WHERE student_id = ?");
        $studentQuery->execute([$student_id]);
        $student = $studentQuery->fetch(PDO::FETCH_ASSOC);
        
        if ($student && isset($_SESSION['user_id'])) {
            // Add notification if the user is logged in
            $first_name = $student['first_name'];
            $last_name = $student['last_name'];
            $message = "Student $first_name $last_name's attendance has been added successfully.";
            addNotification($pdo, 'Attendance Added', $message, $_SESSION['user_id']);
        }

        // Commit the transaction
        $pdo->commit();

        // Redirect with success message
        header("Location: ../php/admin_dashboard.php?success=Attendance+added+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();

        // Display error message
        $errorMessage = "Failed to add attendance: " . htmlspecialchars($e->getMessage());
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
