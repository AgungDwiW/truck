<?php
/**
 * Base routes that redirect to landing page
 * This file contains routes that are not specific to any controller
 */

// Home page route - redirects to landing page
Router::addGet('', 'main');