<?php
/* stats */
add_action('widgets_init', 'widget_stats_widget');
function widget_stats_widget()
{
	register_widget('Widget_Stats');
}

class Widget_Stats extends WP_Widget
{

	function __construct()
	{
		$widget_ops = array('classname' => 'stats-widget');
		$control_ops = array('id_base' => 'stats-widget');
		parent::__construct('stats-widget', 'Lumeno - Stats', $widget_ops, $control_ops);
	}

	function widget($args, $instance)
	{
		global $wpdb;
		extract($args);
		$title = apply_filters('widget_title', (isset($instance['title']) ? $instance['title'] : ''));

		$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) WHERE %s=1", 1);
		$query = $wpdb->get_results($query);
		$users = $wpdb->num_rows;

		echo $before_widget;
		if ($title)
			echo $before_title . esc_attr($title) . $after_title;
		$count_questions = wp_count_posts(ask_questions_type)->publish;
		$count_questions += wp_count_posts(ask_asked_questions_type)->publish;
		$answers_count = get_all_comments_of_post_type(array(ask_questions_type, ask_asked_questions_type));
		$best_answer_option = count(get_comments(array("status" => "approve", 'post_type' => array(ask_questions_type, ask_asked_questions_type), "meta_query" => array(array("key" => "best_answer_comment", "compare" => "=", "value" => "best_answer_comment")))));
		$best_answers_count = (isset($best_answer_option) && $best_answer_option != "" && $best_answer_option > 0 ? $best_answer_option : 0); ?>
		<div class="widget_stats ul_list ul_list-icon-ok">
			<ul>
				<li><i class="icon-question-sign"></i><?php _e("Questions", "vbegy") ?> ( <span><?php echo (int)$count_questions; ?></span> )</li>
				<li><i class="icon-comment"></i><?php _e("Answers", "vbegy") ?> ( <span><?php echo (int)$answers_count ?></span> )</li>
				<li><i class="icon-asterisk"></i><?php _e("Best Answers", "vbegy") ?> ( <span><?php echo (int)$best_answers_count ?></span> )</li>
				<li><i class="icon-user"></i><?php _e("Users", "vbegy") ?> ( <span><?php echo (int)$users ?></span> )</li>
			</ul>
		</div>
	<?php
		echo $after_widget;
	}

	function update($new_instance, $old_instance)
	{
		$instance					 = $old_instance;
		$instance['title']			 = strip_tags($new_instance['title']);
		return $instance;
	}

	function form($instance)
	{
		$defaults = array('title' => __('Stats', 'vbegy'));
		$instance = wp_parse_args((array) $instance, $defaults);

		$categories_obj = get_categories('hide_empty=0');
		$categories = array();
		foreach ($categories_obj as $pn_cat) {
			$categories[$pn_cat->cat_ID] = $pn_cat->cat_name;
		}
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title : </label>
			<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo (isset($instance['title']) ? esc_attr($instance['title']) : ""); ?>" class="widefat" type="text">
		</p>
<?php
	}
}
?>