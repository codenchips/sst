$(function () {
  console.log("Loaded ..");


  $(".select-brand").on("change", function (e) {
    e.preventDefault();
    var brand = $(this).val();
    console.log('selected brand: '+brand);
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
  $(".select-product-type").off("change").on("change", function (e) {
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
  $(".select-product").off("change").on("change", function (e) {
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
  $(".select-sku").off("change").on("change", function (e) {
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


});
