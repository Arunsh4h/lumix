<?php // If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Don't load if askme_admin_init is already defined
if (is_admin() && ! function_exists( 'askme_admin_init' ) ) :
	function askme_admin_init() {
		// Loads the required Options Framework classes.
		require_once locate_template("admin/includes/admin_options.php");
		require_once locate_template("admin/includes/options-media-uploader.php");
		require_once locate_template("admin/includes/options_sanitization.php");
		require_once locate_template("admin/option.php");
	
		// Instantiate the main class.
		$askme_admin = new askme_admin;
		// Instantiate the options page.
		$askme_admin_options = new askme_admin_options;
		$askme_admin_options->init("options");

		// Instantiate the media uploader class
		$askme_media_uploader = new askme_media_uploader;
		$askme_media_uploader->init();
	
	}
	add_action( 'init', 'askme_admin_init', 20 );
	if (strpos($_SERVER['REQUEST_URI'],'page=options') === false) {
		add_action( 'current_screen', 'askme_admin_init' );
	}
endif;