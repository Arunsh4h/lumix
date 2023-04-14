<?php get_header();
	$blog_style        = askme_options("home_display");
	$vbegy_sidebar_all = askme_options("sidebar_layout");
	$k                 = 0;
	$search_value      = esc_js(get_query_var('search') != ""?wp_unslash(sanitize_text_field(get_query_var('search'))):wp_unslash(sanitize_text_field(get_query_var('s'))));
	$search_type       = (isset($_GET["search_type"]) && $_GET["search_type"] != ""?esc_attr($_GET["search_type"]):"");?>
	<div class="page-content page-content-search">
		<div class="boxedtitle page-title">
			<h2>
				<?php if ($search_value != "") {
					echo __("Search results for","vbegy")." ".$search_value;
				}else {
					_e("Search","vbegy");
				}?>
			</h2>
		</div>
		<?php include locate_template("includes/search.php");
	if ($search_type == "" || $search_type == "questions" || $search_type == "products" || $search_type == "posts" || $search_type == "users" || $search_type == "comments" || $search_type == "answers") {?>
		<div class="clearfix"></div>
		</div><!-- End page-content -->
	<?php }
		$hide_form = true;
		include locate_template("includes/content-search.php");
	if ($search_type != "" && $search_type != "questions" && $search_type != "products" && $search_type != "posts" && $search_type != "users" && $search_type != "comments" && $search_type != "answers") {?>
		<div class="clearfix"></div>
		</div><!-- End page-content -->
	<?php }
get_footer();?>