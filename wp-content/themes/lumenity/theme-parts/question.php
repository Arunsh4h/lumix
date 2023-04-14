<?php $question_vote_show = askme_options("question_vote_show");
$question_vote = get_post_meta($post->ID,"question_vote",true);
$question_category = wp_get_post_terms($post->ID,ask_question_category,array("fields" => "all"));
$user_get_current_user_id = get_current_user_id();
$yes_private = ask_private($post->ID,$post->post_author,$user_get_current_user_id);
$vbegy_what_post = askme_post_meta('vbegy_what_post','select',$post->ID);
$vbegy_sidebar_all = askme_post_meta('vbegy_sidebar','select',$post->ID);
$question_poll = get_post_meta($post->ID,'question_poll',true);
$question_type = ($question_poll == 1?" question-type-poll":" question-type-normal");
$the_best_answer = get_post_meta($post->ID,"the_best_answer",true);
$closed_question = get_post_meta($post->ID,"closed_question",true);
$question_favorites = get_post_meta($post->ID,'question_favorites',true);
$anonymously_user = get_post_meta($post->ID,'anonymously_user',true);
$the_author = get_user_by("login",get_the_author());
$user_login_id_l = get_user_by("id",$post->post_author);
$count_post_all = (int)askme_count_comments($post->ID);
if ($post->post_author != 0 && isset($user_login_id_l) && is_object($user_login_id_l)) {
	$user_profile_page = esc_url(add_query_arg("u", $user_login_id_l->user_login,get_page_link(askme_options('user_profile_page'))));
}else {
	$anonymously_question = get_post_meta($post->ID,'anonymously_question',true);
	if ($anonymously_question == 1 && $anonymously_user != "") {
		$question_username = esc_html__("Anonymous","vbegy");
		$question_email = 0;
	}else {
		$question_username = get_post_meta($post->ID, 'question_username',true);
		$question_email = get_post_meta($post->ID, 'question_email',true);
		$question_username = ($question_username != ""?$question_username:esc_html__("Anonymous","vbegy"));
		$question_email = ($question_email != ""?$question_email:0);
	}
}
$active_reports = askme_options("active_reports");
$active_logged_reports = askme_options("active_logged_reports");
$question_type = ($active_reports == 1 && (is_user_logged_in || (!is_user_logged_in && $active_logged_reports != 1))?$question_type:$question_type." no_reports");
$excerpt_questions = askme_options("excerpt_questions");
$question_author = askme_options("question_author");
$question_author_class = (!isset($question_author) || $question_author == 1?" question_author_yes":" question_author_no");
$private_question_content = askme_options("private_question_content");
if ($yes_private != 1 && $private_question_content != 1) {?>
	<article class="question private-question">
		<p class="question-desc"><?php _e("Sorry it's a private question.","vbegy");?></p>
	</article>
<?php }else {
	$question_sticky = "";
	if (is_sticky() && isset($active_sticky) && $active_sticky == true) {
		$question_sticky = " sticky";
		$end_sticky_time = get_post_meta($post->ID,"end_sticky_time",true);
		if ($end_sticky_time != "" && $end_sticky_time < strtotime(date("Y-m-d"))) {
			$question_sticky = "";
		}
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
	}?>
	<article <?php post_class('question'.$question_type.$question_author_class.$question_sticky);?> id="post-<?php echo $post->ID;?>" itemscope itemtype="https://schema.org/Question" <?php do_action("askme_question_attrs",$post->post_author)?>>
		<h2>
			<a itemprop="url" href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark">
				<?php if ($question_sticky == " sticky") {
					echo '<i class="icon-pushpin tooltip-n question-sticky" original-title="'.__("Sticky","vbegy").'"></i>';
				}?>
				<span itemprop="name"><?php the_title();?></span>
			</a>
		</h2>
		<?php if ($active_reports == 1 && (is_user_logged_in || (!is_user_logged_in && $active_logged_reports != 1))) {?>
			<a class="question-report report_q" href="#"><?php _e("Report","vbegy")?></a>
		<?php }
		if ($question_poll == 1) {?>
			<div class="question-type-main"><i class="icon-signal"></i><?php _e("Poll","vbegy")?></div>
		<?php }else {?>
			<div class="question-type-main"><i class="icon-question-sign"></i><?php _e("Question","vbegy")?></div>
		<?php }
		if (!isset($question_author) || $question_author == 1) {?>
			<div class="question-author" itemprop="author" itemscope itemtype="https://schema.org/Person">
				<span itemprop="name" class="hide"><?php echo ($post->post_author > 0?$authordata->display_name:$question_username)?></span>
				<?php if ($post->post_author != 0 && isset($authordata) && is_object($authordata)) {?>
					<a href="<?php echo vpanel_get_user_url($post->post_author);?>" original-title="<?php the_author();?>" class="question-author-img tooltip-n">
				<?php }else {?>
					<div class="question-author-img">
				<?php }?>
					<span></span>
					<?php echo askme_user_avatar (get_the_author_meta(askme_avatar_name(), $post->post_author),65,65,$post->post_author,($post->post_author > 0?$authordata->display_name:$question_username),($post->post_author > 0?"":$question_email),true);
				if ($post->post_author != 0 && isset($authordata) && is_object($authordata)) {?>
					</a>
				<?php }else {?>
					</div>
				<?php }?>
			</div>
		<?php }?>
		<div class="question-inner">
			<div class="clearfix"></div>
			<div class="question-desc<?php echo ($excerpt_questions == 1?" question-desc-no-padding":"")?>">
				<?php if ($yes_private != 1 && $private_question_content == 1) {?>
						<p><?php _e("Sorry it's a private question.","vbegy");?></p>
					<?php }else {
					$custom_permission = askme_options("custom_permission");
					$show_question = askme_options("show_question");
					if (is_user_logged_in) {
						$user_is_login = get_userdata($user_get_current_user_id);
						$user_login_group = key($user_is_login->caps);
						$roles = $user_is_login->allcaps;
					}
					if ($custom_permission != 1 || (is_super_admin($user_get_current_user_id) || (is_user_logged_in && isset($roles["show_question"]) && $roles["show_question"] == 1) || (!is_user_logged_in && $show_question == 1)) || ($user_get_current_user_id > 0 && $user_get_current_user_id == $post->post_author) || ($user_get_current_user_id > 0 && $user_get_current_user_id == $anonymously_user)) {
						$question_poll_loop = askme_options("question_poll_loop");
						if ($question_poll == 1 && $question_poll_loop == 1) {?>
							<div class='question_poll_end'>
								<div class="alert-message error ask-hide"></div>
								<?php echo askme_show_poll_results($post->ID,$user_get_current_user_id)?>
							</div><!-- End question_poll_end -->
							<div class="clearfix height_20"></div>
							<?php
						}
						if ($excerpt_questions != 1) {
							$question_excerpt = askme_options("question_excerpt");
							$question_excerpt_type = askme_options("question_excerpt_type");
							$continue_reading_questions = askme_options("continue_reading_questions");
							$featured_image_loop = askme_options("featured_image_loop");
							if ($featured_image_loop == 1 && has_post_thumbnail()) {
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
								$question_url_1 = ($featured_image_question_lightbox == 1?"":"<a href='".get_permalink($post->ID)."'>");
								$question_url_2 = ($featured_image_question_lightbox == 1?"":"</a>");
								$featured_position = askme_options("featured_position");
								if ($featured_position != "after") {
									echo "<div class='featured_image_question'>".$question_url_1.askme_resize_img($featured_image_question_width,$featured_image_question_height,$img_lightbox).$question_url_2."</div>
									<div class='clearfix'></div>";
								}
							}
							$video_desc_active_loop = askme_options("video_desc_active_loop");
							if ($video_desc_active_loop == 1) {
								$video_desc_loop = askme_options("video_desc_loop");
								$video_description_width = askme_options("video_description_width");
								$video_desc_100_loop = askme_options("video_desc_100_loop");
								$video_description_height = askme_options("video_description_height");
								
								$video_description = get_post_meta($post->ID,'video_description',true);
								$question_sort = askme_options("ask_question_items");
								$video_desc_active = (isset($question_sort["video_desc_active"]["value"]) && $question_sort["video_desc_active"]["value"] == "video_desc_active"?1:0);
								if ($video_desc_active == 1 && $video_description == 1) {
									$video_desc = get_post_meta($post->ID,'video_desc',true);
									$video_id = get_post_meta($post->ID,'video_id',true);
									$video_type = get_post_meta($post->ID,'video_type',true);
									if ($video_id != "") {
										$type = askme_video_iframe($video_type,$video_id,"post_meta","video_id",$post->ID);
										$las_video = '<div class="question-video-loop video-type-'.$video_type.($video_desc_100_loop == 1?' question-video-loop-100':'').($video_desc_loop == "after"?' question-video-loop-after':'').'"><iframe width="'.$video_description_width.'" height="'.$video_description_height.'" src="'.$type.'"></iframe></div>';
										if ($video_desc_loop == "before") {
											echo $las_video;
										}
									}
								}
							}?>
							
							<div itemprop="text"><?php excerpt($question_excerpt,$question_excerpt_type);?></div>
							
							<?php if ($featured_image_loop == 1 && has_post_thumbnail() && $featured_position == "after") {
								echo "<div class='featured_image_question featured_image_after'>".$question_url_1.askme_resize_img($featured_image_question_width,$featured_image_question_height,$img_lightbox).$question_url_2."</div>
								<div class='clearfix'></div>";
							}
							
							if ($video_desc_active_loop == 1 && isset($video_desc_loop) && $video_desc_loop == "after" && isset($video_desc_active) && $video_desc_active == 1 && isset($video_id) && $video_id != "" && isset($video_description) && $video_description == 1) {
								echo $las_video;
							}

							do_action("askme_after_content",$post->ID);
							
							if ($continue_reading_questions == 1) {?>
								<a href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark" class="post-read-more button color small"><?php _e("Continue reading","vbegy");?></a>
								<div class="clearfix"></div>
							<?php }
						}
					}else {
						echo '<div class="note_error"><strong>'.__("Sorry, you do not have permission to view questions.","vbegy").'</strong></div>';
					}
				}
				if ($active_reports == 1 && (is_user_logged_in || (!is_user_logged_in && $active_logged_reports != 1))) {?>
					<div class="explain-reported">
						<h3><?php _e("Please briefly explain why you feel this question should be reported.","vbegy")?></h3>
						<textarea name="explain-reported"></textarea>
						<div class="clearfix"></div>
						<div class="loader_3"></div>
						<div class="color button small report"><?php _e("Report","vbegy")?></div>
						<div class="color button small dark_button cancel"><?php _e("Cancel","vbegy")?></div>
					</div><!-- End reported -->
				<?php }?>
				<div class="no_vote_more"></div>
			</div>
			<?php $question_answer_loop = askme_options("question_answer_loop");
			$question_answer_show = askme_options("question_answer_show");
			if ($question_answer_loop == 1 && $count_post_all > 0) {?>
				<div class="question-with-answer">
					<?php $block_users = askme_options("block_users");
					$author__not_in = array();
					if ($block_users == 1) {
						$get_current_user_id = get_current_user_id();
						if ($get_current_user_id > 0) {
							$get_block_users = get_user_meta($get_current_user_id,"askme_block_users",true);
							if (is_array($get_block_users) && !empty($get_block_users)) {
								$author__not_in = array("author__not_in" => $get_block_users);
							}
						}
					}
					if ($question_answer_show == 'best' && $the_best_answer != "") {
						$comments_args = get_comments(array_merge($author__not_in,array('number' => 1,'comment__in' => $the_best_answer)));
					}else if ($question_answer_show == 'vote') {
						$comments_args = get_comments(array_merge($author__not_in,array('number' => 1,'post_id' => $post->ID,'status' => 'approve','orderby' => 'meta_value_num','meta_key' => 'comment_vote','order' => 'DESC')));
					}else if ($question_answer_show == 'oldest') {
						$comments_args = get_comments(array_merge($author__not_in,array('number' => 1,'post_id' => $post->ID,'status' => 'approve','orderby' => 'comment_date','order' => 'ASC')));
					}else {
						$comments_args = get_comments(array_merge($author__not_in,array('number' => 1,'post_id' => $post->ID,'status' => 'approve','orderby' => 'comment_date','order' => 'DESC')));
					}?>
					<ol class="commentlist clearfix">
					    <?php if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {
							$comment = $comments_args[0];
							$yes_private_answer = ask_private_answer($comment->comment_ID,$comment->user_id,get_current_user_id(),$post->post_author);
							if ($yes_private_answer == 1) {
								$comment_vote = get_comment_meta($comment->comment_ID,'comment_vote',true);
								$comment_vote = (!empty($comment_vote)?$comment_vote:0);
								$answer_type = "answer";
								$no_title = "no_title";
								$k = 0;
								include locate_template("includes/answers.php");
							}
					    }?>
					</ol><!-- End commentlist -->
					<div class="clearfix"></div>
				</div><!-- End question-with-answer -->
			<?php }?>
			<?php do_action("askme_before_end_meta");?>
			<div class="footer-question-meta">
				<div class="question-meta-first">
					<?php $questions_meta = askme_options("questions_meta");
					if (isset($questions_meta["status"]) && $questions_meta["status"] == 1) {?>
						<div class="question-details">
							<?php $comments = get_comments('post_id='.$post->ID);
							if (isset($the_best_answer) && $the_best_answer != "" && $comments) {?>
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
					if (isset($questions_meta["user_name"]) && $questions_meta["user_name"] == 1) {
						if ($post->post_author == 0 || (isset($post->post_author) && $post->post_author > 0 && isset($question_author) && $question_author != 1)) {?>
							<span class="question-author-meta">
								<?php if ($post->post_author == 0) {?>
									<i class="icon-user"></i><span><?php echo $question_username?></span>
								<?php }else if (isset($post->post_author) && $post->post_author > 0 && isset($question_author) && $question_author != 1) {?>
									<a href="<?php echo vpanel_get_user_url($post->post_author);?>" title="<?php the_author();?>"><i class="icon-user"></i><?php the_author();?></a>
								<?php }?>
							</span>
						<?php }
						$get_question_user_id = get_post_meta($post->ID,"user_id",true);
						if ($get_question_user_id != "") {
							$display_name = get_the_author_meta('display_name',$get_question_user_id);
							if (isset($display_name) && $display_name != "") {?>
								<span class="question-author-meta"><a href="<?php echo vpanel_get_user_url($get_question_user_id);?>" title="<?php echo esc_attr($display_name)?>"><i class="icon-user"></i><?php echo esc_html__("Asked to","vbegy")." : ".esc_attr($display_name)?></a></span>
							<?php }
						}
					}
					if (isset($questions_meta["date"]) && $questions_meta["date"] == 1) {?>
						<span class="question-date" itemprop="dateCreated" datetime="<?php echo get_the_date(); ?>"><i class="fa fa-calendar"></i><?php echo '<a href="'.get_the_permalink($post->ID).'">'.human_time_diff(get_the_time('U'), current_time('timestamp')).'</a>';?></span>
					<?php }
					if (isset($questions_meta["answer_meta"]) && $questions_meta["answer_meta"] == 1) {?>
						<span class="question-comment"><a href="<?php echo comments_link()?>"><i class="fa fa-comments"></i><span itemprop="answerCount"><?php echo (int)$count_post_all;?></span> <?php echo _n("Answer","Answers",$count_post_all,"vbegy")?></a></span>
					<?php }
					if (isset($questions_meta["view"]) && $questions_meta["view"] == 1) {?>
						<span class="question-view"><i class="icon-eye-open"></i><?php echo (int)get_post_meta($post->ID,askme_get_meta_stats(),true)?> <?php _e("views","vbegy");?></span>
					<?php }
					$question_bump = askme_options("question_bump");
					if (isset($questions_meta["question_bump"]) && $questions_meta["question_bump"] == 1 && $question_bump == 1 && isset($question_bump_template) && $question_bump_template == true) {?>
						<span class="question-points"><i class="icon-heart"></i><span><?php $question_points = get_post_meta($post->ID, 'question_points', true);echo ($question_points != ""?$question_points:0)." ".__("points","vbegy");?></span></span>
					<?php }
					$active_vote = askme_options("active_vote");
					$active_vote_unlogged = askme_options("active_vote_unlogged");
					$show_dislike_questions = askme_options("show_dislike_questions");
					if ($active_vote == 1 && $question_vote_show != 1 && isset($question_vote_template) && $question_vote_template == true) {?>
						<span class="question-vote-all"><i class="icon-thumbs-<?php echo ($question_vote < 0?"down":"up");?>"></i><span itemprop="upvoteCount"><?php echo ($question_vote != ""?$question_vote:0);?></span></span>
					<?php }
					if (isset($post->post_author) && $post->post_author > 0) {
						echo vpanel_get_badge($post->post_author);
					}
					do_action("askme_after_end_meta",$post);?>
				</div>
				<?php if ($active_vote == 1 && $question_vote_show == 1) {?>
					<div class="question-meta-vote">
						<ul class="single-question-vote">
							<?php if ((is_user_logged_in && $post->post_author != $user_get_current_user_id && $anonymously_user != $user_get_current_user_id) || (!is_user_logged_in && $active_vote_unlogged == 1)) {?>
								<li><a href="#" id="question_vote_up-<?php echo $post->ID?>" class="ask_vote_up question_vote_up vote_allow<?php echo (isset($_COOKIE[askme_options("uniqid_cookie").'question_vote'.$post->ID])?" ".$_COOKIE[askme_options("uniqid_cookie").'question_vote'.$post->ID]."-".$post->ID:"")?> tooltip_s" title="<?php _e("Like","vbegy");?>"><i class="icon-thumbs-up"></i></a></li>
								<?php if ($show_dislike_questions != 1) {?>
									<li><a href="#" id="question_vote_down-<?php echo $post->ID?>" class="ask_vote_down question_vote_down vote_allow<?php echo (isset($_COOKIE[askme_options("uniqid_cookie").'question_vote'.$post->ID])?" ".$_COOKIE[askme_options("uniqid_cookie").'question_vote'.$post->ID]."-".$post->ID:"")?> tooltip_s" title="<?php _e("Dislike","vbegy");?>"><i class="icon-thumbs-down"></i></a></li>
								<?php }
							}else {?>
								<li><a href="#" class="ask_vote_up question_vote_up <?php echo (is_user_logged_in && (($post->post_author == $user_get_current_user_id) || ($anonymously_user == $user_get_current_user_id))?"vote_not_allow":"vote_not_user")?> tooltip_s" original-title="<?php _e("Like","vbegy");?>"><i class="icon-thumbs-up"></i></a></li>
								<?php if ($show_dislike_questions != 1) {?>
									<li><a href="#" class="ask_vote_down question_vote_down <?php echo (is_user_logged_in && (($post->post_author == $user_get_current_user_id) || ($anonymously_user == $user_get_current_user_id))?"vote_not_allow":"vote_not_user")?> tooltip_s" original-title="<?php _e("Dislike","vbegy");?>"><i class="icon-thumbs-down"></i></a></li>
								<?php }
							}?>
						</ul>
						<span class="single-question-vote-result question_vote_result" itemprop="upvoteCount"><?php echo ($question_vote != ""?$question_vote:0)?></span>
					</div>
				<?php }?>
			</div>
		</div>
	</article>
<?php }
if (isset($k) && $k == askme_options("between_questions_position")) {
	$between_adv_type = askme_options("between_adv_type");
	$between_adv_link = askme_options("between_adv_link");
	$between_adv_code = askme_options("between_adv_code");
	$between_adv_href = askme_options("between_adv_href");
	$between_adv_img = askme_options("between_adv_img");
	if (($between_adv_type == "display_code" && $between_adv_code != "") || ($between_adv_type == "custom_image" && $between_adv_img != "")) {
		echo '<div class="clearfix"></div>
		<div class="advertising advertising-posts">';
		if ($between_adv_type == "display_code") {
			echo do_shortcode(stripslashes($between_adv_code));
		}else {
			if ($between_adv_href != "") {
				echo '<a'.($between_adv_link == "new_page"?" target='_blank'":"").' href="'.$between_adv_href.'">';
			}
			echo '<img alt="" src="'.$between_adv_img.'">';
			if ($between_adv_href != "") {
				echo '</a>';
			}
		}
		echo '</div><!-- End advertising -->
		<div class="clearfix"></div>';
	}
}?>