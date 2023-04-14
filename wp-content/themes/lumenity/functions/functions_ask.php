<?php
ob_start();
function ask_members_only() {
	if (!is_user_logged_in) ask_redirect_login();
}
/* Fix the counter */
function askme_confirm_fix_comments() {
	$post_id = (int)$_POST["post_id"];
	$count_post_comments = askme_comment_counter($post_id); // The parents only
	$count_post_all = askme_comment_counter($post_id,1); // With child comments too
	update_post_meta($post_id,"count_post_all",($count_post_all < 0?0:$count_post_all));
	update_post_meta($post_id,"count_post_comments",($count_post_comments < 0?0:$count_post_comments));
	update_post_meta($post_id,"comment_count",($count_post_all < 0?0:$count_post_all));
	die();
}
add_action('wp_ajax_askme_confirm_fix_comments','askme_confirm_fix_comments');
/* vpanel_media_library */
add_action('pre_get_posts','vpanel_media_library');
function vpanel_media_library($wp_query_obj) {
	global $current_user,$pagenow;
	if (!is_a($current_user,'WP_User') || is_super_admin($current_user->ID))
		return;
	if ('admin-ajax.php' != $pagenow || $_REQUEST['action'] != 'query-attachments')
		return;
	if (!current_user_can('manage_media_library'))
		$wp_query_obj->set('author',$current_user->ID);
	return;
}
/* question_poll */
function askme_question_poll($data = array()) {
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$post_id = (int)$data['post_id'];
	$user_id = get_current_user_id();
	$poll_user_only = askme_options("poll_user_only");
	if (!is_user_logged_in() && $poll_user_only == 1) {
		$poll_error = "must_login";
	}else {
		$poll_id       = (int)$data['poll_id'];
		$question_poll = get_post_meta($post_id,'askme_question_poll',true);
		$question_poll = (is_array($question_poll) && !empty($question_poll)?$question_poll:array());
		$no_poll       = "";
		
		$asks = get_post_meta($post_id,"ask",true);
		
		$askme_poll = get_post_meta($post_id,"askme_poll",true);
		$askme_poll = (is_array($askme_poll) && !empty($askme_poll)?$askme_poll:array());
		
		if (isset($asks)) {
			foreach ($asks as $key_ask => $value_ask) {
				$askme_poll[$key_ask] = array(
					"id"       => (int)$asks[$key_ask]["id"],
					"title"    => $value_ask["title"],
					"value"    => (isset($asks[$key_ask]["value"]) && $asks[$key_ask]["value"] != ""?$asks[$key_ask]["value"]:(isset($askme_poll[$key_ask]["value"])?$askme_poll[$key_ask]["value"]:0)),
					"user_ids" => (isset($asks[$key_ask]["user_ids"])?$asks[$key_ask]["user_ids"]:(isset($askme_poll[$key_ask]["user_ids"])?$askme_poll[$key_ask]["user_ids"]:array()))
				);
				if (is_array($value_ask) && isset($value_ask["id"]) && $value_ask["id"] == $poll_id) {
					$found_key = $key_ask;
				}
			}
		}
		$needle     = (isset($found_key) && isset($askme_poll[$found_key])?$askme_poll[$found_key]:array());
		$poll_title = (isset($needle["title"])?$needle["title"]:"");
		$value      = (isset($needle["value"])?$needle["value"]:"");
		$user_ids   = (isset($needle["user_ids"])?$needle["user_ids"]:"");
		
		if ($value == "") {
			$value_end = 1;
		}else {
			$value_end = $value+1;
		}

		$user_ids_end = $user_ids;
		if ((is_array($user_ids) && empty($user_ids)) || !is_array($user_ids) || $user_ids == "") {
			$user_ids_end = array($user_id);
		}else if (is_array($user_ids) && !in_array($user_id,$user_ids)) {
			$user_ids_end = array_merge($user_ids,array($user_id));
		}
		
		foreach ($askme_poll as $key_k => $value_v) {
			if (isset($askme_poll[$key_k]["user_ids"]) && is_array($askme_poll[$key_k]["user_ids"]) && in_array($user_id,$askme_poll[$key_k]["user_ids"]) && $user_id != 0) {
				$no_poll = "no_poll";
			}else {
				if ($value_v["id"] == $needle["id"] && $no_poll != "no_poll") {
					$askme_poll[$key_k] = array("id" => $poll_id,"value" => $value_end,"user_ids" => $user_ids_end);
				}
			}
		}
		
		if ($no_poll != "no_poll") {
			update_post_meta($post_id,'askme_poll',$askme_poll);
			$question_poll_num = get_post_meta($post_id,'question_poll_num',true);
			$question_poll_num++;
			update_post_meta($post_id,'question_poll_num',$question_poll_num);
			
			$get_post = get_post($post_id);
			$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
			if (($get_post->post_author > 0 && $get_post->post_author != $user_id) || ($anonymously_user > 0 && $anonymously_user != $user_id)) {
				askme_notifications_activities(($get_post->post_author > 0?$get_post->post_author:$anonymously_user),($user_id > 0?$user_id:0),($user_id > 0?"":"unlogged"),$post_id,"","poll_question","notifications",$poll_title,ask_questions_type);
			}
			if ($user_id > 0) {
				askme_notifications_activities($user_id,"","",$post_id,"","poll_question","activities",$poll_title,ask_questions_type);
				$point_poll_question = askme_options("point_poll_question");
				if ($point_poll_question > 0) {
					askme_add_points($user_id,$point_poll_question,"+","polling_question",$post_id);
				}
			}
			$update = true;
		}else {
			if ($mobile == true) {
				return "no_poll";
			}
			$poll_error = "no_poll";
		}

		if (isset($update)) {
			if (is_user_logged_in()) {
				if (empty($question_poll)) {
					update_post_meta($post_id,"askme_question_poll",array($user_id));
				}else if (is_array($question_poll) && !in_array($user_id,$question_poll)) {
					update_post_meta($post_id,"askme_question_poll",array_merge($question_poll,array($user_id)));
				}
			}else {
				if (isset($_COOKIE[askme_options("uniqid_cookie").'askme_question_poll'.$post_id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_question_poll'.$post_id] == "askme_yes_poll") {
					unset($_COOKIE[askme_options("uniqid_cookie").'askme_question_poll'.$post_id]);
					setcookie(askme_options("uniqid_cookie").'askme_question_poll'.$post_id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
				}
				setcookie(askme_options("uniqid_cookie").'askme_question_poll'.$post_id,"askme_yes_poll",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
			}
		}

		if ($mobile == true) {
			return $askme_poll;
		}
	}
	if (isset($poll_error) && $poll_error != "") {
		if ($mobile == true) {
			return $poll_error;
		}else {
			echo ($poll_error);
		}
	}else {
		if ($mobile == true && isset($askme_poll)) {
			return $askme_poll;
		}
		echo askme_show_poll_results($post_id,$user_id,"results");
	}
	die();
}
add_action('wp_ajax_askme_question_poll','askme_question_poll');
add_action('wp_ajax_nopriv_askme_question_poll','askme_question_poll');
/* Get poll results */
function askme_show_poll_results($post_id,$user_id,$return = "all") {
	$poll_user_only      = askme_options("poll_user_only");
	$question_poll_yes   = false;
	$question_poll_num   = get_post_meta($post_id,"question_poll_num",true);
	$asks                = get_post_meta($post_id,"ask",true);
	$askme_polls         = get_post_meta($post_id,"askme_poll",true);
	$askme_polls         = (isset($askme_polls) && is_array($askme_polls) && !empty($askme_polls)?$askme_polls:array());
	$askme_question_poll = get_post_meta($post_id,"askme_question_poll",true);
	$askme_question_poll = (isset($askme_question_poll) && is_array($askme_question_poll) && !empty($askme_question_poll)?$askme_question_poll:array());
	if (isset($asks) && is_array($asks)) {
		$key_k = 0;
		foreach ($asks as $key_ask => $value_ask) {
			$key_k++;
			$sort_polls[$key_k]["id"] = (int)$asks[$key_ask]["id"];
			$sort_polls[$key_k]["title"] = $asks[$key_ask]["title"];
			$sort_polls[$key_k]["value"] = (isset($asks[$key_ask]["value"]) && $asks[$key_ask]["value"] != ""?$asks[$key_ask]["value"]:(isset($askme_polls[$key_ask]["value"]) && $askme_polls[$key_ask]["value"] != ""?$askme_polls[$key_ask]["value"]:0));
			$sort_polls[$key_k]["user_ids"] = (isset($asks[$key_ask]["user_ids"]) && $asks[$key_ask]["user_ids"] != ""?$asks[$key_ask]["user_ids"]:(isset($askme_polls[$key_ask]["user_ids"]) && $askme_polls[$key_ask]["user_ids"] != ""?$askme_polls[$key_ask]["user_ids"]:array()));
		}
	}
	$output = '';
	if (isset($sort_polls) && is_array($sort_polls)) {
		if ($return == "results" || (is_user_logged_in() && is_array($askme_question_poll) && in_array($user_id,$askme_question_poll)) || (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_question_poll'.$post_id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_question_poll'.$post_id] == "askme_yes_poll")) {
			$question_poll_yes = true;
		}
		$poll_1 = '<div class="poll_1'.($question_poll_yes == false?" ask-hide":"").'">';
			if ($poll_user_only == 1 && !is_user_logged_in()) {
				$poll_1 .= '<p class="still-not-votes">'.esc_html__("Please login to vote and see the results.","vbegy").'</p>';
			}else {
				if ($question_poll_num > 0) {
					$poll_1 .= '<div class="progressbar-main">
						<div class="progressbar-wrap">';
							foreach($sort_polls as $v_ask):
								$poll_voters = (int)$v_ask['value'];
								if ($question_poll_num != "" || $question_poll_num != 0) {
									$value_poll = round(($poll_voters/$question_poll_num)*100,2);
								}
								$poll_1 .= '<span class="progressbar-title">
									'."<span>".($question_poll_num == 0?0:$value_poll)."%</span>".(isset($v_ask['title']) && $v_ask['title'] != ''?esc_html($v_ask['title']):'')." ".($poll_voters != ""?"( ".ask_count_number($poll_voters)." "._n("voter","voters",$poll_voters,"vbegy")." )":"").'
								</span>
								<div class="progressbar">
								    <div class="progressbar-percent'.($poll_voters == 0?" poll-result":"").'"'.($poll_voters == 0?"":" style='background-color: #3498db'").' attr-percent="'.($poll_voters == 0?100:$value_poll).'"></div>
								</div>';
							endforeach;
						$poll_1 .= '</div><!-- End progressbar-wrap -->
						<div class="poll-num">'.esc_html__("Based On","vbegy")." <span>".($question_poll_num > 0?ask_count_number($question_poll_num):0)." "._n("Vote","Votes",$question_poll_num,"vbegy")."</span>".'</div>
					</div><!-- End progressbar-main -->';
				}else {
					$poll_1 .= '<p class="still-not-votes">'.esc_html__("No votes. Be the first one to vote.","vbegy").'</p>';
				}
			}
			if ($question_poll_yes == false) {
				$poll_1 .= '<input type="submit" class="ed_button poll_polls" value="'.esc_attr__("Voting","vbegy").'"">';
			}
		$poll_1 .= '</div>';
		$output .= apply_filters("askme_show_poll",$poll_1,$poll_user_only,$user_id,$question_poll_yes,$question_poll_num,$sort_polls,$askme_polls);
		$output .= '<div class="clear"></div>';
		if ($question_poll_yes == false) {
			$output .= '<div class="poll_2"><div class="loader_3"></div>
				<div class="form-style form-style-3 askme_form">
					<div class="form-inputs clearfix">';
						foreach($sort_polls as $v_ask):
							$output .= '<p class="askme_radio_p">
								<span class="askme_radio">
									<input class="required-item" id="ask-'.esc_attr($v_ask['id']).'-title-'.esc_attr($post_id).'" name="ask_radio" type="radio" value="poll_'.(int)$v_ask['id'].'"'.(isset($v_ask['title']) && $v_ask['title'] != ''?' data-rel="poll_'.esc_html($v_ask['title']).'"':'').'>
								</span>
								<label for="ask-'.esc_attr($v_ask['id']).'-title-'.esc_attr($post_id).'">'.(isset($v_ask['title']) && $v_ask['title'] != ''?esc_html($v_ask['title']):'').'</label>
							</p>';
						endforeach;
					$output .= '</div>
				</div>';
				if ($question_poll_yes == false) {
					$output .= '<input type="submit" class="color button small poll_results margin_0" value="'.esc_attr__("Results","vbegy").'">';
				}
			$output .= '</div>';
		}
	}
	return $output;
}
/* question_vote_up */
function question_vote_up($data = array()) {
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$get_current_user_id = get_current_user_id();
	$id = (int)$data['id'];

	$count_up = get_post_meta($id,'askme_question_vote_up',true);
	$count_down = get_post_meta($id,'askme_question_vote_down',true);
	$count = get_post_meta($id,'question_vote',true);
	if ($count == "") {
		$count = 0;
	}
	$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
	$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
	
	if ((is_user_logged_in() && is_array($count_up) && in_array($get_current_user_id,$count_up)) || (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_up'.$id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_up'.$id] == "askme_yes")) {
		if ($mobile == true) {
			return esc_html__('Sorry, you cannot vote on the same question more than once.','vbegy');
		}
		echo "no_vote_more".$count;
	}else {
		$get_post = get_post($id);
		$user_id = $get_post->post_author;
		$point_rating_question = (int)askme_options("point_rating_question");
		$active_points = askme_options("active_points");
		
		if ($user_id != $get_current_user_id && $user_id > 0 && $point_rating_question > 0 && $active_points == 1) {
			$add_votes = get_user_meta($user_id,"add_votes_all",true);
			if ($add_votes == "" || $add_votes == 0) {
				update_user_meta($user_id,"add_votes_all",1);
			}else {
				update_user_meta($user_id,"add_votes_all",$add_votes+1);
			}
			
			$user_vote = get_user_by("id",$user_id);
			$_points = get_user_meta($user_id,$user_vote->user_login."_points",true);
			$_points++;

			update_user_meta($user_id,$user_vote->user_login."_points",$_points);
			add_user_meta($user_id,$user_vote->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),($point_rating_question != ""?$point_rating_question:1),"+","rating_question",$id));

			$points_user = get_user_meta($user_id,"points",true);
			update_user_meta($user_id,"points",$points_user+($point_rating_question != ""?$point_rating_question:1));
		}

		if (is_user_logged_in() && is_array($count_down) && in_array($get_current_user_id,$count_down)) {
			$count_down = remove_item_by_value($count_down,$get_current_user_id);
			update_post_meta($id,"askme_question_vote_down",$count_down);
			$askme_question_vote_down = true;
		}
		
		if (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_down'.$id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_down'.$id] == "askme_yes") {
			unset($_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_down'.$id]);
			setcookie(askme_options("uniqid_cookie").'askme_question_vote_down'.$id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
			$askme_question_vote_down = true;
		}
		
		$count++;
		$update = update_post_meta($id,'question_vote',$count);

		if ($update && !isset($askme_question_vote_down)) {
			if (is_user_logged_in()) {
				if (empty($count_up)) {
					$update = update_post_meta($id,"askme_question_vote_up",array($get_current_user_id));
				}else if (is_array($count_up) && !in_array($get_current_user_id,$count_up)) {
					$update = update_post_meta($id,"askme_question_vote_up",array_merge($count_up,array($get_current_user_id)));
				}
			}else {
				setcookie(askme_options("uniqid_cookie").'askme_question_vote_up'.$id,"askme_yes",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
			}
		}
		
		$anonymously_user = get_post_meta($id,"anonymously_user",true);
		if (($get_current_user_id > 0 && $user_id > 0) || ($get_current_user_id > 0 && $anonymously_user > 0)) {
			askme_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$id,"","question_vote_up","notifications","",ask_questions_type);
		}
		if ($get_current_user_id > 0) {
			askme_notifications_activities($get_current_user_id,"","",$id,"","question_vote_up","activities","",ask_questions_type);
		}
		if ($mobile == true) {
			return $count;
		}
		echo $count;
	}
	die();
}
add_action('wp_ajax_question_vote_up','question_vote_up');
add_action('wp_ajax_nopriv_question_vote_up','question_vote_up');
/* question_vote_down */
function question_vote_down($data = array()) {
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$get_current_user_id = get_current_user_id();
	$id = (int)$data['id'];
	$count_up = get_post_meta($id,'askme_question_vote_up',true);
	$count_down = get_post_meta($id,'askme_question_vote_down',true);
	$count = get_post_meta($id,'question_vote',true);
	if ($count == "") {
		$count = 0;
	}
	$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
	$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
	
	if ((is_user_logged_in() && is_array($count_down) && in_array($get_current_user_id,$count_down)) || (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_down'.$id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_down'.$id] == "askme_yes")) {
		if ($mobile == true) {
			return esc_html__('Sorry, you cannot vote on the same question more than once.','vbegy');
		}
		echo "no_vote_more".$count;
	}else {
		$get_post = get_post($id);
		$user_id = $get_post->post_author;
		$point_rating_question = (int)askme_options("point_rating_question");
		$active_points = askme_options("active_points");
		
		if ($user_id != $get_current_user_id && $user_id > 0 && $point_rating_question > 0 && $active_points == 1) {
			$add_votes = get_user_meta($user_id,"add_votes_all",true);
			if ($add_votes == "" || $add_votes == 0) {
				update_user_meta($user_id,"add_votes_all",1);
			}else {
				update_user_meta($user_id,"add_votes_all",$add_votes+1);
			}
			$user_vote = get_user_by("id",$user_id);
			$_points = get_user_meta($user_id,$user_vote->user_login."_points",true);
			$_points++;
		
			update_user_meta($user_id,$user_vote->user_login."_points",$_points);
			add_user_meta($user_id,$user_vote->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),($point_rating_question != ""?$point_rating_question:1),"-","rating_question",$id));
		
			$points_user = get_user_meta($user_id,"points",true);
			update_user_meta($user_id,"points",$points_user-($point_rating_question != ""?$point_rating_question:1));
		}

		if (is_user_logged_in() && is_array($count_up) && in_array($get_current_user_id,$count_up)) {
			$count_up = remove_item_by_value($count_up,$get_current_user_id);
			update_post_meta($id,"askme_question_vote_up",$count_up);
			$askme_question_vote_up = true;
		}
		
		if (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_up'.$id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_up'.$id] == "askme_yes") {
			unset($_COOKIE[askme_options("uniqid_cookie").'askme_question_vote_up'.$id]);
			setcookie(askme_options("uniqid_cookie").'askme_question_vote_up'.$id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
			$askme_question_vote_up = true;
		}
		
		$count--;
		$update = update_post_meta($id,'question_vote',$count);

		if ($update && !isset($askme_question_vote_up)) {
			if (is_user_logged_in()) {
				if (empty($count_down)) {
					$update = update_post_meta($id,"askme_question_vote_down",array($get_current_user_id));
				}else if (is_array($count_down) && !in_array($get_current_user_id,$count_down)) {
					$update = update_post_meta($id,"askme_question_vote_down",array_merge($count_down,array($get_current_user_id)));
				}
			}else {
				setcookie(askme_options("uniqid_cookie").'askme_question_vote_down'.$id,"askme_yes",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
			}
		}
		
		$anonymously_user = get_post_meta($id,"anonymously_user",true);
		if (($get_current_user_id > 0 && $user_id > 0) || ($get_current_user_id > 0 && $anonymously_user > 0)) {
			askme_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$id,"","question_vote_down","notifications","",ask_questions_type);
		}
		if ($get_current_user_id > 0) {
			askme_notifications_activities($get_current_user_id,"","",$id,"","question_vote_down","activities","",ask_questions_type);
		}
		if ($mobile == true) {
			return $count;
		}
		echo $count;
	}
	die();
}
add_action('wp_ajax_question_vote_down','question_vote_down');
add_action('wp_ajax_nopriv_question_vote_down','question_vote_down');
/* comment_vote_up */
function comment_vote_up($data = array()) {
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$get_current_user_id = get_current_user_id();
	$id = (int)$data['id'];
	$count = get_comment_meta($id,'comment_vote',true);
	$count_up = get_comment_meta($id,'askme_comment_vote_up',true);
	$count_down = get_comment_meta($id,'askme_comment_vote_down',true);
	$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
	$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
	
	if (isset($count) && is_array($count) && isset($count["vote"])) {
		update_comment_meta($id,'comment_vote',$count["vote"]);
		$count = get_comment_meta($id,'comment_vote',true);
	}
	
	$count = (!empty($count)?$count:0);
	
	if ($count == "") {
		$count = 0;
	}
	
	if ((is_user_logged_in() && is_array($count_up) && in_array($get_current_user_id,$count_up)) || (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_up'.$id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_up'.$id] == "askme_yes_comment")) {
		if ($mobile == true) {
			return esc_html__('Sorry, you cannot vote on the same answer more than once.','askme');
		}
		echo "no_vote_more".$count;
	}else {
		$get_comment = get_comment($id);
		$post_id = $get_comment->comment_post_ID;
		$active_points = askme_options("active_points");
		$point_rating_answer = (int)askme_options("point_rating_answer");
		$user_votes_id = $get_comment->user_id;
		
		if ($user_votes_id != $get_current_user_id && $user_votes_id > 0 && $point_rating_answer > 0 && $active_points == 1) {
			$add_votes = get_user_meta($user_votes_id,"add_votes_all",true);
			if ($add_votes == "" || $add_votes == 0) {
				update_user_meta($user_votes_id,"add_votes_all",1);
			}else {
				update_user_meta($user_votes_id,"add_votes_all",$add_votes+1);
			}
			$user_vote = get_user_by("id",$user_votes_id);
			$_points = get_user_meta($user_votes_id,$user_vote->user_login."_points",true);
			$_points++;
		
			update_user_meta($user_votes_id,$user_vote->user_login."_points",$_points);
			add_user_meta($user_votes_id,$user_vote->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),($point_rating_answer != ""?$point_rating_answer:1),"+","rating_answer",$post_id,$id));
		
			$points_user = get_user_meta($user_votes_id,"points",true);
			update_user_meta($user_votes_id,"points",$points_user+($point_rating_answer != ""?$point_rating_answer:1));
		}
		
		$anonymously_user = get_comment_meta($id,"anonymously_user",true);
		if (($get_current_user_id > 0 && $user_votes_id > 0) || ($get_current_user_id > 0 && $anonymously_user > 0)) {
			askme_notifications_activities(($user_votes_id > 0?$user_votes_id:$anonymously_user),$get_current_user_id,"",$post_id,$id,"answer_vote_up","notifications","","answer");
		}

		if ($get_current_user_id > 0) {
			askme_notifications_activities($get_current_user_id,"","",$post_id,$id,"answer_vote_up","activities","","answer");
		}
		
		if (is_user_logged_in() && is_array($count_down) && in_array($get_current_user_id,$count_down)) {
			$count_down = remove_item_by_value($count_down,$get_current_user_id);
			update_comment_meta($id,"askme_comment_vote_down",$count_down);
			$askme_comment_vote_down = true;
		}
		
		if (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_down'.$id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_down'.$id] == "askme_yes_comment") {
			unset($_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_down'.$id]);
			setcookie(askme_options("uniqid_cookie").'askme_comment_vote_down'.$id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
			$askme_comment_vote_down = true;
		}
		
		$count++;
		$update = update_comment_meta($id,'comment_vote',$count);

		if ($update && !isset($askme_comment_vote_down)) {
			if (is_user_logged_in()) {
				if (empty($count_up)) {
					$update = update_comment_meta($id,"askme_comment_vote_up",array($get_current_user_id));
				}else if (is_array($count_up) && !in_array($get_current_user_id,$count_up)) {
					$update = update_comment_meta($id,"askme_comment_vote_up",array_merge($count_up,array($get_current_user_id)));
				}
			}else {
				setcookie(askme_options("uniqid_cookie").'askme_comment_vote_up'.$id,"askme_yes_comment",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
			}
		}
		if ($mobile == true) {
			return $count;
		}
		echo $count;
	}
	die();
}
add_action('wp_ajax_comment_vote_up','comment_vote_up');
add_action('wp_ajax_nopriv_comment_vote_up','comment_vote_up');
/* comment_vote_down */
function comment_vote_down($data = array()) {
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$get_current_user_id = get_current_user_id();
	$id = (int)$data['id'];
	$count = get_comment_meta($id,'comment_vote',true);
	$count_up = get_comment_meta($id,'askme_comment_vote_up',true);
	$count_down = get_comment_meta($id,'askme_comment_vote_down',true);
	$count_up = (isset($count_up) && is_array($count_up) && !empty($count_up)?$count_up:array());
	$count_down = (isset($count_down) && is_array($count_down) && !empty($count_down)?$count_down:array());
	
	if (isset($count) && is_array($count) && isset($count["vote"])) {
		update_comment_meta($id,'comment_vote',$count["vote"]);
		$count = get_comment_meta($id,'comment_vote',true);
	}
	
	$count = (!empty($count)?$count:0);
	
	if ($count == "") {
		$count = 0;
	}
	
	if ((is_user_logged_in() && is_array($count_down) && in_array($get_current_user_id,$count_down)) || (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_down'.$id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_down'.$id] == "askme_yes_comment")) {
		if ($mobile == true) {
			return esc_html__('Sorry, you cannot vote on the same answer more than once.','vbegy');
		}
		echo "no_vote_more".$count;
	}else {
		$get_comment = get_comment($id);
		$post_id = $get_comment->comment_post_ID;
		$active_points = askme_options("active_points");
		$point_rating_answer = (int)askme_options("point_rating_answer");
		$user_votes_id = $get_comment->user_id;
		
		if ($user_votes_id != $get_current_user_id && $user_votes_id > 0 && $point_rating_answer > 0 && $active_points == 1) {
			$add_votes = get_user_meta($user_votes_id,"add_votes_all",true);
			if ($add_votes == "" || $add_votes == 0) {
				update_user_meta($user_votes_id,"add_votes_all",1);
			}else {
				update_user_meta($user_votes_id,"add_votes_all",$add_votes+1);
			}
			$user_vote = get_user_by("id",$user_votes_id);
			$_points = get_user_meta($user_votes_id,$user_vote->user_login."_points",true);
			$_points++;
			
			update_user_meta($user_votes_id,$user_vote->user_login."_points",$_points);
			add_user_meta($user_votes_id,$user_vote->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),($point_rating_answer != ""?$point_rating_answer:1),"-","rating_answer",$post_id,$id));
		
			$points_user = get_user_meta($user_votes_id,"points",true);
			update_user_meta($user_votes_id,"points",$points_user-($point_rating_answer != ""?$point_rating_answer:1));
		}
		
		$anonymously_user = get_comment_meta($id,"anonymously_user",true);
		if (($get_current_user_id > 0 && $user_votes_id > 0) || ($get_current_user_id > 0 && $anonymously_user > 0)) {
			askme_notifications_activities(($user_votes_id > 0?$user_votes_id:$anonymously_user),$get_current_user_id,"",$post_id,$id,"answer_vote_down","notifications","","answer");
		}
		
		if ($get_current_user_id > 0) {
			askme_notifications_activities($get_current_user_id,"","",$post_id,$id,"answer_vote_down","activities","","answer");
		}
		
		if (is_user_logged_in() && is_array($count_up) && in_array($get_current_user_id,$count_up)) {
			$count_up = remove_item_by_value($count_up,$get_current_user_id);
			update_comment_meta($id,"askme_comment_vote_up",$count_up);
			$askme_comment_vote_up = true;
		}
		
		if (!is_user_logged_in() && isset($_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_up'.$id]) && $_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_up'.$id] == "askme_yes_comment") {
			unset($_COOKIE[askme_options("uniqid_cookie").'askme_comment_vote_up'.$id]);
			setcookie(askme_options("uniqid_cookie").'askme_comment_vote_up'.$id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
			$askme_comment_vote_up = true;
		}
		
		$count--;
		$update = update_comment_meta($id,'comment_vote',$count);

		if ($update && !isset($askme_comment_vote_up)) {
			if (is_user_logged_in()) {
				if (empty($count_down)) {
					$update = update_comment_meta($id,"askme_comment_vote_down",array($get_current_user_id));
				}else if (is_array($count_down) && !in_array($get_current_user_id,$count_down)) {
					$update = update_comment_meta($id,"askme_comment_vote_down",array_merge($count_down,array($get_current_user_id)));
				}
			}else {
				setcookie(askme_options("uniqid_cookie").'askme_comment_vote_down'.$id,"askme_yes_comment",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
			}
		}
		if ($mobile == true) {
			return $count;
		}
		echo $count;
	}
	die();
}
add_action('wp_ajax_comment_vote_down','comment_vote_down');
add_action('wp_ajax_nopriv_comment_vote_down','comment_vote_down');
/* following_me */
function following_me_ajax() {
	$following_nonce = esc_html($_POST["following_nonce"]);
	if (wp_verify_nonce($following_nonce,'askme_following_nonce')) {
		following_me($_POST);
	}
}
add_action('wp_ajax_following_me_ajax','following_me_ajax');
add_action('wp_ajax_nopriv_following_me_ajax','following_me_ajax');
function following_me($data = array()) {
	$following_you_id = (int)(isset($data["mobile"])?$data["following_var_id"]:$data["following_you_id"]);
	$get_user_by_following_id = get_user_by("id",$following_you_id);
	$active_points = askme_options("active_points");
	$point_following_me = askme_options("point_following_me");
	$point_following_me = ($point_following_me != ""?$point_following_me:1);

	$user_id = get_current_user_id();

	$following_me_get = get_user_meta(get_current_user_id(),"following_me",true);
	if (empty($following_me_get)) {
		$update = update_user_meta($user_id,"following_me",array($following_you_id));
	}else if (is_array($following_me_get) && !in_array($following_you_id,$following_me_get)) {
		$update = update_user_meta($user_id,"following_me",array_merge($following_me_get,array($following_you_id)));
	}

	if (isset($update)) {
		if ($active_points == 1) {
			$points_get = get_user_meta($following_you_id,"points",true);
			if ($points_get == "" or $points_get == 0) {
				update_user_meta($following_you_id,"points",$point_following_me);
			}else {
				$new_points = $points_get+$point_following_me;
				update_user_meta($following_you_id,"points",$new_points);
			}
			
			$_points = get_user_meta($following_you_id,$get_user_by_following_id->user_login."_points",true);
			$_points++;
			
			update_user_meta($following_you_id,$get_user_by_following_id->user_login."_points",$_points);
			add_user_meta($following_you_id,$get_user_by_following_id->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$point_following_me,"+","user_follow","",""));
		}
		
		$another_user_id = $following_you_id;
		$user_id = get_current_user_id();
		if ($user_id > 0 && $another_user_id > 0) {
			askme_notifications_activities($another_user_id,$user_id,"","","","user_follow","notifications");
		}
		if ($user_id > 0) {
			askme_notifications_activities($user_id,$another_user_id,"","","","user_follow","activities");
		}

		$following_you_get = get_user_meta($following_you_id,"following_you",true);
		if (empty($following_you_get)) {
			$update = update_user_meta($following_you_id,"following_you",array($user_id));
		}else if (is_array($following_you_get) && !in_array($user_id,$following_you_get)) {
			$update = update_user_meta($following_you_id,"following_you",array_merge($following_you_get,array($user_id)));
		}
	}
	
	if (!isset($data["mobile"])) {
		$echo_following_you = get_user_meta($following_you_id,"following_you",true);
		echo (isset($echo_following_you) && is_array($echo_following_you)?count($echo_following_you):0);
		die();
	}
}
/* following_not */
function following_not_ajax() {
	$following_nonce = esc_html($_POST["following_nonce"]);
	if (wp_verify_nonce($following_nonce,'askme_following_nonce')) {
		following_not($_POST);
	}
}
add_action('wp_ajax_following_not_ajax','following_not_ajax');
add_action('wp_ajax_nopriv_following_not_ajax','following_not_ajax');
function following_not($data = array()) {
	$following_not_id = (int)(isset($data["mobile"])?$data["following_var_id"]:$data["following_not_id"]);
	$get_user_by_following_not_id = get_user_by("id",$following_not_id);
	$active_points = askme_options("active_points");
	$point_following_me = askme_options("point_following_me");
	$point_following_me = ($point_following_me != ""?$point_following_me:1);

	$user_id = get_current_user_id();
	
	$following_me = get_user_meta($user_id,"following_me",true);
	if (is_array($following_me) && in_array($following_not_id,$following_me)) {
		$remove_following_me = remove_item_by_value($following_me,$following_not_id);
		update_user_meta($user_id,"following_me",$remove_following_me);
		if ($active_points == 1) {
			$points = get_user_meta($following_not_id,"points",true);
			$new_points = $points-$point_following_me;
			if ($new_points < 0) {
				$new_points = 0;
			}
			update_user_meta($following_not_id,"points",$new_points);
			
			$_points = get_user_meta($following_not_id,$get_user_by_following_not_id->user_login."_points",true);
			$_points++;
			
			update_user_meta($following_not_id,$get_user_by_following_not_id->user_login."_points",$_points);
			add_user_meta($following_not_id,$get_user_by_following_not_id->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$point_following_me,"-","user_unfollow","",""));
		}
		
		$another_user_id = $following_not_id;
		if ($user_id > 0 && $another_user_id > 0) {
			askme_notifications_activities($another_user_id,$user_id,"","","","user_unfollow","notifications");
		}
		if ($user_id > 0) {
			askme_notifications_activities($user_id,$another_user_id,"","","","user_unfollow","activities");
		}
		
		$following_you = get_user_meta($following_not_id,"following_you",true);
		$get_user_by_following_not_id2 = get_user_by("id",$user_id);
		if (is_array($following_you) && in_array($user_id,$following_you)) {
			$remove_following_you = remove_item_by_value($following_you,$get_user_by_following_not_id2->ID);
			update_user_meta($following_not_id,"following_you",$remove_following_you);
		}
	}
	
	if (!isset($data["mobile"])) {
		$echo_following_you = get_user_meta($following_not_id,"following_you",true);
		echo (isset($echo_following_you) && is_array($echo_following_you)?count($echo_following_you):0);
		die();
	}
}
/* askme_add_point */
function askme_add_point() {
	$input_add_point = (int)$_POST["input_add_point"];
	$post_id = (int)$_POST["post_id"];
	$user_id = get_current_user_id();
	$user_name = get_user_by("id",$user_id);
	$points_user = get_user_meta($user_id,"points",true);
	$get_post = get_post($post_id);
	if (get_current_user_id() != $get_post->post_author) {
		$return = esc_html__("Sorry no mistake, this is not a question asked.","vbegy");
		if (isset($_POST["mobile"])) {
			return $return;
		}else {
			echo ($return);
		}
	}else if ($points_user >= $input_add_point) {
		if ($input_add_point == "") {
			$return = esc_html__("You must enter a numeric value and a value greater than zero.","vbegy");
			if (isset($_POST["mobile"])) {
				return $return;
			}else {
				echo ($return);
			}
		}else if ($input_add_point <= 0) {
			$return = esc_html__("You must enter a numeric value and a value greater than zero.","vbegy");
			if (isset($_POST["mobile"])) {
				return $return;
			}else {
				echo ($return);
			}
		}else {
			$question_points = get_post_meta($post_id,"question_points",true);
			if ($question_points == 0) {
				$question_points = $input_add_point;
			}else {
				$question_points = $input_add_point+$question_points;
			}
			update_post_meta($post_id,"question_points",$question_points);
			
			$_points = get_user_meta($user_id,$user_name->user_login."_points",true);
			$_points++;
			update_user_meta($user_id,$user_name->user_login."_points",$_points);
			add_user_meta($user_id,$user_name->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$input_add_point,"-","bump_question",$post_id));
			$points_user = get_user_meta($user_id,"points",true);
			update_user_meta($user_id,"points",$points_user-$input_add_point);
			$return = esc_html__("You bump your question now.","vbegy");
			if (isset($_POST["mobile"])) {
				return "get_points";
			}else {
				echo ($return);
			}
			if ($user_id > 0) {
				askme_notifications_activities($user_id,"","",$post_id,"","bump_question","activities","",ask_questions_type);
			}
		}
	}else {
		$return = esc_html__("Your points are insufficient.","vbegy");
		if (isset($_POST["mobile"])) {
			return $return;
		}else {
			echo ($return);
		}
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_askme_add_point','askme_add_point');
add_action('wp_ajax_nopriv_askme_add_point','askme_add_point');
/* ask_redirect_login */
function ask_redirect_login() {
	if (askme_options("login_page") != "") {
		wp_redirect(get_permalink(askme_options("login_page")));
	}else {
		wp_redirect(wp_login_url(home_url()));
	}
	exit;
}
/* ask_get_filesize */
if (!function_exists('ask_get_filesize')) {
	function ask_get_filesize($file) { 
		$bytes = filesize($file);
		$s = array('b','Kb','Mb','Gb');
		$e = floor(log($bytes)/log(1024));
		return sprintf('%.2f '.$s[$e],($bytes/pow(1024,floor($e))));
	}
}
/* report_q */
function report_q($data = array()) {
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$post_id = (int)$data['post_id'];
	$explain = esc_attr($data['explain']);
	$user_id = get_current_user_id();
	
	/* option */
	$ask_option = get_option("ask_option");
	$ask_option_array = get_option("ask_option_array");
	if ($ask_option_array == "") {
		$ask_option_array = array();
	}
	if ($ask_option != "") {
		$ask_option++;
		update_option("ask_option",$ask_option);
		array_push($ask_option_array,$ask_option);
		update_option("ask_option_array",$ask_option_array);
	}else {
		$ask_option = 1;
		add_option("ask_option",$ask_option);
		add_option("ask_option_array",array($ask_option));
	}
	$ask_time = current_time('timestamp');
	/* option */
	if ($user_id > 0 && is_user_logged_in) {
		$name_last = "";
		$id_last = $user_id;
	}else {
		$name_last = 1;
		$id_last = "";
	}
	/* add option */
	add_option("ask_option_".$ask_option,array("post_id" => $post_id,"the_date" => $ask_time,"report_new" => 1,"user_id" => $id_last,"the_author" => $name_last,"item_id_option" => $ask_option,"value" => $explain));
	$send_text = askme_send_mail(
		array(
			'content' => askme_options("email_report_question"),
			'post_id' => $post_id,
		)
	);
	$email_title = askme_options("title_report_question");
	$email_title = ($email_title != ""?$email_title:esc_html__("Report Question","vbegy"));
	$email_title = askme_send_mail(
		array(
			'content' => $email_title,
			'title'   => true,
			'break'   => '',
			'post_id' => $post_id,
		)
	);
	askme_send_mails(
		array(
			'title'   => $email_title,
			'message' => $send_text,
		)
	);
	if ($user_id > 0) {
		askme_notifications_activities($user_id,"","",$post_id,"","report_question","activities","",ask_questions_type);
	}
	if ($mobile == true) {
		return "thank_you";
	}else {
		die();
	}
}
add_action('wp_ajax_report_q','report_q');
add_action('wp_ajax_nopriv_report_q','report_q');
/* report_c */
function report_c($data = array()) {
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$comment_id = (int)$data['comment_id'];
	$explain = esc_attr($data['explain']);
	$get_comment = get_comment($comment_id);
	$post_id = $get_comment->comment_post_ID;
	$user_id = get_current_user_id();
	
	/* option */
	$ask_option_answer = get_option("ask_option_answer");
	$ask_option_answer_array = get_option("ask_option_answer_array");
	if ($ask_option_answer_array == "") {
		$ask_option_answer_array = array();
	}
	if ($ask_option_answer != "") {
		$ask_option_answer++;
		update_option("ask_option_answer",$ask_option_answer);
		array_push($ask_option_answer_array,$ask_option_answer);
		update_option("ask_option_answer_array",$ask_option_answer_array);
	}else {
		$ask_option_answer = 1;
		add_option("ask_option_answer",$ask_option_answer);
		add_option("ask_option_answer_array",array($ask_option_answer));
	}
	$ask_time = current_time('timestamp');
	/* option */
	if ($user_id > 0 && is_user_logged_in) {
		$name_last = "";
		$id_last = $user_id;
	}else {
		$name_last = 1;
		$id_last = "";
	}
	/* add option */
	add_option("ask_option_answer_".$ask_option_answer,array("post_id" => $post_id,"comment_id" => $comment_id,"the_date" => $ask_time,"report_new" => 1,"user_id" => $id_last,"the_author" => $name_last,"item_id_option" => $ask_option_answer,"value" => $explain));
	$send_text = askme_send_mail(
		array(
			'content'    => askme_options("email_report_answer"),
			'post_id'    => $post_id,
			'comment_id' => $comment_id,
		)
	);
	$email_title = askme_options("title_report_answer");
	$email_title = ($email_title != ""?$email_title:esc_html__("Report Answer","vbegy"));
	$email_title = askme_send_mail(
		array(
			'content'    => $email_title,
			'title'      => true,
			'break'      => '',
			'post_id'    => $post_id,
			'comment_id' => $comment_id,
		)
	);
	askme_send_mails(
		array(
			'title'   => $email_title,
			'message' => $send_text,
		)
	);
	if ($user_id > 0) {
		askme_notifications_activities($user_id,"","",$post_id,$comment_id,"report_answer","activities","","answer");
	}
	if ($mobile == true) {
		return "thank_you";
	}else {
		die();
	}
}
add_action('wp_ajax_report_c','report_c');
add_action('wp_ajax_nopriv_report_c','report_c');
/* askme_report_user */
function askme_report_user($data = array()) {
	$mobile = (is_array($data) && !empty($data)?true:false);
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	$author_id = (int)$data['user_id'];
	$explain = esc_attr($data['explain']);
	$user_id = get_current_user_id();
	
	/* option */
	$ask_option_user = get_option("ask_option_user");
	$ask_option_user_array = get_option("ask_option_user_array");
	if ($ask_option_user_array == "") {
		$ask_option_user_array = array();
	}
	if ($ask_option_user != "") {
		$ask_option_user++;
		update_option("ask_option_user",$ask_option_user);
		array_push($ask_option_user_array,$ask_option_user);
		update_option("ask_option_user_array",$ask_option_user_array);
	}else {
		$ask_option_user = 1;
		add_option("ask_option_user",$ask_option_user);
		add_option("ask_option_user_array",array($ask_option_user));
	}
	$ask_time = current_time('timestamp');
	/* option */
	if ($user_id > 0 && is_user_logged_in) {
		$name_last = "";
		$id_last = $user_id;
	}else {
		$name_last = 1;
		$id_last = "";
	}
	/* add option */
	add_option("ask_option_user_".$ask_option_user,array("author_id" => $author_id,"the_date" => $ask_time,"report_new" => 1,"user_id" => $id_last,"the_author" => $name_last,"item_id_option" => $ask_option_user,"value" => $explain));
	$send_text = askme_send_mail(
		array(
			'content'        => askme_options("email_report_user"),
			'sender_user_id' => $author_id,
		)
	);
	$email_title = askme_options("title_report_user");
	$email_title = ($email_title != ""?$email_title:esc_html__("Report User","vbegy"));
	$email_title = askme_send_mail(
		array(
			'content'        => $email_title,
			'title'          => true,
			'break'          => '',
			'sender_user_id' => $author_id,
		)
	);
	askme_send_mails(
		array(
			'title'   => $email_title,
			'message' => $send_text,
		)
	);
	if ($user_id > 0) {
		askme_notifications_activities($user_id,$author_id,"","","","report_user","activities","","user");
	}
	if ($mobile == true) {
		return "thank_you";
	}else {
		die();
	}
}
add_action('wp_ajax_askme_report_user','askme_report_user');
add_action('wp_ajax_nopriv_askme_report_user','askme_report_user');
/* best_answer */
function best_answer() {
	if (!isset($_POST["mobile"])) {
		check_ajax_referer('askme_best_answer_nonce','askme_best_answer_nonce');
	}
	$comment_id = (int)$_POST['comment_id'];
	$get_comment = get_comment($comment_id);
	$user_id = $get_comment->user_id;
	$post_id = $get_comment->comment_post_ID;
	$get_post = get_post($post_id);
	$user_author = $get_post->post_author;
	update_post_meta($post_id,"the_best_answer",$comment_id);
	$active_points = askme_options("active_points");
	$get_current_user_id = get_current_user_id();
	if ($user_id != 0) {
		$user_name = get_user_by("id",$user_id);
		if ($user_id != $user_author && $active_points == 1) {
			$_points = get_user_meta($user_id,$user_name->user_login."_points",true);
			$_points++;
			update_user_meta($user_id,$user_name->user_login."_points",$_points);
			add_user_meta($user_id,$user_name->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),(askme_options("point_best_answer") != ""?askme_options("point_best_answer"):5),"+","select_best_answer",$post_id,$comment_id));
			$points_user = get_user_meta($user_id,"points",true);
			update_user_meta($user_id,"points",$points_user+(askme_options("point_best_answer") != ""?askme_options("point_best_answer"):5));
		}
		$the_best_answer_u = get_user_meta($user_id,"the_best_answer",true);
		if ($the_best_answer_u == "" || $the_best_answer_u < 0) {
			$the_best_answer_u = 0;
		}
		$the_best_answer_u++;
		update_user_meta($user_id,"the_best_answer",$the_best_answer_u);
	}
	update_comment_meta($comment_id,"best_answer_comment","best_answer_comment");
	$option_name = "best_answer_option";
	$best_answer_option = get_option($option_name);
	if ($best_answer_option == "" || $best_answer_option < 0) {
		$best_answer_option = 0;
	}
	$best_answer_option++;
	update_option($option_name,$best_answer_option);
	update_option("best_answer_done","yes");
	
	$point_back_option = askme_options("point_back");
	$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
	if ($point_back_option == 1 && $active_points == 1 && (is_super_admin($get_current_user_id) || ($user_id != $user_author && $user_author > 0) || ($user_id != $anonymously_user && $anonymously_user > 0))) {
		$point_back_number = askme_options("point_back_number");
		$point_back = get_post_meta($post_id,"point_back",true);
		$what_point = get_post_meta($post_id,"what_point",true);
		
		if ($point_back_number > 0) {
			$what_point = $point_back_number;
		}
		
		if ($point_back == "yes" && ($user_author > 0 || $anonymously_user > 0)) {
			$author_points = ($anonymously_user > 0?$anonymously_user:$user_author);
			$user_name2 = get_user_by("id",$author_points);
			$_points = get_user_meta($author_points,$user_name2->user_login."_points",true);
			$_points++;
			update_user_meta($author_points,$user_name2->user_login."_points",$_points);
			add_user_meta($author_points,$user_name2->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),($what_point != ""?$what_point:askme_options("question_points")),"+","point_back",$post_id,$comment_id));
			$points_user = get_user_meta($author_points,"points",true);
			update_user_meta($author_points,"points",$points_user+($what_point != ""?$what_point:askme_options("question_points")));
			
			if ($user_author > 0 || $anonymously_user > 0) {
				askme_notifications_activities(($user_author > 0?$user_author:$anonymously_user),"","",$post_id,$comment_id,"point_back","notifications");
			}
		}
	}
	
	$anonymously_user = get_comment_meta($comment_id,"anonymously_user",true);
	if (($user_id > 0 && $get_current_user_id > 0 && $user_id != $get_current_user_id) || ($anonymously_user > 0 && $get_current_user_id > 0 && $anonymously_user != $get_current_user_id)) {
		askme_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$post_id,$comment_id,"select_best_answer","notifications","","answer");
	}
	if ($get_current_user_id > 0) {
		askme_notifications_activities($get_current_user_id,"","",$post_id,$comment_id,"select_best_answer","activities","","answer");
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_best_answer','best_answer');
add_action('wp_ajax_nopriv_best_answer','best_answer');
/* best_answer_remove */
function best_answer_re() {
	if (!isset($_POST["mobile"])) {
		check_ajax_referer('askme_best_answer_nonce','askme_best_answer_nonce');
	}
	$comment_id = (int)$_POST['comment_id'];
	$get_comment = get_comment($comment_id);
	$user_id = $get_comment->user_id;
	$post_id = $get_comment->comment_post_ID;
	$get_post = get_post($post_id);
	$user_author = $get_post->post_author;
	delete_post_meta($post_id,"the_best_answer",$comment_id);
	$active_points = askme_options("active_points");
	$get_current_user_id = get_current_user_id();
	if ($user_id != 0) {
		$user_name = get_user_by("id",$user_id);
		if ($user_id != $user_author && $active_points == 1) {
			$_points = get_user_meta($user_id,$user_name->user_login."_points",true);
			$_points++;
			update_user_meta($user_id,$user_name->user_login."_points",$_points);
			add_user_meta($user_id,$user_name->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),(askme_options("point_best_answer") != ""?askme_options("point_best_answer"):5),"-","cancel_best_answer",$post_id,$comment_id));
			$points_user = get_user_meta($user_id,"points",true);
			update_user_meta($user_id,"points",$points_user-(askme_options("point_best_answer") != ""?askme_options("point_best_answer"):5));
		}
		$the_best_answer_u = get_user_meta($user_id,"the_best_answer",true);
		$the_best_answer_u--;
		if ($the_best_answer_u < 0) {
			$the_best_answer_u = 0;
		}
		update_user_meta($user_id,"the_best_answer",$the_best_answer_u);
	}
	delete_comment_meta($comment_id,"best_answer_comment");
	$option_name = "best_answer_option";
	$best_answer_option = get_option($option_name);
	if ($best_answer_option == "") {
		$best_answer_option = 0;
	}
	$best_answer_option--;
	if ($best_answer_option < 0) {
		$best_answer_option = 0;
	}
	update_option($option_name,$best_answer_option);
	update_option("best_answer_done","yes");
	
	$point_back_option = askme_options("point_back");
	$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
	if ($point_back_option == 1 && $active_points == 1 && (is_super_admin($get_current_user_id) || ($user_id != $user_author && $user_author > 0) || ($user_id != $anonymously_user && $anonymously_user > 0))) {
		$point_back_number = askme_options("point_back_number");
		$point_back = get_post_meta($post_id,"point_back",true);
		$what_point = get_post_meta($post_id,"what_point",true);
		
		if ($point_back_number > 0) {
			$what_point = $point_back_number;
		}
		
		if ($point_back == "yes" && ($user_author > 0 || $anonymously_user > 0)) {
			$user_name2 = get_user_by("id",$user_author);
			$_points = get_user_meta($user_author,$user_name2->user_login."_points",true);
			$_points++;
			update_user_meta($user_author,$user_name2->user_login."_points",$_points);
			add_user_meta($user_author,$user_name2->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),($what_point != ""?$what_point:askme_options("question_points")),"-","point_removed",$post_id,$comment_id));
			$points_user = get_user_meta($user_author,"points",true);
			update_user_meta($user_author,"points",$points_user-($what_point != ""?$what_point:askme_options("question_points")));
		}
		
		if ($user_author > 0 || $anonymously_user > 0) {
			askme_notifications_activities(($user_author > 0?$user_author:$anonymously_user),"","",$post_id,$comment_id,"point_removed","notifications");
		}
	}
	
	$anonymously_user = get_comment_meta($comment_id,"anonymously_user",true);
	if (($user_id > 0 && $get_current_user_id > 0 && $user_id != $get_current_user_id) || ($anonymously_user > 0 && $get_current_user_id > 0 && $anonymously_user != $get_current_user_id)) {
		askme_notifications_activities(($user_id > 0?$user_id:$anonymously_user),$get_current_user_id,"",$post_id,$comment_id,"cancel_best_answer","notifications","","answer");
	}
	if ($get_current_user_id > 0) {
		askme_notifications_activities($get_current_user_id,"","",$post_id,$comment_id,"cancel_best_answer","activities","","answer");
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_best_answer_re','best_answer_re');
add_action('wp_ajax_nopriv_best_answer_re','best_answer_re');
/* question_close */
function question_close($post_id = 0) {
	$post_id = (int)($post_id > 0?$post_id:$_POST['post_id']);
	$get_post = get_post($post_id);
	$user_author = $get_post->post_author;
	$user_id = get_current_user_id();
	if (($user_author != 0 && $user_author == $user_id) || is_super_admin($user_id)) {
		update_post_meta($post_id,'closed_question',1);
	}
	if ($user_id > 0) {
		askme_notifications_activities($user_id,"","",$post_id,"","closed_question","activities","",ask_questions_type);
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_question_close','question_close');
add_action('wp_ajax_nopriv_question_close','question_close');
/* question_open */
function question_open($post_id = 0) {
	$post_id = (int)($post_id > 0?$post_id:$_POST['post_id']);
	$get_post = get_post($post_id);
	$user_author = $get_post->post_author;
	$user_id = get_current_user_id();
	if (($user_author != 0 && $user_author == $user_id) || is_super_admin($user_id)) {
		delete_post_meta($post_id,'closed_question');
	}
	if ($user_id > 0) {
		askme_notifications_activities($user_id,"","",$post_id,"","opend_question","activities","",ask_questions_type);
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_question_open','question_open');
add_action('wp_ajax_nopriv_question_open','question_open');
/* question_follow */
function question_follow() {
	$post_id = (int)$_POST['post_id'];
	$user_id = get_current_user_id();
	
	$following_questions_user = get_user_meta($user_id,"following_questions",true);
	if (empty($following_questions_user)) {
		update_user_meta($user_id,"following_questions",array($post_id));
	}else if (is_array($following_questions_user) && !in_array($post_id,$following_questions_user)) {
		update_user_meta($user_id,"following_questions",array_merge($following_questions_user,array($post_id)));
	}
	
	$following_questions = get_post_meta($post_id,"following_questions",true);
	if (empty($following_questions)) {
		update_post_meta($post_id,"following_questions",array($user_id));
	}else if (is_array($following_questions) && !in_array($user_id,$following_questions)) {
		update_post_meta($post_id,"following_questions",array_merge($following_questions,array($user_id)));
	}
	
	$get_post = get_post($post_id);
	$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
	if (($user_id > 0 && $get_post->post_author > 0) || ($user_id > 0 && $anonymously_user > 0)) {
		askme_notifications_activities(($get_post->post_author > 0?$get_post->post_author:$anonymously_user),$user_id,"",$post_id,"","follow_question","notifications","",ask_questions_type);
	}
	if ($user_id > 0) {
		askme_notifications_activities($user_id,"","",$post_id,"","follow_question","activities","",ask_questions_type);
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_question_follow','question_follow');
add_action('wp_ajax_nopriv_question_follow','question_follow');
/* question_unfollow */
function question_unfollow() {
	$post_id = (int)$_POST['post_id'];
	$user_id = get_current_user_id();
	
	$following_questions_user = get_user_meta($user_id,"following_questions",true);
	$remove_following_questions_user = remove_item_by_value($following_questions_user,$post_id);
	update_user_meta($user_id,"following_questions",$remove_following_questions_user);
	
	$following_questions = get_post_meta($post_id,"following_questions",true);
	$remove_following_questions = remove_item_by_value($following_questions,$user_id);
	update_post_meta($post_id,"following_questions",$remove_following_questions);
	
	$get_post = get_post($post_id);
	$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
	if (($user_id > 0 && $get_post->post_author > 0) || ($user_id > 0 && $anonymously_user > 0)) {
		askme_notifications_activities(($get_post->post_author > 0?$get_post->post_author:$anonymously_user),$user_id,"",$post_id,"","unfollow_question","notifications","",ask_questions_type);
	}
	if ($user_id > 0) {
		askme_notifications_activities($user_id,"","",$post_id,"","unfollow_question","activities","",ask_questions_type);
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_question_unfollow','question_unfollow');
add_action('wp_ajax_nopriv_question_unfollow','question_unfollow');
/* comment_question_before */
add_filter ('preprocess_comment','comment_question_before');
function comment_question_before($commentdata) {
	$get_post_type_comment = get_post_type($commentdata["comment_post_ID"]);
	if (!is_admin() && $get_post_type_comment != "product") {
		$the_captcha = 0;
		if ($get_post_type_comment == ask_questions_type || $get_post_type_comment == ask_asked_questions_type) {
			$the_captcha = askme_options("the_captcha_answer");
		}else {
			$the_captcha = askme_options("the_captcha_comment");
		}
		$captcha_users = askme_options("captcha_users");
		$captcha_style = askme_options("captcha_style");
		$captcha_question = askme_options("captcha_question");
		$captcha_answer = askme_options("captcha_answer");
		$show_captcha_answer = askme_options("show_captcha_answer");
		if ($the_captcha == 1 && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
			if ($captcha_style == "google_recaptcha") {
				if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
					$secretKey = askme_options("secret_key_recaptcha");
					$data_remote = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);
					if (is_wp_error($data_remote)) {
						if (defined('DOING_AJAX') && DOING_AJAX) {
							die(sprintf(esc_html__('%1$s ERROR %2$s: Robot verification failed, Please try again.','vbegy'),'<strong>','</strong>'));
						}else {
							wp_die(sprintf(esc_html__('%1$s ERROR %2$s: Robot verification failed, Please try again.','vbegy'),'<strong>','</strong>'));
						}
						exit;
					}else {
						$json = json_decode($data_remote['body'],true);
					}
					if ((isset($json["success"]) && $json["success"] == true) || (isset($json["error-codes"]) && isset($json["error-codes"][0]) && $json["error-codes"][0] == "timeout-or-duplicate")) {
						//success
					}else {
						if (defined('DOING_AJAX') && DOING_AJAX) {
							die(sprintf(esc_html__('%1$s ERROR %2$s: Robot verification failed, Please try again.','vbegy'),'<strong>','</strong>'));
						}else {
							wp_die(sprintf(esc_html__('%1$s ERROR %2$s: Robot verification failed, Please try again.','vbegy'),'<strong>','</strong>'));
						}
						exit;
					}
				}else {
					if (defined('DOING_AJAX') && DOING_AJAX) {
						die(sprintf(esc_html__('%1$s ERROR %2$s: Please check on the reCAPTCHA box.','vbegy'),'<strong>','</strong>'));
					}else {
						wp_die(sprintf(esc_html__('%1$s ERROR %2$s: Please check on the reCAPTCHA box.','vbegy'),'<strong>','</strong>'));
					}
					exit;
				}
			}else {
				if (empty($_POST["ask_captcha"])) {
					if (defined('DOING_AJAX') && DOING_AJAX)
						wp_die(__("<strong>ERROR</strong>: please type a captcha.","vbegy"));
					else
						die(__("<strong>ERROR</strong>: please type a captcha.","vbegy"));
					exit;
				}
				if ($captcha_style == "question_answer") {
					if ($captcha_answer != $_POST["ask_captcha"]) {
						if (defined('DOING_AJAX') && DOING_AJAX)
							wp_die(esc_html__('The captcha is incorrect, please try again.','vbegy'));
						else
							die(esc_html__('The captcha is incorrect, please try again.','vbegy'));
						exit;
					}
				}else {
					if (isset($_SESSION["security_code"]) && $_SESSION["security_code"] != $_POST["ask_captcha"]) {
						if (defined('DOING_AJAX') && DOING_AJAX)
							wp_die(esc_html__('The captcha is incorrect, please try again.','vbegy'));
						else
							die(esc_html__('The captcha is incorrect, please try again.','vbegy'));
						exit;
					}
				}
			}
		}

		if ($get_post_type_comment == ask_questions_type || $get_post_type_comment == ask_asked_questions_type) {
			$answer_video = askme_options("answer_video");
			if ($answer_video == 1 && isset($_POST['video_answer_description']) && $_POST['video_answer_description'] == "on" && empty($_POST['video_answer_id'])) {
				wp_die(esc_html__("There are required fields (Video ID).","vbegy"));
				exit;
			}
			
			if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');					
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
				if (!isset($data['mobile']) && !in_array($_FILES['featured_image']['type'],$types)) :
					wp_die(__("Attachment Error, Please upload image only.","vbegy"));
					exit;
				endif;
			endif;

			$answer_anonymously = askme_options("answer_anonymously");
			if ($answer_anonymously == 1) {
				if (isset($_POST['anonymously_answer']) && $_POST['anonymously_answer'] == 1) {
					$commentdata["user_ID"] = 0;
					$commentdata["user_id"] = 0;
					$commentdata["comment_author"] = "";
					$commentdata["comment_author_email"] = "";
					$commentdata["comment_author_url"] = "";
				}
			}
		}
		$terms_active_comment = askme_options("terms_active_comment");
		if (isset($_POST['agree_terms']) && $_POST['agree_terms'] == "on") {
			$_POST['agree_terms'] = 1;
		}
		if ($terms_active_comment == 1 && $_POST['agree_terms'] != 1) {
			wp_die(esc_html__("There are required fields (Agree of the terms).","vbegy"));
			exit;
		}
	}
	$commentdata["comment_content"] = askme_kses_stip($commentdata['comment_content'],"","yes");
	return $commentdata;
}
/* comment_question */
add_action ('comment_post','comment_question');
function comment_question($comment_id) {
	$get_comment = get_comment($comment_id);
	$post_id = $get_comment->comment_post_ID;
	$get_post = get_post($post_id);
	$user_id = get_current_user_id();
	if ($get_post->post_type == ask_questions_type || $get_post->post_type == ask_asked_questions_type) {
		$comment_user_id = $get_comment->user_id;
		add_comment_meta($comment_id,'comment_type',"question");
		add_comment_meta($comment_id,'comment_vote',0);
		$question_user_id = get_post_meta($post_id,"user_id",true);
		if ($question_user_id != "" && $question_user_id > 0) {
			add_comment_meta($comment_id,"answer_question_user","answer_question_user");
		}

		$answer_anonymously = askme_options("answer_anonymously");
		if ($answer_anonymously == 1) {
			if (isset($_POST['anonymously_answer']) && $_POST['anonymously_answer'] == 1) {
				$anonymously_answer = true;
			}
		}
		
		if (isset($_POST["private_answer"]) && $_POST["private_answer"] == 1) {
			add_comment_meta($comment_id,"private_answer",1);
		}
		
		if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');					
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$comment_attachment = wp_handle_upload($_FILES['attachment'],array('test_form' => false),current_time('mysql'));
			if (isset($comment_attachment['error'])) :
				wp_die('Attachment Error: ' . $comment_attachment['error']);
				exit;
			endif;
			$comment_attachment_data = array(
				'post_mime_type' => $comment_attachment['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_attachment['file'])),
				'post_content'   => '',
				'post_status'	=> 'inherit',
				'post_author'	=> (isset($anonymously_answer)?0:($comment_user_id != "" || $comment_user_id != 0?$comment_user_id:0))
			);
			$comment_attachment_id = wp_insert_attachment($comment_attachment_data,$comment_attachment['file'],$post_id);
			$comment_attachment_metadata = wp_generate_attachment_metadata($comment_attachment_id,$comment_attachment['file']);
			wp_update_attachment_metadata($comment_attachment_id, $comment_attachment_metadata);
			add_comment_meta($comment_id,'added_file',$comment_attachment_id);
		endif;
		
		if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');					
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			$comment_featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form' => false),current_time('mysql'));
			if (isset($comment_featured_image['error'])) :
				wp_die('Attachment Error: ' . $comment_featured_image['error']);
				exit;
			endif;
			$comment_featured_image_data = array(
				'post_mime_type' => $comment_featured_image['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_featured_image['file'])),
				'post_content'   => '',
				'post_status'	=> 'inherit',
				'post_author'	=> (isset($anonymously_answer)?0:($comment_user_id != "" || $comment_user_id != 0?$comment_user_id:0))
			);
			$comment_featured_image_id = wp_insert_attachment($comment_featured_image_data,$comment_featured_image['file'],$post_id);
			$comment_featured_image_metadata = wp_generate_attachment_metadata($comment_featured_image_id,$comment_featured_image['file']);
			wp_update_attachment_metadata($comment_featured_image_id, $comment_featured_image_metadata);
			add_comment_meta($comment_id,'featured_image',$comment_featured_image_id);
		endif;

		$answer_video = askme_options("answer_video");
		if ($answer_video == 1) {
			if (isset($_POST['video_answer_description'])) {
				update_comment_meta($comment_id,'video_answer_description',esc_html($_POST['video_answer_description']));
			}
			
			if (isset($_POST['video_answer_type'])) {
				update_comment_meta($comment_id,'video_answer_type',esc_html($_POST['video_answer_type']));
			}
			
			if (isset($_POST['video_answer_id'])) {
				update_comment_meta($comment_id,'video_answer_id',esc_html($_POST['video_answer_id']));
			}
		}

		if (isset($anonymously_answer)) {
			update_comment_meta($comment_id,'anonymously_user',($get_current_user_id > 0?$get_current_user_id:"anonymously"));
		}
		
		if(!session_id()) session_start();
		if ($get_comment->comment_approved == 1) {
			askme_notifications_add_answer($get_comment,$get_post);
			$_SESSION['vbegy_session_answer'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Well Added","vbegy").'</span><br>'.__("The answer has added successfully.","vbegy").'</p></div>';	
			$another_user_id = $get_post->post_author;
			if ($user_id > 0) {
				askme_notifications_activities($user_id,"","",$post_id,$comment_id,"add_answer","activities","","answer","","answer");
			}
			update_comment_meta($comment_id,'comment_approved_before',"yes");
			update_post_meta($post_id,"comment_count",$get_post->comment_count);
		}else {
			$_SESSION['vbegy_session_answer'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Well Added","vbegy").'</span><br>'.__("The answer has added successfully, It's under review.","vbegy").'</p></div>';
			if ($user_id > 0) {
				askme_notifications_activities($user_id,"","","","","approved_answer","activities","","answer","","answer");
			}
		}
	}else {
		if ($get_comment->comment_approved == 1) {
			if ($user_id > 0) {
				askme_notifications_activities($user_id,"","",$post_id,$comment_id,"add_comment","activities");
			}
		}else {
			if ($user_id > 0) {
				askme_notifications_activities($user_id,"","","","","approved_comment","activities");
			}
		}
	}
	askme_after_add_comment($get_comment,$post_id);
}
/* Afrer added comment */
function askme_after_add_comment($get_comment,$post_id) {
	if ($get_comment->comment_approved == 1) {
		$count_post_all = get_post_meta($post_id,"count_post_all",true);
		$count_post_comments = get_post_meta($post_id,"count_post_comments",true);
		if ($count_post_all === "") {
			$count_post_all = askme_comment_counter($post_id,1);
			$count_post_comments = askme_comment_counter($post_id);
		}else {
			$count_post_all++;
			if ($get_comment->comment_parent == 0) {
				$count_post_comments++;
			}
		}
		update_post_meta($post_id,"count_post_all",$count_post_all);
		update_post_meta($post_id,"count_post_comments",$count_post_comments);
	}
}
/* Duplicate comments */
add_filter('duplicate_comment_id','askme_duplicate_comment_id',9,1);
function askme_duplicate_comment_id($dupe_id) {
	if ($dupe_id > 0) {
		$anonymously_user = get_comment_meta($dupe_id,'anonymously_user',true);
		if ($anonymously_user != "") {
			return 0;
		}
	}else {
		return $dupe_id;
	}
}
/* Notifications add answer */
function askme_notifications_add_answer($comment,$get_post) {
	$comment_id = $comment->comment_ID;
	$post_id = $comment->comment_post_ID;
	$comment_user_id = $comment->user_id;
	$post_author = $get_post->post_author;
	$post_title = $get_post->post_title;
	$user_id_question = get_post_meta($post_id,"user_id",true);
	if (empty($user_id_question)) {
		$question_sort_option = "ask_question_items";
	}else {
		$question_sort_option = "ask_user_items";
	}
	$question_sort = askme_options($question_sort_option);
	$active_notified = (isset($question_sort["remember_answer"]["value"]) && $question_sort["remember_answer"]["value"] == "remember_answer"?1:0);
	if ($active_notified == 1) {
		$remember_answer = get_post_meta($post_id,"remember_answer",true);
		if ($remember_answer == 1 && $post_author != $comment_user_id) {
			$the_name = $comment->comment_author;
			if ($post_author != 0) {
				$get_the_author = get_user_by("id",$post_author);
				$the_mail = $get_the_author->user_email;
				$the_author = $get_the_author->display_name;
			}else {
				$the_mail = get_post_meta($post_id,'question_email',true);
				$the_author = get_post_meta($post_id,'question_username',true);
				$the_author = ($the_author != ""?$the_author:esc_html__("Anonymous","vbegy"));
			}
			if ($the_mail != "") {
				$send_text = askme_send_mail(
					array(
						'content'          => askme_options("email_notified_answer"),
						'post_id'          => $post_id,
						'comment_id'       => $comment_id,
						'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
						'received_user_id' => (isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$post_author),
					)
				);
				$email_title = askme_options("title_notified_answer");
				$email_title = ($email_title != ""?$email_title:esc_html__("Answer to your question","vbegy"));
				$email_title = askme_send_mail(
					array(
						'content'          => $email_title,
						'title'            => true,
						'break'            => '',
						'post_id'          => $post_id,
						'comment_id'       => $comment_id,
						'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
						'received_user_id' => (isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$post_author),
					)
				);
				askme_send_mails(
					array(
						'toEmail'     => $the_mail,
						'toEmailName' => $the_author,
						'title'       => $email_title,
						'message'     => $send_text,
					)
				);
			}
		}
	}
	
	$question_follow = askme_options("question_follow");
	$following_questions = get_post_meta($post_id,"following_questions",true);
	if ($question_follow == 1 && isset($following_questions) && is_array($following_questions)) {
		$email_follow_question = askme_options("email_follow_question");
		$email_title = askme_options("title_follow_question");
		$email_title = ($email_title != ""?$email_title:esc_html__("Hi there","vbegy"));
		foreach ($following_questions as $user) {
			if ($user_id_question != $user && $user > 0 && $comment_user_id != $user) {
				$author_user_email = get_the_author_meta("user_email",$user);
				if ($author_user_email != "") {
					$author_display_name = get_the_author_meta("display_name",$user);
					$send_text = askme_send_mail(
						array(
							'content'          => $email_follow_question,
							'post_id'          => $post_id,
							'comment_id'       => $comment_id,
							'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
							'received_user_id' => $user,
						)
					);
					$email_title = askme_send_mail(
						array(
							'content'          => $email_title,
							'title'            => true,
							'break'            => '',
							'post_id'          => $post_id,
							'comment_id'       => $comment_id,
							'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
							'received_user_id' => $user,
						)
					);
					askme_send_mails(
						array(
							'toEmail'     => esc_html($author_user_email),
							'toEmailName' => esc_html($author_display_name),
							'title'       => $email_title,
							'message'     => $send_text,
						)
					);
				}
			}
			$yes_private_answer = ask_private_answer($comment_id,$comment_user_id,$user,$post_author);
			$another_user_id = $user;
			if ($another_user_id > 0 && $comment_user_id != $another_user_id && $yes_private_answer == 1) {
				askme_notifications_activities($another_user_id,$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_question_follow","notifications","","answer");
			}
		}
	}
	
	$active_points = askme_options("active_points");
	if ($comment_user_id != 0) {
		$user_data = get_user_by("id",$comment_user_id);
		if ($comment_user_id != $post_author && $active_points == 1) {
			$_points = get_user_meta($comment_user_id,$user_data->user_login."_points",true);
			$_points++;
			
			update_user_meta($comment_user_id,$user_data->user_login."_points",$_points);
			add_user_meta($comment_user_id,$user_data->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),(askme_options("point_add_comment") != ""?askme_options("point_add_comment"):2),"+","answer_question",$post_id,$comment->comment_ID));
		
			$points_user = get_user_meta($comment_user_id,"points",true);
			update_user_meta($comment_user_id,"points",$points_user+(askme_options("point_add_comment") != ""?askme_options("point_add_comment"):2));
		}
		
		$add_answer = get_user_meta($comment_user_id,"add_answer_all",true);
		$add_answer_m = get_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),true);
		$add_answer_d = get_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),true);
		if ($add_answer_d == "" or $add_answer_d == 0) {
			update_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),1);
		}else {
			update_user_meta($comment_user_id,"add_answer_d_".date_i18n('d_m_Y',current_time('timestamp')),$add_answer_d+1);
		}
		
		if ($add_answer_m == "" or $add_answer_m == 0) {
			update_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),1);
		}else {
			update_user_meta($comment_user_id,"add_answer_m_".date_i18n('m_Y',current_time('timestamp')),$add_answer_m+1);
		}
		
		if ($add_answer == "" or $add_answer == 0) {
			update_user_meta($comment_user_id,"add_answer_all",1);
		}else {
			update_user_meta($comment_user_id,"add_answer_all",$add_answer+1);
		}

	}	
	
	$user_is_comment = get_post_meta($post_id,"user_is_comment",true);
	$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
	$yes_private_answer_1 = ask_private_answer($comment_id,$comment_user_id,$post_author,$post_author);
	$yes_private_answer_2 = ask_private_answer($comment_id,$comment_user_id,$anonymously_user,$post_author);
	if (($yes_private_answer_2 == 1 && $post_author > 0 && $comment_user_id != $post_author) || ($yes_private_answer_2 == 1 && $anonymously_user > 0 && $comment_user_id != $anonymously_user)) {
		askme_notifications_activities(($post_author > 0?$post_author:$anonymously_user),$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_question","notifications","","answer");
	}
	$yes_private_answer = ask_private_answer($comment_id,$comment_user_id,$user_id_question,$post_author);
	if ($yes_private_answer == 1 && $user_id_question != "") {
		if ($user_id_question != $comment_user_id) {
			askme_notifications_activities($user_id_question,$comment_user_id,($comment_user_id == 0?$comment->comment_author:0),$post_id,$comment_id,"answer_asked_question","notifications","","answer");
		}
		if ($user_is_comment != true && $user_id_question == $comment_user_id) {
			update_post_meta($post_id,"user_is_comment",true);
		}
	}

	if ($comment->comment_parent > 0) {
		$get_comment_reply_1 = get_comment($comment->comment_parent);
		askme_notification_reply_answer($get_comment_reply_1,$get_post,$post_id,$following_questions,$anonymously_user,$comment_user_id);
		if ($get_comment_reply_1->comment_parent > 0) {
			$get_comment_reply_2 = get_comment($get_comment_reply_1->comment_parent);
			askme_notification_reply_answer($get_comment_reply_2,$get_post,$post_id,$following_questions,$anonymously_user,$comment_user_id);
		}
	}
}
/* Notification for the reply on the answer */
function askme_notification_reply_answer($comment,$get_post,$post_id,$following_questions,$anonymously_user,$comment_user_id) {
	$question_follow = askme_options("question_follow");
	$not_in_the_follow = true;
	if ($question_follow == 1 && isset($following_questions) && is_array($following_questions) && in_array($comment->user_id,$following_questions)) {
		$not_in_the_follow = false;
	}
	if ($comment->user_id > 0 && $comment->user_id != $comment_user_id && $comment->user_id != $get_post->post_author && $comment->user_id != $anonymously_user && $not_in_the_follow == true) {
		askme_notifications_activities($comment->user_id,$comment_user_id,"",$post_id,$comment->comment_ID,"reply_answer","notifications","","answer");
		$the_name = $comment->comment_author;
		if ($get_post->post_author != 0) {
			$get_the_author = get_user_by("id",$get_post->post_author);
			$the_mail = $get_the_author->user_email;
			$the_author = $get_the_author->display_name;
		}else {
			$the_mail = get_post_meta($post_id,'question_email',true);
			$the_author = get_post_meta($post_id,'question_username',true);
			$the_author = ($the_author != ""?$the_author:esc_html__("Anonymous","vbegy"));
		}
		if ($the_mail != "") {
			$send_text = askme_send_mail(
				array(
					'content'          => askme_options("email_notified_reply"),
					'post_id'          => $post_id,
					'comment_id'       => $comment->comment_ID,
					'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
					'received_user_id' => (isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$post_author),
				)
			);
			$email_title = askme_options("title_notified_reply");
			$email_title = ($email_title != ""?$email_title:esc_html__("Reply to your answer","vbegy"));
			$email_title = askme_send_mail(
				array(
					'content'          => $email_title,
					'title'            => true,
					'break'            => '',
					'post_id'          => $post_id,
					'comment_id'       => $comment->comment_ID,
					'sender_user_id'   => ($comment_user_id > 0?$comment_user_id:$comment),
					'received_user_id' => (isset($anonymously_user) && $anonymously_user > 0?$anonymously_user:$post_author),
				)
			);
			askme_send_mails(
				array(
					'toEmail'     => $the_mail,
					'toEmailName' => $the_author,
					'title'       => $email_title,
					'message'     => $send_text,
				)
			);
		}
	}
}
/* askme_pre_comment_approved */
function askme_pre_comment_approved($approved,$commentdata) {
	if (!is_user_logged_in && $approved != "spam") {
		$comment_unlogged = askme_options("comment_unlogged");
		$approved = ($comment_unlogged == "draft"?0:1);
	}
	return $approved;
}
add_filter('pre_comment_approved','askme_pre_comment_approved','99',2);
/* askme_approve_comment_callback */
add_action('transition_comment_status','askme_approve_comment_callback',10,3);
function askme_approve_comment_callback($new_status,$old_status,$comment) {
	if ($old_status != $new_status) {
		$post_id = $comment->comment_post_ID;
		$count_post_all = get_post_meta($post_id,"count_post_all",true);
		$count_post_comments = get_post_meta($post_id,"count_post_comments",true);
		if ($count_post_all === "") {
			$count_post_all = askme_comment_counter($post_id,1);
			$count_post_comments = askme_comment_counter($post_id);
		}else {
			if ($new_status == "approved") {
				$count_post_all++;
				if ($comment->comment_parent == 0) {
					$count_post_comments++;
				}
			}else if ($old_status == "approved") {
				$count_post_all--;
				if ($comment->comment_parent == 0) {
					$count_post_comments--;
				}
			}
		}
		update_post_meta($post_id,"count_post_all",($count_post_all < 0?0:$count_post_all));
		update_post_meta($post_id,"count_post_comments",($count_post_comments < 0?0:$count_post_comments));
		$comment_id = $comment->comment_ID;
		$get_post = get_post($post_id);
		$post_type = $get_post->post_type;
		if ($post_type == ask_questions_type || $post_type == ask_asked_questions_type) {
			$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
			if (isset($best_answer_comment) && isset($comment_id) && $best_answer_comment == "best_answer_comment") {
				$count_best_answer = true;
			}
			if ($new_status == "approved") {
				if (isset($count_best_answer)) {
					update_post_meta($post_id,"the_best_answer",$comment_id);
				}
			}else if ($old_status == "approved") {
				if (isset($count_best_answer) && $new_status != "trash" && $new_status != "delete") {
					delete_post_meta($post_id,"the_best_answer");
				}
			}
		}
		if ($new_status == 'approved') {
			$comment_approved_before = get_comment_meta($comment_id,'comment_approved_before',true);
			if ($comment_approved_before != "yes") {
				if ($post_type == ask_questions_type || $post_type == ask_asked_questions_type) {
					$another_user_id = $get_post->post_author;
					$user_id = $comment->user_id;
					if ($user_id > 0) {
						askme_notifications_activities($user_id,"","",$post_id,$comment_id,"approved_answer","notifications","","answer");
					}
					askme_notifications_add_answer($comment,$get_post);
				}else {
					if ($comment->user_id > 0) {
						askme_notifications_activities($comment->user_id,"","",$post_id,$comment_id,"approved_comment","notifications");
					}
				}
			}
			update_comment_meta($comment_id,'comment_approved_before',"yes");
		}
	}
}
/* Notifications ask question */
function askme_notifications_ask_question($post_id,$question_username,$user_id,$not_user,$anonymously_user,$get_current_user_id,$approved = false) {
	$send_email_and_notification_question = askme_options("send_email_and_notification_question");
	$send_email_new_question_value = "send_email_new_question";
	$send_email_question_groups_value = "send_email_question_groups";
	if ($send_email_and_notification_question == "both") {
		$send_email_new_question_value = "send_email_new_question_both";
		$send_email_question_groups_value = "send_email_question_groups_both";
	}
	$send_email_new_question = askme_options($send_email_new_question_value);
	$the_author = 0;
	if ($not_user == 0) {
		$the_author = $question_username;
	}
	if ($user_id == "") {
		$private_question = get_post_meta($post_id,"private_question",true);
		if ($send_email_new_question == 1) {
			$email_title = askme_options("title_new_questions");
			$email_title = ($email_title != ""?$email_title:__("New question","vbegy"));
			$user_group = askme_options($send_email_question_groups_value);
			if (is_array($user_group) && !empty($user_group)) {
				foreach ($user_group as $key_group => $value_group) {
					if ($value_group == 1) {
						$user_groups[] = $key_group;
					}
				}
			}
			if (isset($user_groups) && is_array($user_groups) && !empty($user_groups)) {
				$users = get_users(array("meta_query" => array(array("key" => "received_email","compare" => "=","value" => 1)),"role__in" => $user_groups,"fields" => array("ID","user_email","display_name")));
				if (isset($users) && is_array($users) && !empty($users)) {
					foreach ($users as $key => $value) {
						$another_user_id = $value->ID;
						if (is_super_admin($another_user_id) && ($private_question == "on" || $private_question == 1) && (($another_user_id != $anonymously_user && $anonymously_user > 0) || ($another_user_id != $not_user && $not_user > 0))) {
							if ($send_email_and_notification_question == "both") {
								askme_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",ask_questions_type);
							}
						}else {
							if ($not_user != $another_user_id) {
								$yes_private = ask_private($post_id,$not_user,$another_user_id);
								if ($yes_private == 1) {
									$send_text = askme_send_mail(
										array(
											'content'          => askme_options("email_new_questions"),
											'post_id'          => $post_id,
											'sender_user_id'   => ($anonymously_user > 0?"anonymous":$not_user),
											'received_user_id' => $another_user_id,
										)
									);
									$email_title = askme_send_mail(
										array(
											'content'          => $email_title,
											'title'            => true,
											'break'            => '',
											'post_id'          => $post_id,
											'sender_user_id'   => ($anonymously_user > 0?"anonymous":$not_user),
											'received_user_id' => $another_user_id,
										)
									);
									askme_send_mails(
										array(
											'toEmail'     => esc_html($value->user_email),
											'toEmailName' => esc_html($value->display_name),
											'title'       => $email_title,
											'message'     => $send_text,
										)
									);
									if ($send_email_and_notification_question == "both" && $another_user_id > 0 && $not_user != $another_user_id) {
										askme_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",ask_questions_type);
									}
								}
							}
						}
					}
				}
			}
		}
		if ($send_email_and_notification_question == "separately") {
			$send_notification_new_question = askme_options("send_notification_new_question");
			if ($send_notification_new_question == 1) {
				$user_group = askme_options("send_notification_question_groups");
				if (is_array($user_group) && !empty($user_group)) {
					foreach ($user_group as $key_group => $value_group) {
						if ($value_group == 1) {
							$user_groups[] = $key_group;
						}
					}
				}
				if (isset($user_groups) && is_array($user_groups) && !empty($user_groups)) {
					$users = get_users(array("role__in" => $user_groups,"fields" => array("ID","user_email","display_name")));
					if (isset($users) && is_array($users) && !empty($users)) {
						foreach ($users as $key => $value) {
							$another_user_id = $value->ID;
							if (is_super_admin($another_user_id) && ($private_question == "on" || $private_question == 1) && (($another_user_id != $anonymously_user && $anonymously_user > 0) || ($another_user_id != $not_user && $not_user > 0))) {
								askme_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",ask_questions_type);
							}else {
								if ($not_user != $another_user_id) {
									$yes_private = ask_private($post_id,$not_user,$another_user_id);
									if ($yes_private == 1) {
										if ($another_user_id > 0 && $not_user != $another_user_id) {
											askme_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_question","notifications","",ask_questions_type);
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
/* Notifications add post */
function askme_notifications_add_post($post_id,$post_username,$not_user,$get_current_user_id,$approved = false) {
	$send_email_and_notification_post = askme_options("send_email_and_notification_post");
	$send_email_new_post_value = "send_email_new_post";
	$send_email_post_groups_value = "send_email_post_groups";
	if ($send_email_and_notification_post == "both") {
		$send_email_new_post_value = "send_email_new_post_both";
		$send_email_post_groups_value = "send_email_post_groups_both";
	}
	$send_email_new_post = askme_options($send_email_new_post_value);
	$the_author = 0;
	if ($not_user == 0) {
		$the_author = $post_username;
	}
	if ($send_email_new_post == 1) {
		$email_title = askme_options("title_new_posts");
		$email_title = ($email_title != ""?$email_title:__("New post","vbegy"));
		$user_group = askme_options($send_email_post_groups_value);
		if (is_array($user_group) && !empty($user_group)) {
			foreach ($user_group as $key_group => $value_group) {
				if ($value_group == 1) {
					$user_groups[] = $key_group;
				}
			}
		}
		if (isset($user_groups) && is_array($user_groups) && !empty($user_groups)) {
			$users = get_users(array("meta_query" => array(array("key" => "received_email","compare" => "=","value" => 1)),"role__in" => $user_groups,"fields" => array("ID","user_email","display_name")));
			if (isset($users) && is_array($users) && !empty($users)) {
				foreach ($users as $key => $value) {
					$another_user_id = $value->ID;
					if ($another_user_id > 0 && $not_user != $another_user_id) {
						$send_text = askme_send_mail(
							array(
								'content'          => askme_options("email_new_posts"),
								'post_id'          => $post_id,
								'sender_user_id'   => $not_user,
								'received_user_id' => $another_user_id,
							)
						);
						$email_title = askme_send_mail(
							array(
								'content'          => $email_title,
								'title'            => true,
								'break'            => '',
								'post_id'          => $post_id,
								'sender_user_id'   => $not_user,
								'received_user_id' => $another_user_id,
							)
						);
						askme_send_mails(
							array(
								'toEmail'     => esc_html($value->user_email),
								'toEmailName' => esc_html($value->display_name),
								'title'       => $email_title,
								'message'     => $send_text,
							)
						);
						if ($send_email_and_notification_post == "both") {
							askme_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_post","notifications","","post");
						}
					}
				}
			}
		}
	}
	if ($send_email_and_notification_post == "separately") {
		$send_notification_new_post = askme_options("send_notification_new_post");
		if ($send_notification_new_post == 1) {
			$user_group = askme_options("send_notification_post_groups");
			if (is_array($user_group) && !empty($user_group)) {
				foreach ($user_group as $key_group => $value_group) {
					if ($value_group == 1) {
						$user_groups[] = $key_group;
					}
				}
			}
			if (isset($user_groups) && is_array($user_groups) && !empty($user_groups)) {
				$users = get_users(array("role__in" => $user_groups,"fields" => array("ID","user_email","display_name")));
				if (isset($users) && is_array($users) && !empty($users)) {
					foreach ($users as $key => $value) {
						$another_user_id = $value->ID;
						if ($another_user_id > 0 && $not_user != $another_user_id) {
							askme_notifications_activities($another_user_id,$not_user,$the_author,$post_id,"","add_post","notifications","","post");
						}
					}
				}
			}
		}
	}
}
/* new_post */
function new_post() {
	if (isset($_POST)) :
		$return = process_new_posts($_POST);
		if (is_wp_error($return)) :
   			echo '<div class="ask_error"><span><p>'.$return->get_error_message().'</p></span></div>';
   		else :
   			$get_post = get_post($return);
   			if ($get_post->post_type == ask_questions_type || $get_post->post_type == ask_asked_questions_type) {
   				if (is_user_logged_in) {
   					$question_publish = askme_options("question_publish");
   				}else {
   					$question_publish = askme_options("question_publish_unlogged");
   				}
	   			$user_id = get_current_user_id();
	   			$get_question_user = get_post_meta($get_post->ID,"user_id",true);
	   			if ($question_publish == "draft" && !is_super_admin($user_id)) {
					if(!session_id()) session_start();
					$_SESSION['vbegy_session'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Well Added","vbegy").'</span><br>'.__("The question has added successfully, It's under review.","vbegy").'</p></div>';
					
					if ($user_id > 0) {
						askme_notifications_activities($user_id,"","","","","approved_question","activities","",ask_questions_type);
					}
					
					$send_email_draft_questions = askme_options("send_email_draft_questions");
					if ($send_email_draft_questions == 1) {
						$send_text = askme_send_mail(
	   						array(
								'content' => askme_options("email_draft_questions"),
								'post_id' => $return,
							)
	   					);
   						$email_title = askme_options("title_new_draft_questions");
   						$email_title = ($email_title != ""?$email_title:esc_html__("New question for review","vbegy"));
   						$email_title = askme_send_mail(
   							array(
								'content' => $email_title,
								'title'   => true,
								'break'   => '',
								'post_id' => $return,
							)
   						);
   						askme_send_mails(
							array(
								'title'   => $email_title,
								'message' => $send_text,
							)
						);
					}
					
					wp_redirect(esc_url(($get_question_user != ""?vpanel_get_user_url($get_question_user):home_url('/'))));
				}else {
					$anonymously_user = get_post_meta($get_post->ID,"anonymously_user",true);
					$question_username = get_post_meta($get_post->ID,"question_username",true);
					$not_user = ($get_post->post_author > 0?$get_post->post_author:0);
					askme_notifications_ask_question($get_post->ID,$question_username,$get_question_user,$not_user,$anonymously_user,$user_id);
					update_post_meta($return,'post_approved_before',"yes");
					if ($get_post->post_author != $get_question_user && $get_question_user > 0) {
						askme_notifications_activities($get_question_user,$get_post->post_author,"",$get_post->ID,"","add_question_user","notifications","",ask_questions_type);
					}
					if ($user_id > 0) {
						askme_notifications_activities($user_id,"","",$return,"","add_question","activities","",ask_questions_type);
					}
					if ($get_question_user != "") {
						if(!session_id()) session_start();
						$_SESSION['vbegy_session_user'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Well Added","vbegy").'</span><br>'.__("The question has added successfully, When the user answered will view the question.","vbegy").'</p></div>';
					}
					wp_redirect(($get_question_user != ""?vpanel_get_user_url($get_question_user):get_permalink($return)));
				}
			}else if ($get_post->post_type == "post") {
				if (is_user_logged_in) {
					$post_publish = askme_options("post_publish");
				}else {
					$post_publish = askme_options("post_publish_unlogged");
				}
				$user_id = get_current_user_id();
				if ($post_publish == "draft" && !is_super_admin($user_id)) {
					if(!session_id()) session_start();
					$_SESSION['vbegy_session_post'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Well Added","vbegy").'</span><br>'.__("The post has added successfully, It's under review.","vbegy").'</p></div>';
					
					if ($user_id > 0) {
						askme_notifications_activities($user_id,"","","","","approved_post","activities");
					}
					
					$send_email_draft_posts = askme_options("send_email_draft_posts");
					if ($send_email_draft_posts == 1) {
						$send_text = askme_send_mail(
							array(
								'content' => askme_options("email_draft_posts"),
								'post_id' => $return,
							)
						);
						$email_title = askme_options("title_new_draft_posts");
						$email_title = ($email_title != ""?$email_title:esc_html__("New post for review","vbegy"));
						$email_title = askme_send_mail(
							array(
								'content' => $email_title,
								'title'   => true,
								'break'   => '',
								'post_id' => $return,
							)
						);
						askme_send_mails(
							array(
								'title'   => $email_title,
								'message' => $send_text,
							)
						);
					}
					
					wp_redirect(esc_url(home_url('/')));
				}else {
					if ($user_id > 0) {
						askme_notifications_activities($user_id,"","",$return,"","add_post","activities");
					}
					$post_username = get_post_meta($get_post->ID,"post_username",true);
					$not_user = ($get_post->post_author > 0?$get_post->post_author:0);
					askme_notifications_add_post($get_post->ID,$post_username,$not_user,$user_id);
					update_post_meta($return,'post_approved_before',"yes");
					wp_redirect(get_permalink($return));
				}
			}
			exit;
   		endif;
	endif;
}
add_action('new_post','new_post');
/* process_new_posts */
function process_new_posts($data) {
	global $posted;
	set_time_limit(0);
	$errors = new WP_Error();
	$posted = array();
	
	$post_type = (isset($data["post_type"]) && $data["post_type"] != ""?$data["post_type"]:"");
	$user_get_current_user_id = get_current_user_id();
	if ($post_type == "add_question") {
		$ask_question_no_register = askme_options("ask_question_no_register");
		$username_email_no_register = askme_options("username_email_no_register");
		$question_points_active = askme_options("question_points_active");
		$question_points = askme_options("question_points");
		$points = get_user_meta($user_get_current_user_id,"points",true);
		$points = ($points != ""?$points:0);
		
		if (empty($data['user_id'])) {
			$fields = array(
				'title','category','comment','question_poll','remember_answer','private_question','anonymously_question',ask_question_tags,'video_type','video_id','video_description','sticky','attachment','attachment_m','featured_image','ask_captcha','username','email','agree_terms'
			);
		}else {
			$fields = array(
				'title','comment','remember_answer','private_question','anonymously_question','ask_captcha','username','email','agree_terms','user_id'
			);
		}
		
		$fields = apply_filters((empty($data['user_id'])?'askme_add_question_fields':'askme_add_user_question_fields'),$fields,"add");
		
		foreach ($fields as $field) :
			if (isset($data[$field])) $posted[$field] = $data[$field]; else $posted[$field] = '';
		endforeach;
		
		$payment_group = askme_options("payment_group");
		$pay_ask = askme_options("pay_ask");
		$custom_permission = askme_options("custom_permission");
		$ask_question = askme_options("ask_question");
		if (is_user_logged_in) {
			$user_is_login = get_userdata($user_get_current_user_id);
			$user_login_group = key($user_is_login->caps);
			$roles = $user_is_login->allcaps;
			$_allow_to_ask = get_user_meta($user_get_current_user_id,$user_get_current_user_id."_allow_to_ask",true);
		}
		
		if (($custom_permission == 1 && is_user_logged_in && !is_super_admin($user_get_current_user_id) && empty($roles["ask_question"])) || ($custom_permission == 1 && !is_user_logged_in && $ask_question != 1)) {
			$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Sorry, you do not have permission to add a question.","vbegy"));
			if (!is_user_logged_in) {
				$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("You must login to ask a question.","vbegy"));
			}
		}else if (!is_user_logged_in && $ask_question_no_register != 1) {
			$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("You must login to ask a question.","vbegy"));
		}else {
			if (!is_user_logged_in && $pay_ask == 1) {
				$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("You must login to ask a question.","vbegy"));
			}else {
				if (isset($_allow_to_ask) && (int)$_allow_to_ask < 1 && $pay_ask == 1 && $payment_group[$user_login_group] != 1) {
					$errors->add('required','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("You need to pay first.","vbegy"));
				}
			}
		}
		
		if ($points < $question_points && $question_points_active == 1) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.sprintf(__("Sorry do not have the minimum points Please do answer questions,even gaining points (The minimum points = %s).","vbegy"),$question_points));
		
		if (!is_user_logged_in && $ask_question_no_register == 1 && $username_email_no_register == 1 && $user_get_current_user_id == 0) {
			if (empty($posted['username'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (username).","vbegy"));
			if (empty($posted['email'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (email).","vbegy"));
			if (!is_email($posted['email'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Please write correctly email.","vbegy"));
		}
		
		/* Validate Required Fields */
		if (empty($posted['user_id'])) {
			$question_sort_option = "ask_question_items";
			$comment_question = askme_options("comment_question");
			$editor_question_details = askme_options("editor_question_details");
			$title_excerpt_type = askme_options("title_excerpt_type");
			$title_excerpt = askme_options("title_excerpt");
		}else {
			$question_sort_option = "ask_user_items";
			$comment_question = askme_options("content_ask_user");
			$editor_question_details = askme_options("editor_ask_user");
			$title_excerpt_type = askme_options("title_excerpt_type_user");
			$title_excerpt = askme_options("title_excerpt_user");
		}
		$question_sort = askme_options($question_sort_option);
		$the_captcha = askme_options("the_captcha");
		if (isset($question_sort) && is_array($question_sort)) {
			$question_sort = array_merge($question_sort,array("the_captcha" => array("value" => ($the_captcha == 1?"the_captcha":0))));
		}
		$title_question = ((isset($question_sort["title_question"]["value"]) && $question_sort["title_question"]["value"] == "title_question") || (isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] != "comment_question")?1:0);
		$category_question = (isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question"?1:0);
		$category_question_required = askme_options("category_question_required");
		$featured_image_question = (isset($question_sort["featured_image"]["value"]) && $question_sort["featured_image"]["value"] == "featured_image"?1:0);
		$video_desc_active = (isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active"?1:0);
		$terms_active = (isset($question_sort["terms_active"]["value"]) && $question_sort["terms_active"]["value"] == "terms_active"?1:0);
		if ($title_question !== 1 || $comment_question == 1) {
			$comment_question = "required";
		}

		if ($title_question === 1 && empty($posted['title'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (title).","vbegy"));
		if (empty($posted['user_id']) && ($category_question == 1 && $category_question_required == 1 && (empty($posted['category']) || $posted['category'] == '-1' || (is_array($posted['category']) && end($posted['category']) == '-1')))) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (category).","vbegy"));
		if (isset($posted['question_poll']) && $posted['question_poll'] == 1 && isset($data['ask'])) {
			foreach($data['ask'] as $ask) {
				if (empty($ask['ask']) && count($data['ask']) < 2) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Please enter at least two values in poll.","vbegy"));
			}
		}
		if ($comment_question == "required" && isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] == "comment_question" && empty($posted['comment'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (content).","vbegy"));
		if ($video_desc_active == 1 && $posted['video_description'] == 1 && empty($posted['video_id'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (Video ID).","vbegy"));
		
		askme_check_captcha($the_captcha,"question",$posted,$errors);
		
		if ($terms_active == 1 && $posted['agree_terms'] != 1) {
			$errors->add('required-terms',__("There are required fields (Agree of the terms).","vbegy"));
		}
		
		do_action((empty($data['user_id'])?'askme_add_question_errors':'askme_add_user_question_errors'),$errors,$posted,"add",$question_sort);
		
		if (sizeof($errors->errors)>0) return $errors;
		
		/* Create question */
		if (is_user_logged_in) {
			$question_publish = askme_options("question_publish");
		}else {
			$question_publish = askme_options("question_publish_unlogged");
		}
		
		if ($title_question === 1) {
			$question_title = $posted['title'];
		}else {
			$question_title = excerpt_any($title_excerpt,sanitize_text_field($posted['comment']),$title_excerpt_type);
		}
		
		$data_insert = array(
			'post_content' => ask_kses_stip_wpautop($posted['comment']),
			'post_title'   => sanitize_text_field($question_title),
			'post_status'  => ($question_publish == "draft" && !is_super_admin($user_get_current_user_id)?"draft":"publish"),
			'post_author'  => ((!is_user_logged_in && $ask_question_no_register == 1) || $posted['anonymously_question']?0:$user_get_current_user_id),
			'post_type'	   => (empty($data['user_id'])?ask_questions_type:ask_asked_questions_type),
		);
			
		$post_id = wp_insert_post($data_insert);
			
		if ($post_id == 0 || is_wp_error($post_id)) wp_die(__("Error in question.","vbegy"));
		
		if (empty($posted['user_id']) && $category_question == 1 && isset($posted['category']) && $posted['category']) {
			if (is_array($posted['category'])) {
				$cat_ids = array_map( 'intval', $posted['category'] );
				$cat_ids = array_unique( $cat_ids );
			}else {
				$cat_ids = array();
				$cat_ids[] = get_term_by('id',(is_array($posted['category'])?end($posted['category']):$posted['category']),ask_question_category)->slug;
			}
			wp_set_object_terms($post_id,(sizeof($cat_ids) > 0?$cat_ids:array()),ask_question_category);
		}
		
		if (isset($posted['question_poll']))  {
			update_post_meta($post_id,'question_poll',$posted['question_poll']);
		}else {
			update_post_meta($post_id,'question_poll',2);
		}
		
		if (isset($data['ask'])) {
			update_post_meta($post_id,'ask',$data['ask']);
		}
		
		if ($posted['remember_answer']) {
			update_post_meta($post_id,'remember_answer',$posted['remember_answer']);
		}
		
		if ($posted['private_question']) {
			update_post_meta($post_id,'private_question',$posted['private_question']);
			update_post_meta($post_id,'private_question_author',((!is_user_logged_in && $ask_question_no_register == 1) || $posted['anonymously_question']?0:$user_get_current_user_id));
		}
		
		if ($posted['anonymously_question']) {
			update_post_meta($post_id,'anonymously_question',$posted['anonymously_question']);
			update_post_meta($post_id,'anonymously_user',(is_user_logged_in?$user_get_current_user_id:0));
		}
		
		if ($video_desc_active == 1) {
			if ($posted['video_description']) {
				update_post_meta($post_id,'video_description',$posted['video_description']);
			}
			
			if ($posted['video_type']) {
				update_post_meta($post_id,'video_type',$posted['video_type']);
			}
				
			if ($posted['video_id']) {
				update_post_meta($post_id,'video_id',$posted['video_id']);	
			}
		}
		
		$sticky_questions = get_option('sticky_questions');
		$sticky_posts = get_option('sticky_posts');
		if (isset($posted['sticky']) && $posted['sticky'] == "sticky") {
			update_post_meta($post_id,'sticky',1);
			if (is_array($sticky_questions)) {
				if (!in_array($post_id,$sticky_questions)) {
					$array_merge = array_merge($sticky_questions,array($post_id));
					update_option("sticky_questions",$array_merge);
				}
			}else {
				update_option("sticky_questions",array($post_id));
			}
			if (is_array($sticky_posts)) {
				if (!in_array($post_id,$sticky_posts)) {
					$array_merge = array_merge($sticky_posts,array($post_id));
					update_option("sticky_posts",$array_merge);
				}
			}else {
				update_option("sticky_posts",array($post_id));
			}
		}else {
			if (is_array($sticky_questions) && in_array($post_id,$sticky_questions)) {
				$sticky_questions = remove_item_by_value($sticky_questions,$post_id);
				update_option('sticky_questions',$sticky_questions);
			}
			if (is_array($sticky_posts) && in_array($post_id,$sticky_posts)) {
				$sticky_posts = remove_item_by_value($sticky_posts,$post_id);
				update_option('sticky_posts',$sticky_posts);
			}
			delete_post_meta($post_id,'sticky');
		}
		
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		
		if (isset($_FILES['attachment_m'])) {
			$files = $_FILES['attachment_m'];
			if (isset($files) && $files) {
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name'	 => $files['name'][$key]["file_url"],
							'type'	 => $files['type'][$key]["file_url"],
							'tmp_name' => $files['tmp_name'][$key]["file_url"],
							'error'	=> $files['error'][$key]["file_url"],
							'size'	 => $files['size'][$key]["file_url"]
						);
						if ($files['error'][$key]["file_url"] != 0) {
							unset($files['name'][$key]);
							unset($files['type'][$key]);
							unset($files['tmp_name'][$key]);
							unset($files['error'][$key]);
							unset($files['size'][$key]);
						}
					}
				}
			}
			
			if (isset($files) && $files) {
				foreach ($files['name'] as $key => $value) {
					if ($files['name'][$key]) {
						$file = array(
							'name'	 => $files['name'][$key]["file_url"],
							'type'	 => $files['type'][$key]["file_url"],
							'tmp_name' => $files['tmp_name'][$key]["file_url"],
							'error'	=> $files['error'][$key]["file_url"],
							'size'	 => $files['size'][$key]["file_url"]
						);
						$attachment = wp_handle_upload($file,array('test_form' => false),current_time('mysql'));
						if (!isset($attachment['error']) && $attachment) :
							//$errors->add('upload-error',__("Attachment Error: ","vbegy") . $attachment['error']);
							$attachment_data = array(
								'post_mime_type' => $attachment['type'],
								'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
								'post_content'   => '',
								'post_status'	 => 'inherit',
								'post_author'    => ((!is_user_logged_in && $ask_question_no_register == 1) || $posted['anonymously_question']?0:$user_get_current_user_id),
							);
							$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$post_id);
							$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
							wp_update_attachment_metadata($attachment_id,$attachment_metadata);
							$attachment_m_array[] = array("added_file" => $attachment_id);
						endif;
					}
					if (get_post_meta($post_id,'attachment_m')) {
						delete_post_meta($post_id,'attachment_m');
					}
					if (isset($attachment_m_array)) {
						add_post_meta($post_id,'attachment_m',$attachment_m_array);
					}
				}
			}
		}
		
		/* Featured image */
		
		if ($featured_image_question == 1) {
			$featured_image = '';
			
			if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
				$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
				if (!isset($data['mobile']) && !in_array($_FILES['featured_image']['type'],$types)) :
					$errors->add('upload-error',__("Attachment Error, Please upload image only.","vbegy"));
					return $errors;
				endif;
				
				$featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form' => false),current_time('mysql'));
				
				if (isset($featured_image['error'])) :
					$errors->add('upload-error',__("Attachment Error: ","vbegy") . $featured_image['error']);
					return $errors;
				endif;
			endif;
			
			if ($featured_image) :
				$featured_image_data = array(
					'post_mime_type' => $featured_image['type'],
					'post_title'	 => preg_replace('/\.[^.]+$/','',basename($featured_image['file'])),
					'post_content'   => '',
					'post_status'	 => 'inherit',
					'post_author'    => ((!is_user_logged_in && $ask_question_no_register == 1) || $posted['anonymously_question']?0:$user_get_current_user_id),
				);
				$featured_image_id = wp_insert_attachment($featured_image_data,$featured_image['file'],$post_id);
				$featured_image_metadata = wp_generate_attachment_metadata($featured_image_id,$featured_image['file']);
				wp_update_attachment_metadata($featured_image_id, $featured_image_metadata);
				$set_post_thumbnail = set_post_thumbnail($post_id,$featured_image_id);
			endif;
		}
		
		/* Tags */
		
		if (empty($posted['user_id']) && isset($posted[ask_question_tags])) :
			$tags = explode(',',trim(stripslashes($posted[ask_question_tags])));
			$tags = array_map('strtolower',$tags);
			$tags = array_map('trim',$tags);
			wp_set_object_terms($post_id,(sizeof($tags) > 0?$tags:array()),ask_question_tags);
		endif;
		
		if (!is_user_logged_in && $ask_question_no_register == 1 && $user_get_current_user_id == 0) {
			if ($username_email_no_register == 1) {
				$question_username = sanitize_text_field($posted['username']);
				$question_email = sanitize_text_field($posted['email']);
				update_post_meta($post_id,'question_username',$question_username);
				update_post_meta($post_id,'question_email',$question_email);
			}else {
				update_post_meta($post_id,'question_no_username',"no_user");
			}
		}else {
			$user_id = $user_get_current_user_id;
			
			$pay_ask = askme_options("pay_ask");
			if ($pay_ask == 1) {
				$_allow_to_ask = get_user_meta($user_id,$user_id."_allow_to_ask",true);
				if ($_allow_to_ask == "" || $_allow_to_ask < 0) {
					$_allow_to_ask = 0;
				}
				$_allow_to_ask--;
				update_user_meta($user_id,$user_id."_allow_to_ask",($_allow_to_ask < 0?0:$_allow_to_ask));
				
				$_coupon = get_user_meta($user_id,$user_id."_coupon",true);
				$_coupon_value = get_user_meta($user_id,$user_id."_coupon_value",true);
				if (isset($_coupon) && $_coupon != "") {
					update_post_meta($post_id,'_coupon',$_coupon);
					delete_user_meta($user_id,$user_id."_coupon");
				}
				if (isset($_coupon_value) && $_coupon_value != "") {
					update_post_meta($post_id,'_coupon_value',$_coupon_value);
					delete_user_meta($user_id,$user_id."_coupon_value");
				}
				
				$_paid_question = get_user_meta($user_id,'_paid_question',true);
				if (isset($_paid_question) && $_paid_question != "") {
					update_post_meta($post_id,'_paid_question',$_paid_question);
					delete_user_meta($user_id,'_paid_question');
				}
				
				$item_transaction = get_user_meta($user_id,'item_transaction',true);
				if (isset($item_transaction) && $item_transaction != "") {
					update_post_meta($post_id,'item_transaction',$item_transaction);
					delete_user_meta($user_id,'item_transaction');
				}
				
				$paypal_sandbox = get_user_meta($user_id,'paypal_sandbox',true);
				if (isset($paypal_sandbox) && $paypal_sandbox != "") {
					update_post_meta($post_id,'paypal_sandbox',$paypal_sandbox);
					delete_user_meta($user_id,'paypal_sandbox');
				}
				
			}
			
			$point_add_question = askme_options("point_add_question");
			$active_points = askme_options("active_points");
			if ($point_add_question > 0 && $active_points == 1) {
				$current_user = get_user_by("id",$user_id);
				$_points = get_user_meta($user_id,$current_user->user_login."_points",true);
				$_points++;
			
				update_user_meta($user_id,$current_user->user_login."_points",$_points);
				add_user_meta($user_id,$current_user->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$point_add_question,"+","add_question",$post_id));
			
				$points_user = get_user_meta($user_id,"points",true);
				$last_points = $points_user+$point_add_question;
				update_user_meta($user_id,"points",$last_points);
			}
			
			if ($points && $question_points_active == 1) {
				$current_user = get_user_by("id",$user_id);
				$_points = get_user_meta($user_id,$current_user->user_login."_points",true);
				$_points++;
			
				update_user_meta($user_id,$current_user->user_login."_points",$_points);
				add_user_meta($user_id,$current_user->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$question_points,"-","question_point",$post_id));
			
				$points_user = get_user_meta($user_id,"points",true);
				$last_points = $points_user-$question_points;
				update_user_meta($user_id,"points",$last_points);
				
				update_post_meta($post_id,"point_back","yes");
				update_post_meta($post_id,"what_point",$question_points);
			}
		}
		
		if (isset($posted['user_id']) && $posted['user_id'] != "") {
			update_post_meta($post_id,'user_id',(int)$posted['user_id']);
		}

		if (is_user_logged_in) {
			delete_user_meta($user_id,"askme_title_before_payment");
			delete_user_meta($user_id,"askme_comment_before_payment");
		}
		
		/* The default meta */
		update_post_meta($post_id,"vbegy_layout","default");
		update_post_meta($post_id,"vbegy_home_template","default");
		update_post_meta($post_id,"vbegy_site_skin_l","default");
		update_post_meta($post_id,"vbegy_skin","default");
		update_post_meta($post_id,"vbegy_sidebar","default");
		update_post_meta($post_id,"post_from_front","from_front");
		update_post_meta($post_id,"count_post_all",0);
		update_post_meta($post_id,"count_post_comments",0);
		update_post_meta($post_id,"question_vote",0);

		do_action('new_questions',$post_id,$posted);
		do_action((empty($posted['user_id'])?"askme_finished_add_question":"askme_finished_add_user_question"),$post_id,$posted,"add",false);
	}else if ($post_type == "add_post") {
		$add_post_no_register = askme_options("add_post_no_register");
		
		$fields = array(
			'title','comment','category','post_tag','attachment','ask_captcha','agree_terms','username','email'
		);

		$fields = apply_filters('askme_add_post_fields',$fields,"add");
		
		foreach ($fields as $field) :
			if (isset($data[$field])) $posted[$field] = $data[$field]; else $posted[$field] = '';
		endforeach;
		
		if (!is_user_logged_in && $add_post_no_register == 1 && $user_get_current_user_id == 0) {
			if (empty($posted['username'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (username).","vbegy"));
			if (empty($posted['email'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (email).","vbegy"));
			if (!is_email($posted['email'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Please write correctly email.","vbegy"));
		}
		
		/* Validate Required Fields */
		$add_post_items = askme_options("add_post_items");
		$content_post = (isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post"?1:0);
		$terms_active = (isset($add_post_items["terms_active"]["value"]) && $add_post_items["terms_active"]["value"] == "terms_active"?1:0);

		if (empty($posted['title'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (title).","vbegy"));
		if ((empty($posted['category']) || $posted['category'] == '-1')) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (category).","vbegy"));
		if ($content_post == 1 && empty($posted['comment'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (details).","vbegy"));
		
		askme_check_captcha(askme_options("the_captcha_post"),"post",$posted,$errors);

		if ($terms_active == 1 && $posted['agree_terms'] != 1) {
			$errors->add('required-terms',__("There are required fields (Agree of the terms).","vbegy"));
		}

		do_action('askme_add_post_errors',$errors,$posted,"add",$add_post_items);
		
		if (sizeof($errors->errors)>0) return $errors;
		
		/* Create post */
		if (is_user_logged_in) {
			$post_publish = askme_options("post_publish");
		}else {
			$post_publish = askme_options("post_publish_unlogged");
		}
		$data_insert = array(
			'post_content' => ask_kses_stip_wpautop($posted['comment']),
			'post_title'   => sanitize_text_field($posted['title']),
			'post_status'  => ($post_publish == "draft" && !is_super_admin($user_get_current_user_id)?"draft":"publish"),
			'post_author'  => (!is_user_logged_in && $add_post_no_register == 1?0:$user_get_current_user_id),
			'post_type'	   => 'post',
		);
			
		$post_id = wp_insert_post($data_insert);
			
		if ($post_id==0 || is_wp_error($post_id)) wp_die(__("Error in post.","vbegy"));
		
		$terms = array();
		if ($posted['category']) $terms[] = get_term_by('id',(is_array($posted['category'])?end($posted['category']):$posted['category']),'category')->slug;
		if (sizeof($terms) > 0) wp_set_object_terms($post_id,$terms,'category');
	
		$attachment = '';
	
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			
		if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
				
			$attachment = wp_handle_upload($_FILES['attachment'],array('test_form' => false),current_time('mysql'));
						
			if (isset($attachment['error'])) :
				$errors->add('upload-error',__("Attachment Error: ","vbegy") . $attachment['error']);
				
				return $errors;
			endif;
			
		endif;
		if ($attachment) :
			$attachment_data = array(
				'post_mime_type' => $attachment['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
				'post_content'   => '',
				'post_status'	 => 'inherit',
				'post_author'	 => (!is_user_logged_in && $add_post_no_register == 1?0:$user_get_current_user_id)
			);
			$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$post_id);
			$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
			wp_update_attachment_metadata($attachment_id, $attachment_metadata);
			$set_post_thumbnail = set_post_thumbnail($post_id,$attachment_id);
			if (!$set_post_thumbnail) {
				add_post_meta($post_id,'added_file',$attachment_id,true);
			}
		endif;
		
		/* Tags */
		
		if (isset($posted['post_tag'])) :
			$tags = explode(',',trim(stripslashes($posted['post_tag'])));
			$tags = array_map('strtolower',$tags);
			$tags = array_map('trim',$tags);
			wp_set_object_terms($post_id,(sizeof($tags) > 0?$tags:array()),'post_tag');
		endif;
		
		if (!is_user_logged_in && $add_post_no_register == 1 && $user_get_current_user_id == 0) {
			$post_username = sanitize_text_field($posted['username']);
			$post_email = sanitize_text_field($posted['email']);
			update_post_meta($post_id,'post_username',$post_username);
			update_post_meta($post_id,'post_email',$post_email);
		}else {
			$user_id = $user_get_current_user_id;
			$point_add_post = askme_options("point_add_post");
			$active_points = askme_options("active_points");
			if ($point_add_post > 0 && $active_points == 1) {
				$current_user = get_user_by("id",$user_id);
				$_points = get_user_meta($user_id,$current_user->user_login."_points",true);
				$_points++;
			
				update_user_meta($user_id,$current_user->user_login."_points",$_points);
				add_user_meta($user_id,$current_user->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$point_add_post,"+","add_post",$post_id));
			
				$points_user = get_user_meta($user_id,"points",true);
				$last_points = $points_user+$point_add_post;
				update_user_meta($user_id,"points",$last_points);
			}
		}
		
		/* The default meta */
		update_post_meta($post_id,"vbegy_layout","default");
		update_post_meta($post_id,"vbegy_home_template","default");
		update_post_meta($post_id,"vbegy_site_skin_l","default");
		update_post_meta($post_id,"vbegy_skin","default");
		update_post_meta($post_id,"vbegy_sidebar","default");
		update_post_meta($post_id,"post_from_front","from_front");
		update_post_meta($post_id,"count_post_all",0);
		update_post_meta($post_id,"count_post_comments",0);
		do_action('new_posts',$post_id);
		do_action('askme_finished_add_post',$post_id,$posted,"add");
	}
	if ($post_type == "add_question" || $post_type == "add_post") {
		/* Successful */
		return $post_id;
	}
}
/* askme_before_delete_post */
add_action('before_delete_post','askme_before_delete_post');
function askme_before_delete_post($postid) {
	$post_type = get_post_type($postid);
	if (isset($postid) && $postid != "" && ($post_type == "post" || $post_type == ask_questions_type || $post_type == ask_asked_questions_type)) { 
		$favorites_questions = get_post_meta($postid,"favorites_questions",true);
		if (isset($favorites_questions) && is_array($favorites_questions) && count($favorites_questions) >= 1) {
			foreach ($favorites_questions as $user_id) {
				$user_login_id2 = get_user_by("id",$user_id);
				$favorites_questions_user = get_user_meta($user_id,$user_login_id2->user_login."_favorites",true);
				$remove_favorites_questions = remove_item_by_value($favorites_questions_user,$postid);
				update_user_meta($user_id,$user_login_id2->user_login."_favorites",$remove_favorites_questions);
			}
		}
		
		$following_questions = get_post_meta($postid,"following_questions",true);
		if (isset($following_questions) && is_array($following_questions) && count($following_questions) >= 1) {
			foreach ($following_questions as $user_id) {
				$following_questions_user = get_user_meta($user_id,"following_questions",true);
				$remove_following_questions = remove_item_by_value($following_questions_user,$postid);
				update_user_meta($user_id,"following_questions",$remove_following_questions);
			}
		}
	}
	if ($post_type == ask_questions_type || $post_type == "post") {
		$sticky_posts = get_option("sticky_".$post_type."s");
		if (is_array($sticky_posts) && !empty($sticky_posts)) {
			$remove_sticky_posts = remove_item_by_value($sticky_posts,$postid);
			update_option("sticky_".$post_type."s",$remove_sticky_posts);
		}
	}
}
/* Before trash comment */
add_action("trash_comment","askme_trash_comment");
function askme_trash_comment($comment_id) {
	if ($comment_id > 0) {
		$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
		if (isset($best_answer_comment) && isset($comment_id) && $best_answer_comment == "best_answer_comment") {
			$comment = get_comment($comment_id);
			$comment_user_id = $comment->user_id;
			$post_id = $comment->comment_post_ID;
			if ($comment->comment_approved == 1) {
				delete_post_meta($post_id,"the_best_answer");
			}
		}
	}
}
/* askme_before_delete_comment */
add_action('delete_comment','askme_before_delete_comment');
function askme_before_delete_comment($comment_id) {
	$remove_best_answer_stats = askme_options("remove_best_answer_stats");
	$active_points = askme_options("active_points");
	if ($remove_best_answer_stats == 1) {
		$best_answer_comment = get_comment_meta($comment_id,"best_answer_comment",true);
		$get_comment = get_comment($comment_id);
		$user_id = $get_comment->user_id;
		if ($user_id > 0 && $active_points == 1) {
			$point_best_answer = askme_options("point_best_answer");
			$point_best_answer = ($point_best_answer != ""?$point_best_answer:5);
			$point_add_comment = askme_options("point_add_comment");
			$point_add_comment = ($point_add_comment != ""?$point_add_comment:2);
			
			$user_name = get_user_by("id",$user_id);
			$_points = get_user_meta($user_id,$user_name->user_login."_points",true);
			$_points++;
			update_user_meta($user_id,$user_name->user_login."_points",$_points);
			add_user_meta($user_id,$user_name->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$point_add_comment,"-","delete_answer"));
			$points_user = get_user_meta($user_id,"points",true);
			update_user_meta($user_id,"points",$points_user-$point_add_comment);
		}
		
		if (isset($best_answer_comment) && isset($comment_id) && $best_answer_comment == "best_answer_comment") {
			$best_answer_option = get_option("best_answer_option");
			$best_answer_option--;
			delete_post_meta($post_id,"the_best_answer");
			if ($best_answer_option < 0) {
				$best_answer_option = 0;
			}
			update_option("best_answer_option",$best_answer_option);
			
			$the_best_answer_user = get_user_meta($user_id,"the_best_answer",true);
			$the_best_answer_user--;
			if ($the_best_answer_user < 0) {
				$the_best_answer_user = 0;
			}
			update_user_meta($user_id,"the_best_answer",$the_best_answer_user);
			
			if ($user_id > 0 && $active_points == 1) {
				$_points++;
				update_user_meta($user_id,$user_name->user_login."_points",$_points);
				add_user_meta($user_id,$user_name->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$point_best_answer,"-","delete_best_answer"));
				$points_user = get_user_meta($user_id,"points",true);
				update_user_meta($user_id,"points",$points_user-$point_best_answer);
			}
			
			$point_back_option = askme_options("point_back");
			$user_author = get_post_field('post_author',$get_comment->comment_post_ID);
			if ($point_back_option == 1 && $active_points == 1 && $user_id != $user_author) {
				$point_back_number = askme_options("point_back_number");
				$point_back = get_post_meta($post_id,"point_back",true);
				$what_point = get_post_meta($post_id,"what_point",true);
				
				if ($point_back_number > 0) {
					$what_point = $point_back_number;
				}
				
				if ($point_back == "yes" && $user_author > 0) {
					$user_name2 = get_user_by("id",$user_author);
					$_points = get_user_meta($user_author,$user_name2->user_login."_points",true);
					$_points++;
					update_user_meta($user_author,$user_name2->user_login."_points",$_points);
					add_user_meta($user_author,$user_name2->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),($what_point != ""?$what_point:askme_options("question_points")),"-","point_removed"));
					$points_user = get_user_meta($user_author,"points",true);
					update_user_meta($user_author,"points",$points_user-($what_point != ""?$what_point:askme_options("question_points")));
				}
				
				if ($user_author > 0) {
					askme_notifications_activities($user_author,"","","","","point_removed","notifications");
				}
			}
		}
	}
}
/* askme_save_post */
add_action('save_post','askme_save_post',10,3);
function askme_save_post($post_id) {
	if (is_admin()) {
		$post_data = get_post($post_id);
		if ($post_data->post_type == ask_questions_type || $post_data->post_type == ask_asked_questions_type || $post_data->post_type == "post" || $post_data->post_type == "message") {
			if ($post_data->post_type == ask_questions_type || $post_data->post_type == ask_asked_questions_type) {
				$question_username = get_post_meta($post_id,'question_username',true);
				$question_email = get_post_meta($post_id,'question_email',true);
				$anonymously_user = get_post_meta($post_id,'anonymously_user',true);
				if ($question_username == "") {
					$question_no_username = get_post_meta($post_id,'question_no_username',true);
				}
			}
			if ($post_data->post_type == "post") {
				$post_username = get_post_meta($post_id,'post_username',true);
				$post_email = get_post_meta($post_id,'post_email',true);
			}
			if ($post_data->post_type == "message") {
				$message_username = get_post_meta($post_id,'message_username',true);
				$message_email = get_post_meta($post_id,'message_email',true);
			}
			
			if ((isset($anonymously_user) && $anonymously_user != "") || (isset($question_no_username) && $question_no_username == "no_user") || (isset($question_username) && $question_username != "" && isset($question_email) && $question_email != "") || (isset($post_username) && $post_username != "" && isset($post_email) && $post_email != "") || (isset($message_username) && $message_username != "" && isset($message_email) && $message_email != "")) {
				$data_update = array(
					'ID' => $post_id,
					'post_author' => 0,
				);
				remove_action('save_post', 'askme_save_post');
				$post_id = wp_update_post($data_update);
				add_action('save_post', 'askme_save_post');
			}
		}
	}
}
/* run_on_update_post */
add_action('transition_post_status','run_on_update_post',10,3);
function run_on_update_post($new_status,$old_status,$post) {
	if (is_admin()) {
		$post_type = $post->post_type;
		$post_id = $post->ID;
		$post_author = $post->post_author;
		if ($post_type == ask_questions_type || $post_type == ask_asked_questions_type || $post_type == "post" || $post_type == "message") {
			$post_approved_before = get_post_meta($post_id,'post_approved_before',true);
			if ($post_type == ask_questions_type || $post_type == ask_asked_questions_type) {
				$user_id = get_post_meta($post_id,"user_id",true);
				$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
				$question_username = get_post_meta($post_id,'question_username',true);
				$question_email = get_post_meta($post_id,'question_email',true);
				if ($question_username == "") {
					$question_no_username = get_post_meta($post_id,'question_no_username',true);
				}
			}
			if ($post_type == "post") {
				$post_username = get_post_meta($post_id,'post_username',true);
				$post_email = get_post_meta($post_id,'post_email',true);
			}
			if ($post_type == "message") {
				$message_username = get_post_meta($post_id,'message_username',true);
				$message_email = get_post_meta($post_id,'message_email',true);
			}
			
			if ((isset($anonymously_user) && $anonymously_user > 0) || (isset($question_no_username) && $question_no_username == "no_user") || (isset($question_username) && $question_username != "" && isset($question_email) && $question_email != "") || (isset($post_username) && $post_username != "" && isset($post_email) && $post_email != "") || (isset($message_username) && $message_username != "" && isset($message_email) && $message_email != "")) {
				$not_user = 0;
			}else {
				$not_user = $post_author;
			}
		}
		
		if ($old_status != $new_status) {
			if ('publish' == $new_status && $post_type == "message") {
				if ($post_approved_before != "yes") {
					update_post_meta($post_id,'post_approved_before',"yes");
					$get_message_user = get_post_meta($post_id,'message_user_id',true);
					$send_email_message = askme_options("send_email_message");
					if ($post_author != $get_message_user && $get_message_user > 0) {
						askme_notifications_activities($get_message_user,$post_author,($post_author == 0?$get_message_user:""),"","","add_message_user","notifications","","message");
					}
					if ($not_user > 0) {
						askme_notifications_activities($not_user,$get_message_user,"","","","add_message","activities","","message");
					}
					
					if ($send_email_message == 1) {
						$user = get_userdata($get_message_user);
						$send_text = askme_send_mail(
							array(
								'content'          => askme_options("email_new_message"),
								'user_id'          => $get_message_user,
								'post_id'          => $return,
								'sender_user_id'   => $post_author,
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
								'post_id'          => $return,
								'sender_user_id'   => $post_author,
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
		}
		if ('publish' == $new_status && ($post_type == ask_questions_type || $post_type == ask_asked_questions_type || $post_type == "post")) {
			if ($post_approved_before != "yes") {
				update_post_meta($post_id,'post_approved_before',"yes");
				
				if ($not_user > 0 || $anonymously_user > 0) {
					if ($post_type == ask_questions_type || $post_type == ask_asked_questions_type) {
						askme_notifications_activities(($anonymously_user > 0?$anonymously_user:$not_user),"","",$post_id,"","approved_question","notifications","",ask_questions_type);
						if ($post_author != $user_id && $user_id > 0) {
							askme_notifications_activities($user_id,($anonymously_user > 0?0:$not_user),"",$post_id,"","add_question_user","notifications","",ask_questions_type);
						}
					}else if ($not_user > 0) {
						askme_notifications_activities($not_user,"","",$post_id,"","approved_post","notifications");
					}
				}
				
				$user_get_current_user_id = get_current_user_id();
				if ($post_type == ask_questions_type || $post_type == ask_asked_questions_type) {
					askme_notifications_ask_question($post_id,$question_username,$user_id,$not_user,$anonymously_user,$user_get_current_user_id);
				}
				if ($post_type == "post") {
					askme_notifications_add_post($post_id,$post_username,$not_user,$user_get_current_user_id);
				}
			}
		}
	}
}
/* edit_question */
function edit_question() {
	if (isset($_POST)) :
		$return = process_edit_questions($_POST);
		if (is_wp_error($return)) :
   			echo '<div class="ask_error"><span><p>'.$return->get_error_message().'</p></span></div>';
   		else :
   			if(!session_id()) session_start();
   			$question_approved = askme_options("question_approved");
			if ($question_approved == 1 || is_super_admin(get_current_user_id())) {
				$_SESSION['vbegy_session_e'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Edited been successfully","vbegy").'</span><br>'.__("Has been edited successfully.","vbegy").'</p></div>';
				wp_redirect(get_permalink($return));
			}else {
				$_SESSION['vbegy_session_e'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Edited been successfully","vbegy").'</span><br>'.__("Has been edited successfully, The question under review.","vbegy").'</p></div>';
				wp_redirect(esc_url(home_url('/')));
			}
			exit;
   		endif;
	endif;
}
add_action('edit_question','edit_question');
/* process_edit_questions */
function process_edit_questions($data) {
	global $posted;
	set_time_limit(0);
	$errors = new WP_Error();
	$posted = array();

	if (isset($data['mobile'])) {
		$get_question = (int)$data['question_id'];
	}else {
		$get_question = (isset($data['ID'])?(int)$data['ID']:0);
	}
	$get_question_user_id = get_post_meta($get_question,"user_id",true);
	$get_post_q = get_post($get_question);
	
	$fields = array(
		'ID','title','comment','category','question_poll','remember_answer','private_question','anonymously_question',ask_question_tags,'sticky','video_type','video_id','video_description','featured_image'
	);
	
	$fields = apply_filters((empty($get_question_user_id)?'askme_edit_question_fields':'askme_edit_user_question_fields'),$fields,"edit");
	
	foreach ($fields as $field) :
		if (isset($data[$field])) $posted[$field] = $data[$field]; else $posted[$field] = '';
	endforeach;

	/* Validate Required Fields */
	
	if (isset($get_question) && $get_question != 0 && $get_post_q && $get_post_q->post_type == ask_questions_type || $get_post_q->post_type == ask_asked_questions_type) {
		$user_login_id_l = get_user_by("id",$get_post_q->post_author);
		if (!is_super_admin(get_current_user_id()) && $user_login_id_l->ID != get_current_user_id()) {
			$errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Sorry you can't edit this question.","vbegy"));
		}
	}else {
		$errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Sorry no question select or not found.","vbegy"));
	}
	
	if (empty($get_question_user_id)) {
		$question_sort_option = "ask_question_items";
		$comment_question = askme_options("comment_question");
		$editor_question_details = askme_options("editor_question_details");
		$title_excerpt_type = askme_options("title_excerpt_type");
		$title_excerpt = askme_options("title_excerpt");
	}else {
		$question_sort_option = "ask_user_items";
		$comment_question = askme_options("content_ask_user");
		$editor_question_details = askme_options("editor_ask_user");
		$title_excerpt_type = askme_options("title_excerpt_type_user");
		$title_excerpt = askme_options("title_excerpt_user");
	}
	$question_sort = askme_options($question_sort_option);
	$title_question = ((isset($question_sort["title_question"]["value"]) && $question_sort["title_question"]["value"] == "title_question") || (isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] != "comment_question")?1:0);
	$category_question = (isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question"?1:0);
	$category_question_required = askme_options("category_question_required");
	$featured_image_question = (isset($question_sort["featured_image"]["value"]) && $question_sort["featured_image"]["value"] == "featured_image"?1:0);
	$video_desc_active = (isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active"?1:0);
	if ($title_question !== 1 || $comment_question == 1) {
		$comment_question = "required";
	}

	if ($title_question === 1 && empty($posted['title'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (title).","vbegy"));
	if (empty($get_question_user_id) && ($category_question == 1 && $category_question_required == 1 && (empty($posted['category']) || $posted['category'] == '-1' || (is_array($posted['category']) && end($posted['category']) == '-1')))) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (category).","vbegy"));
	if (isset($posted['question_poll']) && $posted['question_poll'] == 1) {
		foreach($data['ask'] as $ask) {
			if (empty($ask['ask']) && count($data['ask']) < 2) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Please enter at least two values in poll.","vbegy"));
		}
	}
	if ($comment_question == "required" && isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] == "comment_question" && empty($posted['comment'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (content).","vbegy"));
	if ($video_desc_active == 1 && $posted['video_description'] == 1 && empty($posted['video_id'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (Video ID).","vbegy"));
	
	do_action((empty($data['user_id'])?'askme_edit_question_errors':'askme_edit_user_question_errors'),$errors,$posted,"edit",$question_sort);
	
	if (sizeof($errors->errors)>0) return $errors;
	
	$question_id = $get_question;
	
	$question_approved = askme_options("question_approved");
	
	/* Edit question */
	$post_name = array();
	$change_question_url = askme_options("change_question_url");
	if ($change_question_url == 1) {
		$post_name = array('post_name' => sanitize_text_field($posted['title']));
	}
	
	if ($title_question === 1) {
		$question_title = $posted['title'];
	}else {
		$question_title = excerpt_any($title_excerpt,sanitize_text_field($posted['comment']),$title_excerpt_type);
	}
	
	$data_update = array(
		'ID'		   => (int)sanitize_text_field($question_id),
		'post_content' => ask_kses_stip_wpautop($posted['comment']),
		'post_title'   => sanitize_text_field($question_title),
		'post_status'  => ($question_approved == 1 || is_super_admin(get_current_user_id())?"publish":"draft"),
		'post_author'  => ($posted['anonymously_question']?0:$get_post_q->post_author),
	);
	
	wp_update_post(array_merge($post_name,$data_update));
	
	if (empty($get_question_user_id) && $category_question == 1 && isset($posted['category']) && $posted['category']) {
		if (is_array($posted['category'])) {
			$cat_ids = array_map( 'intval', $posted['category'] );
			$cat_ids = array_unique( $cat_ids );
		}else {
			$cat_ids = array();
			$cat_ids[] = get_term_by('id',(is_array($posted['category'])?end($posted['category']):$posted['category']),ask_question_category)->slug;
		}
		wp_set_object_terms($question_id,(sizeof($cat_ids) > 0?$cat_ids:array()),ask_question_category);
	}

	if ($posted['question_poll'] && $posted['question_poll'] != "")  {
		update_post_meta($question_id,'question_poll',$posted['question_poll']);
	}else {
		update_post_meta($question_id,'question_poll',2);
	}

	if (isset($data['ask'])) {
		update_post_meta($question_id,'ask',$data['ask']);
	}
	
	if ($posted['remember_answer'] && $posted['remember_answer'] != "") {
		update_post_meta($question_id,'remember_answer',$posted['remember_answer']);
	}else {
		delete_post_meta($question_id,'remember_answer');
	}
	
	if ($posted['private_question'] && $posted['private_question'] != "") {
		update_post_meta($question_id,'private_question',$posted['private_question']);
		$anonymously_user = get_post_meta($question_id,'anonymously_user',true);
		update_post_meta($question_id,'private_question_author',($anonymously_user > 0?$anonymously_user:$get_post_q->post_author));
	}else {
		delete_post_meta($question_id,'private_question');
		delete_post_meta($question_id,'private_question_author');
	}
	
	if ($video_desc_active == 1) {
		if ($posted['video_description'] && $posted['video_description'] != "") {
			update_post_meta($question_id,'video_description',$posted['video_description']);
		}else {
			delete_post_meta($question_id,'video_description');
		}
		
		if ($posted['video_type']) {
			update_post_meta($question_id,'video_type',$posted['video_type']);
		}
			
		if ($posted['video_id']) {
			update_post_meta($question_id,'video_id',$posted['video_id']);	
		}
	}

	$sticky_questions = get_option('sticky_questions');
	$sticky_posts = get_option('sticky_posts');
	if (isset($posted['sticky']) && $posted['sticky'] == "sticky") {
		update_post_meta($question_id,'sticky',1);
		if (is_array($sticky_questions)) {
			if (!in_array($question_id,$sticky_questions)) {
				$array_merge = array_merge($sticky_questions,array($question_id));
				update_option("sticky_questions",$array_merge);
			}
		}else {
			update_option("sticky_questions",array($question_id));
		}
		if (is_array($sticky_posts)) {
			if (!in_array($question_id,$sticky_posts)) {
				$array_merge = array_merge($sticky_posts,array($question_id));
				update_option("sticky_posts",$array_merge);
			}
		}else {
			update_option("sticky_posts",array($question_id));
		}
	}else {
		if (is_array($sticky_questions) && in_array($question_id,$sticky_questions)) {
			$sticky_questions = remove_item_by_value($sticky_questions,$question_id);
			update_option('sticky_questions',$sticky_questions);
		}
		if (is_array($sticky_posts) && in_array($question_id,$sticky_posts)) {
			$sticky_posts = remove_item_by_value($sticky_posts,$question_id);
			update_option('sticky_posts',$sticky_posts);
		}
		delete_post_meta($question_id,'sticky');
	}
	
	/* Featured image */
	
	if ($featured_image_question == 1) {
		$featured_image = '';
		
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
		require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		
		if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
			$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
			if (!isset($data['mobile']) && !in_array($_FILES['featured_image']['type'],$types)) :
				$errors->add('upload-error',__("Attachment Error, Please upload image only.","vbegy"));
				return $errors;
			endif;
			
			$featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form' => false),current_time('mysql'));
			
			if (isset($featured_image['error'])) :
				$errors->add('upload-error',__("Attachment Error: ","vbegy") . $featured_image['error']);
				return $errors;
			endif;
			
		endif;
		if ($featured_image) :
			$ask_question_no_register = askme_options("ask_question_no_register");
			$featured_image_data = array(
				'post_mime_type' => $featured_image['type'],
				'post_title'     => preg_replace('/\.[^.]+$/','',basename($featured_image['file'])),
				'post_content'   => '',
				'post_status'    => 'inherit',
				'post_author'    => (!is_user_logged_in && $ask_question_no_register == 1?0:get_current_user_id())
			);
			$featured_image_id = wp_insert_attachment($featured_image_data,$featured_image['file'],$question_id);
			$featured_image_metadata = wp_generate_attachment_metadata($featured_image_id,$featured_image['file']);
			wp_update_attachment_metadata($featured_image_id, $featured_image_metadata);
			set_post_thumbnail($question_id,$featured_image_id);
		endif;
	}
	
	/* Tags */
	
	if (empty($get_question_user_id)) :
		if (isset($posted[ask_question_tags])) {
			$tags = explode(',',trim(stripslashes($posted[ask_question_tags])));
			$tags = array_map('strtolower',$tags);
			$tags = array_map('trim',$tags);
			wp_set_object_terms($question_id,(sizeof($tags) > 0?$tags:array()),ask_question_tags);
		}else {
			wp_set_object_terms($question_id,array(),ask_question_tags);
		}
	endif;
	
	$post_id = $question_id;
	
	do_action('edit_questions',$question_id,$posted);
	do_action((empty($get_question_user_id)?"askme_finished_edit_user_question":"askme_finished_edit_question"),$question_id,$posted,"edit",$get_question_user_id);
	
	/* Successful */
	return $question_id;
}
/* vpanel_edit_comment */
function vpanel_edit_comment() {
	if ($_POST) :
		$return = process_edit_comments($_POST);
		if (is_wp_error($return)) :
   			echo '<div class="ask_error"><span><p>'.$return->get_error_message().'</p></span></div>';
   		else :
   			if(!session_id()) session_start();
   			$comment_approved = askme_options("comment_approved");
   			if ($comment_approved == 1 || is_super_admin(get_current_user_id())) {
   				$_SESSION['vbegy_session_comment'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Edited been successfully","vbegy").'</span><br>'.__("Has been edited successfully.","vbegy").'</p></div>';
   			}else {
   				$_SESSION['vbegy_session_comment'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Edited been successfully","vbegy").'</span><br>'.__("The comment has added successfully, It's under review.","vbegy").'</p></div>';
   			}
			wp_redirect(get_comment_link($return));
			exit;
   		endif;
	endif;
}
add_action('vpanel_edit_comment','vpanel_edit_comment');
/* process_edit_comments */
function process_edit_comments($data) {
	global $posted;
	set_time_limit(0);
	$get_current_user_id = get_current_user_id();
	$errors = new WP_Error();
	$posted = array();
	
	$fields = array(
		'comment_id','comment_content','private_answer','video_answer_description','video_answer_type','video_answer_id'
	);
	
	foreach ($fields as $field) :
		if (isset($data[$field])) $posted[$field] = $data[$field]; else $posted[$field] = '';
	endforeach;

	/* Validate Required Fields */
	
	$comment_id      = (isset($posted['comment_id'])?(int)$posted['comment_id']:0);
	$comment_content = (isset($posted["comment_content"])?askme_kses_stip($posted["comment_content"]):"");
	$private_answer  = (isset($posted["private_answer"])?esc_html($posted["private_answer"]):"");
	
	$get_comment = get_comment($comment_id);
	$get_post = array();
	if (isset($comment_id) && $comment_id != 0 && is_object($get_comment)) {
		$get_post = get_post($get_comment->comment_post_ID);
	}
	
	if (isset($comment_id) && $comment_id != 0 && $get_post) {
		$can_edit_comment = askme_options("can_edit_comment");
		$comment_approved = askme_options("comment_approved");
		$can_edit_comment_after = askme_options("can_edit_comment_after");
		$can_edit_comment_after = (int)(isset($can_edit_comment_after) && $can_edit_comment_after > 0?$can_edit_comment_after:0);
		if (is_super_admin($get_current_user_id) || ($can_edit_comment == 1 && $get_comment->user_id == $get_current_user_id && $get_comment->user_id != 0 && $get_current_user_id != 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after))) {
		}else {
			$errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("You are not allowed to edit this comment.","vbegy"));
		}
	}else {
		$errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Sorry no comment has you select or not found.","vbegy"));
	}

	$answer_video = askme_options("answer_video");
	if ($answer_video == 1 && isset($posted['video_answer_description']) && $posted['video_answer_description'] == "on" && empty($posted['video_answer_id'])) {
		$errors->add('required-5','<strong>'.esc_html__("Error","vbegy").':&nbsp;</strong> '.esc_html__("There are required fields (Video ID).","vbegy"));
	}

	$attachment_answer = askme_options("attachment_answer");
	$featured_image_answer = askme_options("featured_image_answer");
	if (($attachment_answer == 1 && isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) || ($featured_image_answer == 1 && isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name']))) {
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
	}

	if ($attachment_answer == 1 && isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
		$comment_attachment = wp_handle_upload($_FILES['attachment'],array('test_form' => false),current_time('mysql'));
		if (isset($comment_attachment['error'])) :
			$errors->add('Attachment Error: ' . $comment_attachment['error']);
		endif;
	}

	if ($featured_image_answer == 1 && isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['name'])) :
		$comment_featured_image = '';
		$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
		if (!isset($data['mobile']) && !in_array($_FILES['featured_image']['type'],$types)) :
			$errors->add('upload-error',esc_html__("Attachment Error, Please upload image only.","vbegy"));
		endif;
		
		$comment_featured_image = wp_handle_upload($_FILES['featured_image'],array('test_form' => false),current_time('mysql'));
		
		if (isset($comment_featured_image['error'])) :
			$errors->add('upload-error',esc_html__("Attachment Error: ","vbegy") . $comment_featured_image['error']);
		endif;
		
	endif;
	
	if (empty($comment_content)) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (comment).","vbegy"));
	if (sizeof($errors->errors)>0) return $errors;
	
	/* Edit comment */
	$data_comment['comment_ID'] = $comment_id;
	if (!isset($comment_approved) || $comment_approved == 0) {
		$data_comment['comment_approved'] = 0;
	}
	$data_comment['comment_content']  = $comment_content;
	
	wp_update_comment($data_comment);
	
	update_comment_meta($comment_id,"edit_comment","edited");
	
	if ($private_answer == 1) {
		update_comment_meta($comment_id,"private_answer",1);
	}else {
		delete_comment_meta($comment_id,"private_answer");
	}

	if ($answer_video == 1) {
		if ($posted['video_answer_description'] && $posted['video_answer_description'] != "") {
			update_comment_meta($comment_id,'video_answer_description',esc_html($posted['video_answer_description']));
		}else {
			delete_comment_meta($comment_id,'video_answer_description');
		}
		
		if ($posted['video_answer_type']) {
			update_comment_meta($comment_id,'video_answer_type',esc_html($posted['video_answer_type']));
		}
			
		if ($posted['video_answer_id']) {
			update_comment_meta($comment_id,'video_answer_id',esc_html($posted['video_answer_id']));
		}
	}

	/* Attachment */

	if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
		$comment_attachment_data = array(
			'post_mime_type' => $comment_attachment['type'],
			'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_attachment['file'])),
			'post_content'   => '',
			'post_status'	=> 'inherit',
			'post_author'	=> ($comment_user_id != "" || $comment_user_id != 0?$comment_user_id:0)
		);
		$comment_attachment_id = wp_insert_attachment($comment_attachment_data,$comment_attachment['file'],$post_id);
		$comment_attachment_metadata = wp_generate_attachment_metadata($comment_attachment_id,$comment_attachment['file']);
		wp_update_attachment_metadata($comment_attachment_id, $comment_attachment_metadata);
		update_comment_meta($comment_id,'added_file',$comment_attachment_id);
	endif;

	/* Featured image */
	
	if ($featured_image_answer == 1 && isset($comment_featured_image)) {
		$comment_featured_image_data = array(
			'post_mime_type' => $comment_featured_image['type'],
			'post_title'	 => preg_replace('/\.[^.]+$/','',basename($comment_featured_image['file'])),
			'post_content'   => '',
			'post_status'	 => 'inherit',
			'post_author'	 => ($comment_user_id > 0?$comment_user_id:0)
		);
		$comment_featured_image_id = wp_insert_attachment($comment_featured_image_data,$comment_featured_image['file'],$get_comment->comment_post_ID);
		$comment_featured_image_metadata = wp_generate_attachment_metadata($comment_featured_image_id,$comment_featured_image['file']);
		wp_update_attachment_metadata($comment_featured_image_id, $comment_featured_image_metadata);
		update_comment_meta($comment_id,'featured_image',$comment_featured_image_id);
	}

	do_action('vpanel_edit_comments',$comment_id);
	
	/* Successful */
	return $comment_id;
}
/* vpanel_session */
function vpanel_session ($message = "",$session = "") {
	if(!session_id())
		session_start();
	if ($message) {
		$_SESSION[$session] = $message;
	}else {
		if (isset($_SESSION[$session])) {
			$last_message = $_SESSION[$session];
			unset($_SESSION[$session]);
			echo $last_message;
		}
	}
}
/* vpanel_edit_post */
function vpanel_edit_post() {
	if (isset($_POST)) :
		$return = process_vpanel_edit_posts($_POST);
		if (is_wp_error($return)) :
   			echo '<div class="ask_error"><span><p>'.$return->get_error_message().'</p></span></div>';
   		else :
   			if(!session_id()) session_start();
			$post_approved = askme_options("post_approved");
   			if ($post_approved == 1 || is_super_admin(get_current_user_id())) {
   				$_SESSION['vbegy_session_e'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Edited been successfully","vbegy").'</span><br>'.__("Has been edited successfully.","vbegy").'</p></div>';
   				wp_redirect(get_permalink($return));
   			}else {
   				$_SESSION['vbegy_session_e'] = '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Edited been successfully","vbegy").'</span><br>'.__("The post has added successfully, It's under review.","vbegy").'</p></div>';
   				wp_redirect(esc_url(home_url('/')));
   			}
			exit;
   		endif;
	endif;
}
add_action('vpanel_edit_post','vpanel_edit_post');
/* process_vpanel_edit_posts */
function process_vpanel_edit_posts($data) {
	global $posted;
	set_time_limit(0);
	$errors = new WP_Error();
	$posted = array();
	
	if (isset($data['mobile'])) {
		$get_post = (int)$data['post_id'];
	}else {
		$get_post = (isset($data['ID'])?(int)$data['ID']:0);
	}
	
	$fields = array(
		'ID','title','comment','category','attachment','post_tag'
	);

	$fields = apply_filters('askme_edit_post_fields',$fields,"edit");
	
	foreach ($fields as $field) :
		if (isset($data[$field])) $posted[$field] = $data[$field]; else $posted[$field] = '';
	endforeach;

	/* Validate Required Fields */

	$get_post_q = get_post($get_post);
	if (isset($get_post) && $get_post != 0 && $get_post_q && get_post_type($get_post) == "post") {
		$user_login_id_l = get_user_by("id",$get_post_q->post_author);
		if ($user_login_id_l->ID != get_current_user_id() && !is_super_admin(get_current_user_id())) {
			$errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Sorry you can't edit this post.","vbegy"));
		}
	}else {
		$errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Sorry no post select or not found.","vbegy"));
	}
	if (empty($posted['title'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (title).","vbegy"));

	$add_post_items = askme_options("add_post_items");
	$content_post = (isset($add_post_items["content_post"]["value"]) && $add_post_items["content_post"]["value"] == "content_post"?1:0);
	if ((empty($posted['category']) || $posted['category'] == '-1')) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (category).","vbegy"));
	
	if ($content_post == 1 && empty($posted['comment'])) $errors->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields (content).","vbegy"));

	do_action('askme_edit_post_errors',$errors,$posted,"edit",$add_post_items);
	if (sizeof($errors->errors)>0) return $errors;
	
	$post_id = $get_post;
	
	$post_approved = askme_options("post_approved");
	$post_name = array();
	$change_post_url = askme_options("change_post_url");
	if ($change_post_url == 1) {
		$post_name = array('post_name' => sanitize_text_field($posted['title']));
	}
	
	/* Edit post */
	$data_update = array(
		'ID'		   => sanitize_text_field($post_id),
		'post_content' => ask_kses_stip_wpautop($posted['comment']),
		'post_title'   => ask_kses_stip($posted['title']),
		'post_status'  => ($post_approved == 1 || is_super_admin(get_current_user_id())?"publish":"draft"),
	);
	
	wp_update_post(array_merge($post_name,$data_update));
	
	$terms = array();
	if ($posted['category']) $terms[] = get_term_by('id',$posted['category'],'category')->slug;
	if (sizeof($terms) > 0) wp_set_object_terms($post_id,$terms,'category');
	
	$attachment = '';

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
		
	if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) :
			
		$attachment = wp_handle_upload($_FILES['attachment'],array('test_form' => false),current_time('mysql'));
					
		if (isset($attachment['error'])) :
			$errors->add('upload-error',__("Attachment Error: ","vbegy") . $attachment['error']);
			
			return $errors;
		endif;
		
	endif;
	if ($attachment) :
		$add_post_no_register = askme_options("add_post_no_register");
		$attachment_data = array(
			'post_mime_type' => $attachment['type'],
			'post_title'     => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
			'post_content'   => '',
			'post_status'    => 'inherit',
			'post_author'    => (!is_user_logged_in && $add_post_no_register == 1?0:get_current_user_id())
		);
		$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],$post_id);
		$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
		wp_update_attachment_metadata($attachment_id, $attachment_metadata);
		set_post_thumbnail($post_id,$attachment_id);
	endif;
	
	/* Tags */
	
	if (isset($posted['post_tag'])) :
		$tags = explode(',',trim(stripslashes($posted['post_tag'])));
		$tags = array_map('strtolower',$tags);
		$tags = array_map('trim',$tags);
		wp_set_object_terms($post_id,(sizeof($tags) > 0?$tags:array()),'post_tag');
	endif;

	do_action('vpanel_edit_posts',$post_id);
	do_action('askme_finished_edit_post',$post_id,$posted,"edit");
	
	/* Successful */
	return $post_id;
}
/* add_favorite */
function add_favorite() {
	$post_id = (int)$_POST['post_id'];
	$user_id = get_current_user_id();
	$user_login_id2 = get_user_by("id",$user_id);
	
	$favorites_questions = get_post_meta($post_id,"favorites_questions",true);
	if (empty($favorites_questions)) {
		$update = update_post_meta($post_id,"favorites_questions",array($user_id));
	}else if (is_array($favorites_questions) && !in_array($user_id,$favorites_questions)) {
		$update = update_post_meta($post_id,"favorites_questions",array_merge($favorites_questions,array($user_id)));
	}
	
	$_favorites = get_user_meta($user_id,$user_login_id2->user_login."_favorites",true);
	if (empty($_favorites)) {
		$update = update_user_meta($user_id,$user_login_id2->user_login."_favorites",array($post_id));
	}else if (is_array($_favorites) && !in_array($post_id,$_favorites)) {
		$update = update_user_meta($user_id,$user_login_id2->user_login."_favorites",array_merge($_favorites,array($post_id)));
	}
	
	$count = get_post_meta($post_id,'question_favorites',true);
	if ($count == "") {
		$count = 0;
	}
	$count++;
	$update = update_post_meta($post_id,'question_favorites',$count);
	
	$get_post = get_post($post_id);
	$post_author = $get_post->post_author;
	$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
	if (($user_id > 0 && $post_author > 0) || ($user_id > 0 && $anonymously_user > 0)) {
		askme_notifications_activities(($post_author > 0?$post_author:$anonymously_user),$user_id,"",$post_id,"","question_favorites","notifications","",ask_questions_type);
	}
	if ($user_id > 0) {
		askme_notifications_activities($user_id,"","",$post_id,"","question_favorites","activities","",ask_questions_type);
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_add_favorite','add_favorite');
add_action('wp_ajax_nopriv_add_favorite','add_favorite');
/* remove_favorite */
function remove_favorite() {
	$post_id = (int)$_POST['post_id'];
	$user_id = get_current_user_id();
	$user_login_id2 = get_user_by("id",$user_id);
	
	$favorites_questions = get_post_meta($post_id,"favorites_questions",true);
	if (isset($favorites_questions) && !empty($favorites_questions)) {
		$remove_favorites_questions = remove_item_by_value($favorites_questions,$user_id);
		update_post_meta($post_id,"favorites_questions",$remove_favorites_questions);
	}
	
	$_favorites = get_user_meta($user_id,$user_login_id2->user_login."_favorites",true);
	if (isset($_favorites) && is_array($_favorites) && in_array($post_id,$_favorites)) {
		$remove_item = remove_item_by_value($_favorites,$post_id);
		update_user_meta($user_id,$user_login_id2->user_login."_favorites",$remove_item);
	}
	
	$count = get_post_meta($post_id,'question_favorites',true);
	if ($count == "") {
		$count = 0;
	}
	$count--;
	if ($count < 0) {
		$count = 0;
	}
	$update = update_post_meta($post_id,'question_favorites',$count);
	
	$get_post = get_post($post_id);
	$post_author = $get_post->post_author;
	$anonymously_user = get_post_meta($post_id,"anonymously_user",true);
	if (($user_id > 0 && $post_author > 0) || ($user_id > 0 && $anonymously_user > 0)) {
		askme_notifications_activities(($post_author > 0?$post_author:$anonymously_user),$user_id,"",$post_id,"","question_remove_favorites","notifications","",ask_questions_type);
	}
	if ($user_id > 0) {
		askme_notifications_activities($user_id,"","",$post_id,"","question_remove_favorites","activities","",ask_questions_type);
	}
	if (!isset($_POST["mobile"])) {
		die();
	}
}
add_action('wp_ajax_remove_favorite','remove_favorite');
add_action('wp_ajax_nopriv_remove_favorite','remove_favorite');
/* remove_item_by_value */
function remove_item_by_value($array,$val = '',$preserve_keys = true) {
	if (empty($array) || !is_array($array)) return false;
	if (!in_array($val,$array)) return $array;
	
	foreach($array as $key => $value) {
		if ($value == $val) unset($array[$key]);
	}
	
	return ($preserve_keys === true) ? $array : array_values($array);
}
/* excerpt_row */
function excerpt_row($excerpt_length,$content) {
	global $post;
	$words = explode(' ',$content,$excerpt_length + 1);
	if(count($words) > $excerpt_length) :
		array_pop($words);
		array_push($words,'...');
		$content = implode(' ',$words);
	endif;
		$content = strip_tags($content);
	echo $content;
}
/* excerpt_title_row */
function excerpt_title_row($excerpt_length,$title) {
	global $post;
	$words = explode(' ',$title,$excerpt_length + 1);
	if(count($words) > $excerpt_length) :
		array_pop($words);
		array_push($words,'');
		$title = implode(' ',$words);
	endif;
		$title = strip_tags($title);
	echo $title;
}
/* ask_coupon_valid */
function ask_coupon_valid ($coupons,$coupon_name,$coupons_not_exist,$pay_ask_payment,$what_return = '') {
	if (isset($coupons) && is_array($coupons)) {
		foreach ($coupons as $coupons_k => $coupons_v) {
			if (is_array($coupons_v) && in_array($coupon_name,$coupons_v)) {
				if ($what_return == "coupons_not_exist") {
					return "yes";
				}
				if (isset($coupons_v["coupon_date"]) && $coupons_v["coupon_date"] !="" && $coupons_v["coupon_date"] < date_i18n('m/d/Y',current_time('timestamp'))) {
					return '<div class="alert-message error"><p>'.__("This coupon has expired.","vbegy").'</p></div>';
				}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent") {
					if ((int)$coupons_v["coupon_amount"] > 100) {
						return '<div class="alert-message error"><p>'.__("This coupon is not valid.","vbegy").'</p></div>';
					}else {
						$the_discount = ($pay_ask_payment*$coupons_v["coupon_amount"])/100;
						$last_payment = $pay_ask_payment-$the_discount;
						if ($what_return == "last_payment") {
							return $last_payment;
						}
					}
				}else if (isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount") {
					if ((int)$coupons_v["coupon_amount"] > $pay_ask_payment) {
						return '<div class="alert-message error"><p>'.__("This coupon is not valid.","vbegy").'</p></div>';
					}else {
						$last_payment = $pay_ask_payment-$coupons_v["coupon_amount"];
						if ($what_return == "last_payment") {
							return $last_payment;
						}
					}
				}else {
					return '<div class="alert-message success"><p>'.__("Coupon code applied successfully.","vbegy").'</p></div>';
				}
			}
		}
	}
}
/* ask_find_coupons */
function ask_find_coupons($coupons,$coupon_name) {
	foreach ($coupons as $coupons_k => $coupons_v) {
		if (is_array($coupons_v) && in_array($coupon_name,$coupons_v)) {
			return $coupons_k;
		}
	}
	return false;
}
/* send_admin_notification */
function send_admin_notification($post_id,$post_title) {
	$blogname = get_option('blogname');
	$email = get_option('admin_email');
	$headers = "MIME-Version: 1.0\r\n" . "From: ".$blogname." "."<".$email.">\n" . "Content-Type: text/HTML; charset=\"" . get_option('blog_charset') . "\"\r\n";
	$message = __('Hello there,','vbegy').'<br/><br/>'. 
	__('A new post has been submitted in ','vbegy').$blogname.' site.'.__(' Please find details below:','vbegy').'<br/><br/>'.
	
	'Post title: '.$post_title.'<br/><br/>';
	$post_author_name = get_post_meta($post_id,'ap_author_name',true);
	$post_author_email = get_post_meta($post_id,'ap_author_email',true);
	$post_author_url = get_post_meta($post_id,'ap_author_url',true);
	if($post_author_name!=''){
		$message .= 'Post Author Name: '.$post_author_name.'<br/><br/>';
	}
	if($post_author_email!=''){
		$message .= 'Post Author Email: '.$post_author_email.'<br/><br/>';
	}
	if($post_author_url!=''){
		$message .= 'Post Author URL: '.$post_author_url.'<br/><br/>';
	}
	
	$message .= '____<br/><br/>
	'.__('To take action (approve/reject)- please go here:','vbegy').'<br/>'
	.admin_url().'post.php?post='.$post_id.'&action=edit <br/><br/>
	
	'.__('Thank You','vbegy');
	$subject = __('New Post Submission','vbegy');
	wp_mail($email,$subject,$message,$headers);
}
/* askme_edit_comment */
add_action ('edit_comment','askme_edit_comment');
function askme_edit_comment($comment_id) {
	if (isset($_POST["delete_reason"])) {
		update_comment_meta($comment_id,"delete_reason",esc_attr($_POST["delete_reason"]));
	}
}
/* askme_meta_boxes_comment */
add_action('add_meta_boxes_comment','askme_meta_boxes_comment');
function askme_meta_boxes_comment($comment) {
	$answer_question = get_post_type($comment->comment_post_ID);
	if ($answer_question == ask_questions_type || $answer_question == ask_asked_questions_type || $answer_question == "post") {?>
		<div class="stuffbox">
			<div class="inside">
				<fieldset>
					<legend class="edit-comment-author">Reason if you need to remove it.</legend>
					<table class="form-table editcomment">
						<tbody>
							<tr>
								<td class="first" style="width: 10px;"><label for="delete_reason">Reason:</label></td>
								<td>
									<input id="delete_reason" name="delete_reason" class="code" type="text" value="<?php echo esc_attr(get_comment_meta($comment->comment_ID,"delete_reason",true));?>" style="width: 98%;">
								</td>
							</tr>
						</tbody>
					</table>
					<br>
					<div class="submitbox"><a href="#" class="submitdelete delete-comment-answer" data-div-id="delete_reason" data-id="<?php echo esc_attr($comment->comment_ID);?>" data-action="delete_comment_answer" data-location="<?php echo esc_url(($answer_question == ask_questions_type || $answer_question == ask_asked_questions_type?admin_url('edit-comments.php?comment_status=all&answers=1'):admin_url('edit-comments.php?comment_status=all&comments=1')))?>">Delete?</a></div>
				</fieldset>
			</div>
		</div>
	<?php }?>
<?php }
/* askme_comments_exclude */
add_action('current_screen','askme_comments_exclude',10,2);
function askme_comments_exclude($screen) {
	if ($screen->id != 'edit-comments')
		return;
	if (isset($_GET['answers'])) {
		add_action('pre_get_comments','askme_list_answers',10,1);
	}else if (isset($_GET['best_answers'])) {
		add_action('pre_get_comments','askme_list_best_answers',10,1);
	}else if (isset($_GET['comments'])) {
		add_action('pre_get_comments','askme_list_comments',10,1);
	}
	add_filter('comment_status_links','askme_new_answers_page_link');
}
function askme_list_comments($clauses) {
	$clauses->query_vars['post_type'] = "post";
}
function askme_list_answers($clauses) {
	$clauses->query_vars['post_type'] = ask_questions_type;
}
function askme_list_best_answers($clauses) {
	$clauses->query_vars['post_type'] = ask_questions_type;
	$clauses->query_vars['meta_key'] = "best_answer_comment";
	$clauses->query_vars['meta_value'] = "best_answer_comment";
}
function askme_new_answers_page_link($status_links) {
	$count = get_all_comments_of_post_type(array(ask_questions_type,ask_asked_questions_type));
	$count_posts = get_all_comments_of_post_type("post");
	$best_answer_option = count(get_comments(array("status" => "approve",'post_type' => array(ask_questions_type,ask_asked_questions_type),"meta_query" => array(array("key" => "best_answer_comment","compare" => "=","value" => "best_answer_comment")))));
	$best_answers_count = (isset($best_answer_option) && $best_answer_option != "" && $best_answer_option > 0?$best_answer_option:0);
	$status_links['comments'] = '<a href="edit-comments.php?comment_status=all&comments=1"'.(isset($_GET['comments'])?' class="current"':'').'>'.__('Comments','vbegy').' ('.$count_posts.')</a>';
	$status_links['answers'] = '<a href="edit-comments.php?comment_status=all&answers=1"'.(isset($_GET['answers'])?' class="current"':'').'>'.__('Answers','vbegy').' ('.$count.')</a>';
	$status_links['best_answers'] = '<a href="edit-comments.php?comment_status=all&best_answers=1"'.(isset($_GET['best_answers'])?' class="current"':'').'>'.__('Best Answers','vbegy').' ('.$best_answers_count.')</a>';
	return $status_links;
}
/* askme_before_delete_user */
add_action('delete_user','askme_before_delete_user');
function askme_before_delete_user($user_id) {
	update_user_meta($user_id,"password_changed","changed");
	
	$active_points = askme_options("active_points");
	$point_following_me = askme_options("point_following_me");
	$point_following_me = ($point_following_me != ""?$point_following_me:1);
	
	$following_me = get_user_meta($user_id,"following_me",true);
	if (isset($following_me) && is_array($following_me)) {
		foreach ($following_me as $key => $value) {
			$following_me = get_user_meta($value,"following_me",true);
			$get_user_by_following_not_id = get_user_by("id",$value);
			$remove_following_me = remove_item_by_value($following_me,$user_id);
			update_user_meta($value,"following_me",$remove_following_me);
			if ($active_points == 1) {
				$points = get_user_meta($value,"points",true);
				$new_points = $points-$point_following_me;
				if ($new_points < 0) {
					$new_points = 0;
				}
				update_user_meta($value,"points",$new_points);
				
				$_points = get_user_meta($value,$get_user_by_following_not_id->user_login."_points",true);
				$_points++;
				
				update_user_meta($value,$get_user_by_following_not_id->user_login."_points",$_points);
				add_user_meta($value,$get_user_by_following_not_id->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$point_following_me,"-","delete_follow_user","",""));
			}
			
			$following_you = get_user_meta($value,"following_you",true);
			$remove_following_you = remove_item_by_value($following_you,$user_id);
			update_user_meta($value,"following_you",$remove_following_you);
		}
	}
}
/* update_notifications */
function update_notifications() {
	$user_id = get_current_user_id();
	update_user_meta($user_id,$user_id.'_new_notifications',0);
	if (!isset($_POST["mobile"])) {
		die(1);
	}
}
add_action( 'wp_ajax_update_notifications', 'update_notifications' );
add_action('wp_ajax_nopriv_update_notifications','update_notifications');
/* Resend confirmation */
function askme_resend_confirmation($user_id,$edit = "") {
	if ($edit == "edit") {
		$user_email = get_user_meta($user_id,"askme_edit_email",true);
	}else {
		$user_email = get_the_author_meta("user_email",$user_id);
	}
	$display_name = get_the_author_meta("display_name",$user_id);
	$rand_a = askme_token(15);
	update_user_meta($user_id,"activation",$rand_a);
	$confirm_link = esc_url_raw(add_query_arg(array("u" => $user_id,"activate" => $rand_a,"edit" => ($edit == "edit"?true:false)),esc_url(home_url('/'))));
	$send_text = askme_send_mail(
		array(
			'content'            => askme_options("email_confirm_link"),
			'user_id'            => $user_id,
			'confirm_link_email' => $confirm_link,
		)
	);
	$email_title = askme_options("title_confirm_link");
	$email_title = ($email_title != ""?$email_title:esc_html__("Confirm account","vbegy"));
	$email_title = askme_send_mail(
		array(
			'content'            => $email_title,
			'title'              => true,
			'break'              => '',
			'user_id'            => $user_id,
			'confirm_link_email' => $confirm_link,
		)
	);
	$email_template = askme_options("email_template");
	$mail_smtp = askme_options("mail_smtp");
	$email_template = ($mail_smtp == 1?askme_options("mail_username"):$email_template);
	askme_send_mails(
		array(
			'toEmail'     => esc_html($user_email),
			'toEmailName' => esc_html($display_name),
			'title'       => $email_title,
			'message'     => $send_text,
		)
	);
}
/* Cancel edit email */
function askme_cancel_edit_email () {
	$user_id = (int)$_POST["id"];
	$nonce = esc_html($_POST["nonce"]);
	if (wp_verify_nonce($nonce,'askme_cancel_edit_email')) {
		delete_user_meta($user_id,"activation");
		delete_user_meta($user_id,"askme_edit_email");
	}
	die();
}
add_action('wp_ajax_askme_cancel_edit_email','askme_cancel_edit_email');
add_action('wp_ajax_nopriv_askme_cancel_edit_email','askme_cancel_edit_email');
/* All comments counter */
function askme_comment_counter($post_id,$parent = 0) {
	global $wpdb;
	$parent = ($parent == 0?"AND comment_parent = 0":"");
	$count = $wpdb->get_var("SELECT COUNT(comment_post_id) AS count FROM $wpdb->comments WHERE comment_approved = 1 AND comment_post_ID = $post_id $parent");
	return $count;
}
/* Update the comments count */
function askme_update_comments_count($post_id) {
	$count_post_all = askme_comment_counter($post_id,1);
	$count_post_comments = askme_comment_counter($post_id);
	update_post_meta($post_id,"count_post_all",$count_post_all);
	update_post_meta($post_id,"count_post_comments",$count_post_comments);
}
/* Count the comments */
function askme_count_comments($post_id,$return = "count_post_all",$count_meta = "like_comments_only") {
	if ($count_meta == "like_comments_only") {
		$count_comment_only = askme_options("count_comment_only");
		$return = ($count_comment_only == 1?"count_post_comments":"count_post_all");
	}
	$count_post_all = get_post_meta($post_id,"count_post_all",true);
	if ($count_post_all === "") {
		askme_update_comments_count($post_id);
	}
	$block_count = askme_count_blocked_comments($post_id);
	$count = (int)($return == "count_post_all"?$count_post_all:get_post_meta($post_id,"count_post_comments",true));
	$count = (int)($count-$block_count);
	$count = ($count > 0?$count:0);
	return $count;
}
/* Count the blocked comments */
function askme_count_blocked_comments($post_id) {
	$block_count = 0;
	$block_users = askme_options("block_users");
	if ($block_users == 1) {
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			$get_block_users = get_user_meta($user_id,"askme_block_users",true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__in = array("author__in" => $get_block_users);
				$comments_args = array_merge($author__in,array('post_id' => $post_id,'status' => 'approve'));
				$get_comments = get_comments($comments_args);
				$block_count = (is_array($get_comments) && !empty($get_comments)?count($get_comments):0);
			}
		}
	}
	$count = (int)($block_count > 0?$block_count:0);
	return $count;
}
/* Block and unblock users */
if (!function_exists('askme_block_user')) :
	function askme_block_user () {
		if (!isset($_POST["mobile"])) {
			check_ajax_referer('block_nonce','block_nonce');
		}
		$get_current_user_id = get_current_user_id();
		$user_id = (int)$_POST['user_id'];
		$block_type = esc_html($_POST['block_type']);
		if ($get_current_user_id > 0 && $user_id != $get_current_user_id) {
			$get_block_users = get_user_meta($get_current_user_id,"askme_block_users",true);
			if ($block_type == "block") {
				if (empty($get_block_users)) {
					update_user_meta($get_current_user_id,"askme_block_users",array($user_id));
					$add_notification = true;
				}else if (is_array($get_block_users) && !in_array($user_id,$get_block_users)) {
					update_user_meta($get_current_user_id,"askme_block_users",array_merge($get_block_users,array($user_id)));
					$add_notification = true;
				}
				if (isset($add_notification)) {
					askme_notifications_activities($get_current_user_id,$user_id,"","","","block_user","activities");
				}
			}else {
				if (is_array($get_block_users) && in_array($user_id,$get_block_users)) {
					$get_block_users = remove_item_by_value($get_block_users,$user_id);
					update_user_meta($get_current_user_id,"askme_block_users",$get_block_users);
					askme_notifications_activities($get_current_user_id,$user_id,"","","","unblock_user","activities");
				}
			}
		}
		if (!isset($_POST["mobile"])) {
			die();
		}
	}
endif;
add_action('wp_ajax_askme_block_user','askme_block_user');
add_action('wp_ajax_nopriv_askme_block_user','askme_block_user');
/* Get points */
function askme_add_points($user_id,$points,$relation,$message,$post_id = 0,$comment_id = 0,$another_user_id = 0,$points_type = "points",$items = true) {
	$get_user = get_user_by("id",$user_id);
	$_points = get_user_meta($user_id,$get_user->user_login."_points",true);
	if ($relation == "+") {
		$_points++;
	}else {
		$_points--;
	}

	update_user_meta($user_id,$get_user->user_login."_points",$_points);
	add_user_meta($user_id,$get_user->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$get_points,$relation,$message));

	$points_user = get_user_meta($user_id,$points_type,true);
	update_user_meta($user_id,$points_type,($relation == "+"?$points_user+$get_points:$points_user-$get_points));
}?>