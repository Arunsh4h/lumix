<?php
/* ElitePack Theme Updater */

/* Access the object */
function askme_updater()
{
	global $askme_updater;
	if (!isset($askme_updater)) {
		// Include ElitePack SDK.
		include(dirname(__FILE__) . '/ep-updater-admin-class.php');
		// Loads the updater classes
		$askme_updater = new ElitePack_Theme_Updater_Admin(
			// Config settings
			$config = array(
				'api_url'          => 'https://intself.com/support/',
				'item_id'          => '7935874',
				'name'             => 'Lumeno - Responsive Questions & Answers WordPress',
				'version'          => askme_theme_version,
				'capability'       => 'manage_options',
				'notice_pages'     => false,
				'redirect_url'     => add_query_arg(array('page' => 'registration'), admin_url('admin.php')),
				'theme_folder'     => 'ask-me',
			),
			// Strings
			$strings = array(
				'purchase-license'            => esc_html__('Purchase a License', 'vbegy'),
				'renew-support'               => esc_html__('Renew Support', 'vbegy'),
				'need-help'                   => esc_html__('Need Help?', 'vbegy'),
				'try-again'                   => esc_html__('Try Again', 'vbegy'),
				'register-item'               => esc_html__('Register %s', 'vbegy'),
				'register-message'            => esc_html__('Thank you for choosing %s! Your product must be registered to see the theme options, import our awesome demos, install bundled plugins, receive automatic updates, and access to support.', 'vbegy'),
				'register-button'             => esc_html__('Register Now!', 'vbegy'),
				'register-success-title'      => esc_html__('Congratulations', 'vbegy'),
				'register-success-text'       => esc_html__('Your License is now registered!, theme options, demo import, and bundeled plugins is now unlocked.', 'vbegy'),
				'api-error'                   => esc_html__('An error occurred, please try again.', 'vbegy'),
				'inline-register-item-notice' => esc_html__('Register the theme to unlock automatic updates.', 'vbegy'),
				'inline-renew-support-notice' => esc_html__('Renew your support to unlock automatic updates.', 'vbegy'),
				'date-at-time'                => esc_html__('%1$s at %2$s', 'vbegy'),
				'support-expiring-notice'     => esc_html__('Your support will Expire in %s. Renew your license today and save 25%% to keep getting auto updates and premium support, remember purchasing a new license extends support for all licenses.', 'vbegy'),
				'support-update-failed'       => esc_html__('Failed, try again later.', 'vbegy'),
				'support-not-updated'         => esc_html__('Did not updated, Your support expires on %s', 'vbegy'),
				'support-updated'             => esc_html__('Updated successfully, your support expires on %s', 'vbegy'),
				'update-available'            => esc_html__('There is a new version of %1$s available.', 'vbegy'),
				'update-available-changelog'  => esc_html__('There is a new version of %1$s available, %2$sView version %3$s details%4$s.', 'vbegy'),
				'update-now'                  => esc_html__('update now', 'vbegy'),
				'revoke-license-success'      => esc_html__('License Deactivated Succefuly.', 'vbegy'),
				'cancel'                      => esc_html__('Cancel', 'vbegy'),
				'skip'                        => esc_html__('Skip & Switch', 'vbegy'),
				'send'                        => esc_html__('Send & Switch', 'vbegy'),
				'feedback'                    => esc_html__('%s feedback', 'vbegy'),
				'deactivation-share-reason'   => esc_html__('May we have a little info about why you are switching?', 'vbegy'),
			)
		);
	}
	return $askme_updater;
}

askme_updater();

