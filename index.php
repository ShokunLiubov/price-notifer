<?php

require_once('init.php');

use App\Core\Route\Route;

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

Route::dispatch($requestMethod, $requestPath);
