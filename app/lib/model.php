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

function ajax_login() {
    global $pdo;
    $data = $_POST;
    $email = $data['modal_form_email'];
    $password = $data['modal_form_password'];
    $sql = "SELECT * FROM sst_users 
            WHERE email = '$email' AND password = '$password'
            AND active = 1 LIMIT 1";
    $q = $pdo->query($sql);
    $res = $q->fetch(PDO::FETCH_ASSOC);

    if (isset($res['id'])) {
        $_COOKIE['user_id'] = $res['id'];
        $_COOKIE['user_name'] = $res['name'];
        return_json($res);
    } else {
        $res = array('id' => false, 'error' => "invalid login");
        return_json($res);
    }
}
function user_id() {
    return($_COOKIE['user_id']);
}


function ajax_get_all_by_project() {
    global $pdo;
    $pid = $_POST['pid'];
    $user_id = user_id();

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
            where j.owner_id = $user_id
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
    $user_id = user_id();

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
            where j.owner_id = $user_id
            and r.id = $uid  
            limit 1";
    $q = $pdo->query($sql);
    $res = $q->fetchAll(PDO::FETCH_OBJ);

    return(return_json($res));
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

function get_products() {
    global $pdo;
    $q = $pdo->query("SELECT
    product_name, product_slug_pk, description, site
    FROM p__products WHERE status = 1 AND obsolete = 0 AND hidden = 0 AND uk = 1
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

function ajax_get_types() {
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

function ajax_get_products_for_type() {
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

function ajax_get_skus_for_product() {
    global $pdo;
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
    $data['user_id'] = user_id();
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
            :user_id, 
            1, 
            CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);
    exit($ret);
}



function ajax_add_project() {
    global $pdo;

    $data = (object) $_POST;
    $data->user_id = user_id();
    $project_slug = slugify($data->form_project_name);


    $sql = "INSERT INTO sst_projects 
                  SET 
                  slug = '$project_slug', 
                  `name` = '$data->form_project_name',
                  owner_id = '$data->user_id',
                  version = '1',
                  created_on = CURRENT_TIMESTAMP";
    $res = $pdo->exec($sql);
    if ($res) {
        $lastId  = $pdo->query("SELECT LAST_INSERT_ID()")->fetchColumn();
        $location_slug = slugify($data->form_location);
        $sql = "INSERT INTO sst_locations 
                  SET                   
                  project_id_fk = $lastId,
                  slug = '$location_slug',
                  `name` = '$data->form_location',                                   
                  owner_id = '$data->user_id',
                  version = '1',
                  created_on = CURRENT_TIMESTAMP";
        $res = $pdo->exec($sql);
        if ($res) {
            $lastId  = $pdo->query("SELECT LAST_INSERT_ID()")->fetchColumn();
            $building_slug = slugify($data->form_building);
            $sql = "INSERT INTO sst_buildings  
                  SET                   
                  location_id_fk = $lastId,
                  slug = '$building_slug',
                  `name` = '$data->form_building',                                   
                  owner_id = '$data->user_id',
                  version = '1',
                  created_on = CURRENT_TIMESTAMP";
            $res = $pdo->exec($sql);
        }
        if ($res) {
            $lastId  = $pdo->query("SELECT LAST_INSERT_ID()")->fetchColumn();
            $floor_slug = slugify($data->form_floor);
            $sql = "INSERT INTO sst_floors  
                  SET                   
                  building_id_fk = $lastId,
                  slug = '$floor_slug',
                  `name` = '$data->form_floor',                                   
                  owner_id = '$data->user_id',
                  version = '1',
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
    $data['user_id'] = user_id();
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
            :user_id, 
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
    $data['user_id'] = user_id();

    $q  = "INSERT INTO sst_floors 
            ( `building_id_fk`, `name`, `slug`, `owner_id`, `version`, `created_on`)
            VALUES ( :uid, :floor, :floor_slug, :user_id, 1, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_room() {
    global $pdo;

    $data['uid'] = $_POST['modal_form_uid'];
    $data['room'] = $_POST['modal_form_room'];
    $data['room_slug'] = slugify($_POST['modal_form_room']);
    $data['user_id'] = user_id();

    $q  = "INSERT INTO sst_rooms 
            ( `floor_id_fk`, `name`, `slug`, `owner_id`, `version`, `created_on`)
            VALUES ( :uid, :room, :room_slug, :user_id, 1, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_building() {
    global $pdo;

    $data['uid'] = $_POST['modal_form_uid'];
    $data['building'] = $_POST['modal_form_building'];
    $data['building_slug'] = slugify($_POST['modal_form_building']);
    $data['user_id'] = user_id();

    $q  = "INSERT INTO sst_buildings 
            ( `location_id_fk`, `name`, `slug`, `owner_id`, `version`, `created_on`)
            VALUES ( :uid, :building, :building_slug, :user_id, 1, CURRENT_TIMESTAMP)";

    $pdo->prepare($q)->execute($data);
    $ret = json_encode(['added' => $pdo->lastInsertId()]);

    exit($ret);
}

function ajax_add_location() {
    global $pdo;

    $data['uid'] = $_POST['modal_form_uid'];
    $data['location'] = $_POST['modal_form_location'];
    $data['location_slug'] = slugify($_POST['modal_form_location']);
    $data['user_id'] = user_id();

    $q  = "INSERT INTO sst_locations 
            ( `project_id_fk`, `name`, `slug`, `owner_id`, `version`, `created_on`)
            VALUES ( :uid, :location, :location_slug, :user_id, 1, CURRENT_TIMESTAMP)";

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
    $user_id = user_id();

    $q = $pdo->query("SELECT *, count(sku) as qty FROM
                    sst_products p                      
                    WHERE room_id_fk = $room_id
                    AND owner_id = $user_id 
                    GROUP BY sku");

    $res = $q->fetchAll();
    if (count($res) < 1) {
        $res[0] = array();
    }

    return(return_json($res));
}


function ajax_get_dashtabledata() {
    global $pdo;
    $uid = $_POST['uid'];
    $user_id = user_id();

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
            where j.owner_id = $user_id
            group by j.id
            order by project_slug");

    $res = $q->fetchAll();
    if (count($res) < 1) {
        $res[0] = array();
    }

    return(return_json($res));
}


function ajax_set_qty() {
    global $pdo;

    $id = $_POST['set_qty_product_id'];
    $qty = $_POST['set_qty_qty'];

    $q = $pdo->query("SELECT room_id_fk, brand, `type`, `range`, product_slug, product_name, sku, custom, ref, `order`, owner_id, version  FROM sst_products WHERE id = $id");
    $data = $q->fetch(PDO::FETCH_ASSOC);

    // remove them first
    $del = array();
    $del['room_id'] = $data['room_id_fk'];
    $del['sku'] = $data['sku'];
    $q = "DELETE FROM sst_products WHERE room_id_fk = :room_id AND sku = :sku";
    try {
        $pdo->prepare($q)->execute($del);
    }  catch (Exception $e) {
        exit('del: '.$e->getMessage());
    }
    if ($qty > 0) {
        // and insert the qty
        for ($i = 0; $i < $qty; $i++) {
            $q = "INSERT INTO sst_products 
            (room_id_fk, brand, `type`, `range`, product_slug, product_name, sku, custom, ref, `order`, owner_id, version, created_on)
            VALUES 
            (:room_id_fk, :brand, :type, :range, :product_slug, :product_name, :sku, :custom, :ref, :order, :owner_id, :version, CURRENT_TIMESTAMP)";

            try {
                $pdo->prepare($q)->execute($data);
            } catch (Exception $e) {
                exit('ins' . $e->getMessage());
            }
        }
    }
    $ret = json_encode(['added' => $pdo->lastInsertId()]);
    exit($ret);
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

function ajax_get_notes() {
    global $pdo;

    $room_id = $_POST['room_id'];

    $q = $pdo->query("SELECT 
                              id, 
                              room_id_fk as room_id,
                              note
                              FROM sst_notes
                              WHERE room_id_fk = $room_id  
                              ORDER BY created_on DESC");
    $res = $q->fetchAll(PDO::FETCH_ASSOC);
    return(return_json($res));
}

function ajax_remove_note() {
    global $pdo;

    $uid = $_POST['id'];

    if ($uid) {
        $pdo->prepare("DELETE FROM sst_notes WHERE id=?")->execute([$uid]);
        $ret = json_encode(['deleted' => 1]);
    } else {
        $ret = json_encode(['deleted' => 0]);
    }
    exit($ret);
}

function ajax_save_note() {
    global $pdo;

    $data['note_id'] = $_POST['id'];
    $data['room_id'] = $_POST['room_id'];
    $data['note'] = $_POST['note'];
    $data['user_id'] = user_id();

    if ($data['note_id'] == 0) {
        unset($data['note_id']);
        $q = "INSERT INTO sst_notes (room_id_fk, note, owner_id, version, created_on)
              VALUES
              (:room_id, :note, :user_id, 1, CURRENT_TIMESTAMP)";
    } else {
        $q = "UPDATE sst_notes SET 
              room_id_fk = :room_id, 
              note = :note, 
              owner_id = :user_id,
              version = 1              
              WHERE
              id = :note_id";
    }

    try {
        $pdo->prepare($q)->execute($data);
    }  catch (Exception $e) {
        vd($q);
        exit($e->getMessage());
    }
    $ret = json_encode(['added' => $pdo->lastInsertId()]);
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
