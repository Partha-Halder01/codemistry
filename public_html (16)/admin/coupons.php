<?php
require_once 'admin-auth.php';
require_admin_login();

$error = null;
try {
    $coupons_list = $pdo->query("SELECT * FROM coupons ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

require 'header.php'; 
?>

<h1 class="text-3xl font-bold text-white mb-6">Manage Coupons</h1>

<div id="status-message" class="hidden fixed top-20 right-8 z-50 p-4 rounded-lg shadow-lg text-white"></div>

<?php if ($error): ?>
    <div class="bg-red-800 border border-red-600 text-red-200 p-4 rounded-lg mb-6">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-1">
        <form id="coupon-form" class="bg-gray-800/50 border border-gray-700 p-6 rounded-2xl space-y-4">
            <h2 class="text-2xl font-bold text-white mb-4">Add New Coupon</h2>
            
            <input type="hidden" name="action" value="add_coupon">
            
            <div>
                <label for="coupon_code" class="block text-sm font-medium text-gray-300 mb-2">Coupon Code</label>
                <input type="text" name="coupon_code" id="coupon_code" required class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600 uppercase font-mono tracking-wider">
            </div>
            <div>
                <label for="discount_percentage" class="block text-sm font-medium text-gray-300 mb-2">Discount (%)</label>
                <input type="number" name="discount_percentage" id="discount_percentage" required min="1" max="100" class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600">
            </div>
            <div>
                <label for="use_limit" class="block text-sm font-medium text-gray-300 mb-2">Usage Limit</label>
                <input type="number" name="use_limit" id="use_limit" required min="1" class="w-full bg-gray-700 text-white p-3 rounded-lg border border-gray-600">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg">Add Coupon</button>
        </form>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-gray-800/50 border border-gray-700 rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-800 border-b border-gray-700">
                    <tr>
                        <th class="p-4 text-sm font-semibold text-gray-300">Code</th>
                        <th class="p-4 text-sm font-semibold text-gray-300">Discount</th>
                        <th class="p-4 text-sm font-semibold text-gray-300">Usage</th>
                        <th class="p-4 text-sm font-semibold text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700" id="coupons-table-body">
                    <?php if (empty($coupons_list)): ?>
                        <tr><td colspan="4" class="p-4 text-center text-gray-400">No coupons found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($coupons_list as $coupon): ?>
                        <tr id="coupon-row-<?php echo $coupon['id']; ?>">
                            <td class="p-4 font-mono text-yellow-400"><?php echo htmlspecialchars($coupon['coupon_code']); ?></td>
                            <td class="p-4 font-medium"><?php echo $coupon['discount_percentage']; ?>%</td>
                            <td class="p-4 text-sm"><?php echo $coupon['times_used']; ?> / <?php echo $coupon['use_limit']; ?></td>
                            <td class="p-4">
                                <button onclick="deleteCoupon(<?php echo $coupon['id']; ?>)" class="bg-red-600 hover:bg-red-700 text-white text-sm font-medium py-1 px-3 rounded-lg">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function showStatusMessage(message, isSuccess = true) {
        const statusMessage = document.getElementById('status-message');
        statusMessage.textContent = message;
        statusMessage.className = `fixed top-20 right-8 z-50 p-4 rounded-lg shadow-lg text-white ${isSuccess ? 'bg-green-600' : 'bg-red-600'}`;
        statusMessage.classList.remove('hidden');
        setTimeout(() => {
            statusMessage.classList.add('hidden');
        }, 3000);
    }

    document.getElementById('coupon-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        const response = await apiRequest('add_coupon', formData);
        
        if (response.success) {
            showStatusMessage('Coupon added successfully!', true);
            // Reload the page
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showStatusMessage(response.message || 'An error occurred.', false);
        }
    });

    async function deleteCoupon(id) {
        if (!confirm('Are you sure you want to delete this coupon?')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('coupon_id', id);
        
        const response = await apiRequest('delete_coupon', formData);
        
        if (response.success) {
            showStatusMessage('Coupon deleted successfully!', true);
            const row = document.getElementById(`coupon-row-${id}`);
            if (row) {
                row.remove();
            }
        } else {
            showStatusMessage(response.message || 'Failed to delete coupon.', false);
        }
    }
</script>

<?php require 'footer.php'; ?>
