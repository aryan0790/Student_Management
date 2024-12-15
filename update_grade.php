<?php
require '../php/db_connection.php';
session_start(); // Start session to use session variables

// Fetch the grade details if grade_id is provided via GET
if (isset($_GET['grade_id'])) {
    $grade_id = $_GET['grade_id'];

    // Fetch the grade record from the database
    $gradeQuery = $pdo->prepare("SELECT g.grade_id, g.grade, s.first_name, s.last_name, c.course_name 
                                 FROM grades g 
                                 JOIN students s ON g.student_id = s.student_id 
                                 JOIN courses c ON g.course_id = c.course_id 
                                 WHERE g.grade_id = ?");
    $gradeQuery->execute([$grade_id]);
    $gradeRecord = $gradeQuery->fetch(PDO::FETCH_ASSOC);

    if (!$gradeRecord) {
        // Redirect if the grade record is not found
        header("Location: ../php/admin_dashboard.php?error=Grade+record+not+found");
        exit();
    }
}

// Handle Update Grade
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grade_id = $_POST['grade_id'];
    $grade = $_POST['grade'];

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Update the grade record
        $stmt = $pdo->prepare("UPDATE grades SET grade = ? WHERE grade_id = ?");
        $stmt->execute([$grade, $grade_id]);

        // Fetch student's name for notification
        $studentQuery = $pdo->prepare("SELECT s.first_name, s.last_name 
                                       FROM students s 
                                       JOIN grades g ON s.student_id = g.student_id 
                                       WHERE g.grade_id = ?");
        $studentQuery->execute([$grade_id]);
        $student = $studentQuery->fetch(PDO::FETCH_ASSOC);

        if ($student && isset($_SESSION['user_id'])) {
            // Add a notification for the action
            $first_name = $student['first_name'];
            $last_name = $student['last_name'];
            $message = "Grade for $first_name $last_name has been updated to '$grade'.";
            addNotification($pdo, 'Grade Updated', $message, $_SESSION['user_id']);
        }

        // Commit the transaction
        $pdo->commit();

        // Redirect with success message
        header("Location: ../php/admin_dashboard.php?success=Grade+updated+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        $errorMessage = "Failed to update grade: " . htmlspecialchars($e->getMessage());
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Grade</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>

<body>
    <h2>Update Grade</h2>

    <?php if (isset($gradeRecord)): ?>
        <!-- Update Grade Form -->
        <form action="" method="POST">
            <input type="hidden" name="grade_id" value="<?php echo htmlspecialchars($gradeRecord['grade_id']); ?>">

            <label for="student">Student:</label>
            <input type="text" id="student" name="student" value="<?php echo htmlspecialchars($gradeRecord['first_name'] . ' ' . $gradeRecord['last_name']); ?>" disabled>

            <label for="course">Course:</label>
            <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($gradeRecord['course_name']); ?>" disabled>

            <label for="grade">Grade:</label>
            <select name="grade" id="grade" required>
                <option value="A" <?php echo ($gradeRecord['grade'] == 'A') ? 'selected' : ''; ?>>A</option>
                <option value="B" <?php echo ($gradeRecord['grade'] == 'B') ? 'selected' : ''; ?>>B</option>
                <option value="C" <?php echo ($gradeRecord['grade'] == 'C') ? 'selected' : ''; ?>>C</option>
                <option value="D" <?php echo ($gradeRecord['grade'] == 'D') ? 'selected' : ''; ?>>D</option>
                <option value="E" <?php echo ($gradeRecord['grade'] == 'E') ? 'selected' : ''; ?>>E</option>
                <option value="F" <?php echo ($gradeRecord['grade'] == 'F') ? 'selected' : ''; ?>>F</option>
            </select>

            <button type="submit">Update Grade</button>
        </form>
    <?php else: ?>
        <p>Grade record not found.</p>
    <?php endif; ?>
</body>

</html>
