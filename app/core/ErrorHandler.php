<?php

class ErrorHandler {
    private static $instance = null;
    private $logFile;

    private function __construct() {
        $this->logFile = dirname(__DIR__, 2) . '/logs/error.log';
        
        // Create logs directory if it doesn't exist
        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0777, true);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function handleError($errno, $errstr, $errfile, $errline) {
        $error = [
            'type' => $this->getErrorType($errno),
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'time' => date('Y-m-d H:i:s')
        ];

        $this->logError($error);
        $this->displayError($error);
        return true;
    }

    public function handleException($exception) {
        $error = [
            'type' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'time' => date('Y-m-d H:i:s')
        ];

        $this->logError($error);
        $this->displayError($error);
    }

    private function displayError($error) {
        // Check if it's an AJAX request
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        if ($isAjax) {
            // For AJAX requests, return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => $error['message']
            ]);
        } else {
            // For regular requests, show styled error message
            echo $this->getErrorTemplate($error);
        }
    }

    private function getErrorTemplate($error) {
        $isLoginError = strpos(strtolower($error['message']), 'password') !== false || 
                       strpos(strtolower($error['message']), 'login') !== false ||
                       strpos(strtolower($error['message']), 'akun') !== false ||
                       strpos(strtolower($error['message']), 'nim') !== false;

        $errorClass = $isLoginError ? 'login-error' : 'system-error';
        
        return "
        <style>
            .error-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                max-width: 400px;
                animation: slideIn 0.5s ease-out;
            }
            .error-message {
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 10px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                display: flex;
                align-items: center;
            }
            .login-error {
                background-color: #ffebee;
                border-left: 4px solid #f44336;
                color: #c62828;
            }
            .system-error {
                background-color: #fff3e0;
                border-left: 4px solid #ff9800;
                color: #e65100;
            }
            .error-icon {
                margin-right: 10px;
                font-size: 20px;
            }
            .error-content {
                flex-grow: 1;
            }
            .error-title {
                font-weight: bold;
                margin-bottom: 5px;
            }
            .error-details {
                font-size: 0.9em;
                opacity: 0.8;
            }
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        </style>
        <div class='error-container'>
            <div class='error-message {$errorClass}'>
                <div class='error-icon'>⚠️</div>
                <div class='error-content'>
                    <div class='error-title'>" . htmlspecialchars($error['message']) . "</div>
                    " . (!$isLoginError ? "<div class='error-details'>File: " . htmlspecialchars($error['file']) . " on line " . $error['line'] . "</div>" : "") . "
                </div>
            </div>
        </div>
        <script>
            // Auto-hide error message after 5 seconds
            setTimeout(function() {
                var errorContainer = document.querySelector('.error-container');
                if (errorContainer) {
                    errorContainer.style.animation = 'slideOut 0.5s ease-out';
                    setTimeout(function() {
                        errorContainer.remove();
                    }, 500);
                }
            }, 5000);
        </script>";
    }

    private function logError($error) {
        $logMessage = "[{$error['time']}] {$error['type']}: {$error['message']} in {$error['file']} on line {$error['line']}\n";
        if (isset($error['trace'])) {
            $logMessage .= "Stack trace:\n{$error['trace']}\n";
        }
        $logMessage .= "----------------------------------------\n";

        error_log($logMessage, 3, $this->logFile);
    }

    private function getErrorType($type) {
        switch($type) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
            default:
                return 'UNKNOWN';
        }
    }
} 