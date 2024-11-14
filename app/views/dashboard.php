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
                    <h3>New Project</h3>
                </div>

                <div class="uk-width-1-1 uk-text-left">
                    <label>Project Name</label>
                    <input id="form_project_name"
                           name="form_project_name"
                           class="uk-input free-type"
                           placeholder="My Project"
                           autocomplete="off"
                           required
                           value=""
                           oninvalid="this.setCustomValidity('You must name this project')"
                           oninput="this.setCustomValidity('')" />
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
                           oninvalid="this.setCustomValidity('You enter a location')"
                           oninput="this.setCustomValidity('')" />
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

                <div class="uk-width-1-1 uk-text-left">
                    <label>Floor</label>
                    <input id="form_floor"
                           name="form_floor"
                           class="uk-input free-type"
                           placeholder="Ground Floor"
                           autocomplete="off"
                           list="floor_suggestions"
                           required
                           disabled
                           value=""
                           oninvalid="this.setCustomValidity('Enter a floor name')"
                           oninput="this.setCustomValidity('')" />
                    <datalist id="floor_suggestions">
                        <option value="Ground Floor"></option>
                        <option value="First Floor"></option>
                        <option value="Second Floor"></option>
                    </datalist>
                </div>

            </div>

            <div class="uk-width-1-1 uk-margin uk-text-right">
                <input type="hidden" name="uid" id="uid" value="1" />
                <button id="form-submit-createproject" type="submit" class="uk-button uk-button-primary">Create</button>
                <button class="uk-modal-close uk-button uk-button-default">Cancel</button>
            </div>

        </form>
    </div>
</div>
