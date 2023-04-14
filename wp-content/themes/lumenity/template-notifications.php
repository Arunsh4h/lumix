<?php /* Template Name: Notifications */
get_header();
	$active_notifications = askme_options("active_notifications");
	if ($active_notifications == 1) {
		if (!is_user_logged_in) {?>
			<div class="page-content">
				<div class="boxedtitle page-title"><h2><?php the_title();?></h2></div>
		<?php }?>
			<div class="form-style form-style-4">
				<?php if (!is_user_logged_in) {
					echo '<div class="note_error"><strong>'.__("Please login to see the notifications.","vbegy").'</strong></div>
					<div class="form-style form-style-3">
						'.do_shortcode("[ask_login register_2='yes']").'
					</div>';
				}else {?>
					<div class="page-content page-content-user">
						<div class="user-questions">
							<?php $user_id = get_current_user_id();
							$number = get_option("posts_per_page");
							update_user_meta($user_id,$user_id.'_new_notifications',0);
							$paged = askme_paged();
							
							$_notifications = get_user_meta($user_id,$user_id."_notifications",true);
							for ($notifications = 1; $notifications <= $_notifications; $notifications++) {
								$notification_one[] = get_user_meta($user_id,$user_id."_notifications_".$notifications);
							}
							if (isset($notification_one) and is_array($notification_one)) {
								$notification = array_reverse($notification_one);
								
								$current = max(1,$paged);
								$total_notification = count($notification);
								$pagination_args = array(
									'base' => @esc_url(add_query_arg('paged','%#%')),
									'total' => ceil($total_notification/$number),
									'current' => $current,
									'show_all' => false,
									'prev_text' => '<i class="icon-angle-left"></i>',
									'next_text' => '<i class="icon-angle-right"></i>',
								);
								
								if( !empty($wp_query->query_vars['s']) )
									$pagination_args['add_args'] = array('s'=>get_query_var('s'));
									
								$start = ($current - 1) * $number;
								$end = $start + $number;
								$end = ($total_notification < $end) ? $total_notification : $end;
								for ($i=$start;$i < $end ;++$i ) {
									$notification_result = $notification[$i][0];?>
									<article class="question user-question user-points">
										<div class="question-content">
											<div class="question-bottom">
												<div class="question-meta-first">
													<h3>
														<?php echo askme_show_notifications($notification_result)?>
													</h3>
													<?php if (isset($notification_result["time"])) {?>
														<span class="question-date"><i class="fa fa-calendar"></i><?php echo human_time_diff($notification_result["time"],current_time('timestamp'))." ".__("ago","vbegy")?></span>
													<?php }?>
												</div>
											</div>
										</div>
									</article>
								<?php }
							}else {echo "<p class='no-item'>".__("There are no notifications yet.","vbegy")."</p>";}?>
						</div>
					</div>
					<?php if (isset($notification_one) &&is_array($notification_one) && $pagination_args["total"] > 1) {?>
						<div class='pagination'><?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?></div>
					<?php }
				}
			if (!is_user_logged_in) {?>
				</div><!-- End page-content -->
			<?php }?>
		</div><!-- End main -->
	<?php }else {
		echo "<div class='page-content page-content-user'><p class='no-item'>".__("This page is not active.","vbegy")."</p></div>";
	}
get_footer();?>