<?php /* Template Name: Blog */
get_header();
	$vbegy_what_post      = askme_post_meta('vbegy_what_post','select',$post->ID);
	$vbegy_what_sidebar   = askme_post_meta('vbegy_what_sidebar','select',$post->ID);
	$vbegy_sidebar_all    = askme_post_meta('vbegy_sidebar','radio',$post->ID);
	$post_number          = askme_post_meta('vbegy_post_number_b','text',$post->ID);
	$post_excerpt         = askme_post_meta('vbegy_post_excerpt_b','text',$post->ID);
	$orderby_post         = askme_post_meta('vbegy_orderby_post_b','select',$post->ID);
	$post_display         = askme_post_meta('vbegy_post_display_b','select',$post->ID);
	$post_single_category = askme_post_meta('vbegy_post_single_category_b','select',$post->ID);
	$post_categories      = askme_post_meta('vbegy_post_categories_b','type=checkbox_list',$post->ID);
	$post_posts           = askme_post_meta('vbegy_post_posts_b','text',$post->ID);
	$blog_style           = askme_post_meta('vbegy_blog_style','radio',$post->ID);
	$blog_style           = ($blog_style != ""?$blog_style:"blog_1");
	$post_number          = (isset($post_number) && $post_number != ""?$post_number:get_option("posts_per_page"));
	$post_excerpt         = (isset($post_excerpt) && $post_excerpt != ""?$post_excerpt:40);
	if ($vbegy_sidebar_all == "default") {
		$vbegy_sidebar_all = askme_options("sidebar_layout");
	}else {
		$vbegy_sidebar_all = $vbegy_sidebar_all;
	}
	$taxonomy = 'category';
	$paged = askme_paged();
	if ($post_display == "single_category") {
		$cats_post = array("post_type" => "post",'ignore_sticky_posts' => 1,"paged" => $paged,"posts_per_page" => $post_number,'tax_query' => array(array('taxonomy' => $taxonomy,'field' => 'id','terms' => $post_single_category)));
	}else if ($post_display == "multiple_categories") {
		$categories_a = array();
		if (isset($post_categories) && is_array($post_categories) && !empty($post_categories)) {
			foreach ($post_categories as $key => $value) {
				if ($value !== '0') {
					$categories_a[] = $key;
				}
			}
		}
		$cats_post = array("post_type" => "post",'ignore_sticky_posts' => 1,"paged" => $paged,"posts_per_page" => $post_number,'tax_query' => array(array('taxonomy' => $taxonomy,'field' => 'id','terms' => $categories_a,'operator' => 'IN')));
	}else if ($post_display == "posts") {
		$post_posts = explode(",",$post_posts);
		$cats_post = array('post__in' => $post_posts,'ignore_sticky_posts' => 1,"paged" => $paged,"posts_per_page" => $post_number);
	}else {
		$cats_post = array("post_type" => "post","paged" => $paged,"posts_per_page" => $post_number);
	}
	if ($orderby_post == "popular") {
		$orderby_post = array('orderby' => 'comment_count');
	}else if ($orderby_post == "random") {
		$orderby_post = array('orderby' => 'rand');
	}else {
		$orderby_post = array();
	}

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

	query_posts(array_merge($author__not_in,$orderby_post,$cats_post));
	get_template_part("loop");
	vpanel_pagination();
	wp_reset_query();
get_footer();?>