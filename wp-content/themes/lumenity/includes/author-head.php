<?php $user_login = askme_get_user_object($user_ID);
if (is_object($user_login)) {
	$get_query_var = $user_login->ID;
	$your_avatar = get_the_author_meta(askme_avatar_name(),$get_query_var);
	$url = get_the_author_meta('url',$get_query_var);
	$country = get_the_author_meta('country',$get_query_var);
	$city = get_the_author_meta('city',$get_query_var);
	$phone = get_the_author_meta('phone',$get_query_var);
	$sex = get_the_author_meta('sex',$get_query_var);
	$age = get_the_author_meta('age',$get_query_var);
	$twitter = get_the_author_meta('twitter',$get_query_var);
	$facebook = get_the_author_meta('facebook',$get_query_var);
	$tiktok = get_the_author_meta('tiktok',$get_query_var);
	$linkedin = get_the_author_meta('linkedin',$get_query_var);
	$follow_email = get_the_author_meta('follow_email',$get_query_var);
	$youtube = get_the_author_meta('youtube',$get_query_var);
	$pinterest = get_the_author_meta('pinterest',$get_query_var);
	$instagram = get_the_author_meta('instagram',$get_query_var);
	$show_point_favorite = get_the_author_meta('show_point_favorite',$get_query_var);
	$verified_user = get_the_author_meta('verified_user',$get_query_var);
}else {
	wp_redirect(home_url());
	die();
}

$owner = false;
if ($user_ID == $get_query_var) {
	$owner = true;
}
$get_current_user_id = get_current_user_id();
do_action("askme_action_on_user_page");

$ask_question_to_users = askme_options("ask_question_to_users");
$active_points = askme_options("active_points");
$pay_ask = askme_options("pay_ask");
$block_users = askme_options("block_users");

/* visit */
$meta_stats = askme_get_meta_stats();
$visit_profile = get_user_meta($get_query_var,"visit_profile_all",true);
if ($visit_profile > 0) {
	update_user_meta($get_query_var,$meta_stats,$visit_profile);
	delete_user_meta($get_query_var,"visit_profile_all");
}
$visit_profile = askme_get_post_stats(0,$get_query_var);
$visit_profile_m = get_user_meta($get_query_var,"visit_profile_m_".date_i18n('m_Y',current_time('timestamp')),true);
$visit_profile_d = get_user_meta($get_query_var,"visit_profile_d_".date_i18n('d_m_Y',current_time('timestamp')),true);

if ($visit_profile_d == "" or $visit_profile_d == 0) {
	add_user_meta($get_query_var,"visit_profile_d_".date_i18n('d_m_Y',current_time('timestamp')),1);
}else {
	update_user_meta($get_query_var,"visit_profile_d_".date_i18n('d_m_Y',current_time('timestamp')),$visit_profile_d+1);
}

if ($visit_profile_m == "" or $visit_profile_m == 0) {
	add_user_meta($get_query_var,"visit_profile_m_".date_i18n('m_Y',current_time('timestamp')),1);
}else {
	update_user_meta($get_query_var,"visit_profile_m_".date_i18n('m_Y',current_time('timestamp')),$visit_profile_m+1);
}

/* points */
$points = get_user_meta($get_query_var,"points",true);

/* favorites */
$_favorites = get_user_meta($get_query_var,$user_login->user_login."_favorites",true);

/* followed */
$following_questions = get_user_meta($get_query_var,"following_questions",true);

/* the_best_answer */
$the_best_answer = count(get_comments(array('user_id' => $get_query_var,"status" => "approve",'post_type' => array(ask_questions_type,ask_asked_questions_type),"meta_query" => array(array("key" => "best_answer_comment","compare" => "=","value" => "best_answer_comment")))));

/* following */
$following_me = get_user_meta($get_query_var,"following_me",true);
$following_you = get_user_meta($get_query_var,"following_you",true);

