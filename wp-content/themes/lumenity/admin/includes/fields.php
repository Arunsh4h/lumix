<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      https://wptheming.com
 * @copyright 2013 WP Theming
 */

function askme_optionsframework_options() {
	static $options = null;

	if ( !$options ) {
        // Load options from options.php file (if it exists)
        $location = apply_filters( 'options_framework_location', array('admin/options.php') );
        if ( $optionsfile = locate_template( $location ) ) {
            $maybe_options = require_once $optionsfile;
            if ( is_array( $maybe_options ) ) {
				$options = $maybe_options;
            } else if ( function_exists( 'askme_admin_options' ) ) {
				$options = askme_admin_options();
			}
        }

        // Allow setting/manipulating options via filters
        $options = apply_filters( 'of_options', $options );
	}

	return $options;
}
function askme_optionsframework_tabs() {
	$counter = 0;
	$options = askme_optionsframework_options();
	$menu = '';

	foreach ( $options as $value ) {
		// Heading for Navigation
		if ( $value['type'] == "heading" ) {
			$counter++;
			$class = '';
			$class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
			$class = preg_replace( '/[^a-zA-Z0-9._\-]/', '', strtolower($class) ).'-tab';
			$menu .= '<a id="options-group-'.  $counter . '-tab" class="nav-tab ' . $class .'" title="' . esc_attr( $value['name'] ) . '" href="' . esc_attr( '#options-group-'.  $counter ) . '">' . (isset($value['icon']) && $value['icon'] != ''?'<span class="dashicons dashicons-'.esc_attr($value['icon']).'"></span>':'') . '<span class="options-name">'.esc_html( $value['name'] ).'</span></a>';
		}
	}

	return $menu;
}
/* Get uploader */
function askme_options_uploader ($_id,$_value,$_desc = '',$_name = '',$no_id_name = '',$options = array()) {
	// Gets the unique option id
	$option_name = (strpos($_SERVER['REQUEST_URI'],'page=options') !== false?vpanel_options:askme_meta);
	$output = $id = $class = $int = $value = $value_id = $name = '';
	$id = strip_tags( strtolower( $_id ) );
	
	// If a value is passed and we don't have a stored value, use the value that's passed through.
	if (is_array($_value) && !empty($_value) && array_key_exists('url',$_value) && array_key_exists('id',$_value)) {
		$value = $_value["url"];
		$value_id = $_value["id"];
	}else if (!is_array($_value) && $_value != '' && $value == '') {
		$value = $_value;
	}
	
	if (isset($value) && $value != "" && is_numeric($value)) {
		$value_id = $value;
		$value = wp_get_attachment_url($value_id);
	}

	$value_id = (int)$value_id;

	if ($value_id == 0 && $value != "") {
		if (strpos($value,esc_url(home_url('/'))) !== false && strpos($value,"themes/".get_template()."/image") === false) {
			$value_id = askme_get_attachment_id($value);
		}
	}
	
	if ($_name != '') {
		$name = $_name;
	}else {
		$name = $option_name.'['.$id.']';
	}
	$image_attrs = (isset($options['height'])?'data-height="'.$options['height'].'" ':'');
	$image_attrs = (isset($options['width'])?'data-width="'.$options['width'].'" ':'').$image_attrs;
	
	if ( $value ) {
		$class = ' has-file';
	}
	$output .= '<div class="form-upload-images">
	<input class="image_id" type="hidden" '.($no_id_name == 'no'?'data-attr="'.esc_attr($name).'][id"':'name="'.esc_attr($name).'[id]"').' value="' . (int)$value_id . '">
	<input '.($no_id_name == 'no'?'attr-id="'.$id.'"':'id="'.$id.'"').' class="upload' . esc_attr($class) . '" type="text" '.($no_id_name == 'no'?'data-attr="'.esc_attr($name).'][url"':'name="'.esc_attr($name).'[url]"').' value="' . esc_attr($value) . '" placeholder="' . esc_attr__('No file chosen', "vbegy") .'">';
	if ( function_exists( 'wp_enqueue_media' ) ) {
		if ( ( $value == '' ) ) {
			$output .= '<input '.$image_attrs.($no_id_name == 'no'?'data-attr="upload-'.esc_attr($id).'"':'id="upload-'.esc_attr($id).'"').' class="upload-button button" type="button" value="' . esc_attr__( 'Upload', "vbegy" ) . '">';
		}else {
			$output .= '<input '.$image_attrs.($no_id_name == 'no'?'data-attr="remove-'.esc_attr($id).'"':'id="remove-'.esc_attr($id).'"').' class="remove-file button" type="button" value="' . esc_attr__( 'Remove', "vbegy" ) . '">';
		}
	}else {
		$output .= '<p><i>' . esc_html__( 'Upgrade your version of WordPress for full media support.', "vbegy" ) . '</i></p>';
	}
	$output .= '</div>';
	if ( $_desc != '' ) {
		$output .= '<span class="vpanel-metabox-desc">' . $_desc . '</span>';
	}

	$output .= '<div class="screenshot" '.($no_id_name == 'no'?'data-attr="'.$id.'-image"':'id="'.$id.'-image"').'>';

	if ( $value != '' ) {
		$remove = '<a class="remove-image">'.esc_html__("Remove","vbegy").'</a>';
		$image = preg_match('/\.(jpg|jpeg|png|gif|ico)$/',$value);
		if ( $image ) {
			$output .= '<img src="' . $value . '" alt="' . $value . '">' . $remove;
		}else {
			$parts = explode( "/", $value );
			for( $i = 0; $i < sizeof( $parts ); ++$i ) {
				$title = $parts[$i];
			}

			// No output preview if it's not an image.
			$output .= '';

			// Standard generic output if it's not an image.
			$title = esc_html__( 'View File', "vbegy" );
			$output .= '<div class="no-image"><span class="file_link"><a href="' . esc_url($value) . '" target="_blank" rel="external">'.$title.'</a></span></div>';
		}
	}
	$output .= '</div>';
	return $output;
}
function askme_options_fields($settings = array(),$option_name = "",$page = "options",$post_term = 0,$options_arrgs = array()) {
	global $allowedtags;
	$page = ($page == 'author'?'user':$page);
	$page = ($page == 'meta'?'post':$page);
	$wp_page_template = ($page == "post" && isset($post_term) && $post_term > 0?get_post_meta($post_term,"_wp_page_template",true):"");
	if ($option_name == "") {
		$askme_admin_settings = get_option(askme_options);
		// Gets the unique option id
		if ( isset( $askme_admin_settings['id'] ) ) {
			$option_name = $askme_admin_settings['id'];
		}else {
			$option_name = askme_options;
		}
		if ($page == "options") {
			$settings = get_option($option_name);
		}
	}
	
	$options = $options_arrgs;
	if (empty($options_arrgs)) {
		$options = askme_optionsframework_options($page);
	}
	
	$counter = 0;
	$menu = '';
	$values = array();
	
	foreach ( $options as $value ) {
		$val = $val_terms = $select_value = $output = '';

		// Wrap all options
		if ($value['type'] != "heading") {

			// Keep all ids lowercase with no spaces
			if (isset($value['id'])) {
				$value_name_id = $value['id'];
				$value['id'] = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($value['id']) );
				//$value['id'] = $value['id'];
				$id = 'section-'.($page == "widgets"?$post_term->get_field_id( $value['id'] ):$value['id']);
			}

			$class = 'section';
			$wrap_class = 'wrap_class';
			$options_group = 'options-group';
			if ( isset( $value['type'] ) ) {
				$class .= ' section-'.$value['type'].' vpanel-form-'.$value['type'];
			}
			if ( isset( $value['class'] ) ) {
				$class .= ' '.$value['class'];
			}
			
			if ( ! array_key_exists( 'operator', $value ) || ! in_array( $value['operator'], array( 'and', 'or' ) ) ) {
				$value['operator'] = 'and';
			}

			if ( ! array_key_exists( 'condition', $value ) || ! is_string( $value['condition'] ) ) {
				$value['condition'] = '';
			}

			if ($value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != "content" && $value['type'] != "html" && $value['type'] != "info") {
				// Set default value to $val
				if ( isset( $value['std'] ) ) {
					$val = $value['std'];
				}
				
				$field_id = esc_html(($page == 'widgets'?$post_term->get_field_id($value['id']):(isset($value['id'])?$value['id']:'')));
				if ($page == "options" && isset($value['unset'])) {
					$field_name = "";
				}else {
					$field_name = esc_html(($page == 'widgets'?$post_term->get_field_name($value['id']):($page == 'post' || $page == 'term' || $page == 'user'?$field_id:$option_name.'['.$field_id.']')));
				}
			}
			
			// If the option is already saved, override $val
			if ($value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != 'info' && $value['type'] != "content" && $value['type'] != "html" && !isset($value['readonly'])) {
				if (isset($post_term) || (isset($settings[($value['id'])]) && isset($value['id']) && ($value['type'] != "editor" || ($value['type'] == "editor" && $settings[($value['id'])] != "")))) {
					if ($page == "widgets") {
						$val = (isset($settings[$value['id']])?$settings[$value['id']]:(isset($val)?$val:""));
					}else if ($page == "post" && isset($post_term)) {
						if (isset($value['save']) && $value['save'] == "option") {
							$val_terms = get_option($field_name);
						}else {
							$val_terms = get_post_meta($post_term,$field_name,true);
						}
					}else if ($page == "term" && isset($post_term)) {
						$val_terms = get_term_meta($post_term,$field_name,true);
					}else if ($page == "user" && isset($post_term)) {
						$val_terms = get_user_meta($post_term,$field_name,true);
					}else if ($page == "options") {
						$val = $settings[$field_id];
					}
					
					if ($page == 'post' || $page == 'term' || $page == 'user') {
						if (metadata_exists($page,$post_term,$field_name)) {
							$val = $val_terms;
						}
					}
					
					// Striping slashes of non-array options
					if (!is_array($val)) {
						$val = stripslashes($val);
					}
				}
			}
			
			$val = ($page == "widgets" && isset($value['id']) && isset($value['type']) && $value['type'] == "checkbox" && isset($settings[$value['id']])?$settings[$value['id']]:$val);

			$val = (isset($value['val'])?$value['val']:$val);

			// If there is a description save it for labels
			$explain_value = '';
			if ( isset( $value['desc'] ) ) {
				$explain_value = $value['desc'];
			}
			
			if ($value['type'] != 'heading' && $value['type'] != "heading-2" && $value['type'] != 'info' && $value['type'] != "content" && $value['type'] != "html" && !isset($value['readonly']) && array_key_exists( 'id', $value )) {
				$values[ $value['id'] ] = ($value['type'] == 'checkbox' && $val == ""?0:$val);
			}

			if ( ! askme_field_is_visible( $value['condition'], $value['operator'], $options, $values ) ) {
				$class .= ' hide';
				$wrap_class .= ' hide';
				$options_group .= ' hide';
			}
			
			$condition = empty( $value['condition'] ) ? '' : ' data-condition="'. esc_attr( $value['condition'] ) .'"';
			$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value['operator'] ) .'"';
			
			if ($value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != "content" && $value['type'] != "html" && $value['type'] != "info" && $value['type'] != 'hidden') {
				$output .= '<div data-type="'.$value['type'].'"'.(isset($value['id'])?' data-id="'.esc_attr( $value['id'] ).'"':'').( $condition ).( $operator ).' id="'.esc_attr( $id ).'" class="'.esc_attr( $class ).'"'.(isset($value['margin']) && $value['margin'] != ""?" style='margin:".$value['margin']."'":"").'>';
				$output .= '<div class="name-with-desc">';
				if (isset($value['name'])) {
					$output .= '<h4 class="heading">'.$value['name'].'</h4>';
				}
				$output .= '<div class="option">';
				if ($value['type'] != "heading" && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != "info" && $value['type'] != "content" && $value['type'] != "html" && $value['type'] != 'hidden') {
					if ($value['type'] == "checkbox") {
						$output .= '<label class="explain explain-checkbox" for="'.$field_id.'">'.wp_kses($explain_value,$allowedtags).'</label>';
					}else if ($value['type'] == "checkbox") {
						$output .= '<div class="explain">'.wp_kses($explain_value,$allowedtags).'</div>';
					}else {
						$output .= '<div class="explain vpanel_help"><div>'.wp_kses($explain_value,$allowedtags).'</div></div>';
					}
				}
				if ( $value['type'] != 'editor' && $value['type'] != 'upload' && $value['type'] != 'background' && $value['type'] != 'sidebar' && $value['type'] != 'badges' && $value['type'] != 'coupons' && $value['type'] != 'roles' ) {
					$output .= '</div></div><div class="controls'.(isset($value['limit-height'])?' limit-height':'').'">';
				}else if ( $value['type'] == 'upload' || $value['type'] == 'background' ) {
					$output .= '</div></div><div class="controls controls-upload">';
				}else if ( $value['type'] == 'sidebar' ) {
					$output .= '</div></div><div class="controls controls-sidebar">';
				}else if ( $value['type'] == 'badges' ) {
					$output .= '</div></div><div class="controls controls-badges">';
				}else if ( $value['type'] == 'coupons' ) {
					$output .= '</div></div><div class="controls controls-coupons">';
				}else if ( $value['type'] == 'roles' ) {
					$output .= '</div></div><div class="controls controls-role">';
				}else {
					$output .= '</div></div><div>';
				}
			}
		}

		if ( isset($value['type']) && has_filter( 'vpanel_'.$value['type'] ) ) {
			$output .= apply_filters( 'vpanel_'.$value['type'], $option_name, $value, $val );
		}
		if (isset($value['type'])) {
			$output = apply_filters('askme_'.$value['type'].'_field',$output,$value,$val,(isset($option_name)?$option_name:""),(isset($field_name)?$field_name:""),(isset($field_id)?$field_id:""));
		}
		
		if (isset($value['type'])) {
			switch ( $value['type'] ) {

			// Basic text input
			case 'text':
				$output .= '<input'.(isset($field_id)?' id="'.esc_attr( $field_id ).'" name="'.esc_attr( $field_name ).'"':'').' class="of-input vpanel-form-control" type="text" value="'.esc_attr( $val ).'"'.(isset($value['readonly'])?' readonly':'').'>';
				break;

			// input hidden
			case 'hidden':
				$output .= '<input id="'.esc_attr( $field_id ).'" class="of-input vpanel-form-control" name="'.esc_attr( $field_name ).'" type="hidden" value="'.esc_attr( $val ).'">';
				break;
			
			// Password input
			case 'password':
				$output .= '<input id="'.esc_attr( $field_id ).'" class="of-input vpanel-form-control" name="'.esc_attr( $field_name ).'" type="password" value="'.esc_attr( $val ).'">';
				break;

			// Textarea
			case 'textarea':
				$rows = '8';

				if ( isset( $value['settings']['rows'] ) ) {
					$custom_rows = $value['settings']['rows'];
					if ( is_numeric( $custom_rows ) ) {
						$rows = $custom_rows;
					}
				}

				$val = stripslashes( $val );
				$output .= '<textarea id="'.esc_attr( $field_id ).'" class="of-input vpanel-form-control" name="'.esc_attr( $field_name ).'" rows="'.$rows.'">'.esc_textarea( $val ).'</textarea>';
				break;
			
			// Select custom additions
			case 'custom_addition':
				if (isset($value['addto']) && $value['addto'] != "") {
					$field_id = $value['addto'];
				}else {
					$field_id = $value['id'];
				}
				$field_type = (isset($value['addition'])?$value['addition']:'cat');
				if (isset($value['options'])) {
					$select_options = '<select id="">';
					foreach ($value['options'] as $key_options => $value_options) {
						$select_options .= '<option value="'.$key_options.'">'.$value_options.'</option>';
					}
					$select_options .= '</select>';
				}else {
					$select_options = wp_dropdown_categories(array(
						'taxonomy'          => (isset($value['taxonomy']) && $value['taxonomy'] != ""?$value['taxonomy']:ask_question_category),
					    'orderby'           => 'name',
					    'echo'              => 0,
					    'hide_empty'        => 0,
					    'hierarchical'      => 1,
					    'id'                => (isset($field_id) && $field_id != ""?$field_id:""),
					    'name'              => "",
					    'show_option_none'  => (isset($value['show_option']) && $value['show_option'] != ""?esc_html($value['show_option']):esc_html__('Show Categories','vbegy')),
					    'option_none_value' => (isset($value['option_none']) && $value['option_none'] != ""?esc_html($value['option_none']):0),
					));
				}
				$output .= '
				<div class="styled-select">'.$select_options.'</div>
				<div class="addition_tabs">';
					if (empty($value['addto'])) {
						$output .= '<ul id="'.(isset($field_id) && $field_id != ""?$field_id:"").'-ul" class="sort-sections sort-sections-ul">';
							$i = 0;
							if (isset($val) && is_array($val)) {
								foreach ($val as $key_a => $value_a) {
									if (isset($value['values'])) {
										$object = $value['values'];
										$object_name = $object[$value_a];
									}else {
										$object = get_term_by('id',$value_a,(isset($value['taxonomy']) && $value['taxonomy'] != ""?$value['taxonomy']:ask_question_category));
										$object_name = $object->name;
									}
									$i++;
									$output .= '<li class="li class="additions-li"" id="'.(isset($field_id) && $field_id != ""?$field_id:"").'_additions_li_'.$value_a.'"><div class="widget-head">
										<span>'.((isset($value['option_none']) && $value['option_none'] != "" && $value_a == $value['option_none']) || $value_a == "0"?esc_html__('All Categories','vbegy'):$object_name).'</span></div><input name="'.(isset($field_id) && $field_id != ""?$field_id:"").'['.$field_type.'-'.$value_a.']" value="'.$value_a.'" type="hidden">
										<div>
											<a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>
											<a class="del-builder-item"><span class="dashicons dashicons-trash"></span></a>
										</div>
									</li>';
								}
							}
						$output .= '</ul>';
					}
				$output .= '</div>
				<div class="clear"></div>
				<div class="add-item add-item-2 add-item-6 add-item-7" data-type="'.$field_type.'" data-toadd="'.(isset($value['toadd']) && $value['toadd'] != ""?$value['toadd']:"").'" data-addto="'.(isset($field_id) && $field_id != ""?$field_id:"").'" data-id="'.(isset($field_id) && $field_id != ""?$field_id:"").'_additions" data-name="'.(isset($field_id) && $field_id != ""?esc_attr(($page == 'widgets'?$post_term->get_field_name($value['id']):($page == 'post' || $page == 'term' || $page == 'user'?$field_id:$option_name.'['.$field_id.']'))):"").'">'.(isset($value["button"])?$value["button"]:esc_html__("Add category","vbegy")).'</div>
				<div class="clear"></div>';
				break;

			// Upload images
			case 'upload_images';
				$output .= '<div class="images-uploaded">
					<a data-id="'.$field_id.'" data-name="'.$field_name.'" class="upload_image_button upload_image_button_m" href="#">'.esc_html__("Upload","vbegy").'</a>
					<div class="clear"></div>
					<ul id="'.$field_id.'">';
						$val = (isset($val) && is_array($val)?$val:array());
						if (isset($val) && is_array($val)) {
							foreach ($val as $value_image) {
								$image_url = wp_get_attachment_image($value_image,"thumbnail");
								$output .= "<li id='".$field_id."-item-".$value_image."' class='multi-images'>
									<div class='multi-image'>
										".$image_url."
										<input name='".$field_name."[]' type='hidden' value='".$value_image."'>
										<div class='image-overlay'></div>
										<div class='image-media-bar'>
											<a class='image-edit-media' title='".esc_attr__("Edit","vbegy")."' href='post.php?post=".$value_image."&amp;action=edit' target='_blank'>
												<span class='dashicons dashicons-edit'></span>
											</a>
											<a href='#' class='image-remove-media' title='".esc_attr__("Remove","vbegy")."'>
												<span class='dashicons dashicons-no-alt'></span>
											</a>
										</div>
									</div>
								</li>";
							}
						}
					$output .= '</ul>
				</div>';
				break;

			// Role
			case 'roles':
				global $wp_roles;
				$roles = get_option(esc_attr($field_id));
				$k = 0;
				$array_values = array(
					"ask_question"      => esc_html__("Select ON to can add a question.","vbegy"),
					"show_question"     => esc_html__("Select ON to can view questions.","vbegy"),
					"show_post"         => esc_html__("Select ON to can view posts.","vbegy"),
					"add_answer"        => esc_html__("Select ON to can add an answer.","vbegy"),
					"show_answer"       => esc_html__("Select ON to can view answers.","vbegy"),
					"add_post"          => esc_html__("Select ON to can add a post.","vbegy"),
					"add_comment"       => esc_html__("Select ON to can add a comment.","vbegy"),
					"show_comment"      => esc_html__("Select ON to can view comments.","vbegy"),
					"send_message"      => esc_html__("Select ON to can send a message.","vbegy"),
					"upload_files"      => esc_html__("Select ON to can upload files.","vbegy"),
					"follow_question"   => esc_html__("Select ON to can follow a question.","vbegy"),
					"favorite_question" => esc_html__("Select ON to can add a question at favorite.","vbegy")
				);
				$output .= '
				<input id="role_name" type="text" name="role_name" value="">
				<input id="role_add" type="button" value="+ Add new group">
				<div class="clear"></div>
				<ul id="roles_list" class="roles_list">';
					if (isset($roles) && is_array($roles) && !empty($roles)) {
						foreach ($roles as $role) {$k++;
							if (isset($wp_roles->roles) && isset($role["id"])) {
								unset($wp_roles->roles[$role["id"]]);
							}
							$output .= '<li><div class="widget-head">'.(isset($role["group"])?esc_html($role["group"]):"").'<a class="del-builder-item del-role-item">x</a></div>
								<div class="widget-content">
									<div class="widget-content-div">
										<label for="roles['.$k.'][group]">Type here the group name.</label>
										<input id="roles['.$k.'][group]" type="text" name="roles['.$k.'][group]" value="'.(isset($role["group"]) && $role["group"] != ''?esc_html($role["group"]):'').'">
										
										<input type="hidden" class="group_id" name="roles['.$k.'][id]" value="group_'.$k.'">
										<div class="clearfix"></div>';
										foreach ($array_values as $key_role => $value_role) {
											$output .= '<label class="switch" for="roles['.$k.']['.$key_role.']">
												<input id="roles['.$k.']['.$key_role.']" type="checkbox" name="roles['.$k.']['.$key_role.']"'.(isset($role[$key_role]) && ($role[$key_role] == 'on' || $role[$key_role] == 1)?' checked="checked"':'').'>
												<label for="roles['.$k.']['.$key_role.']" data-on="'.esc_html__("ON","vbegy").'" data-off="'.esc_html__("OFF","vbegy").'"></label>
											</label>
											
											<label for="roles['.$k.']['.$key_role.']">'.$value_role.'</label>
											<div class="clearfix"></div>';
										}
									$output .= '</div>
								</div>
							</li>';
						}
					}
				$output .= '</ul><div class="clear"></div>
				<ul class="roles_list">';
					$roles_default = get_option("roles_default");
					$old_roles = $wp_roles->roles;
					unset($old_roles["activation"]);
					unset($old_roles["ask_under_review"]);
					unset($old_roles["administrator"]);
					do_action("askme_remove_roles",$old_roles);
					foreach ($old_roles as $key_r => $value_r) {
						$output .= '<li>
							<div class="widget-head">'.esc_html($value_r['name']).'</div>
							<div class="widget-content">
								<div class="widget-content-div">';
									foreach ($array_values as $key_role => $value_role) {
										$output .= '<label class="switch" for="roles_default['.$key_r.']['.$key_role.']">
											<input id="roles_default['.$key_r.']['.$key_role.']" type="checkbox" name="roles_default['.$key_r.']['.$key_role.']"'.(isset($roles_default[$key_r][$key_role]) && ($roles_default[$key_r][$key_role] == 'on' || $roles_default[$key_r][$key_role] == 1)?' checked="checked"':'').'>
											<label for="roles_default['.$key_r.']['.$key_role.']" data-on="'.esc_html__("ON","vbegy").'" data-off="'.esc_html__("OFF","vbegy").'"></label>
										</label>
										
										<label for="roles_default['.$key_r.']['.$key_role.']">'.$value_role.'</label>
										<div class="clearfix"></div>';
									}
								$output .= '</div>
							</div>
						</li>';
					}
				$output .= '</ul><div class="clear"></div>
				<script type="text/javascript">roles_j = '.($k+1).';</script>';
				break;
			
			// Sections
			case 'sections':
				$output .= '<ul class="sort-sections">';
					$order_sections_li = $val;
					if (empty($order_sections_li)) {
						$order_sections_li = array(1 => "advertising",2 => "author",3 => "related",4 => "advertising_2",5 => "comments",6 => "next_previous");
					}
					$order_sections = $order_sections_li;
					$i = 0;
					$array_not_found = array("advertising","author","related","advertising_2","comments","next_previous");
					foreach ($array_not_found as $key_not => $value_not) {
						if (!in_array($value_not,$order_sections)) {
							array_push($order_sections,$value_not);
						}
					}
					
					foreach ($order_sections as $key_r => $value_r) {
						$i++;
						if ($value_r == "") {
							unset($order_sections[$key_r]);
						}else {
							$output .= '<li id="'.$field_id."-".esc_attr($value_r).'" class="ui-state-default">
								<div class="widget-head"><span>';
								if ($value_r == "next_previous") {
									$output .= esc_attr("Next and Previous articles");
								}else if ($value_r == "advertising") {
									$output .= esc_attr("Advertising");
								}else if ($value_r == "author") {
									$output .= esc_attr("About the author");
								}else if ($value_r == "related") {
									$output .= esc_attr("Related articles");
								}else if ($value_r == "advertising_2") {
									$output .= esc_attr("Advertising 2");
								}else if ($value_r == "comments") {
									$output .= esc_attr("Comments");
								}
								$output .= '</span></div>
								<input name="'.esc_attr( $field_name.'['.esc_attr($i).']' ).'" value="';if ($value_r == "next_previous") {$output .= esc_attr("next_previous");}else if ($value_r == "advertising") {$output .= esc_attr("advertising");}else if ($value_r == "author") {$output .= esc_attr("author");}else if ($value_r == "related") {$output .= esc_attr("related");}else if ($value_r == "advertising_2") {$output .= esc_attr("advertising_2");}else if ($value_r == "comments") {$output .= esc_attr("comments");}$output .= '" type="hidden">
							</li>';
						}
					}
				$output .= '</ul>';
				break;
			
			// Sort
			case 'sort':
				$output .= '<ul id="'.$value['id'].'" class="sort-sections sort-sections-ul">';
					$sort_sections = $val;
					if (empty($sort_sections) || (count($sort_sections) <> count($value['options']))) {
						if (isset($value['merge']) && !empty($value['merge']) && is_array($value['merge'])) {
							foreach ($value['merge'] as $key_merge => $value_merge) {
								$sort_sections = (!in_array($value_merge,$sort_sections)?array_merge($sort_sections,array($value_merge)):$sort_sections);
							}
						}
					}else {
						if (isset($value['merge']) && !empty($value['merge']) && is_array($value['merge'])) {
							foreach ($value['merge'] as $key_merge => $value_merge) {
								$sort_sections = (!in_array($value_merge,$sort_sections)?array_merge($sort_sections,array($value_merge)):$sort_sections);
							}
						}
					}
					$i = 0;
					
					$array_not_found = $value['options'];
					foreach ($array_not_found as $key_not => $value_not) {
						if (!in_array($value_not,$sort_sections) && !array_key_exists('default',$value_not)) {
							array_push($sort_sections,$value_not);
						}
					}
					
					if (isset($sort_sections) && is_array($sort_sections)) {
						foreach ($sort_sections as $key => $value_for) {
							$i++;
							$output .= '<li id="elements_'.$value['id'].'_'.esc_attr($i).'">
								<div class="widget-head"><span>'.ucfirst(isset($value_for["name"]) && is_array($value_for["name"]) && isset($value_for["name"]["value"])?esc_attr($value_for["name"]["value"]):esc_attr($value_for["name"])).'</span><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.(isset($value["delete"]) && $value["delete"] == "yes" && isset($value_for['getthe']) && $value_for['getthe'] != ""?'<a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
								if (isset($value_for['getthe']) && $value_for['getthe'] != "") {
									$output .= '<div class="widget-content">';
								}
								
								foreach ($value_for as $key_a => $value_a) {
									if ($key_a != "getthe" && isset($value_for['getthe']) && $value_for['getthe'] != "") {
										$output .= '<h4>'.$key_a.'</h4>';
									}
									if (is_array($value_for[$key_a]) && array_key_exists("type",$value_for[$key_a]) && $value_for[$key_a]["type"] != "" && $value_for[$key_a]["type"] != "text" && $key_a != "getthe") {
										if ($value_for[$key_a]["type"] == "textarea") {
											$output .= '<textarea name="'.esc_attr( $field_name.'['.esc_attr($i).']['.$key_a.'][value]' ).'" rows="8" class="of-input vpanel-form-control">'.$value_for[$key_a]["value"].'</textarea>';
										}
									}else {
										$output .= '<input name="'.esc_attr( $field_name.'['.esc_attr($i).']['.$key_a.']'.(isset($value_for["default"]) && $value_for["default"] == "yes"?"":"[value]") ).'" value="'.(isset($value_for[$key_a]) && is_array($value_for[$key_a])?esc_attr($value_for[$key_a]["value"]):esc_attr($value_for[$key_a])).'" type="'.($key_a != "getthe" && isset($value_for['getthe']) && $value_for['getthe'] != ""?"text":"hidden").'">';
									}
									if (!isset($value_for["default"]) && $key_a != "getthe") {
										$output .= '<input name="'.esc_attr( $field_name.'['.esc_attr($i).']['.$key_a.']'.(isset($value_for["default"]) && $value_for["default"] == "yes"?"":"[value]") ).'" value="'.(isset($value_for[$key_a]) && is_array($value_for[$key_a])?esc_attr($value_for[$key_a]["value"]):esc_attr($value_for[$key_a])).'" type="'.($key_a != "getthe" && isset($value_for['getthe']) && $value_for['getthe'] != ""?"text":"hidden").'">';
									}
									if ($key_a != "getthe" && $key_a != "default" && empty($value_for["default"])) {
										$output .= '<input name="'.esc_attr( $field_name.'['.esc_attr($i).']['.$key_a.']'.(isset($value_for["default"]) && $value_for["default"] == "yes"?"":"[type]") ).'" value="'.(isset($value_for["name"]) && is_array($value_for["name"])?esc_attr($value_for["name"]["type"]):"text").'" type="hidden">';
									}
								}
								if (isset($value_for['getthe']) && $value_for['getthe'] != "") {
									$output .= '</div';
								}
							$output .= '</li>';
						}
					}
				$output .= '</ul>';
				break;

			// Select category
			case 'select_category':
				if (isset($value['selected']) && $value['selected'] == "s_f_category") {
					$category = current(wp_get_object_terms($post_term,ask_question_category));
					if (!isset($category->name)) $category = '';
				}
				$output .= '<div class="styled-select">'.
					wp_dropdown_categories(array(
						'show_option_none'  => (isset($value['option_none']) && $value['option_none'] != ""?$value['option_none']:0),
					    'orderby'           => 'name',
					    'hide_empty'        => 0,
					    'hierarchical'      => 1,
					    'echo'              => 0,
					    'class'             => (isset($value['class']) && $value['class'] != ""?$value['class']:""),
					    'name'              => $field_name,
					    'id'                => $field_id,
					    'selected'          => (isset($category->term_id) && $category->term_id != ""?$category->term_id:(isset($val) && $val != ""?$val:"")),
					    'taxonomy'          => (isset($value['taxonomy']) && $value['taxonomy'] != ""?$value['taxonomy']:"category")
					)).
				"</div>";
				break;
			
			// Multicheck category
			case 'multicheck_category':
				$output .= '<div class="vpanel_checklist vpanel_scroll"><ul class="categorychecklist vpanel_categorychecklist">'.
				askme_categories_checklist(array("taxonomy" => (isset($value['taxonomy']) && $value['taxonomy'] != ""?$value['taxonomy']:"category"),"id" => $field_id,"name" => $field_name,"selected_cats" => (isset($val) && is_array($val)?$val:""))).
				'</ul></div>';
				break;

			// Slider
			case 'sliderui':
				$min = $max = $step = $edit = '';
				
				if(!isset($value['min'])){ $min  = '0'; }else{ $min = $value['min']; }
				if(!isset($value['max'])){ $max  = $min + 1; }else{ $max = $value['max']; }
				if(!isset($value['step'])){ $step  = '1'; }else{ $step = $value['step']; }
				
				if (!isset($value['edit'])) { 
					$edit  = ' readonly="readonly"'; 
				}else {
					$edit  = '';
				}
				
				if ($val == '') $val = $min;
				
				//values
				$data = 'data-id="'.$field_id.'" data-val="'.$val.'" data-min="'.$min.'" data-max="'.$max.'" data-step="'.$step.'"';
				
				//html output
				$output .= '<input type="text" name="'.esc_attr( $field_name ).'" id="'.esc_attr( $field_id ).'" value="'.$val.'" class="mini" '.$edit.' />';
				$output .= '<div id="'.$field_id.'-slider" class="v_sliderui" '.$data.'></div>';
				break;
			
			// Badges
			case 'badges':
				$output .= '
				<h4 class="heading">Badge name</h4>
				<input id="badge_name" type="text" name="badge_name" value="">
				
				<div class="clear"></div>
				
				<h4 class="heading">Points</h4>
				<input id="badge_points" type="text" name="badge_points" value="">
				
				<div class="clear"></div>
				
				<h4 class="heading">Color</h4>
				<input id="badge_color" class="of-color badge_color" type="text" name="badge_color" value="">
				
				<div class="clear"></div>
				
				<input id="add_badge" type="button" value="+ Add new badge">
				<div class="clear"></div>
				<ul id="badges_list">';
					$badges = get_option(esc_attr( $field_id ));
					if (isset($badges) && is_array($badges)) {
						foreach ($badges as $badges_k => $badges_v) {
							$output .= '<li>
								<a class="del-builder-item del-badge-item">x</a>
								<div class="widget-head">'.esc_html($badges_v["badge_name"]).'</div>
								<div class="widget-content">
									<h4 class="heading">Badge name</h4>
									<input type="text" name="badges['.esc_html($badges_k).'][badge_name]" value="'.esc_html($badges_v["badge_name"]).'">
									
									<div class="clear"></div>
									
									<h4 class="heading">Points</h4>
									<input type="text" name="badges['.esc_html($badges_k).'][badge_points]" value="'.esc_html($badges_v["badge_points"]).'">
									
									<div class="clear"></div>
									
									<h4 class="heading">Color</h4>
									<input class="of-color badge_color" type="text" name="badges['.esc_html($badges_k).'][badge_color]" value="'.esc_html($badges_v["badge_color"]).'">
									
									<div class="clear"></div>
									
								</div>
							</li>';
						}
					}
				$output .= '</ul>';
				break;
			
			// Element
			case 'elements':
				$output .= '<div class="all_elements">
					<ul class="sort-sections not-sort not-add-item'.(isset($value['hide']) && $value['hide'] == "yes"?" ask_hidden":"").'"'.(isset($value['addto']) && $value['addto'] != ""?" data-to='".$value['addto']."'":"").'>
						<li>';
							if (isset($value["title"]) && $value["title"] != "") {
								$output .= '<a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a>';
							}else {
								$output .= '<div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
							}
							$output .= '<div class="widget-content">';
								foreach ($value['options'] as $key_e => $value_e) {
									$class = 'section';
									$wrap_class = 'wrap_class';
									$options_group = 'options-group';
									if ( isset( $value_e['type'] ) ) {
										$class .= ' section-'.$value_e['type'].' vpanel-form-'.$value_e['type'];
									}
									if ( isset( $value_e['class'] ) ) {
										$class .= ' '.$value_e['class'];
									}

									if ( ! array_key_exists( 'operator', $value_e ) || ! in_array( $value_e['operator'], array( 'and', 'or' ) ) ) {
										$value_e['operator'] = 'and';
									}
					
									if ( ! array_key_exists( 'condition', $value_e ) || ! is_string( $value_e['condition'] ) ) {
										$value_e['condition'] = '';
									}
									
									$condition = empty( $value_e['condition'] ) ? '' : ' data-condition="'. esc_attr( $value_e['condition'] ) .'"';
									$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value_e['operator'] ) .'"';

									if ($value_e["type"] != "heading-2" && $value_e['type'] != "heading-3" && $value_e['type'] != "group") {
										$output .= '<div data-attr="'.$value_e['id'].'" data-type="'.$value_e["type"].'" '.( $condition ).( $operator ).' class="'.esc_attr( $class ).'">'.(isset($value_e["name"]) && $value_e["name"] != ''?'<div class="name-with-desc"><h4 class="heading">'.$value_e["name"].'</h4></div>':'').
										'<div class="all-option">';
									}
										if ($value_e["type"] == "images") {
											$output .= '<div class="image_element">'.
												ask_option_images($field_id,'','',$value_e["options"],$value_e["std"],'',$option_name,'no',$value_e["id"]).
											'</div>';
										}else if ($value_e["type"] == "upload") {
											$output .= "<div class='controls controls-upload'>".askme_options_uploader($value_e["id"],"",null,$value_e["id"],"no")."</div>";
										}else if ($value_e["type"] == "select_category") {
											if (isset($value_e['selected']) && $value_e['selected'] == "s_f_category") {
												$category = current(wp_get_object_terms($post_term,ask_question_category));
												if (!isset($category->name)) $category = '';
											}
											$output .= '<div class="styled-select" data-attr="'.$value_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'">'.
												wp_dropdown_categories(array(
													'show_option_none' => (isset($value_e['option_none']) && $value_e['option_none'] != ""?$value_e['option_none']:0),
												    'orderby'          => 'name',
												    'hide_empty'       => 0,
												    'hierarchical'     => 1,
												    'echo'             => 0,
												    'name'             => "",
												    'id'               => "",
												    'class'            => "check-parent-class".(isset($value_e['class']) && $value_e['class'] != ""?" ".$value_e['class']:""),
												    'selected'         => (isset($category->term_id) && $category->term_id != ""?$category->term_id:""),
												    'taxonomy'         => (isset($value_e['taxonomy']) && $value_e['taxonomy'] != ""?$value_e['taxonomy']:"category")
												)).
											"</div>";
										}else if ($value_e["type"] == "select") {
											$output .= '<div class="styled-select"><select data-attr="'.$value_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="of-input vpanel-form-control" '.(isset($value_e['multiple']) && $value_e['multiple'] != ""?"multiple":"").'>';
											foreach ($value_e['options'] as $key => $option ) {
												$output .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
											}
											$output .= '</select></div>';
										}else if ($value_e["type"] == "radio") {
											foreach ($value_e['options'] as $key => $option ) {
												$output .= '<input '.(isset($value_e['std'])?checked( $value_e['std'], $key, false ):"").' data-attr="'.$value_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="of-input vpanel-form-control of-radio" type="radio" value="'. esc_attr( $key ) . '"><label>' . esc_html( $option ) . '</label>';
											}
										}else if ($value_e["type"] == "textarea") {
											$rows = '8';
											if ( isset( $value['settings']['rows'] ) ) {
												$custom_rows = $value['settings']['rows'];
												if ( is_numeric( $custom_rows ) ) {
													$rows = $custom_rows;
												}
											}
											$output .= '<textarea data-attr="'.$value_e["id"].'" class="of-input vpanel-form-control" rows="'.$rows.'">'.(isset($value_s[$value_e['id']])?stripslashes($value_s[$value_e['id']]):"").'</textarea>';
										}else if ($value_e["type"] == "heading-2" || $value_e['type'] == "heading-3") {
											if ( isset($value_e['end']) && $value_e['end'] == "end" ) {
												if ( isset($value_e['div']) && $value_e['div'] == "div" ) {
													$output .= '</div>';
												}else {
													$output .= '</div></div>';
												}
											}else {
												if ( isset($value_e['div']) && $value_e['div'] == "div" ) {
													$output .= '<div class="'.$wrap_class.'" id="'.(isset($value_e['id']) && $value_e['id'] != ""?"wrap_".$value_e['id']:"").'"'.( $condition ).( $operator ).'>';
												}else {
													$class = '';
													$class = ! empty($value_e['id'])?$value_e['id']:(isset($value_e['name']) && $value_e['name'] != ""?$value_e['name']:"");
													$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
													$output .= '<div'.(isset($value_e['id'])?' id="head-'.$value_e['id'].'"':'').' class="'.$options_group.(isset($value_e['id'])?' head-group head-'.$value_e['id']:'').'"'.( $condition ).( $operator ).'>';
													if ( isset($value_e['name']) ) {
														$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_e['name'] ) . '</h4>';
													}
													$output .= '<div class="vpanel-group-2 ' . $class . '">';
												}
											}
										}else if ($value_e["type"] == "group") {
											if ( isset($value_e['end']) && $value_e['end'] == "end" ) {
												$output .= '</div></div>';
											}else {
												$class = '';
												$class = ! empty($value_e['id'])?$value_e['id']:(isset($value_e['name']) && $value_e['name'] != ""?$value_e['name']:"");
												$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
												$output .= '<div'.(isset($value_e['id'])?' id="head-'.$value_e['id'].'"':'').' class="custom-group '.$options_group.(isset($value_e['id'])?' head-group head-'.$value_e['id']:'').'"'.( $condition ).( $operator ).'>';
												if ( isset($value_e['name']) ) {
													$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_e['name'] ) . '</h4>';
												}
												$output .= '<div class="vpanel-group-2 ' . $class . '">';
											}
										}else if ($value_e["type"] == "checkbox") {
											$output .= '<label class="switch" for="">
												<input data-attr="'.$value_e["id"].'" class="checkbox of-input vpanel-form-control" value="on" type="checkbox" '.checked( (isset($value_e['std'])?$value_e['std']:""), "on", false).'>
												<label for="" data-on="'.esc_attr__("ON","vbegy").'" data-off="'.esc_attr__("OFF","vbegy").'"></label>
											</label>';
										}else {
											if ($value_e["type"] == "slider") {
												$output .= '<div class="section-sliderui">';
											}
											$output .= '<input'.(isset($value['title']) && $value['title'] != ""?" data-title='".$value['title']."'":"").($value_e["type"] == "color"?" class='of-colors'":"").($value_e["type"] == "date"?" class='builder-datepicker'":"").($value_e["type"] == "slider"?" value='".(isset($value_e['value']) && $value_e['value'] != ""?$value_e['value']:"")."' class='mini'":"").' data-attr="'.$value_e["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" data-value="'.(isset($value_e['value']) && $value_e['value'] != ""?$value_e['value']:"").'" type="'.($value_e["type"] == "hidden_id" || $value_e["type"] == "uniq_id"?"hidden":"text").'">';
											if ($value_e["type"] == "slider") {
												$data = 'data-id="slider-id" data-val="'.$value_e['value'].'" data-min="'.$value_e['min'].'" data-max="'.$value_e['max'].'" data-step="'.$value_e['step'].'"';
												$output .= '<div id="slider-id-slider" class="v_slidersui" '. $data .'></div></div>';
											}
										}
										if (isset($value['addto']) && $value['addto'] != "") {
											$output .= '<input data-attr="'.$value_e['id'].'][type" value="'.$value_e["type"].'" type="hidden">';
										}
									if ($value_e["type"] != "heading-2" && $value_e['type'] != "heading-3" && $value_e['type'] != "group") {
										$output .= '</div></div>';
									}
								}
							$output .= '</div>
						</li>
					</ul>
				</div>
				<ul class="sort-sections sort-sections-with sort-sections-ul'.(isset($val) && is_array($val) && !empty($val) && !isset($value['addto'])?'':' sort-sections-empty').'" id="'.(isset($field_id) && $field_id != ""?$field_id:"").'">';
					$i = 0;
					if (isset($val) && is_array($val) && !empty($val) && !isset($value['addto'])) {
						foreach ($val as $value_s) {
							$i++;
							$output .= '<li id="elements_'.$field_id.'_'.$i.'">';
								if (isset($value["title"]) && $value["title"] != "") {
									$output .= '<div class="widget-head"><span>'.esc_attr($value_s["name"]).'</span><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
								}else {
									$output .= '<div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="del-builder-item del-sidebar-item"><span class="dashicons dashicons-trash"></span></a></div>';
								}
								$output .= '<div class="widget-content">';
									foreach ($value['options'] as $key_l => $value_l) {
										$class = 'section';
										$wrap_class = 'wrap_class';
										$options_group = 'options-group';
										if ( isset( $value_l['type'] ) ) {
											$class .= ' section-'.$value_l['type'].' vpanel-form-'.$value_l['type'];
										}
										if ( isset( $value_l['class'] ) ) {
											$class .= ' '.$value_l['class'];
										}
										
										if ( ! array_key_exists( 'operator', $value_l ) || ! in_array( $value_l['operator'], array( 'and', 'or' ) ) ) {
											$value_l['operator'] = 'and';
										}
						
										if ( ! array_key_exists( 'condition', $value_l ) || ! is_string( $value_l['condition'] ) ) {
											$value_l['condition'] = '';
										}
										
										$condition = empty( $value_l['condition'] ) ? '' : ' data-condition="'.  str_ireplace('[%id%]', $option_name."_".$field_id."_".$i."_", esc_attr( $value_l['condition'] ))  .'"';
										$operator = empty( $condition ) ? '' : ' data-operator="'. esc_attr( $value_l['operator'] ) .'"'; 
										if ($value_l["type"] != "heading-2" && $value_l['type'] != "heading-3" && $value_l['type'] != "group") {
											$output .= '<div data-type="'.$value_l["type"].'" data-id="'.$option_name."_".$field_id."_".$i."_".$value_l['id'].'" id="section-'.$option_name."_".$field_id."_".$i."_".$value_l['id'].'"'.( $condition ).( $operator ).' class="'.esc_attr( $class ).'">'.(isset($value_l["name"]) && $value_l["name"] != ''?'<div class="name-with-desc"><h4 class="heading">'.$value_l["name"].'</h4></div>':'').
										'<div class="all-option">';
										}
											if ($value_l["type"] == "images") {
												$output .= '<div class="image_element">'.
													ask_option_images($field_name.'_'.$i.'_'.$value_l['id'],'','',$value_l["options"],$value_s[$value_l['id']],'',$field_name.'['.$i.']['.$value_l['id'].']','',$value_l["id"],'no').
												'</div>';
											}else if ($value_l["type"] == "upload") {
												$output .= "<div class='controls controls-upload'>".askme_options_uploader($field_id.'_'.$i.'_'.$value_l['id'],(isset($value_s[$value_l['id']])?$value_s[$value_l['id']]:""),null,$field_name.'['.$i.']['.$value_l['id'].']')."</div>";
											}else if ($value_l["type"] == "select_category") {
												if (isset($value_l['selected']) && $value_l['selected'] == "s_f_category") {
													$category = current(wp_get_object_terms($post_term,ask_question_category));
													if (!isset($category->name)) $category = '';
												}
												$output .= '<div class="styled-select" data-attr="'.$value_l["id"].'">'.
													wp_dropdown_categories(array(
														'show_option_none' => (isset($value_l['option_none']) && $value_l['option_none'] != ""?$value_l['option_none']:0),
													    'orderby'          => 'name',
													    'hide_empty'       => 0,
													    'hierarchical'     => 1,
													    'echo'             => 0,
													    'class'            => "check-parent-class".(isset($value_l['class']) && $value_l['class'] != ""?" ".$value_l['class']:"")."",
													    'name'             => $field_name.'['.$i.']['.$value_l['id'].']',
													    'id'               => $option_name."_".$field_id.'_'.$i.'_'.$value_l['id'],
													    'selected'         => (isset($category->term_id) && $category->term_id != ""?$category->term_id:(isset($value_s[$value_l['id']]) && $value_s[$value_l['id']] != ""?$value_s[$value_l['id']]:"")),
													    'taxonomy'         => (isset($value_l['taxonomy']) && $value_l['taxonomy'] != ""?$value_l['taxonomy']:"category")
													)).
												"</div>";
											}else if ($value_l["type"] == "select") {
												$output .= '<div class="styled-select"><select data-attr="'.$value_e["id"].'" class="of-input vpanel-form-control" '.(isset($value_l['multiple']) && $value_l['multiple'] != ""?"multiple":"").' name="'.$field_name.'['.$i.']['.$value_l['id'].']'.(isset($value_l['multiple']) && $value_l['multiple'] != ""?"[]":"") . '" id="' . esc_attr( $value_l['id'] ) . '">';
												foreach ($value_l['options'] as $key => $option ) {
													$output .= '<option'. (isset($value_l['multiple']) && $value_l['multiple'] != ""?(isset($value_s[$value_l['id']]) && is_array($value_s[$value_l['id']]) && in_array($key,$value_s[$value_l['id']])?' selected="selected"':""):selected( $value_s[$value_l['id']], $key, false )) .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
												}
												$output .= '</select></div>';
											}else if ($value_l["type"] == "radio") {
												foreach ($value_l['options'] as $key => $option ) {
													$output .= '<input name="'.$field_name.'['.$i.']['.$value_l['id'].']" id="'.$option_name."_".$field_id.'_'.$i.'_'.$value_l['id'].'_'.$key.'" data-attr="'.$value_l["id"].(isset($value['addto']) && $value['addto'] != ""?"][value":"").'" class="of-input vpanel-form-control of-radio" type="radio" value="'. esc_attr( $key ) . '" '.(isset($value_s[$value_l['id']])?checked( $value_s[$value_l['id']], $key, false ):"").'><label for="'.$option_name."_".$field_id.'_'.$i.'_'.$value_l['id'].'_'.$key.'">' . esc_html( $option ) . '</label>';
												}
											}else if ($value_l["type"] == "textarea") {
												$rows = '8';
												if ( isset( $value['settings']['rows'] ) ) {
													$custom_rows = $value['settings']['rows'];
													if ( is_numeric( $custom_rows ) ) {
														$rows = $custom_rows;
													}
												}
												$output .= '<textarea data-attr="'.$value_e["id"].'" class="of-input vpanel-form-control" rows="'.$rows.'" name="'.$field_name.'['.$i.']['.$value_l['id'].']" id="' . esc_attr( $value_l['id'] ) . '">'.(isset($value_s[$value_l['id']])?$value_s[$value_l['id']]:"").'</textarea>';
											}else if ($value_l["type"] == "slider") {
												$output .= '<div class="section-sliderui">'.
												ask_option_sliderui($value_l["min"],$value_l["max"],$value_l["step"],'',$value_s[$value_l['id']],$field_id.'['.$i.']['.$value_l['id'],esc_attr($option_name),$field_name.'_'.$i.'_'.$value_l['id'],'','').
												'</div>';
											}else if ($value_l["type"] == "heading-2" || $value_l['type'] == "heading-3") {
												if ( isset($value_l['end']) && $value_l['end'] == "end" ) {
													if ( isset($value_l['div']) && $value_l['div'] == "div" ) {
														$output .= '</div>';
													}else {
														$output .= '</div></div>';
													}
												}else {
													if ( isset($value_l['div']) && $value_l['div'] == "div" ) {
														$output .= '<div class="'.$wrap_class.'" id="'.(isset($value_l['id']) && $value_l['id'] != ""?"wrap_".$value_l['id']:"").'"'.( $condition ).( $operator ).'>';
													}else {
														$class = '';
														$class = ! empty($value_l['id'])?$value_l['id']:(isset($value_l['name']) && $value_l['name'] != ""?$value_l['name']:"");
														$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
														$output .= '<div'.(isset($value_l['id'])?' id="head-'.$value_l['id'].'"':'').' class="'.$options_group.(isset($value_l['id'])?' head-group head-'.$value_l['id']:'').'"'.( $condition ).( $operator ).'>';
														if ( isset($value_l['name']) ) {
															$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_l['name'] ) . '</h4>';
														}
														$output .= '<div class="vpanel-group-2 ' . $class . '">';
													}
												}
											}else if ($value_l["type"] == "group") {
												if ( isset($value_l['end']) && $value_l['end'] == "end" ) {
													$output .= '</div></div>';
												}else {
													$class = '';
													$class = ! empty($value_l['id'])?$value_l['id']:(isset($value_l['name']) && $value_l['name'] != ""?$value_l['name']:"");
													$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
													$output .= '<div'.(isset($value_l['id'])?' id="head-'.$value_l['id'].'"':'').' class="custom-group '.$options_group.(isset($value_l['id'])?' head-group head-'.$value_l['id']:'').'"'.( $condition ).( $operator ).'>';
													if ( isset($value_l['name']) ) {
														$output .= '<h4 class="vpanel-head-2">' . esc_html( $value_l['name'] ) . '</h4>';
													}
													$output .= '<div class="vpanel-group-2 ' . $class . '">';
												}
											}else if ($value_l["type"] == "checkbox") {
												$output .= '<label class="switch" for="'.$field_id.'_'.$i.'_'.$value_l['id'].'">
													<input id="'.$field_id.'_'.$i.'_'.$value_l['id'].'" class="checkbox of-input vpanel-form-control" value="on" type="checkbox" name="'.$field_name.'['.$i.']['.$value_l['id'].']" '.checked( (isset($value_s[$value_l['id']])?$value_s[$value_l['id']]:""), "on", false).'>
													<label for="'.$field_id.'_'.$i.'_'.$value_l['id'].'" data-on="'.esc_attr__("ON","vbegy").'" data-off="'.esc_attr__("OFF","vbegy").'"></label>
												</label>';
											}else {
												$output .= '<input'.($value_l["type"] == "color"?" class='of-color'":"").($value_l["type"] == "date"?" class='of-datepicker'":"").' name="'.$field_name.'['.$i.']['.$value_l['id'].']" type="'.($value_l["type"] == "hidden_id" || $value_l["type"] == "uniq_id"?"hidden":"text").'" value="'.(isset($value_s[$value_l['id']])?stripslashes(htmlspecialchars($value_s[$value_l['id']])):"").'">';
											}
										if ($value_l["type"] != "heading-2" && $value_l['type'] != "heading-3" && $value_l['type'] != "group") {
											$output .= '</div></div>';
										}
									}
								$output .= '</div>
							</li>';
						}
					}
				$output .= '</ul>
				<input class="add_element'.($page == "post" || $page == "term" || $page == "user"?" no_theme_options":"").(isset($value['addto']) && $value['addto'] != ""?" add_element_to":"").'" type="button" value="'.(isset($value['button']) && $value['button'] != ""?$value['button']:esc_html__("+ Add a new element","vbegy")).'"'.(isset($field_id) && $field_id != ""?" data-id='".$field_id."'":"").(isset($value['title']) && $value['title'] != ""?" data-title='".$value['title']."'":"").'>
				<div class="clear"></div>
				<span data-js="'.esc_js($i+1).'" class="'.$field_id.'_j"></span>';
				break;
			
			// Coupons
			case 'coupons':
				$output .= '
				<h4 class="heading">Coupons name</h4>
				<input id="coupon_name" name="coupon_name" type="text" value="">
				
				<div class="clear"></div>
				
				<h4 class="heading">Discount type</h4>
				<div class="styled-select">
					<select id="coupon_type" name="coupon_type">
						<option value="discount">Discount</option>
						<option value="percent">% Percent</option>
					</select>
				</div>
				
				<div class="clear"></div>
				
				<h4 class="heading">Amount</h4>
				<input id="coupon_amount" name="coupon_amount" class="coupon_amount" type="text" value="">
				
				<div class="clear"></div>
				
				<h4 class="heading">Expiry date</h4>
				<input id="coupon_date" name="coupon_date" class="of-datepicker coupon_date" type="text" value="">
				
				<div class="clear"></div>
				
				<input id="add_coupon" type="button" value="+ Add new coupon">
				<div class="clear"></div>
				<ul id="coupons_list">';
					$coupons = get_option(esc_attr($field_id));
					if (isset($coupons) && is_array($coupons) && !empty($coupons)) {
						foreach ($coupons as $coupons_k => $coupons_v) {
							$output .= '<li>
								<a class="del-builder-item del-coupon-item">x</a>
								<div class="widget-content">
									<h4 class="heading">Coupon name</h4>
									<input type="text" class="coupon_name" name="coupons['.esc_html($coupons_k).'][coupon_name]" value="'.(isset($coupons_v["coupon_name"])?esc_html($coupons_v["coupon_name"]):"").'">
									
									<div class="clear"></div>
									
									<h4 class="heading">Discount type</h4>
									<div class="styled-select">
										<select class="coupon_type" name="coupons['.esc_html($coupons_k).'][coupon_type]">
											<option value="discount"'.(isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "discount"?" selected='selected'":"").'>Discount</option>
											<option value="percent"'.(isset($coupons_v["coupon_type"]) && $coupons_v["coupon_type"] == "percent"?" selected='selected'":"").'>% Percent</option>
										</select>
									</div>
									
									<div class="clear"></div>
									
									<h4 class="heading">Amount</h4>
									<input class="coupon_amount" type="text" name="coupons['.esc_html($coupons_k).'][coupon_amount]" value="'.(isset($coupons_v["coupon_amount"])?esc_html($coupons_v["coupon_amount"]):"").'">
									
									<div class="clear"></div>
									
									<h4 class="heading">Expiry date</h4>
									<input class="of-datepicker coupon_date" type="text" name="coupons['.esc_html($coupons_k).'][coupon_date]" value="'.(isset($coupons_v["coupon_date"])?esc_html($coupons_v["coupon_date"]):"").'">
									
									<div class="clear"></div>
									
								</div>
							</li>';
						}
					}
				$output .= '</ul>';
				break;
			
			// Sidebar Box
			case 'sidebar':
				$output .= '
				<input id="sidebar_name" type="text" name="sidebar_name" value="">
				<input id="sidebar_add" type="button" value="+ Add new sidebar">
				<div class="clear"></div>
				<ul id="sidebars_list">';
					$sidebars = get_option(esc_attr($field_id));
					if($sidebars) {
						foreach ($sidebars as $sidebar) {
							$output .= '<li><div class="widget-head">'.esc_html($sidebar).'<input name="sidebars[]" type="hidden" value="'.esc_html($sidebar).'"><a class="del-builder-item del-sidebar-item">x</a></div></li>';
						}
					}
				$output .= '</ul>';
				break;
			
			// Select Box
			case 'select':
				$output .= '<div class="styled-select"><select class="of-input vpanel-form-control" name="'.esc_attr( $field_name ).'" id="'.esc_attr( $field_id ).'">';
				if (isset($value['options']) && is_array($value['options']) && !empty($value['options'])) {
					foreach ($value['options'] as $key => $option ) {
						$output .= '<option'.selected( $val, $key, false ).' value="'.esc_attr( $key ).'">'.esc_html( $option ).'</option>';
					}
				}
				$output .= '</select></div>';
				break;

			// Radio Box
			case "radio":
				foreach ($value['options'] as $key => $option) {
					$id = $field_id.'-'.$key;
					$output .= '<input class="of-input vpanel-form-control of-radio'.(isset($value['class'])?" ".esc_attr($value['class']):'').'" type="radio" name="'.esc_attr( $field_name ).'" id="'.esc_attr( $id ).'" value="'.esc_attr( $key ).'" '.checked( $val, $key, false).'><label for="'.esc_attr( $id ).'">'.esc_html( $option ).'</label>';
				}
				break;

			// Image Selectors
			case "images":
				foreach ( $value['options'] as $key => $option ) {
					$selected = '';
					if ( $val != '' && ($val == $key) ) {
						$selected = ' of-radio-img-selected';
					}
					$output .= '<input type="radio" id="'.esc_attr( $field_id.'_'.$key).'" class="of-radio-img-radio vpanel-form-control" value="'.esc_attr( $key ).'" name="'.esc_attr( $field_name ).'" '.checked( $val, $key, false ).'>';
					$output .= '<div class="of-radio-img-label">'.esc_html( $key ).'</div>';
					$output .= '<img src="'.esc_url( $option ).'" alt="'.$option.'" class="of-radio-img-img vpanel-radio-img-img'.(isset($value['class'])?" ".esc_attr($value['class']):'').''.$selected.'" onclick="document.getElementById(\''.esc_attr($field_id.'_'.$key).'\').checked=true;">';
				}
				break;

			// Checkbox
			case "checkbox":
				$output .= '<label class="switch" for="' . esc_attr( $field_id ) . '">
					<input id="'.esc_attr( $field_id ).'" class="checkbox of-input vpanel-form-control" type="checkbox" name="'.esc_attr( $field_name ).'" '.checked( $val, 1, false).'>
					<label for="' . esc_attr( $field_id ) . '" data-on="'.esc_html__("ON","vbegy").'" data-off="'.esc_html__("OFF","vbegy").'"></label>
				</label>';
				break;
			
			// Multicheck
			case "multicheck":
				if (isset($value['options']) && is_array($value['options']) && !empty($value['options'])) {
					foreach ($value['options'] as $key => $option) {
						$checked = '';
						$label = $option;
						$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));

						$id = $field_id.'-'.$option;
						$name = $field_name.'['.$option.']';

						if ( isset($val[$option]) ) {
							$checked = ($val[$option] == 1 || $val[$option] == "on"?" checked='checked'":false);
						}

						$output .= '<label class="switch" for="' . esc_attr( $id ) . '">
							<input data-type="multicheck" data-value="'.$option.'" id="'.esc_attr( $id ).'" class="checkbox of-input vpanel-form-control vpanel_multicheck" type="checkbox" name="'.esc_attr( $name ).'" '.$checked.'>
							<label for="' . esc_attr( $id ) . '" data-on="'.esc_html__("ON","vbegy").'" data-off="'.esc_html__("OFF","vbegy").'"></label>
						</label>
						<label for="'.esc_attr( $id ).'">'.esc_html( $label ).'</label>';
					}
				}
				break;
			
			// Multicheck
			case 'multicheck_2':
				$value_option = array();
				$output .= '<ul id="'.(isset($field_id) && $field_id != ""?$field_id:"").'-ul"'.(isset($value['sort']) && $value['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
				if (isset($value['sort']) && $value['sort'] == "yes") {
					$k_sort = 0;
					if (isset($val) && !empty($val) && is_array($val)) {
						$value_option = $val;
					}else {
						$value_option = $value['options'];
					}
				}else {
					$value_option = $value['options'];
				}
				
				if ($value['options'] != $val) {
					if (isset($val) && is_array($val)) {
						foreach ($val as $key_s => $key_s) {
							if (!isset($value['options'][$key_s]) && !isset($val[$key_s]["cat"])) {
								unset($value_option[$key_s]);
							}
						}
					}
					if (isset($value['options']) && is_array($value['options'])) {
						foreach ($value['options'] as $key_s => $value_s) {
							if (!isset($val[$key_s])) {
								$value_option = array_merge($value_option,array($key_s => $value_s));
							}
						}
					}
				}
				
				foreach ($value_option as $key => $option) {
					$checked = '';
					if (isset($value['sort']) && $value['sort'] == "yes") {
						$k_sort++;
						$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));
						if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
							$label = ($val[$option]["value"] > 0?get_term($val[$option]["value"],ask_question_category)->name:esc_html__("Show All Categories","vbegy"));
						}else {
							$label = (isset($value['options'][$option]["sort"])?$value['options'][$option]["sort"]:"");
						}
					}else {
						$label = $option;
						$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($key));
					}
					
					
					$id = $field_id . '-'. $option;
					$name = $field_name.'[' . $option .']';
					
					if ( isset($val[$option]) ) {
						if (isset($value['sort']) && $value['sort'] == "yes") {
							if (isset($val[$option]["value"])) {
								$checked = checked($val[$option]["value"], $option, false);
							}
						}else {
							if (isset($val[$option])) {
								$checked = checked($val[$option], $option, false);
							}
						}
					}
					$output .= '<li'.(isset($value['sort']) && $value['sort'] == "yes" && isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes"?" class='categories' id='".$field_id."_categories_".$val[$key]["value"]."'":'').'>';
						if (isset($value['sort']) && $value['sort'] == "yes") {
							$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.(isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes"?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
							if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
								$output .= '<input name="'.$field_name.'[cat-'.$val[$key]["value"].'][cat]" value="yes" type="hidden"><input name="'.$field_name.'[cat-'.$val[$key]["value"].'][value]" value="'.$val[$key]["value"].'" type="hidden">';
							}else {
								$output .= '<input type="hidden" name="'.esc_attr( $name.'[sort]' ).'" value="'.esc_html( $label ).'">';
							}
						}
						
						if (isset($val[$key]) && is_array($val[$key]) && array_key_exists('cat',$val[$key])) {
							// Cat
						}else {
							$output .= '<label class="switch" for="'.esc_attr($id).'">
								<input value="0" type="hidden" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'">
								<input id="'.esc_attr($id).'" value="'.$option.'" class="checkbox vpanel-input vpanel-form-control" type="checkbox" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'" '. $checked .'>
								<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","vbegy").'" data-off="'.esc_attr__("OFF","vbegy").'"></label>
							</label>';
						}
						$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
						if (isset($value['sort']) && $value['sort'] == "yes") {
							$output .= '</div>';
						}
					$output .= '</li>';
				}
				$output .= '</ul>';
				break;
			
			// Multicheck 3
			case 'multicheck_3':
				$value_option = array();
				$output .= '<ul id="'.(isset($value['id']) && $value['id'] != ""?$value['id']:"").'-ul"'.(isset($value['sort']) && $value['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
				if (isset($value['sort']) && $value['sort'] == "yes") {
					$k_sort = 0;
					if (isset($val) && !empty($val) && is_array($val)) {
						$value_option = $val;
					}else {
						$value_option = $value['options'];
					}
				}else {
					$value_option = $value['options'];
				}
				
				if ($value['options'] != $val) {
					if (isset($val) && is_array($val)) {
						foreach ($val as $key_s => $key_s) {
							if (!isset($value['options'][$key_s]) && !isset($val[$key_s]["cat"]) && !isset($val[$key_s]["page"]) && !isset($val[$key_s]["builder"])) {
								unset($value_option[$key_s]);
							}
						}
					}
					if (isset($value['options']) && is_array($value['options'])) {
						foreach ($value['options'] as $key_s => $value_s) {
							if (!isset($val[$key_s])) {
								$value_option = array_merge($value_option,array($key_s => $value_s));
							}
						}
					}
				}
				
				foreach ($value_option as $key => $option) {
					$o_option = $option;
					$output = apply_filters("askme_show_multicheck_3_field",$output,$value_option,$key,$o_option,$val,$option_name,$field_name,$field_id);
					if (!isset($o_option["builder"])) {
						$checked = '';
						if (isset($value['values']) && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))) {
							$label = $value['values'][$option["value"]];
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
						}else if (isset($value['sort']) && $value['sort'] == "yes") {
							$k_sort++;
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
							if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
								if ($val[$key]["cat"] != "yes" && ($val[$option]["value"] == 0 || $val[$option]["value"] === 0)) {
									$val[$option]["value"] = "q-0";
								}
								if (is_numeric($val[$option]["value"])) {
									$label = get_term($val[$option]["value"]);
									$label = (isset($label->name)?$label->name:"");
								}else if ($val[$option]["value"] === "q-0") {
									$label = esc_html__("All Question Categories","vbegy");
								}else {
									$label = esc_html__("All Categories","vbegy");
								}
							}else if (isset($val[$key]["page"]) && $val[$key]["page"] == "yes") {
								if (is_numeric($val[$option]["value"])) {
									$label = get_the_title($val[$option]["value"]);
								}
							}else {
								$label = (isset($value['options'][$option]["sort"])?$value['options'][$option]["sort"]:"");
							}
						}else {
							$label = $o_option;
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
						}
						
						$id = $option_name . '-' . $field_id . '-'. $option;
						$name = $field_name.'[' . $option .']';
						
						if ( isset($val[$option]) ) {
							if (isset($value['sort']) && $value['sort'] == "yes") {
								if (isset($val[$option]["value"])) {
									$checked = checked($val[$option]["value"], $option, false);
								}
							}else {
								if (isset($val[$option])) {
									$checked = checked($val[$option], $option, false);
								}
							}
						}
						$output .= '<li'.(isset($value['sort']) && $value['sort'] == "yes" && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))?" class='additions-li' id='".$value['id']."_additions_li_".$val[$key]["value"]."'":'').'>';
							if (isset($value['sort']) && $value['sort'] == "yes") {
								$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
								if ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")) {
									if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
										$item_type = 'cat';
									}else {
										$item_type = 'page';
									}
									$name_sort = (isset($value['id']) && $value['id'] != ""?esc_attr(($page == 'widgets'?$post_term->get_field_name($value['id']):($page == 'post' || $page == 'term' || $page == 'user'?$value['id']:$option_name.'['.$value['id'].']'))):"");
									$output .= '<input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].']['.$item_type.']" value="yes" type="hidden"><input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].'][value]" value="'.$val[$key]["value"].'" type="hidden">';
								}else {
									$output .= '<input type="hidden" name="'.esc_attr( $name.'[sort]' ).'" value="'.esc_html( $label ).'">';
								}
							}
							if (isset($o_option["default"]) || (isset($val[$key]) && is_array($val[$key]) && (array_key_exists('cat',$val[$key]) || array_key_exists('page',$val[$key])))) {
								if (isset($o_option["default"])) {
									$output .= '<input value="'.$option.'" type="hidden" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'">
									<input value="yes" type="hidden" name="'.esc_attr( $name.'[default]' ).'">';
								}
							}else {
								$output .= '<label class="switch" for="'.esc_attr($id).'">
									<input value="0" type="hidden" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'">
									<input id="'.esc_attr($id).'" value="'.$option.'" class="checkbox of-input vpanel-form-control" type="checkbox" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'" '. $checked .'>
									<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","vbegy").'" data-off="'.esc_attr__("OFF","vbegy").'"></label>
								</label>';
							}
							$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
							if (isset($value['sort']) && $value['sort'] == "yes") {
								$output .= '</div>';
							}
						$output .= '</li>';
					}
				}
				$output .= '</ul>';
				break;

			// Multicheck sort
			case "multicheck_sort":
				$value_option = array();
				$output .= '<ul id="'.(isset($value['id']) && $value['id'] != ""?$value['id']:"").'-ul"'.(isset($value['sort']) && $value['sort'] == "yes"?' class="sort-sections sort-sections-ul"':'').'>';
				if (isset($value['sort']) && $value['sort'] == "yes") {
					$k_sort = 0;
					if (isset($val) && !empty($val) && is_array($val)) {
						$value_option = $val;
					}else {
						$value_option = $value['options'];
					}
				}else {
					$value_option = $value['options'];
				}
				
				if ($value['options'] != $val) {
					if (isset($val) && is_array($val)) {
						foreach ($val as $key_s => $key_s) {
							if (!isset($value['options'][$key_s]) && !isset($val[$key_s]["cat"]) && !isset($val[$key_s]["page"]) && !isset($val[$key_s]["builder"])) {
								unset($value_option[$key_s]);
							}
						}
					}
					if (isset($value['options']) && is_array($value['options'])) {
						foreach ($value['options'] as $key_s => $value_s) {
							if (!isset($val[$key_s])) {
								$value_option = array_merge($value_option,array($key_s => $value_s));
							}
						}
					}
				}
				
				foreach ($value_option as $key => $option) {
					$o_option = $option;
					$output = apply_filters("vpanel_show_multicheck_field",$output,$value_option,$key,$o_option,$val,$option_name,$field_name,$field_id);
					if (!isset($o_option["builder"])) {
						$checked = '';
						if (isset($value['values']) && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))) {
							$label = $value['values'][$option["value"]];
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
						}else if (isset($value['sort']) && $value['sort'] == "yes") {
							$k_sort++;
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
							if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
								if ($val[$key]["cat"] != "yes" && ($val[$option]["value"] == 0 || $val[$option]["value"] === 0)) {
									$val[$option]["value"] = "q-0";
								}
								if (is_numeric($val[$option]["value"])) {
									$label = get_term($val[$option]["value"]);
									$label = (isset($label->name)?$label->name:"");
								}else if ($val[$option]["value"] === "q-0") {
									$label = esc_html__("All Question Categories","vbegy");
								}else {
									$label = esc_html__("All Categories","vbegy");
								}
							}else if (isset($val[$key]["page"]) && $val[$key]["page"] == "yes") {
								if (is_numeric($val[$option]["value"])) {
									$label = get_the_title($val[$option]["value"]);
								}
							}else {
								$label = (isset($value['options'][$option]["sort"])?$value['options'][$option]["sort"]:"");
							}
						}else {
							$label = $o_option;
							$option = preg_replace('/[^a-zA-Z0-9._\-]/', '', (isset($value["strtolower"]) && $value["strtolower"] == "not"?$key:strtolower($key)));
						}
						
						$id = $option_name . '-' . $field_id . '-'. $option;
						$name = $field_name.'[' . $option .']';
						
						if ( isset($val[$option]) ) {
							if (isset($value['sort']) && $value['sort'] == "yes") {
								if (isset($val[$option]["value"])) {
									$checked = checked($val[$option]["value"], $option, false);
								}
							}else {
								if (isset($val[$option])) {
									$checked = checked($val[$option], $option, false);
								}
							}
						}
						$output .= '<li'.(isset($value['sort']) && $value['sort'] == "yes" && ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes"))?" class='additions-li' id='".$value['id']."_additions_li_".$val[$key]["value"]."'":'').'>';
							if (isset($value['sort']) && $value['sort'] == "yes") {
								$output .= '<div class="widget-head"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a>'.((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")?'<a class="del-cat-item del-builder-item"><span class="dashicons dashicons-trash"></span></a>':'').'</div>';
								if ((isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") || (isset($val[$key]["page"]) && $val[$key]["page"] == "yes")) {
									if (isset($val[$key]["cat"]) && $val[$key]["cat"] == "yes") {
										$item_type = 'cat';
									}else {
										$item_type = 'page';
									}
									$name_sort = (isset($value['id']) && $value['id'] != ""?esc_html(($page == 'widgets'?$post_term->get_field_name($value['id']):($page == 'post' || $page == 'term' || $page == 'user'?$value['id']:$option_name.'['.$value['id'].']'))):"");
									$output .= '<input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].']['.$item_type.']" value="yes" type="hidden"><input name="'.$name_sort.'['.$item_type.'-'.$val[$key]["value"].'][value]" value="'.$val[$key]["value"].'" type="hidden">';
								}else {
									$output .= '<input type="hidden" name="'.esc_attr( $name.'[sort]' ).'" value="'.esc_html( $label ).'">';
								}
							}
							if (isset($o_option["default"]) || (isset($val[$key]) && is_array($val[$key]) && (array_key_exists('cat',$val[$key]) || array_key_exists('page',$val[$key])))) {
								if (isset($o_option["default"])) {
									$output .= '<input value="'.$option.'" type="hidden" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'">
									<input value="yes" type="hidden" name="'.esc_attr( $name.'[default]' ).'">';
								}
							}else {
								$output .= '<label class="switch" for="'.esc_attr($id).'">
									<input value="0" type="hidden" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'">
									<input id="'.esc_attr($id).'" value="'.$option.'" class="checkbox vpanel-input vpanel-form-control" type="checkbox" name="'.esc_attr( $name.(isset($value['sort']) && $value['sort'] == "yes"?'[value]':'') ).'" '. $checked .'>
									<label for="'.esc_attr($id).'" data-on="'.esc_attr__("ON","vbegy").'" data-off="'.esc_attr__("OFF","vbegy").'"></label>
								</label>';
							}
							$output .= '<label for="'.esc_attr($id).'">' . esc_html( $label ) . '</label>';
							if (isset($value['sort']) && $value['sort'] == "yes") {
								$output .= '</div>';
							}
						$output .= '</li>';
					}
				}
				$output .= '</ul>';
				break;

			// Color picker
			case 'color':
				$default_color = '';
				if ( isset($value['std']) ) {
					if ( $val !=  $value['std'] )
						$default_color = ' data-default-color="' .$value['std'] . '" ';
				}
				$output .= '<input name="'.$field_name.'" id="'.$field_id.'" class="of-color '.(isset($value['class'])?esc_attr($value['class']):'').'"  type="text" value="' . esc_attr( $val ) . '"' . $default_color .'>';
				break;

			// Uploader
			case "upload":
				$output .= askme_media_uploader::askme_uploader($field_id,$val,null,$field_name,null,(isset($value['options'])?$value['options']:array()));
				break;

			// Typography
			case 'typography':
				unset( $font_size, $font_style, $font_face, $font_color );
				$font_size = $font_face = $font_style = $font_color = '';

				$typography_defaults = array(
					'size' => '',
					'face' => '',
					'style' => '',
					'color' => ''
				);

				$typography_stored = wp_parse_args( $val, $typography_defaults );

				$typography_options = array(
					'sizes' => of_recognized_font_sizes(),
					'faces' => of_recognized_font_faces(),
					'styles' => of_recognized_font_styles(),
					'color' => true
				);

				$font_size = $font_face = $font_style = $font_color = '';

				if ( isset( $value['options'] ) ) {
					$typography_options = wp_parse_args( $value['options'], $typography_options );
				}

				// Font Size
				if ( $typography_options['sizes'] ) {
					$font_size = '<select class="of-typography of-typography-size" name="' . esc_attr( $field_name.'[size]' ) . '" id="' . esc_attr( $field_id . '_size' ) . '">';
					$sizes = $typography_options['sizes'];
					$font_size .= '<option value="" ' . selected( "default", "default", false ) . '>'.esc_html__("Size","vbegy").'</option>';
					foreach ( $sizes as $i ) {
						$size = $i . 'px';
						$font_size .= '<option value="' . esc_attr( $size ) . '" ' . (isset($typography_stored['size']) && is_string($typography_stored['size'])?selected( $typography_stored['size'], $size, false ):"") . '>' . esc_html( $size ) . '</option>';
					}
					$font_size .= '</select>';
				}

				// Font Face
				if ( $typography_options['faces'] ) {
					$font_face = '<input class="of-typography of-typography-face" name="' . esc_attr( $field_name.'[face]' ) . '" id="' . esc_attr( $field_id . '_face' ) . '" value="'.$typography_stored['face'].'">';
				}

				// Font Styles
				if ( $typography_options['styles'] ) {
					$font_style = '<select class="of-typography of-typography-style" name="'.$field_name.'[style]" id="'. $field_id.'_style">';
					$styles = $typography_options['styles'];
					foreach ( $styles as $key => $style ) {
						$font_style .= '<option value="' . esc_attr( $key ) . '" ' . selected( $typography_stored['style'], $key, false ) . '>'. $style .'</option>';
					}
					$font_style .= '</select>';
				}

				// Font Color
				if ( $typography_options['color'] ) {
					$default_color = '';
					if ( isset($value['std']['color']) ) {
						if ( $val !=  $value['std']['color'] )
							$default_color = ' data-default-color="' .$value['std']['color'] . '" ';
					}
					$font_color = '<input name="' . esc_attr( $field_name.'[color]' ) . '" id="' . esc_attr( $field_id . '_color' ) . '" class="of-color of-typography-color"  type="text" value="' . esc_attr( $typography_stored['color'] ) . '"' . $default_color .'>';
				}

				// Allow modification/injection of typography fields
				$typography_fields = compact( 'font_size', 'font_face', 'font_style', 'font_color' );
				$typography_fields = apply_filters( 'of_typography_fields', $typography_fields, $typography_stored, $option_name, $value );
				$output .= implode( '', $typography_fields );

				break;

			// Background
			case 'background':

				$background = $val;

				// Background Color
				$default_color = '';
				if ( isset( $value['std']['color'] ) ) {
					if ( $val !=  $value['std']['color'] )
						$default_color = ' data-default-color="'.$value['std']['color'].'" ';
				}
				$output .= '<input name="'.esc_attr( $field_name.'[color]' ).'" id="'.esc_attr( $field_id.'_color' ).'" class="of-color of-background-color"  type="text" value="'.(isset($background['color'])?esc_attr($background['color']):"").'"'.$default_color.'>';

				// Background Image
				$background_image = (isset($background['image']) && $background['image'] != ""?$background['image']:"");
				$output .= askme_media_uploader::askme_uploader($field_id,$background_image,null,esc_attr($field_name.'[image]'));

				$class = 'of-background-properties '.(isset($value['class'])?esc_attr($value['class']):'').'';
				if ( '' == $background_image ) {
					$class .= ' hide';
				}
				$output .= '<div class="'.esc_attr( $class ).'">';

				// Background Repeat
				$output .= '<select class="of-background of-background-repeat" name="'.esc_attr( $field_name.'[repeat]'  ).'" id="'.esc_attr( $field_id.'_repeat' ).'">';
				$repeats = of_recognized_background_repeat();

				foreach ($repeats as $key => $repeat) {
					$output .= '<option value="'.esc_attr( $key ).'" '.selected((isset($background['repeat'])?esc_attr($background['repeat']):""), $key, false ).'>'.esc_html( $repeat ).'</option>';
				}
				$output .= '</select>';

				// Background Position
				$output .= '<select class="of-background of-background-position" name="'.esc_attr( $field_name.'[position]' ).'" id="'.esc_attr( $field_id.'_position' ).'">';
				$positions = of_recognized_background_position();

				foreach ($positions as $key=>$position) {
					$output .= '<option value="'.esc_attr( $key ).'" '.selected( (isset($background['position'])?esc_attr($background['position']):""), $key, false ).'>'.esc_html( $position ).'</option>';
				}
				$output .= '</select>';

				// Background Attachment
				$output .= '<select class="of-background of-background-attachment" name="'.esc_attr( $field_name.'[attachment]' ).'" id="'.esc_attr( $field_id.'_attachment' ).'">';
				$attachments = of_recognized_background_attachment();

				foreach ($attachments as $key => $attachment) {
					$output .= '<option value="'.esc_attr( $key ).'" '.selected( (isset($background['attachment'])?esc_attr($background['attachment']):""), $key, false ).'>'.esc_html( $attachment ).'</option>';
				}
				$output .= '</select>';
				$output .= '</div>';

				break;

			// export
			case 'export':
				$rows = '8';
				if ( isset( $value['settings']['rows'] ) ) {
					$custom_rows = $value['settings']['rows'];
					if ( is_numeric( $custom_rows ) ) {
						$rows = $custom_rows;
					}
				}
				$output .= '<textarea id="'.esc_attr( $field_id ).'" class="of-input vpanel-form-control builder_select" rows="'.$rows.'">'.esc_textarea($value['export']).'</textarea>';
				break;
			
			// import
			case 'import':
				$rows = '8';
				$output .= '<textarea id="'.esc_attr($field_id).'" name="'.esc_attr($field_name).'" class="of-input vpanel-form-control" rows="'.$rows.'"></textarea>';
				break;
				
			// Editor
			case 'editor':
				$rich_editing = get_user_meta(get_current_user_id(), 'rich_editing', true);
				if ($rich_editing == true) {
					$output .= '<div class="vpanel_editor"></div>';
				}
				echo ($output);
				$default_editor_settings = array(
					'textarea_name' => $field_name,
					'media_buttons' => "vpanel_editor",
					'tinymce' => array( 'plugins' => 'wordpress' )
				);
				$editor_settings = array();
				if ( isset( $value['settings'] ) ) {
					$editor_settings = $value['settings'];
				}
				$editor_settings = apply_filters("vpanel_editor_settings",$editor_settings,$field_id);
				$editor_settings = array_merge($default_editor_settings,$editor_settings);
				wp_editor($val,$field_id,$editor_settings);
				$output = '';
				break;

			// Content
			case "content":
				if ( isset( $value['content'] ) ) {
					$output .= $value['content'];
				}
				break;

			// HTML
			case 'html':
				if ( isset( $value['html'] ) ) {
					$output .= '<div class="'.esc_attr( $class ).'" id="'.(isset($value['id']) && $value['id'] != ""?$value['id']:"").'" '.( $condition ).( $operator ).'>'.$value['html'].'</div>';
				}
				break;
			
			// Info
			case "info":
				$id = '';
				$class = 'section';
				if ( isset( $value['id'] ) ) {
					$id = 'id="'.esc_attr( $field_id ).'" ';
				}
				if ( isset( $value['type'] ) ) {
					$class .= ' section-'.$value['type'];
				}
				if ( isset( $value['class'] ) ) {
					$class .= ' '.$value['class'];
				}

				$output .= '<div '.$id.'class="'.esc_attr( $class ).'">';
				if ( isset($value['name']) ) {
					$output .= '<h4 class="heading">'.$value['name'].'</h4>';
				}
				if ( isset( $value['desc'] ) ) {
					$output .= apply_filters('of_sanitize_info', $value['desc'] );
				}
				$output .= '</div>';
				break;

			// Heading for Navigation
			case "heading":
				$counter++;
				if ( $counter >= 2 ) {
					$output .= '</div>';
				}
				$class = '';
				$class = ! empty( $value['id'] ) ? $value['id'] : $value['name'];
				$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
				if ( ! array_key_exists( 'template', $value ) || ! is_string( $value['template'] ) ) {
					$value['template'] = '';
				}
				$template = empty( $value['template'] ) ? '' : ' data-template="'. esc_attr( $value['template'] ) .'"';
				if (isset($value['template']) && $value['template'] != "" && $value['template'] != $wp_page_template) {
					$class .= ' hide';
				}
				$output .= '<div'.$template.' id="options-group-'.$counter.'" class="group '.$class.'">';
				//$output .= '<h3>'.($value['icon'] != ''?'<span class="dashicons dashicons-'.$value['icon'].'"></span>':''). esc_html( $value['name'] ) . '</h3>';
				$output .= '<h3>'. esc_html( $value['name'] ) . '</h3>';
				if (isset($value['options'])) {
					$output .= '<ul class="ask_tabs"'.(isset($value['std']) && $value['std'] != ""?' data-std="#head-' . esc_attr( $value['std'] ) . '"':'').'>';
					$k_a = 0;
					foreach ( $value['options'] as $key_h => $value_h ) {
						$k_a++;
						$output .= '<li><a title="' . esc_attr( $value_h ) . '" href="' . esc_attr( '#head-'.  $key_h ) . esc_attr( ',.head-class-'.  $key_h ) . esc_attr( ',#main-head-'.  $key_h ) . '">' . esc_html( $value_h ) . '</a></li>';
					}
					$output .= '</ul>';
				}
				break;
			
			case "heading-2":
				if ( isset($value['end']) && $value['end'] == "end" ) {
					if ( isset($value['div']) && $value['div'] == "div" ) {
						$output .= '</div>';
					}else {
						$output .= '</div></div></div>';
					}
				}else {
					if ( isset($value['div']) && $value['div'] == "div" ) {
						$output .= '<div class="'.$wrap_class.'" id="'.(isset($value['id']) && $value['id'] != ""?"wrap_".$value['id']:"").'"'.( $condition ).( $operator ).'>';
						if ( isset($value['name']) ) {
							$output .= '<h4 class="vpanel-head-2">' . esc_html( $value['name'] ) . '</h4>';
						}
					}else {
						$class = '';
						$class = ! empty( $value['id'] ) ? $value['id'] : (isset($value['name']) && $value['name'] != ""?$value['name']:"");
						$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
						$output .= '<div'.($page == "options"?" class='main-head'":"").(isset($value['id'])?' id="main-head-'.$value['id'].'"':'').'>
						<div'.(isset($value['id'])?' id="head-'.$value['id'].'"':'').' class="'.(isset($value['id'])?'head-class-'.$value['id'].' ':'').$options_group.(isset($value['id'])?' head-group head-'.$value['id']:'').'"'.( $condition ).( $operator ).'>';
						if ( isset($value['name']) ) {
							$output .= '<h4 class="vpanel-head-2">' . esc_html( $value['name'] ) . '</h4>';
						}
						$output .= '<div class="group-2 ' . $class . '">';
					}
				}
				break;

			case "group":
				if ( isset($value['end']) && $value['end'] == "end" ) {
					$output .= '</div></div>';
				}else {
					$class = '';
					$class = ! empty( $value['id'] ) ? $value['id'] : (isset($value['name']) && $value['name'] != ""?$value['name']:"");
					$class = preg_replace('/[^a-zA-Z0-9._\-]/', '', strtolower($class) );
					$output .= '<div'.(isset($value['id'])?' id="head-'.$value['id'].'"':'').' class="custom-group '.(isset($value['id'])?'head-class-'.$value['id'].' ':'').$options_group.(isset($value['id'])?' head-group head-'.$value['id']:'').'"'.( $condition ).( $operator ).'>';
					if ( isset($value['name']) ) {
						$output .= '<h4 class="vpanel-head-2">' . esc_html( $value['name'] ) . '</h4>';
					}
					$output .= '<div class="group-2 ' . $class . '">';
				}
				break;
			}
			
			if (isset($value['type'])) {
				if ($value['type'] != "heading" && $value['type'] != "heading-2" && $value['type'] != "heading-3" && $value['type'] != "group" && $value['type'] != "info" && $value['type'] != "content" && $value['type'] != "html" && $value['type'] != 'hidden') {
					$output .= '</div></div>';
				}
			}
		}

		echo ($output);
	}

	if (askme_admin_fields_class::askme_admin_tabs() != "" && $page != 'term') {
		echo "</div>";
	}
}