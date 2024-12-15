<?php
require '../php/db_connection.php';

// Fetch students
$students = $pdo->query("SELECT s.student_id, s.first_name, s.last_name, s.dob, s.phone, c.course_name 
                        FROM students s 
                        LEFT JOIN courses c 
                        ON s.course_id = c.course_id")->fetchAll(PDO::FETCH_ASSOC);
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
    <h2>Students</h2>
    <a href="../admin_add_feature/add_student.php" class="add-btn">Add Student</a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Date of Birth</th>
                <th>Phone</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo $student['student_id']; ?></td>
                    <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['dob']); ?></td>
                    <td><?php echo htmlspecialchars($student['phone']); ?></td>
                    <td><?php echo htmlspecialchars($student['course_name']); ?></td>
                    <td>
                        <a href="../admin_update_feature/update_student.php?student_id=<?php echo $student['student_id']; ?>">Edit</a>
                        <a href="../admin_delete_feature/delete_student.php?student_id=<?php echo $student['student_id']; ?>" 
                            onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>