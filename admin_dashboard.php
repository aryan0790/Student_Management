<?php
require 'db_connection.php';

// Fetch total counts for the overview
$totalStudents = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalCourses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$totalAttendance = $pdo->query("SELECT COUNT(*) FROM attendance")->fetchColumn();
$totalGrades = $pdo->query("SELECT COUNT(*) FROM grades")->fetchColumn();
$totalAssignments = $pdo->query("SELECT COUNT(*) FROM assignments")->fetchColumn();

// Fetch students, courses, and events based on selection
$section = isset($_GET['section']) ? $_GET['section'] : 'overview';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <script src="../js/admin_dashboard.js"></script>
</head>

<body>
    <div class="dashboard">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="javascript:void(0);" onclick="loadSection('overview')">Dashboard Overview</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('students')">Manage Students</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('courses')">Manage Courses</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('events')">Manage Events</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('attendance')">Manage Attendance</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('grades')">Manage Grades</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('assignments')">Manage Assignments</a></li>
                <li><a href="javascript:void(0);" onclick="loadSection('notifications')">Notifications</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Admin Dashboard</h1>
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($_GET['success']); ?>
                    </div>
                <?php endif; ?>
            </header>

            <section>
                <?php if ($section === 'overview'): ?>
                    <div class="total-counts">
                        <h2>Total Counts</h2>
                        <div class="counts">
                            <div class="count-item">
                                <h3>Total Students</h3>
                                <p><?php echo $totalStudents; ?></p>
                            </div>
                            <div class="count-item">
                                <h3>Total Courses</h3>
                                <p><?php echo $totalCourses; ?></p>
                            </div>
                            <div class="count-item">
                                <h3>Total Events</h3>
                                <p><?php echo $totalEvents; ?></p>
                            </div>
                            <div class="count-item">
                                <h3>Total Attendance</h3>
                                <p><?php echo $totalAttendance; ?></p>
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

                    <!-- Notifications Section -->
                    <div class="notifications-section">
                        <h2>Recent Notifications</h2>
                        <ul class="notifications-list">
                            <!-- Dynamic content will be inserted here via JavaScript -->
                            <li>Loading notifications...</li>
                        </ul>
                    </div>

                    <!-- Upcoming Events Section -->
                    <div class="upcoming-events-section">
                        <h2>Upcoming Events</h2>
                        <ul class="upcoming-events-list">
                            <!-- Dynamic content will be inserted here via JavaScript -->
                            <li>Loading upcoming events...</li>
                        </ul>
                    </div>

                    <!-- Recent Activity Section -->
                    <div class="recent-activity-section">
                        <h2>Recent Activities</h2>
                        <ul class="recent-activity-list">
                            <!-- Dynamic content will be inserted here via JavaScript -->
                            <li>Loading activities...</li>
                        </ul>
                    </div>

                <?php elseif ($section === 'students'): ?>
                    <?php include '../admin_dashboard_sections/students_section.php'; ?>
                <?php elseif ($section === 'courses'): ?>
                    <?php include '../admin_dashboard_sections/courses_section.php'; ?>
                <?php elseif ($section === 'events'): ?>
                    <?php include '../admin_dashboard_sections/events_section.php'; ?>
                <?php elseif ($section === 'attendance'): ?>
                    <?php include '../admin_dashboard_sections/attendance_section.php'; ?>
                <?php elseif ($section === 'grades'): ?>
                    <?php include '../admin_dashboard_sections/grades_section.php'; ?>
                <?php elseif ($section === 'assignments'): ?>
                    <?php include '../admin_dashboard_sections/assignments_section.php'; ?>
                <?php elseif ($section === 'notifications'): ?>
                    <?php include '../admin_dashboard_sections/notifications_section.php'; ?>
                <?php else: ?>
                    <p>Invalid section selected.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>

</html>