$block_users = askme_options("block_users");
$author__not_in = array();
if ($block_users == 1) {
	$user_id = $user_login->ID;
	if ($user_id > 0) {
		$get_block_users = get_user_meta($user_id,"askme_block_users",true);
		if (is_array($get_block_users) && !empty($get_block_users)) {
			$author__not_in = $get_block_users;
		}
	}
}

if (is_array($following_me) && !empty($following_me)) {
	$following_me = array_diff($following_me,$author__not_in);
}
if (is_array($following_you) && !empty($following_you)) {
	$following_you = array_diff($following_you,$author__not_in);
}

/* add_answer */
$date_month = array("year" => date("Y"),"month" => date("m"));
$date_day = array("year" => date("Y"),"month" => date("m"),"day" => date("d"));
$add_answer = get_user_meta($get_query_var,"add_answer_all",true);
$add_answer_m = count(get_comments(array("post_type" => array(ask_questions_type,ask_asked_questions_type),"status" => "approve","user_id" => $get_query_var,'date_query' => array($date_month))));
$add_answer_d = count(get_comments(array("post_type" => array(ask_questions_type,ask_asked_questions_type),"status" => "approve","user_id" => $get_query_var,'date_query' => array($date_month))));
$add_answer = count(get_comments(array("post_type" => array(ask_questions_type,ask_asked_questions_type),"status" => "approve","user_id" => $get_query_var)));

/* add_questions */
$add_questions = askme_count_posts_by_user($get_query_var,array(ask_questions_type,ask_asked_questions_type),"publish");
$add_questions_m = askme_count_posts_by_user($get_query_var,array(ask_questions_type,ask_asked_questions_type),"publish",0,$date_month);
$add_questions_d = askme_count_posts_by_user($get_query_var,array(ask_questions_type,ask_asked_questions_type),"publish",0,$date_day);

$args = array(
	"post_type" => ask_questions_type,
	"author" => $get_query_var,
	"meta_query" => array(array("key" => "question_poll","compare" => "=","value" => 1))
);
$the_query = new WP_Query($args);
$add_polls = $the_query->found_posts;
wp_reset_postdata();

/* paid_questions */
$paid_questions = count_paid_question_by_type($get_query_var);

/* asked_questions */
$asked_questions = count_asked_question_by_type($get_query_var,($owner == true?">=":">"));

/* add_comment */
$add_comment = count(get_comments(array("post_type" => "post","status" => "approve","user_id" => $get_query_var)));

