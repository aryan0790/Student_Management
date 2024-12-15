<?php 
// update_student.php
session_start();
require '../php/db_connection.php';

$message = '';

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Fetch student details
    $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = :student_id");
    $stmt->execute(['student_id' => $student_id]);
    $student = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate inputs
        $first_name = trim(htmlspecialchars($_POST['first_name']));
        $last_name = trim(htmlspecialchars($_POST['last_name']));
        $dob = $_POST['dob']; // Validate date format if necessary
        $phone = trim(htmlspecialchars($_POST['phone']));
        $course_id = $_POST['course_id'] ?? null;

        // Update student information
        $stmt = $pdo->prepare("UPDATE students SET first_name = :first_name, last_name = :last_name, dob = :dob, phone = :phone, course_id = :course_id WHERE student_id = :student_id");
        $stmt->execute([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'dob' => $dob,
            'phone' => $phone,
            'course_id' => $course_id,
            'student_id' => $student_id,
        ]);

        // Notification logic
        if (isset($_SESSION['user_id'])) {
            $notification_title = "Student Updated";
            $notification_message = "Student {$first_name} {$last_name} updated successfully.";
            $created_by = $_SESSION['user_id'];

            $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, related_entity, related_id, created_at) VALUES (?, ?, ?, 'student', ?, NOW())");
            $stmt->execute([$notification_title, $notification_message, $created_by, $student_id]);
        }

        header("Location: ../php/admin_dashboard.php?success=Student+updated+successfully");
        exit();
    }
} else {
    header("Location: ../php/error.php?error=Invalid+student+ID");
    exit();
}
?>

<!-- Update Student Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Student</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>
<body>
    <div class="container">
        <h1>Update Student</h1>
        <form method="POST" class="form">
            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required><br>

            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required><br>

            <label>Date of Birth:</label>
            <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob']); ?>" required><br>

            <label>Phone:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>"><br>

            <label>Course (optional):</label>
            <select name="course_id">
                <option value="">Select a course</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM courses");
                while ($course = $stmt->fetch()) {
                    $selected = ($course['course_id'] == $student['course_id']) ? 'selected' : '';
                    echo "<option value='{$course['course_id']}' $selected>{$course['course_name']}</option>";
                }
                ?>
            </select><br>

            <button type="submit">Update Student</button>
        </form>
    </div>
</body>
</html>
