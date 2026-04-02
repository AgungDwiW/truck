<?php
/**
 * Route definitions for the application
 * 
 * Define routes similar to Django's urls.py format:
 * Router::add('pattern', 'controller@action', 'route_name');
 * 
 * Patterns can include parameters: {param}
 * Example: 'user/{id}/edit' will match /user/123/edit and extract id=123
 * 
 * The controller should be the controller filename without .php
 * The action should correspond to a view file in views/controller/action.php
 * 
 * Routes defined here take precedence over the default segment-based routing.
 */

// ============================================
// Base Routes (loaded first)
// ============================================
if (file_exists(APP_DIR . 'controllers/base_routes.php')) {
    include_once APP_DIR . 'controllers/base_routes.php';
}

// ============================================
// Controller-specific Routes
// ============================================
// Automatically load all *_routes.php files from controllers directory
$controllersDir = APP_DIR . 'controllers/';
if (is_dir($controllersDir)) {
    $routeFiles = glob($controllersDir . '*_routes.php');
    foreach ($routeFiles as $routeFile) {
        // Skip base_routes.php as already loaded
        if (basename($routeFile) === 'base_routes.php') {
            continue;
        }
        include_once $routeFile;
    }
}

// ============================================
// Default Route (keep this as fallback)
// ============================================
// This route ensures the home page works with empty URL
// Already defined in base_routes.php, but we keep it here for safety
if (!Router::getRoutes()) {
    Router::add('', 'main@index', 'home');
}

// ============================================
// Application Routes (legacy - you can still define routes here)
// ============================================
// Add your application routes below.
// You can define cleaner URLs for existing actions.

// Example: Route for gate1 inspection (original URL: /main/gate1)
// Router::add('gate1', 'main@gate1', 'gate1');

// Example: Route for gate1 photo upload (original URL: /main/foto_gate1)
// Router::add('gate1/foto', 'main@foto_gate1', 'foto_gate1');

// Example: Route for user registration (original URL: /main/reg_user)
// Router::add('user/register', 'main@reg_user', 'reg_user');

// Example: Route for editing user with ID parameter (original URL: /main/edit_user?id=123)
// Router::add('user/edit/{id}', 'main@edit_user', 'edit_user');

// Example: Route for truck inspection with parameter (original URL: /main/N_cek_truck?truck_id=456)
// Router::add('truck/{truck_id}/inspect', 'main@N_cek_truck', 'truck_inspect');

// Add your custom routes below this line
