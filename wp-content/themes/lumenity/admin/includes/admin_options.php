<?php
/* Admin fields */
class askme_admin_fields_class
{

	/**
	 * Generates the tabs that are used in the options menu
	 */
	static function askme_admin_tabs($page = "options", $options_arrgs = array(), $post_id = "")
	{
		$counter = 0;
		$options = $options_arrgs;
		if (empty($options_arrgs)) {
			$options = &askme_admin::_askme_admin_options($page);
		}
		if (isset($options) && is_array($options) && !empty($options)) {
			$menu = $class = '';
			$wp_page_template = ($page == "meta" && isset($post_id) ? get_post_meta($post_id, "_wp_page_template", true) : "");
			foreach ($options as $value) {
				// Heading for Navigation
				if (isset($value['type']) && $value['type'] == "heading") {
					$counter++;
					$class = !empty($value['id']) ? $value['id'] : $value['name'];
					$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class)) . '-tab';
					if (!array_key_exists('template', $value) || !is_string($value['template'])) {
						$value['template'] = '';
					}
					$template = empty($value['template']) ? '' : ' data-template="' . esc_attr($value['template']) . '"';
					if (isset($value['template']) && $value['template'] != "" && $value['template'] != $wp_page_template) {
						$class .= ' hide';
					}
					$menu .= '<a' . $template . ' id="options-group-' .  $counter . '-tab" class="nav-tab ' . $class . '" title="' . esc_attr($value['name']) . '" href="' . esc_attr('#options-group-' .  $counter) . '">' . (isset($value['icon']) && $value['icon'] != '' ? '<span class="dashicons dashicons-' . esc_attr($value['icon']) . '"></span>' : '') . '<span class="options-name' . (isset($value['new']) && $value['new'] != "" ? ' options-name-new' : '') . '">' . esc_html($value['name']) . (isset($value['new']) && $value['new'] != '' ? '<span>' . esc_html__('New', 'vbegy') . '</span>' : '') . '</span></a>';
				}
			}
			return $menu;
		}
	}

	/**
	 * Generates the options fields that are used in the form.
	 */
	static function askme_admin_fields($settings = array(), $option_name = "", $page = "options", $post_term = null, $options_arrgs = array())
	{

		askme_options_fields($settings, $option_name, $page, $post_term, $options_arrgs);

		// Outputs closing div if there tabs
		if ($page == "options" || $page == "meta") {
		}
	}
}
/* Admin class */
class askme_admin
{
	static function &_askme_admin_options($page = "options")
	{
		static $options = null;
		if (!$options) {
			// Load options from options.php file (if it exists)
			if ($optionsfile = get_template_directory() . "/admin/" . $page . ".php") {
				$maybe_options = require_once $optionsfile;
				if (is_array($maybe_options)) {
					$options = $maybe_options;
				} else if ($page == "widgets" && function_exists('askme_admin_widgets')) {
					$options = askme_admin_widgets();
				} else if ($page == "term" && function_exists('askme_admin_terms')) {
					$options = askme_admin_terms();
				} else if ($page == "meta" && function_exists('askme_admin_meta')) {
					$options = askme_admin_meta();
				} else if ($page == "options" && function_exists('askme_admin_options')) {
					$options = askme_admin_options();
				}
			}
			// Allow setting/manipulating options via filters
			$options = apply_filters('askme_' . $page, $options);
		}
		return $options;
	}
}

/* Admin options */
class askme_admin_options
{

	/**
	 * Page hook for the options screen
	 *
	 * @since 1.7.0
	 * @type string
	 */
	protected $options_screen = null;

	/**
	 * Hook in the scripts and styles
	 *
	 * @since 1.7.0
	 */
	public function init()
	{

		// Gets options to load
		$options = askme_optionsframework_options();

		// Checks if options are available
		if ($options) {

			// Add the options page and menu item.
			add_action('admin_menu', array($this, 'vpanel_add_admin'), 10);

			// Settings need to be registered after admin_init
			add_action('admin_init', array($this, 'settings_init'));
		}
	}

	/**
	 * Registers the settings
	 *
	 * @since 1.7.0
	 */
	function settings_init()
	{
		// Load Options Framework Settings
		//update_option( "optionsframework",array("id" => "ask") );
		$optionsframework_settings = get_option(askme_options);

		// Registers the settings fields and callback
		register_setting("vpanel", askme_options,  array($this, 'validate_options'));

		// Displays notice after options save
		add_action('optionsframework_after_validate', array($this, 'save_options_notice'));
	}

