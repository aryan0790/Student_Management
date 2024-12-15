<?php
require '../php/db_connection.php';

// Fetch recent notifications
$query = "SELECT notification_message FROM notifications ORDER BY created_at DESC LIMIT 5";
$stmt = $pdo->query($query);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return notifications as JSON
echo json_encode($notifications);
?>
