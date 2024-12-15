<?php
require 'db_connection.php'; // Database connection

session_start();
$user_id = $_SESSION['user_id'];

// Fetch student_id from the students table using user_id
$studentQuery = $pdo->prepare("SELECT student_id FROM students WHERE user_id = ?");
$studentQuery->execute([$user_id]);
$student_id = $studentQuery->fetchColumn();

// Fetch total counts and details for the overview section
$totalCourses = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE course_id IN (SELECT course_id FROM students WHERE student_id = ?)");
$totalCourses->execute([$student_id]);
$totalCourses = $totalCourses->fetchColumn();

$totalEvents = $pdo->prepare("SELECT COUNT(*) FROM events WHERE event_id IN (SELECT event_id FROM event_registrations WHERE student_id = ?)");
$totalEvents->execute([$student_id]);
$totalEvents = $totalEvents->fetchColumn();

$totalGrades = $pdo->prepare("SELECT COUNT(*) FROM grades WHERE student_id = ?");
$totalGrades->execute([$student_id]);
$totalGrades = $totalGrades->fetchColumn();

$totalAssignments = $pdo->prepare("SELECT COUNT(*) FROM student_assignments WHERE student_id = ?");
$totalAssignments->execute([$student_id]);
$totalAssignments = $totalAssignments->fetchColumn();

// Fetch progress tracking data
$courseProgress = $pdo->prepare("
    SELECT course_name, 
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
    GROUP BY courses.course_id
");
$courseProgress->execute([$student_id]);
$progressData = $courseProgress->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming deadlines
$upcomingDeadlines = $pdo->prepare("
    SELECT 
        assignments.assignment_title, 
        assignments.due_date 
    FROM 
        assignments 
    INNER JOIN 
        student_assignments 
    ON 
        student_assignments.assignment_id = assignments.assignment_id 
    WHERE 
        student_assignments.student_id = ? 
        AND assignments.due_date > NOW() 
    ORDER BY 
        assignments.due_date 
    LIMIT 5
");
$upcomingDeadlines->execute([$student_id]);
$deadlines = $upcomingDeadlines->fetchAll(PDO::FETCH_ASSOC);

// Fetch attendance statistics
$attendanceStats = $pdo->prepare("SELECT status, COUNT(*) as total FROM attendance WHERE student_id = ? GROUP BY status");
$attendanceStats->execute([$student_id]);
$attendanceData = $attendanceStats->fetchAll(PDO::FETCH_ASSOC);

// Check for badges (perfect attendance)
$perfectAttendance = $pdo->prepare("SELECT COUNT(*) FROM attendance WHERE student_id = ? AND status = 'present'");
$perfectAttendance->execute([$student_id]);
$perfectAttendanceCount = $perfectAttendance->fetchColumn();

// Fetch tasks for To-Do List
$tasks = $pdo->prepare("SELECT task_title, due_date FROM tasks WHERE student_id = ? ORDER BY due_date");
$tasks->execute([$student_id]);
$taskList = $tasks->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/student_dashboard.css">
    <script src="../js/student_dashboard.js"></script>
</head>
<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Student Panel</h2>
            <ul>
                <li><a href="javascript:void(0);" onclick="loadSection('overview')">Dashboard Overview</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('courses')">My Courses</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('grades')">My Grades</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('attendance')">My Attendance</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('events')">My Events</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('assignments')">My Assignments</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('tasks')">My Tasks</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('notifications')">Notifications</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Student Dashboard</h1>
                <div class="profile-dropdown">
                    <img src="../img/pfp.jpg" alt="Profile Picture" class="profile-img">
                    <div class="profile-dropdown-content">
                        <a href="javascript:void(0);" onclick="loadSection('profile')">My Profile</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </header>

            <section>
                <?php
                $section = isset($_GET['section']) ? $_GET['section'] : 'overview';
                if ($section === 'overview'): ?>
                    <div class="overview">
                        <div class="total-counts">
                            <h2>Overview</h2>
                            <div class="counts">
                                <div class="count-item">
                                    <h3>Total Courses</h3>
                                    <p><?php echo $totalCourses; ?></p>
                                </div>
                                <div class="count-item">
                                    <h3>Total Events</h3>
                                    <p><?php echo $totalEvents; ?></p>
                                </div>
                                <div class="count-item">
                                    <h3>Total Grades</h3>
                                    <p><?php echo $totalGrades; ?></p>
                                </div>
                                <div class="count-item">
                                    <h3>Total Assignments</h3>
                                    <p><?php echo $totalAssignments; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Tracking -->
                        <div class="progress-tracker">
                            <h2>Course Progress</h2>
                            <?php foreach ($progressData as $course): ?>
                                <div class="progress-item">
                                    <h4><?php echo htmlspecialchars($course['course_name']); ?></h4>
                                    <div class="progress-bar">
                                        <div class="progress" style="width: <?php echo htmlspecialchars($course['progress']); ?>%;"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Upcoming Deadlines -->
                        <div class="upcoming-deadlines">
                            <h2>Upcoming Deadlines</h2>
                            <ul>
                                <?php foreach ($deadlines as $deadline): ?>
                                    <li><?php echo htmlspecialchars($deadline['assignment_title']) . ' - Due: ' . htmlspecialchars($deadline['due_date']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Attendance Analytics -->
                        <div class="attendance-analytics">
                            <h2>Attendance Analytics</h2>
                            <?php foreach ($attendanceData as $attendance): ?>
                                <p><?php echo ucfirst(htmlspecialchars($attendance['status'])) . ': ' . htmlspecialchars($attendance['total']); ?></p>
                            <?php endforeach; ?>
                        </div>

                        <!-- Achievements -->
                        <div class="achievement-badges">
                            <h2>Achievements</h2>
                            <?php if ($perfectAttendanceCount >= 10): ?>
                                <div class="badge">Perfect Attendance</div>
                            <?php else: ?>
                                <p>No badges earned yet.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Task Manager -->
                        <div class="task-manager">
                            <h2>Task Manager</h2>
                            <a href="../student/add_task.php" class="add-task-button">Add Task</a>
                            <ul class="task-list">
                                <?php foreach ($taskList as $task): ?>
                                    <li><?php echo htmlspecialchars($task['task_title']) . ' - Due: ' . htmlspecialchars($task['due_date']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php elseif ($section === 'courses'): ?>
                    <?php include '../student_dashboard_sections/courses_section.php'; ?>
                <?php elseif ($section === 'grades'): ?>
                    <?php include '../student_dashboard_sections/grades_section.php'; ?>
                <?php elseif ($section === 'attendance'): ?>
                    <?php include '../student_dashboard_sections/attendance_section.php'; ?>
                <?php elseif ($section === 'events'): ?>
                    <?php include '../student_dashboard_sections/events_section.php'; ?>
                <?php elseif ($section === 'assignments'): ?>
                    <?php include '../student_dashboard_sections/assignments_section.php'; ?>
                <?php elseif ($section === 'tasks'): ?>
                    <?php include '../student_dashboard_sections/tasks_section.php'; ?>
                <?php elseif ($section === 'notifications'): ?>
                    <?php include '../student_dashboard_sections/notifications_section.php'; ?>
                <?php elseif ($section === 'profile'): ?>
                    <?php include '../student_dashboard_sections/profile_section.php'; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>