<?php
// add_event.php
require '../php/db_connection.php';
session_start(); // Start the session

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];
    $event_description = trim($_POST['event_description']);

    // Server-side validation
    if (empty($event_name) || empty($event_date)) {
        $message = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO events (event_name, event_date, event_description) VALUES (:event_name, :event_date, :event_description)");
            $stmt->execute(['event_name' => $event_name, 'event_date' => $event_date, 'event_description' => $event_description]);

            // Add notification only if the user is logged in
            if (isset($_SESSION['user_id'])) {
                addNotification($pdo, 'Event Added', "Event $event_name has been added successfully.", $_SESSION['user_id']);
            }

            header("Location: ../php/admin_dashboard.php?success=Event+added+successfully");
            exit();
        } catch (Exception $e) {
            $message = "Failed to add event: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Function to add a notification
function addNotification($pdo, $title, $message, $created_by) {
    $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, created_at) VALUES (:title, :message, :created_by, NOW())");
    $stmt->execute(['title' => $title, 'message' => $message, 'created_by' => $created_by]);
}
?>

<!-- Add Event Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>
<body>
    <div class="container">
        <h1>Add Event</h1>
        <?php if ($message): ?>
            <p class="alert alert-danger"><?= $message; ?></p>
        <?php endif; ?>
        <form method="POST" class="form">
            <label>Event Name:</label>
            <input type="text" name="event_name" required><br>

            <label>Event Date:</label>
            <input type="date" name="event_date" required><br>

            <label>Event Description:</label>
            <textarea name="event_description" required></textarea><br>

            <button type="submit">Add Event</button>
        </form>
    </div>
</body>
</html>
