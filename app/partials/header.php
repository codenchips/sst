<nav class="uk-navbar-container">
    <div class="uk-container uk-padding-remove">
        <div uk-navbar>

            <div class="uk-navbar-left">

                <a class="uk-navbar-item uk-logo" href="#" aria-label="Back to Home"><img width="80" src="/images/tamlite-logo.jpg"></a>

                <ul class="uk-navbar-nav">
                    <li>
                        <a href="/">
                            <span class="uk-icon uk-margin-small-right" uk-icon="icon: grid"></span>
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="/tables">
                            <span class="uk-icon uk-margin-small-right" uk-icon="icon: grid"></span>
                            Table Mode
                        </a>
                    </li>
                </ul>


            </div>

        </div>
    </div>
</nav>
<input type="hidden" id="site_uid" name="site_uid" value="<?php echo get_uri_part(2); ?>" />
<input type="hidden" id="m_user_id" name="m_user_id" value="" />
<input type="hidden" id="m_room_id" name="m_room_id" value="" />
<input type="hidden" id="m_floor_id" name="m_floor_id" value="" />
<input type="hidden" id="m_building_id" name="m_building_id" value="" />
<input type="hidden" id="m_location_id" name="m_location_id" value="" />
<input type="hidden" id="m_project_id" name="m_project_id" value="<?php echo get_uri_part(2); ?>" />
