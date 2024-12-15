<?php
require '../php/db_connection.php';

// Fetch upcoming events
$query = "SELECT event_name, event_date FROM events ORDER BY event_date ASC LIMIT 5";
$stmt = $pdo->query($query);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return events as JSON
echo json_encode($events);
?>
