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


function ajax_get_all_by_project() {
    global $pdo;
    $pid = $_POST['pid'];

    $sql = "select 
            r.`name` as room_name,r.slug as room_slug,r.id as room_id,
            f.`name` as floor_name,f.slug as floor_slug,f.id as floor_id,
            b.`name` as building_name,b.slug as building_slug,b.id as building_id,
            l.`name` as location_name,l.slug as location_slug,l.id as location_id,
            j.`name` as project_name,j.slug as project_slug,j.id as project_id
            from sst_projects j
            left join sst_locations l on l.project_id_fk = j.id
            left join sst_buildings b on b.location_id_fk = l.id
            left join sst_floors f on f.building_id_fk = b.id
            left join sst_rooms r on r.floor_id_fk = f.id
            where j.owner_id = 1
            and j.id = $pid 
            order by project_slug, location_slug, building_slug, floor_slug, room_slug";
    $q = $pdo->query($sql);
    $res = $q->fetchAll(PDO::FETCH_OBJ);


    foreach ($res as $row) {
        $p[$row->project_slug] = array(
            "project_name" => $row->project_name,
            "project_slug" => $row->project_slug,
            "project_id" => $row->project_id,
        );
    }
    foreach ($res as $row) {
        if ($row->location_id) {
            $p[$row->project_slug][$row->location_slug] = array(
                "location_name" => $row->location_name,
                "location_slug" => $row->location_slug,
                "location_id" => $row->location_id,
            );
        }
    }
    foreach ($res as $row) {
        if ($row->building_id) {
            $p[$row->project_slug][$row->location_slug][$row->building_slug] = array(
                "building_name" => $row->building_name,
                "building_slug" => $row->building_slug,
                "building_id" => $row->building_id,
            );
        }
    }
    foreach ($res as $row) {
        if ($row->floor_id) {
            $p[$row->project_slug][$row->location_slug][$row->building_slug][$row->floor_slug] = array(
                "floor_name" => $row->floor_name,
                "floor_slug" => $row->floor_slug,
                "floor_id" => $row->floor_id,
            );
        }
    }
    foreach ($res as $row) {
        if ($row->room_id) {
            $p[$row->project_slug][$row->location_slug][$row->building_slug][$row->floor_slug][$row->room_slug] = array(
                "room_name" => $row->room_name,
                "room_slug" => $row->room_slug,
                "room_id" => $row->room_id,
            );
        }
    }

    return(return_json($p));
}


function ajax_get_headings_by_room_id() {
    global $pdo;
    $uid = $_POST['uid'];

    $sql = "select 
            r.`name` as room_name,r.slug as room_slug,r.id as room_id,
            f.`name` as floor_name,f.slug as floor_slug,f.id as floor_id,
            b.`name` as building_name,b.slug as building_slug,b.id as building_id,
            l.`name` as location_name,l.slug as location_slug,l.id as location_id,
            j.`name` as project_name,j.slug as project_slug,j.id as project_id
            from sst_projects j
            left join sst_locations l on l.project_id_fk = j.id
            left join sst_buildings b on b.location_id_fk = l.id
            left join sst_floors f on f.building_id_fk = b.id
            left join sst_rooms r on r.floor_id_fk = f.id
            where j.owner_id = 1
            and r.id = $uid  
            limit 1";
    $q = $pdo->query($sql);
    $res = $q->fetchAll(PDO::FETCH_OBJ);

    return(return_json($res));
}








