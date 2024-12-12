<?php
function get_view() {

    //$path = trim($_SERVER['REQUEST_URI'], '/');

    $path = get_uri_part();

    switch ($path) {
        case '' :
        case 'home' :
            require('views/dashboard.php');
            break;
         case 'tables' :
            require('views/tables.php');
            break;
         case 'folio' :
            require('views/schedule.php');
            break;
        case 'account' :
            require('views/account.php');
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
