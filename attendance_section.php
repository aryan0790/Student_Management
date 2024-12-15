<?php
require '../php/db_connection.php';

// Fetch students and their attendance
$attendanceQuery = $pdo->query("SELECT a.attendance_id, s.first_name, s.last_name, a.attendance_date, a.status, c.course_name 
                                FROM attendance a 
                                JOIN students s ON a.student_id = s.student_id 
                                JOIN courses c ON a.course_id = c.course_id");

$attendanceRecords = $attendanceQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>

<body>
    <h2>Manage Attendance</h2>

    <!-- Add Attendance Form -->
    <h3>Add New Attendance</h3>
    <form action="../admin_add_feature/add_attendance.php" method="POST">
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

        <label for="attendance_date">Date:</label>
        <input type="date" name="attendance_date" required>

        <label for="status">Status:</label>
        <select name="status" required>
            <option value="present">Present</option>
            <option value="absent">Absent</option>
            <option value="late">Late</option>
        </select>

        <button type="submit" name="add_attendance">Add Attendance</button>
    </form>

    <!-- Attendance Table -->
    <table>
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Course</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($attendanceRecords as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($record['attendance_date']); ?></td>
                    <td><?php echo htmlspecialchars($record['status']); ?></td>
                    <td>
                        <a href="../admin_update_feature/update_attendance.php?attendance_id=<?php echo $record['attendance_id']; ?>">Edit</a>
                        <a href="../admin_delete_feature/delete_attendance.php?delete_id=<?php echo $record['attendance_id']; ?>" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