	/*
	 * Define menu options (still limited to appearance section)
	 *
	 * Examples usage:
	 *
	 * add_filter( 'optionsframework_menu', function( $menu ) {
	 *     $menu['page_title'] = 'The Options';
	 *	   $menu['menu_title'] = 'The Options';
	 *     return $menu;
	 * });
	 *
	 * @since 1.7.0
	 *
	 */
	/**
	 * Add a subpage called "Theme Options" to the appearance menu.
	 *
	 * @since 1.7.0
	 */
	function vpanel_add_admin()
	{
		$support_activate = askme_updater()->is_active();
		if ($support_activate) {
			add_menu_page('Ask Me Settings', 'Ask Me', 'install_themes', 'options', array($this, 'options_page'), "dashicons-admin-site");
		}

		// Load the required CSS and javscript
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
	}

	/**
	 * Loads the required stylesheets
	 *
	 * @since 1.7.0
	 */
	function enqueue_admin_styles($hook)
	{
		$enqueue = false;
		$hook_arrs = array(
			'widgets.php',
			'post.php',
			'post-new.php',
			'term.php',
			'edit-tags.php',
			'toplevel_page_options',
			'toplevel_page_registration',
			'ask-me_page_registration',
			'toplevel_page_r_questions',
			'reports_page_r_answers',
			'reports-1_page_r_answers',
			'reports_page_r_users',
			'reports-1_page_r_users',
			'reports-2_page_r_users',
			'reports-3_page_r_users',
			'ask-me_page_demo-import',
			'toplevel_page_ask_payments',
			'profile.php',
			'user-new.php',
			'user-edit.php',
			'edit-comments.php',
			'_registration-apps'
		);
		$hook_arrs = apply_filters("ask_me_hook_arr", $hook_arrs);
		foreach ($hook_arrs as $hook_arr) {
			if ($hook_arr == $hook || strpos($hook, $hook_arr) !== false) {
				$enqueue = true;
			}
		}

		if ($hook == 'post.php') {
			global $post;
			$post_type = $post->post_type;
		} else if (array_key_exists('post_type', $_GET)) {
			$post_type = $_GET['post_type'];
		}

		$allow_post_type = apply_filters("ask_me_allow_post_type", array('post', 'page', ask_questions_type, ask_asked_questions_type, 'product'));
		if (isset($post_type) && !in_array($post_type, $allow_post_type)) {
			$enqueue = false;
		}

		if (($hook == 'edit.php' && array_key_exists('post_type', $_GET) && $_GET['post_type'] == 'posts')) {
			$enqueue = true;
		}

		if ($enqueue) {
			wp_enqueue_style('wp-color-picker');
			if (is_rtl()) {
				wp_enqueue_style("vpanel_style_css", askme_framework_dir . 'vpanel_style/vpanel_style_ar.css', array(), askme_theme_version);
				wp_enqueue_style('vpanel', askme_framework_dir . 'css/optionsframework-ar.css', array(), askme_theme_version);
			} else {
				wp_enqueue_style("vpanel_style", askme_framework_dir . 'vpanel_style/vpanel_style.css', array(), askme_theme_version);
				wp_enqueue_style('vpanel', askme_framework_dir . 'css/optionsframework.css', array(), askme_theme_version);
			}
		}
	}

