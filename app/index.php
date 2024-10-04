<!DOCTYPE html>

<?php require('config/database.php'); ?>
<?php require('lib/model.php');


?>
<?php require('lib/helpers.php'); ?>
<?php require('config/routes.php'); ?>


<html lang="en">
    <head>
        <title></title>
        <?php require('partials/head.php'); ?>

        <script id="page-title" type="text/x-handlebars-template">
            <title>{{pageTitle}}</title>
        </script>

    </head>
    <body>
        <div id="container">
        <?php
        require('partials/header.php'); ?>

        <?php

            get_view();

        ?>
        </div>
    </body>
</html>
