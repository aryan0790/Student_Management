<?php
require '../php/db_connection.php';

// Fetch courses
$courses = $pdo->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
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
    <h2>Courses</h2>
    <a href="../admin_add_feature/add_course.php" class="add-btn">Add Course</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo $course['course_id']; ?></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_description']); ?></td>
                    <td>
                        <a href="../admin_update_feature/update_course.php?course_id=<?php echo $course['course_id']; ?>">Edit</a>
                        <a href="../admin_delete_feature/delete_course.php?course_id=<?php echo $course['course_id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>