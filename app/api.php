<?php
    require('config/database.php');
    require('lib/model.php');
    require('lib/helpers.php');

    $method = get_uri_part(2);

    call_user_func('ajax_'.$method);
?>