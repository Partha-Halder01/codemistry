<?php
require_once 'admin-auth.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Username and password are required.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Password is correct!
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                header('Location: index.php');
                exit;
            } else {
                $error_message = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error_message = 'A database error occurred. Please try again.';
            error_log('Admin Login Error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - CodeMistry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; background-color: #0c0a18; } </style>
</head>
<body class="bg-gray-900 text-gray-200 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-sm bg-gray-800/50 p-8 rounded-2xl border border-gray-700">
        <h1 class="text-3xl font-bold text-white text-center mb-6">CodeMistry Admin</h1>
        <form id="login-form" method="POST">
            <div class="space-y-6">
                <input type="text" id="username" name="username" placeholder="Username" required class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                <input type="password" id="password" name="password" placeholder="Password" required class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg">Login</button>
            </div>
            <?php if ($error_message): ?>
                <p id="login-status" class="mt-4 text-center text-red-400"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>