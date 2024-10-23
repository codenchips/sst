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

        <ul class="uk-nav uk-nav-primary side-menus" uk-nav>
            <li class="uk-parent">
            <li class="uk-nav-header">Building</li>
                <ul class="uk-nav-sub">
                    <li><a href="#"><?php echo $buildings[0]; ?></a></li>
                </ul>
            </li>
        </ul>

        <ul class="uk-nav uk-nav-primary">
            <li class="uk-parent">
            <li class="uk-nav-header">Floors and Rooms</li>

            <ul class="uk-nav-sub side-menus floors">
                <?php foreach ($buildings as $building) {
                    $floors = get_floors_for_building($project_slug, $location, $building);
                    foreach ($floors as $floor) { $floor_slug = slugify($floor); ?>
                        <li class="smaller"><a href="#"><?php echo $floor; ?></a></li>
                            <ul id="floor-<?php echo $floor_slug; ?>" class="uk-nav-sub <?php echo $floor_slug; ?>">

                                <?php $rooms = get_rooms_for_floor($project_slug, $location, $building, $floor);
                                foreach ($rooms as $room) { $room_slug = slugify($room); ?>
                                    <li data-location="<?php echo $location; ?>"
                                        data-building="<?php echo $buildings[0]; ?>"
                                        data-floor="<?php echo $floor_slug; ?>"
                                        class="manage-link add-room room-<?php echo $room_slug; ?>"
                                        id="floor-<?php echo $floor_slug; ?>-room-<?php echo $room_slug; ?>">
                                            <a href="#"><?php echo $room; ?></a>
                                    </li>
                                <?php } ?>
                                <!-- placeholder for additional add floor -->
                                <span id="target-add-room"></span>
                                <li class="add-room-static">
                                    <a  data-location="<?php echo $location; ?>"
                                        data-building="<?php echo $buildings[0]; ?>"
                                        data-floor="<?php echo $floor_slug; ?>"
                                        class="manage-link add-room"
                                        href="#">
                                            <span uk-icon="icon: plus; ratio: 1"></span> Add Room</a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                <?php } ?>

                <!-- placeholder for additional add floor -->
                <span id="target-add-floor"></span>
                <!-- add floor button -->
                <a uk-toggle="target: #add-floor" class="manage-link add-floor" href="#">
                    <span uk-icon="icon: plus; ratio: 1"></span> Add Floor</a>
            </ul>
            <li class="uk-nav-divider"></li>
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