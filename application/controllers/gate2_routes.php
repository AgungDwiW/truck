<?php
/**
 * Routes for main controller
 * All routes in this file will be prefixed with '/main' automatically
 */

Router::group('gate2', function() {
    Router::addGet('db_waiting',  'db_waiting');
    
    Router::addGet('cek_gate2',  'cek_gate2');
    Router::addGet('lanjut_gate2',  'lanjut_gate2');

});