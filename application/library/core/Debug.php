<?php
class Debuger
{
    public static $verbose = 1;
    private static $treeAssetsLoaded = false;

    /**
     * Call this at the very top of your application.
     */
    public static function register()
    {
        if (!defined("ENVIRONMENT"))
            define("ENVIRONMENT", "DEV");
        if (self::$verbose) {
            error_reporting(E_ALL);
            // We set this to 1 so that when we return `false` for warnings, 
            // PHP natively displays them on the screen.
            ini_set('display_errors', '1'); 
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', '0'); // Hide everything in production
        }

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
        if (ENVIRONMENT == "DEV")
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
            E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED
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
        if (self::$verbose != 2 and !$forceShow) {
            return; // Do nothing when not verbose
        }

        // Use debug_backtrace to find out exactly where dump() was called from
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[0] ?? [];
        $file = $caller['file'] ?? 'Unknown file';
        $line = $caller['line'] ?? '?';

        // Generate a unique ID for this specific dump block to target it with JavaScript
        $dumpId = uniqid('dump_');

        echo '<div style="background: #282c34; color: #abb2bf; padding: 15px; margin: 10px; border-radius: 6px; font-family: monospace; font-size: 14px; text-align: left; overflow-x: auto; border-left: 5px solid #61afef; box-shadow: 0 4px 6px rgba(0,0,0,0.1); position: relative;">';
        
        // Copy Button (copies the print_r-style text of the data)
        echo '<button onclick="navigator.clipboard.writeText(document.getElementById(\'' . $dumpId . '_raw\').textContent).then(() => { let btn = this; btn.innerText = \'Copied!\'; setTimeout(() => btn.innerText = \'Copy\', 2000); })" style="position: absolute; top: 10px; right: 10px; background: #61afef; color: #282c34; border: none; padding: 5px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; cursor: pointer; z-index: 10;">Copy</button>';

        // Output the file and line number
        echo '<div style="color: #5c6370; border-bottom: 1px solid #3e4451; padding-bottom: 8px; margin-bottom: 10px; font-size: 12px; padding-right: 60px;">';
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
        
        // Output the actual data as a collapsible tree (arrays collapsed by default)
        if (!self::$treeAssetsLoaded){
            self::$treeAssetsLoaded = true;
            echo '<style>'
               . '.dd-node{margin:0;}'
               . '.dd-children{margin-left:20px;border-left:1px dashed #5c6370;padding-left:10px;}'
               . '.dd-toggle{cursor:pointer;color:#61afef;user-select:none;display:inline-block;width:16px;font-weight:bold;}'
               . '.dd-type{color:#61afef;}'
               . '.dd-count{color:#5c6370;font-size:12px;}'
               . '.dd-bracket{color:#5c6370;}'
               . '.dd-key{color:#e5c07b;}'
               . '.dd-str{color:#98c379;}'
               . '.dd-num{color:#d19a66;}'
               . '.dd-line{margin:2px 0;}'
               . '</style>';
            echo '<script>function ddToggle(el){var n=el.parentNode;while(n&&String(n.className).indexOf("dd-node")!==0){n=n.parentNode;}if(!n)return;var c=n.querySelector(".dd-children");if(!c)return;var open=c.style.display!=="none";c.style.display=open?"none":"block";el.textContent=open?"\u25B8":"\u25BE";}</script>';
        }
        echo '<div id="' . $dumpId . '" style="margin: 0; color: #fc9a61; background: #393d46; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 13px; overflow-x: auto;">';
        echo self::renderTree($data);
        echo '</div>';
        // hidden print_r-style source used by the Copy button
        echo '<pre id="' . $dumpId . '_raw" style="display:none">' . htmlspecialchars(print_r($data, true), ENT_QUOTES, 'UTF-8') . '</pre>';
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
            self::writeErrorLog($exception, APP_DIR . "logs/ERROR/");

            echo '<div style="font-family: sans-serif; text-align: center; margin-top: 50px; color: #333;">';
            echo '<h1>500 Internal Server Error</h1>';
            echo '<p>Something went wrong. The issue has been logged. Please try again later.</p>';
            echo '</div>';
            die();
        }

