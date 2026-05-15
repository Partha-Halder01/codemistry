<?php
// This is the central API file for all admin actions.
require_once 'admin-auth.php';
require_admin_login(); // Secure all endpoints

/**
 * Processes an uploaded image file for a service.
 * Handles validation, optimization (resize/compression), saving,
 * and optionally deletes an old image.
 * Requires GD and Fileinfo extensions.
 * @param array $fileInfo The $_FILES['input_name'] array for the uploaded image.
 * @param string|null $existingPath The path of the image being replaced (if any), relative to project root.
 * @return string|false The relative path to the saved, optimized image on success, or false on failure/no new upload.
 * @throws RuntimeException On validation or processing errors.
 */
function processServiceImageUpload(array $fileInfo, ?string $existingPath = null): string|false
{
    // Define the target directory relative to the project root
    $uploadDir = 'uploads/services/';
    $projectRoot = dirname(__DIR__) . '/'; // Goes up from 'admin' to 'zenno-main'
    $targetDir = $projectRoot . $uploadDir;

    // --- 0. Ensure target directory exists and is writable ---
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true)) { // Creates parent dirs if needed
         error_log("Failed to create upload directory: " . $targetDir);
         throw new RuntimeException('Server configuration error: Cannot create upload directory.');
    }
     if (!is_writable($targetDir)) {
         error_log("Upload directory not writable: " . $targetDir);
         throw new RuntimeException('Server configuration error: Upload directory not writable.');
     }

    // --- 1. Check for Upload Errors ---
    if (!isset($fileInfo['error']) || is_array($fileInfo['error'])) {
        throw new RuntimeException('Invalid parameters received for file upload.');
    }
    if ($fileInfo['error'] !== UPLOAD_ERR_OK) {
        if ($fileInfo['error'] === UPLOAD_ERR_NO_FILE) {
            return false; // Indicate no new file was uploaded, not an error
        }
        // Handle other upload errors
        $phpFileUploadErrors = [
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the MAX_FILE_SIZE directive specified in the HTML form.',
            UPLOAD_ERR_PARTIAL    => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
        ];
        $message = $phpFileUploadErrors[$fileInfo['error']] ?? 'Unknown upload error';
        throw new RuntimeException('Error during file upload: ' . $message . ' (Code ' . $fileInfo['error'] . ')');
    }

    // --- 2. Validate File Size and Type ---
     if ($fileInfo['size'] <= 0) { // Check for empty file
         throw new RuntimeException('Uploaded file is empty.');
     }
    if ($fileInfo['size'] > 5 * 1024 * 1024) { // Max 5MB
        throw new RuntimeException('Exceeded file size limit (5MB).');
    }
    if (!function_exists('finfo_open')) {
        throw new RuntimeException('Server configuration error: Fileinfo extension is required for MIME type checking.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($fileInfo['tmp_name']);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mime, $allowedTypes)) {
        throw new RuntimeException('Invalid file format. Only JPG, PNG, WEBP allowed (detected: '.$mime.').');
    }

    // --- 3. Generate Unique Filename & Path ---
    $extension = match ($mime) {
        'image/jpeg' => '.jpg',
        'image/png' => '.png',
        'image/webp' => '.webp',
        default => '' // Should not happen
    };
    // Create a more robust unique name
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($fileInfo['name'], PATHINFO_FILENAME));
    $uniqueName = $safeName . '_' . bin2hex(random_bytes(4)) . $extension; // Add random bytes

    $targetFilePath = $targetDir . $uniqueName;
    $relativePath = $uploadDir . $uniqueName; // Path to save in DB

    // --- 4. Load Image using GD ---
    if (!extension_loaded('gd')) {
        throw new RuntimeException('Server configuration error: GD extension is required for image processing.');
    }
    $image = match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($fileInfo['tmp_name']), // Use @ to suppress warnings, check result
        'image/png' => @imagecreatefrompng($fileInfo['tmp_name']),
        'image/webp' => @imagecreatefromwebp($fileInfo['tmp_name']),
        default => false
    };
    if ($image === false) {
        throw new RuntimeException('Failed to load image resource. The file might be corrupted or invalid.');
    }

    // Preserve transparency for PNG/WEBP
    if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($image, false); // Required for true color images
        imagesavealpha($image, true);
    }

    // --- 5. Optimize: Resize (Optional - e.g., max width) and Compress ---
    $originalWidth = imagesx($image);
    $originalHeight = imagesy($image);
    $maxWidth = 1280; // Max width 1280px
    $quality = 75;    // JPEG/WEBP quality (0-100)
    $pngCompression = 6; // PNG compression (0-9)

    $newWidth = $originalWidth;
    $newHeight = $originalHeight;

    if ($originalWidth > $maxWidth) {
        $ratio = $maxWidth / $originalWidth;
        $newWidth = $maxWidth;
        $newHeight = (int) round($originalHeight * $ratio); // Calculate and round height
    }

    // Create new image canvas
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    if($newImage === false) {
        imagedestroy($image);
        throw new RuntimeException('Failed to create new image canvas.');
    }

    // Handle transparency for new canvas if needed
     if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        if ($transparent !== false) { // Check allocation success
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
    } else {
        // Fill background with white for JPEGs if resizing to prevent black borders
         $white = imagecolorallocate($newImage, 255, 255, 255);
         if($white !== false) {
             imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $white);
         }
    }


    // Resize
    if (!imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight)) {
        imagedestroy($image);
        imagedestroy($newImage);
        throw new RuntimeException('Failed to resize image.');
    }


    // --- 6. Save Optimized Image ---
    $saveSuccess = false;
    switch($mime) {
        case 'image/jpeg':
            $saveSuccess = imagejpeg($newImage, $targetFilePath, $quality);
            break;
        case 'image/png':
            $saveSuccess = imagepng($newImage, $targetFilePath, $pngCompression);
            break;
        case 'image/webp':
             // Check if webp support exists before trying to save
             if (function_exists('imagewebp')) {
                  $saveSuccess = imagewebp($newImage, $targetFilePath, $quality);
             } else {
                  // Fallback: save as PNG or JPG if WEBP not supported? Or just fail?
                  error_log('WEBP save failed: imagewebp function does not exist.');
                  // Let's try saving as JPG as a fallback
                  $fallbackPath = str_replace('.webp', '.jpg', $targetFilePath);
                  $saveSuccess = imagejpeg($newImage, $fallbackPath, $quality);
                  if($saveSuccess) {
                      $relativePath = str_replace('.webp', '.jpg', $relativePath); // Update relative path
                      error_log('Saved image as JPG fallback instead of WEBP.');
                  }
             }
            break;
    }


    // --- 7. Clean Up ---
    imagedestroy($image);
    imagedestroy($newImage);

    if (!$saveSuccess) {
        // Attempt to remove partially created file if saving failed
        if(file_exists($targetFilePath)) @unlink($targetFilePath);
        throw new RuntimeException('Failed to save optimized image to disk.');
    }

    // --- 8. Delete Old Image (if applicable and different from new path) ---
    if ($existingPath && $existingPath !== $relativePath) {
        $oldFilePath = $projectRoot . $existingPath;
        if (file_exists($oldFilePath) && is_writable(dirname($oldFilePath))) { // Check dir writability too
           if(!@unlink($oldFilePath)) { // Use @ to suppress warning if file gone, log instead
               error_log("Could not delete old image: " . $oldFilePath);
           }
        }
    }

    return $relativePath; // Return the path saved in DB
}

