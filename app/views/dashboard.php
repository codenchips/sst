<?php
$project_slug = 'cov-uni';

$types = get_types();
//$location = get_location_for_project($project_slug);
//$buildings = get_buildings_for_location($project_slug, $location);

//vd($buildings, 1);
?>


<div class="uk-width-1-1 uk-margin">


<div uk-grid>
    <div class="uk-width-1-1">
        <div id="dashboard_projects"></div>
    </div>
</div>



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
                <input type="hidden" name="uid" id="uid" value="" />
                <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                <button id="form-submit-special" type="submit" class="uk-button uk-button-primary">Add</button>
            </div>
        </div>
        </form>
    </div>
</div>
