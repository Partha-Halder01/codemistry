<?php
require_once 'admin-auth.php';
require_admin_login();

// Fetch stats
try {
    $stats = [];
    $stats['pending_orders'] = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Paid' OR status = 'Awaiting Final Payment'")->fetchColumn();
    $stats['open_tickets'] = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'open'")->fetchColumn();
    $stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['total_services'] = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
} catch (PDOException $e) {
    $error = "Failed to load dashboard stats: " . $e->getMessage();
}

require 'header.php'; 
?>

<h1 class="text-3xl font-bold text-white mb-6">Admin Dashboard</h1>

<?php if (isset($error)): ?>
    <div class="bg-red-800 border border-red-600 text-red-200 p-4 rounded-lg mb-6">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <a href="orders.php" class="bg-gray-800/50 border border-gray-700 p-6 rounded-2xl hover:border-blue-500 transition-colors">
            <h2 class="text-lg font-semibold text-gray-400">Pending Orders</h2>
            <p class="text-4xl font-extrabold text-white mt-2"><?php echo $stats['pending_orders']; ?></p>
        </a>
        <a href="tickets.php" class="bg-gray-800/50 border border-gray-700 p-6 rounded-2xl hover:border-yellow-500 transition-colors">
            <h2 class="text-lg font-semibold text-gray-400">Open Tickets</h2>
            <p class="text-4xl font-extrabold text-white mt-2"><?php echo $stats['open_tickets']; ?></p>
        </a>
        <div class="bg-gray-800/50 border border-gray-700 p-6 rounded-2xl">
            <h2 class="text-lg font-semibold text-gray-400">Total Users</h2>
            <p class="text-4xl font-extrabold text-white mt-2"><?php echo $stats['total_users']; ?></p>
        </div>
        <a href="services.php" class="bg-gray-800/50 border border-gray-700 p-6 rounded-2xl hover:border-green-500 transition-colors">
            <h2 class="text-lg font-semibold text-gray-400">Total Services</h2>
            <p class="text-4xl font-extrabold text-white mt-2"><?php echo $stats['total_services']; ?></p>
        </a>
    </div>

    <h2 class="text-2xl font-bold text-white mb-4">Quick Actions</h2>
    <div class="flex flex-wrap gap-4">
        <a href="orders.php" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-5 rounded-lg">View All Orders</a>
        <a href="services.php" class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-5 rounded-lg">Manage Services</a>
    </div>
<?php endif; ?>

<?php require 'footer.php'; ?>