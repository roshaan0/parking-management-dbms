<?php


session_start();
require __DIR__ . '/vendor/autoload.php';
// Include the database connection file
require_once 'db.php';

// Check if the form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email and password are set in the POST array
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Look up the user by email from the 'User' table
        // Ensure your table name is 'User' (case-sensitive on some DBs) and columns are 'UserID', 'Name', 'Password', 'Role', 'Email'
        $sql = "SELECT UserID, Name, Password, Role FROM User WHERE Email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check password
            // Ensure the 'Password' column in your 'User' table actually contains hashed passwords
            if (password_verify($password, $user['Password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['role'] = $user['Role'];
                $_SESSION['full_name'] = $user['Name']; // Using 'Name' column from User table

                echo "Login successful! Redirecting to dashboard...";

                // Redirect based on user role
                switch ($user['Role']) {
                    case 'Admin':
                        header("refresh:2; url=AdminDashboard.php");
                        break;
                    case 'Faculty':
                        header("refresh:2; url=FacultyDashboard.php");
                        break;
                    case 'Student':
                        header("refresh:2; url=StudentDashboard.php");
                        break;
                    case 'Security':
                        header("refresh:2; url=SecurityDashboard.php");
                        break;
                    default:
                        // Fallback for unexpected roles or if role is not set
                        echo "Your role is not recognized. Please contact support. Redirecting to login.";
                        header("refresh:3; url=Login.html");
                        break;
                }
                exit(); // Ensure no further code is executed after redirection
            } else {
                echo "❌ Incorrect password. <a href='Login.html'>Try again</a>";
            }
        } else {
            echo "❌ No account found with that email. <a href='Signup.html'>Sign up here</a>";
        }

        $stmt->close();
        $conn->close();
    } else {
        // If email or password were not in the POST data (even if form submitted)
        echo "Please provide both email and password. <a href='Login.html'>Go to Login Page</a>";
    }
} else {
    // If accessed directly via GET request (e.g., typing URL)
    echo "Please submit the login form. <a href='Login.html'>Go to Login Page</a>";
}
?>