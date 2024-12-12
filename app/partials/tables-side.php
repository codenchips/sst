<?php //$data = get_project_sidenav('cov-uni'); ?>


<!-- rooms nav -->
<div class="uk-offcanvas-bar uk-visible@xl tables-side" id="desktop-sidebar">
    <div class="uk-visible@m uk-flex uk-flex-column">

        <h4>Manage Project</h4>

        <div id="locations" class="locations"></div>

    </div>
</div>
<!-- end rooms nav -->




<!-- rooms nav offcanvas-sidebar -->
<div id="offcanvas-sidebar" class="tables-side" uk-offcanvas="mode: slide; overlay: true">


    <div class="uk-offcanvas-bar uk-flex uk-flex-column">

        <h4>Manage Project</h4>

        <div id="locations" class="locations"></div>

    </div>
</div>
<!-- end rooms nav -->


<!-- The handle -->
<!--<button id="sidebar-handle" class="handle" aria-label="Toggle Sidebar" uk-toggle="target: #offcanvas-sidebar"><span>Manage Project</span></button>-->



<!-- remove floor/ room modal -->
<div id="remove-location" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-remove-location">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Remove Location</h3>
                </div>

                <div class="uk-width-1-1">
                    <h4>This will delete this location.</h4>
                    <p>This will remove all rooms on this location and all products assigned to those rooms.</p>
                </div>

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_project_slug" value="<?php echo $project_slug; ?>" />
                    <input type="hidden" name="modal_form_uid" value="" />

                    <button id="form-submit-room" type="submit" class="uk-button uk-button-primary">Remove</button>
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- remove floor/ room modal -->
<div id="remove-building" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-remove-building">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Remove Building</h3>
                </div>

                <div class="uk-width-1-1">
                    <h4>This will delete this building.</h4>
                    <p>This will remove all rooms on this building and all products assigned to those rooms.</p>
                </div>

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_project_slug" value="<?php echo $project_slug; ?>" />
                    <input type="hidden" name="modal_form_uid" value="" />

                    <button id="form-submit-room" type="submit" class="uk-button uk-button-primary">Remove</button>
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- remove floor/ room modal -->
<div id="remove-floor" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-remove-floor">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Remove Floor</h3>
                </div>

                <div class="uk-width-1-1">
                    <h4>This will delete this floor.</h4>
                    <p>This will remove all rooms on this floor and all products assigned to those rooms.</p>
                </div>

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_project_slug" value="<?php echo $project_slug; ?>" />
                    <input type="hidden" name="modal_form_uid" value="" />

                    <button type="submit" class="uk-button uk-button-primary">Remove</button>
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- remove floor/ room modal -->
<div id="remove-room" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-remove-room">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Remove Room</h3>
                </div>

                <div class="uk-width-1-1">
                    <h4>This will delete this room.</h4>
                    <p>This will remove all rooms and all products assigned to this room.</p>
                </div>

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_project_slug" value="<?php echo $project_slug; ?>" />
                    <input type="hidden" name="modal_form_uid" value="" />
                    <button type="submit" class="uk-button uk-button-primary">Remove</button>
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>



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
                    <input type="hidden" name="modal_form_project_id" value="<?php echo $project_slug; ?>" />
                    <input type="hidden" name="modal_form_project_name" value="" />

                    <input type="hidden" name="modal_form_uid" value="" />
                    <div class="form-actions">
                        <button type="submit" class="uk-button uk-button-primary">Add Room</button>
                        <button class="uk-modal-close uk-button uk-button-default">Cancel</button>

                    </div>
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

<!--                <div class="uk-width-1-1 uk-text-left">-->
<!--                    <label>Add a Floorplan</label>-->
<!--                    <input disabled id="modal_form_floorplan"-->
<!--                           name="modal_form_floorplan"-->
<!--                           type="file"-->
<!--                           class="uk-input" />-->
<!--                </div>-->

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_uid" value="" />

                    <button id="form-submit-floor" type="submit" class="uk-button uk-button-primary">Add Floor</button>
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


<!-- add building modal -->
<div id="add-building" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-add-building">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Add Building</h3>
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Name The Building</label>
                    <input id="modal_form_building"
                           name="modal_form_building"
                           class="uk-input free-type"
                           placeholder="Building Name"
                           required
                           value=""
                           oninvalid="this.setCustomValidity('You must enter a name for this building')"
                           oninput="this.setCustomValidity('')" />
                </div>

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_uid" value="" />

                    <button id="form-submit-building" type="submit" class="uk-button uk-button-primary">Add Building</button>
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- add location modal -->
<div id="add-location" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-add-location">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Add Location</h3>
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Name The Location</label>
                    <input id="modal_form_location"
                           name="modal_form_location"
                           class="uk-input free-type"
                           placeholder="Location Name"
                           required
                           value=""
                           oninvalid="this.setCustomValidity('You must enter a name for this location')"
                           oninput="this.setCustomValidity('')" />
                </div>

                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_uid" value="" />

                    <button id="form-submit-location" type="submit" class="uk-button uk-button-primary">Add Location</button>
                    <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>