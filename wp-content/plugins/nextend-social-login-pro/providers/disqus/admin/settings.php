<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProviderAdmin */

$provider = $this->getProvider();

$settings = $provider->settings;
?>

<div class="nsl-admin-sub-content">
    <?php
    $this->renderSettingsHeader();
    ?>

    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" novalidate="novalidate">

        <?php wp_nonce_field('nextend-social-login'); ?>
        <input type="hidden" name="action" value="nextend-social-login"/>
        <input type="hidden" name="view" value="provider-<?php echo $provider->getId(); ?>"/>
        <input type="hidden" name="subview" value="settings"/>
        <input type="hidden" name="settings_saved" value="1"/>
        <input type="hidden" name="tested" id="tested" value="<?php echo esc_attr($settings->get('tested')); ?>"/>
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><label for="api_key"><?php _e('API Key', 'nextend-facebook-connect'); ?>
                        - <em>(<?php _e('Required', 'nextend-facebook-connect'); ?>)</em></label>
                </th>
                <td>
                    <input name="api_key" type="text" id="api_key"
                           value="<?php echo esc_attr($settings->get('api_key')); ?>" class="regular-text">
                    <p class="description"
                       id="tagline-api_key"><?php printf(__('If you are not sure what is your %1$s, please head over to <a href="%2$s">Getting Started</a>', 'nextend-facebook-connect'), 'API Key', $this->getUrl()); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label
                            for="api_secret"><?php _e('API Secret', 'nextend-facebook-connect'); ?>
                        - <em>(<?php _e('Required', 'nextend-facebook-connect'); ?>)</em></label></th>
                <td><input name="api_secret" type="text" id="api_secret"
                           value="<?php echo esc_attr($settings->get('api_secret')); ?>" class="regular-text">
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                                 value="<?php _e('Save Changes'); ?>"></p>

        <?php
        $this->renderOtherSettings();

        $this->renderProSettings();
        ?>
    </form>
</div>