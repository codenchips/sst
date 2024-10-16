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
    AND type_slug_pk NOT IN ('addons', 'accessories')     
    ORDER BY type_name ASC");
    $res = $q->fetchAll(PDO::FETCH_OBJ);
    return ((count($res)) ? $res : false);
}


function get_products()
{
    global $pdo;
    $q = $pdo->query("SELECT
    product_name, product_slug_pk, description, site
    FROM p__products
    ORDER BY type_name ASC");

    $res = $q->fetchAll(PDO::FETCH_OBJ);
    return ((count($res)) ? $res : false);
}

function get_product_name_by_slug($slug) {
    global $pdo;
    $q = $pdo->query("SELECT
                        product_name
                        FROM p__products
                        WHERE product_slug_pk = '$slug'
                        LIMIT 1");
    return($q->fetchAll(PDO::FETCH_COLUMN, 0)[0]);
}


function ajax_get_types()
{
    global $pdo;

    $brand = $_POST['brand'];

    $q = $pdo->query("SELECT type_slug_pk, type_name
    FROM p__products_types
    WHERE site = $brand
    AND type_slug_pk NOT IN ('addons', 'accessories') 
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

function ajax_add_product() {
    global $pdo;

    $data = $_POST;
    $data['product_name'] = get_product_name_by_slug($_POST['form_product']);

    $q  = "INSERT INTO survey_tables
            (brand, `type`, product_slug, product_name, sku, custom, created_on)
            VALUES
            (:form_brand, :form_type, :form_product, :product_name, :form_sku, :form_custom, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_special() {
    global $pdo;

    $data = $_POST;

    $q  = "INSERT INTO survey_tables
            (brand, `type`, product_name, sku, custom, created_on)
            VALUES
            (:form_custom_brand, 'special', :form_custom_product_name, :form_custom_sku, :form_custom, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}


function ajax_get_ptabledata() {
    global $pdo;

    $q = $pdo->query("SELECT COUNT(sku) as qty,
                        id, sku, product_slug, product_name, `position`    
                        FROM survey_tables
                        GROUP BY sku");

    $res = $q->fetchAll();

    return(return_json($res));
}

function ajax_increase_qty() {
    global $pdo;

    $id = $_POST['id'];
    $q = $pdo->query("SELECT `brand`, `type`, `range`, product_slug, product_name, sku, custom, building, floor FROM survey_tables WHERE id = $id");
    $data = $q->fetch(PDO::FETCH_ASSOC);

    $q  = "INSERT INTO survey_tables
            (brand, `type`, `range`, product_slug, product_name, sku, custom, building, floor, created_On)
            VALUES (:brand, :type, :range, :product_slug, :product_name, :sku, :custom, :building, :floor, CURRENT_TIMESTAMP)";

    try {
        $pdo->prepare($q)->execute($data);
    }  catch (Exception $e) {
        exit($e->getMessage());
    }
    $ret = json_encode(['added' => $pdo->lastInsertId()]);
    exit($ret);
}

function ajax_decrease_qty() {
    global $pdo;

    $sku = $_POST['sku'];
    $q = $pdo->query("SELECT id FROM survey_tables WHERE sku = '$sku' ORDER BY created_on DESC LIMIT 1");
    $delete_id = $q->fetch(PDO::FETCH_COLUMN, 0);
    if ($delete_id) {
        $pdo->prepare("DELETE FROM survey_tables WHERE id=?")->execute([$delete_id]);
        $ret = json_encode(['decreased' => 1]);
    } else {
        $ret = json_encode(['decreased' => 0]);
    }
    exit($ret);
}

?>
