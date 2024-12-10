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

function get_project($project_id) {
    global $pdo;
    $q = $pdo->query("SELECT
    p.id, 
    p.name, 
    p.slug, 
    p.version as project_version, 
    p.project_id, 
    p.engineer,
    u.name as username, 
    u.email     
    FROM sst_projects p LEFT JOIN sst_users u on p.owner_id = u.id 
    WHERE p.id = $project_id 
    
    ORDER BY p.name ASC, p.version DESC
    LIMIT 1");

    $res = $q->fetchAll(PDO::FETCH_OBJ);
    return ((count($res)) ? $res[0] : false);
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

function ajax_get_floors_by_project() {
    global $pdo;

    $uid = $_POST['project_id'];

    $q = $pdo->query("select f.id as floor_id, f.name as floor_name 
            from sst_floors f 
            left join sst_buildings b on b.id = f.building_id_fk
            left join sst_locations l on l.id = b.location_id_fk
            left join sst_projects p on p.id = l.project_id_fk
            where p.id = $uid");
    $floors = $q->fetchAll(PDO::FETCH_OBJ);
    return(return_json($floors));
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



function ajax_get_schedule_per_room() {
    global $pdo;
    $project_id = $_POST['project_id'];
    if (!$project_id) {
        return (return_json(array('error' => 'No Project ID')));
    }
    $q = $pdo->query("SELECT
                r.`slug` AS room_slug,
                r.`name` AS room_name, 
                f.`name` AS floor_name, 
                b.`name` AS building_name, 
                l.`name` AS location_name, 
                p.`name` AS project_name,
                d.ref, 			
                d.product_name,
                d.product_slug,					
                d.sku,
                d.custom,
                d.owner_id,
                p.id AS project_id_fk,
                p.slug AS project_slug,
                p.version AS project_version,
                COUNT(d.sku) AS qty,
                GROUP_CONCAT(
                  DISTINCT i.safe_filename ORDER BY i.created_on DESC SEPARATOR '|'
                ) AS image_filenames,
                GROUP_CONCAT(
                  DISTINCT CONCAT(
                        n.note, 
                        ' (updated: ', 
                        DATE_FORMAT(
                            IF(
                                n.last_updated = '0000-00-00 00:00:00',
                                n.created_on,
                                n.last_updated
                            ),
                            '%d/%m/%Y %H:%i'
                        ),
                        ')'
                    ) 
                    ORDER BY n.created_on, n.last_updated ASC SEPARATOR '|'
                ) AS room_notes
            FROM sst_products d
            LEFT JOIN sst_rooms r ON r.id = d.room_id_fk
            LEFT JOIN sst_floors f ON f.id = r.floor_id_fk
            LEFT JOIN sst_buildings b ON b.id = f.building_id_fk
            LEFT JOIN sst_locations l ON l.id = b.location_id_fk
            LEFT JOIN sst_projects p ON p.id = l.project_id_fk
            LEFT JOIN sst_images i ON i.room_id_fk = r.id
            LEFT JOIN sst_notes n ON n.room_id_fk = r.id
            WHERE p.id = $project_id
            GROUP BY d.ref, d.sku, r.id
            ORDER BY r.slug, d.ref");

    $ret = array();
    $res = $q->fetchAll(PDO::FETCH_OBJ);
    foreach ($res as $i => $o) {
        $ret[$o->room_slug][] = $o;
    }
    return (return_json($ret));
}





function ajax_get_products_in_project() {
    global $pdo;
    $project_id = $_POST['project_id'];
    if (!$project_id) {
        return(return_json(array('error'=>'No Project ID')));
    }
    $q = $pdo->query("SELECT
            d.ref, 			
			d.product_name,
			d.product_slug,					
			d.sku,
			d.custom,
			d.owner_id,
			p.id as project_id_fk,
			p.slug as project_slug,
			p.version as project_version,
			count(sku) as qty
			FROM sst_products d 			
			LEFT JOIN sst_rooms r on r.id = d.room_id_fk
			LEFT JOIN sst_floors f on f.id = r.floor_id_fk
			LEFT JOIN sst_buildings b on b.id = f.building_id_fk
			LEFT JOIN sst_locations l on l.id = b.location_id_fk			
			LEFT JOIN sst_projects p on p.id = l.project_id_fk
			WHERE p.id = $project_id
			GROUP BY d.ref,  d.sku");

    $res = $q->fetchAll(PDO::FETCH_ASSOC);

    if (count($res) < 1) {
        $res[0] = array();
        return(return_json($res));
    }

    // clear the schedule of this project
    $pdo->prepare("DELETE FROM sst_schedules WHERE project_id_fk=?")->execute([$project_id]);
    // save these to a schedule.
    foreach ($res as $row) {
        $q  = "INSERT INTO sst_schedules 
            (
            project_id_fk, 
            project_slug,
            project_version,
            product_slug, 
            product_name, 
            sku,   
            ref,           
            qty, 
            custom,
            owner_id, 
            version, 
            created_on)
            VALUES 
            (
            :project_id_fk,
            :project_slug,
            :project_version,  
            :product_slug, 
            :product_name, 
            :sku,   
            :ref,           
            :qty, 
            :custom,
            :owner_id, 
            1, 
            CURRENT_TIMESTAMP)";

        $res = $pdo->prepare($q)->execute($row);
    }

    $q = $pdo->query("SELECT * FROM sst_schedules WHERE project_id_fk = $project_id");
    $res = $q->fetchAll(PDO::FETCH_ASSOC);
    return(return_json($res));
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


function ajax_edit_name() {
    global $pdo;

    //$data['tbl'] = $_POST['modal_form_tbl'];
    $data['room_id'] = $_POST['modal_form_room_id'];
    $data['room_name'] = $_POST['modal_form_name'];

    $sql = "UPDATE sst_rooms SET `name`=:room_name WHERE id=:room_id";

    $pdo->prepare($sql)->execute($data);
}


function ajax_copy_room() {
    global $pdo;
    
    $data['new_room_name'] = $_POST['val'];
    $data['copy_room_id'] = $_POST['modal_form_room_id'];
    $data['to_floor_id'] = $_POST['modal_form_floor'];

    vd($data,1);

//    $sql = "UPDATE sst_rooms SET `name`=:room_name WHERE id=:room_id";
//    $pdo->prepare($sql)->execute($data);
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

function ajax_auto_update() {
    global $pdo;

    $id = $_POST['id'];
    $tbl = $_POST['tbl'];
    $col = $_POST['col'];
    $val = $_POST['val'];

    $sql = "UPDATE $tbl SET $col = '$val' WHERE id= $id";
    $pdo->prepare($sql)->execute();
    echo json_encode(['success' => true, 'message' => 'Update OK']);
}

function ajax_get_images() {
    global $pdo;

    $room_id = $_POST['room_id'];

    $q = $pdo->query("SELECT 
                              id, 
                              room_id_fk as room_id,
                              safe_filename
                              FROM sst_images
                              WHERE room_id_fk = $room_id  
                              ORDER BY created_on DESC");
    $res = $q->fetchAll(PDO::FETCH_ASSOC);
    return(return_json($res));
}


function ajax_image_upload() {
    global $pdo;

    if (!isset($_POST['room_id'])) {
        echo json_encode(['success' => false, 'message' => 'No room id']);
        exit();
    }

    $room_id = intval($_POST['room_id']);

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileSize = $_FILES['image']['size'];
        $fileType = $_FILES['image']['type'];
        $uploadDir = 'uploads/';
        $safeFileName = uniqid() . '-' . basename($fileName);
        $uploadFilePath = $uploadDir . $safeFileName;

        try {
            // Attempt to resize the image using Imagick
            $resizedFilePath = resizeWithImagick($fileTmpPath, $uploadFilePath, 800, 600);

            // Save metadata to the database
            $data = [
                'room_id' => $room_id,
                'safeFileName' => $safeFileName,
                'fileName' => $fileName,
                'user_id' => user_id()
            ];

            $sql = "INSERT INTO sst_images (room_id_fk, filename, safe_filename, owner_id, version, created_on)
                    VALUES (:room_id, :fileName, :safeFileName, :user_id, 1, CURRENT_TIMESTAMP)";
            $pdo->prepare($sql)->execute($data);

            echo json_encode([
                'success' => true,
                'message' => 'File uploaded and resized successfully!',
                'filePath' => $resizedFilePath
            ]);
        } catch (Exception $e) {
            // If Imagick is unavailable or an error occurs, handle it gracefully
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or there was an error.']);
    }
}


/**
 * Resize an image using Imagick.
 *
 * @param string $sourcePath Path to the original uploaded image.
 * @param string $destinationPath Path to save the resized image.
 * @param int $width Desired width of the resized image.
 * @param int $height Desired height of the resized image.
 * @return string Path to the resized image.
 * @throws Exception If resizing fails or unsupported image type.
 */
function resizeWithImagick($sourcePath, $destinationPath, $width, $height) {
    // Check if the Imagick extension is loaded
    if (!extension_loaded('imagick')) {
        throw new Exception('Imagick extension is not installed or enabled on this server.');
    }

    try {
        $imagick = new Imagick($sourcePath);

        // Resize the image while maintaining aspect ratio
        $imagick->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1, true);

        // Save the resized image to the destination path
        if (!$imagick->writeImage($destinationPath)) {
            throw new Exception('Failed to save the resized image.');
        }

        // Free memory
        $imagick->clear();
        $imagick->destroy();

        return $destinationPath; // Return the path of the resized image
    } catch (Exception $e) {
        // Handle Imagick errors gracefully
        throw new Exception('Error resizing image with Imagick: ' . $e->getMessage());
    }
}






?>
