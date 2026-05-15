<?php 
require 'config.php'; // Get DB connection and Razorpay keys
require 'header.php'; // Include the standard header

$order_uid = $_GET['order_uid'] ?? null;
$order_data = null;
$remaining_amount_paise = 0;
$remaining_amount_rupees = 0;
$error_message = null;
$user_owns_order = false;

if (!$order_uid) {
    $error_message = "Order ID is missing.";
} else {
    try {
        // Fetch order details using the unique ID
        $stmt = $pdo->prepare("
            SELECT o.*, s.name as service_name, u.name as user_name, u.email as user_email, u.phone as user_phone 
            FROM orders o 
            JOIN services s ON o.service_id = s.id
            JOIN users u ON o.user_id = u.id
            WHERE o.order_uid = ?");
        $stmt->execute([$order_uid]);
        $order_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order_data) {
            $error_message = "Order not found.";
        } else {
            // Check if the logged-in user owns this order
            if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true && $_SESSION['user_id'] == $order_data['user_id']) {
                $user_owns_order = true;
            }

            if ($order_data['status'] !== 'Awaiting Final Payment') {
                 $error_message = "This order does not require final payment at this time (Status: {$order_data['status']}).";
            } else {
                $remaining_amount_paise = $order_data['total_service_price'] - $order_data['amount_paid'];
                if ($remaining_amount_paise <= 0) {
                     $error_message = "No remaining balance found for this order.";
                } else {
                     $remaining_amount_rupees = $remaining_amount_paise / 100;
                }
            }
        }
    } catch(PDOException $e) {
         $error_message = "Database error fetching order details.";
         // Log $e->getMessage() for debugging
    }
}

?>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>document.title = "Pay Remaining Balance - CodeMistry";</script>
<style> body { overflow-x: auto; } </style> <main class="container mx-auto px-4 py-8 md:py-16">
    <div class="max-w-lg mx-auto bg-gray-800/50 p-8 rounded-2xl border border-gray-700">
        <h1 class="text-3xl font-bold text-white text-center mb-6">Pay Remaining Balance</h1>

        <?php if ($error_message): ?>
            <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg text-center">
                <p><?php echo htmlspecialchars($error_message); ?></p>
                <?php if (!$user_owns_order && $order_data): ?>
                 <p class="mt-2 text-sm">Please <a href="login.php" class="font-bold underline">log in</a> to pay for your order.</p>
                <?php endif; ?>
                 <p class="mt-2 text-sm"><a href="index.php" class="font-bold underline">Go to Homepage</a></p>
            </div>
        <?php elseif ($order_data): ?>
            <div class="space-y-4 mb-6 text-center">
                <p class="text-gray-400">Order ID: <span class="font-mono text-white"><?php echo htmlspecialchars($order_data['order_uid']); ?></span></p>
                <p class="text-gray-400">Service: <span class="text-white"><?php echo htmlspecialchars($order_data['service_name']); ?></span></p>
                <p class="text-lg text-gray-300">Amount Due:</p>
                <p class="text-4xl font-extrabold text-yellow-400">₹<?php echo number_format($remaining_amount_rupees); ?></p>
            </div>

            <?php if ($user_owns_order): ?>
                <button id="pay-final-button" class="w-full bg-green-600 hover:bg-green-700 text-white font-extrabold py-4 rounded-lg text-lg transition-colors">
                    Pay Now Securely
                </button>
                <div id="payment-status" class="mt-4 text-center"></div>

                <script>
                    const payButton = document.getElementById('pay-final-button');
                    const paymentStatus = document.getElementById('payment-status');
                    const orderUid = '<?php echo $order_uid; ?>';
                    const amountPaise = <?php echo $remaining_amount_paise; ?>;
                    const userName = '<?php echo addslashes($order_data['user_name']); ?>';
                    const userEmail = '<?php echo addslashes($order_data['user_email']); ?>';
                    const userPhone = '<?php echo addslashes($order_data['user_phone']); ?>';
                    const serviceName = '<?php echo addslashes($order_data['service_name']); ?>';

                    payButton.addEventListener('click', function() {
                        payButton.disabled = true;
                        payButton.textContent = 'Processing...';
                        paymentStatus.textContent = '';

                        const options = {
                            key: "<?php echo RAZORPAY_KEY_ID; ?>",
                            amount: amountPaise, 
                            currency: "INR",
                            name: "CodeMistry",
                            description: `Final Payment for ${serviceName} (Order ${orderUid})`,
                            image: "https://i.imgur.com/g237s54.png", // Your logo
                            handler: function (response) {
                                // Payment successful, verify on backend
                                paymentStatus.innerHTML = '<p class="text-yellow-400">Verifying payment...</p>';
                                verifyPayment(response.razorpay_payment_id);
                            },
                            prefill: {
                                name: userName,
                                email: userEmail,
                                contact: userPhone
                            },
                            theme: { color: "#3b82f6" },
                            modal: {
                                ondismiss: function() {
                                    payButton.disabled = false;
                                    payButton.textContent = 'Pay Now Securely';
                                    paymentStatus.innerHTML = '<p class="text-sm text-gray-500">Payment cancelled.</p>';
                                }
                            }
                        };
                        const rzp = new Razorpay(options);
                        rzp.open();
                    });

                    function verifyPayment(paymentId) {
                         const formData = new FormData();
                         formData.append('razorpay_payment_id', paymentId);
                         formData.append('order_uid', orderUid);

                         fetch('api.php?action=verify_remaining_payment', {
                             method: 'POST',
                             body: formData
                         })
                         .then(response => response.json())
                         .then(data => {
                             if (data.success) {
                                 paymentStatus.innerHTML = '<p class="text-green-400">Payment Successful! Redirecting to dashboard...</p>';
                                 payButton.style.display = 'none'; // Hide pay button
                                 setTimeout(() => {
                                     window.location.href = 'dashboard.php'; 
                                 }, 3000);
                             } else {
                                 paymentStatus.innerHTML = `<p class="text-red-400">Payment Verification Failed: ${data.message || 'Unknown error'}. Please contact support.</p>`;
                                 payButton.disabled = false;
                                 payButton.textContent = 'Try Again';
                             }
                         })
                         .catch(error => {
                             paymentStatus.innerHTML = '<p class="text-red-400">An error occurred during verification. Please contact support.</p>';
                             payButton.disabled = false;
                             payButton.textContent = 'Try Again';
                         });
                    }
                </script>

            <?php else: ?>
                 <div class="bg-blue-900/50 border border-blue-700 text-blue-300 px-4 py-3 rounded-lg text-center">
                    <p>Please <a href="login.php?redirect=pay-remaining.php?order_uid=<?php echo urlencode($order_uid); ?>" class="font-bold underline">log in</a> to complete the payment for this order.</p>
                 </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</main>

<?php require 'footer.php'; ?>