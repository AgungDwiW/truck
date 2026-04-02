<?php
/**
 * Routes for main controller
 * All routes in this file will be prefixed with '/main' automatically
 */

Router::group('gate1', function() {
    Router::addGet('N_gate1',  'N_gate1');
    Router::addGet('N_gate_api',  'N_gate_api');
    Router::addGet('simpan_gate1', 'simpan_gate1');

});