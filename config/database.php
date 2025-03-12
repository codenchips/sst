<?php

    $host = $_SERVER['HTTP_HOST'];

    $servername = "localhost";

    if (stristr($host, '.test')) {
        $username = "dev";
        $password = "";
        $database = 'tamlite_local';

    } else {
        $username = 'tamli342_admin';
        $password = 'zd5w4tstibk8';
        $database = 'tamli342_tamlite_test';
    }

    $dsn = "mysql:host=$servername;dbname=$database;charset=UTF8";

    try {
        $pdo = new PDO($dsn, $username, $password);

        if ($pdo) {
            //echo "Connected to the $database database successfully!";
            $connected = true;
            $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        }
    } catch (PDOException $e) {
        $connected = false;
        echo $e->getMessage();
    }

?>

