<?php

/**
 * Laravel Application Entry Point
 *
 * This file is the entry point for all HTTP requests to the application.
 * It bootstraps the Laravel framework and handles incoming requests.
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check For Maintenance Mode
|--------------------------------------------------------------------------
|
| If the application is in maintenance mode via the "down" command we will
| require this file so that any prerendered template can be shown instead
| of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Composer Autoloader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader
| for this application. We just need to utilize it! We'll simply
| require it into the script here so we don't need to manually
| load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
