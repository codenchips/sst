<?php

$project_slug = get_uri_part(2);

//$project_slug = 'cov-uni';

$types = get_types();
//$location = get_location_for_project($project_slug);
//$buildings = get_buildings_for_location($project_slug, $location);

//vd($buildings, 1);
?>

<?php include ('./partials/tables-side.php'); ?>





<div style="display:none;" id="table_mode_nodata" class="uk-width-1-1 uk-margin">

    <div class="uk-card uk-card-large uk-card-default">
        <div class="uk-card-header">
            <h3 class="uk-card-title">Welcome to your project, <?php echo $_COOKIE['user_name']; ?></h3>
        </div>
        <div class="uk-card-body">
            <p>Use the <b>sidebar menu</b> to manage buildings, floors and rooms in this project.</p>
            <p>Then select a <b>room</b> to manage the products in that room.</p>
        </div>
        <div class="uk-card-footer uk-align-center">
            <button class="uk-button uk-button-primary" type="button" uk-toggle="target: .tables-side">Manage Project</button>
        </div>

    </div>

</div>



<div style="display:none;" id="table_mode_view" class="uk-width-1-1 uk-margin">

    <div class="uk-width-1-3 uk-margin">
        <button class="uk-button uk-button-primary" type="button" uk-toggle="target: .tables-side">Manage Project</button>
    </div>


    <div class="manage-bar uk-margin" uk-grid>
        <div class="uk-width-1-1 location-heading uk-text-left" >
            <span style="display:none" class="project_name"></span>
            <span style="display:none" class="location_name"></span>
            <span style="display:none" class="building_name"></span>
        </div>
    </div>

<form id="product-select-form">

    <div class="uk-text-center" uk-grid>

        <div class="uk-width-1-3">
            <select id="form_brand"
                    name="form_brand"
                    class="uk-select select-brand">
                <option selected value="1">Tamlite</option>
                <option value="2">xcite</option>
            </select>
        </div>

        <div class="uk-width-1-3">
            <div id="target-select-product-type" class="uk-margin">
                <select id="form_type"
                        name="form_type"
                        class="uk-select select-product-type"
                        oninvalid="this.setCustomValidity('Please select a product type.')"
                        oninput="this.setCustomValidity('')">
                    <option selected value="">Select a Product Type</option>
                    <?php foreach ($types as $p) {
                        echo "<option value='$p->type_slug_pk'>$p->type_name</option>";
                    } ?>
                </select>
            </div>
        </div>

        <div class="uk-width-1-3">
            <div id="target-select-product" class="uk-margin">
                <select required id="form_product"
                        name="form_product"
                        class="uk-select select-product"
                        oninvalid="this.setCustomValidity('Please select a product.')"
                        oninput="this.setCustomValidity('')">
                    <option value="">Select a Product</option>
                </select>
            </div>
        </div>

        <div class="uk-width-1-3">
            <div id="target-select-sku" class="uk-margin">
                <select required id="form_sku"
                        name="form_sku"
                        class="uk-select select-sku"
                        oninvalid="this.setCustomValidity('Please select a SKU.')"
                        oninput="this.setCustomValidity('')">
                    <option value="">Select a SKU</option>
                </select>
            </div>
        </div>

        <div class="uk-width-1-3">
            <button id="form-submit" type="submit" class="uk-button uk-button-primary uk-width-1-1">Add</button>
        </div>

        <div class="uk-width-1-3">
            <button type="button" uk-toggle="target: #add-special" class="uk-button uk-button-primary uk-width-1-1">Add Special</button>
        </div>

        <div class="uk-width-1-1 uk-margin">
            <input type="hidden" name="form_custom" value="0" />
            <input type="hidden" name="uid" id="uid" value="" />
            <input type="hidden" name="add_product_room_id" id="add_product_room_id" value="" />
        </div>
    </div>

</form>



<div uk-grid>
    <div class="uk-width-1-1 room-heading">
        <span style="display:none" class="floor_name"></span>
        <span style="display:none" class="room_name"></span>
    </div>
    <div class="uk-width-1-1">
        <div id="ptable"></div>
    </div>
</div>

<div id="debug"></div>



<!-- add special modal -->
<div id="add-special" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-submit-special">
        <div class="uk-text-center" uk-grid>
            <div class="uk-width-1-1">
                <h3>Add Special</h3>
            </div>

            <div class="uk-width-1-1 uk-text-left">
                <label>Brand</label>
                <select required id="form_brand"
                        name="form_custom_brand"
                        class="uk-select"
                        oninvalid="this.setCustomValidity('You must select a brand')"
                        oninput="this.setCustomValidity('')">
                    <option selected value="">Select Brand</option>
                    <option value="1">Tamlite</option>
                    <option value="2">xcite</option>
                    <option value="3">Other</option>
                </select>
            </div>

            <div class="uk-width-1-1 uk-text-left">
                <label>Product Code or SKU</label>
                <input id="form_custom"
                       name="form_custom_sku"
                       class="uk-input free-type"
                       placeholder="A product code or identifier is required"
                       required
                       value=""
                       oninvalid="this.setCustomValidity('You must enter a product code or unique identifier')"
                       oninput="this.setCustomValidity('')" />
            </div>

            <div class="uk-width-1-1 uk-text-left">
                <label>Product Name</label>
                <input id="form_custom"
                       name="form_custom_product_name"
                       class="uk-input free-type"
                       placeholder="Free type a product name"
                       required
                       value=""
                       oninvalid="this.setCustomValidity('Enter the full name of this product')"
                       oninput="this.setCustomValidity('')" />
            </div>

            <div class="uk-width-1-1">
                <input type="hidden" name="form_custom" value="1" />
                <input type="hidden" name="add_product_room_id" id="add_product_room_id" value="" />
                <button  id="form-submit-special" type="submit" class="uk-button uk-button-primary">Add</button>
                <button  class="uk-modal-close uk-button uk-button-default">Cancel</button>
            </div>
        </div>
        </form>
    </div>
</div>



<!-- Templates -->
<script id="tmp-select-product-type" type="x-tmpl-mustache">
<select required id="form_type" name="form_type" class="uk-select select-product-type"
oninvalid="this.setCustomValidity('Please select a product type.')"
oninput="this.setCustomValidity('')">
<option selected value="">Select a Product Type</option>
{{#types}}
    <option value="{{type_slug_pk}}">{{type_name}}</option>
{{/types}}
</select>
</script>
<script id="tmp-select-product" type="x-tmpl-mustache">
<select required id="form_product" name="form_product" class="uk-select select-product"
oninvalid="this.setCustomValidity('Please select a product.')"
oninput="this.setCustomValidity('')">
<option selected value="">Select a Product</option>
{{#products}}
    <option value="{{product_slug_pk}}">{{hrslug}}</option>
{{/products}}
</select>
</script>
<script id="tmp-select-sku" type="x-tmpl-mustache">
<select required id="form_sku" name="form_sku" class="uk-select select-sku"
oninvalid="this.setCustomValidity('Please select a SKU.')"
oninput="this.setCustomValidity('')">
<option selected value="">Select a SKU</option>
{{#skus}}
    <option>{{.}}</option>
{{/skus}}
</select>
</script>


