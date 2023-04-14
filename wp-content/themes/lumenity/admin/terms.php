<?php /* Style taxonomy */
$args = apply_filters("askme_term_options",array('category',ask_question_category,'product_cat'));
askme_term_options($args);
function askme_term_options( $args ) {
	if (is_array($args) && !empty($args)) {
		foreach ($args as $taxonomy) {
			add_action( $taxonomy .'_add_form_fields', 'askme_term_add_form_fields',1 );
			add_action( $taxonomy .'_edit_form_fields', 'askme_term_edit_form_fields',1 );
			add_action( 'edited_'. $taxonomy, 'askme_save_term', 10 );
			add_action( 'create_'. $taxonomy, 'askme_save_term', 10 );
		}
	}
}
function askme_term_edit_form_fields( $tag ) {?>
	<tr id="optionsframework-metabox" class="group-2 askme_terms">
		<th colspan="2" scope="row" valign="top">
			<div id="optionsframework" class="postbox">
				<?php askme_admin_fields_class::askme_admin_fields("term_edit",prefix_terms,"term",$tag->term_id,askme_admin_terms($tag->taxonomy,$tag->term_id,"edit"));?>
			</div>
		</th>
	</tr>
	<?php
}
function askme_term_add_form_fields( $tag ) {?>
	<div id="optionsframework-metabox" class="group-2 askme_terms">
		<div id="optionsframework" class="postbox">
			<?php askme_admin_fields_class::askme_admin_fields("term_add",prefix_terms,"term",null,askme_admin_terms($tag));?>
		</div>
	</div>
	<?php 
}
function askme_save_term( $term_id ) {
	$term = get_term($term_id);
	if (!function_exists('askme_admin_terms')) {
		require_once locate_template("admin/terms.php");
	}
	$options = askme_admin_terms($term->taxonomy,$term_id);
	foreach ($options as $value) {
		if ($value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != 'info' && $value['type'] != 'group' && $value['type'] != 'html' && $value['type'] != 'content') {
			$val = '';
			if (isset($value['std'])) {
				$val = $value['std'];
			}
			
			$field_name = $value['id'];
			
			if (isset($_POST[$field_name])) {
				$val = $_POST[$field_name];
			}
			
			if (!isset($_POST[$field_name]) && $value['type'] == "checkbox") {
				$val = 0;
			}else if (isset($_POST[$field_name]) && $value['type'] == "checkbox" && $_POST[$field_name] == "on") {
				$val = 1;
			}
			
			if (array() === $val) {
				delete_term_meta($term_id,$field_name);
			}else {
				update_term_meta($term_id,$field_name,$val);
			}
		}
	}
}
/* Term options */
function askme_admin_terms($tax = "",$term_id = "",$type = "add") {
	// Background Defaults
	$background_defaults = array(
		'color'      => '',
		'image'      => '',
		'repeat'     => 'repeat',
		'position'   => 'top center',
		'attachment' => 'scroll'
	);
	
	// Pull all the sidebars into an array
	$new_sidebars = askme_registered_sidebars();
	
	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri(). '/admin/images/';
	$imagepath_theme =  get_template_directory_uri(). '/images/';
	
	$options = array();

	$options = apply_filters('askme_before_terms_options',$options,$tax,$term_id,$type);

	if (isset($tax) && $tax == ask_question_category) {
		$options[] = array(
			'name' => esc_html__("Question Category Setting","vbegy"),
			'type' => 'heading-2'
		);

		$options = apply_filters('askme_terms_before_setting',$options);

		$options[] = array(
			'name' => esc_html__('Private category?','vbegy'),
			'desc' => esc_html__("Select 'On' to enable private category. (In private categories questions can only be seen by the author of the question and the admin).","vbegy"),
			'id'   => prefix_terms.'private',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('Special category?','vbegy'),
			'desc' => esc_html__("Select 'On' to enable special category. (In a special category, the admin must answer the question before anyone else).","vbegy"),
			'id'   => prefix_terms.'special',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'name' => esc_html__('New category?','vbegy'),
			'desc' => esc_html__("Select 'On' to enable new category. (In the new category, admin must answer the question before anyone else and the user has asked question and only admin can answer).","vbegy"),
			'id'   => prefix_terms.'new',
			'type' => 'checkbox'
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
	}

	$tax_activate = apply_filters('askme_tax_activate',false,$tax,$term_id);
	if ($tax == "category" || $tax == ask_question_category || $tax == "product_cat" || $tax_activate == true) {
		$options[] = array(
			'name' => esc_html__("Category Setting","vbegy"),
			'type' => 'heading-2'
		);
		
		/*
		$options[] = array(
			'name' => (isset($tax) && $tax == ask_question_category?esc_html__('Enable the setting at questions','vbegy'):esc_html__('Enable the setting at posts','vbegy')),
			'desc' => (isset($tax) && $tax == ask_question_category?esc_html__("Select ON to enable the setting at inner questions","vbegy"):esc_html__("Select ON to enable the setting at inner posts","vbegy")),
			'id'   => prefix_terms.'setting_single',
			'type' => 'checkbox'
		);
		*/
		
		$options[] = array(
			'name' => esc_html__("Category layout","vbegy"),
			'id'   => prefix_terms."cat_layout",
			'std'  => "default",
			'type' => "images",
			'options' => array(
				'default' => $imagepath.'sidebar_default.jpg',
				'full'    => $imagepath.'full.jpg',
				'fixed'   => $imagepath.'fixed.jpg',
				'fixed_2' => $imagepath.'fixed_2.jpg',
			)
		);

		$options[] = array(
			'name'    => esc_html__('Choose page / post template','vbegy'),
			'id'      => prefix_terms."cat_template",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default'   => $imagepath.'sidebar_default.jpg',
				'grid_1300' => $imagepath.'template_1300.jpg',
				'grid_1200' => $imagepath.'template_1200.jpg',
				'grid_970'  => $imagepath.'template_970.jpg',
			)
		);

		$options[] = array(
			'name'    => esc_html__('Choose category skin','vbegy'),
			'id'      => prefix_terms."cat_skin_l",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default'    => $imagepath.'sidebar_default.jpg',
				'site_light' => $imagepath.'light.jpg',
				'site_dark'  => $imagepath.'dark.jpg',
			)
		);

		$options[] = array(
			'name'    => esc_html__('Choose Your Skin','vbegy'),
			'id'      => prefix_terms."cat_skin",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default'       => $imagepath.'default.jpg',
				'skin'          => $imagepath.'skin.jpg',
				'blue'          => $imagepath.'blue.jpg',
				'gray'          => $imagepath.'gray.jpg',
				'green'         => $imagepath.'green.jpg',
				'moderate_cyan' => $imagepath.'moderate_cyan.jpg',
				'orange'        => $imagepath.'orange.jpg',
				'purple'        => $imagepath.'purple.jpg',
				'red'           => $imagepath.'red.jpg',
				'strong_cyan'   => $imagepath.'strong_cyan.jpg',
				'yellow'        => $imagepath.'yellow.jpg',
			)
		);
		
		$options[] = array(
			'name'		=> esc_html__('Primary Color','vbegy'),
			'id'		=> prefix_terms."primary_color",
			'type'		=> 'color',
		);
		
		$options[] = array(
			'name' => esc_html__('Background','vbegy'),
			'id'   => prefix_terms."custom_background",
			'std'  => $background_defaults,
			'type' => 'background'
		);
		
		$options[] = array(
			'name' => esc_html__("Full Screen Background",'vbegy'),
			'id'   => prefix_terms."background_full",
			'type' => 'checkbox',
			'std'  => 0,
		);

		$options[] = array(
			'name'    => esc_html__('Sidebar','vbegy'),
			'id'      => prefix_terms."cat_sidebar_layout",
			'std'     => "default",
			'type'    => "images",
			'options' => array(
				'default' => $imagepath.'sidebar_default.jpg',
				'right'   => $imagepath.'sidebar_right.jpg',
				'full'    => $imagepath.'sidebar_no.jpg',
				'left'    => $imagepath.'sidebar_left.jpg',
			)
		);
		
		$options[] = array(
			'name'		=> esc_html__('Select your sidebar','vbegy'),
			'id'		=> prefix_terms.'cat_sidebar',
			'type'		=> 'select',
			'condition' => prefix_terms.'cat_sidebar_layout:not(full)',
			'options'	=> $new_sidebars,
		);
		
		$options[] = array(
			'type' => 'heading-2',
			'end'  => 'end'
		);
	}
	
	return $options;
}