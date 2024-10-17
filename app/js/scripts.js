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


  // add floor modeal open
  // grab the project, location and building info
  $("#add-floor").on('show', function (e) {
      $("input[name='modal_form_project_slug']").val('cov-uni2');
      $("input[name='modal_form_location']").val('location2');
      $("input[name='modal_form_building']").val('building2');
  });
  // add floor close. re-open the sidebar
  $("#add-floor").on('hidden', function (e) {
      setTimeout(function() { UIkit.offcanvas($('#tables-side')).show(); },100);
  });


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
