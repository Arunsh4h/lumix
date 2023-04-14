<?php $support_activate = askme_updater()->is_active();
if ($support_activate) {
	/* Remove demo meta */
	add_action('save_post','askme_remove_demo_meta');
	function askme_remove_demo_meta($post_id) {
		delete_post_meta($post_id,'theme_import_demo');
	}
	/* Check if the One Click Demo Import plugin is active */
	if (class_exists('OCDI_Plugin')) {
		add_action('admin_init','askme_disable_ocdi_plugin');
	}
	function askme_disable_ocdi_plugin() {
		if (wp_doing_ajax()) {
			return;
		}
		deactivate_plugins(plugin_basename('one-click-demo-import/one-click-demo-import.php'));
	}
	/* Branding */
	add_filter('pt-ocdi/disable_pt_branding','__return_true');
	/* Demo page setting */
	add_filter('pt-ocdi/plugin_page_setup','askme_plugin_page_setup');
	function askme_plugin_page_setup($default_settings) {
		$default_settings['parent_slug'] = 'options';
		$default_settings['page_title']  = esc_html__('Demo Import','vbegy');
		$default_settings['menu_title']  = esc_html__('Demo Import','vbegy');
		$default_settings['capability']  = 'manage_options';
		$default_settings['menu_slug']   = 'demo-import';
		return $default_settings;
	}
	/* Confirm dialog */
	add_filter('pt-ocdi/confirmation_dialog_options','my_theme_ocdi_confirmation_dialog_options',10,1);
	function my_theme_ocdi_confirmation_dialog_options($options) {
		return array_merge($options,array(
			'width'       => 600,
			'dialogClass' => 'framework-demo-dialog',
			'resizable'   => false,
			'height'      => 600,
			'modal'       => false,
		));
	}
	/* Demo files */
	add_filter('pt-ocdi/import_files','askme_import_files');
	function askme_import_files() {
		$demos = get_transient('askme_import_demos');
		if (isset($demos) && is_array($demos) && !empty($demos)) {
			return $demos;
		}
		if (array_key_exists('page',$_GET) && $_GET['page'] == 'demo-import') {
			$file_path = "https://intself.com/demos.php?demo=ask-me";
			if ($file_path != "") {
				$response = wp_remote_get($file_path,20);
				$values = (is_array($response) && isset($response["body"])?$response["body"]:"");
				$demos = json_decode($values,true);
				set_transient('askme_import_demos', $demos, 60*60*24);
			}
		}
		$demos = (isset($demos) && is_array($demos) && !empty($demos)?$demos:array());
		return $demos;
	}
	/* Before import the demo */
	add_action("pt-ocdi/before_widgets_import","askme_before_widgets_import");
	function askme_before_widgets_import($selected_import) {
		//wp_set_sidebars_widgets(get_option("old_sidebar_widgets"));
		$sidebar_widgets = wp_get_sidebars_widgets();
		update_option("old_sidebar_widgets",$sidebar_widgets);
		update_option("sidebars_widgets",'');
	}
	/* After import the demo */
	add_action('pt-ocdi/after_import','askme_after_import_setup');
	function askme_after_import_setup($selected_import) {
		// Demo name
		update_option("demo_import_name",$selected_import['import_file_name']);

		// Old options
		update_option("old_import_demo_options",get_option("vpanel_ask_me"));

		// Old menus
		update_option("old_nav_menu_locations",get_nav_menu_locations());

		// Update options
		$file_path = $selected_import['import_options_file_url'];
		if ($file_path != "") {
			$response = wp_remote_get($file_path,20);
			$values = (isset($response["body"])?$response["body"]:"");
			if ($values != "") {
				$admin_email = get_bloginfo("admin_email");
				$parse = parse_url(get_site_url());
				$data = base64_decode($values);
				$data = json_decode($data,true);
				$array_options = array("vpanel_ask_me","sidebars","coupons","roles");
				foreach ($array_options as $option) {
					if (isset($data[$option])) {
						if (isset($data[$option]["top_bar_groups"])) {
							$data[$option]["top_bar_groups"] = array('editor' => 'on','author' => 'on','contributor' => 'on','subscriber' => 'on','activation' => 'on');
						}
						if (isset($data[$option]["redirect_groups"])) {
							$data[$option]["redirect_groups"] = array('editor' => 'on','author' => 'on','contributor' => 'on','subscriber' => 'on','activation' => 'on');
						}
						if (isset($data[$option]["send_email_question_groups"])) {
							$data[$option]["send_email_question_groups"] = array('administrator' => 'on');
						}
						if (isset($data[$option]["send_email_question_groups_both"])) {
							$data[$option]["send_email_question_groups_both"] = array('administrator' => 'on');
						}
						if (isset($data[$option]["send_notification_question_groups"])) {
							$data[$option]["send_notification_question_groups"] = array('administrator' => 'on');
						}
						if (isset($data[$option]["send_email_post_groups"])) {
							$data[$option]["send_email_post_groups"] = array('administrator' => 'on');
						}
						if (isset($data[$option]["send_email_post_groups_both"])) {
							$data[$option]["send_email_post_groups_both"] = array('administrator' => 'on');
						}
						if (isset($data[$option]["send_notification_post_groups"])) {
							$data[$option]["send_notification_post_groups"] = array('administrator' => 'on');
						}
						if (isset($data[$option]["add_question_default"])) {
							$data[$option]["add_question_default"] = array('notified' => 'on');
						}
						if (isset($data[$option]["questions_meta"])) {
							$data[$option]["questions_meta"] = array('status' => 'on','category' => 'on','user_name' => 'on','date' => 'on','answer_meta' => 'on','view' => 'on','question_bump' => 'on');
						}
						if (isset($data[$option]["questions_meta_single"])) {
							$data[$option]["questions_meta_single"] = array('status' => 'on','category' => 'on','user_name' => 'on','date' => 'on','answer_meta' => 'on','view' => 'on');
						}
						if (isset($data[$option]["payment_group"])) {
							$data[$option]["payment_group"] = array('administrator' => 'on','editor' => 'on','author' => 'on','contributor' => 'on','subscriber' => 'on','activation' => 'on');
						}
						if (isset($data[$option]["user_links"])) {
							$data[$option]["user_links"] = array('profile' => 'on','messages' => 'on','questions' => 'on','polls' => 'on','asked_questions' => 'on','paid_questions' => 'on','answers' => 'on','best_answers' => 'on','favorite' => 'on','followed' => 'on','points' => 'on','i_follow' => 'on','blocking' => 'on','followers' => 'on','posts' => 'on','comments' => 'on','follow_questions' => 'on','follow_answers' => 'on','follow_posts' => 'on','follow_comments' => 'on','edit_profile' => 'on','activity_log' => 'on','logout' => 'on');
						}
						if (isset($data[$option]["user_meta_admin"])) {
							$data[$option]["user_meta_admin"] = array('points' => 'on','phone' => 'on','country' => 'on','age' => 'on','registration' => 'on');
						}
						if (isset($data[$option]["add_question_default_user"])) {
							$data[$option]["add_question_default_user"] = array('notified' => 'on');
						}
						$data[$option]["paypal_email"] = $admin_email;
						$data[$option]["paypal_email_sandbox"] = $admin_email;
						$data[$option]["email_template_to"] = $admin_email;
						$data[$option]["email_template"] = "no_reply@".$parse['host'];
						$data[$option]["black_list_emails"] = array();
						$data[$option]["application_splash_screen"]["id"] = $data[$option]["application_splash_screen"]["url"] = $data[$option]["app_issuer_id"] = $data[$option]["app_key_id"] = $data[$option]["authkey_content"] = $data[$option]["app_bundle_id"] = $data[$option]["application_icon"]["id"] = $data[$option]["application_icon"]["url"] = $data[$option]["app_name"] = $data[$option]["facebook_app_id"] = $data[$option]["soundcloud_client_id"] = $data[$option]["behance_api_key"] = $data[$option]["google_api"] = $data[$option]["instagram_sessionid"] = $data[$option]["dribbble_client_id"] = $data[$option]["dribbble_client_secret"] = $data[$option]["dribbble_access_token"] = $data[$option]["twitter_consumer_key"] = $data[$option]["twitter_consumer_secret"] = $data[$option]["envato_token"] = "";
						$data[$option]["user_meta_avatar"] = "your_avatar";
						update_option($option,$data[$option]);
					}else {
						delete_option($option);
					}
				}
				update_option("FlushRewriteRules",true);
			}
		}

		// Assign menus to their locations.
		$main_menu_1 = get_term_by('name','Header menu','nav_menu');
		$main_menu_2 = get_term_by('name','Top bar','nav_menu');
		$main_menu_3 = get_term_by('name','Top bar - login','nav_menu');

		set_theme_mod('nav_menu_locations',array('header_menu' => $main_menu_1->term_id,'top_bar' => $main_menu_2->term_id,'top_bar_login' => $main_menu_3->term_id));

		$array_menu = wp_get_nav_menu_items($main_menu_2->term_id);
		$own_url = 'https://intself.com/demo/themes/ask-me/';
		$own_url_2 = 'https://intself.com/demo/themes/ask-me/rtl/';
		$own_url_3 = 'https://intself.com/demo/themes/ask-me/shop/';
		if (is_array($array_menu) && !empty($array_menu)) {
			foreach ($array_menu as $key => $value) {
				if (strpos($value->url,$own_url) !== false || strpos($value->url,$own_url_2) !== false || strpos($value->url,$own_url_3) !== false) {
					update_post_meta($value->ID,'_menu_item_url',str_ireplace(array($own_url,$own_url_2,$own_url_3),esc_url(home_url('/')),$value->url));
				}
			}
		}

		$array_menu = wp_get_nav_menu_items($main_menu_1->term_id);
		if (is_array($array_menu) && !empty($array_menu)) {
			foreach ($array_menu as $key => $value) {
				if (strpos($value->url,$own_url) !== false || strpos($value->url,$own_url_2) !== false || strpos($value->url,$own_url_3) !== false) {
					update_post_meta($value->ID,'_menu_item_url',str_ireplace(array($own_url,$own_url_2,$own_url_3),esc_url(home_url('/')),$value->url));
				}
			}
		}

		// Assign front page and posts page (blog page).
		if ($selected_import["import_file_name"] == "Shop demo") {
			$front_page_id = get_page_by_title('Shop Full Width');
		}else {
			$front_page_id = get_page_by_title((is_rtl()?'الرئيسية 1':'Home 1'));
		}
		update_option('show_on_front','page');
		update_option('page_on_front',$front_page_id->ID);

		// Delete default wordpress data
		$hello_post_id = get_page_by_title('Hello world!',OBJECT,'post');
		$hello_post_id_ar = get_page_by_title('أهلاً بالعالم !',OBJECT,'post');

		// remove hello world post
		if (isset($hello_post_id->ID)) {
			wp_delete_post($hello_post_id->ID,true);
		}

		// remove hello world post
		if (isset($hello_post_id_ar->ID)) {
			wp_delete_post($hello_post_id_ar->ID,true);
		}

		$sample_page_id = get_page_by_title('Sample Page',OBJECT,'page');
		$sample_page_id_ar = get_page_by_title('مثال على صفحة',OBJECT,'page');

		// remove sample page
		if (isset( $sample_page_id->ID)) {
			wp_delete_post($sample_page_id->ID,true);
		}

		// remove sample page
		if (isset($sample_page_id_ar->ID)) {
			wp_delete_post($sample_page_id_ar->ID,true);
		}

		$sticky_post_id = get_page_by_path('is-this-statement-i-see-him-last-night-can-be-understood-as-i-saw-him-last-night',OBJECT,ask_questions_type);
		if (isset($sticky_post_id->ID)) {
			$post_id = $sticky_post_id->ID;
			update_post_meta($post_id,"sticky",1);
			$sticky_posts = get_option('sticky_posts');
			if (is_array($sticky_posts)) {
				if (!in_array($post_id,$sticky_posts)) {
					$array_merge = array_merge($sticky_posts,array($post_id));
					update_option("sticky_posts",$array_merge);
				}
			}else {
				update_option("sticky_posts",array($post_id));
			}
			$sticky_questions = get_option('sticky_questions');
			if (is_array($sticky_questions)) {
				if (!in_array($post_id,$sticky_questions)) {
					$array_merge = array_merge($sticky_questions,array($post_id));
					update_option("sticky_questions",$array_merge);
				}
			}else {
				update_option("sticky_questions",array($post_id));
			}
		}
	}
	/* Header in the demo page */
	add_action("pt-ocdi/plugin_page_header","askme_plugin_page_header");
	function askme_plugin_page_header() {
		echo '<div id="framework-registration-wrap" class="framework-demos-container"><div class="framework-dash-container framework-dash-container-medium"><div class="postbox"><h2><span class="dashicons dashicons-yes library-icon-key"></span><span>'.esc_html__('Choose the demo which you want to import','vbegy').'</span></h2><div class="inside"><div class="main">';
	}
	/* Footer in the demo page */
	add_action("pt-ocdi/plugin_page_footer","askme_plugin_page_footer");
	function askme_plugin_page_footer() {
		echo '</div></div></div></div></div>';
	}
	/* Title in the demo page */
	add_filter('pt-ocdi/plugin_intro_text','askme_plugin_page_title');
	add_filter("pt-ocdi/plugin_page_title","askme_plugin_page_title");
	function askme_plugin_page_title() {
		echo '';
	}
}
