<?php ob_start();/* Template Name: Edit comment & answer */
get_header();
global $posted;
$comment_id = (int)(isset($_GET["comment_id"])?$_GET["comment_id"]:0);
$get_comment = get_comment($comment_id);
$user_id = get_current_user_id();
$get_post = array();
if ($comment_id > 0 && is_object($get_comment)) {
	$get_post = get_post($get_comment->comment_post_ID);
	$can_edit_comment = askme_options("can_edit_comment");
	$can_edit_comment_after = askme_options("can_edit_comment_after");
	$can_edit_comment_after = (int)(isset($can_edit_comment_after) && $can_edit_comment_after > 0?$can_edit_comment_after:0);
	if (version_compare(phpversion(), '5.3.0', '>')) {
		$time_now = strtotime(current_time( 'mysql' ),date_create_from_format('Y-m-d H:i',current_time( 'mysql' )));
	}else {
		list($year, $month, $day, $hour, $minute, $second) = sscanf(current_time( 'mysql' ), '%04d-%02d-%02d %02d:%02d:%02d');
		$datetime = new DateTime("$year-$month-$day $hour:$minute:$second");
		$time_now = strtotime($datetime->format('r'));
	}
	$time_edit_comment = strtotime('+'.$can_edit_comment_after.' hour',strtotime($get_comment->comment_date));
	$time_end = ($time_now-$time_edit_comment)/60/60;
}
	if ( have_posts() ) : while ( have_posts() ) : the_post();?>
		<div class="page-content">
			<div class="boxedtitle page-title"><h2><?php the_title();?></h2></div>
			<?php the_content();
			if ($comment_id > 0 && isset($get_post->ID)) {
				if (is_super_admin($user_id) || ($can_edit_comment == 1 && $get_comment->user_id == $user_id && $get_comment->user_id != 0 && $user_id != 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after))) {
					do_action('vpanel_edit_comment');
					echo '<div class="form-edit-comment form-style form-style-3">
						<form method="post" enctype="multipart/form-data">
							<div class="note_error display"></div>
							<div class="form-inputs clearfix">
								<div>
									<label class="required">'.__("Comment","vbegy").'<span>*</span></label><div class="clearfix"></div><br>';
									$settings = array("textarea_name" => "comment_content","media_buttons" => true,"textarea_rows" => 10);
									$settings = apply_filters('askme_edit_comment_editor_setting',$settings);
									wp_editor((isset($posted['comment_content'])?wp_kses_post($posted['comment_content']):wp_kses_post($get_comment->comment_content)),"comment_content",$settings);
								echo '<br></div>';

								$attachment_answer = askme_options("attachment_answer");
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
									$type = "edit";
									if ($type == "edit") {
										$video_answer_description = get_comment_meta($comment_id,"video_answer_description",true);
										$video_answer_type = get_comment_meta($comment_id,"video_answer_type",true);
										$video_answer_id = get_comment_meta($comment_id,"video_answer_id",true);
									}
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
								
								$private_answer = askme_options("private_answer");
								if (is_user_logged_in && $private_answer == 1) {
									$a_private_answer = get_comment_meta($comment_id,"private_answer",true);
									echo '
									<p class="private_answer_p">
										<label for="private_answer">'.__("Private answer","vbegy").'</label>
										<input type="checkbox" id="private_answer" name="private_answer" value="1" '.((isset($_POST["private_answer"]) && $_POST["private_answer"] == 1)|| $a_private_answer == 1?"checked='checked'":"").'>
										<span class="private_answer"><label for="private_answer">'.esc_html__("Activate this answer as a private answer.","vbegy").'</label></span>
									</p>
									<div class="clearfix"></div>';
								}
								
							echo '</div>
							<p class="form-submit margin_0">
								<input type="hidden" name="comment_id" value="'.$comment_id.'">
								<input type="submit" value="'.__("Edit Comment","vbegy").'" class="button color small submit edit-comment">
							</p>
						</form>
					</div>';
				}else {
					_e("You are not allowed to edit this comment.","vbegy");
				}
			}else {
				_e("Sorry no comment has you select or not found.","vbegy");
			}?>
		</div><!-- End page-content -->
	<?php endwhile; endif;
get_footer();?>