	/**
	 * Loads the required javascript
	 *
	 * @since 1.7.0
	 */
	function enqueue_admin_scripts()
	{
		$categories_obj = get_categories('hide_empty=0');
		$categories = array();
		foreach ($categories_obj as $pn_cat) {
			$categories[$pn_cat->cat_ID] = $pn_cat->cat_name;
		}
		wp_enqueue_script("jquery-ui-datepicker");
		wp_enqueue_script('options-custom', askme_framework_dir . 'js/options-custom.js', array('jquery', 'wp-color-picker', 'jquery-ui-datepicker'), array(), askme_theme_version);
		wp_enqueue_script("admin-ajax", askme_framework_dir . 'js/admin-ajax.js', array('jquery'), array(), askme_theme_version);

		$askme_js = array(
			"ajax_a"                    => admin_url("admin-ajax.php"),
			"confirm_reset"             => esc_html__("Click OK to reset. Any theme settings will be lost.", "vbegy"),
			"confirm_delete"            => esc_html__("Are you sure you want to delete?", "vbegy"),
			"confirm_reports"           => esc_html__("If you press the report will be deleted!", "vbegy"),
			"confirm_delete_attachment" => esc_html__("If you press the attachment will be deleted!", "vbegy"),
			"insert_image"              => esc_html__("Insert Image", "vbegy"),
			"error_uploading_image"     => esc_html__("Attachment Error! Please upload image only.", "vbegy"),
		);
		wp_localize_script("admin-ajax", "admin_ajax", $askme_js);
		wp_enqueue_script("fontselect", askme_framework_dir . 'js/jquery.fontselect.js', array('jquery'));
		wp_enqueue_script("vbegy_more", askme_framework_dir . 'js/more.js', array('jquery'), array(), askme_theme_version);
		$askme_js = array(
			"vpanel_name" => "vpanel_" . vpanel_name,
		);
		wp_localize_script("vbegy_more", "more_ajax", $askme_js);
		wp_enqueue_script("builder_admin", askme_framework_dir . 'js/builder.js', array('jquery', 'jquery-ui-sortable'), array(), askme_theme_version);
		$askme_js = array(
			"ajax_a"       => admin_url("admin-ajax.php"),
			"choose_image" => esc_html__("Choose Image", "vbegy"),
			"edit_image"   => esc_html__("Edit", "vbegy"),
			"upload_image" => esc_html__("Upload", "vbegy"),
			"remove_image" => esc_html__("Remove", "vbegy"),
			"ask_theme"    => askme_options,
			"builder_on"   => esc_html__("ON", "vbegy"),
			"builder_off"  => esc_html__("OFF", "vbegy"),
			"categories"   => $categories,
		);
		wp_localize_script("builder_admin", "builder_ajax", $askme_js);
		wp_enqueue_script("vbegy_tipsy.js", askme_framework_dir . 'js/jquery.tipsy.js', array('jquery'));
		add_action('admin_head', array($this, 'of_admin_head'));
	}

	function of_admin_head()
	{
		// Hook to add custom scripts
		do_action('optionsframework_custom_scripts');
	}

	/**
	 * Builds out the options panel.
	 *
	 * If we were using the Settings API as it was intended we would use
	 * do_settings_sections here.  But as we don't want the settings wrapped in a table,
	 * we'll call our own custom optionsframework_fields.  See options-interface.php
	 * for specifics on how each individual field is generated.
	 *
	 * Nonces are provided using the settings_fields()
	 *
	 * @since 1.7.0
	 */
	function options_page()
	{
		do_action('askme_options_page'); ?>
		<div id="optionsframework-wrap">
			<?php if (!function_exists('mobile_api_options') && !function_exists('mobile_options')) { ?>
				<a class="app-img" href="https://2code.info/checkout/pay_for_apps/33664/?theme=askme" target="_blank"><img alt="Ask Me Mobile Application" src="https://drive.2code.info/discount/960x100-askme.png"></a>
				<section id="footer_call_to_action" class="gray_section call_to_action">
					<div class="container main_content_area">
						<div class="row section">
							<div class="section_container col col12">
								<div class="section_inner_container">
									<div class="row section_inner">
										<div class="col col7">
											<div class="main_section_left_title main_section_title">Test Application!</div>
											<div class="main_section_left_content main_section_content">Test Ask Me application demo
												on Google Play and App Store.</div>
										</div>
										<div class="col col5">
											<div class="row">
												<div class="col col6 col-app">
													<a target="_blank" title="Download Android App" href="https://play.google.com/store/apps/details?id=com.askme.application">
														<img alt="Play Store" src="https://2code.info/mobile/google_play.png">
													</a>
												</div>
												<div class="col col6 col-app">
													<a target="_blank" href="https://apps.apple.com/app/ask-me-application/id1542559413" title="Download IOS App">
														<img alt="App Store" src="https://2code.info/mobile/app_store.png">
													</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
			<?php } ?>
			<form action="options.php" id="main_options_form" method="post">
				<div class="optionsframework-header">
					<a href="<?php echo askme_theme_url_tf ?>" target="_blank"></a>
					<input type="submit" class="button-primary vpanel_save" name="update" value="<?php esc_attr_e('Save Options', 'vbegy'); ?>">
					<div class="vpanel_social">
						<ul>
							<li><a class="vpanel_social_f" href="https://www.facebook.com/2code.info" target="_blank"><i class="dashicons dashicons-facebook"></i></a></li>
							<li><a class="vpanel_social_t" href="https://www.twitter.com/2codeThemes" target="_blank"><i class="dashicons dashicons-twitter"></i></a></li>
							<li><a class="vpanel_social_e" href="https://2code.info/" target="_blank"><i class="dashicons dashicons-email-alt"></i></a></li>
							<li><a class="vpanel_social_s" href="https://2code.info/demo/themes/ask-me/Docs/" target="_blank"><i class="dashicons dashicons-sos"></i></a></li>
						</ul>
					</div>
					<div class="clear"></div>
				</div>
				<div class="optionsframework-content">
					<h2 class="nav-tab-wrapper">
						<?php echo askme_admin_fields_class::askme_admin_tabs(); ?>
					</h2>
					<?php settings_errors('options-framework'); ?>
					<div id="optionsframework-metabox" class="metabox-holder">
						<div id="optionsframework" class="postbox">
							<?php askme_admin_fields_class::askme_admin_fields();
							wp_nonce_field('saving_nonce', 'saving_nonce', true, true) ?>
							<div id="ajax-saving"><i class="dashicons dashicons-yes"></i><?php _e("Saving", "vbegy") ?></div>
							<div id="ajax-reset"><i class="dashicons dashicons-info"></i><?php _e("Reseting Options", "vbegy") ?>
							</div>
							<div id="ajax-load"><i class="dashicons dashicons-info"></i><?php esc_html_e("Loading the page and reclick on the button again", "vbegy") ?>
							</div>
						</div> <!-- / #container -->
					</div>
					<?php do_action('optionsframework_after'); ?>
					<div class="clear"></div>
				</div>
				<div class="optionsframework-footer">
					<input type="submit" class="button-primary vpanel_save" name="update" value="<?php esc_attr_e('Save Options', 'vbegy'); ?>">
					<div id="loading"></div>
					<input type="submit" class="reset-button button-secondary" id="reset_c" name="reset" value="<?php esc_attr_e('Restore Defaults', 'vbegy'); ?>">
					<div class="clear"></div>
				</div>
			</form>
		</div> <!-- / .wrap -->
<?php
	}

