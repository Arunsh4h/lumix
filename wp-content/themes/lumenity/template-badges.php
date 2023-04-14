<?php /* Template Name: Badges & Points */
get_header();
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		$badges_style = askme_options("badges_style");
		$badge_key = "badge_points";
		if ($badges_style == "by_groups_points") {
			$badges = askme_options("badges_groups_points");
		}else if ($badges_style == "by_questions") {
			$badges = askme_options("badges_questions");
			$badge_key = "badge_questions";
		}else if ($badges_style == "by_answers") {
			$badges = askme_options("badges_answers");
			$badge_key = "badge_answers";
		}else {
			$badges = askme_options("badges");
		}
		if (isset($badges) && is_array($badges) && !empty($badges)) {
			$points_badges = array_column($badges,$badge_key);
	    	array_multisort($points_badges,SORT_ASC,$badges);
	    }
		if ($badges_style != "by_groups" && isset($badges) && is_array($badges) && !empty($badges)) {?>
			<div class="page-content page-content-user-profile">
				<div class="user-profile-widget">
					<div class="boxedtitle page-title"><h2><?php _e("Badges","vbegy")?></h2></div>
					<div class="ul_list ul_list-icon-ok">
						<ul>
							<?php foreach ($badges as $badges_k => $badges_v) {
								if (isset($badges_v[$badge_key]) && $badges_v[$badge_key] != "") {
									$badge_values = (int)$badges_v[$badge_key];?>
									<li style="background-color: <?php echo esc_html($badges_v["badge_color"])?>;color: #FFF;"><?php echo strip_tags(stripslashes($badges_v["badge_name"]),"<i>")?><span> ( <span><?php echo esc_html($badge_values)?></span> ) 
										<?php if ($badges_style == "by_questions") {
											echo _n("Question","Questions",$badge_values,"vbegy");
										}else if ($badges_style == "by_answers") {
											echo _n("Answer","Answers",$badge_values,"vbegy");
										}else {
											echo _n("Point","Points",$badge_values,"vbegy");
										}?>
									</span></li>
								<?php }
							}?>
						</ul>
					</div>
				</div><!-- End user-profile-widget -->
			</div><!-- End page-content -->
		<?php }
		$active_points = askme_options("active_points");
		if ($active_points == 1) {
			$points_array = askme_get_points();
			if (is_array($points_array) && !empty($points_array)) {?>
				<div class="page-content page-content-user-profile">
					<div class="user-profile-widget">
						<div class="boxedtitle page-title"><h2><?php _e("Points","vbegy")?></h2></div>
						<div class="ul_list ul_list-icon-ok">
							<ul>
								<?php foreach ($points_array as $key => $value) {
									if (isset($value["points"]) && $value["points"] > 0) {?>
										<li><?php echo askme_get_points_name($key)?><span> ( <span><?php echo ($value["points"])?></span> ) </span></li>
									<?php }
								}?>
							</ul>
						</div>
					</div><!-- End user-profile-widget -->
				</div><!-- End page-content -->
			<?php }
		}
	endwhile; endif;
get_footer();?>