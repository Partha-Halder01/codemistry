<?php
require_once 'admin-auth.php';
require_admin_login();

$error = null;
try {
    // Fetch all orders with user and service info
    $orders_list = $pdo->query("
        SELECT
            o.id, o.order_uid, o.status, o.order_date,
            u.name as user_name,
            s.name as service_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN services s ON o.service_id = s.id
        ORDER BY o.order_date DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

require 'header.php';
?>
<style>
    .order-card.active { border-color: #3b82f6; background-color: rgba(59, 130, 246, 0.1); }
    .order-card { transition: border-color 0.3s, background-color 0.3s; }
    .chat-bubble-user { background-color: #1e40af; }
    .chat-bubble-admin { background-color: #374151; }
    .details-grid { display: grid; grid-template-columns: auto 1fr; gap: 0.5rem 1rem; align-items: center;} /* Style for details */
    .details-label { font-weight: 600; color: #9ca3af; text-align: right; }
    .details-value { color: #e5e7eb; word-break: break-all; } /* Allow long values to wrap */
</style>

<h1 class="text-3xl font-bold text-white mb-6">Manage Orders</h1>

<?php if ($error): ?>
    <div class="bg-red-800 border border-red-600 text-red-200 p-4 rounded-lg mb-6">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-1">
        <div class="bg-gray-800/50 border border-gray-700 rounded-2xl h-[80vh] flex flex-col">
            <h2 class="text-xl font-bold text-white p-4 border-b border-gray-700">All Orders</h2>
            <div class="overflow-y-auto space-y-3 p-3">
                <?php if (empty($orders_list)): ?>
                    <p class="p-4 text-center text-gray-400">No orders found.</p>
                <?php endif; ?>

                <?php foreach ($orders_list as $order): ?>
                    <div id="order-card-<?php echo $order['id']; ?>"
                         class="order-card bg-gray-800 p-4 rounded-lg border border-gray-700 cursor-pointer hover:border-blue-500"
                         onclick="selectOrder(<?php echo $order['id']; ?>)">

                        <div class="flex justify-between items-center mb-2 gap-2">
                            <p class="font-bold text-white text-md truncate" title="<?php echo htmlspecialchars($order['service_name']); ?>">
                                <?php echo htmlspecialchars($order['service_name']); ?>
                            </p>
                            <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php echo ($order['status'] == 'Paid' || $order['status'] == 'Completed') ? 'bg-green-700 text-green-200' : 'bg-yellow-700 text-yellow-200'; ?>
                                whitespace-nowrap">
                                <?php echo htmlspecialchars($order['status']); ?>
                            </span>
                        </div>
                        <p class="text-sm text-gray-400">User: <span class="text-gray-200"><?php echo htmlspecialchars($order['user_name']); ?></span></p>
                        <p class="text-sm text-gray-400">ID: <span class="font-mono text-gray-200"><?php echo htmlspecialchars($order['order_uid']); ?></span></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 flex flex-col gap-6">
        <div id="order-details-section" class="bg-gray-800/50 rounded-2xl border border-gray-700">
            <div class="p-4 border-b border-gray-700">
                <h2 id="details-header" class="text-xl font-bold text-white">Order Details</h2>
            </div>
            <div id="details-content" class="p-6 text-sm">
                <p class="text-gray-400 text-center">Select an order to view its details.</p>
                </div>
        </div>

        <div id="chat-section" class="bg-gray-800/50 rounded-2xl border border-gray-700 flex flex-col flex-1 min-h-[400px]">
             <div class="p-4 border-b border-gray-700">
                <h2 id="chat-header" class="text-xl font-bold text-white">Chat</h2>
            </div>

            <div id="chat-box" class="flex-1 p-6 space-y-4 overflow-y-auto">
                <p class="text-gray-400 text-center">Select an order to view chat history.</p>
            </div>

            <div class="p-4 border-t border-gray-700">
                <form id="chat-form" class="flex space-x-4">
                    <input type="hidden" id="chat-order-id" name="order_id" value="">
                    <input type="text" id="chat-message" name="message" placeholder="Type your message..." required
                           class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600 focus:ring-blue-500 focus:border-blue-500" disabled>
                    <button type="submit" id="chat-submit-button"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg disabled:bg-gray-500 disabled:cursor-not-allowed" disabled>
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let activeOrderId = null;
let chatPollInterval = null;
const chatForm = document.getElementById('chat-form');
const chatInput = document.getElementById('chat-message');
const chatSubmit = document.getElementById('chat-submit-button');
const chatBox = document.getElementById('chat-box');
const chatHeader = document.getElementById('chat-header');
const chatOrderIdInput = document.getElementById('chat-order-id');
const detailsHeader = document.getElementById('details-header');
const detailsContent = document.getElementById('details-content');

function selectOrder(orderId) {
    if (activeOrderId === orderId) return;
    activeOrderId = orderId;

    // Update UI
    document.querySelectorAll('.order-card').forEach(card => card.classList.remove('active'));
    const activeCard = document.getElementById(`order-card-${orderId}`);
    if (activeCard) {
        activeCard.classList.add('active');
    }

    // Reset details and chat areas
    detailsHeader.textContent = `Loading Details for Order #${orderId}...`;
    detailsContent.innerHTML = '<p class="text-gray-400 text-center">Loading...</p>';
    chatHeader.textContent = `Loading Chat for Order #${orderId}...`;
    chatBox.innerHTML = '<p class="text-gray-400 text-center">Loading...</p>';
    chatInput.disabled = true;
    chatSubmit.disabled = true;
    chatOrderIdInput.value = orderId;

    // Load both details and chat
    loadOrderDetails(orderId);
    loadChatMessages(orderId);

    // Clear previous poll and start new one
    if (chatPollInterval) clearInterval(chatPollInterval);
    chatPollInterval = setInterval(() => loadChatMessages(activeOrderId), 10000); // Poll every 10 seconds
}

async function loadOrderDetails(orderId) {
    if (!orderId) {
        detailsContent.innerHTML = '<p class="text-gray-400 text-center">Select an order to view its details.</p>';
        return;
    }

    const formData = new FormData();
    formData.append('order_id', orderId);

    const response = await apiRequest('get_order_details', formData);

    if (response.success && response.order) {
        const order = response.order;
        const orderDate = new Date(order.order_date).toLocaleString('en-IN', {
            day: 'numeric', month: 'short', year: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true
        });
        const totalAmount = (order.total_service_price / 100).toLocaleString('en-IN', { style: 'currency', currency: 'INR' });
        const paidAmount = (order.amount_paid / 100).toLocaleString('en-IN', { style: 'currency', currency: 'INR' });
        const remainingAmount = (order.remaining_amount / 100).toLocaleString('en-IN', { style: 'currency', currency: 'INR' });
        const paymentIds = order.razorpay_payment_id ? order.razorpay_payment_id.split(',').join('<br>') : 'N/A';

        detailsHeader.textContent = `Details for Order ${order.order_uid}`;
        detailsContent.innerHTML = `
            <div class="details-grid">
                <span class="details-label">Order UID:</span>
                <span class="details-value font-mono">${order.order_uid}</span>

                <span class="details-label">Customer:</span>
                <span class="details-value">${order.user_name}</span>

                <span class="details-label">Email:</span>
                <span class="details-value"><a href="mailto:${order.user_email}" class="text-blue-400 hover:underline">${order.user_email}</a></span>

                <span class="details-label">Phone:</span>
                <span class="details-value"><a href="tel:${order.user_phone}" class="text-blue-400 hover:underline">${order.user_phone}</a></span>

                <span class="details-label">Service:</span>
                <span class="details-value">${order.service_name}</span>

                <span class="details-label">Date Placed:</span>
                <span class="details-value">${orderDate}</span>

                <span class="details-label">Status:</span>
                <span class="details-value font-semibold ${order.status === 'Completed' || order.status === 'Paid' ? 'text-green-400' : 'text-yellow-400'}">${order.status}</span>

                <span class="details-label">Payment Plan:</span>
                <span class="details-value capitalize">${order.payment_type}</span>

                <span class="details-label">Total Price:</span>
                <span class="details-value">${totalAmount}</span>

                <span class="details-label">Amount Paid:</span>
                <span class="details-value text-green-400">${paidAmount}</span>

                <span class="details-label">Remaining:</span>
                <span class="details-value ${order.remaining_amount > 0 ? 'text-yellow-400' : 'text-gray-400'}">${remainingAmount}</span>

                <span class="details-label">Coupon Used:</span>
                <span class="details-value font-mono">${order.coupon_code || 'None'}</span>

                <span class="details-label">Payment ID(s):</span>
                <span class="details-value font-mono text-xs">${paymentIds}</span>
            </div>
        `;
    } else {
        detailsHeader.textContent = `Order Details`;
        detailsContent.innerHTML = `<p class="text-red-400 text-center">Failed to load details: ${response.message || 'Unknown error'}</p>`;
    }
}

async function loadChatMessages(orderId) {
    // ...(keep the existing loadChatMessages function as it is)...
    if (!orderId) {
        chatBox.innerHTML = '<p class="text-gray-400 text-center">Select an order to view chat history.</p>';
        return;
    }

    const isFirstLoad = !chatBox.querySelector('.chat-bubble-user, .chat-bubble-admin');
    if (isFirstLoad) {
        chatBox.innerHTML = '<p class="text-gray-400 text-center">Loading chat history...</p>';
    }

    const formData = new FormData();
    formData.append('order_id', orderId);

    const response = await apiRequest('get_admin_chat_messages', formData);

    if (response.success) {
        // Find the corresponding order details from the already fetched list to get UID
        const currentOrderFromList = <?php echo json_encode($orders_list); ?>.find(o => o.id == orderId);
        const orderUidDisplay = currentOrderFromList ? `#${currentOrderFromList.order_uid}` : `#${orderId}`;

        chatHeader.textContent = `Chat for Order ${orderUidDisplay}`; // Update header on success
        chatInput.disabled = false;
        chatSubmit.disabled = false;

        const newMessagesHtml = response.messages.map(msg => {
            const isUser = msg.sender === 'user';
            // Format time nicely
            const messageTime = new Date(msg.created_at).toLocaleTimeString('en-IN', { hour: 'numeric', minute: '2-digit', hour12: true });
            return `
                <div class="flex ${isUser ? 'justify-end' : 'justify-start'} w-full">
                    <div class="max-w-xs md:max-w-md lg:max-w-lg">
                        <div class="${isUser ? 'chat-bubble-user' : 'chat-bubble-admin'} rounded-lg p-3 shadow-md">
                            <p class="text-sm text-white break-words">${msg.message.replace(/\n/g, '<br>')}</p>
                        </div>
                        <p class="text-xs ${isUser ? 'text-right' : 'text-left'} text-gray-500 mt-1 px-1">${messageTime}</p>
                    </div>
                </div>`;
        }).join('');

        chatBox.innerHTML = newMessagesHtml || '<p class="text-gray-400 text-center">No messages yet.</p>';
        // Scroll to bottom only if it was the first load or user was already near the bottom
        const shouldScroll = isFirstLoad || (chatBox.scrollTop + chatBox.clientHeight + 100) >= chatBox.scrollHeight;
        if(shouldScroll){
             chatBox.scrollTop = chatBox.scrollHeight;
        }

    } else {
        chatHeader.textContent = `Could not load chat`;
        chatBox.innerHTML = `<p class="text-red-400 text-center">Failed to load chat: ${response.message || 'Unknown error'}</p>`;
    }
}


chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = chatInput.value.trim();
    if (!message || !activeOrderId) return;

    chatInput.disabled = true;
    chatSubmit.disabled = true;

    const formData = new FormData();
    formData.append('order_id', activeOrderId);
    formData.append('message', message);

    const response = await apiRequest('send_admin_message', formData);

    if (response.success) {
        chatInput.value = ''; // Clear input
        loadChatMessages(activeOrderId); // Reload chat
    } else {
        alert('Failed to send message: ' + (response.message || 'Unknown error'));
    }

    // Re-enable only if the same order is still active
    if(activeOrderId == document.getElementById('chat-order-id').value) {
        chatInput.disabled = false;
        chatSubmit.disabled = false;
        chatInput.focus();
    }
});

// Select the first order on page load if any orders exist
document.addEventListener('DOMContentLoaded', () => {
    const firstOrderCard = document.querySelector('.order-card');
    if (firstOrderCard) {
        const firstOrderId = firstOrderCard.id.replace('order-card-', '');
        if (firstOrderId) {
            selectOrder(parseInt(firstOrderId));
        }
    }
});

</script>

<?php require 'footer.php'; ?>