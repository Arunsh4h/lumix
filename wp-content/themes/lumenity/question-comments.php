<?php
global $question_category,$authordata,$closed_question;
if (isset($question_category[0])) {
	$askme_special = get_term_meta($question_category[0]->term_id,"vbegy_special",true);
	$askme_new = get_term_meta($question_category[0]->term_id,"vbegy_new",true);
}
$askme_special = (isset($askme_special)?$askme_special:"");
$askme_new = (isset($askme_new)?$askme_new:"");
$user_get_current_user_id = get_current_user_id();
$custom_permission = askme_options("custom_permission");
if (is_user_logged_in) {
	$user_is_login = get_userdata($user_get_current_user_id);
	$user_login_group = key($user_is_login->caps);
	$roles = $user_is_login->allcaps;
}
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) :
	die (__('Please do not load this page directly. Thanks!','vbegy'));
endif;
echo '<div id="comments"></div>';
if ( post_password_required() ) :
    ?><p class="no-comments"><?php _e("This question is password protected. Enter the password to view answers.","vbegy");?></p><?php
    return;
endif;
$count_post_all = (int)askme_count_comments($post->ID);
if ($count_post_all == 0) {
	$get_comments_args = array('post_id' => $post->ID,'status' => 'approve');
	$comments_args = get_comments($get_comments_args);
	askme_update_comments_count($post->ID);
	$count_post_all = (int)askme_count_comments($post->ID);
}
if ( have_comments() && $count_post_all > 0 ) :?>
	<div id="commentlist" class="page-content <?php if (is_page()) {echo "no_comment_box";}?>">
		<div class="boxedtitle page-title"><h2><?php comments_number(__('Answers','vbegy'),__('Answer','vbegy'), __('Answers','vbegy'));?> ( <span class="color"><?php echo sprintf("%s",$count_post_all);?></span> )</h2></div>
		<?php 
		$show_answer = askme_options("show_answer");
		if ($custom_permission != 1 || is_super_admin($user_get_current_user_id) || ($custom_permission == 1 && (is_user_logged_in && isset($roles["show_answer"]) && $roles["show_answer"] == 1) || (!is_user_logged_in && $show_answer == 1))) {
			$k = 1;?>
			<ol class="commentlist clearfix">
				<?php $answers_sort = askme_options("answers_sort");
				if ($user_get_current_user_id > 0) {
					$include_unapproved = array($user_get_current_user_id);
				}else {
					$unapproved_email = wp_get_unapproved_comment_author_email();
					if ($unapproved_email) {
						$include_unapproved = array($unapproved_email);
					}
				}
				$include_unapproved_args = (isset($include_unapproved)?array('include_unapproved' => $include_unapproved):array());
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
				if ($answers_sort == "vote") {
					$comments_args = get_comments(array_merge($author__not_in,$include_unapproved_args,array('post_id' => $post->ID,'status' => 'approve','orderby' => array('meta_value_num' => 'DESC','comment_date' => 'ASC'),'meta_key' => 'comment_vote','order' => 'DESC')));
				}else {
					$comments_args = get_comments(array_merge($author__not_in,$include_unapproved_args,array('post_id' => $post->ID,'status' => 'approve','orderby' => 'comment_date','order' => 'ASC')));
				}
	            if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {
		            wp_list_comments('callback=vbegy_answer',$comments_args);
		        }else {
		        	$get_comments_args = array_merge($author__not_in,$include_unapproved_args,array('post_id' => $post->ID,'status' => 'approve'));
	            	$comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'DESC')));
	            	if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {
			            wp_list_comments('callback=vbegy_answer',$comments_args);
			        }else {
		        		wp_list_comments('callback=vbegy_answer');
		        	}
		        }?>
			</ol><!-- End commentlist -->
		<?php }else {
			echo '<div class="note_error"><strong>'.__("Sorry, you do not have permission to view answers.","vbegy").'</strong></div><br>';
		}?>
    </div><!-- End page-content -->
    
    <div class="pagination comments-pagination">
        <?php paginate_comments_links(array('prev_text' => '&laquo;', 'next_text' => '&raquo;'))?>
    </div><!-- End comments-pagination -->
<?php endif;

