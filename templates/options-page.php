<div class="wrap">
    <h1><?php echo __('Social3 Connection Settings', 'social3'); ?></h1>

    <?php if (empty($access_token)) : ?>
        <p>Register on the site <a href="https://social3.io/" target="_blank">Social3</a>, if you do not yet have an account
            on Social3. Next, use the form below to connect the plugin with Social3.</p>

        <form method="post" id="connect_form">
            <?php wp_nonce_field('s3_app_login_action', 's3_app_login_action_nonce') ?>
            <table class="form-table">
                <th class="row"><?php echo __('Use API key:', 'social3'); ?></th>
                <td>
                    <label for="s3_user_use_key">
                        <input name="s3_user[use_key]" type="checkbox" id="s3_user_use_key" value="1" >
                        <?php echo __('Enable', 'social3'); ?>
                    </label>
                </td>
                <tr class="api_key" style="display: none;">
                    <th class="row"><?php echo __('API key:', 'social3'); ?></th>
                    <td>
                        <input name="s3_user[api_key]" value="" type="text" class="regular-text" disabled="disabled"/>
                    </td>
                </tr>
                <tr class="login">
                    <th class="row"><?php echo __('Email:', 'social3'); ?></th>
                    <td>
                        <input name="s3_user[email]" value="" type="email" class="regular-text"/>
                    </td>
                </tr>
                <tr class="login">
                    <th class="row"><?php echo __('Password:', 'social3'); ?></th>
                    <td>
                        <input name="s3_user[password]" value="" type="password" class="regular-text"/>
                    </td>
                </tr>
            </table>
            <button class="button button-primary" type="submit"><?php echo __('Connect', 'social3'); ?></button>
        </form>
        <script>
            ( function ( $ ) {
                "use strict";

                $('#s3_user_use_key').on( 'change', function() {
                    if ($(this).prop('checked')) {
                        $('.api_key').show().find('input').prop('disabled', false);
                        $('.login').hide().find('input').prop('disabled', true);
                    } else {
                        $('.api_key').hide().find('input').prop('disabled', true);
                        $('.login').show().find('input').prop('disabled', false);
                    }
                });
            }( jQuery ) );
        </script>
    <?php else : ?>
        <p><b>You are connected to brand "<?php echo $s3_brand_name; ?>".</b></p>
        <p>If you want to change your account, you must first disconnect.</p>

        <form method="post">
            <?php wp_nonce_field('s3_app_logout_action', 's3_app_logout_action_nonce') ?>
            <button class="button" type="submit"><?php echo __('Disconnect', 'social3'); ?></button>
        </form>

        </br>
        <h1><?php echo __('Social3 Integration', 'social3'); ?></h1>
        <form method="post" id="settings_form">
            <?php wp_nonce_field('s3_app_integration_action', 's3_app_integration_action_nonce') ?>
            <table class="form-table">
                <tr>
                    <th class="row"><?php echo __('Include Script:', 'social3'); ?></th>
                    <td>
                        <label for="s3_integration_status">
                            <input name="s3_integration_status" type="checkbox" id="s3_integration_status" value="1"
                                <?php echo ($s3_integration_status) ? 'checked="checked"': ''; ?>
                            >
                            <?php echo __('Enable', 'social3'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th class="row"><?php echo __('Move integration script to footer:', 'social3'); ?></th>
                    <td>
                        <label for="s3_integration_in_footer">
                            <input name="s3_integration_in_footer" type="checkbox" id="s3_integration_in_footer" value="1"
                                <?php echo ($s3_integration_in_footer) ? 'checked="checked"': ''; ?>
                            >
                            <?php echo __('Enable', 'social3'); ?>
                        </label>
                    </td>
                </tr>
            </table>
            <button class="button button-primary" type="submit"><?php echo __('Save', 'social3'); ?></button>
        </form>
    <?php endif; ?>
</div>

