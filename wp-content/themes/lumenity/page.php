<?php get_header();
	$date_format = (askme_options("date_format")?askme_options("date_format"):get_option("date_format"));
	$vbegy_what_post = askme_post_meta('vbegy_what_post','select',$post->ID);
	$vbegy_sidebar = askme_post_meta('vbegy_sidebar','select',$post->ID);
	if ($vbegy_sidebar == "default") {
		$vbegy_sidebar_all = askme_options("sidebar_layout");
	}else {
		$vbegy_sidebar_all = $vbegy_sidebar;
	}
	$vbegy_google = askme_post_meta('vbegy_google',"textarea",$post->ID);
	$video_id = askme_post_meta('vbegy_video_post_id',"select",$post->ID);
	$video_type = askme_post_meta('vbegy_video_post_type',"text",$post->ID);
	$vbegy_slideshow_type = askme_post_meta('vbegy_slideshow_type','select',$post->ID);
	$type = askme_video_iframe($video_type,$video_id,"post_meta","vbegy_video_post_id",$post->ID);
	if ( have_posts() ) : while ( have_posts() ) : the_post();
		$custom_page_setting = askme_post_meta('vbegy_custom_page_setting','checkbox',$post->ID);
		$post_meta_s = askme_post_meta('vbegy_post_meta_s','checkbox',$post->ID);
		$post_comments_s = askme_post_meta('vbegy_post_comments_s','checkbox',$post->ID);?>
		<article <?php post_class('post single-post');?> id="post-<?php echo $post->ID;?>">
			<div class="post-inner">
				<div class="post-img<?php if (($vbegy_what_post == "image" && !has_post_thumbnail()) || !has_post_thumbnail()) {echo " post-img-0";}else if ($vbegy_what_post == "video") {echo " video_embed";}if ($vbegy_sidebar_all == "full") {echo " post-img-12";}else {echo " post-img-9";}?>">
					<?php include (get_template_directory() . '/includes/head.php');?>
				</div>
	        	<h2 class="post-title"><?php the_title()?></h2>
				<?php $posts_meta = askme_options("post_meta");
				if (($posts_meta == 1 && $post_meta_s == "") || ($posts_meta == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($posts_meta == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_meta_s) && $post_meta_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_meta_s) && $post_meta_s == 1)) {?>
					<div class="post-meta">
					    <span class="meta-author"><i class="icon-user"></i><?php the_author_posts_link();?></span>
					    <span class="meta-date"><i class="fa fa-calendar"></i><?php the_time($date_format);?></span>
					    <span class="meta-comment"><i class="fa fa-comments"></i><?php comments_popup_link(__('0 Comments', 'vbegy'), __('1 Comment', 'vbegy'), '% '.__('Comments', 'vbegy'));?></span>
					    <span class="post-view"><i class="icon-eye-open"></i><?php echo (int)get_post_meta($post->ID,askme_get_meta_stats(),true);?> <?php _e("views","vbegy");?></span>
					</div>
				<?php }?>
				<div class="post-content">
					<?php $show_content = apply_filters("askme_show_content",true);
					if ($show_content == true) {
						the_content();
					}else {
						do_action("askme_close_the_content");
					}?>
					<div class="clearfix"></div>
				</div>
			</div><!-- End post-inner -->
		</article><!-- End article.post -->
		<?php $post_comments = askme_options("post_comments");
		if (($post_comments == 1 && $post_comments_s == "") || ($post_comments == 1 && isset($custom_page_setting) && $custom_page_setting == 0) || ($post_comments == 1 && isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_comments_s) && $post_comments_s != 0) || (isset($custom_page_setting) && $custom_page_setting == 1 && isset($post_comments_s) && $post_comments_s == 1)) {
			comments_template();
		}
	endwhile; endif;
get_footer();?>