<?php /* Mobile options */
function askme_other_plugin() {
	if (is_admin() && !function_exists('mobile_api_options') && !function_exists('mobile_options')) {
		add_filter("askme_options_after_general_setting","askme_mobile_setting_options");
	}
}
add_action('init','askme_other_plugin');
function askme_mobile_setting_options($options) {
	$directory_uri = get_template_directory_uri();
	$imagepath_theme =  $directory_uri.'/images/';

	$more_info = '<a href="https://2code.info/mobile-apps/" target="_blank">'.esc_html__('For more information and buying the mobile APP','vbegy').'</a>';

	// Pull all the pages into an array
	$not_template_pages = array();
	$args = array('post_type' => 'page','nopaging' => true,"meta_query" => array('relation' => 'OR',array("key" => "_wp_page_template","compare" => "NOT EXISTS"),array("key" => "_wp_page_template","compare" => "=","value" => ''),array("key" => "_wp_page_template","compare" => "=","value" => 'default')));
	$not_template_pages[''] = 'Select a page:';
	$the_query = new WP_Query($args);
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$page_post = $the_query->post;
			$not_template_pages[$page_post->ID] = $page_post->post_title;
		}
	}
	wp_reset_postdata();

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// Pull all the roles into an array
	global $wp_roles;
	$new_roles = array();
	foreach ($wp_roles->roles as $key => $value) {
		$new_roles[$key] = $value['name'];
	}

	$array_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"question_vote"   => "question_vote",
		"tags"            => "tags",
		"answer_button"   => "answer_button",
		"answers_count"   => "answers_count",
		"views_count"     => "views_count",
		"followers_count" => "followers_count",
		"favourite"       => "favourite",
	);

	$array_options = array(
		"category"        => esc_html__('Category','vbegy'),
		"date"            => esc_html__('Date','vbegy'),
		"author_image"    => esc_html__('Author Image','vbegy'),
		"author"          => esc_html__('Author','vbegy'),
		"question_vote"   => esc_html__('Question vote','vbegy'),
		"poll"            => esc_html__('Poll','vbegy'),
		"tags"            => esc_html__('Tags','vbegy'),
		"answer_button"   => esc_html__('Answer button','vbegy'),
		"answers_count"   => esc_html__('Answers count','vbegy'),
		"views_count"     => esc_html__('Views count','vbegy'),
		"followers_count" => esc_html__('Followers count','vbegy'),
		"favourite"       => esc_html__('Favourite','vbegy'),
	);

	$array_single_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"question_vote"   => "question_vote",
		"tags"            => "tags",
		"answer_button"   => "answer_button",
		"answers_count"   => "answers_count",
		"views_count"     => "views_count",
		"followers_count" => "followers_count",
		"favourite"       => "favourite",
		"share"           => "share",
	);

	$array_single_options = array(
		"category"        => esc_html__('Category','vbegy'),
		"date"            => esc_html__('Date','vbegy'),
		"author_image"    => esc_html__('Author Image','vbegy'),
		"author"          => esc_html__('Author','vbegy'),
		"question_vote"   => esc_html__('Question vote','vbegy'),
		"tags"            => esc_html__('Tags','vbegy'),
		"answer_button"   => esc_html__('Answer button','vbegy'),
		"answers_count"   => esc_html__('Answers count','vbegy'),
		"views_count"     => esc_html__('Views count','vbegy'),
		"followers_count" => esc_html__('Followers count','vbegy'),
		"favourite"       => esc_html__('Favourite','vbegy'),
		"share"           => esc_html__('Share','vbegy'),
	);

	$array_post_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"tags"            => "tags",
		"comment_button"  => "comment_button",
		"comments_count"  => "comments_count",
		"views_count"     => "views_count",
	);

	$array_post_options = array(
		"category"        => esc_html__('Category','vbegy'),
		"date"            => esc_html__('Date','vbegy'),
		"author_image"    => esc_html__('Author Image','vbegy'),
		"author"          => esc_html__('Author','vbegy'),
		"tags"            => esc_html__('Tags','vbegy'),
		"comment_button"  => esc_html__('Comment button','vbegy'),
		"comments_count"  => esc_html__('Comments count','vbegy'),
		"views_count"     => esc_html__('Views count','vbegy'),
	);

	$array_single_post_std = array(
		"category"        => "category",
		"date"            => "date",
		"author_image"    => "author_image",
		"author"          => "author",
		"tags"            => "tags",
		"comment_button"  => "comment_button",
		"comments_count"  => "comments_count",
		"views_count"     => "views_count",
		"share"           => "share",
	);

	$array_single_post_options = array(
		"category"        => esc_html__('Category','vbegy'),
		"date"            => esc_html__('Date','vbegy'),
		"author_image"    => esc_html__('Author Image','vbegy'),
		"author"          => esc_html__('Author','vbegy'),
		"tags"            => esc_html__('Tags','vbegy'),
		"comment_button"  => esc_html__('Comment button','vbegy'),
		"comments_count"  => esc_html__('Comments count','vbegy'),
		"views_count"     => esc_html__('Views count','vbegy'),
		"share"           => esc_html__('Share','vbegy'),
	);

	$mobile_applications = array(
		"request_app"         => esc_html__('Request my APP','vbegy'),
		"general_mobile"      => esc_html__('General settings','vbegy'),
		"guide_pages"         => esc_html__('Guide pages','vbegy'),
		"setting_page"        => esc_html__('Setting page','vbegy'),
		"header_mobile"       => esc_html__('Mobile header','vbegy'),
		"bottom_bar"          => esc_html__('Bottom bar','vbegy'),
		"side_navbar"         => esc_html__('Side navbar','vbegy'),
		"mobile_question"     => esc_html__('Ask questions','vbegy'),
		"ads_mobile"          => esc_html__('Advertising','vbegy'),
		"app_notifications"   => esc_html__('Notifications','vbegy'),
		"captcha_mobile"      => esc_html__('Captcha settings','vbegy'),
		"home_mobile"         => esc_html__('Home settings','vbegy'),
		"categories_mobile"   => esc_html__('Categories settings','vbegy'),
		"search_mobile"       => esc_html__('Search settings','vbegy'),
		"favourites_mobile"   => esc_html__('Favourites settings','vbegy'),
		"followed_questions"  => esc_html__('Followed Questions','vbegy'),
		"questions_mobile"    => esc_html__('Questions page settings','vbegy'),
		"users_mobile"        => esc_html__('Users settings','vbegy'),
		"comments_mobile"     => esc_html__('Comments and answers','vbegy'),
		"blog_mobile"         => esc_html__('Blog settings','vbegy'),
		"single_mobile"       => esc_html__('Single question settings','vbegy'),
		"single_post_mobile"  => esc_html__('Single post settings','vbegy'),
		"styling_mobile"      => esc_html__('Mobile styling','vbegy'),
		"lang_mobile"         => esc_html__('Language settings','vbegy'),
		"mobile_icons"        => esc_html__('Icons settings','vbegy'),
		"mobile_construction" => esc_html__('Under construction','vbegy')
	);

	$array_comment_std = array(
		"author_image" => "author_image",
		"author"       => "author",
	);

	$array_comment_options = array(
		"author_image" => esc_html__('Author Image','vbegy'),
		"author"       => esc_html__('Author','vbegy'),
	);

	$mobile_applications = apply_filters("mobile_api_applications_options",$mobile_applications);

	$options[] = array(
		'name'    => esc_html__('Mobile APP','vbegy'),
		'id'      => 'mobile_applications',
		'type'    => 'heading',
		'icon'    => 'phone',
		'new'     => true,
		'std'     => 'request_app',
		'options' => $mobile_applications
	);
	
	$options[] = array(
		'name' => esc_html__('Request my APP','vbegy'),
		'id'   => 'request_app',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'  => esc_html__('All the options on this page, if you do not buy the app, will not work.','vbegy'),
		'type'  => 'info',
		'alert' => 'alert-message-warning'
	);

	$options[] = array(
		'name' => esc_html__('Activate a custom URL for your site different than the main URL','vbegy'),
		'desc' => esc_html__('Something like with www or without it, or with https or with http','vbegy'),
		'id'   => 'activate_custom_baseurl',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Type your custom URL for your site different than the main URL','vbegy'),
		'id'        => 'custom_baseurl',
		'std'       => esc_url(home_url('/')),
		'condition' => 'activate_custom_baseurl:not(0)',
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('App Name','vbegy'),
		'desc' => esc_html__("Your app's name shown on Play Store and App Store","vbegy"),
		'id'   => 'app_name',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Upload the application icon and it must be (1024*1024px), PNG and NOT transparent','vbegy'),
		'id'   => 'application_icon',
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('App bundle id','vbegy'),
		'desc' => esc_html__("It must be small letters (from 'a' to 'z'), like info.2code.app","vbegy"),
		'id'   => 'app_bundle_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('App IOS bundle id','vbegy'),
		'desc' => esc_html__("It must be small letters (from 'a' to 'z'), like info.2code.app","vbegy"),
		'id'   => 'app_ios_bundle_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Application splash screen background color (hex code, ex: #FFFFFF)','vbegy'),
		'id'   => 'splash_screen_background',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Upload the application splash screen and it must be (512*512px), PNG and NOT transparent','vbegy'),
		'id'   => 'application_splash_screen',
		'type' => 'upload',
	);

	$options[] = array(
		'name'  => '<a href="https://2code.info/docs/mobile/apple-ios-app/" target="_blank">'.esc_html__('You can get the Issuer ID, KEY ID, Password of APP-SPECIFIC PASSWORDS and AuthKey file from here and these are required if you need the IOS version.','vbegy').'</a>',
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Issuer ID *','vbegy'),
		'id'   => 'app_issuer_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Key ID *','vbegy'),
		'id'   => 'app_key_id',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Add the AuthKey file content, this file for the IOS app *','vbegy'),
		'id'   => 'authkey_content',
		'type' => 'textarea',
	);

	$options[] = array(
		'name'  => esc_html__('Small notifications icon for android.','vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the small notifications icon for android','vbegy'),
		'id'   => 'android_notification',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'android_notification:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('The color of the small notifications icon for android (hex code, ex: #FFFFFF)','vbegy'),
		'id'   => 'android_notification_color',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('The icon of the small notifications icon for android and it must be (20*20px), PNG and transparent','vbegy'),
		'id'   => 'android_notification_icon',
		'type' => 'upload',
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
		'name' => esc_html__('General settings','vbegy'),
		'id'   => 'general_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the icons to use it in the app from: %s','vbegy'),'<a href="https://2code.info/mobile/icons/" target="_blank">'.esc_html__('here','vbegy').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the force update','vbegy'),
		'desc' => esc_html__('The force update to allow the users must update the app to continue using it','vbegy'),
		'id'   => 'force_update',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'force_update:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Last Android version','vbegy'),
		'id'   => 'android_version',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Last IOS version','vbegy'),
		'id'   => 'ios_version',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('The app language','vbegy'),
		'id'   => 'app_lang',
		'type' => 'text',
		'std'  => 'en',
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the circle button to ask a question or add a post','vbegy'),
		'id'   => 'addaction_mobile',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$addaction_mobile_action = apply_filters("mobile_api_addaction_button",array("question" => esc_html__("Ask a question","vbegy"),"post" => esc_html__("Add a post","vbegy")));

	$options[] = array(
		'name'      => esc_html__('Choose the circle button to ask a question or add a post','vbegy'),
		'id'        => 'addaction_mobile_action',
		'std'       => 'question',
		'options'   => $addaction_mobile_action,
		'condition' => 'addaction_mobile:not(0)',
		'type'      => 'radio'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to activate the follow questions on the app','vbegy'),
		'id'   => 'mobile_setting_follow_questions',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to hide the dislike on the app','vbegy'),
		'id'   => 'mobile_setting_dislike',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Select ON to make the app for the logged users only.','vbegy'),
		'id'   => 'mobile_logged_only',
		'type' => 'checkbox'
	);
	
	$options[] = array(
		'name'  => esc_html__('The next two options to make the changes for your app live, and the other users will see the changes after making refresh or reopen the app.','vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the roles you need to show for them the live change for the app","vbegy"),
		'id'      => 'mobile_live_change_groups',
		'type'    => 'multicheck',
		'options' => $new_roles,
		'std'     => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor'),
	);

	$options[] = array(
		'name' => esc_html__('Add more specific user ids to show the live change','vbegy'),
		'id'   => 'mobile_live_change_specific_users',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable to show the parent categories with child category','vbegy'),
		'desc' => esc_html__('Show the parent categories with child category, in following categories page, ask question form, and categories page','vbegy'),
		'id'   => 'mobile_parent_categories',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Write the number of categories which show in the categories page or add 0 to show all of them','vbegy'),
		'id'   => 'mobile_categories_page',
		'std'  => 0,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Guide pages','vbegy'),
		'id'   => 'guide_pages',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Enable or disable the guide pages','vbegy'),
		'id'   => 'onboardmodels_mobile',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'onboardmodels_mobile:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for first guide page','vbegy'),
		'id'   => 'onboardmodels_img_1_mobile',
		'std'  => $imagepath_theme."1.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for first guide page','vbegy'),
		'id'   => 'onboardmodels_title_1_mobile',
		'std'  => "Welcome",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for first guide page','vbegy'),
		'id'   => 'onboardmodels_subtitle_1_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for second guide page','vbegy'),
		'id'   => 'onboardmodels_img_2_mobile',
		'std'  => $imagepath_theme."2.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for second guide page','vbegy'),
		'id'   => 'onboardmodels_title_2_mobile',
		'std'  => "You are here",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for second guide page','vbegy'),
		'id'   => 'onboardmodels_subtitle_2_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Upload the image for third guide page','vbegy'),
		'id'   => 'onboardmodels_img_3_mobile',
		'std'  => $imagepath_theme."3.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Add the title for third guide page','vbegy'),
		'id'   => 'onboardmodels_title_3_mobile',
		'std'  => "Continue to Ask Me",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the sub title for third guide page','vbegy'),
		'id'   => 'onboardmodels_subtitle_3_mobile',
		'std'  => "Lorem Ipsum is simply dummy text of the printing and typesetting industry",
		'type' => 'text',
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
		'name' => esc_html__('Setting page','vbegy'),
		'id'   => 'setting_page',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the text size','vbegy'),
		'id'   => 'text_size_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the rate app','vbegy'),
		'id'   => 'rate_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the edit profile page','vbegy'),
		'id'   => 'edit_profile_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the notifications page','vbegy'),
		'id'   => 'notifications_page_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the users to stop the notifications or not on the app','vbegy'),
		'id'   => 'activate_stop_notification',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options = apply_filters("mobile_api_options_in_settings_page",$options);

	$options[] = array(
		'name' => esc_html__('Enable or disable the about us page','vbegy'),
		'id'   => 'about_us_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the about us page','vbegy'),
		'id'        => 'about_us_page_app',
		'type'      => 'select',
		'condition' => 'about_us_app:not(0)',
		'options'   => $not_template_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the privacy policy page','vbegy'),
		'id'   => 'privacy_policy_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('You must choose the privacy page.','vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the privacy policy page','vbegy'),
		'id'        => 'privacy_policy_page_app',
		'type'      => 'select',
		'condition' => 'privacy_policy_app:not(0)',
		'options'   => $not_template_pages
	);

	$options[] = array(
		'name'  => esc_html__('You must choose the terms page.','vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Choose the terms and conditions page','vbegy'),
		'id'      => 'terms_page_app',
		'type'    => 'select',
		'options' => $not_template_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the FAQs page','vbegy'),
		'id'   => 'faqs_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Choose the FAQs page','vbegy'),
		'id'        => 'faqs_page_app',
		'type'      => 'select',
		'condition' => 'faqs_app:not(0)',
		'options'   => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the contact us page','vbegy'),
		'id'   => 'contact_us_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the share app','vbegy'),
		'id'   => 'share_app',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Share title','vbegy'),
		'id'   => 'share_title',
		'std'  => "Ask Me",
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Share image','vbegy'),
		'id'   => 'share_image',
		'std'  => $directory_uri."/screenshot.png",
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Share android URL','vbegy'),
		'id'   => 'share_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Share IOS URL','vbegy'),
		'id'   => 'share_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Mobile header','vbegy'),
		'id'   => 'header_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Logo position','vbegy'),
		'id'      => 'mobile_logo_position',
		'std'     => 'start',
		'type'    => 'radio',
		'options' => array("start" => esc_html__("Left","vbegy"),"center" => esc_html__("Center","vbegy"))
	);

	$options[] = array(
		'name' => esc_html__('Upload the logo','vbegy'),
		'id'   => 'mobile_logo',
		'std'  => $imagepath_theme."logo-light-2x.png",
		'type' => 'upload',
	);
	
	$options[] = array(
		'name' => esc_html__('Upload the dark logo','vbegy'),
		'id'   => 'mobile_logo_dark',
		'std'  => $imagepath_theme."logo-colored.png",
		'type' => 'upload',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options = apply_filters("mobile_api_after_header_settings",$options);

	$options[] = array(
		'name' => esc_html__('Bottom bar','vbegy'),
		'id'   => 'bottom_bar',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Enable or disable the bottom bar','vbegy'),
		'id'   => 'bottom_bar_activate',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'bottom_bar_activate:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => esc_html__('You must choose 4 items only to show in the bottom bar.','vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$main_pages = array(
		"home"            => esc_html__('Home','vbegy'),
		"ask"             => esc_html__('Ask Question','vbegy'),
		"categories"      => esc_html__('Question Categories','vbegy'),
		"favorite"        => esc_html__('Favorite','vbegy'),
		"followed"        => esc_html__('Followed Questions','vbegy'),
		"settings"        => esc_html__('Settings','vbegy'),
		"questions"       => esc_html__('Questions','vbegy'),
		"blog"            => esc_html__('Blog','vbegy'),
		"users"           => esc_html__('Users','vbegy'),
		"post_categories" => esc_html__('Post Categories','vbegy'),
		"search"          => esc_html__('Search','vbegy'),
		"contact_us"      => esc_html__('Contact Us','vbegy'),
		"post"            => esc_html__('Add Post','vbegy'),
		"points"          => esc_html__('Badges and points','vbegy'),
		"answers"         => esc_html__('Answers','vbegy'),
		"comments"        => esc_html__('Comments','vbegy'),
		"notifications"   => esc_html__('Notifications','vbegy'),
	);
	$main_pages = apply_filters("mobile_api_options_main_pages",$main_pages);

	$bottom_bar_elements = array(
		array(
			"type"    => "radio",
			"id"      => "type",
			"name"    => esc_html__('Type','vbegy'),
			'options' => array(
				'main'       => esc_html__('Main page','vbegy'),
				'q_category' => esc_html__('Question category','vbegy'),
				'p_category' => esc_html__('Post category','vbegy'),
				'page'       => esc_html__('Page','vbegy'),
				'webview'    => esc_html__('Webview page','vbegy'),
			),
			'std'     => 'main',
		),
		array(
			"type"      => "select",
			"id"        => "main",
			"name"      => esc_html__('Main pages','vbegy'),
			'options'   => $main_pages,
			"condition" => "[%id%]type:is(main)",
			'std'       => 'home',
		),
		array(
			"type"      => "select",
			"id"        => "feed",
			"name"      => esc_html__('Feed page','vbegy'),
			'options'   => $options_pages,
			"condition" => "[%id%]type:is(main),[%id%]main:is(feed)",
			'std'       => 'home',
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','vbegy'),
			"id"          => "q_category",
			"taxonomy"    => ask_question_category,
			"name"        => esc_html__('Question category','vbegy'),
			"condition"   => "[%id%]type:is(q_category)",
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','vbegy'),
			"id"          => "p_category",
			"taxonomy"    => "category",
			"name"        => esc_html__('Post category','vbegy'),
			"condition"   => "[%id%]type:is(p_category)",
		),
		array(
			"type"      => "select",
			"id"        => "page",
			"options"   => $not_template_pages,
			"name"      => esc_html__('Page','vbegy'),
			"condition" => "[%id%]type:is(page)",
		),
		array(
			"type"      => "select",
			"id"        => "webview",
			"options"   => $options_pages,
			"name"      => esc_html__('Webview Page','vbegy'),
			"condition" => "[%id%]type:is(webview)",
		),
		array(
			"type"      => "text",
			"id"        => "link",
			"name"      => esc_html__('Or add your custom link','vbegy'),
			"condition" => "[%id%]type:is(webview)"
		),
		array(
			"type" => "text",
			"id"   => "title",
			"name" => esc_html__('New title','vbegy')
		),
		array(
			"type" => "text",
			"id"   => "icon",
			"name" => esc_html__('Icon','vbegy')
		),
	);

	$old_bottom_bar = array();
	$mobile_bottom_bar = askme_options("mobile_bottom_bar");
	if (!is_array($mobile_bottom_bar) || (is_array($mobile_bottom_bar) && empty($mobile_bottom_bar))) {
		$mobile_bottom_bar = array(
			"home" => "home",
			"categories" => "categories",
			"favorite" => "favorite",
			"settings" => "settings",
		);
	}
	if (is_array($mobile_bottom_bar) && !empty($mobile_bottom_bar)) {
		foreach ($mobile_bottom_bar as $key => $value) {
			if ($value != "" && $value == $key) {
				if ($key == "ask") {
					$icon = "0xe826";
				}else if ($key == "home") {
					$icon = "0xe800";
				}else if ($key == "categories") {
					$icon = "0xe801";
				}else if ($key == "favorite") {
					$icon = "0xe803";
				}else if ($key == "settings") {
					$icon = "0xe804";
				}else if ($key == "blog") {
					$icon = "0xedcb";
				}else if ($key == "post") {
					$icon = "0xeb90";
				}else if ($key == "points") {
					$icon = "0xe827";
				}
				$old_bottom_bar[] = array(
					"type" => "main",
					"main" => $key,
					"icon" => $icon
				);
			}
		}
	}
	
	$options[] = array(
		'id'      => "add_bottom_bars",
		'type'    => "elements",
		'button'  => esc_html__('Add a new link','vbegy'),
		'hide'    => "yes",
		'std'     => $old_bottom_bar,
		'options' => $bottom_bar_elements,
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
		'name' => esc_html__('Side navbar','vbegy'),
		'id'   => 'side_navbar',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the side navbar','vbegy'),
		'id'   => 'side_navbar_activate',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'side_navbar_activate:not(0)',
		'type'      => 'heading-2'
	);

	$sidenav_elements = array(
		array(
			"type"    => "radio",
			"id"      => "type",
			"name"    => esc_html__('Type','vbegy'),
			'options' => array(
				'main'       => esc_html__('Main page','vbegy'),
				'q_category' => esc_html__('Question category','vbegy'),
				'p_category' => esc_html__('Post category','vbegy'),
				'page'       => esc_html__('Page','vbegy'),
				'webview'    => esc_html__('Webview page','vbegy'),
			),
			'std'     => 'main',
		),
		array(
			"type"      => "select",
			"id"        => "main",
			"name"      => esc_html__('Main pages','vbegy'),
			'options'   => $main_pages,
			"condition" => "[%id%]type:is(main)",
			'std'       => 'home',
		),
		array(
			"type"      => "select",
			"id"        => "feed",
			"name"      => esc_html__('Feed page','vbegy'),
			'options'   => $options_pages,
			"condition" => "[%id%]type:is(main),[%id%]main:is(feed)",
			'std'       => 'home',
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','vbegy'),
			"id"          => "q_category",
			"taxonomy"    => ask_question_category,
			"name"        => esc_html__('Question category','vbegy'),
			"condition"   => "[%id%]type:is(q_category)",
		),
		array(
			"type"        => "select_category",
			'option_none' => esc_html__('Select a Category','vbegy'),
			"id"          => "p_category",
			"taxonomy"    => "category",
			"name"        => esc_html__('Post category','vbegy'),
			"condition"   => "[%id%]type:is(p_category)",
		),
		array(
			"type"      => "select",
			"id"        => "page",
			"options"   => $not_template_pages,
			"name"      => esc_html__('Page','vbegy'),
			"condition" => "[%id%]type:is(page)",
		),
		array(
			"type"      => "select",
			"id"        => "webview",
			"options"   => $options_pages,
			"name"      => esc_html__('Webview Page','vbegy'),
			"condition" => "[%id%]type:is(webview)",
		),
		array(
			"type"      => "text",
			"id"        => "link",
			"name"      => esc_html__('Or add your custom link','vbegy'),
			"condition" => "[%id%]type:is(webview)"
		),
		array(
			"type" => "text",
			"id"   => "title",
			"name" => esc_html__('New title','vbegy')
		),
		array(
			"type" => "text",
			"id"   => "icon",
			"name" => esc_html__('Icon','vbegy')
		),
	);

	$old_side_nav = array();
	$mobile_side_navbar = askme_options("mobile_side_navbar");
	if (!is_array($mobile_side_navbar) || (is_array($mobile_side_navbar) && empty($mobile_side_navbar))) {
		$mobile_side_navbar = array(
			"home"       => array("sort" => esc_html__('Home','vbegy'),"value" => "home"),
			"ask"        => array("sort" => esc_html__('Ask Question','vbegy'),"value" => "ask"),
			"categories" => array("sort" => esc_html__('Categories','vbegy'),"value" => "categories"),
			"favorite"   => array("sort" => esc_html__('Favorite','vbegy'),"value" => "favorite"),
			"settings"   => array("sort" => esc_html__('Settings','vbegy'),"value" => "settings"),
			"blog"       => array("sort" => esc_html__('Blog','vbegy'),"value" => "blog"),
			"post"       => array("sort" => esc_html__('Add Post','vbegy'),"value" => "post"),
			"points"     => array("sort" => esc_html__('Badges and points','vbegy'),"value" => "points"),
		);
	}
	if (is_array($mobile_side_navbar) && !empty($mobile_side_navbar)) {
		foreach ($mobile_side_navbar as $key => $value) {
			if (isset($value["value"]) && $value["value"] != "" && $value["value"] == $key) {
				if ($key == "ask") {
					$icon = "0xe826";
				}else if ($key == "home") {
					$icon = "0xe800";
				}else if ($key == "categories") {
					$icon = "0xe801";
				}else if ($key == "favorite") {
					$icon = "0xe803";
				}else if ($key == "settings") {
					$icon = "0xe804";
				}else if ($key == "blog") {
					$icon = "0xedcb";
				}else if ($key == "post") {
					$icon = "0xeb90";
				}else if ($key == "points") {
					$icon = "0xe827";
				}
				$old_side_nav[] = array(
					"type" => "main",
					"main" => $key,
					"icon" => $icon
				);
			}
		}
	}
	
	$options[] = array(
		'id'      => "add_sidenavs",
		'type'    => "elements",
		'button'  => esc_html__('Add a new link','vbegy'),
		'hide'    => "yes",
		'std'     => $old_side_nav,
		'options' => $sidenav_elements,
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
		'name' => esc_html__('Ask questions','vbegy'),
		'id'   => 'mobile_question',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Write the number of categories which show in the ask question form or add 0 to show all of them','vbegy'),
		'id'   => 'mobile_question_categories',
		'std'  => 0,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Advertising','vbegy'),
		'id'   => 'ads_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Acivate the advertising','vbegy'),
		'id'   => 'mobile_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id','vbegy'),
		'id'   => 'ad_mob_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id','vbegy'),
		'id'   => 'ad_mob_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'group',
		'end'  => 'end'
	);

	$options[] = array(
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Interstitial adv','vbegy')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile interstitial adv','vbegy'),
		'id'   => 'mobile_interstitial_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_interstitial_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the interstitial','vbegy'),
		'id'   => 'ad_interstitial_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the interstitial','vbegy'),
		'id'   => 'ad_interstitial_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		"name" => esc_html__('Choose how many time will open the ad, you can leave it 0 to open the ad each time opened the questions and posts','vbegy'),
		"id"   => "ad_interstitial_count",
		"type" => "sliderui",
		'std'  => 0,
		"step" => "1",
		"min"  => "0",
		"max"  => "10"
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'group',
		'end'  => 'end'
	);

	$options[] = array(
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Rewarded adv','vbegy')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile rewarded adv','vbegy'),
		'id'   => 'mobile_rewarded_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_rewarded_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the rewarded','vbegy'),
		'id'   => 'ad_rewarded_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the rewarded','vbegy'),
		'id'   => 'ad_rewarded_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		"name" => esc_html__('Choose how many time will open the ad, you can leave it 0 to open the ad each time opened the questions and posts','vbegy'),
		"id"   => "ad_rewarded_count",
		"type" => "sliderui",
		'std'  => 0,
		"step" => "1",
		"min"  => "0",
		"max"  => "10"
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'type' => 'group',
		'end'  => 'end'
	);

	$options[] = array(
		'type'      => 'group',
		'id'        => 'ads_mobile',
		'condition' => 'mobile_adv:not(0)',
		'name'      => esc_html__('Banner adv','vbegy')
	);

	$options[] = array(
		'name' => esc_html__('Activate the mobile banner adv','vbegy'),
		'id'   => 'mobile_banner_adv',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'mobile_banner_adv:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob Android id for the banner','vbegy'),
		'id'   => 'ad_banner_android',
		'type' => 'text',
	);

	$options[] = array(
		'name' => esc_html__('Add the adMob IOS id for the banner','vbegy'),
		'id'   => 'ad_banner_ios',
		'type' => 'text',
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$mobile_api_options = get_option(askme_options);
	$banner_top = (isset($mobile_api_options["banner_top"])?$mobile_api_options["banner_top"]:"");
	$banner_bottom = (isset($mobile_api_options["banner_bottom"])?$mobile_api_options["banner_bottom"]:"");
	$banner_after_post = (isset($mobile_api_options["banner_after_post"])?$mobile_api_options["banner_after_post"]:"");
	$banner_webview = (isset($mobile_api_options["banner_webview"])?$mobile_api_options["banner_webview"]:"");

	$options[] = array(
		'name'      => esc_html__('Select where do you need to activate the ads','vbegy'),
		'id'        => 'mobile_ads',
		'condition' => 'mobile_banner_adv:not(0)',
		'type'      => 'multicheck',
		'std'       => array(
			"top"            => ($banner_top == 1?"top":""),
			"bottom"         => ($banner_bottom == 1?"bottom":""),
			"post_top"       => "post_top",
			"post_bottom"    => "post_bottom",
			"after_post"     => ($banner_after_post == 1?"after_post":""),
			"banner_webview" => ($banner_webview == 1?"banner_webview":""),
		),
		'options' => array(
			"top"             => esc_html__('Banner ad in the top','vbegy'),
			"bottom"          => esc_html__('Banner ad in the bottom','vbegy'),
			"before_home"     => esc_html__('Banner ad before home','vbegy'),
			"after_home"      => esc_html__('Banner ad after home','vbegy'),
			"post_top"        => esc_html__('Banner ad on the post or question in the top','vbegy'),
			"post_bottom"     => esc_html__('Banner ad on the post or question in the bottom','vbegy'),
			"before_comments" => esc_html__('Banner ad before the comments or answers on the posts or questions','vbegy'),
			"after_post"      => esc_html__('Banner ad after the post or question','vbegy'),
			"banner_posts"    => esc_html__('Banner ad after each x number of posts and questions','vbegy'),
			"banner_comments" => esc_html__('Banner ad after each x number of comments and answers','vbegy'),
			"banner_webview"  => esc_html__('Banner ad on the webview page','vbegy'),
		)
	);

	$array_ads = array(
		"top" => array("title" => esc_html__('Banner ad in the top','vbegy'),"key" => "top","value" => esc_html__('Activate custom HTML or custom image for the top ad','vbegy')),
		"bottom" => array("title" => esc_html__('Banner ad in the bottom','vbegy'),"key" => "bottom","value" => esc_html__('Activate custom HTML or custom image for the bottom ad','vbegy')),
		"before_home" => array("title" => esc_html__('Banner ad before home','vbegy'),"key" => "before_home","value" => esc_html__('Activate custom HTML or custom image for the before home ad','vbegy')),
		"after_home" => array("title" => esc_html__('Banner ad after home','vbegy'),"key" => "after_home","value" => esc_html__('Activate custom HTML or custom image for the after home ad','vbegy')),
		"post_top" => array("title" => esc_html__('Banner ad on the post or question in the top','vbegy'),"key" => "post_top","value" => esc_html__('Activate custom HTML or custom image on the post or question in the top','vbegy')),
		"post_bottom" => array("title" => esc_html__('Banner ad on the post or question in the bottom','vbegy'),"key" => "post_bottom","value" => esc_html__('Activate custom HTML or custom image on the post or question in the bottom','vbegy')),
		"before_comments" => array("title" => esc_html__('Banner ad before the comments or answers on the posts or questions','vbegy'),"key" => "before_comments","value" => esc_html__('Activate custom HTML or custom image on before the comments or answers on the posts or questions','vbegy')),
		"after_post" => array("title" => esc_html__('Banner ad after the post or question','vbegy'),"key" => "after_post","value" => esc_html__('Activate custom HTML or custom image on after the post or question','vbegy')),
		"posts" => array("title" => esc_html__('Banner ad after each x number of posts and questions','vbegy'),"key" => "banner_posts","value" => esc_html__('Activate custom HTML or custom image for the posts ad','vbegy'),"position" => esc_html__('Display after x posts and questions','vbegy')),
		"comments" => array("title" => esc_html__('Banner ad after each x number of comments and answers','vbegy'),"key" => "banner_comments","value" => esc_html__('Activate custom HTML or custom image for the comments ad','vbegy'),"position" => esc_html__('Display after x comments and answers','vbegy')),
		"banner_webview" => array("title" => esc_html__('Banner ad on the webview page','vbegy'),"key" => "banner_webview","value" => esc_html__('Activate custom HTML or custom image the webview page','vbegy')),
	);

	if (is_array($array_ads) && !empty($array_ads)) {
		$options[] = array(
			'type' => 'group',
			'end'  => 'end'
		);

		foreach ($array_ads as $key => $value) {
			$options[] = array(
				'type'      => 'group',
				'id'        => 'ads_mobile',
				'condition' => 'mobile_adv:not(0),mobile_banner_adv:not(0),mobile_ads:has('.$value["key"].')',
				'name'      => $value["title"]
			);

			if (isset($value["position"])) {
				$options[] = array(
					'name' => $value["position"],
					'id'   => 'mobile_ad_'.$key.'_position',
					'std'  => '2',
					'type' => 'text'
				);
			}

			$options[] = array(
				'name' => $value["value"],
				'id'   => 'mobile_ad_html_'.$key.'',
				'type' => 'checkbox'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => 'mobile_ad_html_'.$key.':not(0)',
				'type'      => 'heading-2'
			);

			$options[] = array(
				'name'    => esc_html__('Advertising type','vbegy'),
				'id'      => 'mobile_ad_html_'.$key.'_type',
				'std'     => 'custom_image',
				'type'    => 'radio',
				'options' => array("display_code" => esc_html__("Display code","vbegy"),"custom_image" => esc_html__("Custom Image","vbegy"))
			);
			
			$options[] = array(
				'name'      => esc_html__('Image URL','vbegy'),
				'desc'      => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','vbegy'),
				'id'        => 'mobile_ad_html_'.$key.'_img',
				'condition' => 'mobile_ad_html_'.$key.'_type:is(custom_image)',
				'type'      => 'upload'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising URL','vbegy'),
				'id'        => 'mobile_ad_html_'.$key.'_href',
				'std'       => '#',
				'condition' => 'mobile_ad_html_'.$key.'_type:is(custom_image)',
				'type'      => 'text'
			);
			
			$options[] = array(
				'name'      => esc_html__('Advertising Code html','vbegy'),
				'id'        => 'mobile_ad_html_'.$key.'_code',
				'condition' => 'mobile_ad_html_'.$key.'_type:not(custom_image)',
				'type'      => 'textarea'
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'div'  => 'div',
				'end'  => 'end'
			);

			$options[] = array(
				'type' => 'group',
				'end'  => 'end'
			);
		}
		$options[] = array(
			'type' => 'html',
			'html'  => '<div><div>'
		);
	}
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Captcha settings','vbegy'),
		'id'   => 'captcha_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable reCaptcha','vbegy'),
		'id'   => 'activate_captcha_mobile',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_captcha_mobile:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Select where do you need to activate the captcha','vbegy'),
		'id'      => 'captcha_positions',
		'type'    => 'multicheck_3',
		'std'     => array(
			"login"    => "login",
			"register" => "register",
		),
		'options' => array(
			"login"    => esc_html__('Sign in','vbegy'),
			"register" => esc_html__('Sign up','vbegy'),
			"answer"   => esc_html__('Add a new answer','vbegy'),
			"question" => esc_html__('Ask a new question','vbegy'),
		)
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the reCaptcha v2 site and secret keys from: %s','vbegy'),'<a href="https://www.google.com/recaptcha/admin/" target="_blank">'.esc_html__('here','vbegy').'</a> > <a href="https://ahmed.d.pr/DUAKq5" target="_blank">'.esc_html__('like that','vbegy').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('Add this in the domain option: %s','vbegy'),'recaptcha-flutter-plugin.firebaseapp.com'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);
	
	$options[] = array(
		'name' => esc_html__('Site key reCaptcha','vbegy'),
		'id'   => 'site_key_recaptcha_mobile',
		'type' => 'text',
	);
	
	$options[] = array(
		'name' => esc_html__('Secret key reCaptcha','vbegy'),
		'id'   => 'secret_key_recaptcha_mobile',
		'type' => 'text',
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
		'name' => esc_html__('Notifications','vbegy'),
		'id'   => 'app_notifications',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the tabs on the notifications page.","vbegy"),
		'id'      => 'mobile_notifications_tabs',
		'type'    => 'multicheck',
		'std'     => array(
			"unread" => "unread",
			"all"    => "all",
		),
		'options' => array(
			"unread" => esc_html__('Unread','vbegy'),
			"all"    => esc_html__('All','vbegy'),
		)
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable push notifications','vbegy'),
		'id'   => 'push_notifications',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'push_notifications:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'  => '<a href="https://2code.info/docs/mobile/push-notifications-key/" target="_blank">'.esc_html__('You can get the key from here.','vbegy').'</a>',
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Add the app key','vbegy'),
		'id'   => 'app_key',
		'type' => 'text',
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
		'name' => esc_html__('Home settings','vbegy'),
		'id'   => 'home_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$pages = get_pages(array('meta_key' => '_wp_page_template','meta_value' => 'template-home.php'));
	
	$options[] = array(
		'name'    => esc_html__('Choose the home page','vbegy'),
		'id'      => 'home_page_app',
		'type'    => 'select',
		'std'     => (isset($pages) && isset($pages[0]) && isset($pages[0]->ID)?$pages[0]->ID:''),
		'options' => $options_pages
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the homepage','vbegy'),
		'id'   => 'count_posts_home',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the home options for questions','vbegy'),
		'id'      => 'mobile_setting_home',
		'type'    => 'multicheck_3',
		'std'     => $array_std,
		'options' => $array_options
	);

	$options[] = array(
		'name'    => esc_html__('Select the home options for posts','vbegy'),
		'id'      => 'mobile_setting_home_posts',
		'type'    => 'multicheck_3',
		'std'     => $array_post_std,
		'options' => $array_post_options
	);

	$options[] = array(
		'name'      => esc_html__('Activate the ad in the first tab in the top','vbegy'),
		'id'        => 'ads_mobile_top',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Activate the ad in the first tab in the bottom','vbegy'),
		'id'        => 'ads_mobile_bottom',
		'condition' => 'mobile_adv:not(0)',
		'type'      => 'checkbox'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Categories settings','vbegy'),
		'id'   => 'categories_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the categories','vbegy'),
		'id'   => 'count_posts_categories',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the categories options for questions','vbegy'),
		'id'      => 'mobile_setting_categories',
		'type'    => 'multicheck_3',
		'std'     => $array_std,
		'options' => $array_options
	);

	$options[] = array(
		'name'    => esc_html__('Select the categories for posts','vbegy'),
		'id'      => 'mobile_setting_categories_posts',
		'type'    => 'multicheck_3',
		'std'     => $array_post_std,
		'options' => $array_post_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Search settings','vbegy'),
		'id'   => 'search_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options = apply_filters("mobile_api_before_search_question_settings",$options);

	$options[] = array(
		'name' => esc_html__('Items per page in the search','vbegy'),
		'id'   => 'count_posts_search',
		'std'  => "3",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the search options for questions','vbegy'),
		'id'      => 'mobile_setting_search',
		'type'    => 'multicheck_3',
		'std'     => $array_std,
		'options' => $array_options
	);

	$options = apply_filters("mobile_api_after_search_question_settings",$options);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Favourites settings','vbegy'),
		'id'   => 'favourites_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the favourite page','vbegy'),
		'id'   => 'count_posts_favourites',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the favourite page','vbegy'),
		'id'      => 'mobile_setting_favourites',
		'type'    => 'multicheck_3',
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Followed Questions','vbegy'),
		'id'   => 'followed_questions',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the followed page','vbegy'),
		'id'   => 'count_posts_followed',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the followed page','vbegy'),
		'id'      => 'mobile_setting_followed',
		'type'    => 'multicheck_3',
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Questions page settings','vbegy'),
		'id'   => 'questions_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the questions page','vbegy'),
		'id'   => 'count_posts_questions',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the blog page','vbegy'),
		'id'      => 'mobile_setting_questions',
		'type'    => 'multicheck_3',
		'std'     => $array_std,
		'options' => $array_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Users settings','vbegy'),
		'id'   => 'users_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the timeframe to allow the user stay login before logout automatically.","vbegy"),
		'id'      => 'mobile_time_login',
		'type'    => 'radio',
		'options' => array(
			'hours'  => esc_html__('Hours','vbegy'),
			'days' => esc_html__('Days','vbegy'),
		),
		'std'     => "days",
	);
	
	$options[] = array(
		"name"      => esc_html__('Choose the hours to stay login','vbegy'),
		"id"        => "mobile_time_login_hours",
		"type"      => "sliderui",
		'condition' => 'mobile_time_login:is(hours)',
		'std'       => 1,
		"step"      => "1",
		"min"       => "0",
		"max"       => "100"
	);
	
	$options[] = array(
		"name"      => esc_html__('Choose the days to stay login','vbegy'),
		"id"        => "mobile_time_login_days",
		"type"      => "sliderui",
		'condition' => 'mobile_time_login:is(days)',
		'std'       => 7,
		"step"      => "1",
		"min"       => "0",
		"max"       => "1000"
	);

	$options[] = array(
		'name' => esc_html__('Do you want to add a custom link of the signup button on the app?','vbegy'),
		'id'   => 'activate_custom_register_link',
		'type' => 'checkbox',
	);
	
	$options[] = array(
		'name'      => esc_html__('Add the custom link','vbegy'),
		'desc'      => esc_html__('Type the custom link of the register button from here.','vbegy'),
		'id'        => 'custom_register_link',
		'condition' => 'activate_custom_register_link:not(0)',
		'type'      => 'text'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the style of the social icons style on the user profile page.","vbegy"),
		'id'      => 'mobile_social_icon_style',
		'type'    => 'radio',
		'options' => array(
			'icons' => esc_html__('Icons','vbegy'),
			'links' => esc_html__('Links','vbegy'),
		),
		'std'     => "icons",
	);

	$options[] = array(
		'name' => esc_html__('Write the number of users which show in the following steps in the register and edit profile pages.','vbegy'),
		'id'   => 'mobile_api_following_pages',
		'std'  => 6,
		'type' => 'text'
	);

	$options[] = array(
		'name'    => esc_html__("Choose the roles you need to show for the users in the users page.","vbegy"),
		'id'      => 'mobile_users_roles_page',
		'type'    => 'multicheck',
		'options' => $new_roles,
		'std'     => array('administrator' => 'administrator','editor' => 'editor','contributor' => 'contributor','subscriber' => 'subscriber','author' => 'author'),
	);

	$options[] = array(
		'name' => esc_html__('Write the number of users which show in the users page.','vbegy'),
		'id'   => 'mobile_users_page',
		'std'  => 6,
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Comments settings','vbegy'),
		'id'   => 'comments_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the comments or answers page','vbegy'),
		'id'   => 'count_comments_mobile',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the comments for the blog posts','vbegy'),
		'id'      => 'mobile_setting_comments',
		'type'    => 'multicheck_3',
		'std'     => $array_comment_std,
		'options' => $array_comment_options
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the vote on answers page','vbegy'),
		'id'   => 'vote_answer_mobile',
		'std'  => 1,
		'type' => 'checkbox',
	);

	$options[] = array(
		'name'    => esc_html__('Answer sort','vbegy'),
		'id'      => 'mobile_answers_sort',
		'std'     => 'voted',
		'type'    => 'radio',
		'options' => array("voted" => esc_html__("Voted","vbegy"),"oldest" => esc_html__("Oldest","vbegy"),"recent" => esc_html__("Recent","vbegy"))
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the answers for the questions','vbegy'),
		'id'      => 'mobile_setting_answers',
		'type'    => 'multicheck_3',
		'std'     => $array_comment_std,
		'options' => $array_comment_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Blog settings','vbegy'),
		'id'   => 'blog_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Items per page in the blog page','vbegy'),
		'id'   => 'count_posts_blog',
		'std'  => "6",
		'type' => 'text',
	);

	$options[] = array(
		'name'    => esc_html__('Select the setting of the blog page','vbegy'),
		'id'      => 'mobile_setting_blog',
		'type'    => 'multicheck_3',
		'std'     => $array_post_std,
		'options' => $array_post_options
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Single question settings','vbegy'),
		'id'   => 'single_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Tye menu style of report, delete and close for questions or report and delete for answers','vbegy'),
		'id'      => 'menu_style_of_report',
		'std'     => 'menu',
		'options' => array(
			'menu'  => 'Menu style',
			'icons' => 'With icons',
		),
		'type'    => 'radio'
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the single question page','vbegy'),
		'id'      => 'mobile_setting_single',
		'type'    => 'multicheck_3',
		'std'     => $array_single_std,
		'options' => $array_single_options
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the related questions?','vbegy'),
		'id'   => 'app_related_questions',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'app_related_questions:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Related style','vbegy'),
		'desc'    => esc_html__('Type related question style from here.','vbegy'),
		'id'      => 'app_related_style_questions',
		'std'     => 'with_images',
		'options' => array(
			'with_images' => 'With images',
			'list_style'  => 'List style',
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Related questions number','vbegy'),
		'desc' => esc_html__('Type the number of related questions from here.','vbegy'),
		'id'   => 'app_related_number_questions',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Query type','vbegy'),
		'desc'    => esc_html__('Select what will the related questions show.','vbegy'),
		'id'      => 'app_query_related_questions',
		'std'     => 'categories',
		'options' => array(
			'categories' => esc_html__('Questions in the same categories','vbegy'),
			'tags'       => esc_html__('Questions in the same tags (If not find any tags will show by the same categories)','vbegy'),
			'author'     => esc_html__('Questions by the same author','vbegy'),
		),
		'type'    => 'radio'
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
		'name' => esc_html__('Single post settings','vbegy'),
		'id'   => 'single_post_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'    => esc_html__('Select the the setting of the single post page','vbegy'),
		'id'      => 'mobile_setting_single_post',
		'type'    => 'multicheck_3',
		'std'     => $array_single_post_std,
		'options' => $array_single_post_options
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the related posts?','vbegy'),
		'id'   => 'app_related_posts',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'app_related_posts:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name'    => esc_html__('Related style','vbegy'),
		'desc'    => esc_html__('Type related post style from here.','vbegy'),
		'id'      => 'app_related_style',
		'std'     => 'with_images',
		'options' => array(
			'with_images' => 'With images',
			'list_style'  => 'List style',
		),
		'type'    => 'radio'
	);
	
	$options[] = array(
		'name' => esc_html__('Related posts number','vbegy'),
		'desc' => esc_html__('Type the number of related posts from here.','vbegy'),
		'id'   => 'app_related_number',
		'std'  => 5,
		'type' => 'text'
	);
	
	$options[] = array(
		'name'    => esc_html__('Query type','vbegy'),
		'desc'    => esc_html__('Select what will the related posts show.','vbegy'),
		'id'      => 'app_query_related',
		'std'     => 'categories',
		'options' => array(
			'categories' => esc_html__('Posts in the same categories','vbegy'),
			'tags'       => esc_html__('Posts in the same tags (If not find any tags will show by the same categories)','vbegy'),
			'author'     => esc_html__('Posts by the same author','vbegy'),
		),
		'type'    => 'radio'
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
		'name' => esc_html__('Mobile styling','vbegy'),
		'id'   => 'styling_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);
	
	$options[] = array(
		'name'    => esc_html__('APP skin by default','vbegy'),
		'id'      => 'app_skin',
		'std'     => 'light',
		'type'    => 'radio',
		'options' => array("light" => esc_html__("Light","vbegy"),"dark" => esc_html__("Dark","vbegy"))
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the users to choose their skin from the settings page?','vbegy'),
		'id'   => 'activate_switch_mode',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the users to choose their skin from the header icon?','vbegy'),
		'id'   => 'activate_dark_from_header',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Do you need to activate the border bottom color only for the inputs?','vbegy'),
		'id'   => 'activate_input_border_bottom',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'  => esc_html__('Light mode settings.','vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Input background color','vbegy'),
		'id'        => 'inputsbackgroundcolor',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:is(0)',
		'std'       => '#000000'
	);

	$options[] = array(
		'name'      => esc_html__('Input border bottom color','vbegy'),
		'id'        => 'input_border_bottom_color',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:not(0)',
		'std'       => '#000000'
	);

	$options[] = array(
		'name' => esc_html__('Login, signup, and forgot password background color','vbegy'),
		'id'   => 'loginbackground',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Header Background color','vbegy'),
		'id'   => 'appbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Background color','vbegy'),
		'id'   => 'tabbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Background color','vbegy'),
		'id'   => 'bottombarbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Header Text color','vbegy'),
		'id'   => 'appbarcolor',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Tabs underline/border color','vbegy'),
		'id'   => 'tabbarindicatorcolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Tabs text color','vbegy'),
		'id'   => 'tabbartextcolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Active color','vbegy'),
		'id'   => 'tabbaractivetextcolor',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Checkboxes active color','vbegy'),
		'id'   => 'checkboxactivecolor',
		'type' => 'color',
		'std'  => '#505050'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar text color','vbegy'),
		'id'   => 'bottombarinactivecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Active color','vbegy'),
		'id'   => 'bottombaractivecolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Primary color','vbegy'),
		'id'   => 'mobile_primary',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Secondary color','vbegy'),
		'id'   => 'mobile_secondary',
		'type' => 'color',
		'std'  => '#283952'
	);

	$options[] = array(
		'name' => esc_html__('Meta color','vbegy'),
		'id'   => 'secondaryvariant',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar background','vbegy'),
		'id'   => 'mobile_background',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar color','vbegy'),
		'id'   => 'sidemenutextcolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Background','vbegy'),
		'id'   => 'scaffoldbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Button color','vbegy'),
		'id'   => 'buttontextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Divider color','vbegy'),
		'id'   => 'dividercolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Shadow color','vbegy'),
		'id'   => 'shadowcolor',
		'type' => 'color',
		'std'  => '#000000'
	);

	$options[] = array(
		'name' => esc_html__('Button background color','vbegy'),
		'id'   => 'buttonsbackgroudcolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Settings page background color','vbegy'),
		'id'   => 'settingbackgroundcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Settings page text color','vbegy'),
		'id'   => 'settingtextcolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Error background color','vbegy'),
		'id'   => 'errorcolor',
		'type' => 'color',
		'std'  => '#dd3333'
	);

	$options[] = array(
		'name' => esc_html__('Error text color','vbegy'),
		'id'   => 'errortextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Alert background color','vbegy'),
		'id'   => 'alertcolor',
		'type' => 'color',
		'std'  => '#FDEDD3'
	);

	$options[] = array(
		'name' => esc_html__('Alert text color','vbegy'),
		'id'   => 'alerttextcolor',
		'type' => 'color',
		'std'  => '#f5a623'
	);

	$options[] = array(
		'name' => esc_html__('Success background color','vbegy'),
		'id'   => 'successcolor',
		'type' => 'color',
		'std'  => '#4be1ab'
	);

	$options[] = array(
		'name' => esc_html__('Success text color','vbegy'),
		'id'   => 'successtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tooltip Menu color','vbegy'),
		'id'   => 'tooltipmenucolor',
		'type' => 'color',
		'std'  => '#FFFFFF'
	);

	$options[] = array(
		'name' => esc_html__('Highlight background color','vbegy'),
		'id'   => 'highlightcolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Highlight text color','vbegy'),
		'id'   => 'highlighttextcolor',
		'type' => 'color',
		'std'  => '#FFFFFF'
	);

	$options[] = array(
		'name' => esc_html__('Close question button background color','vbegy'),
		'id'   => 'closequestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Close question button color','vbegy'),
		'id'   => 'closequestionbuttoncolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Open question button background color','vbegy'),
		'id'   => 'openquestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#EEEEEE'
	);

	$options[] = array(
		'name' => esc_html__('Open question button color','vbegy'),
		'id'   => 'openquestionbuttoncolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Favourite color','vbegy'),
		'id'   => 'favouritecolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Un favourite color','vbegy'),
		'id'   => 'unfavouritecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Best answer color','vbegy'),
		'id'   => 'bestanswercolor',
		'type' => 'color',
		'std'  => '#26aa6c'
	);

	$options[] = array(
		'name' => esc_html__('Add best answer color','vbegy'),
		'id'   => 'addbestanswercolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Remove best answer color','vbegy'),
		'id'   => 'removebestanswercolor',
		'type' => 'color',
		'std'  => '#AA0000'
	);

	$options[] = array(
		'name' => esc_html__('Verified icon color','vbegy'),
		'id'   => 'verifiedcolor',
		'type' => 'color',
		'std'  => '#5890ff'
	);

	$options[] = array(
		'name'  => esc_html__('Dark mode settings.','vbegy'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Input background color','vbegy'),
		'id'        => 'dark_inputsbackgroundcolor',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:is(0)',
		'std'       => '#2c2c2c'
	);

	$options[] = array(
		'name'      => esc_html__('Input border bottom color','vbegy'),
		'id'        => 'dark_input_border_bottom_color',
		'type'      => 'color',
		'condition' => 'activate_input_border_bottom:not(0)',
		'std'       => '#232323'
	);

	$options[] = array(
		'name' => esc_html__('Login, signup, and forgot password background color','vbegy'),
		'id'   => 'dark_loginbackground',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Header Background color','vbegy'),
		'id'   => 'dark_appbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#252525'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Background color','vbegy'),
		'id'   => 'dark_tabbarbackgroundcolor',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Background color','vbegy'),
		'id'   => 'dark_bottombarbackgroundcolor',
		'type' => 'color',
		'std'  => '#252525'
	);

	$options[] = array(
		'name' => esc_html__('Header Text color','vbegy'),
		'id'   => 'dark_appbarcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tabs underline/border color','vbegy'),
		'id'   => 'dark_tabbarindicatorcolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Tabs text color','vbegy'),
		'id'   => 'dark_tabbartextcolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Tabs Active color','vbegy'),
		'id'   => 'dark_tabbaractivetextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Checkboxes active color','vbegy'),
		'id'   => 'dark_checkboxactivecolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar text color','vbegy'),
		'id'   => 'dark_bottombarinactivecolor',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Bottom bar Active color','vbegy'),
		'id'   => 'dark_bottombaractivecolor',
		'type' => 'color',
		'std'  => '#F0F8FF'
	);

	$options[] = array(
		'name' => esc_html__('General color','vbegy'),
		'id'   => 'dark_mobile_primary',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Primary color','vbegy'),
		'id'   => 'dark_mobile_secondary',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Meta color','vbegy'),
		'id'   => 'dark_secondaryvariant',
		'type' => 'color',
		'std'  => '#7c7c7c'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar background','vbegy'),
		'id'   => 'dark_mobile_background',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Side navbar color','vbegy'),
		'id'   => 'dark_sidemenutextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Background','vbegy'),
		'id'   => 'dark_scaffoldbackgroundcolor',
		'type' => 'color',
		'std'  => '#1a1a1a'
	);

	$options[] = array(
		'name' => esc_html__('Button color','vbegy'),
		'id'   => 'dark_buttontextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Divider color','vbegy'),
		'id'   => 'dark_dividercolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Shadow color','vbegy'),
		'id'   => 'dark_shadowcolor',
		'type' => 'color',
		'std'  => '#2F4F4F'
	);

	$options[] = array(
		'name' => esc_html__('Button background color','vbegy'),
		'id'   => 'dark_buttonsbackgroudcolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Settings page background color','vbegy'),
		'id'   => 'dark_settingbackgroundcolor',
		'type' => 'color',
		'std'  => '#232323'
	);

	$options[] = array(
		'name' => esc_html__('Settings page text color','vbegy'),
		'id'   => 'dark_settingtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Error background color','vbegy'),
		'id'   => 'dark_errorcolor',
		'type' => 'color',
		'std'  => '#dd3333'
	);

	$options[] = array(
		'name' => esc_html__('Error text color','vbegy'),
		'id'   => 'dark_errortextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Alert background color','vbegy'),
		'id'   => 'dark_alertcolor',
		'type' => 'color',
		'std'  => '#FDEDD3'
	);

	$options[] = array(
		'name' => esc_html__('Alert text color','vbegy'),
		'id'   => 'dark_alerttextcolor',
		'type' => 'color',
		'std'  => '#f5a623'
	);

	$options[] = array(
		'name' => esc_html__('Success background color','vbegy'),
		'id'   => 'dark_successcolor',
		'type' => 'color',
		'std'  => '#4be1ab'
	);

	$options[] = array(
		'name' => esc_html__('Success text color','vbegy'),
		'id'   => 'dark_successtextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Tooltip Menu color','vbegy'),
		'id'   => 'dark_tooltipmenucolor',
		'type' => 'color',
		'std'  => '#333739'
	);

	$options[] = array(
		'name' => esc_html__('Highlight background color','vbegy'),
		'id'   => 'dark_highlightcolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Highlight text color','vbegy'),
		'id'   => 'dark_highlighttextcolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Close question button background color','vbegy'),
		'id'   => 'dark_closequestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Close question button color','vbegy'),
		'id'   => 'dark_closequestionbuttoncolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Open question button background color','vbegy'),
		'id'   => 'dark_openquestionbackgroundcolor',
		'type' => 'color',
		'std'  => '#333333'
	);

	$options[] = array(
		'name' => esc_html__('Open question button color','vbegy'),
		'id'   => 'dark_openquestionbuttoncolor',
		'type' => 'color',
		'std'  => '#ffffff'
	);

	$options[] = array(
		'name' => esc_html__('Favourite color','vbegy'),
		'id'   => 'dark_favouritecolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Un favourite color','vbegy'),
		'id'   => 'dark_unfavouritecolor',
		'type' => 'color',
		'std'  => '#6D737C'
	);

	$options[] = array(
		'name' => esc_html__('Best answer color','vbegy'),
		'id'   => 'dark_bestanswercolor',
		'type' => 'color',
		'std'  => '#26aa6c'
	);

	$options[] = array(
		'name' => esc_html__('Add best answer color','vbegy'),
		'id'   => 'dark_addbestanswercolor',
		'type' => 'color',
		'std'  => '#ff7361'
	);

	$options[] = array(
		'name' => esc_html__('Remove best answer color','vbegy'),
		'id'   => 'dark_removebestanswercolor',
		'type' => 'color',
		'std'  => '#AA0000'
	);

	$options[] = array(
		'name' => esc_html__('Verified icon color','vbegy'),
		'id'   => 'dark_verifiedcolor',
		'type' => 'color',
		'std'  => '#5890ff'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Language settings','vbegy'),
		'id'   => 'lang_mobile',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options = apply_filters("mobile_api_language_options",$options);
	
	$options[] = array(
		'type' => 'heading-2',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Icons settings','vbegy'),
		'id'   => 'mobile_icons',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name'  => sprintf(esc_html__('You can get the icons to use it in the app from: %s','vbegy'),'<a href="https://2code.info/mobile/icons/" target="_blank">'.esc_html__('here','vbegy').'</a>'),
		'class' => 'home_page_display',
		'type'  => 'info'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new question icon','vbegy'),
		'id'        => 'mobile_addaction_question',
		'condition' => 'addaction_mobile_action:is(question)',
		'std'       => "0xe965",
		'type'      => 'text'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new post icon','vbegy'),
		'id'        => 'mobile_addaction_post',
		'condition' => 'addaction_mobile_action:is(post)',
		'std'       => "0xf0ca",
		'type'      => 'text'
	);

	$options[] = array(
		'name'      => esc_html__('Add a new group icon','vbegy'),
		'id'        => 'mobile_addaction_group',
		'condition' => 'addaction_mobile_action:is(group)',
		'std'       => "0xe963",
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Answers icon','vbegy'),
		'id'   => 'mobile_answers_icon',
		'std'  => "0xe907",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Best answers icon','vbegy'),
		'id'   => 'mobile_best_answers_icon',
		'std'  => "0xe906",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Delete icon','vbegy'),
		'id'   => 'mobile_delete_icon',
		'std'  => "0xf041",
		'type' => 'text'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'menu_style_of_report:is(icons)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Close question icon','vbegy'),
		'id'   => 'mobile_close_icon',
		'std'  => "0xedf1",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Open question icon','vbegy'),
		'id'   => 'mobile_open_icon',
		'std'  => "0xedf0",
		'type' => 'text'
	);
	
	$options[] = array(
		'type' => 'heading-2',
		'div'  => 'div',
		'end'  => 'end'
	);

	$options[] = array(
		'name' => esc_html__('Add favourite icon','vbegy'),
		'id'   => 'mobile_favourite_icon',
		'std'  => "0xe9cb",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Remove favourite icon','vbegy'),
		'id'   => 'mobile_unfavourite_icon',
		'std'  => "0xe931",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Views icon','vbegy'),
		'id'   => 'mobile_views_icon',
		'std'  => "fa-eye",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Do you want to change the verified icon?','vbegy'),
		'id'   => 'activate_verified_icon',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name'      => esc_html__('Verified icon','vbegy'),
		'id'        => 'mobile_verified_icon',
		'condition' => 'activate_verified_icon:not(0)',
		'std'       => "0xef82",
		'type'      => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Do you want to change the vote icons?','vbegy'),
		'id'   => 'activate_vote_icons',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_vote_icons:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Upvote icon','vbegy'),
		'id'   => 'mobile_upvote_icon',
		'std'  => "0xe825",
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Downvote icon','vbegy'),
		'id'   => 'mobile_downvote_icon',
		'std'  => "0xe824",
		'type' => 'text'
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
		'name' => esc_html__('Under construction','vbegy'),
		'id'   => 'mobile_construction',
		'type' => 'heading-2'
	);

	$options[] = array(
		'name' => $more_info,
		'class' => 'home_page_display',
		'type' => 'info'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the under construction on the mobile apps','vbegy'),
		'id'   => 'activate_mobile_construction',
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_mobile_construction:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Type your title on the construction page','vbegy'),
		'id'   => 'construction_title',
		'std'  => 'CLOSED!',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Type your content on the construction page','vbegy'),
		'id'   => 'construction_content',
		'std'  => 'This app is coming soon',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Upload the image on the construction page','vbegy'),
		'id'   => 'construction_image',
		'type' => 'upload',
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the icon on the construction page','vbegy'),
		'id'   => 'activate_construction_icon',
		'type' => 'checkbox'
	);

	$options[] = array(
		'name' => esc_html__('Enable or disable the button on the construction page','vbegy'),
		'id'   => 'activate_construction_button',
		'std'  => 1,
		'type' => 'checkbox'
	);

	$options[] = array(
		'div'       => 'div',
		'condition' => 'activate_construction_button:not(0)',
		'type'      => 'heading-2'
	);

	$options[] = array(
		'name' => esc_html__('Type your text on the button','vbegy'),
		'id'   => 'construction_button_text',
		'std'  => 'Contact',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('Type your link on the button','vbegy'),
		'id'   => 'construction_button_url',
		'std'  => 'https://2code.info/',
		'type' => 'text'
	);

	$options[] = array(
		'name' => esc_html__('The color of the button on the construction page (hex code, ex: #FFFFFF)','vbegy'),
		'id'   => 'construction_button_color',
		'type' => 'color',
		'std'  => '#ff7361'
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

	return $options;
}?>