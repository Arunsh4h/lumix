<?php
$products_sidebar_layout = askme_options("products_sidebar_layout");
$products_sidebar = askme_options("products_sidebar");
$questions_sidebar_layout = askme_options("questions_sidebar_layout");
$questions_sidebar = askme_options("questions_sidebar");
$sidebar_filter = apply_filters("askme_sidebar_filter",false);
if ($sidebar_filter != "") {
	dynamic_sidebar(sanitize_title($sidebar_filter));
}else if (is_home()) {
    $home_page_sidebar = askme_options("sidebar_home");
    if ($home_page_sidebar == "" || $home_page_sidebar == "default") {
        dynamic_sidebar('sidebar_default');
    }else {
        dynamic_sidebar(sanitize_title($home_page_sidebar));
    }
}else if (is_author()) {
	$author_sidebar = askme_options("author_sidebar");
	$author_sidebar_layout = askme_options("author_sidebar_layout");
	if ($author_sidebar == "" || $author_sidebar == "default") {
		dynamic_sidebar('sidebar_default');
	}else {
	    dynamic_sidebar(sanitize_title($author_sidebar));
	}
}else if (is_category()) {
	$tax_id = get_query_var('cat');
	$cat_sidebar_layout = get_term_meta($tax_id,"vbegy_cat_sidebar_layout",true);
	$cat_sidebar_layout = ($cat_sidebar_layout != ""?$cat_sidebar_layout:"default");
	$cat_sidebar = get_term_meta($tax_id,"vbegy_cat_sidebar",true);
	$cat_sidebar = ($cat_sidebar != ""?$cat_sidebar:"default");
	if ($cat_sidebar == "" || $cat_sidebar == "default") {
		dynamic_sidebar('sidebar_default');
	}else {
	    dynamic_sidebar(sanitize_title($cat_sidebar));
	}
}else if (is_tax("product_cat")) {
	$tax_id = get_term_by('slug',get_query_var('term'),"product_cat");
	$tax_id = $tax_id->term_id;
	$cat_sidebar_layout = get_term_meta($tax_id,"vbegy_cat_sidebar_layout",true);
	$cat_sidebar_layout = ($cat_sidebar_layout != ""?$cat_sidebar_layout:"default");
	$cat_sidebar = get_term_meta($tax_id,"vbegy_cat_sidebar",true);
	$cat_sidebar = ($cat_sidebar != ""?$cat_sidebar:"default");
	if ($cat_sidebar_layout == "" || $cat_sidebar_layout == "default") {
		$cat_sidebar_layout = $products_sidebar_layout;
	}
	if ($cat_sidebar == "" || $cat_sidebar == "default") {
		$cat_sidebar = $products_sidebar;
	}
	if ($cat_sidebar == "" || $cat_sidebar == "default") {
		dynamic_sidebar('sidebar_default');
	}else {
	    dynamic_sidebar(sanitize_title($cat_sidebar));
	}
}else if (is_tax("product_tag") || is_post_type_archive("product")) {
	if ($products_sidebar_layout != "full") {
		if ($products_sidebar == "" || $products_sidebar == "default") {
			dynamic_sidebar('sidebar_default');
		}else {
		    dynamic_sidebar(sanitize_title($products_sidebar));
		}
	}
}else if (is_tax(ask_question_category)) {
	$tax_id = get_term_by('slug',get_query_var('term'),ask_question_category);
	$tax_id = $tax_id->term_id;
	$cat_sidebar_layout = get_term_meta($tax_id,"vbegy_cat_sidebar_layout",true);
	$cat_sidebar_layout = ($cat_sidebar_layout != ""?$cat_sidebar_layout:"default");
	$cat_sidebar = get_term_meta($tax_id,"vbegy_cat_sidebar",true);
	$cat_sidebar = ($cat_sidebar != ""?$cat_sidebar:"default");
	if ($cat_sidebar_layout == "" || $cat_sidebar_layout == "default") {
		$cat_sidebar_layout = $questions_sidebar_layout;
	}
	if ($cat_sidebar == "" || $cat_sidebar == "default") {
		$cat_sidebar = $questions_sidebar;
	}
	if ($cat_sidebar == "" || $cat_sidebar == "default") {
		dynamic_sidebar('sidebar_default');
	}else {
	    dynamic_sidebar(sanitize_title($cat_sidebar));
	}
}else if (is_tax(ask_question_tags) || is_post_type_archive(ask_questions_type)) {
	if ($questions_sidebar == "" || $questions_sidebar == "default") {
		dynamic_sidebar('sidebar_default');
	}else {
	    dynamic_sidebar(sanitize_title($questions_sidebar));
	}
}else if (is_single() or is_page()) {
	$vbegy_what_sidebar = askme_post_meta('vbegy_what_sidebar','select',$post->ID);
	$sidebar_post = askme_post_meta('vbegy_sidebar','radio',$post->ID);
	if (is_singular("product") && ($vbegy_what_sidebar == "" || $vbegy_what_sidebar == "default") && ($sidebar_post == "" || $sidebar_post == "default")) {
		$vbegy_what_sidebar = $products_sidebar;
		dynamic_sidebar(sanitize_title($vbegy_what_sidebar));
		if ($vbegy_what_sidebar == "" || $vbegy_what_sidebar == "default") {
			$else_sidebar = askme_options("else_sidebar");
			if (($else_sidebar == "" || $else_sidebar == "default")) {
			    dynamic_sidebar('sidebar_default');
			}else {
			    dynamic_sidebar(sanitize_title($else_sidebar));
			}
		}
	}else if (is_singular("product") && ($vbegy_what_sidebar == "" || $vbegy_what_sidebar == "default")) {
		if ($products_sidebar_layout != "full") {
			if ($sidebar_post != "" && $sidebar_post != "default") {
				if ($vbegy_what_sidebar != "" && $vbegy_what_sidebar != "default") {
					$vbegy_what_sidebar = $products_sidebar;
					dynamic_sidebar(sanitize_title($vbegy_what_sidebar));
				}else {
					dynamic_sidebar('sidebar_default');
				}
			}else {
				dynamic_sidebar('sidebar_default');
			}
		}
	}else if ((is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) && ($vbegy_what_sidebar == "" || $vbegy_what_sidebar == "default") && ($sidebar_post == "" || $sidebar_post == "default")) {
		$vbegy_what_sidebar = $questions_sidebar;
		dynamic_sidebar(sanitize_title($vbegy_what_sidebar));
		if ($vbegy_what_sidebar == "" || $vbegy_what_sidebar == "default") {
			$else_sidebar = askme_options("else_sidebar");
			if (($else_sidebar == "" || $else_sidebar == "default")) {
			    dynamic_sidebar('sidebar_default');
			}else {
			    dynamic_sidebar(sanitize_title($else_sidebar));
			}
		}
	}else if ((is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) && ($vbegy_what_sidebar == "" || $vbegy_what_sidebar == "default")) {
		if ($questions_sidebar_layout != "full") {
			if ($sidebar_post != "" && $sidebar_post != "default") {
				$vbegy_what_sidebar = $questions_sidebar;
				dynamic_sidebar(sanitize_title($vbegy_what_sidebar));
			}
		}
	}else if ($sidebar_post != "full") {
	    if (isset($vbegy_what_sidebar) && $vbegy_what_sidebar != "default" && $vbegy_what_sidebar != "") {
		    dynamic_sidebar(sanitize_title($vbegy_what_sidebar));
	    }else {
	    	if ($vbegy_what_sidebar == "" || $vbegy_what_sidebar == "default") {
	    		$else_sidebar = askme_options("else_sidebar");
	    		if (($else_sidebar == "" || $else_sidebar == "default")) {
	    		    dynamic_sidebar('sidebar_default');
	    		}else {
	    		    dynamic_sidebar(sanitize_title($else_sidebar));
	    		}
	    	}
	    }
    }
}else  {
    $else_sidebar = askme_options("else_sidebar");
    if (($else_sidebar == "" || $else_sidebar == "default")) {
        dynamic_sidebar('sidebar_default');
    }else {
        dynamic_sidebar(sanitize_title($else_sidebar));
    }
}
?>