<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Go one directory up to find the main config.php
require_once __DIR__ . '/../config.php'; 

/**
 * Checks if an admin is logged in. If not, redirects to the login page.
 */
function require_admin_login() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

/**
 * A helper function to easily output JSON and exit.
 */
function json_response($success, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(['success' => $success] + $data);
    exit;
}
?>