<?php
/**
 * URL helper functions for the routing system
 */

/**
 * Generate URL for a named route
 * 
 * @param string $name Route name
 * @param array $params Route parameters
 * @return string Generated URL
 */
function route($name, $params = [])
{
    return Router::url($name, $params);
}

/**
 * Generate absolute URL for a path
 * 
 * @param string $path URL path
 * @return string Absolute URL
 */
function url($path = '')
{
    return BASE_URL . ltrim($path, '/');
}

/**
 * Check if current route matches given name
 * 
 * @param string $name Route name to check
 * @return bool True if current route matches
 */
function is_route($name)
{
    // This would require tracking current route name
    // For now, return false
    return false;
}