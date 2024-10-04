<?php

function default_ands() {
    return (" AND status  = 1 AND hidden = 0 AND obsolete = 0 AND uk = 1 ");
}

function return_json($res) {
    if (count($res)) {
        $json = json_encode($res);
        echo($json);
        exit();
    } else {
        return false;
    }
}

// Get the types fpr Tamlite
// just to pre-populate the products dropdown before a brand is selected.
function get_types()
{
    global $pdo;
    $q = $pdo->query("SELECT type_slug_pk, type_name
    FROM p__products_types
    WHERE site = 1
    ORDER BY type_name ASC");
    $res = $q->fetchAll(PDO::FETCH_OBJ);
    return ((count($res)) ? $res : false);
}


function get_products()
{
    global $pdo;
    $q = $pdo->query("SELECT
    product_name, product_slug_pk, description, site
    FROM p__products");

    $res = $q->fetchAll(PDO::FETCH_OBJ);
    return ((count($res)) ? $res : false);
}


function ajax_get_types()
{
    global $pdo;

    $brand = $_POST['brand'];

    $q = $pdo->query("SELECT type_slug_pk, type_name
    FROM p__products_types
    WHERE site = $brand
    ORDER BY type_name ASC");

    $res = $q->fetchAll(PDO::FETCH_OBJ);

    return(return_json($res));
}

function ajax_get_products_for_type()
{
    global $pdo;
    $ands = default_ands();


    $slug = $_POST['type_slug'];

    $q = $pdo->query("SELECT
                        UPPER(
                            REPLACE(REPLACE(product_slug_pk, '-', ' '), 'xcite ', '')
                            )
                        as hrslug,
                        product_slug_pk, product_name
                        FROM p__products
                        WHERE type_slug_fk = '$slug'
                        $ands
                        ORDER BY product_name ASC");

    $res = $q->fetchAll();

    return(return_json($res));
}

function ajax_get_skus_for_product()
{
    global $pdo;
    //$ands = default_ands();

    $slug = $_POST['product_slug'];

    $q = $pdo->query("SELECT
                        product_code_pk
                        FROM p__variants
                        WHERE product_slug_fk = '$slug'
                        ORDER BY product_code_pk ASC");

    $res = $q->fetchAll(PDO::FETCH_COLUMN, 0);

    return(return_json($res));
}

?>
