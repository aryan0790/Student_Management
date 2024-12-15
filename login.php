<?php
session_start(); // Start the session
require 'db_connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = trim($_POST['usernameOrEmail']); // Trim input
    $password = $_POST['password'];

    // Prepare SQL statement to find user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :usernameOrEmail OR email = :usernameOrEmail");
    $stmt->execute(['usernameOrEmail' => $usernameOrEmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($user && password_verify($password, $user['password'])) {
        // Store user information in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Set success message
        $_SESSION['success'] = 'You have successfully logged in.';

        // Redirect to the appropriate dashboard based on role
        header("Location: " . ($user['role'] === 'admin' ? 'admin_dashboard.php' : 'student_dashboard.php'));
        exit();
    } else {
        // Generic error message
        $_SESSION['error'] = 'Invalid login credentials.';
        header("Location: ../index.html");
        exit();
    }
}
?>

