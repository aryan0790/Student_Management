<?php
// Fetch student profile information along with course name
$profileQuery = $pdo->prepare("
    SELECT s.first_name, s.last_name, u.email, s.dob, s.phone, s.course_id, c.course_name 
    FROM students s 
    JOIN users u ON s.user_id = u.user_id 
    LEFT JOIN courses c ON s.course_id = c.course_id 
    WHERE s.student_id = ?
");
$profileQuery->execute([$student_id]);
$studentProfile = $profileQuery->fetch(PDO::FETCH_ASSOC);

// Check if the form is submitted for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $dob = $_POST['dob'];
    $phone = trim($_POST['phone']);
    $courseId = $_POST['course_id'];
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // Validate password if it's provided
    if (!empty($password) && $password !== $confirmPassword) {
        $_SESSION['error'] = 'Passwords do not match.';
        header("Location: student_dashboard.php?section=profile");
        exit();
    }

    // Update student profile in the database
    $updateQuery = $pdo->prepare("
        UPDATE students 
        SET first_name = ?, last_name = ?, dob = ?, phone = ?, course_id = ? 
        WHERE student_id = ?
    ");
    $updateQuery->execute([$firstName, $lastName, $dob, $phone, $courseId, $student_id]);

    // Update user email and password if provided
    $userUpdateQuery = $pdo->prepare("
        UPDATE users 
        SET email = ?, password = ? 
        WHERE user_id = (SELECT user_id FROM students WHERE student_id = ?)
    ");

    // Hash the password if it's provided
    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

    // Execute the user update query
    $userUpdateQuery->execute([$email, $hashedPassword, $student_id]);

    // Set success message
    $_SESSION['success'] = 'Profile updated successfully!';
    header("Location: student_dashboard.php?section=profile");
    exit();
}

// Fetch available courses for the dropdown
$coursesQuery = $pdo->prepare("SELECT course_id, course_name FROM courses");
$coursesQuery->execute();
$courses = $coursesQuery->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/student_dashboard_sections.css">
</head>

<body>
    <div class="profile-section">
        <h2>My Profile</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($studentProfile['first_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($studentProfile['last_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($studentProfile['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="dob">Date of Birth:</label>
                <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($studentProfile['dob']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($studentProfile['phone']); ?>">
            </div>

            <div class="form-group">
                <label for="course_id">Course:</label>
                <select id="course_id" name="course_id">
                    <option value="">Select a course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course['course_id']); ?>"
                            <?php echo $course['course_id'] == $studentProfile['course_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="password">New Password (leave blank to keep current password):</label>
                <input type="password" id="password" name="password">
                <input type="checkbox" onclick="togglePasswordVisibility('password')"> Show Password
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password">
                <input type="checkbox" onclick="togglePasswordVisibility('confirm_password')"> Show Password
            </div>

            <button type="submit">Update Profile</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId) {
            const inputField = document.getElementById(inputId);
            inputField.type = inputField.type === "password" ? "text" : "password";
        }
    </script>
</body>

</html>