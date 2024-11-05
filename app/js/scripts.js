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
        $('#locations .room-item a').off('click').on('click', function(e) {
            uid = $(this).data('uid');
            // update the uid on the ptable for ref
            $('input#uid').val(uid);
            updatepTable(uid);
        });
        $(".manage-link.add-room").on('click', function(e) {
            const uid = $(this).data('uid');
            $('input[name=modal_form_uid]').val(uid);
            UIkit.modal($('#add-room')).show();
        });
        $(".manage-link.add-floor").on('click', function(e) {
            const uid = $(this).data('uid');
            $('input[name=modal_form_uid]').val(uid);
            UIkit.modal($('#add-floor')).show();
        });
        $("#locations .remove-link.remove-floor").on('click', function(e) {
            const uid = $(this).data('uid');
            $('input[name=modal_form_uid]').val(uid);
            UIkit.modal($('#remove-floor')).show();
        });
        $("#locations .remove-link.remove-room").on('click', function(e) {
            const uid = $(this).data('uid');
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
            updatepTable($('input#uid').val());
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
            $.ajax("/api/get_project_sidenav", {
                type: "post",
                data: {
                    project_slug: 'cov-uni',
                    site_uid: $('input#site_uid').val(),
                    method: 'get_project_sidenav'
                },
                success: function(data, status, xhr) {
                    $('#locations').empty();
                    const locationsDiv = document.getElementById('locations');
                    var jsonData = $.parseJSON(data);
                    const locationList = createList(jsonData.locations, jsonData.project_slug);
                    locationsDiv.appendChild(locationList);
                    bindNavClicks();
                }
            });
        }
    }



    function createList(obj, parentSlug, locationSlug = parentSlug, level = 1) {
        const ul = document.createElement('ul');
        ul.setAttribute('id', `${parentSlug}-ul`);
        ul.classList.add('location-list');

        let addedFloorsHeading = false;
        let initRoom = false;

        for (const key in obj) {
            if (typeof obj[key] === 'object' && obj[key] !== null) {
                const li = document.createElement('li');
                const itemSlug = obj[key].slug || key;
                const itemUid = obj[key].uid || '';
                li.setAttribute('id', `${itemSlug}-li`);
                li.classList.add('location-item');

                if (level === 1) {
                    const locationHeading = document.createElement('h2');
                    locationHeading.textContent = 'Location';
                    li.appendChild(locationHeading);

                    const locationLink = document.createElement('a');
                    locationLink.href = '#';
                    locationLink.textContent = obj[key].name || key;
                    locationLink.setAttribute('data-slug', itemSlug);
                    locationLink.setAttribute('data-uid', itemUid);
                    li.appendChild(locationLink);

                } else if (level === 2) {
                    const buildingHeading = document.createElement('h3');
                    buildingHeading.textContent = 'Building';
                    li.appendChild(buildingHeading);

                    const buildingLink = document.createElement('a');
                    buildingLink.href = '#';
                    buildingLink.textContent = obj[key].name || key;
                    buildingLink.setAttribute('data-slug', itemSlug);
                    buildingLink.setAttribute('data-uid', itemUid);
                    li.appendChild(buildingLink);

                    ul.appendChild(li);
                    addedFloorsHeading = false;

                } else if (level === 3) {
                    // Add Floors and Rooms heading and Add Floor link, even if no floors exist
                    if (!addedFloorsHeading) {
                        const floorsHeading = document.createElement('h4');
                        floorsHeading.textContent = 'Floors and Rooms';
                        ul.appendChild(floorsHeading);

                        const addFloorLink = document.createElement('a');
                        addFloorLink.href = '#';
                        addFloorLink.innerHTML = '<i class="fa-solid fa-circle-plus"></i>';
                        addFloorLink.setAttribute('data-location', locationSlug);
                        addFloorLink.setAttribute('data-building', itemSlug);
                        addFloorLink.setAttribute('data-uid', itemUid);  // Added data-uid for parent building
                        addFloorLink.classList.add('manage-link', 'add-floor');
                        floorsHeading.appendChild(addFloorLink);

                        addedFloorsHeading = true;
                    }

                    // Add each floor item if floors exist
                    const floorLink = document.createElement('a');
                    floorLink.href = '#';
                    floorLink.textContent = obj[key].name || key;
                    floorLink.setAttribute('data-slug', itemSlug);
                    floorLink.setAttribute('data-uid', itemUid);

                    const floorLi = document.createElement('li');
                    floorLi.setAttribute('id', `${itemSlug}-floor-li`);
                    floorLi.classList.add('floor-item');
                    floorLi.appendChild(floorLink);

                    const removeFloorLink = document.createElement('a');
                    removeFloorLink.href = '#';
                    removeFloorLink.innerHTML = '<i class="fa-solid fa-circle-minus"></i>';
                    removeFloorLink.setAttribute('data-uid', itemUid);
                    removeFloorLink.classList.add('remove-link', 'remove-floor');
                    floorLi.appendChild(removeFloorLink);

                    li.appendChild(floorLi);

                    const addRoomLink = document.createElement('a');
                    addRoomLink.href = '#';
                    addRoomLink.innerHTML = '<i class="fa-solid fa-circle-plus"></i>';
                    addRoomLink.setAttribute('data-location', locationSlug);
                    addRoomLink.setAttribute('data-building', parentSlug);
                    addRoomLink.setAttribute('data-floor', obj[key].slug);
                    addRoomLink.setAttribute('data-uid', itemUid);  // Added data-uid for parent floor
                    addRoomLink.classList.add('manage-link', 'add-room');
                    floorLi.appendChild(addRoomLink);

                } else if (obj[key].name && level > 3) {
                    const roomLink = document.createElement('a');
                    roomLink.href = '#';
                    roomLink.textContent = obj[key].name;
                    roomLink.setAttribute('data-slug', itemSlug);
                    roomLink.setAttribute('data-uid', itemUid);
                    roomLink.setAttribute('data-room-name', obj[key].name || key); // Adding room name
                    roomLink.setAttribute('data-floor-name', obj?.[key]?.floor || ''); // Adding floor name

                    const roomLi = document.createElement('li');
                    roomLi.setAttribute('id', `${itemSlug}-room-li`);
                    roomLi.classList.add('room-item');
                    roomLi.appendChild(roomLink);

                    const removeRoomLink = document.createElement('a');
                    removeRoomLink.href = '#';
                    removeRoomLink.innerHTML = '<i class="fa-solid fa-circle-minus"></i>';
                    removeRoomLink.setAttribute('data-uid', itemUid);
                    removeRoomLink.classList.add('remove-link', 'remove-room');
                    roomLi.appendChild(removeRoomLink);

                    if (!initRoom) {
                        $('input#site_uid').val(itemUid);
                        initRoom = true;
                    }
                    li.appendChild(roomLi);
                }

                li.appendChild(createList(obj[key], itemSlug, locationSlug, level + 1));
                ul.appendChild(li);
            }
        }

        // If there are no floors or rooms, add the heading and add-floor link anyway
        if (level === 3 && !addedFloorsHeading) {
            const floorsHeading = document.createElement('h4');
            floorsHeading.textContent = 'Floors and Rooms';
            ul.appendChild(floorsHeading);

            const addFloorLink = document.createElement('a');
            addFloorLink.href = '#';
            addFloorLink.innerHTML = '<i class="fa-solid fa-circle-plus"></i>';
            addFloorLink.setAttribute('data-location', locationSlug);
            addFloorLink.setAttribute('data-building', parentSlug);
            addFloorLink.setAttribute('data-uid', '');
            addFloorLink.classList.add('manage-link', 'add-floor');
            floorsHeading.appendChild(addFloorLink);
        }

        return ul;
    }






    function updatepTable(uid = false) {
        setTimeout(function() {
            if (uid == false) {
                uid = $('input#site_uid').val();
            }
            if ($('#ptable').length && uid) {
                $.ajax("/api/get_ptabledata", {
                    type: "post",
                    data: { uid: uid },
                    success: function(data, status, xhr) {
                        var jsonData = $.parseJSON(data);
                        if (data) {
                            pTable.setData(data);
                            // set the uid for ref by others
                            $('input#uid').val(uid);
                            $('.room-heading .floor_name').html(jsonData[0].floor_name).show();
                            $('.room-heading .room_name').html(jsonData[0].room_name).show();
                            $('.location-heading .project_name').html(jsonData[0].project_name).show();
                            $('.location-heading .location_name').html(jsonData[0].location_name).show();
                            $('.location-heading .building_name').html(jsonData[0].building_name).show();
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
                    title: "uid",
                    field: "site_uid_fk",
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
                        decreaseQty(cell.getRow().getData().sku, cell.getRow().getData().site_uid_fk);
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
            const uid = cell.getRow().getData().site_uid_fk;
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
            $('#form_location').removeAttr('disabled');
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
                            $('#form_building').removeAttr('disabled');
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