<?php
// REPLACE 'YourSecretAdminPasswordHere' with the actual password you want to use for your admin.
$plainTextPassword = '1212';

// Generate the hash
$hashedPassword = password_hash($plainTextPassword, PASSWORD_DEFAULT);

echo "<h3>Your Hashed Password:</h3>";
echo "<p>" . htmlspecialchars($hashedPassword) . "</p>";
echo "<p>Copy the string above (including the '$2y$' part) and paste it into the 'Password' field for your admin user in phpMyAdmin.</p>";
echo "<p><strong>IMPORTANT:</strong> Delete this 'hash_generator.php' file or remove this code after you're done!</p>";
?>