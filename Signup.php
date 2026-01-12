<?php

require __DIR__ . '/vendor/autoload.php';
// Include the database connection file
require_once 'db.php';

// Get form data
$full_name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Safely get the role from POST data, provide a default if not set
// This handles the "Undefined array key" warning
$role = isset($_POST['role']) ? $_POST['role'] : 'Student'; // Default to 'Student' if role is not set

// Initialize EnrollmentNumber and EmployeeID as NULL by default
$enrollment_number = NULL;
$employee_id = NULL;

// You would typically have input fields in Signup.html for these
// For now, if you want to handle them, you can add conditional logic
// For example:
/*
if ($role === 'Student') {
    $enrollment_number = isset($_POST['enrollment_number']) ? $_POST['enrollment_number'] : NULL;
} elseif ($role === 'Faculty' || $role === 'Security') {
    $employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : NULL;
}
*/

// Validate password
if ($password !== $confirm_password) {
    die("Passwords do not match. <a href='signup.html'>Go back</a>");
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute the SQL statement to insert into the 'User' table
// Note: UserID, Name, Email, Password, Role, EnrollmentNumber, EmployeeID, Phone
$sql = "INSERT INTO User (Name, Email, Phone, Password, Role, EnrollmentNumber, EmployeeID) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters: 'sssssss' for 7 string parameters (Name, Email, Phone, Password, Role, EnrollmentNumber, EmployeeID)
// Use 's' for strings, 'i' for integers, 'd' for doubles, 'b' for blobs
$stmt->bind_param("sssssss", $full_name, $email, $phone, $hashed_password, $role, $enrollment_number, $employee_id);

if ($stmt->execute()) {
    echo "Signup successful! <a href='Login.html'>Go to Login</a>";
} else {
    // Check for duplicate entry error specifically for email
    if ($conn->errno == 1062) { // MySQL error code for duplicate entry
        echo "Error: This email is already registered. Please use a different email or <a href='Login.html'>Log in</a>.";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>