jQuery(document).ready( function($) {
	jQuery(window).on("load",function() {
		/* Templates */
		if (jQuery("#page_template").length) {
			var page_template = jQuery("#page_template");
		}else {
			var page_template = jQuery(".editor-page-attributes__template select,.components-panel__body:not(.edit-post-post-status) .components-select-control__input,#inspector-select-control-0");
		}
		page_template.on("change",function () {
			var page_template_val = jQuery(this).val();
			if (jQuery(".optionsframework-content .nav-tab[data-template]") !== undefined && jQuery(".nav-tab[data-template]") !== false) {
				if (jQuery(".nav-tab[data-template='"+page_template_val+"']").length) {
					jQuery("#optionsframework > .group").hide();
					jQuery(".optionsframework-content .nav-tab").removeClass('nav-tab-active');
					
					jQuery(".optionsframework-content .nav-tab[data-template],#optionsframework > .group[data-template]").hide().addClass("hide");
					
					jQuery(".optionsframework-content .nav-tab[data-template='"+page_template_val+"']").addClass('nav-tab-active');
					jQuery(".optionsframework-content .nav-tab[data-template='"+page_template_val+"'],#optionsframework > .group[data-template='"+page_template_val+"']").show().removeClass('hide');
				}else {
					jQuery(".optionsframework-content .nav-tab[data-template],#optionsframework > .group[data-template]").hide().addClass("hide").removeClass('nav-tab-active');
					if (jQuery(".optionsframework-content .nav-tab.nav-tab-active").length) {
						/* Has active tab */
					}else {
						jQuery('#optionsframework > .group:not(.hide):first-child').animate({
							opacity: 'show',
							height: 'show'
						}, 200, function() {
							jQuery(this).removeClass('hide');
						});
						jQuery('.optionsframework-content .nav-tab-wrapper a:not(.hide):first-child').addClass('nav-tab-active');
					}
				}
			}else {
				jQuery('#optionsframework > .group:not(.hide):first-child').animate({
					opacity: 'show',
					height: 'show'
				}, 200, function() {
					jQuery(this).removeClass('hide');
				});
				jQuery('.optionsframework-content .nav-tab-wrapper a:not(.hide):first-child').addClass('nav-tab-active');
			}
		});
	});
	
	/* Home background */
	if (jQuery("#"+more_ajax.vpanel_name+"-background_type-custom_background:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-background_type-patterns:checked").length == 0) {
		jQuery("#section-custom_background").slideDown(500);
		jQuery("#section-full_screen_background").slideDown(500);
		jQuery("#section-background_color").hide(10);
		jQuery("#section-background_pattern").hide(10);
	}else if (jQuery("#"+more_ajax.vpanel_name+"-background_type-patterns:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-background_type-custom_background:checked").length == 0) {
		jQuery("#section-background_color").slideDown(500);
		jQuery("#section-background_pattern").slideDown(500);
		jQuery("#section-custom_background").hide(10);
		jQuery("#section-full_screen_background").hide(10);
	}
	jQuery("#"+more_ajax.vpanel_name+"-background_type-custom_background").click(function () {
		jQuery("#section-custom_background").slideDown(500);
		jQuery("#section-full_screen_background").slideDown(500);
		jQuery("#section-background_pattern").slideUp(500);
		jQuery("#section-background_color").slideUp(500);
	});
	jQuery("#"+more_ajax.vpanel_name+"-background_type-patterns").click(function () {
		jQuery("#section-custom_background").slideUp(500);
		jQuery("#section-full_screen_background").slideUp(500);
		jQuery("#section-background_pattern").slideDown(500);
		jQuery("#section-background_color").slideDown(500);
	});
	
	/* User background */
	if (jQuery("#"+more_ajax.vpanel_name+"-author_background_type-custom_background:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-author_background_type-patterns:checked").length == 0) {
		jQuery("#section-author_custom_background").slideDown(500);
		jQuery("#section-author_full_screen_background").slideDown(500);
		jQuery("#section-author_background_color").hide(10);
		jQuery("#section-author_background_pattern").hide(10);
	}else if (jQuery("#"+more_ajax.vpanel_name+"-author_background_type-patterns:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-author_background_type-custom_background:checked").length == 0) {
		jQuery("#section-author_background_color").slideDown(500);
		jQuery("#section-author_background_pattern").slideDown(500);
		jQuery("#section-author_custom_background").hide(10);
		jQuery("#section-author_full_screen_background").hide(10);
	}
	jQuery("#"+more_ajax.vpanel_name+"-author_background_type-custom_background").click(function () {
		jQuery("#section-author_custom_background").slideDown(500);
		jQuery("#section-author_full_screen_background").slideDown(500);
		jQuery("#section-author_background_pattern").slideUp(500);
		jQuery("#section-author_background_color").slideUp(500);
	});
	jQuery("#"+more_ajax.vpanel_name+"-author_background_type-patterns").click(function () {
		jQuery("#section-author_custom_background").slideUp(500);
		jQuery("#section-author_full_screen_background").slideUp(500);
		jQuery("#section-author_background_pattern").slideDown(500);
		jQuery("#section-author_background_color").slideDown(500);
	});
	
	/* Questions background */
	if (jQuery("#"+more_ajax.vpanel_name+"-questions_background_type-custom_background:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-questions_background_type-patterns:checked").length == 0) {
		jQuery("#section-questions_custom_background").slideDown(500);
		jQuery("#section-questions_full_screen_background").slideDown(500);
		jQuery("#section-questions_background_color").hide(10);
		jQuery("#section-questions_background_pattern").hide(10);
	}else if (jQuery("#"+more_ajax.vpanel_name+"-questions_background_type-patterns:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-questions_background_type-custom_background:checked").length == 0) {
		jQuery("#section-questions_background_color").slideDown(500);
		jQuery("#section-questions_background_pattern").slideDown(500);
		jQuery("#section-questions_custom_background").hide(10);
		jQuery("#section-questions_full_screen_background").hide(10);
	}
	jQuery("#"+more_ajax.vpanel_name+"-questions_background_type-custom_background").click(function () {
		jQuery("#section-questions_custom_background").slideDown(500);
		jQuery("#section-questions_full_screen_background").slideDown(500);
		jQuery("#section-questions_background_pattern").slideUp(500);
		jQuery("#section-questions_background_color").slideUp(500);
	});
	jQuery("#"+more_ajax.vpanel_name+"-questions_background_type-patterns").click(function () {
		jQuery("#section-questions_custom_background").slideUp(500);
		jQuery("#section-questions_full_screen_background").slideUp(500);
		jQuery("#section-questions_background_pattern").slideDown(500);
		jQuery("#section-questions_background_color").slideDown(500);
	});
	
	/* Products background */
	if (jQuery("#"+more_ajax.vpanel_name+"-products_background_type-custom_background:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-products_background_type-patterns:checked").length == 0) {
		jQuery("#section-products_custom_background").slideDown(500);
		jQuery("#section-products_full_screen_background").slideDown(500);
		jQuery("#section-products_background_color").hide(10);
		jQuery("#section-products_background_pattern").hide(10);
	}else if (jQuery("#"+more_ajax.vpanel_name+"-products_background_type-patterns:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-products_background_type-custom_background:checked").length == 0) {
		jQuery("#section-products_background_color").slideDown(500);
		jQuery("#section-products_background_pattern").slideDown(500);
		jQuery("#section-products_custom_background").hide(10);
		jQuery("#section-products_full_screen_background").hide(10);
	}
	jQuery("#"+more_ajax.vpanel_name+"-products_background_type-custom_background").click(function () {
		jQuery("#section-products_custom_background").slideDown(500);
		jQuery("#section-products_full_screen_background").slideDown(500);
		jQuery("#section-products_background_pattern").slideUp(500);
		jQuery("#section-products_background_color").slideUp(500);
	});
	jQuery("#"+more_ajax.vpanel_name+"-products_background_type-patterns").click(function () {
		jQuery("#section-products_custom_background").slideUp(500);
		jQuery("#section-products_full_screen_background").slideUp(500);
		jQuery("#section-products_background_pattern").slideDown(500);
		jQuery("#section-products_background_color").slideDown(500);
	});
	
	/* Categories Design */
	if (jQuery(".cat_background_type:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-background_type-patterns:checked").length == 0) {
		jQuery("#section-custom_background").slideDown(500);
		jQuery("#section-full_screen_background").slideDown(500);
		jQuery("#section-background_color").hide(10);
		jQuery("#section-background_pattern").hide(10);
	}else if (jQuery("#"+more_ajax.vpanel_name+"-background_type-patterns:checked").length > 0 && jQuery("#"+more_ajax.vpanel_name+"-background_type-custom_background:checked").length == 0) {
		jQuery("#section-background_color").slideDown(500);
		jQuery("#section-background_pattern").slideDown(500);
		jQuery("#section-custom_background").hide(10);
		jQuery("#section-full_screen_background").hide(10);
	}
	jQuery("#"+more_ajax.vpanel_name+"-background_type-custom_background").click(function () {
		jQuery("#section-custom_background").slideDown(500);
		jQuery("#section-full_screen_background").slideDown(500);
		jQuery("#section-background_pattern").slideUp(500);
		jQuery("#section-background_color").slideUp(500);
	});
	jQuery("#"+more_ajax.vpanel_name+"-background_type-patterns").click(function () {
		jQuery("#section-custom_background").slideUp(500);
		jQuery("#section-full_screen_background").slideUp(500);
		jQuery("#section-background_pattern").slideDown(500);
		jQuery("#section-background_color").slideDown(500);
	});
	
	/* Author Sidebar */
	var author_sidebar_layout = jQuery("#section-author_sidebar_layout input[type='radio']:checked").val();
	if (author_sidebar_layout == "full") {
		jQuery("#section-author_sidebar").hide(10);
	}else {
		jQuery("#section-author_sidebar").show(10);
	}
	
	jQuery("#section-author_sidebar_layout img").click(function () {
		var img_this = jQuery(this);
		var author_sidebar_layout_c = img_this.prev().text();
		if (author_sidebar_layout_c == "full") {
			jQuery("#section-author_sidebar").slideUp(500);
		}else {
			jQuery("#section-author_sidebar").slideDown(500);
		}
	});
	
	/* Meta box */
	
	jQuery(".vpanel_checkbox").each(function () {
		var vpanel_checkbox = jQuery(this);
		if (vpanel_checkbox.length > 0) {
			vpanel_checkbox.parent().addClass("vpanel_checkbox_input");
		}
	});
	
	var builder_rating_warp = jQuery("#builder_rating_warp").html();
	jQuery("#builder_rating_warp").remove();
	jQuery("#vbegy_ratings_post").html(builder_rating_warp);
	if (jQuery("#vbegy_ratings_post").length > 0) {
		jQuery("#vbegy_ratings_post ul").sortable({placeholder: "ui-state-highlight"});
	}
	
	if (jQuery("#vbegy_post_display_b").val() == "multiple_categories") {
		jQuery("label[for='vbegy_post_categories_b']").parent().parent().show(10);
		jQuery("label[for='vbegy_post_single_category_b'],label[for='vbegy_post_posts_b']").parent().parent().hide(10);
	}else if (jQuery("#vbegy_post_display_b").val() == "single_category") {
		jQuery("label[for='vbegy_post_single_category_b']").parent().parent().show(10);
		jQuery("label[for='vbegy_post_categories_b'],label[for='vbegy_post_posts_b']").parent().parent().hide(10);
	}else if (jQuery("#vbegy_post_display_b").val() == "posts") {
		jQuery("label[for='vbegy_post_posts_b']").parent().parent().show(10);
		jQuery("label[for='vbegy_post_categories_b'],label[for='vbegy_post_single_category_b']").parent().parent().hide(10);
	}else {
		jQuery("label[for='vbegy_post_single_category_b'],label[for='vbegy_post_categories_b'],label[for='vbegy_post_posts_b']").parent().parent().hide(10);
	}
	
	jQuery("#vbegy_post_display_b").change(function () {
		if (jQuery(this).val() == "multiple_categories") {
			jQuery("label[for='vbegy_post_categories_b']").parent().parent().slideDown(500);
			jQuery("label[for='vbegy_post_single_category_b'],label[for='vbegy_post_posts_b']").parent().parent().slideUp(500);
		}else if (jQuery(this).val() == "single_category") {
			jQuery("label[for='vbegy_post_single_category_b']").parent().parent().slideDown(500);
			jQuery("label[for='vbegy_post_categories_b'],label[for='vbegy_post_posts_b']").parent().parent().slideUp(500);
		}else if (jQuery(this).val() == "posts") {
			jQuery("label[for='vbegy_post_posts_b']").parent().parent().slideDown(500);
			jQuery("label[for='vbegy_post_categories_b'],label[for='vbegy_post_single_category_b']").parent().parent().slideUp(500);
		}else {
			jQuery("label[for='vbegy_post_single_category_b'],label[for='vbegy_post_categories_b'],label[for='vbegy_post_posts_b']").parent().parent().slideUp(500);
		}
	});
	
	var video_description = jQuery("#vpanel_video_description:checked").length;
	if (video_description == 1) {
		jQuery(".video_description").slideDown(300);
	}else {
		jQuery(".video_description").slideUp(300);
	}
	
	jQuery("#vpanel_video_description").click(function () {
		var video_description_c = jQuery("#vpanel_video_description:checked").length;
		if (video_description_c == 1) {
			jQuery(".video_description").slideDown(300);
		}else {
			jQuery(".video_description").slideUp(300);
		}
	});
	
	var custom_sections = jQuery("#vbegy_custom_sections:checked").length;
	if (custom_sections == 1) {
		jQuery("#sort-sections").show(10);
	}else {
		jQuery("#sort-sections").hide(10);
	}
	jQuery("#vbegy_custom_sections").click(function () {
		var custom_sections = jQuery("#vbegy_custom_sections:checked").length;
		if (custom_sections == 1) {
			jQuery("#sort-sections").slideDown(500);
		}else {
			jQuery("#sort-sections").slideUp(500);
		}
	});
	
	/* Categories Design */
	
	jQuery(".cat_name h4").after('<a class="cats-toggle-open">+</a><a class="cats-toggle-close">-</a>');
	
	jQuery(document).on("click",".cats-toggle-open",function () {
		jQuery(this).parent().next(".warp_cat_name").slideToggle(300);
		jQuery(this).css("display","none");
		jQuery(this).parent().find(".cats-toggle-close").css("display","block");
    });

	jQuery(document).on("click",".cats-toggle-close",function () {
		jQuery(this).parent().next(".warp_cat_name").slideToggle("fast");
		jQuery(this).css("display","none");
		jQuery(this).parent().find(".cats-toggle-open").css("display","block");
    });
    
    if (jQuery("#vbegy_custom_featured_image_size").length) {
    	if (jQuery("#vbegy_custom_featured_image_size:checked").val() == 1) {
    		jQuery("label[for='vbegy_featured_image_width']").parent().parent().show(10);
    		jQuery("label[for='vbegy_featured_image_height']").parent().parent().show(10);
    	}else {
    		jQuery("label[for='vbegy_featured_image_width']").parent().parent().hide(10);
    		jQuery("label[for='vbegy_featured_image_height']").parent().parent().hide(10);
    	}
    	jQuery("#vbegy_custom_featured_image_size").click(function () {
    		if (jQuery("#vbegy_custom_featured_image_size:checked").val() == 1) {
    			jQuery("label[for='vbegy_featured_image_width']").parent().parent().slideDown(500);
    			jQuery("label[for='vbegy_featured_image_height']").parent().parent().slideDown(500);
    		}else {
    			jQuery("label[for='vbegy_featured_image_width']").parent().parent().slideUp(500);
    			jQuery("label[for='vbegy_featured_image_height']").parent().parent().slideUp(500);
    		}
    	});
    }

});