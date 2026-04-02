<?php
/**
 * Static File Helper
 * 
 * Provides functionality similar to Django's static file system
 * for managing and serving static assets (CSS, JS, images, etc.)
 */

class StaticHelper
{
    /**
     * @var string Static files base URL
     */
    private static $static_url = null;
    
    /**
     * @var string Static files directory path
     */
    private static $static_dir = null;
    
    /**
     * @var array Cache for file modification times
     */
    private static $cache = [];
    
    /**
     * @var bool Whether to append version query string
     */
    private static $versioning = true;
    
    /**
     * Initialize static helper configuration
     * 
     * @param array $config Configuration options:
     *                      - 'static_url': Base URL for static files (default: BASE_URL . 'static/')
     *                      - 'static_dir': Filesystem path to static directory (default: ROOT_DIR . 'static/')
     *                      - 'versioning': Whether to append version query strings (default: true)
     */
    public static function init($config = [])
    {
        // Set static URL
        if (isset($config['static_url'])) {
            self::$static_url = $config['static_url'];
        } else {
            self::$static_url = BASE_URL . 'static/';
        }
        
        // Set static directory
        if (isset($config['static_dir'])) {
            self::$static_dir = $config['static_dir'];
        } else {
            self::$static_dir = ROOT_DIR . 'static/';
            
        }
        
        // Set versioning preference
        if (isset($config['versioning'])) {
            self::$versioning = (bool) $config['versioning'];
        }
        
        // Ensure static directory exists
        if (!is_dir(self::$static_dir)) {
            throw new Exception("Static directory does not exist: " . self::$static_dir);
        }
    }
    
    /**
     * Get the URL for a static file
     * Similar to Django's {% static 'path/to/file' %}
     * 
     * @param string $path Path to static file relative to static directory
     * @return string Full URL to the static file
     */
    public static function url($path)
    {
        // Initialize if not already done
        if (self::$static_url === null) {
            self::init();
        }
        
        // Clean path
        $path = ltrim($path, '/');
        
        // Build URL
        $url = self::$static_url . $path;
        
        // Add version query string if versioning is enabled
        if (self::$versioning) {
            $version = self::getFileVersion($path);
            if ($version) {
                $url .= '?v=' . $version;
            }
        }
        
        return $url;
    }
    
    /**
     * Get the filesystem path for a static file
     * 
     * @param string $path Path to static file relative to static directory
     * @return string Full filesystem path to the static file
     */
    public static function path($path)
    {
        // Initialize if not already done
        if (self::$static_dir === null) {
            self::init();
        }
        
        // Clean path
        $path = ltrim($path, '/');
        
        return self::$static_dir . $path;
    }
    
    /**
     * Check if a static file exists
     * 
     * @param string $path Path to static file relative to static directory
     * @return bool True if file exists
     */
    public static function exists($path)
    {
        return file_exists(self::path($path));
    }
    
    /**
     * Get file modification time as version string
     * 
     * @param string $path Path to static file relative to static directory
     * @return string|null File modification timestamp or null if file doesn't exist
     */
    private static function getFileVersion($path)
    {
        $full_path = self::path($path);
        
        if (!file_exists($full_path)) {
            return null;
        }
        
        // Use cached version if available
        $cache_key = md5($full_path);
        if (isset(self::$cache[$cache_key])) {
            return self::$cache[$cache_key];
        }
        
        $version = filemtime($full_path);
        self::$cache[$cache_key] = $version;
        
        return $version;
    }
    
    /**
     * Generate HTML link tag for CSS file
     * 
     * @param string $path Path to CSS file relative to static directory
     * @param array $attributes Additional HTML attributes
     * @return string HTML link tag
     */
    public static function css($path, $attributes = [])
    {
        $url = self::url($path);
        
        $attrs = [];
        foreach ($attributes as $key => $value) {
            $attrs[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
        }
        
        $attr_string = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        
        return '<link rel="stylesheet" href="' . htmlspecialchars($url, ENT_QUOTES) . '"' . $attr_string . '>';
    }
    
    /**
     * Generate HTML script tag for JavaScript file
     * 
     * @param string $path Path to JS file relative to static directory
     * @param array $attributes Additional HTML attributes
     * @return string HTML script tag
     */
    public static function js($path, $attributes = [])
    {
        $url = self::url($path);
        
        $attrs = [];
        foreach ($attributes as $key => $value) {
            $attrs[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
        }
        
        $attr_string = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        
        return '<script src="' . htmlspecialchars($url, ENT_QUOTES) . '"' . $attr_string . '></script>';
    }
    
    /**
     * Generate HTML img tag for image file
     * 
     * @param string $path Path to image file relative to static directory
     * @param array $attributes Additional HTML attributes
     * @return string HTML img tag
     */
    public static function img($path, $attributes = [])
    {
        $url = self::url($path);
        
        $attrs = [];
        foreach ($attributes as $key => $value) {
            $attrs[] = $key . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
        }
        
        $attr_string = !empty($attrs) ? ' ' . implode(' ', $attrs) : '';
        
        return '<img src="' . htmlspecialchars($url, ENT_QUOTES) . '"' . $attr_string . '>';
    }
    
    /**
     * Clear the version cache
     */
    public static function clearCache()
    {
        self::$cache = [];
    }
}

// Create a global helper function similar to Django's static template tag
if (!function_exists('static_url')) {
    /**
     * Global helper function to get static file URL
     * Usage: echo static_url('css/style.css');
     * 
     * @param string $path Path to static file
     * @return string Static file URL
     */
    function static_url($path)
    {
        return StaticHelper::url($path);
    }
}

if (!function_exists('static_path')) {
    /**
     * Global helper function to get static file system path
     * 
     * @param string $path Path to static file
     * @return string Static file system path
     */
    function static_path($path)
    {
        return StaticHelper::path($path);
    }
}

if (!function_exists('static_css')) {
    /**
     * Global helper function to generate CSS link tag
     * 
     * @param string $path Path to CSS file
     * @param array $attributes Additional HTML attributes
     * @return string HTML link tag
     */
    function static_css($path, $attributes = [])
    {
        return StaticHelper::css($path, $attributes);
    }
}

if (!function_exists('static_js')) {
    /**
     * Global helper function to generate JavaScript script tag
     * 
     * @param string $path Path to JS file
     * @param array $attributes Additional HTML attributes
     * @return string HTML script tag
     */
    function static_js($path, $attributes = [])
    {
        return StaticHelper::js($path, $attributes);
    }
}

if (!function_exists('static_img')) {
    /**
     * Global helper function to generate image tag
     * 
     * @param string $path Path to image file
     * @param array $attributes Additional HTML attributes
     * @return string HTML img tag
     */
    function static_img($path, $attributes = [])
    {
        return StaticHelper::img($path, $attributes);
    }
}

?>