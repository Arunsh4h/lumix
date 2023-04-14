<?php /* Template Name: Recent question  */
get_header();
	$paged            = askme_paged();
	$sticky_questions = get_option('sticky_questions');
	$active_sticky    = true;
	include locate_template("sticky-question.php");
	
	$custom_args = (isset($custom_args) && is_array($custom_args)?$custom_args:array());
	$block_users = askme_options("block_users");
	if ($block_users == 1) {
		$user_get_current_user_id = get_current_user_id();
		if ($user_get_current_user_id > 0) {
			$get_block_users = get_user_meta($user_get_current_user_id,"askme_block_users",true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__not_in = array("author__not_in" => $get_block_users);
			}
		}
	}
	$args = array_merge($author__not_in,$custom_args,$post__not_in,array("paged" => $paged,"post_type" => ask_questions_type,"posts_per_page" => get_option("posts_per_page")));
	query_posts($args);
	$active_sticky = false;
	get_template_part("loop-question");
	vpanel_pagination();
	wp_reset_query();
get_footer();?>