<?php $content_before_tabs   = askme_post_meta('vbegy_content_before_tabs','type=wysiwyg',$the_page_id);
$content_after_tabs    = askme_post_meta('vbegy_content_after_tabs','type=wysiwyg',$the_page_id);
$posts_per_page        = askme_post_meta('vbegy_posts_per_page','text',$the_page_id);
$vbegy_index_tabs      = askme_post_meta('vbegy_index_tabs','checkbox',$the_page_id);
$vbegy_pagination_tabs = askme_post_meta('vbegy_pagination_tabs','checkbox',$the_page_id);
$vbegy_home_tabs       = askme_post_meta('vbegy_home_tabs','type=checkbox_list',$the_page_id);
$home_tabs             = array(
	"recent-questions" => array("sort" => esc_html__('Recent Questions','vbegy'),"value" => "recent-questions"),
	"most-answers"     => array("sort" => esc_html__('Most Answered','vbegy'),"value" => "most-answers"),
	"answers"          => array("sort" => esc_html__('Answers','vbegy'),"value" => "answers"),
	"no-answers"       => array("sort" => esc_html__('No Answers','vbegy'),"value" => "no-answers"),
	"most-visit"       => array("sort" => esc_html__('Most Visited','vbegy'),"value" => "most-visit"),
	"most-vote"        => array("sort" => esc_html__('Most Voted','vbegy'),"value" => "most-vote"),
	"question-bump"    => array("sort" => esc_html__('Bump Question','vbegy'),"value" => ""),
	"recent-posts"     => array("sort" => esc_html__('Recent Posts','vbegy'),"value" => ""),
);
$vbegy_home_tabs       = (is_array($vbegy_home_tabs) && !empty($vbegy_home_tabs)?$vbegy_home_tabs:$home_tabs);
$posts_meta            = askme_options("post_meta");
$posts_per_page        = ($posts_per_page != "")?$posts_per_page:get_option("posts_per_page");
$paged                 = askme_paged();
$sticky_questions      = get_option('sticky_questions');
$active_points         = askme_options("active_points");
$question_bump         = askme_options("question_bump");
$block_users           = askme_options("block_users");
$get_current_user_id   = get_current_user_id();
$author__not_in_1 = $author__not_in_2 = array();
if ($block_users == 1) {
	if ($get_current_user_id > 0) {
		$get_block_users = get_user_meta($get_current_user_id,"askme_block_users",true);
		if (is_array($get_block_users) && !empty($get_block_users)) {
			$author__not_in_1 = array("author__not_in" => $get_block_users);
			$author__not_in_2 = array("post_author__not_in" => $get_block_users,"author__not_in" => $get_block_users);
		}
	}
}

