<?php
/**
 * CodeMistry Public & User API
 * This file handles all non-admin actions.
 */
// --- START: CRITICAL ERROR HANDLING ---
ob_start(); // Start output buffering
error_reporting(E_ALL); // Report all PHP errors
ini_set('display_errors', 0); // Do not display errors to browser

// Set error handler to prevent any output that would break JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("PHP Error: [$errno] $errstr in $errfile on line $errline");
    return true;
});

// Ensure clean JSON output
header('Content-Type: application/json; charset=utf-8');
// --- END: CRITICAL ERROR HANDLING ---

// **MUST** be the very first thing in the script
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php'; // Defines DB constants, RAZORPAY keys, GMAIL creds, BASE_URL and connects $pdo

// --- Helper Functions ---
function generateUniqueID($length = 8, $prefix = '') {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        try {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        } catch (Exception $e) {
             $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }
    }
    return $prefix . $randomString;
}

function sendUserEmail($toEmail, $toName, $subject, $body) {
    // Try to load PHPMailer via Composer autoload if available
    if (file_exists(__DIR__ . '/vendor/autoload.php')) {
        require_once __DIR__ . '/vendor/autoload.php';
    } else {
        error_log('sendUserEmail: Composer autoload not found, PHPMailer unavailable.');
    }

    // Validate email
    if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
        error_log("Attempted to send email to invalid address: " . $toEmail);
        return false;
    }

    // If PHPMailer is available, use it; otherwise log and return false
    if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
        try {
            // Use fully-qualified namespaced class name when instantiating
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = defined('GMAIL_USER') ? GMAIL_USER : '';
            $mail->Password   = defined('GMAIL_APP_PASS') ? GMAIL_APP_PASS : '';
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = 465;
            $mail->setFrom(defined('GMAIL_USER') ? GMAIL_USER : 'no-reply@example.com', 'CodeMistry');
            $mail->addAddress($toEmail, $toName);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $fullBody = "<!DOCTYPE html><html><head><style>body{font-family: sans-serif; color: #333; line-height: 1.6;} .container{padding: 20px; border: 1px solid #ddd; border-radius: 5px; max-width: 600px; margin: 20px auto; background-color: #f9f9f9;} h2{color: #0056b3;} a{color: #007bff; text-decoration: none;} a:hover{text-decoration: underline;}</style></head><body><div class='container'>" . $body . " <br><p>Thanks,<br>The CodeMistry Team</p></div></body></html>";
            $mail->Body = $fullBody;
            $mail->send();
            return true;
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            // PHPMailer's own Exception provides more context
            error_log("Mailer Error ({$toEmail}): " . ($mail->ErrorInfo ?? $e->getMessage()));
            return false;
        } catch (Exception $e) {
            error_log("Mailer Error (generic) ({$toEmail}): " . $e->getMessage());
            return false;
        }
    } else {
        // PHPMailer not installed: log the email and continue without sending
        error_log("sendUserEmail: PHPMailer not available. To: {$toEmail}, Subject: {$subject}");
        return false;
    }
}
// --- End Helper Functions ---


// --- API Action Routing ---
$action = $_GET['action'] ?? '';

// --- Logout Action ---
if ($action === 'client_logout') {
    session_unset(); 
    session_destroy(); 
    // Send JSON response for JS redirects, prevent direct PHP redirect issues
    header('Content-Type: application/json');
    ob_end_clean(); // Clean buffer before sending JSON
    echo json_encode(['success' => true, 'message' => 'Logged out', 'redirect' => 'login.php']);
    exit;
}

// --- Default to JSON responses ---
header('Content-Type: application/json');

