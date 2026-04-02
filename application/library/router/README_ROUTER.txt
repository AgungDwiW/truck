Router System Quick Start
=========================

The application now includes a flexible router system similar to Django's urls.py.

Features:
- Named routes with URL patterns
- URL parameter extraction (e.g., {id})
- Reverse URL generation by route name
- Seamless integration with existing segment-based routing

1. Defining Routes
------------------
Edit `application/controllers/routes.php` to define your routes:

Example:
```
// Basic route
Router::add('about', 'main@about', 'about');

// Route with parameter  
Router::add('user/{id}', 'main@user_profile', 'user_profile');

// Multiple parameters
Router::add('user/{id}/edit/{section}', 'main@user_edit', 'user_edit');
```

Patterns:
- Use {param_name} for URL parameters
- Parameters match any non-slash character
- Patterns should not start with a slash

Handler format:
- "controller@action" (controller file name without .php, action corresponds to view file)
- Callable function for API endpoints

2. Using Routes in Views
------------------------
Use the `route()` helper function to generate URLs:

```
<a href="<?php echo route('about'); ?>">About</a>
<a href="<?php echo route('user_profile', ['id' => 123]); ?>">User Profile</a>
```

3. Accessing Route Parameters
-----------------------------
When a route matches, parameters are available in $_GET:

Example: For route `user/{id}`, accessing `/user/123` sets `$_GET['id'] = '123'`.

4. Route Matching Priority
--------------------------
1. Registered routes (from routes.php) are checked first
2. If no route matches, falls back to segment-based routing
3. Segment-based routing maintains full backward compatibility

5. Helper Functions
-------------------
- `route($name, $params)` - Generate URL for a named route
- `url($path)` - Generate absolute URL for a path

6. Integration Notes
--------------------
- Routes are automatically loaded by autoloader.php
- The Router is integrated into index.php
- Existing URLs continue to work via fallback routing
- No changes required to existing controllers or views

7. Example: Creating a New Route
--------------------------------
1. Add route to routes.php:
   ```
   Router::add('products/{id}', 'catalog@detail', 'product_detail');
   ```

2. Ensure controller file exists: `application/controllers/catalog.php`
3. Ensure view file exists: `application/views/catalog/detail.php`
4. Use in views: `route('product_detail', ['id' => $product_id])`

For more details, see summary_routing.md in the project root.