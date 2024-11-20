
<div id="spinner" style="display:none;">
    <div uk-spinner="ratio: 4"></div>
</div>




<nav class="uk-navbar-container">
    <div class="uk-container uk-padding-remove">
        <div uk-navbar>

            <div class="uk-navbar-left">


                <a class="uk-navbar-item uk-logo" href="/" aria-label="Back to Home"><img width="80" src="/images/tamlite-logo.jpg"></a>

                <ul class="uk-navbar-nav">
                    <li>
                        <a href="/">
                            <span class="uk-icon uk-margin-small-right" uk-icon="icon: grid"></span>
                            Dashboard
                        </a>
                    </li>
<!---->
<!--                    <li>-->
<!--                        <a href="/tables">-->
<!--                            <span class="uk-icon uk-margin-small-right" uk-icon="icon: grid"></span>-->
<!--                            Table Mode-->
<!--                        </a>-->
<!--                    </li>-->
                </ul>


            </div>

        </div>
    </div>
</nav>
<?php $uid = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : '';  ?>
<input type="hidden" id="site_uid" name="site_uid" value="<?php echo get_uri_part(2); ?>" />
<input type="hidden" id="m_user_id" name="m_user_id" value="<?php echo $uid; ?>" />
<input type="hidden" id="m_room_id" name="m_room_id" value="" />
<input type="hidden" id="m_floor_id" name="m_floor_id" value="" />
<input type="hidden" id="m_building_id" name="m_building_id" value="" />
<input type="hidden" id="m_location_id" name="m_location_id" value="" />
<input type="hidden" id="m_project_id" name="m_project_id" value="<?php echo get_uri_part(2); ?>" />


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