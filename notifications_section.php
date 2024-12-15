<?php
require '../php/db_connection.php';

// Fetch notifications from the database
$notifications = $pdo->query("SELECT notification_id, notification_title, notification_message, created_at 
                             FROM notifications 
                             ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>

<body>
    <h2>Recent Notifications</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Message</th>
                <th>Date Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notifications as $notification): ?>
                <tr>
                    <td><?php echo $notification['notification_id']; ?></td>
                    <td><?php echo htmlspecialchars($notification['notification_title']); ?></td>
                    <td><?php echo htmlspecialchars($notification['notification_message']); ?></td>
                    <td><?php echo htmlspecialchars($notification['created_at']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
