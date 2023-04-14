<?php /* Backup settings */
add_action("askme_options_page", "askme_backup_settings");
function askme_backup_settings()
{
	if (isset($_GET['backup']) && $_GET['backup'] == 'settings') {
		$json = "backup-" . date('Y-m-d', time()) . ".txt";
		$file = fopen($json, 'w');
		$export = array(askme_options, "sidebars", "coupons", "roles");
		$current_options = array();
		foreach ($export as $option) {
			$get_option_ = get_option($option);
			if ($get_option_) {
				$current_options[$option] = $get_option_;
			} else {
				$current_options[$option] = array();
			}
		}
		$array_json = json_encode($current_options);
		$array_json = base64_encode($array_json);
		fwrite($file, $array_json);
		fclose($file);
		header('Content-disposition: attachment; filename=' . $json);
		header('Content-type: text/xml');
		ob_clean();
		flush();
		readfile($json);
		exit();
	}
}
function askme_admin_options()
{

	$buttons = 'bold,|,italic,|,underline,|,link,unlink,|,bullist,numlist,qaimage,qacode';
	$wp_editor_settings = array(
		'wpautop'       => false,
		'textarea_rows' => 10,
		'quicktags' 	=> false,
		'media_buttons' => false,
		'tabindex' 		=> 5,
		'tinymce' 		=> array(
			'toolbar1'              => $buttons,
			'toolbar2'              => '',
			'toolbar3'              => '',
			'autoresize_min_height' => 300,
			'force_p_newlines'      => false,
			'statusbar'             => true,
			'force_br_newlines'     => false
		),
	);

	// Background Defaults
	$background_defaults = array(
		'color' => '',
		'image' => '',
		'repeat' => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	);

	// Pull all the categories into an array
	$options_categories = array();
	$args = array(
		'type'                     => 'post',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => 0,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'category',
		'pad_counts'               => false
	);

	$options_categories_obj = get_categories($args);
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}

	// Pull all the question category into an array
	$options_categories_q = array();
	$args = array(
		'type'                     => ask_questions_type,
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'name',
		'order'                    => 'ASC',
		'hide_empty'               => 0,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => ask_question_category,
		'pad_counts'               => false
	);

	$options_categories_obj_q = get_categories($args);
	$options_categories_q = array();
	foreach ($options_categories_obj_q as $category_q) {
		$options_categories_q[$category_q->term_id] = $category_q->name;
	}

	// Pull all the groups into an array
	$options_groups = array();
	global $wp_roles;
	$options_groups_obj = $wp_roles->roles;
	foreach ($options_groups_obj as $key_r => $value_r) {
		$options_groups[$key_r] = $value_r['name'];
	}

	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ($options_tags_obj as $tag) {
		$options_tags[$tag->term_id] = $tag->name;
	}

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// Pull all the sidebars into an array
	$new_sidebars = askme_registered_sidebars();

	$export = array(askme_options, "sidebars", "coupons", "roles");
	$current_options = array();
	foreach ($export as $options) {
		if (get_option($options)) {
			$current_options[$options] = get_option($options);
		} else {
			$current_options[$options] = array();
		}
	}
	$current_options_e = json_encode($current_options);
	$current_options_e = base64_encode($current_options_e);

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/admin/images/';
	$imagepath_theme =  get_template_directory_uri() . '/images/';

	$options = array();

	$options[] = array(
		'name' => esc_html__('General settings', 'vbegy'),
		'icon' => 'admin-site',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Activate the lightbox at the site', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to active the lightbox at the site.', 'vbegy'),
		'id' => 'active_lightbox',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the go up button at the site', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to active the go up button at the site.', 'vbegy'),
		'id'   => 'go_up_button',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the add question button at the site', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to active the add question button at the site beside go to up button.', 'vbegy'),
		'id'   => 'ask_button',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Hide the top bar for WordPress', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the top bar for WordPress.', 'vbegy'),
		'id'   => 'top_bar_wordpress',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$groups_no_admin = $options_groups;
	unset($groups_no_admin["administrator"]);

	$options[] = array(
		'name'    => esc_html__("Choose the groups you need not allowed for they to see the wp admin top bar.", 'vbegy'),
		'id'      => 'top_bar_groups',
		'type'    => 'multicheck',
		'options' => $groups_no_admin,
		'condition' => 'top_bar_wordpress:not(0)',
		'std'     => array('activation' => 'activation', 'subscriber' => 'subscriber', 'author' => 'author'),
	);

	$options[] = array(
		'name' => esc_html__('Do you need to redirect unlogged from wp admin?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to redirect the unlogged from the wp admin to the theme login page.', 'vbegy'),
		'id'   => 'redirect_wp_admin_unlogged',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to redirect user from wp admin?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to redirect the user from the wp admin.', 'vbegy'),
		'id'   => 'redirect_wp_admin',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__("Choose the groups you need not allowed for they to see the wp admin.", 'vbegy'),
		'id'        => 'redirect_groups',
		'type'      => 'multicheck',
		'options'   => $groups_no_admin,
		'condition' => 'redirect_wp_admin:not(0)',
		'std'       => array('activation' => 'activation', 'subscriber' => 'subscriber', 'author' => 'author'),
	);

	$options[] = array(
		'name' => esc_html__('Enable loader', 'vbegy'),
		'desc' => esc_html__('Select ON to enable loader.', 'vbegy'),
		'id' => 'loader',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Type the date format see this link also : https://codex.wordpress.org/Formatting_Date_and_Time', 'vbegy'),
		'desc' => esc_html__('Type here your date format.', 'vbegy'),
		'id' => 'date_format',
		'std' => 'F j, Y',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__("Head code", 'vbegy'),
		'desc' => esc_html__("Past your Google analytics code in the box", 'vbegy'),
		'id'   => 'head_code',
		'std'  => '',
		'type' => 'textarea'
	);

	$options = apply_filters('askme_after_head_code_options', $options);

	$options[] = array(
		'name' => esc_html__("Footer code", 'vbegy'),
		'desc' => esc_html__("Paste footer code in the box", 'vbegy'),
		'id' => 'footer_code',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => esc_html__("Custom CSS code", 'vbegy'),
		'desc' => esc_html__("Advanced CSS options, Paste your CSS code in the box", 'vbegy'),
		'id' => 'custom_css',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => esc_html__('Enable SEO options', 'vbegy'),
		'desc' => esc_html__('Select ON to enable SEO options.', 'vbegy'),
		'id' => 'seo_active',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'seo_active:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__("SEO keywords", 'vbegy'),
		'desc' => esc_html__("Paste your keywords in the box", 'vbegy'),
		'id' => 'the_keywords',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => esc_html__("Share image", 'vbegy'),
		'desc' => esc_html__("This is the share image", 'vbegy'),
		'id' => 'fb_share_image',
		'type' => 'upload'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__("WordPress login logo", 'vbegy'),
		'desc' => esc_html__("This is the logo that appears on the default WordPress login page", 'vbegy'),
		'id' => 'login_logo',
		'type' => 'upload'
	);

	$options[] = array(
		"name" => esc_html__("WordPress login logo height", 'vbegy'),
		"id" => "login_logo_height",
		"type" => "sliderui",
		"step" => "1",
		"min" => "0",
		"max" => "300"
	);

	$options[] = array(
		"name" => esc_html__("WordPress login logo width", 'vbegy'),
		"id" => "login_logo_width",
		"type" => "sliderui",
		"step" => "1",
		"min" => "0",
		"max" => "300"
	);

	$options[] = array(
		'name' => esc_html__("Custom favicon", 'vbegy'),
		'desc' => esc_html__("Upload the site’s favicon here, You can create new favicon here favicon.", 'vbegy'),
		'id' => 'favicon',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__("Custom favicon for iPhone", 'vbegy'),
		'desc' => esc_html__("Upload your custom iPhone favicon", 'vbegy'),
		'id' => 'iphone_icon',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__("Custom iPhone retina favicon", 'vbegy'),
		'desc' => esc_html__("Upload your custom iPhone retina favicon", 'vbegy'),
		'id' => 'iphone_icon_retina',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__("Custom favicon for iPad", 'vbegy'),
		'desc' => esc_html__("Upload your custom iPad favicon", 'vbegy'),
		'id' => 'ipad_icon',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__("Custom iPad retina favicon", 'vbegy'),
		'desc' => esc_html__("Upload your custom iPad retina favicon", 'vbegy'),
		'id' => 'ipad_icon_retina',
		'type' => 'upload'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options = apply_filters("askme_options_after_general_setting", $options, $options_pages);

	$options[] = array(
		'name' => esc_html__('Header settings', 'vbegy'),
		'icon' => 'menu',
		'type' => 'heading'
	);

	$options[] = array(
		'name' => esc_html__('Top panel', 'vbegy'),
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Top panel settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the top panel.', 'vbegy'),
		'id' => 'login_panel',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'login_panel:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__("Select top panel skin", 'vbegy'),
		'desc' => esc_html__("Select your preferred skin for the top panel.", 'vbegy'),
		'id' => "top_panel_skin",
		'std' => "panel_dark",
		'type' => "images",
		'options' => array(
			'panel_dark' => $imagepath . 'panel_dark.jpg',
			'panel_light' => $imagepath . 'panel_light.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Select side panel skin", 'vbegy'),
		'desc' => esc_html__("Select your preferred skin for the side panel.", 'vbegy'),
		'id' => "side_panel_skin",
		'std' => "dark",
		'type' => "images",
		'options' => array(
			'dark'  => $imagepath . 'menu_dark.jpg',
			'gray'  => $imagepath . 'sidebar_no.jpg',
			'light' => $imagepath . 'menu_light.jpg'
		)
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Header setting', 'vbegy'),
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Header top menu settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the top menu in the header.', 'vbegy'),
		'id' => 'top_menu',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Top header Layout", 'vbegy'),
		'desc' => esc_html__("Top header columns Layout.", 'vbegy'),
		'id' => "top_header_layout",
		'condition' => 'top_menu:not(0)',
		'std' => "2c",
		'type' => "images",
		'options' => array(
			'2c'          => $imagepath . '2c.jpg',
			'header_2c_2' => $imagepath . 'header_2c_2.jpg',
			'header_2c_3' => $imagepath . 'header_2c_3.jpg',
			'menu'        => $imagepath . 'menu.jpg',
			'left_ontent' => $imagepath . 'left_ontent.jpg'
		)
	);

	if (is_rtl()) {
		$options[] = array(
			'name' => esc_html__("Logo position", 'vbegy'),
			'desc' => esc_html__("Select where you would like your logo to appear.", 'vbegy'),
			'id' => "logo_position",
			'std' => "left_logo",
			'type' => "images",
			'options' => array(
				'left_logo' => $imagepath . 'right_logo.jpg',
				'right_logo' => $imagepath . 'left_logo.jpg',
				'center_logo' => $imagepath . 'center_logo.jpg'
			)
		);
	} else {
		$options[] = array(
			'name' => esc_html__("Logo position", 'vbegy'),
			'desc' => esc_html__("Select where you would like your logo to appear.", 'vbegy'),
			'id' => "logo_position",
			'std' => "left_logo",
			'type' => "images",
			'options' => array(
				'left_logo' => $imagepath . 'left_logo.jpg',
				'right_logo' => $imagepath . 'right_logo.jpg',
				'center_logo' => $imagepath . 'center_logo.jpg'
			)
		);
	}

	$options[] = array(
		'name' => esc_html__("Header skin", 'vbegy'),
		'desc' => esc_html__("Select your preferred header skin.", 'vbegy'),
		'id' => "header_skin",
		'std' => "header_dark",
		'type' => "images",
		'options' => array(
			'header_dark' => $imagepath . 'left_logo.jpg',
			'header_light' => $imagepath . 'header_light.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__('Fixed header option', 'vbegy'),
		'desc' => esc_html__('Select ON to enable fixed header.', 'vbegy'),
		'id' => 'header_fixed',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Header search settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the search in the header.', 'vbegy'),
		'id' => 'header_search',
		'std' => 1,
		'type' => 'checkbox'
	);

	if (class_exists('woocommerce')) {
		$options[] = array(
			'name' => esc_html__('Header cart settings', 'vbegy'),
			'desc' => esc_html__('Select ON to enable the cart in the header.', 'vbegy'),
			'id' => 'header_cart',
			'std' => 1,
			'type' => 'checkbox'
		);
	}

	$options[] = array(
		'name' => esc_html__('Header notifications settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the notifications in the header.', 'vbegy'),
		'id' => 'header_notifications',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Header notifications number', 'vbegy'),
		'desc' => esc_html__('Put the header notifications number.', 'vbegy'),
		'id' => 'notifications_number',
		'std' => 10,
		'condition' => 'header_notifications:not(0)',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Logo display', 'vbegy'),
		'desc' => esc_html__('choose Logo display.', 'vbegy'),
		'id' => 'logo_display',
		'std' => 'display_title',
		'type' => 'radio',
		'options' => array("display_title" => "Display site title", "custom_image" => "Custom Image")
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'logo_display:not(display_title)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Logo upload', 'vbegy'),
		'desc' => esc_html__('Upload your custom logo.', 'vbegy'),
		'id'   => 'logo_img',
		'std'  => $imagepath_theme . "logo.png",
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Logo retina upload', 'vbegy'),
		'desc' => esc_html__('Upload your custom logo retina.', 'vbegy'),
		'id'   => 'retina_logo',
		'std'  => $imagepath_theme . "logo-2x.png",
		'type' => 'upload'
	);

	$options[] = array(
		"name" => esc_html__("Logo height", 'vbegy'),
		"id" => "logo_height",
		"type" => "sliderui",
		"step" => "1",
		"min" => "0",
		"max" => "300",
		'std' => '57'
	);

	$options[] = array(
		"name" => esc_html__("Logo width", 'vbegy'),
		"id" => "logo_width",
		"type" => "sliderui",
		"step" => "1",
		"min" => "0",
		"max" => "300",
		'std' => '146'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Breadcrumbs settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable breadcrumbs.', 'vbegy'),
		'id' => 'breadcrumbs',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Breadcrumbs separator', 'vbegy'),
		'desc' => esc_html__('Add your breadcrumbs separator.', 'vbegy'),
		'id'   => 'breadcrumbs_separator',
		'std'  => '/',
		'condition' => 'breadcrumbs:not(0)',
		'type' => 'text',
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Big search setting', 'vbegy'),
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Big search after header', 'vbegy'),
		'desc' => esc_html__('Select ON to enable big search.', 'vbegy'),
		'id' => 'big_search',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Big search in all pages or home page only?', 'vbegy'),
		'desc'    => esc_html__('Big search work in all pages or home page only?', 'vbegy'),
		'id'      => 'big_search_work',
		'condition' => 'big_search:not(0)',
		'std'     => "all_pages",
		'options' => array(
			'home_page'     => 'Home page',
			'all_pages'     => 'All pages',
			'pages_no_home' => 'All pages without home',
		),
		'type'    => 'select'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Video setting', 'vbegy'),
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Big video after header', 'vbegy'),
		'desc' => esc_html__('Select ON to enable big video.', 'vbegy'),
		'id'   => 'big_video',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'big_video:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Big video in all pages or home page only?', 'vbegy'),
		'desc'    => esc_html__('Big video work in all pages or home page only?', 'vbegy'),
		'id'      => 'big_video_work',
		'std'     => "all_pages",
		'options' => array(
			'home_page'     => 'Home page',
			'all_pages'     => 'All pages',
			'pages_no_home' => 'All pages without home',
		),
		'type'    => 'select'
	);

	$options[] = array(
		'name'		=> esc_html__('Video height', 'vbegy'),
		'id'		=> 'video_height',
		'desc'		=> esc_html__('Put here the video height.', 'vbegy'),
		'type'		=> 'text',
		'std'		=> '500',
	);

	$options[] = array(
		'name'		=> esc_html__('Video type', 'vbegy'),
		'id'		=> 'video_type',
		'type'		=> 'select',
		'options'	=> array(
			'youtube'  => "Youtube",
			'vimeo'    => "Vimeo",
			'daily'    => "Dialymotion",
			'facebook' => "Facebook video",
			'tiktok'   => "TikTok video",
			'html5'    => "HTML 5",
			'embed'    => "Custom embed",
		),
		'std'		=> 'youtube',
		'desc'		=> esc_html__('Choose from here the video type', 'vbegy'),
	);

	$options[] = array(
		'name'		=> esc_html__('Video ID', 'vbegy'),
		'id'		=> 'video_id',
		'condition' => 'video_type:not(html5),video_type:not(embed)',
		'desc'		=> esc_html__('Put here the video id : https://www.youtube.com/watch?v=sdUUx5FdySs EX : "sdUUx5FdySs".', 'vbegy'),
		'type'		=> 'text',
	);

	$options[] = array(
		'name'		=> esc_html__('Custom embed', 'vbegy'),
		'id'		=> 'custom_embed',
		'desc'		=> esc_html__('Put your Custom embed html', 'vbegy'),
		'condition' => 'video_type:is(embed)',
		'type'		=> 'textarea',
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'video_type:is(html5)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Video Image', 'vbegy'),
		'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.', 'vbegy'),
		'id'   => 'video_image',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Mp4 video', 'vbegy'),
		'id'   => 'video_mp4',
		'desc' => esc_html__('Put here the mp4 video', 'vbegy'),
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('M4v video', 'vbegy'),
		'id'   => 'video_m4v',
		'desc' => esc_html__('Put here the m4v video', 'vbegy'),
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Webm video', 'vbegy'),
		'id'   => 'video_webm',
		'desc' => esc_html__('Put here the webm video', 'vbegy'),
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Ogv video', 'vbegy'),
		'id'   => 'video_ogv',
		'desc' => esc_html__('Put here the ogv video', 'vbegy'),
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Wmv video', 'vbegy'),
		'id'   => 'video_wmv',
		'desc' => esc_html__('Put here the wmv video', 'vbegy'),
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Flv video', 'vbegy'),
		'id'   => 'video_flv',
		'desc' => esc_html__('Put here the flv video', 'vbegy'),
		'type' => 'text',
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Responsive settings', 'vbegy'),
		'icon' => 'smartphone',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('The mobile apps bar enable or disable?', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the mobile apps bar.', 'vbegy'),
		'id'   => 'mobile_bar_apps',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_bar_apps:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Mobile apps bar skin', 'vbegy'),
		'desc'    => esc_html__('Choose the mobile apps bar skin.', 'vbegy'),
		'id'      => 'mobile_apps_bar_skin',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("dark" => esc_html__("Dark", "vbegy"), "light" => esc_html__("Light", "vbegy"), "colored" => esc_html__("Colored", "vbegy"))
	);

	$options[] = array(
		'name' => esc_html__('Type your iPhone app link', 'vbegy'),
		'id'   => 'mobile_bar_apps_iphone',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Type your Android app link', 'vbegy'),
		'id'   => 'mobile_bar_apps_android',
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__("Choose the mobile menu skin", 'vbegy'),
		'id'   => "mobile_menu",
		'std'  => "dark",
		'type' => "images",
		'options' => array(
			'dark'  => $imagepath . 'menu_dark.jpg',
			'gray'  => $imagepath . 'sidebar_no.jpg',
			'light' => $imagepath . 'menu_light.jpg',
		)
	);

	$options[] = array(
		'name' => esc_html__('Header top menu settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the top menu in the mobile menu.', 'vbegy'),
		'id' => 'top_menu_mobile',
		'std' => 1,
		'type' => 'checkbox'
	);

	if (class_exists('woocommerce')) {
		$options[] = array(
			'name' => esc_html__('Cart settings', 'vbegy'),
			'desc' => esc_html__('Select ON to enable the cart in the mobile menu.', 'vbegy'),
			'id' => 'mobile_cart',
			'std' => 1,
			'type' => 'checkbox'
		);
	}

	$options[] = array(
		'name' => esc_html__('Notifications settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the notifications in the mobile menu.', 'vbegy'),
		'id' => 'mobile_notifications',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Header menu settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the menu in the mobile menu.', 'vbegy'),
		'id' => 'main_menu_mobile',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Social enable or disable', 'vbegy'),
		'desc' => esc_html__('Social or disable.', 'vbegy'),
		'id' => 'social_mobile',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Search settings', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the search in the mobile menu.', 'vbegy'),
		'id' => 'search_mobile',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Ask question button', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the ask question button.', 'vbegy'),
		'id' => 'ask_question_mobile',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Home page', 'vbegy'),
		'icon' => 'admin-home',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__("Note: this options work in the home page only and if you don't choose the Front page.", "vbegy"),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Home top box settings', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to enable the home top box.', 'vbegy'),
		'id' => 'index_top_box',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Home top box layout', 'vbegy'),
		'id' => 'index_top_box_layout',
		'std' => '1',
		'type' => 'radio',
		'options' => array("1" => "Style 1", "2" => "Style 2")
	);

	$options[] = array(
		'name' => esc_html__('Question title or comment', 'vbegy'),
		'id' => 'index_title_comment',
		'std' => 'title',
		'type' => 'radio',
		'options' => array("title" => "Title", "comment" => "Comment")
	);

	$options[] = array(
		'name' => esc_html__('Remove the content?', 'vbegy'),
		'desc' => esc_html__('Remove the content (Title, Content, Buttons and Ask question)?', 'vbegy'),
		'id'   => 'remove_index_content',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Home top box background', 'vbegy'),
		'id'      => 'index_top_box_background',
		'std'     => 'background',
		'type'    => 'hidden',
	);

	$options[] = array(
		'name' => esc_html__("Background", 'vbegy'),
		'desc' => esc_html__("Upload a image, Or enter URL to an image if it is already uploaded.", 'vbegy'),
		'id' => 'background_home',
		'std' => $background_defaults,
		'type' => 'background'
	);

	$options[] = array(
		'name' => esc_html__("Full Screen Background", 'vbegy'),
		'id'   => "background_full_home",
		'type' => 'checkbox',
		'std'  => 0,
	);

	$options[] = array(
		'name' => esc_html__('Home top box title', 'vbegy'),
		'desc' => esc_html__('Put the Home top box title.', 'vbegy'),
		'id' => 'index_title',
		'std' => 'Welcome to Lumeno',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Home top box content', 'vbegy'),
		'desc' => esc_html__('Put the Home top box content.', 'vbegy'),
		'id' => 'index_content',
		'std' => 'Duis dapibus aliquam mi, Eget euismod sem scelerisque ut.Vivamus at elit quis urna adipiscing iaculis.Curabitur vitae velit in neque dictum blandit.Proin in iaculis neque.',
		'type' => 'textarea'
	);

	$options[] = array(
		'name' => esc_html__('About Us title', 'vbegy'),
		'desc' => esc_html__('Put the About Us title.', 'vbegy'),
		'id' => 'index_about',
		'std' => 'About Us',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('About Us link', 'vbegy'),
		'desc' => esc_html__('Put the About Us link.', 'vbegy'),
		'id' => 'index_about_h',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Join Now title', 'vbegy'),
		'desc' => esc_html__('Put the Join Now title.', 'vbegy'),
		'id' => 'index_join',
		'std' => 'Join Now',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Join Now link', 'vbegy'),
		'desc' => esc_html__('Put the Join Now link.', 'vbegy'),
		'id' => 'index_join_h',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('About Us title if logged in', 'vbegy'),
		'desc' => esc_html__('Put the About Us title if logged in.', 'vbegy'),
		'id' => 'index_about_login',
		'std' => 'About Us',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('About Us link if login', 'vbegy'),
		'desc' => esc_html__('Put the About Us link if logged in.', 'vbegy'),
		'id' => 'index_about_h_login',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Ask question title if logged in', 'vbegy'),
		'desc' => esc_html__('Put the Ask question title if logged in.', 'vbegy'),
		'id' => 'index_join_login',
		'std' => 'Ask question',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Ask question link if logged in', 'vbegy'),
		'desc' => esc_html__('Put the Ask question link if logged in.', 'vbegy'),
		'id' => 'index_join_h_login',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => sprintf(esc_html__('Go to the page and add new page template %s, Choose the template page (Home) set it a static page %s.', 'vbegy'), '<a href="post-new.php?post_type=page">' . esc_html__("from here", "vbegy") . '</a>', '<a href="options-reading.php">' . esc_html__("from here", "vbegy") . '</a>'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$question_settings = apply_filters("askme_question_settings_tabs", array(
		"general_setting"   => esc_html__("General settings", "vbegy"),
		"question_slug"     => esc_html__("Question slugs", "vbegy"),
		"add_edit_delete"   => esc_html__("Add - Edit - Delete", "vbegy"),
		"questions_loop"    => esc_html__("Questions & Loop settings", "vbegy"),
		"inner_question"    => esc_html__("Inner question", "vbegy"),
		"questions_styling" => esc_html__("Questions styling", "vbegy"),
	));

	$options[] = array(
		'name'    => esc_html__('Questions', 'vbegy'),
		'icon'    => 'editor-help',
		'type'    => 'heading',
		'std'     => 'general_setting',
		'options' => $question_settings
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'general_setting',
		'name' => esc_html__("General settings", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Activate the reports in site?', 'vbegy'),
		'desc' => esc_html__('Activate the reports enable or disable.', 'vbegy'),
		'id' => 'active_reports',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the reports in site for the logged users only?', 'vbegy'),
		'desc' => esc_html__('Reports in site for the logged users only enable or disable.', 'vbegy'),
		'id' => 'active_logged_reports',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the user can check the best answer in site?', 'vbegy'),
		'desc' => esc_html__('Best answer enable or disable.', 'vbegy'),
		'id' => 'active_best_answer',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the vote in site?', 'vbegy'),
		'desc' => esc_html__('Vote enable or disable.', 'vbegy'),
		'id' => 'active_vote',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the vote in site for the unlgged users?', 'vbegy'),
		'desc' => esc_html__('Activate the vote enable or disable for the unlgged users.', 'vbegy'),
		'id' => 'active_vote_unlogged',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the mention in site?', 'vbegy'),
		'desc' => esc_html__('Activate the mention enable or disable.', 'vbegy'),
		'id'   => 'active_mention',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the points system in site?', 'vbegy'),
		'desc' => esc_html__('Activate the points system enable or disable.', 'vbegy'),
		'id' => 'active_points',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to hide the dislike at questions', 'vbegy'),
		'desc' => esc_html__('If you put it ON the dislike will not show.', 'vbegy'),
		'id' => 'show_dislike_questions',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('When delete the question or answer have a best answer remove it from the stats and user point?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to remove the best answer from the user point.', 'vbegy'),
		'id' => 'remove_best_answer_stats',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'question_slug',
		'name' => esc_html__("Question slugs", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Questions archive slug', 'vbegy'),
		'desc' => esc_html__('Add your questions archive slug.', 'vbegy'),
		'id'   => 'archive_questions_slug',
		'std'  => 'questions',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Click ON if you need to remove the question slug and put / in the questions slug.', 'vbegy'),
		'id' => 'remove_question_slug',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Click ON, if you need to make the question slug with number instant of title and choose "Post name" from WordPress Settings/Permalinks.', 'vbegy'),
		'id'   => 'question_slug_numbers',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Questions slug', 'vbegy'),
		'desc' => esc_html__('Add your questions slug.', 'vbegy'),
		'id' => 'question_slug',
		'condition' => 'remove_question_slug:is(0)',
		'std' => ask_questions_type,
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Questions category slug', 'vbegy'),
		'desc' => esc_html__('Add your questions category slug.', 'vbegy'),
		'id' => 'category_questions_slug',
		'std' => ask_question_category,
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Questions tag slug', 'vbegy'),
		'desc' => esc_html__('Add your questions tag slug.', 'vbegy'),
		'id' => 'tag_questions_slug',
		'std' => 'question-tag',
		'type' => 'text'
	);

	$options = apply_filters('askme_after_tag_slug', $options);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'add_edit_delete',
		'name' => esc_html__("Add - Edit - Delete", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Add question setting.', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__("Add question page", 'vbegy'),
		'desc' => esc_html__("Create a page using the Add question template and select it here", 'vbegy'),
		'id' => 'add_question',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Any one can ask question without register', 'vbegy'),
		'desc' => esc_html__('Any one can ask question without register enable or disable.', 'vbegy'),
		'id' => 'ask_question_no_register',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'ask_question_no_register',
		'condition' => 'ask_question_no_register:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Activate username and email for the non-registered users.', 'vbegy'),
		'desc' => esc_html__('The username and email for the non-registered users is enable or disable.', 'vbegy'),
		'id' => 'username_email_no_register',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Activate ask question form with popup also?', 'vbegy'),
		'desc' => esc_html__('Activate ask question form with popup is enable or disable.', 'vbegy'),
		'id' => 'ask_question_popup',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Charge points for questions', 'vbegy'),
		'desc' => esc_html__('How many points should be taken from the user’s account for asking questions.', 'vbegy'),
		'id' => 'question_points',
		'std' => '5',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Charge points for questions settings', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to charge points from users for asking questions.', 'vbegy'),
		'id' => 'question_points_active',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Point back to the user when he select the best answer', 'vbegy'),
		'id' => 'point_back',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'point_back',
		'condition' => 'point_back:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Or type here the point want back', 'vbegy'),
		'desc' => esc_html__('Or type here the point want back, Type 0 to back all the point.', 'vbegy'),
		'id' => 'point_back_number',
		'std' => '0',
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Choose question status for users only', 'vbegy'),
		'desc' => esc_html__('Choose question status after user publish the question.', 'vbegy'),
		'id' => 'question_publish',
		'options' => array("publish" => esc_html__("Auto publish", "vbegy"), "draft" => esc_html__("Need a review", "vbegy")),
		'std' => 'draft',
		'type' => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Choose question status for unlogged user only', 'vbegy'),
		'desc' => esc_html__('Choose question status after unlogged user publish the question.', 'vbegy'),
		'id' => 'question_publish_unlogged',
		'options' => array("publish" => esc_html__("Auto publish", "vbegy"), "draft" => esc_html__("Need a review", "vbegy")),
		'std' => 'draft',
		'type' => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Send email when the question need a review', 'vbegy'),
		'desc' => esc_html__('Email for questions review enable or disable.', 'vbegy'),
		'id' => 'send_email_draft_questions',
		'std' => 1,
		'type' => 'checkbox'
	);

	$ask_question_items = array(
		"title_question"       => array("sort" => esc_html__('Question Title', 'vbegy'), "value" => "title_question"),
		"categories_question"  => array("sort" => esc_html__('Question Categories', 'vbegy'), "value" => "categories_question"),
		"tags_question"        => array("sort" => esc_html__('Question Tags', 'vbegy'), "value" => "tags_question"),
		"poll_question"        => array("sort" => esc_html__('Question Poll', 'vbegy'), "value" => "poll_question"),
		"attachment_question"  => array("sort" => esc_html__('Question Attachment', 'vbegy'), "value" => "attachment_question"),
		"featured_image"       => array("sort" => esc_html__('Featured image', 'vbegy'), "value" => "featured_image"),
		"comment_question"     => array("sort" => esc_html__('Question content', 'vbegy'), "value" => "comment_question"),
		"anonymously_question" => array("sort" => esc_html__('Ask Anonymously', 'vbegy'), "value" => "anonymously_question"),
		"video_desc_active"    => array("sort" => esc_html__('Video Description', 'vbegy'), "value" => "video_desc_active"),
		"private_question"     => array("sort" => esc_html__('Private Question', 'vbegy'), "value" => "private_question"),
		"remember_answer"      => array("sort" => esc_html__('Remember Answer', 'vbegy'), "value" => "remember_answer"),
		"terms_active"         => array("sort" => esc_html__('Terms of Service and Privacy Policy', 'vbegy'), "value" => "terms_active"),
	);

	$ask_question_items_std = $ask_question_items;
	unset($ask_question_items_std["attachment_question"]);

	$options[] = array(
		'name'    => esc_html__("Select what's show at ask question form", "vbegy"),
		'id'      => 'ask_question_items',
		'type'    => 'multicheck_3',
		'sort'    => 'yes',
		'std'     => $ask_question_items_std,
		'options' => $ask_question_items
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'ask_question_items:has_not(title_question)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Excerpt type for title from the content', 'vbegy'),
		'desc' => esc_html__('Choose form here the excerpt type.', 'vbegy'),
		'id' => 'title_excerpt_type',
		'type' => "select",
		'options' => array(
			'words' => 'Words',
			'characters' => 'Characters'
		)
	);

	$options[] = array(
		'name' => esc_html__('Excerpt title from the content', 'vbegy'),
		'desc' => esc_html__('Put here the excerpt title from the content.', 'vbegy'),
		'id' => 'title_excerpt',
		'std' => 10,
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'categories_question',
		'condition' => 'ask_question_items:has(categories_question)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Category in ask question form is required', 'vbegy'),
		'id' => 'category_question_required',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Category at ask question form single, multi or ajax", 'vbegy'),
		'desc' => esc_html__("Choose category is show at ask question form single, multi or ajax", 'vbegy'),
		'id' => 'category_single_multi',
		'std' => 'single',
		'type' => 'radio',
		'options' =>
		array(
			"single" => "Single",
			"multi"  => "Multi",
			"ajax"  => "Ajax"
		)
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Details in ask question form is required', 'vbegy'),
		'id' => 'comment_question',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Editor enable or disable for details in add question form', 'vbegy'),
		'id' => 'editor_question_details',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Choose the way of the sending mails and notifications of a new question', 'vbegy'),
		'id'      => 'send_email_and_notification_question',
		'options' => array(
			'both'       => esc_html__('Both with the same options', 'vbegy'),
			'separately' => esc_html__('Separately', 'vbegy'),
		),
		'std'     => 'both',
		'type'    => 'radio'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'send_email_and_notification_question:has(both)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Send mail and notification to the users about the notification of a new question', 'vbegy'),
		'desc' => esc_html__('Send mail and notification enable or disable.', 'vbegy'),
		'id'   => 'send_email_new_question_both',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Send mail and notification for custom roles about the notification of a new question', 'vbegy'),
		'id'        => 'send_email_question_groups_both',
		'type'      => 'multicheck',
		'condition' => 'send_email_new_question_both:not(0)',
		'std' => array("editor" => 1, "administrator" => 1, "author" => 1, "contributor" => 1, "subscriber" => 1),
		'options'   => $options_groups
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'send_email_and_notification_question:has(separately)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Send email for the user to notified a new question', 'vbegy'),
		'desc' => esc_html__('Send email enable or disable.', 'vbegy'),
		'id' => 'send_email_new_question',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'send_email_new_question',
		'condition' => 'send_email_new_question:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Send email for custom groups to notified a new question', 'vbegy'),
		'id' => 'send_email_question_groups',
		'type' => 'multicheck',
		'std' => array("editor" => 1, "administrator" => 1, "author" => 1, "contributor" => 1, "subscriber" => 1),
		'options' => $options_groups
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Send notification for the user to notified a new question', 'vbegy'),
		'desc' => esc_html__('Send notification enable or disable.', 'vbegy'),
		'id' => 'send_notification_new_question',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'send_notification_new_question',
		'condition' => 'send_notification_new_question:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Send notification for custom groups to notified a new question', 'vbegy'),
		'id' => 'send_notification_question_groups',
		'type' => 'multicheck',
		'std' => array("editor" => 1, "administrator" => 1, "author" => 1, "contributor" => 1, "subscriber" => 1),
		'options' => $options_groups
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to hide the content only for the private question?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the content only for the private question.', 'vbegy'),
		'id'   => 'private_question_content',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'terms_active',
		'condition' => 'ask_question_items:has(terms_active)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('Terms of Service and Privacy Policy', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id' => 'terms_active_target',
		'std' => "new_page",
		'type' => 'select',
		'options' => array("same_page" => "Same page", "new_page" => "New page")
	);

	$options[] = array(
		'name' => esc_html__("Terms page", 'vbegy'),
		'desc' => esc_html__("Select the terms page", 'vbegy'),
		'id' => 'terms_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Type the terms link if you don't like a page", 'vbegy'),
		'id' => 'terms_link',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Activate Privacy Policy', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to activate Privacy Policy.', 'vbegy'),
		'id'   => 'privacy_policy',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'privacy_policy:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'privacy_active_target',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'name'    => esc_html__('Privacy Policy page', 'vbegy'),
		'desc'    => esc_html__('Select the privacy policy page', 'vbegy'),
		'id'      => 'privacy_page',
		'type'    => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Type the privacy policy link if you don't like a page", "vbegy"),
		'id'   => 'privacy_link',
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name'      => esc_html__('Select the checked by default options when add a new question', 'vbegy'),
		'id'        => 'add_question_default',
		'type'      => 'multicheck',
		'std'       => array(
			"notified" => 1,
		),
		'options' => array(
			"poll"        => esc_html__('Poll', 'vbegy'),
			"video"       => esc_html__('Video', 'vbegy'),
			"notified"    => esc_html__('Notified', 'vbegy'),
			"private"     => esc_html__("Private question", 'vbegy'),
			"anonymously" => esc_html__("Ask anonymously", 'vbegy'),
			"terms"       => esc_html__("Terms", 'vbegy'),
			"sticky"      => esc_html__("Sticky", 'vbegy'),
		)
	);

	$options[] = array(
		'name' => esc_html__('Edit question setting.', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__("Edit question page", 'vbegy'),
		'desc' => esc_html__("Create a page using the Edit question template and select it here", 'vbegy'),
		'id' => 'edit_question',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('After edit question approved auto or need to approved again?', 'vbegy'),
		'desc' => esc_html__('Press ON to approved auto', 'vbegy'),
		'id' => 'question_approved',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate user can edit the questions', 'vbegy'),
		'desc' => esc_html__('Select ON if you want the user can edit the questions.', 'vbegy'),
		'id' => 'question_edit',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('After edit question change the URL like the title?', 'vbegy'),
		'desc' => esc_html__('Press ON to edit the URL', 'vbegy'),
		'id' => 'change_question_url',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Delete question setting.', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Activate user can delete the questions', 'vbegy'),
		'desc' => esc_html__('Select ON if you want the user can delete the questions.', 'vbegy'),
		'id' => 'question_delete',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('When the users delete the question went to the trash or delete it ever?', 'vbegy'),
		'id'      => 'delete_question',
		'options' => array(
			'delete' => esc_html__('Delete', 'vbegy'),
			'trash'  => esc_html__('Trash', 'vbegy'),
		),
		'std'     => 'delete',
		'type'    => 'radio'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'questions_loop',
		'name' => esc_html__("Questions & Loop settings", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Select the meta for the questions loop', 'vbegy'),
		'id' => 'questions_meta',
		'type' => 'multicheck',
		'std' => array(
			"status" => 1,
			"category" => 1,
			"user_name" => 1,
			"date" => 1,
			"answer_meta" => 1,
			"view" => 1,
			"question_bump" => 1,
		),
		'options' => array(
			"status" => "Question status",
			"category" => "Category",
			"user_name" => "Username and asked to",
			"date" => "Date",
			"answer_meta" => "Answer meta",
			"view" => "Views",
			"question_bump" => "Question bump points",
		)
	);

	$options[] = array(
		'name' => esc_html__('Display Like/disLike in the loop', 'vbegy'),
		'desc' => esc_html__('Display Like/disLike in the loop enable or disable.', 'vbegy'),
		'id' => 'question_vote_show',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the author image in the questions loop', 'vbegy'),
		'desc' => esc_html__('If you put it OFF the author name will add in the meta.', 'vbegy'),
		'id' => 'question_author',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to show the poll in questions loop', 'vbegy'),
		'id'   => 'question_poll_loop',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to show featured image in the questions', 'vbegy'),
		'id' => 'featured_image_loop',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to enable the lightbox for featured image', 'vbegy'),
		'id' => 'featured_image_question_lightbox',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		"name" => esc_html__("Set the width for the featured image for the questions", 'vbegy'),
		"id" => "featured_image_question_width",
		"type" => "sliderui",
		'std' => 260,
		"step" => "1",
		"min" => "50",
		"max" => "600"
	);

	$options[] = array(
		"name" => esc_html__("Set the height for the featured image for the questions", 'vbegy'),
		"id" => "featured_image_question_height",
		"type" => "sliderui",
		'std' => 185,
		"step" => "1",
		"min" => "50",
		"max" => "600"
	);

	$options[] = array(
		'name'    => esc_html__('Featured image position', 'vbegy'),
		'desc'    => esc_html__('Choose the featured image position.', 'vbegy'),
		'id'      => 'featured_position',
		'options' => array("before" => "Before content", "after" => "After content"),
		'std'     => 'before',
		'type'    => 'select'
	);

	$options[] = array(
		'name' => esc_html__('Video description settings at the question loop', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to let users to add video with their question.', 'vbegy'),
		'id' => 'video_desc_active_loop',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'video_desc_active_loop',
		'condition' => 'video_desc_active_loop:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Video description position at the question loop', 'vbegy'),
		'desc' => esc_html__('Choose the video description position.', 'vbegy'),
		'id' => 'video_desc_loop',
		'options' => array("before" => "Before content", "after" => "After content"),
		'std' => 'after',
		'type' => 'select'
	);

	$options[] = array(
		"name" => esc_html__("Set the width for the video description for the questions", 'vbegy'),
		"id" => "video_description_width",
		"type" => "sliderui",
		'std' => 260,
		"step" => "1",
		"min" => "50",
		"max" => "600"
	);

	$options[] = array(
		'name' => esc_html__('Or set the video description with 100%?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to set the video description 100%.', 'vbegy'),
		'id' => 'video_desc_100_loop',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		"name" => esc_html__("Set the height for the video description for the questions", 'vbegy'),
		"id" => "video_description_height",
		"type" => "sliderui",
		'std' => 500,
		"step" => "1",
		"min" => "50",
		"max" => "600"
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to hide the excerpt in questions', 'vbegy'),
		'id' => 'excerpt_questions',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to show continue reading button in the questions', 'vbegy'),
		'id' => 'continue_reading_questions',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Excerpt type for question', 'vbegy'),
		'desc' => esc_html__('Choose form here the excerpt type.', 'vbegy'),
		'id' => 'question_excerpt_type',
		'type' => "select",
		'options' => array(
			'words' => 'Words',
			'characters' => 'Characters'
		)
	);

	$options[] = array(
		'name' => esc_html__('Excerpt question', 'vbegy'),
		'desc' => esc_html__('Put here the excerpt question.', 'vbegy'),
		'id' => 'question_excerpt',
		'std' => 40,
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Activate the answer at loop by best answer, most voted, last answer or first answer', 'vbegy'),
		'id'   => 'question_answer_loop',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Answer type', 'vbegy'),
		'desc'      => esc_html__("Choose what's the answer you need to show from here.", "vbegy"),
		'id'        => 'question_answer_show',
		'condition' => 'question_answer_loop:not(0)',
		'options'   => array(
			'best'   => esc_html__('Best answer', 'vbegy'),
			'vote'   => esc_html__('Most voted', 'vbegy'),
			'last'   => esc_html__('Last answer', 'vbegy'),
			'oldest' => esc_html__('First answer', 'vbegy'),
		),
		'std'       => 'best',
		'type'      => 'radio'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'inner_question',
		'name' => esc_html__("Inner question", 'vbegy'),
	);

	$order_sections = array(
		array("name" => esc_html__('Advertising', 'vbegy'), "value" => "advertising", "default" => "yes"),
		array("name" => esc_html__('About the author', 'vbegy'), "value" => "author", "default" => "yes"),
		array("name" => esc_html__('Related questions', 'vbegy'), "value" => "related", "default" => "yes"),
		array("name" => esc_html__('Advertising 2', 'vbegy'), "value" => "advertising_2", "default" => "yes"),
		array("name" => esc_html__('Comments', 'vbegy'), "value" => "comments", "default" => "yes"),
		array("name" => esc_html__('Next and Previous questions', 'vbegy'), "value" => "next_previous", "default" => "yes"),
	);

	$options[] = array(
		'name'    => esc_html__('Sort your sections', 'vbegy'),
		'id'      => 'question_order_sections',
		'type'    => 'sort',
		'std'     => $order_sections,
		'options' => $order_sections
	);

	$options[] = array(
		'name' => esc_html__('Select the meta for the single question page', 'vbegy'),
		'id' => 'questions_meta_single',
		'type' => 'multicheck',
		'std' => array(
			"status" => 1,
			"category" => 1,
			"user_name" => 1,
			"date" => 1,
			"answer_meta" => 1,
			"view" => 1,
		),
		'options' => array(
			"status" => "Question status",
			"category" => "Category",
			"user_name" => "Username and asked to",
			"date" => "Date",
			"answer_meta" => "Answer meta",
			"view" => "Views",
		)
	);

	$options[] = array(
		'name' => esc_html__('Video description position', 'vbegy'),
		'desc' => esc_html__('Choose the video description position.', 'vbegy'),
		'id' => 'video_desc',
		'options' => array("before" => "Before content", "after" => "After content"),
		'std' => 'after',
		'type' => 'select'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to show featured image in the single question', 'vbegy'),
		'id' => 'featured_image_single',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate poll for user only?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want the poll allow to users only.', 'vbegy'),
		'id' => 'poll_user_only',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select the question control style', 'vbegy'),
		'id' => 'question_control_style',
		'std' => "style_1",
		'type' => 'select',
		'options' => array("style_1" => "Style 1", "style_2" => "Style 2")
	);

	$options[] = array(
		'name' => esc_html__('Activate user can follow the questions', 'vbegy'),
		'desc' => esc_html__('Select ON if you want the user can follow the questions.', 'vbegy'),
		'id' => 'question_follow',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate close and open questions', 'vbegy'),
		'desc' => esc_html__('Select ON if you want active close and open questions.', 'vbegy'),
		'id' => 'question_close',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate close and open questions for the admin only', 'vbegy'),
		'desc' => esc_html__('Select ON if you want active close and open questions for the admin only.', 'vbegy'),
		'id'   => 'question_close_admin',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the question bump', 'vbegy'),
		'desc' => esc_html__('Select ON if you want the question bump.', 'vbegy'),
		'id' => 'question_bump',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Share enable or disable', 'vbegy'),
		'id' => 'question_share',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Author info box enable or disable', 'vbegy'),
		'id' => 'question_author_box',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Answers enable or disable', 'vbegy'),
		'id' => 'question_answers',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to show featured image in the question answers', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the featured image in the question answers.', 'vbegy'),
		'id' => 'featured_image_question_answers',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Related question enable or disable', 'vbegy'),
		'id' => 'related_question',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Related question number', 'vbegy'),
		'desc' => esc_html__('Type related question number from here.', 'vbegy'),
		'id' => 'related_number_question',
		'std' => '5',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__("Related question query", 'vbegy'),
		'desc' => esc_html__("Select your related question query.", 'vbegy'),
		'id' => "related_query_question",
		'std' => "categories",
		'type' => "select",
		'options' => array(
			'categories' => 'Categories',
			'tags' => 'Tags',
		)
	);

	$options[] = array(
		'name' => esc_html__('Navigation question enable or disable', 'vbegy'),
		'desc' => esc_html__('Navigation question (next and previous questions) enable or disable.', 'vbegy'),
		'id' => 'question_navigation',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Navigation question for the same category only?', 'vbegy'),
		'desc' => esc_html__('Navigation question (next and previous questions) for the same category only?', 'vbegy'),
		'id' => 'question_nav_category',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'questions_styling',
		'name' => esc_html__("Questions Styling", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__("Custom Logo position - Header skin - Logo display?", 'vbegy'),
		'desc' => esc_html__("Click ON to make a Custom Logo position - Header skin - Logo display", 'vbegy'),
		'id' => 'questions_custom_header',
		'std' => '0',
		'type' => 'checkbox'
	);

	if (is_rtl()) {
		$options[] = array(
			'name' => esc_html__("Logo position for questions", 'vbegy'),
			'desc' => esc_html__("Select where you would like your logo to appear for questions.", 'vbegy'),
			'id' => "questions_logo_position",
			'std' => "left_logo",
			'type' => "images",
			'options' => array(
				'left_logo' => $imagepath . 'right_logo.jpg',
				'right_logo' => $imagepath . 'left_logo.jpg',
				'center_logo' => $imagepath . 'center_logo.jpg'
			)
		);
	} else {
		$options[] = array(
			'name' => esc_html__("Logo position for questions", 'vbegy'),
			'desc' => esc_html__("Select where you would like your logo to appear for questions.", 'vbegy'),
			'id' => "questions_logo_position",
			'std' => "left_logo",
			'type' => "images",
			'options' => array(
				'left_logo' => $imagepath . 'left_logo.jpg',
				'right_logo' => $imagepath . 'right_logo.jpg',
				'center_logo' => $imagepath . 'center_logo.jpg'
			)
		);
	}

	$options[] = array(
		'name' => esc_html__("Header skin for questions", 'vbegy'),
		'desc' => esc_html__("Select your preferred header skin for questions.", 'vbegy'),
		'id' => "questions_header_skin",
		'std' => "header_dark",
		'type' => "images",
		'options' => array(
			'header_dark' => $imagepath . 'left_logo.jpg',
			'header_light' => $imagepath . 'header_light.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__('Logo display for questions', 'vbegy'),
		'desc' => esc_html__('choose Logo display for questions.', 'vbegy'),
		'id' => 'questions_logo_display',
		'std' => 'display_title',
		'type' => 'radio',
		'options' => array("display_title" => "Display site title", "custom_image" => "Custom Image")
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'questions_logo_display:not(display_title)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Logo upload for questions', 'vbegy'),
		'desc' => esc_html__('Upload your custom logo for questions.', 'vbegy'),
		'id' => 'questions_logo_img',
		'std' => '',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Logo retina upload for questions', 'vbegy'),
		'desc' => esc_html__('Upload your custom logo retina for questions.', 'vbegy'),
		'id' => 'questions_retina_logo',
		'std' => '',
		'type' => 'upload'
	);

	$options[] = array(
		"name" => esc_html__("Logo height", 'vbegy'),
		"id" => "questions_logo_height",
		"type" => "sliderui",
		"step" => "1",
		"min" => "0",
		"max" => "300",
		'std' => '57'
	);

	$options[] = array(
		"name" => esc_html__("Logo width", 'vbegy'),
		"id" => "questions_logo_width",
		"type" => "sliderui",
		"step" => "1",
		"min" => "0",
		"max" => "300",
		'std' => '146'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__("Questions sidebar layout", 'vbegy'),
		'id' => "questions_sidebar_layout",
		'std' => "default",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'right' => $imagepath . 'sidebar_right.jpg',
			'full' => $imagepath . 'sidebar_no.jpg',
			'left' => $imagepath . 'sidebar_left.jpg',
		)
	);

	$options[] = array(
		'name' => esc_html__("Questions Page Sidebar", 'vbegy'),
		'id' => "questions_sidebar",
		'std' => '',
		'options' => $new_sidebars,
		'type' => 'select'
	);

	$options[] = array(
		'name' => esc_html__("Questions page layout", 'vbegy'),
		'id' => "questions_layout",
		'std' => "default",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'full' => $imagepath . 'full.jpg',
			'fixed' => $imagepath . 'fixed.jpg',
			'fixed_2' => $imagepath . 'fixed_2.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Choose template", 'vbegy'),
		'desc' => esc_html__("Choose template layout.", 'vbegy'),
		'id' => "questions_template",
		'std' => "default",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'grid_1300' => $imagepath . 'template_1300.jpg',
			'grid_1200' => $imagepath . 'template_1200.jpg',
			'grid_970' => $imagepath . 'template_970.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Site skin", 'vbegy'),
		'desc' => esc_html__("Choose Site skin.", 'vbegy'),
		'id' => "questions_skin_l",
		'std' => "default",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'site_light' => $imagepath . 'light.jpg',
			'site_dark' => $imagepath . 'dark.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Choose Your Skin", 'vbegy'),
		'class' => "site_skin",
		'id' => "questions_skin",
		'std' => "default",
		'type' => "images",
		'options' => array(
			'default'	    => $imagepath . 'default.jpg',
			'skins'		    => $imagepath . 'skin.jpg',
			'blue'			=> $imagepath . 'blue.jpg',
			'gray'			=> $imagepath . 'gray.jpg',
			'green'			=> $imagepath . 'green.jpg',
			'moderate_cyan' => $imagepath . 'moderate_cyan.jpg',
			'orange'		=> $imagepath . 'orange.jpg',
			'purple'	    => $imagepath . 'purple.jpg',
			'red'			=> $imagepath . 'red.jpg',
			'strong_cyan'	=> $imagepath . 'strong_cyan.jpg',
			'yellow'		=> $imagepath . 'yellow.jpg',
		)
	);

	$options[] = array(
		'name' => esc_html__("Primary Color", 'vbegy'),
		'id' => 'questions_primary_color',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__("Background Type", 'vbegy'),
		'id' => 'questions_background_type',
		'std' => 'patterns',
		'type' => 'radio',
		'options' =>
		array(
			"patterns" => "Patterns",
			"custom_background" => "Custom Background"
		)
	);

	$options[] = array(
		'name' => esc_html__("Background Color", 'vbegy'),
		'id' => 'questions_background_color',
		'std' => "#FFF",
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__("Choose Pattern", 'vbegy'),
		'id' => "questions_background_pattern",
		'std' => "bg13",
		'type' => "images",
		'options' => array(
			'bg1' => $imagepath . 'bg1.jpg',
			'bg2' => $imagepath . 'bg2.jpg',
			'bg3' => $imagepath . 'bg3.jpg',
			'bg4' => $imagepath . 'bg4.jpg',
			'bg5' => $imagepath . 'bg5.jpg',
			'bg6' => $imagepath . 'bg6.jpg',
			'bg7' => $imagepath . 'bg7.jpg',
			'bg8' => $imagepath . 'bg8.jpg',
			'bg9' => $imagepath . '../../images/patterns/bg9.png',
			'bg10' => $imagepath . '../../images/patterns/bg10.png',
			'bg11' => $imagepath . '../../images/patterns/bg11.png',
			'bg12' => $imagepath . '../../images/patterns/bg12.png',
			'bg13' => $imagepath . 'bg13.jpg',
			'bg14' => $imagepath . 'bg14.jpg',
			'bg15' => $imagepath . '../../images/patterns/bg15.png',
			'bg16' => $imagepath . '../../images/patterns/bg16.png',
			'bg17' => $imagepath . 'bg17.jpg',
			'bg18' => $imagepath . 'bg18.jpg',
			'bg19' => $imagepath . 'bg19.jpg',
			'bg20' => $imagepath . 'bg20.jpg',
			'bg21' => $imagepath . '../../images/patterns/bg21.png',
			'bg22' => $imagepath . 'bg22.jpg',
			'bg23' => $imagepath . '../../images/patterns/bg23.png',
			'bg24' => $imagepath . '../../images/patterns/bg24.png',
		)
	);

	$options[] = array(
		'name' => esc_html__("Custom Background", 'vbegy'),
		'id' => 'questions_custom_background',
		'std' => $background_defaults,
		'type' => 'background'
	);

	$options[] = array(
		'name' => esc_html__("Full Screen Background", 'vbegy'),
		'desc' => esc_html__("Click ON to Full Screen Background", 'vbegy'),
		'id' => 'questions_full_screen_background',
		'std' => '0',
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options = apply_filters('askme_after_question_setting_options', $options);

	$options[] = array(
		'name' => esc_html__('Answers & comments', 'vbegy'),
		'icon' => 'format-chat',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the editor in the comment or answer', 'vbegy'),
		'id' => 'comment_editor',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Comments & answers enable or disable for user only', 'vbegy'),
		'id' => 'post_comments_user',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable to count the comments or answers only with replies or not', 'vbegy'),
		'id'   => 'count_comment_only',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Note: if you need all the answers/comments manually approved, From here Settings >> Discussion >> Comment must be manually approved.', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Choose answers/comments status for unlogged user only', 'vbegy'),
		'desc' => esc_html__('Choose answers/comments status after unlogged user publish the answers/comments.', 'vbegy'),
		'id' => 'comment_unlogged',
		'options' => array("publish" => esc_html__("Auto publish", "vbegy"), "draft" => esc_html__("Need a review", "vbegy")),
		'std' => 'draft',
		'type' => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Activate the private answer or not?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want active the private answer.', 'vbegy'),
		'id' => 'private_answer',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Activate the user who added the question to see private answer or not?', 'vbegy'),
		'desc'      => esc_html__('Select ON if you want active the user who added the question to see private answer.', 'vbegy'),
		'id'        => 'private_answer_user',
		'condition' => 'private_answer:not(0)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to hide the dislike at answers', 'vbegy'),
		'desc' => esc_html__('If you put it ON the dislike will not show.', 'vbegy'),
		'id' => 'show_dislike_answers',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Answers sort by", 'vbegy'),
		'desc' => esc_html__("Choose the answers sort by (it's show at the question page only)", 'vbegy'),
		'id' => 'answers_sort',
		'std' => 'date',
		'type' => 'radio',
		'options' =>
		array(
			"date" => "Date",
			"vote" => "Vote"
		)
	);

	$options[] = array(
		'name' => esc_html__('Attachment in a new answer form', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the attachment in a new answer form.', 'vbegy'),
		'id' => 'attachment_answer',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('Featured image', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Featured image in a new answer form', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the featured image in a new answer form.', 'vbegy'),
		'id' => 'featured_image_answer',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'featured_image_answer:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to show featured image in the answers', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the featured image in the answers.', 'vbegy'),
		'id' => 'featured_image_in_answers',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to enable the lightbox for featured image', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the lightbox for featured image.', 'vbegy'),
		'id' => 'featured_image_answers_lightbox',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		"name" => esc_html__("Set the width for the featured image for the answers", 'vbegy'),
		"id" => "featured_image_answer_width",
		"type" => "sliderui",
		'std' => 260,
		"step" => "1",
		"min" => "50",
		"max" => "600"
	);

	$options[] = array(
		"name" => esc_html__("Set the height for the featured image for the answers", 'vbegy'),
		"id" => "featured_image_answer_height",
		"type" => "sliderui",
		'std' => 185,
		"step" => "1",
		"min" => "50",
		"max" => "600"
	);

	$options[] = array(
		'name'    => esc_html__('Featured image position', 'vbegy'),
		'desc'    => esc_html__('Choose the featured image position.', 'vbegy'),
		'id'      => 'featured_answer_position',
		'options' => array("before" => "Before content", "after" => "After content"),
		'std'     => 'before',
		'type'    => 'select'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name'  => esc_html__('Answer anonymously', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Add answer anonymously', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the answer anonymously.', 'vbegy'),
		'id'   => 'answer_anonymously',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('Video', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Video in the answer form', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the video in the answer form.', 'vbegy'),
		'id'   => 'answer_video',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'answer_video:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Video description position at answer', 'vbegy'),
		'desc'    => esc_html__('Choose the video description position.', 'vbegy'),
		'id'      => 'video_answer_position',
		'options' => array("before" => "Before content", "after" => "After content"),
		'std'     => 'after',
		'type'    => 'select'
	);

	$options[] = array(
		'name' => esc_html__('Set the video description with 100%?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to set the video description 100%.', 'vbegy'),
		'id'   => 'video_answer_100',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		"name"      => esc_html__("Set the width for the video description for the answer", 'vbegy'),
		"id"        => "video_answer_width",
		'condition' => 'video_answer_100:is(0)',
		"type"      => "sliderui",
		'std'       => 260,
		"step"      => "1",
		"min"       => "50",
		"max"       => "600"
	);

	$options[] = array(
		"name" => esc_html__("Set the height for the video description for the answer", 'vbegy'),
		"id"   => "video_answer_height",
		"type" => "sliderui",
		'std'  => 500,
		"step" => "1",
		"min"  => "50",
		"max"  => "600"
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name'  => esc_html__('Terms of Service and Privacy Policy', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Activate the Terms of Service and Privacy Policy', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to activate the Terms of Service and Privacy Policy.', 'vbegy'),
		'id'   => 'terms_active_comment',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'terms_active_comment:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Select the checked by default option', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to checked it by default.', 'vbegy'),
		'id'   => 'terms_checked_comment',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'terms_active_target_comment',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'name'    => esc_html__('Terms page', 'vbegy'),
		'desc'    => esc_html__('Select the terms page', 'vbegy'),
		'id'      => 'terms_page_comment',
		'type'    => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Type the terms link if you don't like a page", "vbegy"),
		'id'   => 'terms_link_comment',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Activate Privacy Policy', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to activate Privacy Policy.', 'vbegy'),
		'id'   => 'privacy_policy_comment',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'privacy_policy_comment:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'privacy_active_target_comment',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'name'    => esc_html__('Privacy Policy page', 'vbegy'),
		'desc'    => esc_html__('Select the privacy policy page', 'vbegy'),
		'id'      => 'privacy_page_comment',
		'type'    => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Type the privacy policy link if you don't like a page", "vbegy"),
		'id'   => 'privacy_link_comment',
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Edit comments', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('User can edit the comment or answer?', 'vbegy'),
		'desc' => esc_html__('User can edit the comment or answer?', 'vbegy'),
		'id' => 'can_edit_comment',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		"name" => esc_html__("User can edit the comment or answer after x hours", 'vbegy'),
		"desc" => esc_html__("If you want the user edit it all the time leave it 0", 'vbegy'),
		"id" => "can_edit_comment_after",
		"type" => "sliderui",
		'std' => 1,
		"step" => "1",
		"min" => "0",
		"max" => "24"
	);

	$options[] = array(
		'name' => esc_html__("Edit comment page", 'vbegy'),
		'desc' => esc_html__("Create a page using the Edit post template and select it here", 'vbegy'),
		'id' => 'edit_comment',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('After edit comment or answer approved auto or need to approved again?', 'vbegy'),
		'desc' => esc_html__('Press ON to approved auto', 'vbegy'),
		'id' => 'comment_approved',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Delete comments', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('User can delete the comment or answer?', 'vbegy'),
		'id'   => 'can_delete_comment',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('When the users delete the comment or answer went to the trash or delete it ever?', 'vbegy'),
		'id'      => 'delete_comment',
		'options' => array(
			'delete' => esc_html__('Delete', 'vbegy'),
			'trash'  => esc_html__('Trash', 'vbegy'),
		),
		'std'     => 'delete',
		'type'    => 'radio'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$payment_setting = apply_filters("askme_payment_setting_tabs", array(
		"payment_setting" => "Payment setting",
		"pay_to_ask"      => "Pay to ask",
		"pay_to_sticky"   => "Pay to sticky question",
		"coupons_setting" => "Coupons setting",
	));

	$options[] = array(
		'name'    => esc_html__('Payment setting', 'vbegy'),
		'icon'    => 'tickets-alt',
		'type'    => 'heading',
		'std'     => 'general_setting',
		'options' => $payment_setting
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'payment_setting',
		'name' => esc_html__("Payment setting", 'vbegy'),
	);

	$options[] = array(
		'name'     => esc_html__('Custom text after the payment button', 'vbegy'),
		'id'       => 'custom_text_payment',
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$payment_methods = array(
		"paypal"  => array("sort" => esc_html__('PayPal', 'vbegy'), "value" => "paypal"),
		"stripe"  => array("sort" => esc_html__('Stripe', 'vbegy'), "value" => "stripe"),
		"bank"    => array("sort" => esc_html__('Bank Transfer', 'vbegy'), "value" => "bank"),
		"custom"  => array("sort" => esc_html__('Custom Payment', 'vbegy'), "value" => "custom"),
		"custom2" => array("sort" => esc_html__('Custom Payment 2', 'vbegy'), "value" => "custom2"),
	);

	$payment_methods_std = array(
		"paypal" => array("sort" => esc_html__('PayPal', 'vbegy'), "value" => "paypal"),
		"stripe" => array("sort" => esc_html__('Stripe', 'vbegy'), "value" => "stripe"),
	);

	$options[] = array(
		'name'    => esc_html__('Select the payment methods', 'vbegy'),
		'id'      => 'payment_methodes',
		'type'    => 'multicheck_3',
		'sort'    => 'yes',
		'std'     => $payment_methods_std,
		'options' => $payment_methods,
	);

	$options[] = array(
		'name' => esc_html__('Currency code', 'vbegy'),
		'desc' => esc_html__('Choose form here the currency code.', 'vbegy'),
		'id' => 'currency_code',
		'std' => 'USD',
		'type' => "select",
		'options' => array(
			'USD' => 'USD',
			'EUR' => 'EUR',
			'GBP' => 'GBP',
			'JPY' => 'JPY',
			'CAD' => 'CAD',
			'INR' => 'INR',
			'TRY' => 'TRY',
			'BRL' => 'BRL',
			'HUF' => 'HUF'
		)
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_methodes:has(paypal)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('PayPal', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable PayPal sandbox', 'vbegy'),
		'desc' => esc_html__('PayPal sandbox can be used to test payments.', 'vbegy'),
		'id' => 'paypal_sandbox',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Upload your PayPal logo', 'vbegy'),
		'desc' => esc_html__('Upload your custom logo for the PayPal.', 'vbegy'),
		'id'   => 'paypal_logo',
		'std'  => $imagepath_theme . "logo.png",
		'type' => 'upload',
	);

	$options[] = array(
		'std'      => esc_url(home_url('/')) . "?action=paypal",
		'name'     => esc_html__("Put this link at IPN", "vbegy"),
		'readonly' => 'readonly',
		'type'     => 'text'
	);

	$options[] = array(
		'name' => esc_html__("PayPal email", 'vbegy'),
		'desc' => esc_html__("put your PayPal email", 'vbegy'),
		'id' => 'paypal_email',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__("PayPal Identity Token", 'vbegy'),
		'desc' => esc_html__("From here Profile >> Profile and settings >> My selling tools >> Website preferences >> Update >> Identity Token", 'vbegy'),
		'id' => 'identity_token',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of the bottom image.', 'vbegy'),
		'id'   => 'paypal_payment_image',
		'type' => 'upload'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_methodes:has(stripe)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('Stripe', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_style:is(style_1)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of tab.', 'vbegy'),
		'id'   => 'stripe_tab_image',
		'type' => 'upload'
	);

	$options[] = array(
		"name" => esc_html__("Choose the width of the tab image", "vbegy"),
		"id"   => "stripe_tab_image_width",
		"type" => "sliderui",
		'std'  => "100",
		"step" => "50",
		"min"  => "50",
		"max"  => "300"
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Publishable key', 'vbegy'),
		'id'   => 'publishable_key',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Secret key', 'vbegy'),
		'id'   => 'secret_key',
		'type' => 'text'
	);

	$options[] = array(
		'std'      => esc_url(home_url('/')) . "?action=stripe",
		'name'     => esc_html__("Put this link at webhooks", "vbegy"),
		'readonly' => 'readonly',
		'type'     => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Activate the address info', 'vbegy'),
		'desc' => esc_html__("Select ON to active the address info, it's very important for some countries to activate it.", "vbegy"),
		'id'   => 'stripe_address',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of the bottom image.', 'vbegy'),
		'id'   => 'stripe_payment_image',
		'type' => 'upload'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_methodes:has(bank)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_style:is(style_1)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of tab.', 'vbegy'),
		'id'   => 'bank_tab_image',
		'type' => 'upload'
	);

	$options[] = array(
		"name" => esc_html__("Choose the width of the tab image", "vbegy"),
		"id"   => "bank_tab_image_width",
		"type" => "sliderui",
		'std'  => "100",
		"step" => "50",
		"min"  => "50",
		"max"  => "300"
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name'  => esc_html__('Bank transfer', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'     => esc_html__('Bank transfer details', 'vbegy'),
		'id'       => 'bank_transfer_details',
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of the bottom image.', 'vbegy'),
		'id'   => 'bank_payment_image',
		'type' => 'upload'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_methodes:has(custom)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('Custom Payment', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		"name" => esc_html__("Custom payment tab name", "vbegy"),
		"id"   => "custom_payment_tab",
		"type" => "text",
		'std'  => "Custom payment"
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_style:is(style_1)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of tab.', 'vbegy'),
		'id'   => 'custom_tab_image',
		'type' => 'upload'
	);

	$options[] = array(
		"name" => esc_html__("Choose the width of the tab image", "vbegy"),
		"id"   => "custom_tab_image_width",
		"type" => "sliderui",
		'std'  => "100",
		"step" => "50",
		"min"  => "50",
		"max"  => "300"
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name'     => esc_html__('Custom payment details', 'vbegy'),
		'id'       => 'custom_payment_details',
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of the bottom image.', 'vbegy'),
		'id'   => 'custom_payment_image',
		'type' => 'upload'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_methodes:has(custom2)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('Custom Payment 2', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		"name" => esc_html__("Custom payment tab name", "vbegy"),
		"id"   => "custom_payment_tab2",
		"type" => "text",
		'std'  => "Custom payment"
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'payment_style:is(style_1)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of tab.', 'vbegy'),
		'id'   => 'custom2_tab_image',
		'type' => 'upload'
	);

	$options[] = array(
		"name" => esc_html__("Choose the width of the tab image", "vbegy"),
		"id"   => "custom2_tab_image_width",
		"type" => "sliderui",
		'std'  => "100",
		"step" => "50",
		"min"  => "50",
		"max"  => "300"
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name'     => esc_html__('Custom payment details', 'vbegy'),
		'id'       => 'custom_payment_details2',
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name' => esc_html__('Upload the custom image of the bottom image.', 'vbegy'),
		'id'   => 'custom2_payment_image',
		'type' => 'upload'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'pay_to_ask',
		'name' => esc_html__("Pay to ask", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Pay to ask question', 'vbegy'),
		'desc' => esc_html__('Select ON to active the pay to ask question.', 'vbegy'),
		'id' => 'pay_ask',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'video_setting',
		'condition' => 'pay_ask:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Payment type', 'vbegy'),
		'desc'    => esc_html__('Choose the payment type for the ask question', 'vbegy'),
		'id'      => 'payment_type_ask',
		'std'     => 'paypal',
		'type'    => 'radio',
		'options' =>
		array(
			"payments"        => esc_html__('Payment methods', 'vbegy'),
			"points"          => esc_html__('By points', 'vbegy'),
			"payments_points" => esc_html__('Payment methods and points', 'vbegy')
		)
	);

	$options[] = array(
		'name' => esc_html__("Choose the groups add a question without pay", 'vbegy'),
		'id' => 'payment_group',
		'type' => 'multicheck',
		'options' => $options_groups
	);

	$options[] = array(
		"name"      => esc_html__("What's price to ask a new question?", 'vbegy'),
		"desc"      => esc_html__("Type here price of the payment to ask a new question", 'vbegy'),
		"id"        => "pay_ask_payment",
		"type"      => "text",
		'condition' => 'payment_type_ask:has(payments),payment_type_ask:has(payments_points)',
		'operator'  => 'or',
		'std'       => 10
	);

	$options[] = array(
		"name"      => esc_html__("What's points to ask a new question?", 'vbegy'),
		"desc"      => esc_html__("Type here points of the payment to ask a new question", 'vbegy'),
		"id"        => "ask_payment_points",
		"type"      => "text",
		'condition' => 'payment_type_ask:has(points),payment_type_ask:has(payments_points)',
		'operator'  => 'or',
		'std'       => 20
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'pay_to_sticky',
		'name' => esc_html__("Pay to sticky question", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Pay to sticky question at the top', 'vbegy'),
		'desc' => esc_html__('Select ON to active the pay to sticky question.', 'vbegy'),
		'id' => 'pay_to_sticky',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'video_setting',
		'condition' => 'pay_to_sticky:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Payment type', 'vbegy'),
		'desc'    => esc_html__('Choose the payment type sticky the question', 'vbegy'),
		'id'      => 'payment_type_sticky',
		'std'     => 'paypal',
		'type'    => 'radio',
		'options' =>
		array(
			"payments"        => esc_html__('Payment methods', 'vbegy'),
			"points"          => esc_html__('By points', 'vbegy'),
			"payments_points" => esc_html__('Payment methods and points', 'vbegy')
		)
	);

	$options[] = array(
		"name"      => esc_html__("What's price to sticky the question?", 'vbegy'),
		"desc"      => esc_html__("Type here price of the payment to sticky the question.", 'vbegy'),
		"id"        => "pay_sticky_payment",
		"type"      => "text",
		'condition' => 'payment_type_sticky:has(payments),payment_type_sticky:has(payments_points)',
		'operator'  => 'or',
		'std'       => 5
	);

	$options[] = array(
		"name"      => esc_html__("What's points to sticky the question?", 'vbegy'),
		"desc"      => esc_html__("Type here points of the payment to sticky the question", 'vbegy'),
		"id"        => "sticky_payment_points",
		"type"      => "text",
		'condition' => 'payment_type_sticky:has(points),payment_type_sticky:has(payments_points)',
		'operator'  => 'or',
		'std'       => 10
	);

	$options[] = array(
		"name" => esc_html__("What's the days to sticky the question?", 'vbegy'),
		"desc" => esc_html__("Type here the days of the payment to sticky the question", 'vbegy'),
		"id"   => "days_sticky",
		"type" => "sliderui",
		'std'  => 7,
		"step" => "1",
		"min"  => "0",
		"max"  => "365"
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options = apply_filters('askme_after_pay_to_sticky_options', $options);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'coupons_setting',
		'name' => esc_html__("Coupons setting", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Activate the Coupons', 'vbegy'),
		'desc' => esc_html__('Select ON to active the coupons.', 'vbegy'),
		'id' => 'active_coupons',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Show the free coupons when add a new question or sticky questions?', 'vbegy'),
		'desc' => esc_html__('Select ON to show the free coupons.', 'vbegy'),
		'id' => 'free_coupons',
		'type' => 'checkbox'
	);

	$options[] = array(
		'desc' => esc_html__("Add your Coupons.", 'vbegy'),
		'id' => "coupons",
		'std' => '',
		'type' => 'coupons'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Captcha setting', 'vbegy'),
		'icon' => 'admin-network',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in ask question form)', 'vbegy'),
		'id' => 'the_captcha',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in add post form)', 'vbegy'),
		'id' => 'the_captcha_post',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in register form)', 'vbegy'),
		'id' => 'the_captcha_register',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in login form)', 'vbegy'),
		'id' => 'the_captcha_login',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in forgot password form)', 'vbegy'),
		'id'   => 'the_captcha_password',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in answer form)', 'vbegy'),
		'id' => 'the_captcha_answer',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in comment form)', 'vbegy'),
		'id' => 'the_captcha_comment',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Captcha enable or disable (in send message form)', 'vbegy'),
		'id' => 'the_captcha_message',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Captcha works for unlogged users or unlogged and logged users', 'vbegy'),
		'id'        => 'captcha_users',
		'std'       => 'unlogged',
		'operator'  => 'or',
		'condition' => 'the_captcha:not(0),the_captcha_post:not(0),the_captcha_category:not(0),the_captcha_answer:not(0),the_captcha_comment:not(0),the_captcha_message:not(0)',
		'type'      => 'radio',
		'options'   =>
		array(
			"unlogged" => esc_html__('Unlogged users', 'vbegy'),
			"both"     => esc_html__('Unlogged and logged users', 'vbegy')
		)
	);

	$options[] = array(
		'div'       => 'div',
		'operator'  => 'or',
		'condition' => 'the_captcha:not(0),the_captcha_post:not(0),the_captcha_category:not(0),the_captcha_register:not(0),the_captcha_login:not(0),the_captcha_password:not(0),the_captcha_answer:not(0),the_captcha_comment:not(0),the_captcha_message:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__("Captcha style", 'vbegy'),
		'desc' => esc_html__("Choose the captcha style", 'vbegy'),
		'id' => 'captcha_style',
		'std' => 'question_answer',
		'type' => 'radio',
		'options' =>
		array(
			"question_answer"  => esc_html__('Question and answer', 'vbegy'),
			"normal_captcha"   => esc_html__('Normal captcha', 'vbegy'),
			"google_recaptcha" => esc_html__('Google reCaptcha', 'vbegy')
		)
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'captcha_style:is(google_recaptcha)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the reCaptcha site and secret keys from: %s', 'vbegy'), '<a href="https://www.google.com/recaptcha/admin/" target="_blank">' . esc_html__('here', 'vbegy') . '</a>'),
		'type'  => 'info',
		'class' => 'home_page_display'
	);

	$options[] = array(
		'name' => esc_html__('Site key reCaptcha', 'vbegy'),
		'id'   => 'site_key_recaptcha',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Secret key reCaptcha', 'vbegy'),
		'id'   => 'secret_key_recaptcha',
		'type' => 'text',
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the reCaptcha language code from: %s', 'vbegy'), '<a href="https://developers.google.com/recaptcha/docs/language/" target="_blank">' . esc_html__('here', 'vbegy') . '</a>'),
		'type'  => 'info',
		'class' => 'home_page_display'
	);

	$options[] = array(
		'name' => esc_html__('ReCaptcha language', 'vbegy'),
		'id'   => 'recaptcha_language',
		'type' => 'text',
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'captcha_style:is(question_answer)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Captcha answer enable or disable in forms', 'vbegy'),
		'desc' => esc_html__('Captcha answer enable or disable.', 'vbegy'),
		'id' => 'show_captcha_answer',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Captcha question", 'vbegy'),
		'desc' => esc_html__("put the Captcha question", 'vbegy'),
		'id' => 'captcha_question',
		'type' => 'text',
		'std' => "What is the capital of Egypt?"
	);

	$options[] = array(
		'name' => esc_html__("Captcha answer", 'vbegy'),
		'desc' => esc_html__("put the Captcha answer", 'vbegy'),
		'id' => 'captcha_answer',
		'type' => 'text',
		'std' => "Cairo"
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('User settings', 'vbegy'),
		'icon' => 'admin-users',
		'type' => 'heading',
		'std'     => 'user_setting',
		'options' => array(
			"user_setting"       => esc_html__("User setting", "vbegy"),
			"register_setting"   => esc_html__("Register setting", "vbegy"),
			"profile_setting"    => esc_html__("Edit profile", "vbegy"),
			"ask_user"           => esc_html__("Ask user", "vbegy"),
			"user_group_setting" => esc_html__("User group setting", "vbegy"),
			"author_page"        => esc_html__("Author Page", "vbegy"),
		)
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'user_setting',
		'name' => esc_html__("User setting", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Block users for each other enable or disable.', 'vbegy'),
		'id'   => 'block_users',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Report users for each other enable or disable.', 'vbegy'),
		'id'   => 'report_users',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('All the site for the register users only?', 'vbegy'),
		'desc' => esc_html__('Click ON to active the site for the register users only.', 'vbegy'),
		'id'   => 'site_users_only',
		'std'  => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the notifications system in site?', 'vbegy'),
		'desc' => esc_html__('Activate the notifications system enable or disable.', 'vbegy'),
		'id' => 'active_notifications',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the activity log in site?', 'vbegy'),
		'desc' => esc_html__('Activate the activity log enable or disable.', 'vbegy'),
		'id' => 'active_activity_log',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select the user links', 'vbegy'),
		'id' => 'user_links',
		'type' => 'multicheck',
		'std' => array(
			"profile" => 1,
			"messages" => 1,
			"questions" => 1,
			"polls" => 1,
			"asked_questions" => 1,
			"paid_questions" => 1,
			"answers" => 1,
			"best_answers" => 1,
			"favorite" => 1,
			"followed" => 1,
			"points" => 1,
			"i_follow" => 1,
			"followers" => 1,
			"blocking" => 1,
			"posts" => 1,
			"comments" => 1,
			"follow_questions" => 1,
			"follow_answers" => 1,
			"follow_posts" => 1,
			"follow_comments" => 1,
			"edit_profile" => 1,
			"activity_log" => 1,
			"logout" => 1,
		),
		'options' => array(
			"profile" => "Profile page",
			"messages" => "Messages",
			"questions" => "Questions",
			"polls" => "Polls",
			"asked_questions" => "Asked Questions",
			"paid_questions" => "Paid Questions",
			"answers" => "Answers",
			"best_answers" => "Best Answers",
			"favorite" => "Favorite Questions",
			"followed" => "Followed Questions",
			"points" => "Points",
			"i_follow" => "Authors I Follow",
			"followers" => "Followers",
			"blocking" => "Blocking Users",
			"posts" => "Posts",
			"comments" => "Comments",
			"follow_questions" => "Follow Questions",
			"follow_answers" => "Follow Answers",
			"follow_posts" => "Follow Posts",
			"follow_comments" => "Follow Comments",
			"edit_profile" => "Edit Profile",
			"activity_log" => "Activity Log",
			"logout" => "Logout",
		)
	);

	$options[] = array(
		'name' => esc_html__('Select the columns in the user admin', 'vbegy'),
		'id' => 'user_meta_admin',
		'type' => 'multicheck',
		'std' => array(
			"points" => 0,
			"phone" => 0,
			"country" => 0,
			"age" => 0,
			"registration" => 0,
		),
		'options' => array(
			"points" => "Points",
			"phone" => "Phone",
			"country" => "Country",
			"age" => "Age",
			"registration" => "Registration date",
		)
	);

	$user_profile_pages = array(
		"questions"           => array("sort" => esc_html__('Questions', 'vbegy'), "value" => "questions"),
		"polls"               => array("sort" => esc_html__('Polls', 'vbegy'), "value" => "polls"),
		"answers"             => array("sort" => esc_html__('Answers', 'vbegy'), "value" => "answers"),
		"best-answers"        => array("sort" => esc_html__('Best Answers', 'vbegy'), "value" => "best-answers"),
		"asked-questions"     => array("sort" => esc_html__('Asked Questions', 'vbegy'), "value" => "asked-questions"),
		"paid-questions"      => array("sort" => esc_html__('Paid Questions', 'vbegy'), "value" => "paid-questions"),
		"followed"            => array("sort" => esc_html__('Followed Questions', 'vbegy'), "value" => "followed"),
		"favorites"           => array("sort" => esc_html__('Favorite Questions', 'vbegy'), "value" => "favorites"),
		"points"              => array("sort" => esc_html__('Points', 'vbegy'), "value" => "points"),
		"posts"               => array("sort" => esc_html__('Posts', 'vbegy'), "value" => "posts"),
		"comments"            => array("sort" => esc_html__('Comments', 'vbegy'), "value" => "comments"),
		"i_follow"            => array("sort" => esc_html__('Authors I Follow', 'vbegy'), "value" => "i_follow"),
		"followers"           => array("sort" => esc_html__('Followers', 'vbegy'), "value" => "followers"),
		"blocking"            => array("sort" => esc_html__('Blocking Users', 'vbegy'), "value" => "blocking"),
		"followers-questions" => array("sort" => esc_html__('Followers Questions', 'vbegy'), "value" => "followers-questions"),
		"followers-answers"   => array("sort" => esc_html__('Followers Answers', 'vbegy'), "value" => "followers-answers"),
		"followers-posts"     => array("sort" => esc_html__('Followers Posts', 'vbegy'), "value" => "followers-posts"),
		"followers-comments"  => array("sort" => esc_html__('Followers Comments', 'vbegy'), "value" => "followers-comments"),
	);

	$options[] = array(
		'name'         => esc_html__('Select the user links at stats section', 'vbegy'),
		'id'           => 'user_profile_pages',
		'type'         => 'multicheck_3',
		'sort'         => 'yes',
		'limit-height' => 'yes',
		'std'          => $user_profile_pages,
		'options'      => $user_profile_pages
	);

	$options[] = array(
		'name' => esc_html__("Login and register page", 'vbegy'),
		'desc' => esc_html__("Select the Login and register page", 'vbegy'),
		'id' => 'login_register_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User edit profile page", 'vbegy'),
		'desc' => esc_html__("Select the User edit profile page", 'vbegy'),
		'id' => 'user_edit_profile_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Activity log page", 'vbegy'),
		'desc' => esc_html__("Select the Activity log page", 'vbegy'),
		'id' => 'activity_log_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Notifications page", 'vbegy'),
		'desc' => esc_html__("Select the Notifications page", 'vbegy'),
		'id' => 'notifications_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User post page", 'vbegy'),
		'desc' => esc_html__("Select User post page", 'vbegy'),
		'id' => 'post_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User comment page", 'vbegy'),
		'desc' => esc_html__("Select User comment page", 'vbegy'),
		'id' => 'comment_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User question page", 'vbegy'),
		'desc' => esc_html__("Select User question page", 'vbegy'),
		'id' => 'question_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User polls page", 'vbegy'),
		'desc' => esc_html__("Select User polls page", 'vbegy'),
		'id' => 'polls_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User asked question page", 'vbegy'),
		'desc' => esc_html__("Select User asked question page", 'vbegy'),
		'id' => 'asked_question_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Paid questions page", 'vbegy'),
		'desc' => esc_html__("Select the paid questions page", 'vbegy'),
		'id' => 'paid_question',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User answer page", 'vbegy'),
		'desc' => esc_html__("Select User answer page", 'vbegy'),
		'id' => 'answer_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User best answer page", 'vbegy'),
		'desc' => esc_html__("Select User best answer page", 'vbegy'),
		'id' => 'best_answer_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User favorite question page", 'vbegy'),
		'desc' => esc_html__("Select User favorite question page", 'vbegy'),
		'id' => 'favorite_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User followed question page", 'vbegy'),
		'desc' => esc_html__("Select User followed question page", 'vbegy'),
		'id' => 'followed_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User point page", 'vbegy'),
		'desc' => esc_html__("Select User point page", 'vbegy'),
		'id' => 'point_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Authors I Follow page", 'vbegy'),
		'desc' => esc_html__("Select Authors I Follow page", 'vbegy'),
		'id' => 'i_follow_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Blocking users page", 'vbegy'),
		'desc' => esc_html__("Select Blocking users page", 'vbegy'),
		'id' => 'blocking_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User Followers page", 'vbegy'),
		'desc' => esc_html__("Select User Followers page", 'vbegy'),
		'id' => 'followers_user_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User follow question page", 'vbegy'),
		'desc' => esc_html__("Select User follow question page", 'vbegy'),
		'id' => 'follow_question_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User follow answer page", 'vbegy'),
		'desc' => esc_html__("Select User follow answer page", 'vbegy'),
		'id' => 'follow_answer_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User follow posts page", 'vbegy'),
		'desc' => esc_html__("Select User follow posts page", 'vbegy'),
		'id' => 'follow_post_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("User follow comment page", 'vbegy'),
		'desc' => esc_html__("Select User follow comment page", 'vbegy'),
		'id' => 'follow_comment_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'register_setting',
		'name' => esc_html__("Register setting", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__("Register in default group", 'vbegy'),
		'desc' => esc_html__("Select the default group", 'vbegy'),
		'id' => 'default_group',
		'std' => 'subscriber',
		'type' => 'select',
		'options' => $options_groups
	);

	$options[] = array(
		'name' => esc_html__('After register go to?', 'vbegy'),
		'id' => 'after_register',
		'std' => "same_page",
		'type' => 'select',
		'options' => array("same_page" => "Same page", "home" => "Home", "profile" => "Profile", "custom_link" => "Custom link")
	);

	$options[] = array(
		'name' => esc_html__("Type the link if you don't like above", 'vbegy'),
		'id' => 'after_register_link',
		'condition' => 'after_register:is(custom_link)',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('After login go to?', 'vbegy'),
		'id' => 'after_login',
		'std' => "same_page",
		'type' => 'select',
		'options' => array("same_page" => "Same page", "home" => "Home", "profile" => "Profile", "custom_link" => "Custom link")
	);

	$options[] = array(
		'name' => esc_html__("Type the link if you don't like above", 'vbegy'),
		'id' => 'after_login_link',
		'condition' => 'after_login:is(custom_link)',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('After logout go to?', 'vbegy'),
		'id' => 'after_logout',
		'std' => "same_page",
		'type' => 'select',
		'options' => array("same_page" => "Same page", "home" => "Home", "custom_link" => "Custom link")
	);

	$options[] = array(
		'name' => esc_html__("Type the link if you don't like above", 'vbegy'),
		'id' => 'after_logout_link',
		'condition' => 'after_logout:is(custom_link)',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to remove the confirm password from the register form.', 'vbegy'),
		'id'   => 'comfirm_password',
		'type' => 'checkbox'
	);

	$allow_duplicate_names = array(
		"nickname"     => array("sort" => esc_html__('Nickname', 'vbegy'), "value" => ""),
		"display_name" => array("sort" => esc_html__('Display Name', 'vbegy'), "value" => ""),
	);

	$options[] = array(
		'name'    => esc_html__("Choose if you want the nickname and display name to be duplicate or not", "vbegy"),
		'id'      => 'allow_duplicate_names',
		'type'    => 'multicheck_3',
		'sort'    => 'yes',
		'std'     => $allow_duplicate_names,
		'options' => $allow_duplicate_names
	);

	$options[] = array(
		'name' => esc_html__('Send a welcome email when the user is registered', 'vbegy'),
		'desc' => esc_html__('Welcome mail for user review enable or disable.', 'vbegy'),
		'id'   => 'send_welcome_mail',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Confirm email after registering enable or disable', 'vbegy'),
		'id' => 'confirm_email',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('The membership under review?', 'vbegy'),
		'desc' => esc_html__('Check ON to review the users before complete the register.', 'vbegy'),
		'id' => 'user_review',
		'std' => 0,
		'type' => 'checkbox'
	);

	$register_items = array(
		"username"      => array("sort" => esc_html__('Username', 'vbegy'), "value" => "username", "default" => "yes"),
		"email"         => array("sort" => esc_html__('E-mail', 'vbegy'), "value" => "email", "default" => "yes"),
		"password"      => array("sort" => esc_html__('Password', 'vbegy'), "value" => "username", "default" => "yes"),
		"nickname"      => array("sort" => esc_html__('Nickname', 'vbegy'), "value" => "nickname"),
		"first_name"    => array("sort" => esc_html__('First Name', 'vbegy'), "value" => "first_name"),
		"last_name"     => array("sort" => esc_html__('Last Name', 'vbegy'), "value" => "last_name"),
		"display_name"  => array("sort" => esc_html__('Display Name', 'vbegy'), "value" => "display_name"),
		"image_profile" => array("sort" => esc_html__('Image Profile', 'vbegy'), "value" => "image_profile"),
		"country"       => array("sort" => esc_html__('Country', 'vbegy'), "value" => "country"),
		"city"          => array("sort" => esc_html__('City', 'vbegy'), "value" => "city"),
		"phone"         => array("sort" => esc_html__('Phone', 'vbegy'), "value" => "phone"),
		"gender"        => array("sort" => esc_html__('Gender', 'vbegy'), "value" => "gender"),
		"age"           => array("sort" => esc_html__('Age', 'vbegy'), "value" => "age"),
	);
	$register_items_std = array(
		"username"      => array("sort" => esc_html__('Username', 'vbegy'), "value" => "username", "default" => "yes"),
		"email"         => array("sort" => esc_html__('E-mail', 'vbegy'), "value" => "email", "default" => "yes"),
		"password"      => array("sort" => esc_html__('Password', 'vbegy'), "value" => "username", "default" => "yes"),
		"nickname"      => array("sort" => esc_html__('Nickname', 'vbegy'), "value" => ""),
		"first_name"    => array("sort" => esc_html__('First Name', 'vbegy'), "value" => ""),
		"last_name"     => array("sort" => esc_html__('Last Name', 'vbegy'), "value" => ""),
		"display_name"  => array("sort" => esc_html__('Display Name', 'vbegy'), "value" => ""),
		"image_profile" => array("sort" => esc_html__('Image Profile', 'vbegy'), "value" => ""),
		"country"       => array("sort" => esc_html__('Country', 'vbegy'), "value" => ""),
		"city"          => array("sort" => esc_html__('City', 'vbegy'), "value" => ""),
		"phone"         => array("sort" => esc_html__('Phone', 'vbegy'), "value" => ""),
		"gender"        => array("sort" => esc_html__('Gender', 'vbegy'), "value" => ""),
		"age"           => array("sort" => esc_html__('Age', 'vbegy'), "value" => ""),
	);

	$options[] = array(
		'name'    => esc_html__("Select what's show at register form", "vbegy"),
		'id'      => 'register_items',
		'type'    => 'multicheck_3',
		'sort'    => 'yes',
		'options' => $register_items,
		'std'     => $register_items_std
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'register_items:has(first_name),register_items:has(last_name),register_items:has(display_name),register_items:has(image_profile),register_items:has(gender),register_items:has(country),register_items:has(city),register_items:has(phone),register_items:has(age)',
		'operator'  => 'or',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Activate other at the gender.', 'vbegy'),
		'id'   => 'gender_other',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('Required setting', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('First name in register is required.', 'vbegy'),
		'id'        => 'first_name_required_register',
		'condition' => 'register_items:has(first_name)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Last name in register is required.', 'vbegy'),
		'id'        => 'last_name_required_register',
		'condition' => 'register_items:has(last_name)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Display name in register is required.', 'vbegy'),
		'id'        => 'display_name_required_register',
		'condition' => 'register_items:has(display_name)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Profile picture in register is required', 'vbegy'),
		'id'        => 'profile_picture_required_register',
		'condition' => 'register_items:has(image_profile)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Gender in register is required.', 'vbegy'),
		'id'        => 'gender_required_register',
		'condition' => 'register_items:has(gender)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Country in register is required.', 'vbegy'),
		'id'        => 'country_required_register',
		'condition' => 'register_items:has(country)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('City in register is required.', 'vbegy'),
		'id'        => 'city_required_register',
		'condition' => 'register_items:has(city)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Phone in register is required.', 'vbegy'),
		'id'        => 'phone_required_register',
		'condition' => 'register_items:has(phone)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Age in register is required.', 'vbegy'),
		'id'        => 'age_required_register',
		'condition' => 'register_items:has(age)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name'  => esc_html__('Terms of Service and Privacy Policy', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Activate the page terms of service and privacy policy?', 'vbegy'),
		'desc' => esc_html__('Select ON if you want active the page terms of service and privacy policy.', 'vbegy'),
		'id' => 'terms_active_register',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'terms_active_register:not(0)',
		'div'       => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Select the checked by default option', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to checked it by default.', 'vbegy'),
		'id'   => 'terms_checked_register',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id' => 'terms_active_target_register',
		'std' => "new_page",
		'type' => 'select',
		'options' => array("same_page" => "Same page", "new_page" => "New page")
	);

	$options[] = array(
		'name' => esc_html__("Terms page", 'vbegy'),
		'desc' => esc_html__("Select the terms page", 'vbegy'),
		'id' => 'terms_page_register',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Type the terms link if you don't like a page", 'vbegy'),
		'id' => 'terms_link_register',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Activate Privacy Policy', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to activate Privacy Policy.', 'vbegy'),
		'id'   => 'privacy_policy_register',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'privacy_policy_register:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'privacy_active_target_register',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'name'    => esc_html__('Privacy Policy page', 'vbegy'),
		'desc'    => esc_html__('Select the privacy policy page', 'vbegy'),
		'id'      => 'privacy_page_register',
		'type'    => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Type the privacy policy link if you don't like a page", "vbegy"),
		'id'   => 'privacy_link_register',
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Register content', 'vbegy'),
		'desc' => esc_html__('Put the register content in top panel and register page.', 'vbegy'),
		'id' => 'register_content',
		'std' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.Morbi adipiscing gravdio, sit amet suscipit risus ultrices eu.Fusce viverra neque at purus laoreet consequa.Vivamus vulputate posuere nisl quis consequat.',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'profile_setting',
		'name' => esc_html__("Edit profile", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Add profile picture in edit profile form', 'vbegy'),
		'id' => 'profile_picture_profile',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Profile picture in edit profile form is required', 'vbegy'),
		'id' => 'profile_picture_required_profile',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Add URL in edit profile form', 'vbegy'),
		'id' => 'url_profile',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('URL in edit profile form is required', 'vbegy'),
		'id' => 'url_required_profile',
		'std' => 0,
		'type' => 'checkbox'
	);

	$edit_profile_items_1 = array(
		"nickname"      => array("sort" => esc_html__('Nickname', 'vbegy'), "value" => "nickname"),
		"first_name"    => array("sort" => esc_html__('First Name', 'vbegy'), "value" => "first_name"),
		"last_name"     => array("sort" => esc_html__('Last Name', 'vbegy'), "value" => "last_name"),
		"display_name"  => array("sort" => esc_html__('Display Name', 'vbegy'), "value" => "display_name"),
		"email"         => array("sort" => esc_html__('E-mail', 'vbegy'), "value" => "email", "default" => "yes"),
		"password"      => array("sort" => esc_html__('Password', 'vbegy'), "value" => "username", "default" => "yes"),
		"country"       => array("sort" => esc_html__('Country', 'vbegy'), "value" => "country"),
		"city"          => array("sort" => esc_html__('City', 'vbegy'), "value" => "city"),
		"phone"         => array("sort" => esc_html__('Phone', 'vbegy'), "value" => "phone"),
		"gender"        => array("sort" => esc_html__('Gender', 'vbegy'), "value" => "gender"),
		"age"           => array("sort" => esc_html__('Age', 'vbegy'), "value" => "age"),
	);

	$options[] = array(
		'name'    => esc_html__("Select what's show at edit profile at the Basic Information section", "vbegy"),
		'id'      => 'edit_profile_items_1',
		'type'    => 'multicheck_3',
		'sort'    => 'yes',
		'std'     => $edit_profile_items_1,
		'options' => $edit_profile_items_1
	);

	$options[] = array(
		'name' => esc_html__('Confirm email after editing email enable or disable', 'vbegy'),
		'id'   => 'confirm_edit_email',
		'std'  => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'edit_profile_items_1:has(first_name),edit_profile_items_1:has(last_name),edit_profile_items_1:has(display_name),edit_profile_items_1:has(gender),edit_profile_items_1:has(country),edit_profile_items_1:has(city),edit_profile_items_1:has(phone),edit_profile_items_1:has(age)',
		'operator'  => 'or',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('Required setting', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('First name in edit profile is required.', 'vbegy'),
		'id'        => 'first_name_required',
		'condition' => 'edit_profile_items_1:has(first_name)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Last name in edit profile is required.', 'vbegy'),
		'id'        => 'last_name_required',
		'condition' => 'edit_profile_items_1:has(last_name)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Display name in edit profile is required.', 'vbegy'),
		'id'        => 'display_name_required',
		'condition' => 'edit_profile_items_1:has(display_name)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Gender in edit profile is required.', 'vbegy'),
		'id'        => 'sex_required_profile',
		'condition' => 'edit_profile_items_1:has(gender)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Country in edit profile is required.', 'vbegy'),
		'id'        => 'country_required_profile',
		'condition' => 'edit_profile_items_1:has(country)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('City in edit profile is required.', 'vbegy'),
		'id'        => 'city_required_profile',
		'condition' => 'edit_profile_items_1:has(city)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Phone in edit profile is required.', 'vbegy'),
		'id'        => 'phone_required_profile',
		'condition' => 'edit_profile_items_1:has(phone)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Age in edit profile is required.', 'vbegy'),
		'id'        => 'age_required_profile',
		'condition' => 'edit_profile_items_1:has(age)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options = apply_filters('askme_edit_profile_options', $options);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'ask_user',
		'name' => esc_html__("Ask user", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Ask question to the users', 'vbegy'),
		'desc' => esc_html__('Any one can ask question to the users enable or disable.', 'vbegy'),
		'id'   => 'ask_question_to_users',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'ask_question_to_users:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Click ON, if you need to remove the asked question slug and choose "Post name" from WordPress Settings/Permalinks.', 'vbegy'),
		'id'   => 'remove_asked_question_slug',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Click ON, if you need to make the asked question slug with number instant of title and choose "Post name" from WordPress Settings/Permalinks.', 'vbegy'),
		'id'   => 'asked_question_slug_numbers',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Asked question slug', 'vbegy'),
		'desc'      => esc_html__('Add your asked question slug.', 'vbegy'),
		'id'        => 'asked_question_slug',
		'std'       => ask_asked_questions_type,
		'condition' => 'remove_asked_question_slug:is(0)',
		'type'      => 'text'
	);

	$ask_user_items = array(
		"title_question"       => array("sort" => esc_html__('Question Title', 'vbegy'), "value" => "title_question"),
		"comment_question"     => array("sort" => esc_html__('Question content', 'vbegy'), "value" => "comment_question"),
		"anonymously_question" => array("sort" => esc_html__('Ask Anonymously', 'vbegy'), "value" => "anonymously_question"),
		"private_question"     => array("sort" => esc_html__('Private Question', 'vbegy'), "value" => "private_question"),
		"remember_answer"      => array("sort" => esc_html__('Remember Answer', 'vbegy'), "value" => "remember_answer"),
		"terms_active"         => array("sort" => esc_html__('Terms of Service and Privacy Policy', 'vbegy'), "value" => "terms_active"),
	);

	$options[] = array(
		'name'    => esc_html__("Select what's show at ask user question form", "vbegy"),
		'id'      => 'ask_user_items',
		'type'    => 'multicheck_3',
		'sort'    => 'yes',
		'std'     => $ask_user_items,
		'options' => $ask_user_items
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'ask_user_items:has_not(title_question)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Excerpt type for title from the content', 'vbegy'),
		'desc' => esc_html__('Choose form here the excerpt type.', 'vbegy'),
		'id' => 'title_excerpt_type_user',
		'type' => "select",
		'options' => array(
			'words' => 'Words',
			'characters' => 'Characters'
		)
	);

	$options[] = array(
		'name' => esc_html__('Excerpt title from the content', 'vbegy'),
		'desc' => esc_html__('Put here the excerpt title from the content.', 'vbegy'),
		'id' => 'title_excerpt_user',
		'std' => 10,
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the editor for details in ask question form', 'vbegy'),
		'id'   => 'editor_ask_user',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Select the checked by default options at ask user question form', 'vbegy'),
		'id'        => 'add_question_default_user',
		'type'      => 'multicheck',
		'condition' => 'ask_user_items:has(anonymously_question),ask_user_items:has(private_question),ask_user_items:has(remember_answer),ask_user_items:has(terms_active)',
		'operator'  => 'or',
		'std'       => array(
			"notified" => 1,
		),
		'options' => array(
			"notified"    => esc_html__('Notified', 'vbegy'),
			"private"     => esc_html__("Private question", "vbegy"),
			"anonymously" => esc_html__("Ask anonymously", "vbegy"),
			"terms"       => esc_html__("Terms", "vbegy"),
		)
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'user_group_setting',
		'name' => esc_html__("User group setting", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can add a custom permission.', 'vbegy'),
		'id' => 'custom_permission',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'custom_permission:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__("Without login user", 'vbegy'),
		'class' => 'home_page_display custom_permission_note',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can add a question.', 'vbegy'),
		'id' => 'ask_question',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can view other questions.', 'vbegy'),
		'id' => 'show_question',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can add an answer.', 'vbegy'),
		'id' => 'add_answer',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can view other answers.', 'vbegy'),
		'id' => 'show_answer',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can add a post.', 'vbegy'),
		'id' => 'add_post',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can add a comment.', 'vbegy'),
		'id' => 'add_comment',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can view other comments.', 'vbegy'),
		'id' => 'show_comment',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can send a message.', 'vbegy'),
		'id' => 'send_message',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to can view other posts.', 'vbegy'),
		'id' => 'show_post',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'id' => "roles",
		'std' => '',
		'type' => 'roles'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'author_page',
		'name' => esc_html__("Author Page", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Hide the user registered in profile page', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the user registered in profile page.', 'vbegy'),
		'id' => 'user_registered',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Hide the user country in profile page', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the user country in profile page.', 'vbegy'),
		'id' => 'user_country',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Hide the user city in profile page', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the user city in profile page.', 'vbegy'),
		'id' => 'user_city',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Hide the user phone in profile page', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the user phone in profile page.', 'vbegy'),
		'id' => 'user_phone',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Hide the user age in profile page', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the user age in profile page.', 'vbegy'),
		'id' => 'user_age',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Hide the user gender in profile page', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the user gender in profile page.', 'vbegy'),
		'id' => 'user_sex',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Hide the user URL in profile page', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the user URL in profile page.', 'vbegy'),
		'id' => 'user_url',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options = apply_filters('askme_author_page_options', $options);

	$options[] = array(
		'name' => esc_html__('Hide the author stats', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to hide the author stats in profile page.', 'vbegy'),
		'id' => 'author_stats',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Author sidebar layout", 'vbegy'),
		'id' => "author_sidebar_layout",
		'std' => "default",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'right' => $imagepath . 'sidebar_right.jpg',
			'full' => $imagepath . 'sidebar_no.jpg',
			'left' => $imagepath . 'sidebar_left.jpg',
		)
	);

	$options[] = array(
		'name' => esc_html__("Author Page Sidebar", 'vbegy'),
		'id' => "author_sidebar",
		'std' => '',
		'options' => $new_sidebars,
		'type' => 'select'
	);

	$options[] = array(
		'name' => esc_html__("Author page layout", 'vbegy'),
		'id' => "author_layout",
		'std' => "full",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'full' => $imagepath . 'full.jpg',
			'fixed' => $imagepath . 'fixed.jpg',
			'fixed_2' => $imagepath . 'fixed_2.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Choose template", 'vbegy'),
		'id' => "author_template",
		'std' => "grid_1200",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'grid_1300' => $imagepath . 'template_1300.jpg',
			'grid_1200' => $imagepath . 'template_1200.jpg',
			'grid_970' => $imagepath . 'template_970.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Site skin", 'vbegy'),
		'desc' => esc_html__("Choose Site skin.", 'vbegy'),
		'id' => "author_skin_l",
		'std' => "site_light",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'site_light' => $imagepath . 'light.jpg',
			'site_dark' => $imagepath . 'dark.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Choose Your Skin", 'vbegy'),
		'class' => "site_skin",
		'id' => "author_skin",
		'std' => "default",
		'type' => "images",
		'options' => array(
			'default'	    => $imagepath . 'default.jpg',
			'skins'		    => $imagepath . 'skin.jpg',
			'blue'			=> $imagepath . 'blue.jpg',
			'gray'			=> $imagepath . 'gray.jpg',
			'green'			=> $imagepath . 'green.jpg',
			'moderate_cyan' => $imagepath . 'moderate_cyan.jpg',
			'orange'		=> $imagepath . 'orange.jpg',
			'purple'	    => $imagepath . 'purple.jpg',
			'red'			=> $imagepath . 'red.jpg',
			'strong_cyan'	=> $imagepath . 'strong_cyan.jpg',
			'yellow'		=> $imagepath . 'yellow.jpg',
		)
	);

	$options[] = array(
		'name' => esc_html__("Primary Color", 'vbegy'),
		'id' => 'author_primary_color',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__("Background Type", 'vbegy'),
		'id' => 'author_background_type',
		'std' => 'patterns',
		'type' => 'radio',
		'options' =>
		array(
			"patterns" => "Patterns",
			"custom_background" => "Custom Background"
		)
	);

	$options[] = array(
		'name' => esc_html__("Background Color", 'vbegy'),
		'id' => 'author_background_color',
		'std' => "#FFF",
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__("Choose Pattern", 'vbegy'),
		'id' => "author_background_pattern",
		'std' => "bg13",
		'type' => "images",
		'options' => array(
			'bg1' => $imagepath . 'bg1.jpg',
			'bg2' => $imagepath . 'bg2.jpg',
			'bg3' => $imagepath . 'bg3.jpg',
			'bg4' => $imagepath . 'bg4.jpg',
			'bg5' => $imagepath . 'bg5.jpg',
			'bg6' => $imagepath . 'bg6.jpg',
			'bg7' => $imagepath . 'bg7.jpg',
			'bg8' => $imagepath . 'bg8.jpg',
			'bg9' => $imagepath . '../../images/patterns/bg9.png',
			'bg10' => $imagepath . '../../images/patterns/bg10.png',
			'bg11' => $imagepath . '../../images/patterns/bg11.png',
			'bg12' => $imagepath . '../../images/patterns/bg12.png',
			'bg13' => $imagepath . 'bg13.jpg',
			'bg14' => $imagepath . 'bg14.jpg',
			'bg15' => $imagepath . '../../images/patterns/bg15.png',
			'bg16' => $imagepath . '../../images/patterns/bg16.png',
			'bg17' => $imagepath . 'bg17.jpg',
			'bg18' => $imagepath . 'bg18.jpg',
			'bg19' => $imagepath . 'bg19.jpg',
			'bg20' => $imagepath . 'bg20.jpg',
			'bg21' => $imagepath . '../../images/patterns/bg21.png',
			'bg22' => $imagepath . 'bg22.jpg',
			'bg23' => $imagepath . '../../images/patterns/bg23.png',
			'bg24' => $imagepath . '../../images/patterns/bg24.png',
		)
	);

	$options[] = array(
		'name' => esc_html__("Custom Background", 'vbegy'),
		'id' => 'author_custom_background',
		'std' => $background_defaults,
		'type' => 'background'
	);

	$options[] = array(
		'name' => esc_html__("Full Screen Background", 'vbegy'),
		'desc' => esc_html__("Click ON to Full Screen Background", 'vbegy'),
		'id' => 'author_full_screen_background',
		'std' => '0',
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Message setting', 'vbegy'),
		'icon' => 'email-alt',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Activate messages to the users', 'vbegy'),
		'desc' => esc_html__('Any one can send message to the users enable or disable.', 'vbegy'),
		'id'   => 'active_message',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Messages page", 'vbegy'),
		'desc' => esc_html__("Select the messages page", 'vbegy'),
		'id' => 'messages_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Choose message status', 'vbegy'),
		'desc' => esc_html__('Choose message status after user publish the question.', 'vbegy'),
		'id' => 'message_publish',
		'options' => array("publish" => esc_html__("Auto publish", "vbegy"), "draft" => esc_html__("Need a review", "vbegy")),
		'std' => 'draft',
		'type' => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Any one can send message without register', 'vbegy'),
		'desc' => esc_html__('Any one can send message without register enable or disable.', 'vbegy'),
		'id' => 'send_message_no_register',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the featured image in send message form', 'vbegy'),
		'id'   => 'featured_image_message',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'featured_image_message:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to enable the lightbox for featured image', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the lightbox for featured image.', 'vbegy'),
		'id'   => 'featured_image_message_lightbox',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		"name" => esc_html__("Set the width for the featured image for the message", "vbegy"),
		"id"   => "featured_image_message_width",
		"type" => "sliderui",
		'std'  => 260,
		"step" => "1",
		"min"  => "50",
		"max"  => "600"
	);

	$options[] = array(
		"name" => esc_html__("Set the height for the featured image for the message", "vbegy"),
		"id"   => "featured_image_message_height",
		"type" => "sliderui",
		'std'  => 185,
		"step" => "1",
		"min"  => "50",
		"max"  => "600"
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Details in send message form is required', 'vbegy'),
		'id' => 'comment_message',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Editor enable or disable for details in send message form', 'vbegy'),
		'id' => 'editor_message_details',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Send email after send a message?', 'vbegy'),
		'id' => 'send_email_message',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate user can delete the messages', 'vbegy'),
		'desc' => esc_html__('Select ON if you want the user can delete the messages.', 'vbegy'),
		'id' => 'message_delete',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate user can seen the message by send notification', 'vbegy'),
		'desc' => esc_html__('Select ON if you want the user know if any one seen the message by send notification.', 'vbegy'),
		'id' => 'seen_message',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options = apply_filters('askme_after_options_messages', $options);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Badges & Points setting', 'vbegy'),
		'icon' => 'star-filled',
		'type' => 'heading',
		'std'     => 'general_setting',
		'options' => array(
			"badges_setting" => "Badges setting",
			"points_setting" => "Points setting",
		)
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'badges_setting',
		'name' => esc_html__("Badges setting", 'vbegy'),
	);

	$options[] = array(
		'name'    => esc_html__('Choose the badges style', 'vbegy'),
		'desc'    => esc_html__('Choose from here the badges style.', 'vbegy'),
		'id'      => 'badges_style',
		'options' => array(
			"by_points" => esc_html__("By points", "vbegy"),
			"by_groups" => esc_html__("By groups", "vbegy"),
			"by_groups_points" => esc_html__("By groups and points", "vbegy"),
			"by_questions" => esc_html__("By questions", "vbegy"),
			"by_answers" => esc_html__("By answers", "vbegy")
		),
		'std'     => 'by_points',
		'type'    => 'select'
	);

	$badges_groups = $options_groups;
	unset($badges_groups["activation"]);

	$badge_elements = array(
		array(
			"type"    => "select",
			"id"      => "badge_name",
			"options" => $badges_groups,
			"name"    => esc_html__('Badge name', 'vbegy')
		),
		array(
			"type" => "color",
			"id"   => "badge_color",
			"name" => esc_html__('Color', 'vbegy')
		),
	);

	$options[] = array(
		'id'        => "badges_groups",
		'type'      => "elements",
		'hide'      => "yes",
		'button'    => esc_html__('Add new badge', 'vbegy'),
		'options'   => $badge_elements,
		'condition' => 'badges_style:is(by_groups)',
	);

	$badge_elements = array(
		array(
			"type"    => "text",
			"id"      => "badge_name",
			"name"    => esc_html__('Badge name', 'vbegy')
		),
		array(
			"type"    => "select",
			"id"      => "badge_group",
			"options" => $badges_groups,
			"name"    => esc_html__('Badge group', 'vbegy')
		),
		array(
			"type" => "text",
			"id"   => "badge_points",
			"name" => esc_html__('Points', 'vbegy')
		),
		array(
			"type" => "color",
			"id"   => "badge_color",
			"name" => esc_html__('Color', 'vbegy')
		)
	);

	$options[] = array(
		'id'        => "badges_groups_points",
		'type'      => "elements",
		'hide'      => "yes",
		'button'    => esc_html__('Add new badge', 'vbegy'),
		'options'   => apply_filters("askme_badges_groups_points", $badge_elements, $badges_groups),
		'condition' => 'badges_style:is(by_groups_points)',
	);

	$badge_elements = array(
		array(
			"type" => "text",
			"id"   => "badge_name",
			"name" => esc_html__('Badge name', 'vbegy')
		),
		array(
			"type" => "text",
			"id"   => "badge_points",
			"name" => esc_html__('Points', 'vbegy')
		),
		array(
			"type" => "color",
			"id"   => "badge_color",
			"name" => esc_html__('Color', 'vbegy')
		)
	);

	$get_badges = get_option("badges");
	$array_badges_option = (is_array($get_badges) && !empty($get_badges) ? array('val' => $get_badges) : array());
	$array_badges = array_merge(array(
		'id'        => "badges",
		'type'      => "elements",
		'hide'      => "yes",
		'button'    => esc_html__('Add new badge', 'vbegy'),
		'options'   => apply_filters("askme_badges_points", $badge_elements, $badges_groups),
		'condition' => 'badges_style:is(by_points)',
	), $array_badges_option);

	$options[] = $array_badges;

	$badge_elements = array(
		array(
			"type" => "text",
			"id"   => "badge_name",
			"name" => esc_html__('Badge name', 'vbegy')
		),
		array(
			"type" => "text",
			"id"   => "badge_questions",
			"name" => esc_html__('Questions', 'vbegy')
		),
		array(
			"type" => "color",
			"id"   => "badge_color",
			"name" => esc_html__('Color', 'vbegy')
		)
	);

	$options[] = array(
		'id'        => "badges_questions",
		'type'      => "elements",
		'sort'      => "no",
		'hide'      => "yes",
		'button'    => esc_html__('Add a new badge', 'vbegy'),
		'options'   => $badge_elements,
		'condition' => 'badges_style:is(by_questions)',
	);

	$badge_elements = array(
		array(
			"type" => "text",
			"id"   => "badge_name",
			"name" => esc_html__('Badge name', 'vbegy')
		),
		array(
			"type" => "text",
			"id"   => "badge_answers",
			"name" => esc_html__('Answers', 'vbegy')
		),
		array(
			"type" => "color",
			"id"   => "badge_color",
			"name" => esc_html__('Color', 'vbegy')
		)
	);

	$options[] = array(
		'id'        => "badges_answers",
		'type'      => "elements",
		'sort'      => "no",
		'hide'      => "yes",
		'button'    => esc_html__('Add a new badge', 'vbegy'),
		'options'   => $badge_elements,
		'condition' => 'badges_style:is(by_answers)',
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'points_setting',
		'name' => esc_html__("Points setting", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__("Points for add a new question (put it 0 for off the option)", 'vbegy'),
		'desc' => esc_html__("put the Points choose for add a new question", 'vbegy'),
		'id' => 'point_add_question',
		'type' => 'text',
		'std' => 0
	);

	$options[] = array(
		'name' => esc_html__("Points for add a new post (put it 0 for off the option)", 'vbegy'),
		'desc' => esc_html__("put the Points choose for add a new post", 'vbegy'),
		'id' => 'point_add_post',
		'type' => 'text',
		'std' => 0
	);

	$options[] = array(
		'name' => esc_html__("Points for choosing the best answer", 'vbegy'),
		'desc' => esc_html__("put the Points for choosing the best answer", 'vbegy'),
		'id' => 'point_best_answer',
		'type' => 'text',
		'std' => 5
	);

	$options[] = array(
		'name' => esc_html__("Points Rating question", 'vbegy'),
		'desc' => esc_html__("put the Points Rating question", 'vbegy'),
		'id' => 'point_rating_question',
		'type' => 'text',
		'std' => 1
	);

	$options[] = array(
		'name' => esc_html__('Points for choosing a poll on the question', 'vbegy'),
		'desc' => esc_html__('put the points for choosing a poll on the question', 'vbegy'),
		'id'   => 'point_poll_question',
		'type' => 'text',
		'std'  => 1
	);

	$options[] = array(
		'name' => esc_html__("Points add answer", 'vbegy'),
		'desc' => esc_html__("put the Points add answer", 'vbegy'),
		'id' => 'point_add_comment',
		'type' => 'text',
		'std' => 2
	);

	$options[] = array(
		'name' => esc_html__("Points Rating answer", 'vbegy'),
		'desc' => esc_html__("put the Points Rating answer", 'vbegy'),
		'id' => 'point_rating_answer',
		'type' => 'text',
		'std' => 1
	);

	$options[] = array(
		'name' => esc_html__("Points following user", 'vbegy'),
		'desc' => esc_html__("put the Points following user", 'vbegy'),
		'id' => 'point_following_me',
		'type' => 'text',
		'std' => 1
	);

	$options[] = array(
		'name' => esc_html__("Points for a new user", 'vbegy'),
		'desc' => esc_html__("put the Points for a new user", 'vbegy'),
		'id' => 'point_new_user',
		'type' => 'text',
		'std' => 20
	);

	$options = apply_filters('askme_options_end_of_points', $options);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Blog & Article settings', 'vbegy'),
		'icon' => 'admin-post',
		'type' => 'heading',
		'std'     => 'general_setting_blog',
		'options' => array(
			"general_setting_blog" => "General settings",
			"add_edit_delete_blog" => "Add - Edit - Delete",
		)
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'general_setting_blog',
		'name' => esc_html__("General settings", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__("Blog display", 'vbegy'),
		'desc' => esc_html__("Choose the Blog display", 'vbegy'),
		'id' => 'home_display',
		'std' => 'blog_1',
		'type' => 'radio',
		'options' =>
		array(
			"blog_1" => "Blog 1",
			"blog_2" => "Blog 2"
		)
	);

	$order_sections = array(
		array("name" => esc_html__('Advertising', 'vbegy'), "value" => "advertising", "default" => "yes"),
		array("name" => esc_html__('About the author', 'vbegy'), "value" => "author", "default" => "yes"),
		array("name" => esc_html__('Related articles', 'vbegy'), "value" => "related", "default" => "yes"),
		array("name" => esc_html__('Advertising 2', 'vbegy'), "value" => "advertising_2", "default" => "yes"),
		array("name" => esc_html__('Comments', 'vbegy'), "value" => "comments", "default" => "yes"),
		array("name" => esc_html__('Next and Previous articles', 'vbegy'), "value" => "next_previous", "default" => "yes"),
	);

	$options[] = array(
		'name'    => esc_html__('Sort your sections', 'vbegy'),
		'id'      => 'order_sections',
		'type'    => 'sort',
		'std'     => $order_sections,
		'options' => $order_sections
	);

	$options[] = array(
		'name' => esc_html__('Hide the featured image in the single post', 'vbegy'),
		'desc' => esc_html__('Click ON to hide the featured image in the single post.', 'vbegy'),
		'id' => 'featured_image',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Excerpt type', 'vbegy'),
		'desc' => esc_html__('Choose form here the excerpt type.', 'vbegy'),
		'id' => 'excerpt_type',
		'type' => "select",
		'options' => array(
			'words' => 'Words',
			'characters' => 'Characters'
		)
	);

	$options[] = array(
		'name' => esc_html__('Excerpt post', 'vbegy'),
		'desc' => esc_html__('Put here the excerpt post.', 'vbegy'),
		'id' => 'post_excerpt',
		'std' => 40,
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Post meta enable or disable', 'vbegy'),
		'id' => 'post_meta',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Share enable or disable', 'vbegy'),
		'id' => 'post_share',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Author info box enable or disable', 'vbegy'),
		'id' => 'post_author_box',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Comments enable or disable', 'vbegy'),
		'id' => 'post_comments',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Related post enable or disable', 'vbegy'),
		'id' => 'related_post',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Related post number', 'vbegy'),
		'desc' => esc_html__('Type related post number from here.', 'vbegy'),
		'id' => 'related_number',
		'std' => '5',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__("Related post query", 'vbegy'),
		'desc' => esc_html__("Select your related post query.", 'vbegy'),
		'id' => "related_query",
		'std' => "categories",
		'type' => "select",
		'options' => array(
			'categories' => 'Categories',
			'tags' => 'Tags',
		)
	);

	$options[] = array(
		'name' => esc_html__('Navigation post enable or disable', 'vbegy'),
		'desc' => esc_html__('Navigation post (next and previous posts) enable or disable.', 'vbegy'),
		'id' => 'post_navigation',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Navigation post for the same category only?', 'vbegy'),
		'desc' => esc_html__('Navigation post (next and previous posts) for the same category only?', 'vbegy'),
		'id' => 'post_nav_category',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'id'   => 'add_edit_delete_blog',
		'name' => esc_html__("Add - Edit - Delete", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Add post setting.', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__("Add post page", 'vbegy'),
		'desc' => esc_html__("Create a page using the Add post template and select it here", 'vbegy'),
		'id' => 'add_post_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Activate add post form with popup also?', 'vbegy'),
		'desc' => esc_html__('Activate add post form with popup is enable or disable.', 'vbegy'),
		'id' => 'add_post_popup',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Any one can add post without register', 'vbegy'),
		'desc' => esc_html__('Any one can add post without register enable or disable.', 'vbegy'),
		'id' => 'add_post_no_register',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Choose post status', 'vbegy'),
		'desc' => esc_html__('Choose post status after user publish the post.', 'vbegy'),
		'id' => 'post_publish',
		'options' => array("publish" => esc_html__("Auto publish", "vbegy"), "draft" => esc_html__("Need a review", "vbegy")),
		'std' => 'draft',
		'type' => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Choose post status for unlogged user only', 'vbegy'),
		'desc' => esc_html__('Choose post status after unlogged user publish the post.', 'vbegy'),
		'id' => 'post_publish_unlogged',
		'options' => array("publish" => esc_html__("Auto publish", "vbegy"), "draft" => esc_html__("Need a review", "vbegy")),
		'std' => 'draft',
		'type' => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Send email when the post need a review', 'vbegy'),
		'desc' => esc_html__('Email for posts review enable or disable.', 'vbegy'),
		'id' => 'send_email_draft_posts',
		'std' => 1,
		'type' => 'checkbox'
	);

	$add_post_items = array(
		"tags_post"      => array("sort" => esc_html__('Post Tags', 'vbegy'), "value" => "tags_post"),
		"featured_image" => array("sort" => esc_html__('Post featured image', 'vbegy'), "value" => "featured_image"),
		"content_post"   => array("sort" => esc_html__('Post content', 'vbegy'), "value" => "content_post"),
		"terms_active"   => array("sort" => esc_html__('Terms of Service and Privacy Policy', 'vbegy'), "value" => ""),
	);

	$options[] = array(
		'name'    => esc_html__("Select what's show at add post form", "vbegy"),
		'id'      => 'add_post_items',
		'type'    => 'multicheck_3',
		'sort'    => 'yes',
		'std'     => $add_post_items,
		'options' => $add_post_items
	);

	$options[] = array(
		'name' => esc_html__('Attachment in add post form', 'vbegy'),
		'desc' => esc_html__('Select ON to enable the attachment in add post form.', 'vbegy'),
		'id' => 'attachment_post',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Editor enable or disable for details in add post form', 'vbegy'),
		'id' => 'editor_post_details',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Choose the way of the sending mails and notifications of a new post', 'vbegy'),
		'id'      => 'send_email_and_notification_post',
		'options' => array(
			'both'       => esc_html__('Both with the same options', 'vbegy'),
			'separately' => esc_html__('Separately', 'vbegy'),
		),
		'std'     => 'both',
		'type'    => 'radio'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'send_email_and_notification_post:has(both)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Send mail and notification to the users about the notification of a new post', 'vbegy'),
		'desc' => esc_html__('Send mail and notification enable or disable.', 'vbegy'),
		'id'   => 'send_email_new_post_both',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Send mail and notification for custom roles about the notification of a new post', 'vbegy'),
		'id'        => 'send_email_post_groups_both',
		'type'      => 'multicheck',
		'condition' => 'send_email_new_post_both:not(0)',
		'std'       => array("editor" => "editor", "administrator" => "administrator", "author" => "author", "contributor" => "contributor", "subscriber" => "subscriber"),
		'options'   => $options_groups
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'send_email_and_notification_post:has(separately)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Send email for the user to notified a new post', 'vbegy'),
		'desc' => esc_html__('Send email enable or disable.', 'vbegy'),
		'id' => 'send_email_new_post',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'send_email_new_post',
		'condition' => 'send_email_new_post:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Send email for custom groups to notified a new post', 'vbegy'),
		'id' => 'send_email_post_groups',
		'type' => 'multicheck',
		'std' => array("editor" => 1, "administrator" => 1, "author" => 1, "contributor" => 1, "subscriber" => 1),
		'options' => $options_groups
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Send notification for the user to notified a new post', 'vbegy'),
		'desc' => esc_html__('Send notification enable or disable.', 'vbegy'),
		'id' => 'send_notification_new_post',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'id'        => 'send_notification_new_post',
		'condition' => 'send_notification_new_post:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Send notification for custom groups to notified a new post', 'vbegy'),
		'id' => 'send_notification_post_groups',
		'type' => 'multicheck',
		'std' => array("editor" => 1, "administrator" => 1, "author" => 1, "contributor" => 1, "subscriber" => 1),
		'options' => $options_groups
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'add_post_items:has(terms_active)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('Terms of Service and Privacy Policy', 'vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Select the checked by default option', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to checked it by default.', 'vbegy'),
		'id'   => 'terms_checked_post',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'terms_active_target_post',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'name'    => esc_html__('Terms page', 'vbegy'),
		'desc'    => esc_html__('Select the terms page', 'vbegy'),
		'id'      => 'terms_page_post',
		'type'    => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Type the terms link if you don't like a page", "vbegy"),
		'id'   => 'terms_link_post',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Activate Privacy Policy', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to activate Privacy Policy.', 'vbegy'),
		'id'   => 'privacy_policy_post',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'privacy_policy_post:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'privacy_active_target_post',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'name'    => esc_html__('Privacy Policy page', 'vbegy'),
		'desc'    => esc_html__('Select the privacy policy page', 'vbegy'),
		'id'      => 'privacy_page_post',
		'type'    => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Type the privacy policy link if you don't like a page", "vbegy"),
		'id'   => 'privacy_link_post',
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Edit post setting.', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('The users can edit the posts?', 'vbegy'),
		'id' => 'can_edit_post',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Edit post page", 'vbegy'),
		'desc' => esc_html__("Create a page using the Edit post template and select it here", 'vbegy'),
		'id' => 'edit_post',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('After edit post approved auto or need to approved again?', 'vbegy'),
		'desc' => esc_html__('Press ON to approved auto', 'vbegy'),
		'id' => 'post_approved',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('After edit post change the URL like the title?', 'vbegy'),
		'desc' => esc_html__('Press ON to edit the URL', 'vbegy'),
		'id' => 'change_post_url',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Delete post setting.', 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Activate user can delete the posts', 'vbegy'),
		'desc' => esc_html__('Select ON if you want the user can delete the posts.', 'vbegy'),
		'id' => 'post_delete',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'    => esc_html__('When the users delete the post went to the trash or delete it ever?', 'vbegy'),
		'id'      => 'delete_post',
		'options' => array(
			'delete' => esc_html__('Delete', 'vbegy'),
			'trash'  => esc_html__('Trash', 'vbegy'),
		),
		'std'     => 'delete',
		'type'    => 'radio'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Search setting', 'vbegy'),
		'icon' => 'search',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__("Search page", 'vbegy'),
		'desc' => esc_html__("Create a page using the Search template and select it here", 'vbegy'),
		'id' => 'search_page',
		'type' => 'select',
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__("Default search", 'vbegy'),
		'desc' => esc_html__("Choose what's the default search", 'vbegy'),
		'id' => 'default_search',
		'type' => 'select',
		'stc' => 'questions',
		'options' => array(
			"questions"           => "Questions",
			"answers"             => "Answers",
			ask_question_category => "Question categories",
			ask_question_tags     => "Question tags",
			"posts"               => "Posts",
			"comments"            => "Comments",
			"category"            => "Post categories",
			"post_tag"            => "Post tags",
			"products"            => "Products",
			"product_cat"         => "Products categories",
			"product_tag"         => "Products tags",
			"users"               => "Users",
		)
	);

	$options[] = array(
		'name'    => esc_html__('Select the search options', 'vbegy'),
		'desc'    => esc_html__('Select the search options on the search page.', 'vbegy'),
		'id'      => 'search_attrs',
		'type'    => 'multicheck_sort',
		'sort'    => 'yes',
		'std'     => array(
			"questions"           => array("sort" => "Questions", "value" => "on"),
			"answers"             => array("sort" => "Answers", "value" => "on"),
			ask_question_category => array("sort" => "Question categories", "value" => "on"),
			ask_question_tags     => array("sort" => "Question tags", "value" => "on"),
			"posts"               => array("sort" => "Posts", "value" => "on"),
			"comments"            => array("sort" => "Comments", "value" => "on"),
			"category"            => array("sort" => "Post categories", "value" => "on"),
			"post_tag"            => array("sort" => "Post tags", "value" => "on"),
			"products"            => array("sort" => "Products", "value" => "on"),
			"product_cat"         => array("sort" => "Products categories", "value" => "on"),
			"product_tag"         => array("sort" => "Products tags", "value" => "on"),
			"users"               => array("sort" => "Users", "value" => "on"),
		),
		'options' => array(
			"questions"           => array("sort" => "Questions", "value" => "on"),
			"answers"             => array("sort" => "Answers", "value" => "on"),
			ask_question_category => array("sort" => "Question categories", "value" => "on"),
			ask_question_tags     => array("sort" => "Question tags", "value" => "on"),
			"posts"               => array("sort" => "Posts", "value" => "on"),
			"comments"            => array("sort" => "Comments", "value" => "on"),
			"category"            => array("sort" => "Post categories", "value" => "on"),
			"post_tag"            => array("sort" => "Post tags", "value" => "on"),
			"products"            => array("sort" => "Products", "value" => "on"),
			"product_cat"         => array("sort" => "Products categories", "value" => "on"),
			"product_tag"         => array("sort" => "Products tags", "value" => "on"),
			"users"               => array("sort" => "Users", "value" => "on"),
		)
	);

	$options[] = array(
		'name'  => esc_html__("Choose the live search enable or disable", 'vbegy'),
		'id'    => "live_search",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'name' => esc_html__('Search result number', 'vbegy'),
		'desc' => esc_html__('Type the search result number from here.', 'vbegy'),
		'id' => 'search_result_number',
		'std' => '5',
		'type' => 'text'
	);

	$options[] = array(
		'name'  => esc_html__("Show search at users template", 'vbegy'),
		'desc'  => esc_html__("Show search at users template from the breadcrumb", 'vbegy'),
		'id'    => "user_search",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'name'  => esc_html__("Show filter at users template", 'vbegy'),
		'desc'  => esc_html__("Show filter at users template from the breadcrumb", 'vbegy'),
		'id'    => "user_filter",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'name' => esc_html__('Show filter at categories and archive pages', 'vbegy'),
		'desc' => esc_html__('Click ON to enable the filter at categories and archive pages.', 'vbegy'),
		'id'   => 'category_filter',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Show at the filter categories parent categories', 'vbegy'),
		'desc' => esc_html__('Click ON to enable the filter categories parent categories and will show the child categires.', 'vbegy'),
		'id'   => 'child_category',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__("Show search at category archives", 'vbegy'),
		'desc'  => esc_html__("Show search at category archives from the breadcrumb", 'vbegy'),
		'id'    => "cat_archives_search",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'name'  => esc_html__("Show search at categories template", 'vbegy'),
		'desc'  => esc_html__("Show search at categories template from the breadcrumb", 'vbegy'),
		'id'    => "cat_search",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'name'  => esc_html__("Show filter at categories template", 'vbegy'),
		'desc'  => esc_html__("Show filter at categories template from the breadcrumb", 'vbegy'),
		'id'    => "cat_filter",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'name'  => esc_html__("Show search at tag archives", 'vbegy'),
		'desc'  => esc_html__("Show search at tag archives from the breadcrumb", 'vbegy'),
		'id'    => "tag_archives_search",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'name'  => esc_html__("Show search at tags template", 'vbegy'),
		'desc'  => esc_html__("Show search at tags template from the breadcrumb", 'vbegy'),
		'id'    => "tag_search",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'name'  => esc_html__("Show filter at tags template", 'vbegy'),
		'desc'  => esc_html__("Show filter at tags template from the breadcrumb", 'vbegy'),
		'id'    => "tag_filter",
		'type'  => 'checkbox',
		'std'   => 1,
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Sidebar', 'vbegy'),
		'icon' => 'align-none',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'id' => "sidebars",
		'std' => '',
		'type' => 'sidebar'
	);

	$options[] = array(
		'name' => esc_html__("Sidebar width", 'vbegy'),
		'id' => 'sidebar_width',
		'std' => 'col-md-3',
		'type' => 'radio',
		'options' =>
		array(
			"col-md-3" => "1/4",
			"col-md-4" => "1/3"
		)
	);

	$options[] = array(
		'name' => esc_html__("Sticky sidebar", 'vbegy'),
		'desc' => esc_html__("Click ON to active the sticky sidebar", 'vbegy'),
		'id' => 'sticky_sidebar',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Sidebar layout", 'vbegy'),
		'id' => "sidebar_layout",
		'std' => "default",
		'type' => "images",
		'options' => array(
			'default' => $imagepath . 'sidebar_default.jpg',
			'right' => $imagepath . 'sidebar_right.jpg',
			'full' => $imagepath . 'sidebar_no.jpg',
			'left' => $imagepath . 'sidebar_left.jpg',
		)
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'sidebar_layout:not(full)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__("Home Page Sidebar", 'vbegy'),
		'id' => "sidebar_home",
		'std' => '',
		'options' => $new_sidebars,
		'type' => 'select'
	);

	$options[] = array(
		'name' => esc_html__("Else home page, single and page", 'vbegy'),
		'id' => "else_sidebar",
		'std' => '',
		'options' => $new_sidebars,
		'type' => 'select'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Styling & Typography', 'vbegy'),
		'icon' => 'art',
		'type' => 'heading',
		'std'     => 'general_setting',
		'options' => array(
			"styling"   => esc_html__('Styling', 'vbegy'),
			"typography"   => esc_html__('Typography', 'vbegy'),
		)
	);

	$options[] = array(
		'name' => esc_html__('Styling', 'vbegy'),
		'id'   => 'styling',
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__("Home page layout", 'vbegy'),
		'id' => "home_layout",
		'std' => "full",
		'type' => "images",
		'options' => array(
			'full' => $imagepath . 'full.jpg',
			'fixed' => $imagepath . 'fixed.jpg',
			'fixed_2' => $imagepath . 'fixed_2.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Choose template", 'vbegy'),
		'desc' => esc_html__("Choose template layout.", 'vbegy'),
		'id' => "home_template",
		'std' => "grid_1200",
		'type' => "images",
		'options' => array(
			'grid_1300' => $imagepath . 'template_1300.jpg',
			'grid_1200' => $imagepath . 'template_1200.jpg',
			'grid_970' => $imagepath . 'template_970.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Site skin", 'vbegy'),
		'desc' => esc_html__("Choose Site skin.", 'vbegy'),
		'id' => "site_skin_l",
		'std' => "site_light",
		'type' => "images",
		'options' => array(
			'site_light' => $imagepath . 'light.jpg',
			'site_dark' => $imagepath . 'dark.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Choose Your Skin", 'vbegy'),
		'class' => "site_skin",
		'id' => "site_skin",
		'std' => "skins",
		'type' => "images",
		'options' => array(
			'skins'		    => $imagepath . 'skin.jpg',
			'blue'			=> $imagepath . 'blue.jpg',
			'gray'			=> $imagepath . 'gray.jpg',
			'green'			=> $imagepath . 'green.jpg',
			'moderate_cyan' => $imagepath . 'moderate_cyan.jpg',
			'orange'		=> $imagepath . 'orange.jpg',
			'purple'	    => $imagepath . 'purple.jpg',
			'red'			=> $imagepath . 'red.jpg',
			'strong_cyan'	=> $imagepath . 'strong_cyan.jpg',
			'yellow'		=> $imagepath . 'yellow.jpg',
		)
	);

	$options[] = array(
		'name' => esc_html__("Primary Color", 'vbegy'),
		'id' => 'primary_color',
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__("Background Type", 'vbegy'),
		'id' => 'background_type',
		'std' => 'patterns',
		'type' => 'radio',
		'options' =>
		array(
			"patterns" => "Patterns",
			"custom_background" => "Custom Background"
		)
	);

	$options[] = array(
		'name' => esc_html__("Background Color", 'vbegy'),
		'id' => 'background_color',
		'std' => "#FFF",
		'type' => 'color'
	);

	$options[] = array(
		'name' => esc_html__("Choose Pattern", 'vbegy'),
		'id' => "background_pattern",
		'std' => "bg13",
		'type' => "images",
		'options' => array(
			'bg1' => $imagepath . 'bg1.jpg',
			'bg2' => $imagepath . 'bg2.jpg',
			'bg3' => $imagepath . 'bg3.jpg',
			'bg4' => $imagepath . 'bg4.jpg',
			'bg5' => $imagepath . 'bg5.jpg',
			'bg6' => $imagepath . 'bg6.jpg',
			'bg7' => $imagepath . 'bg7.jpg',
			'bg8' => $imagepath . 'bg8.jpg',
			'bg9' => $imagepath . '../../images/patterns/bg9.png',
			'bg10' => $imagepath . '../../images/patterns/bg10.png',
			'bg11' => $imagepath . '../../images/patterns/bg11.png',
			'bg12' => $imagepath . '../../images/patterns/bg12.png',
			'bg13' => $imagepath . 'bg13.jpg',
			'bg14' => $imagepath . 'bg14.jpg',
			'bg15' => $imagepath . '../../images/patterns/bg15.png',
			'bg16' => $imagepath . '../../images/patterns/bg16.png',
			'bg17' => $imagepath . 'bg17.jpg',
			'bg18' => $imagepath . 'bg18.jpg',
			'bg19' => $imagepath . 'bg19.jpg',
			'bg20' => $imagepath . 'bg20.jpg',
			'bg21' => $imagepath . '../../images/patterns/bg21.png',
			'bg22' => $imagepath . 'bg22.jpg',
			'bg23' => $imagepath . '../../images/patterns/bg23.png',
			'bg24' => $imagepath . '../../images/patterns/bg24.png',
		)
	);

	$options[] = array(
		'name' => esc_html__("Custom Background", 'vbegy'),
		'id' => 'custom_background',
		'std' => $background_defaults,
		'type' => 'background'
	);

	$options[] = array(
		'name' => esc_html__("Full Screen Background", 'vbegy'),
		'desc' => esc_html__("Click ON to Full Screen Background", 'vbegy'),
		'id' => 'full_screen_background',
		'std' => '0',
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Typography', 'vbegy'),
		'id'   => 'typography',
		'type' => 'heading-2',
	);

	$options[] = array(
		"name"    => esc_html__('Main font', 'vbegy'),
		"id"      => "main_font",
		"type"    => "typography",
		'std'     => array("face" => "Default font", "color" => "", "style" => "", "size" => 9),
		'options' => array("color" => false, "styles" => false, "sizes" => false)
	);

	$options[] = array(
		"name"    => esc_html__('Second font', 'vbegy'),
		"id"      => "second_font",
		"type"    => "typography",
		'std'     => array("face" => "Default font", "color" => "", "style" => "", "size" => 9),
		'options' => array("color" => false, "styles" => false, "sizes" => false)
	);

	$options[] = array(
		"name"    => esc_html__('General Typography', 'vbegy'),
		"id"      => "general_typography",
		"type"    => "typography",
		'options' => array('faces' => false)
	);

	$options[] = array(
		'name' => esc_html__('General link color', 'vbegy'),
		"id"   => "general_link_color",
		"type" => "color"
	);

	$options[] = array(
		"name"    => esc_html__('H1', 'vbegy'),
		"id"      => "h1",
		"type"    => "typography",
		'options' => array('faces' => false, "color" => false)
	);

	$options[] = array(
		"name"    => esc_html__('H2', 'vbegy'),
		"id"      => "h2",
		"type"    => "typography",
		'options' => array('faces' => false, "color" => false)
	);

	$options[] = array(
		"name"    => esc_html__('H3', 'vbegy'),
		"id"      => "h3",
		"type"    => "typography",
		'options' => array('faces' => false, "color" => false)
	);

	$options[] = array(
		"name"    => esc_html__('H4', 'vbegy'),
		"id"      => "h4",
		"type"    => "typography",
		'options' => array('faces' => false, "color" => false)
	);

	$options[] = array(
		"name"    => esc_html__('H5', 'vbegy'),
		"id"      => "h5",
		"type"    => "typography",
		'options' => array('faces' => false, "color" => false)
	);

	$options[] = array(
		"name"    => esc_html__('H6', 'vbegy'),
		"id"      => "h6",
		"type"    => "typography",
		'options' => array('faces' => false, "color" => false)
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	if (class_exists('woocommerce')) {
		$options[] = array(
			'name' => esc_html__('Products Setting', 'vbegy'),
			'icon' => 'admin-home',
			'type' => 'heading'
		);

		$options[] = array(
			'type' => 'heading-2',
		);

		$options[] = array(
			'name' => esc_html__("Custom Logo position - Header skin - Logo display?", 'vbegy'),
			'desc' => esc_html__("Click ON to make a Custom Logo position - Header skin - Logo display", 'vbegy'),
			'id' => 'products_custom_header',
			'std' => '0',
			'type' => 'checkbox'
		);

		if (is_rtl()) {
			$options[] = array(
				'name' => esc_html__("Logo position for products", 'vbegy'),
				'desc' => esc_html__("Select where you would like your logo to appear for products.", 'vbegy'),
				'id' => "products_logo_position",
				'std' => "left_logo",
				'type' => "images",
				'options' => array(
					'left_logo' => $imagepath . 'right_logo.jpg',
					'right_logo' => $imagepath . 'left_logo.jpg',
					'center_logo' => $imagepath . 'center_logo.jpg'
				)
			);
		} else {
			$options[] = array(
				'name' => esc_html__("Logo position for products", 'vbegy'),
				'desc' => esc_html__("Select where you would like your logo to appear for products.", 'vbegy'),
				'id' => "products_logo_position",
				'std' => "left_logo",
				'type' => "images",
				'options' => array(
					'left_logo' => $imagepath . 'left_logo.jpg',
					'right_logo' => $imagepath . 'right_logo.jpg',
					'center_logo' => $imagepath . 'center_logo.jpg'
				)
			);
		}

		$options[] = array(
			'name' => esc_html__("Header skin for products", 'vbegy'),
			'desc' => esc_html__("Select your preferred header skin for products.", 'vbegy'),
			'id' => "products_header_skin",
			'std' => "header_dark",
			'type' => "images",
			'options' => array(
				'header_dark' => $imagepath . 'left_logo.jpg',
				'header_light' => $imagepath . 'header_light.jpg'
			)
		);

		$options[] = array(
			'name' => esc_html__('Logo display for products', 'vbegy'),
			'desc' => esc_html__('choose Logo display for products.', 'vbegy'),
			'id' => 'products_logo_display',
			'std' => 'display_title',
			'type' => 'radio',
			'options' => array("display_title" => "Display site title", "custom_image" => "Custom Image")
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => 'products_logo_display:not(display_title)',
			'type'      => 'heading-2'
		);

		$options[] = array(
			'name' => esc_html__('Logo upload for products', 'vbegy'),
			'desc' => esc_html__('Upload your custom logo for products.', 'vbegy'),
			'id' => 'products_logo_img',
			'std' => '',
			'type' => 'upload'
		);

		$options[] = array(
			'name' => esc_html__('Logo retina upload for products', 'vbegy'),
			'desc' => esc_html__('Upload your custom logo retina for products.', 'vbegy'),
			'id' => 'products_retina_logo',
			'std' => '',
			'type' => 'upload'
		);

		$options[] = array(
			"name" => esc_html__("Logo height", 'vbegy'),
			"id" => "products_logo_height",
			"type" => "sliderui",
			"step" => "1",
			"min" => "0",
			"max" => "300",
			'std' => '57'
		);

		$options[] = array(
			"name" => esc_html__("Logo width", 'vbegy'),
			"id" => "products_logo_width",
			"type" => "sliderui",
			"step" => "1",
			"min" => "0",
			"max" => "300",
			'std' => '146'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);

		$options[] = array(
			'name' => esc_html__('Related products number', 'vbegy'),
			'desc' => esc_html__('Type related products number from here.', 'vbegy'),
			'id' => 'related_products_number',
			'std' => '3',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Related products number full width', 'vbegy'),
			'desc' => esc_html__('Type related products number full width from here.', 'vbegy'),
			'id' => 'related_products_number_full',
			'std' => '4',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('Excerpt title in products pages', 'vbegy'),
			'desc' => esc_html__('Type excerpt title in products pages from here.', 'vbegy'),
			'id' => 'products_excerpt_title',
			'std' => '40',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__("Products sidebar layout", 'vbegy'),
			'id' => "products_sidebar_layout",
			'std' => "default",
			'type' => "images",
			'options' => array(
				'default' => $imagepath . 'sidebar_default.jpg',
				'right' => $imagepath . 'sidebar_right.jpg',
				'full' => $imagepath . 'sidebar_no.jpg',
				'left' => $imagepath . 'sidebar_left.jpg',
			)
		);

		$options[] = array(
			'name' => esc_html__("Products Page Sidebar", 'vbegy'),
			'id' => "products_sidebar",
			'std' => '',
			'options' => $new_sidebars,
			'type' => 'select'
		);

		$options[] = array(
			'name' => esc_html__("Products page layout", 'vbegy'),
			'id' => "products_layout",
			'std' => "default",
			'type' => "images",
			'options' => array(
				'default' => $imagepath . 'sidebar_default.jpg',
				'full' => $imagepath . 'full.jpg',
				'fixed' => $imagepath . 'fixed.jpg',
				'fixed_2' => $imagepath . 'fixed_2.jpg'
			)
		);

		$options[] = array(
			'name' => esc_html__("Choose template", 'vbegy'),
			'desc' => esc_html__("Choose template layout.", 'vbegy'),
			'id' => "products_template",
			'std' => "default",
			'type' => "images",
			'options' => array(
				'default' => $imagepath . 'sidebar_default.jpg',
				'grid_1300' => $imagepath . 'template_1300.jpg',
				'grid_1200' => $imagepath . 'template_1200.jpg',
				'grid_970' => $imagepath . 'template_970.jpg'
			)
		);

		$options[] = array(
			'name' => esc_html__("Site skin", 'vbegy'),
			'desc' => esc_html__("Choose Site skin.", 'vbegy'),
			'id' => "products_skin_l",
			'std' => "default",
			'type' => "images",
			'options' => array(
				'default' => $imagepath . 'sidebar_default.jpg',
				'site_light' => $imagepath . 'light.jpg',
				'site_dark' => $imagepath . 'dark.jpg'
			)
		);

		$options[] = array(
			'name' => esc_html__("Choose Your Skin", 'vbegy'),
			'class' => "site_skin",
			'id' => "products_skin",
			'std' => "default",
			'type' => "images",
			'options' => array(
				'default'	    => $imagepath . 'default.jpg',
				'skins'		    => $imagepath . 'skin.jpg',
				'blue'			=> $imagepath . 'blue.jpg',
				'gray'			=> $imagepath . 'gray.jpg',
				'green'			=> $imagepath . 'green.jpg',
				'moderate_cyan' => $imagepath . 'moderate_cyan.jpg',
				'orange'		=> $imagepath . 'orange.jpg',
				'purple'	    => $imagepath . 'purple.jpg',
				'red'			=> $imagepath . 'red.jpg',
				'strong_cyan'	=> $imagepath . 'strong_cyan.jpg',
				'yellow'		=> $imagepath . 'yellow.jpg',
			)
		);

		$options[] = array(
			'name' => esc_html__("Primary Color", 'vbegy'),
			'id' => 'products_primary_color',
			'type' => 'color'
		);

		$options[] = array(
			'name' => esc_html__("Background Type", 'vbegy'),
			'id' => 'products_background_type',
			'std' => 'patterns',
			'type' => 'radio',
			'options' =>
			array(
				"patterns" => "Patterns",
				"custom_background" => "Custom Background"
			)
		);

		$options[] = array(
			'name' => esc_html__("Background Color", 'vbegy'),
			'id' => 'products_background_color',
			'std' => "#FFF",
			'type' => 'color'
		);

		$options[] = array(
			'name' => esc_html__("Choose Pattern", 'vbegy'),
			'id' => "products_background_pattern",
			'std' => "bg13",
			'type' => "images",
			'options' => array(
				'bg1' => $imagepath . 'bg1.jpg',
				'bg2' => $imagepath . 'bg2.jpg',
				'bg3' => $imagepath . 'bg3.jpg',
				'bg4' => $imagepath . 'bg4.jpg',
				'bg5' => $imagepath . 'bg5.jpg',
				'bg6' => $imagepath . 'bg6.jpg',
				'bg7' => $imagepath . 'bg7.jpg',
				'bg8' => $imagepath . 'bg8.jpg',
				'bg9' => $imagepath . '../../images/patterns/bg9.png',
				'bg10' => $imagepath . '../../images/patterns/bg10.png',
				'bg11' => $imagepath . '../../images/patterns/bg11.png',
				'bg12' => $imagepath . '../../images/patterns/bg12.png',
				'bg13' => $imagepath . 'bg13.jpg',
				'bg14' => $imagepath . 'bg14.jpg',
				'bg15' => $imagepath . '../../images/patterns/bg15.png',
				'bg16' => $imagepath . '../../images/patterns/bg16.png',
				'bg17' => $imagepath . 'bg17.jpg',
				'bg18' => $imagepath . 'bg18.jpg',
				'bg19' => $imagepath . 'bg19.jpg',
				'bg20' => $imagepath . 'bg20.jpg',
				'bg21' => $imagepath . '../../images/patterns/bg21.png',
				'bg22' => $imagepath . 'bg22.jpg',
				'bg23' => $imagepath . '../../images/patterns/bg23.png',
				'bg24' => $imagepath . '../../images/patterns/bg24.png',
			)
		);

		$options[] = array(
			'name' => esc_html__("Custom Background", 'vbegy'),
			'id' => 'products_custom_background',
			'std' => $background_defaults,
			'type' => 'background'
		);

		$options[] = array(
			'name' => esc_html__("Full Screen Background", 'vbegy'),
			'desc' => esc_html__("Click ON to Full Screen Background", 'vbegy'),
			'id' => 'products_full_screen_background',
			'std' => '0',
			'type' => 'checkbox'
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
	}

	$options[] = array(
		'name' => esc_html__('Advertising', 'vbegy'),
		'icon' => 'megaphone',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Advertising at 404 pages enable or disable', 'vbegy'),
		'id'   => 'adv_404',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'name' => esc_html__("Advertising after header", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Advertising type', 'vbegy'),
		'id' => 'header_adv_type',
		'std' => 'custom_image',
		'type' => 'radio',
		'options' => array("display_code" => "Display code", "custom_image" => "Custom Image")
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'header_adv_type:is(custom_image)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Image URL', 'vbegy'),
		'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.', 'vbegy'),
		'id' => 'header_adv_img',
		'std' => '',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Advertising url', 'vbegy'),
		'id' => 'header_adv_href',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'header_adv_link',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__("Advertising Code html (Ex: Google ads)", 'vbegy'),
		'condition' => 'header_adv_type:is(display_code)',
		'id' => 'header_adv_code',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'name' => esc_html__("Advertising 1 in post and question", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Advertising type', 'vbegy'),
		'id' => 'share_adv_type',
		'std' => 'custom_image',
		'type' => 'radio',
		'options' => array("display_code" => "Display code", "custom_image" => "Custom Image")
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'share_adv_type:is(custom_image)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Image URL', 'vbegy'),
		'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.', 'vbegy'),
		'id' => 'share_adv_img',
		'std' => '',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Advertising URL', 'vbegy'),
		'id' => 'share_adv_href',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'share_adv_link',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__("Advertising Code html (Ex: Google ads)", 'vbegy'),
		'condition' => 'share_adv_type:is(display_code)',
		'id' => 'share_adv_code',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'name' => esc_html__("Advertising 2 in post and question", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Advertising type', 'vbegy'),
		'id' => 'related_adv_type',
		'std' => 'custom_image',
		'type' => 'radio',
		'options' => array("display_code" => "Display code", "custom_image" => "Custom Image")
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'related_adv_type:is(custom_image)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Image URL', 'vbegy'),
		'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.', 'vbegy'),
		'id' => 'related_adv_img',
		'std' => '',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Advertising URL', 'vbegy'),
		'id' => 'related_adv_href',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'related_adv_link',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__("Advertising Code html (Ex: Google ads)", 'vbegy'),
		'condition' => 'related_adv_type:is(display_code)',
		'id' => 'related_adv_code',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'name' => esc_html__("Advertising after content", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Advertising type', 'vbegy'),
		'id' => 'content_adv_type',
		'std' => 'custom_image',
		'type' => 'radio',
		'options' => array("display_code" => "Display code", "custom_image" => "Custom Image")
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'content_adv_type:is(custom_image)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Image URL', 'vbegy'),
		'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.', 'vbegy'),
		'id' => 'content_adv_img',
		'std' => '',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Advertising URL', 'vbegy'),
		'id' => 'content_adv_href',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'content_adv_link',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__("Advertising Code html (Ex: Google ads)", 'vbegy'),
		'condition' => 'content_adv_type:is(display_code)',
		'id' => 'content_adv_code',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'name' => esc_html__("Between questions and posts", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Between questions or posts position', 'vbegy'),
		'id' => 'between_questions_position',
		'std' => '2',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Advertising type', 'vbegy'),
		'id' => 'between_adv_type',
		'std' => 'custom_image',
		'type' => 'radio',
		'options' => array("display_code" => "Display code", "custom_image" => "Custom Image")
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'between_adv_type:is(custom_image)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Image URL', 'vbegy'),
		'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.', 'vbegy'),
		'id' => 'between_adv_img',
		'std' => '',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Advertising URL', 'vbegy'),
		'id' => 'between_adv_href',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'between_adv_link',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__("Advertising Code html (Ex: Google ads)", 'vbegy'),
		'condition' => 'between_adv_type:is(display_code)',
		'id' => 'between_adv_code',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'heading-2',
		'name' => esc_html__("Between comments and answers", 'vbegy'),
	);

	$options[] = array(
		'name' => esc_html__('Between comments and answers position', 'vbegy'),
		'id' => 'between_comments_position',
		'std' => '2',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Advertising type', 'vbegy'),
		'id' => 'between_comments_adv_type',
		'std' => 'custom_image',
		'type' => 'radio',
		'options' => array("display_code" => "Display code", "custom_image" => "Custom Image")
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'between_comments_adv_type:is(custom_image)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Image URL', 'vbegy'),
		'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.', 'vbegy'),
		'id' => 'between_comments_adv_img',
		'std' => '',
		'type' => 'upload'
	);

	$options[] = array(
		'name' => esc_html__('Advertising URL', 'vbegy'),
		'id' => 'between_comments_adv_href',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__('Open the page in same page or a new page?', 'vbegy'),
		'id'      => 'between_comments_adv_link',
		'std'     => "new_page",
		'type'    => 'select',
		'options' => array("same_page" => esc_html__("Same page", "vbegy"), "new_page" => esc_html__("New page", "vbegy"))
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__("Advertising Code html (Ex: Google ads)", 'vbegy'),
		'condition' => 'between_comments_adv_type:is(display_code)',
		'id' => 'between_comments_adv_code',
		'std' => '',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Social settings', 'vbegy'),
		'icon' => 'share',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Social header enable or disable', 'vbegy'),
		'desc' => esc_html__('Social enable or disable.', 'vbegy'),
		'id' => 'social_icon_h',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Social footer enable or disable', 'vbegy'),
		'desc' => esc_html__('Social enable or disable.', 'vbegy'),
		'id' => 'social_icon_f',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'social_icon_h:not(0),social_icon_f:not(0)',
		'operator'  => 'or',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Twitter URL', 'vbegy'),
		'desc' => esc_html__('Type the twitter URL from here.', 'vbegy'),
		'id' => 'twitter_icon_f',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Facebook URL', 'vbegy'),
		'desc' => esc_html__('Type the facebook URL from here.', 'vbegy'),
		'id' => 'facebook_icon_f',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('TikTok URL', 'vbegy'),
		'desc' => esc_html__('Type the TikTok URL from here.', 'vbegy'),
		'id' => 'tiktok_icon_f',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Youtube URL', 'vbegy'),
		'desc' => esc_html__('Type the youtube URL from here.', 'vbegy'),
		'id' => 'youtube_icon_f',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Skype', 'vbegy'),
		'desc' => esc_html__('Type the skype from here.', 'vbegy'),
		'id' => 'skype_icon_f',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Linkedin URL', 'vbegy'),
		'desc' => esc_html__('Type the linkedin URL from here.', 'vbegy'),
		'id' => 'linkedin_icon_f',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Flickr URL', 'vbegy'),
		'desc' => esc_html__('Type the flickr URL from here.', 'vbegy'),
		'id' => 'flickr_icon_f',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Instagram URL', 'vbegy'),
		'desc' => esc_html__('Type the instagram URL from here.', 'vbegy'),
		'id' => 'instagram_icon_f',
		'std' => '#',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('RSS enable or disable', 'vbegy'),
		'id' => 'rss_icon_f',
		'std' => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end',
		'div'  => 'div'
	);

	$options[] = array(
		'name' => esc_html__('RSS URL if you want change the default URL', 'vbegy'),
		'desc' => esc_html__('Type the RSS URL if you want change the default URL or leave it empty for enable the default URL.', 'vbegy'),
		'id' => 'rss_icon_f_other',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Emails settings', 'vbegy'),
		'icon' => 'email',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name'    => esc_html__('Email template style', 'vbegy'),
		'id'      => 'email_style',
		'std'     => 'style_1',
		'type'    => 'radio',
		'options' => array("style_1" => "Style 1", "style_2" => "Style 2")
	);

	$options[] = array(
		'name' => esc_html__("Custom logo for mail template", "vbegy"),
		'desc' => esc_html__("Upload your custom logo for mail template", "vbegy"),
		'id'   => 'logo_email_template',
		'std'  => $imagepath_theme . "logo.png",
		'type' => 'upload'
	);

	$options[] = array(
		'name'      => esc_html__('Background Color for the mail template', 'vbegy'),
		'id'        => 'background_email',
		'condition' => 'email_style:not(style_2)',
		'type'      => 'color',
		'std'       => '#272930'
	);

	$options[] = array(
		'name' => esc_html__('Custom email custom display name', 'vbegy'),
		'id'   => 'custom_mail_name',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__("Add your email custom display name", "vbegy"),
		'desc'      => esc_html__("Add it professional name, like 2code", "vbegy"),
		'id'        => 'mail_name',
		'condition' => 'custom_mail_name:not(0)',
		'std'       => get_bloginfo('name'),
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail enable or disable', 'vbegy'),
		'id'   => 'mail_smtp',
		'type' => 'checkbox'
	);

	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'mail_smtp:not(0)',
		'div'       => 'div'
	);

	$parse = parse_url(get_site_url());

	$options[] = array(
		'name' => esc_html__("Add your email for mail template", "vbegy"),
		'desc' => esc_html__("Add it professional email, like no_reply@intself.com", "vbegy"),
		'id'   => 'email_template',
		'std'  => "no_reply@" . $parse['host'],
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail host', 'vbegy'),
		'id'   => 'mail_host',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail port', 'vbegy'),
		'id'   => 'mail_port',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail username', 'vbegy'),
		'id'   => 'mail_username',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('SMTP mail password', 'vbegy'),
		'id'   => 'mail_password',
		'type' => 'password',
	);

	$options[] = array(
		'name'    => esc_html__('SMTP mail secure', 'vbegy'),
		'id'      => 'mail_secure',
		'std'     => 'ssl',
		'type'    => 'radio',
		'options' => array("ssl" => "SSL", "tls" => "TLS", "none" => esc_html__("No Encryption", "vbegy"))
	);

	$options[] = array(
		'name'  => esc_html__('SMTP Authentication', 'vbegy'),
		'id'    => 'smtp_auth',
		'std'   => 1,
		'type'  => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('Disable SSL Certificate Verification', 'vbegy'),
		'id'    => 'disable_ssl',
		'type'  => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__("Add your email to receive the different mails", "vbegy"),
		'id'   => 'email_template_to',
		'std'  => get_bloginfo("admin_email"),
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Mail footer enable or disable', 'vbegy'),
		'id'   => 'active_footer_email',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'active_footer_email:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Social for the mail in the footer enable or disable', 'vbegy'),
		'id'   => 'social_footer_email',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__("Add your copyrights for your mail templates", "vbegy"),
		'id'   => 'copyrights_for_email',
		'std'  => '&copy; ' . date('Y') . ' Lumeno. All Rights Reserved<br>With Love by <a href="https://intself.com/" target="_blank">2code</a>.',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at the all templates</h4>
		<p>[%blogname%] - The site title.</p>
		<p>[%site_url%] - The site URL.</p>
		<p>[%messages_url%] - The messages URL page.</p>',
	);

	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at Reset password, Confirm email and Approve user</h4>
		<p>[%user_login%] - The user login name.</p>
		<p>[%user_name%] - The user name.</p>
		<p>[%user_nicename%] - The user nice name.</p>
		<p>[%display_name%] - The user display name.</p>
		<p>[%user_email%] - The user email.</p>
		<p>[%user_profile%] - The user profile URL.</p>
		<p>[%display_name_sender%] - The user display name.</p>
		<p>[%user_profile_sender%] - The user profile URL.</p>',
	);

	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variable work at Reset password and Confirm email</h4>
		<p>[%confirm_link_email%] - Confirm email for the user to reset the password at reset password template and at the confirm email template is confirm email to active the user.</p>',
	);

	$options[] = array(
		'name'     => esc_html__('Reset password title', 'vbegy'),
		'id'       => 'title_new_password',
		'std'      => "Reset your password",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Reset password template', 'vbegy'),
		'id'       => 'email_new_password',
		'std'      => "<p>Someone requested that the password be reset for the following account:</p><p>Username: [%display_name%].' ([%user_login%])</p><p>If this was a mistake, just ignore this email and nothing will happen.</p><p>To reset your password, visit the following address:</p><p><a href='[%confirm_link_email%]'>Click here to reset your password</a></p><p>If the link above does not work, Please use your browser to go to:</p><p>[%confirm_link_email%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variable work at this template only</h4>
		<p>[%reset_password%] - The user password.</p>',
	);

	$options[] = array(
		'name'     => esc_html__('Reset password 2 title', 'vbegy'),
		'id'       => 'title_new_password_2',
		'std'      => "Reset your password",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Reset password 2 template', 'vbegy'),
		'id'       => 'email_new_password_2',
		'std'      => "<p>You are : [%display_name%] ([%user_login%])</p><p>The New Password : [%reset_password%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'send_welcome_mail:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('New welcome mail title', 'vbegy'),
		'id'   => 'title_welcome_mail',
		'std'  => 'Welcome',
		'type' => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('New welcome mail template', 'vbegy'),
		'id'       => 'email_welcome_mail',
		'std'      => "<p>Hi [%display_name%]</p><p>Welcome to our site.</p><p>[%blogname%]</p><p><a href='[%site_url%]'>[%site_url%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'confirm_email:not(0),confirm_edit_email:not(0)',
		'operator'  => 'or',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'confirm_email:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Confirm mail title', 'vbegy'),
		'id'   => 'title_confirm_link',
		'std'  => "Confirm account",
		'type' => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Confirm mail template', 'vbegy'),
		'id'       => 'email_confirm_link',
		'std'      => "<p>Hi there</p><p>Your registration has been successful! To confirm your account, kindly click on 'Activate' below.</p><p><a href='[%confirm_link_email%]'>Activate</a></p><p>If the link above does not work, Please use your browser to go to:</p><p>[%confirm_link_email%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'confirm_edit_email:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Confirm mail title', 'vbegy'),
		'id'   => 'title_confirm_edit_email_link',
		'std'  => "Confirm account",
		'type' => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Confirm mail template', 'vbegy'),
		'id'       => 'edit_email_confirm_link',
		'std'      => "<p>Hi there</p><p>You edited your email! To confirm your email, kindly click on 'Activate' below.</p><p><a href='[%confirm_link_email%]'>Activate</a></p><p>If the link above does not work, Please use your browser to go to:</p><p>[%confirm_link_email%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name' => esc_html__('Edited email title', 'vbegy'),
		'id'   => 'title_edited_email_link',
		'std'  => "Edited email",
		'type' => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Edited email template', 'vbegy'),
		'id'       => 'edited_email_link',
		'std'      => "<p>Hi there</p><p>You edited your email successfully!</p><p><a href='[%site_url%]'>[%blogname%]</a></p><p>[%site_url%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Confirm mail 2 title', 'vbegy'),
		'id'   => 'title_confirm_link_2',
		'std'  => "Confirm account",
		'type' => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Confirm mail 2 template', 'vbegy'),
		'id'       => 'email_confirm_link_2',
		'std'      => "<p>Hi there</p><p>This is the link to activate your membership</p><p><a href='[%confirm_link_email%]'>Activate</a></p><p>If the link above does not work, Please use your browser to go to:</p><p>[%confirm_link_email%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name'     => esc_html__('Approve user title', 'vbegy'),
		'id'       => 'title_approve_user',
		'std'      => "Confirm account",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Approve user template', 'vbegy'),
		'id'       => 'email_approve_user',
		'std'      => "<p>Hi there</p><p>We just approved your member.</p><p><a href='[%site_url%]'>[%blogname%]</a></p><p>[%site_url%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type'      => 'heading-2',
		'condition' => 'report_users:not(0)',
		'div'       => 'div'
	);

	$options[] = array(
		'name' => esc_html__('Report user title', 'vbegy'),
		'id'   => 'title_report_user',
		'std'  => "Report User",
		'type' => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Report user template', 'vbegy'),
		'id'       => 'email_report_user',
		'std'      => "<p>Hi there</p><p>Abuse has been reported on the use of the following user</p><p><a href='[%user_profile_sender%]'>[%display_name_sender%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variable work at this template only</h4>
		<p>[%messages_title%] - Show the message title.</p>',
	);

	$options[] = array(
		'name'     => esc_html__('Send message title', 'vbegy'),
		'id'       => 'title_new_message',
		'std'      => "New message",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Send message template', 'vbegy'),
		'id'       => 'email_new_message',
		'std'      => "<p>Hi there</p><p>There is a new message</p><p><a href='[%messages_url%]'>[%messages_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at this template only</h4>
		<p>[%item_name%] - Show the item name.</p>
		<p>[%item_price%] - Show the item price.</p>
		<p>[%item_currency%] - Show the item currency.</p>
		<p>[%payer_email%] - Show the payer email.</p>
		<p>[%first_name%] - Show the payer first name.</p>
		<p>[%last_name%] - Show the payer last name.</p>
		<p>[%item_transaction%] - Show the transaction id.</p>
		<p>[%date%] - Show the payment date.</p>
		<p>[%time%] - Show the payment time.</p>',
	);

	$options[] = array(
		'name'     => esc_html__('New payment title', 'vbegy'),
		'id'       => 'title_new_payment',
		'std'      => "Instant Payment Notification - Received Payment",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('New payment template', 'vbegy'),
		'id'       => 'email_new_payment',
		'std'      => "<p>An instant payment notification was successfully recieved</p><p>With [%item_price%] [%item_currency%]</p><p>From [%payer_email%] [%first_name%] - [%last_name%] on [%date%] at [%time%]</p><p>The item transaction id [%item_transaction%]</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at all next 7 templates</h4>
		<p>[%post_title%] - Show the post title.</p>
		<p>[%post_link%] - Show the post link.</p>
		<p>[%the_author_post%] - Show the post author.</p>',
	);

	$options[] = array(
		'type'    => 'content',
		'content' => '<h4>Variables work at Report answer, Notified answer and Follow question</h4>
		<p>[%answer_link%] - Show the answer link.</p>
		<p>[%the_name%] - Show the answer author.</p>',
	);

	$options[] = array(
		'name'     => esc_html__('Report question title', 'vbegy'),
		'id'       => 'title_report_question',
		'std'      => "Question report",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Report question template', 'vbegy'),
		'id'       => 'email_report_question',
		'std'      => "<p>Hi there</p><p>Abuse has been reported on the use of the following question</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name'     => esc_html__('Report answer title', 'vbegy'),
		'id'       => 'title_report_answer',
		'std'      => "Answer report",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Report answer template', 'vbegy'),
		'id'       => 'email_report_answer',
		'std'      => "<p>Hi there</p><p>Abuse has been reported on the use of the following comment</p><p><a href='[%answer_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name'     => esc_html__('Notified answer title', 'vbegy'),
		'id'       => 'title_notified_answer',
		'std'      => "Answer to your question",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Notified answer template', 'vbegy'),
		'id'       => 'email_notified_answer',
		'std'      => "<p>Hi there</p><p>We would tell you [%the_author_post%] That the new post was added on a common theme by [%the_name%] Entitled [%the_name%] [%post_title%]</p><p>Click ON the link below to go to the topic</p><p><a href='[%answer_link%]'>[%post_title%]</a></p><p>There may be more of Posts and we hope the answer to encourage members and get them to help.</p><p>Accept from us Sincerely</p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name'     => esc_html__('Notified reply on the answer title', 'vbegy'),
		'id'       => 'title_notified_reply',
		'std'      => "Reply to your answer",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Notified reply on the answer template', 'vbegy'),
		'id'       => 'email_notified_reply',
		'std'      => "<p>Hi there</p><p>There is a new reply to your following answer</p><p><a href='[%answer_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name'     => esc_html__('Follow question title', 'vbegy'),
		'id'       => 'title_follow_question',
		'std'      => "Hi there",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('Follow question template', 'vbegy'),
		'id'       => 'email_follow_question',
		'std'      => "<p>Hi there</p><p>There is a new answer to your following question</p><p><a href='[%answer_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name'     => esc_html__('New questions title', 'vbegy'),
		'id'       => 'title_new_questions',
		'std'      => "New question",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('New questions template', 'vbegy'),
		'id'       => 'email_new_questions',
		'std'      => "<p>Hi there</p><p>There is a new question</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name'     => esc_html__('New posts title', 'vbegy'),
		'id'       => 'title_new_posts',
		'std'      => "New post",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('New posts template', 'vbegy'),
		'id'       => 'email_new_posts',
		'std'      => "<p>Hi there</p><p>There is a new post</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name'     => esc_html__('New questions for review title', 'vbegy'),
		'id'       => 'title_new_draft_questions',
		'std'      => "New question for review",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('New questions for review template', 'vbegy'),
		'id'       => 'email_draft_questions',
		'std'      => "<p>Hi there</p><p>There is a new question for the review</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'name'     => esc_html__('New posts for review title', 'vbegy'),
		'id'       => 'title_new_draft_posts',
		'std'      => "New post for review",
		'type'     => 'text'
	);

	$options[] = array(
		'name'     => esc_html__('New posts for review template', 'vbegy'),
		'id'       => 'email_draft_posts',
		'std'      => "<p>Hi there</p><p>There is a new post for the review</p><p><a href='[%post_link%]'>[%post_title%]</a></p>",
		'type'     => 'editor',
		'settings' => $wp_editor_settings
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Footer settings', 'vbegy'),
		'icon' => 'tagcloud',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__("Footer skin", 'vbegy'),
		'desc' => esc_html__("Choose the footer skin.", 'vbegy'),
		'id' => "footer_skin",
		'std' => "footer_dark",
		'type' => "images",
		'options' => array(
			'footer_dark' => $imagepath . 'footer_dark.jpg',
			'footer_light' => $imagepath . 'footer_light.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__("Footer Layout", 'vbegy'),
		'desc' => esc_html__("Footer columns Layout.", 'vbegy'),
		'id' => "footer_layout",
		'std' => "footer_4c",
		'type' => "images",
		'options' => array(
			'footer_1c' => $imagepath . 'footer_1c.jpg',
			'footer_2c' => $imagepath . '2c.jpg',
			'footer_3c' => $imagepath . 'footer_3c.jpg',
			'footer_4c' => $imagepath . 'footer_4c.jpg',
			'footer_5c' => $imagepath . 'footer_5c.jpg',
			'footer_no' => $imagepath . 'footer_no.jpg'
		)
	);

	$options[] = array(
		'name' => esc_html__('Copyrights', 'vbegy'),
		'desc' => esc_html__('Put the copyrights of footer.', 'vbegy'),
		'id' => 'footer_copyrights',
		'std' => 'Copyright ' . date('Y') . ' Lumeno | <a href=https://intself.com/>By 2code</a>',
		'type' => 'textarea'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__("Advanced", 'vbegy'),
		'id'   => "advanced",
		'icon' => 'upload',
		'type' => 'heading'
	);

	$options[] = array(
		'type' => 'heading-2',
	);

	$options[] = array(
		'name' => esc_html__('Ajax file load from admin or theme', 'vbegy'),
		'desc' => esc_html__('choose ajax file load from admin or theme.', 'vbegy'),
		'id' => 'ajax_file',
		'std' => 'admin',
		'type' => 'select',
		'options' => array("admin" => "Admin", "theme" => "Theme")
	);

	$options[] = array(
		'name' => esc_html__('Google API (Get it from here : https://developers.google.com/+/api/oauth)', 'vbegy'),
		'desc' => esc_html__('Type here the Google API.', 'vbegy'),
		'id' => 'google_api',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Twitter consumer key', 'vbegy'),
		'id' => 'twitter_consumer_key',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Twitter consumer secret', 'vbegy'),
		'id' => 'twitter_consumer_secret',
		'std' => '',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the views at your site?', 'vbegy'),
		'id'   => 'active_post_stats',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'active_post_stats:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Post meta stats field.', 'vbegy'),
		'desc' => esc_html__('Change this if you have used a post views plugin before.', 'vbegy'),
		'id'   => 'post_meta_stats',
		'std'  => 'post_stats',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate cache for views at your site?', 'vbegy'),
		'id'   => 'cache_post_stats',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Activate the visits at the site work by cookie', 'vbegy'),
		'desc' => esc_html__('Select ON if you want to active the cookie for the visits at the site.', 'vbegy'),
		'id'   => 'visit_cookie',
		'type' => 'checkbox'
	);

	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('User meta avatar field.', 'vbegy'),
		'desc' => esc_html__('Change this if you have used a user avatar or social plugin before.', 'vbegy'),
		'id'   => 'user_meta_avatar',
		'std'  => 'you_avatar',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Click ON to create all theme pages (28 pages)', 'vbegy'),
		'id' => 'theme_pages',
		'std' => 0,
		'type' => 'checkbox'
	);

	$options[] = array(
		'id'   => 'uniqid_cookie',
		'std'  => askme_token(15),
		'type' => 'hidden'
	);

	$options[] = array(
		'name' => esc_html__("If you wont to export setting please refresh the page before that", 'vbegy'),
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'   => esc_html__("Export Setting", 'vbegy'),
		'desc'   => esc_html__("Copy this to saved file", 'vbegy'),
		'id'     => 'export_setting',
		'export' => $current_options_e,
		'type'   => 'export'
	);

	$options[] = array(
		'name'  => '<a href="' . add_query_arg(array('page' => 'options', 'backup' => 'settings'), admin_url('admin.php')) . '" class="button button-primary backup-settings">' . esc_html__('Backup your settings', 'vbegy') . '</a>',
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__("Import Setting", 'vbegy'),
		'desc' => esc_html__("Put here the import setting", 'vbegy'),
		'id'   => 'import_setting',
		'type' => 'import'
	);

	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	return $options;
}
