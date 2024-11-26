<?php

$project_slug = get_uri_part(2);

//$project_slug = 'cov-uni';

$types = get_types();
//$location = get_location_for_project($project_slug);
//$buildings = get_buildings_for_location($project_slug, $location);

//vd($buildings, 1);
?>

<?php //include ('./partials/tables-side.php'); ?>


<div style="display:none;" id="schedule_mode_nodata" class="uk-width-1-1 uk-margin">

    <div class="uk-card uk-card-large uk-card-default">
        <div class="uk-card-header">
            <h3 class="uk-card-title">This will be your Schedule.</h3>
        </div>
        <div class="uk-card-body">
        </div>
        <div class="uk-card-footer uk-align-center">
        </div>
    </div>

</div>



<div style="display:block;" id="schedule_mode_view" class="uk-width-1-1 uk-margin">


<div uk-grid>
    <div class="uk-width-1-1">
    <ul class="uk-subnav uk-margin uk-subnav-pill" uk-switcher="animation: uk-animation-fade">
        <li><a href="#">Contents</a></li>
        <li><a href="#">Details</a></li>
        <li><a href="#">Cover</a></li>
    </ul>
    <div class="uk-switcher uk-margin">
        <div>

            <div class="uk-width-1-1">
                <div id="stable"></div>
            </div>
            <div class="uk-card-footer uk-align-center">
                <button id="gen_datasheets" class="uk-button uk-button-primary" type="button">Generate Datasheets</button>
            </div>

        </div>
        <div>Hello again! <a href="#" uk-switcher-item="next">Next item</a></div>
        <div>Bazinga! <a href="#" uk-switcher-item="previous">Previous item</a></div>
    </div>
    </div>




</div>



<!-- set qty modal -->
<div id="folio-progress" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-submit-folio-progress">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Processing</h3>
                </div>

                <div class="uk-width-1-1 uk-align-center">
                    <progress id="js-progressbar" class="uk-progress" value="10" max="100"></progress>
                    <p id="progress-text">Gathering Product Data ...</p>
                </div>

                <div class="uk-width-1-1 uk-align-center">
                    <button type="submit" disabled id="download_datasheets" class="uk-button uk-button-primary" type="button">Download Datasheets</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- set qty modal -->
<div id="set-qty" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-submit-set-qty">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Set Quantity</h3>
                </div>

                <div class="uk-width-1-1 uk-align-center">
                    <input id="set_qty_qty" class="uk-input uk-width-1-5" name="set_qty_qty" type="number" min="0" max="999" step="1" value="">
                </div>

                <div class="uk-width-1-1 uk-margin-small">
                    <input type="hidden" name="set_qty_product_id" id="set_qty_product_id" value="" />
                    <input type="hidden" name="set_qty_sku" id="set_qty_sku" value="" />

                    <button  id="form-submit-set-qty" type="submit" class="uk-button uk-button-primary">OK</button>
                    <button  class="uk-modal-close uk-button uk-button-default">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

