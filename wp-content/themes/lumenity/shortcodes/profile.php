<?php /* ask_edit_profile_shortcode */
add_shortcode('ask_edit_profile', 'ask_edit_profile_shortcode');
function ask_edit_profile_shortcode($atts, $content = null) {
	global $posted;
	$a = shortcode_atts( array(
	    'style' => '',
	), $atts );
	$out = '';
	if (!is_user_logged_in) {
		$out .= '<div class="note_error"><strong>'.__("Please login to edit profile .","vbegy").'</strong></div>
		<div class="form-style form-style-3">
			'.do_shortcode("[ask_login register_2='yes']").'
		</div>';
	}else {
		do_action('ask_edit_profile_form');
		if (isset($_POST['askme_profile_nonce']) && wp_verify_nonce($_POST['askme_profile_nonce'],'askme_profile_nonce')) {
			$valid_form = true;
		}
		$out .= '<form class="edit-profile-form vpanel_form" method="post" enctype="multipart/form-data">';
			$user_id = get_current_user_id();
			$user_info = get_userdata($user_id);
			$user_group = askme_get_user_group($user_info);
			$user_id = $user_info->ID;
			$your_avatar_meta = askme_avatar_name();
			$your_avatar = get_the_author_meta($your_avatar_meta,$user_id);
			$url = get_the_author_meta('url',$user_id);
			$twitter = get_the_author_meta('twitter',$user_id);
			$facebook = get_the_author_meta('facebook',$user_id);
			$tiktok = get_the_author_meta('tiktok',$user_id);
			$youtube = get_the_author_meta('youtube',$user_id);
			$linkedin = get_the_author_meta('linkedin',$user_id);
			$follow_email = get_the_author_meta('follow_email',$user_id);
			$follow_email = ($follow_email == "on" || $follow_email == 1?1:0);
			$display_name = get_the_author_meta('display_name',$user_id);
			$country = get_the_author_meta('country',$user_id);
			$city = get_the_author_meta('city',$user_id);
			$age = get_the_author_meta('age',$user_id);
			$phone = get_the_author_meta('phone',$user_id);
			$sex = get_the_author_meta('sex',$user_id);
			$instagram = get_the_author_meta('instagram',$user_id);
			$pinterest = get_the_author_meta('pinterest',$user_id);
			
			$edit_profile_items_1 = askme_options("edit_profile_items_1");
			$first_name_required = askme_options("first_name_required");
			$last_name_required = askme_options("last_name_required");
			$display_name_required = askme_options("display_name_required");
			$phone_required_profile = askme_options("phone_required_profile");
			$country_required_profile = askme_options("country_required_profile");
			$city_required_profile = askme_options("city_required_profile");
			$age_required_profile = askme_options("age_required_profile");
			$sex_required_profile = askme_options("sex_required_profile");
			$url_profile = askme_options("url_profile");
			$url_required_profile = askme_options("url_required_profile");
			$show_point_favorite = get_the_author_meta('show_point_favorite',$user_id);
			$received_email = get_the_author_meta('received_email',$user_id);
			$received_email_post = get_the_author_meta('received_email_post',$user_id);
			$received_message = get_the_author_meta('received_message',$user_id);

			$confirm_edit_email = askme_options("confirm_edit_email");
			if ($confirm_edit_email == 1) {
				$edit_email = get_user_meta($user_id,"askme_edit_email",true);
				if ($edit_email != "") {
					$out .= '<div class="alert-message warning alert-confirm-email"><p>'.sprintf(esc_html__('There is a pending change of the email to %1$s. %2$s Cancel %3$s','askme'),$edit_email,'<a class="cancel-edit-email" data-nonce="'.wp_create_nonce("askme_cancel_edit_email").'" data-id="'.$user_id.'" href="'.esc_url(get_page_link(askme_options('user_edit_profile_page'))).'">','</a>').'</p></div>';
				}
			}
			
			$out .= '<div class="form-inputs clearfix">';
				if (isset($edit_profile_items_1) && is_array($edit_profile_items_1) && !empty($edit_profile_items_1)) {
					foreach ($edit_profile_items_1 as $key_items => $value_items) {
						if ($key_items == "nickname" && isset($value_items["value"]) && $value_items["value"] == "nickname") {
							$out .= '<p>
								<label>'.__("Nickname","vbegy").'<span class="required">*</span></label>
								<input class="required-item" name="nickname" id="nickname" type="text" value="'.esc_attr(isset($_POST["nickname"]) && isset($valid_form)?$_POST["nickname"]:$user_info->nickname).'">
							</p>';
						}else if ($key_items == "first_name" && isset($value_items["value"]) && $value_items["value"] == "first_name") {
							$out .= '<p>
								<label '.($first_name_required == 1?'class="required"':'').'>'.__("First Name","vbegy").($first_name_required == 1?'<span>*</span>':'').'</label>
								<input'.($first_name_required == 1?' class="required-item"':'').' name="first_name" id="first_name" type="text" value="'.esc_attr(isset($_POST["first_name"]) && isset($valid_form)?$_POST["first_name"]:$user_info->first_name).'">
							</p>';
						}else if ($key_items == "last_name" && isset($value_items["value"]) && $value_items["value"] == "last_name") {
							$out .= '<p>
								<label '.($last_name_required == 1?'class="required"':'').'>'.__("Last Name","vbegy").($last_name_required == 1?'<span>*</span>':'').'</label>
								<input'.($last_name_required == 1?' class="required-item"':'').' name="last_name" id="last_name" type="text" value="'.esc_attr(isset($_POST["last_name"]) && isset($valid_form)?$_POST["last_name"]:$user_info->last_name).'">
							</p>';
						}else if ($key_items == "display_name" && isset($value_items["value"]) && $value_items["value"] == "display_name") {
							$out .= '<p>
								<label '.($display_name_required == 1?'class="required"':'').'>'.__("Display name","vbegy").($display_name_required == 1?'<span>*</span>':'').'</label>
								<input'.($display_name_required == 1?' class="required-item"':'').' name="display_name" id="display_name" type="text" value="'.esc_attr(isset($_POST["display_name"]) && isset($valid_form)?$_POST["display_name"]:$user_info->display_name).'">
							</p>';
						}else if ($key_items == "email" && isset($value_items["value"]) && $value_items["value"] == "email") {
							$out .= '<p>
								<label for="email" class="required">'.__("E-Mail","vbegy").'<span>*</span></label>
								<input class="required-item" name="email" id="email" type="email" value="'.esc_attr(isset($_POST["email"]) && isset($valid_form)?$_POST["email"]:$user_info->user_email).'">
							</p>'.apply_filters('askme_edit_profile_after_email',false,(isset($_POST)?$_POST:array()),$user_id,$a);
						}else if ((!isset($a["style"]) || (isset($a["style"]) && $a["style"] != "2")) && $key_items == "password" && isset($value_items["value"]) && $value_items["value"] == "password") {
							$out .= '<p>
								<label for="newpassword" class="required">'.__("Password","vbegy").'<span>*</span></label>
								<input class="required-item" name="pass1" id="newpassword" type="password" value="">
							</p>
							<p>
								<label for="newpassword2" class="required">'.__("Confirm Password","vbegy").'<span>*</span></label>
								<input class="required-item" name="pass2" id="newpassword2" type="password" value="">
							</p>';
						}else if ($key_items == "country" && isset($value_items["value"]) && $value_items["value"] == "country") {
							$out .= '<p>
								<label for="country" '.($country_required_profile == 1?'class="required"':'').'>'.__("Country","vbegy").($country_required_profile == 1?'<span>*</span>':'').'</label>
								<span class="styled-select">
									<select name="country" id="country" class="askme-custom-select'.($country_required_profile == 1?' required-item':'').'">
										<option value="">'.__( 'Select a country&hellip;', 'vbegy' ).'</option>';
											foreach( vpanel_get_countries() as $key => $value )
												$out .= '<option value="' . esc_attr( $key ) . '"' . selected( (isset($_POST["country"]) && isset($valid_form)?$_POST["country"]:$country), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';
									$out .= '</select>
								</span>
							</p>';
						}else if ($key_items == "city" && isset($value_items["value"]) && $value_items["value"] == "city") {
							$city_output = '<p>
								<label for="city" '.($city_required_profile == 1?'class="required"':'').'>'.__("City","vbegy").($city_required_profile == 1?'<span>*</span>':'').'</label>
								<input type="text" '.($city_required_profile == 1?'class="required-item"':'').' name="city" id="city" value="'.esc_attr(isset($_POST["city"]) && isset($valid_form)?$_POST["city"]:$city).'">
							</p>'.apply_filters('askme_edit_profile_after_city',false,(isset($_POST)?$_POST:array()),$user_id);
							$out .= apply_filters("askme_filter_ctiy_profile",$city_output,$user_id);
						}else if ($key_items == "age" && isset($value_items["value"]) && $value_items["value"] == "age") {
							$out .= '<p>
								<label for="age" '.($age_required_profile == 1?'class="required"':'').'>'.__("Age","vbegy").($age_required_profile == 1?'<span>*</span>':'').'</label>
								<input type="text" '.($age_required_profile == 1?'class="required-item"':'').' name="age" id="age" value="'.esc_attr(isset($_POST["age"]) && isset($valid_form)?$_POST["age"]:$age).'">
							</p>';
						}else if ($key_items == "phone" && isset($value_items["value"]) && $value_items["value"] == "phone") {
							$out .= '<p>
								<label for="phone" '.($phone_required_profile == 1?'class="required"':'').'>'.__("Phone","vbegy").($phone_required_profile == 1?'<span>*</span>':'').'</label>
								<input type="text" '.($phone_required_profile == 1?'class="required-item"':'').' name="phone" id="phone" value="'.esc_attr(isset($_POST["phone"]) && isset($valid_form)?$_POST["phone"]:$phone).'">
							</p>';
						}else if ($key_items == "gender" && isset($value_items["value"]) && $value_items["value"] == "gender") {
							$gender_other = askme_options("gender_other");
							$sex = (isset($_POST["sex"]) && isset($valid_form)?$_POST["sex"]:$sex);
							$out .= '<p>
								<label '.($sex_required_profile == 1?'class="required"':'').'>'.__("Gender","vbegy").($sex_required_profile == 1?'<span>*</span>':'').'</label>
								<input'.($sex_required_profile == 1?' class="required-item"':'').' id="sex_male" name="sex" type="radio" value="1"'.($sex == "male" || $sex == "1"?' checked="checked"':' checked="checked"').'>
								<label for="sex_male">'.__("Male","vbegy").'</label>
								<input'.($sex_required_profile == 1?' class="required-item"':'').' id="sex_female" name="sex" type="radio" value="2"'.($sex == "female" || $sex == "2"?' checked="checked"':'').'>
								<label for="sex_female">'.__("Female","vbegy").'</label>';
								if ($gender_other == 1) {
									$out .= '<input'.($sex_required_profile == 1?' class="required-item"':'').' id="sex_other" name="sex" type="radio" value="3"'.($sex == "other" || $sex == "3"?' checked="checked"':'').'>
									<label for="sex_other">'.__("Other","vbegy").'</label>';
								}
							$out .= '</p>';
						}
					}
				}
			$out .= '</div>
			<div class="form-style form-style-2 form-style-3">';
				$profile_picture_profile = askme_options("profile_picture_profile");
				if ($profile_picture_profile == 1) {
					$profile_picture_required_profile = askme_options("profile_picture_required_profile");
					if ($your_avatar) {
						$out .= "<div class='user-profile-img edit-profile-img'>".askme_user_avatar($your_avatar,79,79,$user_id,$user_info->display_name)."</div>";
					}
					
					$out .= '
						<label '.($profile_picture_required_profile == 1?'class="required"':'').' for="'.$your_avatar_meta.'">'.__("Profile Picture","vbegy").($profile_picture_required_profile == 1?'<span>*</span>':'').'</label>
						<div class="fileinputs">
							<input type="file" name="'.$your_avatar_meta.'" id="'.$your_avatar_meta.'" value="'.$your_avatar.'">
							<div class="fakefile">
								<button type="button" class="small margin_0">'.__("Select file","vbegy").'</button>
								<span><i class="icon-arrow-up"></i>'.__("Browse","vbegy").'</span>
							</div>
						</div>
					<div class="clearfix"></div>
					<p></p>';
				}
				
				$out .= '<p>
					<label for="description">'.__("About Yourself","vbegy").'</label>
					<textarea name="description" id="description" cols="58" rows="8">'.esc_attr(isset($_POST["description"]) && isset($valid_form)?$_POST["description"]:$user_info->description).'</textarea>
				</p>
			</div>
			<div class="form-inputs clearfix">';
				if ($url_profile == 1) {
					$out .= '<p>
						<label '.($url_required_profile == 1?'class="required"':'').'>'.__("Website","vbegy").($url_required_profile == 1?'<span>*</span>':'').'</label>
						<input'.($url_required_profile == 1?' class="required-item"':'').' name="url" id="url" type="text" value="'.esc_url(isset($_POST["url"]) && isset($valid_form)?$_POST["url"]:$url).'">
					</p>';
				}
				$out .= '<p>
					<label for="facebook">'.__("Facebook","vbegy").'</label>
					<input type="text" name="facebook" id="facebook" value="'.esc_url(isset($_POST["facebook"]) && isset($valid_form)?$_POST["facebook"]:$facebook).'">
				</p>
				<p>
					<label for="twitter">'.__("Twitter","vbegy").'</label>
					<input type="text" name="twitter" id="twitter" value="'.esc_url(isset($_POST["twitter"]) && isset($valid_form)?$_POST["twitter"]:$twitter).'">
				</p>
				<p>
					<label for="tiktok">'.__("TikTok","vbegy").'</label>
					<input type="text" name="tiktok" id="tiktok" value="'.esc_url(isset($_POST["tiktok"]) && isset($valid_form)?$_POST["tiktok"]:$tiktok).'">
				</p>
				<p>
					<label for="youtube">'.__("Youtube","vbegy").'</label>
					<input type="text" name="youtube" id="youtube" value="'.esc_url(isset($_POST["youtube"]) && isset($valid_form)?$_POST["youtube"]:$youtube).'">
				</p>
				<p>
					<label for="linkedin">'.__("Linkedin","vbegy").'</label>
					<input type="text" name="linkedin" id="linkedin" value="'.esc_url(isset($_POST["linkedin"]) && isset($valid_form)?$_POST["linkedin"]:$linkedin).'">
				</p>
				<p>
					<label for="instagram">'.__("Instagram","vbegy").'</label>
					<input type="text" name="instagram" id="instagram" value="'.esc_url(isset($_POST["instagram"]) && isset($valid_form)?$_POST["instagram"]:$instagram).'">
				</p>
				<p>
					<label for="pinterest">'.__("Pinterest","vbegy").'</label>
					<input type="text" name="pinterest" id="pinterest" value="'.esc_url(isset($_POST["pinterest"]) && isset($valid_form)?$_POST["pinterest"]:$pinterest).'">
				</p>
			</div>
			
			<label for="show_point_favorite">
				<input type="checkbox" name="show_point_favorite" id="show_point_favorite" value="1" '.checked((isset($_POST["show_point_favorite"]) && isset($valid_form)?$_POST["show_point_favorite"]:$show_point_favorite),1,false).'>
				'.__("Show your private pages for all the users?","vbegy").'
			</label>

			<label for="follow_email">
				<input type="checkbox" name="follow_email" id="follow_email" value="1" '.checked(esc_attr(isset($_POST["follow_email"]) && isset($valid_form)?$_POST["follow_email"]:$follow_email),1,false).'>
				'.__("Follow-up email","vbegy").'
			</label>';

			$send_email_and_notification_question = askme_options("send_email_and_notification_question");
			$send_email_new_question_value = "send_email_new_question";
			$send_email_question_groups_value = "send_email_question_groups";
			if ($send_email_and_notification_question == "both") {
				$send_email_new_question_value = "send_email_new_question_both";
				$send_email_question_groups_value = "send_email_question_groups_both";
			}
			$send_email_new_question = askme_options($send_email_new_question_value);
			$send_email_question_groups = askme_options($send_email_question_groups_value);
			
			if ($send_email_new_question == 1) {
				if (isset($send_email_question_groups) && is_array($send_email_question_groups)) {
					foreach ($send_email_question_groups as $key => $value) {
						if ($value == 1) {
							$send_email_question_groups[$key] = $key;
						}else {
							unset($send_email_question_groups[$key]);
						}
					}
				}
				if (is_array($send_email_question_groups) && in_array($user_group,$send_email_question_groups)) {
					$out .= '<label for="received_email">
						<input type="checkbox" name="received_email" id="received_email" value="1" '.checked(esc_attr(isset($_POST["received_email"]) && isset($valid_form)?$_POST["received_email"]:$received_email),1,false).'>
						'.__("Received mail when user ask a new question","vbegy").'
					</label>';
				}
			}

			$send_email_and_notification_post = askme_options("send_email_and_notification_post");
			$send_email_new_post_value = "send_email_new_post";
			$send_email_post_groups_value = "send_email_post_groups";
			if ($send_email_and_notification_post == "both") {
				$send_email_new_post_value = "send_email_new_post_both";
				$send_email_post_groups_value = "send_email_post_groups_both";
			}
			$send_email_new_post = askme_options($send_email_new_post_value);
			$send_email_post_groups = askme_options($send_email_post_groups_value);
			
			if ($send_email_new_post == 1) {
				if (isset($send_email_post_groups) && is_array($send_email_post_groups)) {
					foreach ($send_email_post_groups as $key => $value) {
						if ($value == 1) {
							$send_email_post_groups[$key] = $key;
						}else {
							unset($send_email_post_groups[$key]);
						}
					}
				}
				if (is_array($send_email_post_groups) && in_array($user_group,$send_email_post_groups)) {
					$out .= '<label for="received_email_post">
						<input type="checkbox" name="received_email_post" id="received_email_post" value="1" '.checked(esc_attr(isset($_POST["received_email_post"]) && isset($valid_form)?$_POST["received_email_post"]:$received_email_post),1,false).'>
						'.__("Received mail when user add a new post","vbegy").'
					</label>';
				}
			}
			
			$active_message = askme_options("active_message");
			if ($active_message = 1) {
				$out .= '<label for="received_message">
					<input type="checkbox" name="received_message" id="received_message" value="1" '.checked(esc_attr(isset($_POST["received_message"]) && isset($valid_form)?$_POST["received_message"]:$received_message),($received_message == ""?"":1),false).'>
					'.__("Received message from another users?","vbegy").'
				</label>';
			}
			
			$out .= '<p class="form-submit">
				<input type="hidden" name="user_action" value="edit_profile">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="admin_bar_front" value="1">
				<input type="hidden" name="user_id" id="user_id" value="'.$user_id.'">
				<input type="hidden" name="user_login" id="user_login" value="'.$user_info->user_login.'">
				'.wp_nonce_field('askme_profile_nonce','askme_profile_nonce',true,false).'
				<input type="submit" value="'.__("Save","vbegy").'" class="button color small login-submit submit">
			</p>
		
		</form>';
	}
	return $out;
}
/* ask_edit_profile_form */
function ask_edit_profile_form($data = array()) {
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	if (isset($data["user_action"]) && $data["user_action"] == "edit_profile") :
		$return = askme_check_edit_profile($data);
		if (is_wp_error($return)) :
			$error_string = $return->get_error_message();
   			echo '<div class="ask_error"><span><p>'.$return->get_error_message().'</p></span></div>';
   		else :
   			echo '<div class="ask_done"><span><p>'.__("Profile has been updated","vbegy").'</p></span></div>';
   		endif;
	endif;
}
add_action('ask_edit_profile_form','ask_edit_profile_form');
/* askme_check_edit_profile */
function askme_check_edit_profile($data = array()) {
	$user_id = get_current_user_id();
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	if ($user_id > 0 && isset($data['askme_profile_nonce']) && wp_verify_nonce($data['askme_profile_nonce'],'askme_profile_nonce')) {
		return askme_process_edit_profile_form($data,$user_id);
	}else {
		$errors = new WP_Error();
		$errors->add('nonce-error', __("There is an error, Please reload the page and try again.","vbegy"));
		return $errors;
	}
}
/* askme_process_edit_profile_form */
function askme_process_edit_profile_form($data = array(),$user_id = 0) {
	if ($user_id > 0) {
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		return askme_complete_edit_profile($data,$user_id);
	}
}
/* askme_complete_edit_profile */
function askme_complete_edit_profile($data = array(),$user_id = 0) {
	if ($user_id > 0) {
		$data = (is_array($data) && !empty($data)?$data:$_POST);
		global $posted;
		require_once(ABSPATH . 'wp-admin/includes/user.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		$errors = "";
		$errors_2 = new WP_Error();
		$posted = array(
			'email'               => (isset($data['email']) && $data['email'] != ""?esc_html($data['email']):""),
			'pass1'               => ((isset($data["mobile"]) || (isset($data['pass1']))) && $data['pass1'] != ""?esc_html($data['pass1']):""),
			'pass2'               => ((isset($data["mobile"]) || (isset($data['pass2']))) && $data['pass2'] != ""?esc_html($data['pass2']):""),
			'first_name'          => (isset($data['first_name']) && $data['first_name'] != ""?esc_html($data['first_name']):""),
			'last_name'           => (isset($data['last_name']) && $data['last_name'] != ""?esc_html($data['last_name']):""),
			'nickname'            => (isset($data['nickname']) && $data['nickname'] != ""?esc_html($data['nickname']):""),
			'display_name'        => (isset($data['display_name']) && $data['display_name'] != ""?esc_html($data['display_name']):""),
			'country'             => (isset($data['country']) && $data['country'] != ""?esc_html($data['country']):""),
			'city'                => (isset($data['city']) && $data['city'] != ""?esc_html($data['city']):""),
			'phone'               => (isset($data['phone']) && $data['phone'] != ""?esc_html($data['phone']):""),
			'sex'                 => (isset($data['sex']) && $data['sex'] != ""?esc_html($data['sex']):""),
			'age'                 => (isset($data['age']) && $data['age'] != ""?esc_html($data['age']):""),
			'url'                 => (isset($data['url']) && $data['url'] != ""?esc_url($data['url']):""),
			'description'         => (isset($data['description']) && $data['description'] != ""?esc_html($data['description']):""),
			'facebook'            => (isset($data['facebook']) && $data['facebook'] != ""?esc_url($data['facebook']):""),
			'tiktok'              => (isset($data['tiktok']) && $data['tiktok'] != ""?esc_url($data['tiktok']):""),
			'twitter'             => (isset($data['twitter']) && $data['twitter'] != ""?esc_url($data['twitter']):""),
			'youtube'             => (isset($data['youtube']) && $data['youtube'] != ""?esc_url($data['youtube']):""),
			'linkedin'            => (isset($data['linkedin']) && $data['linkedin'] != ""?esc_url($data['linkedin']):""),
			'instagram'           => (isset($data['instagram']) && $data['instagram'] != ""?esc_url($data['instagram']):""),
			'pinterest'           => (isset($data['pinterest']) && $data['pinterest'] != ""?esc_url($data['pinterest']):""),
			'show_point_favorite' => (isset($data['show_point_favorite']) && $data['show_point_favorite'] != ""?esc_html($data['show_point_favorite']):""),
			'received_message'    => (isset($data['received_message']) && $data['received_message'] != ""?esc_html($data['received_message']):""),
			'received_email'      => (isset($data['received_email']) && $data['received_email'] != ""?esc_html($data['received_email']):""),
			'received_email_post' => (isset($data['received_email_post']) && $data['received_email_post'] != ""?esc_html($data['received_email_post']):""),
			'follow_email'        => (isset($data['follow_email']) && $data['follow_email'] != ""?esc_html($data['follow_email']):""),
			'mobile'              => (isset($data['mobile']) && $data['mobile'] != ""?esc_html($data['mobile']):""),
		);
		
		$posted = apply_filters('askme_edit_profile_fields',$posted);
		$edit_profile_items_1 = askme_options("edit_profile_items_1");
		$nickname = (isset($edit_profile_items_1["nickname"]["value"]) && $edit_profile_items_1["nickname"]["value"] == "nickname"?1:0);
		$first_name = (isset($edit_profile_items_1["first_name"]["value"]) && $edit_profile_items_1["first_name"]["value"] == "first_name"?1:0);
		$last_name = (isset($edit_profile_items_1["last_name"]["value"]) && $edit_profile_items_1["last_name"]["value"] == "last_name"?1:0);
		$display_name = (isset($edit_profile_items_1["display_name"]["value"]) && $edit_profile_items_1["display_name"]["value"] == "display_name"?1:0);
		$profile_picture = (isset($edit_profile_items_1["image_profile"]["value"]) && $edit_profile_items_1["image_profile"]["value"] == "image_profile"?1:0);
		$country = (isset($edit_profile_items_1["country"]["value"]) && $edit_profile_items_1["country"]["value"] == "country"?1:0);
		$city = (isset($edit_profile_items_1["city"]["value"]) && $edit_profile_items_1["city"]["value"] == "city"?1:0);
		$phone = (isset($edit_profile_items_1["phone"]["value"]) && $edit_profile_items_1["phone"]["value"] == "phone"?1:0);
		$gender = (isset($edit_profile_items_1["gender"]["value"]) && $edit_profile_items_1["gender"]["value"] == "gender"?1:0);
		$age = (isset($edit_profile_items_1["age"]["value"]) && $edit_profile_items_1["age"]["value"] == "age"?1:0);
		$allow_duplicate_names = askme_options("allow_duplicate_names");
		$allow_nickname = (isset($allow_duplicate_names["nickname"]["value"]) && $allow_duplicate_names["nickname"]["value"] == "nickname"?1:0);
		$allow_display_name = (isset($allow_duplicate_names["display_name"]["value"]) && $allow_duplicate_names["display_name"]["value"] == "display_name"?1:0);
		
		$profile_picture_required_profile = askme_options("profile_picture_required_profile");
		$first_name_required = askme_options("first_name_required");
		$last_name_required = askme_options("last_name_required");
		$display_name_required = askme_options("display_name_required");
		$phone_required_profile = askme_options("phone_required_profile");
		$country_required_profile = askme_options("country_required_profile");
		$city_required_profile = askme_options("city_required_profile");
		$age_required_profile = askme_options("age_required_profile");
		$sex_required_profile = askme_options("sex_required_profile");

		$url_profile = askme_options("url_profile");
		$url_required_profile = askme_options("url_required_profile");
		
		if (empty($posted['email'])) $errors_2->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("There are required fields.","vbegy"));
		if ($posted['pass1'] !== $posted['pass2']) $errors_2->add('required-field','<strong>'.__("Error","vbegy").':&nbsp;</strong> '.__("Password does not match.","vbegy"));

		if ($nickname === 1 && empty($posted['nickname'])) {
			$errors_2->add('required-nickname', __("There are required fields ( Nickname ).","vbegy"));
		}
		if ($allow_nickname !== 1 && isset($posted['nickname']) && $posted['nickname'] != "") {
			global $wpdb;
			$check_nickname = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users as users, $wpdb->usermeta as meta WHERE users.ID = meta.user_id AND meta.meta_key = 'nickname' AND meta.meta_value = %s AND users.ID <> %d",$posted['nickname'],$user_id));
			if ($check_nickname > 0) {
				$errors_2->add('required-field','<strong>'.esc_html__("Error","vbegy").':&nbsp;</strong> '.esc_html__("This nickname is already available.","vbegy"));
			}
		}
		$confirm_edit_email = askme_options("confirm_edit_email");
		if ($confirm_edit_email == 1) {
			$user = get_userdata($user_id);
			$user_email = $user->user_email;
			// Check the e-mail address
			if (!is_email($data['email'])) {
				$errors->add('right-email',esc_html__("Please write correctly email.","vbegy"));
			}else if ($data['email'] != $user_email && email_exists($data['email'])) {
				$errors->add('registered-email',esc_html__("This email is already registered, please choose another one.","vbegy"));
			}
		}
		if ($first_name === 1 && $first_name_required == 1 && empty($posted['first_name'])) {
			$errors_2->add('required-first_name', __("There are required fields ( First Name ).","vbegy"));
		}
		if ($last_name === 1 && $last_name_required == 1 && empty($posted['last_name'])) {
			$errors_2->add('required-last_name', __("There are required fields ( Last Name ).","vbegy"));
		}
		if ($display_name === 1 && $display_name_required == 1 && empty($posted['display_name'])) {
			$errors_2->add('required-display_name', __("There are required fields ( Display Name ).","vbegy"));
		}
		if ($allow_display_name !== 1 && isset($posted['display_name']) && $posted['display_name'] != "") {
			global $wpdb;
			$check_display_name = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users WHERE display_name = %s AND ID <> %d",$posted['display_name'],$user_id));
			if ($check_display_name > 0) {
				$errors_2->add('required-field','<strong>'.esc_html__("Error","vbegy").':&nbsp;</strong> '.esc_html__("This display name is already available.","vbegy"));
			}
		}
		if ($country === 1 && $country_required_profile == 1 && empty($posted['country'])) {
			$errors_2->add('required-country', __("There are required fields ( Country ).","vbegy"));
		}
		if ($city === 1 && $city_required_profile == 1 && empty($posted['city'])) {
			$errors_2->add('required-city', __("There are required fields ( City ).","vbegy"));
		}
		if ($age === 1 && $age_required_profile == 1 && empty($posted['age'])) {
			$errors_2->add('required-age', __("There are required fields ( Age ).","vbegy"));
		}
		if ($phone === 1 && $phone_required_profile == 1 && empty($posted['phone'])) {
			$errors_2->add('required-phone', __("There are required fields ( Phone ).","vbegy"));
		}
		if ($gender === 1 && $sex_required_profile == 1 && empty($posted['sex'])) {
			$errors_2->add('required-sex', __("There are required fields ( Sex ).","vbegy"));
		}
		if ($url_profile == 1 && $url_required_profile == 1 && empty($posted['url'])) {
			$errors_2->add('required-url', __("There are required fields ( URL ).","vbegy"));
		}
		
		do_action('askme_edit_profile_errors',$errors_2,$posted);

		$confirm_edit_email = askme_options("confirm_edit_email");
		if ($confirm_edit_email == 1) {
			$data_email = $data["email"];
			if (isset($_POST) || isset($data)) {
				$user = get_userdata($user_id);
				$user_email = $user->user_email;
				$data["email"] = $_POST["email"] = esc_html($user_email);
			}
		}
		
		isset($data['admin_bar_front']) ? 'true' : 'false';
		$your_avatar_meta = askme_avatar_name();
		$get_your_avatar = get_user_meta($user_id,$your_avatar_meta,true);
		$errors_user = edit_user($user_id);
		if (is_wp_error($errors_user)) return $errors_user;

		if ($confirm_edit_email == 1 && isset($data_email)) {
			$data["email"] = $data_email;
		}

		do_action("askme_personal_update_profile",$user_id,$posted,isset($_FILES)?$_FILES:array(),"edit");
		
		if (isset($_FILES[$your_avatar_meta]) && !empty($_FILES[$your_avatar_meta]['name'])) :
			$mime = $_FILES[$your_avatar_meta]["type"];
			if (!isset($data['mobile']) && $mime != 'image/jpeg' && $mime != 'image/jpg' && $mime != 'image/png') {
				$errors_2->add('upload-error', esc_html__('Error type, Please upload: jpg,jpeg,png','vbegy'));
				if ($errors_2->get_error_code()) return $errors_2;
			}else {
				$your_avatar = wp_handle_upload($_FILES[$your_avatar_meta],array('test_form' => false),current_time('mysql'));
				if ($your_avatar && isset($your_avatar["url"])) :
					$filename = $your_avatar["file"];
					$filetype = wp_check_filetype( basename( $filename ), null );
					$wp_upload_dir = wp_upload_dir();
					
					$attachment = array(
						'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
						'post_mime_type' => $filetype['type'],
						'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					);
					$attach_id = wp_insert_attachment( $attachment, $filename );
					$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
					wp_update_attachment_metadata( $attach_id, $attach_data );
					update_user_meta($user_id,$your_avatar_meta,$attach_id);
				endif;
				if (isset($your_avatar['error']) && $your_avatar) :
					if (isset($errors_2->add)) {
						$errors_2->add('upload-error', esc_html__('Error in upload the image : ','vbegy') . $your_avatar['error']);
						if ($errors_2->get_error_code()) return $errors_2;
					}
					return $errors_2;
				endif;
			}
		else:
			if ($profile_picture_required_profile == 1 && $get_your_avatar == "") {
				$errors_2->add('required-profile_picture', __("There are required fields ( Profile Picture ).","vbegy"));
				return $errors_2;
			}
			update_user_meta($user_id,$your_avatar_meta,$get_your_avatar);
		endif;
		
		if (sizeof($errors_2->errors)>0) return $errors_2;

		do_action("askme_after_edit_profile",$user_id,$posted,isset($_FILES)?$_FILES:array(),"edit",(isset($user_email)?$user_email:""));

		if (isset($data["mobile"])) {
			return $user_id;
		}else {
			return;
		}
	}
}
/* After edit profile */
add_action("askme_after_edit_profile","askme_after_edit_profile",1,5);
function askme_after_edit_profile($user_id,$posted,$files = array(),$edit = "edit",$user_email = "") {
	$confirm_edit_email = askme_options("confirm_edit_email");
	if ($posted['email'] != $user_email && $confirm_edit_email == 1) {
		update_user_meta($user_id,"askme_edit_email",esc_html($posted['email']));
		$rand_a = askme_token(15);
		update_user_meta($user_id,"activation",$rand_a);
		$confirm_link = esc_url_raw(add_query_arg(array("u" => $user_id,"activate" => $rand_a,"edit" => true),esc_url(home_url('/'))));
		$send_text = askme_send_mail(
			array(
				'content'            => askme_options("edit_email_confirm_link"),
				'user_id'            => $user_id,
				'confirm_link_email' => $confirm_link,
			)
		);
		$email_title = askme_options("title_confirm_edit_email_link");
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
		if (!isset($posted['display_name'])) {
			$get_user = get_userdata($user_id);
		}
		askme_send_mails(
			array(
				'toEmail'     => esc_html($posted['email']),
				'toEmailName' => esc_html(isset($posted['display_name'])?$posted['display_name']:$get_user->display_name),
				'title'       => $email_title,
				'message'     => $send_text,
			)
		);
		if (!isset($posted['mobile'])) {
			if(!session_id()) session_start();
			$_SESSION['vbegy_session_all'] = '<div class="alert-message success"><p>'.esc_html__("Check your email please to activate your membership.","vbegy").'</p></div>';
		}
	}
}
/* vpanel_show_extra_profile_fields */
add_action( 'show_user_profile', 'vpanel_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'vpanel_show_extra_profile_fields' );
function vpanel_show_extra_profile_fields( $user ) { ?>
	<table class="form-table">
		<?php $user_group = askme_get_user_group($user);
		$user_review = askme_options("user_review");
		$user_id = get_current_user_id();
		if (is_super_admin($user_id) && $user_review == 1) {
			if (isset($user->caps["ask_under_review"]) && $user->caps["ask_under_review"] == 1) {?>
				<tr>
					<th><label for="approve_user"><?php _e("Approve this user","vbegy")?></label></th>
					<td>
						<input type="checkbox" name="approve_user" id="approve_user" value="1"><br>
					</td>
				</tr>
			<?php }
		}
		$your_avatar_meta = askme_avatar_name();
		$your_avatar = get_the_author_meta($your_avatar_meta,$user->ID);
		if (current_user_can('upload_files')) {?>
			<tr class="rwmb-upload-wrapper">
				<th><label for="<?php echo esc_attr($your_avatar_meta)?>"><?php _e("Your avatar","vbegy")?></label></th>
				<td>
					<input type="hidden" class="image_id" value="<?php echo (isset($your_avatar) && $your_avatar != "" && is_numeric($your_avatar)?esc_attr($your_avatar):esc_url($your_avatar));?>" id="<?php echo esc_attr($your_avatar_meta)?>" name="<?php echo esc_attr($your_avatar_meta)?>">
					<input id="<?php echo esc_attr($your_avatar_meta)?>_button" class="upload_image_button button upload-button-2" type="button" value="Upload Image">
				</td>
			</tr>
		<?php }
		
		if ($your_avatar) {?>
			<tr>
				<th><label><?php _e("Your avatar","vbegy")?></label></th>
				<td>
					<div class="<?php echo esc_attr($your_avatar_meta)?>"><?php echo askme_user_avatar($your_avatar,85,85,$user->ID,get_the_author_meta('display_name',$user->ID));?></div>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<th><label for="country"><?php _e("Country","vbegy")?></label></th>
			<td>
				<select name="country" id="country">
					<option value=""><?php _e( 'Select a country&hellip;', 'vbegy' )?></option>
						<?php foreach( vpanel_get_countries() as $key => $value )
							echo '<option value="' . esc_attr( $key ) . '"' . selected( esc_attr( get_the_author_meta( 'country', $user->ID ) ), esc_attr( $key ), false ) . '>' . esc_html( $value ) . '</option>';?>
				</select>
			</td>
		</tr>
		<?php $show_ctiy_admin = apply_filters("askme_filter_show_city_admin",true,$user->ID);
		if ($show_ctiy_admin == true) {?>
			<tr>
				<th><label for="city"><?php _e("City","vbegy")?></label></th>
				<td>
					<input type="text" name="city" id="city" value="<?php echo esc_attr( get_the_author_meta( 'city', $user->ID ) ); ?>" class="regular-text"><br>
				</td>
			</tr>
		<?php }?>
		<tr>
			<th><label for="age"><?php _e("Age","vbegy")?></label></th>
			<td>
				<input type="text" name="age" id="age" value="<?php echo esc_attr( get_the_author_meta( 'age', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
		<tr>
			<th><label for="phone"><?php _e("Phone","vbegy")?></label></th>
			<td>
				<input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
		<tr>
			<?php
			$sex = esc_attr(get_the_author_meta( 'sex', $user->ID ) );
			$gender_other = askme_options("gender_other");
			?>
			<th><label><?php _e("Gender","vbegy")?></label></th>
			<td>
				<input id="sex_male" name="sex" type="radio" value="1"<?php echo (isset($sex) && ($sex == "male" || $sex == "1")?' checked="checked"':' checked="checked"')?>>
				<label for="sex_male"><?php _e("Male","vbegy")?></label>
				
				<input id="sex_female" name="sex" type="radio" value="2"<?php echo (isset($sex) && ($sex == "female" || $sex == "2")?' checked="checked"':'')?>>
				<label for="sex_female"><?php _e("Female","vbegy")?></label>
				
				<?php if ($gender_other == 1) {?>
					<input id="sex_other" name="sex" type="radio" value="3"<?php echo (isset($sex) && ($sex == "other" || $sex == "3")?' checked="checked"':'')?>>
					<label for="sex_other"><?php _e("Other","vbegy")?></label>
				<?php }?>
			</td>
		</tr>
		<tr>
			<th><label for="follow_email"><?php _e("Follow-up email","vbegy")?></label></th>
			<td>
				<?php $follow_email = get_the_author_meta( 'follow_email', $user->ID );
				$follow_email = ($follow_email == "on" || $follow_email == 1?1:0)?>
				<input type="checkbox" name="follow_email" id="follow_email" value="1" <?php checked($follow_email,1,true)?>><br>
			</td>
		</tr>
		<?php $active_message = askme_options("active_message");
		if ($active_message == 1) {?>
			<tr>
				<?php $received_message = esc_attr( get_the_author_meta( 'received_message', $user->ID ) )?>
				<th><label for="received_message"><?php _e("Received messages?","vbegy")?></label></th>
				<td>
					<input type="checkbox" name="received_message" id="received_message" value="1" <?php checked($received_message,($received_message == ""?"":1),true)?>><br>
				</td>
			</tr>
		<?php }
		if (is_super_admin(get_current_user_id()) && !is_super_admin($user->ID)) {?>
			<tr>
				<?php $block_message = esc_attr( get_the_author_meta( 'block_message', $user->ID ) )?>
				<th><label for="block_message"><?php _e("block messages?","vbegy")?></label></th>
				<td>
					<input type="checkbox" name="block_message" id="block_message" value="1" <?php checked($block_message,1,true)?>><br>
				</td>
			</tr>
		<?php }?>
	</table>
	<h3><?php _e("Show the points, favorite question, followed question, authors i follow, followers, question follow, answer follow, post follow and comment follow","vbegy")?></h3>
	<table class="form-table">
		<tr>
			<?php $show_point_favorite = esc_attr( get_the_author_meta( 'show_point_favorite', $user->ID ) )?>
			<th><label for="show_point_favorite"><?php _e("Show this pages only for me or any one?","vbegy")?></label></th>
			<td>
				<input type="checkbox" name="show_point_favorite" id="show_point_favorite" value="1" <?php checked($show_point_favorite,1,true)?>><br>
			</td>
		</tr>
	</table>
	<?php $send_email_and_notification_question = askme_options("send_email_and_notification_question");
	$send_email_new_question_value = "send_email_new_question";
	$send_email_question_groups_value = "send_email_question_groups";
	if ($send_email_and_notification_question == "both") {
		$send_email_new_question_value = "send_email_new_question_both";
		$send_email_question_groups_value = "send_email_question_groups_both";
	}
	$send_email_new_question = askme_options($send_email_new_question_value);
	$send_email_question_groups = askme_options($send_email_question_groups_value);
	if ($send_email_new_question == 1) {
		if (isset($send_email_question_groups) && is_array($send_email_question_groups)) {
			foreach ($send_email_question_groups as $key => $value) {
				if ($value == 1) {
					$send_email_question_groups[$key] = $key;
				}else {
					unset($send_email_question_groups[$key]);
				}
			}
		}
		if (is_array($send_email_question_groups) && in_array($user_group,$send_email_question_groups)) {?>
			<h3><?php _e("Received email when any one ask a new question","vbegy")?></h3>
			<table class="form-table">
				<tr>
					<?php $received_email = esc_attr( get_the_author_meta( 'received_email', $user->ID ) )?>
					<th><label for="received_email"><?php _e("Received email?","vbegy")?></label></th>
					<td>
						<input type="checkbox" name="received_email" id="received_email" value="1" <?php checked($received_email,1,true)?>><br>
					</td>
				</tr>
			</table>
		<?php }
	}
	
	$send_email_and_notification_post = askme_options("send_email_and_notification_post");
	$send_email_new_post_value = "send_email_new_post";
	$send_email_post_groups_value = "send_email_post_groups";
	if ($send_email_and_notification_post == "both") {
		$send_email_new_post_value = "send_email_new_post_both";
		$send_email_post_groups_value = "send_email_post_groups_both";
	}
	$send_email_new_post = askme_options($send_email_new_post_value);
	$send_email_post_groups = askme_options($send_email_post_groups_value);
	if ($send_email_new_post == 1) {
		if (isset($send_email_post_groups) && is_array($send_email_post_groups)) {
			foreach ($send_email_post_groups as $key => $value) {
				if ($value == 1) {
					$send_email_post_groups[$key] = $key;
				}else {
					unset($send_email_post_groups[$key]);
				}
			}
		}
		if (is_array($send_email_post_groups) && in_array($user_group,$send_email_post_groups)) {?>
			<h3><?php _e("Received email when any one add a new post","vbegy")?></h3>
			<table class="form-table">
				<tr>
					<?php $received_email_post = esc_attr( get_the_author_meta( 'received_email_post', $user->ID ) )?>
					<th><label for="received_email_post"><?php _e("Received email?","vbegy")?></label></th>
					<td>
						<input type="checkbox" name="received_email_post" id="received_email_post" value="1" <?php checked($received_email_post,1,true)?>><br>
					</td>
				</tr>
			</table>
		<?php }
	}
	$active_points = askme_options("active_points");
	if (is_super_admin(get_current_user_id()) && $active_points == 1) {?>
		<h3><?php _e( 'Add or remove points for the user', 'vbegy' ) ?></h3>
		<table class="form-table">
			<tr>
				<th><label><?php _e("Add or remove points","vbegy")?></label></th>
				<td>
					<div>
						<select name="add_remove_point">
							<option value="add"><?php _e("Add","vbegy")?></option>
							<option value="remove"><?php _e("Remove","vbegy")?></option>
						</select>
					</div><br>
					<div><?php _e("The points","vbegy")?></div><br>
					<input type="text" name="the_points" class="regular-text"><br><br>
					<div><?php _e("The reason","vbegy")?></div><br>
					<input type="text" name="the_reason" class="regular-text"><br>
				</td>
			</tr>
		</table>
	<?php }
	if (is_super_admin(get_current_user_id())) {?>
		<h3><?php _e( 'Check if you need this user choose or remove the best answer', 'vbegy' ) ?></h3>
		<table class="form-table">
			<tr>
				<?php $user_best_answer = esc_attr( get_the_author_meta( 'user_best_answer', $user->ID ) )?>
				<th><label for="user_best_answer"><?php _e("Select user?","vbegy")?></label></th>
				<td>
					<input type="checkbox" name="user_best_answer" id="user_best_answer" value="1" <?php checked($user_best_answer,1,true)?>><br>
				</td>
			</tr>
		</table>
		<h3><?php _e( 'Check if you need this user is verified user', 'vbegy' ) ?></h3>
		<table class="form-table">
			<tr>
				<?php $verified_user = esc_attr( get_the_author_meta( 'verified_user', $user->ID ) )?>
				<th><label for="verified_user"><?php _e("Select user?","vbegy")?></label></th>
				<td>
					<input type="checkbox" name="verified_user" id="verified_user" value="1" <?php checked($verified_user,1,true)?>><br>
				</td>
			</tr>
		</table>
		<input type="hidden" name="admin" value="save">
	<?php }?>
	<h3><?php _e( 'Social Networking', 'vbegy' ) ?></h3>
	<table class="form-table">
		<tr>
			<th><label for="twitter"><?php _e("Twitter","vbegy")?></label></th>
			<td>
				<input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
		<tr>
			<th><label for="facebook"><?php _e("Facebook","vbegy")?></label></th>
			<td>
				<input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
		<tr>
			<th><label for="tiktok"><?php _e("TikTok","vbegy")?></label></th>
			<td>
				<input type="text" name="tiktok" id="tiktok" value="<?php echo esc_attr( get_the_author_meta( 'tiktok', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
		<tr>
			<th><label for="youtube"><?php _e("Youtube","vbegy")?></label></th>
			<td>
				<input type="text" name="youtube" id="youtube" value="<?php echo esc_attr( get_the_author_meta( 'youtube', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
		<tr>
			<th><label for="linkedin"><?php _e("linkedin","vbegy")?></label></th>
			<td>
				<input type="text" name="linkedin" id="linkedin" value="<?php echo esc_attr( get_the_author_meta( 'linkedin', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
		<tr>
			<th><label for="pinterest"><?php _e("Pinterest","vbegy")?></label></th>
			<td>
				<input type="text" name="pinterest" id="pinterest" value="<?php echo esc_attr( get_the_author_meta( 'pinterest', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
		<tr>
			<th><label for="instagram"><?php _e("Instagram","vbegy")?></label></th>
			<td>
				<input type="text" name="instagram" id="instagram" value="<?php echo esc_attr( get_the_author_meta( 'instagram', $user->ID ) ); ?>" class="regular-text"><br>
			</td>
		</tr>
	</table>
	<?php $protocol = is_ssl() ? 'https' : 'http';?>
	<input type="hidden" name="redirect_to" value="<?php echo urldecode(wp_unslash( $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))?>">
<?php }
/* Save user's meta */
add_action('askme_personal_update_profile','aslme_save_extra_profile_fields',1,2);
add_action( 'personal_options_update', 'aslme_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'aslme_save_extra_profile_fields' );
function aslme_save_extra_profile_fields( $user_id,$data = array() ) {
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	if ( !isset($data["mobile"]) && !current_user_can( 'edit_user', $user_id ) ) return false;
	
	if (isset($data['twitter'])) {
		$twitter = sanitize_text_field($data['twitter']);
		update_user_meta( $user_id, 'twitter', $twitter );
	}
	
	if (isset($data['facebook'])) {
		$facebook = sanitize_text_field($data['facebook']);
		update_user_meta( $user_id, 'facebook', $facebook );
	}
	
	if (isset($data['tiktok'])) {
		$tiktok = sanitize_text_field($data['tiktok']);
		update_user_meta( $user_id, 'tiktok', $tiktok );
	}
	
	if (isset($data['linkedin'])) {
		$linkedin = sanitize_text_field($data['linkedin']);
		update_user_meta( $user_id, 'linkedin', $linkedin );
	}
	
	if (isset($data['instagram'])) {
		$instagram = sanitize_text_field($data['instagram']);
		update_user_meta( $user_id, 'instagram', $instagram );
	}
	
	if (isset($data['pinterest'])) {
		$pinterest = sanitize_text_field($data['pinterest']);
		update_user_meta( $user_id, 'pinterest', $pinterest );
	}
	
	if (isset($data['youtube'])) {
		$youtube = sanitize_text_field($data['youtube']);
		update_user_meta( $user_id, 'youtube', $youtube );
	}
	
	if (isset($data['follow_email'])) {
		$follow_email = sanitize_text_field($data['follow_email']);
		update_user_meta( $user_id, 'follow_email', $follow_email );
	}else {
		delete_user_meta( $user_id, 'follow_email' );
	}
	
	$your_avatar_meta = askme_avatar_name();
	if (isset($data[$your_avatar_meta])) {
		$your_avatar = sanitize_text_field($data[$your_avatar_meta]);
		update_user_meta( $user_id, $your_avatar_meta, $your_avatar );
	}
	
	if (isset($data['country'])) {
		$country = sanitize_text_field($data['country']);
		update_user_meta( $user_id, 'country', $country );
	}
	
	if (isset($data['city'])) {
		$city = sanitize_text_field($data['city']);
		update_user_meta( $user_id, 'city', $city );
	}
	
	if (isset($data['age'])) {
		$age = sanitize_text_field($data['age']);
		update_user_meta( $user_id, 'age', $age );
	}
	
	if (isset($data['sex'])) {
		$sex = sanitize_text_field($data['sex']);
		update_user_meta( $user_id, 'sex', $sex );
	}
	
	if (isset($data['phone'])) {
		$phone = sanitize_text_field($data['phone']);
		update_user_meta( $user_id, 'phone', $phone );
	}
	
	do_action("askme_edit_profile_save",(isset($data)?$data:array()),$user_id);
	
	if (isset($data['show_point_favorite'])) {
		$show_point_favorite = sanitize_text_field($data['show_point_favorite']);
		update_user_meta( $user_id, 'show_point_favorite', $show_point_favorite );
	}else {
		delete_user_meta( $user_id, 'show_point_favorite' );
	}
	
	if (isset($data['received_message'])) {
		$received_message = sanitize_text_field($data['received_message']);
		update_user_meta( $user_id, 'received_message', $received_message );
	}else {
		update_user_meta( $user_id, 'received_message', 2 );
	}
	
	if (isset($data['block_message'])) {
		$block_message = sanitize_text_field($data['block_message']);
		update_user_meta( $user_id, 'block_message', $block_message );
	}else {
		delete_user_meta( $user_id, 'block_message' );
	}
	
	if (isset($data['received_email'])) {
		$received_email = sanitize_text_field($data['received_email']);
		update_user_meta( $user_id, 'received_email', $received_email );
	}else {
		delete_user_meta( $user_id, 'received_email' );
	}
	
	if (isset($data['received_email_post'])) {
		$received_email_post = sanitize_text_field($data['received_email_post']);
		update_user_meta( $user_id, 'received_email_post', $received_email_post );
	}else {
		delete_user_meta( $user_id, 'received_email_post' );
	}
	
	if (isset($data['admin']) && $data['admin'] == "save" && isset($data['user_best_answer'])) {
		$user_best_answer = sanitize_text_field($data['user_best_answer']);
		update_user_meta( $user_id, 'user_best_answer', $user_best_answer );
	}
	
	if (isset($data['admin']) && $data['admin'] == "save" && isset($data['verified_user'])) {
		$verified_user = sanitize_text_field($data['verified_user']);
		update_user_meta( $user_id, 'verified_user', $verified_user );
	}
	
	$active_points = askme_options("active_points");
	if (is_super_admin(get_current_user_id()) && $active_points == 1) {
		$add_remove_point = "";
		$the_points = "";
		$the_reason = "";
		if (isset($data['add_remove_point'])) {
			$add_remove_point = esc_attr($data['add_remove_point']);
		}
		if (isset($data['the_points'])) {
			$the_points = (int)esc_attr($data['the_points']);
		}
		if (isset($data['the_reason'])) {
			$the_reason = esc_attr($data['the_reason']);
		}
		if ($the_points > 0) {
			$current_user = get_user_by("id",$user_id);
			$_points = get_user_meta($user_id,$current_user->user_login."_points",true);
			$_points++;
			
			$points_user = get_user_meta($user_id,"points",true);
			if ($add_remove_point == "remove") {
				$add_remove_point_last = "-";
				$the_reason_last = "admin_remove_points";
				update_user_meta($user_id,"points",$points_user-$the_points);
			}else {
				$add_remove_point_last = "+";
				$the_reason_last = "admin_add_points";
				update_user_meta($user_id,"points",$points_user+$the_points);
			}
			
			if (get_current_user_id() > 0 && $user_id > 0) {
				askme_notifications_activities($user_id,get_current_user_id(),"","","",$the_reason_last,"notifications");
			}
			
			$the_reason = (isset($the_reason) && $the_reason != ""?$the_reason:$the_reason_last);
			update_user_meta($user_id,$current_user->user_login."_points",$_points);
			add_user_meta($user_id,$current_user->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$the_points,$add_remove_point_last,$the_reason));
		}
	}
	$edit_profile_items_1 = askme_options("edit_profile_items_1");
	$nickname = (isset($edit_profile_items_1["nickname"]["value"]) && $edit_profile_items_1["nickname"]["value"] == "nickname"?"on":0);
	if ($nickname !== 'on' && isset($data['nickname'])) {
		$data['nickname'] = $_POST['nickname'] = get_the_author_meta("user_login",$user_id);
	}
	$nicename_nickname = (isset($data['nickname']) && $data['nickname'] != ""?sanitize_text_field($data['nickname']):sanitize_text_field($data['user_name']));
	edit_user($user_id);
	
	$user_data = get_userdata($user_id);
	$default_group = askme_get_user_group($user_data);
	if (isset($data['role']) && $data['role'] != "" && $default_group != $data['role']) {
		$default_group = esc_attr($data['role']);
	}
	
	if (is_super_admin(get_current_user_id()) && isset($data['approve_user']) && $data['approve_user'] == 1) {
		$default_group = askme_options("default_group");
		$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
		$approve_user = get_user_meta($user_id,"approve_user",true);
		if ($approve_user == "") {
			$send_text = askme_send_mail(
				array(
					'content' => askme_options("email_approve_user"),
					'user_id' => $user_id,
				)
			);
			$email_title = askme_options("title_approve_user");
			$email_title = ($email_title != ""?$email_title:esc_html__("Confirm account","vbegy"));
			$email_title = askme_send_mail(
				array(
					'content' => $email_title,
					'title'   => true,
					'break'   => '',
					'user_id' => $user_id,
				)
			);
			askme_send_mails(
				array(
					'toEmail'     => esc_html($user_data->user_email),
					'toEmailName' => esc_html($user_data->display_name),
					'title'       => $email_title,
					'message'     => $send_text,
				)
			);
			update_user_meta($user_id,"approve_user",1);
		}
	}
	
	wp_update_user(array('ID' => $user_id,'user_nicename' => $nicename_nickname,'nickname' => $nicename_nickname,'role' => $default_group));
	if (!isset($data["mobile"]) && isset($data["redirect_to"]) && $data["redirect_to"] != "") {
		wp_redirect(esc_url($data["redirect_to"]));
		die();
	}
}?>