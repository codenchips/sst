<?php

function vd($v, $exit = false) {
    echo '<pre>';
        var_dump($v);
    echo '</pre>';
    if ($exit) exit();
}

function get_uri_part($part = 1) {
    $parts = parse_url($_SERVER['REQUEST_URI']);
    $path = explode('/', $parts['path']);
    return($path[$part]);
}


?>
