<?php /* Template name: Blocking Users */
global $user_ID;
if (!is_user_logged_in && empty($_GET['u'])) {
	wp_redirect(home_url());
}
$get_u = (int)(is_user_logged_in && empty($_GET['u'])?$user_ID:$_GET['u']);
$user_login = get_userdata($get_u);
if (empty($user_login)) {
	wp_redirect(home_url());
}
$owner = false;
if ($user_ID == $user_login->ID) {
	$owner = true;
}
if ($owner == false) {
	wp_redirect(home_url());
}
get_header();
	include (get_template_directory() . '/includes/author-head.php');
	$block_users = askme_options("block_users");
	
	if ($block_users == 1) {
		if (isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)) {
			$get_block_users = get_user_meta($user_login->ID,"askme_block_users",true);
			$rows_per_page = get_option("posts_per_page");
			$paged         = askme_paged();
			$offset		   = ($paged-1)*$rows_per_page;
			$users		   = get_users(array('include' => $get_block_users,'blog_id' => 1,'orderby' => 'registered'));
			$query         = get_users(array('offset' => $offset,'number' => $rows_per_page,'include' => $get_block_users,'blog_id' => 1,'orderby' => 'registered'));
			$total_users   = count($users);
			$total_query   = count($query);
			$total_pages   = (int)ceil($total_users/$rows_per_page);
			  
			foreach ($query as $user) {
				$your_avatar = get_the_author_meta(askme_avatar_name(),$user->ID);
				$country = get_the_author_meta('country',$user->ID);
				$url = get_the_author_meta('url',$user->ID);
				$twitter = get_the_author_meta('twitter',$user->ID);
				$facebook = get_the_author_meta('facebook',$user->ID);
				$tiktok = get_the_author_meta('tiktok',$user->ID);
				$linkedin = get_the_author_meta('linkedin',$user->ID);
				$follow_email = get_the_author_meta('follow_email',$user->ID);
				$youtube = get_the_author_meta('youtube',$user->ID);
				$pinterest = get_the_author_meta('pinterest',$user->ID);
				$instagram = get_the_author_meta('instagram',$user->ID);?>
				<div class="about-author clearfix">
					<div class="author-image">
					<a href="<?php echo vpanel_get_user_url($user->ID);?>" original-title="<?php echo $user->display_name?>" class="tooltip-n">
						<?php echo askme_user_avatar($your_avatar,65,65,$user->ID,$user->display_name);?>
					</a>
					</div>
					<div class="author-bio">
						<h4>
							<a href="<?php echo vpanel_get_user_url($user->ID);?>"><?php echo $user->display_name?></a>
							<?php $verified_user = get_the_author_meta('verified_user',$user->ID);
							if ($verified_user == 1) {
								echo '<img class="verified_user tooltip-n" alt="'.__("Verified","vbegy").'" original-title="'.__("Verified","vbegy").'" src="'.get_template_directory_uri().'/images/verified.png">';
							}
							echo vpanel_get_badge($user->ID)?>
						</h4>
						<?php echo $user->description?>
						<div class="clearfix"></div>
						<br>
						<?php if ($facebook || $tiktok || $twitter || $linkedin || $follow_email || $youtube || $pinterest || $instagram) { ?>
							<span class="user-follow-me"><?php _e("Follow Me","vbegy")?></span>
							<div class="social_icons social_icons_display">
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
									<a href="<?php echo $user->user_email?>" original-title="<?php _e("Email","vbegy")?>" class="tooltip-n">
										<span class="icon_i">
											<span class="icon_square" icon_size="30" span_bg="#000" span_hover="#2f3239">
												<i class="social_icon-email"></i>
											</span>
										</span>
									</a>
								<?php }?>
							</div>
						<?php }
						if ($user_login->ID > 0) {
							$get_block_users = get_user_meta($user_login->ID,"askme_block_users",true);
							echo '<div class="user_block user_blocking_page'.(!empty($get_block_users) && in_array($user->ID,$get_block_users)?" user_block_done":"").'">
								<div class="loader_3 user_block_loader"></div>';
								if (!empty($get_block_users) && in_array($user->ID,$get_block_users)) {
									echo '<a href="#" class="unblock-user-page button color small" data-nonce="'.wp_create_nonce("block_nonce").'" data-rel="'.(int)$user->ID.'" title="'.esc_attr__("Unblock","vbegy").'"><span class="block-value">'.esc_html__("Unblock","vbegy").'</span></a>';
								}else {
									echo '<a href="#" class="block-user-page button color small" data-rel="'.(int)$user->ID.'" data-nonce="'.wp_create_nonce("block_nonce").'" title="'.esc_attr__("Block","vbegy").'"><span class="block-value">'.esc_html__("Block","vbegy").'</span></a>';
								}
							echo '</div>';
						}?>
					</div>
				</div>
			<?php }
			
			if ($total_users > $total_query) {
				echo '<div class="pagination">';
				$current_page = max(1,get_query_var('paged'));
				echo paginate_links(array(
					'base' => @esc_url(add_query_arg('paged','%#%')),
					'format' => 'page/%#%/?u='.$get_u,
					'current' => $current_page,
					'show_all' => false,
					'total' => $total_pages,
					'prev_text' => '<i class="icon-angle-left"></i>',
					'next_text' => '<i class="icon-angle-right"></i>',
				));
				echo '</div><div class="clearfix"></div>';
			}
		}else {
			echo "<div class='page-content page-content-user'><p class='no-item'>".__("There are no blocking users yet .","vbegy")."</p></div>";
		}
	}else {
		echo "<div class='page-content page-content-user'><p class='no-item'>".__("Sorry, this page is not available .","vbegy")."</p></div>";
	}
get_footer();?>