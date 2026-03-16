# Routing System Documentation

This document describes the routing mechanism used in the Truck Inspection application.

## Overview

The application implements a custom PHP-based routing system that maps URLs to controller files and view templates. The routing is handled primarily by `index.php` (the front controller), with support from `.htaccess` URL rewriting.

## Entry Point

All requests are directed to `index.php` via the `.htaccess` rewrite rule:

```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d 
RewriteRule . index.php [L]
```

This ensures that any request for a non-existent file or directory is passed to `index.php` for processing.

## Routing Logic in `index.php`

### 1. Session and Authentication
- Sessions are started immediately
- User authentication is checked via `User::checkLogin()`
- If login fails, the user is redirected to `login.php`

### 2. URL Parsing
The script extracts the controller and action from the request URL:

```php
// Default values
$controller = 'main';
$action = 'index';
$url = '';

// Get request URL and script URL
$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';

// Remove the script path from request URL
if($request_url != $script_url) 
    $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');

// Split URL into segments (remove query string)
$segments = explode('?', $url);
$org_segments = explode('/', $request_url);
```

### 3. Controller and Action Determination
- The first URL segment becomes the controller name (default: 'main')
- The `action` parameter from `$_GET['action']` becomes the action (default: 'index')
- Example: `/main?action=gate1` → controller='main', action='gate1'

### 4. File Validation
The script checks for the existence of:
- Controller file: `application/controllers/{controller}.php`
- View file: `application/views/{controller}/{action}.php`

If both files exist, the controller is included; otherwise, an error page is shown.

## Controller Structure (`application/controllers/main.php`)

### Action Handling
The main controller uses two approaches:

1. **Direct Action Handling**: For specific AJAX-like actions (`cari_truck`, `kirim_input_nopol`, `kirim_edit_nopol`), the controller includes the corresponding view file directly and exits.

2. **Switch-Case Routing**: For other actions, a switch statement determines which template to load. Most actions use the `master.php` template with `navtop.php` header.

### Template Inclusion
- Valid actions load `master.php` which serves as the main layout template
- The template includes `navtop.php` (navigation header) and the specific view content
- Some actions may have special handling (e.g., `simpan_gate1`, `vmipost`, `simupost`)

## Master Template (`master.php`)

The master template provides a consistent HTML layout with:
- Bootstrap CSS and JavaScript
- Custom styles and scripts
- Navigation header (if `$navtop` is set)
- Dynamic content inclusion via `include($content)`
- Footer (if `$footer` is set)

The `$content` variable points to the view file path determined by the routing system.

## View Structure

Views are stored in `application/views/{controller}/{action}.php`. Each view file contains the specific HTML/PHP content for that action.

## Authentication Flow

1. **Login Page** (`login.php`): Handles user authentication via POST request
2. **Session Check**: `index.php` verifies login status using `User::checkLogin()`
3. **Redirection**: Successful login redirects to `/main` (default controller/action)

## URL Patterns

### Standard Routes
- `/main` → controller='main', action='index'
- `/main?action=gate1` → controller='main', action='gate1'
- `/controllerName?action=actionName` → general pattern

### Direct File Access
Static assets (CSS, JS, images) are accessible because `.htaccess` only rewrites requests for non-existent files/directories.

## Error Handling

If the controller or view file doesn't exist, the system loads `application/assets/error.php` (though the exact error handling may vary).

## Configuration

- **APP_DIR**: Defined in `application/config/config.php` as `'application/'`
- **Root Path**: Constants are set for filesystem navigation
- **Autoloading**: `autoloader.php` includes essential utility classes

## Key Files

1. `index.php` - Front controller, handles all routing
2. `.htaccess` - URL rewriting rules
3. `application/controllers/main.php` - Main controller logic
4. `master.php` - Main layout template
5. `login.php` - Authentication page
6. `application/config/config.php` - Configuration constants

## Summary

The routing system is a simple but effective custom PHP implementation that:
- Uses URL rewriting to create clean URLs
- Maps URL segments to controller and action
- Validates file existence before inclusion
- Provides a consistent template system via `master.php`
- Integrates authentication checks at the routing level

This approach keeps the application lightweight without requiring a full MVC framework, while maintaining separation of concerns between controllers, views, and templates.