if ( comments_open() ) :
	if (askme_options("Ahmed") == 1) :comment_form();endif;
	
	$yes_new = 1;
	if (have_comments()) {
		if (isset($question_category[0]) && $askme_new == "on") {
			$yes_new = 0;
			if ($post->post_author != $user_get_current_user_id) {
				$yes_new = 1;
			}
			if (is_super_admin($user_get_current_user_id)) {
				$yes_new = 0;
			}
		}else {
			$yes_new = 0;
		}
	}else {
		if (isset($question_category[0]) && $askme_new == "on") {
			if (isset($post->post_author) && $post->post_author > 0 && $post->post_author == $user_get_current_user_id) {
				$yes_new = 1;
			}
			if (isset($post->ID) && $post->post_author > 0 && $post->post_author == $user_get_current_user_id) {
				$yes_new = 1;
			}
		}else if (isset($question_category[0]) && $askme_new != "on") {
			$yes_new = 0;
		}
		if (empty($question_category[0]) || is_super_admin($user_get_current_user_id)) {
			$yes_new = 0;
		}
	}
	if ($yes_new != 1) {?>
		<div id="respond" class="comment-respond page-content clearfix <?php if (!have_comments()) {echo "no_comment_box";}?>">
		    <div class="boxedtitle page-title"><h2><?php comment_form_title(__('Leave an answer','vbegy'),__('Leave an answer to %s','vbegy'));?></h2></div>
		    
		    <?php $add_answer = askme_options("add_answer");
		    if ($custom_permission != 1 || is_super_admin($user_get_current_user_id) || ($custom_permission == 1 && (is_user_logged_in && isset($roles["add_answer"]) && $roles["add_answer"] == 1) || (!is_user_logged_in && $add_answer == 1))) {
			    $post_comments_user = askme_options("post_comments_user");
			    $post_comments_user_active = true;
			    if ($post_comments_user == 1) {
			    	if (!is_user_logged_in) {
			    		$post_comments_user_active = false;
			    	}
			    }
			    
			    if (!is_user_logged_in && get_option("comment_registration")) {?>
			    	<p class="no-login-comment"><?php printf(__('You must <a href="%s" class="login-comments">login</a> or <a href="%s" class="signup">register</a> to add a new answer .','vbegy'),get_page_link(askme_options('login_register_page')),get_page_link(askme_options('login_register_page')))?></p>
			    <?php }else {
					if ($post_comments_user_active == true) {
						$yes_special = 1;
						if (have_comments()) {
							$yes_special = 0;
						}else {
							if (isset($question_category[0]) && $askme_special == "on") {
								if (isset($post->post_author) && $post->post_author > 0 && $post->post_author == $user_get_current_user_id) {
									$yes_special = 1;
								}
							}else if ((isset($question_category[0]) && $askme_special != "on") || !isset($question_category[0])) {
								$yes_special = 0;
							}else if (empty($question_category)) {
								$yes_special = 0;
							}
							if (is_super_admin($user_get_current_user_id)) {
								$yes_special = 0;
							}
						}
						if ($yes_special == 1) {
						    _e("Sorry this question is a special, The admin must answer first .","vbegy");
						}else {
							if (isset($closed_question) && $closed_question == 1) {?>
								<p class="no-login-comment"><?php _e('Sorry this question is closed .','vbegy')?></p>
							<?php }else {?>
								<form action="<?php echo esc_url(site_url( '/wp-comments-post.php' ))?>" method="post" id="commentform" enctype="multipart/form-data">
									<div class="ask_error"></div>
								    <?php if ( is_user_logged_in ) : ?>
								        <p><?php _e('Logged in as','vbegy')?> <a href="<?php echo get_option('siteurl');?>/wp-admin/profile.php"><?php echo $user_identity;?></a>. <a href="<?php echo wp_logout_url(get_permalink());?>" title="Log out of this account"><?php _e('Log out &raquo;','vbegy')?></a></p>
								    <?php else :
								    	$require_name_email = get_option("require_name_email");?>
								        <div id="respond-inputs" class="clearfix">
								            <p>
								                <label<?php echo ($require_name_email == 1?' class="required"':'')?> for="comment_name"><?php echo __('Name','vbegy').($require_name_email == 1?'<span>*</span>':'')?></label>
								                <input name="author" type="text" value="" id="comment_name" aria-required="true">
								            </p>
								            <p>
								                <label<?php echo ($require_name_email == 1?' class="required"':'')?> for="comment_email"><?php echo __('E-Mail','vbegy').($require_name_email == 1?'<span>*</span>':'')?></label>
								                <input name="email" type="text" value="" id="comment_email" aria-required="true">
								            </p>
								            <p class="last">
								                <label class="required" for="comment_url"><?php _e('Website','vbegy');?></label>
								                <input name="url" type="text" value="" id="comment_url">
								            </p>
								        </div>
								    <?php endif;?>
									<div class="clearfix">
										<?php $attachment_answer = askme_options("attachment_answer");
										if ($attachment_answer == 1) {?>
										    <label for="attachment"><?php _e('Attachment','vbegy');?></label>
										    <div class="fileinputs">
										    	<input type="file" name="attachment" id="attachment">
										    	<div class="fakefile">
										    		<button type="button" class="small margin_0"><?php _e('Select file','vbegy');?></button>
										    		<span><i class="icon-arrow-up"></i><?php _e('Browse','vbegy');?></span>
										    	</div>
										    </div>
									    <?php }
									    
									    $featured_image_answer = askme_options("featured_image_answer");
									    if ($featured_image_answer == 1) {?>
									        <label for="featured_image"><?php _e('Featured image','vbegy');?></label>
									        <div class="fileinputs">
									        	<input type="file" name="featured_image" id="featured_image">
									        	<div class="fakefile">
									        		<button type="button" class="small margin_0"><?php _e('Select file','vbegy');?></button>
									        		<span><i class="icon-arrow-up"></i><?php _e('Browse','vbegy');?></span>
									        	</div>
									        </div>
									    <?php }

									    $answer_video = askme_options("answer_video");
										if ($answer_video == 1) {
											$type = "add";
											$posted_video = array();
											$fields = array(
												'video_answer_description','video_answer_type','video_answer_id'
											);
											foreach ($fields as $field) :
												if (isset($_POST[$field])) $posted_video[$field] = $_POST[$field]; else $posted_video[$field] = '';
											endforeach;

											echo '<div class="form-inputs clearfix">
												<p class="question_poll_p answer_video_p">
													<label for="video_answer_description">'.__("Video","vbegy").'</label>
													<input type="checkbox" id="video_answer_description" class="video_answer_description_input" name="video_answer_description" value="on"'.($type == "add" && isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on"?" checked='checked'":($type == "edit" && ((isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on") || (empty($posted_video['video_answer_description']) && $video_answer_description == "on"))?" checked='checked'":"")).'>
													<span><label for="video_answer_description">'.esc_html__("Add a video to describe the problem better.","vbegy").'</label></span>
												</p>

												<div class="video_answer_description ask-hide"'.($type == "add" && isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on"?" style='display:block;'":($type == "edit" && ((isset($posted_video['video_answer_description']) && $posted_video['video_answer_description'] == "on") || $video_answer_description == "on")?" style='display:block;'":"")).'>
													<p>
														<label for="video_answer_type">'.esc_html__("Video type","vbegy").'</label>
														<span class="styled-select">
															<select id="video_answer_type" name="video_answer_type">
																<option value="youtube"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "youtube"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "youtube") || (isset($video_answer_type) && $video_answer_type == "youtube")?' selected="selected"':''):'')).'>Youtube</option>
																<option value="vimeo"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "vimeo"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "vimeo") || (isset($video_answer_type) && $video_answer_type == "vimeo")?' selected="selected"':''):'')).'>Vimeo</option>
																<option value="daily"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "daily"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "daily") || (isset($video_answer_type) && $video_answer_type == "daily")?' selected="selected"':''):'')).'>Dialymotion</option>
																<option value="facebook"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "facebook"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "facebook") || (isset($video_answer_type) && $video_answer_type == "facebook")?' selected="selected"':''):'')).'>Facebook</option>
																<option value="tiktok"'.($type == "add" && isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "tiktok"?' selected="selected"':($type == "edit"?((isset($posted_video['video_answer_type']) && $posted_video['video_answer_type'] == "tiktok") || (isset($video_answer_type) && $video_answer_type == "tiktok")?' selected="selected"':''):'')).'>TikTok</option>
															</select>
														</span>
														<span class="form-description">'.esc_html__("Choose from here the video type.","vbegy").'</span>
													</p>
													
													<p>
														<label for="video_answer_id">'.esc_html__("Video ID","vbegy").'</label>
														<input name="video_answer_id" id="video_answer_id" class="video_answer_id" type="text" value="'.esc_attr($type == "add" && isset($posted_video['video_answer_id'])?$posted_video['video_answer_id']:($type == "edit"?(isset($posted_video['video_answer_id']) && $posted_video['video_answer_id'] != ""?$posted_video['video_answer_id']:$video_answer_id):"")).'">
														<span class="form-description">'.esc_html__("Put here the video id : https://www.youtube.com/watch?v=sdUUx5FdySs Ex: 'sdUUx5FdySs'.","vbegy").'</span>
													</p>
												</div>
											</div>';
										}
									    echo askme_add_captcha(askme_options("the_captcha_answer"),"answer",rand(0000,9999));?>
									</div>
								    <div id="respond-textarea">
								        <p>
								            <label class="required" for="comment"><?php _e('Answer','vbegy');?><span>*</span></label>
								            <?php $comment_editor = askme_options("comment_editor");
								            if ($comment_editor == 1) {
								                $settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
								                $settings = apply_filters('askme_answer_editor_setting',$settings);
								                wp_editor("","comment",$settings);
								            }else {?>
								            	<textarea id="comment" name="comment" aria-required="true" cols="58" rows="10"></textarea>
								            <?php }?>
								        </p>
								    </div>
								    
								    <?php $private_answer = askme_options("private_answer");
								    if (is_user_logged_in && $private_answer == 1) {
								    	echo '
								    	<p class="private_answer_p">
								    		<label for="private_answer">'.__("Private answer","vbegy").'</label>
								    		<input type="checkbox" id="private_answer" name="private_answer" value="1">
								    		<span class="private_answer"><label for="private_answer">'.__("Activate this answer as a private answer.","vbegy").'</label></span>
								    	</p>
								    	<div class="clearfix"></div>';
								    }

								    $answer_anonymously = askme_options("answer_anonymously");
									if ($answer_anonymously == 1) {
										echo '
										<p class="question_poll_p">
											<label for="anonymously_answer">'.__("Answer Anonymously","vbegy").'</label>
											<input type="checkbox" class="ask_anonymously" id="anonymously_answer" name="anonymously_answer" value="1">';
											if (is_user_logged_in) {
												$user_id = get_current_user_id();
												$your_avatar = get_the_author_meta(askme_avatar_name(),$user_id);
												$display_name = get_the_author_meta('display_name',$user_id);
												echo '<span class="question_poll anonymously_span ask_named">';
													if ($your_avatar) {
														echo askme_user_avatar($your_avatar,25,25,$user_id,$display_name);
													}else {
														echo get_avatar($user_id,'25','');
													}
													echo '<span>'.$display_name.' '.esc_html__("answers","vbegy").'</span>
												</span>
												<span class="question_poll anonymously_span ask_none">
													<img alt="'.esc_html__("Anonymous","vbegy").'" src="'.get_template_directory_uri().'/images/avatar.png">
													<span>'.esc_html__("Anonymous answers","vbegy").'</span>
												</span>';
											}else {
												echo '<span class="question_poll">'.__("Anonymous answers","vbegy").'</span>';
											}
										echo '</p><div class="clearfix"></div>';
									}
								    
								    $terms_active_comment = askme_options("terms_active_comment");
								    if ($terms_active_comment == 1) {
									    $terms_checked_comment = askme_options("terms_checked_comment");
										if ((isset($_POST['agree_terms']) && $_POST['agree_terms'] == 1) || ($terms_checked_comment == 1 && empty($_POST))) {
											$active_terms = true;
										}
										$terms_link = askme_options("terms_link_comment");
										$terms_link_page = askme_options("terms_page_comment");
										$terms_active_target = askme_options("terms_active_target_comment");
										$privacy_policy = askme_options('privacy_policy_comment');
										$privacy_active_target = askme_options('privacy_active_target_comment');
										$privacy_page = askme_options('privacy_page_comment');
										$privacy_link = askme_options('privacy_link_comment');
										echo '<p class="question_poll_p">
											<label for="agree_terms" class="required">'.__("Terms","vbegy").'<span>*</span></label>
											<input type="checkbox" id="agree_terms" name="agree_terms" value="1" '.(isset($active_terms)?"checked='checked'":"").'>
											<span class="question_poll">'.sprintf(wp_kses(__("By answering, you agree to the <a target='%s' href='%s'>Terms of Service</a>%s.","vbegy"),array('a' => array('href' => array(),'target' => array()))),($terms_active_target == "same_page"?"_self":"_blank"),(isset($terms_link) && $terms_link != ""?$terms_link:(isset($terms_page) && $terms_page != ""?get_page_link($terms_page):"#")),($privacy_policy == 1?" ".sprintf(wp_kses(__("and <a target='%s' href='%s'>Privacy Policy</a>","vbegy"),array('a' => array('href' => array(),'target' => array()))),($privacy_active_target == "same_page"?"_self":"_blank"),(isset($privacy_link) && $privacy_link != ""?$privacy_link:(isset($privacy_page) && $privacy_page != ""?get_page_link($privacy_page):"#"))):"")).'</span>
										</p><div class="clearfix"></div>';
									}?>
									<div class="cancel-comment-reply"><?php cancel_comment_reply_link(__("Click here to cancel reply.","vbegy"));?></div>
									<?php echo apply_filters( 'comment_form_field_comment', false );?>
								    <p class="form-submit">
								    	<input name="submit" type="submit" id="submit" value="<?php _e('Post your answer','vbegy')?>" class="button small color">
								    	<?php comment_id_fields();?>
								    	<?php do_action('comment_form', $post->ID);?>
								    </p>
								</form>
							<?php }
						}
					}else {?>
						<p class="no-login-comment"><?php printf(__('You must <a href="%s" class="login-comments">login</a> or <a href="%s" class="signup">register</a> to add a new answer.','vbegy'),get_page_link(askme_options('login_register_page')),get_page_link(askme_options('login_register_page')))?></p>
					<?php }
				}
			}else {
				echo '<div class="note_error"><strong>'.__("Sorry, you do not have permission to answer to this question .","vbegy").'</strong></div>';
			}?>
		</div>
	<?php }
endif;