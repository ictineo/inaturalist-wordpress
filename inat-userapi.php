<?php

function theme_add_user () {
  $output = '
<form accept-charset="UTF-8" id="inat-login-user-add" method="post" action="'.plugins_url('adduser.php',__FILE__).'">
  <div>
    <div class="form-item form-type-textfield form-item-inat-login-usradd-login">
      <label for="edit-inat-login-usradd-login">'.__('User login','inat').' <span title="This field is required." class="form-required">*</span></label>
      <input type="text" class="form-text required" maxlength="128" size="60" placeholder="'.__('User login','inat').'" name="inat_login_usradd_login" id="edit-inat-login-usradd-login">
    </div>
    <div class="form-item form-type-textfield form-item-inat-login-usradd-email">
      <label for="edit-inat-login-usradd-email">'.__('User email','inat').' <span title="This field is required." class="form-required">*</span></label>
      <input type="text" class="form-text required" maxlength="128" size="60" placeholder="'.__('User email', 'inat').'" name="inat_login_usradd_email" id="edit-inat-login-usradd-email">
    </div>
    <div class="form-item form-type-password form-item-inat-login-usradd-pwd">
      <label for="edit-inat-login-usradd-pwd">'.__('Password','inat').' <span title="This field is required." class="form-required">*</span></label>
      <input type="password" class="form-text required" maxlength="128" size="60" name="inat_login_usradd_pwd" id="edit-inat-login-usradd-pwd">
    </div>
    <div class="form-item form-type-password form-item-inat-login-usradd-pwdc">
      <label for="edit-inat-login-usradd-pwdc">'.__('Repeat password', 'inat').' <span title="This field is required." class="form-required">*</span></label>
      <input type="password" class="form-text required" maxlength="128" size="60" name="inat_login_usradd_pwdc" id="edit-inat-login-usradd-pwdc">
    </div>
    <div class="form-item form-type-textarea form-item-inat-login-usradd-desc">
      <label for="edit-inat-login-usradd-desc">'.__('User description', 'inat').' <span title="This field is required." class="form-required">*</span></label>
      <div class="form-textarea-wrapper resizable textarea-processed resizable-textarea"><textarea class="form-textarea required" rows="5" cols="60" name="inat_login_usradd_desc" id="edit-inat-login-usradd-desc">
      </textarea>
    </div>
  </div>
  <input type="hidden" name="inat_base_url" value="'.get_option('inat_base_url').'" />
  <input type="hidden" name="inat_login_id" value="'.get_option('inat_login_id').'" />
  <input type="hidden" name="site_url" value="'.site_url().'" />
  <input type="hidden" name="inat_login_callback" value="'.get_option('inat_login_callback').'" />
  <input type="hidden" name="inat_post_id" value="'.get_option('inat_post_id').'" />
  <div id="edit-actions" class="form-actions form-wrapper"><input type="submit" class="form-submit" value="'.__('Create user','inat').'" name="op" id="edit-submit">
  </div>
</div>
</form>
    ';
  return $output;
}
