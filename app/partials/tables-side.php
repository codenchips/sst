<?php //$data = get_project_sidenav('cov-uni'); ?>


<!-- rooms nav -->
<div id="tables-side" uk-offcanvas="mode: reveal; overlay: true">
    <div class="uk-offcanvas-bar uk-flex uk-flex-column">

        <h4>Manage Project</h4>

        <div id="locations"></div>



        <ul class="uk-nav uk-nav-primary side-menus" uk-nav>
            <li class="uk-nav-divider"></li>
            <li class="uk-parent">
                <li class="uk-nav-header">Location</li>
                <ul class="uk-nav-sub">
                    <li><a href="#"><?php echo $location; ?></a></li>
                </ul>
            </li>
        </ul>



    </div>
</div>

<!-- end rooms nav -->


<!-- add room modal -->
<div id="add-room" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-add-room">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Add Room</h3>
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Name The Room</label>
                    <input id="modal_form_room"
                           name="modal_form_room"
                           class="uk-input free-type"
                           placeholder="Room Name"
                           required
                           value=""
                           oninvalid="this.setCustomValidity('You must enter a name for this floor')"
                           oninput="this.setCustomValidity('')" />
                </div>

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_project_slug" value="<?php echo $project_slug; ?>" />
                    <input type="hidden" name="modal_form_location" value="<?php echo $location; ?>" />
                    <input type="hidden" name="modal_form_building" value="<?php echo $buildings[0]; ?>" />
                    <input type="hidden" name="modal_form_floor" value="" />
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                    <button id="form-submit-room" type="submit" class="uk-button uk-button-primary">Add Room</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- add floor modal -->
<div id="add-floor" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-add-floor">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Add Floor</h3>
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Name The Floor</label>
                    <input id="modal_form_floor"
                           name="modal_form_floor"
                           class="uk-input free-type"
                           placeholder="Floor Name"
                           required
                           value=""
                           oninvalid="this.setCustomValidity('You must enter a name for this floor')"
                           oninput="this.setCustomValidity('')" />
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Add a Floorplan</label>
                    <input id="modal_form_floorplan"
                           name="modal_form_floorplan"
                           type="file"
                           class="uk-input" />
                </div>

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_project_slug" value="<?php echo $project_slug; ?>" />
                    <input type="hidden" name="modal_form_location" value="<?php echo $location; ?>" />
                    <input type="hidden" name="modal_form_building" value="<?php echo $buildings[0]; ?>" />
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                    <button id="form-submit-floor" type="submit" class="uk-button uk-button-primary">Add Floor</button>
                </div>
            </div>
        </form>
    </div>
</div>