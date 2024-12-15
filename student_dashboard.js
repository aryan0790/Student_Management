function loadSection(section) {
    window.location.href = `student_dashboard.php?section=${section}`;
}

// Function to fetch course progress
function updateCourseProgress() {
    fetch('../student/course_progress.php')
        .then(response => response.json())
        .then(data => {
            const progressList = document.querySelector('.course-progress-list');
            progressList.innerHTML = ''; // Clear the list

            data.forEach(course => {
                const li = document.createElement('li');
                li.textContent = `Course: ${course.course_name} - Progress: ${course.progress}%`;
                progressList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching course progress:', error));
}

// Function to fetch grades
function updateGrades() {
    fetch('../student/grades.php')
        .then(response => response.json())
        .then(data => {
            const gradesList = document.querySelector('.grades-list');
            gradesList.innerHTML = ''; // Clear the list

            data.forEach(grade => {
                const li = document.createElement('li');
                li.textContent = `Course: ${grade.course_name} - Grade: ${grade.grade}`;
                gradesList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching grades:', error));
}

// Function to fetch attendance
function updateAttendance() {
    fetch('../student/attendance.php')
        .then(response => response.json())
        .then(data => {
            const attendanceList = document.querySelector('.attendance-list');
            attendanceList.innerHTML = ''; // Clear the list

            data.forEach(record => {
                const li = document.createElement('li');
                li.textContent = `Date: ${record.date} - Status: ${record.status}`;
                attendanceList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching attendance:', error));
}

// Function to fetch student-specific notifications
function updateStudentNotifications() {
    fetch('../student/notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.querySelector('.student-notifications-list');
            notificationsList.innerHTML = ''; // Clear the list

            data.forEach(notification => {
                const li = document.createElement('li');
                li.textContent = notification.notification_message;
                notificationsList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching student notifications:', error));
}

// Call these functions to update the sections when the page loads
document.addEventListener('DOMContentLoaded', function() {
    updateCourseProgress(); // Load course progress
    updateGrades(); // Load grades
    updateAttendance(); // Load attendance
    updateStudentNotifications(); // Load student notifications
});