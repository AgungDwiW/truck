<?php
spl_autoload_register(function ($class) {

    $prefix = 'App\\';
    $baseDir = __DIR__ . '/application/';

    // Is this our namespace?
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // Remove the namespace prefix
    $relativeClass = substr($class, strlen($prefix));

    // Convert namespace separators into directory separators
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

include_once APP_DIR . "library/model/ApiModel.php";
include_once APP_DIR . "library/core/Debug.php";
include_once APP_DIR . "library/core/Cache.php";
// Apps that ship their own User model (custom session name / permission
// checks) require it BEFORE this file; in that case the shared Auth/User must
// not be loaded (class User redeclare).
if (!class_exists('User', false)) {
	include_once APP_DIR . "library/Auth/User.php";
}
include_once APP_DIR . "library/model/Table.php";

include_once APP_DIR . "library/core/utility_function_withoutJS.php";
include_once APP_DIR . "library/core/CSRF.php";


CSRF::initSession();
Debuger::register();
