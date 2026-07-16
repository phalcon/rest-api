<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists(dirname(__FILE__) . '/public' . $uri)) {
    return false;
}

$_GET['_url'] = $_SERVER['REQUEST_URI'];

require_once dirname(__FILE__) . '/public/index.php';
