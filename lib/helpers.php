<?php

function vd($v, $exit = false) {
    echo '<pre>';
        print_r($v);
    echo '</pre>';
    if ($exit) exit();
}

function get_uri_part($part = 1) {
    $parts = parse_url($_SERVER['REQUEST_URI']);
    $path = explode('/', $parts['path']);
    if (isset($path[$part])) {
        return ($path[$part]);
    } else {
        return (false);
    }
}

function slugify($str) {
    $str = trim($str);
    $str = strtolower(str_replace(' ', '-', $str));
    return($str);
}

?>