	/**
	 * Validate Options.
	 *
	 * This runs after the submit/reset button has been clicked and
	 * validates the inputs.
	 *
	 * @uses $_POST['reset'] to restore default options
	 */
	function validate_options($input)
	{

		/*
		 * Restore Defaults.
		 *
		 * In the event that the user clicked the "Restore Defaults"
		 * button, the options defined in the theme's options.php
		 * file will be added to the option for the active theme.
		 */

		if (isset($_POST['reset'])) {
			add_settings_error('options-framework', 'restore_defaults', __('Default options restored.', 'vbegy'), 'updated fade');
			return $this->get_default_values();
		}

		/*
		 * Update Settings
		 *
		 * This used to check for $_POST['update'], but has been updated
		 * to be compatible with the theme customizer introduced in WordPress 3.4
		 */

		$clean = array();
		$options = askme_optionsframework_options();
		foreach ($options as $option) {

			if (!isset($option['id'])) {
				continue;
			}

			if (!isset($option['type'])) {
				continue;
			}

			$id = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($option['id']));

			// Set checkbox to false if it wasn't sent in the $_POST
			if ('checkbox' == $option['type'] && !isset($input[$id])) {
				$input[$id] = false;
			}

			// Set each item in the multicheck to false if it wasn't sent in the $_POST
			if ('multicheck_2' == $option['type'] && 'multicheck' == $option['type'] && 'multicheck_sort' == $option['type'] && !isset($input[$id])) {
				foreach ($option['options'] as $key => $value) {
					$input[$id][$key] = 0;
				}
			}

			// For a value to be submitted to database it must pass through a sanitization filter
			if (isset($input[$id]) && has_filter('of_sanitize_' . $option['type'])) {
				$clean[$id] = apply_filters('of_sanitize_' . $option['type'], $input[$id], $option);
			}
		}

		// Hook to run after validation
		do_action('optionsframework_after_validate', $clean);

		return $clean;
	}

	/**
	 * Display message when options have been saved
	 */

	function save_options_notice()
	{
		add_settings_error('options-framework', 'save_options', __('Options saved.', 'vbegy'), 'updated fade');
	}

	/**
	 * Get the default values for all the theme options
	 *
	 * Get an array of all default values as set in
	 * options.php. The 'id','std' and 'type' keys need
	 * to be defined in the configuration array. In the
	 * event that these keys are not present the option
	 * will not be included in this function's output.
	 *
	 * @return array Re-keyed options configuration array.
	 *
	 */

	function get_default_values()
	{
		$output = array();
		$config = askme_optionsframework_options();
		foreach ((array) $config as $option) {
			if (!isset($option['id'])) {
				continue;
			}
			if (!isset($option['std'])) {
				continue;
			}
			if (!isset($option['type'])) {
				continue;
			}
			if (has_filter('of_sanitize_' . $option['type'])) {
				$output[$option['id']] = apply_filters('of_sanitize_' . $option['type'], $option['std'], $option);
			}
		}
		return $output;
	}
}
