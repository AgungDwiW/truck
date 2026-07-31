<?php
/**
 * Router class for URL routing similar to Django's urls.py
 * 
 * Allows defining named routes and matching URLs to controllers/actions
 * Supports parameter extraction from URL patterns
 */

class Router
{
    /**
     * @var array Registered routes
     */
    private static $routes = [];
    
    /**
     * @var array Named routes for reverse URL generation
     */
    private static $namedRoutes = [];
    
    /**
     * @var Router Singleton instance
     */
    private static $instance = null;
    
    /**
     * @var string Current route group prefix
     */
    private static $currentPrefix = '';
    
    /**
     * @var array Stack for nested route groups
     */
    private static $prefixStack = [];
    
    /**
     * Get singleton instance
     * 
     * @return Router
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

   
    /**
     * Add a route to the router
     * 
     * @param string $pattern URL pattern (e.g., 'user/{id}/edit')
     * @param mixed $handler Controller action (e.g., 'user@edit') or callable
     * @param string|null $name Route name for reverse URL generation
     */
    /**
     * Add a route to the router
     */
    public static function add($pattern, $handler, $name = null)
    {
        // 1. Safely prepend group prefix to pattern (handles empty patterns like '')
        if (self::$currentPrefix !== '') {
            $cleanPattern = ltrim($pattern, '/');
            $pattern = $cleanPattern !== '' 
                ? rtrim(self::$currentPrefix, '/') . '/' . $cleanPattern 
                : rtrim(self::$currentPrefix, '/');
        }
        
        // 2. Prepend group prefix to handler if it's a simple string
        if (is_string($handler) && self::$currentPrefix !== '') {
            if (strpos($handler, '/') === false && strpos($handler, '@') === false) {
                $handler = rtrim(self::$currentPrefix, '/') . '/' . $handler;
            }
        }
        
        // 3. Use handler as name if name is not specified
        if ($name === null && is_string($handler)) {
            $name = $handler;
        }
        
        $regex = self::patternToRegex($pattern);
        
        $route = [
            'pattern' => $pattern,
            'regex' => $regex,
            'handler' => $handler,
            'params' => self::extractParamNames($pattern),
            'name' => $name,
            'method' => 'ANY' // <-- Add this
        ];
        
        self::$routes[] = $route;
        
        if ($name !== null) {
            self::$namedRoutes[$name] = $route;
        }
        
        return $route;
    }

    public static function addGet($pattern, $handler = null, $name = null)
    {
        // 1. Safely prepend group prefix to pattern
        if (self::$currentPrefix !== '') {
            $cleanPattern = ltrim($pattern, '/');
            $pattern = $cleanPattern !== '' 
                ? rtrim(self::$currentPrefix, '/') . '/' . $cleanPattern 
                : rtrim(self::$currentPrefix, '/');
        }
        
        // 3. Use handler as name if name is not specified
        if ($name === null && is_string($handler)) {
            $name = $handler;
        }
        // 2. Prepend group prefix to handler if it's a simple string
        if (is_string($handler) && self::$currentPrefix !== '') {
            if (strpos($handler, '/') === false && strpos($handler, '@') === false) {
                $handler = rtrim(self::$currentPrefix, '/') . '/' . $handler;
            }
        }
        
        
        $regex = self::patternToRegex($pattern);
        
        // If a handler is provided, use it. Otherwise, default to parseGet.
        $actualHandler = $handler ? $handler : ["Router", "parseGet"];
        
        $route = [
            'pattern' => $pattern,
            'regex' => $regex,
            'handler' => $actualHandler,
            'params' => self::extractParamNames($pattern),
            'name'  => $name,
            'method' => 'GET' // <-- Add this
        ];
        
        self::$routes[] = $route;
        
        if ($name !== null) {
            self::$namedRoutes[$name] = $route;
        }
        
        return $route;
    }