if ($vbegy_index_tabs == 1 || $vbegy_index_tabs == "tabs") {
	if ($content_before_tabs != "") {
		echo "<div class='clearfix'></div>".do_shortcode($content_before_tabs)."<div class='clearfix'></div>";
	}?>
	<div class="tabs-warp question-tab">
		<?php do_action("askme_bofore_tabs");
		$pagination_tabs = array();
		if ($vbegy_pagination_tabs == 1) {
			$pagination_tabs = array("paged" => $paged);
		}
		include locate_template("includes/slugs.php");
		$first_one = askme_home_setting($vbegy_home_tabs,$the_page_id);?>
		<ul class="tabs not-tabs">
			<?php if (isset($vbegy_home_tabs) && is_array($vbegy_home_tabs)) {
				if (isset($first_one) && $first_one != "") {
					askme_home_tabs($vbegy_home_tabs,$first_one,(isset($the_page_id) && $the_page_id != ""?$the_page_id:""));
				}
			}?>
		</ul>

		<?php do_action("askme_after_tabs");
		$get_loop = $orderby_post = "";
		if (isset($wp_page_template) && $wp_page_template == "template-home.php" && isset($first_one) && $first_one != "") {
			$get_tax = get_term_by('slug',$first_one,ask_question_category);
		}
		$tabs_available = apply_filters("askme_home_page_tabs_available",false,$vbegy_home_tabs,$first_one);
		if (isset($first_one) && $first_one != "" && ($tabs_available == true || ($first_one == $recent_posts_slug && isset($vbegy_home_tabs["recent-posts"]["value"]) && $vbegy_home_tabs["recent-posts"]["value"] != "" && $vbegy_home_tabs["recent-posts"]["value"] != "0") || ($first_one == $answers_slug && isset($vbegy_home_tabs["answers"]["value"]) && $vbegy_home_tabs["answers"]["value"] != "" && $vbegy_home_tabs["answers"]["value"] != "0") || ($first_one == $recent_questions_slug && isset($vbegy_home_tabs["recent-questions"]["value"]) && $vbegy_home_tabs["recent-questions"]["value"] != "" && $vbegy_home_tabs["recent-questions"]["value"] != "0") || ($first_one == $most_answers_slug && isset($vbegy_home_tabs["most-answers"]["value"]) && $vbegy_home_tabs["most-answers"]["value"] != "" && $vbegy_home_tabs["most-answers"]["value"] != "0") || ($first_one == $no_answers_slug && isset($vbegy_home_tabs["no-answers"]["value"]) && $vbegy_home_tabs["no-answers"]["value"] != "" && $vbegy_home_tabs["no-answers"]["value"] != "0") || ($first_one == $most_visit_slug && isset($vbegy_home_tabs["most-visit"]["value"]) && $vbegy_home_tabs["most-visit"]["value"] != "" && $vbegy_home_tabs["most-visit"]["value"] != "0") || ($first_one == $most_vote_slug && isset($vbegy_home_tabs["most-vote"]["value"]) && $vbegy_home_tabs["most-vote"]["value"] != "" && $vbegy_home_tabs["most-vote"]["value"] != "0") || ($first_one == $recent_posts_slug && isset($vbegy_home_tabs["recent-posts"]["value"]) && $vbegy_home_tabs["recent-posts"]["value"] != "" && $vbegy_home_tabs["recent-posts"]["value"] != "0") || ($question_bump == 1 && $active_points == 1 && $first_one == $question_bump_slug && isset($vbegy_home_tabs["question-bump"]["value"]) && $vbegy_home_tabs["question-bump"]["value"] != "" && $vbegy_home_tabs["question-bump"]["value"] != "0"))) {
			$get_loop = true;
		}else if (isset($first_one) && $first_one != "" && (($first_one == "all" && isset($vbegy_home_tabs["cat-q-0"]["value"]) && $vbegy_home_tabs["cat-q-0"]["value"] != "" && $vbegy_home_tabs["cat-q-0"]["value"] == "q-0") || (isset($get_tax->term_id) && $get_tax->term_id > 0))) {
			$get_loop = true;
		}else {
			echo '<div class="alert-message error"><i class="icon-ok"></i><p><span>'.esc_html__("Not found","vbegy").'</span><br>'.esc_html__("Sorry, this page is not found.","vbegy").'</p></div>';
		}
		$get_loop = apply_filters("askme_home_get_loop",$get_loop);

		if ($get_loop == true) {
			if (isset($first_one) && $first_one != "") {
	    		if ($first_one == $recent_questions_slug) {?>
				    <div class="tab-inner-warp">
						<div class="tab-inner">
							<?php $question_bump_template = $question_vote_template = false;
							$active_sticky = true;
							$k = 0;
							include locate_template("sticky-question.php");
							
							$is_questions_sticky = false;
							$args = array_merge($author__not_in_1,$pagination_tabs,$post__not_in,array("post_type" => ask_questions_type,"posts_per_page" => $posts_per_page));
							$args = apply_filters("askme_recent_questions_query",$args,$posts_per_page,$pagination_tabs,$post__not_in);
							query_posts($args);
							get_template_part("loop-question");
							if ($vbegy_pagination_tabs == 1) {
								vpanel_pagination();
							}
							wp_reset_query();?>
					    </div>
					</div>
				<?php }else if ($first_one == $most_answers_slug) {?>
					<div class="tab-inner-warp">
						<div class="tab-inner">
							<?php $args = array_merge($author__not_in_1,$pagination_tabs,array("post_type" => ask_questions_type,"posts_per_page" => $posts_per_page,'orderby' => 'comment_count','order' => "DESC"));
							$args = apply_filters("askme_most_responses_query",$args,$posts_per_page,$pagination_tabs);
							query_posts($args);
							$question_bump_template = $active_sticky = $question_vote_template = false;
							$k = 0;
							get_template_part("loop-question");
							if ($vbegy_pagination_tabs == 1) {
								vpanel_pagination();
							}
							wp_reset_query();?>
					    </div>
					</div>
				<?php }else if ($first_one == $no_answers_slug) {?>
					<div class="tab-inner-warp">
						<div class="tab-inner">
							<?php
							$question_bump_template = $active_sticky = $question_vote_template = false;
							$k = 0;
							$args = array_merge($author__not_in_1,array("paged" => $paged,"post_type" => ask_questions_type,"posts_per_page" => $posts_per_page,"orderby" => array("comment_count" => "DESC","date" => "DESC")));
							add_filter('posts_where', 'ask_filter_where');
							query_posts($args);
							get_template_part("loop-question");
							vpanel_pagination();
							remove_filter( 'posts_where', 'ask_filter_where' );
							wp_reset_query();?>
					    </div>
					</div>
				<?php }else if ($first_one == $answers_slug) {?>
					<div class="tab-inner-warp">
						<div class="tab-inner">
							<?php $k = 0;
							$args = apply_filters("askme_recently_answered_query",array_merge($author__not_in_2,array('status' => 'approve','post_type' => ask_questions_type)));
							$comments_all = get_comments($args);
							$max_num_pages = $total = ceil(sizeof($comments_all)/$posts_per_page);
							$current_page = max(1,$paged);
							if (!empty($comments_all)) {
								$pagination_args = array(
									'base' => @esc_url(add_query_arg('paged','%#%')),
									'format' => 'page/%#%/',
									'show_all' => false,
									'current' => $current_page,
									'total' => $total,
									'prev_text' => '<i class="icon-angle-left"></i>',
									'next_text' => '<i class="icon-angle-right"></i>',
								);
								
								$start = ($current_page - 1) * $posts_per_page;
								$end   = $start + $posts_per_page;?>
								<div id="commentlist" class="page-content">
									<ol class="commentlist clearfix">
										<?php $end = (sizeof($comments_all) < $end) ? sizeof($comments_all) : $end;
										for ($i = $start;$i < $end ;++$i ) {$k++;
											$comment = $comments_all[$i];
											$comment_vote = get_comment_meta($comment->comment_ID,'comment_vote',true);
											$comment_vote = (!empty($comment_vote)?$comment_vote:0);
											if ($comment->user_id != 0){
												$user_login_id_l = get_user_by("id",$comment->user_id);
											}
											$yes_private = ask_private($comment->comment_post_ID,get_post($comment->comment_post_ID)->post_author,$get_current_user_id);
											if ($yes_private == 1) {
												$answer_type = "answer";
												include locate_template("includes/answers.php");
											}
										}?>
									</ol>
								</div>
								<?php if ($comments_all && $pagination_args["total"] > 1 && $vbegy_pagination_tabs == 1) {
									echo '<div class="pagination">'.paginate_links($pagination_args).'</div><div class="clearfix"></div>';
								}
							}else {
								echo "<div class='page-content page-content-user'><p class='no-item'>".__("No answers Found.","vbegy")."</p></div>";
							}?>
					    </div>
					</div>
				<?php }else if ($first_one == $most_visit_slug) {?>
					<div class="tab-inner-warp">
						<div class="tab-inner">
							<?php $args = array_merge($author__not_in_1,$pagination_tabs,array("posts_per_page" => $posts_per_page,"post_type" => ask_questions_type,'orderby' => array('post_stats_order' => "DESC"),"meta_query" => array('post_stats_order' => array('type' => 'numeric',"key" => askme_get_meta_stats(),"value" => 0,"compare" => ">"))));
							query_posts($args);
							$question_bump_template = $active_sticky = $question_vote_template = false;
							$k = 0;
							get_template_part("loop-question");
							if ($vbegy_pagination_tabs == 1) {
								vpanel_pagination();
							}
							wp_reset_query();?>
					    </div>
					</div>
				<?php }else if ($first_one == $most_vote_slug) {?>
					<div class="tab-inner-warp">
						<div class="tab-inner">
							<?php $args = array_merge($author__not_in_1,$pagination_tabs,array("posts_per_page" => $posts_per_page,"post_type" => ask_questions_type,'orderby' => array('question_vote_order' => "DESC"),"meta_query" => array('question_vote_order' => array('type' => 'numeric',"key" => "question_vote","value" => 0,"compare" => ">"))));
							query_posts($args);
							$question_bump_template = $active_sticky = false;
							$question_vote_template = true;
							$k = 0;
							get_template_part("loop-question");
							if ($vbegy_pagination_tabs == 1) {
								vpanel_pagination();
							}
							wp_reset_query();?>
					    </div>
					</div>
				<?php }else if ($question_bump == 1 && $active_points == 1 && $first_one == $question_bump_slug) {?>
					<div class="tab-inner-warp">
						<div class="tab-inner">
							<?php if ($active_points == 1 && $question_bump == 1) {
								$args = array_merge($author__not_in_1,array("paged" => $paged,"post_type" => ask_questions_type,"posts_per_page" => $posts_per_page,'orderby' => array('question_points_order' => "DESC"),"meta_query" => array('question_points_order' => array('type' => 'numeric',"key" => "question_points","value" => 0,"compare" => ">="))));
								add_filter('posts_where', 'ask_filter_where');
								query_posts($args);
								$question_bump_template = true;
								$question_vote_template = $active_sticky = false;
								$k = 0;
								get_template_part("loop-question");
								if ($vbegy_pagination_tabs == 1) {
									vpanel_pagination();
								}
								remove_filter( 'posts_where', 'ask_filter_where' );
								wp_reset_query();
							}else {
								echo "<div class='page-content page-content-user'><p class='no-item'>".__("This page is not active .","vbegy")."</p></div>";
							}?>
					    </div>
					</div>
				<?php }else if ($first_one == $recent_posts_slug) {?>
					<div class="tab-inner-warp">
						<div class="tab-inner">
							<?php $vbegy_sidebar_all = vpanel_sidebars("sidebar_where");
							$args = array_merge($author__not_in_1,$pagination_tabs,array("post_type" => "post","posts_per_page" => $posts_per_page));
							$blog_style = askme_options("home_display");
							query_posts($args);
							$question_bump_template = $active_sticky = $question_vote_template = false;
							$k = 0;
							get_template_part("loop");
							if ($vbegy_pagination_tabs == 1) {
								vpanel_pagination();
							}
							wp_reset_query();?>
					    </div>
					</div>
				<?php }else if (isset($first_one) && $first_one != "" && is_string($first_one) && ($first_one == "all" || (isset($get_tax->term_id) && $get_tax->term_id > 0))) {?>
					<div class="tab-inner-warp">
						<div class="tab-inner">
							<?php $k = 0;
							$question_bump_template = $question_vote_template = false;
							$active_sticky = true;
							
							if (isset($get_tax->term_id) && $get_tax->term_id > 0) {
								$cat_array = $custom_args = array_merge($author__not_in_1,array('tax_query' => array(array('taxonomy' => ask_question_category,'field' => 'slug','terms' => $first_one))));
							}else {
								$cat_array = array();
							}
							include locate_template("sticky-question.php");
							
							$is_questions_sticky = false;
							$args = array_merge($author__not_in_1,$pagination_tabs,$cat_array,$post__not_in,array("post_type" => ask_questions_type,"posts_per_page" => $posts_per_page));
							$args = apply_filters("askme_all_categories_query",$args,$posts_per_page,$pagination_tabs,$post__not_in,$first_one);
							query_posts($args);
							get_template_part("loop-question");
							if ($vbegy_pagination_tabs == 1) {
								vpanel_pagination();
							}
							wp_reset_query();?>
					    </div>
					</div>
				<?php }
			}
		}
		vpanel_pagination();?>
	</div><!-- End tabs-warp -->
	<?php if ($content_after_tabs != "") {
		echo "<div class='clearfix'></div>".do_shortcode($content_after_tabs)."<div class='clearfix'></div>";
	}
}else if ($vbegy_index_tabs == 2 || $vbegy_index_tabs == "recent") {
	$args = array("paged" => $paged,"post_type" => ask_questions_type,"posts_per_page" => $posts_per_page);
	query_posts($args);
	get_template_part("loop-question");
	vpanel_pagination();
	wp_reset_query();
}else {
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		$date_format = (askme_options("date_format")?askme_options("date_format"):get_option("date_format"));
		$vbegy_what_post = askme_post_meta('vbegy_what_post','select',$post->ID);
		$vbegy_google = askme_post_meta('vbegy_google',"textarea",$post->ID);
		$video_id = askme_post_meta('vbegy_video_post_id',"select",$post->ID);
		$video_type = askme_post_meta('vbegy_video_post_type',"text",$post->ID);
		$vbegy_slideshow_type = askme_post_meta('vbegy_slideshow_type','select',$post->ID);
		$type = askme_video_iframe($video_type,$video_id,"post_meta","vbegy_video_post_id",$post->ID);
		$custom_page_setting = askme_post_meta('vbegy_custom_page_setting','checkbox',$post->ID);
		$post_meta_s = askme_post_meta('vbegy_post_meta_s','checkbox',$post->ID);
		$post_comments_s = askme_post_meta('vbegy_post_comments_s','checkbox',$post->ID);
		$vbegy_sidebar_all = vpanel_sidebars("sidebar_where");?>
		<article <?php post_class('post single-post');?> id="post-<?php echo $post->ID;?>">
			<div class="post-inner">
				<div class="post-img<?php if ($vbegy_what_post == "image" && !has_post_thumbnail()) {echo " post-img-0";}else if ($vbegy_what_post == "video") {echo " video_embed";}if ($vbegy_sidebar_all == "full") {echo " post-img-12";}else {echo " post-img-9";}?>">
					<?php include (get_template_directory() . '/includes/head.php');?>
				</div>
	        	<h2 class="post-title"><?php the_title()?></h2>
				<?php $posts_meta = askme_options("post_meta");
				if (($posts_meta == 1 && $post_meta_s == "") || ($posts_meta == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($posts_meta == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_meta_s) && $post_meta_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_meta_s) && $post_meta_s == 1)) {?>
					<div class="post-meta">
					    <span class="meta-author"><i class="icon-user"></i><?php the_author_posts_link();?></span>
					    <span class="meta-date"><i class="fa fa-calendar"></i><?php the_time($date_format);?></span>
					    <span class="meta-comment"><i class="fa fa-comments"></i><?php comments_popup_link(__('0 Comments', 'vbegy'), __('1 Comment', 'vbegy'), '% '.__('Comments', 'vbegy'));?></span>
					    <span class="post-view"><i class="icon-eye-open"></i><?php echo (int)get_post_meta($post->ID,askme_get_meta_stats(),true)?> <?php _e("views","vbegy");?></span>
					</div>
				<?php }?>
				<div class="post-content">
					<?php the_content();?>
					<div class="clearfix"></div>
				</div>
			</div><!-- End post-inner -->
		</article><!-- End article.post -->
		<?php $post_comments = askme_options("post_comments");
		if (($post_comments == 1 && $post_comments_s == "") || ($post_comments == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($post_comments == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_comments_s) && $post_comments_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_comments_s) && $post_comments_s == 1)) {
			comments_template();
		}
	endwhile; endif;
}