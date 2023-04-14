<?php $user_get_current_user_id = get_current_user_id();
$is_super_admin = is_super_admin($user_get_current_user_id);
$custom_permission = askme_options("custom_permission");?>
<div id="question-<?php echo $post->ID;?>" itemprop="mainEntity" itemscope itemtype="https://schema.org/Question">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post();
		$question_vote = get_post_meta($post->ID,"question_vote",true);
		$question_category = wp_get_post_terms($post->ID,ask_question_category,array("fields" => "all"));
		$get_question_user_id = get_post_meta($post->ID,"user_id",true);
		$anonymously_user = get_post_meta($post->ID,'anonymously_user',true);
		$yes_private = ask_private($post->ID,$post->post_author,$user_get_current_user_id);
		$vbegy_what_post = askme_post_meta('vbegy_what_post','select',$post->ID);
		$vbegy_sidebar_all = askme_post_meta('vbegy_sidebar','select',$post->ID);
		
		$the_best_answer = get_post_meta($post->ID,"the_best_answer",true);
		if (isset($the_best_answer) && $the_best_answer != "") {
			$get_comment = get_comment($the_best_answer);
			if (empty($get_comment)) {
				delete_post_meta($post->ID,"the_best_answer");
			}
		}
		
		$question_poll = get_post_meta($post->ID,'question_poll',true);
		$question_type = ($question_poll == 1?" question-type-poll":" question-type-normal");
		$closed_question = get_post_meta($post->ID,"closed_question",true);
		$question_favorites = get_post_meta($post->ID,'question_favorites',true);
		
		$the_author = get_user_by("login",get_the_author());
		$user_login_id_l = get_user_by("id",$post->post_author);
		if ($post->post_author != 0) {
			$user_profile_page = esc_url(add_query_arg("u", $user_login_id_l->user_login,get_page_link(askme_options('user_profile_page'))));
		}
		$private_question_content = askme_options("private_question_content");
		
		if (!$is_super_admin && $yes_private != 1 && $private_question_content != 1) {?>
			<article class="question private-question">
				<p class="question-desc"><?php _e("Sorry it's a private question.","vbegy");?></p>
			</article>
		<?php }else {
			$custom_page_setting = askme_post_meta('vbegy_custom_page_setting','checkbox',$post->ID);
			$post_share_s = askme_post_meta('vbegy_post_share_s','checkbox',$post->ID);
			$post_author_box_s = askme_post_meta('vbegy_post_author_box_s','checkbox',$post->ID);
			$related_post_s = askme_post_meta('vbegy_related_post_s','checkbox',$post->ID);
			$post_comments_s = askme_post_meta('vbegy_post_comments_s','checkbox',$post->ID);
			$post_navigation_s = askme_post_meta('vbegy_post_navigation_s','checkbox',$post->ID);
			$active_reports = askme_options("active_reports");
			$active_logged_reports = askme_options("active_logged_reports");
			$question_type = ($active_reports == 1 && (is_user_logged_in || (!is_user_logged_in && $active_logged_reports != 1))?$question_type:$question_type." no_reports");
			
			$_paid_question = get_post_meta($post->ID, '_paid_question', true);
			
			if (($is_super_admin || ($anonymously_user > 0 && $user_get_current_user_id == $anonymously_user) || ($post->post_author > 0 && $user_get_current_user_id == $post->post_author)) && (isset($_paid_question) && $_paid_question == "paid")) {
				echo '<div class="alert-message info"><i class="icon-ok"></i><p><span>'.__("Paid question","vbegy").'</span><br>'.__("This is a paid question.","vbegy").'</p></div>';
			}
			
			$question_sticky = "";
			$end_sticky_time = get_post_meta($post->ID,"end_sticky_time",true);
			if (is_sticky()) {
				if ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) {
					delete_post_meta($post->ID,"start_sticky_time");
					delete_post_meta($post->ID,"end_sticky_time");
					delete_post_meta($post->ID,'sticky');
					$sticky_questions = get_option('sticky_questions');
					if (is_array($sticky_questions) && in_array($post->ID,$sticky_questions)) {
						$sticky_posts = get_option('sticky_posts');
						$sticky_posts = remove_item_by_value($sticky_posts,$post->ID);
						update_option('sticky_posts',$sticky_posts);
						$sticky_questions = remove_item_by_value($sticky_questions,$post->ID);
						update_option('sticky_questions',$sticky_questions);
					}
				}
				if (($is_super_admin || ($anonymously_user > 0 && $user_get_current_user_id == $anonymously_user) || ($post->post_author > 0 && $user_get_current_user_id == $post->post_author)) && ($end_sticky_time != "" && $end_sticky_time >= strtotime(date("Y-m-d")))) {
					echo '<div class="alert-message success"><i class="icon-ok"></i><p><span>'.__("Sticky time","vbegy").'</span><br>'.__("This question will sticky to","vbegy").': '.date("Y-m-d",$end_sticky_time).'</p></div>';
				}
				$question_sticky = " sticky";
				if ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) {
					$question_sticky = "";
				}
			}else {
				$end_sticky_time = "";
			}
			
			if ($is_super_admin && ((isset($_paid_question) && $_paid_question == "paid") || is_sticky())) {
				if (isset($_paid_question) && $_paid_question == "paid") {
					$item_transaction = get_post_meta($post->ID, 'item_transaction', true);
					$paypal_sandbox = get_post_meta($post->ID, 'paypal_sandbox', true);
				}
				
				if (is_sticky()) {
					$item_transaction_sticky = get_post_meta($post->ID, 'item_transaction_sticky', true);
					$paypal_sandbox_sticky = get_post_meta($post->ID, 'paypal_sandbox_sticky', true);
				}
				
				if ((isset($_paid_question) && $_paid_question == "paid" && ((isset($item_transaction) && $item_transaction != "") || (isset($paypal_sandbox) && $paypal_sandbox != "" && $paypal_sandbox = "sandbox"))) || (is_sticky() && ((isset($item_transaction_sticky) && $item_transaction_sticky != "") || (isset($paypal_sandbox_sticky) && $paypal_sandbox_sticky != "" && $paypal_sandbox_sticky = "sandbox")))) {
					echo '<a href="#" class="paid-details color button small f_left">'.__("Paid details","vbegy").'</a>
					<div class="clearfix"></div>
					<div class="paid-question-area">';
						if (isset($_paid_question) && $_paid_question == "paid") {
							if (isset($item_transaction) && $item_transaction != "") {
								echo '<div class="alert-message warning"><i class="icon-flag"></i><p><span>'.__("Transaction id","vbegy").'</span><br>'.__("The transaction id","vbegy").' : '.$item_transaction.'</p></div>';
							}
							if (isset($paypal_sandbox) && $paypal_sandbox != "" && $paypal_sandbox = "sandbox") {
								echo '<div class="alert-message error"><i class="icon-ok"></i><p><span>'.__("PayPal sandbox","vbegy").'</span><br>'.__("This transaction is from PayPal sandbox.","vbegy").'</p></div>';
							}
						}
						
						if (is_sticky()) {
							if (isset($item_transaction_sticky) && $item_transaction_sticky != "") {
								echo '<div class="alert-message warning"><i class="icon-flag"></i><p><span>'.__("Transaction id","vbegy").'</span><br>'.__("The transaction id for sticky question","vbegy").' : '.$item_transaction_sticky.'</p></div>';
							}
							if (isset($paypal_sandbox_sticky) && $paypal_sandbox_sticky != "" && $paypal_sandbox_sticky = "sandbox") {
								echo '<div class="alert-message error"><i class="icon-ok"></i><p><span>'.__("PayPal sandbox","vbegy").'</span><br>'.__("This transaction is from PayPal sandbox for sticky question.","vbegy").'</p></div>';
							}
						}
					echo '</div>';
				}
			}
			
			if (($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || $is_super_admin) {
				$question_delete = askme_options("question_delete");
				if ($question_delete == 1 || $is_super_admin) {
					if (isset($_GET) && isset($_GET["delete"]) && $_GET["delete"] == $post->ID && isset($post->post_status) && $post->post_status == "publish") {
						$delete_posts_nonce = askme_delete_posts_nonce($post,$_GET);
						if ($delete_posts_nonce == "done") {
							$_SESSION['vbegy_session_all'] = '<div class="alert-message success"><p>'.esc_html__("Has been deleted successfully.","vbegy").'</p></div>';
							$protocol = is_ssl() ? 'https' : 'http';
							$redirect_to = wp_unslash( $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
							$redirect_to = (isset($_GET["page"]) && esc_attr($_GET["page"]) != ""?esc_attr($_GET["page"]):$redirect_to);
							if ( is_ssl() && force_ssl_admin() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )$secure_cookie = false; else $secure_cookie = '';
							wp_redirect(((isset($_GET["page"]) && esc_attr($_GET["page"]) != "") || is_page()?$redirect_to:home_url()));
						}
					}
				}
			}
			
			if (!is_admin() && isset($_GET["delete_comment"]) && $_GET["delete_comment"] != "") {
				$comment_id  = (int)$_GET["delete_comment"];
				$get_comment = get_comment($comment_id);
				if (isset($get_comment) && $comment_id > 0 && isset($get_comment->comment_approved) && $get_comment->comment_approved == 1) {
					$comment_user_id    = $get_comment->user_id;
					$comment_type       = get_comment_meta($comment_id,"comment_type",true);
					$can_delete_comment = askme_options("can_delete_comment");
					$delete_comment     = askme_options("delete_comment");
					if (($comment_user_id > 0 && $comment_user_id == $user_get_current_user_id && $can_delete_comment == 1) || current_user_can('edit_comment',$comment_id) || $is_super_admin) {
						if ($user_get_current_user_id > 0) {
							askme_notifications_activities($user_get_current_user_id,"","","","","delete_".($comment_type == "question"?"answer":"comment"),"activities","",($comment_type == "question"?"answer":"comment"));
						}
						if ($comment_user_id > 0 && $user_get_current_user_id != $comment_user_id) {
							askme_notifications_activities($comment_user_id,"","","","","delete_".($comment_type == "question"?"answer":"comment"),"notifications","",($comment_type == "question"?"answer":"comment"));
						}
						wp_delete_comment($comment_id,($delete_comment == "trash"?false:true));
						$_SESSION['vbegy_session_all'] = '<div class="alert-message success"><p>'.esc_html__("Has been deleted successfully.","vbegy").'</p></div>';
						$protocol    = is_ssl() ? 'https' : 'http';
						$redirect_to = esc_url(remove_query_arg('delete_comment',wp_unslash( $protocol.'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])));
						$redirect_to = (isset($_GET["page"]) && esc_attr($_GET["page"]) != ""?esc_attr($_GET["page"]):$redirect_to);
						wp_redirect($redirect_to);
						exit;
					}
				}
			}?>
			<article <?php post_class('question single-question'.$question_type.$question_sticky);?> id="post-<?php echo $post->ID;?>" <?php do_action("askme_question_attrs",$post->post_author)?>>
				<?php $question_follow = askme_options("question_follow");
				$question_control_style = askme_options("question_control_style");
				$following_questions = get_post_meta($post->ID,"following_questions",true);

				$question_close_admin = askme_options("question_close_admin");
				$question_close = askme_options("question_close");
				if (!$is_super_admin && $question_close == 1 && $question_close_admin == 1) {
					$question_close = "0";
				}
				if ($question_control_style == "style_1" && (($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || (($custom_permission != 1 || ($is_super_admin || (is_user_logged_in && isset($roles["follow_question"]) && $roles["follow_question"] == 1))) && $question_follow == 1 && is_user_logged_in) || ($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || ($anonymously_user != 0 && $anonymously_user == $user_get_current_user_id) || $is_super_admin)) {?>
					<div class="edit-delete-follow-close">
						<h2>
							<?php $question_edit = askme_options("question_edit");
							if (($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || $is_super_admin) {
								if ($question_edit == 1 || $is_super_admin) {?>
									<span class="question-edit">
										<a href="<?php echo esc_url(add_query_arg("q", $post->ID,get_page_link(askme_options('edit_question'))))?>" original-title="<?php _e("Edit the question","vbegy")?>" class="tooltip-n"><i class="icon-edit"></i></a>
									</span>
								<?php }
								if ($question_delete == 1 || $is_super_admin) {?>
									<span class="question-delete">
										<a href="<?php echo esc_url(add_query_arg(array("delete" => $post->ID,"askme_delete_nonce" => wp_create_nonce("askme_delete_nonce")),get_permalink($post->ID)))?>" original-title="<?php _e("Delete the question","vbegy")?>" class="tooltip-n"><i class="icon-remove"></i></a>
									</span>
								<?php }
							}
							if (($custom_permission != 1 || ($is_super_admin || (is_user_logged_in && isset($roles["follow_question"]) && $roles["follow_question"] == 1))) && ($question_follow == 1 || $is_super_admin) && is_user_logged_in && $user_get_current_user_id != $get_question_user_id && (($user_get_current_user_id != $post->post_author) || ($anonymously_user != "" && $anonymously_user != $user_get_current_user_id))) {?>
								<span class="question-follow">
									<?php if (isset($following_questions) && is_array($following_questions) && in_array($user_get_current_user_id,$following_questions)) {?>
										<a href="#" original-title="<?php _e("Unfollow the question","vbegy")?>" class="tooltip-n unfollow-question"><i class="icon-circle-arrow-down"></i></a>
									<?php }else {?>
										<a href="#" original-title="<?php _e("Follow the question","vbegy")?>" class="tooltip-n"><i class="icon-circle-arrow-up"></i></a>
									<?php }?>
								</span>
							<?php }
							if (($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || $is_super_admin) {
								if (isset($question_close) && $question_close == 1) {
									if (isset($closed_question) && $closed_question == 1) {?>
										<span class="question-open">
											<a href="#" original-title="<?php _e("Open the question","vbegy")?>" class="tooltip-n"><i class="icon-unlock"></i></a>
										</span>
									<?php }else {?>
										<span class="question-close">
											<a href="#" original-title="<?php _e("Close the question","vbegy")?>" class="tooltip-n"><i class="icon-lock"></i></a>
										</span>
									<?php }
								}
							}?>
						</h2>
					</div>
				<?php }
				$the_title_filter = apply_filters("askme_single_question_title",true);
				if ($the_title_filter == true) {?>
					<h2>
						<?php if ($question_sticky == " sticky") {
							echo '<i class="icon-pushpin tooltip-n question-sticky" original-title="'.__("Sticky","vbegy").'"></i>';
						}?>
						<span itemprop="name"><?php the_title();?></span>
					</h2>
				<?php }
				if ($active_reports == 1 && (is_user_logged_in || (!is_user_logged_in && $active_logged_reports != 1))) {?>
					<a class="question-report report_q" href="#"><?php _e("Report","vbegy")?></a>
				<?php }
				if ($question_poll == 1) {?>
					<div class="question-type-main"><i class="icon-signal"></i><?php _e("Poll","vbegy")?></div>
				<?php }else {?>
					<div class="question-type-main"><i class="icon-question-sign"></i><?php _e("Question","vbegy")?></div>
				<?php }?>
				<div class="question-inner">
					<div class="clearfix"></div>
					<div class="question-desc">
						<?php
						if ($yes_private != 1 && $private_question_content == 1) {?>
							<p><?php _e("Sorry it's a private question.","vbegy");?></p>
						<?php }else {
							$comments = get_comments('post_id='.$post->ID);
							$show_question = askme_options("show_question");
							if (is_user_logged_in) {
								$user_is_login = get_userdata($user_get_current_user_id);
								$user_login_group = key($user_is_login->caps);
								$roles = $user_is_login->allcaps;
							}
							if (($custom_permission != 1 || ($is_super_admin || (is_user_logged_in && isset($roles["show_question"]) && $roles["show_question"] == 1) || (!is_user_logged_in && $show_question == 1)) || ($user_get_current_user_id > 0 && $user_get_current_user_id == $post->post_author) || ($user_get_current_user_id > 0 && $user_get_current_user_id == $anonymously_user))) {
								$show_content = apply_filters("askme_show_content",true);
								if ($show_content == true) {
									if ($active_reports == 1 && (is_user_logged_in || (!is_user_logged_in && $active_logged_reports != 1))) {?>
										<div class="explain-reported">
											<h3><?php _e("Please briefly explain why you feel this question should be reported.","vbegy")?></h3>
											<textarea name="explain-reported"></textarea>
											<div class="clearfix"></div>
											<div class="loader_3"></div>
											<div class="color button small report"><?php _e("Report","vbegy")?></div>
											<div class="color button small dark_button cancel"><?php _e("Cancel","vbegy")?></div>
										</div><!-- End reported -->
									<?php }
									if ($question_poll == 1) {?>
										<div class='question_poll_end'>
											<div class="alert-message error ask-hide"></div>
											<?php echo askme_show_poll_results($post->ID,$user_get_current_user_id)?>
										</div><!-- End question_poll_end -->
										<div class="clearfix height_20"></div>
										<?php
									}
									$video_description = get_post_meta($post->ID,'video_description',true);
									$question_sort = askme_options("ask_question_items");
									$video_desc_active = (isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active"?1:0);
									if ($video_desc_active == 1 && $video_description == 1) {
										$video_desc = get_post_meta($post->ID,'video_desc',true);
										$video_id = get_post_meta($post->ID,'video_id',true);
										$video_type = get_post_meta($post->ID,'video_type',true);
										if ($video_id != "") {
											$type = askme_video_iframe($video_type,$video_id,"post_meta","video_id",$post->ID);
											if ($vbegy_sidebar_all == "full") {
										    	$las_video = '<div class="question-video video-type-'.$video_type.'"><iframe height="600" src="'.$type.'"></iframe></div>';
											}else {
										    	$las_video = '<div class="question-video video-type-'.$video_type.'"><iframe height="450" src="'.$type.'"></iframe></div>';
											}
											if (askme_options("video_desc") == "before") {
												echo $las_video;
											}
										}
									}
									$featured_image_single = askme_options("featured_image_single");
									if ($featured_image_single == 1 && has_post_thumbnail()) {
										$custom_featured_image_size = askme_post_meta('vbegy_custom_featured_image_size','checkbox',$post->ID);
										if ($custom_featured_image_size == 1) {
											$featured_image_question_width = askme_post_meta('vbegy_featured_image_width','slider',$post->ID);
											$featured_image_question_height = askme_post_meta('vbegy_featured_image_height','slider',$post->ID);
										}else {
											$featured_image_question_width = askme_options("featured_image_question_width");
											$featured_image_question_height = askme_options("featured_image_question_height");
										}
										$featured_image_question_lightbox = askme_options("featured_image_question_lightbox");
										$featured_image_question_width = ($featured_image_question_width != ""?$featured_image_question_width:260);
										$featured_image_question_height = ($featured_image_question_height != ""?$featured_image_question_height:185);
										$img_lightbox = ($featured_image_question_lightbox == 1?"lightbox":false);
										$featured_position = askme_options("featured_position");
										if ($featured_position != "after") {
											echo "<div class='featured_image_question'>".askme_resize_img($featured_image_question_width,$featured_image_question_height,$img_lightbox)."</div>
											<div class='clearfix'></div>";
										}
									}?>
										<div class="content-text" itemprop="text"><?php the_content();?></div>
									
									<?php if ($featured_image_single == 1 && has_post_thumbnail() && $featured_position == "after") {
										echo "<div class='featured_image_question featured_image_after'>".askme_resize_img($featured_image_question_width,$featured_image_question_height,$img_lightbox)."</div>
										<div class='clearfix'></div>";
									}
									if (askme_options("video_desc") == "after" && $video_desc_active == 1 && isset($video_id) && $video_id != "" && $video_description == 1) {
										echo $las_video;
									}
									do_action("askme_after_content",$post->ID);
									if (is_user_logged_in) {
										if ($custom_permission != 1 || ($is_super_admin || (is_user_logged_in && isset($roles["favorite_question"]) && $roles["favorite_question"] == 1))) {
											if ($user_get_current_user_id != $get_question_user_id && (($user_get_current_user_id != $post->post_author && $post->post_author > 0) || ($anonymously_user != "" && $anonymously_user != $user_get_current_user_id))) {
												$user_login_id2 = get_user_by("id",$user_get_current_user_id);
												$_favorites = get_user_meta($user_get_current_user_id,$user_login_id2->user_login."_favorites",true);
												if (isset($_favorites) && is_array($_favorites) && in_array($post->ID,$_favorites)) {?>
													<a class="remove_favorite add_favorite_in color button small" title="<?php _e("Remove the question of my favorites","vbegy")?>" href="#"><?php _e("Remove the question of my favorites","vbegy")?></a>
												<?php }else {?>
													<a class="add_favorite add_favorite_in color button small" title="<?php _e("Add a question to Favorites","vbegy")?>" href="#"><?php _e("Add a question to Favorites","vbegy")?></a>
												<?php }
											}
										}
										$question_bump = askme_options("question_bump");
										$active_points = askme_options("active_points");
										if (empty($comments) && (($user_get_current_user_id == $post->post_author && $post->post_author != 0) || ($user_get_current_user_id == $anonymously_user && $anonymously_user != 0)) && $question_bump == 1 && $active_points == 1) {?>
											<div class="form-style form-style-2 form-add-point">
												<p class="clearfix">
													<input id="input-add-point" name="" type="text" placeholder="<?php _e("Question bump points","vbegy")?>">
													<a class="color button small margin_0 f_left" href="#"><?php _e("Bump","vbegy")?></a>
												</p>
											</div>
										<?php }
										$pay_to_sticky = askme_options("pay_to_sticky");
										if ($pay_to_sticky == 1) {
											if (($end_sticky_time == "" || ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))))) {
												$days_sticky    = (int)askme_options("days_sticky");
												$days_sticky    = ($days_sticky > 0?$days_sticky:7);
												
												$_allow_to_sticky = get_user_meta($user_get_current_user_id,$user_get_current_user_id."_allow_to_sticky",true);
												
												if (isset($_POST["process"]) && $_POST["process"] == "sticky") {
													/* Pay by points */
													if (isset($_POST["points"]) && $_POST["points"] > 0) {
														$points_price = (int)$_POST["points"];
														$points_user = get_user_meta($user_get_current_user_id,"points",true);
														if ($points_price <= $points_user) {
															$current_user = get_user_by("id",$user_get_current_user_id);
															update_post_meta($post->ID,"sticky_points",$points_price);

															$_points = get_user_meta($user_get_current_user_id,$current_user->user_login."_points",true);
															$_points++;
														
															update_user_meta($user_get_current_user_id,$current_user->user_login."_points",$_points);
															add_user_meta($user_get_current_user_id,$current_user->user_login."_points_".$_points,array(date_i18n('Y/m/d',current_time('timestamp')),date_i18n('g:i a',current_time('timestamp')),$points_price,"-","sticky_points","","time" => current_time('timestamp')));

															update_user_meta($user_get_current_user_id,"points",$points_user-$points_price);
															$_SESSION['vbegy_session_all'] = '<div class="alert-message success"><p>'.esc_html__("You have just stickied the question by points.","vbegy").'</p></div>';
														}else {
															$_SESSION['vbegy_session_all'] = '<div class="alert-message error"><p>'.esc_html__("Sorry, you haven't enough points","vbegy").'</p></div>';
															wp_safe_redirect(get_the_permalink($post->ID));
															die();
														}
													}

													update_post_meta($post->ID,'sticky',1);
													if (is_array($sticky_questions)) {
														if (!in_array($post->ID,$sticky_questions)) {
															$array_merge = array_merge($sticky_questions,array($post->ID));
															update_option("sticky_questions",$array_merge);
														}
													}else {
														update_option("sticky_questions",array($post->ID));
													}
													if (is_array($sticky_posts)) {
														if (!in_array($post->ID,$sticky_posts)) {
															$array_merge = array_merge($sticky_posts,array($post->ID));
															update_option("sticky_posts",$array_merge);
														}
													}else {
														update_option("sticky_posts",array($post->ID));
													}
													update_post_meta($post->ID,"start_sticky_time",strtotime(date("Y-m-d")));
													update_post_meta($post->ID,"end_sticky_time",strtotime(date("Y-m-d",strtotime(date("Y-m-d")." +$days_sticky days"))));
													wp_safe_redirect(get_the_permalink());
													die();
												}
												
												if ((($anonymously_user > 0 && $user_get_current_user_id == $anonymously_user) || ($post->post_author > 0 && $user_get_current_user_id == $post->post_author)) && (($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) || (!is_sticky())) && isset($_allow_to_sticky) && (int)$_allow_to_sticky < 1 && $pay_to_sticky == 1) {
													$payment_type_sticky = askme_options("payment_type_sticky");
													echo '<a href="#" class="pay-to-sticky color button small f_left">'.__("Pay to sticky question","vbegy").'</a>
													<div class="clearfix"></div>
													<div class="pay-to-sticky-area">'.
														askme_payment_form("pay_sticky",$payment_type_sticky,$_POST,$post->ID,$days_sticky).
													'</div>';
												}
											}
										}
									}?>
									<div class="clearfix"></div>
									<div class="loader_2"></div>
									<?php
									$added_file = get_post_meta($post->ID, 'added_file', true);
									if ($added_file != "") {
										echo "<div class='clearfix'></div><br><a class='attachment-link' href='".wp_get_attachment_url($added_file)."'><i class='icon-link'></i>".__("Attachment","vbegy")."</a>";
									}
									$attachment_m = get_post_meta($post->ID, 'attachment_m',true);
									if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
										foreach ($attachment_m as $key => $value) {
											echo "<div class='clearfix'></div><br><a class='attachment-link' href='".wp_get_attachment_url($value["added_file"])."'><i class='icon-link'></i>".__("Attachment","vbegy")."</a>";
										}
									}?>
									<div class='clearfix'></div>
									
									<?php if ($question_control_style == "style_2" && ($is_super_admin || ($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || ($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || (($custom_permission != 1 || ($is_super_admin || (is_user_logged_in && isset($roles["follow_question"]) && $roles["follow_question"] == 1))) && $question_follow == 1 && is_user_logged_in && $user_get_current_user_id != $get_question_user_id && (($user_get_current_user_id != $post->post_author) || ($anonymously_user != "" && $anonymously_user != $user_get_current_user_id))))) {?>
										<div class="edit-delete-follow-close-2">
											<?php $question_edit = askme_options("question_edit");
											if (($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || $is_super_admin) {
												if ($question_edit == 1 || $is_super_admin) {?>
													<div class="question-edit">
														<a href="<?php echo esc_url(add_query_arg("q", $post->ID,get_page_link(askme_options('edit_question'))))?>" original-title="<?php _e("Edit the question","vbegy")?>" class="tooltip-n color button small margin_0 f_left"><?php _e("Edit","vbegy")?></a>
													</div>
												<?php }
												if ($question_delete == 1 || $is_super_admin) {?>
													<div class="question-delete">
														<a href="<?php echo esc_url(add_query_arg(array("delete" => $post->ID,"askme_delete_nonce" => wp_create_nonce("askme_delete_nonce")),get_permalink($post->ID)))?>" original-title="<?php _e("Delete the question","vbegy")?>" class="tooltip-n color button small margin_0 f_left"><?php _e("Delete","vbegy")?></a>
													</div>
												<?php }
											}
											
											if ($custom_permission != 1 || ($is_super_admin || (is_user_logged_in && isset($roles["follow_question"]) && $roles["follow_question"] == 1)) && ($question_follow == 1 || $is_super_admin) && is_user_logged_in && $user_get_current_user_id != $get_question_user_id && (($user_get_current_user_id != $post->post_author) || ($anonymously_user != "" && $anonymously_user != $user_get_current_user_id))) {?>
												<div class="question-follow">
													<?php if (isset($following_questions) && is_array($following_questions) && in_array($user_get_current_user_id,$following_questions)) {?>
														<a href="#" original-title="<?php _e("Unfollow the question","vbegy")?>" class="tooltip-n unfollow-question color button small margin_0 f_left"><?php _e("Unfollow","vbegy")?></a>
													<?php }else {?>
														<a href="#" original-title="<?php _e("Follow the question","vbegy")?>" class="tooltip-n color button small margin_0 f_left"><?php _e("Follow","vbegy")?></a>
													<?php }?>
												</div>
											<?php }
											if (($post->post_author != 0 && $post->post_author == $user_get_current_user_id) || $is_super_admin) {
												if (isset($question_close) && $question_close == 1) {
													if (isset($closed_question) && $closed_question == 1) {?>
														<div class="question-open">
															<a href="#" original-title="<?php _e("Open the question","vbegy")?>" class="tooltip-n color button small margin_0 f_left"><?php _e("Open","vbegy")?></a>
														</div>
													<?php }else {?>
														<div class="question-close">
															<a href="#" original-title="<?php _e("Close the question","vbegy")?>" class="tooltip-n color button small margin_0 f_left"><?php _e("Close","vbegy")?></a>
														</div>
													<?php }
												}
											}?>
											<div class="clearfix"></div>
										</div>
									<?php }?>
									<div class="no_vote_more"></div>
								<?php }else {
									do_action("askme_close_the_content");
								}
							}else {
								echo '<div class="note_error"><strong>'.__("Sorry do not have permission to view questions!","vbegy").'</strong></div>';
							}
						}?>
					</div>
					<?php do_action("askme_before_end_meta");?>
					<div class="footer-question-meta">
						<div class="question-meta-first">
							<?php $questions_meta = askme_options("questions_meta_single");
							if (isset($questions_meta["status"]) && $questions_meta["status"] == 1) {?>
								<div class="question-details">
									<?php if (isset($the_best_answer) && $the_best_answer != "" && $comments) {?>
										<span class="question-answered question-answered-done"><i class="icon-ok"></i><?php _e("solved","vbegy")?></span>
									<?php }else if (isset($closed_question) && $closed_question == 1) {?>
										<span class="question-answered question-closed"><i class="icon-lock"></i><?php _e("closed","vbegy")?></span>
									<?php }else if ($the_best_answer == "" && $comments) {?>
										<span class="question-answered"><i class="icon-ok"></i><?php _e("in progress","vbegy")?></span>
									<?php }?>
									<span class="question-favorite"><i class="<?php echo ($question_favorites > 0?"icon-star":"icon-star-empty");?>"></i><?php echo ($question_favorites != ""?$question_favorites:0);?></span>
								</div>
							<?php }
							if (isset($questions_meta["category"]) && $questions_meta["category"] == 1) {
								echo get_the_term_list($post->ID,ask_question_category,'<span class="question-category"><i class="fa fa-folder"></i>',', ','</span>');
							}
							if (isset($questions_meta["user_name"]) && $questions_meta["user_name"] == 1) {?>
								<span class="question-author-meta">
									<?php if ($post->post_author == 0) {
										$anonymously_question = get_post_meta($post->ID,'anonymously_question',true);
										if ($anonymously_question == 1 && $anonymously_user != "") {
											$question_username = esc_html__("Anonymous","vbegy");
											$question_email = 0;
										}else {
											$question_username = get_post_meta($post->ID, 'question_username',true);
											$question_email = get_post_meta($post->ID, 'question_email',true);
											$question_username = ($question_username != ""?$question_username:esc_html__("Anonymous","vbegy"));
											$question_email = ($question_email != ""?$question_email:0);
										}?>
										<i class="icon-user"></i><span><?php echo $question_username?></span>
									<?php }else {?>
										<a href="<?php echo vpanel_get_user_url($post->post_author)?>"><i class="icon-user"></i><?php echo get_the_author()?></a>
									<?php }?>
								</span>
								<?php if ($get_question_user_id != "") {
									$display_name = get_the_author_meta('display_name',$get_question_user_id);
									if (isset($display_name) && $display_name != "") {?>
										<span class="question-author-meta"><a href="<?php echo vpanel_get_user_url($get_question_user_id);?>" title="<?php echo esc_attr($display_name)?>"><i class="icon-user"></i><?php echo esc_html__("Asked to","vbegy")." : ".esc_attr($display_name)?></a></span>
									<?php }
								}
							}
							if (isset($questions_meta["date"]) && $questions_meta["date"] == 1) {?>
								<span class="question-date"><i class="fa fa-calendar"></i><?php echo '<a href="'.get_the_permalink($post->ID).'" itemprop="url">'.human_time_diff(get_the_time('U'), current_time('timestamp')).'</a>';?></span>
								<?php $get_the_time = get_the_time('c',$post->ID);
								$puplished_date = ($get_the_time?$get_the_time:get_the_modified_date('c',$post->ID));
								echo '<span class="ask-hide" itemprop="dateCreated" datetime="'.$puplished_date.'">'.$puplished_date.'</span>
								<span class="ask-hide" itemprop="datePublished" datetime="'.$puplished_date.'">'.$puplished_date.'</span>';
							}
							if (isset($questions_meta["answer_meta"]) && $questions_meta["answer_meta"] == 1) {
								$count_post_all = (int)askme_count_comments($post->ID);?>
								<span class="question-comment"><a href="<?php echo comments_link()?>"><i class="fa fa-comments"></i><span itemprop="answerCount"><?php echo (int)$count_post_all;?></span> <?php echo _n("Answer","Answers",$count_post_all,"vbegy")?></a></span>
							<?php }
							if (isset($questions_meta["view"]) && $questions_meta["view"] == 1) {?>
								<span class="question-view"><i class="icon-eye-open"></i><?php echo (int)get_post_meta($post->ID,askme_get_meta_stats(),true)?> <?php _e("views","vbegy");?></span>
							<?php }
							if (isset($post->post_author) && $post->post_author > 0) {
								echo vpanel_get_badge($post->post_author);
							}
							do_action("askme_after_end_meta",$post);?>
						</div>
						<?php $active_vote = askme_options("active_vote");
						$active_vote_unlogged = askme_options("active_vote_unlogged");
						$show_dislike_questions = askme_options("show_dislike_questions");
						if ($active_vote == 1) {?>
							<div class="question-meta-vote">
								<ul class="single-question-vote">
									<?php if ((is_user_logged_in && $post->post_author != $user_get_current_user_id && $anonymously_user != $user_get_current_user_id) || (!is_user_logged_in && $active_vote_unlogged == 1)) {?>
										<li><a href="#" id="question_vote_up-<?php echo $post->ID?>" class="single-question-vote-up ask_vote_up question_vote_up vote_allow<?php echo (isset($_COOKIE[askme_options("uniqid_cookie").'question_vote'.$post->ID])?" ".$_COOKIE[askme_options("uniqid_cookie").'question_vote'.$post->ID]."-".$post->ID:"")?> tooltip_s" title="<?php _e("Like","vbegy");?>"><i class="icon-thumbs-up"></i></a></li>
										<?php if ($show_dislike_questions != 1) {?>
											<li><a href="#" id="question_vote_down-<?php echo $post->ID?>" class="single-question-vote-down ask_vote_down question_vote_down vote_allow<?php echo (isset($_COOKIE[askme_options("uniqid_cookie").'question_vote'.$post->ID])?" ".$_COOKIE[askme_options("uniqid_cookie").'question_vote'.$post->ID]."-".$post->ID:"")?> tooltip_s" title="<?php _e("Dislike","vbegy");?>"><i class="icon-thumbs-down"></i></a></li>
										<?php }
									}else {?>
										<li><a href="#" class="single-question-vote-up ask_vote_up question_vote_up <?php echo (is_user_logged_in && (($post->post_author == $user_get_current_user_id) || ($anonymously_user == $user_get_current_user_id))?"vote_not_allow":"vote_not_user")?> tooltip_s" original-title="<?php _e("Like","vbegy");?>"><i class="icon-thumbs-up"></i></a></li>
										<?php if ($show_dislike_questions != 1) {?>
											<li><a href="#" class="single-question-vote-down ask_vote_down question_vote_down <?php echo (is_user_logged_in && (($post->post_author == $user_get_current_user_id) || ($anonymously_user == $user_get_current_user_id))?"vote_not_allow":"vote_not_user")?> tooltip_s" original-title="<?php _e("Dislike","vbegy");?>"><i class="icon-thumbs-down"></i></a></li>
										<?php }
									}?>
								</ul>
								<span itemprop="upvoteCount" class="single-question-vote-result question_vote_result"><?php echo ($question_vote != ""?$question_vote:0)?></span>
							</div>
						<?php }?>
					</div>
				</div>
			</article>
			
			<?php do_action("askme_action_after_post_content",$post->ID,$post->post_author);
			$terms = wp_get_object_terms( $post->ID, ask_question_tags );
			$post_share = askme_options("question_share");
			if ($terms || (($post_share == 1 && $post_share_s == "") || ($post_share == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($post_share == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_share_s) && $post_share_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_share_s) && $post_share_s == 1))) {?>
				<div class="share-tags page-content">
					<?php
					if ($terms) :
						echo '<div class="question-tags"><i class="icon-tags"></i>';
							$terms_array = array();
							foreach ($terms as $term) :
								$terms_array[] = '<a href="'.get_term_link($term->slug, ask_question_tags).'">'.$term->name.'</a>';
							endforeach;
							echo implode(' , ', $terms_array);
						echo '</div>';
					endif;
					
					if (($post_share == 1 && $post_share_s == "") || ($post_share == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($post_share == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_share_s) && $post_share_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_share_s) && $post_share_s == 1)) {
						$url = urlencode(get_permalink());
						$title = urlencode(get_the_title($post->ID));?>
						<div class="share-inside-warp">
							<ul>
								<li>
									<a href="https://www.facebook.com/sharer.php?u=<?php echo ($url);?>" target="_blank">
										<span class="icon_i">
											<span class="icon_square" icon_size="20" span_bg="#3b5997" span_hover="#666">
												<i i_color="#FFF" class="social_icon-facebook"></i>
											</span>
										</span>
									</a>
									<a href="https://www.facebook.com/sharer.php?u=<?php echo ($url)?>" target="_blank"><?php _e("Facebook","vbegy");?></a>
								</li>
								<li>
									<a href="http://twitter.com/share?text=<?php echo ($title);?>&amp;url=<?php echo ($url)?>" target="_blank">
										<span class="icon_i">
											<span class="icon_square" icon_size="20" span_bg="#00baf0" span_hover="#666">
												<i i_color="#FFF" class="social_icon-twitter"></i>
											</span>
										</span>
									</a>
									<a target="_blank" href="http://twitter.com/share?text=<?php echo ($title);?>&amp;url=<?php echo ($url)?>"><?php _e("Twitter","vbegy");?></a>
								</li>
								<li>
									<a href="https://www.tumblr.com/share/link?url=<?php echo ($url)?>&amp;name=<?php echo ($title)?>" target="_blank">
										<span class="icon_i">
											<span class="icon_square" icon_size="20" span_bg="#44546b" span_hover="#666">
												<i i_color="#FFF" class="social_icon-tumblr"></i>
											</span>
										</span>
									</a>
									<a href="https://www.tumblr.com/share/link?url=<?php echo ($url)?>&amp;name=<?php echo ($title)?>" target="_blank"><?php _e("Tumblr","vbegy");?></a>
								</li>
								<?php $pinterestimage = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'full' );?>
								<li>
									<a target="_blank" href="https://pinterest.com/pin/create/button/?url=<?php echo ($url)?>&media=<?php echo (isset($pinterestimage[0])?$pinterestimage[0]:""); ?>&description=<?php the_title(); ?>">
										<span class="icon_i">
											<span class="icon_square" icon_size="20" span_bg="#c7151a" span_hover="#666">
												<i i_color="#FFF" class="icon-pinterest"></i>
											</span>
										</span>
									</a>
									<a href="https://pinterest.com/pin/create/button/?url=<?php echo ($url)?>&media=<?php echo (isset($pinterestimage[0])?$pinterestimage[0]:""); ?>&description=<?php the_title(); ?>" target="_blank"><?php _e("Pinterest","vbegy");?></a>
								</li>
								<li>
									<a target="_blank" href="https://api.whatsapp.com/send?text=<?php echo ($title)?> - <?php echo ($url)?>">
										<span class="icon_i">
											<span class="icon_square" icon_size="20" span_bg="#25D366" span_hover="#666">
												<i i_color="#FFF" class="fa-brands fa-whatsapp"></i>
											</span>
										</span>
									</a>
									<a href="https://api.whatsapp.com/send?text=<?php echo ($title)?> - <?php echo ($url)?>" target="_blank"><?php _e("WhatsApp","vbegy");?></a>
								</li>
								<li>
									<a target="_blank" onClick="popup = window.open('mailto:?subject=<?php the_title(); ?>&amp;body=<?php the_permalink(); ?>', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false" href="#">
										<span class="icon_i">
											<span class="icon_square" icon_size="20" span_bg="#000" span_hover="#666">
												<i i_color="#FFF" class="social_icon-email"></i>
											</span>
										</span>
									</a>
									<a target="_blank" onClick="popup = window.open('mailto:?subject=<?php the_title(); ?>&amp;body=<?php the_permalink(); ?>', 'PopupPage', 'height=450,width=500,scrollbars=yes,resizable=yes'); return false" href="#"><?php _e("Email","vbegy");?></a>
								</li>
								<?php do_action("askme_after_share",$post->ID)?>
							</ul>
							<span class="share-inside-f-arrow"></span>
							<span class="share-inside-l-arrow"></span>
						</div><!-- End share-inside-warp -->
						<div class="share-inside"><i class="icon-share-alt"></i><?php _e("Share","vbegy");?></div>
					<?php }?>
					<div class="clearfix"></div>
				</div><!-- End share-tags -->
			<?php }
		}
	endwhile; endif;
	
	if (!$is_super_admin && $yes_private != 1) {
	
	}else {
		$vbegy_custom_sections = get_post_meta($post->ID,"vbegy_custom_sections",true);
		if (isset($vbegy_custom_sections) && $vbegy_custom_sections == 1) {
			$order_sections = get_post_meta($post->ID,"vbegy_order_sections",true);
		}else {
			$order_sections = askme_options("question_order_sections");
		}
		if (empty($order_sections)) {
			$order_sections = array(
				array("name" => esc_html__('Advertising','vbegy'),"value" => "advertising","default" => "yes"),
				array("name" => esc_html__('About the author','vbegy'),"value" => "author","default" => "yes"),
				array("name" => esc_html__('Related articles','vbegy'),"value" => "related","default" => "yes"),
				array("name" => esc_html__('Advertising 2','vbegy'),"value" => "advertising_2","default" => "yes"),
				array("name" => esc_html__('Comments','vbegy'),"value" => "comments","default" => "yes"),
				array("name" => esc_html__('Next and Previous articles','vbegy'),"value" => "next_previous","default" => "yes"),
			);
		}
		if (is_array($order_sections) && !empty($order_sections)) {
			foreach ($order_sections as $key_r => $value_r) {
				if (isset($value_r["value"]) && $value_r["value"] == "advertising") {
					$vbegy_share_adv_type = askme_post_meta('vbegy_share_adv_type','radio',$post->ID);
					$vbegy_share_adv_link = askme_post_meta('vbegy_share_adv_link','radio',$post->ID);
					$vbegy_share_adv_code = askme_post_meta('vbegy_share_adv_code','textarea',$post->ID);
					$vbegy_share_adv_href = askme_post_meta('vbegy_share_adv_href','text',$post->ID);
					$vbegy_share_adv_img = askme_post_meta('vbegy_share_adv_img','upload',$post->ID);
					
					if ((is_single() || is_page()) && (($vbegy_share_adv_type == "display_code" && $vbegy_share_adv_code != "") || ($vbegy_share_adv_type == "custom_image" && $vbegy_share_adv_img != ""))) {
						$share_adv_type = $vbegy_share_adv_type;
						$share_adv_link = $vbegy_share_adv_link;
						$share_adv_code = $vbegy_share_adv_code;
						$share_adv_href = $vbegy_share_adv_href;
						$share_adv_img = $vbegy_share_adv_img;
					}else {
						$share_adv_type = askme_options("share_adv_type");
						$share_adv_link = askme_options("share_adv_link");
						$share_adv_code = askme_options("share_adv_code");
						$share_adv_href = askme_options("share_adv_href");
						$share_adv_img = askme_options("share_adv_img");
					}
					if (($share_adv_type == "display_code" && $share_adv_code != "") || ($share_adv_type == "custom_image" && $share_adv_img != "")) {
						echo '<div class="clearfix"></div>
						<div class="advertising advertising-share">';
						if ($share_adv_type == "display_code") {
							echo do_shortcode(stripslashes($share_adv_code));
						}else {
							if ($share_adv_href != "") {
								echo '<a'.($share_adv_link == "new_page"?" target='_blank'":"").' href="'.$share_adv_href.'">';
							}
							echo '<img alt="" src="'.$share_adv_img.'">';
							if ($share_adv_href != "") {
								echo '</a>';
							}
						}
						echo '</div><!-- End advertising -->
						<div class="clearfix"></div>';
					}
				}else if (isset($value_r["value"]) && $value_r["value"] == "advertising_2") {
					$vbegy_related_adv_type = askme_post_meta('vbegy_related_adv_type','radio',$post->ID);
					$vbegy_related_adv_link = askme_post_meta('vbegy_related_adv_link','radio',$post->ID);
					$vbegy_related_adv_code = askme_post_meta('vbegy_related_adv_code','textarea',$post->ID);
					$vbegy_related_adv_href = askme_post_meta('vbegy_related_adv_href','text',$post->ID);
					$vbegy_related_adv_img = askme_post_meta('vbegy_related_adv_img','upload',$post->ID);
					
					if ((is_single() || is_page()) && (($vbegy_related_adv_type == "display_code" && $vbegy_related_adv_code != "") || ($vbegy_related_adv_type == "custom_image" && $vbegy_related_adv_img != ""))) {
						$related_adv_type = $vbegy_related_adv_type;
						$related_adv_link = $vbegy_related_adv_link;
						$related_adv_code = $vbegy_related_adv_code;
						$related_adv_href = $vbegy_related_adv_href;
						$related_adv_img = $vbegy_related_adv_img;
					}else {
						$related_adv_type = askme_options("related_adv_type");
						$related_adv_link = askme_options("related_adv_link");
						$related_adv_code = askme_options("related_adv_code");
						$related_adv_href = askme_options("related_adv_href");
						$related_adv_img = askme_options("related_adv_img");
					}
					if (($related_adv_type == "display_code" && $related_adv_code != "") || ($related_adv_type == "custom_image" && $related_adv_img != "")) {
						echo '<div class="clearfix"></div>
						<div class="advertising advertising-related">';
						if ($related_adv_type == "display_code") {
							echo do_shortcode(stripslashes($related_adv_code));
						}else {
							if ($related_adv_href != "") {
								echo '<a'.($related_adv_link == "new_page"?" target='_blank'":"").' href="'.$related_adv_href.'">';
							}
							echo '<img alt="" src="'.$related_adv_img.'">';
							if ($related_adv_href != "") {
								echo '</a>';
							}
						}
						echo '</div><!-- End advertising -->
						<div class="clearfix"></div>';
					}
				}else if (isset($value_r["value"]) && $value_r["value"] == "author") {
					$post_author_box = askme_options("question_author_box");
					if (($post_author_box == 1 && $post_author_box_s == "") || ($post_author_box == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($post_author_box == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_author_box_s) && $post_author_box_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_author_box_s) && $post_author_box_s == 1)) {
						if ($post->post_author > 0) {
							$twitter = get_the_author_meta('twitter',$post->post_author);
							$facebook = get_the_author_meta('facebook',$post->post_author);
							$tiktok = get_the_author_meta('tiktok',$post->post_author);
							$linkedin = get_the_author_meta('linkedin',$post->post_author);
							$follow_email = get_the_author_meta('follow_email',$post->post_author);
							$youtube = get_the_author_meta('youtube',$post->post_author);
							$pinterest = get_the_author_meta('pinterest',$post->post_author);
							$instagram = get_the_author_meta('instagram',$post->post_author);
							$verified_user = get_the_author_meta('verified_user',$post->post_author);?>
							<div itemprop="author" itemscope itemtype="https://schema.org/Person" class="about-author clearfix">
								<span itemprop="name" class="hide"><?php echo $authordata->display_name?></span>
							    <div class="author-image">
							    	<a href="<?php echo vpanel_get_user_url($post->post_author,$authordata->nickname);?>" original-title="<?php the_author();?>" class="tooltip-n">
							    		<?php echo askme_user_avatar(get_the_author_meta(askme_avatar_name(),$post->post_author),65,65,$post->post_author,$authordata->display_name,null,true);?>
							    	</a>
							    </div>
							    <div class="author-bio">
							        <h4>
							        	<?php echo __("About","vbegy")." <a href='".vpanel_get_user_url($post->post_author,$authordata->nickname)."'>".get_the_author()."</a>";
							        	if ($verified_user == 1) {
							        		echo '<img class="verified_user tooltip-n" alt="'.__("Verified","vbegy").'" original-title="'.__("Verified","vbegy").'" src="'.get_template_directory_uri().'/images/verified.png">';
							        	}
							        	echo vpanel_get_badge($post->post_author);?>
							        </h4>
							        <?php the_author_meta('description');?>
							        <div class="clearfix"></div>
							        <?php if ($facebook || $tiktok || $twitter || $linkedin || $follow_email || $youtube || $pinterest || $instagram) { ?>
							        	<br>
							        	<span class="user-follow-me"><?php _e("Follow Me","vbegy")?></span>
							        	<div class="social_icons_display_2">
								        	<?php if ($facebook) {?>
									        	<a href="<?php echo $facebook?>" original-title="<?php _e("Facebook","vbegy")?>" class="tooltip-n">
									        		<span class="icon_i">
									        			<span class="icon_square" icon_size="30" span_bg="#3b5997" span_hover="#2f3239">
									        				<i class="social_icon-facebook"></i>
									        			</span>
									        		</span>
									        	</a>
								        	<?php }
								        	if ($twitter) {?>
									        	<a href="<?php echo $twitter?>" original-title="<?php _e("Twitter","vbegy")?>" class="tooltip-n">
									        		<span class="icon_i">
									        			<span class="icon_square" icon_size="30" span_bg="#00baf0" span_hover="#2f3239">
									        				<i class="social_icon-twitter"></i>
									        			</span>
									        		</span>
									        	</a>
								        	<?php }
								        	if ($tiktok) {?>
									        	<a href="<?php echo $tiktok?>" original-title="<?php _e("TikTok","vbegy")?>" class="tooltip-n">
									        		<span class="icon_i">
									        			<span class="icon_square" icon_size="30" span_bg="#3b5997" span_hover="#2f3239">
									        				<i class="fab fa-tiktok"></i>
									        			</span>
									        		</span>
									        	</a>
								        	<?php }
								        	if ($linkedin) {?>
									        	<a href="<?php echo $linkedin?>" original-title="<?php _e("Linkedin","vbegy")?>" class="tooltip-n">
									        		<span class="icon_i">
									        			<span class="icon_square" icon_size="30" span_bg="#006599" span_hover="#2f3239">
									        				<i class="social_icon-linkedin"></i>
									        			</span>
									        		</span>
									        	</a>
								        	<?php }
								        	if ($pinterest) {?>
									        	<a href="<?php echo $pinterest?>" original-title="<?php _e("Pinterest","vbegy")?>" class="tooltip-n">
									        		<span class="icon_i">
									        			<span class="icon_square" icon_size="30" span_bg="#e13138" span_hover="#2f3239">
									        				<i class="social_icon-pinterest"></i>
									        			</span>
									        		</span>
									        	</a>
								        	<?php }
								        	if ($instagram) {?>
									        	<a href="<?php echo $instagram?>" original-title="<?php _e("Instagram","vbegy")?>" class="tooltip-n">
									        		<span class="icon_i">
									        			<span class="icon_square" icon_size="30" span_bg="#548bb6" span_hover="#2f3239">
									        				<i class="social_icon-instagram"></i>
									        			</span>
									        		</span>
									        	</a>
								        	<?php }
								        	if ($follow_email) {?>
									        	<a href="mailto:<?php echo $authordata->user_email?>" original-title="<?php _e("Email","vbegy")?>" class="tooltip-n">
									        		<span class="icon_i">
									        			<span class="icon_square" icon_size="30" span_bg="#000" span_hover="#2f3239">
									        				<i class="social_icon-email"></i>
									        			</span>
									        		</span>
									        	</a>
								        	<?php }?>
							        	</div>
							        <?php }?>
							    </div>
							</div><!-- End about-author -->
						<?php }
					}
				}else if (isset($value_r["value"]) && $value_r["value"] == "related") {
					$related_post = askme_options("related_question");
					if (($related_post == 1 && $related_post_s == "") || ($related_post == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($related_post == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($related_post_s) && $related_post_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($related_post_s) && $related_post_s == 1)) {
						$related_no = askme_options('related_number_question') ? askme_options('related_number_question') : 5;
						global $post;
						$orig_post = $post;
						$related_query_ = array();
						$related_cat_tag = askme_options("related_query_question");
						
						if ($related_cat_tag == "tags") {
							$term_list = wp_get_post_terms($post->ID, ask_question_tags, array("fields" => "ids"));
							$related_query_ = array('tax_query' => array(array('taxonomy' => ask_question_tags,'field' => 'id','terms' => $term_list,'operator' => 'IN')));
						}else {
							$categories = wp_get_post_terms($post->ID,ask_question_category,array("fields" => "ids"));
							$related_query_ = array('tax_query' => array(array('taxonomy' => ask_question_category,'field' => 'id','terms' => $categories,'operator' => 'IN')));
						}
						$author__not_in = array();
				    	$block_users = askme_options("block_users");
						if ($block_users == 1) {
							if ($user_get_current_user_id > 0) {
								$get_block_users = get_user_meta($user_get_current_user_id,"askme_block_users",true);
								if (is_array($get_block_users) && !empty($get_block_users)) {
									$author__not_in = array("author__not_in" => $get_block_users);
								}
							}
						}
						
						$args = array_merge($author__not_in,$related_query_,array('post_type' => $post->post_type,'post__not_in' => array($post->ID),'posts_per_page'=> $related_no));
						$related_query = new wp_query( $args );
						if ($related_query->have_posts()) : ;?>
							<div id="related-posts">
								<h2><?php _e("Related questions","vbegy");?></h2>
								<ul class="related-posts">
									<?php while ( $related_query->have_posts() ) : $related_query->the_post()?>
										<li class="related-item"><h3><a  href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>"><i class="icon-double-angle-right"></i><?php the_title();?></a></h3></li>
									<?php endwhile;?>
								</ul>
							</div><!-- End related-posts -->
						<?php endif;
						$post = $orig_post;
						wp_reset_postdata();
					}
				}else if (isset($value_r["value"]) && $value_r["value"] == "comments") {
					$post_comments = askme_options("question_answers");
					if (($post_comments == 1 && $post_comments_s == "") || ($post_comments == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($post_comments == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_comments_s) && $post_comments_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_comments_s) && $post_comments_s == 1)) {
						comments_template("/question-comments.php");
					}
				}else if (isset($value_r["value"]) && $value_r["value"] == "next_previous") {
					$post_navigation = askme_options("question_navigation");
					if (($post_navigation == 1 && $post_navigation_s == "") || ($post_navigation == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($post_navigation == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_navigation_s) && $post_navigation_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_navigation_s) && $post_navigation_s == 1)) {
						$question_nav_category = askme_options("question_nav_category");?>
						<div class="post-next-prev clearfix">
						    <p class="prev-post">
						        <?php if ($question_nav_category == 1) {
						        	previous_post_link('%link','<i class="icon-double-angle-left"></i>'.__('&nbsp;Previous question','vbegy'),true,'',ask_question_category);
						        }else {
						        	previous_post_link('%link','<i class="icon-double-angle-left"></i>'.__('&nbsp;Previous question','vbegy'));
						        }?>
						    </p>
						    <p class="next-post">
						    	<?php if ($question_nav_category == 1) {
						    		next_post_link('%link',__('Next question&nbsp;','vbegy').'<i class="icon-double-angle-right"></i>',true,'',ask_question_category);
						    	}else {
						    		next_post_link('%link',__('Next question&nbsp;','vbegy').'<i class="icon-double-angle-right"></i>');
						    	}?>
						    </p>
						</div><!-- End post-next-prev -->
					<?php }
				}
			}
		}
	}?>
</div>