<?php
require '../php/db_connection.php';
session_start(); // Start the session

// Check if the attendance ID is provided
if (!isset($_GET['attendance_id'])) { // corrected to check for 'attendance_id' instead of 'user_id'
    header('Location: ../php/admin_dashboard.php?error=No+attendance+ID+provided');
    exit();
}

$attendance_id = $_GET['attendance_id'];

// Fetch the existing attendance record
$stmt = $pdo->prepare("SELECT student_id, course_id, attendance_date, status FROM attendance WHERE attendance_id = ?");
$stmt->execute([$attendance_id]);
$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attendance) {
    header('Location: ../php/admin_dashboard.php?error=Attendance+record+not+found');
    exit();
}

// Handle Update Attendance
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];

    try {
        // Start transaction
        $pdo->beginTransaction();

        // Update the attendance record
        $stmt = $pdo->prepare("UPDATE attendance SET student_id = ?, course_id = ?, attendance_date = ?, status = ? WHERE attendance_id = ?");
        $stmt->execute([$student_id, $course_id, $attendance_date, $status, $attendance_id]);

        // Fetch the student's name for the notification message
        $studentQuery = $pdo->prepare("SELECT first_name, last_name FROM students WHERE student_id = ?");
        $studentQuery->execute([$student_id]);
        $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

        if ($student && isset($_SESSION['user_id'])) {
            // Add notification if the user is logged in
            $first_name = $student['first_name'];
            $last_name = $student['last_name'];
            $message = "Attendance for student $first_name $last_name has been updated.";
            addNotification($pdo, 'Attendance Updated', $message, $_SESSION['user_id']);
        }

        // Commit transaction
        $pdo->commit();

        // Redirect with success message
        header('Location: ../php/admin_dashboard.php?success=Attendance+updated');
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();

        // Redirect with error message
        $errorMessage = "Failed to update attendance: " . htmlspecialchars($e->getMessage());
        header("Location: ../php/admin_dashboard.php?error=" . urlencode($errorMessage));
        exit();
    }
}

// Function to add notification
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendance</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>
<body>
    <h2>Edit Attendance</h2>

    <form action="" method="POST">
        <label for="student">Student:</label>
        <select name="student_id" required>
            <?php
            // Fetch students
            $studentsQuery = $pdo->query("SELECT student_id, first_name, last_name FROM students");
            $students = $studentsQuery->fetchAll(PDO::FETCH_ASSOC);
            foreach ($students as $student) {
                $selected = $student['student_id'] == $attendance['student_id'] ? 'selected' : '';
                echo "<option value='{$student['student_id']}' {$selected}>{$student['first_name']} {$student['last_name']}</option>";
            }
            ?>
        </select>

        <label for="course">Course:</label>
        <select name="course_id" required>
            <?php
            // Fetch courses
            $coursesQuery = $pdo->query("SELECT course_id, course_name FROM courses");
            $courses = $coursesQuery->fetchAll(PDO::FETCH_ASSOC);
            foreach ($courses as $course) {
                $selected = $course['course_id'] == $attendance['course_id'] ? 'selected' : '';
                echo "<option value='{$course['course_id']}' {$selected}>{$course['course_name']}</option>";
            }
            ?>
        </select>

        <label for="attendance_date">Date:</label>
        <input type="date" name="attendance_date" value="<?php echo $attendance['attendance_date']; ?>" required>

        <label for="status">Status:</label>
        <select name="status" required>
            <option value="present" <?php echo $attendance['status'] === 'present' ? 'selected' : ''; ?>>Present</option>
            <option value="absent" <?php echo $attendance['status'] === 'absent' ? 'selected' : ''; ?>>Absent</option>
            <option value="late" <?php echo $attendance['status'] === 'late' ? 'selected' : ''; ?>>Late</option>
        </select>

        <button type="submit">Update Attendance</button>
    </form>
</body>
</html>

