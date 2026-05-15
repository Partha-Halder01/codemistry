<?php

/**
 * Laravel Backend Entry Point for Hostinger Shared Hosting
 *
 * This file sits at /public_html/backend.php
 * The .htaccess rewrites ALL /api/* requests to this file
 * while preserving the full REQUEST_URI (e.g., /api/services).
 * Laravel's routes/api.php are auto-prefixed with /api so this works perfectly.
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/../codemistry_backend/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../codemistry_backend/bootstrap/app.php';

if (file_exists($maintenance = __DIR__.'/../codemistry_backend/storage/framework/maintenance.php')) {
    require $maintenance;
}

$app->handleRequest(Request::capture());
