<?php
/**
 * Routes for main controller
 * All routes in this file will be prefixed with '/main' automatically
 */

Router::group('report', function() {
    // Flat action routes (for compatibility with existing links)
    // ============================================
    Router::addGet('',  'index', "report_index");
    Router::addPost('api/{action}', 'api', 'report_api');

});
