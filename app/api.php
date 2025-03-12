<?php
header("Access-Control-Allow-Origin: *");  // Allow all origins (for testing)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

    require('config/database.php');
    require('lib/model.php');
    require('lib/helpers.php');

    $method = get_uri_part(2);
    //$method = $_POST['method'];

    //vd($method,1);

    call_user_func('ajax_'.$method);
?>
