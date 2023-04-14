<?php
update_option( 'ask-me_ep_license_status', 'valid' );
update_option( 'ask-me_ep_license_key', 'license_key' );

if (is_admin() and isset($_GET['activated']) and $pagenow == "themes.php") {
	wp_redirect('admin.php?page=registration');
}
define("askme_theme_version","6.9.1");
define('askme_framework_dir',get_template_directory_uri().'/admin/');

/* add title-tag */
add_theme_support('title-tag');

/* Theme name */
$themename = wp_get_theme();
$themename = preg_replace("/\W/", "_", strtolower($themename) );
define("vpanel_name","ask_me");
define("askme_options","vpanel_ask_me");
define("theme_name","Ask Me");
function ask_questions_type() {
	$ask_questions_type = apply_filters("ask_questions_type","question");
	if ($ask_questions_type == "" || $ask_questions_type == "post") {
		$ask_questions_type = "question";
	}
	return $ask_questions_type;
}
function ask_asked_questions_type() {
	$ask_asked_questions_type = apply_filters("ask_asked_questions_type","asked-question");
	if ($ask_asked_questions_type == "" || $ask_asked_questions_type == "post" || $ask_asked_questions_type == "question") {
		$ask_asked_questions_type = "asked-question";
	}
	return $ask_asked_questions_type;
}
function ask_question_category() {
	$ask_question_category = apply_filters("ask_question_category","question-category");
	if ($ask_question_category == "" || $ask_question_category == "category" || $ask_question_category == "post_tag") {
		$ask_question_category = "question-category";
	}
	return $ask_question_category;
}
function ask_question_tags() {
	$ask_question_tags = apply_filters("ask_question_tags","question_tags");
	if ($ask_question_tags == "" || $ask_question_tags == "category" || $ask_question_tags == "post_tag" || $ask_question_tags == ask_question_category()) {
		$ask_question_tags = "question_tags";
	}
	return $ask_question_tags;
}
if (!defined("ask_questions_type")) {
	define("ask_questions_type",ask_questions_type());
}
if (!defined("ask_asked_questions_type")) {
	define("ask_asked_questions_type",ask_asked_questions_type());
}
if (!defined("ask_question_category")) {
	define("ask_question_category",ask_question_category());
}
if (!defined("ask_question_tags")) {
	define("ask_question_tags",ask_question_tags());
}

/* Updater */
require_once get_template_directory().'/admin/updater/elitepack-config.php';

/* Require files */
require_once get_template_directory() . '/admin/plugins/class-tgm-plugin-activation.php';
require_once get_template_directory() . '/admin/plugins/plugins.php';
require_once get_template_directory() . '/admin/options-framework.php';
require_once get_template_directory() . '/admin/includes/fields.php';

if (is_admin() && (isset($_GET['page']) && $_GET['page'] == "options") && ($pagenow == "admin.php" || $pagenow == "themes.php")) {
	require_once get_template_directory() . '/admin/options.php';
}
$taxonomy_filter = apply_filters("askme_taxonomy_terms_option_filter",false);
if (is_admin() && (isset($_REQUEST['taxonomy']) && ($taxonomy_filter = true || $_REQUEST['taxonomy'] == "category" || $_REQUEST['taxonomy'] == ask_question_category)) && ($pagenow == "term.php" || $pagenow == "edit-tags.php" || (isset($_REQUEST["action"]) && $_REQUEST["action"] == "add-tag"))) {
	require_once get_template_directory() . '/admin/terms.php';
}
if (is_admin() && ($pagenow == "post-new.php" || ((isset($_REQUEST['post']) && $_REQUEST['post'] != "") || (isset($_REQUEST['post_ID']) && $_REQUEST['post_ID'] != "")) && ($pagenow == "post.php" || $pagenow == "themes.php"))) {
	require_once get_template_directory() . '/admin/meta.php';
}
if (is_admin() && ($pagenow == "profile.php" || (isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "") && $pagenow == "user-edit.php")) {
	//require_once get_template_directory() . '/admin/author.php';
}
require_once get_template_directory() . '/admin/functions/resizer.php';
require_once get_template_directory() . '/admin/functions/main_functions.php';
require_once get_template_directory() . '/admin/functions/widget_functions.php';
require_once get_template_directory() . '/admin/functions/nav_menu.php';
require_once get_template_directory() . '/admin/functions/menu.php';
require_once get_template_directory() . '/admin/functions/register_post.php';
require_once get_template_directory() . '/admin/functions/meta_setting.php';
require_once get_template_directory() . '/functions/mails.php';
require_once get_template_directory() . '/functions/shortcode_ask.php';
require_once get_template_directory() . '/functions/functions_ask.php';
require_once get_template_directory() . '/shortcodes/register.php';
require_once get_template_directory() . '/shortcodes/profile.php';
require_once get_template_directory() . '/shortcodes/question.php';
require_once get_template_directory() . '/shortcodes/post.php';
require_once get_template_directory() . '/functions/messages.php';

/* Woocommerce */
include get_template_directory() . '/admin/woocommerce/woocommerce.php';

/* Mobile */
require_once get_template_directory().'/admin/mobile/mobile-options.php';

/* Payments */
require_once get_template_directory().'/payments/payment.php';
require_once get_template_directory().'/payments/paypal.php';
require_once get_template_directory().'/payments/stripe.php';

/* Demo */
if (is_admin()) {
	require get_template_directory().'/admin/demos/one-click-demo-import/one-click-demo-import.php';
	require_once get_template_directory().'/admin/demos/demos.php';
}

/* Editor */
include get_template_directory() . '/editor/editor.php';

/* Widgets */
include get_template_directory() . '/admin/widgets/stats.php';
include get_template_directory() . '/admin/widgets/signup.php';
include get_template_directory() . '/admin/widgets/questions_categories.php';
include get_template_directory() . '/admin/widgets/counter.php';
include get_template_directory() . '/admin/widgets/contact.php';
include get_template_directory() . '/admin/widgets/login.php';
include get_template_directory() . '/admin/widgets/profile_links.php';
include get_template_directory() . '/admin/widgets/highest_points.php';
include get_template_directory() . '/admin/widgets/questions.php';
include get_template_directory() . '/admin/widgets/twitter.php';
include get_template_directory() . '/admin/widgets/flickr.php';
include get_template_directory() . '/admin/widgets/video.php';
include get_template_directory() . '/admin/widgets/subscribe.php';
include get_template_directory() . '/admin/widgets/comments.php';
include get_template_directory() . '/admin/widgets/buttons.php';
include get_template_directory() . '/admin/widgets/tabs.php';
include get_template_directory() . '/admin/widgets/adv-120x600.php';
include get_template_directory() . '/admin/widgets/adv-234x60.php';
include get_template_directory() . '/admin/widgets/adv-250x250.php';
include get_template_directory() . '/admin/widgets/adv-120x240.php';
include get_template_directory() . '/admin/widgets/adv-125x125.php';

/* vbegy_load_theme */
function vbegy_load_theme() {
	global $wpdb;
	/* Default RSS feed links */
	add_theme_support('automatic-feed-links');

	/* Post Thumbnails */
	add_theme_support('post-thumbnails');
	set_post_thumbnail_size( 50, 50, true );
	set_post_thumbnail_size( 60, 60, true );
	set_post_thumbnail_size( 80, 80, true );
	set_post_thumbnail_size( 250,160, true );
	set_post_thumbnail_size( 250,190, true );
	set_post_thumbnail_size( 1098,590, true );
	set_post_thumbnail_size( 806,440, true );
	set_post_thumbnail_size( 660,330, true );

	add_image_size('vbegy_img_1', 50, 50, true );
	add_image_size('vbegy_img_2', 60, 60, true );
	add_image_size('vbegy_img_3', 80, 80, true );
	add_image_size('vbegy_img_4', 250,160, true );
	add_image_size('vbegy_img_5', 250,190, true );
	add_image_size('vbegy_img_6', 1098,590, true );
	add_image_size('vbegy_img_7', 806,440, true );
	add_image_size('vbegy_img_8', 660,330, true );

	/* Valid HTML5 */
	add_theme_support('html5', array('search-form', 'comment-form', 'comment-list') );
	/* This theme uses its own gallery styles */
	add_filter('use_default_gallery_style', '__return_false');
	//$wpdb->query($wpdb->prepare("ALTER TABLE ".$wpdb->users." CHANGE `user_nicename` `user_nicename` VARCHAR(255) NOT NULL DEFAULT %s;",''));
	load_theme_textdomain('vbegy',get_template_directory().'/languages');
}
add_action('after_setup_theme', 'vbegy_load_theme');

