<?php
/*-----------------------------------------------------------------------------------*/
/* Add meta boxes */
/*-----------------------------------------------------------------------------------*/
add_action('add_meta_boxes', 'askme_meta_boxes');
function askme_meta_boxes($post_type)
{
	global $post;
	$allow_post_type = apply_filters("askme_allow_post_type", array('post', 'page', ask_questions_type, ask_asked_questions_type, 'product'));
	if (in_array($post_type, $allow_post_type)) {
		add_meta_box('askme_meta_tabs', esc_html__('Page settings', "vbegy"), 'askme_meta_tabs', $post_type, 'normal', 'high');
	}
}
/*-----------------------------------------------------------------------------------*/
/* Page settings */
/*-----------------------------------------------------------------------------------*/
function askme_meta_tabs()
{
	global $post;
	wp_nonce_field('askme_builder_save_meta', 'askme_save_meta_nonce');
	$askme_admin_meta = askme_admin_meta();
	if (is_array($askme_admin_meta) && !empty($askme_admin_meta)) { ?>
		<div id="optionsframework-wrap">
			<div class="optionsframework-header">
				<a href="<?php echo askme_theme_url_tf ?>" target="_blank"></a>
				<div class="vpanel_social">
					<ul>
						<li><a class="vpanel_social_f" href="https://www.facebook.com/intself.com" target="_blank"><i class="dashicons dashicons-facebook"></i></a></li>
						<li><a class="vpanel_social_t" href="https://www.twitter.com/2codeThemes" target="_blank"><i class="dashicons dashicons-twitter"></i></a></li>
						<li><a class="vpanel_social_e" href="https://intself.com/" target="_blank"><i class="dashicons dashicons-email-alt"></i></a></li>
						<li><a class="vpanel_social_s" href="https://intself.com/demo/themes/ask-me/Docs/" target="_blank"><i class="dashicons dashicons-sos"></i></a></li>
					</ul>
				</div>
				<div class="clear"></div>
			</div>
			<div class="optionsframework-content">
				<h2 class="nav-tab-wrapper">
					<?php echo askme_admin_fields_class::askme_admin_tabs("meta", $askme_admin_meta, $post->ID); ?>
				</h2>
				<?php settings_errors('options-framework'); ?>
				<div id="optionsframework-metabox" class="metabox-holder">
					<div id="optionsframework" class="postbox">
						<?php askme_admin_fields_class::askme_admin_fields("meta", askme_meta, "meta", $post->ID, $askme_admin_meta); ?>
					</div><!-- End container -->
				</div>
			</div>
			<div class="clear"></div>
		</div><!-- End wrap -->
	<?php }
}
/*-----------------------------------------------------------------------------------*/
/* Process save meta box */
/*-----------------------------------------------------------------------------------*/
add_action('save_post', 'askme_meta_save', 1, 2);
function askme_meta_save($post_id, $post)
{
	if (!isset($_POST)) return $post_id;
	$allow_post_type = apply_filters("askme_allow_post_type", array('post', 'page', ask_questions_type, ask_asked_questions_type, 'group'));
	if (!in_array($post->post_type, $allow_post_type)) return $post_id;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	if (!isset($_POST['askme_save_meta_nonce']) || !wp_verify_nonce($_POST['askme_save_meta_nonce'], 'askme_builder_save_meta')) return $post_id;
	if (!current_user_can('edit_post', $post_id)) return $post_id;

	do_action("askme_action_meta_save", $_POST, $post);

	$options = askme_admin_meta();
	foreach ($options as $value) {
		if (!isset($value['unset']) && $value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != 'info' && $value['type'] != 'group' && $value['type'] != 'html' && $value['type'] != 'content') {
			$val = "";

			if (isset($value['std'])) {
				$val = $value['std'];
			}

			$field_name = $value['id'];

			if (isset($_POST[$field_name])) {
				$val = $_POST[$field_name];
			}

			if ($value['type'] == "checkbox") {
				if (isset($_POST[$field_name])) {
					$val = 1;
				} else {
					$val = 0;
				}
			}

			if (array() === $val) {
				if (isset($value['save']) && $value['save'] == "option") {
					delete_option($field_name);
				} else {
					delete_post_meta($post->ID, $field_name);
				}
			} else if (isset($_POST[$field_name]) || $value['type'] == "checkbox") {
				if ($value['id'] == "question_poll" && $val != "on" && $val != 1) {
					update_post_meta($post->ID, 'question_poll', 2);
				} else {
					if (isset($_POST["private_question"]) && ($_POST["private_question"] == "on" || $_POST["private_question"] == 1)) {
						$anonymously_user = askme_post_meta("anonymously_user", "", false);
						update_post_meta($post->ID, 'private_question_author', ($anonymously_user > 0 ? $anonymously_user : $post->post_author));
					}
					if (isset($value['save']) && $value['save'] == "option") {
						update_option($field_name, $val);
					} else {
						update_post_meta($post->ID, $field_name, $val);
					}
				}
			}
		}
	}
	do_action("askme_action_after_meta_save", $_POST, $post);
}
/* Save question meta */
add_action("askme_action_meta_save", "askme_action_meta_save", 1, 2);
function askme_action_meta_save($posted, $post)
{
	if ($post->post_type == ask_questions_type) {
		$post_id = $post->ID;
		// category
		$get_question_user_id = get_post_meta($post_id, "user_id", true);
		if (empty($get_question_user_id) && isset($posted['vbegy_question_category'])) {
			$new_term_slug = get_term_by('id', (isset($posted['vbegy_question_category']) ? stripslashes($posted['vbegy_question_category']) : ""), ask_question_category)->slug;
			wp_set_object_terms($post_id, $new_term_slug, ask_question_category);
		}

		$sticky_questions = get_option('sticky_questions');
		$sticky_posts = get_option('sticky_posts');
		if (isset($posted['sticky_question']) && $posted['sticky_question'] == "sticky" && isset($posted['sticky']) && $posted['sticky'] == "sticky") {
			update_post_meta($post_id, 'sticky', 1);
			if (is_array($sticky_questions)) {
				if (!in_array($post_id, $sticky_questions)) {
					$array_merge = array_merge($sticky_questions, array($post_id));
					update_option("sticky_questions", $array_merge);
				}
			} else {
				update_option("sticky_questions", array($post_id));
			}
			if (is_array($sticky_posts)) {
				if (!in_array($post_id, $sticky_posts)) {
					$array_merge = array_merge($sticky_posts, array($post_id));
					update_option("sticky_posts", $array_merge);
				}
			} else {
				update_option("sticky_posts", array($post_id));
			}
		} else {
			delete_post_meta($post_id, "end_sticky_time");
			if (is_array($sticky_questions) && in_array($post_id, $sticky_questions)) {
				$sticky_questions = remove_item_by_value($sticky_questions, $post_id);
				update_option('sticky_questions', $sticky_questions);
			}
			if (is_array($sticky_posts) && in_array($post_id, $sticky_posts)) {
				$sticky_posts = remove_item_by_value($sticky_posts, $post_id);
				update_option('sticky_posts', $sticky_posts);
			}
			delete_post_meta($post_id, 'sticky');
		}
	}
}
/*-----------------------------------------------------------------------------------*/
/* Add meta boxes */
/*-----------------------------------------------------------------------------------*/
add_action('add_meta_boxes', 'builder_meta_boxes');
function builder_meta_boxes()
{
	add_meta_box('delete_post', __('Delete post', 'vbegy'), 'delete_post', 'post', 'side');
	add_meta_box('delete_post', __('Delete question', 'vbegy'), 'delete_post', ask_questions_type, 'side');
	add_meta_box('delete_post', __('Delete question', 'vbegy'), 'delete_post', ask_asked_questions_type, 'side');
}
/*-----------------------------------------------------------------------------------*/
/* Delete post questions */
/*-----------------------------------------------------------------------------------*/
function delete_post()
{
	global $post; ?>
	<div class="minor-publishing">
		<div class="rwmb-field">
			<div class="rwmb-label">
				<label for="vbegy_delete_reason">Reason if you need to remove it.</label>
			</div>
			<div class="rwmb-input vpanel_checkbox_input">
				<input type="text" class="rwmb-input" name="vbegy_delete_reason" id="vbegy_delete_reason" value="<?php echo esc_attr(get_post_meta($post->ID, "vbegy_delete_reason", true)); ?>">
			</div>
			<div class="clear"></div><br>
			<div class="submitbox"><a href="#" class="submitdelete delete-question-post" data-div-id="vbegy_delete_reason" data-id="<?php echo esc_attr($post->ID); ?>" data-action="delete_question_post" data-location="<?php echo esc_url(($post->post_type == ask_questions_type || $post->post_type == ask_asked_questions_type ? admin_url('edit.php?post_type=' . $post->post_type) : admin_url('edit.php'))) ?>">Delete?</a></div>
		</div>
	</div>
<?php
}
/*-----------------------------------------------------------------------------------*/
/* Process builder meta box */
/*-----------------------------------------------------------------------------------*/
add_action('save_post', 'builder_meta_save', 1, 2);
function builder_meta_save($post_id, $post)
{
	global $wpdb;
	if (!isset($_POST)) return $post_id;
	if ($post->post_type != 'page' && $post->post_type != ask_questions_type && $post->post_type != ask_asked_questions_type && $post->post_type != 'post') return $post_id;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;
	if (!isset($_POST['builder_save_meta_nonce']) || !wp_verify_nonce($_POST['builder_save_meta_nonce'], 'builder_save_meta')) return $post_id;
	if (!current_user_can('edit_post', $post_id)) return $post_id;

	if (isset($_POST["vbegy_custom_sections"]) && $_POST["vbegy_custom_sections"] == 1) {
		$vbegy_custom_sections = $_POST["vbegy_custom_sections"];
		update_post_meta($post->ID, "vbegy_custom_sections", $vbegy_custom_sections);
	} else {
		delete_post_meta($post->ID, "vbegy_custom_sections");
	}

	if (isset($_POST["vbegy_delete_reason"]) && $_POST["vbegy_delete_reason"] != "") {
		$vbegy_delete_reason = $_POST["vbegy_delete_reason"];
		update_post_meta($post->ID, "vbegy_delete_reason", $vbegy_delete_reason);
	}

	if ($post->post_type == ask_questions_type) {
		$sticky_questions = get_option('sticky_questions');
		$sticky_posts = get_option('sticky_posts');
		if (isset($_POST['sticky_question']) && $_POST['sticky_question'] == "sticky" && isset($_POST['sticky']) && $_POST['sticky'] == "sticky") {
			update_post_meta($post_id, 'sticky', 1);
			if (is_array($sticky_questions)) {
				if (!in_array($post_id, $sticky_questions)) {
					$array_merge = array_merge($sticky_questions, array($post_id));
					update_option("sticky_questions", $array_merge);
				}
			} else {
				update_option("sticky_questions", array($post_id));
			}
			if (is_array($sticky_posts)) {
				if (!in_array($post_id, $sticky_posts)) {
					$array_merge = array_merge($sticky_posts, array($post_id));
					update_option("sticky_posts", $array_merge);
				}
			} else {
				update_option("sticky_posts", array($post_id));
			}
		} else {
			if (is_array($sticky_questions) && in_array($post_id, $sticky_questions)) {
				$sticky_questions = remove_item_by_value($sticky_questions, $post_id);
				update_option('sticky_questions', $sticky_questions);
			}
			if (is_array($sticky_posts) && in_array($post_id, $sticky_posts)) {
				$sticky_posts = remove_item_by_value($sticky_posts, $post_id);
				update_option('sticky_posts', $sticky_posts);
			}
			delete_post_meta($post_id, 'sticky');
		}
	}
} ?>