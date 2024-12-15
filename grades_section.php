<?php
require '../php/db_connection.php';

// Fetch students and their grades
$gradesQuery = $pdo->query("SELECT g.grade_id, s.first_name, s.last_name, g.grade, c.course_name 
                            FROM grades g 
                            JOIN students s ON g.student_id = s.student_id 
                            JOIN courses c ON g.course_id = c.course_id");

$gradeRecords = $gradesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Grades</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>

<body>
    <h2>Manage Grades</h2>

    <!-- Add New Grade Form -->
    <h3>Add New Grade</h3>
    <form action="../admin_add_feature/add_grade.php" method="POST">
        <label for="student">Student:</label>
        <select name="student_id" required>
            <?php
            $studentsQuery = $pdo->query("SELECT student_id, first_name, last_name FROM students");
            $students = $studentsQuery->fetchAll(PDO::FETCH_ASSOC);
            foreach ($students as $student) {
                echo "<option value='{$student['student_id']}'>{$student['first_name']} {$student['last_name']}</option>";
            }
            ?>
        </select>

        <label for="course">Course:</label>
        <select name="course_id" required>
            <?php
            $coursesQuery = $pdo->query("SELECT course_id, course_name FROM courses");
            $courses = $coursesQuery->fetchAll(PDO::FETCH_ASSOC);
            foreach ($courses as $course) {
                echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
            }
            ?>
        </select>

        <label for="grade">Grade:</label>
        <select name="grade" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
            <option value="F">F</option>
        </select>

        <button type="submit" name="add_grade">Add Grade</button>
    </form>

    <!-- Grades Table -->
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Course</th>
                <th>Grade</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($gradeRecords as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['grade']); ?></td>
                    <td>
                        <a href="../admin_update_feature/update_grade.php?grade_id=<?php echo $record['grade_id']; ?>">Edit</a>
                        <a href="../admin_delete_feature/delete_grade.php?delete_id=<?php echo $record['grade_id']; ?>" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
