<?php /* Template name: Follow answer */
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
$show_point_favorite = get_user_meta($user_login->ID,"show_point_favorite",true);
if ($show_point_favorite != 1 && $owner == false) {
	wp_redirect(home_url());
}
get_header();
	include (get_template_directory() . '/includes/author-head.php');
	$following_me_array = get_user_meta($user_login->ID,"following_me",true);
	$block_users = askme_options("block_users");
	$author__not_in = array();
	if ($block_users == 1) {
		$user_id = $user_login->ID;
		if ($user_id > 0) {
			$get_block_users = get_user_meta($user_id,"askme_block_users",true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__not_in = $get_block_users;
			}
		}
	}
	if (is_array($following_me_array) && !empty($following_me_array)) {
		$following_me_array = array_diff($following_me_array,$author__not_in);
	}
	
	if (isset($following_me_array) && is_array($following_me_array) && !empty($following_me_array)) {
		$rows_per_page = get_option("posts_per_page");
		$paged         = askme_paged();
		$offset		   = ($paged-1)*$rows_per_page;
		
		$comments_all = get_comments(array("author__in" => $following_me_array,"status" => "approve",'post_type' => ask_questions_type));
		
		if (empty($following_me_array)) {
			$comments_all = array();
		}
		
		if ($comments_all) {
			$current = max( 1, $paged );
			$start = ($current - 1) * $rows_per_page;
			$end = $start + $rows_per_page;
			$pagination_args = array(
				'base' => @esc_url(add_query_arg('paged','%#%')),
				'format' => 'paged/%#%/?u='.$get_u,
				'total' => (int)ceil(count($comments_all)/$rows_per_page),
				'current' => $current,
				'show_all' => false,
				'prev_text' => '<i class="icon-angle-left"></i>',
				'next_text' => '<i class="icon-angle-right"></i>',
			);
			
			if( !empty($wp_query->query_vars['s']) )
				$pagination_args['add_args'] = array('s'=>get_query_var('s'));?>
			
			<div id="commentlist" class="page-content">
				<ol class="commentlist clearfix">
					<?php $k = 0;
					$end = (sizeof($comments_all) < $end) ? sizeof($comments_all) : $end;
					for ($k_loop = $start;$k_loop < $end ;++$k_loop ) {
						$k++;
						$comment = $comments_all[$k_loop];
						$comment_vote = get_comment_meta($comment->comment_ID,'comment_vote',true);
						$comment_vote = (!empty($comment_vote)?$comment_vote:0);
						if ($comment->user_id != 0){
							$user_login_id_l = get_user_by("id",$comment->user_id);
						}
						$yes_private = ask_private($comment->comment_post_ID,$post->post_author,get_current_user_id());
						if ($yes_private == 1) {
							$answer_type = "answer";
							include locate_template("includes/answers.php");
						}else {?>
							<li class="comment"><div class="comment-body clearfix"><?php _e("Sorry, this is a private answer.","vbegy");?></div></li>
						<?php }
					}?>
				</ol>
			</div>
		<?php }else {echo "<div class='page-content page-content-user'><div class='user-questions'><p class='no-item'>".__("No answers yet .","vbegy")."</p></div></div>";}
	}else {
		echo "<div class='page-content page-content-user'><div class='user-questions'><p class='no-item'>".__("There are no user follow yet .","vbegy")."</p></div></div>";
	}
	if (isset($following_me_array) && is_array($following_me_array) && !empty($following_me_array) && $comments_all && $pagination_args["total"] > 1 ) : ?>
		<div class='pagination'><?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?></div>
	<?php endif;
get_footer();?>