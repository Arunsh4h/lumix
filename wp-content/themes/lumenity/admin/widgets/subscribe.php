<?php
/* subscribe */
add_action( 'widgets_init', 'widget_subscribe_widget' );
function widget_subscribe_widget() {
	register_widget( 'Widget_Subscribe' );
}
class Widget_Subscribe extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'subscribe-widget'  );
		$control_ops = array( 'id_base' => 'subscribe-widget' );
		parent::__construct( 'subscribe-widget','Ask Me - Subscribe', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		$title			   = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );
		$text_feedburner   = (isset($instance['text_feedburner'])?esc_html($instance['text_feedburner']):'');
		$newsletter_action = (isset($instance['newsletter_action'])?esc_html($instance['newsletter_action']):'');

		echo $before_widget;
			if ( $title )
				echo $before_title.esc_attr($title).$after_title;?>
	
			<div class="subscribe_widget">
				<p class="subscribe_text"><?php echo (isset($text_feedburner)?do_shortcode($text_feedburner):"<br>");?></p>
				<div class="clearfix"></div>
				<div class="form-style form-style-2">
					<form class="validate" action="<?php echo esc_attr($newsletter_action)?>" method="post" name="mc-embedded-subscribe-form" target="_blank" novalidate>
						<p class="subscribe-text">
							<input name="EMAIL" type="email" value="<?php _e("Email","vbegy");?>" onfocus="if(this.value=='<?php _e("Email","vbegy");?>')this.value='';" onblur="if(this.value=='')this.value='<?php _e("Email","vbegy");?>';">
							<i class="icon-rss"></i>
						</p>
						<input name="subscribe" type="submit" id="submit" class="button color small sidebar_submit" value="<?php _e('Subscribe','vbegy')?>">
					</form>
				</div>
			</div>
		<?php echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance					   = $old_instance;
		$instance['title']			   = strip_tags( $new_instance['title'] );
		$instance['text_feedburner']   = $new_instance['text_feedburner'];
		$instance['newsletter_action'] = $new_instance['newsletter_action'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'title' => 'Subscribe','text_feedburner' => 'Subscribe to our email newsletter .');
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title : </label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo (isset($instance['title'])?esc_attr($instance['title']):"");?>" class="widefat" type="text">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'text_feedburner' ); ?>">Text above Email Input Field : <small>( support : Html & Shortcodes )</small> </label>
			<textarea rows="5" id="<?php echo $this->get_field_id( 'text_feedburner' ); ?>" name="<?php echo $this->get_field_name( 'text_feedburner' ); ?>" class="widefat"><?php echo (isset($instance['text_feedburner'])?esc_attr($instance['text_feedburner']):"");?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'newsletter_action' ); ?>">Newsletter action </label>
			<input type="text" id="<?php echo $this->get_field_id( 'newsletter_action' ); ?>" name="<?php echo $this->get_field_name( 'newsletter_action' ); ?>" class="widefat" value="<?php echo (isset($instance['newsletter_action'])?esc_attr($instance['newsletter_action']):"");?>">
		</p>
	<?php
	}
}
?>