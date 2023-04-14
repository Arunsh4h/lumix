<?php
function askme_is_post_type($post_types = array("post")) {
	if (isset($post_types) && is_array($post_types)) {
		$screen = get_current_screen();
		if (in_array($screen->post_type,$post_types)) {
			return true;
		}
	}
}
/* Meta options */
function askme_admin_meta() {
	global $post;
	// Background Defaults
	$background_defaults = array(
		'color' => '',
		'image' => '',
		'repeat' => 'repeat',
		'position' => 'top center',
		'attachment'=>'scroll'
	);

	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	$options_question_categories = array();
	$options_question_categories_obj = get_categories();
	foreach ($options_question_categories_obj as $category) {
		$options_question_categories[$category->cat_ID] = $category->cat_name;
	}
	
	$sidebars = get_option('sidebars');
	$new_sidebars = array('default'=> 'Default');
	foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
		$new_sidebars[$sidebar['id']] = $sidebar['name'];
	}
	
	// Menus
    $menus = array();
    $all_menus = get_terms('nav_menu',array('hide_empty' => true));
	foreach ($all_menus as $menu) {
	    $menus[$menu->term_id] = $menu->name;
	}
	
	// Pull all the groups into an array
	$options_groups = array();
	global $wp_roles;
	$options_groups_obj = $wp_roles->roles;
	foreach ($options_groups_obj as $key_r => $value_r) {
		$options_groups[$key_r] = $value_r['name'];
	}
	
	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri(). '/admin/images/';
	$imagepath_theme =  get_template_directory_uri(). '/images/';

	$options = array();

	$options = apply_filters('askme_options_before_meta_options',$options,$post);

	if (askme_is_post_type(array(ask_questions_type,ask_asked_questions_type))) {
		$options[] = array(
			'name' => esc_html__('Question settings','vbegy'),
			'id'   => 'question_settings',
			'icon' => 'editor-help',
			'type' => 'heading'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$question_poll = get_post_meta($post->ID,"question_poll",true);
		$get_question_user_id = get_post_meta($post->ID,"user_id",true);
		$show_the_anonymously = apply_filters("askme_show_the_anonymously",true);
		
		$question_html = '<div class="custom-meta-field">';
			if ($show_the_anonymously == true && $post->post_author == 0) {
				$anonymously_question = get_post_meta($post->ID,"anonymously_question",true);
				$anonymously_user = get_post_meta($post->ID,"anonymously_user",true);
				if (($anonymously_question == "on" || $anonymously_question == 1) && $anonymously_user != "") {
					$question_username = esc_html__('Anonymous','vbegy');
					$question_email = 0;
				}else {
					$question_username = get_post_meta($post->ID,"question_username",true);
					$question_email = get_post_meta($post->ID,"question_email",true);
					$question_username = ($question_username != ""?$question_username:esc_html__('Anonymous','vbegy'));
					$question_email = ($question_email != ""?$question_email:"");
				}
				$question_html .= '<ul>
					<li><div class="clear"></div><br><span class="dashicons dashicons-admin-users"></span> '.esc_attr($question_username).'</li>';
					if ($question_email != "") {
						$question_html .= '<li><div class="clear"></div><br><span class="dashicons dashicons-email-alt"></span> '.esc_attr($question_email).'</li>';
					}
				$question_html .= '</ul>';
			}
			
			if ($get_question_user_id != "") {
				$display_name = get_the_author_meta('display_name',$get_question_user_id);
				if (isset($display_name) && $display_name != "") {
					$question_html .= '<ul>
						<li><div class="clear"></div><br><span class="dashicons dashicons-admin-users"></span> '.esc_html__('This question has asked to','vbegy').' <a target="_blank" href="'. get_author_posts_url($get_question_user_id).'">'.esc_attr($display_name).'</a></li>
					</ul>
					<div class="no-user-question"></div>';
				}
			}else {
				$added_file = get_post_meta($post->ID,"added_file",true);
				if ($added_file != "") {
					$question_html .= '<ul><li><div class="clear"></div><br><a href="'.wp_get_attachment_url($added_file).'">'.esc_html__('Attachment','vbegy').'</a> - <a class="delete-this-attachment single-attachment" href="'.$added_file.'">'.esc_html__('Delete','vbegy').'</a></li></ul>';
				}
				$attachment_m = get_post_meta($post->ID,"attachment_m",true);
				if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
					$question_html .= '<ul>';
						foreach ($attachment_m as $key => $value) {
							$question_html .= '<li><div class="clear"></div><br><a href="'.wp_get_attachment_url($value["added_file"]).'">'.esc_html__('Attachment','vbegy').'</a> - <a class="delete-this-attachment" href="'.$value["added_file"].'">'.esc_html__('Delete','vbegy').'</a></li>';
						}
					$question_html .= '</ul>';
				}
			}
		$question_html .= '</div>';
		
		$options[] = array(
			'type'    => 'content',
			'content' => $question_html,
		);
		
		if ($get_question_user_id == "") {
	       	$question_html = '<div class="custom-meta-field">';
		        $asks = get_post_meta($post->ID,"ask",true);
		        $askme_poll = get_post_meta($post->ID,"askme_poll",true);
		        if ($question_poll != "" && ($question_poll == "on" || $question_poll == 1)) {
		        	if (isset($askme_poll) && is_array($askme_poll)) {
		        		$i = 0;
		        		$question_html .= '<div class="custom-meta-label"><label>'.esc_html__('Stats of Users','vbegy').'</label></div><div class="clear"></div><br>';
		        		foreach ($askme_poll as $askme_polls):$i++;
		        			$question_html .= (isset($asks[$askme_polls['id']]['title']) && $asks[$askme_polls['id']]['title'] != ''?esc_html( $asks[$askme_polls['id']]['title'] ).' --- ':'').(isset($askme_polls['value']) && $askme_polls['value'] != 0?stripslashes( $askme_polls['value'] ):0)." Votes <br>";
			        		if (isset($askme_polls['user_ids']) && is_array($askme_polls['user_ids'])) {
			        			foreach ($askme_polls['user_ids'] as $key => $value) {
			        				if ($value > 0) {
			        					$user_name = get_the_author_meta("display_name",$value);
			        					if (isset($user_name) && $user_name != "") {
			        						$question_html .= '<div class="vpanel_checkbox_input"><p class="description">'.$user_name.' '.esc_html__('Has vote for','vbegy').' '.(isset($asks[$askme_polls['id']]['title']) && $asks[$askme_polls['id']]['title'] != ''?esc_html( $asks[$askme_polls['id']]['title'] ):'').'</p></div>';
			        					}
			        				}else {
			        					$question_html .= '<div class="vpanel_checkbox_input"><p class="description">'.esc_html__('Unregistered user has vote for','vbegy').' '.(isset($asks[$askme_polls['id']]['title']) && $asks[$askme_polls['id']]['title'] != ''?esc_html( $asks[$askme_polls['id']]['title'] ):'').'</p></div>';
			        				}
			        			}
			        			$question_html .= '<br>';
			        		}
			        	endforeach;
		        	}
		        }
			$question_html .= '</div>';
			
			$options = apply_filters('askme_options_before_question_poll',$options);

			if ($post->ID > 0) {
				$html_content = '<a class="button fix-comments" data-post="'.$post->ID.'" href="'.admin_url("post.php?post=".$post->ID."&action=edit").'">'.esc_html__("Fix the answers count","vbegy").'</a>';
				$options[] = array(
					'name' => $html_content,
					'type' => 'info'
				);
			}

			$options[] = array(
				'name'  => esc_html__("Question settings","vbegy"),
				'class' => 'home_page_display',
				'type'  => 'info'
			);

			$options[] = array(
				'name' => esc_html__('Is this question is a poll?','vbegy'),
				'id'   => 'question_poll',
				'type' => 'checkbox'
			);

			$options = apply_filters('askme_options_after_question_poll',$options);

			$options[] = array(
				'type'      => 'heading-2',
				'condition' => 'question_poll:not(0),question_poll:not(2)',
				'div'       => 'div'
			);

			$question_poll_array = array(
				array(
					"type" => "text",
					"id"   => "title",
					"name" => esc_html__('Title','vbegy'),
				),
				array(
					"type" => "hidden_id",
					"id"   => "id"
				),
			);
			
			$poll_image = askme_options("poll_image");
			$question_poll_image = array();
			if ($poll_image == 1) {
				$question_poll_image = array(
					array(
						"type" => "upload",
						"id"   => "image",
						"name" => esc_html__('Image','vbegy'),
					)
				);
			}
			
			$options[] = array(
				'id'        => "ask",
				'type'      => "elements",
				'button'    => esc_html__('Add a new option to poll','vbegy'),
				'not_theme' => 'not',
				'hide'      => "yes",
				'options'   => array_merge($question_poll_array,$question_poll_image)
			);
			
			$options[] = array(
				'type'    => 'content',
				'content' => $question_html,
			);
			
			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);
		}
		
		if ($get_question_user_id == "") {
			$hide_question_categories = apply_filters('askme_hide_question_categories',false);
			$category_single_multi = askme_options("category_single_multi");
			if ($hide_question_categories == false && $category_single_multi != "multi") {
				$options[] = array(
					'name'        => esc_html__('Choose from here the question category.','vbegy'),
					'id'          => prefix_meta.'question_category',
					'option_none' => esc_html__('Select a Category','vbegy'),
					'type'        => 'select_category',
					'taxonomy'    => ask_question_category,
					'selected'    => 's_f_category'
				);
			}
			
			$options[] = array(
				'name' => esc_html__('Video description','vbegy'),
				'desc' => esc_html__('Add a Video to describe the problem better.','vbegy'),
				'id'   => 'video_description',
				'type' => 'checkbox',
			);
			
			$options[] = array(
				'name'      => esc_html__('Video type','vbegy'),
				'id'        => 'video_type',
				'type'      => 'select',
				'options'   => array(
					'youtube'  => esc_html__("Youtube","vbegy"),
					'vimeo'    => esc_html__("Vimeo","vbegy"),
					'daily'    => esc_html__("Dialymotion","vbegy"),
					'facebook' => esc_html__("Facebook","vbegy"),
					'tiktok'   => esc_html__("TikTok","vbegy"),
				),
				'std'       => 'youtube',
				'condition' => 'video_description:not(0)',
				'desc'      => esc_html__('Choose from here the video type.','vbegy'),
			);
			
			$options[] = array(
				'name'      => esc_html__('Video ID','vbegy'),
				'desc'      => esc_html__('Put the Video ID here: https://www.youtube.com/watch?v=sdUUx5FdySs Ex: "sdUUx5FdySs".','vbegy'),
				'id'        => "video_id",
				'condition' => 'video_description:not(0)',
				'type'      => 'text',
			);
			
			$ask_question_items = askme_options("ask_question_items");
			if (isset($ask_question_items["featured_image"]["value"]) && $ask_question_items["featured_image"]["value"] == "featured_image") {
				$options[] = array(
					'name' => esc_html__('Custom featured image size','vbegy'),
					'desc' => esc_html__('Select ON to set the custom featured image size.','vbegy'),
					'id'   => prefix_meta.'custom_featured_image_size',
					'type' => 'checkbox'
				);
				
				$options[] = array(
					'type'      => 'heading-2',
					'condition' => prefix_meta.'custom_featured_image_size:not(0)',
					'div'       => 'div'
				);
				
				$options[] = array(
					"name" => esc_html__("Featured image width","vbegy"),
					"id"   => prefix_meta."featured_image_width",
					"type" => "sliderui",
					"std"  => "260",
					"step" => "1",
					"min"  => "50",
					"max"  => "600"
				);
				
				$options[] = array(
					"name" => esc_html__("Featured image height","vbegy"),
					"id"   => prefix_meta."featured_image_height",
					"type" => "sliderui",
					"std"  => "185",
					"step" => "1",
					"min"  => "50",
					"max"  => "600"
				);
				
				$options[] = array(
					'type' => 'heading-2',
					'end'  => 'end',
					'div'  => 'div'
				);
			}
		}
		
		$options[] = array(
			'name' => esc_html__('Notification by e-mail','vbegy'),
			'desc' => esc_html__('Get notified by email when someone answers this question','vbegy'),
			'id'   => 'remember_answer',
			'type' => 'checkbox',
		);
		
		$options[] = array(
			'name' => esc_html__('Private question?','vbegy'),
			'desc' => esc_html__('This question is a private question?','vbegy'),
			'id'   => 'private_question',
			'type' => 'checkbox',
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
	}
	
	if (askme_is_post_type(array("post","page",ask_questions_type,ask_asked_questions_type,"product"))) {
		$options[] = array(
			'name' => esc_html__('Post and Page Options','vbegy'),
			'id'   => 'post_page',
			'icon' => 'admin-site',
			'type' => 'heading',
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'    => esc_html__('Layout','vbegy'),
			'id'      => prefix_meta."layout",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default' => $imagepath.'sidebar_default.jpg',
				'full'    => $imagepath.'full.jpg',
				'fixed'   => $imagepath.'fixed.jpg',
				'fixed_2' => $imagepath.'fixed_2.jpg',
			)
		);

		$options[] = array(
			'name'    => esc_html__('Choose page / post template','vbegy'),
			'id'      => prefix_meta."home_template",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default'   => $imagepath.'sidebar_default.jpg',
				'grid_1300' => $imagepath.'template_1300.jpg',
				'grid_1200' => $imagepath.'template_1200.jpg',
				'grid_970'  => $imagepath.'template_970.jpg',
			)
		);

		$options[] = array(
			'name'    => esc_html__('Choose page / post skin','vbegy'),
			'id'      => prefix_meta."site_skin_l",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default'    => $imagepath.'sidebar_default.jpg',
				'site_light' => $imagepath.'light.jpg',
				'site_dark'  => $imagepath.'dark.jpg',
			)
		);

		$options[] = array(
			'name'    => esc_html__('Choose Your Skin','vbegy'),
			'id'      => prefix_meta."skin",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default'       => $imagepath.'default.jpg',
				'skin'          => $imagepath.'skin.jpg',
				'blue'          => $imagepath.'blue.jpg',
				'gray'          => $imagepath.'gray.jpg',
				'green'         => $imagepath.'green.jpg',
				'moderate_cyan' => $imagepath.'moderate_cyan.jpg',
				'orange'        => $imagepath.'orange.jpg',
				'purple'        => $imagepath.'purple.jpg',
				'red'           => $imagepath.'red.jpg',
				'strong_cyan'   => $imagepath.'strong_cyan.jpg',
				'yellow'        => $imagepath.'yellow.jpg',
			)
		);
		
		$options[] = array(
			'name'		=> esc_html__('Primary Color','vbegy'),
			'id'		=> prefix_meta."primary_color",
			'type'		=> 'color',
		);
		
		$options[] = array(
			'name'		=> esc_html__('Background','vbegy'),
			'id'		=> prefix_meta."background_img",
			'type'		=> 'upload',
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background color",'vbegy'),
			'id'		=> prefix_meta."background_color",
			'type'		=> 'color',
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background repeat",'vbegy'),
			'id'		=> prefix_meta."background_repeat",
			'type'		=> 'select',
			'options'	=> array(
				'repeat'	=> 'repeat',
				'no-repeat'	=> 'no-repeat',
				'repeat-x'	=> 'repeat-x',
				'repeat-y'	=> 'repeat-y',
			),
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background fixed",'vbegy'),
			'id'		=> prefix_meta."background_fixed",
			'type'		=> 'select',
			'options'	=> array(
				'fixed'  => 'fixed',
				'scroll' => 'scroll',
			),
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background position x",'vbegy'),
			'id'		=> prefix_meta."background_position_x",
			'type'		=> 'select',
			'options'	=> array(
				'left'	 => 'left',
				'center' => 'center',
				'right'	 => 'right',
			),
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background position y",'vbegy'),
			'id'		=> prefix_meta."background_position_y",
			'type'		=> 'select',
			'options'	=> array(
				'top'	 => 'top',
				'center' => 'center',
				'bottom' => 'bottom',
			),
		);
		
		$options[] = array(
			'name' => esc_html__("Full Screen Background",'vbegy'),
			'id'   => prefix_meta."background_full",
			'type' => 'checkbox',
			'std'  => 0,
		);

		$options[] = array(
			'name'    => esc_html__('Sidebar','vbegy'),
			'id'      => prefix_meta."sidebar",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default' => $imagepath.'sidebar_default.jpg',
				'right'   => $imagepath.'sidebar_right.jpg',
				'full'    => $imagepath.'sidebar_no.jpg',
				'left'    => $imagepath.'sidebar_left.jpg',
			)
		);
		
		$options[] = array(
			'name'		=> esc_html__('Select your sidebar','vbegy'),
			'id'		=> prefix_meta.'what_sidebar',
			'type'		=> 'select',
			'condition' => prefix_meta.'sidebar:not(full)',
			'options'	=> $new_sidebars,
		);

		if (askme_is_post_type(array("post","page","product"))) {
			$options[] = array(
				'name'		=> esc_html__('Head post','vbegy'),
				'id'		=> prefix_meta.'what_post',
				'type'		=> 'select',
				'options'	=> array(
					'image'     => "Featured Image",
					'lightbox'  => "Lightbox",
					'google'    => "Google Map",
					'slideshow' => "Slideshow",
					'video'     => "Video",
				),
				'std'		=> 'image',
				'desc'		=> esc_html__('Choose from here the post type.','vbegy'),
			);
			
			$options[] = array(
				'name'		=> esc_html__('Google map','vbegy'),
				'desc'		=> esc_html__("Put your google map html",'vbegy'),
				'id'		=> prefix_meta."google",
				'type'		=> 'textarea',
				'cols'		=> "40",
				'condition' => prefix_meta.'what_post:is(google)',
				'rows'		=> "8"
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'what_post:is(slideshow)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'		=> esc_html__('Slideshow ?','vbegy'),
				'id'		=> prefix_meta.'slideshow_type',
				'type'		=> 'select',
				'options'	=> array(
					'custom_slide' => "Custom Slideshow",
					'upload_images' => "Upload your images",
				),
				'std'		=> 'custom_slide',
			);

			$slide_elements = array(
				array(
					"type" => "upload",
					"id"   => "image_url",
					"name" => esc_html__('Image URL','vbegy')
				),
				array(
					"type" => "text",
					"id"   => "slide_link",
					"name" => esc_html__('Slide Link','vbegy')
				)
			);

			$options[] = array(
				'id'        => prefix_meta.'slideshow_post',
				'type'      => "elements",
				'not_theme' => "not",
				'hide'      => "yes",
				'button'    => esc_html__('Add a new slide','vbegy'),
				'options'   => $slide_elements,
				'condition' => prefix_meta.'slideshow_type:is(custom_slide)',
			);
			
			$options[] = array(
				'name'	=> esc_html__('Upload your images','vbegy'),
				'id'	=> prefix_meta."upload_images",
				'condition' => prefix_meta.'slideshow_type:is(upload_images)',
				'type'	=> 'upload_images',
			);

			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'what_post:is(video)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name'		=> esc_html__('Video type','vbegy'),
				'id'		=> prefix_meta.'video_post_type',
				'type'		=> 'select',
				'options'	=> array(
					'youtube' => "Youtube",
					'vimeo' => "Vimeo",
					'daily' => "Dialymotion",
					'facebook' => "Facebook",
					'tiktok' => "TikTok",
					'html5' => "HTML 5",
				),
				'std'		=> 'youtube',
				'desc'		=> esc_html__('Choose from here the video type','vbegy'),
			);
			
			$options[] = array(
				'name'		=> esc_html__('Video ID','vbegy'),
				'id'		=> prefix_meta.'video_post_id',
				'desc'		=> esc_html__('Put here the video id : https://www.youtube.com/watch?v=sdUUx5FdySs EX : "sdUUx5FdySs".','vbegy'),
				'type'		=> 'text',
				'condition' => prefix_meta.'video_post_type:not(html5)',
				'std'		=> ''
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'video_post_type:is(html5)',
				'type'      => 'heading-2'
			);
			
			$options[] = array(
				'name' => esc_html__('Video Image','vbegy'),
				'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','vbegy'),
				'id'   => prefix_meta.'video_image',
				'std'  => '',
				'type' => 'upload'
			);
			
			$options[] = array(
				'name'		=> esc_html__('Mp4 video','vbegy'),
				'id'		=> prefix_meta.'video_mp4',
				'desc'		=> esc_html__('Put here the mp4 video','vbegy'),
				'type'		=> 'text',
				'std'		=> ''
			);
			
			$options[] = array(
				'name'		=> esc_html__('M4v video','vbegy'),
				'id'		=> prefix_meta.'video_m4v',
				'desc'		=> esc_html__('Put here the m4v video','vbegy'),
				'type'		=> 'text',
				'std'		=> ''
			);
			
			$options[] = array(
				'name'		=> esc_html__('Webm video','vbegy'),
				'id'		=> prefix_meta.'video_webm',
				'desc'		=> esc_html__('Put here the webm video','vbegy'),
				'type'		=> 'text',
				'std'		=> ''
			);
			
			$options[] = array(
				'name'		=> esc_html__('Ogv video','vbegy'),
				'id'		=> prefix_meta.'video_ogv',
				'desc'		=> esc_html__('Put here the ogv video','vbegy'),
				'type'		=> 'text',
				'std'		=> ''
			);
			
			$options[] = array(
				'name'		=> esc_html__('Wmv video','vbegy'),
				'id'		=> prefix_meta.'video_wmv',
				'desc'		=> esc_html__('Put here the wmv video','vbegy'),
				'type'		=> 'text',
				'std'		=> ''
			);
			
			$options[] = array(
				'name'		=> esc_html__('Flv video','vbegy'),
				'id'		=> prefix_meta.'video_flv',
				'desc'		=> esc_html__('Put here the flv video','vbegy'),
				'type'		=> 'text',
				'std'		=> ''
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
		}
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name' => esc_html__('Single Pages Options','vbegy'),
			'id'   => 'single_page',
			'icon' => 'schedule',
			'type' => 'heading',
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);

		if (askme_is_post_type(array("post",ask_questions_type,ask_asked_questions_type))) {
			if (askme_is_post_type(array("post"))) {
				if ($post->ID > 0) {
					$html_content = '<a class="button fix-comments" data-post="'.$post->ID.'" href="'.admin_url("post.php?post=".$post->ID."&action=edit").'">'.esc_html__("Fix the comments count","vbegy").'</a>';
					$options[] = array(
						'name' => $html_content,
						'type' => 'info'
					);
				}
			}
			$options[] = array(
				'name' => esc_html__('Custom sections','vbegy'),
				'id'   => prefix_meta.'custom_sections',
				'type' => 'checkbox'
			);

			$options[] = array(
				'div'       => 'div',
				'condition' => prefix_meta.'custom_sections:not(0)',
				'type'      => 'heading-2'
			);
			if (askme_is_post_type(array(ask_questions_type,ask_asked_questions_type))) {
				$order_sections = array(
					array("name" => esc_html__('Advertising','vbegy'),"value" => "advertising","default" => "yes"),
					array("name" => esc_html__('About the author','vbegy'),"value" => "author","default" => "yes"),
					array("name" => esc_html__('Related questions','vbegy'),"value" => "related","default" => "yes"),
					array("name" => esc_html__('Advertising 2','vbegy'),"value" => "advertising_2","default" => "yes"),
					array("name" => esc_html__('Comments','vbegy'),"value" => "comments","default" => "yes"),
					array("name" => esc_html__('Next and Previous questions','vbegy'),"value" => "next_previous","default" => "yes"),
				);
			}else {
				$order_sections = array(
					array("name" => esc_html__('Advertising','vbegy'),"value" => "advertising","default" => "yes"),
					array("name" => esc_html__('About the author','vbegy'),"value" => "author","default" => "yes"),
					array("name" => esc_html__('Related articles','vbegy'),"value" => "related","default" => "yes"),
					array("name" => esc_html__('Advertising 2','vbegy'),"value" => "advertising_2","default" => "yes"),
					array("name" => esc_html__('Comments','vbegy'),"value" => "comments","default" => "yes"),
					array("name" => esc_html__('Next and Previous articles','vbegy'),"value" => "next_previous","default" => "yes"),
				);
			}
			
			$options[] = array(
				'name'    => esc_html__('Sort your sections','vbegy'),
				'id'      => prefix_meta.'order_sections',
				'type'    => 'sort',
				'std'     => $order_sections,
				'options' => $order_sections
			);

			$options[] = array(
				'type' => 'heading-2',
				'end'  => 'end',
				'div'  => 'div'
			);
		}

		$options[] = array(
			'name' => esc_html__('Choose a custom page setting','vbegy'),
			'id'   => prefix_meta.'custom_page_setting',
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => prefix_meta.'custom_page_setting:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Sticky sidebar enable or disable','vbegy'),
			'id'   => prefix_meta.'sticky_sidebar_s',
			'std'  => 1,
			'type' => 'checkbox'
		);
		
		if (askme_is_post_type(array("page","post"))) {
			$options[] = array(
				'name' => esc_html__('Post meta enable or disable','vbegy'),
				'id'   => prefix_meta.'post_meta_s',
				'std'  => 1,
				'type' => 'checkbox'
			);
		}
		
		if (askme_is_post_type(array("post",ask_questions_type,ask_asked_questions_type,"product"))) {
			$options[] = array(
				'name' => esc_html__('Share enable or disable','vbegy'),
				'id'   => prefix_meta.'post_share_s',
				'std'  => 1,
				'type' => 'checkbox'
			);
		}
		
		if (askme_is_post_type(array("post",ask_questions_type,ask_asked_questions_type))) {
			$options[] = array(
				'name' => esc_html__('Author info box enable or disable','vbegy'),
				'id'   => prefix_meta.'post_author_box_s',
				'std'  => 1,
				'type' => 'checkbox'
			);
		}
		
		if (askme_is_post_type(array("post",ask_questions_type,ask_asked_questions_type,"product"))) {
			$options[] = array(
				'name' => esc_html__('Related post enable or disable','vbegy'),
				'id'   => prefix_meta.'related_post_s',
				'std'  => 1,
				'type' => 'checkbox'
			);
		}
		
		if (askme_is_post_type(array("post",ask_questions_type,ask_asked_questions_type,"page"))) {
			$options[] = array(
				'name' => esc_html__('Comments enable or disable','vbegy'),
				'id'   => prefix_meta.'post_comments_s',
				'std'  => 1,
				'type' => 'checkbox'
			);
		}
		
		if (askme_is_post_type(array("post",ask_questions_type,ask_asked_questions_type,"product"))) {
			$options[] = array(
				'name' => esc_html__('Navigation post enable or disable','vbegy'),
				'desc' => esc_html__('Navigation post ( next and previous posts) enable or disable.','vbegy'),
				'id'   => prefix_meta.'post_navigation_s',
				'std'  => 1,
				'type' => 'checkbox'
			);
		}

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
			'name' => esc_html__('Advertising Options','vbegy'),
			'id'   => 'advertising_meta',
			'icon' => 'admin-post',
			'type' => 'heading',
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name'  => esc_html__("Advertising after header",'vbegy'),
			'id'    => prefix_meta.'header_adv_n',
			'type'  => 'info',
			'class' => 'home_page_display'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','vbegy'),
			'id'      => prefix_meta.'header_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'class'   => 'radio',
			'options' => array("display_code" => "Display code","custom_image" => "Custom Image")
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => prefix_meta.'header_adv_type:is(custom_image)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Image URL','vbegy'),
			'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','vbegy'),
			'id'   => prefix_meta.'header_adv_img',
			'std'  => '',
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising url','vbegy'),
			'id'   => prefix_meta.'header_adv_href',
			'std'  => '#',
			'type' => 'text'
		);

		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','vbegy'),
			'id'      => prefix_meta.'header_adv_link',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","vbegy"),"new_page" => esc_html__("New page","vbegy"))
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__("Advertising Code html ( Ex: Google ads)",'vbegy'),
			'id'   => prefix_meta.'header_adv_code',
			'std'  => '',
			'condition' => prefix_meta.'header_adv_type:is(display_code)',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name'  => esc_html__("Advertising 1 in post and question",'vbegy'),
			'id'    => prefix_meta.'share_adv_n',
			'type'  => 'info',
			'class' => 'home_page_display'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising type','vbegy'),
			'id'   => prefix_meta.'share_adv_type',
			'std'  => 'custom_image',
			'type' => 'radio',
			'class'   => 'radio',
			'options' => array("display_code" => "Display code","custom_image" => "Custom Image")
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => prefix_meta.'share_adv_type:is(custom_image)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Image URL','vbegy'),
			'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','vbegy'),
			'id'   => prefix_meta.'share_adv_img',
			'std'  => '',
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising url','vbegy'),
			'id'   => prefix_meta.'share_adv_href',
			'std'  => '#',
			'type' => 'text'
		);

		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','vbegy'),
			'id'      => prefix_meta.'share_adv_link',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","vbegy"),"new_page" => esc_html__("New page","vbegy"))
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__("Advertising Code html ( Ex: Google ads)",'vbegy'),
			'id'   => prefix_meta.'share_adv_code',
			'std'  => '',
			'condition' => prefix_meta.'share_adv_type:is(display_code)',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name'  => esc_html__("Advertising 2 in post and question",'vbegy'),
			'id'    => prefix_meta.'related_adv_n',
			'type'  => 'info',
			'class' => 'home_page_display'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising type','vbegy'),
			'id'   => prefix_meta.'related_adv_type',
			'std'  => 'custom_image',
			'type' => 'radio',
			'class'   => 'radio',
			'options' => array("display_code" => "Display code","custom_image" => "Custom Image")
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => prefix_meta.'related_adv_type:is(custom_image)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Image URL','vbegy'),
			'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','vbegy'),
			'id'   => prefix_meta.'related_adv_img',
			'std'  => '',
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising url','vbegy'),
			'id'   => prefix_meta.'related_adv_href',
			'std'  => '#',
			'type' => 'text'
		);

		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','vbegy'),
			'id'      => prefix_meta.'related_adv_link',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","vbegy"),"new_page" => esc_html__("New page","vbegy"))
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__("Advertising Code html ( Ex: Google ads)",'vbegy'),
			'id'   => prefix_meta.'related_adv_code',
			'std'  => '',
			'condition' => prefix_meta.'related_adv_type:is(display_code)',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name'  => esc_html__("Advertising after content",'vbegy'),
			'id'    => prefix_meta.'content_adv_n',
			'type'  => 'info',
			'class' => 'home_page_display'
		);
		
		$options[] = array(
			'name'    => esc_html__('Advertising type','vbegy'),
			'id'      => prefix_meta.'content_adv_type',
			'std'     => 'custom_image',
			'type'    => 'radio',
			'class'   => 'radio',
			'options' => array("display_code" => "Display code","custom_image" => "Custom Image")
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => prefix_meta.'content_adv_type:is(custom_image)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Image URL','vbegy'),
			'desc' => esc_html__('Upload a image, or enter URL to an image if it is already uploaded.','vbegy'),
			'id'   => prefix_meta.'content_adv_img',
			'std'  => '',
			'type' => 'upload'
		);
		
		$options[] = array(
			'name' => esc_html__('Advertising url','vbegy'),
			'id'   => prefix_meta.'content_adv_href',
			'std'  => '#',
			'type' => 'text'
		);

		$options[] = array(
			'name'    => esc_html__('Open the page in same page or a new page?','vbegy'),
			'id'      => prefix_meta.'content_adv_link',
			'std'     => "new_page",
			'type'    => 'select',
			'options' => array("same_page" => esc_html__("Same page","vbegy"),"new_page" => esc_html__("New page","vbegy"))
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__("Advertising Code html ( Ex: Google ads)",'vbegy'),
			'id'   => prefix_meta.'content_adv_code',
			'std'  => '',
			'condition' => prefix_meta.'content_adv_type:is(display_code)',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
	}

	if (askme_is_post_type(array("page"))) {
		$options[] = array(
			'name'     => esc_html__('Comments Options','vbegy'),
			'id'       => 'template_comments',
			'icon'     => 'admin-comments',
			'type'     => 'heading',
			'template' => 'template-comments.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__("Comment type",'vbegy'),
			'desc' => esc_html__("Select the comment type.",'vbegy'),
			'id' => prefix_meta."comment_type",
			'std' => "comments",
			'type' => "radio",
			'options' => array(
				'comments' => 'Comments',
				'answers' => 'Answers',
			)
		);

		$options[] = array(
			'name'    => esc_html__('Order by','vbegy'),
			'desc'    => esc_html__('Select the comments order by.','vbegy'),
			'id'      => prefix_meta."orderby_answers_a",
			'std'     => "date",
			'type'    => "radio",
			'options' => array(
				'date'  => 'Date',
				'votes' => 'Voted - Work at answers only.',
			)
		);
		
		$options[] = array(
			'name'    => esc_html__('Order','vbegy'),
			'id'      => prefix_meta.'order_answers',
			'std'     => "DESC",
			'type'    => 'radio',
			'options' => array(
				'DESC'  => 'Descending',
				'ASC'   => 'Ascending',
			),
		);
		
		$options[] = array(
			'name' => esc_html__('Comments per page','vbegy'),
			'desc' => esc_html__('put the comments per page','vbegy'),
			'id'   => prefix_meta.'answers_number',
			'type' => 'text',
			'std'  => "10"
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		
		$options[] = array(
			'name'     => esc_html__('Blog Options','vbegy'),
			'id'       => 'blog_template',
			'icon'     => 'admin-page',
			'type'     => 'heading',
			'template' => 'template-blog.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__("Blog style",'vbegy'),
			'desc' => esc_html__("Select the blog style.",'vbegy'),
			'id' => prefix_meta."blog_style",
			'std' => "blog_1",
			'type' => "radio",
			'options' => array(
				'blog_1' => 'Blog 1',
				'blog_2' => 'Blog 2',
			)
		);

		$options[] = array(
			'name' => esc_html__("Post number",'vbegy'),
			'desc' => esc_html__("put the post number",'vbegy'),
			'id' => prefix_meta.'post_number_b',
			'type' => 'text',
			'std' => "5"
		);
		
		$options[] = array(
			'name' => esc_html__("Excerpt post",'vbegy'),
			'desc' => esc_html__("Put here the excerpt post",'vbegy'),
			'id' => prefix_meta.'post_excerpt_b',
			'type' => 'text',
			'std' => "5"
		);
		
		$options[] = array(
			'name' => esc_html__("Order by",'vbegy'),
			'desc' => esc_html__("Select the post order by.",'vbegy'),
			'id' => prefix_meta."orderby_post_b",
			'std' => "recent",
			'type' => "select",
			'options' => array(
				'recent' => 'Recent',
				'popular' => 'Popular',
				'random' => 'Random',
			)
		);
		
		$options[] = array(
			'name'		=> esc_html__("Display by",'vbegy'),
			'id'		=> prefix_meta."post_display_b",
			'type'		=> 'select',
			'options'	=> array(
				'lasts'	=> 'Lasts',
				'single_category' => 'Single category',
				'multiple_categories' => 'Multiple categories',
				'posts'	=> 'Custom posts',
			),
			'std'		=> 'lasts',
		);
		
		$options[] = array(
			'name'		=> esc_html__('Single category','vbegy'),
			'id'		=> prefix_meta.'post_single_category_b',
			'type'		=> 'select',
			'condition' => prefix_meta.'post_display_b:is(single_category)',
			'options'	=> $options_categories,
		);
		
		$options[] = array(
			'name' => esc_html__("Post categories",'vbegy'),
			'desc' => esc_html__("Select the post categories.",'vbegy'),
			'id' => prefix_meta."post_categories_b",
			'condition' => prefix_meta.'post_display_b:is(multiple_categories)',
			'options' => $options_categories,
			'type' => 'multicheck'
		);
		
		$options[] = array(
			'name'     => esc_html__("Post ids",'vbegy'),
			'desc'     => esc_html__("Type the post ids.",'vbegy'),
			'id'       => prefix_meta."post_posts_b",
			'condition' => prefix_meta.'post_display_b:is(posts)',
			'std'      => '',
			'type'     => 'text',
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name'     => esc_html__('Contact us Options','vbegy'),
			'id'       => 'contact_us',
			'icon'     => 'email-alt',
			'type'     => 'heading',
			'template' => 'template-contact_us.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Map','vbegy'),
			'desc' => esc_html__('Put the code iframe map.','vbegy'),
			'id'   => prefix_meta.'contact_map',
			'std'  => '<iframe height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?q=egypt&amp;hl=en&amp;sll=26.820553,30.802498&amp;sspn=16.874794,19.753418&amp;hnear=Egypt&amp;t=m&amp;z=6&amp;output=embed"></iframe>',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name' => esc_html__('Form shortcode','vbegy'),
			'desc' => esc_html__('Put the form shortcode.','vbegy'),
			'id'   => prefix_meta.'contact_form',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('About widget enable or disable','vbegy'),
			'id'   => prefix_meta.'about_widget',
			'std'  => 1,
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('About content','vbegy'),
			'desc' => esc_html__('Put the about content.','vbegy'),
			'id'   => prefix_meta.'about_content',
			'condition' => prefix_meta.'about_widget:not(0)',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name' => esc_html__('Address','vbegy'),
			'desc' => esc_html__('Put the address.','vbegy'),
			'id'   => prefix_meta.'address',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Phone','vbegy'),
			'desc' => esc_html__('Put the phone.','vbegy'),
			'id'   => prefix_meta.'phone',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Email','vbegy'),
			'desc' => esc_html__('Put the email.','vbegy'),
			'id'   => prefix_meta.'email',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Social enable or disable','vbegy'),
			'desc' => esc_html__('Social widget enable or disable.','vbegy'),
			'id'   => prefix_meta.'social',
			'std'  => 1,
			'type' => 'checkbox'
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => prefix_meta.'social:not(0)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Facebook','vbegy'),
			'desc' => esc_html__('Put the facebook.','vbegy'),
			'id'   => prefix_meta.'facebook',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Twitter','vbegy'),
			'desc' => esc_html__('Put the twitter.','vbegy'),
			'id'   => prefix_meta.'twitter',
			'type' => 'text'
		);

		$options[] = array(
			'name' => esc_html__('TikTok','vbegy'),
			'desc' => esc_html__('Put the tiktok.','vbegy'),
			'id'   => prefix_meta.'tiktok',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Youtube','vbegy'),
			'desc' => esc_html__('Put the youtube.','vbegy'),
			'id'   => prefix_meta.'youtube',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Linkedin','vbegy'),
			'desc' => esc_html__('Put the linkedin.','vbegy'),
			'id'   => prefix_meta.'linkedin',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Instagram','vbegy'),
			'desc' => esc_html__('Put the instagram.','vbegy'),
			'id'   => prefix_meta.'instagram',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Dribbble','vbegy'),
			'desc' => esc_html__('Put the dribbble.','vbegy'),
			'id'   => prefix_meta.'dribbble',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Pinterest','vbegy'),
			'desc' => esc_html__('Put the pinterest.','vbegy'),
			'id'   => prefix_meta.'pinterest',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Rss enable or disable','vbegy'),
			'desc' => esc_html__('Rss widget enable or disable.','vbegy'),
			'id'   => prefix_meta.'rss',
			'std'  => 1,
			'type' => 'checkbox'
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
			'name'     => esc_html__('FAQs Setting','vbegy'),
			'id'       => 'faqs_template',
			'icon'     => 'info',
			'type'     => 'heading',
			'template' => 'template-faqs.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'id'      => prefix_meta."faqs",
			'type'    => "elements",
			'button'  => "Add new faq",
			'hide'    => "yes",
			'options' => array(
				array(
					"type" => "text",
					"id"   => "text",
					"name" => esc_html__("Title",'vbegy'),
				),
				array(
					"type" => "textarea",
					"id"   => "textarea",
					"name" => esc_html__("Content",'vbegy'),
				),
			),
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name'     => esc_html__('User Options','vbegy'),
			'id'       => 'users_template',
			'icon'     => 'admin-users',
			'type'     => 'heading',
			'template' => 'template-users.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Users per page','vbegy'),
			'desc' => esc_html__('Put the users per page.','vbegy'),
			'id'   => prefix_meta.'users_per_page',
			'std'  => '10',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Choose the user groups show','vbegy'),
			'id'      => prefix_meta.'user_group',
			'type'    => 'multicheck',
			'std'     => array("editor","administrator","author","contributor","subscriber"),
			'options' => $options_groups,
		);
		
		$options[] = array(
			'name'    => esc_html__('Order by','vbegy'),
			'id'      => prefix_meta.'user_sort',
			'type'    => 'select',
			'std' => "registered",
			'options'	=> array(
				'user_registered' => 'Register',
				'display_name'    => 'Name',
				'ID'              => 'ID',
				'question_count'  => 'Questions',
				'answers'         => 'Answers',
				'the_best_answer' => 'Best Answers',
				'points'          => 'Points',
				'post_count'      => 'Posts',
				'comments'        => 'Comments',
			),
		);
		
		$options[] = array(
			'name'    => esc_html__('Order','vbegy'),
			'id'      => prefix_meta.'user_order',
			'std'     => "DESC",
			'type'    => 'radio',
			'options' => array(
				'DESC'  => 'Descending',
				'ASC'   => 'Ascending',
			),
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name'     => esc_html__('Categories Options','vbegy'),
			'id'       => 'categories_template',
			'icon'     => 'category',
			'type'     => 'heading',
			'template' => 'template-categories.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Categories per page','vbegy'),
			'desc' => esc_html__('Put the categories per page.','vbegy'),
			'id'   => prefix_meta.'cats_per_page',
			'std'  => '50',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Categories type','vbegy'),
			'id'      => prefix_meta.'cats_tax',
			'std'     => ask_questions_type,
			'type'    => 'radio',
			'options' => array(
				ask_questions_type => 'Question categories',
				'product'          => 'Product categories',
				'post'             => 'Post categories',
			),
		);
		
		$options[] = array(
			'name'    => esc_html__('Order by','vbegy'),
			'id'      => prefix_meta.'cat_sort',
			'std'     => "count",
			'type'    => 'radio',
			'options' => array(
				'count' => 'Popular',
				'name'  => 'Name',
			),
		);
		
		$options[] = array(
			'name'    => esc_html__('Order','vbegy'),
			'id'      => prefix_meta.'cat_order',
			'std'     => "DESC",
			'type'    => 'radio',
			'options' => array(
				'DESC'  => 'Descending',
				'ASC'   => 'Ascending',
			),
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);

		$options[] = array(
			'name'     => esc_html__('Tags Options','vbegy'),
			'id'       => 'tags_template',
			'icon'     => 'tag',
			'type'     => 'heading',
			'template' => 'template-tags.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Tags per page','vbegy'),
			'desc' => esc_html__('Put the tags per page.','vbegy'),
			'id'   => prefix_meta.'tags_per_page',
			'std'  => '50',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'    => esc_html__('Tags type','vbegy'),
			'id'      => prefix_meta.'tags_tax',
			'std'     => ask_questions_type,
			'type'    => 'radio',
			'options' => array(
				ask_questions_type => 'Question tags',
				'product'          => 'Product tags',
				'post'             => 'Post tags',
			),
		);
		
		$options[] = array(
			'name'    => esc_html__('Order by','vbegy'),
			'id'      => prefix_meta.'tag_sort',
			'std'     => "count",
			'type'    => 'radio',
			'options' => array(
				'count' => 'Popular',
				'name'  => 'Name',
			),
		);
		
		$options[] = array(
			'name'    => esc_html__('Tags style','vbegy'),
			'desc'    => esc_html__('Choose the tags style.','vbegy'),
			'id'      => prefix_meta.'tag_style',
			'options' => array(
				'advanced' => 'Advanced',
				'simple'   => 'Simple',
			),
			'std'     => 'advanced',
			'type'    => 'radio'
		);
		
		$options[] = array(
			'name'    => esc_html__('Order','vbegy'),
			'id'      => prefix_meta.'tag_order',
			'std'     => "DESC",
			'type'    => 'radio',
			'options' => array(
				'DESC'  => 'Descending',
				'ASC'   => 'Ascending',
			),
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		/*

		$options[] = array(
			'name'     => esc_html__('Forum Options','vbegy'),
			'id'       => 'forum_option',
			'icon'     => 'admin-site',
			'type'     => 'heading',
			'template' => 'template-forum.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Choose the categories show','vbegy'),
			'desc' => esc_html__('Choose the categories show.','vbegy'),
			'id'   => prefix_meta.'forum_categories',
			'type' => 'questions_categories',
			'show_all' => 'no'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
		*/

		$options[] = array(
			'name'     => esc_html__('Home Options','vbegy'),
			'id'       => 'ask_me',
			'icon'     => 'admin-home',
			'type'     => 'heading',
			'template' => 'template-home.php'
		);
		
		$options[] = array(
			'type' => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Home top box enable or disable','vbegy'),
			'id'   => prefix_meta.'index_top_box',
			'std'  => 1,
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Home top box layout','vbegy'),
			'id'      => prefix_meta.'index_top_box_layout',
			'std'     => '1',
			'class'   => 'index_top_box_layout',
			'type'    => 'radio',
			'options' => array("1" => "Style 1","2" => "Style 2")
		);
		
		$options[] = array(
			'name'    => esc_html__('Question title or comment','vbegy'),
			'id'      => prefix_meta.'index_title_comment',
			'std'     => 'title',
			'class'   => 'index_title_comment',
			'type'    => 'radio',
			'options' => array("title" => "Title","comment" => "Comment")
		);
		
		$options[] = array(
			'name' => esc_html__('Remove the content ?','vbegy'),
			'desc' => esc_html__('Remove the content ( Title, content, buttons and ask question ) ?','vbegy'),
			'id'   => prefix_meta.'remove_index_content',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name'    => esc_html__('Home top box background','vbegy'),
			'id'      => prefix_meta.'index_top_box_background',
			'std'     => 'background',
			'class'   => 'index_top_box_background',
			'type'    => 'radio',
			'options' => array("background" => "Background","slideshow" => "Slideshow")
		);
		
		$options[] = array(
			'name'	=> esc_html__('Upload your images','vbegy'),
			'id'	=> prefix_meta."upload_images_home",
			'condition' => prefix_meta.'index_top_box_background:is(slideshow)',
			'type'	=> 'upload_images',
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => prefix_meta.'index_top_box_background:is(background)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background color",'vbegy'),
			'id'		=> prefix_meta."background_color_home",
			'type'		=> 'color',
		);
		
		$options[] = array(
			'name'		=> esc_html__('Background','vbegy'),
			'id'		=> prefix_meta."background_img_home",
			'type'		=> 'upload',
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background repeat",'vbegy'),
			'id'		=> prefix_meta."background_repeat_home",
			'type'		=> 'select',
			'options'	=> array(
				'repeat'	=> 'repeat',
				'no-repeat'	=> 'no-repeat',
				'repeat-x'	=> 'repeat-x',
				'repeat-y'	=> 'repeat-y',
			),
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background fixed",'vbegy'),
			'id'		=> prefix_meta."background_fixed_home",
			'type'		=> 'select',
			'options'	=> array(
				'fixed'  => 'fixed',
				'scroll' => 'scroll',
			),
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background position x",'vbegy'),
			'id'		=> prefix_meta."background_position_x_home",
			'type'		=> 'select',
			'options'	=> array(
				'left'	 => 'left',
				'center' => 'center',
				'right'	 => 'right',
			),
		);
		
		$options[] = array(
			'name'		=> esc_html__("Background position y",'vbegy'),
			'id'		=> prefix_meta."background_position_y_home",
			'type'		=> 'select',
			'options'	=> array(
				'top'	 => 'top',
				'center' => 'center',
				'bottom' => 'bottom',
			),
		);
		
		$options[] = array(
			'name' => esc_html__("Full Screen Background",'vbegy'),
			'id'   => prefix_meta."background_full_home",
			'type' => 'checkbox',
			'std'  => 0,
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Home top box title','vbegy'),
			'desc' => esc_html__('Put the Home top box title.','vbegy'),
			'id'   => prefix_meta.'index_title',
			'std'  => 'Welcome to Lumeno',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Home top box content','vbegy'),
			'desc' => esc_html__('Put the Home top box content.','vbegy'),
			'id'   => prefix_meta.'index_content',
			'std'  => 'Duis dapibus aliquam mi, eget euismod sem scelerisque ut. Vivamus at elit quis urna adipiscing iaculis. Curabitur vitae velit in neque dictum blandit. Proin in iaculis neque.',
			'type' => 'textarea'
		);
		
		$options[] = array(
			'name' => esc_html__('About Us title','vbegy'),
			'desc' => esc_html__('Put the About Us title.','vbegy'),
			'id'   => prefix_meta.'index_about',
			'std'  => 'About Us',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('About Us link','vbegy'),
			'desc' => esc_html__('Put the About Us link.','vbegy'),
			'id'   => prefix_meta.'index_about_h',
			'std'  => '#',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Join Now title','vbegy'),
			'desc' => esc_html__('Put the Join Now title.','vbegy'),
			'id'   => prefix_meta.'index_join',
			'std'  => 'Join Now',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Join Now link','vbegy'),
			'desc' => esc_html__('Put the Join Now link.','vbegy'),
			'id'   => prefix_meta.'index_join_h',
			'std'  => '#',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('About Us title if login','vbegy'),
			'desc' => esc_html__('Put the About Us title if login.','vbegy'),
			'id'   => prefix_meta.'index_about_login',
			'std'  => 'About Us',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('About Us link if login','vbegy'),
			'desc' => esc_html__('Put the About Us link if login.','vbegy'),
			'id'   => prefix_meta.'index_about_h_login',
			'std'  => '#',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Ask question title if login','vbegy'),
			'desc' => esc_html__('Put the Ask question title if login.','vbegy'),
			'id'   => prefix_meta.'index_join_login',
			'std'  => 'Ask question',
			'type' => 'text'
		);
		
		$options[] = array(
			'name' => esc_html__('Ask question link if login','vbegy'),
			'desc' => esc_html__('Put the Ask question link if login.','vbegy'),
			'id'   => prefix_meta.'index_join_h_login',
			'std'  => '#',
			'type' => 'text'
		);
		
		$options[] = array(
			'name'		=> esc_html__("Page style",'vbegy'),
			'id'		=> prefix_meta."index_tabs",
			'type'		=> 'radio',
			'std'		=> "tabs",
			'options'	=> array(
				"tabs"	 => "Tabs",
				"recent" => 'Recent questions',
				"page"	 => 'Page content',
			),
		);

		$options[] = array(
			'div'       => 'div',
			'condition' => prefix_meta.'index_tabs:is(tabs)',
			'type'      => 'heading-2'
		);
		
		$options[] = array(
			'name' => esc_html__('Tabs pagination enable or disable','vbegy'),
			'id'   => prefix_meta.'pagination_tabs',
			'std'  => 1,
			'type' => 'checkbox'
		);

		$options[] = array(
			'name'        => esc_html__('Choose the categories show','vbegy'),
			'id'          => prefix_meta.'categories_show',
			'type'        => 'custom_addition',
			'taxonomy'    => ask_question_category,
			'addto'       => prefix_meta.'home_tabs',
			'toadd'       => 'yes',
			'option_none' => 'q-0'
		);
		
		$home_tabs = array(
			"recent_questions"  => array("sort" => esc_html__('Recent Questions','vbegy'),"value" => "recent_questions"),
			"most_responses"    => array("sort" => esc_html__('Most Answered','vbegy'),"value" => "most_responses"),
			"recently_answered" => array("sort" => esc_html__('Answers','vbegy'),"value" => "recently_answered"),
			"no_answers"        => array("sort" => esc_html__('No Answers','vbegy'),"value" => "no_answers"),
			"most_visit"        => array("sort" => esc_html__('Most Visited','vbegy'),"value" => "most_visit"),
			"most_vote"         => array("sort" => esc_html__('Most Voted','vbegy'),"value" => "most_vote"),
			"question_bump"     => array("sort" => esc_html__('Bump Question','vbegy'),"value" => "question_bump"),
			"recent_posts"      => array("sort" => esc_html__('Recent Posts','vbegy'),"value" => "recent_posts"),
		);

		$home_tabs = array(
			"recent-questions"     => array("sort" => esc_html__('Recent Questions','vbegy'),"value" => "recent-questions"),
			"most-answers"         => array("sort" => esc_html__('Most Answered','vbegy'),"value" => "most-answers"),
			"answers"              => array("sort" => esc_html__('Answers','vbegy'),"value" => "answers"),
			"no-answers"           => array("sort" => esc_html__('No Answers','vbegy'),"value" => "no-answers"),
			"most-visit"           => array("sort" => esc_html__('Most Visited','vbegy'),"value" => "most-visit"),
			"most-vote"            => array("sort" => esc_html__('Most Voted','vbegy'),"value" => "most-vote"),
			"question-bump"        => array("sort" => esc_html__('Bump Question','vbegy'),"value" => ""),
			"recent-posts"         => array("sort" => esc_html__('Recent Posts','vbegy'),"value" => ""),
		);

		$home_tabs = apply_filters("askme_meta_home_tabs",$home_tabs);
		
		$options[] = array(
			'name'    => esc_html__('Select the tabs you want to show','vbegy'),
			'id'      => prefix_meta.'home_tabs',
			'type'    => 'multicheck_3',
			'sort'    => 'yes',
			'std'     => $home_tabs,
			'options' => $home_tabs
		);

		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end',
			'div'  => 'div'
		);
		
		$options[] = array(
			'name' => esc_html__('Posts per page','vbegy'),
			'desc' => esc_html__('Put the Posts per page.','vbegy'),
			'id'   => prefix_meta.'posts_per_page',
			'std'  => '10',
			'condition' => prefix_meta.'index_top_box_background:not(3)',
			'type' => 'text'
		);
		
		$options[] = array(
		    'name'    => esc_html__('Content before tabs','vbegy'),
		    'id'      => prefix_meta.'content_before_tabs',
		    'type'    => 'editor',
		    'raw'     => false,
		    'options' => array(
		        'textarea_rows' => 5,
		        'teeny'         => true,
		    ),
		);
		
		$options[] = array(
		    'name'    => esc_html__('Content after tabs','vbegy'),
		    'id'      => prefix_meta.'content_after_tabs',
		    'type'    => 'editor',
		    'raw'     => false,
		    'options' => array(
		        'textarea_rows' => 5,
		        'teeny'         => true,
		    ),
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
	}

	return $options;
}
