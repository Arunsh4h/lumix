<?php $settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
/* add_post_shortcode */
add_shortcode('add_post', 'add_post_shortcode');
function add_post_shortcode($atts, $content = null) {
	global $posted,$settings;
	$user_get_current_user_id = get_current_user_id();
	$add_post_no_register = askme_options("add_post_no_register");
	$add_post = askme_options("add_post");
	$custom_permission = askme_options("custom_permission");
	$editor_post_details = askme_options("editor_post_details");
	if (is_user_logged_in) {
		$user_is_login = get_userdata($user_get_current_user_id);
		$user_login_group = key($user_is_login->caps);
		$roles = $user_is_login->allcaps;
	}
	
	$out = '';
	if (($custom_permission == 1 && is_user_logged_in && !is_super_admin($user_get_current_user_id) && empty($roles["add_post"])) || ($custom_permission == 1 && !is_user_logged_in && $add_post != 1)) {
		$out .= '<div class="note_error"><strong>'.__("Sorry, you do not have permission to add a post .","vbegy").'</strong></div>';
	}else if (!is_user_logged_in && $add_post_no_register != 1) {
		$out .= '<div class="form-style form-style-3"><div class="note_error"><strong>'.__("You must login to add post .","vbegy").'</strong></div>'.do_shortcode("[ask_login register_2='yes']").'</div>';
	}else {
		$post_sort = askme_options("add_post_items");
		if ($_POST) {
			$post_type = (isset($_POST["post_type"]) && $_POST["post_type"] != ""?esc_html($_POST["post_type"]):"");
		}else {
			$post_type = "";
		}
		
		if (isset($_POST["post_type"]) && $_POST["post_type"] == "add_post") {
			do_action('new_post');
		}
		
		if ($post_type != "edit_post" && $post_type != "add_question") {
			$out .= '<div class="form-posts"><div class="form-style form-style-3 post-submit">
				<div class="add_post">
					<div '.(!is_user_logged_in?"class='if_no_login'":"").'>';
						$rand_p = rand(1,1000);
						$out .= '
						<form class="new-post-form" method="post" enctype="multipart/form-data">
							<div class="note_error display"></div>
							<div class="form-inputs clearfix">';
								if (!is_user_logged_in && $add_post_no_register == 1) {
									$out .= '<p>
										<label for="post-username-'.$rand_p.'" class="required">'.__("Username","vbegy").'<span>*</span></label>
										<input name="username" id="post-username-'.$rand_p.'" class="the-username" type="text" value="'.(isset($posted['username'])?$posted['username']:'').'">
										<span class="form-description">'.__("Please type your username .","vbegy").'</span>
									</p>
									
									<p>
										<label for="post-email-'.$rand_p.'" class="required">'.__("E-Mail","vbegy").'<span>*</span></label>
										<input name="email" id="post-email-'.$rand_p.'" class="the-email" type="text" value="'.(isset($posted['email'])?$posted['email']:'').'">
										<span class="form-description">'.__("Please type your E-Mail .","vbegy").'</span>
									</p>';
								}
								$out .= '<p>
									<label for="post-title-'.$rand_p.'" class="required">'.__("Post Title","vbegy").'<span>*</span></label>
									<input name="title" id="post-title-'.$rand_p.'" class="the-title" type="text" value="'.(isset($posted['title'])?ask_kses_stip($posted['title']):'').'">
									<span class="form-description">'.__("Please choose an appropriate title for the post .","vbegy").'</span>
								</p>
								<div class="div_category">
									<label for="post-category-'.$rand_p.'" class="required">'.__("Category","vbegy").'<span>*</span></label>
									'.ask_me_select_categories($rand_p,(isset($posted['category'])?$posted['category']:(isset($_POST['category'])?$_POST['category']:"")),null,'','category').'
									<span class="form-description">'.__("Please choose the appropriate section so easily search for your post .","vbegy").'</span>
								</div>';
								
								if (isset($post_sort) && is_array($post_sort)) {
									foreach ($post_sort as $sort_key => $sort_value) {
										$out = apply_filters("askme_post_sort",$out,"add_post_items",$post_sort,$sort_key,$sort_value,"add",$posted,array(),(isset($get_post)?$get_post:0));
										if ($sort_key == "tags_post" && isset($post_sort["tags_post"]["value"]) && $post_sort["tags_post"]["value"] == "tags_post") {
											$out .= '<p>
												<label for="post_tag-'.$rand_p.'">'.__("Tags","vbegy").'</label>
												<input type="text" class="input post_tag" name="post_tag" id="post_tag-'.$rand_p.'" value="'.(isset($posted['post_tag'])?$posted['post_tag']:'').'" data-seperator=",">
												<span class="form-description">'.__("Please choose  suitable Keywords Ex : ","vbegy").'<span class="color">'.__("post , video","vbegy").'</span> .</span>
											</p>';
										}else if ($sort_key == "featured_image" && isset($post_sort["featured_image"]["value"]) && $post_sort["featured_image"]["value"] == "featured_image") {
											$out .= '<label for="attachment-'.$rand_p.'">'.__("Attachment","vbegy").'</label>
											<div class="fileinputs">
												<input type="file" class="file" name="attachment" id="attachment-'.$rand_p.'">
												<div class="fakefile">
													<button type="button" class="button small margin_0">'.__("Select file","vbegy").'</button>
													<span><i class="icon-arrow-up"></i>'.__("Browse","vbegy").'</span>
												</div>
											</div>';
										}else if ($sort_key == "content_post" && isset($post_sort["content_post"]["value"]) && $post_sort["content_post"]["value"] == "content_post") {
											$out .= '<div class="details-area">
												<label for="post-details-'.$rand_p.'" '.(askme_options("content_post") == 1?'class="required"':'').'>'.__("Details","vbegy").(askme_options("content_post") == 1?'<span>*</span>':'').'</label>';
												if ($editor_post_details == 1) {
													ob_start();
													$settings = apply_filters('askme_add_post_editor_setting',$settings);
													wp_editor((isset($posted['comment'])?ask_kses_stip_wpautop($posted['comment']):""),"post-details-".$rand_p,$settings);
													$editor_contents = ob_get_clean();
													
													$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
												}else {
													$out .= '<textarea name="comment" id="post-details-'.$rand_p.'" class="the-textarea" aria-required="true" cols="58" rows="8">'.(isset($posted['comment'])?ask_kses_stip($posted['comment']):"").'</textarea>';
												}
												$out .= '<div class="clearfix"></div>
											</div>';
										}else if ($sort_key == "terms_active" && isset($post_sort["terms_active"]["value"]) && $post_sort["terms_active"]["value"] == "terms_active") {
											$terms_checked_post = askme_options("terms_checked_post");
											if ((isset($posted['agree_terms']) && $posted['agree_terms'] == 1) || ($terms_checked_post == 1 && empty($posted))) {
												$active_terms = true;
											}
											$terms_link = askme_options("terms_link_post");
											$terms_link_page = askme_options("terms_page_post");
											$terms_active_target = askme_options("terms_active_target_post");
											$privacy_policy = askme_options('privacy_policy_post');
											$privacy_active_target = askme_options('privacy_active_target_post');
											$privacy_page = askme_options('privacy_page_post');
											$privacy_link = askme_options('privacy_link_post');
											$out .= '<p class="question_poll_p">
												<label for="agree_terms-'.$rand_p.'" class="required">'.__("Terms","vbegy").'<span>*</span></label>
												<input type="checkbox" id="agree_terms-'.$rand_p.'" name="agree_terms" value="1" '.(isset($active_terms)?"checked='checked'":"").'>
												<span class="question_poll">'.sprintf(wp_kses(__("By asking your question, you agree to the <a target='%s' href='%s'>Terms of Service</a>%s.","vbegy"),array('a' => array('href' => array(),'target' => array()))),($terms_active_target == "same_page"?"_self":"_blank"),(isset($terms_link) && $terms_link != ""?$terms_link:(isset($terms_page) && $terms_page != ""?get_page_link($terms_page):"#")),($privacy_policy == 1?" ".sprintf(wp_kses(__("and <a target='%s' href='%s'>Privacy Policy</a>","vbegy"),array('a' => array('href' => array(),'target' => array()))),($privacy_active_target == "same_page"?"_self":"_blank"),(isset($privacy_link) && $privacy_link != ""?$privacy_link:(isset($privacy_page) && $privacy_page != ""?get_page_link($privacy_page):"#"))):"")).'</span>
											</p>';
										}
									}
								}
								$out .= askme_add_captcha(askme_options("the_captcha_post"),"post",$rand_p);
							$out .= '</div>
							<p class="form-submit margin_0">
								<input type="hidden" name="post_type" value="add_post">
								<input type="submit" value="'.__("Publish Your Post","vbegy").'" class="button color small submit add_qu publish-post">
							</p>
						
						</form>
					</div>
				</div>
			</div></div>';
		}
	}
	return $out;
}
/* vpanel_edit_post_shortcode */
add_shortcode('vpanel_edit_post', 'vpanel_edit_post_shortcode');
function vpanel_edit_post_shortcode($atts, $content = null) {
	global $posted,$settings;
	$editor_post_details = askme_options("editor_post_details");
	$post_sort = askme_options("add_post_items");
	
	$out = '';
	if (!is_user_logged_in) {
		$out .= '<div class="form-style form-style-3"><div class="note_error"><strong>'.__("You must login to add post .","vbegy").'</strong></div>'.do_shortcode("[ask_login register_2='yes']").'</div>';
	}else {
		$get_post = (int)$_GET["edit_post"];
		$get_post_p = get_post($get_post);
		$p_tag = "";
		if ($terms = wp_get_object_terms( $get_post, 'post_tag' )) :
			$terms_array = array();
			foreach ($terms as $term) :
				$terms_array[] = $term->name;
				$p_tag = implode(' , ', $terms_array);
			endforeach;
		endif;
		
		$category = wp_get_post_terms($get_post,'category',array("fields" => "ids"));
		if (isset($category) && is_array($category)) {
			$category = $category[0];
		}
		
		if (isset($_POST["post_type"]) && $_POST["post_type"] == "edit_post") {
			do_action('vpanel_edit_post');
		}
		
		$out .= '<div class="form-posts"><div class="form-style form-style-3 post-submit">
			<div class="add_post">
				<div '.(!is_user_logged_in?"class='if_no_login'":"").'>';
					$rand_e = rand(1,1000);
					$out .= '
					<form class="new-post-form" method="post" enctype="multipart/form-data">
						<div class="note_error display"></div>
						<div class="form-inputs clearfix">
							<p>
								<label for="post-title-'.$rand_e.'" class="required">'.__("Post Title","vbegy").'<span>*</span></label>
								<input name="title" id="post-title-'.$rand_e.'" class="the-title" type="text" value="'.(isset($posted['title'])?ask_kses_stip($posted['title']):ask_kses_stip($get_post_p->post_title)).'">
								<span class="form-description">'.__("Please choose an appropriate title for the post .","vbegy").'</span>
							</p>
							<div class="div_category">
								<label for="post-category-'.$rand_e.'" class="required">'.__("Category","vbegy").'<span>*</span></label>
								'.ask_me_select_categories($rand_e,(isset($posted['category'])?$posted['category']:(isset($category) && !empty($category)?$category:"")),null,$get_post,'category').'
								<span class="form-description">'.__("Please choose the appropriate section so easily search for your post .","vbegy").'</span>
							</div>';
							
							if (isset($post_sort) && is_array($post_sort)) {
								foreach ($post_sort as $sort_key => $sort_value) {
									$out = apply_filters("askme_post_sort",$out,"add_post_items",$post_sort,$sort_key,$sort_value,"edit",array(),$posted,(isset($get_post)?$get_post:0));
									if ($sort_key == "tags_post" && isset($post_sort["tags_post"]["value"]) && $post_sort["tags_post"]["value"] == "tags_post") {
										$out .= '<p>
											<label for="post_tag-'.$rand_e.'">'.__("Tags","vbegy").'</label>
											<input type="text" class="input post_tag" name="post_tag" id="post_tag-'.$rand_e.'" value="'.(isset($posted['post_tag'])?$posted['post_tag']:$p_tag).'" data-seperator=",">
											<span class="form-description">'.__("Please choose  suitable Keywords Ex : ","vbegy").'<span class="color">'.__("post , video","vbegy").'</span> .</span>
										</p>';
									}else if ($sort_key == "featured_image" && isset($post_sort["featured_image"]["value"]) && $post_sort["featured_image"]["value"] == "featured_image") {
										$out .= '<label for="attachment-'.$rand_e.'">'.__("Attachment","vbegy").'</label>
										<div class="fileinputs">
											<input type="file" class="file" name="attachment" id="attachment-'.$rand_e.'">
											<div class="fakefile">
												<button type="button" class="button small margin_0">'.__("Select file","vbegy").'</button>
												<span><i class="icon-arrow-up"></i>'.__("Browse","vbegy").'</span>
											</div>
										</div>';
									}else if ($sort_key == "content_post" && isset($post_sort["content_post"]["value"]) && $post_sort["content_post"]["value"] == "content_post") {
										$out .= '<div class="details-area">
											<label for="post-details-'.$rand_e.'" '.(askme_options("content_post") == 1?'class="required"':'').'>'.__("Details","vbegy").(askme_options("content_post") == 1?'<span>*</span>':'').'</label>';
											
											if ($editor_post_details == 1) {
												ob_start();
												$settings = apply_filters('askme_edit_post_editor_setting',$settings);
												wp_editor((isset($posted['comment'])?ask_kses_stip_wpautop($posted['comment']):$get_post_p->post_content),"post-details-".$rand_e,$settings);
												$editor_contents = ob_get_clean();
												
												$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
											}else {
												$out .= '<textarea name="comment" id="post-details-'.$rand_e.'" class="the-textarea" aria-required="true" cols="58" rows="8">'.(isset($posted['comment'])?ask_kses_stip($posted['comment']):ask_kses_stip($get_post_p->post_content,"yes")).'</textarea>';
											}
											$out .= '<div class="clearfix"></div>
										</div>';
									}
								}
							}
						$out .= '</div>
						<p class="form-submit margin_0">
							<input type="hidden" name="ID" value="'.$get_post.'">
							<input type="hidden" name="post_type" value="edit_post">
							<input type="submit" value="'.__("Edit Your post","vbegy").'" class="button color small submit add_qu publish-post">
						</p>
					</form>
				</div>
			</div>
		</div></div>';
	}
	return $out;
}?>