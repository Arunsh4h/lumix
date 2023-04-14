<?php global $wpdb;
if (is_page() && !is_page_template("template-search.php")) {
	$user_group = askme_post_meta('vbegy_user_group','type=checkbox_list',$post->ID);
	$user_sort  = askme_post_meta('vbegy_user_sort','type=select',$post->ID);
	$user_order = askme_post_meta('vbegy_user_order','type=radio',$post->ID);
	$number     = askme_post_meta('vbegy_users_per_page','type=text',$post->ID);
	$number     = (isset($number) && $number > 0?$number:apply_filters('vbegy_users_per_page',get_option('posts_per_page')));
}else {
	$user_sort  = (isset($_GET["user_filter"]) && $_GET["user_filter"] != ""?esc_html($_GET["user_filter"]):(isset($user_sort)?$user_sort:""));
	$user_order = "DESC";
	$number     = askme_options("users_per_page");
	$number     = (isset($number) && $number > 0?$number:apply_filters('users_per_page',get_option('posts_per_page')));
}

$author__not_in = array();
$block_users = askme_options("block_users");
if ($block_users == 1) {
	$user_id = get_current_user_id();
	if ($user_id > 0) {
		$get_block_users = get_user_meta($user_id,"askme_block_users",true);
		if (is_array($get_block_users) && !empty($get_block_users)) {
			$author__not_in = $get_block_users;
		}
	}
}
$block_users_sql = (isset($get_block_users) && is_array($get_block_users) && !empty($get_block_users)?" AND $wpdb->users.ID NOT IN (".implode(",",$get_block_users).") ":"");

$active_points  = askme_options("active_points");
$paged          = askme_paged();
$offset         = ($paged -1) * $number;

$meta_key_array = $all_role_array = array();
$implode_array  = "";
$capabilities   = $wpdb->get_blog_prefix(get_current_blog_id()) . 'capabilities';
if (isset($user_group) && is_array($user_group) && !empty($user_group)) {
	foreach ($user_group as $role => $name) {
		if ($name !== '0') {
			$all_role_array[] = $role;
			$meta_key_array[] = "( $wpdb->usermeta.meta_key = '$capabilities' AND $wpdb->usermeta.meta_value RLIKE '$role' )";
		}
	}
	$implode_array = "AND (".implode(" OR ",$meta_key_array).")";
}
$user_sort = (isset($_GET["user_filter"]) && $_GET["user_filter"] != ""?esc_html($_GET["user_filter"]):(isset($user_sort) && $user_sort != ""?$user_sort:"user_registered"));

if (!isset($not_get_result_page)) {
	$search_value = esc_js(get_query_var('search') != ""?wp_unslash(sanitize_text_field(get_query_var('search'))):wp_unslash(sanitize_text_field(get_query_var('s'))));
}
$name_array = preg_split("/[\s,]+/", $search_value);
if (isset($search_value) && $search_value != "") {
	$search_args = " AND (( $wpdb->users.user_login RLIKE '$search_value' OR $wpdb->users.user_nicename RLIKE '$search_value') OR ( ( $wpdb->usermeta.meta_key = 'user_login' AND $wpdb->usermeta.meta_value RLIKE '$search_value' ) OR ( $wpdb->usermeta.meta_key = 'display_name' AND $wpdb->usermeta.meta_value RLIKE '$search_value' ) OR ( $wpdb->usermeta.meta_key = 'user_nicename' AND $wpdb->usermeta.meta_value RLIKE '$search_value' ) OR ( $wpdb->usermeta.meta_key = 'first_name' AND $wpdb->usermeta.meta_value RLIKE '".(isset($name_array[0]) && $name_array[0] != ""?$name_array[0]:$search_value)."' ) OR ( $wpdb->usermeta.meta_key = 'last_name' AND $wpdb->usermeta.meta_value RLIKE '".(isset($name_array[0]) && $name_array[0] != ""?$name_array[0]:$search_value)."' ) ) ) ";
	$implode_array = " ";
}else {
	$search_args = " ";
}

