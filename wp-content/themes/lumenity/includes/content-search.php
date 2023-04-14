<?php
$search_type     = (isset($_GET["search_type"]) && $_GET["search_type"] != ""?esc_attr($_GET["search_type"]):"");
$post_pagination = 'pagination';
$search_attrs    = askme_options("search_attrs");

if ( have_posts() ) :
	include locate_template("includes/search.php");
endif;

if (is_page_template("template-search.php")) {
	$template_search = true;
}

if (($search_type == "answers" && isset($search_attrs["answers"]) && isset($search_attrs["answers"]["value"]) && $search_attrs["answers"]["value"] == "answers") || ($search_type == "comments" && isset($search_attrs["comments"]) && isset($search_attrs["comments"]["value"]) && $search_attrs["comments"]["value"] == "comments")) {
	include locate_template("includes/comments.php");
}else if ($search_type == "users" && isset($search_attrs["users"]) && isset($search_attrs["users"]["value"]) && $search_attrs["users"]["value"] == "users") {
	include locate_template("includes/users.php");
}else if (($search_type == ask_question_category && isset($search_attrs[ask_question_category]) && isset($search_attrs[ask_question_category]["value"]) && $search_attrs[ask_question_category]["value"] == ask_question_category) || ($search_type == "product_cat" && isset($search_attrs["product_cat"]) && isset($search_attrs["product_cat"]["value"]) && $search_attrs["product_cat"]["value"] == "product_cat") || ($search_type == "category" && isset($search_attrs["category"]) && isset($search_attrs["category"]["value"]) && $search_attrs["category"]["value"] == "category")) {
	include locate_template("includes/categories.php");
}else if (($search_type == ask_question_tags && isset($search_attrs[ask_question_tags]) && isset($search_attrs[ask_question_tags]["value"]) && $search_attrs[ask_question_tags]["value"] == ask_question_tags) || ($search_type == "product_tag" && isset($search_attrs["product_tag"]) && isset($search_attrs["product_tag"]["value"]) && $search_attrs["product_tag"]["value"] == "product_tag") || ($search_type == "post_tag" && isset($search_attrs["post_tag"]) && isset($search_attrs["post_tag"]["value"]) && $search_attrs["post_tag"]["value"] == "post_tag")) {
	include locate_template("includes/tags.php");
}else {
	if ($search_value != "") {
		if ($search_type == "products" && isset($search_attrs["products"]) && isset($search_attrs["products"]["value"]) && $search_attrs["products"]["value"] == "products") {
			$post_type_array = array('product');
		}else if ($search_type == "posts" && isset($search_attrs["posts"]) && isset($search_attrs["posts"]["value"]) && $search_attrs["posts"]["value"] == "posts") {
			$post_type_array = array('post');
		}else {
			$post_type_array = array(ask_questions_type);
		}

		$block_users = askme_options("block_users");
		$author__not_in = array();
		if ($block_users == 1) {
			$get_current_user_id = get_current_user_id();
			if ($get_current_user_id > 0) {
				$get_block_users = get_user_meta($get_current_user_id,"askme_block_users",true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("author__not_in" => $get_block_users);
				}
			}
		}
		
		$args = array_merge($author__not_in,array('s' => $search_value,'paged' => $paged,'post_type' => $post_type_array));
		$args = apply_filters("askme_filter_content_search_query",$args,$paged,$search_value,$post_type_array);
		query_posts($args);
		if ($search_type == "posts") {
			$blog_style = askme_options("home_display");
		}
		if ($search_type == "products") {
			do_action('woocommerce_archive_description');
			if (have_posts()) : ?>
				<div class="woocommerce-page">
				<ul class = "products woocommerce_products products_grid clearfix">
					<?php while (have_posts()) : the_post();
						woocommerce_get_template_part('content','product');
					endwhile;?>
				</ul>
				</div>
				<?php do_action('woocommerce_after_shop_loop');
			elseif (!woocommerce_product_subcategories(array('before' => woocommerce_product_loop_start(false), 'after' => woocommerce_product_loop_end(false)))) :
				woocommerce_get_template('loop/no-products-found.php');
			endif;
		}else {
			get_template_part("loop".($search_type == "posts"?"":"-question"));
			vpanel_pagination();
		}
		wp_reset_query();
	}
}?>