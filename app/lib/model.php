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

function ajax_get_project_sidenav() {
    global $pdo;

    $project_slug = $_POST['project_slug'];

    $q = $pdo->query("SELECT * FROM survey_sites WHERE project_slug = '$project_slug'");
    $res = $q->fetchAll(PDO::FETCH_OBJ);

    $nav = array();
    $nav['project_slug'] = $res[0]->project_slug;

    foreach ($res as $key => $val) {
        $location = slugify($val->location);
        $nav['locations'][$location]['name'] = $val->location;
        $nav['locations'][$location]['slug'] = $location;
        if ($val->building) {
            $nav['locations'][$location][slugify($val->building)] = array(
                'slug' => slugify($val->building),
                'name' => $val->building,
                'uid' => $val->site_uid_pk
            );
        }
    }

    foreach ($res as $key => $val) {
        $location = slugify($val->location);
        $building = slugify($val->building);
        $floor = slugify($val->floor);
        if ($floor) {
            $nav['locations'][$location][$building][$floor] = array(
                'slug' => $floor,
                'name' => $val->floor,
                'uid' => $val->site_uid_pk
            );
        }
    }

    foreach ($res as $key => $val) {
        $location = slugify($val->location);
        $building = slugify($val->building);
        $floor = slugify($val->floor);
        $room = slugify($val->room);
        if ($room) {
            $nav['locations'][$location][$building][$floor][$room] = array(
                'slug' => $room,
                'name' => $val->room,
                'uid' => $val->site_uid_pk
            );
        }
    }

    //vd($nav,1);

    return(return_json($nav));
}

