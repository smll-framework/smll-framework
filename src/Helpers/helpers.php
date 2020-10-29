<?php

/**
 * View latte templates
 *
 */
if (!function_exists('view')) {
    function view($path, $data = []) {
        return \Smll\View\View::render($path, $data);
    }
}

/**
 * Get Request Object
 *
 */
if (!function_exists('request')) {
    function request() {
        return new \Smll\Http\Request();
    }
}

/**
 * Get Session Object
 *
 */
if (!function_exists('session')) {
    function session() {
        return new \Smll\Session\Session();
    }
}

/**
 * Get Cookie Object
 *
 */
if (!function_exists('cookie')) {
    function cookie() {
        return new \Smll\Cookie\Cookie();
    }
}

