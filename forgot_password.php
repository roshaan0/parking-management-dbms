<?php
session_start();
require __DIR__ . '/vendor/autoload.php'; // If using Composer for libraries like PHPMailer
require_once 'db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
$messageType = 'error'; // Default to error

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $_POST['email'];

    $sql_user = "SELECT UserID, Name FROM User WHERE Email = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $email);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();

    if ($result_user->num_rows === 1) {
        $user = $result_user->fetch_assoc();
        $userId = $user['UserID'];

        // Generate a unique token
        $token = bin2hex(random_bytes(32));
        $expires = date("U") + 1800; // Token expires in 30 minutes

        // Store token in a new table 'PasswordResets' (UserID, Email, Token, ExpiresAt)
        // Or add Token and TokenExpiresAt columns to User table (simpler for this example)
        $sql_delete_old_token = "DELETE FROM PasswordResets WHERE Email = ?";
        $stmt_delete = $conn->prepare($sql_delete_old_token);
        $stmt_delete->bind_param("s", $email);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        $sql_insert_token = "INSERT INTO PasswordResets (Email, Token, ExpiresAt) VALUES (?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert_token);
        if($stmt_insert) {
            $stmt_insert->bind_param("ssi", $email, $token, $expires); // Store Unix timestamp for expires
            if ($stmt_insert->execute()) {
                $resetLink = "http://localhost/your_project_folder/reset_password.php?token=" . $token; // CHANGE THIS URL

                // ---- START PHPMailer (Simplified Example - requires setup) ----
                // $mail = new PHPMailer(true);
                // try {
                //     //Server settings
                //     $mail->isSMTP();
                //     $mail->Host       = 'smtp.example.com'; // Set the SMTP server to send through
                //     $mail->SMTPAuth   = true;
                //     $mail->Username   = 'your_email@example.com'; // SMTP username
                //     $mail->Password   = 'your_email_password'; // SMTP password
                //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                //     $mail->Port       = 587;

                //     //Recipients
                //     $mail->setFrom('no-reply@yourdomain.com', 'BU-CPMS');
                //     $mail->addAddress($email, $user['Name']);

                //     //Content
                //     $mail->isHTML(true);
                //     $mail->Subject = 'Password Reset Request - BU-CPMS';
                //     $mail->Body    = "Hello " . htmlspecialchars($user['Name']) . ",<br><br>Someone requested a password reset for your account. If this was you, click the link below to reset your password. This link is valid for 30 minutes:<br><a href='" . $resetLink . "'>" . $resetLink . "</a><br><br>If you did not request this, please ignore this email.<br><br>Thanks,<br>The BU-CPMS Team";
                //     $mail->AltBody = "Hello " . htmlspecialchars($user['Name']) . ",\n\nSomeone requested a password reset for your account. If this was you, copy and paste the following link into your browser to reset your password. This link is valid for 30 minutes:\n" . $resetLink . "\n\nIf you did not request this, please ignore this email.\n\nThanks,\nThe BU-CPMS Team";

                //     $mail->send();
                //     $message = 'Password reset instructions have been sent to your email address.';
                //     $messageType = 'success';
                // } catch (Exception $e) {
                //     $message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}. Please try again later or contact support.";
                //     // For debugging, you might show the reset link directly if email fails in dev
                //     // $message .= "<br>DEV ONLY: Reset link: <a href='$resetLink'>$resetLink</a>";
                // }
                // ---- END PHPMailer ----

                // Fallback if email sending is not set up (for development)
                 $message = 'Password reset link would be sent (email functionality not live). For testing, use this link: <a href="' . $resetLink . '">' . $resetLink . '</a>';
                 $messageType = 'success';


            } else {
                $message = "Error storing reset token: " . $stmt_insert->error;
            }
            $stmt_insert->close();
        } else {
             $message = "Error preparing token storage: " . $conn->error;
        }

    } else {
        $message = "No account found with that email address. Please check your email or sign up.";
    }
    $stmt_user->close();
    $conn->close();
} else {
    $message = "Please enter your email address.";
}

// Store message in session and redirect back to forgot-password.html to display it
// Or output directly if not redirecting. For simplicity here:
$_SESSION['forgot_password_message'] = $message;
$_SESSION['forgot_password_message_type'] = $messageType;
header("Location: forgot-password-status.php"); // A page to display this message
exit;

?>