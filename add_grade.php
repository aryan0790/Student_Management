<?php
require '../php/db_connection.php';
session_start(); // Start session to use session variables

// Handle Add Grade
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_grade'])) {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade = strtoupper(trim($_POST['grade'])); // Convert grade to uppercase and trim whitespace

    // Validate grade (assuming valid grades are A, B, C, D, F)
    $valid_grades = ['A', 'B', 'C', 'D', 'F'];
    if (!in_array($grade, $valid_grades)) {
        header("Location: ../php/admin_dashboard.php?error=Invalid+grade");
        exit();
    }

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Insert or update the grade record
        $stmt = $pdo->prepare("INSERT INTO grades (student_id, course_id, grade) 
                               VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE grade = VALUES(grade)");
        $stmt->execute([$student_id, $course_id, $grade]);

        // Fetch student's name for notification
        $studentQuery = $pdo->prepare("SELECT first_name, last_name FROM students WHERE student_id = ?");
        $studentQuery->execute([$student_id]);
        $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

        if ($student && isset($_SESSION['user_id'])) {
            // Add a notification for the action
            $first_name = $student['first_name'];
            $last_name = $student['last_name'];
            $message = "Grade '$grade' for $first_name $last_name has been added.";
            addNotification($pdo, 'Grade Added', $message, $_SESSION['user_id']);
        }

        // Commit the transaction
        $pdo->commit();

        // Redirect with success message
        header("Location: ../php/admin_dashboard.php?success=Grade+added+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        $errorMessage = "Failed to add grade: " . htmlspecialchars($e->getMessage());
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
