<?php
session_start(); // Start the session
require 'db_connection.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role']; // Ensure role is included in the form
    $password = $_POST['password'];

    // Check if the user already exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $stmt->execute(['username' => $username, 'email' => $email]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
        // Redirect back with an error message
        $_SESSION['error'] = 'Username or Email already registered.';
        header("Location: ../index.html");
        exit();
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashedPassword, 'role' => $role]);

        // Get the last inserted user ID
        $userId = $pdo->lastInsertId();

        // Create an entry in the students table
        if ($role === 'student') { // Ensure that only students have entries created in the students table
            $stmt = $pdo->prepare("INSERT INTO students (user_id) VALUES (:user_id)");
            $stmt->execute(['user_id' => $userId]);
        }

        // Set success message
        $_SESSION['success'] = 'You have successfully registered!';
        header("Location: ../index.html");
        exit();
    }
}
?>
