<?php
/* askme_field_is_visible */
function askme_field_is_visible( $condition, $operator, $fields, $values ) {
	
	if ( ! is_string( $condition ) || empty( $condition ) ) {
		return true;
	}

	if ( ! is_array( $fields ) ) {
		$fields = array();
	}

	if ( ! is_array( $values ) ) {
		$values = array();
	}
	
	$field_values = array();
	foreach ( $fields as $v ) {
		if (isset($v['id'])) {
			$field_values[ $v['id'] ] = array_key_exists( $v['id'], $values ) ? $values[ $v['id'] ] : ( array_key_exists( 'std', $v ) ? $v['std'] : '' );
		}
	}
	
	$bool_arr = array();
	$cond_arr = array_map( function($v) { $l = substr($v, -1); if ( $l != ')' ) { $v .= ')'; } return $v; }, explode( '),', $condition ) );
	
	foreach ( $cond_arr as $v ) {

		$bool = false;

		preg_match( '#^([a-z0-9_]+)\:(not|is|has|has_not)\(([a-z0-9-_\,]+)\)$#', trim( $v ), $match );

		if ( ! empty( $match ) ) {

			$id = $match[1];
			$op = $match[2];
			$val = $match[3];

			if ( in_array( $op, array( 'is', 'not' ) ) ) {
				if ($val == "empty" && ($op == 'is' || $op == 'not')) {
					if ($op == 'not') {
						$bool = ( $field_values[ $id ] != "" );
					}else {
						$bool = ( $field_values[ $id ] == "" );
					}
				}else {
					$bool = ( array_key_exists( $id, $field_values ) && $field_values[ $id ] == $val );
					
					if ( $op == 'not' ) {
						$bool = ( ! $bool );
					}
				}
			}else if ( in_array( $op, array( 'has', 'has_not' ) ) ) {
				if ( ! array_key_exists( $id, $field_values ) ) {
					$field_values[ $id ] = array();
				}

				if ( is_string( $field_values[ $id ] ) ) {
					$field_values[ $id ] = array_filter( explode( ',', $field_values[ $id ] ), function( $mv ) { return trim( $mv ); } );
				}

				if ( ! is_array( $field_values[ $id ] ) ) {
					$field_values[ $id ] = array();
				}
				
				if (isset($field_values[$id][$val])) {
					$bool = ((isset($field_values[$id][$val]["value"]) && $field_values[$id][$val]["value"] == $val) || (isset($field_values[$id][$val]) && $field_values[$id][$val] == 1) || (isset($field_values[$id][$val]) && $field_values[$id][$val] == $val));
				}else {
					$val = array_filter( explode( ',', $val ), function( $mv ) { return trim( $mv ); } );
					$bool = ( array_intersect( $val, $field_values[ $id ] ) == $val || ( count( $field_values[ $id ] ) == 1 && end( $field_values[ $id ] ) == 'all' ) );
				}
				
				if ( $op == 'has_not' ) {
					$bool = ( ! $bool );
				}
			}

		}

		$bool_arr[] = $bool;
	}

	if ( $operator == 'or' ) {
		return in_array( true, $bool_arr, true );
	}else {
		return ( ! in_array( false, $bool_arr, true ) );
	}
}
/* Question */
if ((bool)get_option("FlushRewriteRules")) {
	flush_rewrite_rules(true);
	delete_option("FlushRewriteRules");
}
/* Make the questions with number */
add_filter('post_type_link','askme_question_number_slug',10,2);
function askme_question_number_slug($post_link,$post) {
	$question_slug_numbers = askme_options("question_slug_numbers");
	if ($question_slug_numbers == 1) {
		if (ask_questions_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$question_slug = askme_options('question_slug');
		$question_slug = ($question_slug != ""?$question_slug:ask_questions_type);
		$post_link = str_replace('/'.$question_slug.'/'.$post->post_name,'/'.$question_slug.'/'.$post->ID,$post_link);
	}
	return $post_link;
}
add_filter('post_type_link','askme_asked_question_number_slug',10,2);
function askme_asked_question_number_slug($post_link,$post) {
	$asked_question_slug_numbers = askme_options("asked_question_slug_numbers");
	if ($asked_question_slug_numbers == 1) {
		if (ask_asked_questions_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$asked_question_slug = askme_options('asked_question_slug');
		$asked_question_slug = ($asked_question_slug != ""?$asked_question_slug:ask_asked_questions_type);
		$post_link = str_replace('/'.$asked_question_slug.'/'.$post->post_name,'/'.$asked_question_slug.'/'.$post->ID,$post_link);
	}
	return $post_link;
}
/* Remove question slug */
add_filter('post_type_link','askme_remove_slug',10,2);
function askme_remove_slug($post_link,$post) {
	$remove_question_slug = askme_options("remove_question_slug");
	if ($remove_question_slug == 1) {
		if (ask_questions_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$question_slug = askme_options('question_slug');
		$question_slug = ($question_slug != ""?$question_slug:ask_questions_type);
		$post_link = str_replace('/'.$question_slug.'/','/',$post_link);
	}
	return $post_link;
}
add_action('pre_get_posts','askme_parse_request');
function askme_parse_request($query) {
	$remove_question_slug = askme_options("remove_question_slug");
	$remove_asked_question_slug = askme_options("remove_asked_question_slug");
	if ($remove_question_slug == 1 || $remove_asked_question_slug == 1) {
		if (!$query->is_main_query() || 2 != count($query->query) || !isset($query->query['page'])) {
			return;
		}
		if (!empty($query->query['name'])) {
			$query->set('post_type',array('page','post',ask_questions_type,ask_asked_questions_type));
		}
	}
}
add_filter('post_type_link','askme_asked_remove_slug',10,2);
function askme_asked_remove_slug($post_link,$post) {
	$remove_asked_question_slug = askme_options("remove_asked_question_slug");
	if ($remove_asked_question_slug == 1) {
		if (ask_asked_questions_type != $post->post_type || 'publish' != $post->post_status) {
			return $post_link;
		}
		$asked_question_slug = askme_options('asked_question_slug');
		$asked_question_slug = ($asked_question_slug != ""?$asked_question_slug:ask_asked_questions_type);
		$post_link = str_replace('/'.$asked_question_slug.'/','/',$post_link);
	}
	return $post_link;
}
function askme_get_current_url() {
	$REQUEST_URI = strtok($_SERVER['REQUEST_URI'],'?');
	$real_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')?'https://':'http://';
	$real_url .= $_SERVER['SERVER_NAME'].$REQUEST_URI;
	return $real_url;
}
add_action('template_redirect','askme_auto_redirect_old');
function askme_auto_redirect_old() {
	$remove_question_slug = askme_options("remove_question_slug");
	$remove_asked_question_slug = askme_options("remove_asked_question_slug");
	if ($remove_question_slug == 1 || $remove_asked_question_slug == 1) {
		global $post;
		if (!is_preview() && is_single() && is_object($post) && (($remove_question_slug == 1 && $post->post_type == ask_questions_type) || ($remove_asked_question_slug == 1 && $post->post_type == ask_asked_questions_type))) {
			$new_url = get_permalink();
			$real_url = askme_get_current_url();
			if (substr_count($new_url,'/') != substr_count($real_url,'/') && strstr($real_url,$new_url) == false) {
				wp_redirect($new_url,301);
				die();
			}
		}
	}
}
function question_post_types_init() {
	$remove_question_slug    = askme_options("remove_question_slug");

	$archive_questions_slug  = askme_options('archive_questions_slug');
	$archive_questions_slug  = ($archive_questions_slug != ""?$archive_questions_slug:"questions");
	
	$question_slug           = askme_options('question_slug');
	$question_slug           = ($question_slug != ""?$question_slug:ask_questions_type);
	
	$category_questions_slug = askme_options('category_questions_slug');
	$category_questions_slug = ($category_questions_slug != ""?$category_questions_slug:ask_question_category);
	
	$tag_questions_slug      = askme_options('tag_questions_slug');
	$tag_questions_slug      = ($tag_questions_slug != ""?$tag_questions_slug:"question-tag");

	$question_sort = askme_options("ask_question_items");
	$featured_image_question = (isset($question_sort["featured_image"]["value"]) && $question_sort["featured_image"]["value"] == "featured_image"?1:0);
	$thumbnail               = ($featured_image_question == 1?array("thumbnail"):array());
	register_post_type( ask_questions_type,
		array(
			'label' => esc_html__('Questions','vbegy'),
			'labels' => array(
				'name'               => esc_html__('Questions','vbegy'),
				'singular_name'      => esc_html__('Questions','vbegy'),
				'menu_name'          => esc_html__('Questions','vbegy'),
				'name_admin_bar'     => esc_html__('Questions','vbegy'),
				'add_new'            => esc_html__('Add New','vbegy'),
				'add_new_item'       => esc_html__('Add New question','vbegy'),
				'new_item'           => esc_html__('New Question','vbegy'),
				'edit_item'          => esc_html__('Edit Question','vbegy'),
				'view_item'          => esc_html__('View Question','vbegy'),
				'view_items'         => esc_html__('View Questions','vbegy'),
				'all_items'          => esc_html__('All Questions','vbegy'),
				'search_items'       => esc_html__('Search Questions','vbegy'),
				'parent_item_colon'  => esc_html__('Parent Question:','vbegy'),
				'not_found'          => esc_html__('No Questions Found.','vbegy'),
				'not_found_in_trash' => esc_html__('No Questions Found in Trash.','vbegy'),
			),
			'description'         => '',
			'public'              => true,
			'show_ui'             => true,
			'capability_type'     => 'post',
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'hierarchical'        => false,
			'rewrite'             => array('slug' => ($remove_question_slug == 1?false:$question_slug),'hierarchical' => true,'with_front' => false),
			'query_var'           => true,
			'show_in_rest'        => true,
			'has_archive'         => $archive_questions_slug,
			'menu_position'       => 5,
			'menu_icon'           => "dashicons-editor-help",
			'supports'            => array_merge($thumbnail,array('title','editor','comments','author')),
			'taxonomies'          => array(ask_question_category,ask_question_tags),
		)
	);

	$question_slug_numbers = askme_options("question_slug_numbers");
	if ($question_slug_numbers == 1) {
		$removed = ($remove_question_slug == 1?'':$question_slug.'/');
		add_rewrite_rule($removed.'([0-9]+)?$','index.php?post_type='.$question_slug.'&p=$matches[1]','top');
	}
		
	$labels = array(
		'name'              => esc_html__('Question Categories','vbegy'),
		'singular_name'     => esc_html__('Question Categories','vbegy'),
		'search_items'      => esc_html__('Search Categories','vbegy'),
		'all_items'         => esc_html__('All Categories','vbegy'),
		'parent_item'       => esc_html__('Question Categories','vbegy'),
		'parent_item_colon' => esc_html__('Question Categories','vbegy'),
		'edit_item'         => esc_html__('Edit Category','vbegy'),
		'update_item'       => esc_html__('Edit','vbegy'),
		'add_new_item'      => esc_html__('Add New Category','vbegy'),
		'new_item_name'     => esc_html__('Add New Category','vbegy')
	);
	
	if (ask_question_category != "category") {
		register_taxonomy(ask_question_category,ask_questions_type,array(
			'hierarchical' => true,
			'labels'       => $labels,
			'show_ui'      => true,
			'query_var'    => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => $category_questions_slug, 'with_front' => false ),
		));
	}
	
	register_taxonomy( ask_question_tags,
		array(ask_questions_type),
		array(
			'hierarchical' => false,
			'labels' => array(
				'name'              => esc_html__('Question Tags','vbegy'),
				'singular_name'     => esc_html__('Question Tags','vbegy'),
				'search_items'      => esc_html__('Search Tags','vbegy'),
				'all_items'         => esc_html__('All Tags','vbegy'),
				'parent_item'       => esc_html__('Question Tags','vbegy'),
				'parent_item_colon' => esc_html__('Question Tags','vbegy'),
				'edit_item'         => esc_html__('Edit Tag','vbegy'),
				'update_item'       => esc_html__('Edit','vbegy'),
				'add_new_item'      => esc_html__('Add New Tag','vbegy'),
				'new_item_name'     => esc_html__('Add New Tag','vbegy')
			),
			'show_ui'      => true,
			'query_var'    => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => $tag_questions_slug ),
		)
	);
}  
add_action( 'init', 'question_post_types_init', 0 );
/* vpanel_remove_meta_boxes */
function vpanel_remove_meta_boxes() {
	global $post;
	$category_single_multi = askme_options("category_single_multi");
	if ($category_single_multi != "multi") {
		remove_meta_box( ask_question_category.'div', ask_questions_type, 'side' );
	}
	if (isset($post->ID)) {
		$get_question_user_id = get_post_meta($post->ID,"user_id",true);
		if ($get_question_user_id != "") {
			remove_meta_box( 'tagsdiv-'.ask_question_tags, ask_questions_type, 'side' );
		}
	}
}
add_action( 'do_meta_boxes' , 'vpanel_remove_meta_boxes' );
/* Admin columns for post types */
function askme_question_columns($old_columns){
	$columns = array();
	$columns["cb"]       = "<input type=\"checkbox\">";
	$columns["title"]    = __("Title","vbegy");
	$columns["type"]     = __("Type","vbegy");
	$columns["author_q"] = __("Author","vbegy");
	$columns["category"] = __("Category","vbegy");
	$columns["tag"]      = __("Tags","vbegy");
	$columns["comments"] = "<span class='vers comment-grey-bubble' title='".__("Answers","vbegy")."'><span class='screen-reader-text'>".__("Answers","vbegy")."</span></span>";
	$columns["date"]     = __("Date","vbegy");
	return $columns;
}
add_filter('manage_edit-'.ask_questions_type.'_columns', 'askme_question_columns');

function askme_question_custom_columns($column) {
	global $post;
	switch ( $column ) {
		case 'type' :
			$question_poll = get_post_meta($post->ID,'question_poll',true);
			if ($question_poll == 1) {
				echo '<a href="'.admin_url('edit.php?post_type='.$post->post_type.'&types=poll').'">'.__("Poll","vbegy").'</a>';
			}else {
				echo '<a href="'.admin_url('edit.php?post_type='.$post->post_type.'&types=question').'">'.__("Question","vbegy").'</a>';
			}
		break;
		case 'author_q' :
			$display_name = get_the_author_meta('display_name',$post->post_author);
			if ($post->post_author > 0) {
				echo '<a href="edit.php?post_type='.$post->post_type.'&author='.$post->post_author.'">'.$display_name.'</a>';
			}else {
				$anonymously_question = get_post_meta($post->ID,'anonymously_question',true);
				$anonymously_user = get_post_meta($post->ID,'anonymously_user',true);
				if ($anonymously_question == 1 && $anonymously_user != "") {
					esc_html_e("Anonymous","vbegy");
				}else {
					$question_username = get_post_meta($post->ID,'question_username',true);
					$question_username = ($question_username != ""?$question_username:esc_html__("Anonymous","vbegy"));
					echo $question_username;
				}
			}
			$user_id = get_post_meta($post->ID,'user_id',true);
			if ($user_id != "") {
				$display_name = get_the_author_meta('display_name',$user_id);
				echo "<br>".esc_html__("Asked to","vbegy")." <a href='".get_author_posts_url($user_id)."' target='_blank'>".$display_name."</a>";
			}
		break;
		case 'category' :
			$question_category = wp_get_post_terms($post->ID,ask_question_category,array("fields" => "all"));
			if (isset($question_category[0])) {?>
				<a href="<?php echo admin_url('edit.php?'.(ask_question_category == "category"?"category_name":ask_question_category).'='.$question_category[0]->slug.'&post_type='.ask_questions_type);?>"><?php echo $question_category[0]->name?></a>
			<?php }else {
				echo '<span aria-hidden="true">—</span><span class="screen-reader-text">'.__("No category","vbegy").'</span>';
			}
		break;
		case 'tag' :
			$terms = wp_get_object_terms($post->ID,ask_question_tags);
			if ($terms) :
				$terms_array = array();
				foreach ($terms as $term) :
					$terms_array[] = '<a href="'.admin_url('edit.php?'.ask_question_tags.'='.$term->slug.'&post_type='.ask_questions_type).'">'.$term->name.'</a>';
				endforeach;
				echo implode(', ',$terms_array);
			else:
				echo '<span aria-hidden="true">—</span><span class="screen-reader-text">'.__("No tags","vbegy").'</span>';
			endif;
		break;
	}
}
add_action('manage_'.ask_questions_type.'_posts_custom_column', 'askme_question_custom_columns', 2);
/* Question menus */
add_action('admin_menu','askme_add_admin_question');
function askme_add_admin_question() {
	add_submenu_page('edit.php?post_type='.ask_questions_type,esc_html__('Answers','vbegy'),esc_html__('Answers','vbegy'),'manage_options','edit-comments.php?comment_status=all&answers=1');
	add_submenu_page('edit.php?post_type='.ask_questions_type,esc_html__('Best Answers','vbegy'),esc_html__('Best Answers','vbegy'),'manage_options','edit-comments.php?comment_status=all&best_answers=1');
}
/* Answers tab */
add_action('admin_menu','askme_add_admin_answer');
function askme_add_admin_answer() {
	add_comments_page(esc_html__('Comments','vbegy'),esc_html__('Comments','vbegy'),'moderate_comments','edit-comments.php?comment_status=all&comments=1');
	add_comments_page(esc_html__('Answers','vbegy'),esc_html__('Answers','vbegy'),'moderate_comments','edit-comments.php?comment_status=all&answers=1');
	add_comments_page(esc_html__('Best Answers','vbegy'),esc_html__('Best Answers','vbegy'),'moderate_comments','edit-comments.php?comment_status=all&best_answers=1');
}

/* Asked question post type */
if (!function_exists('askme_asked_question_post_type')) :
	function askme_asked_question_post_type() {
		$remove_asked_question_slug = askme_options("remove_asked_question_slug");
		$asked_questions_convert = get_option("askme_asked_questions_convert");
		if ($asked_questions_convert == "") {
			$query = array('posts_per_page' => -1,'post_status' => 'any','post_type' => ask_questions_type,"meta_query" => array(array("key" => "user_id","compare" => "EXISTS")));
			$items = get_posts($query);
			if (is_array($items) && !empty($items)) {
				foreach ($items as $post) {
					set_post_type($post->ID,ask_asked_questions_type);
				}
			}
			update_option("askme_asked_questions_convert",true);
		}
		
		$asked_question_slug = askme_options('asked_question_slug');
		$asked_question_slug = ($asked_question_slug != ""?$asked_question_slug:ask_asked_questions_type);
	   
		register_post_type(ask_asked_questions_type,
			array(
				'label' => esc_html__('Asked Questions','vbegy'),
				'labels' => array(
					'name'               => esc_html__('Asked Questions','vbegy'),
					'singular_name'      => esc_html__('Asked Questions','vbegy'),
					'menu_name'          => esc_html__('Asked Questions','vbegy'),
					'name_admin_bar'     => esc_html__('Asked Question','vbegy'),
					'add_new'            => esc_html__('Add New','vbegy'),
					'add_new_item'       => esc_html__('Add New Asked Question','vbegy'),
					'new_item'           => esc_html__('New Asked Question','vbegy'),
					'edit_item'          => esc_html__('Edit Asked Question','vbegy'),
					'view_item'          => esc_html__('View Asked Question','vbegy'),
					'view_items'         => esc_html__('View Asked Questions','vbegy'),
					'all_items'          => esc_html__('Asked Questions','vbegy'),
					'search_items'       => esc_html__('Search Asked Questions','vbegy'),
					'parent_item_colon'  => esc_html__('Parent Asked Question:','vbegy'),
					'not_found'          => esc_html__('No Asked Questions Found.','vbegy'),
					'not_found_in_trash' => esc_html__('No Asked Questions Found in Trash.','vbegy'),
				),
				'description'         => '',
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'edit.php?post_type='.ask_questions_type,
				'capability_type'     => 'post',
				'capabilities'        => array('create_posts' => 'do_not_allow'),
				'map_meta_cap'        => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'hierarchical'        => false,
				'rewrite'             => array('slug' => apply_filters("askme_asked_question_slug",($remove_asked_question_slug == 1?false:$asked_question_slug)),'hierarchical' => true,'with_front' => false),
				'query_var'           => true,
				'show_in_rest'        => true,
				'has_archive'         => false,
				'menu_position'       => 5,
				'menu_icon'           => "dashicons-editor-help",
				'supports'            => array('title','editor','comments','author'),
			)
		);

		$asked_question_slug_numbers = askme_options("asked_question_slug_numbers");
		if ($asked_question_slug_numbers == 1) {
			$removed = ($remove_asked_question_slug == 1?'':$asked_question_slug.'/');
			add_rewrite_rule($removed.'([0-9]+)?$','index.php?post_type='.$asked_question_slug.'&p=$matches[1]','top');
		}
	}
endif;
add_action('init','askme_asked_question_post_type',0);
/* Admin columns for post types */
add_filter('manage_edit-'.ask_questions_type.'_columns', 'askme_question_columns');
add_action('manage_'.ask_questions_type.'_posts_custom_column','askme_question_custom_columns',2);

function question_updated_messages($messages) {
	global $post_ID;
	$get_permalink = get_permalink($post_ID);
	$messages[ask_questions_type] = array(
		0 => '',
		1 => sprintf( __('Updated. <a href="%s">View question</a>','vbegy'),esc_url($get_permalink)),
	);
	$messages[ask_asked_questions_type] = array(
		0 => '',
		1 => sprintf( __('Updated. <a href="%s">View question</a>','vbegy'),esc_url($get_permalink)),
	);
	return $messages;
}
add_filter('post_updated_messages','question_updated_messages');

if (!function_exists('get_question_details')) {
	function get_question_details( $post_id ) { 
		
		$category = current(wp_get_object_terms($post_id,ask_question_category));
		$video_type = get_post_meta($post_id,'video_type',true);
		$video_id = get_post_meta($post_id,'video_id',true);
		
		if (!isset($category->name)) $category = '';
		
		$question_details = array(
			'category'   => $category,
			'video_type' => $video_type,
			'video_id'   => $video_id,
		);
		return $question_details;
	}
}
/* askme_questions_status */
add_filter( "views_edit-".ask_questions_type, "askme_questions_status" );
function askme_questions_status($status) {
	global $wpdb;
	$prepare = $wpdb->prepare("SELECT $wpdb->posts.* FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) WHERE 1=1 AND ( ( $wpdb->postmeta.meta_key = 'question_poll' AND $wpdb->postmeta.meta_value = %s ) ) AND $wpdb->posts.post_type = '".ask_questions_type."' AND (($wpdb->posts.post_status = 'publish')) GROUP BY $wpdb->posts.ID ORDER BY $wpdb->posts.post_date DESC",1);
	$result = $wpdb->get_results($prepare);
	$query_poll_count = count($result);
	$query_question_count = wp_count_posts(ask_questions_type)->publish-$query_poll_count;
	
	$prepare_sticky = $wpdb->prepare("SELECT $wpdb->posts.* FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) WHERE 1=1 AND ( ( $wpdb->postmeta.meta_key = 'sticky' AND $wpdb->postmeta.meta_value = %s ) ) AND $wpdb->posts.post_type = '".ask_questions_type."' AND (($wpdb->posts.post_status = 'publish')) GROUP BY $wpdb->posts.ID ORDER BY $wpdb->posts.post_date DESC",1);
	$result_sticky = $wpdb->get_results($prepare_sticky);
	$query_sticky_count = count($result_sticky);

	$wp_count_asked_questions = wp_count_posts(ask_asked_questions_type);
	$count_asked_questions = 0;
	foreach ($wp_count_asked_questions as $key => $value) {
		$count_asked_questions += $value;
	}
	
	if (isset($_GET['types'])) {
		$get_status = esc_attr($_GET['types']);
	}
	return array_merge( $status, array(
		'question' => '<a href="'.admin_url('edit.php?post_type='.ask_questions_type.'&types=question').'"'.(isset($get_status) && $get_status == "question"?' class="current"':'').'>'.__('Question','vbegy').' ('.$query_question_count.')</a>',
		'poll' => '<a href="'.admin_url('edit.php?post_type='.ask_questions_type.'&types=poll').'"'.(isset($get_status) && $get_status == "poll"?' class="current"':'').'>'.__('Poll','vbegy').' ('.$query_poll_count.')</a>',
		'sticky' => '<a href="'.admin_url('edit.php?post_type='.ask_questions_type.'&types=sticky').'"'.(isset($get_status) && $get_status == "sticky"?' class="current"':'').'>'.__('Sticky','vbegy').' ('.$query_sticky_count.')</a>',
		'asked' => '<a href="'.admin_url('edit.php?post_type='.ask_questions_type).'"'.(isset($get_status) && $get_status == "asked"?' class="current"':'').'>'.esc_html__('Asked Questions','vbegy').' ('.$count_asked_questions.')</a>',
	));
}
add_action('current_screen','askme_questions_exclude',10,2);
function askme_questions_exclude($screen) {
	if ($screen->id != 'edit-'.ask_questions_type)
		return;
	if (isset($_GET['types'])) {
		$get_status = esc_attr($_GET['types']);
	}
	add_action('pre_get_posts','askme_list_questions');
}
function askme_list_questions($query) {
	global $post_type,$pagenow;
	if ($pagenow == 'edit.php' && $post_type == ask_questions_type) {
		$get_status = (isset($_GET['types'])?esc_html($_GET['types']):'');
		if ($get_status == "poll") {
			$query->query_vars['meta_key'] = "question_poll";
			$query->query_vars['meta_value'] = 1;
			$query->query_vars['post_type'] = ask_questions_type;
		}else if ($get_status == "sticky") {
			$query->query_vars['meta_key'] = "sticky";
			$query->query_vars['meta_value'] = 1;
			$query->query_vars['post_type'] = ask_questions_type;
		}else if ($get_status == "question") {
			$query->query_vars['meta_key'] = "question_poll";
			$query->query_vars['meta_value'] = 2;
			$query->query_vars['post_type'] = ask_questions_type;
		}else if (is_post_type_archive(ask_questions_type) && $query->is_main_query()) {
			//$query->query_vars['post_type'] = array(ask_questions_type,ask_asked_questions_type);
			//$query->set('post_type',array(ask_questions_type,ask_asked_questions_type));
			$query->query_vars['post_type'] = ask_questions_type;
		}
	}
}
/* Set post & question stats */
add_action("askme_action_after_post_content","askme_set_page_visits");
add_action("askme_action_on_user_page","askme_set_page_visits");
function askme_set_page_visits($post_id = 0,$post_author = 0) {
	$active_post_stats = askme_options("active_post_stats");
	if ($active_post_stats == 1 && (is_single($post_id) || is_author())) {
		$user_login = askme_get_user_object();
		if (is_object($user_login)) {
			$get_query_var = $user_login->ID;
		}
		$active_stats = true;
		$user_id = get_current_user_id();
		$yes_private = (is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)?ask_private($post_id,$post_author,$user_id):1);
		if (!is_super_admin($user_id) && $yes_private != 1) {
			$active_stats = false;
		}
		if ($active_stats == true) {?>
			<div class="activate-post-stats <?php echo esc_attr(is_author()?"page-visits-user":"page-visits-post")?>" data-id="<?php echo (int)(is_author() && isset($get_query_var)?$get_query_var:$post_id)?>"></div>
		<?php }
	}
}
/* Get meta */
function askme_get_meta($meta,$post_id = 0,$comment_id = 0,$term_id = 0,$user_id = 0) {
	if ($comment_id > 0) {
		$value = get_comment_meta($comment_id,$meta,true);
	}else if ($post_id > 0) {
		$value = get_post_meta($post_id,$meta,true);
	}else if ($term_id > 0) {
		$value = get_term_meta($term_id,$meta,true);
	}else if ($user_id > 0) {
		$value = get_user_meta($user_id,$meta,true);
	}
	if (isset($value)) {
		return $value;
	}
}
/* Add meta */
function askme_add_meta($meta,$value,$post_id = 0,$comment_id = 0,$term_id = 0,$user_id = 0) {
	if ($comment_id > 0) {
		add_comment_meta($comment_id,$meta,$value);
	}else if ($post_id > 0) {
		add_post_meta($post_id,$meta,$value);
	}else if ($term_id > 0) {
		add_term_meta($term_id,$meta,$value);
	}else if ($user_id > 0) {
		add_user_meta($user_id,$meta,$value);
	}
}
/* Update meta */
function askme_update_meta($meta,$value,$post_id = 0,$comment_id = 0,$term_id = 0,$user_id = 0) {
	if ($comment_id > 0) {
		update_comment_meta($comment_id,$meta,$value);
	}else if ($post_id > 0) {
		update_post_meta($post_id,$meta,$value);
	}else if ($term_id > 0) {
		update_term_meta($term_id,$meta,$value);
	}else if ($user_id > 0) {
		update_user_meta($user_id,$meta,$value);
	}
}
/* Delete meta */
function askme_delete_meta($meta,$post_id = 0,$comment_id = 0,$term_id = 0,$user_id = 0) {
	if ($comment_id > 0) {
		delete_comment_meta($comment_id,$meta);
	}else if ($post_id > 0) {
		delete_post_meta($post_id,$meta);
	}else if ($term_id > 0) {
		delete_term_meta($term_id,$meta);
	}else if ($user_id > 0) {
		delete_user_meta($user_id,$meta);
	}
}
/* Update post stats */
add_action('wp_ajax_askme_update_post_stats','askme_update_post_stats');
add_action('wp_ajax_nopriv_askme_update_post_stats','askme_update_post_stats');
function askme_update_post_stats($post_id = 0,$user_id = 0) {
	$post_id = (int)($post_id > 0?$post_id:(isset($_POST["post_id"])?$_POST["post_id"]:0));
	$user_id = (int)($user_id > 0?$user_id:(isset($_POST["user_id"])?$_POST["user_id"]:0));
	$meta_id = ($user_id > 0?$user_id:$post_id);
	$meta_stats = askme_get_meta_stats();
	$cache_post_stats = askme_options("cache_post_stats");
	$current_stats = askme_get_meta($meta_stats,$post_id,0,0,$user_id);
	$visit_cookie = askme_options("visit_cookie");
	if ($visit_cookie != 1 || ($visit_cookie == 1 && !isset($_COOKIE[askme_options("uniqid_cookie").'askme_meta_stats'.$meta_id]))) {
		if (!isset($current_stats)) {
			askme_update_meta($meta_stats,1,$post_id,0,0,$user_id);
			if ($cache_post_stats == 1) {
				set_transient($meta_stats.$meta_id,(int)$current_stats+1,60*60*24);
			}
		}else {
			askme_update_meta($meta_stats,(int)$current_stats+1,$post_id,0,0,$user_id);
			if ($cache_post_stats == 1) {
				$post_stats = get_transient($meta_stats.$meta_id);
				if ($post_stats == false) {
					set_transient($meta_stats.$meta_id,(int)$current_stats+1,60*60*24);
				}
			}
		}
	}
	if ($visit_cookie == 1) {
		$uniqid_cookie = askme_options("uniqid_cookie");
		if (isset($_COOKIE[$uniqid_cookie.'askme_meta_stats'.$meta_id]) && $_COOKIE[$uniqid_cookie.'askme_meta_stats'.$meta_id] == "askme_meta_stats") {
			unset($_COOKIE[$uniqid_cookie.'askme_meta_stats'.$meta_id]);
			setcookie($uniqid_cookie.'askme_meta_stats'.$meta_id,"",-1,COOKIEPATH,COOKIE_DOMAIN);
		}
		setcookie($uniqid_cookie.'askme_meta_stats'.$meta_id,"askme_meta_stats",time()+YEAR_IN_SECONDS,COOKIEPATH,COOKIE_DOMAIN);
	}
}
if (is_admin()) {
	/* Count new reports */
	$ask_option_array = get_option("ask_option_array");
	if (is_array($ask_option_array)) {
		foreach ($ask_option_array as $key => $value) {
			$ask_one_option = get_option("ask_option_".$value);
			$post_no_empty = get_post($ask_one_option["post_id"]);
			if (!isset($post_no_empty)) {
				unset($ask_one_option);
			}
			if (isset($ask_one_option) && $ask_one_option["report_new"] == 1) {
				$count_report_new[] = $ask_one_option["report_new"];
			}
		}
	}
	/* Count new reports answers */
	$ask_option_answer_array = get_option("ask_option_answer_array");
	if (is_array($ask_option_answer_array)) {
		foreach ($ask_option_answer_array as $key => $value) {
			$ask_one_option = get_option("ask_option_answer_".$value);
			$comment_no_empty = get_comment($ask_one_option["comment_id"]);
			if (!isset($comment_no_empty)) {
				unset($ask_one_option);
			}
			if (isset($ask_one_option) && $ask_one_option["report_new"] == 1) {
				$count_report_answer_new[] = $ask_one_option["report_new"];
			}
		}
	}
	/* Count new reports users */
	$ask_option_user_array = get_option("ask_option_user_array");
	if (is_array($ask_option_user_array)) {
		foreach ($ask_option_user_array as $key => $value) {
			$ask_one_option = get_option("ask_option_user_".$value);
			$askme_profile_url = get_author_posts_url($ask_one_option["author_id"]);
			$display_name = get_the_author_meta('display_name',$ask_one_option["author_id"]);
			if ($askme_profile_url != "" && $display_name != "") {
				$user_no_empty = true;
			}
			if (!isset($user_no_empty)) {
				unset($ask_one_option);
			}
			if (isset($ask_one_option) && $ask_one_option["report_new"] == 1) {
				$count_report_user_new[] = $ask_one_option["report_new"];
			}
		}
	}
	/* reports_delete */
	function reports_delete() {
		$reports_delete_id = (int)esc_html($_POST["reports_delete_id"]);
		/* delete option */
		delete_option("ask_option_".$reports_delete_id);
		$ask_option_array = get_option("ask_option_array");
		$ask_option = get_option("ask_option");
		$ask_option--;
		update_option("ask_option",$ask_option);
		$arr = array_diff($ask_option_array, array($reports_delete_id));
		update_option("ask_option_array",$arr);
		die();
	}
	add_action("wp_ajax_nopriv_reports_delete","reports_delete");
	add_action("wp_ajax_reports_delete","reports_delete");
	/* reports_view */
	function reports_view() {
		$reports_view_id = (int)esc_html($_POST["reports_view_id"]);
		/* option */
		$ask_one_option = get_option("ask_option_".$reports_view_id);
		$item_id_option = $ask_one_option["item_id_option"];
		foreach ($ask_one_option as $key => $value) {
			if ($key == "report_new") {
				$ask_one_option["report_new"] = 0;
			}
		}
		update_option("ask_option_".$reports_view_id,$ask_one_option);
		die();
	}
	add_action("wp_ajax_nopriv_reports_view","reports_view");
	add_action("wp_ajax_reports_view","reports_view");
	/* reports_answers_delete */
	function reports_answers_delete() {
		$reports_delete_id = (int)esc_html($_POST["reports_delete_id"]);
		/* delete option */
		delete_option("ask_option_answer_".$reports_delete_id);
		$ask_option_answer_array = get_option("ask_option_answer_array");
		$ask_option_answer = get_option("ask_option_answer");
		$ask_option_answer--;
		update_option("ask_option_answer",$ask_option_answer);
		$arr = array_diff($ask_option_answer_array, array($reports_delete_id));
		update_option("ask_option_answer_array",$arr);
		die();
	}
	add_action("wp_ajax_nopriv_reports_answers_delete","reports_answers_delete");
	add_action("wp_ajax_reports_answers_delete","reports_answers_delete");
	/* reports_answers_view */
	function reports_answers_view() {
		$reports_view_id = (int)esc_html($_POST["reports_view_id"]);
		echo $reports_view_id;
		/* option */
		$ask_one_option = get_option("ask_option_answer_".$reports_view_id);
		$item_id_option = $ask_one_option["item_id_option"];
		foreach ($ask_one_option as $key => $value) {
			if ($key == "report_new") {
				$ask_one_option["report_new"] = 0;
			}
		}
		update_option("ask_option_answer_".$reports_view_id,$ask_one_option);
		die();
	}
	add_action("wp_ajax_nopriv_reports_answers_view","reports_answers_view");
	add_action("wp_ajax_reports_answers_view","reports_answers_view");
	/* reports_users_delete */
	function reports_users_delete() {
		$reports_delete_id = (int)esc_html($_POST["reports_delete_id"]);
		/* delete option */
		delete_option("ask_option_user_".$reports_delete_id);
		$ask_option_user_array = get_option("ask_option_user_array");
		$ask_option_user = get_option("ask_option_user");
		$ask_option_user--;
		update_option("ask_option_user",$ask_option_user);
		$arr = array_diff($ask_option_user_array, array($reports_delete_id));
		update_option("ask_option_user_array",$arr);
		die();
	}
	add_action("wp_ajax_nopriv_reports_users_delete","reports_users_delete");
	add_action("wp_ajax_reports_users_delete","reports_users_delete");
	/* reports_users_view */
	function reports_users_view() {
		$reports_view_id = (int)esc_html($_POST["reports_view_id"]);
		echo $reports_view_id;
		/* option */
		$ask_one_option = get_option("ask_option_user_".$reports_view_id);
		$item_id_option = $ask_one_option["item_id_option"];
		foreach ($ask_one_option as $key => $value) {
			if ($key == "report_new") {
				$ask_one_option["report_new"] = 0;
			}
		}
		update_option("ask_option_user_".$reports_view_id,$ask_one_option);
		die();
	}
	add_action("wp_ajax_nopriv_reports_users_view","reports_users_view");
	add_action("wp_ajax_reports_users_view","reports_users_view");
	/* publishing_action_post */
	function publishing_action_post() {
		$post_ID = (int)$_POST["post_ID"];
			$question_username = get_post_meta($post_ID, 'question_username', true);
			$question_email = get_post_meta($post_ID, 'question_email', true);
			$post_username = get_post_meta($post_ID, 'post_username', true);
			$post_email = get_post_meta($post_ID, 'post_email', true);
			if ($question_username != "") {
				$question_no_username = get_post_meta($post_ID,'question_no_username',true);
			}
			if (((isset($question_no_username) && $question_no_username == "no_user") || (isset($question_username) && $question_username != "" && isset($question_email) && $question_email != "")) || (isset($post_username) && $post_username != "" && isset($post_email) && $post_email != "")) {
				$get_post = get_post($post_ID);
				$publish_date = $get_post->post_date;
					$data = array(
						'ID' => $post_ID,
						'post_author' => 0,
					);
				wp_update_post($data);
			}
	}
	add_action('wp_ajax_publishing_action_post','publishing_action_post');
	add_action('wp_ajax_nopriv_publishing_action_post','publishing_action_post');
	/* confirm_delete_attachment */
	function confirm_delete_attachment() {
		$attachment_id     = (int)$_POST["attachment_id"];
		$post_id           = (int)$_POST["post_id"];
		$single_attachment = esc_attr($_POST["single_attachment"]);
		if ($single_attachment == "Yes") {
			wp_delete_attachment($attachment_id);
			delete_post_meta($post_id, 'added_file');
		}else {
			$attachment_m = get_post_meta($post_id,'attachment_m',true);
			if (isset($attachment_m) && is_array($attachment_m) && !empty($attachment_m)) {
				foreach ($attachment_m as $key => $value) {
					if ($value["added_file"] == $attachment_id) unset($attachment_m[$key]);
					wp_delete_attachment($value["added_file"]);
				}
			}
			update_post_meta($post_id, 'attachment_m', $attachment_m);
		}
		die();
	}
	add_action('wp_ajax_confirm_delete_attachment','confirm_delete_attachment');
	add_action('wp_ajax_nopriv_confirm_delete_attachment','confirm_delete_attachment');
}
/* ask_add_admin_page_reports */
function ask_add_admin_page_reports() {
	$active_reports = askme_options("active_reports");
	if ($active_reports == 1) {
		global $count_report_new,$count_report_answer_new,$count_report_user_new;
		$count_report_new = (isset($count_report_new) && is_array($count_report_new)?count($count_report_new):0);
		$count_report_answer_new = (isset($count_report_answer_new) && is_array($count_report_answer_new)?count($count_report_answer_new):0);
		$count_report_user_new = (isset($count_report_user_new) && is_array($count_report_user_new)?count($count_report_user_new):0);
		$count_lasts = $count_report_new+$count_report_answer_new+$count_report_user_new;
		$vpanel_page = add_menu_page('Reports', 'Reports '.($count_lasts > 0?'<span class="count_report_new awaiting-mod count-'.$count_lasts.'"><span class="count_lasts pending-count">'.$count_lasts.'</span></span>':'') ,'manage_options', 'r_questions' , 'r_questions','dashicons-email-alt');
		add_submenu_page( 'r_questions', 'Questions', 'Questions '.($count_report_new > 0?'<span class="count_report_new awaiting-mod count-'.$count_report_new.'"><span class="count_report_question_new pending-count">'.$count_report_new.'</span></span>':''), 'manage_options', 'r_questions', 'r_questions' );
		add_submenu_page( 'r_questions', 'Answers', 'Answers '.($count_report_answer_new > 0?'<span class="count_report_new awaiting-mod count-'.$count_report_answer_new.'"><span class="count_report_answer_new pending-count">'.$count_report_answer_new.'</span></span>':''), 'manage_options', 'r_answers', 'r_answers' );
		$report_users = askme_options("report_users");
		if ($report_users == 1) {
			add_submenu_page( 'r_questions', 'Users', 'Users '.($count_report_user_new > 0?'<span class="count_report_new awaiting-mod count-'.$count_report_user_new.'"><span class="count_report_user_new pending-count">'.$count_report_user_new.'</span></span>':''), 'manage_options', 'r_users', 'r_users' );
		}
		do_action("askme_add_admin_page_reports");
	}
	$pay_ask = askme_options("pay_ask");
	$pay_to_sticky = askme_options("pay_to_sticky");
	$apply_filters = apply_filters("askme_new_payments_menu",false);
	if ($pay_ask == 1 || $pay_to_sticky == 1 || $apply_filters == true) {
		$new_payments = get_option("new_payments");
		add_menu_page('Payments', 'Payments '.($new_payments > 0?'<span class="count_report_new awaiting-mod count-'.$new_payments.'"><span class="pending-count">'.$new_payments.'</span></span>':'') ,'manage_options', 'ask_payments' , 'ask_payments','dashicons-cart');
	}
}
add_action('admin_menu', 'ask_add_admin_page_reports');
/* ask_payments */
function ask_payments () {?>
	<div class="reports-warp">
		<div class="reports-head"><i class="dashicons dashicons-cart"></i>Payments</div>
		<div class="reports-padding">
			<?php $the_currency = get_option("the_currency");
			if (isset($the_currency) && is_array($the_currency)) {
				echo "All my money<br>";
				foreach ($the_currency as $key => $currency) {
					if (isset($currency) && $currency != "") {
						$all_money = get_option("all_money_".$currency);
						echo "<br>".(isset($all_money) && $all_money != ""?$all_money:0)." ".$currency."<br>";
						//$_all_my_payment = get_user_meta(get_current_user_id(),get_current_user_id()."_all_my_payment_".$currency,true);
						//echo " all my payment ".(isset($_all_my_payment) && $_all_my_payment != ""?$_all_my_payment:0)." ".$currency."<br>";
					}
				}
				echo "<br>";
			}?>
			<div class="reports-table">
				<div class="reports-table-head">
					<div class="div-payment">Price - (coupon)</div>
					<div class="div-payment">Author</div>
					<div class="div-payment">Item</div>
					<div class="div-payment">Date</div>
					<div class="div-payment">Transaction</div>
					<div class="div-payment div-payment-last">Payer email - (sandbox)</div>
				</div><!-- End reports-table-head -->
				<?php
				$_payments = get_option("payments_option");
				$rows_per_page = get_option("posts_per_page");
				for ($payments = 1; $payments <= $_payments; $payments++) {
					$payment_one[] = get_option("payments_".$payments);
				}
				
				if (isset($payment_one) && is_array($payment_one) && !empty($payment_one)) {?>
					<div class="reports-table-items">
					<?php
					$new_payments = update_option("new_payments",0);
					$payment = array_reverse($payment_one);
					$paged = (isset($_GET["paged"])?(int)$_GET["paged"]:1);
					$current = max(1,$paged);
					$pagination_args = array(
						'base' => @esc_url(add_query_arg('paged','%#%')),
						'total' => ceil(sizeof($payment)/$rows_per_page),
						'current' => $current,
						'show_all' => false,
						'prev_text' => '&laquo; Previous',
						'next_text' => 'Next &raquo;',
					);
					
					if( !empty($wp_query->query_vars['s']) )
						$pagination_args['add_args'] = array('s'=>get_query_var('s'));
						
					$start = ($current - 1) * $rows_per_page;
					$end = $start + $rows_per_page;
					$end = (sizeof($payment) < $end) ? sizeof($payment) : $end;
					for ($i=$start;$i < $end ;++$i ) {
						$payment_result = $payment[$i];
						$date_years = (isset($payment_result["date_years"]) && $payment_result["date_years"] != ""?$payment_result["date_years"]:(isset($payment_result[0]) && $payment_result[0] != ""?$payment_result[0]:""));
						$date_hours = (isset($payment_result["date_hours"]) && $payment_result["date_hours"] != ""?$payment_result["date_hours"]:(isset($payment_result[1]) && $payment_result[1] != ""?$payment_result[1]:""));
						$item_number = (isset($payment_result["item_number"]) && $payment_result["item_number"] != ""?$payment_result["item_number"]:(isset($payment_result[2]) && $payment_result[2] != ""?$payment_result[2]:""));
						$item_price = (isset($payment_result["item_price"]) && $payment_result["item_price"] != ""?$payment_result["item_price"]:(isset($payment_result[3]) && $payment_result[3] != ""?$payment_result[3]:""));
						$item_currency = (isset($payment_result["item_currency"]) && $payment_result["item_currency"] != ""?$payment_result["item_currency"]:(isset($payment_result[4]) && $payment_result[4] != ""?$payment_result[4]:""));
						$item_transaction = (isset($payment_result["item_transaction"]) && $payment_result["item_transaction"] != ""?$payment_result["item_transaction"]:(isset($payment_result[5]) && $payment_result[5] != ""?$payment_result[5]:""));
						$payer_email = (isset($payment_result["payer_email"]) && $payment_result["payer_email"] != ""?$payment_result["payer_email"]:(isset($payment_result[6]) && $payment_result[6] != ""?$payment_result[6]:""));
						$first_name = (isset($payment_result["first_name"]) && $payment_result["first_name"] != ""?$payment_result["first_name"]:(isset($payment_result[7]) && $payment_result[7] != ""?$payment_result[7]:""));
						$last_name = (isset($payment_result["last_name"]) && $payment_result["last_name"] != ""?$payment_result["last_name"]:(isset($payment_result[8]) && $payment_result[8] != ""?$payment_result[8]:""));
						$user_id = (isset($payment_result["user_id"]) && $payment_result["user_id"] != ""?$payment_result["user_id"]:(isset($payment_result[9]) && $payment_result[9] != ""?$payment_result[9]:""));
						$sandbox = (isset($payment_result["sandbox"]) && $payment_result["sandbox"] != ""?$payment_result["sandbox"]:(isset($payment_result[10]) && $payment_result[10] != ""?$payment_result[10]:""));
						$time = (isset($payment_result["time"]) && $payment_result["time"] != ""?human_time_diff($payment_result["time"],current_time('timestamp'))." ago":(isset($payment_result[11]) && $payment_result[11] != ""?human_time_diff($payment_result[11],current_time('timestamp'))." ago":$date_years." ".$date_hours));
						$coupon = (isset($payment_result["coupon"]) && $payment_result["coupon"] != ""?$payment_result["coupon"]:(isset($payment_result[12]) && $payment_result[12] != ""?$payment_result[12]:""));
						$coupon_value = (isset($payment_result["coupon_value"]) && $payment_result["coupon_value"] != ""?$payment_result["coupon_value"]:(isset($payment_result[13]) && $payment_result[13] != ""?$payment_result[13]:""));
						$item_name = (isset($payment_result["item_name"]) && $payment_result["item_name"] != ""?$payment_result["item_name"]:"---");?>
						<div class="reports-table-item">
							<div class="div-payment"><?php echo $item_price." ".$item_currency.(isset($coupon) && $coupon != ""?" - (".$coupon.")":"")?></a></div>
							<div class="div-payment">
								<a href="<?php echo vpanel_get_user_url((int)$user_id);?>"><?php echo get_the_author_meta("display_name",(int)$user_id)?></a>
							</div>
							<div class="div-payment">
								<?php echo $item_name?>
							</div>
							<div class="div-payment">
								<?php echo $time?>
							</div>
							<div class="div-payment"><?php echo $item_transaction?></a></div>
							<div class="div-payment div-payment-last"><?php echo $payer_email.(isset($sandbox) && $sandbox != ""?" - (".$sandbox.")":"")?></a></div>
						</div>
					<?php } ?>
					</div><!-- End reports-table-items -->
				<?php }else {
					echo "<p>There are no payments yet</p>";
				}
				?>
			</div><!-- End reports-table -->
			<?php if (isset($payment_one) &&is_array($payment_one) && $pagination_args["total"] > 1) {?>
				<div class='reports-paged'>
					<?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?>
				</div><!-- End reports-paged -->
				<div class="clear"></div>
			<?php }?>
		</div><!-- End reports-padding -->
	</div><!-- End reports-warp -->
	<?php
}
/* r_questions */
function r_questions () {?>
	<div class="reports-warp">
		<div class="reports-head"><i class="dashicons dashicons-flag"></i>Questions Reports</div>
		<div class="reports-padding">
			<div class="reports-table">
				<div class="reports-table-head">
					<div class="report-link">Link</div>
					<div class="report-author">Author</div>
					<div class="report-date">Date</div>
					<div class="reports-options">Options</div>
				</div><!-- End reports-table-head -->
				<?php
				$rows_per_page = get_option("posts_per_page");
				$ask_option = get_option("ask_option");
				$ask_option_array = get_option("ask_option_array");
				if (is_array($ask_option_array)) {
					foreach ($ask_option_array as $key => $value) {
						$ask_one_option[$value] = get_option("ask_option_".$value);
						$post_no_empty = get_post($ask_one_option[$value]["post_id"]);
						if (!isset($post_no_empty)) {
							unset($ask_one_option[$value]);
						}
					}
				}
				if (isset($ask_one_option) && is_array($ask_one_option) && !empty($ask_one_option)) {?>
					<div class="reports-table-items">
					<?php
					$ask_reports_option = array_reverse($ask_one_option);
					$paged = (isset($_GET["paged"])?(int)$_GET["paged"]:1);
					$current = max(1,$paged);
					$pagination_args = array(
						'base' => @esc_url(add_query_arg('paged','%#%')),
						'total' => ceil(sizeof($ask_reports_option)/$rows_per_page),
						'current' => $current,
						'show_all' => false,
						'prev_text' => '&laquo; Previous',
						'next_text' => 'Next &raquo;',
					);
					if( !empty($wp_query->query_vars['s']) )
						$pagination_args['add_args'] = array('s' => get_query_var('s'));
						
					$start = ($current - 1) * $rows_per_page;
					$end = $start + $rows_per_page;
					$end = (sizeof($ask_reports_option) < $end) ? sizeof($ask_reports_option) : $end;
					for ($i=$start;$i < $end ;++$i ) {
						$ask_reports_option_result = $ask_reports_option[$i];?>
						<div class="reports-table-item">
							<div class="report-link"><a href="<?php echo get_the_permalink($ask_reports_option_result["post_id"]);?>"><?php echo get_the_permalink($ask_reports_option_result["post_id"]);?></a></div>
							<div class="report-author">
								<?php
								if ($ask_reports_option_result["the_author"] != "") {
									if ($ask_reports_option_result["the_author"] == 1) {
										echo "Not user";
									}else {
										echo $ask_reports_option_result["the_author"];
									}
								}else {
									?><a href="<?php echo vpanel_get_user_url((int)$ask_reports_option_result["user_id"]);?>"><?php echo get_the_author_meta("display_name",(int)$ask_reports_option_result["user_id"])?></a><?php
								}
								?>
							</div>
							<div class="report-date"><?php echo human_time_diff($ask_reports_option_result["the_date"],current_time('timestamp'))." ago"?></div>
							<div class="reports-options">
								<a href="#" class="reports-view dashicons dashicons-search" attr="<?php echo $ask_reports_option_result["item_id_option"]?>"></a>
								<a href="#" attr="<?php echo $ask_reports_option_result["item_id_option"]?>" class="reports-delete dashicons dashicons-no"></a>
								<?php if ($ask_reports_option_result["report_new"] == 1) {?>
									<div title="New reports" class="reports-new dashicons dashicons-email-alt"></div>
								<?php }?>
							</div>
							<div id="reports-<?php echo $ask_reports_option_result["item_id_option"]?>" class="reports-pop">
								<div class="reports-pop-no-scroll">
									<div class="reports-pop-inner">
										<a href="#" class="reports-close dashicons dashicons-no"></a>
										<div class="clear"></div>
										<div class="reports-pop-warp">
											<div>
												<div>Message</div>
												<div><?php echo nl2br(stripslashes($ask_reports_option_result["value"]))?></div>
											</div>
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					</div><!-- End reports-table-items -->
				<?php }else {
					echo "<p>There are no reports yet</p>";
				}
				?>
			</div><!-- End reports-table -->
			<?php if (isset($pagination_args["total"]) && $pagination_args["total"] > 1) {?>
				<div class='reports-paged'>
					<?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?>
				</div><!-- End reports-paged -->
				<div class="clear"></div>
			<?php }?>
		</div><!-- End reports-padding -->
	</div><!-- End reports-warp -->
	<?php
}
/* r_answers */
function r_answers () {
	?>
	<div class="reports-warp">
		<div class="reports-head"><i class="dashicons dashicons-flag"></i>Answers Reports</div>
		<div class="reports-padding">
			<div class="reports-table">
				<div class="reports-table-head">
					<div class="report-link">Link</div>
					<div class="report-author">Author</div>
					<div class="report-date">Date</div>
					<div class="reports-options">Options</div>
				</div><!-- End reports-table-head -->
				<?php
				$rows_per_page = get_option("posts_per_page");
				$ask_option_answer = get_option("ask_option_answer");
				$ask_option_answer_array = get_option("ask_option_answer_array");
				if (is_array($ask_option_answer_array)) {
					foreach ($ask_option_answer_array as $key => $value) {
						$ask_one_option[$value] = get_option("ask_option_answer_".$value);
						$comment_no_empty = get_comment($ask_one_option[$value]["comment_id"]);
						if (!isset($comment_no_empty)) {
							unset($ask_one_option[$value]);
						}
					}
				}
				if (isset($ask_one_option) && is_array($ask_one_option) && !empty($ask_one_option)) {?>
					<div class="reports-table-items">
					<?php
					$ask_reports_option = array_reverse($ask_one_option);
					$paged = (isset($_GET["paged"])?(int)$_GET["paged"]:1);
					$current = max(1,$paged);
					$pagination_args = array(
						'base' => @esc_url(add_query_arg('paged','%#%')),
						'total' => ceil(sizeof($ask_reports_option)/$rows_per_page),
						'current' => $current,
						'show_all' => false,
						'prev_text' => '&laquo; Previous',
						'next_text' => 'Next &raquo;',
					);
					if( !empty($wp_query->query_vars['s']) )
						$pagination_args['add_args'] = array('s' => get_query_var('s'));
						
					$start = ($current - 1) * $rows_per_page;
					$end = $start + $rows_per_page;
					$end = (sizeof($ask_reports_option) < $end) ? sizeof($ask_reports_option) : $end;
					for ($i=$start;$i < $end ;++$i ) {
						$ask_reports_option_result = $ask_reports_option[$i];?>
						<div class="reports-table-item">
							<div class="report-link"><a href="<?php echo get_the_permalink($ask_reports_option_result["post_id"]);?>#comment-<?php echo $ask_reports_option_result["comment_id"]?>"><?php echo get_the_permalink($ask_reports_option_result["post_id"]);?>#comment-<?php echo $ask_reports_option_result["comment_id"]?></a></div>
							<div class="report-author">
								<?php
								if ($ask_reports_option_result["the_author"] != "") {
									if ($ask_reports_option_result["the_author"] == 1) {
										echo "Not user";
									}else {
										echo $ask_reports_option_result["the_author"];
									}
								}else {
									?><a href="<?php echo vpanel_get_user_url((int)$ask_reports_option_result["user_id"]);?>"><?php echo get_the_author_meta("display_name",(int)$ask_reports_option_result["user_id"])?></a><?php
								}
								?>
							</div>
							<div class="report-date"><?php echo human_time_diff($ask_reports_option_result["the_date"],current_time('timestamp'))." ago"?></div>
							<div class="reports-options">
								<a href="#" class="reports-view reports-answers dashicons dashicons-search" attr="<?php echo $ask_reports_option_result["item_id_option"]?>"></a>
								<a href="#" attr="<?php echo $ask_reports_option_result["item_id_option"]?>" class="reports-delete reports-answers dashicons dashicons-no"></a>
								<?php if ($ask_reports_option_result["report_new"] == 1) {?>
									<div title="New reports" class="reports-new dashicons dashicons-email-alt"></div>
								<?php }?>
							</div>
							<div id="reports-<?php echo $ask_reports_option_result["item_id_option"]?>" class="reports-pop">
								<div class="reports-pop-no-scroll">
									<div class="reports-pop-inner">
										<a href="#" class="reports-close dashicons dashicons-no"></a>
										<div class="clear"></div>
										<div class="reports-pop-warp">
											<div>
												<div>Message</div>
												<div><?php echo nl2br($ask_reports_option_result["value"])?></div>
											</div>
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					</div><!-- End reports-table-items -->
				<?php }else {
					echo "<p>There are no reports yet</p>";
				}
				?>
			</div><!-- End reports-table -->
			<?php if (isset($pagination_args["total"]) && $pagination_args["total"] > 1) {?>
				<div class='reports-paged'>
					<?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?>
				</div><!-- End reports-paged -->
				<div class="clear"></div>
			<?php }?>
		</div><!-- End reports-padding -->
	</div><!-- End reports-warp -->
	<?php
}
/* r_users */
function r_users () {
	?>
	<div class="reports-warp">
		<div class="reports-head"><i class="dashicons dashicons-flag"></i>Users Reports</div>
		<div class="reports-padding">
			<div class="reports-table">
				<div class="reports-table-head">
					<div class="report-link">Link</div>
					<div class="report-author">Author</div>
					<div class="report-date">Date</div>
					<div class="reports-options">Options</div>
				</div><!-- End reports-table-head -->
				<?php
				$rows_per_page = get_option("posts_per_page");
				$ask_option_user = get_option("ask_option_user");
				$ask_option_user_array = get_option("ask_option_user_array");
				if (is_array($ask_option_user_array)) {
					foreach ($ask_option_user_array as $key => $value) {
						$ask_one_option[$value] = get_option("ask_option_user_".$value);
						$askme_profile_url = get_author_posts_url($ask_one_option[$value]["author_id"]);
						$display_name = get_the_author_meta('display_name',$ask_one_option[$value]["author_id"]);
						if ($askme_profile_url != "" && $display_name != "") {
							$user_no_empty = true;
						}
						if (!isset($user_no_empty)) {
							unset($ask_one_option[$value]);
						}
					}
				}
				if (isset($ask_one_option) && is_array($ask_one_option) && !empty($ask_one_option)) {?>
					<div class="reports-table-items">
					<?php
					$ask_reports_option = array_reverse($ask_one_option);
					$paged = (isset($_GET["paged"])?(int)$_GET["paged"]:1);
					$current = max(1,$paged);
					$pagination_args = array(
						'base' => @esc_url(add_query_arg('paged','%#%')),
						'total' => ceil(sizeof($ask_reports_option)/$rows_per_page),
						'current' => $current,
						'show_all' => false,
						'prev_text' => '&laquo; Previous',
						'next_text' => 'Next &raquo;',
					);
					if( !empty($wp_query->query_vars['s']) )
						$pagination_args['add_args'] = array('s' => get_query_var('s'));
						
					$start = ($current - 1) * $rows_per_page;
					$end = $start + $rows_per_page;
					$end = (sizeof($ask_reports_option) < $end) ? sizeof($ask_reports_option) : $end;
					for ($i=$start;$i < $end ;++$i ) {
						$ask_reports_option_result = $ask_reports_option[$i];?>
						<div class="reports-table-item">
							<div class="report-link"><a href="<?php echo get_author_posts_url($ask_reports_option_result["author_id"]);?>"><?php echo get_author_posts_url($ask_reports_option_result["author_id"]);?></a></div>
							<div class="report-author">
								<?php
								if ($ask_reports_option_result["the_author"] != "") {
									if ($ask_reports_option_result["the_author"] == 1) {
										echo "Not user";
									}else {
										echo $ask_reports_option_result["the_author"];
									}
								}else {
									?><a href="<?php echo vpanel_get_user_url((int)$ask_reports_option_result["user_id"]);?>"><?php echo get_the_author_meta("display_name",(int)$ask_reports_option_result["user_id"])?></a><?php
								}
								?>
							</div>
							<div class="report-date"><?php echo human_time_diff($ask_reports_option_result["the_date"],current_time('timestamp'))." ago"?></div>
							<div class="reports-options">
								<a href="#" class="reports-view reports-users dashicons dashicons-search" attr="<?php echo $ask_reports_option_result["item_id_option"]?>"></a>
								<a href="#" attr="<?php echo $ask_reports_option_result["item_id_option"]?>" class="reports-delete reports-users dashicons dashicons-no"></a>
								<?php if ($ask_reports_option_result["report_new"] == 1) {?>
									<div title="New reports" class="reports-new dashicons dashicons-email-alt"></div>
								<?php }?>
							</div>
							<div id="reports-<?php echo $ask_reports_option_result["item_id_option"]?>" class="reports-pop">
								<div class="reports-pop-no-scroll">
									<div class="reports-pop-inner">
										<a href="#" class="reports-close dashicons dashicons-no"></a>
										<div class="clear"></div>
										<div class="reports-pop-warp">
											<div>
												<div>Message</div>
												<div><?php echo nl2br($ask_reports_option_result["value"])?></div>
											</div>
										</div>
										<div class="clear"></div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
					</div><!-- End reports-table-items -->
				<?php }else {
					echo "<p>There are no reports yet</p>";
				}
				?>
			</div><!-- End reports-table -->
			<?php if (isset($pagination_args["total"]) && $pagination_args["total"] > 1) {?>
				<div class='reports-paged'>
					<?php echo (paginate_links($pagination_args) != ""?paginate_links($pagination_args):"")?>
				</div><!-- End reports-paged -->
				<div class="clear"></div>
			<?php }?>
		</div><!-- End reports-padding -->
	</div><!-- End reports-warp -->
	<?php
}
/* vpanel_user_table */
function vpanel_user_table( $column ) {
	$user_meta_admin = askme_options("user_meta_admin");
	if (isset ($user_meta_admin) && is_array($user_meta_admin)) {
		$column['question'] = __('Questions','vbegy');
		if (isset($user_meta_admin["points"]) && $user_meta_admin["points"] == 1) {
			$column['points'] = __('Points','vbegy');
		}
		if (isset($user_meta_admin["phone"]) && $user_meta_admin["phone"] == 1) {
			$column['phone'] = __('Phone','vbegy');
		}
		if (isset($user_meta_admin["country"]) && $user_meta_admin["country"] == 1) {
			$column['country'] = __('Country','vbegy');
		}
		if (isset($user_meta_admin["age"]) && $user_meta_admin["age"] == 1) {
			$column['age'] = __('Age','vbegy');
		}
		if (isset($user_meta_admin["registration"]) && $user_meta_admin["registration"] == 1) {
			$column['registration'] = __('Registration date','vbegy');
		}
	}
	return $column;
}
add_filter( 'manage_users_columns', 'vpanel_user_table' );
function vpanel_user_table_row( $val, $column_name, $user_id ) {
	switch ($column_name) {
		case 'question' :
			$count_user_questions = askme_count_posts_by_user($user_id,array(ask_questions_type,ask_asked_questions_type),"publish");
			return ($count_user_questions > 0?'<a href="'.admin_url('edit.php?post_type='.ask_questions_type.'&author='.$user_id).'">':'').$count_user_questions.($count_user_questions > 0?'</a>':'');
			break;
		case 'points' :
			$points = (int)get_the_author_meta( 'points', $user_id );
			return $points;
			break;
		case 'phone' :
			return get_the_author_meta( 'phone', $user_id );
			break;
		case 'country' :
			$get_countries = vpanel_get_countries();
			$country = get_the_author_meta( 'country', $user_id );
			if ($country && isset($get_countries[$country])) {
				return $get_countries[$country];
			}else {
				return '';
			}
			break;
		case 'registration' :
			$date_format = askme_options("date_format");
			$date_format = ($date_format?$date_format:get_option("date_format"));
			$registered = get_the_author_meta( 'registered', $user_id );
			return date($date_format,strtotime($registered));
			break;
		case 'age' :
			return get_the_author_meta( 'age', $user_id );
			break;
		default:
	}
}
add_filter( 'manage_users_custom_column', 'vpanel_user_table_row', 10, 3 );
/* askme_comment_columns */
function askme_comment_columns ($columns) {
	return array_merge( $columns, array(
		'answers' => __('Answer/Comment','vbegy'),
		'best_answer' => __('Best answer?','vbegy')
	));
}
add_filter('manage_edit-comments_columns','askme_comment_columns');
function askme_comment_column ($column,$comment_ID) {
	switch ( $column ) {
		case 'answers':
			$comment_type = get_comment_meta($comment_ID,"comment_type",true);
			if (isset($comment_type) && $comment_type == "question") {
				echo __('Answer','vbegy');
			}else {
				echo __('Comment','vbegy');
			}
		break;
		case 'best_answer':
			$best_answer_comment = get_comment_meta($comment_ID,"best_answer_comment",true);
			if (isset($best_answer_comment) && $best_answer_comment == "best_answer_comment") {
				echo __('Best answer','vbegy');
			}else {
				echo '<span aria-hidden="true">—</span><span class="screen-reader-text">'.__("Not best answer","vbegy").'</span>';
			}
		break;
	}
}
add_filter('manage_comments_custom_column','askme_comment_column',10,2);
/* ask_sticky_question */
function ask_sticky_question() {?>
	<input name="sticky_question" type="hidden" value="sticky">
	<label class="switch" for="sticky-question">
		<input id="sticky-question" name="sticky" type="checkbox" value="sticky" <?php checked( is_sticky() ); ?>>
		<label for="sticky-question" data-on="<?php esc_html_e("ON","vbegy")?>" data-off="<?php esc_html_e("OFF","vbegy")?>"></label>
	</label>
	<label for="sticky-question" class="selectit"><?php _e( 'Stick this question' ) ?></label>
	<?php
}
add_action( 'admin_init', 'ask_sticky_add_meta_box' );
function ask_sticky_add_meta_box() {
	if( ! current_user_can( 'edit_others_posts' ) )
		return;
	add_meta_box( 'ask_sticky_question', __( 'Sticky' ), 'ask_sticky_question', ask_questions_type, 'side', 'high' );
}
/* Admin columns for post types */
function askme_post_columns($old_columns) {
	$columns = array();
	$columns["cb"]       = "<input type=\"checkbox\">";
	$columns["title"]    = __("Title","vbegy");
	$columns["author_p"] = __("Author","vbegy");
	$columns["categories"] = __("Categories","vbegy");
	$columns["tags"]      = __("Tags","vbegy");
	$columns["comments"] = "<span class='vers comment-grey-bubble' title='".__("Comments","vbegy")."'><span class='screen-reader-text'>".__("Comments","vbegy")."</span></span>";
	$columns["date"]     = __("Date","vbegy");
	return $columns;
}
add_filter('manage_edit-post_columns', 'askme_post_columns');

function askme_post_custom_columns($column) {
	global $post;
	switch ( $column ) {
		case 'author_p' :
			$display_name = get_the_author_meta('display_name',$post->post_author);
			if ($post->post_author > 0) {
				echo '<a href="edit.php?post_type=post&author='.$post->post_author.'">'.$display_name.'</a>';
			}else {
				echo get_post_meta($post->ID,'post_username',true);
			}
		break;
	}
}
add_action('manage_post_posts_custom_column', 'askme_post_custom_columns', 2);?>