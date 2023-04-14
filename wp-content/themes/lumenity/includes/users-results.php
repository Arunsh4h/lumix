<?php $total_query = $wpdb->num_rows;
if (isset($query) && !empty($query)) {
	foreach ($query as $user) {
		$user = (isset($user->ID)?$user->ID:$user);
		echo ask_author($user,$user_sort);
	}
}else {
	$no_user = true;
}

if (isset($total_users) && $total_users > $total_query) {
	$current_page = max(1,$paged);
	$pagination_args = array(
		'format'    => (is_page_template("template-search.php")?'':'page/%#%/'),
		'current' => $current_page,
		'total' => $total_pages,
		'prev_text' => '<i class="fa fa-angle-left"></i>',
		'next_text' => '<i class="fa fa-angle-right"></i>',
	);
	if (!get_option('permalink_structure')) {
		$pagination_args['base'] = esc_url_raw(add_query_arg('paged','%#%'));
	}
	if (is_page_template("template-search.php")) {
		$pagination_args['format'] = '?paged=%#%';
	}
	echo '<div class="pagination">'
		.paginate_links($pagination_args).
	'</div><div class="clearfix"></div>';
}

if (isset($no_user) && $no_user == true) {
	include locate_template("includes/search-none.php");
}
?>