function get_location_for_project($project_slug) {
    global $pdo;
    $q = $pdo->query("SELECT location FROM survey_sites WHERE project_slug = '$project_slug'");
    return ($q->fetch(PDO::FETCH_COLUMN, 0));
}
function get_buildings_for_location($project_slug, $location) {
    global $pdo;
    $q = $pdo->query("SELECT building 
                              FROM survey_sites 
                              WHERE location = '$location' && project_slug = '$project_slug' 
                              GROUP BY building");
    return ($q->fetchAll(PDO::FETCH_COLUMN, 0));
}
function get_floors_for_building($project_slug, $location, $building) {
    global $pdo;
    $q = $pdo->query("SELECT floor 
                              FROM survey_sites 
                              WHERE building = '$building' && 
                                    location = '$location' && 
                                    project_slug = '$project_slug'                               
                              GROUP BY floor 
                              ORDER BY created_on ASC ");
    return ($q->fetchAll(PDO::FETCH_COLUMN, 0));
}
function get_rooms_for_floor($project_slug, $location, $building, $floor) {
    global $pdo;
    $q = $pdo->query("SELECT room 
                              FROM survey_sites 
                              WHERE building = '$building' && 
                                    location = '$location' && 
                                    project_slug = '$project_slug' &&
                                    floor = '$floor' 
                              GROUP BY floor");
    return ($q->fetchAll(PDO::FETCH_COLUMN, 0));
}


// Get the types fpr Tamlite
// just to pre-populate the products dropdown before a brand is selected.
function get_types() {
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
            (site_uid_fk, brand, `type`, product_slug, product_name, sku, custom, created_on)
            VALUES
            (:uid, :form_brand, :form_type, :form_product, :product_name, :form_sku, :form_custom, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_special() {
    global $pdo;

    $data = $_POST;

    $q  = "INSERT INTO survey_tables
            (site_uid_fk, brand, `type`, product_name, sku, custom, created_on)
            VALUES
            (:uid, :form_custom_brand, 'special', :form_custom_product_name, :form_custom_sku, :form_custom, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_floor() {
    global $pdo;

        $uid = $_POST['modal_form_uid'];
        $floor = $_POST['modal_form_floor'];

        $q = $pdo->query("SELECT `project_slug`, `location`, `building`
                                    FROM survey_sites WHERE site_uid_pk = $uid LIMIT 1");
        $data = $q->fetch(PDO::FETCH_ASSOC);

        $q  = "INSERT INTO survey_sites
            ( `project_slug`, `location`, `building`, `floor`, `created_on`)
            VALUES ( :project_slug, :location, :building, '$floor', CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_room() {
    global $pdo;

    $uid = $_POST['modal_form_uid'];
    $room = $_POST['modal_form_room'];

    $q = $pdo->query("SELECT `project_slug`, `location`, `building`, `floor`
                                    FROM survey_sites WHERE site_uid_pk = '$uid' LIMIT 1");
    $data = $q->fetch(PDO::FETCH_ASSOC);

    $q  = "INSERT INTO survey_sites
            ( `project_slug`, `location`, `building`, `floor`, `room`, `created_on`)
            VALUES ( :project_slug, :location, :building, :floor, '$room', CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}


function ajax_remove_floor() {
    global $pdo;

    $uid = $_POST['modal_form_uid'];

    $q = $pdo->query("SELECT `project_slug`, `location`, `building`, `floor`
                                    FROM survey_sites WHERE site_uid_pk = $uid LIMIT 1");
    $data = $q->fetch(PDO::FETCH_OBJ);

    // get all uids from sites to be deleted from both sites and tables
    // that match this project, location and building
    $q = $pdo->query("SELECT `site_uid_pk` FROM survey_sites WHERE 
                      `project_slug` = '$data->project_slug' AND 
                      `location` = '$data->location' AND
                      `building` = '$data->building' AND 
                      `floor` = '$data->floor'");
    $data = $q->fetchAll(PDO::FETCH_ASSOC);
    $idsStr = "(";
    foreach ($data as $row) {
        $idsStr .= $row['site_uid_pk'].',';
    }
    $idsStr = trim($idsStr, ',');
    $idsStr .= ")";

    if ($uid) {
        $pdo->query("DELETE FROM survey_tables WHERE site_uid_fk IN $idsStr")->execute();
        $pdo->query("DELETE FROM survey_sites WHERE site_uid_pk IN $idsStr")->execute();
        $ret = json_encode(['deleted' => 1]);
    } else {
        $ret = json_encode(['deleted' => 0]);
    }
    exit($ret);
}

function ajax_remove_room() {
    global $pdo;

    $uid = $_POST['modal_form_uid'];

    if ($uid) {
        $pdo->prepare("DELETE FROM survey_tables WHERE site_uid_fk=?")->execute([$uid]);
        $pdo->prepare("DELETE FROM survey_sites WHERE site_uid_pk=?")->execute([$uid]);
        $ret = json_encode(['deleted' => 1]);
    } else {
        $ret = json_encode(['deleted' => 0]);
    }
    exit($ret);
}


function ajax_get_ptabledata() {
    global $pdo;
    $uid = $_POST['uid'];

    $q = $pdo->query("SELECT 
                        s.room as room_name,
                        s.floor as floor_name,
                        COUNT(t.sku) as qty,
                        t.id, t.site_uid_fk, t.sku, t.ref, t.product_slug, t.product_name, t.`position`    
                        FROM survey_tables t
                        LEFT JOIN survey_sites s 
                          ON  t.site_uid_fk = s.site_uid_pk 
                        WHERE site_uid_fk = $uid 
                        GROUP BY sku");

    $res = $q->fetchAll();
    if (count($res) < 1) {
        $res[0] = array();
    }

    //vd($res, 1);


    return(return_json($res));
}

function ajax_increase_qty() {
    global $pdo;

    $id = $_POST['id'];
    $q = $pdo->query("SELECT `site_uid_fk`, `brand`, `type`, `range`, product_slug, product_name, sku, ref, custom, building, floor FROM survey_tables WHERE id = $id");
    $data = $q->fetch(PDO::FETCH_ASSOC);

    $q  = "INSERT INTO survey_tables
            (site_uid_fk, brand, `type`, `range`, product_slug, product_name, sku, ref, custom, building, floor, created_On)
            VALUES (:site_uid_fk, :brand, :type, :range, :product_slug, :product_name, :sku, :ref, :custom, :building, :floor, CURRENT_TIMESTAMP)";

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
    $uid = $_POST['uid'];

    $q = $pdo->query("SELECT id FROM survey_tables 
                              WHERE sku = '$sku'  
                              AND site_uid_fk = $uid 
                              ORDER BY created_on DESC 
                              LIMIT 1");
    $delete_id = $q->fetch(PDO::FETCH_COLUMN, 0);
    if ($delete_id) {
        $pdo->prepare("DELETE FROM survey_tables WHERE id=?")->execute([$delete_id]);
        $ret = json_encode(['decreased' => 1]);
    } else {
        $ret = json_encode(['decreased' => 0]);
    }
    exit($ret);
}

function ajax_edit_ref() {
    global $pdo;

    $data = [
        'sku' => $_POST['sku'],
        'ref' => $_POST['ref'],
        'uid' => $_POST['uid']
    ];

    $sql = "UPDATE survey_tables SET ref=:ref WHERE sku=:sku AND site_uid_fk=:uid";
    $pdo->prepare($sql)->execute($data);
}

?>
