<?php
function get_view() {

    //$path = trim($_SERVER['REQUEST_URI'], '/');

    $path = get_uri_part();

    switch ($path) {
        case '' :
        case 'home' :
            require('views/home.php');
            break;
         case 'tables' :
            require('views/tables.php');
            break;
         case 'about' :
            require('views/about.php');
            break;
         case 'api' :
            require('lib/api.php');
            break;

        default :
            require('views/404.php');
            break;
    }
}

?>
