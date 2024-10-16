<?php
    $servername = "localhost";
    $username = "dev";
    $password = "";
    $database = 'tamlite_local';

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
