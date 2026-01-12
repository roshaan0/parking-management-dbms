<?php
session_start();
require_once 'db.php';

$token = $_GET['token'] ?? null;
$message = '';
$messageType = 'error';
$showForm = false;

if ($token) {
    $sql_check_token = "SELECT Email, ExpiresAt FROM PasswordResets WHERE Token = ? AND ExpiresAt >= ?";
    $stmt_check = $conn->prepare($sql_check_token);
    $currentTime = date("U");
    $stmt_check->bind_param("si", $token, $currentTime);
    $stmt_check->execute();
    $result_token = $stmt_check->get_result();

    if ($result_token->num_rows === 1) {
        $tokenData = $result_token->fetch_assoc();
        $email = $tokenData['Email'];
        $showForm = true; // Valid token, show password reset form
    } else {
        $message = "Invalid or expired password reset token. Please request a new one.";
    }
    $stmt_check->close();
} else {
    $message = "No reset token provided.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['token'], $_POST['password'], $_POST['confirm_password'])) {
    $postedToken = $_POST['token'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $showForm = true; // Keep form visible on POST error

    // Re-validate token on POST
    $sql_recheck_token = "SELECT Email, ExpiresAt FROM PasswordResets WHERE Token = ? AND ExpiresAt >= ?";
    $stmt_recheck = $conn->prepare($sql_recheck_token);
    $currentTimePost = date("U");
    $stmt_recheck->bind_param("si", $postedToken, $currentTimePost);
    $stmt_recheck->execute();
    $result_recheck_token = $stmt_recheck->get_result();

    if ($result_recheck_token->num_rows === 1) {
        $tokenDataPost = $result_recheck_token->fetch_assoc();
        $emailForUpdate = $tokenDataPost['Email'];

        if ($newPassword === $confirmPassword) {
            if (strlen($newPassword) >= 6) { // Basic password strength
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $sql_update_password = "UPDATE User SET Password = ? WHERE Email = ?";
                $stmt_update = $conn->prepare($sql_update_password);
                $stmt_update->bind_param("ss", $hashedPassword, $emailForUpdate);

                if ($stmt_update->execute()) {
                    // Password updated, now invalidate the token
                    $sql_delete_token = "DELETE FROM PasswordResets WHERE Email = ?";
                    $stmt_delete = $conn->prepare($sql_delete_token);
                    $stmt_delete->bind_param("s", $emailForUpdate);
                    $stmt_delete->execute();
                    $stmt_delete->close();

                    $message = "Your password has been reset successfully! You can now <a href='Login.html'>log in</a>.";
                    $messageType = 'success';
                    $showForm = false; // Hide form on success
                } else {
                    $message = "Error updating password: " . $stmt_update->error;
                }
                $stmt_update->close();
            } else {
                $message = "Password must be at least 6 characters long.";
            }
        } else {
            $message = "Passwords do not match.";
        }
    } else {
         $message = "Invalid or expired token for password update. Please request a new reset.";
         $showForm = false;
    }
    $stmt_recheck->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - BU-CPMS</title>
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; }
        h1 { margin-bottom: 20px; color: #333; }
        .form-group { margin-bottom: 15px; text-align: left; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="password"] { width: calc(100% - 22px); padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #3498db; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; width: 100%; }
        button:hover { background-color: #2980b9; }
        .message { margin-top: 15px; padding: 10px; border-radius: 4px; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
        .login-link { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Your Password</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo $message; // Allow HTML for link in success message ?>
            </div>
        <?php endif; ?>

        <?php if ($showForm): ?>
            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>

        <?php if (!$showForm && $messageType !== 'success'): // Show login link if form is hidden due to error but not success ?>
            <div class="login-link">
                <a href="Login.html">Back to Login</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>