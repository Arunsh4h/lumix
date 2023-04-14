<?php $settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
/* is_user_logged_in_data */
function is_user_logged_in_data ($user_links = array("profile" => 1,"messages" => 1,"questions" => 1,"asked_questions" => 1,"paid_questions" => 1,"answers" => 1,"favorite" => 1,"followed" => 1,"points" => 1,"i_follow" => 1,"followers" => 1,"posts" => 1,"follow_questions" => 1,"follow_answers" => 1,"follow_posts" => 1,"follow_comments" => 1,"edit_profile" => 1,"logout" => 1),$profile_widget = "") {
	$out = '';
	if (is_user_logged_in) {
		$user_login = get_userdata(get_current_user_id());
		$your_avatar = get_the_author_meta(askme_avatar_name(),$user_login->ID);
		$url = get_the_author_meta('url',$user_login->ID);
		$twitter = get_the_author_meta('twitter',$user_login->ID);
		$facebook = get_the_author_meta('facebook',$user_login->ID);
		$tiktok = get_the_author_meta('tiktok',$user_login->ID);
		$youtube = get_the_author_meta('youtube',$user_login->ID);
		$linkedin = get_the_author_meta('linkedin',$user_login->ID);
		$follow_email = get_the_author_meta('follow_email',$user_login->ID);
		$verified_user = get_the_author_meta('verified_user',$user_login->ID);
		$out .= '<div class="row">';
			if ($profile_widget != "on") {
				$out .= '<div class="col-md-8">
					<div class="is-login-left user-profile-img">
						<a original-title="'.$user_login->display_name.'" class="tooltip-n" href="'.vpanel_get_user_url($user_login->ID).'">
							'.askme_user_avatar($your_avatar,79,79,$user_login->ID,$user_login->display_name).'
						</a>
					</div>
					<div class="is-login-right">
						<h2>'.__("Welcome","vbegy").' '.$user_login->display_name.($verified_user == 1?'<img class="verified_user tooltip-n" alt="'.__("Verified","vbegy").'" original-title="'.__("Verified","vbegy").'" src="'.get_template_directory_uri().'/images/verified.png">':'').vpanel_get_badge($user_login->ID).'</h2>';
						if (isset($user_login->description) && $user_login->description != "") {
							$out .= '<p>'.nl2br($user_login->description).'</p>';
						}
						if ($youtube || $facebook || $tiktok || $twitter || $linkedin || $follow_email) {
							$out .= '<div class="social_icons social_icons_display">';
								if ($facebook) {
									$out .= '<a href="'.$facebook.'" original-title="'.__("Facebook","vbegy").'" class="tooltip-n">
										<span class="icon_i">
											<span class="icon_square" icon_size="30" span_bg="#3b5997">
												<i class="social_icon-facebook"></i>
											</span>
										</span>
									</a>';
								}
								if ($twitter) {
									$out .= '<a href="'.$twitter.'" original-title="'.__("Twitter","vbegy").'" class="tooltip-n">
										<span class="icon_i">
											<span class="icon_square" icon_size="30" span_bg="#00baf0">
												<i class="social_icon-twitter"></i>
											</span>
										</span>
									</a>';
								}
								if ($tiktok) {
									$out .= '<a href="'.$tiktok.'" original-title="'.__("TikTok","vbegy").'" class="tooltip-n">
										<span class="icon_i">
											<span class="icon_square" icon_size="30" span_bg="#3b5997">
												<i class="fab fa-tiktok"></i>
											</span>
										</span>
									</a>';
								}
								if ($youtube) {
									$out .= '<a href="'.$youtube.'" original-title="'.__("Youtube","vbegy").'" class="tooltip-n">
										<span class="icon_i">
											<span class="icon_square" icon_size="30" span_bg="#c4302b">
												<i class="social_icon-youtube"></i>
											</span>
										</span>
									</a>';
								}
								if ($linkedin) {
									$out .= '<a href="'.$linkedin.'" original-title="'.__("Linkedin","vbegy").'" class="tooltip-n">
										<span class="icon_i">
											<span class="icon_square" icon_size="30" span_bg="#006599">
												<i class="social_icon-linkedin"></i>
											</span>
										</span>
									</a>';
								}
								if ($follow_email) {
									$out .= '<a href="mailto:'.$user_login->user_email.'" original-title="'.__("Email","vbegy").'" class="tooltip-n">
										<span class="icon_i">
											<span class="icon_square" icon_size="30" span_bg="#000">
												<i class="social_icon-email"></i>
											</span>
										</span>
									</a>';
								}
							$out .= '</div>';
						}
					$out .= '</div>
				</div>';
			}
			
			$get_lang = esc_attr(get_query_var("lang"));
			$get_lang_array = array();
			if (isset($get_lang) && $get_lang != "") {
				$get_lang_array = array("lang" => $get_lang);
			}
			
			$out .= '<div class="'.($profile_widget != "on"?"col-md-4":"col-md-12").'">';
				$active_points = askme_options("active_points");
				if (isset($user_links) && is_array($user_links) && ((isset($user_links["profile"]) && ($user_links["profile"] == 1 || $user_links["profile"] == "on")) || (isset($user_links["messages"]) && ($user_links["messages"] == 1 || $user_links["messages"] == "on")) || (isset($user_links["questions"]) && ($user_links["questions"] == 1 || $user_links["questions"] == "on")) || (isset($user_links["polls"]) && ($user_links["polls"] == 1 || $user_links["polls"] == "on")) || (isset($user_links["best_answers"]) && ($user_links["best_answers"] == 1 || $user_links["best_answers"] == "on")) || (isset($user_links["asked_questions"]) && ($user_links["asked_questions"] == 1 || $user_links["asked_questions"] == "on")) || (isset($user_links["paid_questions"]) && ($user_links["paid_questions"] == 1 || $user_links["paid_questions"] == "on")) || (isset($user_links["answers"]) && ($user_links["answers"] == 1 || $user_links["answers"] == "on")) || (isset($user_links["favorite"]) && ($user_links["favorite"] == 1 || $user_links["favorite"] == "on")) || (isset($user_links["followed"]) && ($user_links["followed"] == 1 || $user_links["followed"] == "on")) || (isset($user_links["points"]) && ($user_links["points"] == 1 || $user_links["points"] == "on")) || (isset($user_links["i_follow"]) && ($user_links["i_follow"] == 1 || $user_links["i_follow"] == "on")) || (isset($user_links["followers"]) && ($user_links["followers"] == 1 || $user_links["followers"] == "on")) || (isset($user_links["posts"]) && ($user_links["posts"] == 1 || $user_links["posts"] == "on")) || (isset($user_links["follow_questions"]) && ($user_links["follow_questions"] == 1 || $user_links["follow_questions"] == "on")) || (isset($user_links["follow_answers"]) && ($user_links["follow_answers"] == 1 || $user_links["follow_answers"] == "on")) || (isset($user_links["follow_posts"]) && ($user_links["follow_posts"] == 1 || $user_links["follow_posts"] == "on")) || (isset($user_links["follow_comments"]) && ($user_links["follow_comments"] == 1 || $user_links["follow_comments"] == "on")) || (isset($user_links["edit_profile"]) && ($user_links["edit_profile"] == 1 || $user_links["edit_profile"] == "on")) || (isset($user_links["logout"]) && ($user_links["logout"] == 1 || $user_links["logout"] == "on")))) {
					if ($profile_widget != "on") {
						$out .= '<h2>'.__("Quick Links","vbegy").'</h2>';
					}
					$out .= '<ul class="user_quick_links">';
						if (isset($user_links) && is_array($user_links) && ((isset($user_links["profile"]) && ($user_links["profile"] == 1 || $user_links["profile"] == "on")) || (isset($user_links["messages"]) && ($user_links["messages"] == 1 || $user_links["messages"] == "on")) || (isset($user_links["questions"]) && ($user_links["questions"] == 1 || $user_links["questions"] == "on")) || (isset($user_links["polls"]) && ($user_links["polls"] == 1 || $user_links["polls"] == "on")) || (isset($user_links["best_answers"]) && ($user_links["best_answers"] == 1 || $user_links["best_answers"] == "on")) || (isset($user_links["asked_questions"]) && ($user_links["asked_questions"] == 1 || $user_links["asked_questions"] == "on")) || (isset($user_links["answers"]) && ($user_links["answers"] == 1 || $user_links["answers"] == "on")) || (isset($user_links["favorite"]) && ($user_links["favorite"] == 1 || $user_links["favorite"] == "on")) || (isset($user_links["followed"]) && ($user_links["followed"] == 1 || $user_links["followed"] == "on")) || (isset($user_links["points"]) && ($user_links["points"] == 1 || $user_links["points"] == "on")) || (isset($user_links["i_follow"]) && ($user_links["i_follow"] == 1 || $user_links["i_follow"] == "on")) || (isset($user_links["followers"]) && ($user_links["followers"] == 1 || $user_links["followers"] == "on")))) {
							if (isset($user_links) && is_array($user_links) && (isset($user_links["profile"]) && ($user_links["profile"] == 1 || $user_links["profile"] == "on"))) {
								$out .= '<li><a href="'.vpanel_get_user_url($user_login->ID).'"><i class="icon-home"></i>'.__("Profile page","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["messages"]) && ($user_links["messages"] == 1 || $user_links["messages"] == "on"))) {
								$out .= '<li><a href="'.esc_url(get_page_link(askme_options('messages_page'))).'"><i class="icon-envelope-alt"></i>'.__("Messages","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["questions"]) && ($user_links["questions"] == 1 || $user_links["questions"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('question_user_page')))).'"><i class="icon-question-sign"></i>'.__("Questions","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["polls"]) && ($user_links["polls"] == 1 || $user_links["polls"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('polls_user_page')))).'"><i class="icon-question-sign"></i>'.__("Polls","vbegy").'</a></li>';
							}
							$ask_question_to_users = askme_options("ask_question_to_users");
							if ($ask_question_to_users == 1 && isset($user_links) && is_array($user_links) && (isset($user_links["asked_questions"]) && ($user_links["asked_questions"] == 1 || $user_links["asked_questions"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('asked_question_user_page')))).'"><i class="icon-question-sign"></i>'.__("Asked Questions","vbegy").'</a></li>';
							}
							$pay_ask = askme_options("pay_ask");
							if ($pay_ask == 1 && isset($user_links) && is_array($user_links) && (isset($user_links["paid_questions"]) && ($user_links["paid_questions"] == 1 || $user_links["paid_questions"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg("u", esc_attr($user_login->ID),get_page_link(askme_options('paid_question')))).'"><i class="icon-shopping-cart"></i>'.__("Paid questions","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["answers"]) && ($user_links["answers"] == 1 || $user_links["answers"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('answer_user_page')))).'"><i class="fa fa-comments"></i>'.__("Answers","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["best_answers"]) && ($user_links["best_answers"] == 1 || $user_links["best_answers"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('best_answer_user_page')))).'"><i class="fa fa-comments"></i>'.__("Best Answers","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["favorite"]) && ($user_links["favorite"] == 1 || $user_links["favorite"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('favorite_user_page')))).'"><i class="icon-star"></i>'.__("Favorite Questions","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["followed"]) && ($user_links["followed"] == 1 || $user_links["followed"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('followed_user_page')))).'"><i class="icon-question-sign"></i>'.__("Followed Questions","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["points"]) && ($user_links["points"] == 1 || $user_links["points"] == "on")) && $active_points == 1) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('point_user_page')))).'"><i class="icon-heart"></i>'.__("Points","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["i_follow"]) && ($user_links["i_follow"] == 1 || $user_links["i_follow"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('i_follow_user_page')))).'"><i class="icon-user-md"></i>'.__("Authors I Follow","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["followers"]) && ($user_links["followers"] == 1 || $user_links["followers"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('followers_user_page')))).'"><i class="icon-user"></i>'.__("Followers","vbegy").'</a></li>';
							}
						}
						if (isset($user_links) && is_array($user_links) && ((isset($user_links["posts"]) && ($user_links["posts"] == 1 || $user_links["posts"] == "on")) || (isset($user_links["comments"]) && ($user_links["comments"] == 1 || $user_links["comments"] == "on")) || (isset($user_links["follow_questions"]) && ($user_links["follow_questions"] == 1 || $user_links["follow_questions"] == "on")) || (isset($user_links["follow_answers"]) && ($user_links["follow_answers"] == 1 || $user_links["follow_answers"] == "on")) || (isset($user_links["follow_posts"]) && ($user_links["follow_posts"] == 1 || $user_links["follow_posts"] == "on")) || (isset($user_links["follow_comments"]) && ($user_links["follow_comments"] == 1 || $user_links["follow_comments"] == "on")) || (isset($user_links["edit_profile"]) && ($user_links["edit_profile"] == 1 || $user_links["edit_profile"] == "on")) || (isset($user_links["logout"]) && ($user_links["logout"] == 1 || $user_links["logout"] == "on")))) {
							if (isset($user_links) && is_array($user_links) && (isset($user_links["posts"]) && ($user_links["posts"] == 1 || $user_links["posts"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('post_user_page')))).'"><i class="icon-file-alt"></i>'.__("Posts","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["comments"]) && ($user_links["comments"] == 1 || $user_links["comments"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('comment_user_page')))).'"><i class="fa fa-comments"></i>'.__("Comments","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["follow_questions"]) && ($user_links["follow_questions"] == 1 || $user_links["follow_questions"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('follow_question_page')))).'"><i class="icon-question-sign"></i>'.__("Follow questions","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["follow_answers"]) && ($user_links["follow_answers"] == 1 || $user_links["follow_answers"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('follow_answer_page')))).'"><i class="fa fa-comments"></i>'.__("Follow answers","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["follow_posts"]) && ($user_links["follow_posts"] == 1 || $user_links["follow_posts"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('follow_post_page')))).'"><i class="icon-file-alt"></i>'.__("Follow posts","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["follow_comments"]) && ($user_links["follow_comments"] == 1 || $user_links["follow_comments"] == "on"))) {
								$out .= '<li><a href="'.esc_url(add_query_arg(array_merge(array("u" => esc_attr($user_login->ID),$get_lang_array)),get_page_link(askme_options('follow_comment_page')))).'"><i class="fa fa-comments"></i>'.__("Follow comments","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["activity_log"]) && ($user_links["activity_log"] == 1 || $user_links["activity_log"] == "on"))) {
								$out .= '<li><a href="'.esc_url(get_page_link(askme_options('activity_log_page'))).'"><i class="fas fa-thumbtack"></i>'.__("Activity log","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["edit_profile"]) && ($user_links["edit_profile"] == 1 || $user_links["edit_profile"] == "on"))) {
								$out .= '<li><a href="'.esc_url(get_page_link(askme_options('user_edit_profile_page'))).'"><i class="icon-pencil"></i>'.__("Edit profile","vbegy").'</a></li>';
							}
							if (isset($user_links) && is_array($user_links) && (isset($user_links["logout"]) && ($user_links["logout"] == 1 || $user_links["logout"] == "on"))) {
								$protocol = is_ssl() ? 'https' : 'http';
								$out .= '<li><a href="'.askme_get_logout().'"><i class="icon-signout"></i>'.__("Logout","vbegy").'</a></li>';
							}
						}
					$out .= '</ul>';
				}
			$out .= '</div><!-- End col-md-4 -->
		</div><!-- End row -->';
	}else {
		$out .= '<div class="form-style form-style-3">
			'.do_shortcode("[ask_login]").'
		</div>';
	}
	return $out;
}
/* Login shortcode */
function ask_login ($atts, $content = null) {
	global $user_identity,$user_ID;
	$protocol = is_ssl() ? 'https' : 'http';
	$a = shortcode_atts( array(
	    'forget' => 'forget',
	    'register' => '',
	    'register_2' => '',
	), $atts );
	$out = '';
	if (is_user_logged_in) :
		$user_login = get_userdata(get_current_user_id());
		$out .= is_user_logged_in_data(askme_options("user_links"));
	else:
		$ajax_file = askme_options("ajax_file");
		$ajax_file = ($ajax_file == "theme"?get_template_directory_uri().'/includes/ajax.php':admin_url("admin-ajax.php"));
		$out .= do_action('askme_social_login').do_action('oa_social_login').do_action('miniorange_social_login').(shortcode_exists('wordpress_social_login')?'<div class="clearfix"></div><br>'.do_shortcode("[wordpress_social_login]"):"").(shortcode_exists('apsl-login')?'<div class="clearfix"></div><br>'.do_shortcode("[apsl-login]"):"").(shortcode_exists('apsl-login-lite')?'<div class="clearfix"></div><br>'.do_shortcode("[apsl-login-lite]"):"").(shortcode_exists('nextend_social_login')?'<div class="clearfix"></div><br>'.do_shortcode("[nextend_social_login]"):"").'<div class="ask_form inputs">
			<form class="login-form ask_login" action="'.home_url('/').'" method="post">
				<div class="ask_error"></div>
				
				<div class="form-inputs clearfix">
					<p class="login-text">
						<input class="required-item" type="text" placeholder="'.__("Username","vbegy").'" name="log">
						<i class="icon-user"></i>
					</p>
					<p class="login-password">
						<input class="required-item" type="password" placeholder="'.__("Password","vbegy").'" name="pwd">
						<i class="icon-lock"></i>
						'.(isset($a["forget"]) && $a["forget"] == "false"?'':'<a href="#">'.__("Forget","vbegy").'</a>').'
					</p>
					'.askme_add_captcha(askme_options("the_captcha_login"),"login",rand(1,1000)).'
				</div>
				
				<p class="form-submit login-submit">
					<span class="loader_2"></span>
					<input type="submit" value="'.__("Log in","vbegy").'" class="button color small login-submit submit sidebar_submit">
					'.(isset($a["register"]) && $a["register"] == "button"?'<input type="button" class="signup button color small submit sidebar_submit" value="'.__("Register","vbegy").'">':'').'
				</p>
				
				<div class="rememberme">
					<label><input type="checkbox"input name="rememberme" value="forever" checked="checked"> '.__("Remember Me","vbegy").'</label>
				</div>
				
				<input type="hidden" name="redirect_to" value="'.wp_unslash( $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'">
				<input type="hidden" name="ajax_url" value="'.$ajax_file.'">
				<input type="hidden" name="form_type" value="ask-login">
				<div class="errorlogin"></div>
			</form>
		</div>'.(isset($a["register_2"]) && $a["register_2"] == "yes"?"<ul class='login-links login-links-r'><li><a href='#'>".__("Register","vbegy")."</a></li></ul>":"");
	endif;
	return $out;
}
function ask_login_shortcode() {
	add_shortcode("ask_login","ask_login");
}
add_action("init","ask_login_shortcode");
add_filter("the_content","do_shortcode");
add_filter("widget_text","do_shortcode");
function ask_login_jquery() {
	if (isset($_REQUEST['redirect_to'])) {
		$redirect_to = esc_url_raw($_REQUEST['redirect_to']);
	}
	$after_login = askme_options("after_login");
	$after_login_link = askme_options("after_login_link");
	
	if ( is_ssl() && force_ssl_admin() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )$secure_cookie = false; else $secure_cookie = '';
	
	// Check the username
	if ( !isset($_POST['log']) ) :
		$user = new WP_Error();
		$user->add('empty_username', __('<strong>Error :&nbsp;</strong>please insert your name .','vbegy'));
	elseif ( !isset($_POST['pwd']) ) :
		$error = new WP_Error();
		$user->add('empty_username', __('<strong>Error :&nbsp;</strong>please insert your password .','vbegy'));
	endif;
	
	$data                  = array();
	$data['user_login']    = $_POST['log'];
	$data['user_password'] = $_POST['pwd'];
	$data['remember']      = (isset($_POST['rememberme'])?$_POST['rememberme']:'');
	$secure_cookie         = is_ssl() ? true : false;

	$user = wp_signon($data,$secure_cookie);
	if (isset($_REQUEST['redirect_to']) && $after_login == "same_page") {
		$redirect_to = esc_url_raw($_REQUEST['redirect_to']);
	}else if (isset($user->ID) && $user->ID > 0 && $after_login == "profile") {
		$redirect_to = vpanel_get_user_url($user->ID);
	}else if ($after_login == "custom_link") {
		$redirect_to = esc_url($after_login_link);
	}else {
		$redirect_to = esc_url(home_url('/'));
	}

	if (isset($user->ID)) {
		do_action('wp_login',$user->user_login,$user);
		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID,true);
	}

	if (ask_is_ajax()) :
		// Result
		$result = array();
		if ( !is_wp_error($user) ) :
			$result['success'] = 1;
			$result['redirect'] = $redirect_to;
		else :
			$result['success'] = 0;
			foreach ($user->errors as $error) {
				$result['error'] = $error[0];
				break;
			}
		endif;
		echo json_encode($result);
		die();
	else :
		if ( !is_wp_error($user) ) :
			wp_redirect($redirect_to);
			exit;
		endif;
	endif;
	return $user;
}
if (!function_exists('ask_is_ajax')) {
	function ask_is_ajax() {
		if (defined('DOING_AJAX')) return true;
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') return true;
		return false;
	}
}
function ask_login_process() {
	global $ask_login_errors;
	if (isset($_POST['login-form']) && $_POST['login-form']) :
		$ask_login_errors = ask_login_jquery();
	endif;
}
add_action('init','ask_login_process');
function ask_ajax_login_process() {
	ask_login_jquery();
	die();
}
add_action('wp_ajax_ask_ajax_login_process','ask_ajax_login_process');
add_action('wp_ajax_nopriv_ask_ajax_login_process','ask_ajax_login_process');
/* Lostpassword shortcode */
add_shortcode('ask_lost_pass', 'ask_lost_pass');
function ask_lost_pass($atts, $content = null) {
	global $user_identity;
	$a = shortcode_atts( array(
	    'dark_button' => '',
	), $atts );
	$out = '';
	if (is_user_logged_in) :
		$user_login = get_userdata(get_current_user_id());
		$out .= is_user_logged_in_data(askme_options("user_links"));
	else:
		do_action('ask_lost_password');
		$rand_w = rand(1,1000);
		$out .= '
		<form method="post" class="ask-lost-password ask_form" action="">
			<div class="ask_error"></div>
			<div class="form-inputs clearfix">
				<p>
					<label for="user_mail_'.$rand_w.'" class="required">'.__("E-Mail","vbegy").'<span>*</span></label>
					<input type="email" class="required-item" name="user_mail" id="user_mail_'.$rand_w.'">
				</p>
				'.askme_add_captcha(askme_options("the_captcha_password"),"password",$rand_w).'
			</div>
			<p class="form-submit">
				<input type="submit" value="'.__("Reset","vbegy").'" class="button color '.(isset($a["dark_button"]) && $a["dark_button"] == "dark_button"?"dark_button":"").' small submit">
				<input type="hidden" name="form_type" value="ask-forget">
			</p>
		</form>';
	endif;
	return $out;
}
function ask_process_lost_pass($data) {
	$data = apply_filters("askme_forgot_password_data",$data);
	$errors = new WP_Error();
	$fields = array('user_mail','form_type','ask_captcha');
	foreach ($fields as $field) :
		if (isset($data[$field])) $data[$field] = $data[$field]; else $data[$field] = '';
	endforeach;
	$data = array_map('stripslashes', $data);
	if ( !isset($data['mobile']) && is_user_logged_in ) :
		$user_id = get_current_user_id();
		$errors->add('already_logged', sprintf(wp_kses(__("You are already logged in, If you want to change your password go to <a href='%s'>edit profile</a>.","vbegy"),array('a' => array('href' => array()))),esc_url(get_page_link(askme_options('user_edit_profile_page')))));
	elseif ( !isset($data['user_mail']) ) :
		$errors->add('empty_email', sprintf(esc_html__('Please insert your email.','vbegy'),'<strong>','</strong>'));
	elseif ( !email_exists($data['user_mail']) ) :
		$errors->add('invalid_email', sprintf(esc_html__('There is no user registered with that email address.','vbegy'),'<strong>','</strong>'));
	elseif (isset($data['user_mail']) && $data['user_mail'] != "") :
		$get_user_by_mail = get_user_by('email',esc_html($data['user_mail']));
		if (!isset($get_user_by_mail->ID)) :
			$errors->add('invalid_email', sprintf(esc_html__('There is no user registered with that email address.','vbegy'),'<strong>','</strong>'));
		endif;
	endif;

	askme_check_captcha(askme_options("the_captcha_password"),"password",$data,$errors);
	
	if ( $errors->get_error_code() ) return $errors;
	if ($_POST['form_type']) {
		unset($_POST["form_type"]);
	}

	$user_data = array();
	$user_data["get_user_id"] = $get_user_by_mail->ID;
	$user_data["user_mail"] = $data['user_mail'];
	if (isset($data['user_email'])) {
		$user_data["user_email"] = $data['user_email'];
	}
	$user_data["display_name"] = $get_user_by_mail->display_name;
	$user_data = apply_filters("askme_forgot_password_user_data",$user_data);
	$rand_a = askme_token(15);
	$get_reset_password = get_user_meta($user_data["get_user_id"],"reset_password",true);
	if ($get_reset_password == "") :
		update_user_meta($user_data["get_user_id"],"reset_password",$rand_a);
		$get_reset_password = $rand_a;
	endif;
	$confirm_link_email = esc_url_raw(add_query_arg(array("u" => $user_data["get_user_id"],"reset_password" => $get_reset_password),esc_url(home_url('/'))));
	$send_text = askme_send_mail(
		array(
			'content'            => askme_options("email_new_password"),
			'user_id'            => $user_data["get_user_id"],
			'confirm_link_email' => $confirm_link_email,
		)
	);
	$email_title = askme_options("title_new_password");
	$email_title = ($email_title != ""?$email_title:esc_html__("Reset your password","vbegy"));
	$email_title = askme_send_mail(
		array(
			'content'            => $email_title,
			'title'              => true,
			'break'              => '',
			'user_id'            => $user_data["get_user_id"],
			'confirm_link_email' => $confirm_link_email,
		)
	);
	askme_send_mails(
		array(
			'toEmail'     => esc_html((isset($user_data["user_email"])?$user_data["user_email"]:$user_data["user_mail"])),
			'toEmailName' => esc_html($user_data["display_name"]),
			'title'       => $email_title,
			'message'     => $send_text,
		)
	);
	return;
}
function ask_lost_pass_word() {
	if (isset($_POST['form_type']) && $_POST['form_type'] == "ask-forget") :
		$return = ask_process_lost_pass($_POST);
		if ( is_wp_error($return) ) :
   			echo '<div class="ask_error"><strong>'.__("Error","vbegy").':&nbsp;'.$return->get_error_message().'</strong></div>';
   		else :
   			echo '<div class="ask_done"><strong>'.__("Check your email please.","vbegy").'</strong></div>';
   		endif;
	endif;
}
add_action('ask_lost_password', 'ask_lost_pass_word');
/* Change password email */
add_filter("send_password_change_email","askme_password_changed",1,2);
function askme_password_changed($return,$user) {
	if (isset($user["ID"])) {
		update_user_meta($user["ID"],"password_changed","changed");
	}
	return false;
}
/* hex2rgb */
function hex2rgb ($hex) {
   $hex = str_replace("#","",$hex);
   if (strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   }else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b);
   return $rgb;
}
/* ask_sanitize_user */ 
function ask_sanitize_user ($username, $raw_username, $strict) {
	if (!$strict) {
		return $username;
	}
	return sanitize_user(stripslashes($raw_username),false);
}
add_filter ('sanitize_user', 'ask_sanitize_user', 10, 3);
/* Get logout url */
function askme_get_logout() {
	$after_logout = askme_options("after_logout");
	$after_logout_link = askme_options("after_logout_link");
	$protocol = is_ssl() ? 'https' : 'http';
	if ($after_logout == "same_page") {
		$redirect_to = $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}else if ($after_logout == "custom_link" && $after_logout_link != "") {
		$redirect_to = esc_url($after_logout_link);
	}else {
		$redirect_to = esc_url(home_url('/'));
	}
	$url = esc_url(wp_logout_url(wp_unslash($redirect_to)));
	return apply_filters('askme_filter_get_logout',$url);
}
/* Profile logout */
add_action('askme_action_get_logout','askme_action_get_logout');
if (!function_exists('askme_action_get_logout')) :
	function askme_action_get_logout() {
		echo askme_get_logout();
	}
endif;
/* Stop sent WordPress mail */
add_action('init','askme_send_new_user_notifications');
function askme_send_new_user_notifications() {
	remove_action('register_new_user','wp_send_new_user_notifications');
}?>