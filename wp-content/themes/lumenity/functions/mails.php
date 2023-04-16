<?php /* Send mails */
if (!function_exists('askme_send_mails')) :
	function askme_send_mails($args = array()) {
		$defaults = array(
			'toEmail'       => '',
			'toEmailName'   => '',
			'title'         => '',
			'message'       => '',
			'email_code'    => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$toEmail       = $args['toEmail'];
		$toEmailName   = $args['toEmailName'];
		$title         = $args['title'];
		$message       = $args['message'];
		$email_code    = $args['email_code'];

		$toEmail = ($toEmail != ""?$toEmail:askme_options("email_template_to"));
		$toEmail = ($toEmail != ""?$toEmail:get_bloginfo("admin_email"));
		$toEmailName = ($toEmailName != ""?$toEmailName:get_bloginfo('name'));
		
		$toEmail = apply_filters("askme_sendemail_to",$toEmail);
		$toEmailName = apply_filters("askme_sendemail_toname",$toEmailName);
		if ($email_code == "") {
			$message = askme_email_code($message);
		}
		add_filter('wp_mail_content_type','askme_set_content_type');
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail($toEmail,htmlspecialchars_decode($title),$message,$headers);
	}
endif;
if (!function_exists('askme_set_content_type')) :
	function askme_set_content_type(){
		return "text/html";
	}
endif;
/* PHPMailer action */
add_action('phpmailer_init','askme_wp_phpmailer');
if (!function_exists('askme_wp_phpmailer')) :
	function askme_wp_phpmailer($phpmailer) {
		$mail_smtp = askme_options("mail_smtp");
		if ($mail_smtp == 1) {
			$email_template = askme_options("email_template");
			$mail_host = askme_options("mail_host");
			$mail_username = askme_options("mail_username");
			$mail_password = askme_options("mail_password");
			$mail_secure = askme_options("mail_secure");
			$mail_port = askme_options("mail_port");
			$disable_ssl = askme_options("disable_ssl");
			$smtp_auth = askme_options("smtp_auth");

		    $phpmailer->isSMTP();     
		    $phpmailer->Host = $mail_host;
		    $phpmailer->SMTPAuth = ($smtp_auth == 1?true:false);
		    $phpmailer->Port = $mail_port;
		    $phpmailer->SMTPSecure = $mail_secure;
		    $phpmailer->Username = $mail_username;
		    $phpmailer->Password = $mail_password;
		    $phpmailer->Sender = $email_template;
		    $phpmailer->From = $email_template;
		}
		$bloginfo_name = get_bloginfo('name');
		$custom_mail_name = askme_options('custom_mail_name');
		$mail_name = askme_options('mail_name');
		$mail_name = ($custom_mail_name == 1 && $mail_name != ""?$mail_name:$bloginfo_name);
		$phpmailer->FromName = $mail_name;
		$mail_issue_fixed = get_option("askme_mail_issue_fixed");
		if ($mail_issue_fixed != "done") {
			$setting_options = get_option(askme_options);
			if ((isset($setting_options['email_template']) && $setting_options['email_template'] != "") || (isset($setting_options['email_template_to']) && $setting_options['email_template_to'] != "")) {
				$parse = parse_url(get_site_url());
				$whitelist = array(
					'127.0.0.1',
					'::1'
				);
				if (in_array($_SERVER['REMOTE_ADDR'],$whitelist) || $parse['host'] == "intself.com") {
					$not_replace = true;
				}
				
				if (isset($setting_options['email_template']) && $setting_options['email_template'] != "" && !isset($not_replace)) {
					if (strpos($setting_options['email_template'],'@intself.com') !== false) {
						$setting_options['email_template'] = "no_reply@".$parse['host'];
						$change_it = true;
					}
				}
				if (isset($setting_options['email_template_to']) && $setting_options['email_template_to'] != "" && !isset($not_replace)) {
					if (strpos($setting_options['email_template_to'],'@intself.com') !== false || strpos($setting_options['email_template_to'],'Intselfthemes@') !== false || strpos($setting_options['email_template_to'],'vbegy.info@') !== false) {
						$setting_options['email_template_to'] = get_bloginfo("admin_email");
						$change_it = true;
					}
				}
				if (isset($change_it)) {
					update_option(askme_options,$setting_options);
				}
			}
			update_option("askme_mail_issue_fixed","done");
		}
		return $phpmailer;
	}
endif;
if (!function_exists('askme_set_content_type')) :
	function askme_set_content_type(){
		return "text/html";
	}
endif;
/* Send mail template */
if (!function_exists('askme_send_mail')) :
	function askme_send_mail($args = array()) {
		$defaults = array(
			'content'            => '',
			'title'              => '',
			'break'              => 'break',
			'user_id'            => 0,
			'post_id'            => 0,
			'comment_id'         => 0,
			'reset_password'     => '',
			'confirm_link_email' => '',
			'item_price'         => '',
			'item_name'          => '',
			'item_currency'      => '',
			'payer_email'        => '',
			'first_name'         => '',
			'last_name'          => '',
			'item_transaction'   => '',
			'date'               => '',
			'time'               => '',
			'category'           => '',
			'custom'             => '',
			'sender_user_id'     => '',
			'received_user_id'   => 0,
			'invitation_link'    => '',
			'request'            => '',
		);
		
		$args = wp_parse_args($args,$defaults);
		
		$content            = $args['content'];
		$title              = $args['title'];
		$break              = $args['break'];
		$user_id            = $args['user_id'];
		$post_id            = $args['post_id'];
		$comment_id         = $args['comment_id'];
		$reset_password     = $args['reset_password'];
		$confirm_link_email = $args['confirm_link_email'];
		$item_price         = $args['item_price'];
		$item_name          = $args['item_name'];
		$item_currency      = $args['item_currency'];
		$payer_email        = $args['payer_email'];
		$first_name         = $args['first_name'];
		$last_name          = $args['last_name'];
		$item_transaction   = $args['item_transaction'];
		$date               = $args['date'];
		$time               = $args['time'];
		$category           = $args['category'];
		$custom             = $args['custom'];
		$sender_user_id     = $args['sender_user_id'];
		$received_user_id   = $args['received_user_id'];
		$invitation_link    = $args['invitation_link'];
		$request            = $args['request'];

		$content = str_ireplace('[%blogname%]', '<span class="mail-class-blogname">'.get_bloginfo('name').'</span>', $content);
		$content = str_ireplace('[%site_url%]', esc_url(home_url('/')), $content);
		
		if ($user_id > 0) {
			$user = new WP_User($user_id);
			$content = str_ireplace('[%messages_url%]' , esc_url(get_page_link(askme_options('messages_page'))), $content);
			$content = str_ireplace('[%user_login%]'   , '<span class="mail-class-user_login">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_name%]'    , '<span class="mail-class-user_name">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_nicename%]', '<span class="mail-class-user_nicename">'.ucfirst($user->user_nicename).'</span>', $content);
			$content = str_ireplace('[%display_name%]' , '<span class="mail-class-display_name">'.ucfirst($user->display_name).'</span>', $content);
			$content = str_ireplace('[%user_email%]'   , '<span class="mail-class-user_email">'.$user->user_email.'</span>', $content);
			$content = str_ireplace('[%user_profile%]' , vpanel_get_user_url($user->ID), $content);
			$content = str_ireplace('[%users_link%]'   , admin_url("users.php?role=ask_under_review"), $content);
		}
		
		if ($sender_user_id == "anonymous") {
			$content = str_ireplace('[%user_login_sender%]'   , '<span class="mail-class-user_login_sender">'.esc_html__("Anonymous","vbegy").'</span>', $content);
			$content = str_ireplace('[%user_name_sender%]'    , '<span class="mail-class-user_name_sender">'.esc_html__("Anonymous","vbegy").'</span>', $content);
			$content = str_ireplace('[%user_nicename_sender%]', '<span class="mail-class-user_nicename_sender">'.esc_html__("Anonymous","vbegy").'</span>', $content);
			$content = str_ireplace('[%display_name_sender%]' , '<span class="mail-class-display_name_sender">'.esc_html__("Anonymous","vbegy").'</span>', $content);
			$content = str_ireplace('[%user_email_sender%]'   , '<span class="mail-class-user_email_sender">'.esc_html__("Anonymous","vbegy").'</span>', $content);
			$content = str_ireplace('[%user_profile_sender%]' , esc_url(home_url('/')), $content);
		}else if (is_numeric($sender_user_id) && $sender_user_id > 0) {
			$user = new WP_User($sender_user_id);
			$content = str_ireplace('[%user_login_sender%]'   , '<span class="mail-class-user_login_sender">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_name_sender%]'    , '<span class="mail-class-user_name_sender">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_nicename_sender%]', '<span class="mail-class-user_nicename_sender">'.ucfirst($user->user_nicename).'</span>', $content);
			$content = str_ireplace('[%display_name_sender%]' , '<span class="mail-class-display_name_sender">'.ucfirst($user->display_name).'</span>', $content);
			$content = str_ireplace('[%user_email_sender%]'   , '<span class="mail-class-user_email_sender">'.$user->user_email.'</span>', $content);
			$content = str_ireplace('[%user_profile_sender%]' , vpanel_get_user_url($user->ID), $content);
		}else {
			if (is_object($sender_user_id)) {
				$content = str_ireplace('[%user_login_sender%]'   , '<span class="mail-class-user_login_sender">'.$sender_user_id->comment_author.'</span>', $content);
				$content = str_ireplace('[%user_name_sender%]'    , '<span class="mail-class-user_name_sender">'.$sender_user_id->comment_author.'</span>', $content);
				$content = str_ireplace('[%user_nicename_sender%]', '<span class="mail-class-user_nicename_sender">'.ucfirst($sender_user_id->comment_author).'</span>', $content);
				$content = str_ireplace('[%display_name_sender%]' , '<span class="mail-class-display_name_sender">'.ucfirst($sender_user_id->comment_author).'</span>', $content);
				$content = str_ireplace('[%user_email_sender%]'   , '<span class="mail-class-user_email_sender">'.$sender_user_id->comment_author_email.'</span>', $content);
				$content = str_ireplace('[%user_profile_sender%]' , esc_url(($sender_user_id->comment_author_url != ''?$sender_user_id->comment_author_url:home_url('/'))), $content);
			}
		}
		
		if ($received_user_id > 0) {
			$user = new WP_User($received_user_id);
			$content = str_ireplace('[%user_login%]'   , '<span class="mail-class-user_login">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_name%]'    , '<span class="mail-class-user_name">'.$user->user_login.'</span>', $content);
			$content = str_ireplace('[%user_nicename%]', '<span class="mail-class-user_nicename">'.ucfirst($user->user_nicename).'</span>', $content);
			$content = str_ireplace('[%display_name%]' , '<span class="mail-class-display_name">'.ucfirst($user->display_name).'</span>', $content);
			$content = str_ireplace('[%user_email%]'   , '<span class="mail-class-user_email">'.$user->user_email.'</span>', $content);
			$content = str_ireplace('[%user_profile%]' , vpanel_get_user_url($user->ID), $content);
		}
		
		if ($reset_password != '') {
			$content = str_ireplace('[%reset_password%]', $reset_password, $content);
		}
		if ($confirm_link_email != '') {
			$content = str_ireplace('[%confirm_link_email%]', $confirm_link_email, $content);
		}
		
		if ($comment_id > 0) {
			$get_comment = get_comment($comment_id);
			$content = str_ireplace('[%comment_link%]', admin_url("edit-comments.php?comment_status=moderated"), $content);
			$content = str_ireplace('[%answer_link%]' , get_permalink($post_id).'#li-comment-'.$comment_id, $content);
			$content = str_ireplace('[%answer_url%]'  , get_permalink($post_id).'#li-comment-'.$comment_id, $content);
			$content = str_ireplace('[%comment_url%]' , get_permalink($post_id).'#li-comment-'.$comment_id, $content);
			$content = str_ireplace('[%the_name%]'    , '<span class="mail-class-the_name">'.$get_comment->comment_author.'</span>', $content);
		}
		
		if ($post_id > 0) {
			$post = get_post($post_id);
			$content = str_ireplace('[%messages_title%]', '<span class="mail-class-messages_title">'.$post->post_title.'</span>', $content);
			$content = str_ireplace('[%question_title%]', '<span class="mail-class-question_title">'.$post->post_title.'</span>', $content);
			$content = str_ireplace('[%post_title%]'    , '<span class="mail-class-post_title">'.$post->post_title.'</span>', $content);
			$content = str_ireplace('[%question_link%]' , ($post->post_status == 'publish'?get_permalink($post_id):admin_url('post.php?post='.$post_id.'&action=edit')), $content);
			$content = str_ireplace('[%post_link%]'     , ($post->post_status == 'publish'?get_permalink($post_id):admin_url('post.php?post='.$post_id.'&action=edit')), $content);
			if ($post->post_author > 0) {
				$get_the_author = get_user_by("id",$post->post_author);
				$the_author_post = $get_the_author->display_name;
			}else {
				$the_author_post = get_post_meta($post_id,($post->post_type == ask_questions_type || $post->post_type == ask_asked_questions_type?'question_username':'post_username'),true);
				$the_author_post = ($the_author_post != ''?$the_author_post:esc_html__("Anonymous","vbegy"));
			}
			$content = str_ireplace('[%the_author_question%]', '<span class="mail-class-the_author_question">'.$the_author_post.'</span>', $content);
			$content = str_ireplace('[%the_author_post%]'    , '<span class="mail-class-the_author_post">'.$the_author_post.'</span>', $content);
		}
		
		if ($item_price != '') {
			$content = str_ireplace('[%item_price%]', '<span class="mail-class-item_price">'.$item_price.'</span>', $content);
		}
		if ($item_name != '') {
			$content = str_ireplace('[%item_name%]', '<span class="mail-class-item_name">'.$item_name.'</span>', $content);
		}
		if ($item_currency != '') {
			$content = str_ireplace('[%item_currency%]', '<span class="mail-class-item_currency">'.$item_currency.'</span>', $content);
		}
		if ($payer_email != '') {
			$content = str_ireplace('[%payer_email%]', '<span class="mail-class-payer_email">'.$payer_email.'</span>', $content);
		}
		if ($first_name != '') {
			$content = str_ireplace('[%first_name%]', '<span class="mail-class-first_name">'.$first_name.'</span>', $content);
		}else if (isset($user) && isset($user->display_name)) {
			$content = str_ireplace('[%first_name%]', '<span class="mail-class-first_name">'.ucfirst($user->display_name).'</span>', $content);
		}else {
			$content = str_ireplace('[%first_name%]', '', $content);
		}
		if ($last_name != '') {
			$content = str_ireplace('[%last_name%]', '<span class="mail-class-last_name">'.$last_name.'</span>', $content);
		}else {
			$content = str_ireplace('[%last_name%]', '', $content);
		}
		if ($item_transaction != '') {
			$content = str_ireplace('[%item_transaction%]', '<span class="mail-class-item_transaction">'.$item_transaction.'</span>', $content);
		}
		if ($date != '') {
			$content = str_ireplace('[%date%]', '<span class="mail-class-date">'.$date.'</span>', $content);
		}
		if ($time != '') {
			$content = str_ireplace('[%time%]', '<span class="mail-class-time">'.$time.'</span>', $content);
		}
		if ($category != '') {
			$content = str_ireplace('[%category_link%]', admin_url('admin.php?page=askme_new_categories'), $content);
			$content = str_ireplace('[%category_name%]', '<span class="mail-class-category_name">'.$category.'</span>', $content);
		}
		if ($request != '') {
			$content = str_ireplace('[%request_link%]', admin_url('edit.php?post_type=request'), $content);
			$content = str_ireplace('[%request_name%]', '<span class="mail-class-request_name">'.$request.'</span>', $content);
		}
		if ($invitation_link != '') {
			$content = str_ireplace('[%invitation_link%]', '<span class="mail-class-invitation_link">'.$invitation_link.'</span>', $content);
		}
		if ($custom != '') {
			$custom_content = apply_filters('vbegy_filter_send_email',false);
			$content = str_ireplace('[%custom_link%]', $custom_content, $content);
			$content = str_ireplace('[%custom_name%]', '<span class="mail-class-custom_name">'.$custom.'</span>', $content);
		}
		$break = apply_filters("vbegy_email_template_break",$break);
		if ($break == "break") {
			$return = nl2br(askme_kses_stip($content,"yes"));
		}else {
			if ($title == true) {
				$return = strip_tags(stripslashes($content));
			}else {
				$return = stripslashes($content);
			}
		}
		return $return;
	}
endif;
/* Emails */
if (!function_exists('askme_email_code')) :
	function askme_email_code($content,$mail = "",$user_id = "") {
		$active_footer_email = askme_options("active_footer_email");
		$social_footer_email = askme_options("social_footer_email");
		$copyrights_for_email = askme_options("copyrights_for_email");
		$logo_email_template = askme_image_url_id(askme_options("logo_email_template"));
		$custom_image_mail = askme_image_url_id(askme_options("custom_image_mail"));
		$background_email = askme_options("background_email");
		$background_email = ($background_email != ""?$background_email:"#272930");
		$email_style = askme_options("email_style");
		$social_td = '';
		if ($social_footer_email == 1) {
			$sort_social = askme_options("sort_social");
			$social = array(
				array("name" => "Facebook",   "value" => "facebook",   "icon" => "facebook"),
				array("name" => "Twitter",    "value" => "twitter",    "icon" => "twitter"),
				array("name" => "tiktok",     "value" => "tiktok",     "icon" => "tiktok"),
				array("name" => "Linkedin",   "value" => "linkedin",   "icon" => "linkedin"),
				array("name" => "Dribbble",   "value" => "dribbble",   "icon" => "dribbble"),
				array("name" => "Youtube",    "value" => "youtube",    "icon" => "play"),
				array("name" => "Vimeo",      "value" => "vimeo",      "icon" => "vimeo"),
				array("name" => "Skype",      "value" => "skype",      "icon" => "skype"),
				array("name" => "WhatsApp",   "value" => "whatsapp",   "icon" => "whatsapp"),
				array("name" => "Flickr",     "value" => "flickr",     "icon" => "flickr"),
				array("name" => "Soundcloud", "value" => "soundcloud", "icon" => "soundcloud"),
				array("name" => "Instagram",  "value" => "instagram",  "icon" => "instagrem"),
				array("name" => "Pinterest",  "value" => "pinterest",  "icon" => "pinterest")
			);
			if (is_array($sort_social) && !empty($sort_social)) {
				$k = 0;
				foreach ($sort_social as $key_r => $value_r) {$k++;
					if (isset($sort_social[$key_r]["value"])) {
						$sort_social_value = $sort_social[$key_r]["value"];
						$social_icon_h = askme_options($sort_social_value."_icon_h");
						if ($sort_social_value != "rss" && $social_icon_h != "") {
							$social_url = ($sort_social_value == "skype"?"skype:":"").($sort_social_value == "whatsapp"?"whatsapp://send?abid=":"").($sort_social_value != "skype" && $sort_social_value != "whatsapp"?esc_url($social_icon_h):$social_icon_h).($sort_social_value == "skype"?"?call":"").($sort_social_value == "whatsapp"?"&text=".esc_html__("Hello","vbegy"):"");
							if ($email_style == "style_2") {
								$social_td .= '<a href="'.$social_url.'" title="'.$value_r["name"].'" style="color:#707478; margin-right:10px;font-size:14px;font-weight:400;">'.$value_r["name"].'</a>';
							}else {
								$social_td .= '<a href="'.$social_url.'" title="'.$value_r["name"].'" style="color:#707478; margin-right:10px;font-size:14px;font-weight:400;"><img alt="'.$value_r["name"].'" width="32" height="32" src="'.get_template_directory_uri().'/images/social/'.$value_r["value"].'.png" style="line-height:100%;outline:none;text-decoration:none;border:none"></a>';
							}
						}
					}
				}
			}
		}

		$primary_color = askme_options("primary_color");
		if ($primary_color != "") {
			$skin = $primary_color;
		}else {
			$skins = array("skins" => "#ff7361","blue" => "#3498db","gray" => "#8a8a8a","green" => "#1bbc9b","moderate_cyan" => "#38cbcb","orange" => "#fdb655","purple" => "#8e74b2","red" => "#ef3852","strong_cyan" => "#27bebe","yellow" => "#BAA56A");
			$site_skin = askme_options('site_skin');
			if ($site_skin == "site_skin" || $site_skin == "default" || $site_skin == "") {
				$skin = $skins["skins"];
			}else {
				$skin = $skins[$site_skin];
			}
		}

		$is_rtl = is_rtl();
		
		return '<!doctype html>
		<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
				<title></title>
				<!--[if !mso]><!-- -->
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<!--<![endif]-->
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
				<meta name="viewport" content="width=device-width, initial-scale=1">
				<style type="text/css">
					#outlook a {
						padding: 0;
					}
					body {
						margin: 0;
						padding: 0;
						-webkit-text-size-adjust: 100%;
						-ms-text-size-adjust: 100%;
					}
					table,td {
						border-collapse: collapse;
						mso-table-lspace: 0pt;
						mso-table-rspace: 0pt;
					}
					img {
						border: 0;
						height: auto;
						line-height: 100%;
						outline: none;
						text-decoration: none;
						-ms-interpolation-mode: bicubic;
					}
					p {
						display: block;
						margin: 13px 0;
						line-height: 24px;
					}
					a.hover:hover {
						color: '.$skin.' !important;
					}
					/* Ar-Style */
					.rtl-css {
						text-align: right !important;
					}
				</style>
				<!--[if mso]>
				<xml>
					<o:OfficeDocumentSettings>
					<o:AllowPNG/>
					<o:PixelsPerInch>96</o:PixelsPerInch>
					</o:OfficeDocumentSettings>
				</xml>
				<![endif]-->
				<!--[if lte mso 11]>
				<style type="text/css">
					.mj-outlook-group-fix {
						width:100% !important;
					}
				</style>
				<![endif]-->
				<!--[if !mso]><!-->
				<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet" type="text/css">
				<style type="text/css">
					@import url(https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap);
				</style>
				<!--<![endif]-->
				<style type="text/css">
				@media only screen and (min-width:480px) {
					.mj-column-per-100 {
						width: 100% !important;
						max-width: 100%;
					}
				}
				</style>
				<style type="text/css">
				@media only screen and (max-width:480px) {
					table.mj-full-width-mobile {
						width: 100% !important;
					}
					td.mj-full-width-mobile {
						width: auto !important;
					}
					.wrapper {
						margin: 0 10px 0 10px !important;
					}
					p {
						line-height: 26px !important;
					}
				}
				</style>
			</head>
			<body style="background-color:#eeeeee;">
				<div style="background-color:#eeeeee;">
					<!--[if mso | IE]>
					<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
						<tr>
							<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
								<![endif]-->
								<div style="margin:0px auto;max-width:600px;">
									<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
										<tbody>
											<tr>
												<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:20px 0;text-align:center;">
													<!--[if mso | IE]>
													<table role="presentation" border="0" cellpadding="0" cellspacing="0">
														'.($email_style == "style_2"?'<tr>
															<td width="600px">
																<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
																	<tr>
																		<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
																			<![endif]-->
																			<div style="margin:0px auto;border-radius:12px 12px 0 0;max-width:600px;">
																				<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;border-radius:12px 12px 0 0;">
																					<tbody>
																						<tr>
																							<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:0 0 0 0;text-align:center;">
																								<!--[if mso | IE]>
																								<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																									<tr>
																										<td style="vertical-align:top;width:600px;">
																											<![endif]-->
																											<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;">
																												<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																													<tbody>
																														<tr>
																															<td style="vertical-align:top;padding:0 0 30px 0;">
																																<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																	<tr>
																																		<td align="center" style="font-size:0px;padding:10px 25px;word-break:break-word;">
																																			<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
																																				<tbody>
																																					<tr>
																																						<td style="width:140px;">
																																							<a href="'.esc_url(home_url('/')).'" target="_blank">'.($logo_email_template != ''?'<img style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="140" height="auto" alt="'.esc_attr(get_option('blogname')).'" src="'.$logo_email_template.'">':'').'</a>
																																						</td>
																																					</tr>
																																				</tbody>
																																			</table>
																																		</td>
																																	</tr>
																																</table>
																															</td>
																														</tr>
																													</tbody>
																												</table>
																											</div>
																											<!--[if mso | IE]>
																										</td>
																									</tr>
																								</table>
																								<![endif]-->
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</div>
																			<!--[if mso | IE]>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>':'').'
														<tr>
															<td width="600px">
																<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
																	<tr>
																		<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
																			<![endif]-->
																			<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;'.($email_style == "style_2"?"border-radius:12px;":"").'max-width:600px;" class="wrapper">
																				<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;'.($email_style == "style_2"?"border-radius:12px;":"border:solid 1px #d9d9d9;").'">
																					<tbody>
																						<tr>
																							<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:0;text-align:center;">
																								<!--[if mso | IE]>
																								<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																									<tr>
																										<td style="vertical-align:top;width:600px;">
																											<![endif]-->
																											<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;"'.($is_rtl?' class="rtl-css"':'').'>
																												<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																													<tbody>
																														<tr>
																															<td style="vertical-align:top;'.($email_style == "style_2"?'padding-top: 20px;':'padding: 20px;').'">
																																<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																	'.($email_style == "style_2"?'':'
																																	<tr style="padding:0 20px;width:100%;background-color:'.$background_email.';">
																																		<td style="vertical-align:top;width:600px;">
																																			<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;">
																																				<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																					<tbody>
																																						<tr>
																																							<td style="vertical-align:top;">
																																								<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																									<tr>
																																										<td align="center" style="font-size:0px;padding:30px;word-break:break-word;">
																																											<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;">
																																												<tbody>
																																													<tr>
																																														<td style="width:140px;">
																																															<a href="'.esc_url(home_url('/')).'" target="_blank">'.($logo_email_template != ''?'<img style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="140" height="auto" alt="'.esc_attr(get_option('blogname')).'" src="'.$logo_email_template.'">':'').'</a>
																																														</td>
																																													</tr>
																																												</tbody>
																																											</table>
																																										</td>
																																									</tr>
																																								</table>
																																							</td>
																																						</tr>
																																					</tbody>
																																				</table>
																																			</div>
																																		</td>
																																	</tr>').'
																																	'.($mail == 'email_custom_mail' && $custom_image_mail != ''?'<tr>
																																	<td style="line-height:32px;padding:20px 20px 20px;text-align:center;" valign="baseline"><a href="'.esc_url(home_url('/')).'" target="_blank">'.($custom_image_mail != ''?'<img alt="'.esc_attr(get_option('blogname')).'" src="'.$custom_image_mail.'">':'').'</a></td>
																																	</tr>':'').'
																																	<tr>
																																		<td align="left" style="font-size:0px;padding:10px '.($email_style == "style_2"?"25px":"0").' 20px;word-break:break-word;"'.($is_rtl?' class="rtl-css"':'').'>
																																			<div style="font-family:Roboto, sans-serif;font-size:14px;line-height:25px;text-align:'.($is_rtl?'right':'left').';color:#000000;"'.($is_rtl?' class="rtl-css"':'').'>
																																				'.$content.'
																																			</div>
																																		</td>
																																	</tr>
																																</table>
																															</td>
																														</tr>
																													</tbody>
																												</table>
																											</div>
																											<!--[if mso | IE]>
																										</td>
																									</tr>
																								</table>
																								<![endif]-->
																							</td>
																						</tr>
																					</tbody>
																				</table>
																			</div>
																			<!--[if mso | IE]>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
														'.($active_footer_email == 'on'?'
															'.(isset($social_td) && $social_td != ''?'
															<tr>
																<td width="600px">
																	<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
																		<tr>
																			<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
																				<![endif]-->
																				<div style="margin:0px auto;max-width:600px;">
																					<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
																						<tbody>
																							<tr>
																								<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:0;text-align:center;">
																									<!--[if mso | IE]>
																									<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																										<tr>
																											<td style="vertical-align:top;width:600px;">
																												<![endif]-->
																												<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;">
																													<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																														<tbody>
																															<tr>
																																<td style="vertical-align:top;padding-top:20px;">
																																	<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																		<tr>
																																			<td align="left" style="font-size:0px;padding:10px 25px;padding-bottom:5px;word-break:break-word;">
																																				<table cellpadding="0" cellspacing="0" width="100%" border="0" style="color:#000000;font-family:Roboto, sans-serif;font-size:13px;line-height:22px;table-layout:auto;width:100%;border:none;">
																																				<th style="padding:0">'.$social_td.'</th>
																																				</table>
																																			</td>
																																		</tr>
																																	</table>
																																</td>
																															</tr>
																														</tbody>
																													</table>
																												</div>
																												<!--[if mso | IE]>
																											</td>
																										</tr>
																									</table>
																									<![endif]-->
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</div>
																				<!--[if mso | IE]>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															':'').
															($copyrights_for_email != ""?'
															<tr>
																<td width="600px">
																	<table align="center" border="0" cellpadding="0" cellspacing="0" style="width:600px;" width="600">
																		<tr>
																			<td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
																				<![endif]-->
																				<div style="margin:0px auto;max-width:600px;">
																					<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
																						<tbody>
																							<tr>
																								<td style="direction:'.($is_rtl?'rtl':'ltr').';font-size:0px;padding:0;text-align:center;">
																									<!--[if mso | IE]>
																									<table role="presentation" border="0" cellpadding="0" cellspacing="0">
																										<tr>
																											<td style="vertical-align:top;width:600px;">
																												<![endif]-->
																												<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:'.($is_rtl?'rtl':'ltr').';display:inline-block;vertical-align:top;width:100%;"'.($is_rtl?' class="rtl-css"':'').'>
																													<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																														<tbody>
																															<tr>
																																<td style="vertical-align:top;padding-top:0;">
																																	<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
																																		<tr>
																																			<td align="center" style="font-size:0px;padding:0;padding-bottom:20px;word-break:break-word;">
																																				<div style="font-family:Roboto, sans-serif;font-size:14px;line-height:25px;text-align:center;color:#707478;">
																																					<p>'.$copyrights_for_email.'</p>
																																				</div>
																																			</td>
																																		</tr>
																																	</table>
																																</td>
																															</tr>
																														</tbody>
																													</table>
																												</div>
																												<!--[if mso | IE]>
																											</td>
																										</tr>
																									</table>
																									<![endif]-->
																								</td>
																							</tr>
																						</tbody>
																					</table>
																				</div>
																				<!--[if mso | IE]>
																			</td>
																		</tr>
																	</table>
																</td>
															</tr>
															':'').'
														':'').'
													</table>
													<![endif]-->
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<!--[if mso | IE]>
							</td>
						</tr>
					</table>
					<![endif]-->
				</div>
			</body>
		</html>';
	}
endif;
