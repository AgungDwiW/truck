<?php
/**
 * Routes for main controller
 * All routes in this file will be prefixed with '/main' automatically
 */

Router::group('FG', function() {
    Router::addGet('FG_cek_truck',  'FG_cek_truck');
    Router::addGet('FG_cek_truck_rev',  'FG_cek_truck_rev');
    Router::addGet('FG_cek_nopol',  'FG_cek_nopol');
    Router::addGet('FG_cek_nopol_new',  'FG_cek_nopol_new');

});