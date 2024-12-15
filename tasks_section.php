<?php
// Include database connection
require '../php/db_connection.php';

// Fetch student ID from session or define it directly for testing
$student_id = $_SESSION['user_id'];

// Fetch tasks for the student
try {
    $tasksQuery = $pdo->prepare("SELECT task_title, due_date, status FROM tasks WHERE student_id = ? ORDER BY due_date ASC");
    $tasksQuery->execute([$student_id]);
    $tasksData = $tasksQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error'] = 'Failed to fetch tasks: ' . htmlspecialchars($e->getMessage());
    header("Location: ../php/student_dashboard.php?section=tasks");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tasks</title>
    <link rel="stylesheet" href="../css/student_dashboard_sections.css">
</head>

<body>
    <h2>My Tasks</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php
            echo htmlspecialchars($_SESSION['error']);
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (empty($tasksData)): ?>
        <p>You have no tasks at this time.</p>
    <?php else: ?>
        <table class="tasks-table">
            <thead>
                <tr>
                    <th>Task Title</th>
                    <th>Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasksData as $task): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($task['task_title']); ?></td>
                        <td><?php echo htmlspecialchars(date('F j, Y', strtotime($task['due_date']))); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($task['status'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>

</html>


