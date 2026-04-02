<?php
/**
 * Routes for auth controller
 * All routes in this file will be prefixed with '/auth' automatically
 */

Router::group('auth', function() {
    // Login page (if needed)
    // Router::add('login', 'auth@index', 'auth_login'); // handled by login.php
    
    // Sign out
    Router::add('signout', 'auth@signout', 'auth_signout');
    
    // Other auth actions can be added here
});