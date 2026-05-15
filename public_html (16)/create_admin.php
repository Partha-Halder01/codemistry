<?php
require 'config.php'; // Includes your $pdo connection

// --- SET YOUR ADMIN USERNAME AND PASSWORD HERE ---
$admin_username = 'admin';
$admin_password = 'partha'; // <--- CHANGE THIS
// -------------------------------------------------

$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
    $stmt->execute([$admin_username, $hashed_password]);
    echo "<h1>SUCCESS!</h1>";
    echo "<p>Admin user '<strong>" . htmlspecialchars($admin_username) . "</strong>' was created successfully.</p>";
    echo "<p>Your password is: <strong>" . htmlspecialchars($admin_password) . "</strong></p>";
    echo "<p><strong>IMPORTANT: Delete this file (create_admin.php) NOW.</strong></p>";

} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        echo "<h1>ERROR</h1><p>An admin user with the username '<strong>" . htmlspecialchars($admin_username) . "</strong>' already exists.</p>";
    } else {
        echo "<h1>Database Error</h1><p>" . $e->getMessage() . "</p>";
    }
}
?>