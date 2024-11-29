<?php

$project_slug = get_uri_part(2);

$p = get_project($project_slug);

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



<div style="display:block;" id="schedule_mode_view" class="uk-width-1-1 uk-margin-remove">

<div uk-grid>
    <div class="uk-width-1-1 schedule-topper uk-margin-remove uk-padding-remove-vertical" uk-grid>
        <div class="uk-width-auto uk-padding-remove">
            <p class="heading">Project:</p>
        </div>
        <div class="uk-width-expand uk-padding-remove">
            <p id="info_project_name"><?php echo $p->name; ?></p>
        </div>
        <div class="uk-width-auto uk-padding-remove">
            <p class="heading">Project ID:</p>
        </div>
        <div class="uk-width-auto uk-padding-remove">
            <p id="info_project_id"><?php echo $p->project_id; ?></p>
        </div>
    </div>
    <div class="uk-width-1-1 schedule-topper uk-margin-remove uk-padding-remove-vertical" uk-grid>
        <div class="uk-width-auto uk-padding-remove">
            <p class="heading">Engineer:</p>
        </div>
        <div class="uk-width-expand uk-padding-remove">
            <p id="info_engineer"><?php echo ($p->engineer != "") ? $p->engineer : $_COOKIE['user_name']; ?></p>
        </div>
        <div class="uk-width-auto uk-padding-remove">
            <p class="heading">Date:</p>
        </div>
        <div class="uk-width-auto uk-padding-remove">
            <p id="info_date"><?php echo date("d/m/Y"); ?></p>
        </div>
    </div>

    <div class="uk-width-1-1">
        <div id="stable"></div>
    </div>
    <div class="uk-width-1-1 uk-align-center">
        <button id="gen_datasheets" class="uk-button uk-button-primary uk-align-center" type="button">Generate Datasheets</button>
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