    public static function addPost($pattern, $handler = null, $name = null)
    {
        // 1. Safely prepend group prefix to pattern
        if (self::$currentPrefix !== '') {
            $cleanPattern = ltrim($pattern, '/');
            $pattern = $cleanPattern !== '' 
                ? rtrim(self::$currentPrefix, '/') . '/' . $cleanPattern 
                : rtrim(self::$currentPrefix, '/');
        }
        // 3. Use handler as name if name is not specified
        if ($name === null && is_string($handler)) {
            $name = $handler;
        }
        
        
        // 2. Prepend group prefix to handler if it's a simple string
        if (is_string($handler) && self::$currentPrefix !== '') {
            if (strpos($handler, '/') === false && strpos($handler, '@') === false) {
                $handler = rtrim(self::$currentPrefix, '/') . '/' . $handler;
            }
        }
        
        
        
        $regex = self::patternToRegex($pattern);
        
        // If a handler is provided, use it. Otherwise, default to parsePost.
        $actualHandler = $handler ? $handler : ["Router", "parsePost"];
        
        $route = [
            'pattern' => $pattern,
            'regex' => $regex,
            'handler' => $actualHandler,
            'params' => self::extractParamNames($pattern),
            'name'  => $name,
            'method' => 'POST' // <-- Add this
        ];
        
        self::$routes[] = $route;
        
        if ($name !== null) {
            self::$namedRoutes[$name] = $route;
        }
        
        return $route;
    }
    
    
    /**
     * Create a route group with a common prefix
     * 
     * @param string $prefix Prefix for all routes in the group
     * @param callable $callback Function that defines routes within the group
     */
    public static function group($prefix, $callback)
    {
        // Push current prefix onto stack
        self::$prefixStack[] = self::$currentPrefix;
        
        // Set new prefix
        if (self::$currentPrefix === '') {
            self::$currentPrefix = $prefix;
        } else {
            self::$currentPrefix = rtrim(self::$currentPrefix, '/') . '/' . ltrim($prefix, '/');
        }
        
        // Execute callback to define routes
        call_user_func($callback);
        
        // Restore previous prefix
        self::$currentPrefix = array_pop(self::$prefixStack);
    }
    
    /**
     * Match a URL against registered routes
     * 
     * @param string $url URL to match
     * @return array|null Matched route with parameters or null if no match
     */
    public static function match($url)
    {
        // Remove leading/trailing slashes
        $url = trim($url, '/');
        
        foreach (self::$routes as $route) {
            // Debuger::dump([$route['regex'], $url, preg_match($route['regex'], $url, $matches)],1);
            if (preg_match($route['regex'], $url, $matches) || preg_match($route['regex'], $url."/", $matches)) {
                // Extract parameter values
                $params = [];
                foreach ($route['params'] as $paramName) {
                    if (isset($matches[$paramName])) {
                        $params[$paramName] = $matches[$paramName];
                    }
                }
                
                return [
                    'route' => $route,
                    'params' => $params,
                    'handler' => $route['handler']
                ];
            }
        }
        
        return null;
    }
    
    /**
     * Generate URL for a named route
     * 
     * @param string $name Route name
     * @param array $params Parameters for the route
     * @return string Generated URL
     * @throws Exception if route not found or missing parameters
     */
    public static function url($name, $params = [])
    {
        if (!isset(self::$namedRoutes[$name])) {
            throw new Exception("Route '{$name}' not found");
        }
        
        $route = self::$namedRoutes[$name];
        $url = $route['pattern'];
        // Debuger::dump([$url, $params]);
        // Replace parameters in the pattern
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }
        // Debuger::dump($url);
        
