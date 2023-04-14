<?php
/* Signup shortcode */
add_shortcode('ask_signup', 'ask_signup_shortcode');
function ask_signup_shortcode($atts, $content = null) {
	global $posted;
	$a = shortcode_atts( array(
	    'dark_button' => '',
	), $atts );
	$out = '';
	if (is_user_logged_in) {
		$user_login = get_userdata(get_current_user_id());
		$out .= is_user_logged_in_data(askme_options("user_links"));
	}else {
		$protocol = is_ssl() ? 'https' : 'http';
		$rand_w = rand(1,1000);
		$comfirm_password = askme_options("comfirm_password");
		$register_items = askme_options("register_items");
		$first_name_required = askme_options("first_name_required_register");
		$last_name_required = askme_options("last_name_required_register");
		$display_name_required = askme_options("display_name_required_register");
		$country_required_register = askme_options("country_required_register");
		$city_required_register = askme_options("city_required_register");
		$age_required_register = askme_options("age_required_register");
		$phone_required_register = askme_options("phone_required_register");
		$gender_required_register = askme_options("gender_required_register");

		$out .= do_action('askme_social_signup').apply_filters('askme_signup_before_form',false).'
		<form method="post" class="signup_form ask_form" enctype="multipart/form-data">';
			if (shortcode_exists('askme_social_login') || shortcode_exists('rdp-linkedin-login') || shortcode_exists('oa_social_login') || shortcode_exists('miniorange_social_login') || shortcode_exists('wordpress_social_login') || shortcode_exists('apsl-login') || shortcode_exists('apsl-login-lite') || shortcode_exists('nextend_social_login')) {
				$out .= '<p>'.
					(shortcode_exists('askme_social_signup')?do_shortcode("[askme_social_signup]"):"").
					(shortcode_exists('rdp-linkedin-login')?do_shortcode("[rdp-linkedin-login]"):"").
					(shortcode_exists('oa_social_login')?do_shortcode("[oa_social_login]"):"").
					(shortcode_exists('miniorange_social_login')?do_shortcode("[miniorange_social_login]"):"").
					(shortcode_exists('wordpress_social_login')?do_shortcode("[wordpress_social_login]"):"").
					(shortcode_exists('apsl-login')?do_shortcode("[apsl-login]"):"").
					(shortcode_exists('apsl-login-lite')?do_shortcode("[apsl-login-lite]"):"").
					(shortcode_exists('nextend_social_login')?do_shortcode("[nextend_social_login]"):"").
				'</p>';
			}
			$out .= do_action('ask_signup').'
			<div class="ask_error"></div>
				<div class="form-inputs clearfix">';
					if (isset($register_items) && is_array($register_items) && !empty($register_items)) {
						foreach ($register_items as $key_items => $value_items) {
							if ($key_items == "nickname" && isset($value_items["value"]) && $value_items["value"] == "nickname") {
								$out .= '<p>
									<label for="nickname_'.$rand_w.'" class="required">'.__("Nickname","vbegy").'<span>*</span></label>
									<input type="text" class="required-item" name="nickname" id="nickname_'.$rand_w.'" value="'.(isset($posted["nickname"])?$posted["nickname"]:"").'">
								</p>';
							}else if ($key_items == "username" && isset($value_items["value"]) && $value_items["value"] == "username") {
								$out .= '<p>
									<label for="user_name_'.$rand_w.'" class="required">'.__("Username","vbegy").'<span>*</span></label>
									<input type="text" class="required-item" name="user_name" id="user_name_'.$rand_w.'" value="'.(isset($posted["user_name"])?$posted["user_name"]:"").'">
								</p>';
							}else if ($key_items == "email" && isset($value_items["value"]) && $value_items["value"] == "email") {
								$out .= '<p>
									<label for="email_'.$rand_w.'" class="required">'.__("E-Mail","vbegy").'<span>*</span></label>
									<input type="email" class="required-item" name="email" id="email_'.$rand_w.'" value="'.(isset($posted["email"])?$posted["email"]:"").'">
								</p>';
							}else if ($key_items == "password" && isset($value_items["value"]) && $value_items["value"] == "password") {
							$out .= '<p>
								<label for="pass1_'.$rand_w.'" class="required">'.__("Password","vbegy").'<span>*</span></label>
								<input type="password" class="required-item" name="pass1" id="pass1_'.$rand_w.'" autocomplete="off">
							</p>';
							if ($comfirm_password != 1) {
								$out .= '<p>
									<label for="pass2_'.$rand_w.'" class="required">'.__("Confirm Password","vbegy").'<span>*</span></label>
									<input type="password" class="required-item" name="pass2" id="pass2_'.$rand_w.'" autocomplete="off">
								</p>';
							}
							}else if ($key_items == "image_profile" && isset($value_items["value"]) && $value_items["value"] == "image_profile") {
								$profile_picture_required_register = askme_options("profile_picture_required_register");
								$out .= '<label '.($profile_picture_required_register == 1?'class="required"':'').' for="attachment_'.$rand_w.'">'.__('Profile Picture','vbegy').($profile_picture_required_register == 1?'<span>*</span>':'').'</label>
								<div class="fileinputs">
									<input type="file" name="'.askme_avatar_name().'" id="attachment_'.$rand_w.'">
									<div class="fakefile">
										<button type="button" class="small margin_0">'.__('Select file','vbegy').'</button>
										<span><i class="icon-arrow-up"></i>'.__('Browse','vbegy').'</span>
									</div>
								</div>';
							}else if ($key_items == "first_name" && isset($value_items["value"]) && $value_items["value"] == "first_name") {
								$out .= '<p>
									<label for="first_name_'.$rand_w.'" '.($first_name_required == 1?'class="required"':'').'>'.__("First Name","vbegy").($first_name_required == 1?'<span>*</span>':'').'</label>
									<input'.($first_name_required == 1?' class="required-item"':'').' name="first_name" id="first_name_'.$rand_w.'" type="text" value="'.(isset($posted["first_name"])?$posted["first_name"]:"").'">
								</p>';
							}else if ($key_items == "last_name" && isset($value_items["value"]) && $value_items["value"] == "last_name") {
								$out .= '<p>
									<label for="last_name_'.$rand_w.'" '.($last_name_required == 1?'class="required"':'').'>'.__("Last Name","vbegy").($last_name_required == 1?'<span>*</span>':'').'</label>
									<input'.($last_name_required == 1?' class="required-item"':'').' name="last_name" id="last_name_'.$rand_w.'" type="text" value="'.(isset($posted["last_name"])?$posted["last_name"]:"").'">
								</p>';
							}else if ($key_items == "display_name" && isset($value_items["value"]) && $value_items["value"] == "display_name") {
								$out .= '<p>
									<label for="display_name_'.$rand_w.'" '.($display_name_required == 1?'class="required"':'').'>'.__("Display name","vbegy").($display_name_required == 1?'<span>*</span>':'').'</label>
									<input'.($display_name_required == 1?' class="required-item"':'').' name="display_name" id="display_name_'.$rand_w.'" type="text" value="'.(isset($posted["display_name"])?$posted["display_name"]:"").'">
								</p>';
							}else if ($key_items == "country" && isset($value_items["value"]) && $value_items["value"] == "country") {
								$out .= '<p>
									<label for="country_'.$rand_w.'" '.($country_required_register == 1?'class="required"':'').'>'.__("Country","vbegy").($country_required_register == 1?'<span>*</span>':'').'</label>
									<span class="styled-select">
										<select name="country" id="country_'.$rand_w.'" '.($country_required_register == 1?'class="required-item"':'').'>
											<option value="">'.__( 'Select a country&hellip;', 'vbegy' ).'</option>';
												foreach( vpanel_get_countries() as $key => $value )
													$out .= '<option value="' . esc_attr( $key ) . '"' . (isset($posted["country"])?selected( $posted["country"], esc_attr( $key ), false ):"") . '>' . esc_html( $value ) . '</option>';
										$out .= '</select>
									</span>
								</p>';
							}else if ($key_items == "city" && isset($value_items["value"]) && $value_items["value"] == "city") {
								$city_output = '<p>
									<label for="city_'.$rand_w.'" '.($city_required_register == 1?'class="required"':'').'>'.__("City","vbegy").($city_required_register == 1?'<span>*</span>':'').'</label>
									<input type="text" '.($city_required_register == 1?'class="required-item"':'').' name="city" id="city_'.$rand_w.'" value="'.(isset($posted["city"])?$posted["city"]:"").'">
								</p>';
								$out .= apply_filters("askme_filter_ctiy_register",$city_output);
							}else if ($key_items == "age" && isset($value_items["value"]) && $value_items["value"] == "age") {
								$out .= '<p>
									<label for="age_'.$rand_w.'" '.($age_required_register == 1?'class="required"':'').'>'.__("Age","vbegy").($age_required_register == 1?'<span>*</span>':'').'</label>
									<input type="text" '.($age_required_register == 1?'class="required-item"':'').' name="age" id="age_'.$rand_w.'" value="'.(isset($posted["age"])?$posted["age"]:"").'">
								</p>';
							}else if ($key_items == "phone" && isset($value_items["value"]) && $value_items["value"] == "phone") {
								$out .= '<p>
									<label for="phone_'.$rand_w.'" '.($phone_required_register == 1?'class="required"':'').'>'.__("Phone","vbegy").($phone_required_register == 1?'<span>*</span>':'').'</label>
									<input type="text" '.($phone_required_register == 1?'class="required-item"':'').' name="phone" id="phone_'.$rand_w.'" value="'.(isset($posted["phone"])?$posted["phone"]:"").'">
								</p>';
							}else if ($key_items == "gender" && isset($value_items["value"]) && $value_items["value"] == "gender") {
								$gender_other = askme_options("gender_other");
								$out .= '<p>
									<label '.($gender_required_register == 1?'class="required"':'').'>'.__("Gender","vbegy").($gender_required_register == 1?'<span>*</span>':'').'</label>
									<input'.($gender_required_register == 1?' class="required-item"':'').' id="sex_male_'.$rand_w.'" name="sex" type="radio" value="1"'.(isset($posted["sex"]) && $posted["sex"] == "1"?' checked="checked"':' checked="checked"').'>
									<label for="sex_male_'.$rand_w.'">'.__("Male","vbegy").'</label>
								</p>
								<p>
									<input'.($gender_required_register == 1?' class="required-item"':'').' id="sex_female_'.$rand_w.'" name="sex" type="radio" value="2"'.(isset($posted["sex"]) && $posted["sex"] == "2"?' checked="checked"':'').'>
									<label for="sex_female_'.$rand_w.'">'.__("Female","vbegy").'</label>
								</p>';
								if ($gender_other == 1) {
									$out .= '<p>
										<input'.($gender_required_register == 1?' class="required-item"':'').' id="sex_other_'.$rand_w.'" name="sex" type="radio" value="3"'.(isset($posted["sex"]) && $posted["sex"] == "3"?' checked="checked"':'').'>
										<label for="sex_other_'.$rand_w.'">'.__("Other","vbegy").'</label>
									</p>';
								}
							}
						}
					}
					
					$out .= askme_add_captcha(askme_options("the_captcha_register"),"register",$rand_w);
					
					$terms_active_register = askme_options("terms_active_register");
					if ($terms_active_register == 1) {
						$terms_checked_register = askme_options("terms_checked_register");
						if ((isset($posted['agree_terms']) && $posted['agree_terms'] == 1) || ($terms_checked_register == 1 && empty($posted))) {
							$active_terms = true;
						}
						$terms_link_register = askme_options("terms_link_register");
						$terms_page_register = askme_options("terms_page_register");
						$terms_active_target_register = askme_options("terms_active_target_register");
						$privacy_policy_register = askme_options('privacy_policy_register');
						$privacy_active_target_register = askme_options('privacy_active_target_register');
						$privacy_page_register = askme_options('privacy_page_register');
						$privacy_link_register = askme_options('privacy_link_register');
						$out .= '<p class="question_poll_p">
							<label for="agree_terms-'.$rand_w.'" class="required">'.__("Terms","vbegy").'<span>*</span></label>
							<input type="checkbox" id="agree_terms-'.$rand_w.'" name="agree_terms" value="1" '.(isset($active_terms)?"checked='checked'":"").'>
							<span class="question_poll">'.sprintf(wp_kses(__("By registering, you agree to the <a target='%s' href='%s'>Terms of Service</a>%s.","vbegy"),array('a' => array('href' => array(),'target' => array()))),($terms_active_target_register == "same_page"?"_self":"_blank"),(isset($terms_link_register) && $terms_link_register != ""?$terms_link_register:(isset($terms_page_register) && $terms_page_register != ""?get_page_link($terms_page_register):"#")),($privacy_policy_register == 1?" ".sprintf(wp_kses(__("and <a target='%s' href='%s'>Privacy Policy</a>","vbegy"),array('a' => array('href' => array(),'target' => array()))),($privacy_active_target_register == "same_page"?"_self":"_blank"),(isset($privacy_link_register) && $privacy_link_register != ""?$privacy_link_register:(isset($privacy_page_register) && $privacy_page_register != ""?get_page_link($privacy_page_register):"#"))):"")).'</span>
						</p>';
					}
					
				$out .= '</div>
				<p class="form-submit">
					<input type="hidden" name="redirect_to" value="'.wp_unslash( $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'">
					<input type="submit" name="register" value="'.__("Signup","vbegy").'" class="button color '.(isset($a["dark_button"]) && $a["dark_button"] == "dark_button"?"dark_button":"").' small submit">
					<input type="hidden" name="form_type" value="ask-signup">'.
					apply_filters("askme_signup_hidden_form",false).'
				</p>
		</form>'.apply_filters("askme_signup_after_form",false);
	}
	return $out;
}
function askme_signup_process($data = array()) {
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	global $posted;
	$errors = new WP_Error();
	if (isset($data['form_type']) && $data['form_type'] == "ask-signup") :
		// Process signup form
		$posted = array(
			'user_name'    => esc_html($data['user_name']),
			'email'        => esc_html(trim($data['email'])),
			'pass1'        => esc_html($data['pass1']),
			'pass2'        => (isset($data['pass2']) && $data['pass2'] != ""?esc_html($data['pass2']):""),
			'redirect_to'  => esc_url($data['redirect_to']),
			'ask_captcha'  => (isset($data['ask_captcha']) && $data['ask_captcha'] != ""?esc_html($data['ask_captcha']):""),
			'country'      => (isset($data['country']) && $data['country'] != ""?esc_html($data['country']):""),
			'city'         => (isset($data['city']) && $data['city'] != ""?esc_html($data['city']):""),
			'age'          => (isset($data['age']) && $data['age'] != ""?esc_html($data['age']):""),
			'phone'        => (isset($data['phone']) && $data['phone'] != ""?esc_html($data['phone']):""),
			'sex'          => (isset($data['sex']) && $data['sex'] != ""?esc_html($data['sex']):""),
			'nickname'     => (isset($data['nickname']) && $data['nickname'] != ""?esc_html($data['nickname']):""),
			'first_name'   => (isset($data['first_name']) && $data['first_name'] != ""?esc_html($data['first_name']):""),
			'last_name'    => (isset($data['last_name']) && $data['last_name'] != ""?esc_html($data['last_name']):""),
			'display_name' => (isset($data['display_name']) && $data['display_name'] != ""?esc_html($data['display_name']):""),
			'agree_terms'  => (isset($data['agree_terms']) && $data['agree_terms'] != ""?esc_html($data['agree_terms']):""),
		);

		$posted = apply_filters('askme_register_posted',$posted);

		$posted = array_map('stripslashes', $posted);
		$posted['username'] = sanitize_user((isset($posted['username'])?$posted['username']:""));
		// Validation
		$comfirm_password = askme_options("comfirm_password");
		if ( empty($posted['user_name']) ) $errors->add('required-user_name',__("Please enter your name.","vbegy"));
		if ( empty($posted['email']) ) $errors->add('required-email',__("Please enter your email.","vbegy"));
		if ( empty($posted['pass1']) ) $errors->add('required-pass1',__("Please enter your password.","vbegy"));
		if ( $comfirm_password != 1 && empty($posted['pass2']) ) $errors->add('required-pass2',__("Please rewrite password.","vbegy"));
		if ( $comfirm_password != 1 && $posted['pass1']!==$posted['pass2'] ) $errors->add('required-pass1',__("Password does not match.","vbegy"));
		
		$register_items = askme_options("register_items");
		$nickname = (isset($register_items["nickname"]["value"]) && $register_items["nickname"]["value"] == "nickname"?1:0);
		$first_name = (isset($register_items["first_name"]["value"]) && $register_items["first_name"]["value"] == "first_name"?1:0);
		$last_name = (isset($register_items["last_name"]["value"]) && $register_items["last_name"]["value"] == "last_name"?1:0);
		$display_name = (isset($register_items["display_name"]["value"]) && $register_items["display_name"]["value"] == "display_name"?1:0);
		$profile_picture = (isset($register_items["image_profile"]["value"]) && $register_items["image_profile"]["value"] == "image_profile"?1:0);
		$country = (isset($register_items["country"]["value"]) && $register_items["country"]["value"] == "country"?1:0);
		$city = (isset($register_items["city"]["value"]) && $register_items["city"]["value"] == "city"?1:0);
		$phone = (isset($register_items["phone"]["value"]) && $register_items["phone"]["value"] == "phone"?1:0);
		$gender = (isset($register_items["gender"]["value"]) && $register_items["gender"]["value"] == "gender"?1:0);
		$age = (isset($register_items["age"]["value"]) && $register_items["age"]["value"] == "age"?1:0);
		$allow_duplicate_names = askme_options("allow_duplicate_names");
		$allow_nickname = (isset($allow_duplicate_names["nickname"]["value"]) && $allow_duplicate_names["nickname"]["value"] == "nickname"?1:0);
		$allow_display_name = (isset($allow_duplicate_names["display_name"]["value"]) && $allow_duplicate_names["display_name"]["value"] == "display_name"?1:0);

		$first_name_required = askme_options("first_name_required_register");
		$last_name_required = askme_options("last_name_required_register");
		$display_name_required = askme_options("display_name_required_register");
		$country_required_register = askme_options("country_required_register");
		$city_required_register = askme_options("city_required_register");
		$age_required_register = askme_options("age_required_register");
		$phone_required_register = askme_options("phone_required_register");
		$gender_required_register = askme_options("gender_required_register");
		$profile_picture_required_register = askme_options("profile_picture_required_register");
		
		if (!isset($data["mobile"])) {
			askme_check_captcha(askme_options("the_captcha_register"),"register",$posted,$errors);
		}

		$your_avatar_meta = askme_avatar_name();
		
		if (isset($_FILES[$your_avatar_meta]) && !empty($_FILES[$your_avatar_meta]['name'])) :
			$mime = $_FILES[$your_avatar_meta]["type"];
			if (!isset($data['mobile']) && $mime != 'image/jpeg' && $mime != 'image/jpg' && $mime != 'image/png') {
				$errors->add('upload-error', esc_html__('Error type, Please upload: jpg,jpeg,png','vbegy'));
				if ($errors->get_error_code()) return $errors;
			}else {
				require_once(ABSPATH . "wp-admin" . '/includes/file.php');
				require_once(ABSPATH . "wp-admin" . '/includes/image.php');
				$your_avatar = wp_handle_upload($_FILES[$your_avatar_meta],array('test_form' => false),current_time('mysql'));
				if ( isset($your_avatar['error']) ) :
					$errors->add('upload-error',  __('Error in upload the image : ','vbegy') . $your_avatar['error'] );
					return $errors;
				endif;
			}
		else:
			if ($profile_picture === 1 && $profile_picture_required_register == 1) {
				$errors->add('required-profile_picture', __("There are required fields ( Profile Picture ).","vbegy"));
			}
		endif;
		if (isset($your_avatar['error']) && isset($your_avatar)) :
			if (isset($errors->add)) {
				$errors->add('upload-error', esc_html__('Error in upload the image : ','vbegy') . $your_avatar['error']);
				if ($errors->get_error_code()) return $errors;
			}
			return $errors;
		endif;
		
		if ($nickname === 1 && empty($posted['nickname'])) {
			$errors->add('required-nickname', __("There are required fields ( Nickname ).","vbegy"));
		}
		if ($allow_nickname !== 1 && isset($posted['nickname']) && $posted['nickname'] != "") {
			global $wpdb;
			$check_nickname = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users as users, $wpdb->usermeta as meta WHERE users.ID = meta.user_id AND meta.meta_key = 'nickname' AND meta.meta_value = %s AND users.ID <> %d",$posted['nickname'],$user_id));
			if ($check_nickname > 0) {
				$errors->add('required-field','<strong>'.esc_html__("Error","vbegy").':&nbsp;</strong> '.esc_html__("This nickname is already available.","vbegy"));
			}
		}
		if ($first_name === 1 && $first_name_required == 1 && empty($posted['first_name'])) {
			$errors->add('required-first_name', __("There are required fields ( First Name ).","vbegy"));
		}
		if ($last_name === 1 && $last_name_required == 1 && empty($posted['last_name'])) {
			$errors->add('required-last_name', __("There are required fields ( Last Name ).","vbegy"));
		}
		if ($display_name === 1 && $display_name_required == 1 && empty($posted['display_name'])) {
			$errors->add('required-display_name', __("There are required fields ( Display Name ).","vbegy"));
		}
		if ($allow_display_name !== 1 && isset($posted['display_name']) && $posted['display_name'] != "") {
			global $wpdb;
			$check_display_name = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->users WHERE display_name = %s AND ID <> %d",$posted['display_name'],$user_id));
			if ($check_display_name > 0) {
				$errors->add('required-field','<strong>'.esc_html__("Error","vbegy").':&nbsp;</strong> '.esc_html__("This display name is already available.","vbegy"));
			}
		}
		if ($country === 1 && $country_required_register == 1 && empty($posted['country'])) {
			$errors->add('required-country', __("There are required fields ( Country ).","vbegy"));
		}
		if ($city === 1 && $city_required_register == 1 && empty($posted['city'])) {
			$errors->add('required-city', __("There are required fields ( City ).","vbegy"));
		}
		if ($age === 1 && $age_required_register == 1 && empty($posted['age'])) {
			$errors->add('required-age', __("There are required fields ( Age ).","vbegy"));
		}
		if ($phone === 1 && $phone_required_register == 1 && empty($posted['phone'])) {
			$errors->add('required-phone', __("There are required fields ( Phone ).","vbegy"));
		}
		if ($gender === 1 && $gender_required_register == 1 && empty($posted['sex'])) {
			$errors->add('required-sex', __("There are required fields ( Gender ).","vbegy"));
		}
		
		$terms_active_register = askme_options("terms_active_register");
		if ($terms_active_register == 1 && $posted['agree_terms'] != 1) {
			$errors->add('required-terms', __("There are required fields ( Agree of the terms ).","vbegy"));
		}
		// Check the username
		if ( username_exists( $posted['user_name'] ) ) :
			$errors->add('required-user_name',__("This account is already registered.","vbegy"));
		endif;
		// Check the e-mail address
		if ( !is_email( $posted['email'] ) ) :
			$errors->add('required-email',__("Please write correctly email.","vbegy"));
		elseif ( email_exists( $posted['email'] ) ) :
			$errors->add('required-email',__("This email is already registered, please choose another one.","vbegy"));
		endif;
		if ( $errors->get_error_code() ) return $errors;
		if ( !$errors->get_error_code() ) :
			do_action('register_post', $posted['user_name'], $posted['email'], $errors);
			$errors = apply_filters( 'registration_errors', $errors, $posted['user_name'], $posted['email'] );
			do_action('askme_register_errors',$errors,$posted);
			// if there are no errors, let's create the user account
			if ( !$errors->get_error_code() ) :
				$user_id = wp_create_user( $posted['user_name'], $posted['pass1'], $posted['email'] );
				if (is_wp_error($user_id)) {
					$errors->add('error', sprintf(__('<strong>Error</strong>: Sorry, You can not register, Please contact the webmaster','vbegy').': ',get_option('admin_email')));
					if ( $errors->get_error_code() ) {return $errors;}
				}else {
					update_user_meta($user_id,"the_best_answer",0);
					if (isset($your_avatar) && isset($your_avatar["url"])) :
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
					do_action("askme_before_register",$user_id,$posted);
					do_action("askme_after_register",$user_id,$posted,isset($_FILES)?$_FILES:array(),"register");

					if (isset($data["mobile"])) {
						return $user_id;
					}else {
						$secure_cookie = is_ssl() ? true : false;
						wp_set_auth_cookie($user_id, true, $secure_cookie);
						
						$after_register = askme_options("after_register");
						$after_register_link = askme_options("after_register_link");
						
						if (isset($posted['redirect_to']) && $after_register == "same_page") {
							$redirect_to = esc_url_raw($posted['redirect_to']);
						}else if (isset($user_id) && $user_id > 0 && $after_register == "profile") {
							$redirect_to = vpanel_get_user_url($user_id);
						}else if ($after_register == "custom_link") {
							$redirect_to = esc_url($after_register_link);
						}else {
							$redirect_to = esc_url(home_url('/'));
						}
						wp_safe_redirect($redirect_to);
						exit;
					}
				}
			endif;
		endif;
	endif;
	if (!isset($data["mobile"])) {
		return;
	}
}
/* Default group */
function askme_default_group() {
	$activate_review_users = apply_filters("askme_activate_review_users",false);
	$user_review = askme_options("user_review");
	$confirm_email = askme_options("confirm_email");
	$default_group = askme_options("default_group");
	$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
	$default_group = ($activate_review_users == true || $user_review == 1?"ask_under_review":$default_group);
	$default_group = apply_filters("askme_register_default_group",$default_group);
}
/* Before registration */
add_action("askme_before_register","askme_before_register",1,2);
function askme_before_register($user_id,$posted) {
	$activate_review_users = apply_filters("askme_activate_review_users",false);
	$user_review = askme_options("user_review");
	$confirm_email = askme_options("confirm_email");
	if ($confirm_email == 1 && $user_review != 1) {
		$default_group = "activation";
		$rand_a = askme_token(15);
		update_user_meta($user_id,"activation",$rand_a);
		$user_data = get_user_by("id",$user_id);

		$confirm_link = esc_url_raw(add_query_arg(array("u" => $user_id,"activate" => $rand_a),esc_url(home_url('/'))));
		$send_text = askme_send_mail(
			array(
				'content'            => askme_options("email_confirm_link_2"),
				'user_id'            => $user_id,
				'confirm_link_email' => $confirm_link,
			)
		);
		$email_title = askme_options("title_confirm_link_2");
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
		askme_send_mails(
			array(
				'toEmail'     => esc_html($posted['email']),
				'toEmailName' => esc_html($posted['user_name']),
				'title'       => $email_title,
				'message'     => $send_text,
			)
		);
	}else {
		$default_group = askme_options("default_group");
		$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
		$default_group = ($activate_review_users == true || $user_review == 1?"ask_under_review":$default_group);
		$default_group = apply_filters("askme_register_default_group",$default_group);
	}
	$nickname = ($posted['nickname'] != ""?$posted['nickname']:$posted['user_name']);
	$display_name = ($posted['display_name'] != ""?$posted['display_name']:$posted['user_name']);
	wp_update_user(array('ID' => $user_id,'role' => ($confirm_email == 1?'activation':$default_group),'user_nicename' => $nickname,'nickname' => $nickname,'display_name' => $display_name));
	$array_posts = array("first_name","last_name","country","city","phone","sex","age");
	foreach ($array_posts as $key => $value) {
		if (isset($posted[$value]) && $posted[$value] != "") {
			update_user_meta($user_id,$value,sanitize_text_field($posted[$value]));
		}
	}
}
/* After registration */
add_action('user_register','askme_user_registration',1,1);
function askme_user_registration($user_id) {
	$activate_review_users = apply_filters("askme_activate_review_users",false);
	$user_review = askme_options("user_review");
	$confirm_email = askme_options("confirm_email");
	if ($activate_review_users == true || ($user_review == 1 && $confirm_email != 1) || ($user_review == 1 && $confirm_email == 1)) {
		$ask_under_review = get_role("ask_under_review");
		if (!isset($ask_under_review)) {
			add_role("ask_under_review",esc_html__("Under review","vbegy"),array('read' => false));
		}
	}
	if ($confirm_email == 1) {
		$default_group = 'activation';
		$activation = get_role("activation");
		if (!isset($activation)) {
			add_role("activation",esc_html__("Activation","vbegy"),array('read' => false));
		}
	}else {
		$default_group = askme_options("default_group");
		$default_group = (isset($default_group) && $default_group != ""?$default_group:"subscriber");
		$default_group = ($activate_review_users == true || $user_review == 1?"ask_under_review":$default_group);
		$default_group = apply_filters("askme_register_default_group",$default_group);
	}
	if ($default_group == "ask_under_review" || $default_group == "activation") {
		update_user_meta($user_id,"askme_default_group",$default_group);
	}
}
/* After registration */
add_action('user_register','askme_after_registration',2,1);
add_action('askme_after_registration','askme_after_registration',1,1);
function askme_after_registration($user_id) {
	$register_default_options = askme_options("register_default_options");
	$askme_default_group = get_user_meta($user_id,"askme_default_group",true);
	if ($askme_default_group == "ask_under_review" || $askme_default_group == "activation") {
		// Not activated or under review
	}else {
		do_action("askme_register_welcome",$user_id);
	}
}
/* Welcome register */
add_action("nsl_register_new_user","askme_register_welcome");
add_action("oa_social_login_action_before_user_login","askme_register_welcome");
add_action("mo_user_register","askme_register_welcome");
add_action("wsl_hook_process_login_after_wp_insert_user","askme_register_welcome");
add_action("APSL_createUser","askme_register_welcome");
add_action("askme_register_welcome","askme_register_welcome");
function askme_register_welcome($user_id) {
	$user_id = (is_object($user_id)?$user_id->ID:$user_id);
	askme_send_welcome_mail($user_id);
	$active_points = askme_options("active_points");
	askme_add_gift_points($user_id,$active_points);
}
/* Send welcome mail */
function askme_send_welcome_mail($user_id) {
	$send_welcome_mail = askme_options("send_welcome_mail");
	if ($send_welcome_mail == 1) {
		$welcome_mail = get_user_meta($user_id,"welcome_mail",true);
		if ($welcome_mail == "") {
			$send_text = askme_send_mail(
				array(
					'content' => askme_options("email_welcome_mail"),
					'user_id' => $user_id,
				)
			);
			$email_title = askme_options("title_welcome_mail");
			$email_title = ($email_title != ""?$email_title:esc_html__("Welcome","vbegy"));
			$email_title = askme_send_mail(
				array(
					'content' => $email_title,
					'title'   => true,
					'break'   => '',
					'user_id' => $user_id,
				)
			);
			$user_email = get_the_author_meta("user_email",$user_id);
			$display_name = get_the_author_meta("display_name",$user_id);
			if ($user_email != "") {
				askme_send_mails(
					array(
						'toEmail'     => esc_html($user_email),
						'toEmailName' => esc_html($display_name),
						'title'       => $email_title,
						'message'     => $send_text,
					)
				);
			}
			update_user_meta($user_id,"welcome_mail","done");
		}
	}
}
/* Add gift points */
function askme_add_gift_points($user_id,$active_points) {
	$point_new_user = (int)askme_options("point_new_user");
	if ($user_id > 0 && $active_points == 1 && $point_new_user > 0) {
		$gift_site = get_user_meta($user_id,"gift_site",true);
		if ($gift_site == "") {
			$current_user = get_user_by("id",$user_id);
			$_points = get_user_meta($user_id,$current_user->user_login."_points",true);
			$_points++;
		
			update_user_meta($user_id,$current_user->user_login."_points",$_points);
			add_user_meta($user_id,$current_user->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$point_new_user,"+","gift_site"));
		
			$points_user = (int)get_user_meta($user_id,"points",true);
			update_user_meta($user_id,"points",($points_user+$point_new_user));
			
			askme_notifications_activities($user_id,"","","","","gift_site","notifications");
			update_user_meta($user_id,"gift_site","done");
		}
	}
}
function ask_signup($data = array()) {
	$data = (is_array($data) && !empty($data)?$data:$_POST);
	if (isset($data['form_type']) && $data['form_type'] == "ask-signup") :
		$return = askme_signup_process($data);
		if (is_wp_error($return) ) :
			echo '<div class="ask_error"><strong><p>'.__("Error","vbegy").':&nbsp;</strong>'.wptexturize(str_replace('<strong>'.__("Error","vbegy").'</strong>: ', '', $return->get_error_message())).'</p></div>';
   		endif;
	endif;
}
add_action('ask_signup', 'ask_signup');?>