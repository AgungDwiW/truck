# Routing System Documentation

This document describes the routing mechanism used in the Truck Inspection application.

## Overview

The application implements a custom PHP-based routing system that maps clean URLs to controller files and view templates. The routing is handled primarily by `index.php` (the front controller), with support from `.htaccess` URL rewriting.

## Entry Point

All requests are directed to `index.php` via the `.htaccess` rewrite rule:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d 
RewriteRule . index.php [L]
```

This ensures that any request for a non-existent file or directory is passed to `index.php` for processing.

## New Routing Logic in `index.php` (Clean URLs)

### 1. Session and Authentication
- Sessions are started immediately
- User authentication is checked via `User::checkLogin()`
- If login fails, the user is redirected to `login.php`

### 2. URL Parsing (Clean URL Format)
The script extracts the controller and view path from the request URL using a clean URL format:

```php
// Default values
$controller = 'main';
$view_path = 'index';  // Default view path

// Parse URL segments
$segments = explode('/', $path);

if (count($segments) > 0 && !empty($segments[0])) {
    $controller = $segments[0];
    
    // If we have multiple segments (clean URL format)
    if (count($segments) > 1) {
        // Remove controller from segments and join remaining as view path
        array_shift($segments);
        $view_path = implode('/', $segments);
    } 
    // If only controller segment exists, check for action in query string (backward compatibility)
    else if (isset($_GET['action']) && !empty($_GET['action'])) {
        $view_path = $_GET['action'];
    }
}

// Set action variable for compatibility with existing controller code
$action = $view_path;
```

### 3. File Validation
The script checks for the existence of:
- Controller file: `application/controllers/{controller}.php`
- View file: `application/views/{controller}/{view_path}.php`

If both files exist, the controller is included; otherwise, an error page is shown.

### 4. Backward Compatibility
The system maintains backward compatibility with old-style URLs:
- Old format: `/main?action=index` still works
- New format: `/main/index` is preferred

## URL Patterns

### Standard Routes
- `/main` → controller='main', view='index' (default)
- `/main/index` → controller='main', view='index'
- `/main/cari_truck` → controller='main', view='cari_truck'
- `/main/gate1` → controller='main', view='gate1'
- `/auth/signout` → controller='auth', view='signout'

### Subfolder Support
- `/controller/subfolder/view` → controller='controller', view='subfolder/view'
- Example: `/main/subfolder/page` loads `application/views/main/subfolder/page.php`

### Query String Parameters
Query string parameters are preserved and accessible via `$_GET`:
- `/main/edit_nopol?nopol=ABC123` → controller='main', view='edit_nopol', `$_GET['nopol']='ABC123'`

## Controller Structure

### Main Controller (`application/controllers/main.php`)
The main controller handles routing with three approaches:

1. **Direct Action Handling**: For specific AJAX-like actions (`cari_truck`, `kirim_input_nopol`, `kirim_edit_nopol`), the controller includes the corresponding view file directly and exits.

2. **Switch-Case Routing**: For known actions, a switch statement determines which template to load. Most actions use the `master.php` template with `navtop.php` header.

3. **Default Case**: For views not explicitly listed (including subfolder views), the controller loads the `master.php` template with navigation by default.

### Auth Controller (`application/controllers/auth.php`)
Handles authentication-related actions:
- `index`: Redirects to application models
- `signout`: Destroys session and redirects to login
- Default case: Directly includes the view file if it exists

## Master Template (`master.php`)

The master template provides a consistent HTML layout with:
- Bootstrap CSS and JavaScript
- Custom styles and scripts
- Navigation header (if `$navtop` is set)
- Dynamic content inclusion via `include($content)`
- Footer (if `$footer` is set)

The `$content` variable is set by the controller to point to the view file path determined by the routing system.

## View Structure

Views are stored in `application/views/{controller}/{view_path}.php`. Each view file contains the specific HTML/PHP content for that action.

## Authentication Flow

1. **Login Page** (`login.php`): Handles user authentication via POST request
2. **Session Check**: `index.php` verifies login status using `User::checkLogin()`
3. **Redirection**: Successful login redirects to `/main` (default controller/view)

## Error Handling

If the controller or view file doesn't exist, the system loads `application/assets/error.php`.

## Configuration

- **APP_DIR**: Defined in `application/config/config.php` as `'application/'`
- **Root Path**: Constants are set for filesystem navigation
- **Autoloading**: `autoloader.php` includes essential utility classes

## Migration from Old Routing

### Changes Made:
1. **Updated `index.php`**: Modified to parse clean URLs instead of relying on `$_GET['action']`
2. **Updated controllers**: Modified to use `$action` variable instead of `$_GET['action']`
3. **Updated all links**: Changed from `?action=` format to clean URLs:
   - Old: `href="main?action=index"`
   - New: `href="main/index"`
4. **Updated redirects**: Changed PHP header redirects to use clean URLs
5. **Updated AJAX calls**: Modified JavaScript XMLHttpRequest calls to use clean URLs

### Backward Compatibility:
- Old-style URLs with `?action=` parameter still work
- System automatically detects and handles both formats
- All internal links have been updated to use clean URLs

## Key Files

1. `index.php` - Front controller, handles all routing
2. `.htaccess` - URL rewriting rules  
3. `application/controllers/main.php` - Main controller logic
4. `application/controllers/auth.php` - Authentication controller
5. `master.php` - Main layout template
6. `login.php` - Authentication page
7. `application/config/config.php` - Configuration constants

## New Router System (Django-style)

The application now includes a flexible router system similar to Django's `urls.py` that allows defining named routes with parameters.

### Router Features

- **Named routes**: Define routes with names for easy URL generation
- **URL parameters**: Capture parts of the URL as named parameters (e.g., `{id}`)
- **Route matching**: Match incoming URLs against registered routes
- **URL generation**: Generate URLs for named routes with parameters
- **Fallback compatibility**: If no route matches, falls back to segment-based routing

### Defining Routes

Routes are defined in `application/config/routes.php`. Example:
```php
// Basic route
Router::add('about', 'main@about', 'about');