        // --- VERBOSE (DEBUG) BEHAVIOR ---
        // Even in dev the error is written to application/logs/debug/Y-m/Y-m-d.log
        self::writeErrorLog($exception, APP_DIR . "logs/debug/");

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

    /**
     * Appends an error entry to a daily log file:
     *   {logBase}/{Y-m}/{Y-m-d}.log
     * Used by both production (logs/ERROR/) and dev (logs/debug/).
     */
    private static function writeErrorLog($exception, $logBase)
    {
        $logDir = rtrim($logBase, '/') . '/' . date('Y-m');
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0777, true);
        }
        $logFile   = $logDir . '/' . date('Y-m-d') . '.log';
        $type      = get_class($exception);
        $message   = $exception->getMessage();
        $file      = $exception->getFile();
        $line      = $exception->getLine();
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$type}: {$message}" . PHP_EOL .
                      "File: {$file} Line: {$line}" . PHP_EOL .
                      "Stack Trace:" . PHP_EOL . $exception->getTraceAsString() . PHP_EOL .
                      str_repeat('-', 50) . PHP_EOL;
        @error_log($logMessage, 3, $logFile);
    }

    /**
     * Renders any value as a collapsible tree. Arrays/objects are rendered
     * collapsed by default; click the toggle (▸) to expand / collapse.
     * The top-level value (depth 0) is expanded on default so its children
     * (and their nested arrays) start collapsed.
     * Example:
     *   ▾ array (2) [
     *        'a' => 'asdasd'
     *        'b' => ▸ array (1) [
     *                   ...
     *               ]
     *   ]
     */
    private static function renderTree($data, $depth = 0)
    {
        if ($depth > 20)
            return '<span class="dd-bracket">...</span>';

        // top level starts expanded; every nested level starts collapsed
        $open      = ($depth === 0);
        $open       = 1;
        $glyph     = $open ? '&#9662;' : '&#9656;';
        $styleAttr = $open ? '' : " style='display:none'";

        if (is_array($data)){
            $html = "<div class='dd-node'><span class='dd-toggle' onclick='ddToggle(this)'>{$glyph}</span> ";
            $html .= "<span class='dd-type'>array</span> <span class='dd-bracket'>[</span><span class='dd-count'>(".count($data).")</span>";
            $html .= "<div class='dd-children'{$styleAttr}>";
            foreach($data as $key => $value){
                $html .= "<div class='dd-line'><span class='dd-key'>".htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8')."</span> => ".self::renderTree($value, $depth + 1)."</div>";
            }
            $html .= "</div><span class='dd-bracket'>]</span></div>";
            return $html;
        }

        if (is_object($data)){
            $vars = get_object_vars($data);
            $html = "<div class='dd-node'><span class='dd-toggle' onclick='ddToggle(this)'>{$glyph}</span> ";
            $html .= "<span class='dd-type'>".htmlspecialchars(get_class($data), ENT_QUOTES, 'UTF-8')."</span> <span class='dd-bracket'>{</span><span class='dd-count'>(".count($vars).")</span>";
            $html .= "<div class='dd-children'{$styleAttr}>";
            foreach($vars as $key => $value){
                $html .= "<div class='dd-line'><span class='dd-key'>".htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8')."</span> => ".self::renderTree($value, $depth + 1)."</div>";
            }
            $html .= "</div><span class='dd-bracket'>}</span></div>";
            return $html;
        }

        if (is_string($data))
            return "<span class='dd-str'>'".htmlspecialchars($data, ENT_QUOTES, 'UTF-8')."'</span>";
        if ($data === null)
            return "<span class='dd-num'>null</span>";
        if ($data === true)
            return "<span class='dd-num'>true</span>";
        if ($data === false)
            return "<span class='dd-num'>false</span>";
        return "<span class='dd-num'>".htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8')."</span>";
    }

    public static function show(){
        self::$verbose = 2;
    }
    public static function hide(){
        self::$verbose = 1;
    }
}