<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) :
	die (__('Please do not load this page directly. Thanks!','vbegy'));
endif;

if ( post_password_required() ) :
    ?><p class="no-comments"><?php _e("This post is password protected. Enter the password to view comments.","vbegy");?></p><?php
    return;
endif;
echo '<div id="comments"></div>';
$user_get_current_user_id = get_current_user_id();
$custom_permission = askme_options("custom_permission");
if (is_user_logged_in) {
	$user_is_login = get_userdata($user_get_current_user_id);
	$user_login_group = key($user_is_login->caps);
	$roles = $user_is_login->allcaps;
}
$count_post_all = (int)askme_count_comments($post->ID);
if ($count_post_all == 0) {
	$get_comments_args = array('post_id' => $post->ID,'status' => 'approve');
	$comments_args = get_comments($get_comments_args);
	askme_update_comments_count($post->ID);
	$count_post_all = (int)askme_count_comments($post->ID);
}
if ( have_comments() && $count_post_all > 0 ) :
	$k = 0;?>
	<div id="commentlist" class="page-content <?php if (is_page()) {echo "no_comment_box";}?>">
		<div class="boxedtitle page-title"><h2><?php comments_number(__('Comments','vbegy'),__('Comment','vbegy'), __('Comments','vbegy'));?> ( <span class="color"><?php echo sprintf("%s",$count_post_all);?></span> )</h2></div>
		<?php $show_comment = askme_options("show_comment");
		if ($post->post_type != "post" || ($post->post_type == "post" && ($custom_permission != 1 || is_super_admin($user_get_current_user_id) || ($custom_permission == 1 && (is_user_logged_in && isset($roles["show_comment"]) && $roles["show_comment"] == 1) || (!is_user_logged_in && $show_comment == 1))))) {?>
			<ol class="commentlist clearfix">
	            <?php if ($user_get_current_user_id > 0) {
					$include_unapproved = array($user_get_current_user_id);
				}else {
					$unapproved_email = wp_get_unapproved_comment_author_email();
					if ($unapproved_email) {
						$include_unapproved = array($unapproved_email);
					}
				}
				$include_unapproved_args = (isset($include_unapproved)?array('include_unapproved' => $include_unapproved):array());
				$author__not_in = array();
		    	$block_users = askme_options("block_users");
				if ($block_users == 1) {
					if ($user_get_current_user_id > 0) {
						$get_block_users = get_user_meta($user_get_current_user_id,"askme_block_users",true);
						if (is_array($get_block_users) && !empty($get_block_users)) {
							$author__not_in = array("author__not_in" => $get_block_users);
						}
					}
				}
				$get_comments_args = array_merge($author__not_in,$include_unapproved_args,array('post_id' => $post->ID,'status' => 'approve'));
	            $comments_args = get_comments(array_merge($get_comments_args,array('orderby' => 'comment_date','order' => 'DESC')));
	            if (isset($comments_args) && is_array($comments_args) && !empty($comments_args)) {
		            wp_list_comments('callback=vbegy_comment',$comments_args);
		        }else {
		        	wp_list_comments('callback=vbegy_comment');
		        }?>
	        </ol><!-- End commentlist -->
	    <?php }else {
			echo '<div class="note_error"><strong>'.__("Sorry, you do not have permission to view comments.","vbegy").'</strong></div><br>';
		}?>
    </div><!-- End page-content -->
    
    <div class="pagination comments-pagination">
        <?php paginate_comments_links(array('prev_text' => '&laquo;', 'next_text' => '&raquo;'))?>
    </div><!-- End comments-pagination -->
<?php endif;

