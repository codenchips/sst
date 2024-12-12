<?php
$p = get_user();
?>

<div id="account" class="uk-width-1-1">

    <div class="uk-width-1-1" uk-grid>
        <div class="uk-width-1-1 uk-text-left">
            <h3 class="uk-card-title">Your details</h3>
        </div>


        <div class="uk-width-1-2 uk-text-left uk-margin">
            <label>Name</label>
            <input id="form_name"
                   name="form_name"
                   class="uk-input free-type auto-update set-cookie"
                   data-id="<?php echo $p->id; ?>"
                   data-tbl="sst_users"
                   data-col="name"
                   data-cookie="user_name"
                   placeholder="Your name"
                   autocomplete="off"
                   maxlength="40"
                   required
                   value="<?php echo $p->name; ?>"
                   oninvalid="this.setCustomValidity('You must enter a name')"
                   oninput="this.setCustomValidity('')" />
        </div>
        <div class="uk-width-1-2 uk-text-left uk-margin">
            <label>Email</label>
            <input id="form_email"
                   name="form_email"
                   type="email"
                   class="uk-input free-type auto-update"
                   data-id="<?php echo $p->id; ?>"
                   data-tbl="sst_users"
                   data-col="email"
                   placeholder="Your email address"
                   autocomplete="off"
                   pattern=".+@tamlite\.co\.uk"
                   maxlength="40"
                   required
                   value="<?php echo $p->email; ?>"
                   oninvalid="this.setCustomValidity('You must enter an email address')"
                   oninput="this.setCustomValidity('.+@tamlite\.co\.uk')" />
        </div>
        <div class="uk-width-1-2 uk-text-left uk-margin">
            <label>Area Code</label>
            <input id="form_code"
                   name="form_code"
                   class="uk-input free-type auto-update"
                   data-id="<?php echo $p->id; ?>"
                   data-tbl="sst_users"
                   data-col="code"
                   maxlength="9"
                   placeholder="Your area code"
                   autocomplete="off"
                   value="<?php echo $p->code; ?>"
                   oninvalid="this.setCustomValidity('Enter a name')"
                   oninput="this.setCustomValidity('')" />
        </div>
        <div class="uk-width-1-2 uk-text-left uk-margin">
            <label>Password</label>
            <input id="form_password"
                   type="password"
                   name="form_password1"
                   class="uk-input free-type auto-update"
                   data-id="<?php echo $p->id; ?>"
                   data-tbl="sst_users"
                   data-col="password"
                   placeholder="PAssword"
                   autocomplete="off"
                   maxlength="40"
                   required
                   value="<?php echo $p->password; ?>"
                   oninvalid="this.setCustomValidity('You enter a password')"
                   oninput="this.setCustomValidity('')" />
        </div>



    </div>
</div>
