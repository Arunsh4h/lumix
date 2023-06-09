<?php ob_start();/* Template Name: Edit question */
get_header();
$get_question = (isset($_GET["q"])?(int)$_GET["q"]:0);
$get_post_q = get_post($get_question);
	if ( have_posts() ) : while ( have_posts() ) : the_post();?>
		<div class="page-content">
			<div class="boxedtitle page-title"><h2><?php the_title();?></h2></div>
			<?php the_content();
			$question_edit = askme_options("question_edit");
			$user_get_current_user_id = get_current_user_id();
			if ($question_edit == 1 || is_super_admin($user_get_current_user_id)) {
				if (isset($get_question) && $get_question != 0 && $get_post_q && ($get_post_q->post_type == ask_questions_type || $get_post_q->post_type == ask_asked_questions_type)) {
					if ($get_post_q->post_author != 0 || is_super_admin($user_get_current_user_id)) {
						$user_login_id_l = get_user_by("id",(is_super_admin($user_get_current_user_id)?$user_get_current_user_id:$get_post_q->post_author));
						if ($user_login_id_l->ID == $user_get_current_user_id || is_super_admin($user_get_current_user_id)) {
							echo do_shortcode("[edit_question]");
						}else {
							_e("Sorry you can't edit this question.","vbegy");
						}
					}else {
						_e("Sorry you can't edit this question.","vbegy");
					}
				}else {
					_e("Sorry no question has you select or not found.","vbegy");
				}
			}else {
				_e("Sorry you don't have a premmisions to edit this question.","vbegy");
			}?>
		</div><!-- End page-content -->
	<?php endwhile; endif;
get_footer();?>