if ($user_sort == "post_count" || $user_sort == "question_count" || $user_sort == "answers" || $user_sort == "comments") {
	if ($user_sort == "question_count" || $user_sort == "answers") {
		$question_custom_post_type = " AND ($wpdb->posts.post_type = '".ask_questions_type."' OR $wpdb->posts.post_type = '".ask_asked_questions_type."')";
	}else {
		$question_custom_post_type = " AND $wpdb->posts.post_type = 'post'";
	}
	if ($user_sort == "post_count" || $user_sort == "question_count") {
		$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id ) LEFT OUTER JOIN ( SELECT post_author, COUNT(*) as post_count FROM $wpdb->posts WHERE ( ( post_status = 'publish' OR post_status = 'private' ) ".$question_custom_post_type.") GROUP BY post_author ) p ON ($wpdb->users.ID = p.post_author) WHERE %s=1".$search_args.$implode_array.$block_users_sql,1);
	}else {
		$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id ) LEFT OUTER JOIN ( SELECT user_id, COUNT(*) as total FROM $wpdb->comments INNER JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID ) WHERE ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' ) ".$answer_custom_post_type." GROUP BY user_id ) c ON ($wpdb->users.ID = c.user_id) WHERE %s=1".$search_args.$implode_array.$block_users_sql,1);
	}
	
	$users = $wpdb->get_results($query);
	$total_users = $wpdb->num_rows;
	$total_pages = ceil($total_users/$number);
	
	if ($user_sort == "post_count" || $user_sort == "question_count") {
		$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id ) LEFT OUTER JOIN ( SELECT post_author, COUNT(*) as post_count FROM $wpdb->posts WHERE ( ( post_status = 'publish' OR post_status = 'private' )  ".$question_custom_post_type.") GROUP BY post_author ) p ON ($wpdb->users.ID = p.post_author) WHERE %s=1".$search_args.$implode_array.$block_users_sql." ORDER BY post_count $user_order limit $offset,$number",1);
	}else {
		$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ( $wpdb->users.ID = $wpdb->usermeta.user_id ) LEFT OUTER JOIN ( SELECT user_id, COUNT(*) as total FROM $wpdb->comments INNER JOIN $wpdb->posts ON ( $wpdb->comments.comment_post_ID = $wpdb->posts.ID ) WHERE ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private' ) ".$answer_custom_post_type." GROUP BY user_id ) c ON ($wpdb->users.ID = c.user_id) WHERE %s=1".$search_args.$implode_array.$block_users_sql." ORDER BY total $user_order limit $offset,$number",1);
	}
	$query = $wpdb->get_results($query);
}else if (($user_sort == "points" && $active_points == 1) || $user_sort == "the_best_answer") {
	$args = array(
		'role__in'       => (isset($all_role_array) && is_array($all_role_array)?$all_role_array:array()),
		'meta_query'     => ($search_value != ""?array(
								'relation' => 'AND',
								'points_order' => array("key" => $user_sort,"value" => 0,"compare" => ">"),
								array(
									'relation' => 'OR',
									array("key" => "display_name","value" => $search_value,"compare" => "RLIKE"),
									array("key" => "nickname","value" => $search_value,"compare" => "RLIKE"),
									array("key" => "user_login","value" => $search_value,"compare" => "RLIKE"),
									array("key" => "first_name","value" => $search_value,"compare" => "RLIKE"),
									array("key" => "last_name","value" => $search_value,"compare" => "RLIKE")
								)
							):array(array("key" => $user_sort,"value" => 0,"compare" => ">"))),
		'orderby'        => 'meta_value_num',
		'order'          => $user_order,
		'offset'         => $offset,
		'search'         => ($search_value != ""?'*'.$search_value.'*':''),
		'search_columns' => ($search_value != ""?array('ID','user_login','user_nicename','user_email','user_url','display_name'):array()),
		'number'         => $number,
		'fields'         => 'ID',
		'exclude'        => $author__not_in
	);
	
	$query = new WP_User_Query($args);
	$total_query = $query->get_total();
	$total_pages = ceil($total_query/$number);
	$query = $query->get_results();
}else {
	if ($user_sort != "user_registered" && $user_sort != "display_name" && $user_sort != "ID") {
		$user_sort = "user_registered";
	}
	$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) WHERE %s=1".$search_args.$implode_array.$block_users_sql,1);
	$users = $wpdb->get_results($query);
	
	$total_users = $wpdb->num_rows;
	$total_pages = ceil($total_users/$number);
	$query = $wpdb->prepare("SELECT DISTINCT SQL_CALC_FOUND_ROWS $wpdb->users.ID FROM $wpdb->users INNER JOIN $wpdb->usermeta ON ($wpdb->users.ID = $wpdb->usermeta.user_id) WHERE %s=1".$search_args.$implode_array.$block_users_sql." ORDER BY ".$user_sort." $user_order limit $offset,$number",1);
	$query = $wpdb->get_results($query);
}

if (isset($not_get_result_page) && $not_get_result_page == true) {
	if (($user_sort == "points" && $active_points == 1) || $user_sort == "the_best_answer") {
		$total_query = $query->get_total();
		$total_pages = ceil($total_query/$number);
	}else {
		$total_query = $wpdb->num_rows;
	}
	if (isset($query) && !empty($query)) {
		foreach ($query as $user) {
			$k_search++;
			if ($search_result_number >= $k_search) {
				$user = (isset($user->ID)?$user->ID:$user);
				$your_avatar = get_the_author_meta(askme_avatar_name(),$user);
				$display_name = get_the_author_meta('display_name',$user);
				echo '<li>
					<a class="get-results" href="'.vpanel_get_user_url($user).'" title="'.$display_name.'">
						'.askme_user_avatar($your_avatar,20,20,$user,$display_name).'
					</a>
					<a href="'.vpanel_get_user_url($user).'" title="'.$display_name.'">'.str_ireplace($search_value,"<strong>".$search_value."</strong>",$display_name).'</a>
				</li>';
			}else {
				echo "<li><a href='".esc_url(add_query_arg(array("search" => $search_value,"search_type" => $search_type),(isset($search_page) && $search_page != ""?get_page_link($search_page):"")))."'>".__("View all results.","vbegy")."</a></li>";
				exit;
			}
		}
	}else {
		echo "<li class='no-search-result'>".__("No results found.","vbegy")."</li>";
	}
}else {
	include locate_template("includes/users-results.php");
}?>