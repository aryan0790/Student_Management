INSERT INTO users (username, email, password, role) VALUES
('admin1', 'admin1@example.com', 'adminpassword', 'admin'),
('student1', 'student1@example.com', 'studentpassword', 'student'),
('student2', 'student2@example.com', 'studentpassword', 'student');

INSERT INTO courses (course_name, course_description) VALUES
('Mathematics 101', 'An introduction to basic mathematics.'),
('History 101', 'A general overview of world history.'),
('Computer Science 101', 'Introduction to computer programming and algorithms.');

INSERT INTO students (user_id, first_name, last_name, dob, phone, course_id) VALUES
(2, 'John', 'Doe', '2000-01-01', '1234567890', 1),
(3, 'Jane', 'Smith', '1999-02-15', '0987654321', 2);

INSERT INTO events (event_name, event_date, event_description) VALUES
('Math Seminar', '2024-11-10', 'A seminar on advanced math topics.'),
('History Exhibition', '2024-12-01', 'A detailed exhibition on historical events.');

INSERT INTO event_registrations (event_id, student_id) VALUES
(1, 1),
(2, 2);

INSERT INTO grades (student_id, course_id, grade) VALUES
(1, 1, 'A'),
(2, 2, 'B');

INSERT INTO attendance (student_id, course_id, attendance_date, status) VALUES
(1, 1, '2024-10-10', 'present'),
(2, 2, '2024-10-11', 'absent');

INSERT INTO notifications (notification_title, notification_message, created_by, related_entity, related_id) VALUES
('Grade Posted', 'Your grade for Math 101 has been posted.', 1, 'grade', 1),
('New Event', 'A new history exhibition event has been scheduled.', 1, 'event', 2);

INSERT INTO assignments (course_id, assignment_title, file_name, due_date, status) VALUES
(1, 'Math Homework 1', 'math_homework_1.pdf', '2024-10-20', 'submitted'),
(2, 'History Essay', 'history_essay.pdf', '2024-10-22', 'pending');

INSERT INTO student_assignments (assignment_id, student_id, submission_file, submission_status, submission_date) VALUES
(1, 1, 'math_homework_1_submission.pdf', 'submitted', '2024-10-18 12:30:00'),
(2, 2, 'history_essay_submission.pdf', 'pending', '2024-10-19 10:00:00');

INSERT INTO tasks (student_id, task_title, task_description, due_date, status) VALUES
(1, 'Complete Math Homework', 'Finish all exercises in Chapter 1.', '2024-10-25', 'pending'),
(2, 'Write History Essay', 'Submit the essay on WWII.', '2024-10-26', 'completed');