<?php /* Template Name: Comments  */
get_header();
	$comment_type      = askme_post_meta('vbegy_comment_type','radio',$post->ID);
	$orderby_answers_a = askme_post_meta('vbegy_orderby_answers_a','radio',$post->ID);
	$order_answers     = askme_post_meta('vbegy_order_answers','radio',$post->ID);
	$answers_number    = askme_post_meta('vbegy_answers_number','text',$post->ID);
	$rows_per_page     = ($answers_number != "" && $answers_number > 0?$answers_number:get_option("posts_per_page"));
	$post_type         = ($comment_type == "answers"?ask_questions_type:"post");
	$paged             = askme_paged();
	$offset		       = ($paged-1)*$rows_per_page;
	$block_users = askme_options("block_users");
	if ($block_users == 1) {
		$user_get_current_user_id = get_current_user_id();
		if ($user_get_current_user_id > 0) {
			$get_block_users = get_user_meta($user_get_current_user_id,"askme_block_users",true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__not_in = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
			}
		}
	}
	if ($orderby_answers_a == 'votes' && ($post_type == ask_questions_type || $post_type == ask_asked_questions_type)) {
		$comments	    = get_comments(array_merge($author__not_in,array('order' => (isset($order_answers)?$order_answers:'DESC'),'orderby' => 'meta_value_num','meta_key' => 'comment_vote',"post_type" => $post_type,"status" => "approve")));
		$query		    = get_comments(array_merge($author__not_in,array('order' => (isset($order_answers)?$order_answers:'DESC'),'orderby' => 'meta_value_num','meta_key' => 'comment_vote',"offset" => $offset,"post_type" => $post_type,"status" => "approve","number" => $rows_per_page)));
	}else if ($orderby_answers_a == 'oldest') {
		$comments	    = get_comments(array_merge($author__not_in,array('order' => 'ASC','orderby' => 'comment_date',"post_type" => $post_type,"status" => "approve")));
		$query		    = get_comments(array_merge($author__not_in,array('order' => 'ASC','orderby' => 'comment_date',"offset" => $offset,"post_type" => $post_type,"status" => "approve","number" => $rows_per_page)));
	}else {
		$comments	    = get_comments(array_merge($author__not_in,array('order' => (isset($order_answers) && $orderby_answers_a == 'date'?$order_answers:'DESC'),'orderby' => 'comment_date',"post_type" => $post_type,"status" => "approve")));
		$query		    = get_comments(array_merge($author__not_in,array('order' => (isset($order_answers) && $orderby_answers_a == 'date'?$order_answers:'DESC'),'orderby' => 'comment_date',"offset" => $offset,"post_type" => $post_type,"status" => "approve","number" => $rows_per_page)));
	}
	
	$total_comments = count($comments);
	$total_query    = count($query);
	$total_pages    = (int)ceil($total_comments/$rows_per_page);
	if ($query) {?>
		<div id="commentlist" class="page-content">
			<ol class="commentlist clearfix">
				<?php $k = 0;
				foreach ($query as $comment) {
					$k++;
					$comment_vote = get_comment_meta($comment->comment_ID,'comment_vote',true);
					$comment_vote = (!empty($comment_vote)?$comment_vote:0);
					if ($comment->user_id != 0){
						$user_login_id_l = get_user_by("id",$comment->user_id);
					}
					$yes_private = ask_private($comment->comment_post_ID,get_post($comment->comment_post_ID)->post_author,get_current_user_id());
					if ($yes_private == 1) {
						$answer_type = ($post_type == "post"?"comment":"answer");
						include locate_template("includes/answers.php");
					}else {?>
						<li class="comment"><div class="comment-body clearfix"><?php _e("Sorry, this is a private answer.","vbegy");?></div></li>
					<?php }
				}?>
			</ol>
		</div>
		<?php if ($total_comments > $total_query) {
			$page_paged = (get_query_var("paged") != ""?"paged":(get_query_var("page") != ""?"page":"paged"));
			echo '<div class="pagination">';
			$current_page = max(1,$paged);
			echo paginate_links(array(
				'base' => add_query_arg($page_paged,'%#%'),
				'format' => 'page/%#%/',
				'current' => $current_page,
				'show_all' => false,
				'total' => $total_pages,
				'prev_text' => '<i class="icon-angle-left"></i>',
				'next_text' => '<i class="icon-angle-right"></i>',
			));
			echo '</div><div class="clearfix"></div>';
		}
	}else {
		echo "<div class='page-content page-content-user'><p class='no-item'>".__("No Answers Found.","vbegy")."</p></div>";
	}
get_footer();?>