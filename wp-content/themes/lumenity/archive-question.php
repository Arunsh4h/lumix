<?php get_header();
	global $vbegy_sidebar_all;
	$paged             = askme_paged();
	$sticky_questions  = get_option('sticky_questions');
	$active_sticky     = true;
	$block_users = askme_options("block_users");
	$author__not_in = array();
	if ($block_users == 1) {
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			$get_block_users = get_user_meta($user_id,"askme_block_users",true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__not_in = array("author__not_in" => $get_block_users);
			}
		}
	}
	$custom_args = $author__not_in;
	include locate_template("sticky-question.php");
	
	$custom_args = (isset($custom_args) && is_array($custom_args)?$custom_args:array());
	$args = array_merge($custom_args,$post__not_in,array("paged" => $paged,"post_type" => ask_questions_type));
	query_posts($args);
	$active_sticky = false;
	get_template_part("loop-question","archive");
	vpanel_pagination();
	wp_reset_query();
get_footer();?>