/* Disable the notices in the Registeration page */
add_filter('ask-me/notice/show', 'askme_updater_notices', 10, 2);
function askme_updater_notices($status, $id)
{
	if (get_current_screen()->id == 'theme-options_page_options') {
		if ($id == 'ask-me-activated' || $id == 'ask-me-activation-notice') {
			return false;
		}
	}
	return $status;
}
/* Add the Registeration custom page */
add_action('admin_menu', 'askme_registeration_menu', 13);
function askme_registeration_menu()
{
	$support_activate = askme_updater()->is_active();
	if ($support_activate) {
		add_submenu_page('options', 'Registration', 'Registration', 'manage_options', 'registration', 'askme_registeration_page');
	} else {
		add_menu_page('Register Lumeno', 'Register Lumeno', 'manage_options', 'registration', 'askme_registeration_page', 'dashicons-admin-site');
	}
}
/* The registeration page content */
function askme_registeration_page()
{
	$support_activate = askme_updater()->is_active();
	if ($support_activate) {
		$intro = esc_html__('Thank you for choosing askme! Your product is already registered, so you have access to:', 'vbegy');
		$title = esc_html__('Congratulations! Your product is registered now.', 'vbegy');
		$icon  = 'yes';
		$class = 'is-registered';
	} else {
		$intro = esc_html__('Thank you for choosing askme! Your product must be registered to:', 'vbegy');
		$title = esc_html__('Click on the button below to begin the registration process.', 'vbegy');
		$icon  = 'no';
		$class = 'not-registered';
	}
	$foreach = array(
		"admin-site"       => esc_html__('See the theme options', 'vbegy'),
		"admin-appearance" => esc_html__('Import our awesome demos', 'vbegy'),
		"admin-plugins"    => esc_html__('Install the included plugins', 'vbegy'),
		"update"           => esc_html__('Receive automatic updates', 'vbegy'),
		"businessman"      => esc_html__('Access to support', 'vbegy')
	); ?>
	<div id="framework-registration-wrap" class="framework-demos-container <?php echo esc_attr($class) ?>">
		<div class="framework-dash-container framework-dash-container-medium">
			<div class="postbox">
				<h2><span><?php echo esc_html__('Welcome to', 'vbegy') . ' ' . theme_name . '!'; ?></span></h2>
				<div class="inside">
					<div class="main">
						<h3 class="framework-dash-margin-remove"><span class="dashicons dashicons-<?php echo esc_attr($icon); ?> library-icon-key"></span> <?php echo ($title); ?></h3>
						<p class="framework-dash-text-lead"><?php echo ($intro); ?></p>
						<ul>
							<?php foreach ($foreach as $icon => $item) {
								if ($icon == "admin-site") {
									$link = admin_url('admin.php?page=options');
								} else if ($icon == "admin-appearance") {
									$link = admin_url('admin.php?page=demo-import');
								} else if ($icon == "businessman") {
									$link = 'https://intself.com/support/';
								} else {
									$link = "";
								} ?>
								<li><i class="dashicons dashicons-<?php echo esc_attr($icon) ?>"></i><?php echo ($link != "" ? "<a target='_blank' href='" . $link . "'>" : "") . ($item) . ($link != "" ? "</a>" : "") ?></li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="community-events-footer">
					<?php if (!$support_activate) { ?>
						<div class="framework-registration-wrap">
							<a href="<?php echo askme_updater()->activate_link(); ?>" class="button button-primary"><?php esc_html_e('Register Now!', 'vbegy'); ?></a>
							<a href="<?php echo askme_updater()->purchase_url(); ?>" class="button" target="_blank"><?php esc_html_e('Purchase a License', 'vbegy'); ?></a>
						</div>
					<?php } else { ?>
						<div class="framework-support-status framework-support-status-active">
							<?php esc_html_e('License Status:', 'vbegy'); ?> <span><?php esc_html_e('Active', 'vbegy') ?></span>
							<a class="button" href="<?php echo askme_updater()->deactivate_license_link() ?>"><?php esc_html_e('Revoke License', 'vbegy') ?></a>
						</div>
						<?php $support_info = askme_updater()->support_period_info();
						if (!empty($support_info['status'])) {
							switch ($support_info['status']) {
								case 'expiring':
									$support_message = sprintf(esc_html__('Expiring! will expire on %s', 'vbegy'), $support_info['date']);
									break;
								case 'active':
									$support_message = esc_html__('Active', 'vbegy');
									break;
								default:
									$support_message = esc_html__('Expired', 'vbegy');
									break;
							}
						}
						if (!empty($support_message)) { ?>
							<div class="framework-support-status framework-support-status-<?php echo ($support_info['status']) ?>">
								<?php esc_html_e('Support Status:', 'vbegy'); ?> <span><?php echo ($support_message) ?></span>
								<a class="button" href="<?php echo askme_updater()->refresh_support_expiration_link() ?>"><?php esc_html_e('Refresh Expiration Date', 'vbegy') ?></a>
							</div>
					<?php }
					} ?>
				</div>
			</div>
		</div><!-- framework-dash-container -->
	</div><!-- framework-demos-container -->
<?php
} ?>