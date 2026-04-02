<?php
/**
 * Routes for main controller
 * All routes in this file will be prefixed with '/main' automatically
 */

Router::group('material', function() {
    Router::addGet('N_cek_truck',  'N_cek_truck');
    Router::addGet('cek_nopol',  'cek_nopol');

});