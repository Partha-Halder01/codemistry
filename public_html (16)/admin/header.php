<?php
// This header assumes 'admin-auth.php' has already been included
// and require_admin_login() has been called.
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - CodeMistry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0c0a18; }
        .nav-link {
            display: block; padding: 0.75rem 1.5rem; border-radius: 0.5rem;
            color: #d1d5db; transition: background-color 0.2s, color 0.2s;
        }
        .nav-link:hover { background-color: #374151; color: white; }
        .nav-link.active { background-color: #3b82f6; color: white; font-weight: 600; }
        /* Custom scrollbar for webkit */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #1f2937; }
        ::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="bg-gray-900 text-gray-200">
    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0 flex flex-col border-r border-gray-700">
            <div class="h-16 flex items-center justify-center px-4 border-b border-gray-700">
                <a href="index.php" class="text-2xl font-bold text-white">Code<span class="text-blue-500">Mistry</span></a>
            </div>
            <nav class="flex-1 overflow-y-auto p-4 space-y-2">
                <a href="index.php" class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Dashboard</a>
                <a href="orders.php" class="nav-link <?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">Orders</a>
                <a href="services.php" class="nav-link <?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">Services</a>
                <a href="coupons.php" class="nav-link <?php echo ($current_page == 'coupons.php') ? 'active' : ''; ?>">Coupons</a>
                <a href="tickets.php" class="nav-link <?php echo ($current_page == 'tickets.php') ? 'active' : ''; ?>">Tickets</a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <p class="text-sm text-gray-400">Logged in as:</p>
                <p class="font-semibold text-white"><?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin'; ?></p>
                <a href="logout.php" class="mt-2 block text-center w-full nav-link bg-red-600/50 hover:bg-red-600 text-white font-medium">Logout</a>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto p-6 md:p-8">
          

<
</body>
</html>