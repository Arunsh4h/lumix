				<?php $site_users_only = askme_options("site_users_only");
				if ($site_users_only != 1) {
					$site_users_only = "no";
				}else {
					$site_users_only = (!is_user_logged_in?"yes":"no");
				}
				
				$confirm_email = askme_options("confirm_email");
				$user_review = askme_options("user_review");
				
				if (is_user_logged_in) {
					$user_id = get_current_user_id();
					$if_user_id = get_user_by("id",$user_id);
				}
				
				if (is_user_logged_in && $confirm_email == 1) {
					if (isset($if_user_id->caps["activation"]) && $if_user_id->caps["activation"] == 1) {
						$site_users_only = "yes";
					}
				}
				
				if (is_user_logged_in && $user_review == 1) {
					if (isset($if_user_id->caps["ask_under_review"]) && $if_user_id->caps["ask_under_review"] == 1) {
						$site_users_only = "yes";
					}
				}

				$site_users_only = apply_filters("askme_site_users_only",$site_users_only,(isset($if_user_id)?$if_user_id:array()));
				
				$adv_404 = askme_options("adv_404");
				if (is_404() && $adv_404 == 1) {
					$adv_404 = "on";
				}else {
					$adv_404 = "";
				}
				
				if ($site_users_only != "yes") {
					if (($adv_404 != "on" && is_404()) || !is_404()) {
						if (is_single() || is_page()) {
							$vbegy_content_adv_type = askme_post_meta('vbegy_content_adv_type','radio',$post->ID);
							$vbegy_content_adv_link = askme_post_meta('vbegy_content_adv_link','radio',$post->ID);
							$vbegy_content_adv_code = askme_post_meta('vbegy_content_adv_code','textarea',$post->ID);
							$vbegy_content_adv_href = askme_post_meta('vbegy_content_adv_href','text',$post->ID);
							$vbegy_content_adv_img = askme_post_meta('vbegy_content_adv_img','upload',$post->ID);
						}
						
						if ((is_single() || is_page()) && (($vbegy_content_adv_type == "display_code" && $vbegy_content_adv_code != "") || ($vbegy_content_adv_type == "custom_image" && $vbegy_content_adv_img != ""))) {
							$content_adv_type = $vbegy_content_adv_type;
							$content_adv_link = $vbegy_content_adv_link;
							$content_adv_code = $vbegy_content_adv_code;
							$content_adv_href = $vbegy_content_adv_href;
							$content_adv_img = $vbegy_content_adv_img;
						}else {
							$content_adv_type = askme_options("content_adv_type");
							$content_adv_link = askme_options("content_adv_link");
							$content_adv_code = askme_options("content_adv_code");
							$content_adv_href = askme_options("content_adv_href");
							$content_adv_img = askme_options("content_adv_img");
						}
						if (($content_adv_type == "display_code" && $content_adv_code != "") || ($content_adv_type == "custom_image" && $content_adv_img != "")) {
							echo '<div class="clearfix"></div>
							<div class="advertising advertising-footer">';
							if ($content_adv_type == "display_code") {
								echo do_shortcode(stripslashes($content_adv_code));
							}else {
								if ($content_adv_href != "") {
									echo '<a'.($content_adv_link == "new_page"?" target='_blank'":"").' href="'.$content_adv_href.'">';
								}
								echo '<img alt="" src="'.$content_adv_img.'">';
								if ($content_adv_href != "") {
									echo '</a>';
								}
							}
							echo '</div><!-- End advertising -->
							<div class="clearfix"></div>';
						}
					}
				}
				?>
				
				</div><!-- End main -->
				<?php
				if (!is_404() && $site_users_only != "yes") {
					$sidebar_width = askme_options("sidebar_width");
					
					if (is_single() || is_page()) {
						$custom_page_setting = askme_post_meta('vbegy_custom_page_setting','checkbox',$post->ID);
					}
					$sticky_sidebar_class = "";
					if ((is_single() || is_page()) && isset($custom_page_setting) && $custom_page_setting == 1) {
						$sticky_sidebar = askme_post_meta('vbegy_sticky_sidebar_s','checkbox',$post->ID);
					}else {
						$sticky_sidebar = askme_options("sticky_sidebar");
					}
					if ($sticky_sidebar == 1) {
						$sticky_sidebar_class = " sticky-sidebar";
					}
					?>
					<aside class="<?php echo (isset($sidebar_width) && $sidebar_width != ""?$sidebar_width:"col-md-3")?> sidebar<?php echo esc_attr($sticky_sidebar_class);?>">
						<?php get_sidebar();?>
					</aside><!-- End sidebar -->
				<?php }?>
				<div class="clearfix"></div>
			</div><!-- End with-sidebar-container -->
		</div><!-- End row -->
	</section><!-- End container -->
	<?php $footer_skin = askme_options("footer_skin");
	$footer_layout = askme_options("footer_layout");
	if ($site_users_only != "yes") {
		if ($footer_layout != "footer_no") {?>
			<footer id="footer" class="<?php if ($footer_skin == "footer_light") {echo "footer_light_top";}else {echo "footer_dark";}?>">
				<section class="container">
					<div class="row">
						<?php if ($footer_layout == "footer_1c") {?>
							<div class="col-md-12">
								<?php dynamic_sidebar('footer_1c_sidebar');?>
							</div>
						<?php }else if ($footer_layout == "footer_2c") {?>
							<div class="col-md-6">
								<?php dynamic_sidebar('footer_1c_sidebar');?>
							</div>
							<div class="col-md-6">
								<?php dynamic_sidebar('footer_2c_sidebar');?>
							</div>
						<?php }else if ($footer_layout == "footer_3c") {?>
							<div class="col-md-4">
								<?php dynamic_sidebar('footer_1c_sidebar');?>
							</div>
							<div class="col-md-4">
								<?php dynamic_sidebar('footer_2c_sidebar');?>
							</div>
							<div class="col-md-4">
								<?php dynamic_sidebar('footer_3c_sidebar');?>
							</div>
						<?php }else if ($footer_layout == "footer_4c") {?>
							<div class="col-md-3">
								<?php dynamic_sidebar('footer_1c_sidebar');?>
							</div>
							<div class="col-md-3">
								<?php dynamic_sidebar('footer_2c_sidebar');?>
							</div>
							<div class="col-md-3">
								<?php dynamic_sidebar('footer_3c_sidebar');?>
							</div>
							<div class="col-md-3">
								<?php dynamic_sidebar('footer_4c_sidebar');?>
							</div>
						<?php }else if ($footer_layout == "footer_5c") {?>
							<div class="col-md-4">
								<?php dynamic_sidebar('footer_1c_sidebar');?>
							</div>
							<div class="col-md-2">
								<?php dynamic_sidebar('footer_2c_sidebar');?>
							</div>
							<div class="col-md-3">
								<?php dynamic_sidebar('footer_3c_sidebar');?>
							</div>
							<div class="col-md-3">
								<?php dynamic_sidebar('footer_4c_sidebar');?>
							</div>
						<?php }?>
					</div><!-- End row -->
				</section><!-- End container -->
			</footer><!-- End footer -->
		<?php }
	}?>
	<footer id="footer-bottom" class="<?php if ($footer_skin == "footer_light") {echo "footer_light_bottom";}if ($footer_layout == "footer_no") {echo " no-footer";}?>">
		<section class="container">
			<div class="copyrights f_left"><?php echo stripcslashes(askme_options("footer_copyrights"))?></div>
			<?php $social_icon_f = askme_options("social_icon_f");
			if ($social_icon_f == 1) {?>
				<div class="social_icons f_right">
					<?php $footer_social = true;
					include locate_template("includes/social.php");?>
				</div><!-- End social_icons -->
			<?php }?>
		</section><!-- End container -->
	</footer><!-- End footer-bottom -->
</div><!-- End wrap -->
<?php $go_up_button = askme_options("go_up_button");
if ($go_up_button == 1) {?>
	<div class="go-up"><i class="icon-chevron-up"></i></div>
<?php }
$ask_button = askme_options("ask_button");
if ($ask_button == 1) {?>
	<a href="<?php echo esc_url(get_page_link(askme_options('add_question')))?>" title="<?php echo esc_attr__("Ask a question","vbegy")?>" class="ask-button"><i class="icon-pencil"></i></a>
<?php }
wp_footer(); ?>
</body>
</html>