function ajax_get_project_sidenav() {
    global $pdo;

    $site_uid = $_POST['site_uid'];
    $q = $pdo->query("SELECT project_slug, location FROM survey_sites WHERE site_uid_pk = '$site_uid'");
    $site_res = $q->fetch(PDO::FETCH_OBJ);

    $q = $pdo->query("SELECT * FROM survey_sites WHERE project_slug = '$site_res->project_slug' AND location = '$site_res->location'");
    $res = $q->fetchAll(PDO::FETCH_OBJ);  // id!

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

function get_projects() {
    global $pdo;

    $q = $pdo->query( "select *, s.site_uid_pk as site_uid from 
            survey_projects p left join survey_sites s
            on p.project_slug = s.project_slug
            group by s.project_slug");
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
    unset($data['uid']);
    $data['product_name'] = get_product_name_by_slug($_POST['form_product']);

    $q  = "INSERT INTO sst_products 
            (
            room_id_fk, 
            brand, 
            `type`, 
            product_slug, 
            product_name, 
            sku, 
            custom,
            owner_id, 
            version, 
            created_on)
            VALUES 
            (
            :add_product_room_id, 
            :form_brand, 
            :form_type, 
            :form_product, 
            :product_name, 
            :form_sku, 
            :form_custom, 
            1, 
            1, 
            CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}



function ajax_add_project() {
    global $pdo;

    $data = (object) $_POST;
    $project_slug = slugify($data->form_project_name);


    $sql = "INSERT INTO survey_projects 
                  SET 
                  project_slug = '$project_slug', 
                  project_name = '$data->form_project_name',
                  owner_id = '$data->uid',
                  version = '1',
                  created_on = CURRENT_TIMESTAMP";
    $res = $pdo->exec($sql);
    if ($res) {
        $lastId  = $pdo->query("SELECT LAST_INSERT_ID()")->fetchColumn();
        $sql = "INSERT INTO survey_sites 
                  SET 
                  project_slug = '$project_slug',
                  project_id = $lastId,
                  location = '$data->form_location', 
                  building = '$data->form_building',                
                  owner_id = '$data->uid',
                  version = '1',
                  created_on = CURRENT_TIMESTAMP";
        $res = $pdo->exec($sql);
        if ($res) {
            $lastId  = $pdo->query("SELECT LAST_INSERT_ID()")->fetchColumn();
            $sql = "INSERT INTO survey_tables 
                  SET 
                  site_uid_fk = $lastId,
                  `type` = 'placeholder', 
                  created_on = CURRENT_TIMESTAMP";
            $res = $pdo->exec($sql);
        }
    }
    if ($res) {
        $ret = json_encode(['added' => $pdo->lastInsertId()]);
    } else {
        $ret = json_encode(['failed' => $pdo->lastInsertId()]);
    }
    exit($ret);
}


function ajax_add_special() {
    global $pdo;

    $data = $_POST;
    $data['custom_slug'] = slugify($_POST['form_custom_product_name']);

    $q  = "INSERT INTO sst_products 
            (
            room_id_fk, 
            brand, 
            `type`, 
            product_slug,   
            product_name,           
            sku, 
            custom,
            owner_id, 
            version, 
            created_on)
            VALUES 
            (
            :add_product_room_id, 
            :form_custom_brand, 
            'special', 
            :custom_slug,              
            :form_custom_product_name,
            :form_custom_sku, 
            :form_custom, 
            1, 
            1, 
            CURRENT_TIMESTAMP)";


    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_floor() {
    global $pdo;

    $data['uid'] = $_POST['modal_form_uid'];
    $data['floor'] = $_POST['modal_form_floor'];
    $data['floor_slug'] = slugify($_POST['modal_form_floor']);

    $q  = "INSERT INTO sst_floors 
            ( `building_id_fk`, `name`, `slug`, `owner_id`, `version`, `created_on`)
            VALUES ( :uid, :floor, :floor_slug, 1, 1, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_room() {
    global $pdo;

    $data['uid'] = $_POST['modal_form_uid'];
    $data['room'] = $_POST['modal_form_room'];
    $data['room_slug'] = slugify($_POST['modal_form_room']);

    $q  = "INSERT INTO sst_rooms 
            ( `floor_id_fk`, `name`, `slug`, `owner_id`, `version`, `created_on`)
            VALUES ( :uid, :room, :room_slug, 1, 1, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_building() {
    global $pdo;

    $data['uid'] = $_POST['modal_form_uid'];
    $data['building'] = $_POST['modal_form_building'];
    $data['building_slug'] = slugify($_POST['modal_form_building']);

    $q  = "INSERT INTO sst_buildings 
            ( `location_id_fk`, `name`, `slug`, `owner_id`, `version`, `created_on`)
            VALUES ( :uid, :building, :building_slug, 1, 1, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_location() {
    global $pdo;

    $data['uid'] = $_POST['modal_form_uid'];
    $data['location'] = $_POST['modal_form_location'];
    $data['location_slug'] = slugify($_POST['modal_form_location']);

    $q  = "INSERT INTO sst_locations 
            ( `project_id_fk`, `name`, `slug`, `owner_id`, `version`, `created_on`)
            VALUES ( :uid, :location, :location_slug, 1, 1, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}


function ajax_remove_location() {
    global $pdo;

    $uid = $_POST['modal_form_uid'];

    $buildings = false;
    $floors = false;
    $rooms = false;

    // get buildings in this location
    $q = $pdo->query("SELECT id FROM sst_buildings WHERE location_id_fk = $uid");
    $buildings = $q->fetchAll(PDO::FETCH_COLUMN, 0);

    // get the floors in the building
    if ($buildings) {
        $buildingsStr = implode(',', $buildings);
        $q = $pdo->query("SELECT id FROM sst_floors WHERE building_id_fk IN ($buildingsStr)");
        $floors = $q->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    if ($floors) {
        $floorsStr = implode(',',$floors);
        // get the rooms in all of these floors
        $q = $pdo->query("SELECT id FROM sst_rooms WHERE floor_id_fk IN ($floorsStr)");
        $rooms = $q->fetchAll(PDO::FETCH_COLUMN, 0);
        $roomsStr = implode(',', $rooms);
    }

    if ($uid) {

        $pdo->prepare("DELETE FROM sst_locations WHERE id = ?")->execute([$uid]);
        $pdo->prepare("DELETE FROM sst_buildings WHERE location_id_fk = ?")->execute([$uid]);
        if ($buildings) {
            $pdo->prepare("DELETE FROM sst_floors WHERE building_id_fk IN (?)")->execute([$buildingsStr]);
        }
        if ($floors) {
            $pdo->prepare("DELETE FROM sst_rooms WHERE floor_id_fk IN (?)")->execute($floorsStr);
        }
        if ($rooms) {
            $pdo->prepare("DELETE FROM sst_products WHERE room_id_fk IN (?)")->execute([$roomsStr]);
        }
        $ret = json_encode(['deleted' => 1]);
    } else {
        $ret = json_encode(['deleted' => 0]);
    }
    exit($ret);
}


function ajax_remove_building() {
    global $pdo;

    $uid = $_POST['modal_form_uid'];

    $floors = false;
    $rooms = false;

    // get the floors in the building
    $q = $pdo->query("SELECT id FROM sst_floors WHERE building_id_fk = $uid");
    $floors = $q->fetchAll(PDO::FETCH_COLUMN, 0);

    if (count($floors) > 0) {
        $floorsStr = implode(',',$floors);
        // get the rooms in all of these floors
        $q = $pdo->query("SELECT id FROM sst_rooms WHERE floor_id_fk IN ($floorsStr)");
        $rooms = $q->fetchAll(PDO::FETCH_COLUMN, 0);
        $roomsStr = implode(',', $rooms);
    }

    if ($uid) {
        $pdo->prepare("DELETE FROM sst_buildings WHERE id = ?")->execute([$uid]);
        $pdo->prepare("DELETE FROM sst_floors WHERE building_id_fk = ?")->execute([$uid]);
        if ($floors) {
            $pdo->prepare("DELETE FROM sst_rooms WHERE floor_id_fk IN (?)")->execute($floorsStr);
        }
        if ($rooms) {
            $pdo->prepare("DELETE FROM sst_products WHERE room_id_fk IN (?)")->execute([$roomsStr]);
        }
        $ret = json_encode(['deleted' => 1]);
    } else {
        $ret = json_encode(['deleted' => 0]);
    }
    exit($ret);
}


function ajax_remove_floor() {
    global $pdo;

    $uid = $_POST['modal_form_uid'];

    // get the rooms in the floor
    $q = $pdo->query("SELECT id FROM sst_rooms WHERE floor_id_fk = $uid");
    $rooms = $q->fetchAll(PDO::FETCH_COLUMN, 0);
    $roomsStr = implode(',',$rooms);

    if ($uid) {

        $pdo->prepare("DELETE FROM sst_floors WHERE id = ?")->execute([$uid]);
        $pdo->prepare("DELETE FROM sst_rooms WHERE floor_id_fk = ?")->execute([$uid]);
        $pdo->prepare("DELETE FROM sst_products WHERE room_id_fk IN (?)")->execute([$roomsStr]);
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
        $pdo->prepare("DELETE FROM sst_rooms WHERE id=?")->execute([$uid]);
        $pdo->prepare("DELETE FROM sst_products WHERE room_id_fk=?")->execute([$uid]);
        $ret = json_encode(['deleted' => 1]);
    } else {
        $ret = json_encode(['deleted' => 0]);
    }
    exit($ret);
}

function ajax_get_products_in_room() {
    global $pdo;
    $room_id = $_POST['room_id'];

    $q = $pdo->query("SELECT *, count(sku) as qty FROM
                    sst_products p                      
                    WHERE room_id_fk = $room_id
                    AND owner_id = 1 
                    GROUP BY sku");

    $res = $q->fetchAll();
    if (count($res) < 1) {
        $res[0] = array();
    }

    return(return_json($res));
}


function ajax_get_ptabledata() {
    global $pdo;
    $uid = $_POST['uid'];

        $q = $pdo->query("SELECT
                        s.room AS room_name,
                        s.floor AS floor_name,
                        s.location AS location_name,
                        s.building AS building_name,
                        p.project_name,
                        COUNT( t.sku ) AS qty,
                        t.id,
                        t.site_uid_fk,
                        t.sku,
                        t.ref,
                        t.product_slug,
                        t.product_name,
                        t.`position`,
                        s.project_id as pid
                    FROM
                        survey_tables t	
                        INNER JOIN survey_sites s ON t.site_uid_fk = s.site_uid_pk
                        INNER JOIN survey_projects p ON p.id = s.project_id 	
                    WHERE
                        site_uid_fk = $uid
                    GROUP BY  sku");

    $res = $q->fetchAll();
    if (count($res) < 1) {
        $res[0] = array();
    }

    //vd($res, 1);


    return(return_json($res));
}

function ajax_get_dashtabledata() {
    global $pdo;
    $uid = $_POST['uid'];

    $q = $pdo->query("select 
            j.`name` as project_name,
            j.slug as project_slug,
            j.version as version,
            j.id as project_id,
            DATE_FORMAT(j.created_on, '%d/%c/%y') as created,
            count(d.sku) as products 
            from sst_projects j
            left join sst_locations l on l.project_id_fk = j.id
            left join sst_buildings b on b.location_id_fk = l.id
            left join sst_floors f on f.building_id_fk = b.id
            left join sst_rooms r on r.floor_id_fk = f.id
            left join sst_products d on d.room_id_fk = r.id
            where j.owner_id = $uid
            group by j.id
            order by project_slug");

    $res = $q->fetchAll();
    if (count($res) < 1) {
        $res[0] = array();
    }

    return(return_json($res));
}

function ajax_get_locations_for_project() {
    global $pdo;

    $project_name = $_POST['project_name'];
    $slug = slugify($project_name);

    $q = $pdo->query("SELECT location FROM survey_sites 
                              WHERE project_slug = '$slug'
                              GROUP BY location");
    $res = $q->fetchAll(PDO::FETCH_OBJ);

    if (count($res)) {
        $html = "";
        foreach ($res as $l) {
            $html .= "<option value='$l->location'></option>";
        }
    }
    exit($html);


}


function ajax_increase_qty() {
    global $pdo;

    $id = $_POST['id'];
    $q = $pdo->query("SELECT room_id_fk, brand, `type`, `range`, product_slug, product_name, sku, custom, ref, `order`, owner_id, version  FROM sst_products WHERE id = $id");
    $data = $q->fetch(PDO::FETCH_ASSOC);

    $q  = "INSERT INTO sst_products 
            (room_id_fk, brand, `type`, `range`, product_slug, product_name, sku, custom, ref, `order`, owner_id, version, created_on)
            VALUES 
            (:room_id_fk, :brand, :type, :range, :product_slug, :product_name, :sku, :custom, :ref, :order, :owner_id, :version, CURRENT_TIMESTAMP)";

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
    $room_id = $_POST['uid'];

    $q = $pdo->query("SELECT id FROM sst_products
                              WHERE sku = '$sku'
                              AND room_id_fk = $room_id
                              ORDER BY created_on DESC
                              LIMIT 1");
    $delete_id = $q->fetch(PDO::FETCH_COLUMN, 0);
    if ($delete_id) {
        $pdo->prepare("DELETE FROM sst_products WHERE id=?")->execute([$delete_id]);
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

    $sql = "UPDATE sst_products SET ref=:ref WHERE sku=:sku AND room_id_fk=:uid";
    $pdo->prepare($sql)->execute($data);
}

?>