        // Check for any remaining required parameters
        if (preg_match('/\{([^}]+)\}/', $url, $matches)) {
            throw new Exception("Missing required parameter '{$matches[1]}' for route '{$name}'");
        }
        if (defined('BASE_URL')) {
            return BASE_URL . $url;
        } else {
            return '/' . $url;
        }
    }
    
    /**
     * Get all registered routes
     * 
     * @return array
     */
    public static function getRoutes()
    {
        return self::$routes;
    }
    
    /**
     * Get all named routes
     * 
     * @return array
     */
    public static function getNamedRoutes()
    {
        return self::$namedRoutes;
    }
    
    /**
     * Clear all routes (useful for testing)
     */
    public static function clear()
    {
        self::$routes = [];
        self::$namedRoutes = [];
    }
    
    /**
     * Convert URL pattern to regex
     * 
     * @param string $pattern URL pattern with {param} placeholders
     * @return string Regular expression
     */
    private static function patternToRegex($pattern)
    {
        // Escape special regex characters
        $regex = preg_quote($pattern, '/');
        
        // Unescape curly braces that were escaped by preg_quote
        $regex = str_replace(['\{', '\}'], ['{', '}'], $regex);
        
        // Replace {param} with named capture groups
        $regex = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^\/]+)', $regex);
        
        // Match whole string
        return '/^' . $regex . '$/';
    }
    
    /**
     * Extract parameter names from pattern
     * 
     * @param string $pattern URL pattern
     * @return array Parameter names
     */
    private static function extractParamNames($pattern)
    {
        preg_match_all('/\{([^}]+)\}/', $pattern, $matches);
        return $matches[1] ?? [];
    }
    
    /**
     * Parse handler string (e.g., 'controller@action')
     * 
     * @param string $handler Handler string
     * @return array [controller, action]
     */
    public static function parseHandler($handler)
    {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            return explode('@', $handler, 2);
        }
        
        return [null, null];
    }

    public static function parseGet($content,$params = Null){
        $navtop = 'navtop.php';
        // $footer = 'footer.php';
        require('master.php');
        exit();
    }
    public static function parsePost($content,$params = Null){
        include($content);
        exit();
    }


    
    public static  function handleRouting(){
        // Set our defaults
        $controller = 'main';
        $view_path = 'index';  // Default view path
        $url = '';
        $route_params = [];
        
        // Get request url and script url
        $request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
        $script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';

        // Remove the script path from request URL
        if($request_url != $script_url) {
            $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');
        }

        // Remove query string from URL for routing purposes
        $url_segments = explode('?', $url);
        $path = $url_segments[0];
        // Debuger::dump($path,1);
        
        $matchedRoute = Router::match($path);
        // Debuger::dump(Router::getNamedRoutes(),1);
        // exit();
        if ($matchedRoute !== null) {
            // Route matched, extract controller and action from handler
            $handler = $matchedRoute['handler'];
            $route_params = $matchedRoute['params'];
            $pattern  = $matchedRoute['route']['pattern'];
            if (is_string($handler)) {
                // Handler format: "controller/action" or "controller@action"
                if (strpos($handler, '@') !== false) {
                    list($controller, $view_path) = explode('@', $handler, 2);
                } elseif (strpos($handler, '/') !== false) {
                    list($controller, $view_path) = explode('/', $handler, 2);
                } else {
                    // Assume handler is just controller, default to index action
                    $controller = $handler;
                    $view_path = 'index';
                }
                // Set action variable for compatibility with existing controller code
                $action = $view_path;
                
                // Store route parameters in $_GET for backward compatibility
                foreach ($route_params as $key => $value) {
                    $_GET[$key] = $value;
                }
                if ($view_path =='')
                    $view_path = 'index';
                
                // Get our controller file
                $controller_path = APP_DIR . 'controllers/' . $controller . '.php';
                $view_full_path = APP_DIR . 'views/' . $controller . '/' . $view_path . '.php';

                // Set content variable for master.php template
                // Debuger::dump([$controller, $view_path, $controller_path, $view_full_path],1);
                // exit();
                // Set content variable for master.php template
                $content = $view_full_path;
                
                // Check which method was used to register the route
                $routeMethod = $matchedRoute['route']['method'] ?? 'ANY';

                if ($routeMethod === 'GET') {
                    // Send directly to parseGet instead of including the controller
                    self::parseGet($content, array_values($route_params));
                    exit();
                } elseif ($routeMethod === 'POST') {
                    // Send directly to parsePost instead of including the controller
                    self::parsePost($content, array_values($route_params));
                    exit();
                } else {
                    // Fallback to standard controller include for generic add() routes
                    if (file_exists($controller_path) && file_exists($view_full_path)) {
                        include($controller_path);
                        exit();
                    } else {
                        require_once(APP_DIR . 'assets/error.php');
                        exit();
                    }
                }
            } elseif (is_callable($handler)) {
                // Callable handler - execute it and exit
                if (strpos($pattern, '/') !== false) {
                    list($controller, $view_path) = explode('/', $pattern, 2);
                } else {
                    // Assume handler is just controller, default to index action
                    $controller = $pattern;
                    $view_path = 'index';
                }
                
                // --- NEW FALLBACK LOGIC ---
                if ($controller == '') {
                    // If the pattern was "", check if a name was provided (e.g., "something")
                    $routeName = $matchedRoute['route']['name'] ?? null;
                    
                    if (!empty($routeName)) {
                        // Check if the name includes a specific view (e.g., "something/custom")
                        if (strpos($routeName, '/') !== false) {
                            list($controller, $view_path) = explode('/', $routeName, 2);
                        } else {
                            $controller = $routeName;
                            $view_path = 'index';
                        }
                    } else {
                        // Ultimate fallback if no pattern AND no name was provided
                        $controller = "main";
                        $view_path = 'index';
                    }
                }
                if ($view_path == '')
                    $view_path = 'index';
                // --- END NEW FALLBACK LOGIC ---

                $controller_path = APP_DIR . 'controllers/' . $controller . '.php';
                $view_full_path = APP_DIR . 'views/' . $controller . '/' . $view_path . '.php';
                Debuger::dump([$controller, $view_path, $controller_path, $view_full_path],1);
                // exit();
                // Set content variable for master.php template
                $content = $view_full_path;
                call_user_func_array($handler,[$content,  array_values($route_params)]);

                exit;
            }
        
        }
    }
}