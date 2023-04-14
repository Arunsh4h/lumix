<?php /* Template Name: Most Responses / answers */
get_header();
	$paged = askme_paged();
	$author__not_in = array();
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
	$args = array_merge($author__not_in,array("paged" => $paged,"post_type" => ask_questions_type,"orderby" => "comment_count","posts_per_page" => get_option("posts_per_page")));
	add_filter('posts_where', 'ask_filter_where_more');
	query_posts($args);
	get_template_part("loop-question");
	vpanel_pagination();
	wp_reset_query();
	remove_filter( 'posts_where', 'ask_filter_where_more' );
get_footer();?>