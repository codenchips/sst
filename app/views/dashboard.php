<?php
$project_slug = 'cov-uni';
$types = get_types();
//$location = get_location_for_project($project_slug);
//$buildings = get_buildings_for_location($project_slug, $location);

//vd($projects, 1);

function project_select_options() {
    $projects = get_projects();
    $html = "";
    foreach ($projects as $p) {
        $html .= "<option value='$p->project_name'></option>";
    }
    return($html);
}
?>
<div class="uk-width-1-1 uk-margin">

<div uk-grid>

    <div class="uk-width-1-1 uk-text-right">
        <button class="uk-button uk-button-primary" type="button" uk-toggle="target: #create-project">New </button>
    </div>

</div>

<div uk-grid>
    <div class="uk-width-1-1">
        <div id="dashboard_projects"></div>
    </div>
</div>



<!-- add project modal -->
<div id="create-project" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close></button>
        <form id="form-create-project">
            <div class="uk-text-center" uk-grid>

                <div class="uk-width-1-1">
                    <h3>New Location</h3>
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Select an existing project or create a new project</label>
                    <input id="form_project_name"
                           name="form_project_name"
                           class="uk-input free-type"
                           placeholder="My Project"
                           autocomplete="off"
                           required
                           value=""
                           list="project_names"
                           oninvalid="this.setCustomValidity('You must name this project')"
                           oninput="this.setCustomValidity('')" />
                            <datalist id="project_names">
                                <?php echo project_select_options(); ?>
                            </datalist>
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Location</label>
                    <input id="form_location"
                           name="form_location"
                           class="uk-input free-type"
                           placeholder="The town, city or area"
                           autocomplete="off"
                           required
                           disabled
                           value=""
                           list="form_location_select"
                           oninvalid="this.setCustomValidity('You enter a location')"
                           oninput="this.setCustomValidity('')" />
                    <datalist id="form_location_select"></datalist>
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Building</label>
                    <input id="form_building"
                           name="form_building"
                           class="uk-input free-type"
                           placeholder="Main warehouse"
                           autocomplete="off"
                           required
                           disabled
                           value=""
                           oninvalid="this.setCustomValidity('Enter a building name')"
                           oninput="this.setCustomValidity('')" />
                </div>

            </div>

            <div class="uk-width-1-1 uk-margin uk-text-right">
                <input type="hidden" name="uid" id="uid" value="1" />
                <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
                <button id="form-submit-createproject" type="submit" class="uk-button uk-button-primary">Create</button>
            </div>

        </form>
    </div>
</div>
