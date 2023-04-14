<?php $settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
/* message post type */
function message_post_types_init() {
	$messages_slug = askme_options('message_slug');
	$messages_slug = (isset($messages_slug) && $messages_slug != ""?$messages_slug:"message");
		register_post_type( 'message',
				array(
					'label' => __('Messages','vbegy'),
						'labels' => array(
				'name'               => __('Messages','vbegy'),
				'singular_name'      => __('Messages','vbegy'),
				'menu_name'          => __('Messages','vbegy'),
				'name_admin_bar'     => __('Messages','vbegy'),
				'add_new'            => __('Add New','vbegy'),
				'add_new_item'       => __('Add New Message','vbegy'),
				'new_item'           => __('New Message','vbegy'),
				'edit_item'          => __('Edit Message','vbegy'),
				'view_item'          => __('View Message','vbegy'),
				'view_items'         => __('View Messages','vbegy'),
				'all_items'          => __('All Messages','vbegy'),
				'search_items'       => __('Search Messages','vbegy'),
				'parent_item_colon'  => __('Parent Message:','vbegy'),
				'not_found'          => __('No Messages Found.','vbegy'),
				'not_found_in_trash' => __('No Messages Found in Trash.','vbegy'),
						),
						'description'         => '',
						'public'              => false,
						'show_ui'             => true,
						'capability_type'     => 'post',
						'capabilities'        => array('create_posts' => 'do_not_allow'),
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => false,
						'hierarchical'        => false,
						'query_var'           => false,
						'show_in_rest'        => false,
						'has_archive'         => false,
			'menu_position'       => 5,
			'menu_icon'           => "dashicons-email-alt",
						'supports'            => array('title','editor'),
				)
		);
}  
add_action( 'init', 'message_post_types_init', 0 );
function message_updated_messages($messages) {
	global $post_ID;
	$messages['message'] = array(
		0 => '',
		1 => '',
	);
	return $messages;
}
add_filter('post_updated_messages','message_updated_messages');
/* Admin columns for post types */
function askme_message_columns($old_columns){
	$columns = array();
	$columns["cb"]       = "<input type=\"checkbox\">";
	$columns["title"]    = __("Title","vbegy");
	$columns["content"]  = __("Content","vbegy");
	$columns["author_m"] = __("Author","vbegy");
	$columns["to_user"]  = __("To user","vbegy");
	$columns["date"]     = __("Date","vbegy");
	$columns["delete"]   = __("User delete?","vbegy");
	return $columns;
}
add_filter('manage_edit-message_columns', 'askme_message_columns');

