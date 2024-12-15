function loadSection(section) {
    window.location.href = `admin_dashboard.php?section=${section}`;
}

// Function to fetch notifications
function updateNotifications() {
    fetch('../admin/notifications.php')
        .then(response => response.json())
        .then(data => {
            const notificationsList = document.querySelector('.notifications-list');
            notificationsList.innerHTML = ''; // Clear the list

            data.forEach(notification => {
                const li = document.createElement('li');
                li.textContent = notification.notification_message;
                notificationsList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching notifications:', error));
}

// Function to fetch upcoming events
function updateEvents() {
    fetch('../admin/events.php')
        .then(response => response.json())
        .then(data => {
            const eventsList = document.querySelector('.upcoming-events-list');
            eventsList.innerHTML = ''; // Clear the list

            data.forEach(event => {
                const li = document.createElement('li');
                li.textContent = `Event: ${event.event_name} on ${event.event_date}`;
                eventsList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching events:', error));
}

// Function to fetch recent activities
function updateRecentActivities() {
    fetch('../admin/activities.php')
        .then(response => response.json())
        .then(data => {
            const activitiesList = document.querySelector('.recent-activity-list');
            activitiesList.innerHTML = ''; // Clear the list

            data.forEach(activity => {
                const li = document.createElement('li');
                li.textContent = `${activity.student_name} enrolled in "${activity.course_name}"`;
                activitiesList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching recent activities:', error));
}

// Function to fetch assignments for students
function updateAssignments() {
    fetch('../admin/assignments.php')
        .then(response => response.json())
        .then(data => {
            const assignmentsList = document.querySelector('.assignments-list');
            assignmentsList.innerHTML = ''; // Clear the list

            data.forEach(assignment => {
                const li = document.createElement('li');
                li.textContent = `Assignment: ${assignment.assignment_title} - Due: ${assignment.due_date}`;
                assignmentsList.appendChild(li);
            });
        })
        .catch(error => console.error('Error fetching assignments:', error));
}

// Call these functions to update the sections when the page loads
document.addEventListener('DOMContentLoaded', function() {
    updateNotifications(); // Load notifications
    updateEvents(); // Load upcoming events
    updateRecentActivities(); // Load recent activities
    updateAssignments(); // Load assignments
});