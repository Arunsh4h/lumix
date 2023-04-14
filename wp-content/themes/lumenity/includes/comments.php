<?php $paged = askme_paged();
if (!isset($not_get_result_page)) {
	$search_value = esc_js(get_query_var('search') != ""?wp_unslash(sanitize_text_field(get_query_var('search'))):wp_unslash(sanitize_text_field(get_query_var('s'))));
}
$get_current_user_id = get_current_user_id();
$block_users = askme_options("block_users");
$author__not_in = array();
if ($block_users == 1) {
	if ($get_current_user_id > 0) {
		$get_block_users = get_user_meta($get_current_user_id,"askme_block_users",true);
		if (is_array($get_block_users) && !empty($get_block_users)) {
			$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
		}
	}
}

$args = array_merge($author__not_in,array('search' => $search_value,"meta_query" => array(array("key" => "answer_question_private","compare" => "NOT EXISTS")),'post_type' => ($search_type == "answers"?ask_questions_type:"post")));

$comments_query = new WP_Comment_Query;
$comments_all = $comments_query->query($args);

$current = max(1,$paged);
$post_number = apply_filters('ask_search_per_page',get_option("posts_per_page"));

if (isset($not_get_result_page) && $not_get_result_page == true) {
	if (!empty($comments_all) && !is_wp_error($comments_all)) {
		foreach ($comments_all as $comment) {
			$k_search++;
			if ($search_result_number >= $k_search) {
				$comment_id = esc_attr($comment->comment_ID);
				$get_post = get_post($comment->comment_post_ID);
				$post_author = $get_post->post_author;
				$yes_private = ask_private($comment->comment_post_ID,$post_author,$get_current_user_id);
				$yes_private_answer = ask_private_answer($comment_id,$comment->user_id,$get_current_user_id,$post_author);
				if ($yes_private == 1 && $yes_private_answer == 1) {
					echo '<li><a href="'.get_permalink($comment->comment_post_ID).'#comment-'.$comment_id.'">'.str_ireplace($search_value,"<strong>".$search_value."</strong>",wp_html_excerpt($comment->comment_content,60)).'</a></li>';
				}
			}else {
				echo "<li><a href='".esc_url(add_query_arg(array("search" => $search_value,"search_type" => $search_type),(isset($search_page) && $search_page != ""?get_page_link($search_page):"")))."'>".__("View all results.","vbegy")."</a></li>";
				exit;
			}
		}
	}else {
		echo "<li class='no-search-result'>".__("No results found.","vbegy")."</li>";
	}
}else {
	include( get_template_directory() . '/includes/comments-results.php' );
}?>