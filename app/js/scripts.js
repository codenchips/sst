$(function() {
    console.log("Loaded ..");

    var iconPlus = function(cell, formatterParams, onRendered) {
        return '<i class="fa-solid fa-circle-plus"></i>';
    };
    var iconMinus = function(cell, formatterParams, onRendered) {
        return '<i class="fa-solid fa-circle-minus"></i>';
    };
    var iconX = function(cell, formatterParams, onRendered) {
        return '<i class="fa-solid fa-circle-xmark"></i>';
    };



    /*
    * TABLE MODE
    */
    $(".select-brand").on("change", function(e) {
        e.preventDefault();
        var brand = $(this).val();
        console.log('selected brand: ' + brand);
        $(".select-sku,.select-product,.select-product-type").val("");
        $.ajax("/api/get_types", {
            type: "post",
            data: {
                brand: brand
            },
            success: function(data, status, xhr) {
                var jsonData = $.parseJSON(data);
                var template = $("#tmp-select-product-type").html();
                var rendered = Mustache.render(template, {
                    types: jsonData
                });
                $("#target-select-product-type").html(rendered);
                bindSelectProductType();
            }
        });
    });
    // get products for type
    bindSelectProductType(); // prebind as brand may not be fired
    function bindSelectProductType() {
        $(".select-sku,.select-product").val("");
        $(".select-product-type").val("").off("change").on("change", function(e) {
            e.preventDefault();
            var slug = $(this).val();
            console.log('slected type: ' + slug);
            $.ajax("/api/get_products_for_type", {
                type: "post",
                data: {
                    type_slug: slug
                },
                success: function(data, status, xhr) {
                    var jsonData = $.parseJSON(data);
                    var template = $("#tmp-select-product").html();
                    var rendered = Mustache.render(template, {
                        products: jsonData
                    });
                    $("#target-select-product").html(rendered);
                    bindSelectProduct();
                }
            });
        });
    }
    // END products for type
    // get skus for product
    function bindSelectProduct() {
        $(".select-sku").val("");
        $(".select-product").val("").off("change").on("change", function(e) {
            e.preventDefault();
            var slug = $(this).val();
            console.log('selected product: ' + slug);
            $.ajax("/api/get_skus_for_product", {
                type: "post",
                data: {
                    product_slug: slug
                },
                success: function(data, status, xhr) {
                    var jsonData = $.parseJSON(data);
                    var template = $("#tmp-select-sku").html();
                    var rendered = Mustache.render(template, {
                        skus: jsonData
                    });
                    $("#target-select-sku").html(rendered);
                    bindSelectSKU();
                }
            });
        });
    }
    // END get skus for product
    // get skus for product
    function bindSelectSKU() {
        // only do this if we want to pull up an image or something on sku selection
        return (true);
        $(".select-sku").val("").off("change").on("change", function(e) {
            e.preventDefault();
            var slug = $(this).val();
            console.log('selected sku: ' + slug);
            $.ajax("/api/get_skus_for_product", {
                type: "post",
                data: {
                    sku: slug
                },
                success: function(data, status, xhr) {
                    var jsonData = $.parseJSON(data);
                    var template = $("#tmp-select-sku").html();
                    var rendered = Mustache.render(template, {
                        skus: jsonData
                    });
                    $("#target-select-sku").html(rendered);
                }
            });
        });
    }
    // END get skus for product
    $("#product-select-form").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('product select form submitted');
        const form = document.querySelector("#product-select-form");
        if ($('form_sku').val() != "") {
            sendData(form, 'add_product');
        } else {
            alert('You must select a SKU')
        }
    });
    $("#form-submit-special").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Special form submitted');
        const form = document.querySelector("#form-submit-special");
        sendData(form, 'add_special');
        UIkit.modal($('#add-special')).hide();
    });
    $("#form-add-floor").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add floor  form submitted');
        const form = document.querySelector("#form-add-floor");
        sendData(form, 'add_floor');
        // update sidebar nav
        updateTableSideNav();
        UIkit.modal($('#add-floor')).hide();
    });
    $("#form-add-building").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add building  form submitted');
        const form = document.querySelector("#form-add-building");
        sendData(form, 'add_building');
        // update sidebar nav
        updateTableSideNav();
        UIkit.modal($('#add-building')).hide();
    });
    $("#form-add-location").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add location  form submitted');
        const form = document.querySelector("#form-add-location");
        sendData(form, 'add_location');
        // update sidebar nav
        updateTableSideNav();
        UIkit.modal($('#add-location')).hide();
    });
    $("#form-add-room").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add room submitted');
        const form = document.querySelector("#form-add-room");
        sendData(form, 'add_room');
        // update sidebar nav
        updateTableSideNav();
        UIkit.modal($('#add-room')).hide();
    });
    $("#form-remove-floor").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Remove floor submitted');
        const form = document.querySelector("#form-remove-floor");
        sendData(form, 'remove_floor');
        // update sidebar nav
        updateTableSideNav();
        UIkit.modal($('#remove-floor')).hide();
        UIkit.offcanvas($('#tables-side')).show();
    });
    $("#form-remove-room").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Remove room submitted');
        const form = document.querySelector("#form-remove-room");
        sendData(form, 'remove_room');
        // update sidebar nav
        updateTableSideNav();
        UIkit.modal($('#remove-room')).hide();
        UIkit.offcanvas($('#tables-side')).show();
    });
    // add floor close. re-open the sidebar
    $("#add-floor,#add-room,#form-remove-room,#form-remove-floor").on('hidden', function(e) {
        setTimeout(function() {
            UIkit.offcanvas($('#tables-side')).show();
        }, 200);
    });



    function bindNavClicks() {
        $(".room-list .room-item a").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            const action = $(this).data('action');

            if (action == 'add') {
                $('input[name=modal_form_uid]').val(uid);
                UIkit.modal($('#add-room')).show();
            } else {
                $('input#uid,input#m_room_id,input#add_product_room_id').val(uid);
                updatepTable();
            }
        });

        $(".floor-list .floor-name a").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            const action = $(this).data('action');

            if (action == 'add') {
                $('input[name=modal_form_uid]').val(uid);
                UIkit.modal($('#add-floor')).show();
            } else {
                // $('input#uid,input#m_room_id,input#add_product_room_id').val(uid);
                // updatepTable();
            }
        });

        $(".building-list p a").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            const action = $(this).data('action');

            if (action == 'add') {
                $('input[name=modal_form_uid]').val(uid);
                UIkit.modal($('#add-building')).show();
            } else {
                // $('input#uid,input#m_room_id,input#add_product_room_id').val(uid);
                // updatepTable();
            }
        });


        $("#locations .add-location").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            const action = $(this).data('action');

            if (action == 'add') {
                $('input[name=modal_form_uid]').val(uid);
                UIkit.modal($('#add-location')).show();
            } else {
                // $('input#uid,input#m_room_id,input#add_product_room_id').val(uid);
                // updatepTable();
            }
        });


        $("#locations li.room-item i").on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            $('input[name=modal_form_uid]').val(uid);
            UIkit.modal($('#remove-room')).show();
        });
    }
    async function sendData(form, method) {
        const formData = new FormData(form);
        try {
            const response = await fetch("/api/" + method, {
                method: "post",
                body: formData,
            });
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }
            console.log(await response.json());
            updatepTable();
        } catch (e) {
            console.error(e);
        }
    }
    if ($('#ptable').length) {
        updatepTable(false);
    }
    if ($('#tables-side').length) {
        updateTableSideNav();
    }

    function updateTableSideNav() {
        if ($('#tables-side').length) {

            $.ajax("/api/get_all_by_project", {
                type: "post",
                data: {
                    pid: 1,
                },
                success: function(data, status, xhr) {
                    console.log(data);
                    $('#locations').empty();
                    var jsonData = $.parseJSON(data);
                    const locationList = generateNavMenu(jsonData);
                    $('#locations').html(locationList);
                    bindNavClicks();


                }
            });

            // $.ajax("/api/get_project_sidenav", {
            //     type: "post",
            //     data: {
            //         project_slug: 'cov-uni',
            //         site_uid: $('input#site_uid').val(),
            //         method: 'get_project_sidenav'
            //     },
            //     success: function(data, status, xhr) {
            //         $('#locations').empty();
            //         const locationsDiv = document.getElementById('locations');
            //         var jsonData = $.parseJSON(data);
            //         const locationList = createList(jsonData.locations, jsonData.project_slug);
            //         locationsDiv.appendChild(locationList);
            //         bindNavClicks();
            //     }
            // });
        }
    }





    function generateNavMenu(data) {
        const $menu = $('<ul class="nav-menu"></ul>');

        // Level 0: Projects
        $.each(data, function (projectKey, projectData) {
            // Level 1: Locations within each project
            $.each(projectData, function (locationKey, locationData) {
                if (locationKey === 'project_name' || locationKey === 'project_slug' || locationKey === 'project_id') return;

                const locationName = locationData.location_name || "Add location";
                const $locationItem = $('<li class="location-item"></li>');
                $locationItem.append(`
                <div class="location-header">
                    <span class="location-name">${locationName}</span>
                    <div class="action-icons">
                        <i class="fa-solid fa-circle-minus" data-id="${locationData.location_id}" data-action="remove"></i>
                    </div>
                </div>
            `);

                const $buildingList = $('<ul class="building-list"></ul>');
                let hasBuildings = false;

                // Level 2: Buildings within each location
                $.each(locationData, function (buildingKey, buildingData) {
                    if (buildingKey === 'location_name' || buildingKey === 'location_slug' || buildingKey === 'location_id') return;

                    hasBuildings = true; // Mark that we have at least one building
                    const buildingName = buildingData.building_name || "Add building";
                    const $buildingItem = $('<li class="building-item"></li>');
                    $buildingItem.append(`
                    <h4 class="building-header">
                        <span class="building-name">${buildingName}</span>
                        <div class="action-icons">
                            <i class="fa-solid fa-circle-minus" data-id="${buildingData.building_id}" data-action="remove"></i>
                        </div>
                    </h4>
                `);

                    const $floorList = $('<ul class="floor-list"></ul>');
                    let hasFloors = false;

                    // Level 3: Floors within each building
                    $.each(buildingData, function (floorKey, floorData) {
                        if (floorKey === 'building_name' || floorKey === 'building_slug' || floorKey === 'building_id') return;

                        hasFloors = true; // Mark that we have at least one floor
                        const floorName = floorData.floor_name || "Add floor";
                        const $floorItem = $('<li class="floor-item"></li>');
                        $floorItem.append(`
                        <div class="floor-header">
                            <span class="floor-name">${floorName}</span>
                            <div class="action-icons">
                                <i class="fa-solid fa-circle-minus" data-id="${floorData.floor_id}" data-action="remove"></i>
                            </div>
                        </div>
                    `);

                        const $roomList = $('<ul class="room-list"></ul>');
                        let hasRooms = false;

                        // Level 4: Rooms within each floor
                        $.each(floorData, function (roomKey, roomData) {
                            if (roomKey === 'floor_name' || roomKey === 'floor_slug' || roomKey === 'floor_id') return;

                            hasRooms = true; // Mark that we have at least one room
                            const roomName = roomData.room_name || "Add room";
                            const $roomItem = $('<li class="room-item"></li>');
                            $roomItem.append(`<span class="room-name"><a href="#" data-id="${roomData.room_id}">${roomName}</a></span>`);
                            if (roomData.room_name) {
                                $roomItem.append(`<i class="fa-solid fa-circle-minus action-icon" data-id="${roomData.room_id}" data-action="remove"></i>`);
                            }
                            $roomList.append($roomItem);
                        });

                        // Show "Add Room" link if there is a valid floor ID
                        if (floorData.floor_id) {
                            $roomList.append(`<li class="room-item"><span class="room-name"><a href="#" data-action="add" data-id="${floorData.floor_id}">Add room</a></span></li>`);
                        }

                        $floorItem.append($roomList);
                        $floorList.append($floorItem);
                    });

                    // Show "Add Floor" link if there is a valid building ID
                    if (buildingData.building_id) {
                        $floorList.append(`<li class="floor-item"><span class="floor-name"><a href="#" data-id="${buildingData.building_id}" data-action="add">Add floor</a></span></li>`);
                    }

                    $buildingItem.append($floorList);
                    $buildingList.append($buildingItem);
                });

                // Always show "Add Building" link with the location ID
                if (locationData.location_id) {
                    $buildingList.append(`<li class="building-item"><p><a href="#" data-id="${locationData.location_id}" data-action="add">Add building</a></p></li>`);
                }

                $locationItem.append($buildingList);
                $menu.append($locationItem);
            });
        });

        const project_id = $('input#m_project_id').val();
        $menu.append(`
        <ul class="building-list">
            <li class="building-item"><p><a class="add-location" href="#" data-id="${project_id}" data-action="add">Add Location</a></p></li>
        </ul>
    `);

        return $menu;
    }









    function updatepTable(room_id = false) {
        setTimeout(function() {
            if (room_id == false) {
                room_id = $('input#m_room_id').val();
            }
            if ($('#ptable').length && room_id) {
                $.ajax("/api/get_products_in_room", {
                    type: "post",
                    data: { room_id: room_id },
                    success: function(data, status, xhr) {
                        var jsonData = $.parseJSON(data);
                        if (data) {
                            pTable.setData(data);
                            // set the uid for ref by others
                            $('input#uid').val(room_id);
                            // $('.room-heading .floor_name').html(jsonData[0].floor_name).show();
                            // $('.room-heading .room_name').html(jsonData[0].room_name).show();
                            // $('.location-heading .project_name').html(jsonData[0].project_name).show();
                            // $('.location-heading .location_name').html(jsonData[0].location_name).show();
                            // $('.location-heading .building_name').html(jsonData[0].building_name).show();
                        } else {
                            pTable.setData([]);
                        }
                    }
                });
            }
        },100);
    }
    // pTable.on("rowClick", function(e, row){
    //     alert("Row " + row.getData().id + " Clicked");
    // });
    if ($('#ptable').length) {
        var pTable = new Tabulator("#ptable", {
            importFormat: "json",
            layout: "fitColumns",
            loader: false,
            dataLoaderError: "There was an error loading the data",
            columns: [{
                title: "id",
                field: "id",
                visible: false
            },
                {
                    title: "room_id",
                    field: "room_id_fk",
                    visible: false
                },
                {
                    title: "product_slug",
                    field: "product_slug",
                    visible: false
                },
                {
                    title: "SKU",
                    field: "sku",
                    width: 150
                },
                {
                    title: "Product",
                    field: "product_name",
                    hozAlign: "left"
                },
                {
                    title: "Ref",
                    field: "ref",
                    editor: "input",
                    editorParams: {
                        search: true,
                        mask: "",
                        selectContents: true,
                        elementAttributes: {
                            maxlength: "5",
                        }
                    }
                },
                {
                    title: "Qty",
                    field: "qty",
                    width: 80,
                    hozAlign: "left"
                },
                {
                    headerSort: false,
                    formatter: iconPlus,
                    width: 30,
                    hozAlign: "center",
                    cellClick: function (e, cell) {
                        increaseQty(cell.getRow().getData().id);
                    }
                },
                {
                    headerSort: false,
                    formatter: iconMinus,
                    width: 30,
                    hozAlign: "center",
                    cellClick: function (e, cell) {
                        decreaseQty(cell.getRow().getData().sku, cell.getRow().getData().room_id_fk);
                    }
                },
                {
                    visible: false,
                    headerSort: false,
                    formatter: iconX,
                    width: 30,
                    hozAlign: "center",
                    cellClick: function (e, cell) {
                        removeByID(cell.getRow().getData().sku);
                    }
                },
            ],
        });
        pTable.on("cellEdited", function (cell) {
            //cell - cell component
            const sku = cell.getRow().getData().sku;
            const uid = cell.getRow().getData().room_id;
            const ref = cell.getRow().getData().ref
            //console.log('sku: '+sku+' ref: '+ref);
            if (sku != "" && ref != "") {
                $.ajax("/api/edit_ref", {
                    type: "post",
                    data: {
                        sku: sku,
                        ref: ref,
                        uid: uid
                    },
                    success: function (data, status, xhr) {
                        var res = $.parseJSON(data);
                        if (res.updated != 0) {
                            //updatepTable();
                        }
                    }
                });
            }
        });
    }

    function increaseQty(id) {
        $.ajax("/api/increase_qty", {
            type: "post",
            data: {
                id: id
            },
            success: function(data, status, xhr) {
                var res = $.parseJSON(data);
                if (res.added != 0) {
                    updatepTable($('input#uid').val());
                }
            }
        });
    }

    function decreaseQty(sku, uid) {
        console.log('dec: ' + uid);
        $.ajax("/api/decrease_qty", {
            type: "post",
            data: {
                sku: sku,
                uid: uid
            },
            success: function(data, status, xhr) {
                var res = $.parseJSON(data);
                if (res.descreased != 0) {
                    updatepTable(uid);
                }
            }
        });
    }

    UIkit.util.on('#tables-side', 'show', function () {
       //console.log('show nav');
    });

    /*
    * // END TABLE MODE
    */


    /*
    *  DASHBOARD
    */
    if ($('#dashboard_projects').length) {
        console.log('Update DashTable');
        updateDashTable();
    }

    function updateDashTable(uid = false) {
        if (uid === false) {
            uid = 1  // user_id
        }
        if ($('#dashboard_projects').length ) {
            $.ajax("/api/get_dashtabledata", {
                type: "post",
                data: {
                    uid: uid
                },
                success: function(data, status, xhr) {
                    var jsonData = $.parseJSON(data);

                    if (data) {
                        dashTable.setData(data);
                    } else {
                        dashTable.setData([]);
                    }
                }
            });
        }
    }

    if ($('#dashboard_projects').length) {
        var dashTable = new Tabulator("#dashboard_projects", {
            importFormat: "json",
            layout: "fitColumns",
            loader: false,
            dataLoaderError: "There was an error loading the data",
            initialSort:[
                {column:"project_name", dir:"asc"}, //sort by this first
            ],
            // groupBy: "project_name",
            // groupHeader:function(value, count, data, group){
            //     //value - the value all members of this group share
            //     //count - the number of rows in this group
            //     //data - an array of all the row data objects in this group
            //     //group - the group component for the group
            //
            //     return "Project Name: <span style='color:#0f7ae5; margin-left: 10px;'>" + value + "</span><span style='float:right;'><a href='#'>Add Location</a></span>";
            // },
            columns: [{
                    title: "project_id",
                    field: "id",
                    visible: false
                },
                {
                    title: "site_uid",
                    field: "site_uid",
                    visible: false
                },
                {
                    title: "project_slug",
                    field: "project_slug",
                    visible: false
                },
                {
                    title: "Project Name",
                    field: "project_name",
                    formatter: "link",
                    sorter:"string",
                    visible: true,
                    headerSortStartingDir:"desc",
                    formatterParams:{
                        labelField: "project_name",
                        target: "_self",
                        url: "#",
                    },
                    cellClick: function(e, cell) {
                        location = "/tables/"+cell.getRow().getData().site_uid;
                    }
                },

                {
                    title: "Site",
                    field: "location",
                    formatter: "link",
                    sorter:"string",
                    formatterParams:{
                        labelField: "location",
                        target: "_self",
                        url: "#",
                    },
                    cellClick: function(e, cell) {
                        location = "/tables/"+cell.getRow().getData().site_uid;
                    }
                },
                {
                    title: "Rev",
                    field: "version",
                    width: 100
                },
                {
                    title: "Created On",
                    field: "created",
                    width: 150
                },
                {
                    visible: true,
                    headerSort: false,
                    formatter: iconX,
                    width: 30,
                    hozAlign: "center",
                    cellClick: function (e, cell) {
                        deleteProject(cell.getRow().getData().site_uid);
                    }
                },
            ],
        });
    }

    function deleteProject(slug) {
        alert ('DELETE: '+slug);
    }


    $("#form-create-project").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add project submitted');
        const form = document.querySelector("#form-create-project");
        sendData(form, 'add_project');
        // update sidebar nav
        //updateTableSideNav();
        UIkit.modal($('#create-project')).hide();
    });

    $('#form_project_name').off('focus').on('focus', function(e) {
        $('#form_location').attr({'disabled':'disabled'});
        $('#form_building').attr({'disabled':'disabled'});
    });
    $('#form_project_name').off('blur').on('blur', function(e) {
        if ($(this).val() != "") {
            $('#form_location').removeAttr('disabled').focus();

        }
    });


    $('#form_location').off('focus').on('focus', function(e) {
        if ($('#form_project_name').val() != "") {
            $.ajax("/api/get_locations_for_project", {
                type: "post",
                data: {
                    project_name: $('#form_project_name').val()
                },
                success: function (data, status, xhr) {
                    var html = data;
                    if (html) {
                        $("#form_location_select").html(html);
                    }
                    $('#form_location').off('focus').on('focus', function(e) {
                        $('#form_building').attr({'disabled':'disabled'});
                    });
                    $('#form_location').off('blur').on('blur', function(e) {
                        if ($(this).val() != "") {
                            $('#form_building').removeAttr('disabled').focus();
                        }
                    });
                }
            });
        }
    });




    /*
    *  DASHBOARD
    */


});