switch ($action) {

    case 'update_user_details':
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || empty($_SESSION['user_id'])) {
            http_response_code(403);
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Authentication required.']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        // Basic Validation
        if (empty($name) || empty($phone)) {
            http_response_code(400);
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Name and Phone cannot be empty.']);
            exit;
        }
        // Optional: More robust phone validation if needed

        try {
            // Check if phone number already exists for ANOTHER user
            $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE phone = ? AND id != ?");
            $stmtCheck->execute([$phone, $user_id]);
            if ($stmtCheck->fetch()) {
                 http_response_code(409); // Conflict
                 ob_end_clean();
                 echo json_encode(['success' => false, 'message' => 'This phone number is already associated with another account.']);
                 exit;
            }


            $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
            if ($stmt->execute([$name, $phone, $user_id])) {
                // Update name in session if needed for immediate reflection? Optional.
                // $_SESSION['user_name'] = $name; // Only if you store/use it
                ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'Details updated successfully.']);
            } else {
                throw new PDOException("Failed to update user details.");
            }
        } catch (PDOException $e) {
            error_log("API Error (update_user_details for user {$user_id}): " . $e->getMessage());
            http_response_code(500);
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Database error updating details.']);
            exit;
        }
        break; // Added break
        
    case 'get_services':
        try {
            $stmt = $pdo->query("SELECT id, name, description, deposit_price, cover_image_path, rating FROM services ORDER BY id ASC");
            if (!$stmt) {
                throw new PDOException("Failed to execute query");
            }
            $services = $stmt->fetchAll();
            ob_end_clean(); // Clean buffer before any output
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => true, 'services' => $services], JSON_THROW_ON_ERROR);
            exit;
        } catch (PDOException | JsonException $e) {
            error_log("API Error (get_services): " . $e->getMessage());
            http_response_code(500);
            ob_end_clean(); // Clean buffer before any output
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Error fetching services: ' . $e->getMessage()], JSON_THROW_ON_ERROR);
            exit;
        }
        break;
        
    case 'get_service_detail':
        $service_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$service_id) { http_response_code(400); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Invalid Service ID.']); exit; }
        try {
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
            $stmt->execute([$service_id]);
            $service = $stmt->fetch();
            ob_end_clean(); // Clean buffer
            echo $service ? json_encode(['success' => true, 'service' => $service]) : json_encode(['success' => false, 'message' => 'Service not found.']);
            exit;
        } catch (PDOException $e) {
             error_log("API Error (get_service_detail): " . $e->getMessage());
             http_response_code(500);
             ob_end_clean(); // Clean buffer
             echo json_encode(['success' => false, 'message' => 'Error fetching service details.']);
             exit;
        }
        break;

    case 'apply_coupon':
        $data = json_decode(file_get_contents('php://input'), true);
        $code = strtoupper(trim($data['code'] ?? ''));
        if (!$code) { ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Coupon code cannot be empty.']); exit;}
        try {
            $stmt = $pdo->prepare("SELECT * FROM coupons WHERE coupon_code = ?");
            $stmt->execute([$code]);
            $coupon = $stmt->fetch();
            ob_end_clean(); // Clean buffer
            if (!$coupon) { echo json_encode(['success' => false, 'message' => 'Invalid coupon code.']); exit; }
            if ($coupon['times_used'] >= $coupon['use_limit']) { echo json_encode(['success' => false, 'message' => 'This coupon has reached its usage limit.']); exit; }
            echo json_encode(['success' => true, 'discount_percentage' => $coupon['discount_percentage']]);
            exit; // Added exit
        } catch (PDOException $e) {
             error_log("API Error (apply_coupon): " . $e->getMessage());
             http_response_code(500);
             ob_end_clean(); // Clean buffer
             echo json_encode(['success' => false, 'message' => 'Error applying coupon.']);
             exit; // Added exit
        }
        break;


    case 'create_order':
        $data = json_decode(file_get_contents('php://input'), true);
        // Log received data for debugging
        error_log("create_order received data: " . print_r($data, true)); 

        // Refined Validation - Check individual fields for clarity
        $errors = [];
        if (empty(trim($data['name'] ?? ''))) $errors[] = 'Name is required.';
        if (empty(trim($data['phone'] ?? ''))) $errors[] = 'Phone is required.';
        if (empty(trim($data['email'] ?? '')) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required.';
        if (empty($data['razorpay_payment_id'])) $errors[] = 'Payment ID is missing.'; // Crucial check
        if (empty($data['service_id']) || !filter_var($data['service_id'], FILTER_VALIDATE_INT)) $errors[] = 'Valid Service ID is required.';
        if (empty($data['plan']) || !in_array($data['plan'], ['full', 'deposit'])) $errors[] = 'Valid Payment Plan is required.';
        // Amount check: Allow 0 only if discount is 100% or more (sanity check)
        $amount = $data['amount'] ?? null;
        $discount = $data['discount_percentage'] ?? 0;
        if (!isset($amount) || !is_numeric($amount) || ($amount < 0) || ($amount == 0 && $discount < 100 && $data['razorpay_payment_id'] !== 'FREE_ORDER')) {
             $errors[] = 'Valid Payment Amount is required.';
        }


        if (!empty($errors)) {
            http_response_code(400); 
            // Join errors into a single message
            $errorMessage = "Missing or invalid required fields: " . implode(' ', $errors);
            error_log("create_order validation failed: " . $errorMessage . " | Data: " . print_r($data, true)); // Log validation failure
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => false, 'message' => $errorMessage]); 
            exit; 
        }

        try {
            $pdo->beginTransaction(); // Start transaction

            // --- Fetch Service Details ---
            $stmtService = $pdo->prepare("SELECT name, full_price, deposit_price FROM services WHERE id = ?");
            $stmtService->execute([$data['service_id']]);
            $service = $stmtService->fetch();
            if (!$service) { throw new Exception('Service not found.', 404); }
            
            $total_service_price_paise = $service['full_price'] * 100; 
            $amount_paid_paise = intval($data['amount']); // Ensure it's an integer
            $payment_type = $data['plan']; 
            
            // Re-calculate expected amount server-side for validation
            $coupon_code = !empty($data['coupon_code']) ? strtoupper($data['coupon_code']) : null;
            $discount_percentage = 0;
            if ($coupon_code) {
                 $stmtCouponCheck = $pdo->prepare("SELECT discount_percentage, use_limit, times_used FROM coupons WHERE coupon_code = ?");
                 $stmtCouponCheck->execute([$coupon_code]);
                 $couponData = $stmtCouponCheck->fetch();
                 if ($couponData && $couponData['times_used'] < $couponData['use_limit']) {
                     $discount_percentage = $couponData['discount_percentage'];
                 } else {
                     // Coupon is invalid or expired, ignore it but proceed with order at full price maybe? Or fail? Let's fail for now.
                     throw new Exception('Applied coupon is invalid or expired.', 400); 
                 }
            }
            
            $calculated_expected_amount_paise = ($payment_type === 'full') 
                ? round($total_service_price_paise * (1 - ($discount_percentage / 100))) 
                : ($service['deposit_price'] * 100); // Deposit isn't usually discounted, adjust if needed
            
            // Stricter amount validation - Use calculated value
            if (abs($amount_paid_paise - $calculated_expected_amount_paise) > 10 && $data['razorpay_payment_id'] !== 'FREE_ORDER') { // Allow tolerance, ignore for FREE orders
                 error_log("create_order Amount Mismatch. Order Data: " . print_r($data, true) . " | Expected Server Calc: " . $calculated_expected_amount_paise);
                 throw new Exception('Payment amount mismatch detected. Please refresh and try again.', 400);
            }

            // Calculate remaining balance correctly
            $remaining_balance_paise = 0;
             if ($payment_type === 'deposit') {
                 $remaining_balance_paise = $total_service_price_paise - ($service['deposit_price'] * 100); 
             } else { // Full payment
                 $remaining_balance_paise = $total_service_price_paise - $amount_paid_paise; // Amount paid includes discount
             }
             // Ensure remaining balance isn't negative
             $remaining_balance_paise = max(0, $remaining_balance_paise); 


            $remaining_balance_rupees = $remaining_balance_paise / 100;
            $amount_paid_rupees = $amount_paid_paise / 100;
            
            // --- Find or Create User ---
            $phone = trim($data['phone']);
            $email = strtolower(trim($data['email']));
            $name = trim($data['name']);
            
            $user_id = null;
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = ?");
                $stmt->execute([$phone]);
                $user = $stmt->fetch();
            }

            if ($user) { 
                $user_id = $user['id'];
                $stmtUpdate = $pdo->prepare("UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?"); // Always update to latest info
                $stmtUpdate->execute([$name, $phone, $email, $user_id]);
                error_log("Found existing user ID: " . $user_id . " for email/phone: " . $email . "/" . $phone);
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, phone, email) VALUES (?, ?, ?)");
                $stmt->execute([$name, $phone, $email]);
                $user_id = $pdo->lastInsertId();
                 error_log("Created new user ID: " . $user_id . " for email/phone: " . $email . "/" . $phone);
            }
            if (!$user_id) { throw new Exception('Failed to create or find user.', 500); }

            // --- Generate Unique Order ID ---
            $order_uid = generateUniqueID(8, 'CMO-'); 
            $stmtCheck = $pdo->prepare("SELECT id FROM orders WHERE order_uid = ?");
            $stmtCheck->execute([$order_uid]);
            while($stmtCheck->fetch()) {
                 $order_uid = generateUniqueID(8, 'CMO-');
                 $stmtCheck->execute([$order_uid]);
            }
            
            // --- Determine Initial Status ---
             $initial_status = ($remaining_balance_paise <= 0) ? 'Paid' : 'Awaiting Final Payment';

            // --- Create Order ---
            $stmt = $pdo->prepare("INSERT INTO orders (order_uid, user_id, service_id, payment_type, total_service_price, amount_paid, razorpay_payment_id, coupon_code, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $order_uid, $user_id, $data['service_id'], $payment_type, 
                $total_service_price_paise, $amount_paid_paise, $data['razorpay_payment_id'],
                $coupon_code, $initial_status 
            ]);
            
            // Increment coupon usage only if it was actually used and order succeeded
            if ($coupon_code && $discount_percentage > 0) {
                $stmtCoupon = $pdo->prepare("UPDATE coupons SET times_used = times_used + 1 WHERE coupon_code = ?");
                $stmtCoupon->execute([$coupon_code]);
            }
            
            // --- Send Confirmation Email ---
            $emailBody = "<h2>Thank you for your order, $name! (Order #$order_uid)</h2>
                          <p>We have successfully received your payment and your project is now underway.</p>
                          <p><strong>Service:</strong> {$service['name']}</p>
                          <p><strong>Amount Paid:</strong> ₹" . number_format($amount_paid_rupees) . "</p>";
            if ($remaining_balance_rupees > 0) {
                $emailBody .= "<p><strong>Remaining Balance:</strong> ₹" . number_format($remaining_balance_rupees) . "</p>
                               <p>This balance will be due upon project completion, before final launch.</p>";
            } else {
                $emailBody .= "<p><strong>Remaining Balance:</strong> ₹0</p>";
            }
            $emailBody .= "<p>You can view your project status and chat with us by logging into your dashboard: <a href='" . BASE_URL . "login.php'>Login Here</a></p>";
            sendUserEmail($email, $name, "Your Order Confirmation (#$order_uid) from CodeMistry", $emailBody);
            
            // --- Log User In ---
             // Regenerate session ID for security after potential user creation/login action
            if (session_status() === PHP_SESSION_ACTIVE) {
                 session_regenerate_id(true); 
            }
            $_SESSION['user_id'] = $user_id; 
            $_SESSION['user_logged_in'] = true;
            error_log("User logged in. Session user_id set to: " . $_SESSION['user_id']); // Log session set
            
            $pdo->commit(); // Commit transaction
            
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => true, 'message' => 'Order placed successfully!', 'redirect' => 'dashboard.php?new=true']); // Add redirect hint
            exit; // Added exit

        } catch (Exception $e) {
            if ($pdo->inTransaction()) { // Check if transaction was started before rolling back
                $pdo->rollBack(); 
            }
            // Log detailed error
            error_log("API Error (create_order): Code " . $e->getCode() . " - " . $e->getMessage() . " | Data: " . print_r($data, true));
            $errorCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500; // Use specific error codes if available
            http_response_code($errorCode);
            // Provide a user-friendly message, don't expose raw DB errors
            $userMessage = ($errorCode == 400) ? $e->getMessage() : 'Order creation failed due to an internal error. Please contact support.';
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => false, 'message' => $userMessage]);
            exit; // Added exit
        }
        break;

    case 'create_ticket':
        // Reset output buffering
        while (ob_get_level()) ob_end_clean();
        ob_start();
        
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        // Initialize response array
        $response = ['success' => false, 'message' => '', 'ticket_id' => null];

        try {
            // Get raw POST data
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $email = isset($_POST['email']) ? trim($_POST['email']) : '';
            $message = isset($_POST['message']) ? trim($_POST['message']) : '';

            // Validate and sanitize inputs
            if (empty($name)) {
                $response['message'] = 'Name is required';
                echo json_encode($response);
                exit;
            }

            if (empty($phone)) {
                $response['message'] = 'Phone number is required';
                echo json_encode($response);
                exit;
            }

            if (empty($message)) {
                $response['message'] = 'Message is required';
                echo json_encode($response);
                exit;
            }

            // Clean and standardize inputs
            $name = htmlspecialchars(strip_tags($name));
            $phone = preg_replace('/[^0-9+]/', '', $phone);
            $message = htmlspecialchars(strip_tags($message));

            // Handle email (optional)
            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $response['message'] = 'Please provide a valid email address';
                    echo json_encode($response);
                    exit;
                }
                $email = strtolower($email);
            } else {
                $email = null;
            }

            try {
                // Start transaction
                $pdo->beginTransaction();

                // Generate ticket ID
                $ticket_uid = generateUniqueID(6, 'TKT-');
                $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE ticket_uid = ?");
                $check_stmt->execute([$ticket_uid]);
                
                while ($check_stmt->fetchColumn() > 0) {
                    $ticket_uid = generateUniqueID(6, 'TKT-');
                    $check_stmt->execute([$ticket_uid]);
                }

                // Insert ticket
                $insert_stmt = $pdo->prepare("
                    INSERT INTO tickets 
                    (ticket_uid, name, phone, email, message, status) 
                    VALUES 
                    (?, ?, ?, ?, ?, 'open')
                ");

                $insert_result = $insert_stmt->execute([
                    $ticket_uid,
                    $name,
                    $phone,
                    $email,
                    $message
                ]);

                if (!$insert_result) {
                    throw new PDOException("Failed to create ticket");
                }

                // Send confirmation email if address provided
                if ($email) {
                    try {
                        $subject = "Your Support Ticket #{$ticket_uid} - CodeMistry";
                        $body = "<h2>Hi {$name},</h2>
                                <p>Thank you for contacting CodeMistry Support.</p>
                                <p>We have received your query and assigned it ticket number: <strong>#{$ticket_uid}</strong></p>
                                <p>Our team will review your message and respond within 24 hours.</p>
                                <br>
                                <p><strong>Your Message:</strong></p>
                                <blockquote style=\"border-left: 4px solid #ccc; padding-left: 1em; margin-left: 1em; font-style: italic;\">"
                                . nl2br(htmlspecialchars($message)) .
                                "</blockquote>
                                <br>
                                <p>Best regards,<br>CodeMistry Support Team</p>";

                        sendUserEmail($email, $name, $subject, $body);
                    } catch (Exception $emailError) {
                        // Log but don't fail if email fails
                        error_log("Warning: Ticket {$ticket_uid} created but confirmation email failed: " . $emailError->getMessage());
                    }
                }

                // Commit the transaction
                $pdo->commit();

                // Send success response
                http_response_code(200);
                if (ob_get_length()) ob_end_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Your ticket has been created successfully' . 
                                ($email ? '. A confirmation email has been sent to your email address.' : '.'),
                    'ticket_id' => $ticket_uid
                ]);
                exit; // Added exit

            } catch (Exception $e) {
                // Roll back on any error during the transaction
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                throw $e; // Re-throw to be caught by outer try-catch
            }

        } catch (Exception $e) {
            error_log("API Error (create_ticket): " . $e->getMessage());
            
            $statusCode = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;
            http_response_code($statusCode);
            
            // Ensure clean output buffer
            while (ob_get_level()) ob_end_clean();
            
            // Ensure proper JSON response
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => false,
                'message' => $statusCode === 400 ? $e->getMessage() : 'An error occurred while creating your ticket. Please try again.',
                'error_code' => $statusCode
            ], JSON_THROW_ON_ERROR);
            exit; // Added exit
        }
        break;


    case 'get_dashboard_data':
        // **CRITICAL FIX: Ensure user_id exists in session**
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || empty($_SESSION['user_id'])) { 
            http_response_code(403); 
            error_log("Dashboard access denied: Session invalid or user_id missing. Session data: " . print_r($_SESSION, true)); 
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => false, 'message' => 'Authentication required. Please log in again.']); 
            exit; 
        }
        
        $user_id = $_SESSION['user_id'];
        error_log("Fetching dashboard data for user_id: " . $user_id); 
        
        try {
            // Fetch User Details - Ensure user actually exists
            $stmtUser = $pdo->prepare("SELECT id, name, phone, email FROM users WHERE id = ?");
            $stmtUser->execute([$user_id]);
            $user = $stmtUser->fetch();
            
            if(!$user){ 
                 http_response_code(404); 
                 error_log("Dashboard data error: User ID {$user_id} not found in database."); 
                 session_unset(); session_destroy(); // Log user out
                 ob_end_clean(); // Clean buffer
                 echo json_encode(['success' => false, 'message' => 'User account not found. Please log in again.']); 
                 exit;
            }
            
            // Fetch Orders *specifically for this user*
            $stmtOrders = $pdo->prepare("
                SELECT 
                    o.id, o.order_uid, o.order_date, o.payment_type,
                    o.total_service_price, o.amount_paid, o.status,
                    s.name as service_name 
                FROM orders o 
                JOIN services s ON o.service_id = s.id 
                WHERE o.user_id = ?  -- This WHERE clause is essential!
                ORDER BY o.order_date DESC
            ");
            $stmtOrders->execute([$user_id]);
            $orders = $stmtOrders->fetchAll();
            
            error_log("Dashboard data for user {$user_id}: Found " . count($orders) . " orders.");
            
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => true, 'user' => $user, 'orders' => $orders]);
            exit; // Added exit

        } catch (PDOException $e) {
             error_log("API Error (get_dashboard_data for user {$user_id}): " . $e->getMessage());
             http_response_code(500);
             ob_end_clean(); // Clean buffer
             echo json_encode(['success' => false, 'message' => 'Error fetching dashboard data.']);
             exit; // Added exit
        }
        break;

    case 'verify_remaining_payment':
         if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || empty($_SESSION['user_id'])) { 
             http_response_code(403); 
             ob_end_clean(); // Clean buffer
             echo json_encode(['success' => false, 'message' => 'Authentication required. Please log in again.']); 
             exit; 
         }

        $razorpayPaymentId = $_POST['razorpay_payment_id'] ?? null;
        $orderUid = $_POST['order_uid'] ?? null; 
        $user_id = $_SESSION['user_id']; 

        error_log("Attempting verify_remaining_payment for user {$user_id}, order_uid {$orderUid}, payment_id {$razorpayPaymentId}"); 

        if (!$razorpayPaymentId || !$orderUid) {
            http_response_code(400);
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => false, 'message' => 'Missing payment details (payment ID or Order UID).']);
            exit;
        }
        
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT o.id, o.user_id, o.total_service_price, o.amount_paid, o.status, o.razorpay_payment_id as existing_payment_ids, u.name, u.email
                                  FROM orders o JOIN users u ON o.user_id = u.id 
                                  WHERE o.order_uid = ? AND o.user_id = ?");
            $stmt->execute([$orderUid, $user_id]);
            $order = $stmt->fetch();

            if (!$order) {
                error_log("verify_remaining_payment failed: Order UID {$orderUid} not found for user {$user_id}.");
                throw new Exception('Order not found or access denied.', 404);
            }
            
            if ($order['status'] !== 'Awaiting Final Payment') {
                error_log("verify_remaining_payment failed: Order {$orderUid} status is '{$order['status']}', not 'Awaiting Final Payment'.");
                // Check if already paid
                if ($order['status'] === 'Paid' || $order['status'] === 'Completed') {
                     throw new Exception('This order has already been fully paid or completed.', 400);
                } else {
                     throw new Exception('This order does not require final payment at this time.', 400);
                }
            }

            $remaining_amount_paise = $order['total_service_price'] - $order['amount_paid'];
            if ($remaining_amount_paise <= 0) {
                 error_log("verify_remaining_payment failed: Order {$orderUid} has no remaining balance according to DB.");
                 // Maybe update status anyway if it somehow got stuck? Or just error out.
                  $stmtUpdateStatus = $pdo->prepare("UPDATE orders SET status = 'Paid' WHERE id = ?");
                  $stmtUpdateStatus->execute([$order['id']]);
                  $pdo->commit();
                  ob_end_clean(); // Clean buffer
                  echo json_encode(['success' => true, 'message' => 'No remaining balance found. Order status corrected to Paid.']);
                  exit;
                 // throw new Exception('No remaining balance found for this order.', 400);
            }
            
            // --- IMPORTANT: Verify Razorpay Signature AND Amount ---
            $isSignatureValid = true; // Placeholder - REPLACE WITH ACTUAL VERIFICATION
            // You *must* implement signature verification here using RAZORPAY_KEY_SECRET
            // Also, fetch payment details from Razorpay API to confirm the amount paid matches $remaining_amount_paise
            /* Example structure (requires Razorpay PHP SDK or cURL):
                // use Razorpay\Api\Api;
                // use Razorpay\Api\Errors\SignatureVerificationError;
                $api = new \Razorpay\Api\Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
                try {
                    $attributes = array(
                        'razorpay_order_id' => $_POST['razorpay_order_id'], // Need order_id from client-side
                        'razorpay_payment_id' => $razorpayPaymentId,
                        'razorpay_signature' => $_POST['razorpay_signature'] // Need signature from client-side
                    );
                    $api->utility->verifyPaymentSignature($attributes);
                    $isSignatureValid = true;

                    // Additionally fetch payment details and check amount
                    $payment = $api->payment->fetch($razorpayPaymentId);
                    if ($payment->amount != $remaining_amount_paise) {
                        error_log("Amount mismatch: Expected {$remaining_amount_paise}, Got {$payment->amount}");
                        $isSignatureValid = false; 
                    }

                } catch(\Razorpay\Api\Errors\SignatureVerificationError $e) {
                    $isSignatureValid = false;
                    error_log("Razorpay Signature Verification Failed: " . $e->getMessage());
                } catch (\Exception $e) {
                    $isSignatureValid = false; // Treat API errors during verification as failure
                     error_log("Error fetching Razorpay payment details: " . $e->getMessage());
                }
            */

            if ($isSignatureValid) {
                // Update order
                $new_amount_paid = $order['amount_paid'] + $remaining_amount_paise; // Add the remaining amount
                $new_status = 'Paid'; // Change status to Paid
                $updated_payment_ids = $order['existing_payment_ids'] ? $order['existing_payment_ids'] . ',' . $razorpayPaymentId : $razorpayPaymentId;
                
                $stmtUpdate = $pdo->prepare("UPDATE orders SET amount_paid = ?, status = ?, razorpay_payment_id = ? WHERE id = ?");
                $stmtUpdate->execute([$new_amount_paid, $new_status, $updated_payment_ids, $order['id']]);

                // Send email confirmation
                if($order['email']) {
                    $subject = "Final Payment Received for Order #{$orderUid}";
                    $body = "<h2>Hi {$order['name']},</h2>
                             <p>We have successfully received your final payment of ₹" . number_format($remaining_amount_paise / 100) . " for Order #{$orderUid}.</p>
                             <p>Your order status is now updated to 'Paid'. We will notify you again once the project is marked as completed by our team.</p>
                             <p>You can view your updated order details in your dashboard: <a href='" . BASE_URL . "login.php'>Login Here</a></p>";
                    sendUserEmail($order['email'], $order['name'], $subject, $body);
                }

                $pdo->commit();
                error_log("verify_remaining_payment successful for order {$orderUid}, user {$user_id}.");
                ob_end_clean(); // Clean buffer
                echo json_encode(['success' => true, 'message' => 'Final payment successful!']);
                exit; // Added exit
            } else {
                error_log("verify_remaining_payment failed: Invalid signature or amount mismatch for order {$orderUid}, user {$user_id}.");
                throw new Exception('Invalid payment signature or amount mismatch.', 400);
            }
        } catch (Exception $e) {
             if ($pdo->inTransaction()) {
                $pdo->rollBack();
             }
             error_log("API Error (verify_remaining_payment for user {$user_id}, order {$orderUid}): " . $e->getMessage());
             $errorCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
             http_response_code($errorCode);
             ob_end_clean(); // Clean buffer
             echo json_encode(['success' => false, 'message' => 'Payment verification failed: ' . $e->getMessage()]);
             exit; // Added exit
        }
        break;

    // --- Authentication and Chat ---
    case 'signup':
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? strtolower(trim($_POST['email'])) : null;
        if (empty($name) || empty($phone) || empty($email)) { 
             http_response_code(400);
             ob_end_clean(); // Clean buffer
             echo json_encode(['success' => false, 'message' => 'All fields are required and email must be valid.']); 
             exit; // Use exit
        }
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR phone = ?");
            $stmt->execute([$email, $phone]);
            if ($stmt->fetch()) { 
                 http_response_code(409); // Conflict
                 ob_end_clean(); // Clean buffer
                 echo json_encode(['success' => false, 'message' => 'An account with this email or phone already exists.']); 
                 exit; // Use exit
            }
            $stmt = $pdo->prepare("INSERT INTO users (name, phone, email) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $phone, $email])) {
                $user_id = $pdo->lastInsertId(); 
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_email'] = $email; 
                $_SESSION['otp_time'] = time();
                $emailBody = "<h1>Welcome to CodeMistry!</h1><p>Your verification code is: <strong>$otp</strong></p><p>This code will expire in 10 minutes.</p>";
                if (sendUserEmail($email, $name, 'Verify Your CodeMistry Account', $emailBody)) {
                    // REMOVED ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Account created! An OTP has been sent to your email.']);
                } else {
                     // REMOVED http_response_code(500);
                     // REMOVED ob_end_clean();
                     echo json_encode(['success' => false, 'message' => 'Account created, but could not send OTP email.']);
                }
            } else { throw new PDOException("Failed to insert user."); }
        } catch (PDOException $e) { /* ... error handling ... */ 
            error_log("API Error (signup): " . $e->getMessage());
            http_response_code(500);
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => false, 'message' => 'Failed to create account due to a database error.']);
            exit; // Use exit
        } catch (Exception $e) { /* ... error handling ... */ 
            error_log("API Error (signup - OTP): " . $e->getMessage());
            http_response_code(500);
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => false, 'message' => 'Failed to create account due to an error.']);
            exit; // Use exit
        }
        exit; // Use exit

    case 'send_email_otp':
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? strtolower(trim($_POST['email'])) : null;
        if (!$email) { /* ... error handling ... */ 
             http_response_code(400);
             ob_end_clean(); // Clean buffer
             echo json_encode(['success' => false, 'message' => 'Invalid email address provided.']); 
             exit; // Use exit
        }
        try {
            $stmt = $pdo->prepare("SELECT name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if (!$user) { /* ... error handling ... */ 
                 http_response_code(404);
                 ob_end_clean(); // Clean buffer
                 echo json_encode(['success' => false, 'message' => 'No account found with this email address.']); 
                 exit; // Use exit
            }
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_email'] = $email;
            $_SESSION['otp_time'] = time();
            $emailBody = "<h1>CodeMistry Login</h1><p>Your One-Time Password (OTP) is: <strong>$otp</strong></p><p>This code will expire in 10 minutes.</p>";
            if (sendUserEmail($email, $user['name'], 'Your Login OTP for CodeMistry', $emailBody)) {
                // REMOVED ob_end_clean();
                echo json_encode(['success' => true, 'message' => 'An OTP has been sent to your email.']);
            } else { /* ... error handling ... */ 
                 // REMOVED http_response_code(500);
                 // REMOVED ob_end_clean();
                 echo json_encode(['success' => false, 'message' => 'Failed to send OTP email.']);
            }
        } catch (PDOException $e) { /* ... error handling ... */ 
            error_log("API Error (send_email_otp): " . $e->getMessage());
            http_response_code(500);
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => false, 'message' => 'Database error looking up user.']);
            exit; // Use exit
        } catch (Exception $e) { /* ... error handling ... */ 
            error_log("API Error (send_email_otp - Mail): " . $e->getMessage());
            http_response_code(500);
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => false, 'message' => 'Error sending OTP.']);
            exit; // Use exit
        }
        exit; // Use exit

    case 'verify_email_otp':
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? strtolower(trim($_POST['email'])) : null;
        $otp = trim($_POST['otp'] ?? '');
        if (empty($otp) || empty($email)) { /* ... error ... */ http_response_code(400); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Email and OTP are required.']); exit; }
        if (!isset($_SESSION['otp']) || !isset($_SESSION['otp_time']) || !isset($_SESSION['otp_email'])) { /* ... error ... */ http_response_code(400); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'OTP session invalid or expired.']); exit; }
        if ($_SESSION['otp_email'] !== $email) { /* ... error ... */ http_response_code(400); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'OTP was sent to a different email.']); exit; }
        if (time() - $_SESSION['otp_time'] > 600) { unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['otp_email']); /* ... error ... */ http_response_code(400); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'OTP has expired.']); exit; }
        if ($_SESSION['otp'] == $otp) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                if ($user) {
                    session_regenerate_id(true); 
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_logged_in'] = true;
                    error_log("User logged in via OTP. Session user_id set to: " . $_SESSION['user_id']); // Log session set
                    unset($_SESSION['otp'], $_SESSION['otp_time'], $_SESSION['otp_email']);
                    // REMOVED ob_end_clean();
                    echo json_encode(['success' => true, 'message' => 'Login successful!', 'redirect' => 'dashboard.php']); // Add redirect hint
                } else { /* ... error ... */ http_response_code(404); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'User account not found.']); }
            } catch (PDOException $e) { /* ... error ... */ 
                 error_log("API Error (verify_email_otp): " . $e->getMessage());
                 http_response_code(500);
                 ob_end_clean(); // Clean buffer
                 echo json_encode(['success' => false, 'message' => 'Database error during login.']);
                 exit; // Use exit
            }
        } else { /* ... error ... */ http_response_code(400); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Invalid OTP entered.']); }
        exit; // Use exit

    // In api.php

    case 'send_chat_message':
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || empty($_SESSION['user_id'])) { 
            http_response_code(403); 
            ob_end_clean(); 
            echo json_encode(['success' => false, 'message' => 'Not logged in.']); 
            exit; 
        }
        
        $user_id = $_SESSION['user_id'];
        $order_id_raw = $_POST['order_id'] ?? null;
        $message = trim($_POST['message'] ?? '');

        // More robust validation
        $order_id = filter_var($order_id_raw, FILTER_VALIDATE_INT);
        
        if (empty($message) || $order_id === false || $order_id <= 0) {
            http_response_code(400); 
            ob_end_clean(); 
            
            // Give a more specific error
            $error_msg = empty($message) ? 'Message cannot be empty.' : 'Invalid Order ID provided.';
            error_log("send_chat_message failed: " . $error_msg . " (Raw Order ID: " . htmlspecialchars($order_id_raw) . ")");
            
            echo json_encode(['success' => false, 'message' => $error_msg]); 
            exit;
        }

        try {
            // Check if user owns this order
            $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
            $stmt->execute([$order_id, $user_id]);
            if (!$stmt->fetch()) { 
                http_response_code(403); 
                ob_end_clean(); 
                echo json_encode(['success' => false, 'message' => 'Unauthorized chat access.']); 
                exit; 
            }
            
            // Insert the message
            $stmt = $pdo->prepare("INSERT INTO messages (order_id, sender, message) VALUES (?, 'user', ?)");
            $stmt->execute([$order_id, $message]);
            
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => true]);
            exit; 
            
        } catch (PDOException $e) { 
            error_log("API Error (send_chat_message): " . $e->getMessage());
            http_response_code(500);
            ob_end_clean(); 
            echo json_encode(['success' => false, 'message' => 'Database error sending message.']);
            exit; 
        }
        break;

    case 'get_chat_messages':
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true || empty($_SESSION['user_id'])) { http_response_code(403); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Not logged in.']); exit; }
        $user_id = $_SESSION['user_id'];
        $order_id = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT); 
        if (!$order_id) { http_response_code(400); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Invalid Order ID.']); exit; }
         try {
            $stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ?");
            $stmt->execute([$order_id, $user_id]);
            if (!$stmt->fetch()) { http_response_code(403); ob_end_clean(); echo json_encode(['success' => false, 'message' => 'Unauthorized chat access.']); exit; }
            $stmt = $pdo->prepare("SELECT sender, message, created_at FROM messages WHERE order_id = ? ORDER BY created_at ASC");
            $stmt->execute([$order_id]);
            $messages = $stmt->fetchAll();
            ob_end_clean(); // Clean buffer
            echo json_encode(['success' => true, 'messages' => $messages]);
            exit; // Use exit
         } catch (PDOException $e) { /* ... error handling ... */ 
             error_log("API Error (get_chat_messages): " . $e->getMessage());
             http_response_code(500);
             ob_end_clean(); // Clean buffer
             echo json_encode(['success' => false, 'message' => 'Database error fetching messages.']);
             exit; // Use exit
         }
        break;
        
    default:
        http_response_code(400);
        ob_end_clean(); // Clean buffer
        echo json_encode(['success' => false, 'message' => 'Invalid API action specified.']);
        break;
}

// Final cleanup of buffer in case no output was sent
if (ob_get_level() > 0) {
    ob_end_clean();
}
?>