<?php
require '../php/db_connection.php'; // Your PDO connection file

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_assignment'])) {
    $courseId = $_POST['course_id'];
    $assignmentTitle = $_POST['assignment_title'];
    $dueDate = $_POST['due_date'];
    $fileName = $_FILES['assignment_file']['name'];
    $targetDir = "../uploads/";
    $targetFile = $targetDir . basename($fileName);

    if (move_uploaded_file($_FILES['assignment_file']['tmp_name'], $targetFile)) {
        $stmt = $pdo->prepare("INSERT INTO assignments (course_id, assignment_title, file_name, due_date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$courseId, $assignmentTitle, $fileName, $dueDate]);
        echo "<div class='alert alert-success'>Assignment uploaded successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error uploading file.</div>";
    }
}

// Handle delete logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_assignment'])) {
    $assignmentId = $_POST['assignment_id'];
    $stmt = $pdo->prepare("DELETE FROM assignments WHERE assignment_id = ?");
    if ($stmt->execute([$assignmentId])) {
        echo "<div class='alert alert-success'>Assignment deleted successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting assignment.</div>";
    }
}

// Fetch existing assignments
$assignments = $pdo->query("SELECT a.assignment_id, a.assignment_title, a.file_name, a.due_date, c.course_name 
                            FROM assignments a 
                            JOIN courses c 
                            ON a.course_id = c.course_id")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/admin_dashboard_sections.css">
</head>
<body>
<h2>Manage Assignments</h2>
<form method="POST" enctype="multipart/form-data">
    <label for="course_id">Select Course:</label>
    <select name="course_id" required>
        <?php
        $courses = $pdo->query("SELECT course_id, course_name FROM courses")->fetchAll();
        foreach ($courses as $course) {
            echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
        }
        ?>
    </select>

    <label for="assignment_title">Assignment Title:</label>
    <input type="text" name="assignment_title" required>

    <label for="due_date">Due Date:</label>
    <input type="date" name="due_date" required>

    <label for="assignment_file">Upload Assignment File:</label>
    <input type="file" name="assignment_file" accept=".pdf,.docx,.pptx" required>

    <button type="submit" name="upload_assignment">Upload Assignment</button>
</form>

<h3>Existing Assignments</h3>
<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>File</th>
            <th>Due Date</th>
            <th>Course</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($assignments as $assignment): ?>
            <tr>
                <td><?php echo htmlspecialchars($assignment['assignment_title']); ?></td>
                <td><a href="../uploads/<?php echo htmlspecialchars($assignment['file_name']); ?>" target="_blank">View File</a></td>
                <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                <td><?php echo htmlspecialchars($assignment['course_name']); ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="assignment_id" value="<?php echo htmlspecialchars($assignment['assignment_id']); ?>">
                        <button type="submit" name="delete_assignment" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
