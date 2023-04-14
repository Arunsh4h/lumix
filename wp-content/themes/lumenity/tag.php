<?php get_header();
	$blog_style = askme_options("home_display");
	$vbegy_sidebar_all = askme_options("sidebar_layout");
	$tag_description   = tag_description();
	$term              = $wp_query->get_queried_object();
	$paged             = askme_paged();
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

	$args = array_merge($author__not_in,array('tax_query' => array(array('taxonomy' => $term->taxonomy,'field' => 'slug','terms' => $term->slug))));
	
	query_posts($args);
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
	get_template_part("loop","tag");
	vpanel_pagination();
get_footer();?>