if ( comments_open() ) :
	if (askme_options("Ahmed") == 1) :comment_form();endif;?>
	
	<div id="respond" class="comment-respond page-content clearfix <?php if (!have_comments()) {echo "no_comment_box";}?>">
	    <div class="boxedtitle page-title"><h2><?php comment_form_title(__('Leave a reply','vbegy'),__('Leave a reply to %s','vbegy'));?></h2></div>
	    <?php $add_comment = askme_options("add_comment");
	    if (is_user_logged_in) {
	    	$user_is_login = get_userdata($user_get_current_user_id);
	    	$user_login_group = key($user_is_login->caps);
	    	$roles = $user_is_login->allcaps;
	    }
	    if ($post->post_type != "post" || ($post->post_type == "post" && ($custom_permission != 1 || is_super_admin($user_get_current_user_id) || ($custom_permission == 1 && (is_user_logged_in && isset($roles["add_comment"]) && $roles["add_comment"] == 1) || (!is_user_logged_in && $add_comment == 1))))) {
	    	$post_comments_user = askme_options("post_comments_user");
		    $post_comments_user_active = true;
		    if ($post_comments_user == 1) {
		    	if (!is_user_logged_in) {
		    		$post_comments_user_active = false;
		    	}
		    }
		    if ($post_comments_user_active == true) {?>
			    <form action="<?php echo esc_url(site_url( '/wp-comments-post.php' ))?>" method="post" id="commentform">
			    	<div class="ask_error"></div>
			        <?php if ( is_user_logged_in ) : ?>
			            <p><?php _e('Logged in as','vbegy')?> <a href="<?php echo get_option('siteurl');?>/wp-admin/profile.php"><?php echo $user_identity;?></a>. <a href="<?php echo wp_logout_url(get_permalink());?>" title="Log out of this account"><?php _e('Log out &raquo;','vbegy')?></a></p>
			        <?php else :
			        	$require_name_email = get_option("require_name_email");?>
				        <div id="respond-inputs" class="clearfix">
				            <p>
				                <label<?php echo ($require_name_email == 1?' class="required"':'')?> for="comment_name"><?php echo __('Name','vbegy').($require_name_email == 1?'<span>*</span>':'')?></label>
				                <input name="author" type="text" value="" id="comment_name" aria-required="true">
				            </p>
				            <p>
				                <label<?php echo ($require_name_email == 1?' class="required"':'')?> for="comment_email"><?php echo __('E-Mail','vbegy').($require_name_email == 1?'<span>*</span>':'')?></label>
				                <input name="email" type="text" value="" id="comment_email" aria-required="true">
				            </p>
				            <p class="last">
				                <label class="required" for="comment_url"><?php _e('Website','vbegy');?></label>
				                <input name="url" type="text" value="" id="comment_url">
				            </p>
				        </div>
			        <?php endif;?>
			        <div class="clearfix">
			        	<?php
			        	echo askme_add_captcha(askme_options("the_captcha_comment"),"comment",rand(0000,9999));
			        	?>
			        </div>
			        <div id="respond-textarea">
			            <p>
			                <label class="required" for="comment"><?php _e('Comment','vbegy');?><span>*</span></label>
			                <?php $comment_editor = askme_options("comment_editor");
			                if ($comment_editor == 1) {
			                    $settings = array("textarea_name" => "comment","media_buttons" => true,"textarea_rows" => 10);
			                    $settings = apply_filters('askme_comment_editor_setting',$settings);
			                    wp_editor("","comment",$settings);
			                }else {?>
			                	<textarea id="comment" name="comment" aria-required="true" cols="58" rows="10"></textarea>
			                <?php }?>
			            </p>
			        </div>
			        <?php $terms_active_comment = askme_options("terms_active_comment");
			        if ($terms_active_comment == 1) {
			        	$terms_checked_comment = askme_options("terms_checked_comment");
						if ((isset($_POST['agree_terms']) && $_POST['agree_terms'] == 1) || ($terms_checked_comment == 1 && empty($_POST))) {
							$active_terms = true;
						}
						$terms_link = askme_options("terms_link_comment");
						$terms_link_page = askme_options("terms_page_comment");
						$terms_active_target = askme_options("terms_active_target_comment");
						$privacy_policy = askme_options('privacy_policy_comment');
						$privacy_active_target = askme_options('privacy_active_target_comment');
						$privacy_page = askme_options('privacy_page_comment');
						$privacy_link = askme_options('privacy_link_comment');
						echo '<p class="question_poll_p">
							<label for="agree_terms" class="required">'.__("Terms","vbegy").'<span>*</span></label>
							<input type="checkbox" id="agree_terms" name="agree_terms" value="1" '.(isset($active_terms)?"checked='checked'":"").'>
							<span class="question_poll">'.sprintf(wp_kses(__("By commenting, you agree to the <a target='%s' href='%s'>Terms of Service</a>%s.","vbegy"),array('a' => array('href' => array(),'target' => array()))),($terms_active_target == "same_page"?"_self":"_blank"),(isset($terms_link) && $terms_link != ""?$terms_link:(isset($terms_page) && $terms_page != ""?get_page_link($terms_page):"#")),($privacy_policy == 1?" ".sprintf(wp_kses(__("and <a target='%s' href='%s'>Privacy Policy</a>","vbegy"),array('a' => array('href' => array(),'target' => array()))),($privacy_active_target == "same_page"?"_self":"_blank"),(isset($privacy_link) && $privacy_link != ""?$privacy_link:(isset($privacy_page) && $privacy_page != ""?get_page_link($privacy_page):"#"))):"")).'</span>
						</p><div class="clearfix"></div>';
					}?>
		        	<div class="cancel-comment-reply"><?php cancel_comment_reply_link(__("Click here to cancel reply.","vbegy"));?></div>
		        	<?php echo apply_filters( 'comment_form_field_comment', false );?>
			        <p class="form-submit">
			        	<input name="submit" type="submit" id="submit" value="<?php _e('Post Comment','vbegy')?>" class="button small color">
			        	<?php comment_id_fields();?>
			        	<?php do_action('comment_form', $post->ID);?>
			        </p>
			    </form>
			<?php }else {?>
				<p class="no-login-comment"><?php printf(__('You must <a href="%s" class="login-comments">login</a> or <a href="%s" class="signup">register</a> to add a new comment .','vbegy'),get_page_link(askme_options('login_register_page')),get_page_link(askme_options('login_register_page')))?></p>
			<?php }
		}else {
			echo '<div class="note_error"><strong>'.__("Sorry, you do not have permission to comment to this post.","vbegy").'</strong></div>';
		}?>
	</div>
<?php endif;?>