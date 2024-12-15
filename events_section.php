<?php
require '../php/db_connection.php';

// Fetch events
$events = $pdo->query("SELECT * FROM events")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>

<body>
    <h2>Events</h2>
    <a href="../admin_add_feature/add_event.php" class="add-btn">Add Event</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo $event['event_id']; ?></td>
                    <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                    <td><?php echo htmlspecialchars($event['event_date']); ?></td>
                    <td><?php echo htmlspecialchars($event['event_description']); ?></td>
                    <td>
                        <a href="../admin_update_feature/update_event.php?event_id=<?php echo $event['event_id']; ?>">Edit</a>
                        <a href="../admin_delete_feature/delete_event.php?event_id=<?php echo $event['event_id']; ?>" 
                        onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>