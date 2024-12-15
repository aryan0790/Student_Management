<?php
// update_event.php
session_start();
require '../php/db_connection.php';

$message = '';

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Fetch event details
    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id = :event_id");
    $stmt->execute(['event_id' => $event_id]);
    $event = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate inputs
        $event_name = trim(htmlspecialchars($_POST['event_name']));
        $event_date = $_POST['event_date']; // Validate date format if necessary
        $event_description = trim(htmlspecialchars($_POST['event_description']));

        $stmt = $pdo->prepare("UPDATE events SET event_name = :event_name, event_date = :event_date, event_description = :event_description WHERE event_id = :event_id");
        $stmt->execute([
            'event_name' => $event_name,
            'event_date' => $event_date,
            'event_description' => $event_description,
            'event_id' => $event_id,
        ]);

        // Notification logic
        if (isset($_SESSION['user_id'])) {
            $notification_title = "Event Updated";
            $notification_message = "Event {$event_name} updated successfully.";
            $created_by = $_SESSION['user_id'];

            $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, related_entity, related_id, created_at) VALUES (?, ?, ?, 'event', ?, NOW())");
            $stmt->execute([$notification_title, $notification_message, $created_by, $event_id]);
        }

        header("Location: ../php/admin_dashboard.php?success=Event+updated+successfully");
        exit();
    }
} else {
    header("Location: ../php/admin_dashboard.php?error=Invalid+event+ID");
    exit();
}
?>

<!-- Update Event Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Event</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>
<body>
    <div class="container">
        <h1>Update Event</h1>
        <form method="POST" class="form">
            <label>Event Name:</label>
            <input type="text" name="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required><br>

            <label>Event Date:</label>
            <input type="date" name="event_date" value="<?php echo htmlspecialchars($event['event_date']); ?>" required><br>

            <label>Event Description:</label>
            <textarea name="event_description"><?php echo htmlspecialchars($event['event_description']); ?></textarea><br>

            <button type="submit">Update Event</button>
        </form>
    </div>
</body>
</html>
