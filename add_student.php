<?php
// add_student.php
require '../php/db_connection.php';
session_start(); // Start the session

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = $_POST['dob'];
    $phone = trim($_POST['phone']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash('password123', PASSWORD_DEFAULT);  // Hash the password
    $course_id = $_POST['course_id'] ?? null;  // Optional course assignment

    // Server-side validation
    if (empty($first_name) || empty($last_name) || empty($dob) || empty($username) || empty($email)) {
        $message = "All fields are required.";
    } else {
        try {
            // Start a transaction
            $pdo->beginTransaction();

            // Insert into users table
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'student')");
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $password]);
            $user_id = $pdo->lastInsertId();  // Get the inserted user ID

            // Insert into students table
            $stmt = $pdo->prepare("INSERT INTO students (user_id, first_name, last_name, dob, phone, course_id) VALUES (:user_id, :first_name, :last_name, :dob, :phone, :course_id)");
            $stmt->execute([
                'user_id' => $user_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'dob' => $dob,
                'phone' => $phone,
                'course_id' => $course_id,
            ]);

            // Add notification only if the user is logged in
            if (isset($_SESSION['user_id'])) {
                addNotification($pdo, 'Student Added', "Student $first_name $last_name has been added successfully.", $_SESSION['user_id']);
            }

            // Commit the transaction
            $pdo->commit();
            header("Location: ../php/admin_dashboard.php?success=Student+added+successfully");
            exit();
        } catch (Exception $e) {
            // Rollback on error
            $pdo->rollBack();
            $message = "Failed to add student: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Function to add a notification
function addNotification($pdo, $title, $message, $created_by) {
    $stmt = $pdo->prepare("INSERT INTO notifications (notification_title, notification_message, created_by, created_at) VALUES (:title, :message, :created_by, NOW())");
    $stmt->execute(['title' => $title, 'message' => $message, 'created_by' => $created_by]);
}
?>

<!-- Add Student Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>
<body>
    <div class="container">
        <h1>Add Student</h1>
        <?php if ($message): ?>
            <p class="alert alert-danger"><?= $message; ?></p>
        <?php endif; ?>
        <form method="POST" class="form">
            <label>First Name:</label>
            <input type="text" name="first_name" required><br>

            <label>Last Name:</label>
            <input type="text" name="last_name" required><br>

            <label>Date of Birth:</label>
            <input type="date" name="dob" required><br>

            <label>Phone:</label>
            <input type="text" name="phone"><br>

            <label>Username:</label>
            <input type="text" name="username" required><br>

            <label>Email:</label>
            <input type="email" name="email" required><br>

            <label>Course (optional):</label>
            <select name="course_id">
                <option value="">Select a course</option>
                <?php
                $stmt = $pdo->query("SELECT * FROM courses");
                while ($course = $stmt->fetch()) {
                    echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
                }
                ?>
            </select><br>

            <button type="submit">Add Student</button>
        </form>
    </div>
</body>
</html>
