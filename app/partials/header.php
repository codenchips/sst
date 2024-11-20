<?php
$project_id = get_uri_part(2);
?>


<div id="spinner" style="display:none;">
    <div uk-spinner="ratio: 4"></div>
</div>

<nav class="uk-navbar-container">
    <div class="uk-container uk-padding-remove">
        <div uk-navbar>
            <a class="uk-navbar-item uk-logo" href="/"><img width="80" src="/images/tamlite-logo.jpg"></a>
            <div class="uk-navbar-right">
                <ul class="uk-navbar-nav mobile-nav">
                    <li>
                        <a href="/">
                            <span class="uk-icon" uk-icon="icon: world; ratio: 2"></span>
                            <small>Projects</small>
                        </a>
                    </li>
                    <?php if ($project_id) { ?>
                        <li>
                            <a href="/tables/<?php echo $project_id; ?>">
                                <span class="uk-icon" uk-icon="icon: file-text; ratio: 2"></span>
                                <small>Table Mode</small>
                            </a>
                        </li>
                        <li>
                            <a href="/schedule/<?php echo $project_id; ?>">
                                <span class="uk-icon" uk-icon="icon: file-pdf; ratio: 2"></span>
                                <small>Schedule</small>
                            </a>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="#">
                            <span class="uk-icon" uk-icon="icon: user; ratio: 2"></span>
                            <small>Account</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<?php $uid = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';  ?>
<input type="hidden" id="site_uid" name="site_uid" value="<?php echo $project_id; ?>" />
<input type="hidden" id="m_project_id" name="m_project_id" value="<?php echo $project_id; ?>" />
<input type="hidden" id="m_user_id" name="m_user_id" value="<?php echo $uid; ?>" />
<input type="hidden" id="m_room_id" name="m_room_id" value="" />
<input type="hidden" id="m_floor_id" name="m_floor_id" value="" />
<input type="hidden" id="m_building_id" name="m_building_id" value="" />
<input type="hidden" id="m_location_id" name="m_location_id" value="" />


<!-- Login modal -->
<div id="login" class="loginmodal" uk-modal>
    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
        <button class="uk-modal-close-default" type="button" uk-close hidden></button>
        <form id="form-login">
            <div class="uk-text-center" uk-grid>
                <div class="uk-width-1-1">
                    <h3>Login</h3>
                </div>
                <div class="uk-width-1-1 uk-text-left">
                    <label>Email</label>
                    <input id="modal_form_email"
                           name="modal_form_email"
                           class="uk-input free-type"
                           placeholder="name@tamlite.co.uk"
                           required
                           value=""
                           oninvalid="this.setCustomValidity('Enter your email address')"
                           oninput="this.setCustomValidity('')" />
                </div>
                <div class="uk-width-1-1 uk-text-left">
                    <label>Password</label>
                    <input id="modal_form_password"
                           name="modal_form_password"
                           class="uk-input free-type"
                           type="password"
                           placeholder=""
                           required
                           value=""
                           oninvalid="this.setCustomValidity('Enter your password')"
                           oninput="this.setCustomValidity('')" />
                </div>
                <div style="display:none;" class="uk-width-1-1 uk-text-left login-error">
                    <p class=""></p>
                </div>
                <div class="uk-width-1-1">
                    <input type="hidden" name="modal_form_uid" value="" />
                    <div class="form-actions">
                        <button id="form-submit-login" type="submit" class="uk-button uk-button-primary">Login</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>