/* follow questions - answers - posts - comments */
$follow_questions = 0;
$follow_answers = 0;
$follow_posts = 0;
$follow_comments = 0;
if (is_array($following_me) && !empty($following_me)) {
	$following_me_array = $following_me;
}
if (isset($following_me_array) && is_array($following_me_array) && !empty($following_me_array)) {
	foreach ($following_me_array as $key => $value) {
		$follow_questions += askme_count_posts_by_user($value,ask_questions_type,"publish");
		$follow_posts += askme_count_posts_by_user($value,"post","publish");
		$follow_answers += count(get_comments(array("post_type" => ask_questions_type,"status" => "approve","user_id" => $value)));
		$follow_comments += count(get_comments(array("post_type" => "post","status" => "approve","user_id" => $value)));
	}
}
?>
<div class="row">
	<div class="user-profile">
		<div class="col-md-12">
			<div class="page-content">
				<h2>
					<?php _e("About","vbegy")?> <a href="<?php echo vpanel_get_user_url($get_query_var);?>"><?php echo $user_login->display_name?></a>
					<?php if ($verified_user == 1) {
						echo '<img class="verified_user tooltip-n" alt="'.__("Verified","vbegy").'" original-title="'.__("Verified","vbegy").'" src="'.get_template_directory_uri().'/images/verified.png">';
					}
					echo vpanel_get_badge($get_query_var)?>
				</h2>
				<div class="user-profile-img">
					<a original-title="<?php echo $user_login->display_name?>" class="tooltip-n" href="<?php echo vpanel_get_user_url($get_query_var);?>">
						<?php echo askme_user_avatar($your_avatar,79,79,$get_query_var,$user_login->display_name);?>
					</a>
				</div>
				<div class="ul_list ul_list-icon-ok about-user">
					<?php
					$user_registered = askme_options("user_registered");
					$user_country = askme_options("user_country");
					$user_city = askme_options("user_city");
					$user_phone = askme_options("user_phone");
					$user_age = askme_options("user_age");
					$user_sex = askme_options("user_sex");
					$user_url = askme_options("user_url");
					if ($user_registered != 1 || $user_country != 1 || $user_city != 1 || $user_phone != 1 || $user_age != 1 || $user_sex != 1 || $user_url != 1) {?>
						<ul>
							<?php if ($user_registered != 1) {
								$date_format = askme_options("date_format");
								$register_date = explode(" ",$user_login->user_registered);
								if (isset($register_date[0]) && isset($register_date[1])) {
									$register_date_1 = explode("-",$register_date[0]);
									$register_date_2 = explode(":",$register_date[1]);
								}?>
								<li><i class="icon-plus"></i><strong><?php _e("Registered","vbegy")?>: </strong><span><?php echo (isset($register_date_1[0]) && isset($register_date_1[1]) && isset($register_date_1[2]) && isset($register_date_2[0]) && isset($register_date_2[1]) && isset($register_date_2[2])?date($date_format,mktime($register_date_2[0], $register_date_2[1], $register_date_2[2], $register_date_1[1], $register_date_1[2], $register_date_1[0])):substr($user_login->user_registered, 0, 10));?></span></li>
							<?php }
							if ($phone && $user_phone != 1) {?>
								<li><i class="icon-phone"></i><strong><?php _e("Phone","vbegy")?>: </strong><span><?php echo $phone?></span></li>
							<?php }
							$get_countries = vpanel_get_countries();
							if ($country && $user_country != 1 && isset($get_countries[$country])) {?>
								<li><i class="icon-map-marker"></i><strong><?php _e("Country","vbegy")?>: </strong><span><?php echo $get_countries[$country]?></span></li>
							<?php }
							$show_city_profile = apply_filters("askme_show_city_profile",true,$get_query_var);
							if ($show_city_profile == true && $city && $user_city != 1) {?>
								<li><i class="icon-map-marker"></i><strong><?php _e("City","vbegy")?>: </strong><span><?php echo $city?></span></li>
							<?php }
							if ($age && $user_age != 1) {?>
								<li><i class="icon-heart"></i><strong><?php _e("Age","vbegy")?>: </strong><span><?php echo $age?></span></li>
							<?php }
							if (isset($sex) && !empty($sex) && $user_sex != 1) {?>
								<li><i class="icon-user"></i><strong><?php _e("Gender","vbegy")?>: </strong><span><?php echo ($sex == "male" || $sex == 1?__("Male","vbegy"):($sex == "other" || $sex == 3?__("Other","vbegy"):__("Female","vbegy")))?></span></li>
							<?php }
							if ($url && $user_url != 1) {?>
								<li><i class="icon-globe"></i><strong><?php _e("Website","vbegy")?>: </strong><a target="_blank" href="<?php echo $url?>"><?php _e("view","vbegy")?></a></li>
							<?php }
							do_action("askme_author_page_li",$get_query_var);?>
						</ul>
					<?php }?>
				</div>
				<div class="clearfix"></div>
				<p><?php echo nl2br($user_login->description)?></p>
				<div class="clearfix"></div>
				<?php if ($owner == true) {
					$get_lang = esc_attr(get_query_var("lang"));
					$get_lang_array = array();
					if (isset($get_lang) && $get_lang != "") {
						$get_lang_array = array("lang" => $get_lang);
					}?>
					<a class="button color small margin_0" href="<?php echo esc_url(get_page_link(askme_options('user_edit_profile_page')))?>"><?php _e("Edit profile","vbegy")?></a>
				<?php }else {
					if ($ask_question_to_users == 1) {?>
						<form class="form_ask_user" method="get" action="<?php echo esc_url(get_page_link(askme_options('add_question')))?>">
							<button class="button color small"><?php echo esc_html__("Ask","vbegy")." ".get_the_author_meta("display_name",$get_query_var)?></button>
							<input type="hidden" name="user_id" value="<?php echo $get_query_var?>">
						</form>
					<?php }
					
					$active_message = askme_options("active_message");
					$send_message_no_register = askme_options("send_message_no_register");
					$received_message = esc_attr( get_the_author_meta( 'received_message', $get_query_var ) );
					$block_message = esc_attr( get_the_author_meta( 'block_message', $get_current_user_id ) );
					if ($active_message == 1) {
						$filter_message = apply_filters("askme_filter_send_message",true,$get_current_user_id);
						if ($filter_message == true) {
							$user_block_message = array();
							if (is_user_logged_in) {
								$user_block_message = get_user_meta($get_query_var,"user_block_message",true);
							}
							if (((!is_user_logged_in && $send_message_no_register == 1) || (is_user_logged_in && (empty($user_block_message) || (isset($user_block_message) && is_array($user_block_message) && !in_array($get_current_user_id,$user_block_message))) && ($block_message != 1 || is_super_admin($get_current_user_id)) && ($received_message == "" || $received_message == 1)))) {?>
								<a href="#" class="button color small form_message"><?php echo esc_html__("Message","vbegy")?></a>
							<?php }
							if (is_user_logged_in && !is_super_admin($get_query_var)) {
								$user_block_message = get_user_meta($get_current_user_id,"user_block_message",true);
								if (isset($user_block_message) && is_array($user_block_message) && in_array($get_query_var,$user_block_message)) {?>
									<a href="#" class="button color small block_message unblock_message" data-id="<?php echo (int)$get_query_var?>"><?php echo esc_html__("Unblock Message","vbegy")?></a>
								<?php }else {?>
									<a href="#" class="button color small block_message" data-id="<?php echo (int)$get_query_var?>"><?php echo esc_html__("Block Message","vbegy")?></a>
								<?php }
							}
						}
					}
					
					if (is_user_logged_in) {
						$following_me2 = get_user_meta($get_current_user_id,"following_me",true);
						$following_me2 = (is_array($following_me2)?$following_me2:array());
						if (isset($following_me2) && is_array($following_me2) && !empty($following_me2) and in_array($get_query_var,$following_me2)) {?>
							<a href="#" class="link_follow following_not button color small" rel="<?php echo $get_query_var?>" data-nonce="<?php echo wp_create_nonce("askme_following_nonce")?>"><?php _e("Unfollow","vbegy")?></a>
						<?php }else {?>
							<a href="#" class="link_follow following_you button color small" rel="<?php echo $get_query_var?>" data-nonce="<?php echo wp_create_nonce("askme_following_nonce")?>"><?php _e("Follow","vbegy")?></a>
						<?php }
					}

					$report_users = askme_options("report_users");
					if (is_user_logged_in && $report_users == 1 && $get_query_var > 0 && $get_query_var != $get_current_user_id) {?>
						<a href="<?php echo (int)$get_query_var?>" class="report_user button color small"><?php _e("Report user","vbegy")?></a>
						<div class="explain-reported">
							<h3><?php _e("Please briefly explain why you feel this user should be reported.","vbegy")?></h3>
							<textarea name="explain-reported"></textarea>
							<div class="clearfix"></div>
							<div class="loader_3"></div>
							<div class="color button small report"><?php _e("Report","vbegy")?></div>
							<div class="color button small dark_button cancel"><?php _e("Cancel","vbegy")?></div>
						</div><!-- End reported -->
					<?php }
					
					if ($block_users == 1 && !$owner && $get_current_user_id > 0) {
						$get_block_users = get_user_meta($get_current_user_id,"askme_block_users",true);
						echo '<div class="user_block'.(!empty($get_block_users) && in_array($get_query_var,$get_block_users)?" user_block_done":"").'">
							<div class="loader_3 user_block_loader"></div>';
							if (!empty($get_block_users) && in_array($get_query_var,$get_block_users)) {
								echo '<a href="#" class="unblock-user-page button color small" data-nonce="'.wp_create_nonce("block_nonce").'" data-rel="'.(int)$get_query_var.'" title="'.esc_attr__("Unblock","vbegy").'"><span class="block-value">'.esc_html__("Unblock","vbegy").'</span></a>';
							}else {
								echo '<a href="#" class="block-user-page button color small" data-rel="'.(int)$get_query_var.'" data-nonce="'.wp_create_nonce("block_nonce").'" title="'.esc_attr__("Block","vbegy").'"><span class="block-value">'.esc_html__("Block","vbegy").'</span></a>';
							}
						echo '</div>';
					}
				}?>
				<div class="clearfix"></div>
				<?php if ($facebook || $tiktok || $twitter || $linkedin || $follow_email || $youtube || $pinterest || $instagram) { ?>
					<br>
					<span class="user-follow-me"><?php _e("Follow Me","vbegy")?></span>
					<div class="social_icons social_icons_display">
						<?php if ($facebook) {?>
							<a href="<?php echo $facebook?>" original-title="<?php _e("Facebook","vbegy")?>" target="_blank" class="tooltip-n">
								<span class="icon_i">
									<span class="icon_square" icon_size="30" span_bg="#3b5997" span_hover="#2f3239">
										<i class="social_icon-facebook"></i>
									</span>
								</span>
							</a>
						<?php }
						if ($twitter) {?>
							<a href="<?php echo $twitter?>" original-title="<?php _e("Twitter","vbegy")?>" target="_blank" class="tooltip-n">
								<span class="icon_i">
									<span class="icon_square" icon_size="30" span_bg="#00baf0" span_hover="#2f3239">
										<i class="social_icon-twitter"></i>
									</span>
								</span>
							</a>
						<?php }
						if ($tiktok) {?>
							<a href="<?php echo $tiktok?>" original-title="<?php _e("TikTok","vbegy")?>" target="_blank" class="tooltip-n">
								<span class="icon_i">
									<span class="icon_square" icon_size="30" span_bg="#3b5997" span_hover="#2f3239">
										<i class="fab fa-tiktok"></i>
									</span>
								</span>
							</a>
						<?php }
						if ($linkedin) {?>
							<a href="<?php echo $linkedin?>" original-title="<?php _e("Linkedin","vbegy")?>" target="_blank" class="tooltip-n">
								<span class="icon_i">
									<span class="icon_square" icon_size="30" span_bg="#006599" span_hover="#2f3239">
										<i class="social_icon-linkedin"></i>
									</span>
								</span>
							</a>
						<?php }
						if ($pinterest) {?>
							<a href="<?php echo $pinterest?>" original-title="<?php _e("Pinterest","vbegy")?>" target="_blank" class="tooltip-n">
								<span class="icon_i">
									<span class="icon_square" icon_size="30" span_bg="#e13138" span_hover="#2f3239">
										<i class="social_icon-pinterest"></i>
									</span>
								</span>
							</a>
						<?php }
						if ($instagram) {?>
							<a href="<?php echo $instagram?>" original-title="<?php _e("Instagram","vbegy")?>" target="_blank" class="tooltip-n">
								<span class="icon_i">
									<span class="icon_square" icon_size="30" span_bg="#548bb6" span_hover="#2f3239">
										<i class="social_icon-instagram"></i>
									</span>
								</span>
							</a>
						<?php }
						if ($follow_email) {?>
							<a href="mailto:<?php echo $user_login->user_email?>" original-title="<?php _e("Email","vbegy")?>" class="tooltip-n">
								<span class="icon_i">
									<span class="icon_square" icon_size="30" span_bg="#000" span_hover="#2f3239">
										<i class="social_icon-email"></i>
									</span>
								</span>
							</a>
						<?php }?>
					</div>
				<?php }?>
			</div><!-- End page-content -->
		</div><!-- End col-md-12 -->
		<?php $user_profile_pages = askme_options("user_profile_pages");
		$user_profile_default = array(
			"questions"           => array("sort" => esc_html__('Questions','vbegy'),"value" => "questions"),
			"polls"               => array("sort" => esc_html__('Polls','vbegy'),"value" => "polls"),
			"answers"             => array("sort" => esc_html__('Answers','vbegy'),"value" => "answers"),
			"best-answers"        => array("sort" => esc_html__('Best Answers','vbegy'),"value" => "best-answers"),
			"asked-questions"     => array("sort" => esc_html__('Asked Questions','vbegy'),"value" => "asked-questions"),
			"paid-questions"      => array("sort" => esc_html__('Paid Questions','vbegy'),"value" => "paid-questions"),
			"followed"            => array("sort" => esc_html__('Followed Questions','vbegy'),"value" => "followed"),
			"favorites"           => array("sort" => esc_html__('Favorite Questions','vbegy'),"value" => "favorites"),
			"points"              => array("sort" => esc_html__('Points','vbegy'),"value" => "points"),
			"posts"               => array("sort" => esc_html__('Posts','vbegy'),"value" => "posts"),
			"comments"            => array("sort" => esc_html__('Comments','vbegy'),"value" => "comments"),
			"i_follow"            => array("sort" => esc_html__('Authors I Follow','vbegy'),"value" => "i_follow"),
			"followers"           => array("sort" => esc_html__('Followers','vbegy'),"value" => "followers"),
			"blocking"            => array("sort" => esc_html__('Blocking Users','vbegy'),"value" => "blocking"),
			"followers-questions" => array("sort" => esc_html__('Followers Questions','vbegy'),"value" => "followers-questions"),
			"followers-answers"   => array("sort" => esc_html__('Followers Answers','vbegy'),"value" => "followers-answers"),
			"followers-posts"     => array("sort" => esc_html__('Followers Posts','vbegy'),"value" => "followers-posts"),
			"followers-comments"  => array("sort" => esc_html__('Followers Comments','vbegy'),"value" => "followers-comments"),
		);
		$user_profile_pages = (is_array($user_profile_pages) && !empty($user_profile_pages)?$user_profile_pages:$user_profile_default);
		if (isset($user_profile_pages) && is_array($user_profile_pages)) {?>
			<div class="col-md-12">
				<div class="page-content page-content-user-profile">
					<div class="user-profile-widget">
						<h2><?php _e("User Stats","vbegy")?></h2>
						<div class="ul_list ul_list-icon-ok">
							<ul>
								<?php foreach ($user_profile_pages as $key => $value) {
									if ($key == "questions" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0") {?>
										<li><i class="icon-question-sign"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('question_user_page'))))?>"><?php _e("Questions","vbegy")?><span> ( <span><?php echo ($add_questions == ""?0:$add_questions)?></span> ) </span></a></li>
									<?php }else if ($key == "polls" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0") {?>
										<li><i class="icon-signal"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('polls_user_page'))))?>"><?php _e("Polls","vbegy")?><span> ( <span><?php echo ($add_polls == ""?0:$add_polls)?></span> ) </span></a></li>
									<?php }else if ($key == "answers" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0") {?>
										<li><i class="icon-comment"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('answer_user_page'))))?>"><?php _e("Answers","vbegy")?><span> ( <span><?php echo ($add_answer == ""?0:$add_answer)?></span> ) </span></a></li>
									<?php }else if ($ask_question_to_users == 1 && $key == "asked-questions" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0") {?>
										<li><i class="icon-question-sign"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('asked_question_user_page'))))?>"><?php _e("Asked Questions","vbegy")?><span> ( <span><?php echo ($asked_questions == ""?0:$asked_questions)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($key == "favorites" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li><i class="icon-star"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('favorite_user_page'))))?>"><?php _e("Favorite Questions","vbegy")?><span> ( <span><?php echo (is_array($_favorites)?count($_favorites):0)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($key == "followed" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li><i class="icon-question-sign"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('followed_user_page'))))?>"><?php _e("Followed Questions","vbegy")?><span> ( <span><?php echo (is_array($following_questions)?count($following_questions):0)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($active_points == 1 && $key == "points" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li><i class="icon-heart"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('point_user_page'))))?>"><?php _e("Points","vbegy")?><span> ( <span><?php echo (int)$points?></span> ) </span></a></li>
									<?php }else if ($key == "posts" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0") {?>
										<li><i class="icon-file-alt"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('post_user_page'))))?>"><?php _e("Posts","vbegy")?><span> ( <span><?php echo askme_count_posts_by_user($get_query_var,"post","publish")?></span> ) </span></a></li>
									<?php }else if ($key == "comments" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0") {?>
										<li><i class="icon-comment"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('comment_user_page'))))?>"><?php _e("Comments","vbegy")?><span> ( <span><?php echo ($add_comment == ""?0:$add_comment)?></span> ) </span></a></li>
									<?php }else if ($key == "best-answers" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0") {?>
										<li><i class="icon-asterisk"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('best_answer_user_page'))))?>"><?php _e("Best Answers","vbegy")?><span> ( <span><?php echo ($the_best_answer == ""?0:$the_best_answer)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($key == "i_follow" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li class="authors_follow"><i class="icon-user-md"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('i_follow_user_page'))))?>"><?php _e("Authors I Follow","vbegy")?><span> ( <span><?php echo (is_array($following_me)?count($following_me):0)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($key == "followers" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li class="followers"><i class="icon-user"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('followers_user_page'))))?>"><?php _e("Followers","vbegy")?><span> ( <span><?php echo (is_array($following_you)?count($following_you):0)?></span> ) </span></a></li>
									<?php }else if ($block_users == 1&& $owner == true && ($key == "blocking" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {
										$get_block_users = get_user_meta($get_query_var,"askme_block_users",true);?>
										<li class="blocking_user"><i class="icon-ban-circle"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('blocking_user_page'))))?>"><?php _e("Blocking Users","vbegy")?><span> ( <span><?php echo (is_array($get_block_users)?count($get_block_users):0)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($key == "followers-questions" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li class="follow_questions"><i class="icon-question-sign"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('follow_question_page'))))?>"><?php _e("Follow questions","vbegy")?><span> ( <span><?php echo esc_attr($follow_questions)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($key == "followers-answers" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li class="follow_answers"><i class="icon-comment"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('follow_answer_page'))))?>"><?php _e("Follow answers","vbegy")?><span> ( <span><?php echo esc_attr($follow_answers)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($key == "followers-posts" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li class="follow_posts"><i class="icon-file-alt"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('follow_post_page'))))?>"><?php _e("Follow posts","vbegy")?><span> ( <span><?php echo esc_attr($follow_posts)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($key == "followers-comments" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
									<li class="follow_comments"><i class="icon-comments"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('follow_comment_page'))))?>"><?php _e("Follow comments","vbegy")?><span> ( <span><?php echo esc_attr($follow_comments)?></span> ) </span></a></li>
									<?php }else if (($show_point_favorite == 1 || $owner == true) && ($pay_ask == 1 && $key == "paid-questions" && isset($value["value"]) && $value["value"] != "" && $value["value"] != "0")) {?>
										<li class="paid_question"><i class="icon-shopping-cart"></i><a href="<?php echo esc_url(add_query_arg("u", esc_attr($get_query_var),get_page_link(askme_options('paid_question'))))?>"><?php _e("Paid questions","vbegy")?><span> ( <span><?php echo esc_attr($paid_questions)?></span> ) </span></a></li>
									<?php }
								}?>
							</ul>
						</div>
					</div><!-- End user-profile-widget -->
				</div><!-- End page-content -->
			</div><!-- End col-md-12 -->
		<?php }?>
	</div><!-- End user-profile -->
</div><!-- End row -->
<div class="clearfix"></div>