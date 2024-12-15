<?php
require '../php/db_connection.php';  // Database connection
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html'); // Redirect to login if user is not logged in
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_title = $_POST['task_title'];
    $task_description = $_POST['task_description'];
    $due_date = $_POST['due_date'];

    // Insert task into the database
    $query = $pdo->prepare("INSERT INTO tasks (student_id, task_title, task_description, due_date) VALUES (?, ?, ?, ?)");
    $result = $query->execute([$user_id, $task_title, $task_description, $due_date]);

    if ($result) {
        $message = "Task successfully added!";
        
        // Add notification for the student
        $notificationQuery = $pdo->prepare("
            INSERT INTO notifications (notification_title, notification_message, created_by, related_entity, related_id) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $notificationTitle = "New Task Added";
        $notificationMessage = "A new task has been added: " . htmlspecialchars($task_title);
        $relatedEntity = 'task'; // Assuming you have a task type for the related entity
        $relatedId = $pdo->lastInsertId(); // Get the ID of the newly created task

        $notificationQuery->execute([$notificationTitle, $notificationMessage, $user_id, $relatedEntity, $relatedId]);
    } else {
        $message = "Error adding task. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link rel="stylesheet" href="../css/student_dashboard_sections.css">
</head>
<body>
    <div class="task-form">
        <h2>Add New Task</h2>

        <?php if (!empty($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="task_title">Task Title:</label>
            <input type="text" id="task_title" name="task_title" required>

            <label for="task_description">Task Description:</label>
            <textarea id="task_description" name="task_description"></textarea>

            <label for="due_date">Due Date:</label>
            <input type="date" id="due_date" name="due_date" required>

            <button type="submit">Add Task</button>
        </form>

        <a href="../php/student_dashboard.php?section=tasks" class="back-to-task">Back to Tasks</a>
    </div>
</body>
</html>
