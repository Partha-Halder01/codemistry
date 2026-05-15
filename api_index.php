<?php

/**
 * Laravel API Entry Point for Hostinger Shared Hosting
 *
 * This file is at /public_html/api/index.php.
 * All requests routed here already have /api/... as REQUEST_URI.
 * Laravel's routes/api.php auto-prefixes all routes with /api,
 * so we must NOT strip it — just boot Laravel with the full path.
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/../../codemistry_backend/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var \Illuminate\Foundation\Application $app */
$app = require_once __DIR__.'/../../codemistry_backend/bootstrap/app.php';

if (file_exists($maintenance = __DIR__.'/../../codemistry_backend/storage/framework/maintenance.php')) {
    require $maintenance;
}

$app->handleRequest(Request::capture());
