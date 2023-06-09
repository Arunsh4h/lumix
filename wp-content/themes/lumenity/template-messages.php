<?php /* Template Name: Messages */
get_header();
	$active_message = askme_options("active_message");
	if ($active_message == 1) {
		if (!is_user_logged_in) {?>
			<div class="page-content">
				<div class="boxedtitle page-title"><h2><?php the_title();?></h2></div>
		<?php }?>
			<div class="form-style form-style-4">
				<?php if (!is_user_logged_in) {
					echo '<div class="note_error"><strong>'.__("Please login to see the your messages.","vbegy").'</strong></div>
					<div class="form-style form-style-3">
						'.do_shortcode("[ask_login register_2='yes']").'
					</div>';
				}else {
					$rows_per_page = get_option("posts_per_page");
					$user_get_current_user_id = get_current_user_id();
					$count_new_message = count_new_message($user_get_current_user_id);?>
					<div class="question-tab">
						<ul class="tabs not-tabs">
							<li class="tab"><a href="<?php echo esc_url(get_page_link(askme_options('messages_page')))?>"<?php echo (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send")?' class="current"':'')?>><?php _e("Inbox","vbegy")?> <?php echo($count_new_message > 0?"<span>( ".$count_new_message." )</span>":"")?></a></li>
							<li class="tab"><a href="<?php echo esc_url(add_query_arg("show", "send"),get_page_link(askme_options('messages_page')))?>"<?php echo (isset($_GET["show"]) && $_GET["show"] == "send"?' class="current"':'')?>><?php _e("Sent","vbegy")?></a></li>
						</ul>
					</div>
					
					<div class="page-content page-content-user">
						<div class="user-questions">
							<?php $paged = askme_paged();
							if (isset($_GET["show"]) && $_GET["show"] == "send") {
								$message_show = "send";
								$attrs = array("author" => $user_get_current_user_id,"meta_query" => array(array("key" => "delete_send_message","compare" => "NOT EXISTS")));
							}else {
								$message_show = "inbox";
								$attrs = array("meta_query" => array('relation' => 'AND',array("key" => "delete_inbox_message","compare" => "NOT EXISTS"),array("key" => "message_user_id","compare" => "=","value" => $user_get_current_user_id)));
							}
							$args = array_merge(array('post_type' => 'message','posts_per_page' => $rows_per_page,'paged' => $paged),$attrs);
							query_posts($args);
							if (have_posts()) : while ( have_posts() ) : the_post();
								$message_user_id = get_post_meta($post->ID,"message_user_id",true);
								$message_delete = askme_options("message_delete");
								if (($message_delete == 1 || is_super_admin($user_get_current_user_id)) && isset($_GET) && isset($_GET["delete"]) && $_GET["delete"] == $post->ID) {
									askme_delete_messages($post->ID,$post->post_author,$user_get_current_user_id,$message_user_id);
								}?>
								<article class="question user-question user-points user-messages">
									<div class="question-content">
										<div class="question-bottom">
											<div class="question-meta-first">
												<h3>
													<?php echo "<a href='#' class='view-message tooltip-n' original-title='".__("View the message","vbegy")."' data-id='".$post->ID."' data-show='".$message_show."'>";
														if (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send")) {
															$message_new = get_post_meta($post->ID,'message_new',true);?>
															<i class="message_new<?php echo ($message_new == 1?" message-new":"")?> icon-envelope-alt"></i>
														<?php }
														the_title();
														echo "<i class='message-open-close icon-plus'></i><span class='message_loader loader_2'></span>
													</a>";?>
												</h3>
												<?php $display_name = get_the_author_meta('display_name',$post->post_author);?>
												<span class="author-message">
													<?php if ($post->post_author > 0) {
														echo '<a href="'.get_author_posts_url($post->post_author).'"><i class="fa fa-user"></i>'.$display_name.'</a>';
													}else {
														echo '<i class="fa fa-user"></i>'.get_post_meta($post->ID,'message_username',true);
													}?>
												</span>
												<span class="question-date"><i class="fa fa-calendar"></i><?php echo human_time_diff(get_the_time('U'), current_time('timestamp'));?></span>
												<?php if ($post->post_author > 0 && (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send"))) {?>
													<span class="message-reply"><a href="#" data-id="<?php echo esc_attr($post->ID)?>" data-user-id="<?php echo esc_attr($post->post_author)?>"><i class="icon-reply"></i><?php _e("Reply","vbegy");?></a></span>
												<?php }
												if (($post->post_author > 0 && $post->post_author == $user_get_current_user_id) || $message_user_id == $user_get_current_user_id) {
													if ($message_delete == 1 || is_super_admin($user_get_current_user_id)) {?>
														<span class="message-delete"><a href="<?php echo (empty($_GET["show"]) || (isset($_GET["show"]) && $_GET["show"] != "send")?esc_url(add_query_arg("delete", $post->ID,get_page_link(askme_options('messages_page')))):esc_url(add_query_arg("delete", $post->ID,add_query_arg("show", "send"),get_page_link(askme_options('messages_page')))))?>" data-id="<?php echo esc_attr($post->ID)?>"><i class="icon-remove"></i><?php _e("Delete","vbegy");?></a></span>
													<?php }
												}
												do_action("askme_after_message_buttons",$post,$message_user_id,$user_get_current_user_id)?>
											</div>
										</div>
									</div>
									<div class="message-content"></div>
								</article>
							<?php endwhile;else:echo "<p class='no-item'>".__("No messages yet .","vbegy")."</p>";endif;?>
						</div>
					</div>
					
					<?php if ($wp_query->max_num_pages > 1 ) :
						vpanel_pagination(array("base" => @esc_url(add_query_arg('paged','%#%')),"format" => 'paged/%#%/'));
					endif;
					wp_reset_query();
				}
			if (!is_user_logged_in) {?>
				</div><!-- End page-content -->
			<?php }?>
		</div><!-- End main -->
	<?php }else {
		echo "<div class='page-content page-content-user'><p class='no-item'>".__("This page is not active.","vbegy")."</p></div>";
	}
get_footer();?>