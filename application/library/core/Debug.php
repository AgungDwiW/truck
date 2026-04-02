<?php

class Debuger
{
    public static $verbose = 1;

    /**
     * Call this at the very top of your application.
     */
    public static function register()
    {
        if (self::$verbose) {
            error_reporting(E_ALL);
            // We set this to 1 so that when we return `false` for warnings, 
            // PHP natively displays them on the screen.
            ini_set('display_errors', '1'); 
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', '0'); // Hide everything in production
        }
        if (!defined("ENVIRONMENT"))
            define("ENVIRONMENT", "DEV");

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
        if (defined("ENVIRONMENT") && ENVIRONMENT == "DEV")
            self::$verbose =1;
        else 
            self::$verbose =0;
    }

    /**
     * Intercepts PHP errors.
     */
    public static function handleError($level, $message, $file = '', $line = 0)
    {
        // Respect the @ error suppression operator
        if (!(error_reporting() & $level)) {
            return false;
        }

        // Define which error levels should NOT kill the script
        $nonFatalLevels = [
            E_WARNING, E_NOTICE, E_CORE_WARNING, E_COMPILE_WARNING, 
            E_USER_WARNING, E_USER_NOTICE, E_STRICT, E_DEPRECATED, E_USER_DEPRECATED
        ];

        // If it's a warning or notice, don't handle it. 
        // Returning false hands control back to PHP's default handler.
        if (in_array($level, $nonFatalLevels)) {
            return false; 
        }

        // For actual errors (e.g., E_USER_ERROR, E_RECOVERABLE_ERROR), convert to an Exception
        throw new \ErrorException($message, 0, $level, $file, $line);
    }

    public static function handleException($exception)
    {
        self::renderErrorScreen($exception);
    }

