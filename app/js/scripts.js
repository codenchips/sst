$(function () {
  console.log("Loaded ..");


  $(".select-brand").on("change", function (e) {
    e.preventDefault();
    var brand = $(this).val();
    console.log('selected brand: '+brand);
    $(".select-sku,.select-product,.select-product-type").val("");
    $.ajax("/api/get_types", {
      type: "post",
      data: { brand: brand },
      success: function (data, status, xhr) {
        var jsonData = $.parseJSON(data);
        var template = $("#tmp-select-product-type").html();
        var rendered = Mustache.render(template, { types: jsonData });
        $("#target-select-product-type").html(rendered);
        bindSelectProductType();
      }
    });
  });



  // get products for type
  bindSelectProductType();  // prebind as brand may not be fired
  function bindSelectProductType() {
    $(".select-sku,.select-product").val("");
    $(".select-product-type").val("").off("change").on("change", function (e) {
      e.preventDefault();
      var slug = $(this).val();
      console.log('slected type: '+slug);
      $.ajax("/api/get_products_for_type", {
        type: "post",
        data: { type_slug: slug },
        success: function (data, status, xhr) {
          var jsonData = $.parseJSON(data);
          var template = $("#tmp-select-product").html();
          var rendered = Mustache.render(template, { products: jsonData });
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
    $(".select-product").val("").off("change").on("change", function (e) {
      e.preventDefault();
      var slug = $(this).val();
      console.log('selected product: '+slug);
      $.ajax("/api/get_skus_for_product", {
        type: "post",
        data: { product_slug: slug },
        success: function (data, status, xhr) {
          var jsonData = $.parseJSON(data);
          var template = $("#tmp-select-sku").html();
          var rendered = Mustache.render(template, { skus: jsonData });
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
    $(".select-sku").val("").off("change").on("change", function (e) {
      e.preventDefault();
      var slug = $(this).val();
      console.log('selected sku: '+slug);
      $.ajax("/api/get_skus_for_product", {
        type: "post",
        data: { sku: slug },
        success: function (data, status, xhr) {
          var jsonData = $.parseJSON(data);
          var template = $("#tmp-select-sku").html();
          var rendered = Mustache.render(template, { skus: jsonData });
          $("#target-select-sku").html(rendered);
        }
      });
  });
  }
  // END get skus for product

  $("#product-select-form").off("submit").on("submit", function (e) {
    e.preventDefault();
    console.log('product select form submitted');
    const form = document.querySelector("#product-select-form");
    if ($('form_sku').val() != "") {
        sendData(form, 'add_product');
    } else {
      alert('You must select a SKU')
    }
  });

  $("#form-submit-special").off("submit").on("submit", function (e) {
      e.preventDefault();
      console.log('Special form submitted');
      const form = document.querySelector("#form-submit-special");
      sendData(form, 'add_special');
      UIkit.modal($('#add-special')).hide();
  });

  $("#form-add-floor").off("submit").on("submit", function (e) {
      e.preventDefault();
      console.log('Add floor  form submitted');
      const location = $("#form-add-floor input[name=modal_form_location]").val();
      const building = $("#form-add-floor input[name=modal_form_building]").val();
      const floor = $("#form-add-floor input[name=modal_form_floor]").val();
      const form = document.querySelector("#form-add-floor");
      sendData(form, 'add_floor');
      // update sidebar nav
      var template = $("#tmp-new-floor").html();
      var rendered = Mustache.render(template, { floor: floor, location: location, building: building });
      $("#target-add-floor").append(rendered);
      //$('.add-room-static').remove();

      UIkit.modal($('#add-floor')).hide();
  });


  // add floor close. re-open the sidebar
  $("#add-floor, #add-room").on('hidden', function (e) {
      setTimeout(function() {
          UIkit.offcanvas($('#tables-side')).show();
        },100);
  });


    function bindNavClicks() {
        $(".manage-link.add-room").on('click', function (e) {
            const floor = $(this).data('floor');

            $('input[name=modal_form_floor]').val(floor);

            UIkit.modal($('#add-room')).show();
        });

        $(".manage-link.add-floor").on('click', function (e) {
            UIkit.modal($('#add-floor')).show();
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
        updatepTable();
    }
    if ($('#tables-side').length) {
        updateTableSideNav();
    }

    function updateTableSideNav() {
        if ($('#tables-side').length) {
            $.ajax("/api/get_project_sidenav", {
                type: "post",
                data: { project_slug: 'cov-uni' },
                success: function (data, status, xhr) {
                    const locationsDiv = document.getElementById('locations');
                    var jsonData = $.parseJSON(data);
                    const locationList = createList(jsonData.locations, jsonData.project_slug);
                    locationsDiv.appendChild(locationList);
                    bindNavClicks();
                }
            });
        }
    }


    function createList(obj, parentSlug, level = 1) {
        const ul = document.createElement('ul');
        ul.setAttribute('id', `${parentSlug}-ul`);
        ul.classList.add('location-list');

        let addedFloorsHeading = false;

        for (const key in obj) {
            if (typeof obj[key] === 'object' && obj[key] !== null) {
                const li = document.createElement('li');
                const itemSlug = obj[key].slug || key;
                const itemUid = obj[key].uid || '';  // Get the UID or fallback to an empty string
                li.setAttribute('id', `${itemSlug}-li`);
                li.classList.add('location-item');

                if (level === 1) {
                    const locationHeading = document.createElement('h2');
                    locationHeading.textContent = 'Location:';
                    li.appendChild(locationHeading);

                    const locationLink = document.createElement('a');
                    locationLink.href = '#';
                    locationLink.textContent = obj[key].name || key;
                    locationLink.setAttribute('data-slug', itemSlug);
                    locationLink.setAttribute('data-uid', itemUid);  // Add data-uid attribute
                    li.appendChild(locationLink);

                    const addBuildingLink = document.createElement('a');
                    addBuildingLink.href = '#';
                    addBuildingLink.textContent = 'Add a Building';
                    addBuildingLink.setAttribute('data-location', parentSlug);
                    addBuildingLink.setAttribute('data-project', 'cov-uni');
                    li.appendChild(addBuildingLink);
                } else if (level === 2) {
                    const buildingHeading = document.createElement('h3');
                    buildingHeading.textContent = 'Building:';
                    li.appendChild(buildingHeading);

                    const buildingLink = document.createElement('a');
                    buildingLink.href = '#';
                    buildingLink.textContent = obj[key].name || key;
                    buildingLink.setAttribute('data-slug', itemSlug);
                    buildingLink.setAttribute('data-uid', itemUid);  // Add data-uid attribute
                    li.appendChild(buildingLink);

                    const addFloorLink = document.createElement('a');
                    addFloorLink.href = '#';
                    addFloorLink.textContent = 'Add a Floor';
                    addFloorLink.setAttribute('data-location', parentSlug);
                    addFloorLink.setAttribute('data-project', 'cov-uni');
                    addFloorLink.classList.add('manage-link', 'add-floor');
                    li.appendChild(addFloorLink);

                    ul.appendChild(li);
                    addedFloorsHeading = false;
                } else if (level === 3) {
                    if (!addedFloorsHeading) {
                        const floorsHeading = document.createElement('h4');
                        floorsHeading.textContent = 'Floors and Rooms:';
                        ul.appendChild(floorsHeading);
                        addedFloorsHeading = true;
                    }

                    const floorLink = document.createElement('a');
                    floorLink.href = '#';
                    floorLink.textContent = obj[key].name || key;
                    floorLink.setAttribute('data-slug', itemSlug);
                    floorLink.setAttribute('data-uid', itemUid);  // Add data-uid attribute

                    const floorLi = document.createElement('li');
                    floorLi.setAttribute('id', `${itemSlug}-floor-li`);
                    floorLi.classList.add('floor-item');
                    floorLi.appendChild(floorLink);
                    li.appendChild(floorLi);

                    const addRoomLink = document.createElement('a');
                    addRoomLink.href = '#';
                    addRoomLink.textContent = 'Add a Room';
                    addRoomLink.setAttribute('data-location', parentSlug);
                    addRoomLink.setAttribute('data-building', itemSlug);
                    addRoomLink.setAttribute('data-floor', obj[key].slug);
                    addRoomLink.classList.add('manage-link', 'add-room');
                    floorLi.appendChild(addRoomLink);
                }

                if (obj[key].name && level > 3) {
                    const roomLink = document.createElement('a');
                    roomLink.href = '#';
                    roomLink.textContent = obj[key].name;
                    roomLink.setAttribute('data-slug', itemSlug);
                    roomLink.setAttribute('data-uid', itemUid);  // Add data-uid attribute

                    const roomLi = document.createElement('li');
                    roomLi.setAttribute('id', `${itemSlug}-room-li`);
                    roomLi.classList.add('room-item');
                    roomLi.appendChild(roomLink);
                    li.appendChild(roomLi);
                }

                li.appendChild(createList(obj[key], itemSlug, level + 1));
                ul.appendChild(li);
            }
        }
        return ul;
    }




    function updatepTable() {
        if ($('#ptable').length) {
            $.ajax("/api/get_ptabledata", {
                type: "post",
                success: function (data, status, xhr) {
                    //$("#debug").html(data);
                    pTable.setData(data);
                }
            });
        }
    }


    // pTable.on("rowClick", function(e, row){
    //     alert("Row " + row.getData().id + " Clicked");
    // });


    var iconPlus = function(cell, formatterParams, onRendered) {
        return '<i class="fa-solid fa-circle-plus"></i>';
    };
    var iconMinus = function(cell, formatterParams, onRendered) {
        return '<i class="fa-solid fa-circle-minus"></i>';
    };
    var iconX = function(cell, formatterParams, onRendered) {
        return '<i class="fa-solid fa-circle-xmark"></i>';
    };


    var pTable = new Tabulator("#ptable", {
        importFormat: "json",
        layout: "fitColumns",
        columns: [
            { title: "id", field: "id", visible: false },
            { title: "product_slug", field: "product_slug", visible: false },
            { title: "SKU", field: "sku", width: 150 },
            { title: "Product", field: "product_name", hozAlign: "left" },
            { title: "Ref", field: "ref", editor: "input", editorParams: {
                    search: true,
                    mask: "",
                    selectContents: true,
                    elementAttributes:{
                        maxlength:"5",
                    }
                }},
            { title: "Qty", field: "qty", width: 80, hozAlign:"left" },
            { headerSort: false, formatter: iconPlus, width: 30, hozAlign:"center",
                cellClick:  function(e, cell) { increaseQty(cell.getRow().getData().id); }
                },
            { headerSort: false, formatter: iconMinus, width: 30, hozAlign:"center",
                cellClick: function(e, cell) { decreaseQty(cell.getRow().getData().sku); }
                },
            { visible: false, headerSort: false, formatter: iconX, width: 30, hozAlign:"center",
                cellClick: function(e, cell) { removeByID(cell.getRow().getData().sku); }
                },
        ],
    });

    pTable.on("cellEdited", function(cell){
        //cell - cell component
        const sku = cell.getRow().getData().sku;
        const ref = cell.getRow().getData().ref
        //console.log('sku: '+sku+' ref: '+ref);
        if (sku != "" && ref != "") {
            $.ajax("/api/edit_ref", {
                type: "post",
                data: {sku: sku, ref: ref},
                success: function (data, status, xhr) {
                    var res = $.parseJSON(data);
                    if (res.updated != 0) {
                        //updatepTable();
                    }
                }
            });
        }
    });

    function increaseQty(id) {
        $.ajax("/api/increase_qty", {
            type: "post",
            data: {id: id},
            success: function (data, status, xhr) {
                var res = $.parseJSON(data);
                if (res.added != 0) {
                    updatepTable();
                }
            }
        });
    }
    function decreaseQty(sku) {
        $.ajax("/api/decrease_qty", {
            type: "post",
            data: {sku: sku},
            success: function (data, status, xhr) {
                var res = $.parseJSON(data);
                if (res.descreased != 0) {
                    updatepTable();
                }
            }
        });
    }
    function removeByID(id) {
        alert ("DELETE: "+id);
    }


});
