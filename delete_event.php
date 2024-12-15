<?php
// delete_event.php
session_start();
require '../php/db_connection.php';

if (isset($_GET['event_id'])) {
    $event_id = $_GET['event_id'];

    // Start a transaction
    $pdo->beginTransaction();

    try {
        // Fetch event details for notification
        $stmt = $pdo->prepare("SELECT event_name FROM events WHERE event_id = :event_id");
        $stmt->execute(['event_id' => $event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        // If you have an Event Registrations table, delete registrations associated with the event
        $stmt = $pdo->prepare("DELETE FROM event_registrations WHERE event_id = :event_id");
        $stmt->execute(['event_id' => $event_id]);

        // Delete the event
        $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = :event_id");
        $stmt->execute(['event_id' => $event_id]);

        // Notification logic
        if (isset($_SESSION['user_id'])) {
            $notification_title = "Event Deleted";
            $notification_message = "Event {$event['event_name']} deleted successfully.";
            $created_by = $_SESSION['user_id'];

            $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, related_entity, related_id, created_at) VALUES (?, ?, ?, 'event', ?, NOW())");
            $stmt->execute([$notification_title, $notification_message, $created_by, $event_id]);
        }

        // Commit the transaction
        $pdo->commit();

        header("Location: ../php/admin_dashboard.php?success=Event+deleted+successfully");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $pdo->rollBack();
        header("Location: ../php/admin_dashboard.php?error=Failed+to+delete+event");
        exit();
    }
} else {
    echo "Invalid event ID!";
    exit();
}
?>
