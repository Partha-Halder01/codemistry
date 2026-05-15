<?php
// Temporary API endpoint just for ticket creation
require 'config.php';

// Ensure clean output
while (ob_get_level()) ob_end_clean();
ob_start();

header('Content-Type: application/json; charset=utf-8');

// --- Small helper implementations (local to this temp endpoint) ---
if (!function_exists('generateUniqueID')) {
    function generateUniqueID($length = 8, $prefix = '') {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        try {
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } catch (Exception $e) {
            // Fallback to mt_rand
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[mt_rand(0, strlen($characters) - 1)];
            }
        }
        return $prefix . $randomString;
    }
}

if (!function_exists('sendUserEmail')) {
    function sendUserEmail($toEmail, $toName, $subject, $body) {
        // Try to use PHPMailer if available, otherwise log and return false
        try {
            if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = defined('GMAIL_SMTP_HOST') ? GMAIL_SMTP_HOST : 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = defined('GMAIL_USER') ? GMAIL_USER : '';
                $mail->Password   = defined('GMAIL_APP_PASS') ? GMAIL_APP_PASS : '';
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port       = 465;
                $mail->setFrom(defined('GMAIL_USER') ? GMAIL_USER : 'no-reply@example.com', 'CodeMistry');
                $mail->addAddress($toEmail, $toName);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = "<!doctype html><html><body>" . $body . "</body></html>";
                $mail->send();
                return true;
            } else {
                // PHPMailer not installed; log the email content for debugging
                error_log("sendUserEmail: PHPMailer not available. To: $toEmail Subject: $subject");
                return false;
            }
        } catch (Exception $e) {
            error_log("sendUserEmail error: " . $e->getMessage());
            return false;
        }
    }
}

// Initialize response
$response = [
    'success' => false,
    'message' => '',
    'ticket_id' => null
];

// Verify request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Method not allowed';
    echo json_encode($response);
    exit;
}

try {
    // Get and validate inputs
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validate required fields
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

    // Clean inputs
    $name = htmlspecialchars(strip_tags($name));
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    $message = htmlspecialchars(strip_tags($message));

    // Process email if provided
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

    // Database operation
    try {
        $pdo->beginTransaction();

        // Generate ticket ID
        $ticket_uid = generateUniqueID(6, 'TKT-');
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE ticket_uid = ?");
        $check_stmt->execute([$ticket_uid]);
        
        // Ensure unique ticket ID
        while ($check_stmt->fetchColumn() > 0) {
            $ticket_uid = generateUniqueID(6, 'TKT-');
            $check_stmt->execute([$ticket_uid]);
        }

        // Create ticket
        $insert_stmt = $pdo->prepare("
            INSERT INTO tickets (ticket_uid, name, phone, email, message, status)
            VALUES (?, ?, ?, ?, ?, 'open')
        ");

        if (!$insert_stmt->execute([$ticket_uid, $name, $phone, $email, $message])) {
            throw new PDOException("Failed to create ticket");
        }

        // Send email if address provided
        if ($email) {
            // Only attempt to load PHPMailer if composer autoload exists
            if (file_exists(__DIR__ . '/vendor/autoload.php')) {
                require_once __DIR__ . '/vendor/autoload.php';
            } else {
                // Composer not installed; sendUserEmail will detect absence and log
                error_log("Composer autoload not found; skipping PHPMailer load for ticket emails.");
            }

            $subject = "Support Ticket #{$ticket_uid} Received";
            $body = "<h2>Hello {$name},</h2>
                    <p>Thank you for contacting CodeMistry Support.</p>
                    <p>Your ticket (#{$ticket_uid}) has been received.</p>
                    <p>We will respond within 24 hours.</p>
                    <br>
                    <p><strong>Your Message:</strong></p>
                    <blockquote style='background: #f5f5f5; padding: 15px; border-left: 4px solid #333;'>"
                    . nl2br(htmlspecialchars($message)) .
                    "</blockquote>";

            sendUserEmail($email, $name, $subject, $body);
        }

        // Commit and send success response
        $pdo->commit();
        $response['success'] = true;
        $response['ticket_id'] = $ticket_uid;
        $response['message'] = 'Ticket created successfully' . 
            ($email ? '. A confirmation email has been sent.' : '.');
        
        echo json_encode($response);
        exit;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Database error in create_ticket: " . $e->getMessage());
        $response['message'] = 'Unable to create ticket. Please try again.';
        echo json_encode($response);
        exit;
    }

} catch (Exception $e) {
    error_log("General error in create_ticket: " . $e->getMessage());
    $response['message'] = 'An unexpected error occurred. Please try again.';
    echo json_encode($response);
    exit;
}
?>