    public static function handleShutdown()
    {
        $error = error_get_last();
        // Catch fatal errors right before the script stops
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $exception = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
            self::renderErrorScreen($exception);
        }
    }

    public static function dump($data, $forceShow = false, $stopExecution = false)
    {
        if (self::$verbose!=2 and !$forceShow) {
            return; // Do nothing when not verbose
        }

        // Use debug_backtrace to find out exactly where dump() was called from
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[0] ?? [];
        $file = $caller['file'] ?? 'Unknown file';
        $line = $caller['line'] ?? '?';

        echo '<div style="background: #282c34; color: #abb2bf; padding: 15px; margin: 10px; border-radius: 6px; font-family: monospace; font-size: 14px; text-align: left; overflow-x: auto; border-left: 5px solid #61afef; box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: relative;">';
        
        
        // Output the file and line number
        echo '<div style="color: #5c6370; border-bottom: 1px solid #3e4451; padding-bottom: 8px; margin-bottom: 10px; font-size: 12px;">';
        echo '<strong style="color: #61afef;">Dumped from:</strong> <span style="color: #98c379;">' . htmlspecialchars($file) . '</span> on line <strong style="color: #e5c07b;">' . $line . '</strong> <br>';
            
            $caller = $trace[1] ?? [];
            $file = $caller['file'] ?? 'Unknown file';
            $line = $caller['line'] ?? '?';
        echo '<strong style="color: #61afef;"> --- </strong> <span style="color: #98c379;">' . htmlspecialchars($file) . '</span> on line <strong style="color: #e5c07b;">' . $line . '</strong> <br>';
            $caller = $trace[2] ?? [];
            $file = $caller['file'] ?? 'Unknown file';
            $line = $caller['line'] ?? '?';
        echo '<strong style="color: #61afef;"> --- </strong> <span style="color: #98c379;">' . htmlspecialchars($file) . '</span> on line <strong style="color: #e5c07b;">' . $line . '</strong>';
        echo '</div>';
        
        echo '<pre style="margin: 0; white-space: pre-wrap; color: #fc9a61;background: #393d46; ">';
        print_r($data);
        echo '</pre>';
        echo '</div>';
        
        if ($stopExecution) {
            die();
        }
    }

    /**
     * Renders the custom error screen or handles production logging.
     */
    private static function renderErrorScreen($exception)
    {
        if (!headers_sent()) {
            http_response_code(500);
        }

        $type = get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();

        // --- NON-VERBOSE (PRODUCTION) BEHAVIOR ---
        if (!self::$verbose) {
            $logDir = __DIR__ . '/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            $logDir = $logDir . "/" . date('Y-m');
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . '/'.date('Y-m-d').'.log';
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] {$type}: {$message}" . PHP_EOL .
              "File: {$absoluteFilePath} Line: {$line}" . PHP_EOL .
              "Stack Trace:" . PHP_EOL . $exception->getTraceAsString() . PHP_EOL .
              str_repeat('-', 50) . PHP_EOL;
            error_log($logMessage, 3, $logFile);

            echo '<div style="font-family: sans-serif; text-align: center; margin-top: 50px; color: #333;">';
            echo '<h1>500 Internal Server Error</h1>';
            echo '<p>Something went wrong. The issue has been logged. Please try again later.</p>';
            echo '</div>';
            die();
        }

        // --- VERBOSE (DEBUG) BEHAVIOR ---
        $trace = $exception->getTraceAsString();
        $rawTrace = $exception->getTrace();

        $contextArgs = [];
        if (!empty($rawTrace[0]['args'])) {
            $contextArgs = $rawTrace[0]['args'];
        }

        $requestState = [
            '$_POST' => $_POST,
            '$_GET' => $_GET,
            '$_SESSION' => $_SESSION ?? null
        ];

        echo '<div style="background: #fdf2f2; color: #b91c1c; padding: 25px; margin: 20px; border-left: 8px solid #ef4444; border-radius: 6px; font-family: system-ui, sans-serif; box-shadow: 0 4px 6px rgba(0,0,0,0.1); line-height: 1.5; padding-bottom: 40px;">';
        echo "<h2 style='margin-top: 0; border-bottom: 2px solid #fca5a5; padding-bottom: 10px;'>🚨 Uncaught $type</h2>";
        echo "<p style='font-size: 18px;'><strong>Message:</strong> $message</p>";
        echo "<p style='background: #fee2e2; padding: 10px; border-radius: 4px; border: 1px solid #fca5a5;'><strong>File:</strong> <code>$file</code><br><strong>Line:</strong> $line</p>";
        
        echo "<h3 style='margin-top: 20px;'>Active Variables & Context:</h3>";
        echo "<div style='display: flex; gap: 20px; flex-wrap: wrap;'>";
        
        echo "<div style='flex: 1; min-width: 300px; background: #ffffff; padding: 15px; border: 1px solid #d1d5db; border-radius: 4px; overflow-x: auto;'>";
        echo "<strong style='color: #374151;'>Function Arguments (from trace)</strong>";
        echo "<pre style='color: #4b5563; font-size: 13px; margin-top: 10px;'>" . print_r($contextArgs, true) . "</pre>";
        echo "</div>";

        echo "<div style='flex: 1; min-width: 300px; background: #ffffff; padding: 15px; border: 1px solid #d1d5db; border-radius: 4px; overflow-x: auto;'>";
        echo "<strong style='color: #374151;'>Request State</strong>";
        echo "<pre style='color: #4b5563; font-size: 13px; margin-top: 10px;'>" . print_r(array_filter($requestState), true) . "</pre>";
        echo "</div>";
        echo "</div>";

        if (!empty($trace)) {
            echo "<h3 style='margin-top: 20px;'>Stack Trace:</h3>";
            echo "<pre style='background: #ffffff; color: #374151; padding: 15px; border: 1px solid #d1d5db; border-radius: 4px; overflow-x: auto; font-size: 13px;'>$trace</pre>";
        }
        
        echo '</div>';
        die();
    }

    public static function show(){
        self::$verbose = 2;
    }
    public static function hide(){
        self::$verbose = 1;
    }
}