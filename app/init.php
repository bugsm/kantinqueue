<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error handling setup
require_once __DIR__ . '/core/ErrorHandler.php';
$errorHandler = ErrorHandler::getInstance();
set_error_handler([$errorHandler, 'handleError']);
set_exception_handler([$errorHandler, 'handleException']);

// Display errors in development environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/App.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Database.php';