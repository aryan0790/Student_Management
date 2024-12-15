<?php
require '../php/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch student_id from the students table using user_id
    $studentQuery = $pdo->prepare("SELECT student_id FROM students WHERE user_id = ?");
    $studentQuery->execute([$user_id]);
    $student_id = $studentQuery->fetchColumn();

    if (!$student_id) {
        echo json_encode(['error' => 'Student not found']);
        exit;
    }

    // Fetch grades
    $gradesQuery = $pdo->prepare("
        SELECT courses.course_name, grades.grade 
        FROM grades 
        INNER JOIN courses ON grades.course_id = courses.course_id 
        WHERE student_id = ?
    ");
    $gradesQuery->execute([$student_id]);
    $gradesData = $gradesQuery->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($gradesData);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
