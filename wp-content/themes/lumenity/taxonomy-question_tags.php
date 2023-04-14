<?php get_header();
	$vbegy_sidebar_all = askme_options("sidebar_layout");
	$tag_description   = tag_description();
	$tax_slug          = get_term_by('slug',get_query_var('term'),esc_attr(get_query_var('taxonomy')));
	$paged             = askme_paged();
	$sticky_questions  = get_option('sticky_questions');
	$active_sticky     = true;
	if (!empty($tag_description)) {?>
		<article class="post clearfix">
			<div class="post-inner">
		        <h2 class="post-title"><?php echo esc_html__("Tag","vbegy")." : ".esc_attr(single_tag_title("", false));?></a></h2>
		        <div class="post-content">
		            <?php echo $tag_description?>
		        </div><!-- End post-content -->
		    </div><!-- End post-inner -->
		</article><!-- End article.post -->
	<?php }
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

	$custom_args = array_merge($author__not_in,array('tax_query' => array(array('taxonomy' => ask_question_tags,'field' => 'slug','terms' => $tax_slug->slug))));
	include locate_template("sticky-question.php");
	$args = array_merge($custom_args,$post__not_in,array("paged" => $paged));
	query_posts($args);
	$active_sticky = false;
	get_template_part("loop-question","category");
	vpanel_pagination();
	wp_reset_query();
get_footer();?>