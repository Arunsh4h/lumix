<?php $settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
/* ask_me_categories_checklist */
function ask_me_categories_checklist ($args = array()) {
	$defaults = array(
		'selected_cats' => false,
		'taxonomy' => 'category',
	);
	
	$r = wp_parse_args( $args, $defaults );
	$taxonomy = $r['taxonomy'];
	$args['name'] = $r['name'];
	$args['id'] = $r['id'];
	$args['selected_cats'] = $r['selected_cats'];
	$categories = (array) get_terms( $taxonomy, array( 'get' => 'all' ) );
	$output = '';
	foreach ($categories as $key => $value) {
		$output .= '<li id="'.$args['name'].$taxonomy.'-'.$value->term_id.'">
			<label class="selectit"><input value="'.$value->term_id.'" '.(is_array($args['selected_cats']) && in_array($value->term_id,$args['selected_cats'])?checked($value->term_id,$value->term_id,false):'').' type="checkbox" name="'.$args['name'].'[]" id="'.$args['name'].'in-'.$taxonomy.'-'.$value->term_id.'"> '.$value->name.'</label>
		</li>';
	}
	return $output;
}
if (!function_exists('ask_me_select_categories')) {
	function ask_me_select_categories ($rand,$select,$attr = array(),$post_id = '',$taxonomy = '') {
		$category_single_multi = askme_options("category_single_multi");
		if ($category_single_multi == "ajax" && $taxonomy != "category") {
			$attr = array(
				'name'             => 'category',
				'taxonomy'         => $taxonomy,
				'orderby'          => 'name',
				'order'            => 'ASC',
				'required'         => 'yes',
				'show_option_none' => __("Select a Category","vbegy"),
			);
			$out              = '';
			$rand             = rand(1,1000);
			$taxonomy         = $attr['taxonomy'];
			$show_option_none = $attr['show_option_none'];
			$class            = ' ask_'.$attr['name'].'_'.$rand;
			$div_class        = 'ask_'.$attr['name'].'_'.$rand;
			
			$terms = array();
			if ($post_id) {
				$terms = wp_get_post_terms($post_id,$taxonomy,array('fields' => 'ids'));
				if (!empty($terms) && is_array($terms)) {
					asort($terms);
				}
				$child_c = get_term(end($terms),$taxonomy);
				if ($child_c->parent > 0) {
					$terms[] = $child_c->parent;
				}
				
				while ($child_c->parent > 0) {
					$child_c = get_term($child_c->parent,$taxonomy);
					if (!is_wp_error($child_c)) {
						if ($child_c->parent > 0) {
							$terms[] = $child_c->parent;
							continue;
						}
					}else {
						break;
					}
				}
			}else {
				$terms = $select;
				if (!empty($terms) && is_array($terms)) {
					asort($terms);
				}
			}
			if (!empty($terms) && is_array($terms)) {
				$terms = array_unique($terms);
			}
			
			$out .= '<span class="category-wrap'.$class.'">';
				if (empty($terms) || (is_array($terms) && !count($terms))) {
					$out .= '<span id="level-0" data-level="0">'.
					ask_categories_select(null,$attr,0).'
					</span>';
				}else {
					$level = 0;
					$last_term_id = end($terms);
					foreach( $terms as $term_id) {
						$class = ($last_term_id != $term_id)?'hasChild':'';
						$out .= '<span id="ask-level-'.$level.'" data-level="'.$level.'" >'.
							ask_categories_select($term_id,$attr,$level).'
						</span>';
						$attr['parent_cat'] = $term_id;
						$level++;
					}
				}
			$out .= '</span>
			<span class="category_loader loader_2"></span>';
			return $out;
		}else if ($category_single_multi == "multi" && $taxonomy != "category") {
			$args = array(
				'selected_cats' => $select,
				'taxonomy'      => $taxonomy,
				'id'            => ($taxonomy == ask_question_category?ask_question_category:"post-category").'-'.$rand,
				'name'          => 'category'
			);
			return '<ul class="row">'.ask_me_categories_checklist($args).'</ul>';
		}else {
			$select = (!empty($select) && is_array($select) && isset($select[0])?$select[0]:$select);
			return '<span class="styled-select">'.wp_dropdown_categories(array("orderby" => "name","echo" => "0","show_option_none" => esc_html__("Select a Category","vbegy"),'taxonomy' => $taxonomy, 'hide_empty' => 0,'depth' => 0,'class' => 'askme-custom-select','id' => ($taxonomy == ask_question_category?ask_question_category:"post-category").'-'.$rand,'name' => 'category','hierarchical' => true,'selected' => $select)).'</span>';
		}
	}
}
/* ask_me_child_cats */
if (!function_exists('ask_me_child_cats')) {
	function ask_me_child_cats () {
		$parentCat  = esc_html($_POST['catID']);
		$field_attr = stripcslashes($_POST['field_attr']);
		$field_attr = json_decode($field_attr, true);
		$taxonomy   = esc_html($field_attr['taxonomy']);
		$terms = null;
		$result = '';
		
		if ($parentCat < 1) {
			echo $result;
			die();
		}
		
		$terms = get_terms(array('taxonomy' => $taxonomy,'child_of'=> $parentCat,'hide_empty'=> 0));
		if ($terms) {
			$field_attr['parent_cat'] = $parentCat;
			if ( is_array($terms)) {
				foreach ($terms as $key => $term) {
					$terms[$key] = (array)$term;
				}
			}
			$result .= ask_categories_select(null,$field_attr,0);
		}else {
			die();
		}
		
		echo $result;
		die();
	}
}
add_action('wp_ajax_ask_me_child_cats','ask_me_child_cats');
add_action('wp_ajax_nopriv_ask_me_child_cats','ask_me_child_cats');
/* ask_categories_select */
if (!function_exists('ask_categories_select')) {
	function ask_categories_select ($terms,$attr,$level) {
		$out              = '';
		$selected         = $terms ? $terms : '';
		$required         = sprintf('data-required="%s" data-type="select"',$attr['required']);
		$taxonomy         = $attr['taxonomy'];
		$rand             = rand(1,1000);
		$class            = ' ask_'.$attr['name'].'_'.$rand.'_'.$level;
		$multi            = (isset($attr['multi'])?$attr['multi']:'[]');
		$show_option_none = (isset($attr['show_option_none'])?$attr['show_option_none']:__('Select a Category','vbegy'));
		
		$select = wp_dropdown_categories(array(
			'show_option_none' => $show_option_none,
			'hierarchical'     => 1,
			'hide_empty'       => 0,
			'orderby'          => isset($attr['orderby'])?$attr['orderby']:'name',
			'order'            => isset($attr['order'])?$attr['order']:'ASC',
			'name'             => $attr['name'].$multi,
			'taxonomy'         => $taxonomy,
			'echo'             => 0,
			'title_li'         => '',
			'class'            => 'cat-ajax askme-custom-select '.$taxonomy.$class,
			'id'               => 'cat-ajax '.$taxonomy.$class,
			'selected'         => $selected,
			'depth'            => 1,
			'child_of'         => isset($attr['parent_cat'])?$attr['parent_cat']:''
		));
		
		$attr = array(
			'required'     => $attr['required'],
			'name'         => $attr['name'],
			'orderby'      => $attr['orderby'],
			'order'        => $attr['order'],
			'name'         => $attr['name'],
			'taxonomy'     => $attr['taxonomy'],
		);
		
		$out .= '<span class="styled-select">'.str_replace('<select','<select data-taxonomy='.json_encode($attr).' '.$required,$select).'</span>';
		
		return $out;
	}
}
/* ask_question_shortcode */
add_shortcode('ask_question', 'ask_question_shortcode');
function ask_question_shortcode($atts, $content = null) {
	global $posted,$settings;
	$a = shortcode_atts( array(
	    'type' => '',
	), $atts );
	$out = '';
	$ask_question_no_register = askme_options("ask_question_no_register");
	$ask_question = askme_options("ask_question");
	$editor_question_details = askme_options("editor_question_details");
	$custom_permission = askme_options("custom_permission");
	$pay_ask = askme_options("pay_ask");
	$payment_group = askme_options("payment_group");
	$user_get_current_user_id = get_current_user_id();
	$your_avatar_meta = askme_avatar_name();
	$rand_q = rand(1,1000);
	
	if (is_user_logged_in) {
		$user_is_login = get_userdata($user_get_current_user_id);
		$user_login_group = key($user_is_login->caps);
		$roles = $user_is_login->allcaps;
	}
	
	if (($custom_permission == 1 && is_user_logged_in && !is_super_admin($user_get_current_user_id) && empty($roles["ask_question"])) || ($custom_permission == 1 && !is_user_logged_in && $ask_question != 1)) {
		$out .= '<div class="note_error"><strong>'.__("Sorry, you do not have permission to add a question.","vbegy").'</strong></div>';
		if (!is_user_logged_in) {
			$out .= '<div class="form-style form-style-3"><div class="note_error"><strong>'.__("You must login to ask a question.","vbegy").'</strong></div>'.do_shortcode("[ask_login register_2='yes']").'</div>';
		}
	}else if (!is_user_logged_in && $ask_question_no_register != 1) {
		$out .= '<div class="form-style form-style-3"><div class="note_error"><strong>'.__("You must login to ask a question.","vbegy").'</strong></div>'.do_shortcode("[ask_login register_2='yes']").'</div>';
	}else {
		if (!is_user_logged_in && $pay_ask == 1) {
			$out .= '<div class="form-style form-style-3"><div class="note_error"><strong>'.__("You must login to ask a question.","vbegy").'</strong></div>'.do_shortcode("[ask_login register_2='yes']").'</div>';
		}else {
			$_allow_to_ask = (int)(isset($user_get_current_user_id) && $user_get_current_user_id != ""?get_user_meta($user_get_current_user_id,$user_get_current_user_id."_allow_to_ask",true):"");
			if (isset($_POST["process"]) && $_POST["process"] == "ask") {
				/* Pay by points */
				if (isset($_POST["points"]) && $_POST["points"] > 0) {
					$points_price = (int)$_POST["points"];
					$points_user = get_user_meta($user_get_current_user_id,"points",true);
					if(!session_id()) session_start();
					if ($points_price <= $points_user) {
						$current_user = get_user_by("id",$user_get_current_user_id);
						$_points = get_user_meta($user_get_current_user_id,$current_user->user_login."_points",true);
						$_points++;
					
						update_user_meta($user_get_current_user_id,$current_user->user_login."_points",$_points);
						add_user_meta($user_get_current_user_id,$current_user->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$points_price,"-","ask_points","","time" => current_time('timestamp')));

						update_user_meta($user_get_current_user_id,"points",$points_user-$points_price);
						$_SESSION['vbegy_session_all'] = '<div class="alert-message success"><p>'.esc_html__("You have just bought to ask question by points.","vbegy").'</p></div>';
					}else {
						$_SESSION['vbegy_session_all'] = '<div class="alert-message error"><p>'.esc_html__("Sorry, you haven't enough points","vbegy").'</p></div>';
						wp_safe_redirect(esc_url(get_page_link(askme_options('add_question'))));
						die();
					}
				}
				/* Number allow to ask question */
				if ($_allow_to_ask < 0) {
					$_allow_to_ask = 0;
				}
				$_allow_to_ask++;
				update_user_meta($user_get_current_user_id,$user_get_current_user_id."_allow_to_ask",$_allow_to_ask);
				wp_safe_redirect(esc_url(get_page_link(askme_options('add_question'))));
				die();
			}
			
			if (isset($_allow_to_ask) && (int)$_allow_to_ask < 1 && $pay_ask == 1 && isset($payment_group[$user_login_group]) && $payment_group[$user_login_group] == "") {
				if (isset($_POST["form_type"]) && $_POST["form_type"] == "empty-post" && ((isset($_POST["comment"]) && $_POST["comment"] != "") || (isset($_POST["title"]) && $_POST["title"] != ""))) {
					$_post_title_question = (isset($_POST["title"]) && $_POST["title"] != ""?ask_kses_stip(stripslashes(htmlspecialchars($_POST["title"]))):"");
					$_post_comment_question = (isset($_POST["comment"]) && $_POST["comment"] != ""?ask_kses_stip(stripslashes(htmlspecialchars($_POST["comment"]))):"");
					if (is_user_logged_in) {
						if ($_post_title_question != "") {
							update_user_meta($user_get_current_user_id,"askme_title_before_payment",$_post_title_question);
						}
						if ($_post_comment_question != "") {
							update_user_meta($user_get_current_user_id,"askme_comment_before_payment",$_post_comment_question);
						}
					}
				}
				$payment_type_ask = askme_options("payment_type_ask");
				echo askme_payment_form("ask_question",$payment_type_ask,$_POST);
			}else {
				$question_points_active = askme_options("question_points_active");
				$question_points = askme_options("question_points");
				$points = get_user_meta($user_get_current_user_id,"points",true);
				$points = ($points != ""?$points:0);
				if ($_POST) {
					$post_type = (isset($_POST["post_type"]) && $_POST["post_type"] != ""?esc_html($_POST["post_type"]):"");
				}else {
					$post_type = "";
				}
				
				if (isset($_POST["post_type"]) && $_POST["post_type"] == "add_question") {
					do_action('new_post');
				}
				
				if (($question_points_active == 0 || ($points >= $question_points && $question_points_active == 1)) && $post_type != "edit_question" && $post_type != "add_post") {
					$users_by_id = $get_user_id = 0;
					if (isset($_GET["user_id"]) && $_GET["user_id"] != "") {
						$get_user_id = (int)$_GET["user_id"];
						$get_users_by_id = get_users(array("include" => array($get_user_id)));
						if (isset($get_users_by_id) && !empty($get_users_by_id)) {
							$users_by_id = 1;
						}
					}
					if ($users_by_id == 1) {
						$question_sort_option = "ask_user_items";
						$comment_question = askme_options("content_ask_user");
						$editor_question_details = askme_options("editor_ask_user");
						$add_question_default = askme_options("add_question_default_user");
					}else {
						$question_sort_option = "ask_question_items";
						$comment_question = askme_options("comment_question");
						$editor_question_details = askme_options("editor_question_details");
						$add_question_default = askme_options("add_question_default");
					}
					$question_sort = askme_options($question_sort_option);
					$the_captcha = askme_options("the_captcha");
					if (isset($question_sort) && is_array($question_sort)) {
						$question_sort = array_merge($question_sort,array("the_captcha" => array("value" => ($the_captcha == 1?"the_captcha":0))));
					}

					$comment_question = "";
					if (isset($question_sort["title_question"]["value"]) && $question_sort["title_question"]["value"] == "title_question") {
						$comment_question = askme_options("comment_question");
						if ($comment_question == 1) {
							$comment_question = "required";
						}
					}else {
						$comment_question = "required";
					}
					
					if (is_user_logged_in && $user_get_current_user_id == $get_user_id) {
						echo '<div class="alert-message error"><p>'.__("You can't ask yourself.","vbegy").'</p></div>';
					}else {
						$out .= '<div class="form-posts"><div class="form-style form-style-3 question-submit">
							<div class="ask_question">
								<div '.(!is_user_logged_in?"class='if_no_login'":"").'>';?>
									<script type="text/javascript">
										jQuery(function () {
											jQuery("input.question_poll").each(function () {
												var poll = jQuery(this);
												if (poll.is(':checked')) {
													poll.parent().parent().find(".poll_options").slideDown(500);
												}else {
													poll.parent().parent().find(".poll_options").slideUp(500);
												}
												
												poll.click(function () {
													var poll = jQuery(this);
													if (poll.is(':checked')) {
														poll.parent().parent().find(".poll_options").slideDown(500);
													}else {
														poll.parent().parent().find(".poll_options").slideUp(500);
													}
												});
											});
										});
									</script><?php
									if ($question_points_active == 1) {
										$out .= '<div class="alert-message info"><i class="icon-ok"></i><p><span>'.__("Note","vbegy").'</span><br>'.sprintf(__("Will lose %s points when adding a new question.","vbegy"),$question_points).'</p></div>';
									}
									if ($users_by_id == 1) {
										$display_name = get_the_author_meta('display_name', $get_user_id);
										$out .= '<div class="ask-user-question">
											'.askme_user_avatar(get_the_author_meta($your_avatar_meta,$get_user_id),42,42,$get_user_id,$display_name).'
											'.esc_html__("Ask","vbegy").' '.$display_name.' '.esc_html("a question","vbegy").'
										</div>'.
										apply_filters("askme_ask_user_with_points",false,$get_user_id);
									}
									$ask_user_with_points_form = apply_filters("askme_ask_user_with_points_form",true,$get_user_id);
									if ($ask_user_with_points_form == true) {
										$out .= '<form class="new-question-form" method="post" enctype="multipart/form-data">
											<div class="note_error display"></div>
											<div class="form-inputs clearfix">';
												$username_email_no_register = askme_options("username_email_no_register");
												if (!is_user_logged_in && $ask_question_no_register == 1 && $username_email_no_register == 1) {
													$out .= '<p>
														<label for="question-username-'.$rand_q.'" class="required">'.__("Username","vbegy").'<span>*</span></label>
														<input name="username" id="question-username-'.$rand_q.'" class="the-username" type="text" value="'.(isset($posted['username'])?$posted['username']:'').'">
														<span class="form-description">'.__("Please type your username .","vbegy").'</span>
													</p>
													
													<p>
														<label for="question-email-'.$rand_q.'" class="required">'.__("E-Mail","vbegy").'<span>*</span></label>
														<input name="email" id="question-email-'.$rand_q.'" class="the-email" type="text" value="'.(isset($posted['email'])?$posted['email']:'').'">
														<span class="form-description">'.__("Please type your E-Mail .","vbegy").'</span>
													</p>';
												}
												
												$out .= apply_filters('askme_add_question_before_title',false,$posted);
												$askme_title_before_payment = get_user_meta($user_get_current_user_id,"askme_title_before_payment",true);
												$askme_comment_before_payment = get_user_meta($user_get_current_user_id,"askme_comment_before_payment",true);
												if ($askme_title_before_payment != "" && (!isset($_POST) || (isset($_POST) && empty($_POST)))) {
													$posted["title"] = $askme_title_before_payment;
												}
												if ($askme_comment_before_payment != "" && (!isset($_POST) || (isset($_POST) && empty($_POST)))) {
													$posted["comment"] = $askme_comment_before_payment;
												}
												
												if (isset($question_sort) && is_array($question_sort)) {
													foreach ($question_sort as $sort_key => $sort_value) {
														$out = apply_filters("askme_question_sort",$out,$question_sort_option,$question_sort,$sort_key,$sort_value,"add",$posted,$posted,(isset($get_question)?$get_question:0));
														if ($sort_key == "title_question" && ((isset($question_sort["title_question"]["value"]) && $question_sort["title_question"]["value"] == "title_question") || (isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] != "comment_question"))) {
															$out .= '<p>
																<label for="question-title-'.$rand_q.'" class="required">'.__("Question Title","vbegy").'<span>*</span></label>
																<input name="title" id="question-title-'.$rand_q.'" class="the-title" type="text" value="'.(isset($posted['title'])?ask_kses_stip(stripslashes(htmlspecialchars($posted['title']))):(isset($_POST["title"])?ask_kses_stip(stripslashes(htmlspecialchars($_POST["title"]))):"")).'">
																<span class="form-description">'.__("Please choose an appropriate title for the question to answer it even easier .","vbegy").'</span>
															</p>'.apply_filters('askme_add_question_after_title',false,$posted);
														}else if ($sort_key == "categories_question" && $users_by_id == 0 && isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question") {
															$category_question_required = askme_options("category_question_required");
															$out .= '<div class="div_category">
																<label for="'.ask_question_category.'-'.$rand_q.'"'.($category_question_required == 1?' class="required"':'').'>'.__("Category","vbegy").($category_question_required == 1?'<span>*</span>':'').'</label>
																'.ask_me_select_categories($rand_q,(isset($posted['category'])?$posted['category']:""),null,'',ask_question_category).'
																<span class="form-description">'.__("Please choose the appropriate category so others can easily search your question.","vbegy").'</span>
															</div>';
														}else if ($sort_key == "tags_question" && $users_by_id == 0 && isset($question_sort["tags_question"]["value"]) && $question_sort["tags_question"]["value"] == "tags_question") {
															$out .= '<p>
																<label for="question_tags-'.$rand_q.'">'.__("Tags","vbegy").'</label>
																<input type="text" class="input question_tags" name="'.ask_question_tags.'" id="question_tags-'.$rand_q.'" value="'.(isset($posted[ask_question_tags])?stripslashes(htmlspecialchars($posted[ask_question_tags])):'').'" data-seperator=",">
																<span class="form-description">'.__("Please choose  suitable Keywords Ex : ","vbegy").'<span class="color">'.__("question , poll","vbegy").'</span> .</span>
															</p>';
														}else if ($sort_key == "poll_question" && $users_by_id == 0 && isset($question_sort["poll_question"]["value"]) && $question_sort["poll_question"]["value"] == "poll_question") {
															$out .= '<p class="question_poll_p">
																<label for="question_poll-'.$rand_q.'">'.__("Poll","vbegy").'</label>
																<input type="checkbox" id="question_poll-'.$rand_q.'" class="question_poll" value="1" name="question_poll" '.((isset($posted['question_poll']) && $posted['question_poll'] == 1) || (isset($add_question_default["poll"]) && $add_question_default["poll"] == 1 && empty($posted))?"checked='checked'":"").'>
																<span class="question_poll">'.__("This question is a poll ?","vbegy").'</span>
																<span class="poll-description">'.__("If you want to be doing a poll click here .","vbegy").'</span>
															</p>
															
															<div class="clearfix"></div>
															<div class="poll_options">
																<p class="form-submit add_poll">
																	<button type="button" class="button color small submit add_poll_button add_poll_button_js"><i class="icon-plus"></i>'.__("Add Field","vbegy").'</button>
																</p>
																<ul class="question_poll_item question_polls_item">';
																	if (isset($_POST['ask']) && is_array($_POST['ask'])) {
																		foreach($_POST['ask'] as $ask) {
																			if (stripslashes($ask['title']) != "") {
																				$out .= '<li id="poll_li_'.(int)$ask['id'].'">
																					<div class="poll-li">
																						<p><input id="ask['.(int)$ask['id'].'][title]" class="ask" name="ask['.(int)$ask['id'].'][title]" value="'.stripslashes($ask['title']).'" type="text"></p>
																						<input id="ask['.(int)$ask['id'].'][value]" name="ask['.(int)$ask['id'].'][value]" value="" type="hidden">
																						<input id="ask['.(int)$ask['id'].'][id]" name="ask['.(int)$ask['id'].'][id]" value="'.(int)$ask['id'].'" type="hidden">
																						<div class="del-poll-li"><i class="icon-remove"></i></div>
																						<div class="move-poll-li"><i class="icon-fullscreen"></i></div>
																					</div>
																				</li>';
																			}
																		}
																	}else {
																		$out .= '<li id="poll_li_1">
																			<div class="poll-li">
																				<p><input id="ask[1][title]" class="ask" name="ask[1][title]" value="" type="text"></p>
																				<input id="ask[1][value]" name="ask[1][value]" value="" type="hidden">
																				<input id="ask[1][id]" name="ask[1][id]" value="1" type="hidden">
																				<div class="del-poll-li"><i class="icon-remove"></i></div>
																				<div class="move-poll-li"><i class="icon-fullscreen"></i></div>
																			</div>
																		</li><li id="poll_li_2">
																			<div class="poll-li">
																				<p><input id="ask[2][title]" class="ask" name="ask[2][title]" value="" type="text"></p>
																				<input id="ask[2][value]" name="ask[2][value]" value="" type="hidden">
																				<input id="ask[2][id]" name="ask[2][id]" value="2" type="hidden">
																				<div class="del-poll-li"><i class="icon-remove"></i></div>
																				<div class="move-poll-li"><i class="icon-fullscreen"></i></div>
																			</div>
																		</li>';
																	}
																$out .= '</ul>
																<div class="clearfix"></div>
															</div>';
														}else if ($sort_key == "attachment_question" && $users_by_id == 0 && isset($question_sort["attachment_question"]["value"]) && $question_sort["attachment_question"]["value"] == "attachment_question") {
															$out .= '<label>'.__("Attachment","vbegy").'</label>
															<div class="question-multiple-upload">
																<div class="clearfix"></div>
																<p class="form-submit add_poll">
																	<button type="button" class="button color small submit add_poll_button add_upload_button_js"><i class="icon-plus"></i>'.__("Add Field","vbegy").'</button>
																</p>
																<ul class="question_poll_item question_upload_item"></ul>
																<script> var next_attachment = 1;</script>
																<div class="clearfix"></div>
															</div>';
														}else if ($sort_key == "featured_image" && $users_by_id == 0 && isset($question_sort["featured_image"]["value"]) && $question_sort["featured_image"]["value"] == "featured_image") {
															$out .= '<div class="featured_image_question">
																<label for="featured_image-'.$rand_q.'">'.__("Featured image","vbegy").'</label>
																<div class="fileinputs">
																	<input type="file" class="file" name="featured_image" id="featured_image-'.$rand_q.'">
																	<div class="fakefile">
																		<button type="button" class="button small margin_0">'.__("Select file","vbegy").'</button>
																		<span><i class="icon-arrow-up"></i>'.__("Browse","vbegy").'</span>
																	</div>
																</div>
															</div>';
														}else if ($sort_key == "comment_question" && isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] == "comment_question") {
															$out .= '<div class="details-area">
																<label for="question-details-'.$rand_q.'" '.($comment_question == "required"?'class="required"':'').'>'.__("Details","vbegy").($comment_question == "required"?'<span>*</span>':'').'</label>';
																$last_value = "";
																if (isset($question_sort["title_question"]["value"]) && $question_sort["title_question"]["value"] != "title_question" && isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] == "comment_question") {
																	$last_value = (isset($_POST["title"])?ask_kses_stip($_POST["title"]):"");
																}
																if ($editor_question_details == 1) {
																	ob_start();
																	$settings = apply_filters('askme_add_question_editor_setting',$settings);
																	wp_editor((isset($posted['comment'])?ask_kses_stip_wpautop($posted['comment']):(isset($_POST["comment"])?wp_kses_post($_POST["comment"]):$last_value)),"question-details-".$rand_q,$settings);
																	$editor_contents = ob_get_clean();
																	$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
																}else {
																	$out .= '<textarea name="comment" id="question-details-'.$rand_q.'" class="the-textarea" aria-required="true" cols="58" rows="8">'.(isset($posted['comment'])?ask_kses_stip($posted['comment']):(isset($_POST["comment"])?ask_kses_stip($_POST["comment"]):$last_value)).'</textarea>';
																}
																$out .= '<div class="clearfix"></div>
															</div>';
														}else if ($sort_key == "video_desc_active" && $users_by_id == 0 && isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active") {
															$out .= '
															<p class="question_poll_p">
																<label for="video_description-'.$rand_q.'">'.__("Video description","vbegy").'</label>
																<input type="checkbox" id="video_description-'.$rand_q.'" class="video_description_input" name="video_description" value="1" '.((isset($posted['video_description']) && $posted['video_description'] == 1) || (isset($add_question_default["video"]) && $add_question_default["video"] == 1 && empty($posted))?"checked='checked'":"").'>
																<span class="question_poll">'.__("Do you need a video to description the problem better ?","vbegy").'</span>
															</p>
															
															<div class="video_description" '.((isset($posted['video_description']) && $posted['video_description'] == 1) || (isset($add_question_default["video"]) && $add_question_default["video"] == 1 && empty($posted))?"style='display:block;'":"").'>
																<p>
																	<label for="video_type-'.$rand_q.'">'.__("Video type","vbegy").'</label>
																	<span class="styled-select">
																		<select id="video_type-'.$rand_q.'" name="video_type">
																			<option value="youtube" '.(isset($posted['video_type']) && $posted['video_type'] == "youtube"?' selected="selected"':'').'>Youtube</option>
																			<option value="vimeo" '.(isset($posted['video_type']) && $posted['video_type'] == "vimeo"?' selected="selected"':'').'>Vimeo</option>
																			<option value="daily" '.(isset($posted['video_type']) && $posted['video_type'] == "daily"?' selected="selected"':'').'>Dialymotion</option>
																			<option value="facebook" '.(isset($posted['video_type']) && $posted['video_type'] == "facebook"?' selected="selected"':'').'>Facebook</option>
																			<option value="tiktok" '.(isset($posted['video_type']) && $posted['video_type'] == "tiktok"?' selected="selected"':'').'>TikTok</option>
																		</select>
																	</span>
																	<span class="form-description">'.__("Choose from here the video type .","vbegy").'</span>
																</p>
																
																<p>
																	<label for="video_id-'.$rand_q.'">'.__("Video ID","vbegy").'</label>
																	<input name="video_id" id="video_id-'.$rand_q.'" class="video_id" type="text" value="'.(isset($posted['video_id'])?$posted['video_id']:'').'">
																	<span class="form-description">'.__("Put here the video id : https://www.youtube.com/watch?v=sdUUx5FdySs EX : 'sdUUx5FdySs'.","vbegy").'</span>
																</p>
															</div>';
														}else if ($sort_key == "remember_answer" && isset($question_sort["remember_answer"]["value"]) && $question_sort["remember_answer"]["value"] == "remember_answer") {
															$out .= '<p class="question_poll_p remember_answer_p">
																<label for="remember_answer-'.$rand_q.'">'.__("Notified","vbegy").'</label>
																<input type="checkbox" id="remember_answer-'.$rand_q.'" name="remember_answer" value="1" '.((isset($posted['remember_answer']) && $posted['remember_answer'] == 1) || (isset($add_question_default["notified"]) && $add_question_default["notified"] == 1 && empty($posted))?"checked='checked'":"").'>
																<span class="question_poll">'.__("Notified by e-mail at incoming answers.","vbegy").'</span>
															</p>';
														}else if (is_user_logged_in && $sort_key == "private_question" && isset($question_sort["private_question"]["value"]) && $question_sort["private_question"]["value"] == "private_question") {
															$out .= '<p class="question_poll_p">
																<label for="private_question-'.$rand_q.'">'.__("Private question","vbegy").'</label>
																<input type="checkbox" id="private_question-'.$rand_q.'" name="private_question" value="1" '.((isset($posted['private_question']) && $posted['private_question'] == 1) || (isset($add_question_default["private"]) && $add_question_default["private"] == 1 && empty($posted))?"checked='checked'":"").'>
																<span class="question_poll">'.__("Activate this question as a private question.","vbegy").'</span>
															</p>';
														}else if ((is_user_logged_in || (!is_user_logged_in && (($username_email_no_register == 1 && $ask_question_no_register == 1)) || $ask_question_no_register != 1)) && $sort_key == "anonymously_question" && isset($question_sort["anonymously_question"]["value"]) && $question_sort["anonymously_question"]["value"] == "anonymously_question") {
															$out .= '<p class="question_poll_p">
																<label for="anonymously_question-'.$rand_q.'">'.__("Ask Anonymously","vbegy").'</label>
																<input type="checkbox" class="ask_anonymously" id="anonymously_question-'.$rand_q.'" name="anonymously_question" value="1" '.((isset($posted['anonymously_question']) && $posted['anonymously_question'] == 1) || (isset($add_question_default["anonymously"]) && $add_question_default["anonymously"] == 1 && empty($posted))?"checked='checked'":"").'>';
																if (is_user_logged_in) {
																	$your_avatar = get_the_author_meta($your_avatar_meta,$user_get_current_user_id);
																	$display_name = get_the_author_meta('display_name',$user_get_current_user_id);
																	$out .= '<span class="question_poll anonymously_span ask_named'.(empty($posted['anonymously_question'])?' anonymously_span_show':'').'">';
																		if ($your_avatar) {
																			$out .= askme_user_avatar($your_avatar,25,25,$user_get_current_user_id,$display_name);
																		}else {
																			$out .= get_avatar($user_get_current_user_id,'25','');
																		}
																		$out .= '<span>'.$display_name.' '.esc_html__("asks","vbegy").'</span>
																	</span>
																	<span class="question_poll anonymously_span ask_none'.((isset($posted['anonymously_question']) && $posted['anonymously_question'] == 1) || (isset($add_question_default["anonymously"]) && $add_question_default["anonymously"] == 1 && empty($posted))?' anonymously_span_show':'').'">
																		<img alt="'.esc_html__("Anonymous","vbegy").'" src="'.get_template_directory_uri().'/images/avatar.png">
																		<span>'.esc_html__("Anonymous asks","vbegy").'</span>
																	</span>';
																}else {
																	$out .= '<span class="question_poll">'.__("Anonymous asks","vbegy").'</span>';
																}
															$out .= '</p>';
														}else if ($sort_key == "the_captcha" && isset($question_sort["the_captcha"]["value"]) && $question_sort["the_captcha"]["value"] == "the_captcha") {
															$out .= askme_add_captcha(askme_options("the_captcha"),"question",$rand_q);
														}else if ($sort_key == "terms_active" && isset($question_sort["terms_active"]["value"]) && $question_sort["terms_active"]["value"] == "terms_active") {
															$terms_link = askme_options("terms_link");
															$terms_link_page = askme_options("terms_page");
															$terms_active_target = askme_options("terms_active_target");
															$privacy_policy = askme_options('privacy_policy');
															$privacy_active_target = askme_options('privacy_active_target');
															$privacy_page = askme_options('privacy_page');
															$privacy_link = askme_options('privacy_link');
															$out .= '<p class="question_poll_p">
																<label for="agree_terms-'.$rand_q.'" class="required">'.__("Terms","vbegy").'<span>*</span></label>
																<input type="checkbox" id="agree_terms-'.$rand_q.'" name="agree_terms" value="1" '.((isset($posted['agree_terms']) && $posted['agree_terms'] == 1) || (isset($add_question_default["terms"]) && $add_question_default["terms"] == 1 && empty($posted))?"checked='checked'":"").'>
																<span class="question_poll">'.sprintf(wp_kses(__("By asking your question, you agree to the <a target='%s' href='%s'>Terms of Service</a>%s.","vbegy"),array('a' => array('href' => array(),'target' => array()))),($terms_active_target == "same_page"?"_self":"_blank"),(isset($terms_link) && $terms_link != ""?$terms_link:(isset($terms_link_page) && $terms_link_page != ""?get_page_link($terms_link_page):"#")),($privacy_policy == 1?" ".sprintf(wp_kses(__("and <a target='%s' href='%s'>Privacy Policy</a>","vbegy"),array('a' => array('href' => array(),'target' => array()))),($privacy_active_target == "same_page"?"_self":"_blank"),(isset($privacy_link) && $privacy_link != ""?$privacy_link:(isset($privacy_page) && $privacy_page != ""?get_page_link($privacy_page):"#"))):"")).'</span>
															</p>';
														}
													}
												}
												if (is_user_logged_in() && is_super_admin($user_get_current_user_id) && $users_by_id != 1) {
													$out .= '<p class="question_poll_p">
														<label for="sticky-'.$rand_q.'">'.esc_html__("Sticky","vbegy").'</label>
														<input type="checkbox" id="sticky-'.$rand_q.'" class="sticky_input" name="sticky" value="sticky"'.((isset($posted['sticky']) && $posted['sticky'] == "sticky") || (isset($add_question_default["sticky"]) && $add_question_default["sticky"] == 1 && empty($posted))?" checked='checked'":"").'>
														<span class="question_poll">'.esc_html__("Stick this question","vbegy").' '.esc_html("Note: this option shows for the admin only!","vbegy").'</span>
													</p>';
												}
											$out .= '</div>
											
											<p class="form-submit">
												<input type="hidden" name="post_type" value="add_question">';
												if (isset($a["type"]) && $a["type"] == "popup") {
													$out .= '<input type="hidden" name="form_type" value="question-popup">';
												}else {
													$out .= '<input type="hidden" name="form_type" value="add_question">';
												}
												if ($users_by_id == 1) {
													$out .= '<input type="hidden" name="user_id" value="'.$get_user_id.'">';
												}
												$out .= '<input type="submit" value="'.__("Publish Your Question","vbegy").'" class="button color small submit add_qu publish-question">
											</p>
										
										</form>';
									}else {
										$out .= apply_filters("askme_filter_ask_user_messages",false,$get_user_id);
									}
								$out .= '</div>
							</div>
						</div></div>';
					}
				}else {
					$out .= sprintf(__("Sorry do not have the minimum points Please do answer questions, even gaining points ( The minimum points = %s ) .","vbegy"),$question_points);
				}
			}
		}
	}
	return $out;
}
/* edit_question_shortcode */
add_shortcode('edit_question', 'edit_question_shortcode');
function edit_question_shortcode($atts, $content = null) {
	global $posted,$settings;
	$editor_question_details = askme_options("editor_question_details");
	$out = '';
	if (!is_user_logged_in) {
		$out .= '<div class="form-style form-style-3"><div class="note_error"><strong>'.__("You must login to edit question .","vbegy").'</strong></div>'.do_shortcode("[ask_login register_2='yes']").'</div>';
	}else {
		$get_question = (int)$_GET["q"];
		$get_post_q = get_post($get_question);
		$q_tag = "";
		if ($terms = wp_get_object_terms( $get_question, ask_question_tags )) :
			$terms_array = array();
			foreach ($terms as $term) :
				$terms_array[] = $term->name;
				$q_tag = implode(' , ', $terms_array);
			endforeach;
		endif;
		
		$question_category = wp_get_post_terms($get_question,ask_question_category,array("fields" => "ids"));
		if (isset($_POST["post_type"]) && $_POST["post_type"] == "edit_question") {
			do_action('edit_question');
		}
		$get_question_user_id = get_post_meta($get_question,"user_id",true);
		if (empty($get_question_user_id)) {
			$question_sort_option = "ask_question_items";
			$comment_question = askme_options("comment_question");
			$editor_question_details = askme_options("editor_question_details");
			$add_question_default = askme_options("add_question_default");
		}else {
			$question_sort_option = "ask_user_items";
			$comment_question = askme_options("content_ask_user");
			$editor_question_details = askme_options("editor_ask_user");
			$add_question_default = askme_options("add_question_default_user");
		}
		$question_sort = askme_options($question_sort_option);
		$the_captcha = askme_options("the_captcha");
		if (isset($question_sort) && is_array($question_sort)) {
			$question_sort = array_merge($question_sort,array("the_captcha" => array("value" => ($the_captcha == 1?"the_captcha":0))));
		}
		$comment_question = "";
		if (isset($question_sort["title_question"]["value"]) && $question_sort["title_question"]["value"] == "title_question") {
			$comment_question = askme_options("comment_question");
			if ($comment_question == 1) {
				$comment_question = "required";
			}
		}else {
			$comment_question = "required";
		}
		
		$out .= '<div class="form-posts"><div class="form-style form-style-3 question-submit">
			<div class="ask_question">
				<div '.(!is_user_logged_in?"class='if_no_login'":"").'>';
					$rand_e = rand(1,1000);
					$out .= '
					<form class="new-question-form" method="post" enctype="multipart/form-data">
						<div class="note_error display"></div>
						<div class="form-inputs clearfix">';
							$out .= apply_filters('askme_edit_question_before_title',false,$posted,$get_question);
							if (isset($question_sort) && is_array($question_sort)) {
								foreach ($question_sort as $sort_key => $sort_value) {
									$out = apply_filters("askme_question_sort",$out,$question_sort_option,$question_sort,$sort_key,$sort_value,"edit",$posted,$posted,(isset($get_question)?$get_question:0));
									if ($sort_key == "title_question" && ((isset($question_sort["title_question"]["value"]) && $question_sort["title_question"]["value"] == "title_question") || (isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] != "comment_question"))) {
										$out .= '<p>
											<label for="question-title-'.$rand_e.'" class="required">'.__("Question Title","vbegy").'<span>*</span></label>
											<input name="title" id="question-title-'.$rand_e.'" class="the-title" type="text" value="'.(isset($posted['title'])?ask_kses_stip(stripslashes(htmlspecialchars($posted['title']))):ask_kses_stip(stripslashes(htmlspecialchars($get_post_q->post_title)))).'">
											<span class="form-description">'.__("Please choose an appropriate title for the question to answer it even easier .","vbegy").'</span>
										</p>'.apply_filters('askme_edit_question_after_title',false,$posted,$get_question);
									}else if ($sort_key == "categories_question" && empty($get_question_user_id) && isset($question_sort["categories_question"]["value"]) && $question_sort["categories_question"]["value"] == "categories_question") {
										$category_question_required = askme_options("category_question_required");
										$out .= '<div class="div_category">
											<label for="'.ask_question_category.'-'.$rand_e.'"'.($category_question_required == 1?' class="required"':'').'>'.__("Category","vbegy").($category_question_required == 1?'<span>*</span>':'').'</label>
											'.ask_me_select_categories($rand_e,(isset($posted['category'])?$posted['category']:(isset($question_category) && !empty($question_category)?$question_category:"")),null,$get_question,ask_question_category).'
											<span class="form-description">'.__("Please choose the appropriate section so easily search for your question .","vbegy").'</span>
										</div>';
									}else if ($sort_key == "tags_question" && empty($get_question_user_id) && isset($question_sort["tags_question"]["value"]) && $question_sort["tags_question"]["value"] == "tags_question") {
										$out .= '<p>
											<label for="question_tags-'.$rand_e.'">'.__("Tags","vbegy").'</label>
											<input type="text" class="input question_tags" name="'.ask_question_tags.'" id="question_tags-'.$rand_e.'" value="'.(isset($posted[ask_question_tags])?stripslashes(htmlspecialchars($posted[ask_question_tags])):stripslashes(htmlspecialchars($q_tag))).'" data-seperator=",">
											<span class="form-description">'.__("Please choose  suitable Keywords Ex : ","vbegy").'<span class="color">'.__("question , poll","vbegy").'</span> .</span>
										</p>';
									}else if ($sort_key == "poll_question" && empty($get_question_user_id) && isset($question_sort["poll_question"]["value"]) && $question_sort["poll_question"]["value"] == "poll_question") {
										$out .= '<p class="question_poll_p">
											<label for="question_poll-'.$rand_e.'">'.__("Poll","vbegy").'</label>
											<input type="checkbox" id="question_poll-'.$rand_e.'" class="question_poll" value="1" name="question_poll" '.(isset($posted['question_poll']) && $posted['question_poll'] == 1 || get_post_meta($get_question,"question_poll",true) == 1?"checked='checked'":"").'>
											<span class="question_poll">'.__("This question is a poll ?","vbegy").'</span>
											<span class="poll-description">'.__("If you want to be doing a poll click here .","vbegy").'</span>
										</p>
										
										<div class="clearfix"></div>
										<div class="poll_options">
											<p class="form-submit add_poll">
												<button type="button" class="button color small submit add_poll_button add_poll_button_js"><i class="icon-plus"></i>'.__("Add Field","vbegy").'</button>
											</p>
											<ul class="question_poll_item question_polls_item">';
												if (isset($_POST['ask']) && is_array($_POST['ask'])) {
													$q_ask = $_POST['ask'];
												}else {
													$q_ask = get_post_meta($get_question,"ask",true);
												}
												if (isset($q_ask) && is_array($q_ask)) {
													foreach($q_ask as $ask) {
														if (stripslashes($ask['title']) != "") {
															$out .= '<li id="poll_li_'.(int)$ask['id'].'">
																<div class="poll-li">
																	<p><input id="ask['.(int)$ask['id'].'][title]" class="ask" name="ask['.(int)$ask['id'].'][title]" value="'.stripslashes($ask['title']).'" type="text"></p>
																	<input id="ask['.(int)$ask['id'].'][value]" name="ask['.(int)$ask['id'].'][value]" value="" type="hidden">
																	<input id="ask['.(int)$ask['id'].'][id]" name="ask['.(int)$ask['id'].'][id]" value="'.(int)$ask['id'].'" type="hidden">
																	<div class="del-poll-li"><i class="icon-remove"></i></div>
																	<div class="move-poll-li"><i class="icon-fullscreen"></i></div>
																</div>
															</li>';
														}
													}
												}else {
													$out .= '<li id="poll_li_1">
														<div class="poll-li">
															<p><input id="ask[1][title]" class="ask" name="ask[1][title]" value="" type="text"></p>
															<input id="ask[1][value]" name="ask[1][value]" value="" type="hidden">
															<input id="ask[1][id]" name="ask[1][id]" value="1" type="hidden">
															<div class="del-poll-li"><i class="icon-remove"></i></div>
															<div class="move-poll-li"><i class="icon-fullscreen"></i></div>
														</div>
													</li>
													<li id="poll_li_2">
														<div class="poll-li">
															<p><input id="ask[2][title]" class="ask" name="ask[2][title]" value="" type="text"></p>
															<input id="ask[2][value]" name="ask[2][value]" value="" type="hidden">
															<input id="ask[2][id]" name="ask[2][id]" value="2" type="hidden">
															<div class="del-poll-li"><i class="icon-remove"></i></div>
															<div class="move-poll-li"><i class="icon-fullscreen"></i></div>
														</div>
													</li>';
												}
											$out .= '</ul>
											<div class="clearfix"></div>
										</div>';
									}else if ($sort_key == "featured_image" && empty($get_question_user_id) && isset($question_sort["featured_image"]["value"]) && $question_sort["featured_image"]["value"] == "featured_image") {
										$out .= '<div class="featured_image_question">
											<label for="featured_image-'.$rand_e.'">'.__("Featured image","vbegy").'</label>
											<div class="fileinputs">
												<input type="file" class="file" name="featured_image" id="featured_image-'.$rand_e.'">
												<div class="fakefile">
													<button type="button" class="button small margin_0">'.__("Select file","vbegy").'</button>
													<span><i class="icon-arrow-up"></i>'.__("Browse","vbegy").'</span>
												</div>
											</div>
										</div>';
									}else if ($sort_key == "comment_question" && isset($question_sort["comment_question"]["value"]) && $question_sort["comment_question"]["value"] == "comment_question") {
										$out .= '<div class="details-area">
											<label for="question-details-'.$rand_e.'" '.($comment_question == "required"?'class="required"':'').'>'.__("Details","vbegy").($comment_question == "required"?'<span>*</span>':'').'</label>';
											if ($editor_question_details == 1) {
												ob_start();
												$settings = apply_filters('askme_edit_question_editor_setting',$settings);
												wp_editor((isset($posted['comment'])?ask_kses_stip_wpautop($posted['comment']):$get_post_q->post_content),"question-details-".$rand_e,$settings);
												$editor_contents = ob_get_clean();
												$out .= '<div class="the-details the-textarea">'.$editor_contents.'</div>';
											}else {
												$out .= '<textarea name="comment" id="question-details-'.$rand_e.'" class="the-textarea" aria-required="true" cols="58" rows="8">'.(isset($posted['comment'])?ask_kses_stip($posted['comment']):ask_kses_stip($get_post_q->post_content,"yes")).'</textarea>';
											}
											$out .= '<div class="clearfix"></div>
										</div>';
									}else if ($sort_key == "video_desc_active" && empty($get_question_user_id) && isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active") {
										$q_video_description = get_post_meta($get_question,"video_description",true);
										$q_video_type = get_post_meta($get_question,"video_type",true);
										$q_video_id = get_post_meta($get_question,"video_id",true);
										$out .= '<div class="form-inputs clearfix">
											<p class="question_poll_p">
												<label for="video_description-'.$rand_e.'">'.__("Video description","vbegy").'</label>
												<input type="checkbox" id="video_description-'.$rand_e.'" class="video_description_input" name="video_description" value="1" '.(isset($posted['video_description']) && $posted['video_description'] == 1 || $q_video_description == 1?"checked='checked'":"").'>
												<span class="question_poll">'.__("Do you need a video to description the problem better ?","vbegy").'</span>
											</p>
											
											<div class="video_description" '.(isset($posted['video_description']) && $posted['video_description'] == 1 || $q_video_description == 1?"style='display:block;'":"").'>
												<p>
													<label for="video_type-'.$rand_e.'">'.__("Video type","vbegy").'</label>
													<span class="styled-select">
														<select id="video_type-'.$rand_e.'" class="video_type" name="video_type">
															<option value="youtube" '.(isset($posted['video_type']) && $posted['video_type'] == "youtube" || $q_video_type == "youtube"?' selected="selected"':'').'>Youtube</option>
															<option value="vimeo" '.(isset($posted['video_type']) && $posted['video_type'] == "vimeo" || $q_video_type == "vimeo"?' selected="selected"':'').'>Vimeo</option>
															<option value="daily" '.(isset($posted['video_type']) && $posted['video_type'] == "daily" || $q_video_type == "daily"?' selected="selected"':'').'>Dialymotion</option>
															<option value="facebook" '.(isset($posted['video_type']) && $posted['video_type'] == "facebook" || $q_video_type == "facebook"?' selected="selected"':'').'>Facebook</option>
															<option value="tiktok" '.(isset($posted['video_type']) && $posted['video_type'] == "tiktok" || $q_video_type == "tiktok"?' selected="selected"':'').'>TikTok</option>
														</select>
													</span>
													<span class="form-description">'.__("Choose from here the video type .","vbegy").'</span>
												</p>
												
												<p>
													<label for="video_id-'.$rand_e.'">'.__("Video ID","vbegy").'</label>
													<input name="video_id" id="video_id-'.$rand_e.'" class="video_id" type="text" value="'.(isset($posted['video_id'])?$posted['video_id']:$q_video_id).'">
													<span class="form-description">'.__("Put here the video id : https://www.youtube.com/watch?v=sdUUx5FdySs EX : 'sdUUx5FdySs'.","vbegy").'</span>
												</p>
											</div>
										</div>';
									}else if ($sort_key == "remember_answer" && isset($question_sort["remember_answer"]["value"]) && $question_sort["remember_answer"]["value"] == "remember_answer") {
										$q_remember_answer = get_post_meta($get_question,"remember_answer",true);
										$out .= '<p class="question_poll_p">
											<label for="remember_answer-'.$rand_e.'">'.__("Notified","vbegy").'</label>
											<input type="checkbox" id="remember_answer-'.$rand_e.'" class="remember_answer" name="remember_answer" value="1" '.(isset($posted['remember_answer']) && $posted['remember_answer'] == 1 || $q_remember_answer == 1?"checked='checked'":"").'>
											<span class="question_poll">'.__("Notified by e-mail at incoming answers.","vbegy").'</span>
										</p>';
									}else if (is_user_logged_in && $sort_key == "private_question" && isset($question_sort["private_question"]["value"]) && $question_sort["private_question"]["value"] == "private_question") {
										$q_private_question = get_post_meta($get_question,"private_question",true);
										$out .= '<p class="question_poll_p">
											<label for="private_question-'.$rand_e.'">'.__("Private question","vbegy").'</label>
											<input type="checkbox" id="private_question-'.$rand_e.'" class="private_question" name="private_question" value="1" '.(isset($posted['private_question']) && $posted['private_question'] == 1 || $q_private_question == 1?"checked='checked'":"").'>
											<span class="question_poll">'.__("Activate this question as a private question.","vbegy").'</span>
										</p>';
									}
								}
							}
							if (is_user_logged_in() && is_super_admin(get_current_user_id()) && empty($get_question_user_id)) {
								$q_sticky = get_post_meta($get_question,"sticky",true);
								$out .= '<p class="question_poll_p">
									<label for="sticky-'.$rand_e.'">'.esc_html__("Sticky","vbegy").'</label>
									<input type="checkbox" id="sticky-'.$rand_e.'" class="sticky_input" name="sticky" value="sticky"'.(((isset($posted['sticky']) && $posted['sticky'] == "sticky") || (!isset($posted['sticky']) && $q_sticky))?" checked='checked'":"").'>
									<span class="question_poll">'.esc_html__("Stick this question","vbegy").' '.esc_html("Note: this option shows for the admin only!","vbegy").'</span>
								</p>';
							}
						$out .= '</div>
						<p class="form-submit">
							<input type="hidden" name="ID" value="'.$get_question.'">
							<input type="hidden" name="post_type" value="edit_question">
							<input type="submit" value="'.__("Edit Your Question","vbegy").'" class="button color small submit add_qu publish-question">
						</p>
					
					</form>
				</div>
			</div>
		</div></div>';
	}
	return $out;
}?>