<?php if (isset($sticky_questions) && is_array($sticky_questions) && !empty($sticky_questions) && $paged == 1) {
	if (isset($custom_args) && is_array($custom_args) && !empty($custom_args)) {
		$custom_args = $custom_args;
	}else {
		$custom_args = array();
	}
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
	$args = array_merge($author__not_in,$custom_args,array("nopaging" => true,"post_type" => ask_questions_type,"post__in" => $sticky_questions));
	query_posts($args);
	global $blog_style,$authordata,$question_bump_template,$question_vote_template,$k;
	if (!isset($k)) {
		$k = 0;
	}
	if (have_posts() ) :
		while (have_posts() ) : the_post();
			$k++;
			include ("theme-parts/question.php");
		endwhile;
		$is_questions_sticky = true;
	endif;
	wp_reset_query();
}
$post__not_in = array();
$sticky_questions = get_option("sticky_questions");
if (isset($sticky_questions) && is_array($sticky_questions) && !empty($sticky_questions)) {
	$post__not_in = array("post__not_in" => $sticky_questions);
}?>