// Route with parameter
Router::add('user/{id}', 'main@user_profile', 'user_profile');

// Multiple parameters
Router::add('user/{id}/edit/{section}', 'main@user_edit', 'user_edit');
```

### Using Routes in Controllers and Views

1. **URL generation in views**:
   ```php
   <a href="<?php echo route('about'); ?>">About</a>
   <a href="<?php echo route('user_profile', ['id' => 123]); ?>">User Profile</a>
   ```

2. **Route parameters**: When a route matches, parameters are available in `$_GET`.
   For route `user/{id}`, accessing `/user/123` makes `$_GET['id'] = '123'`.

### Route Matching Priority

1. Registered routes (from `routes.php`) are checked first
2. If no route matches, falls back to segment-based routing
3. Segment-based routing maintains full backward compatibility

### Router Class

The `Router` class is located in `application/library/Router.php` and provides:
- `Router::add($pattern, $handler, $name)` - Add a route
- `Router::match($url)` - Match a URL against routes
- `Router::url($name, $params)` - Generate URL for a named route
- `Router::getRoutes()` - Get all registered routes

### Helper Functions

`url_helper.php` provides convenience functions:
- `route($name, $params)` - Generate URL for a named route
- `url($path)` - Generate absolute URL for a path

### Example Usage

1. Define a route in `routes.php`:
   ```php
   Router::add('truck/{truck_id}/inspect', 'main@N_cek_truck', 'truck_inspect');
   ```

2. Use in view template:
   ```php
   <a href="<?php echo route('truck_inspect', ['truck_id' => 456]); ?>">
       Inspect Truck #456
   </a>
   ```

3. The controller `main.php` and view `N_cek_truck.php` receive `$_GET['truck_id'] = '456'`.

### Integration with Existing System

The new router system integrates seamlessly:
- Existing URLs continue to work via segment-based routing
- New routes can be added incrementally
- No changes required to existing controllers or views
- Route parameters are available via `$_GET` for compatibility


## Summary

The new routing system provides clean, SEO-friendly URLs while maintaining backward compatibility:
- Uses URL rewriting to create clean URLs like `/controller/view`
- Supports nested paths like `/controller/subfolder/view`
- Maps URL segments to controller and view files
- Validates file existence before inclusion
- Provides a consistent template system via `master.php`
- Integrates authentication checks at the routing level
- Maintains backward compatibility with old `?action=` format

This approach modernizes the URL structure while keeping the application lightweight without requiring a full MVC framework.