/* vbegy_scripts_styles */
function vbegy_scripts_styles() {
	global $post;
	$active_lightbox = askme_options("active_lightbox");
	if ($active_lightbox == 1) {
		add_filter('body_class', 'lightbox_body_classes');
		function lightbox_body_classes($classes) {
			$classes[] = 'active-lightbox';
			return $classes;
		}
	}
	
	add_filter('body_class', 'login_body_classes');
	function login_body_classes($classes) {
		if (is_user_logged_in) {
			$classes[] = 'wrap-user-login';
		}else {
			$classes[] = 'wrap-user-not-login';
		}
		return $classes;
	}
	$protocol = is_ssl() ? 'https' : 'http';
	wp_enqueue_style('open-sans', $protocol.'://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,400,300,600,700');
	wp_enqueue_style('droidarabickufi', $protocol.'://fonts.googleapis.com/earlyaccess/droidarabickufi.css');
	wp_enqueue_style('v_base', get_template_directory_uri( __FILE__ ).'/css/base.css');
	wp_enqueue_style('v_lists', get_template_directory_uri( __FILE__ ).'/css/lists.css');
	wp_enqueue_style('v_bootstrap', get_template_directory_uri( __FILE__ ).'/css/bootstrap.min.css');
	wp_enqueue_style('v_prettyPhoto', get_template_directory_uri( __FILE__ ).'/css/prettyPhoto.css');
	wp_enqueue_style('v_font_awesome_old', get_template_directory_uri( __FILE__ ).'/css/font-awesome-old/css/font-awesome.min.css');
	wp_enqueue_style('v_font_awesome', get_template_directory_uri( __FILE__ ).'/css/font-awesome/css/font-awesome.min.css');
	wp_enqueue_style('v_fontello', get_template_directory_uri( __FILE__ ).'/css/fontello/css/fontello.css');
	wp_enqueue_style('v_enotype', get_template_directory_uri( __FILE__ ).'/woocommerce/enotype/enotype.css');
	wp_enqueue_style('select2-css','https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',array(),'4.1.0-rc.0');
	wp_enqueue_style('v_css', get_template_directory_uri().'/style.css','',null,'all');
	if (is_rtl()) {
		wp_enqueue_style('v_bootstrap_ar', get_template_directory_uri( __FILE__ ) . '/css/bootstrap.min-ar.css');
		wp_enqueue_style('v_rtl_css',get_template_directory_uri().'/css/rtl.css',array(),askme_theme_version);
		wp_enqueue_style('v_responsive_rtl', get_template_directory_uri( __FILE__ )."/css/rtl-responsive.css",array(),askme_theme_version);
	}else {
		wp_enqueue_style('v_main_css',get_template_directory_uri().'/css/main.css',array(),askme_theme_version);
		wp_enqueue_style('v_responsive', get_template_directory_uri( __FILE__ )."/css/responsive.css",array(),askme_theme_version);
	}
	if (is_category()) {
		$tax_id = get_query_var('cat');
	}
	$site_skin_all = askme_options("site_skin_l");
	if (is_author()) {
		$author_skin_l = askme_options("author_skin_l");
		if ($author_skin_l == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}else if (is_category()) {
		$cat_skin_l = get_term_meta($tax_id,"vbegy_cat_skin_l",true);
		$cat_skin_l = ($cat_skin_l != ""?$cat_skin_l:"default");
		if ($cat_skin_l == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}else if (is_tax("product_cat")) {
		$tax_id = get_term_by('slug',get_query_var('term'),"product_cat");
		$tax_id = $tax_id->term_id;
		$cat_skin_l = get_term_meta($tax_id,"vbegy_cat_skin_l",true);
		$cat_skin_l = ($cat_skin_l != ""?$cat_skin_l:"default");
		if ($cat_skin_l == "" || $cat_skin_l == "default") {
			$cat_skin_l = askme_options("products_skin_l");
		}
		if ($cat_skin_l == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}else if (is_tax("product_tag") || is_post_type_archive("product")) {
		$products_skin_l = askme_options("products_skin_l");
		if ($products_skin_l == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}else if (is_tax(ask_question_category)) {
		$tax_id = get_term_by('slug',get_query_var('term'),ask_question_category);
		$tax_id = $tax_id->term_id;
		$cat_skin_l = get_term_meta($tax_id,"vbegy_cat_skin_l",true);
		$cat_skin_l = ($cat_skin_l != ""?$cat_skin_l:"default");
		if ($cat_skin_l == "" || $cat_skin_l == "default") {
			$cat_skin_l = askme_options("questions_skin_l");
		}
		if ($cat_skin_l == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}else if (is_tax(ask_question_tags) || is_post_type_archive(ask_questions_type)) {
		$questions_skin_l = askme_options("questions_skin_l");
		if ($questions_skin_l == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}else if (is_single() || is_page()) {
		$vbegy_site_skin_l = askme_post_meta('vbegy_site_skin_l','radio',$post->ID);
		if (is_singular("product") && ($vbegy_site_skin_l == "" || $vbegy_site_skin_l == "default")) {
			$vbegy_site_skin_l = askme_options("products_skin_l");
		}
		if ((is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) && ($vbegy_site_skin_l == "" || $vbegy_site_skin_l == "default")) {
			$vbegy_site_skin_l = askme_options("questions_skin_l");
		}
		if ($vbegy_site_skin_l == "" || $vbegy_site_skin_l == "default") {
			$vbegy_site_skin_l = $site_skin_all;
		}
		if ($vbegy_site_skin_l == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}else {
		if ($site_skin_all == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}
	
	if ((is_author() && ($author_skin_l == "" || $author_skin_l == "default")) || ((is_single() || is_page()) && ($vbegy_site_skin_l == "" || $vbegy_site_skin_l == "default")) || (is_category() && ($cat_skin_l == "" || $cat_skin_l == "default")) || (is_tax("product_cat") && ($cat_skin_l == "" || $cat_skin_l == "default")) || (is_tax("product_tag") && ($products_skin_l == "" || $products_skin_l == "default")) || ((is_post_type_archive("product")) && ($products_skin_l == "" || $products_skin_l == "default")) || (is_tax(ask_question_category) && ($cat_skin_l == "" || $cat_skin_l == "default")) || (is_tax(ask_question_tags) && ($questions_skin_l == "" || $questions_skin_l == "default")) || ((is_post_type_archive(ask_questions_type)) && ($questions_skin_l == "" || $questions_skin_l == "default"))) {
		if ($site_skin_all == "site_dark") {
			wp_enqueue_style('v_dark', get_template_directory_uri( __FILE__ )."/css/dark.css",array(),askme_theme_version);
			add_filter('body_class', 'dark_skin_body_classes');
			function dark_skin_body_classes($classes) {
				$classes[] = 'dark_skin';
				return $classes;
			}
		}
	}
	
	$site_skin = askme_options('site_skin');
	if ($site_skin != "default" && $site_skin != "default_color") {
		wp_enqueue_style('skin-'.$site_skin, get_template_directory_uri( __FILE__ )."/css/skins/".$site_skin.".css",array(),askme_theme_version);
	}else {
		wp_enqueue_style('v-skins', get_template_directory_uri( __FILE__ )."/css/skins/skins.css",array(),askme_theme_version);
	}
	
	wp_enqueue_style('vpanel_custom', get_template_directory_uri( __FILE__ )."/css/custom.css");
	
	$custom_css = '';
	$vbegy_layout = "";
	$cat_layout = "";
	$products_layout = "";
	$questions_layout = "";
	$author_layout = "";
	if (is_category()) {
		$tax_id = get_query_var('cat');

		$cat_layout = get_term_meta($tax_id,"vbegy_cat_layout",true);
		$cat_layout = ($cat_layout != ""?$cat_layout:"default");

		$custom_background = get_term_meta($tax_id,"vbegy_custom_background",true);
		$background_img = (isset($custom_background["image"])?$custom_background["image"]:"");
		$background_color = (isset($custom_background["color"])?$custom_background["color"]:"");
		$background_repeat = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
		$background_fixed = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
		$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");

		$cat_full_screen_background = get_term_meta($tax_id,"vbegy_background_full",true);

		$cat_skin = get_term_meta($tax_id,"vbegy_cat_skin",true);
		$cat_skin = ($cat_skin != ""?$cat_skin:"default");

		$primary_color_c = get_term_meta($tax_id,"vbegy_primary_color",true);
	}else if (is_tax("product_cat")) {
		$tax_id = get_term_by('slug',get_query_var('term'),"product_cat");
		$tax_id = $tax_id->term_id;
		$cat_layout = get_term_meta($tax_id,"vbegy_cat_layout",true);
		$cat_layout = ($cat_layout != ""?$cat_layout:"default");

		$custom_background = get_term_meta($tax_id,"vbegy_custom_background",true);
		$background_img = (isset($custom_background["image"])?$custom_background["image"]:"");
		$background_color = (isset($custom_background["color"])?$custom_background["color"]:"");
		$background_repeat = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
		$background_fixed = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
		$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");

		$cat_full_screen_background = get_term_meta($tax_id,"vbegy_background_full",true);

		$cat_skin = get_term_meta($tax_id,"vbegy_cat_skin",true);
		$cat_skin = ($cat_skin != ""?$cat_skin:"default");

		$primary_color_c = get_term_meta($tax_id,"vbegy_primary_color",true);
		if ($primary_color_c == "") {
			$primary_color_c = askme_options('products_primary_color');
		}
		if ($cat_skin == "" || $cat_skin == "default") {
			$cat_skin = askme_options('products_skin');
		}
		$background_type = "";
		$background_pattern = "";
		if ($cat_layout == "" || $cat_layout == "default") {
			$cat_layout = askme_options("products_layout");
			if ($cat_layout == "fixed" || $cat_layout == "fixed_2"):
				$background_type = askme_options("products_background_type");
				$background_pattern = askme_options("products_background_pattern");
				$custom_background = askme_options("products_custom_background");
				$background_img = (isset($custom_background["image"])?$custom_background["image"]:"");
				$background_color = (isset($custom_background["color"])?$custom_background["color"]:"");
				$background_repeat = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
				$background_fixed = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
				$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");
				$cat_full_screen_background = askme_options("products_full_screen_background");
			endif;
		}
	}else if (is_tax("product_tag") || is_post_type_archive("product")) {
		$products_layout = askme_options('products_layout');
		$products_background_type = askme_options('products_background_type');
		$products_background_color = askme_options('products_background_color');
		$products_background_pattern = askme_options('products_background_pattern');
		$products_custom_background = askme_options('products_custom_background');
		$products_full_screen_background = askme_options('products_full_screen_background');
		$vbegy_skin = askme_options('products_skin');
		$primary_color_c = askme_options('products_primary_color');
	}else if (is_tax(ask_question_category)) {
		$tax_id = get_term_by('slug',get_query_var('term'),ask_question_category);
		$tax_id = $tax_id->term_id;
		$cat_layout = get_term_meta($tax_id,"vbegy_cat_layout",true);
		$cat_layout = ($cat_layout != ""?$cat_layout:"default");

		$custom_background = get_term_meta($tax_id,"vbegy_custom_background",true);
		$background_img = (isset($custom_background["image"])?$custom_background["image"]:"");
		$background_color = (isset($custom_background["color"])?$custom_background["color"]:"");
		$background_repeat = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
		$background_fixed = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
		$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");

		$cat_full_screen_background = get_term_meta($tax_id,"vbegy_background_full",true);

		$cat_skin = get_term_meta($tax_id,"vbegy_cat_skin",true);
		$cat_skin = ($cat_skin != ""?$cat_skin:"default");

		$primary_color_c = get_term_meta($tax_id,"vbegy_primary_color",true);
		$primary_color_c = ($primary_color_c != ""?$primary_color_c:"");
		if ($primary_color_c == "") {
			$primary_color_c = askme_options('questions_primary_color');
		}
		if ($cat_skin == "" || $cat_skin == "default") {
			$cat_skin = askme_options('questions_skin');
		}
		$background_type = "";
		$background_pattern = "";
		if ($cat_layout == "" || $cat_layout == "default") {
			$cat_layout = askme_options("questions_layout");
			if ($cat_layout == "fixed" || $cat_layout == "fixed_2"):
				$background_type = askme_options("questions_background_type");
				$custom_background = askme_options("questions_custom_background");
				$background_pattern = askme_options("questions_background_pattern");
				$background_img = (isset($custom_background["image"])?$custom_background["image"]:"");
				$background_color = (isset($custom_background["color"])?$custom_background["color"]:"");
				$background_repeat = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
				$background_fixed = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
				$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");
				$cat_full_screen_background = askme_options("questions_full_screen_background");
			endif;
		}
	}else if (is_tax(ask_question_tags) || is_post_type_archive(ask_questions_type)) {
		$questions_layout = askme_options('questions_layout');
		$questions_background_type = askme_options('questions_background_type');
		$questions_background_color = askme_options('questions_background_color');
		$questions_background_pattern = askme_options('questions_background_pattern');
		$questions_custom_background = askme_options('questions_custom_background');
		$questions_full_screen_background = askme_options('questions_full_screen_background');
		$vbegy_skin = askme_options('questions_skin');
		$primary_color_c = askme_options('questions_primary_color');
	}else if (is_author()) {
		$author_layout = askme_options('author_layout');
		$author_background_type = askme_options('author_background_type');
		$author_background_color = askme_options('author_background_color');
		$author_background_pattern = askme_options('author_background_pattern');
		$author_custom_background = askme_options('author_custom_background');
		$author_full_screen_background = askme_options('author_full_screen_background');
		$vbegy_skin = askme_options('author_skin');
		$primary_color_a = askme_options('author_primary_color');
	}else if (is_single() || is_page()) {
		global $post;
		$vbegy_layout = askme_post_meta('vbegy_layout','radio',$post->ID);
		$primary_color_p = askme_post_meta('vbegy_primary_color','color',$post->ID);
		$vbegy_skin = askme_post_meta('vbegy_skin','radio',$post->ID);
		if (is_singular("product")) {
			if ($vbegy_layout == "" || $vbegy_layout == "default") {
				$vbegy_layout = askme_options("products_layout");
			}
			if ($vbegy_skin == "" || $vbegy_skin == "default") {
				$vbegy_skin = askme_options("products_skin");
			}
			if ($primary_color_p == "") {
				$primary_color_p = askme_options("products_primary_color");
			}
		}
		if (is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) {
			if ($vbegy_layout == "" || $vbegy_layout == "default") {
				$vbegy_layout = askme_options("questions_layout");
			}
			if ($vbegy_skin == "" || $vbegy_skin == "default") {
				$vbegy_skin = askme_options("questions_skin");
			}
			if ($primary_color_p == "") {
				$primary_color_p = askme_options("questions_primary_color");
			}
		}
		if ($vbegy_skin == "" || $vbegy_skin == "default") {
			$primary_color = askme_options("primary_color");
			if ($primary_color != "") :
				$custom_css .= all_css_color($primary_color);
			endif;
		}
	}
	
	if (is_category() && $cat_layout != "default") {
		if ($cat_layout != "full") {
			if ($cat_full_screen_background == "on" || $cat_full_screen_background == 1) {
				$custom_css .= '.background-cover {';
					if (!empty($background_color)) {
						$custom_css .= 'background-color: '.esc_attr($background_color).';';
					}
					$custom_css .= 'background-image : url("'.esc_attr($background_img).'");
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr($background_img).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.esc_attr($background_img).'\',sizingMethod=\'scale\')";';
				$custom_css .= '}';
			}else {
				if (!empty($background_img)) {
					$custom_css .= 'body,body.dark_skin {
						background:'.esc_attr($background_color);
						if ($cat_full_screen_background != "on" && $cat_full_screen_background != 1) {
							$custom_css .= ' url('.esc_attr($background_img).') '.esc_attr($background_repeat).' '.esc_attr($background_position_x).' '.esc_attr($background_position_y).' '.esc_attr($background_fixed).';';
						}
					$custom_css .= '}';
				}
			}
		}
	}else if (is_tax("product_cat") && $cat_layout != "default") {
		if ($cat_layout != "full") {
			if ($cat_full_screen_background == "on" || $cat_full_screen_background == 1) {
				$custom_css .= '.background-cover {';
					if (!empty($background_color)) {
						$custom_css .= 'background-color: '.$background_color.';';
					}
					$custom_css .= 'background-image : url("'.$background_img.'");
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.$background_img.'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.$background_img.'\',sizingMethod=\'scale\')";
				}';
			}else {
				if ($background_type == "patterns" || !empty($custom_background) || $background_color != "") {
					$custom_css .= 'body,body.dark_skin {
						background:';
						if ($background_type == "patterns") {
							if ($background_pattern != "default") {
								$custom_css .= esc_attr($background_color).' url('.esc_attr(get_template_directory_uri()).'/images/patterns/'.esc_attr($background_pattern).'.png) repeat;';
							}
						}
						if (!empty($custom_background)) {
							if ($cat_full_screen_background != "on" && $cat_full_screen_background != 1) {
								$custom_css .= esc_attr($background_color).' url("'.esc_attr($background_img).'") '.esc_attr($background_repeat).' '.esc_attr($background_fixed).' '.esc_attr($background_position).';';
							}
						}
					$custom_css .= '}';
				}
			}
		}
	}else if ((is_tax("product_tag") && $products_layout != "default") || ((is_post_type_archive("product")) && $products_layout != "default")) {
		if ($products_layout != "full") {
			$custom_background = $products_custom_background;
			if (($products_full_screen_background == "on" || $products_full_screen_background == 1) && $products_background_type != "patterns") {
				$custom_css .= '.background-cover {';
					if (!empty($products_background_color)) {
						$custom_css .= 'background-color: '.esc_attr($products_background_color) .';';
					}
					$custom_css .= 'background-image : url("'.esc_attr($custom_background["image"]).'");
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr($custom_background["image"]).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.($custom_background["image"]).'\',sizingMethod=\'scale\')";
				}';
			}else {
				if ($products_background_type == "patterns" || !empty($custom_background) || $background_color != "") {
					$custom_css .= 'body,body.dark_skin {
						background:';
						if ($products_background_type == "patterns") {
							if ($products_background_pattern != "default") {
								$custom_css .= esc_attr($products_background_color).' url('.esc_attr(get_template_directory_uri()).'/images/patterns/'.esc_attr($products_background_pattern).'.png) repeat;';
							}
						}
						if (!empty($custom_background)) {
							if ($products_full_screen_background != "on" && $products_full_screen_background != 1) {
								$custom_css .= esc_attr($custom_background["color"]).' url('.esc_attr($custom_background["image"]).') '.esc_attr($custom_background["repeat"]).' '.esc_attr($custom_background["position"]).' '.esc_attr($custom_background["attachment"]).';';
							}
						}
					$custom_css .= '}';
				}
			}
		}
	}else if (is_tax(ask_question_category) && $cat_layout != "default") {
		if ($cat_layout != "full") {
			if ($cat_full_screen_background == "on" || $cat_full_screen_background == 1) {
				$custom_css .= '.background-cover {';
					if (!empty($background_color)) {
						$custom_css .= 'background-color: '.$background_color.';';
					}
					$custom_css .= 'background-image : url("'.$background_img.'");
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.$background_img.'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.$background_img.'\',sizingMethod=\'scale\')";
				}';
			}else {
				if ($background_type == "patterns" || !empty($custom_background) || $background_color != "") {
					$custom_css .= 'body,body.dark_skin {
						background: '.esc_attr($background_color);
						if ($background_type == "patterns") {
							if ($background_pattern != "default") {
								$custom_css .= ' url('.esc_attr(get_template_directory_uri()).'/images/patterns/'.esc_attr($background_pattern).'.png) repeat;';
							}
						}
						if (!empty($custom_background)) {
							if ($cat_full_screen_background != "on" && $cat_full_screen_background != 1) {
								$custom_css .= ' url("'.esc_attr($background_img).'") '.esc_attr($background_repeat).' '.esc_attr($background_fixed).' '.esc_attr($background_position).';';
							}
						}
					$custom_css .= '}';
				}
			}
		}
	}else if ((is_tax(ask_question_tags) && $questions_layout != "default") || ((is_post_type_archive(ask_questions_type)) && $questions_layout != "default")) {
		if ($questions_layout != "full") {
			$custom_background = $questions_custom_background;
			if (($questions_full_screen_background == "on" || $questions_full_screen_background == 1) && $questions_background_type != "patterns") {
				$custom_css .= '.background-cover {';
					if (!empty($questions_background_color)) {
						$custom_css .= 'background-color: '.esc_attr($questions_background_color) .';';
					}
					$custom_css .= 'background-image : url("'.esc_attr($custom_background["image"]).'");
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr($custom_background["image"]).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.($custom_background["image"]).'\',sizingMethod=\'scale\')";
				}';
			}else {
				if ($questions_background_type == "patterns" || !empty($custom_background) || $background_color != "") {
					$custom_css .= 'body,body.dark_skin {
						background:';
						if ($questions_background_type == "patterns") {
							if ($questions_background_pattern != "default") {
								$custom_css .= esc_attr($questions_background_color).' url('.esc_attr(get_template_directory_uri()).'/images/patterns/'.esc_attr($questions_background_pattern).'.png) repeat;';
							}
						}
						if (!empty($custom_background)) {
							if ($questions_full_screen_background != "on" && $questions_full_screen_background != 1) {
								$custom_css .= esc_attr($custom_background["color"]).' url('.esc_attr($custom_background["image"]).') '.esc_attr($custom_background["repeat"]).' '.esc_attr($custom_background["position"]).' '.esc_attr($custom_background["attachment"]).';';
							}
						}
					$custom_css .= '}';
				}
			}
		}
	}else if (is_author() && $author_layout != "default") {
		if ($author_layout != "full") {
			$custom_background = $author_custom_background;
			if (($author_full_screen_background == "on" || $author_full_screen_background == 1) && $author_background_type != "patterns") {
				$custom_css .= '.background-cover {';
					if (!empty($author_background_color)) {
						$custom_css .= 'background-color:'.esc_attr($author_background_color).';';
					}
					$custom_css .= 'background-image : url("'.esc_attr($custom_background["image"]).'");
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr($custom_background["image"]).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.($custom_background["image"]).'\',sizingMethod=\'scale\')";
				}';
			}else {
				if ($author_background_type == "patterns" || !empty($custom_background) || $background_color != "") {
					$custom_css .= 'body,body.dark_skin {
						background:';
						if ($author_background_type == "patterns") {
							if ($author_background_pattern != "default") {
								$custom_css .= esc_attr($author_background_color).' url('.esc_attr(get_template_directory_uri()).'/images/patterns/'.esc_attr($author_background_pattern).'.png) repeat;';
							}
						}
						if (!empty($custom_background)) {
							if ($author_full_screen_background != "on" && $author_full_screen_background != 1) {
								$custom_css .= esc_attr($custom_background["color"]).' url('.esc_attr($custom_background["image"]).') '.esc_attr($custom_background["repeat"]).' '.esc_attr($custom_background["position"]).' '.esc_attr($custom_background["attachment"]).';';
							}
						}
					$custom_css .= '}';
				}
			}
		}
	}else if ((is_single() || is_page()) && $vbegy_layout != "" && $vbegy_layout != "default"):
		if ($vbegy_layout == "fixed" || $vbegy_layout == "fixed_2"):
			$custom_background = get_post_meta($post->ID,"vbegy_custom_background",true);
			$background_img = (isset($custom_background["image"])?$custom_background["image"]:"");
			$background_color = (isset($custom_background["color"])?$custom_background["color"]:"");
			$background_repeat = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
			$background_fixed = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
			$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");

			$background_full = askme_post_meta('vbegy_background_full','checkbox',$post->ID);
			$background_type = "";
			$background_pattern = "";
			$vbegy_layout = askme_post_meta('vbegy_layout','radio',$post->ID);
			if (is_singular("product")) {
				$vbegy_layout = askme_options("products_layout");
				if ($vbegy_layout == "fixed" || $vbegy_layout == "fixed_2"):
					$background_type = askme_options("products_background_type");
					$custom_background = askme_options("products_custom_background");
					$background_pattern = askme_options("products_background_pattern");
					$background_img = (isset($custom_background["image"])?$custom_background["image"]:"");
					$background_color = (isset($custom_background["color"])?$custom_background["color"]:"");
					$background_repeat = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
					$background_fixed = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
					$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");
					$background_full = askme_options("products_full_screen_background");
				endif;
			}
			if (is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) {
				$vbegy_layout = askme_options("questions_layout");
				if ($vbegy_layout == "fixed" || $vbegy_layout == "fixed_2"):
					$background_type = askme_options("questions_background_type");
					$custom_background = askme_options("questions_custom_background");
					$background_pattern = askme_options("questions_background_pattern");
					$background_img = (isset($custom_background["image"])?$custom_background["image"]:"");
					$background_color = (isset($custom_background["color"])?$custom_background["color"]:"");
					$background_repeat = (isset($custom_background["repeat"])?$custom_background["repeat"]:"");
					$background_fixed = (isset($custom_background["attachment"])?$custom_background["attachment"]:"");
					$background_position = (isset($custom_background["position"])?$custom_background["position"]:"");
					$background_full = askme_options("questions_full_screen_background");
				endif;
			}
			if ($background_full == 1 && $background_type != "patterns"):
				$custom_css .= '.background-cover {';
					if (!empty($background_color)) {
						$custom_css .= 'background-color: '.esc_attr($background_color).';';
					}
					$custom_css .= 'background-image : url("'.esc_attr($background_img).'");
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr($background_img).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.esc_attr($background_img).'\',sizingMethod=\'scale\')";
				}';
			else:
				if ($background_type == "patterns" || $background_color || $background_img) {
					$custom_css .= 'body,body.dark_skin {
						background:';
						if ($background_type == "patterns") {
							if ($background_pattern != "default") {
								$custom_css .= esc_attr($background_color).' url('.esc_attr(get_template_directory_uri()).'/images/patterns/'.esc_attr($background_pattern).'.png) repeat;';
							}
						}
						if ($background_color || $background_img) {
							if ($background_full != 1) {
								$custom_css .= esc_attr($background_color).' url("'.esc_attr($background_img).'") '.esc_attr($background_repeat).' '.esc_attr($background_fixed).' '.esc_attr($background_position).';';
							}
						}
					$custom_css .= '}';
				}
			endif;
		endif;
	else:
		if (askme_options("home_layout") != "full") {
			$custom_background = askme_options("custom_background");
			$full_screen_background = askme_options("full_screen_background");
			if (($full_screen_background == "on" || $full_screen_background == 1) && askme_options("background_type") != "patterns") {
				$custom_css .= '.background-cover {';
					$background_color_s = askme_options("background_color");
					if (!empty($background_color_s)) {
						$custom_css .= 'background-color: '.esc_attr($background_color_s).';';
					}
					$custom_css .= 'background-image : url("'.esc_attr($custom_background["image"]).'");
					filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="'.esc_attr($custom_background["image"]).'",sizingMethod="scale");
					-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.esc_attr($custom_background["image"]).'\',sizingMethod=\'scale\')";
				}';
			}else {
				if (askme_options("background_type") == "patterns" || !empty($custom_background) || $background_color != "") {
					$custom_css .= 'body,body.dark_skin {
						background:';
						if (askme_options("background_type") == "patterns") {
							if (askme_options("background_pattern") != "default") {
								$custom_css .= askme_options("background_color").' url('.get_template_directory_uri().'/images/patterns/'.askme_options("background_pattern").'.png) repeat;';
							}
						}
						if (!empty($custom_background)) {
							if ($full_screen_background != "on" && $full_screen_background != 1) {
								$custom_css .= esc_attr($custom_background["color"]).' url('.esc_attr($custom_background["image"]).') '.esc_attr($custom_background["repeat"]).' '.esc_attr($custom_background["position"]).' '.esc_attr($custom_background["attachment"]).';';
							}
						}
					$custom_css .= '}';
				}
			}
		}
	endif;
	
	if (is_category() && $primary_color_c == "") {
		if ($cat_skin != "default" && $cat_skin != "default_color") {
			if ($cat_skin == "skins") {
				wp_enqueue_style('v-skins', get_template_directory_uri( __FILE__ )."/css/skins/skins.css",array(),askme_theme_version);
			}else if (!empty($cat_skin)) {
				wp_enqueue_style('skin-'.$cat_skin, get_template_directory_uri( __FILE__ )."/css/skins/".$cat_skin.".css",array(),askme_theme_version);
			}
		}else {
			$primary_color = askme_options("primary_color");
			if ($primary_color != "") :
				$custom_css .= all_css_color($primary_color);
			endif;
		}
	}else if (is_category() && $primary_color_c != "") {
		$custom_css .= all_css_color($primary_color_c);
	}else if (is_tax("product_cat") && $primary_color_c == "") {
		if ($cat_skin != "default" && $cat_skin != "default_color") {
			if ($cat_skin == "skins") {
				wp_enqueue_style('v-skins', get_template_directory_uri( __FILE__ )."/css/skins/skins.css",array(),askme_theme_version);
			}else if (!empty($cat_skin)) {
				wp_enqueue_style('skin-'.$cat_skin, get_template_directory_uri( __FILE__ )."/css/skins/".$cat_skin.".css",array(),askme_theme_version);
			}
		}else {
			$primary_color = askme_options("primary_color");
			if ($primary_color != "") :
				$custom_css .= all_css_color($primary_color);
			endif;
		}
	}else if (is_tax("product_cat") && $primary_color_c != "") {
		$custom_css .= all_css_color($primary_color_c);
	}else if ((is_tax("product_tag") && ($primary_color_c == "")) || ((is_post_type_archive("product")) && ($primary_color_c == ""))) {
		if ($vbegy_skin != "default" && $vbegy_skin != "default_color") {
			if ($vbegy_skin == "skins") {
				wp_enqueue_style('v-skins', get_template_directory_uri( __FILE__ )."/css/skins/skins.css",array(),askme_theme_version);
			}else if (!empty($vbegy_skin)) {
				wp_enqueue_style('skin-'.$vbegy_skin, get_template_directory_uri( __FILE__ )."/css/skins/".$vbegy_skin.".css",array(),askme_theme_version);
			}
		}else {
			$primary_color = askme_options("primary_color");
			if ($primary_color != "") :
				$custom_css .= all_css_color($primary_color);
			endif;
		}
	}else if ((is_tax("product_tag") && ($primary_color_c != "")) || (is_post_type_archive("product")) && ($primary_color_c != "")) {
		$custom_css .= all_css_color($primary_color_c);
	}else if (is_tax(ask_question_category) && $primary_color_c == "") {
		if ($cat_skin != "default" && $cat_skin != "default_color") {
			if ($cat_skin == "skins") {
				wp_enqueue_style('v-skins', get_template_directory_uri( __FILE__ )."/css/skins/skins.css",array(),askme_theme_version);
			}else if (!empty($cat_skin)) {
				wp_enqueue_style('skin-'.$cat_skin, get_template_directory_uri( __FILE__ )."/css/skins/".$cat_skin.".css",array(),askme_theme_version);
			}
		}else {
			$primary_color = askme_options("primary_color");
			if ($primary_color != "") :
				$custom_css .= all_css_color($primary_color);
			endif;
		}
	}else if (is_tax(ask_question_category) && $primary_color_c != "") {
		$custom_css .= all_css_color($primary_color_c);
	}else if ((is_tax(ask_question_tags) && ($primary_color_c == "")) || ((is_post_type_archive(ask_questions_type)) && $primary_color_c == "")) {
		if ($vbegy_skin != "default" && $vbegy_skin != "default_color") {
			if ($vbegy_skin == "skins") {
				wp_enqueue_style('v-skins', get_template_directory_uri( __FILE__ )."/css/skins/skins.css",array(),askme_theme_version);
			}else if (!empty($vbegy_skin)) {
				wp_enqueue_style('skin-'.$vbegy_skin, get_template_directory_uri( __FILE__ )."/css/skins/".$vbegy_skin.".css",array(),askme_theme_version);
			}
		}else {
			$primary_color = askme_options("primary_color");
			if ($primary_color != "") :
				$custom_css .= all_css_color($primary_color);
			endif;
		}
	}else if ((is_tax(ask_question_tags) && ($primary_color_c != "")) || (is_post_type_archive(ask_questions_type)) && ($primary_color_c != "")) {
		$custom_css .= all_css_color($primary_color_c);
	}else if (is_author() && $primary_color_a == "") {
		if ($vbegy_skin != "default" && $vbegy_skin != "default_color") {
			if ($vbegy_skin == "skins") {
				wp_enqueue_style('v-skins', get_template_directory_uri( __FILE__ )."/css/skins/skins.css",array(),askme_theme_version);
			}else if (!empty($vbegy_skin)) {
				wp_enqueue_style('skin-'.$vbegy_skin, get_template_directory_uri( __FILE__ )."/css/skins/".$vbegy_skin.".css",array(),askme_theme_version);
			}
		}else {
			$primary_color = askme_options("primary_color");
			if ($primary_color != "") :
				$custom_css .= all_css_color($primary_color);
			endif;
		}
	}else if (is_author() && $primary_color_a != "") {
		$custom_css .= all_css_color($primary_color_a);
	}else if ((is_single() || is_page()) && $primary_color_p == "") {
		if ($vbegy_skin != "default" && $vbegy_skin != "default_color") {
			if ($vbegy_skin == "skins") {
				wp_enqueue_style('v-skins', get_template_directory_uri( __FILE__ )."/css/skins/skins.css",array(),askme_theme_version);
			}else if (!empty($vbegy_skin)) {
				wp_enqueue_style('skin-'.$vbegy_skin, get_template_directory_uri( __FILE__ )."/css/skins/".$vbegy_skin.".css",array(),askme_theme_version);
			}
		}else {
			$primary_color = askme_options("primary_color");
			if ($primary_color != "") :
				$custom_css .= all_css_color($primary_color);
			endif;
		}
	}else if ((is_single() || is_page()) && $primary_color_p != "") {
		$custom_css .= all_css_color($primary_color_p);
	}else {
		$primary_color = askme_options("primary_color");
		if ($primary_color != "") :
			$custom_css .= all_css_color($primary_color);
		endif;
	}
	
	$logo_display = askme_options("logo_display");
	$logo_width = askme_options("logo_width");
	
	if (is_tax("product_cat") || is_tax("product_tag") || is_post_type_archive("product") || is_singular("product")) {
		$products_custom_header = askme_options("products_custom_header");
		if ($products_custom_header == 1) {
			$logo_display = askme_options("products_logo_display");
			$logo_width = askme_options("products_logo_width");
		}
	}else if (is_tax(ask_question_category) || is_tax(ask_question_tags) || is_post_type_archive(ask_questions_type) || (is_singular(ask_questions_type) || is_singular(ask_asked_questions_type))) {
		$questions_custom_header = askme_options("questions_custom_header");
		if ($questions_custom_header == 1) {
			$logo_display = askme_options("questions_logo_display");
			$logo_width = askme_options("questions_logo_width");
		}
	}
	
	if ($logo_display == "custom_image") {
		$custom_css .= '.logo img {
			max-width: '.$logo_width.'px;
		}';
	}

	/* Fonts */

	$main_font = askme_options("main_font");
	if (isset($main_font["face"]) && $main_font["face"] != "default" && $main_font["face"] != "Default font" && $main_font["face"] != "") {
		$main_font["face"] = str_replace("+"," ",$main_font["face"]);
		$custom_css .= '
		body,.qoute p,input,.button,label,.more,blockquote,.widget ul li,textarea,h1, h2, h3, h4, h5, h6,select,.f_left.language_selector ul li a.lang_sel_sel {
			font-family: "'.$main_font["face"].'";
		}';
	}

	$second_font = askme_options("second_font");
	if (isset($second_font["face"]) && $second_font["face"] != "default" && $second_font["face"] != "Default font" && $second_font["face"] != "") {
		$second_font["face"] = str_replace("+"," ",$second_font["face"]);
		$custom_css .= '
		.question-favorite,.question-category,.question-author-meta,.question-date,.author-message,.message-reply,.message-delete,.question-comment,.question-view,.question-points,.question-vote-all,.question-category a,.question-author-meta a,.question-comment a,.question-answered,.widget_social li span,.widget_stats li span,.widget_highest_points .comment,.related-item span,.copyrights,.error_404 h2,.registe-user span,.user-profile-widget .ul_list li span span,.pagination a,.pagination span,.question-reply,.block-stats-2,.block-stats-3,.block-stats-4,.question-vote-result,.single-question-vote-result,.main-content .page-content .boxedtitle.page-title h2 span.color,.commentlist li .date,.question-type-poll .progressbar-title,.post .post-meta .meta-author,.post .post-meta .meta-date,.post .post-meta .meta-categories a,.post .post-meta .meta-comment a,.post .post-meta .post-view a {
			font-family: "'.$second_font["face"].'";
		}';
	}

	$custom_css .= askme_general_typography("general_typography","body,p");
	$custom_css .= askme_general_color('general_link_color','a','color');
	
	for ($i = 1; $i <= 6; $i++) {
		$custom_css .= askme_general_typography("h".$i,"h".$i);
	}
	
	/* custom_css */
	if(askme_options("custom_css")) {
		$custom_css .= askme_options("custom_css");
	}
	if (is_single() || is_page()) {
		$custom_css .= askme_post_meta('vbegy_footer_css','textarea',$post->ID);
	}
	
	wp_add_inline_style('vpanel_custom',stripslashes($custom_css));
	
	wp_enqueue_script('select2-js','https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',array("jquery"),'4.1.0-rc.0');
	wp_enqueue_script("v_easing", get_template_directory_uri( __FILE__ )."/js/jquery.easing.1.3.min.js",array("jquery"));
	wp_enqueue_script("v_html5", get_template_directory_uri( __FILE__ )."/js/html5.js",array("jquery"));
	wp_enqueue_script("v_modernizr", get_template_directory_uri( __FILE__ )."/js/modernizr.js",array("jquery"),'1.0.0',true);
	wp_enqueue_script("v_jflickrfeed", get_template_directory_uri( __FILE__ )."/js/jflickrfeed.min.js",array("jquery"));
	wp_enqueue_script("v_inview", get_template_directory_uri( __FILE__ )."/js/jquery.inview.min.js",array("jquery"));
	wp_enqueue_script("v_tipsy", get_template_directory_uri( __FILE__ )."/js/jquery.tipsy.js",array("jquery"));
	wp_enqueue_script("v_tabs", get_template_directory_uri( __FILE__ )."/js/tabs.js",array("jquery"));
	wp_enqueue_script("v_flexslider", get_template_directory_uri( __FILE__ )."/js/jquery.flexslider.js",array("jquery"));
	wp_enqueue_script("v_prettyphoto", get_template_directory_uri( __FILE__ )."/js/jquery.prettyPhoto.js",array("jquery"));
	wp_enqueue_script("v_carouFredSel", get_template_directory_uri( __FILE__ )."/js/jquery.carouFredSel-6.2.1-packed.js",array("jquery"));
	wp_enqueue_script("v_scrollTo", get_template_directory_uri( __FILE__ )."/js/jquery.scrollTo.js",array("jquery"));
	wp_enqueue_script("v_nav", get_template_directory_uri( __FILE__ )."/js/jquery.nav.js",array("jquery"));
	wp_enqueue_script("v_tags", get_template_directory_uri( __FILE__ )."/js/tags.js",array("jquery"));
	wp_enqueue_script("v_theia", get_template_directory_uri( __FILE__ )."/js/theia.js",array("jquery"));
	wp_enqueue_script("v_mCustomScrollbar", get_template_directory_uri( __FILE__ )."/js/mCustomScrollbar.js",array("jquery"));
	if (is_rtl()) {
		wp_enqueue_script("v_bxslider", get_template_directory_uri( __FILE__ )."/js/jquery.bxslider.min-ar.js",array("jquery"));
	}else {
		wp_enqueue_script("v_bxslider", get_template_directory_uri( __FILE__ )."/js/jquery.bxslider.min.js",array("jquery"));
	}
	$captcha_style = askme_options("captcha_style");
	if ($captcha_style == "google_recaptcha") {
		$recaptcha_language = askme_options("recaptcha_language");
		wp_enqueue_script("v_recaptcha", "https://www.google.com/recaptcha/api.js".($recaptcha_language != ""?"?hl=".$recaptcha_language:""),array("jquery"),'1.0.0',true);
	}
	if (is_user_logged_in()) {
		$payment_methods = askme_options("payment_methodes");
		if (isset($payment_methods["stripe"]["value"]) && $payment_methods["stripe"]["value"] == "stripe") {
			wp_enqueue_script("askme-stripe","https://js.stripe.com/v3/",array("jquery"),askme_theme_version,true);
		}
	}
	$publishable_key = askme_options("publishable_key");
	$products_excerpt_title = askme_options("products_excerpt_title");
	$products_excerpt_title = (isset($products_excerpt_title)?$products_excerpt_title:40);
	$captcha_answer = askme_options("captcha_answer");
	$ajax_file = askme_options("ajax_file");
	$ajax_file = ($ajax_file == "theme"?get_template_directory_uri().'/includes/ajax.php':admin_url("admin-ajax.php"));
	wp_enqueue_script("askme-custom", get_template_directory_uri( __FILE__ )."/js/custom.min.js",array("jquery","jquery-ui-core","jquery-ui-sortable","jquery-ui-datepicker"),askme_theme_version);
	$askme_js = array(
		"products_excerpt_title"       => $products_excerpt_title,
		"captcha_answer"               => $captcha_answer,
		"v_get_template_directory_uri" => get_template_directory_uri(),
		"admin_url"                    => $ajax_file,
		"publishable_key"              => $publishable_key,
		'askme_best_answer_nonce'      => wp_create_nonce('askme_best_answer_nonce'),
		"ask_error_text"               => esc_html__("Please fill the required field.","vbegy"),
		"ask_error_captcha"            => esc_html__("The captcha is incorrect, please try again.","vbegy"),
		"ask_error_empty"              => esc_html__("Fill out all the required fields.","vbegy"),
		"no_vote_question"             => esc_html__("Sorry, you cannot vote your question.","vbegy"),
		"no_vote_more"                 => esc_html__("Sorry, you cannot vote on the same question more than once.","vbegy"),
		"no_vote_user"                 => esc_html__("Rating is available to members only.","vbegy"),
		"no_vote_answer"               => esc_html__("Sorry, you cannot vote your answer.","vbegy"),
		"no_vote_more_answer"          => esc_html__("Sorry, you cannot vote on the same answer more than once.","vbegy"),
		"sure_delete"                  => esc_html__("Are you sure you want to delete the question?","vbegy"),
		"sure_delete_post"             => esc_html__("Are you sure you want to delete the post?","vbegy"),
		"sure_delete_comment"          => esc_html__("Are you sure you want to delete the comment?","vbegy"),
		"sure_delete_answer"           => esc_html__("Are you sure you want to delete the answer?","vbegy"),
		"sure_delete_message"          => esc_html__("Are you sure you want to delete the message?","vbegy"),
		"choose_best_answer"           => esc_html__("Select as best answer","vbegy"),
		"cancel_best_answer"           => esc_html__("Cancel the best answer","vbegy"),
		"best_answer"                  => esc_html__("Best answer","vbegy"),
		"follow_question_attr"         => esc_html__("Follow the question","vbegy"),
		"unfollow_question_attr"       => esc_html__("Unfollow the question","vbegy"),
		"follow_question"              => esc_html__("Follow","vbegy"),
		"unfollow_question"            => esc_html__("Unfollow","vbegy"),
		"block_user"                   => esc_html__("Block","vbegy"),
		"unblock_user"                 => esc_html__("Unblock","vbegy"),
		"select_file"                  => esc_html__("Select file","vbegy"),
		"browse"                       => esc_html__("Browse","vbegy"),
		"block_message_text"           => esc_html__("Block Message","vbegy"),
		"unblock_message_text"         => esc_html__("Unblock Message","vbegy"),
		"cancel_reply"                 => esc_html__("Click here to cancel reply.","vbegy"),
		"must_login"                   => esc_html__("Please login to vote and see the results.","vbegy"),
		"no_poll_more"                 => esc_html__("Sorry, you cannot poll on the same question more than once.","vbegy"),
		'insert_image'                 => esc_html__('Insert Image','vbegy'),
		'error_uploading_image'        => esc_html__('Attachment Error! Please upload image only.','vbegy'),
	);
	wp_localize_script("askme-custom","askme_js",$askme_js);
	if (is_rtl()) {
		wp_enqueue_script("v_custom_ar", get_template_directory_uri( __FILE__ )."/js/custom-ar.min.js",array("jquery"),askme_theme_version);
	}
	$main_font = askme_options("main_font");
	$second_font = askme_options("second_font");
	if (isset($main_font["face"])) {
		$earlyaccess_main = askme_earlyaccess_fonts($main_font["face"]);
		if ($earlyaccess_main == "earlyaccess") {
			$main_font_style = strtolower(str_replace("+","",$main_font["face"]));
			wp_enqueue_style('askme-'.$main_font_style, $protocol.'://fonts.googleapis.com/earlyaccess/'.$main_font_style.'.css');
		}else {
			wp_enqueue_style('askme-fonts',askme_fonts_url(),array(),askme_theme_version);
		}
	}
	if (isset($second_font["face"])) {
		$earlyaccess_second = askme_earlyaccess_fonts($second_font["face"]);
		if ($earlyaccess_second == "earlyaccess") {
			$second_font_style = strtolower(str_replace("+","",$second_font["face"]));
			wp_enqueue_style('askme-'.$second_font_style, $protocol.'://fonts.googleapis.com/earlyaccess/'.$second_font_style.'.css');
		}else {
			wp_enqueue_style('askme-fonts',askme_fonts_url(),array(),askme_theme_version);
		}
	}
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}
}
add_action('wp_enqueue_scripts','vbegy_scripts_styles');
/* askme_fonts_url */
function askme_fonts_url() {
	$font_url = '';
	$show_fonts = apply_filters("askme_show_fonts",true);
	if ($show_fonts == true) {
		if ('off' !== _x('on','Google font: on or off','vbegy')) {
			$main_font   = askme_options("main_font");
			$second_font = askme_options("second_font");
			$earlyaccess_main = askme_earlyaccess_fonts($main_font["face"]);
			$earlyaccess_second = askme_earlyaccess_fonts($second_font["face"]);
			$safe_fonts  = array(
				'arial'      => 'Arial',
				'verdana'    => 'Verdana',
				'trebuchet'  => 'Trebuchet',
				'times'      => 'Times New Roman',
				'tahoma'     => 'Tahoma',
				'geneva'     => 'Geneva',
				'georgia'    => 'Georgia',
				'palatino'   => 'Palatino',
				'helvetica'  => 'Helvetica',
				'museo_slab' => 'Museo Slab'
			);
			if ((isset($second_font["face"]) && $earlyaccess_second != "earlyaccess" && (($second_font["face"] != "Default font" && $second_font["face"] != "default" && $second_font["face"] != "") || $second_font["face"] == "default" || $second_font["face"] == "Default font" || $second_font["face"] == "") && !in_array($second_font["face"],$safe_fonts)) || (isset($main_font["face"]) && $earlyaccess_main != "earlyaccess" && (($main_font["face"] != "Default font" && $main_font["face"] != "default" && $main_font["face"] != "") || $main_font["face"] == "default" || $main_font["face"] != "Default font" || $main_font["face"] == "") && !in_array($main_font["face"],$safe_fonts))) {
				$font_url = add_query_arg('family',urlencode((is_rtl()?"'Droid Arabic Kufi',":"")."'".(isset($second_font["face"]) && $second_font["face"] != "Default font" && $second_font["face"] != "default" && $second_font["face"] != ""?str_ireplace("+"," ",$second_font["face"]):'Open Sans').':100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i|'.(isset($main_font["face"]) && $main_font["face"] != "Default font" && $main_font["face"] != "default" && $main_font["face"] != ""?str_ireplace("+"," ",$main_font["face"]):'Roboto').':100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&amp;subset=cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese&amp;display=swap' ),"//fonts.googleapis.com/css");
			}
		}
	}
	return $font_url;
}
/* wp head */
function vbegy_head() {
	global $post;
	if (!function_exists('wp_site_icon') || !has_site_icon()) {
	    $default_favicon    = get_template_directory_uri()."/images/favicon.png";
	    $favicon            = askme_options("favicon");
	    $iphone_icon        = askme_options("iphone_icon");
	    $iphone_icon_retina = askme_options("iphone_icon_retina");
	    $ipad_icon          = askme_options("ipad_icon");
	    $ipad_icon_retina   = askme_options("ipad_icon_retina");
	    
		echo '<link rel="shortcut icon" href="'.esc_url((isset($favicon) && $favicon != ""?$favicon:$default_favicon)).'" type="image/x-icon">' ."\n";
	
	    /* Favicon iPhone */
	    if (isset($iphone_icon) && $iphone_icon != "") {
	        echo '<link rel="apple-touch-icon-precomposed" href="'.esc_url($iphone_icon).'">' ."\n";
	    }
	
	    /* Favicon iPhone 4 Retina display */
	    if (isset($iphone_icon_retina) && $iphone_icon_retina != "") {
	        echo '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="'.esc_url($iphone_icon_retina).'">' ."\n";
	    }
	
	    /* Favicon iPad */
	    if (isset($ipad_icon) && $ipad_icon != "") {
	        echo '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="'.esc_url($ipad_icon).'">' ."\n";
	    }
	
	    /* Favicon iPad Retina display */
	    if (isset($ipad_icon_retina) && $ipad_icon_retina != "") {
	        echo '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="'.esc_url($ipad_icon_retina).'">' ."\n";
	    }
	}

	$primary_color = askme_options("primary_color");
	if ($primary_color != "") {
		$skin = $primary_color;
	}else {
		$skins = array("skins" => "#ff7361","blue" => "#3498db","gray" => "#8a8a8a","green" => "#1bbc9b","moderate_cyan" => "#38cbcb","orange" => "#fdb655","purple" => "#8e74b2","red" => "#ef3852","strong_cyan" => "#27bebe","yellow" => "#BAA56A");
		$site_skin = askme_options('site_skin');
		if ($site_skin == "skins" || $site_skin == "default" || $site_skin == "default_color") {
			$skin = $skins["skins"];
		}else {
			$skin = $skins[$site_skin];
		}
	}
	if (isset($skin) && $skin != "") {
		echo '<meta name="theme-color" content="'.$skin.'">
		<meta name="msapplication-navbutton-color" content="'.$skin.'">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">';
	}

    /* Seo */
    $the_seo = stripslashes(askme_options("the_keywords"));

    if (askme_options("seo_active") == 1) {
    	$fbShareImage = get_option('fb_share_image');
    	
    	echo '<meta property="og:site_name" content="'.htmlspecialchars(get_bloginfo('name')).'" />'."\n";
    	echo '<meta property="og:type" content="website" />'."\n";
    	
        if (!is_home() && !is_front_page() && (is_single() || is_page())) {
        	if ( have_posts() ) : while ( have_posts() ) : the_post();
        		$vpanel_image = vpanel_image();
        		if ((function_exists("has_post_thumbnail") && has_post_thumbnail()) || !empty($vpanel_image)) {
        			if (has_post_thumbnail()) {
						$image_id = get_post_thumbnail_id($post->ID);
						$image_url = wp_get_attachment_image_src($image_id,"vbegy_img_8");
    		        	$post_thumb = $image_url[0];
    		        }else {
    		        	$post_thumb = $vpanel_image;
    		        }
    		    }else {
    		        $protocol = is_ssl() ? 'https' : 'http';
    		        
    		        $video_id = askme_post_meta('vbegy_video_post_id',"select",$post->ID);
    		        $video_type = askme_post_meta('vbegy_video_post_type',"text",$post->ID);
    		        if (is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) {
    		        	$video_id = get_post_meta($post->ID,'video_id',true);
    		        	$video_type = get_post_meta($post->ID,'video_type',true);
    		        }
    		
    				if (!empty($video_id)) {
			            if ($video_type == 'youtube') {
			                $post_thumb = $protocol.'://img.youtube.com/vi/'.$video_id.'/0.jpg';
			            }else if ($video_type == 'vimeo') {
			                $url = $protocol.'://vimeo.com/api/v2/video/'.$video_id.'.php';
			                $contents = @file_get_contents($url);
			                $thumb = @unserialize(trim($contents));
			                $post_thumb = $thumb[0]['thumbnail_large'];
			            }elseif ($video_type == 'daily') {
			                $post_thumb = 'https://www.dailymotion.com/thumbnail/video/'.$video_id;
			            }
		            }
    		    }
    		    
    		    $post_thumb = apply_filters("askme_filter_og_image",(isset($post_thumb)?$post_thumb:""),(isset($post->ID) && $post->ID > 0?$post->ID:0));
    		    if (!empty($post_thumb)) {
    		        echo '<meta property="og:image" content="' . $post_thumb . '" />' . "\n";
    		    }else {
    		    	$fb_share_image = askme_options("fb_share_image");
		    		$logo_display = askme_options("logo_display");
		    		$logo_img = askme_image_url_id(askme_options("logo_img"));
    		    	if (!empty($fb_share_image)) {
    		        	echo '<meta property="og:image" content="' . $fb_share_image . '" />' . "\n";
    		        }else if ($logo_display == "custom_image" && isset($logo_img) && $logo_img != "") {
    		        	echo '<meta property="og:image" content="' . $logo_img . '" />' . "\n";?>
    		        <?php }
    		    }
        			
        		$title = the_title('', '', false);
        		$php_version = explode('.', phpversion());
        		if(count($php_version) && $php_version[0]>=5)
        			$title = html_entity_decode($title,ENT_QUOTES,'UTF-8');
        		else
        			$title = html_entity_decode($title,ENT_QUOTES);
        			echo '<meta property="og:title" content="'.htmlspecialchars($title).'" />'."\n";
        			echo '<meta property="og:url" content="'.get_permalink().'" />'."\n";
        				$description = trim(get_the_excerpt());
        			if ($description != '')
        			    	echo '<meta property="og:description" content="'.htmlspecialchars($description).'" />'."\n";
        			    	
        	    if (is_singular(ask_questions_type)) {
        	    	if ($terms = wp_get_object_terms( $post->ID, ask_question_tags)) :
        	    		$the_tags_post = '';
        	    			$terms_array = array();
        	    			foreach ($terms as $term) :
        	    				$the_tags_post .= $term->name . ',';
        	    			endforeach;
        	    			echo '<meta name="keywords" content="' . trim($the_tags_post, ',') . '">' ."\n";
        	    	endif;
        		}else {
        	    	$posttags = get_the_tags();
        		    if ($posttags) {
        		        $the_tags_post = '';
        		        foreach ($posttags as $tag) {
        		            $the_tags_post .= $tag->name . ',';
        		        }
        		        echo '<meta name="keywords" content="' . trim($the_tags_post, ',') . '">' ."\n";
        		    }
        	    }
        	endwhile;endif;
        }else {
        	$fb_share_image = askme_options("fb_share_image");
        	$logo_display = askme_options("logo_display");
        	$logo_img = askme_options("logo_img");
        	if (!empty($fb_share_image)) {
        		echo '<meta property="og:image" content="' . $fb_share_image . '" />' . "\n";
        	}else if ($logo_display == "custom_image" && isset($logo_img) && $logo_img != "") {
        		echo '<meta property="og:image" content="' . $logo_img . '" />' . "\n";
        	}
        	echo '<meta property="og:title" content="'.get_bloginfo('name').'" />' . "\n";
        	echo '<meta property="og:url" content="'.home_url().'" />' . "\n";
        	echo '<meta property="og:description" content="'.get_bloginfo('description').'" />' . "\n";
	        echo "<meta name='keywords' content='".$the_seo."'>" ."\n";
        }
    }
    
    /* head_code */
    if(askme_options("head_code")) {
        echo stripslashes(askme_options("head_code"));
    }
}
add_action('wp_head', 'vbegy_head');
function vbegy_footer() {
    /* footer_code */
    if(askme_options("footer_code")) {
        echo stripslashes(askme_options("footer_code"));
    }
}
add_action('wp_footer', 'vbegy_footer');
/* wp login head */
function vbegy_login_logo() {
	$login_logo        = askme_options("login_logo");
	$login_logo_height = askme_options("login_logo_height");
	$login_logo_width  = askme_options("login_logo_width");
	if (isset($login_logo) && $login_logo != "") {
		echo '<style type="text/css">
		.login h1 a {
			background-image:url('.$login_logo.') !important;
			background-size: auto !important;
			'.(isset($login_logo_height) && $login_logo_height != ""?"height: ".$login_logo_height."px !important;":"").'
			'.(isset($login_logo_width) && $login_logo_width != ""?"width: ".$login_logo_width."px !important;":"").'
		}
		</style>';
	}
}
add_action('login_head',  'vbegy_login_logo');
/* all_css_color */
if (!function_exists('all_css_color')) :
	function all_css_color($color_1) {
		$all_css_color = '
		::-moz-selection {
		    background: '.esc_attr($color_1).';
		}
		::selection {
		    background: '.esc_attr($color_1).';
		}
		.more:hover,.button.color,.button.black:hover,.go-up,.widget_portfolio .portfolio-widget-item:hover .portfolio_img:before,.popular_posts .popular_img:hover a:before,.widget_flickr a:hover:before,.widget_highest_points .author-img a:hover:before,.question-author-img:hover span,.pagination a:hover,.pagination span:hover,.pagination span.current,.about-author .author-image a:hover:before,.avatar-img a:hover:before,.question-comments a,.flex-direction-nav li a:hover,.button.dark_button.color:hover,.table-style-2 thead th,.progressbar-percent,.carousel-arrow a:hover,.box_icon:hover .icon_circle,.box_icon:hover .icon_soft_r,.box_icon:hover .icon_square,.bg_default,.box_warp_colored,.box_warp_hover:hover,.post .boxedtitle i,.single-question-title i,.question-type,.post-type,.social_icon a,.page-content .boxedtitle,.main-content .boxedtitle,.flex-caption h2,.flex-control-nav li a.flex-active,.bxslider-overlay:before,.navigation .header-menu ul li ul li:hover > a,.navigation .header-menu ul li ul li.current_page_item > a,#header-top,.navigation > .header-menu > ul > li:hover > a,.navigation > .header-menu > ul > li.current_page_item > a,.navigation > .header-menu > ul > li.current-menu-item > a,.top-after-header,.breadcrumbs,#footer-bottom .social_icons ul li a:hover,.tagcloud a:hover,input[type="checkbox"],.login-password a:hover,.tab a.current,.question-type-main,.question-report:hover,.load-questions,.del-poll-li:hover,.styled-select::before,.fileinputs span,.post .post-type,.divider span,.widget_menu li.current_page_item a,.accordion .accordion-title.active a,.tab-inner-warp,.navigation_mobile,.user-profile-img a:hover:before,.post-pagination > span,#footer.footer_dark .tagcloud a:hover,input[type="submit"],.woocommerce button[type="submit"],.post-delete a,.post-edit a,.woocommerce [type="submit"][name="update_cart"]:hover,.buttons .button.wc-forward:hover,.button.checkout.wc-forward,.woocommerce .widget_price_filter .price_slider_wrapper .ui-widget-content,.woocommerce-page .widget_price_filter .price_slider_wrapper .ui-widget-content,ul.products li .woocommerce_product_thumbnail .woocommerce_woo_cart_bt .button,ul.products li .woocommerce_product_thumbnail .yith-wcwl-add-button .add_to_wishlist,.cart_list .remove,.wc-proceed-to-checkout .button.wc-forward,.single_add_to_cart_button,.return-to-shop a,.button-default.empty-cart,.wc-proceed-to-checkout a,.button[name="calc_shipping"],.price_slider_amount button.button[type="submit"],.button.checkout.wc-forward,.button.view,#footer.footer_dark .buttons .button.wc-forward,#footer.footer_dark .buttons .button.wc-forward:first-child:hover,.woocommerce-MyAccount-downloads-file.button.alt,.ask-button:hover,.ui-datepicker-header,.ui-datepicker-current-day,.mobile-bar-apps-colored .mobile-bar-content,.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
			 background-color: '.esc_attr($color_1).';
		}
		p a,li a, a:hover,.button.normal:hover,span.color,#footer a:hover,.widget a:hover,.question h3 a:hover,.boxedtitle h1 a:hover,.boxedtitle h2 a:hover,.boxedtitle h3 a:hover,.boxedtitle h4 a:hover,.boxedtitle h5 a:hover,.boxedtitle h6 a:hover,.box_icon:hover span i,.color_default,.navigation_mobile > ul a:hover,.navigation_mobile > ul li ul li:hover:before,.post .post-meta .meta-author a:hover,.post .post-meta .meta-categories a:hover,.post .post-meta .meta-comment a:hover,.question h2 a:hover,.question-category a:hover,.question-reply:hover i,.question-category a:hover i,.question-comment a:hover,.question-comment a:hover i,.question-reply:hover,.post .post-meta .meta-author:hover a,.post .post-meta .meta-author:hover i,.post .post-meta .meta-categories:hover i,.post .post-meta .meta-comment:hover a,.post .post-meta .meta-comment:hover i,.post-title a:hover,.question-tags a,.question .question-type,.comment-author a:hover,.comment-reply:hover,.user-profile-widget li a:hover,.taglist .tag a.delete:before,.form-style p span.color,.post-tags,.post-tags a,.related-posts li a:hover,.related-posts li a:hover i,#footer.footer_light_top .related-posts li a:hover,.related-posts li a:hover i,.share-inside,.share-inside-warp ul li a:hover,.user-points .question-vote-result,.navigation > .header-menu > ul > li > a > .menu-nav-arrow,#footer-bottom a,.widget h3.widget_title,#footer .related-item span,.widget_twitter ul li:before,#footer .widget_twitter .tweet_time a,.widget_highest_points li h6 a,#footer .widget_contact ul li span,.rememberme label,.ask_login .ask_captcha_p i,.login-text i,.subscribe-text i,.widget_search .search-submit,.login-password i,.question-tags,.question-tags i,.panel-pop h2,input[type="text"],input[type="password"],input[type="email"],input[type="url"],input[type="number"],textarea,select,.panel-pop p,.main-content .page-content .boxedtitle.page-title h2,.fakefile button,.login p,.login h2,.contact-us h2,.share-inside i,#related-posts h2,.comment-reply,.post-title,.post-title a,.user-profile h2,.user-profile h2 a,.stats-head,.block-stats-1,.block-stats-2,.block-stats-3,.block-stats-4,.user-question h3 a,.icon_shortcode .ul_icons li,.testimonial-client span,.box_icon h1,.box_icon h2,.box_icon h3,.box_icon h4,.box_icon h5,.box_icon h6,.widget_contact ul li i,#footer.footer_light_top .widget a:hover,#header .logo h2 a:hover,.widget_tabs.tabs-warp .tabs li a,#footer .widget .widget_highest_points a,#footer .related-item h3 a:hover,#footer.footer_dark .widget .widget_comments a:hover,#footer .widget_tabs.tabs-warp .tabs li a,.dark_skin .sidebar .widget a:hover,.user-points h3,.woocommerce mark,.woocommerce .product_list_widget ins span,.woocommerce-page .product_list_widget ins span,ul.products li .product-details h3 a:hover,ul.products li .product-details .price,ul.products li .product-details h3 a:hover,ul.products li .product-details > a:hover,.widget.woocommerce:not(.widget_product_categories):not(.widget_layered_nav) ul li a:hover,.price > .amount,.woocommerce-page .product .woocommerce-woo-price ins span,.cart_wrapper .widget_shopping_cart_content ul li a:hover,.woocommerce-billing-fields > h3,#order_review_heading,.woocommerce .sections h2,.yith-wcwl-share > h4,.woocommerce .sections h3,.woocommerce header.title h3,.main-title > h4,.woocommerce h2,.post-content .woocommerce h3,.box-default.woocommerce-message .button,.woocommerce .cart .product-name a:hover,header.title a,.widget_search label:before,.post .post-meta .post-view a:hover,.post .post-meta .post-view:hover a,.post .post-meta .post-view:hover i,.question-author-meta a:hover,.question-author-meta a:hover i,ul.login-links a:hover,input[type="tel"],.styled-select select,.woocommerce-MyAccount-content .woocommerce-Button.button,.widget_categories .accordion .accordion-title a:hover,.dark_skin .widget_categories .accordion .accordion-title a:hover,.select2-container--default .select2-selection--single,.select2-container--default .select2-selection--single .select2-selection__rendered {
			 color: '.esc_attr($color_1).';
		}
		.loader_html,input[type="text"]:focus,input[type="password"]:focus,input[type="email"]:focus,input[type="url"]:focus,input[type="number"]:focus,textarea:focus,.box_icon .form-style textarea:focus,.social_icon a,#footer-bottom .social_icons ul li a:hover,.widget_login input[type="text"],.widget_search input[type="text"],.widget_search input[type="search"],.widget_product_search input[type="search"],.subscribe_widget input[type="text"],.widget_login input[type="password"],.panel_light.login-panel input[type="text"],.panel_light.login-panel input[type="password"],#footer.footer_dark .tagcloud a:hover,#footer.footer_dark .widget_search input[type="text"],.widget_search input[type="search"]:focus,#footer.footer_dark .subscribe_widget input[type="text"]:focus,#footer.footer_dark .widget_login input[type="text"]:focus,#footer.footer_dark .widget_login input[type="password"]:focus,.dark_skin .sidebar .widget_search input[type="text"],.widget_search input[type="search"]:focus,.dark_skin .sidebar .subscribe_widget input[type="text"]:focus,.dark_skin .sidebar .widget_login input[type="text"]:focus,.dark_skin .sidebar .widget_login input[type="password"]:focus,input[type="tel"]:focus,.sidebar .tagcloud a:hover,.tagcloud a:hover {
			border-color: '.esc_attr($color_1).';
		}
		.tabs {
			border-bottom-color: '.esc_attr($color_1).';
		}
		.tab a.current {
			border-top-color: '.esc_attr($color_1).';
		}
		.tabs-vertical .tab a.current,blockquote {
			border-right-color: '.esc_attr($color_1).';
		}
		blockquote {
			border-left-color: '.esc_attr($color_1).';
		}';
		$color_1_rgb = hex2rgb($color_1);
		if (isset($color_1_rgb) && is_array($color_1_rgb)) {
			$all_css_color .= '
			.top-after-header .col-md-9 p textarea,.widget_login input[type="text"],.widget_search input[type="text"],.widget_search input[type="search"],.widget_product_search input[type="search"],.subscribe_widget input[type="text"],.widget_login input[type="password"],.panel_light.login-panel input[type="text"],.panel_light.login-panel input[type="password"],blockquote,.qoute {
				background: rgba('.implode(",",$color_1_rgb).',0.20);
			}';
		}
		return $all_css_color;
	}
