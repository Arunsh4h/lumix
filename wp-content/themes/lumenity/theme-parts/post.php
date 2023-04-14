<?php 
global $blog_style,$vbegy_sidebar_all,$authordata;
$user_get_current_user_id = get_current_user_id();
$posts_meta = askme_options("post_meta");
$date_format = (askme_options("date_format")?askme_options("date_format"):get_option("date_format"));
$post_excerpt_option = askme_options("post_excerpt");
$post_excerpt = (isset($post_excerpt) && $post_excerpt != ""?$post_excerpt:(isset($post_excerpt_option) && $post_excerpt_option != ""?$post_excerpt_option:40));
$vbegy_what_post = askme_post_meta('vbegy_what_post','select',$post->ID);
$vbegy_google = askme_post_meta('vbegy_google',"textarea",$post->ID);
$video_id = askme_post_meta('vbegy_video_post_id',"select",$post->ID);
$video_type = askme_post_meta('vbegy_video_post_type',"text",$post->ID);
$vbegy_slideshow_type = askme_post_meta('vbegy_slideshow_type','select',$post->ID);
$type = askme_video_iframe($video_type,$video_id,"post_meta","vbegy_video_post_id",$post->ID);?>
<article <?php post_class('post clearfix '.($blog_style == "blog_2"?"blog_2":"").(is_sticky()?" sticky_post":""));?> role="article" itemtype="https://schema.org/Article">
    <?php $custom_permission = askme_options("custom_permission");
    $show_post = askme_options("show_post");
    if (is_user_logged_in) {
        $user_is_login = get_userdata($user_get_current_user_id);
        $user_login_group = key($user_is_login->caps);
        $roles = $user_is_login->allcaps;
    }
    if ($post->post_type != "post" || ($post->post_type == "post" && ($custom_permission != 1 || (is_super_admin($user_get_current_user_id) || (is_user_logged_in && isset($roles["show_post"]) && $roles["show_post"] == 1) || (!is_user_logged_in && $show_post == 1)) || ($user_get_current_user_id > 0 && $user_get_current_user_id == $post->post_author)))) {?>
        <div class="post-inner">
            <?php if ($blog_style != "blog_2") {?>
                <div class="post-img<?php if ($vbegy_what_post == "video") {echo " video_embed";}else if ($vbegy_what_post == "lightbox") {echo " post-img-lightbox";}else if ($vbegy_what_post == "google") {echo " map_embed";}else if (($vbegy_what_post == "image" && !has_post_thumbnail()) || ($vbegy_what_post == "" && !has_post_thumbnail())) {echo " post-img-0";}if ($vbegy_sidebar_all == "full") {echo " post-img-12";}else {echo " post-img-9";}?>">
                    <?php if (has_post_thumbnail() && $vbegy_what_post == "image") {?><a href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><?php }
                        include locate_template("includes/head.php");?>
                    <?php if (has_post_thumbnail()) {?></a><?php }?>
                </div>
            <?php }?>
            <h2 itemprop="name" class="post-title">
                <?php if ($vbegy_what_post == "lightbox") {?>
                    <span class="post-type"><i class="icon-zoom-in"></i></span>
                <?php }else if ($vbegy_what_post == "google") {?>
                    <span class="post-type"><i class="icon-map-marker"></i></span>
                <?php }else if ($vbegy_what_post == "video") {?>
                    <span class="post-type"><i class="icon-play-circle"></i></span>
                <?php }else if ($vbegy_what_post == "slideshow") {?>
                    <span class="post-type"><i class="icon-film"></i></span>
                <?php }else {
                    if (has_post_thumbnail()) {?>
                        <span class="post-type"><i class="icon-picture"></i></span>
                    <?php }else {?>
                        <span class="post-type"><i class="icon-file-alt"></i></span>
                    <?php }
                }?>
                <a itemprop="url" href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><?php the_title();?></a>
            </h2>
            <?php
            if ($blog_style == "blog_2") {?>
                <div class="post-img<?php if ($vbegy_what_post == "image" && !has_post_thumbnail()) {echo " post-img-0";}else if ($vbegy_what_post == "lightbox") {echo " post-img-lightbox";}else if ($vbegy_what_post == "video") {echo " video_embed";}else if ($vbegy_what_post == "google") {echo " map_embed";}if ($vbegy_sidebar_all == "full") {echo " post-img-12";}else {echo " post-img-9";}?>">
                    <?php if (has_post_thumbnail() && $vbegy_what_post == "image") {?><a href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark"><?php }
                        include locate_template("includes/head.php");?>
                    <?php if (has_post_thumbnail()) {?></a><?php }?>
                </div>
            <?php }
            if ($posts_meta == 1) {
                $post_username = get_post_meta($post->ID, 'post_username',true);
                $post_email = get_post_meta($post->ID, 'post_email',true);?>
                <div class="post-meta">
                    <span class="meta-author" itemprop="author" rel="author"><i class="icon-user"></i>
                        <?php 
                        if ($post->post_author > 0) {?>
                            <a href="<?php echo vpanel_get_user_url($post->post_author);?>" title="<?php the_author();?>"><?php the_author();?></a>
                        <?php }else {
                            echo ($post_username);
                        }
                        ?>
                    </span>
                    <?php if (isset($post->post_author) && $post->post_author > 0) {
                        echo vpanel_get_badge($post->post_author);
                    }?>
                    <span class="meta-date" datetime="<?php the_time('c'); ?>" itemprop="datePublished"><i class="fa fa-calendar"></i><?php the_time($date_format);?></span>
                    <?php if (!is_page()) {?>
                        <span class="meta-categories"><i class="icon-suitcase"></i><?php the_category(' , ');?></span>
                    <?php }
                    $count_post_all = (int)askme_count_comments($post->ID);?>
                    <span class="meta-comment"><i class="fa fa-comments"></i><a href="<?php echo comments_link()?>"><?php echo sprintf(_n("%s Comment","%s Comments",$count_post_all,"vbegy"),$count_post_all);?></a></span>
                    <span class="post-view"><i class="icon-eye-open"></i><?php echo (int)get_post_meta($post->ID,askme_get_meta_stats(),true)?> <?php _e("views","vbegy");?></span>
                </div>
            <?php }?>
            <div class="post-content">
                <p><?php if ($blog_style == "blog_2") {excerpt($post_excerpt);}else {excerpt($post_excerpt);}?></p>
                <a href="<?php the_permalink();?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>" rel="bookmark" class="post-read-more button color small"><?php _e("Continue reading","vbegy");?></a>
            </div><!-- End post-content -->
        </div><!-- End post-inner -->
    <?php }else {
        echo '<div class="note_error"><strong>'.__("Sorry, you do not have permission to view posts.","vbegy").'</strong></div>';
    }?>
</article><!-- End article.post -->
<?php if (isset($k) && $k == askme_options("between_questions_position")) {
    $between_adv_type = askme_options("between_adv_type");
    $between_adv_link = askme_options("between_adv_link");
    $between_adv_code = askme_options("between_adv_code");
    $between_adv_href = askme_options("between_adv_href");
    $between_adv_img = askme_options("between_adv_img");
    if (($between_adv_type == "display_code" && $between_adv_code != "") || ($between_adv_type == "custom_image" && $between_adv_img != "")) {
        echo '<div class="clearfix"></div>
        <div class="advertising advertising-posts">';
        if ($between_adv_type == "display_code") {
            echo do_shortcode(stripslashes($between_adv_code));
        }else {
            if ($between_adv_href != "") {
                echo '<a'.($between_adv_link == "new_page"?" target='_blank'":"").' href="'.$between_adv_href.'">';
            }
            echo '<img alt="" src="'.$between_adv_img.'">';
            if ($between_adv_href != "") {
                echo '</a>';
            }
        }
        echo '</div><!-- End advertising -->
        <div class="clearfix"></div>';
    }
}?>