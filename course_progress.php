<?php
require '../php/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch student_id from the students table using user_id
try {
    $studentQuery = $pdo->prepare("SELECT student_id FROM students WHERE user_id = ?");
    $studentQuery->execute([$user_id]);
    $student_id = $studentQuery->fetchColumn();

    if (!$student_id) {
        echo json_encode(['error' => 'Student not found']);
        exit;
    }

    // Fetch course progress
    $courseProgressQuery = $pdo->prepare("
        SELECT courses.course_name, 
               CASE 
                   WHEN grade = 'A' THEN 100
                   WHEN grade = 'B' THEN 85
                   WHEN grade = 'C' THEN 70
                   WHEN grade = 'D' THEN 55
                   WHEN grade = 'F' THEN 40
                   ELSE 0 
               END AS progress 
        FROM grades 
        INNER JOIN courses ON grades.course_id = courses.course_id 
        WHERE student_id = ?
    ");
    $courseProgressQuery->execute([$student_id]);
    $progressData = $courseProgressQuery->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($progressData);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
