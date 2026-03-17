# Static File Helper Library

## Overview

The Static File Helper Library provides a Django-like static file management system for PHP applications. It simplifies referencing static assets (CSS, JS, images, fonts) by automatically generating correct URLs and optionally adding version query strings for cache busting.

## Features

1. **Automatic URL generation** - Similar to Django's `{% static 'path/to/file' %}`
2. **Cache busting** - Optional version query strings based on file modification time
3. **HTML tag generation** - Convenient methods for generating `<link>`, `<script>`, and `<img>` tags
4. **File existence checking** - Verify if static files exist before referencing them
5. **Flexible configuration** - Customizable static URL and directory paths
6. **Global helper functions** - Easy-to-use functions similar to Django template tags

## Installation

The library is automatically included via `application/library/autoload.php`. No additional installation is required.

## Configuration

By default, the library uses:
- Static URL: `BASE_URL . 'static/'` (e.g., `http://127.0.0.1/truck/static/`)
- Static directory: `ROOT_DIR . 'static/'` (e.g., `C:\xampp\htdocs\truck\static\`)
- Versioning: Enabled (adds `?v=timestamp` query strings)

To customize configuration, call `StaticHelper::init()` with options:

```php
StaticHelper::init([
    'static_url' => 'https://cdn.example.com/assets/',
    'static_dir' => '/var/www/assets/',
    'versioning' => false
]);
```

## Usage Examples

### 1. Basic URL Generation

```php
// Get static file URL
$css_url = StaticHelper::url('css/style.css');
// Returns: http://127.0.0.1/truck/static/css/style.css?v=1234567890

// Using global helper function
echo static_url('js/app.js');
```

### 2. Generating HTML Tags

```php
// CSS file
echo StaticHelper::css('css/bootstrap.min.css');
// Output: <link rel="stylesheet" href="http://127.0.0.1/truck/static/css/bootstrap.min.css?v=1234567890">

// JavaScript file
echo StaticHelper::js('js/jquery.min.js');
// Output: <script src="http://127.0.0.1/truck/static/js/jquery.min.js?v=1234567890"></script>

// Image file
echo StaticHelper::img('images/logo.png', ['width' => '100', 'height' => '50', 'alt' => 'Logo']);
// Output: <img src="http://127.0.0.1/truck/static/images/logo.png?v=1234567890" width="100" height="50" alt="Logo">

// Using global helper functions
echo static_css('css/custom.css');
echo static_js('js/app.js', ['defer' => 'defer']);
echo static_img('images/photo.jpg', ['class' => 'img-responsive']);
```

### 3. File System Path

```php
$path = StaticHelper::path('css/style.css');
// Returns: C:\xampp\htdocs\truck\static\css\style.css

// Check if file exists
if (StaticHelper::exists('css/style.css')) {
    // File exists
}
```

### 4. In Views/Templates

```php
<?php
// Initialize at the top of your view file
StaticHelper::init();
?>

<!DOCTYPE html>
<html>
<head>
    <?= static_css('css/bootstrap.min.css') ?>
    <?= static_css('css/custom.css', ['media' => 'screen']) ?>
    <?= static_js('js/jquery.min.js') ?>
</head>
<body>
    <header>
        <?= static_img('images/logo.png', ['alt' => 'Company Logo']) ?>
    </header>
    
    <!-- Content here -->
    
    <?= static_js('js/app.js', ['defer' => 'defer']) ?>
</body>
</html>
```

## Migration from Old System

### Before (Old way):
```php
<link href="<?=BASE_URL?>static/css/bootstrap.min.css" rel="stylesheet">
<script src="<?=BASE_URL?>static/js/jquery.min.js"></script>
<img src="static/images/logo.png">
```

### After (Using Static Helper):
```php
<?= static_css('css/bootstrap.min.css') ?>
<?= static_js('js/jquery.min.js') ?>
<?= static_img('images/logo.png') ?>
```

### Benefits:
1. **Cleaner code** - No need to manually concatenate BASE_URL
2. **Cache busting** - Automatic version query strings prevent stale cache
3. **Centralized management** - Change static file location in one place
4. **Type safety** - HTML attributes are properly escaped

## API Reference

### StaticHelper Class Methods

| Method | Description |
|--------|-------------|
| `StaticHelper::init($config)` | Initialize with custom configuration |
| `StaticHelper::url($path)` | Get URL for static file |
| `StaticHelper::path($path)` | Get filesystem path for static file |
| `StaticHelper::exists($path)` | Check if static file exists |
| `StaticHelper::css($path, $attributes)` | Generate CSS link tag |
| `StaticHelper::js($path, $attributes)` | Generate JavaScript script tag |
| `StaticHelper::img($path, $attributes)` | Generate image tag |
| `StaticHelper::clearCache()` | Clear version cache |

### Global Helper Functions

| Function | Description |
|----------|-------------|
| `static_url($path)` | Get static file URL |
| `static_path($path)` | Get static file system path |
| `static_css($path, $attributes)` | Generate CSS link tag |
| `static_js($path, $attributes)` | Generate JavaScript script tag |
| `static_img($path, $attributes)` | Generate image tag |

## Best Practices

1. **Always initialize** - Call `StaticHelper::init()` at the beginning of each PHP file that uses static helpers
2. **Use in templates** - The helper functions are ideal for view/template files
3. **Versioning for production** - Enable versioning in production for cache busting
4. **Disable versioning for development** - Consider disabling versioning during development for faster reloads
5. **Check file existence** - Use `StaticHelper::exists()` before referencing files in conditional logic

## Integration with Existing Codebase

The library has been integrated into the following files:

1. **Master templates** - `master.php`, `navtop.php`, `login.php`
2. **View files** - `gate1.php`, `gate1_new.php`, `foto_gate1.php`, `N_gate1.php`, etc.
3. **Utility files** - `utility_function.php`
4. **Standalone pages** - `report.php`, `temuan.php`

## Troubleshooting

### Common Issues:

1. **"Class StaticHelper not found"**
   - Ensure `application/library/autoload.php` is included
   - Check that `StaticHelper.php` exists in `application/library/core/`

2. **Static files not loading**
   - Verify the static directory exists at the configured path
   - Check file permissions
   - Ensure BASE_URL is correctly defined in config.php

3. **Version query strings not appearing**
   - Check that versioning is enabled: `StaticHelper::init(['versioning' => true])`
   - Verify file modification time is accessible

## Related Files

- `application/library/core/StaticHelper.php` - Main library file
- `application/library/autoload.php` - Autoloads the library
- `application/config/config.php` - Contains BASE_URL definition
- `static/` - Static files directory structure

## Performance Considerations

1. **Cache** - File modification times are cached per request
2. **File operations** - `file_exists()` and `filemtime()` calls are minimized
3. **Memory** - Minimal memory footprint
4. **Versioning overhead** - Disable versioning if performance is critical and cache busting isn't needed