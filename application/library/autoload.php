<?php
include_once APP_DIR . "library/core/Cache.php";
include_once APP_DIR . "library/core/Debug.php";
include_once APP_DIR . "library/core/utility_function_withoutJS.php";
include_once APP_DIR . "library/model/Table.php";
include_once APP_DIR . "library/core/CSRF.php";
include_once APP_DIR . "library/router/Router.php";
include_once APP_DIR . "library/router/url_helper.php";
include_once APP_DIR . "config/routes.php";

CSRF::initSession();
Debuger::register();
?>