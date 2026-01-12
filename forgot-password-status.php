<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset Status</title>
     <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f5f5f5; font-family: 'Segoe UI', sans-serif; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; }
        .message { margin-top: 15px; padding: 10px; border-radius: 4px; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
        .login-link { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Reset Status</h1>
        <?php
        if (isset($_SESSION['forgot_password_message'])) {
            $type = $_SESSION['forgot_password_message_type'] ?? 'error';
            echo '<div class="message ' . htmlspecialchars($type) . '">' . $_SESSION['forgot_password_message'] . '</div>'; // Message can contain HTML (link)
            unset($_SESSION['forgot_password_message']);
            unset($_SESSION['forgot_password_message_type']);
        } else {
            echo '<p>No status to display.</p>';
        }
        ?>
        <div class="login-link">
            <a href="Login.html">Back to Login</a>
        </div>
         <?php if (isset($resetLinkDevOnly) && !empty($resetLinkDevOnly)): ?>
            <p style="margin-top: 15px; font-size: 0.9em; color: #555;">(Dev only) Reset Link: <a href="<?php echo htmlspecialchars($resetLinkDevOnly); ?>"><?php echo htmlspecialchars($resetLinkDevOnly); ?></a></p>
        <?php endif; ?>
    </div>
</body>
</html>