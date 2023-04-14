<?php
/* Adv 125x125 */
add_action( 'widgets_init', 'widget_adv125x125_widget' );
function widget_adv125x125_widget() {
	register_widget( 'Widget_Adv125x125' );
}
class Widget_Adv125x125 extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'adv125x125-widget'  );
		$control_ops = array( 'id_base' => 'adv125x125-widget' );
		parent::__construct( 'adv125x125-widget','Ask Me - Adv 125x125', $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		$title      = apply_filters('widget_title', (isset($instance['title'])?$instance['title']:'') );

		$adv_href_1 = (isset($instance['adv_href_1'])?esc_html($instance['adv_href_1']):'');
		$adv_link_1 = (isset($instance['adv_link_1'])?esc_html($instance['adv_link_1']):'');
		$adv_img_1  = (isset($instance['adv_img_1'])?esc_html($instance['adv_img_1']):'');
		$image_id_1 = (isset($instance['image_id_1'])?esc_html($instance['image_id_1']):'');
		$adv_code_1 = (isset($instance['adv_code_1'])?$instance['adv_code_1']:'');

		$adv_href_2 = (isset($instance['adv_href_2'])?esc_html($instance['adv_href_2']):'');
		$adv_link_2 = (isset($instance['adv_link_2'])?esc_html($instance['adv_link_2']):'');
		$adv_img_2  = (isset($instance['adv_img_2'])?esc_html($instance['adv_img_2']):'');
		$image_id_2 = (isset($instance['image_id_2'])?esc_html($instance['image_id_2']):'');
		$adv_code_2 = (isset($instance['adv_code_2'])?$instance['adv_code_2']:'');

		$adv_href_3 = (isset($instance['adv_href_3'])?esc_html($instance['adv_href_3']):'');
		$adv_link_3 = (isset($instance['adv_link_3'])?esc_html($instance['adv_link_3']):'');
		$adv_img_3  = (isset($instance['adv_img_3'])?esc_html($instance['adv_img_3']):'');
		$image_id_3 = (isset($instance['image_id_3'])?esc_html($instance['image_id_3']):'');
		$adv_code_3 = (isset($instance['adv_code_3'])?$instance['adv_code_3']:'');

		$adv_href_4 = (isset($instance['adv_href_4'])?esc_html($instance['adv_href_4']):'');
		$adv_link_4 = (isset($instance['adv_link_4'])?esc_html($instance['adv_link_4']):'');
		$adv_img_4  = (isset($instance['adv_img_4'])?esc_html($instance['adv_img_4']):'');
		$image_id_4 = (isset($instance['image_id_4'])?esc_html($instance['image_id_4']):'');
		$adv_code_4 = (isset($instance['adv_code_4'])?$instance['adv_code_4']:'');
			
		?>
		<div class="advertising advertising-4a">
			<div class="advertising-1">
				<?php if ($adv_code_1 == "") {
					if ($adv_href_1 != "") {?><a<?php echo ($adv_link_1 == "new_page"?" target='_blank'":"")?> href="<?php echo $adv_href_1?>"><?php }?>
						<img alt="" src="<?php echo $adv_img_1?>">
					<?php if ($adv_href_1 != "") {?></a><?php }?>
				<?php }else {
					echo $adv_code_1;
				}?>
			</div>
			<div class="advertising-1">
				<?php if ($adv_code_2 == "") {
					if ($adv_href_2 != "") {?><a<?php echo ($adv_link_2 == "new_page"?" target='_blank'":"")?> href="<?php echo $adv_href_2?>"><?php }?>
						<img alt="" src="<?php echo $adv_img_2?>">
					<?php if ($adv_href_2 != "") {?></a><?php }?>
				<?php }else {
					echo $adv_code_2;
				}?>
			</div>
			<div class="advertising-1">
				<?php if ($adv_code_3 == "") {
					if ($adv_href_3 != "") {?><a<?php echo ($adv_link_3 == "new_page"?" target='_blank'":"")?> href="<?php echo $adv_href_3?>"><?php }?>
						<img alt="" src="<?php echo $adv_img_3?>">
					<?php if ($adv_href_3 != "") {?></a><?php }?>
				<?php }else {
					echo $adv_code_3;
				}?>
			</div>
			<div class="advertising-1">
				<?php if ($adv_code_4 == "") {
					if ($adv_href_4 != "") {?><a<?php echo ($adv_link_4 == "new_page"?" target='_blank'":"")?> href="<?php echo $adv_href_4?>"><?php }?>
						<img alt="" src="<?php echo $adv_img_4?>">
					<?php if ($adv_href_4 != "") {?></a><?php }?>
				<?php }else {
					echo $adv_code_4;
				}?>
			</div>
		</div><!-- End advertising -->
		<div class="clearfix"></div>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance               = $old_instance;
		$instance['title']      = strip_tags( $new_instance['title'] );
		
		$instance['adv_code_1'] = $new_instance['adv_code_1'];
		$instance['adv_link_1'] = $new_instance['adv_link_1'];
		$instance['adv_img_1']  = $new_instance['adv_img_1'];
		$instance['image_id_1'] = $new_instance['image_id_1'];
		$instance['adv_href_1'] = $new_instance['adv_href_1'];
		
		$instance['adv_code_2'] = $new_instance['adv_code_2'];
		$instance['adv_link_2'] = $new_instance['adv_link_2'];
		$instance['adv_img_2']  = $new_instance['adv_img_2'];
		$instance['image_id_2'] = $new_instance['image_id_2'];
		$instance['adv_href_2'] = $new_instance['adv_href_2'];
		
		$instance['adv_code_3'] = $new_instance['adv_code_3'];
		$instance['adv_link_3'] = $new_instance['adv_link_3'];
		$instance['adv_img_3']  = $new_instance['adv_img_3'];
		$instance['image_id_3'] = $new_instance['image_id_3'];
		$instance['adv_href_3'] = $new_instance['adv_href_3'];
		
		$instance['adv_code_4'] = $new_instance['adv_code_4'];
		$instance['adv_link_4'] = $new_instance['adv_link_4'];
		$instance['adv_img_4']  = $new_instance['adv_img_4'];
		$instance['image_id_4'] = $new_instance['image_id_4'];
		$instance['adv_href_4'] = $new_instance['adv_href_4'];
		return $instance;
	}

	function form( $instance ) {
		$defaults = array( 'title' => 'Adv 125x125' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title : </label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo (isset($instance['title'])?esc_attr($instance['title']):""); ?>" class="widefat" type="text">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_img_1' ); ?>">Image URL : </label>
			<input id="<?php echo $this->get_field_id( 'adv_img_1' ); ?>" name="<?php echo $this->get_field_name( 'adv_img_1' ); ?>" value="<?php echo (isset($instance['adv_img_1'])?$instance['adv_img_1']:"");?>" class="widefat upload" type="text">
			<br><br>
			<input class="upload_image_button button upload-button-2 upload-button-widget" type="button" value="Upload">
			<br><br>
			<input id="<?php echo $this->get_field_id( 'image_id_1' ); ?>" name="<?php echo $this->get_field_name( 'image_id_1' ); ?>" value="<?php echo (isset($instance['image_id_1'])?$instance['image_id_1']:"");?>" class="widefat image_id" type="hidden">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_href_1' ); ?>">Advertising url : </label>
			<input id="<?php echo $this->get_field_id( 'adv_href_1' ); ?>" name="<?php echo $this->get_field_name( 'adv_href_1' ); ?>" value="<?php echo (isset($instance['adv_href_1'])?esc_attr($instance['adv_href_1']):""); ?>" class="widefat" type="text">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_link_1' ); ?>">Open the page in same page or a new page?</label>
			<select id="<?php echo $this->get_field_id( 'adv_link_1' ); ?>" name="<?php echo $this->get_field_name( 'adv_link_1' ); ?>">
				<option value="same_page" <?php if( isset($instance['adv_link_1']) && $instance['adv_link_1'] == 'same_page' ) echo "selected=\"selected\""; else echo ""; ?>><?php esc_html_e("Same page","vbegy")?></option>
				<option value="new_page" <?php if( isset($instance['adv_link_1']) && $instance['adv_link_1'] == 'new_page' ) echo "selected=\"selected\""; else echo ""; ?>><?php esc_html_e("New page","vbegy")?></option>
			</select>
		</p>
		<em style="display:block; border-bottom:1px solid #CCC; margin-bottom:15px;">OR</em>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_code_1' ); ?>">Advertising Code html ( Ex: Google ads) : </label>
			<textarea id="<?php echo $this->get_field_id( 'adv_code_1' ); ?>" name="<?php echo $this->get_field_name( 'adv_code_1' ); ?>" class="widefat"><?php echo (isset($instance['adv_code_1'])?($instance['adv_code_1']):""); ?></textarea>
		</p>
		
		<em style="display:block; border-bottom:1px solid #CCC; margin-bottom:15px;"></em>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_img_2' ); ?>">Image URL : </label>
			<input id="<?php echo $this->get_field_id( 'adv_img_2' ); ?>" name="<?php echo $this->get_field_name( 'adv_img_2' ); ?>" value="<?php echo (isset($instance['adv_img_2'])?$instance['adv_img_2']:"");?>" class="widefat upload" type="text">
			<br><br>
			<input class="upload_image_button button upload-button-2 upload-button-widget" type="button" value="Upload">
			<br><br>
			<input id="<?php echo $this->get_field_id( 'image_id_2' ); ?>" name="<?php echo $this->get_field_name( 'image_id_2' ); ?>" value="<?php echo (isset($instance['image_id_2'])?$instance['image_id_2']:"");?>" class="widefat image_id" type="hidden">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_href_2' ); ?>">Advertising url : </label>
			<input id="<?php echo $this->get_field_id( 'adv_href_2' ); ?>" name="<?php echo $this->get_field_name( 'adv_href_2' ); ?>" value="<?php echo (isset($instance['adv_href_2'])?esc_attr($instance['adv_href_2']):""); ?>" class="widefat" type="text">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_link_2' ); ?>">Open the page in same page or a new page?</label>
			<select id="<?php echo $this->get_field_id( 'adv_link_2' ); ?>" name="<?php echo $this->get_field_name( 'adv_link_2' ); ?>">
				<option value="same_page" <?php if( isset($instance['adv_link_2']) && $instance['adv_link_2'] == 'same_page' ) echo "selected=\"selected\""; else echo ""; ?>><?php esc_html_e("Same page","vbegy")?></option>
				<option value="new_page" <?php if( isset($instance['adv_link_2']) && $instance['adv_link_2'] == 'new_page' ) echo "selected=\"selected\""; else echo ""; ?>><?php esc_html_e("New page","vbegy")?></option>
			</select>
		</p>
		<em style="display:block; border-bottom:1px solid #CCC; margin-bottom:15px;">OR</em>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_code_2' ); ?>">Advertising Code html ( Ex: Google ads) : </label>
			<textarea id="<?php echo $this->get_field_id( 'adv_code_2' ); ?>" name="<?php echo $this->get_field_name( 'adv_code_2' ); ?>" class="widefat"><?php echo (isset($instance['adv_code_2'])?($instance['adv_code_2']):""); ?></textarea>
		</p>
		
		<em style="display:block; border-bottom:1px solid #CCC; margin-bottom:15px;"></em>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_img_3' ); ?>">Image URL : </label>
			<input id="<?php echo $this->get_field_id( 'adv_img_3' ); ?>" name="<?php echo $this->get_field_name( 'adv_img_3' ); ?>" value="<?php echo (isset($instance['adv_img_3'])?$instance['adv_img_3']:"");?>" class="widefat upload" type="text">
			<br><br>
			<input class="upload_image_button button upload-button-2 upload-button-widget" type="button" value="Upload">
			<br><br>
			<input id="<?php echo $this->get_field_id( 'image_id_3' ); ?>" name="<?php echo $this->get_field_name( 'image_id_3' ); ?>" value="<?php echo (isset($instance['image_id_3'])?$instance['image_id_3']:"");?>" class="widefat image_id" type="hidden">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_href_3' ); ?>">Advertising url : </label>
			<input id="<?php echo $this->get_field_id( 'adv_href_3' ); ?>" name="<?php echo $this->get_field_name( 'adv_href_3' ); ?>" value="<?php echo (isset($instance['adv_href_3'])?esc_attr($instance['adv_href_3']):""); ?>" class="widefat" type="text">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_link_3' ); ?>">Open the page in same page or a new page?</label>
			<select id="<?php echo $this->get_field_id( 'adv_link_3' ); ?>" name="<?php echo $this->get_field_name( 'adv_link_3' ); ?>">
				<option value="same_page" <?php if( isset($instance['adv_link_3']) && $instance['adv_link_3'] == 'same_page' ) echo "selected=\"selected\""; else echo ""; ?>><?php esc_html_e("Same page","vbegy")?></option>
				<option value="new_page" <?php if( isset($instance['adv_link_3']) && $instance['adv_link_3'] == 'new_page' ) echo "selected=\"selected\""; else echo ""; ?>><?php esc_html_e("New page","vbegy")?></option>
			</select>
		</p>
		<em style="display:block; border-bottom:1px solid #CCC; margin-bottom:15px;">OR</em>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_code_3' ); ?>">Advertising Code html ( Ex: Google ads) : </label>
			<textarea id="<?php echo $this->get_field_id( 'adv_code_3' ); ?>" name="<?php echo $this->get_field_name( 'adv_code_3' ); ?>" class="widefat"><?php echo (isset($instance['adv_code_3'])?($instance['adv_code_3']):""); ?></textarea>
		</p>
		
		<em style="display:block; border-bottom:1px solid #CCC; margin-bottom:15px;"></em>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_img_4' ); ?>">Image URL : </label>
			<input id="<?php echo $this->get_field_id( 'adv_img_4' ); ?>" name="<?php echo $this->get_field_name( 'adv_img_4' ); ?>" value="<?php echo (isset($instance['adv_img_4'])?$instance['adv_img_4']:"");?>" class="widefat upload" type="text">
			<br><br>
			<input class="upload_image_button button upload-button-2 upload-button-widget" type="button" value="Upload">
			<br><br>
			<input id="<?php echo $this->get_field_id( 'image_id_4' ); ?>" name="<?php echo $this->get_field_name( 'image_id_4' ); ?>" value="<?php echo (isset($instance['image_id_4'])?$instance['image_id_4']:"");?>" class="widefat image_id" type="hidden">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_href_4' ); ?>">Advertising url : </label>
			<input id="<?php echo $this->get_field_id( 'adv_href_4' ); ?>" name="<?php echo $this->get_field_name( 'adv_href_4' ); ?>" value="<?php echo (isset($instance['adv_href_4'])?esc_attr($instance['adv_href_4']):""); ?>" class="widefat" type="text">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_link_4' ); ?>">Open the page in same page or a new page?</label>
			<select id="<?php echo $this->get_field_id( 'adv_link_4' ); ?>" name="<?php echo $this->get_field_name( 'adv_link_4' ); ?>">
				<option value="same_page" <?php if( isset($instance['adv_link_4']) && $instance['adv_link_4'] == 'same_page' ) echo "selected=\"selected\""; else echo ""; ?>><?php esc_html_e("Same page","vbegy")?></option>
				<option value="new_page" <?php if( isset($instance['adv_link_4']) && $instance['adv_link_4'] == 'new_page' ) echo "selected=\"selected\""; else echo ""; ?>><?php esc_html_e("New page","vbegy")?></option>
			</select>
		</p>
		<em style="display:block; border-bottom:1px solid #CCC; margin-bottom:15px;">OR</em>
		<p>
			<label for="<?php echo $this->get_field_id( 'adv_code_4' ); ?>">Advertising Code html ( Ex: Google ads) : </label>
			<textarea id="<?php echo $this->get_field_id( 'adv_code_4' ); ?>" name="<?php echo $this->get_field_name( 'adv_code_4' ); ?>" class="widefat"><?php echo (isset($instance['adv_code_4'])?($instance['adv_code_4']):""); ?></textarea>
		</p>
	<?php
	}
}
?>