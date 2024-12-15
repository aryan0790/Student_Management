<?php
require '../php/db_connection.php';

// Fetch recent activities (e.g., enrollments)
$query = "SELECT CONCAT(students.first_name, ' ', students.last_name) as student_name, courses.course_name 
          FROM students
          JOIN courses ON students.course_id = courses.course_id
          ORDER BY students.created_at DESC LIMIT 5";
$stmt = $pdo->query($query);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return activities as JSON
echo json_encode($activities);
?>