function askme_message_custom_columns($column) {
	global $post;
	$to_user = get_post_meta($post->ID,'message_user_id',true);
	$display_name_user = get_the_author_meta('display_name',$to_user);
	switch ( $column ) {
		case 'author_m' :
			$display_name = get_the_author_meta('display_name',$post->post_author);
			if ($post->post_author > 0) {
				echo '<a href="edit.php?post_type=message&author='.$post->post_author.'">'.$display_name.'</a>';
			}else {
				echo get_post_meta($post->ID,'message_username',true);
			}
		break;
		case 'content' :
			echo $post->post_content;
		break;
		case 'to_user' :
			echo '<a href="'.get_author_posts_url($to_user).'">'.$display_name_user.'</a>';
		break;
		case 'delete' :
			$delete_send_message = get_post_meta($post->ID,"delete_send_message",true);
			$delete_inbox_message = get_post_meta($post->ID,"delete_inbox_message",true);
			if ($delete_inbox_message == 1) {
				echo '<a href="'.get_author_posts_url($to_user).'">'.$display_name_user.'</a> '.__("delete his inbox message.","vbegy");
			}
			if ($delete_send_message == 1 || $delete_inbox_message == 1) {
				if ($delete_send_message == 1 && $delete_inbox_message == 1) {
					echo '<br>';
				}
				if ($delete_send_message == 1) {
					$display_name = get_the_author_meta('display_name',$post->post_author);
					echo '<a href="'.get_author_posts_url($post->post_author).'">'.$display_name.'</a> '.__("delete his sent message.","vbegy");
				}
			}
			if ($delete_inbox_message != 1 && $delete_send_message != 1) {
				echo '<span aria-hidden="true">—</span><span class="screen-reader-text">'.__("No one delete it","vbegy").'</span>';
			}
		break;
	}
}
add_action('manage_message_posts_custom_column', 'askme_message_custom_columns', 2);
/* send_message_shortcode */
add_shortcode('send_message', 'send_message_shortcode');
function send_message_shortcode($atts, $content = null) {
	global $posted,$settings;
	$a = shortcode_atts( array(
			'type' => '',
	), $atts );
	$out = '';
	$user_get_current_user_id = get_current_user_id();
	$send_message = askme_options("send_message");
	$send_message_no_register = askme_options("send_message_no_register");
	$custom_permission = askme_options("custom_permission");
	
	if (is_user_logged_in) {
		$user_is_login = get_userdata($user_get_current_user_id);
		$user_login_group = key($user_is_login->caps);
		$roles = $user_is_login->allcaps;
	}
	
	if (($custom_permission == 1 && is_user_logged_in && !is_super_admin($user_get_current_user_id) && empty($roles["send_message"])) || ($custom_permission == 1 && !is_user_logged_in && $send_message != 1)) {
		$out .= '<div class="note_error"><strong>'.__("Sorry, you do not have permission to send message.","vbegy").'</strong></div>';
		if (!is_user_logged_in) {
			$out .= '<div class="form-style form-style-3"><div class="note_error"><strong>'.__("You must login to send a message.","vbegy").'</strong></div>'.do_shortcode("[ask_login register_2='yes']").'</div>';
		}
	}else if (!is_user_logged_in && $send_message_no_register != 1) {
		$out .= '<div class="form-style form-style-3"><div class="note_error"><strong>'.__("You must login to send a message.","vbegy").'</strong></div>'.do_shortcode("[ask_login register_2='yes']").'</div>';
	}else {
		if ($_POST) {
			$post_type = (isset($_POST["post_type"]) && $_POST["post_type"] != ""?esc_html($_POST["post_type"]):"");
		}else {
			$post_type = "";
		}
		
		if (isset($_POST["post_type"]) && $_POST["post_type"] == "send_message") {
			do_action('new_message');
		}
		
		if ($post_type != "add_question" && $post_type != "edit_question" && $post_type != "add_post" && $post_type != "edit_post") {
			$users_by_id = $get_user_id = 0;
			if (isset($_GET["user_id"]) && $_GET["user_id"] != "") {
				$get_user_id = (int)$_GET["user_id"];
				$get_users_by_id = get_users(array("include" => array($get_user_id)));
				if (isset($get_users_by_id) && !empty($get_users_by_id)) {
					$users_by_id = 1;
				}
			}else if (is_author()) {
				$users_by_id = $get_user_id = 0;
				$user_login = get_queried_object();
				if (isset($user_login) && is_object($user_login)) {
					$user_login = get_userdata(esc_attr($user_login->ID));
				}
				if (isset($user_login) && !is_object($user_login)) {
					$user_login = get_user_by('login',urldecode(get_query_var('author_name')));
				}
				if (isset($user_login) && !is_object($user_login)) {
					$user_login = get_user_by('slug',urldecode(get_query_var('author_name')));
				}
				if (isset($user_login) && is_object($user_login)) {
					$users_by_id = 1;
					$get_user_id = $user_login->ID;
				}
			}
			
			if (is_user_logged_in && $user_get_current_user_id == $get_user_id) {
				echo '<div class="alert-message error"><p>'.__("You can't send message for yourself.","vbegy").'</p></div>';
			}else {
				$comment_message = askme_options("comment_message");
				$out .= '<div class="form-posts"><div class="form-style form-style-3 message-submit">
					<div class="send_message">
						<div '.(!is_user_logged_in?"class='if_no_login'":"").'>';
							$rand_m = rand(1,1000);
							$out .= '
							<form class="new-message-form" method="post" enctype="multipart/form-data">
								<div class="note_error display"></div>
								<div class="form-inputs clearfix">';
									if (!is_user_logged_in && $send_message_no_register == 1) {
										$out .= '<p>
											<label for="message-username-'.$rand_m.'" class="required">'.__("Username","vbegy").'<span>*</span></label>
											<input name="username" id="message-username-'.$rand_m.'" class="the-username" type="text" value="'.(isset($posted['username'])?$posted['username']:'').'">
											<span class="form-description">'.__("Please type your username .","vbegy").'</span>
										</p>
										
										<p>
											<label for="message-email-'.$rand_m.'" class="required">'.__("E-Mail","vbegy").'<span>*</span></label>
											<input name="email" id="message-email-'.$rand_m.'" class="the-email" type="text" value="'.(isset($posted['email'])?$posted['email']:'').'">
											<span class="form-description">'.__("Please type your E-Mail .","vbegy").'</span>
										</p>';
									}
									$out .= '<p>
										<label for="message-title-'.$rand_m.'" class="required">'.__("Message Title","vbegy").'<span>*</span></label>
										<input name="title" id="message-title-'.$rand_m.'" class="the-title" type="text" value="'.(isset($posted['title'])?ask_kses_stip($posted['title']):(isset($_POST["title"])?ask_kses_stip($_POST["title"]):"")).'">
									</p>';
									
									$featured_image_message = askme_options("featured_image_message");
									if ($featured_image_message == 1) {
										$out .= '<div class="featured_image_question">
											<label for="featured_image-'.$rand_m.'">'.__("Featured image","vbegy").'</label>
											<div class="fileinputs">
												<input type="file" class="file" name="featured_image" id="featured_image-'.$rand_m.'">
												<div class="fakefile">
													<button type="button" class="button small margin_0">'.__("Select file","vbegy").'</button>
													<span><i class="icon-arrow-up"></i>'.__("Browse","vbegy").'</span>
												</div>
											</div>
										</div>';
									}
								$out .= '</div>
								<div class="details-area">
									<label for="message-details-'.$rand_m.'" '.($comment_message == 1?'class="required"':'').'>'.__("Details","vbegy").($comment_message == 1?'<span>*</span>':'').'</label>';
									
									$editor_message_details = askme_options("editor_message_details");
									if ($editor_message_details == 1) {
										ob_start();
										$settings = apply_filters('askme_message_editor_setting',$settings);
										wp_editor((isset($posted['comment'])?ask_kses_stip_wpautop($posted['comment']):(isset($_POST["comment"])?wp_kses_post($_POST["comment"]):"")),"message-details-".$rand_m,$settings);
										$editor_contents = ob_get_clean();
										
										$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
									}else {
										$out .= '<textarea name="comment" id="message-details-'.$rand_m.'" class="the-textarea" aria-required="true" cols="58" rows="8">'.(isset($posted['comment'])?ask_kses_stip($posted['comment']):(isset($_POST["comment"])?ask_kses_stip($_POST["comment"]):"")).'</textarea>';
									}
								$out .= '<div class="clearfix"></div></div>
								
								<div class="form-inputs clearfix">
								
								'.askme_add_captcha(askme_options("the_captcha_message"),"message",$rand_m).'
								
								</div>
								
								<p class="form-submit">
									<input type="hidden" name="post_type" value="send_message">';
									if (isset($a["type"]) && $a["type"] == "popup") {
										$out .= '<input type="hidden" name="form_type" value="message-popup">';
									}else {
										$out .= '<input type="hidden" name="form_type" value="send_message">';
									}
									if ($users_by_id == 1) {
										$out .= '<input type="hidden" name="user_id" value="'.$get_user_id.'">';
									}
									$out .= '<input type="submit" value="'.__("Send Your Message","vbegy").'" class="button color small submit send-message">
								</p>
							
							</form>
						</div>
					</div>
				</div></div>';
			}
		}
	}
	return $out;
}
/* new_message */
function new_message() {
	if ($_POST) :
		$return = process_new_messages($_POST);
		if (is_wp_error($return)) :
				echo '<div class="ask_error"><span><p>'.$return->get_error_message().'</p></span></div>';
			else :
				if (get_post_type($return) == "message") {
				$get_post = get_post($return);
					$user_id = get_current_user_id();
					$get_message_user = get_post_meta($get_post->ID,"message_user_id",true);
					if ($get_post->post_status == "publish") {
						askme_notification_send_message($get_post,$user_id,$get_message_user);
					}
				if(!session_id()) session_start();
				$_SESSION['vbegy_session_message'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Message been successfully","vbegy").'</span><br>'.__("The message has been sent successfully.","vbegy").'</p></div>';
				wp_redirect((is_author()?esc_url(vpanel_get_user_url($get_message_user)):esc_url(get_page_link(askme_options('messages_page')))));
				exit;
			}
			exit;
			endif;
	endif;
}
add_action('new_message','new_message');
/* Notification send message */
function askme_notification_send_message($get_post,$user_id,$get_message_user) {
	if ($user_id != $get_message_user) {
		$message_username = get_post_meta($get_post->ID,'message_username',true);
		if ($get_post->post_author != $get_message_user && $get_message_user > 0) {
			askme_notifications_activities($get_message_user,$get_post->post_author,($get_post->post_author == 0?$message_username:""),"","","add_message_user","notifications","","message");
		}
		if ($user_id > 0) {
			askme_notifications_activities($user_id,$get_message_user,"","","","add_message","activities","","message");
		}

		$send_email_message = askme_options("send_email_message");
		if ($send_email_message == 1) {
			$user = get_userdata($get_message_user);
			$send_text = askme_send_mail(
				array(
					'content'          => askme_options("email_new_message"),
					'user_id'          => $get_message_user,
					'post_id'          => $get_post->ID,
					'sender_user_id'   => $get_post->post_author,
					'received_user_id' => $user->ID,
				)
			);
			$email_title = askme_options("title_new_message");
			$email_title = ($email_title != ""?$email_title:esc_html__("New message","vbegy"));
			$email_title = askme_send_mail(
				array(
					'content'          => $email_title,
					'title'            => true,
					'break'            => '',
					'user_id'          => $get_message_user,
					'post_id'          => $get_post->ID,
					'sender_user_id'   => $get_post->post_author,
					'received_user_id' => $user->ID,
				)
			);
			askme_send_mails(
				array(
					'toEmail'     => esc_html($user->user_email),
					'toEmailName' => esc_html($user->display_name),
					'title'       => $email_title,
					'message'     => $send_text,
				)
			);
		}
	}
}
/* process_new_messages */
function process_new_messages($data) {
	global $posted;
	set_time_limit(0);
	$errors = new WP_Error();
	$posted = array();
	
	$post_type = (isset($data["post_type"]) && $data["post_type"] != ""?$data["post_type"]:"");
	if ($post_type == "send_message") {
		
		$fields = array(
			'title','comment','ask_captcha','username','email','user_id'
		);
		
		foreach ($fields as $field) :
			if (isset($data[$field])) $posted[$field] = $data[$field]; else $posted[$field] = '';
		endforeach;
		
		$user_get_current_user_id = get_current_user_id();
		$featured_image_message = askme_options("featured_image_message");
		$custom_permission = askme_options("custom_permission");
		$send_message_no_register = askme_options("send_message_no_register");
		$send_message = askme_options("send_message");
		if (is_user_logged_in) {
			$user_is_login = get_userdata($user_get_current_user_id);
			$user_login_group = key($user_is_login->caps);
			$roles = $user_is_login->allcaps;
		}
		
		if (($custom_permission == 1 && is_user_logged_in && !is_super_admin($user_get_current_user_id) && empty($roles["send_message"])) || ($custom_permission == 1 && !is_user_logged_in && $send_message != 1)) {
			$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Sorry, you do not have permission to send message.","vbegy"));
			if (!is_user_logged_in) {
				$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("You must login to send a message.","vbegy"));
			}
		}else if (!is_user_logged_in && $send_message_no_register != 1) {
			$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("You must login to send a message.","vbegy"));
		}else if ($posted['user_id'] == $user_get_current_user_id) {
			$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("You can't send message for yourself.","vbegy"));
		}else if ($posted['user_id'] == "") {
			$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are a error.","vbegy"));
		}
		
		if (!is_user_logged_in && $send_message_no_register == 1 && get_current_user_id() == 0) {
			if (empty($posted['username'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (username).","vbegy"));
			if (empty($posted['email'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (email).","vbegy"));
			if (!is_email($posted['email'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Please write correctly email.","vbegy"));
		}
		
		/* Validate Required Fields */
		if (empty($posted['title'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (title).","vbegy"));

		/* Featured image */

		if ($featured_image_message == 1) {
			$featured_image = '';

			require_once(ABSPATH . 'wp-admin/includes/image.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			
			if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
				if (!isset($data['mobile']) && !in_array($_FILES['featured_image']['type'],$types)) :
					$errors->add('upload-error',esc_html__("Attachment Error! Please upload image only.","vbegy"));
					return $errors;
				endif;
				
				$featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form' => false),current_time('mysql'));
				
				if (isset($featured_image['error'])) :
					$errors->add('upload-error',esc_html__("Attachment Error: ","vbegy") . $featured_image['error']);
					return $errors;
				endif;
			endif;
		}
		
		$comment_message = askme_options("comment_message");
		if ($comment_message == 1) {
			if (empty($posted['comment'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (content).","vbegy"));
		}

		askme_check_captcha(askme_options("the_captcha_message"),"message",$posted,$errors);
		
		if (sizeof($errors->errors)>0) return $errors;
		$message_publish = askme_options("message_publish");
		
		/* Create message */

		$data = array(
			'post_content' => ($posted['comment']),
			'post_title'   => sanitize_text_field($posted['title']),
			'post_status'  => ($message_publish == "publish" || is_super_admin(get_current_user_id())?"publish":"draft"),
			'post_author'  => (!is_user_logged_in && $send_message_no_register == 1?0:get_current_user_id()),
			'post_type'	=> 'message',
		);
			
		$post_id = wp_insert_post($data);
			
		if ($post_id==0 || is_wp_error($post_id)) wp_die(__("Error in message.","vbegy"));
		
		/* Featured image */
		
		if (isset($featured_image['type']) && isset($featured_image['file'])) :
			$featured_image_data = array(
				'post_mime_type' => $featured_image['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($featured_image['file'])),
				'post_content'   => '',
				'post_status'	 => 'inherit',
				'post_author'    => $user_get_current_user_id,
			);
			$featured_image_id = wp_insert_attachment($featured_image_data,$featured_image['file'],$post_id);
			$featured_image_metadata = wp_generate_attachment_metadata($featured_image_id,$featured_image['file']);
			wp_update_attachment_metadata($featured_image_id,$featured_image_metadata);
			set_post_thumbnail($post_id,$featured_image_id);
		endif;
		
		if (!is_user_logged_in && $send_message_no_register == 1 && get_current_user_id() == 0) {
			$message_username = sanitize_text_field($posted['username']);
			$message_email = sanitize_text_field($posted['email']);
			update_post_meta($post_id,'message_username',$message_username);
			update_post_meta($post_id,'message_email',$message_email);
		}
		
		update_post_meta($post_id,'message_user_id',(int)$posted['user_id']);
		update_post_meta($post_id,'message_new',1);
		$new_messages_count = (int)get_user_meta((int)$message_add['user_id'],"askme_new_messages_count",true);
		$new_messages_count++;
		update_user_meta((int)$message_add['user_id'],"askme_new_messages_count",$new_messages_count);
		
		do_action('new_messages',$post_id);
	}
	if ($post_type == "send_message") {
		/* Successful */
		return $post_id;
	}
}
/* ask_message_view */
function ask_message_view() {
	global $post;
	$seen_message = askme_options("seen_message");
	$message_id = (int)$_POST["message_id"];
	$user_id = get_current_user_id();
	$the_query = new WP_Query(array("p" => $message_id,"post_type" => "message","meta_query" => array(array("key" => "message_user_id","compare" => "=","value" => $user_id))));
	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post_author = $post->post_author;
			$message_user_id = get_post_meta($message_id,'message_user_id',true);
			$message_new = get_post_meta($message_id,'message_new',true);
			if ($message_new == 1) {
				delete_post_meta($message_id,'message_new');
				$message_new = get_post_meta($post->ID,'message_new',true);
				$new_messages_count = (int)get_user_meta($user_id,"askme_new_messages_count",true);
				$new_messages_count--;
				update_user_meta($user_id,"askme_new_messages_count",$new_messages_count);
			}
			if ($seen_message == 1 && $message_new == 1 && $user_id == $message_user_id) {
				askme_notifications_activities($post_author,$message_user_id,"","","","seen_message","notifications","","message");
			}
			if (!isset($_POST["mobile"])) {
				$featured_image_message = askme_options("featured_image_message");
				if ($featured_image_message == 1) {
					$featured_image = get_post_meta($message_id,"_thumbnail_id",true);
					if ($featured_image != "") {
						$img_url = wp_get_attachment_url($featured_image,"full");
						if ($img_url != "") {
							$featured_image_message_lightbox = askme_options("featured_image_message_lightbox");
							$featured_image_message_width = askme_options("featured_image_message_width");
							$featured_image_message_height = askme_options("featured_image_message_height");
							$featured_image_message_width = ($featured_image_message_width != ""?$featured_image_message_width:260);
							$featured_image_message_height = ($featured_image_message_height != ""?$featured_image_message_height:185);
							$link_url = ($featured_image_message_lightbox == 1?$img_url:"");
							$last_image = askme_resize_img($featured_image_message_width,$featured_image_message_height,"",$featured_image);
							if (isset($last_image) && $last_image != "") {
								echo "<div class='featured_image_message'>".($link_url != ""?"<a href='".$link_url."'>":"").$last_image.($link_url != ""?"</a>":"")."</div>
								<div class='clearfix'></div>";
							}
						}
					}
				}
				echo "<div>".$post->post_content;
					do_action("askme_after_message_content",$message_id,$post_author,$user_id);
				echo "</div>";
			}
		}
	}
	wp_reset_postdata();
	if (!isset($_POST["mobile"])) {
		die(1);
	}
}
add_action( 'wp_ajax_ask_message_view', 'ask_message_view' );
add_action('wp_ajax_nopriv_ask_message_view','ask_message_view');
/* ask_message_reply */
function ask_message_reply() {
	$message_id = (int)$_POST["message_id"];
	$get_message = get_post($message_id);
	if (isset($get_message->ID) && $get_message->ID > 0) {
		$get_the_title = $get_message->post_title;
		$current_user = get_current_user_id();
		$message_user_id = get_post_meta($message_id,'message_user_id',true);
		if ($get_the_title != "" && $current_user > 0 && $current_user == $message_user_id) {
			echo str_ireplace(esc_html__("RE:","vbegy")." ".esc_html__("RE:","vbegy")." ".esc_html__("RE:","vbegy")." ",esc_html__("RE:","vbegy")." ".esc_html__("RE:","vbegy")." ",esc_html__("RE:","vbegy")." ".$get_the_title);
			if (!isset($_POST["mobile"])) {
				die(1);
			}
		}
	}
}
add_action( 'wp_ajax_ask_message_reply', 'ask_message_reply' );
add_action('wp_ajax_nopriv_ask_message_reply','ask_message_reply');
/* ask_block_message */
function ask_block_message() {
	$user_id      = (int)$_POST["user_id"];
	$current_user = get_current_user_id();
	
	$user_block_message = get_user_meta($current_user,"user_block_message",true);
	if (empty($user_block_message)) {
		update_user_meta(get_current_user_id(),"user_block_message",array($user_id));
	}else {
		update_user_meta(get_current_user_id(),"user_block_message",array_merge($user_block_message,array($user_id)));
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action( 'wp_ajax_ask_block_message', 'ask_block_message' );
add_action('wp_ajax_nopriv_ask_block_message','ask_block_message');
/* ask_unblock_message */
function ask_unblock_message() {
	$user_id      = (int)$_POST["user_id"];
	$current_user = get_current_user_id();
	
	$user_block_message = get_user_meta(get_current_user_id(),"user_block_message",true);
	$remove_user_block_message = remove_item_by_value($user_block_message,$user_id);
	update_user_meta(get_current_user_id(),"user_block_message",$remove_user_block_message);
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action( 'wp_ajax_ask_unblock_message', 'ask_unblock_message' );
add_action('wp_ajax_nopriv_ask_unblock_message','ask_unblock_message');
/* Delete messages */
function askme_delete_messages($post_id,$post_author,$user_get_current_user_id,$message_user_id) {
	if (($post_author > 0 && $post_author == $user_get_current_user_id) || $message_user_id == $user_get_current_user_id) {
		if ($post_author == $user_get_current_user_id || $message_user_id == $user_get_current_user_id) {
			askme_notifications_activities($user_get_current_user_id,"","","","",($message_user_id == $user_get_current_user_id?"delete_inbox_message":"delete_send_message"),"activities","","message");
			if ($post_author == $user_get_current_user_id) {
				update_post_meta($post_id,"delete_send_message",1);
			}else {
				update_post_meta($post_id,"delete_inbox_message",1);
			}
		}
		if (!isset($_GET["mobile"])) {
			$protocol = is_ssl() ? 'https' : 'http';
			$redirect_to = wp_unslash( $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			$redirect_to = (isset($_GET["page"]) && esc_attr($_GET["page"]) != ""?esc_attr($_GET["page"]):$redirect_to);
			if ( is_ssl() && force_ssl_admin() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )$secure_cookie = false; else $secure_cookie = '';
			wp_redirect((isset($_GET["show"]) && $_GET["show"] == "send"?esc_url(add_query_arg("show", "send"),get_page_link(askme_options('messages_page'))):esc_url(get_page_link(askme_options('messages_page')))));
			exit;
		}
	}
}?>