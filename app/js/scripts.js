$(function() {
    console.log("Loaded ..");

    var login = UIkit.modal('.loginmodal', {
        bgClose : false,
        escClose : false
    });
    var uid = getCookie('user_id');
    if (!uid) {
        UIkit.modal('.loginmodal').show();
    } else {
        $('#m_user_id').val(uid);
    }
    $("#form-login").off("submit").on("submit", function(e) {
        e.preventDefault();
        $('.login-error').hide();
        console.log('Login submitted');
        const form = document.querySelector("#form-login");
        const formData = new FormData(form);
            $.ajax("/api/login", {
                type: "post",
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false,
                cache: false,
                data: formData,
                success: function(data, status, xhr) {
                    var jsonData = $.parseJSON(data);
                    if (jsonData.id) {
                        $('#m_user_id').val(jsonData.id);
                        setCookie('user_id', jsonData.id);
                        setCookie('user_name', jsonData.name);
                        UIkit.modal($('#login')).hide();
                    }
                    if (jsonData.error) {
                        $('.login-error p').html(jsonData.error);
                        $('.login-error').show();
                    }
                }
            });
    });


    function setCookie(cname, cvalue, exdays) {
        const d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        let expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        let name = cname + "=";
        let ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }



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

    // END get skus for product
    $("#product-select-form").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('product select form submitted');
        const form = document.querySelector("#product-select-form");
        if ($('form_sku').val() != "") {
            //sendData(form, 'add_product');
            (async () => {
                try {
                    const result = await sendData(form, "add_product");
                    console.log("Result from backend:", result);
                    // Perform additional logic with `result`.
                    updatepTable();
                } catch (error) {
                    console.error("Error during sendData:", error);
                    // Handle the error.
                    alert('There as a network error, please try again');
                }
            })();
        } else {
            alert('You must select a SKU')
        }
    });
    $("#form-submit-special").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Special form submitted');
        const form = document.querySelector("#form-submit-special");
        //sendData(form, 'add_special');
        (async () => {
            try {
                const result = await sendData(form, "add_special");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updatepTable();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();
        UIkit.modal($('#add-special')).hide();
    });
    $("#form-add-floor").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add floor  form submitted');
        const form = document.querySelector("#form-add-floor");
        //sendData(form, 'add_floor');
        // update sidebar nav
        (async () => {
            try {
                const result = await sendData(form, "add_floor");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateTableSideNav();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();
        UIkit.modal($('#add-floor')).hide();
    });
    $("#form-add-building").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add building  form submitted');
        const form = document.querySelector("#form-add-building");
        //sendData(form, 'add_building');
        // update sidebar nav
        (async () => {
            try {
                const result = await sendData(form, "add_building");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateTableSideNav();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();
        UIkit.modal($('#add-building')).hide();
    });
    $("#form-add-location").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add location  form submitted');
        const form = document.querySelector("#form-add-location");
        //sendData(form, 'add_location');
        (async () => {
            try {
                const result = await sendData(form, "add_location");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateTableSideNav();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();
        UIkit.modal($('#add-location')).hide();
    });
    $("#form-add-room").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add room submitted');
        const form = document.querySelector("#form-add-room");
        //sendData(form, 'add_room');
        (async () => {
            try {
                const result = await sendData(form, "add_room");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateTableSideNav();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();

        UIkit.modal($('#add-room')).hide();
    });

    $("#form-remove-location").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Remove location submitted');
        const form = document.querySelector("#form-remove-location");
        //sendData(form, 'remove_location');
        // update sidebar nav
        (async () => {
            try {
                const result = await sendData(form, "remove_location");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateTableSideNav();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();
        UIkit.modal($('#remove-location')).hide();
        UIkit.offcanvas($('.tables-side')).show();
    });
    $("#form-remove-building").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Remove Building submitted');
        const form = document.querySelector("#form-remove-building");
        //sendData(form, 'remove_building');
        // update sidebar nav
        (async () => {
            try {
                const result = await sendData(form, "remove_building");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateTableSideNav();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();
        UIkit.modal($('#remove-building')).hide();
        UIkit.offcanvas($('.tables-side')).show();
    });
    $("#form-remove-floor").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Remove floor submitted');
        const form = document.querySelector("#form-remove-floor");
        //sendData(form, 'remove_floor');
        // update sidebar nav
        (async () => {
            try {
                const result = await sendData(form, "remove_floor");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateTableSideNav();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();
        UIkit.modal($('#remove-floor')).hide();
        UIkit.offcanvas($('.tables-side')).show();
    });
    $("#form-remove-room").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Remove room submitted');
        const form = document.querySelector("#form-remove-room");
        //sendData(form, 'remove_room');
        // update sidebar nav
        (async () => {
            try {
                const result = await sendData(form, "remove_room");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateTableSideNav();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();
        UIkit.modal($('#remove-room')).hide();
        UIkit.offcanvas($('.tables-side')).show();
    });
    // add floor close. re-open the sidebar
    $("#add-floor,#add-room,#form-remove-room,#form-remove-floor").on('hidden', function(e) {
        setTimeout(function() {
            UIkit.offcanvas($('.tables-side')).show();
        }, 200);
    });



    function deleteProject(slug) {
        alert ('DELETE: '+slug);
    }


    function updateTableModeHeadings(uid) {

        $.ajax("/api/get_headings_by_room_id", {
            type: "post",
            data: { uid: uid },
            success: function(data, status, xhr) {
                const jsonData = $.parseJSON(data);
                const hd = jsonData[0]
                console.log(hd.room_name);

                $('span.project_name').text(hd.project_name).show();
                $('span.location_name').text(hd.location_name).show();
                $('span.building_name').text(hd.building_name).show();
                $('span.floor_name').text(hd.floor_name).show();
                $('span.room_name').text(hd.room_name).show();
            }
        });
    }


    function bindNavClicks() {

        $(".room-list .room-item.view-room a").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');

            $('input#uid,input#m_room_id,input#add_product_room_id').val(uid);

            updateTableModeHeadings(uid);
            updatepTable();

            $('#table_mode_nodata').slideUp(1000);
            $('#table_mode_view').slideDown(1000);



        });


        $(".room-list .room-item.add-room a").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            const action = $(this).data('action');

            if (action == 'add') {
                $('input[name=modal_form_uid]').val(uid);
                UIkit.modal($('#add-room')).show();
            } else {
                // $('input#uid,input#m_room_id,input#add_product_room_id').val(uid);
                // updatepTable();
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


        $(".locations .add-location").off('click').on('click', function(e) {
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


        $(".locations li.room-item i.action-icon").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            $('input[name=modal_form_uid]').val(uid);
            UIkit.modal($('#remove-room')).show();
        });
        $(".locations .action-icons.floor i").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            $('input[name=modal_form_uid]').val(uid);
            UIkit.modal($('#remove-floor')).show();
        });
        $(".locations .action-icons.building i").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            $('input[name=modal_form_uid]').val(uid);
            UIkit.modal($('#remove-building')).show();
        });
        $(".locations .action-icons.location i").off('click').on('click', function(e) {
            e.preventDefault();
            const uid = $(this).data('id');
            $('input[name=modal_form_uid]').val(uid);
            UIkit.modal($('#remove-location')).show();
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

            const data = await response.json(); // Parse the response JSON.
            console.log(data);

            // Optional: Perform additional actions based on visibility or other conditions.
            if ($('#ptable').filter(':visible').length) {
                console.log('ptable visible');
                //updatepTable(); // Ensure this function doesn't interfere with the returned data.
            }

            return data; // Return the parsed data for further use.
        } catch (e) {
            console.error(e);
            throw e; // Re-throw the error for the calling function to handle.
        }
    }

    if ($('#ptable').length) {
        const currentRoomId = $('input#m_room_id').val();
        if (currentRoomId == "") {
            console.warn("no room id");
            if ($('#ptable').filter(':visible').length) {
                updatepTable(false);
            }
            $('#table_mode_nodata').fadeIn(1000);

        } else {

        }
    }
    if ($('.tables-side').length) {
        const currentProjectId = $('input#m_project_id').val();
        if (currentProjectId == "") {
            console.warn("no room id");
            //$('#table_mode_nodata').fadeIn(1000);
        } else {
            updateTableSideNav(currentProjectId);
        }
    }

    function updateTableSideNav(currentProjectId) {
        if ($('.tables-side').length) {
            if (!currentProjectId) currentProjectId = $('#m_project_id').val();
            $.ajax("/api/get_all_by_project", {
                type: "post",
                data: {
                    pid: currentProjectId,
                },
                success: function(data, status, xhr) {
                    console.log(data);
                    $('.locations').empty();
                    var jsonData = $.parseJSON(data);
                    const locationList = generateNavMenu(jsonData);
                    $('.locations').html(locationList);
                    bindNavClicks();


                }
            });
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
                    <div class="action-icons location">
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
                        <div class="action-icons building">
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
                            <div class="action-icons floor">
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
                            const $roomItem = $('<li class="room-item view-room"></li>');
                            $roomItem.append(`<span class="room-name"><a href="#" data-id="${roomData.room_id}">${roomName}</a></span>`);
                            if (roomData.room_name) {
                                $roomItem.append(`<i class="fa-solid fa-circle-minus action-icon" data-id="${roomData.room_id}" data-action="remove"></i>`);
                            }
                            $roomList.append($roomItem);
                        });

                        // Show "Add Room" link if there is a valid floor ID
                        if (floorData.floor_id) {
                            $roomList.append(`<li class="room-item add-room"><span class="room-name"><a href="#" data-action="add" data-id="${floorData.floor_id}">Add room</a></span></li>`);
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
        </ul>`);

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
                        } else {
                            pTable.setData([]);
                        }
                    }
                });
            }
        },100);
        //$('#table_mode_nodata').slideUp(1000);
        //$('#table_mode_view').slideDown(1000);
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
            const uid = cell.getRow().getData().room_id_fk;
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

    UIkit.util.on('.tables-side', 'show', function () {
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
                    console.log(jsonData[0].project_name);
                    if (jsonData[0].project_name) {
                        $('#dashboard_projects').show();
                        dashTable.setData(data);
                    } else {
                        //alert("no data");
                        $('#dashboard_projects').hide();
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
            columns: [{
                    title: "project_id",
                    field: "id",
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
                        location = "/tables/"+cell.getRow().getData().project_id;
                    }
                },
                {
                    title: "Products",
                    field: "products",
                    width: 120
                },
                {
                    title: "Rev",
                    field: "version",
                    width: 80
                },
                {
                    title: "Created",
                    field: "created",
                    width: 110
                },
                {
                    visible: true,
                    headerSort: false,
                    formatter: iconX,
                    width: 20,
                    hozAlign: "center",
                    cellClick: function (e, cell) {
                        deleteProject(cell.getRow().getData().project_id);
                    }
                },
            ],
        });
    }



    $("#form-create-project").off("submit").on("submit", function(e) {
        e.preventDefault();
        console.log('Add project submitted');
        const form = document.querySelector("#form-create-project");
        //sendData(form, 'add_project');

        (async () => {
            try {
                const result = await sendData(form, "add_project");
                console.log("Result from backend:", result);
                // Perform additional logic with `result`.
                updateDashTable();
            } catch (error) {
                console.error("Error during sendData:", error);
                // Handle the error.
                alert('There was a network error, please try again.');
            }
        })();

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
    $('#form_location').off('blur').on('blur', function(e) {
        if ($(this).val() != "") {
            $('#form_building').removeAttr('disabled').focus();
        }
    });
    $('#form_building').off('blur').on('blur', function(e) {
        if ($(this).val() != "") {
            $('#form_floor').removeAttr('disabled').focus();
        }
    });




    /*
    *  DASHBOARD
    */


});
