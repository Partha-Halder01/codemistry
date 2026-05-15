<?php
/**
 * CodeMistry Configuration File
 */

// --- BASE URL ---
// IMPORTANT: Set this to the root URL of your website. 
// For XAMPP, it might be 'http://localhost/ewiuhgi/' (including the trailing slash)
// For a live domain, it would be 'https://yourdomain.com/'
define('BASE_URL', 'https://codemistry.in/'); // <-- ADJUST THIS

// --- DATABASE CONFIGURATION ---
define('DB_HOST', 'localhost');
define('DB_NAME', 'u327799122_codemistry_db');
define('DB_USER', 'u327799122_root');
define('DB_PASS', '5/Io~EkF8YnQ');

// --- RAZORPAY API KEYS ---
define('RAZORPAY_KEY_ID', 'rzp_live_RUTZbUme0C8tge');
define('RAZORPAY_KEY_SECRET', 'T1np2DCVhLYaT7GiUrjGW8GT');

// --- GMAIL SMTP CONFIGURATION ---
define('GMAIL_USER', 'codemistry359@gmail.com'); 
define('GMAIL_APP_PASS', 'ltiq qcdd kjgp thhr');


// --- DATABASE CONNECTION (PDO) ---
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Good practice
} catch (PDOException $e) {
    // In production, log error instead of die()
    error_log("DATABASE CONNECTION ERROR: " . $e->getMessage()); 
    die("A database error occurred. Please try again later."); // User-friendly message
}
?>