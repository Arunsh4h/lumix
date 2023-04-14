<?php
/* Ask Question */
add_action( 'widgets_init', 'widget_ask_widget' );
function widget_ask_widget() {
	register_widget( 'Widget_Ask' );
}

class Widget_Ask extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'ask-widget'  );
		$control_ops = array( 'id_base' => 'ask-widget' );
		parent::__construct( 'ask-widget','Ask Me - Buttons', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		$popup_type = (isset($instance['popup_type'])?esc_html($instance['popup_type']):"");
		$button = (isset($instance['button'])?esc_html($instance['button']):"");
		if ($button == "custom") {
			$button_class = "";
			$button_target = (isset($instance['button_target'])?esc_html($instance['button_target']):'');
			$button_link = (isset($instance['button_link'])?esc_html($instance['button_link']):'');
			$button_text = (isset($instance['button_text'])?esc_html($instance['button_text']):'');
		}else if ($button == "post") {
			$button_class = ($popup_type == "popup"?" add-post-link":"");
			$button_link = get_page_link(askme_options('add_post_page'));
			$button_text = esc_html__("Add A New Post","vbegy");
		}else if (!is_user_logged_in() && $button == "login") {
			$button_class = ($popup_type == "popup"?" login-popup":"");
			$button_link = get_page_link(askme_options('login_register_page'));
			$button_text = esc_html__("Login","vbegy");
		}else if (!is_user_logged_in() && $button == "signup") {
			$button_class = ($popup_type == "popup"?" signup":"");
			$button_link = get_page_link(askme_options('login_register_page'));
			$button_text = esc_html__("Create A New Account","vbegy");
		}else {
			$button_class = ($popup_type == "popup"?" ask-question-link":"");
			$button_link = get_page_link(askme_options('add_question'));
			$button_text = esc_html__("Ask A Question","vbegy");
		}
		$button_target = ($button == "custom" && isset($button_target) && $button_target == "new_page"?"_blank":"_self");
		echo '<div class="widget_ask">
			<a a target="'.esc_attr($button_target).'" href="'.esc_url($button_link).'" class="color button small margin_0'.$button_class.'">'.$button_text.'</a>
		</div>';
	}

	function update( $new_instance, $old_instance ) {
		$instance		           = $old_instance;
		$instance['title']         = strip_tags( $new_instance['title'] );
		$instance['popup_type']    = $new_instance['popup_type'];
		$instance['button']        = $new_instance['button'];
		$instance['button_target'] = $new_instance['button_target'];
		$instance['button_link']   = $new_instance['button_link'];
		$instance['button_text']   = $new_instance['button_text'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'title' => __('Ask question','vbegy'), 'popup_type' => 'link', 'button' => 'question', 'button_target' => 'same_page');
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title : </label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo (isset($instance['title'])?esc_attr($instance['title']):""); ?>" class="widefat" type="text">
		</p>
		<p>
			<label>Ask a question type : </label>
			<br>
			<?php $query_type = array("link" => "Open the form at the link","popup" => "Open the form with popup");
			foreach ($query_type as $key_r => $value_r) {?>
				<input id="<?php echo self::get_field_id( 'popup_type' )."-".$key_r ?>" value="<?php echo esc_attr($key_r)?>" type="radio" name="<?php echo self::get_field_name( 'popup_type' ); ?>" <?php echo checked($key_r, (isset($instance['popup_type'])?esc_attr($instance['popup_type']):""), false)?>>
				<label for="<?php echo self::get_field_id( 'popup_type' )."-".$key_r ?>"><?php echo esc_attr($value_r)?></label>
				<br>
			<?php }?>
		</p>
		<p>
			<label>Botton : </label>
			<br>
			<?php $bottons = array("signup" => "Create A New Account","login" => "Login","question" => "Ask A Question","post" => "Add A Post","custom" => "Custom link");
			foreach ($bottons as $key_r => $value_r) {?>
				<input id="<?php echo self::get_field_id( 'button' )."-".$key_r ?>" value="<?php echo esc_attr($key_r)?>" type="radio" name="<?php echo self::get_field_name( 'button' ); ?>" <?php echo checked($key_r, (isset($instance['button'])?esc_attr($instance['button']):""), false)?>>
				<label for="<?php echo self::get_field_id( 'button' )."-".$key_r ?>"><?php echo esc_attr($value_r)?></label>
				<br>
			<?php }?>
		</p>
		<p>
			<label>Open the page in same page or a new page?</label>
			<br>
			<?php $bottons = array("same_page" => "Same page","new_page" => "New page");
			foreach ($bottons as $key_r => $value_r) {?>
				<input id="<?php echo self::get_field_id( 'button_target' )."-".$key_r ?>" value="<?php echo esc_attr($key_r)?>" type="radio" name="<?php echo self::get_field_name( 'button_target' ); ?>" <?php echo checked($key_r, (isset($instance['button_target'])?esc_attr($instance['button_target']):""), false)?>>
				<label for="<?php echo self::get_field_id( 'button_target' )."-".$key_r ?>"><?php echo esc_attr($value_r)?></label>
				<br>
			<?php }?>
		</p>
		<p>
			<label>Type the button link : </label>
			<br>
			<input id="<?php echo $this->get_field_id( 'button_link' ); ?>" name="<?php echo $this->get_field_name( 'button_link' ); ?>" value="<?php echo (isset($instance['button_link'])?esc_attr($instance['button_link']):""); ?>" class="widefat" type="text">
		</p>
		<p>
			<label>Type the button text : </label>
			<br>
			<input id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" value="<?php echo (isset($instance['button_text'])?esc_attr($instance['button_text']):""); ?>" class="widefat" type="text">
		</p>
	<?php
	}
}
?>