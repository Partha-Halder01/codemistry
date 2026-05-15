<?php
// --- Database Connection Diagnostic Tool ---
// This file is for debugging purposes only.

// --- Step 1: Configuration ---
// These are the default credentials for a standard XAMPP installation.
$db_host = 'localhost';
$db_name = 'codemistry_db';
$db_user = 'root';
$db_pass = ''; // Default XAMPP password is an empty string.

// --- Step 2: Displaying the Settings Being Used ---
// This helps confirm that we are testing with the correct values.
echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>DB Check</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-gray-900 text-white p-8 font-sans'>";
echo "<h1 class='text-2xl font-bold mb-4'>Database Connection Test</h1>";
echo "<div class='bg-gray-800 p-4 rounded-lg'>";
echo "<p><strong>Host:</strong> " . $db_host . "</p>";
echo "<p><strong>Database Name:</strong> " . $db_name . "</p>";
echo "<p><strong>Username:</strong> " . $db_user . "</p>";
echo "<p><strong>Password:</strong> '<em>" . $db_pass . "</em>' (An empty string is correct for default XAMPP) </p>";
echo "</div>";
echo "<h2 class='text-xl font-bold mt-6 mb-2'>Result:</h2>";

// --- Step 3: Attempting the Connection ---
try {
    // We use the variables defined above to try and connect.
    $pdo = new PDO("mysql:host=" . $db_host . ";dbname=" . $db_name, $db_user, $db_pass);
    
    // If the line above doesn't throw an error, the connection was successful.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // --- Success Message ---
    echo "<div class='bg-green-800 border border-green-600 text-green-200 p-4 rounded-lg'>";
    echo "<strong class='font-bold'>SUCCESS!</strong><br>";
    echo "The connection to the database '<strong>" . $db_name . "</strong>' was established successfully.";
    echo "</div>";

} catch (PDOException $e) {
    // If an error (an "exception") occurs during the connection attempt, we catch it here.
    
    // --- Error Message ---
    echo "<div class='bg-red-800 border border-red-600 text-red-200 p-4 rounded-lg'>";
    echo "<strong class='font-bold'>CONNECTION FAILED!</strong><br>";
    echo "<p class='mt-2'>The script could not connect to the database. Here is the specific error message:</p>";
    echo "<pre class='bg-gray-900 p-2 rounded mt-2 text-white'>" . $e->getMessage() . "</pre>";
    echo "</div>";

    // --- Common Causes & Solutions ---
    echo "<h3 class='text-lg font-bold mt-6 mb-2'>Possible Causes & How to Fix:</h3>";
    echo "<ul class='list-disc list-inside bg-gray-800 p-4 rounded-lg space-y-2'>";
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<li><strong>Access Denied:</strong> This means the username or password is incorrect. Double-check the `DB_USER` and `DB_PASS` in your `config.php`. The default for XAMPP is 'root' with an empty password.</li>";
    }
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "<li><strong>Unknown Database:</strong> The database named '<strong>" . $db_name . "</strong>' does not exist. Make sure you have created it in phpMyAdmin and imported the `db_schema.sql` file.</li>";
    }
     if (strpos($e->getMessage(), 'No connection could be made') !== false) {
        echo "<li><strong>Connection Refused:</strong> MySQL is not running. Please open your XAMPP Control Panel and ensure that 'MySQL' has been started and has a green background.</li>";
    }
    echo "<li><strong>Check File Locations:</strong> Ensure all your files (including this one) are inside the `C:\\xampp\\htdocs\\codemistry\\` folder.</li>";
    echo "</ul>";
}

echo "</body></html>";
?>
