<?php
require_once 'admin-auth.php';
require_admin_login(); // Ensure admin is logged in

$error = null;
$filter_status = $_GET['status'] ?? 'open'; // Default to showing 'open' tickets

try {
    // Prepare SQL query with a placeholder for the status
    $sql = "SELECT id, ticket_uid, name, phone, email, message, status, created_at 
            FROM tickets";
            
    if ($filter_status === 'open') {
        $sql .= " WHERE status = 'open'";
    } elseif ($filter_status === 'closed') {
         $sql .= " WHERE status = 'closed'";
    } // else 'all' - no WHERE clause needed
    
    $sql .= " ORDER BY created_at DESC"; // Order by most recent first
    
    $stmt = $pdo->query($sql); // Execute the constructed query
    $tickets_list = $stmt->fetchAll();

} catch (PDOException $e) {
    $error = "Database error fetching tickets: " . $e->getMessage();
}

require 'header.php'; // Include admin header
?>

<h1 class="text-3xl font-bold text-white mb-6">Manage Support Tickets</h1>

<div id="status-message" class="hidden fixed top-20 right-8 z-50 p-4 rounded-lg shadow-lg text-white max-w-sm"></div>

<?php if ($error): ?>
    <div class="bg-red-800 border border-red-600 text-red-200 p-4 rounded-lg mb-6">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="mb-4 flex space-x-2">
    <a href="tickets.php?status=open" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo ($filter_status === 'open') ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'; ?>">
        Open Tickets
    </a>
    <a href="tickets.php?status=closed" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo ($filter_status === 'closed') ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'; ?>">
        Closed Tickets
    </a>
     <a href="tickets.php?status=all" class="px-4 py-2 rounded-lg text-sm font-medium <?php echo ($filter_status === 'all') ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'; ?>">
        All Tickets
    </a>
</div>

<div class="bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden">
    <table class="w-full text-left min-w-[700px]">
        <thead class="bg-gray-800 border-b border-gray-700">
            <tr>
                <th class="p-4 text-sm font-semibold text-gray-300">Ticket ID</th>
                <th class="p-4 text-sm font-semibold text-gray-300">User Info</th>
                <th class="p-4 text-sm font-semibold text-gray-300">Message</th>
                <th class="p-4 text-sm font-semibold text-gray-300">Status</th>
                <th class="p-4 text-sm font-semibold text-gray-300">Received</th>
                <th class="p-4 text-sm font-semibold text-gray-300">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            <?php if (empty($tickets_list)): ?>
                <tr><td colspan="6" class="p-6 text-center text-gray-400">No tickets found matching this filter.</td></tr>
            <?php endif; ?>
            
            <?php foreach ($tickets_list as $ticket): ?>
                <tr id="ticket-row-<?php echo $ticket['id']; ?>">
                    <td class="p-4 font-mono text-sm text-yellow-400 align-top"><?php echo htmlspecialchars($ticket['ticket_uid']); ?></td>
                    <td class="p-4 text-sm align-top">
                        <p class="font-medium text-white"><?php echo htmlspecialchars($ticket['name']); ?></p>
                        <p class="text-gray-400"><?php echo htmlspecialchars($ticket['phone']); ?></p>
                        <?php if ($ticket['email']): ?>
                            <p class="text-gray-400 truncate max-w-[150px]"><?php echo htmlspecialchars($ticket['email']); ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="p-4 text-sm text-gray-300 align-top max-w-xs break-words">
                        <?php echo nl2br(htmlspecialchars($ticket['message'])); ?>
                    </td>
                    <td class="p-4 text-sm align-top">
                         <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                            <?php echo ($ticket['status'] == 'open') ? 'bg-yellow-700 text-yellow-200' : 'bg-green-700 text-green-200'; ?>">
                            <?php echo ucfirst(htmlspecialchars($ticket['status'])); ?>
                        </span>
                    </td>
                    <td class="p-4 text-sm text-gray-400 align-top whitespace-nowrap">
                        <?php echo date('d M Y, H:i', strtotime($ticket['created_at'])); ?>
                    </td>
                    <td class="p-4 text-sm align-top">
                        <?php if ($ticket['status'] == 'open'): ?>
                            <button onclick="updateTicketStatus(<?php echo $ticket['id']; ?>, 'closed')" 
                                    class="bg-green-600 hover:bg-green-700 text-white text-xs font-medium py-1 px-3 rounded-lg">
                                Mark Closed
                            </button>
                        <?php else: ?>
                             <button onclick="updateTicketStatus(<?php echo $ticket['id']; ?>, 'open')" 
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium py-1 px-3 rounded-lg">
                                Re-open
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Function to show status messages (like in services.php)
    function showStatusMessage(message, isSuccess = true) {
        const statusMessage = document.getElementById('status-message');
        statusMessage.textContent = message;
        statusMessage.className = `fixed top-20 right-8 z-50 p-4 rounded-lg shadow-lg text-white max-w-sm ${isSuccess ? 'bg-green-600' : 'bg-red-600'}`;
        statusMessage.classList.remove('hidden');
        setTimeout(() => {
            statusMessage.classList.add('hidden');
        }, 3000);
    }

    // Function to update ticket status via API
    async function updateTicketStatus(ticketId, newStatus) {
        if (!confirm(`Are you sure you want to mark ticket #${ticketId} as ${newStatus}?`)) {
            return;
        }
        
        const formData = new FormData();
        formData.append('ticket_id', ticketId);
        formData.append('status', newStatus);
        
        // Assuming you have the apiRequest function in admin/footer.php
        const response = await apiRequest('update_ticket_status', formData);
        
        if (response.success) {
            showStatusMessage(`Ticket status updated to ${newStatus}!`, true);
            // Optionally: Reload the page or update the row dynamically
            setTimeout(() => {
                window.location.reload(); // Simple reload for now
            }, 1000);
        } else {
            showStatusMessage(response.message || 'Failed to update status.', false);
        }
    }
</script>

<?php require 'footer.php'; // Include admin footer ?>