endif;
/* Content Width */
if (!isset( $content_width )) {
	$content_width = 785;
}
/* Remove private questions from API */
add_filter('rest_prepare_'.ask_questions_type,'askme_remove_user_questions',10,3);
add_filter('rest_prepare_'.ask_asked_questions_type,'askme_remove_user_questions',10,3);
if (!function_exists('askme_remove_user_questions')) :
	function askme_remove_user_questions($data,$post,$request) {
		$_data = $data->data;
		$params = $request->get_params();
		$user_id          = get_post_meta($_data['id'],"user_id",true);
		$user_is_comment  = get_post_meta($_data['id'],"user_is_comment",true);
		$private_question = get_post_meta($_data['id'],"private_question",true);
		if ($private_question == 1 || $private_question == "on" || ($user_id != "" && $user_is_comment != true)) {
			unset($_data["id"]);
			unset($_data["guid"]);
			unset($_data["content"]);
			unset($_data["slug"]);
			unset($_data["title"]);
			unset($_data["link"]);
			unset($_data["author"]);
			unset($_data["_links"]);
			foreach($data->get_links() as $_linkKey => $_linkVal) {
				$data->remove_link($_linkKey);
			}
		}
		$data->data = $_data;
		return $data;
	}
endif;
/* vpanel_feed_request */
function vpanel_feed_request ($qv) {
	if (isset($qv['feed']) && !isset($qv['post_type'])) {
		$qv['post_type'] = array('post', ask_questions_type, ask_asked_questions_type, 'product');
	}
	return $qv;
}
add_filter('request', 'vpanel_feed_request');?>