// --- Action Routing ---
$action = $_POST['action'] ?? '';

try {
    switch ($action) {

        // --- Order Actions ---

        case 'get_order_details': // Fetches detailed info for one order
            $order_id = $_POST['order_id'] ?? 0;
            if (empty($order_id) || !filter_var($order_id, FILTER_VALIDATE_INT)) {
                json_response(false, ['message' => 'Valid Order ID is required.']);
            }

            $stmt = $pdo->prepare("
                SELECT
                    o.id, o.order_uid, o.status, o.order_date, o.payment_type,
                    o.total_service_price, o.amount_paid, o.razorpay_payment_id, o.coupon_code,
                    u.name as user_name, u.email as user_email, u.phone as user_phone,
                    s.name as service_name
                FROM orders o
                JOIN users u ON o.user_id = u.id
                JOIN services s ON o.service_id = s.id
                WHERE o.id = ?
            ");
            $stmt->execute([$order_id]);
            $order_details = $stmt->fetch();

            if (!$order_details) {
                 json_response(false, ['message' => 'Order not found.']);
            }

            // Calculate remaining amount (in paise)
            $order_details['remaining_amount'] = max(0, $order_details['total_service_price'] - $order_details['amount_paid']);

            json_response(true, ['order' => $order_details]);
            break; // Added break

        case 'send_admin_message':
            $order_id = $_POST['order_id'] ?? 0;
            $message = trim($_POST['message'] ?? '');
            if (empty($order_id) || empty($message)) {
                json_response(false, ['message' => 'Order ID and message are required.']);
            }
            $stmt = $pdo->prepare("INSERT INTO messages (order_id, sender, message) VALUES (?, 'admin', ?)");
            $stmt->execute([$order_id, $message]);
            json_response(true);
            break;

         case 'get_admin_chat_messages': // Fetches chat messages for an order
            $order_id = $_POST['order_id'] ?? 0;
            if (empty($order_id) || !filter_var($order_id, FILTER_VALIDATE_INT)) {
                json_response(false, ['message' => 'Valid Order ID is required.']);
            }
            $stmt = $pdo->prepare("SELECT sender, message, created_at FROM messages WHERE order_id = ? ORDER BY created_at ASC");
            $stmt->execute([$order_id]);
            $messages = $stmt->fetchAll();
            json_response(true, ['messages' => $messages]);
            break;

        case 'update_order_status':
            $order_id = $_POST['order_id'] ?? 0;
            $status = trim($_POST['status'] ?? '');
            $validStatuses = ['Paid', 'Awaiting Final Payment', 'Completed', 'Cancelled', 'Processing']; // Add more if needed
            if (empty($order_id) || empty($status) || !in_array($status, $validStatuses)) {
                json_response(false, ['message' => 'Valid Order ID and Status are required.']);
            }
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);
            json_response(true);
            break;

        // --- Service Actions ---
        case 'add_service':
        case 'edit_service':
            $service_id = $_POST['service_id'] ?? null;
            $name = trim($_POST['name'] ?? '');
            $desc = trim($_POST['description'] ?? '');
            $full_price = filter_var($_POST['full_price'], FILTER_VALIDATE_INT);
            $deposit_price = filter_var($_POST['deposit_price'], FILTER_VALIDATE_INT);
            $features = trim($_POST['features'] ?? '');
            $rating = filter_var($_POST['rating'] ?? '5.0', FILTER_VALIDATE_FLOAT);
            $existing_img_path = trim($_POST['existing_cover_image_path'] ?? '') ?: null;
            $final_img_path = $existing_img_path;

            if ($rating === false || $rating < 0.0 || $rating > 5.0) $rating = 5.0;
            $rating = number_format($rating, 1);

            if (empty($name) || $full_price === false || $deposit_price === false || empty($features) || $full_price <= 0 || $deposit_price < 0) {
                 json_response(false, ['message' => 'Missing required fields or invalid price (Name, Prices > 0, Features required).']);
            }
            if ($deposit_price > $full_price) {
                 json_response(false, ['message' => 'Deposit price cannot be greater than the full price.']);
            }

            // FAQ Builder Logic
            $faq_questions = $_POST['faq_q'] ?? [];
            $faq_answers = $_POST['faq_a'] ?? [];
            $faq_array = [];
            if (is_array($faq_questions) && is_array($faq_answers) && count($faq_questions) === count($faq_answers)) {
                foreach ($faq_questions as $index => $question) {
                    $question = trim($question);
                    $answer = trim($faq_answers[$index] ?? '');
                    if (!empty($question) && !empty($answer)) {
                        $faq_array[] = ['q' => $question, 'a' => $answer];
                    }
                }
            }
            $faq = json_encode($faq_array);
            if ($faq === false) { json_response(false, ['message' => 'Failed to encode FAQ data.']); }

            // Image Upload Handling
            try {
                if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadedImagePath = processServiceImageUpload($_FILES['cover_image'], $existing_img_path);
                    if ($uploadedImagePath !== false) {
                         $final_img_path = $uploadedImagePath;
                    }
                }
            } catch (RuntimeException $e) {
                 json_response(false, ['message' => 'Image processing error: ' . $e->getMessage()]);
            }

            // Database Operation
            if ($action === 'add_service') {
                $sql = "INSERT INTO services (name, description, full_price, deposit_price, features, cover_image_path, faq, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [$name, $desc, $full_price, $deposit_price, $features, $final_img_path, $faq, $rating];
                $message = "Service added successfully!";
            } else { // edit_service
                if (empty($service_id) || !filter_var($service_id, FILTER_VALIDATE_INT)) {
                    json_response(false, ['message' => 'Valid Service ID missing for edit.']);
                }
                $sql = "UPDATE services SET name=?, description=?, full_price=?, deposit_price=?, features=?, cover_image_path=?, faq=?, rating=? WHERE id = ?";
                $params = [$name, $desc, $full_price, $deposit_price, $features, $final_img_path, $faq, $rating, $service_id];
                $message = "Service updated successfully!";
            }
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute($params)) {
                 json_response(true, ['message' => $message]);
            } else {
                 json_response(false, ['message' => 'Database error saving service.']);
            }
            break;


        case 'delete_service':
            $service_id = $_POST['service_id'] ?? 0;
            if (empty($service_id) || !filter_var($service_id, FILTER_VALIDATE_INT)) {
                json_response(false, ['message' => 'Valid Service ID is required.']);
            }
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE service_id = ?");
            $stmtCheck->execute([$service_id]);
            if ($stmtCheck->fetchColumn() > 0) {
                 json_response(false, ['message' => 'Cannot delete service. It is linked to existing orders.']);
            }
             $stmtPath = $pdo->prepare("SELECT cover_image_path FROM services WHERE id = ?");
             $stmtPath->execute([$service_id]);
             $imagePath = $stmtPath->fetchColumn();
            $stmtDelete = $pdo->prepare("DELETE FROM services WHERE id = ?");
            if ($stmtDelete->execute([$service_id])) {
                 if ($imagePath) {
                     $projectRoot = dirname(__DIR__) . '/';
                     $fullImagePath = $projectRoot . $imagePath;
                     if (file_exists($fullImagePath) && is_writable(dirname($fullImagePath))) {
                         if (!@unlink($fullImagePath)) {
                              error_log("Failed to delete image file after deleting service record: " . $fullImagePath);
                         }
                     }
                 }
                 json_response(true, ['message' => 'Service deleted.']);
            } else {
                 json_response(false, ['message' => 'Failed to delete service from database.']);
            }
            break;


        // --- Coupon Actions ---
        case 'add_coupon':
            $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
            $discount = filter_var($_POST['discount_percentage'], FILTER_VALIDATE_INT);
            $limit = filter_var($_POST['use_limit'], FILTER_VALIDATE_INT);
            if (empty($code) || $discount === false || $limit === false || $discount < 1 || $discount > 100 || $limit < 1) {
                json_response(false, ['message' => 'All fields required. Discount (1-100), Limit (>0).']);
            }
            try {
                $stmt = $pdo->prepare("INSERT INTO coupons (coupon_code, discount_percentage, use_limit) VALUES (?, ?, ?)");
                $stmt->execute([$code, $discount, $limit]);
                json_response(true, ['message' => 'Coupon added.']);
            } catch (PDOException $e) {
                if ($e->errorInfo[1] == 1062) {
                    json_response(false, ['message' => 'A coupon with this code already exists.']);
                } else { throw $e; }
            }
            break;

        case 'delete_coupon':
            $coupon_id = $_POST['coupon_id'] ?? 0;
            if (empty($coupon_id) || !filter_var($coupon_id, FILTER_VALIDATE_INT)) {
                json_response(false, ['message' => 'Valid Coupon ID is required.']);
            }
            $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
            $stmt->execute([$coupon_id]);
            json_response(true, ['message' => 'Coupon deleted.']);
            break;

        // --- Ticket Actions ---
        case 'update_ticket_status':
            $ticket_id = $_POST['ticket_id'] ?? 0;
            $status = $_POST['status'] ?? '';
            if (empty($ticket_id) || !filter_var($ticket_id, FILTER_VALIDATE_INT) || !in_array($status, ['open', 'closed'])) {
                json_response(false, ['message' => 'Invalid ticket ID or status.']);
            }
            $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
            $stmt->execute([$status, $ticket_id]);
            json_response(true, ['message' => 'Ticket status updated.']);
            break;

        default:
            json_response(false, ['message' => 'Invalid admin API action specified.']);
    }

} catch (PDOException $e) {
    error_log("Admin API PDO Error (" . $action . "): " . $e->getMessage());
    json_response(false, ['message' => 'A database error occurred. Please check server logs.']);
} catch (Exception $e) { // Catch RuntimeException from image processing or other general exceptions
    error_log("Admin API General Error (" . $action . "): " . $e->getMessage());
    json_response(false, ['message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}

?>