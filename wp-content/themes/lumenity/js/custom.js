jQuery(document).ready(function($) {

	var is_RTL  = jQuery('body').hasClass('rtl')?true:false;
	
	/* Menu */
	
	jQuery(".navigation ul li ul").parent("li").addClass("parent-list");
	jQuery(".parent-list").find("a:first").append(" <span class='menu-nav-arrow'><i class='icon-angle-down'></i></span>");
	
	jQuery(".navigation ul a").removeAttr("title");
	jQuery(".navigation ul ul").css({display: "none"});
	jQuery(".navigation ul li").each(function() {	
		var sub_menu = jQuery(this).find("ul:first");
		jQuery(this).hover(function() {	
			sub_menu.stop().css({overflow:"hidden", height:"auto", display:"none", paddingTop:0}).slideDown(250, function() {
				jQuery(this).css({overflow:"visible", height:"auto"});
			});	
		},function() {	
			sub_menu.stop().slideUp(50, function() {	
				jQuery(this).css({overflow:"hidden", display:"none"});
			});
		});	
	});
	
	/* Header fixed */
	
	var aboveHeight   = jQuery("#header").outerHeight();
	var fixed_enabled = jQuery("#wrap").hasClass("fixed-enabled");
	if(fixed_enabled){
		jQuery(window).scroll(function(){
			if(jQuery(window).scrollTop() > aboveHeight) {
				jQuery("#header").css({"top":"0"}).addClass("fixed-nav");
			}else{
				jQuery("#header").css({"top":"auto"}).removeClass("fixed-nav");
			}
		});
	}else {
		jQuery("#header").removeClass("fixed-nav");
	}
	
	/* Mobile */
	
	if (navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || 
		navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || 
		navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/BlackBerry/i) || 
		navigator.userAgent.match(/Windows Phone/i)) { 
		var mobile_device = true; 
	}else { 
		var mobile_device = false; 
	}
	
	/* Header and footer fix mobile */
	
	jQuery(window).bind("resize", function () {
		if (jQuery(this).width() < 465) {
			jQuery(".social_icons").each(function () {
				if (jQuery(this).find("li").length > 10) {
					jQuery(this).find("li i").addClass("font11");
					jQuery(this).find("li i").removeClass("font17");
				}
			});
		}else {
			jQuery(".social_icons").each(function () {
				if (jQuery(this).find("li").length > 10) {
					jQuery(this).find("li i").addClass("font17");
					jQuery(this).find("li i").removeClass("font11");
				}
			});
		}
		
		if (jQuery(this).width() < 767) {
			jQuery(".panel-pop").each(function () {
				var panel_pop = jQuery(this);
				var panel_width = panel_pop.outerWidth();
				if (jQuery("body").hasClass("rtl")) {
					panel_pop.css("margin-right","-"+panel_width/2+"px");
				}else {
					panel_pop.css("margin-left","-"+panel_width/2+"px");
				}
			});
		}
	});
	
	if (jQuery(this).width() < 767) {
		jQuery(".panel-pop").each(function () {
			var panel_pop = jQuery(this);
			var panel_width = panel_pop.outerWidth();
			if (jQuery("body").hasClass("rtl")) {
				panel_pop.css("margin-right","-"+panel_width/2+"px");
			}else {
				panel_pop.css("margin-left","-"+panel_width/2+"px");
			}
		});
	}
	
	if (jQuery(window).width() < 465) {
		jQuery(".social_icons").each(function () {
			if (jQuery(this).find("li").length > 10) {
				jQuery(this).find("li i").addClass("font11");
				jQuery(this).find("li i").removeClass("font17");
			}
		});
	}else {
		jQuery(".social_icons").each(function () {
			if (jQuery(this).find("li").length > 10) {
				jQuery(this).find("li i").addClass("font17");
				jQuery(this).find("li i").removeClass("font11");
			}
		});
	}
	
	if (jQuery(".header-search input").length) {
		jQuery(".header-search input").focus(function(event) {
			jQuery(this).css('width','180px');
		});
		jQuery(".header-search input").blur(function(event) {
			jQuery(this).css('width','120px');
		});
	}
	
	if (jQuery(".live-search").length) {
		jQuery(".live-search").each(function () {
			var live_search = jQuery(this);
			var typingTimer;
			var doneTypingInterval = 500;
			live_search.on("keyup",function() {
				live_search  = jQuery(this);
				var search_value = live_search.val();
				if (search_value == "") {
					live_search.parent().find(".search-results").addClass("results-empty").html("").hide();
				}else {
					var search_type = live_search.parent().find(".search_type").val();
					if (search_type === undefined || search_type === false) {
						search_type = live_search.parent().parent().parent().find(".search_type").val();
					}
					var search_loader = live_search.parent().find(".search_loader");
					clearTimeout(typingTimer);
					typingTimer = setTimeout(function () {
						if (live_search.hasClass("header-live-search")) {
							jQuery(".header-search button i").attr("class","fa fa-refresh fa-spin");
						}else if (live_search.hasClass("breadcrumbs-live-search")) {
							jQuery(".search-input-form button i").attr("class","fa fa-refresh fa-spin");
						}else if (live_search.hasClass("live-search-big")) {
							live_search.parent().find("i").attr("class","fa fa-refresh fa-spin");
						}else {
							search_loader.show(10);
						}
						var data = live_search.closest("form").serialize();
						jQuery.ajax({
							url: askme_js.admin_url,
							type: "POST",
							data: data+"&action=ask_live_search&search_value="+search_value,
							success:function(result) {
								live_search.parent().find(".search-results").removeClass("results-empty").html(result).slideDown(300);
								if (live_search.hasClass("header-live-search")) {
									jQuery(".header-search button i").attr("class","fa fa-search");
								}else if (live_search.hasClass("breadcrumbs-live-search")) {
									jQuery(".search-input-form button i").attr("class","fa fa-search");
								}else if (live_search.hasClass("live-search-big")) {
									live_search.parent().find("i").attr("class","fa fa-search");
								}else {
									search_loader.hide(10);
								}
							}
						});
					},500);
				}
			});
			
			live_search.on('focus',function() {
				var live_search  = jQuery(this);
				if (live_search.parent().find(".results-empty").length == 0) {
					live_search.parent().find(".search-results").show();
				}
			});
			
			jQuery(".search_type").change(function () {
				if (jQuery(this).parent().parent().parent().find(".results-empty").length == 0) {
					jQuery(this).parent().parent().parent().find(".search-results").addClass("results-empty").html("").hide();
				}
			});
			
			var outputContainer = live_search.parent().find('.search-results');
			var input 			= live_search.get(0);
			jQuery('body').bind('click', function(e){
				if ( !jQuery.contains( outputContainer.get(0), e.target) && e.target != input ){
					outputContainer.hide();
				}
			});
		});
	}
	
	if (jQuery(".search-type-div").length) {
		jQuery(".search_type").change(function () {
			var ThisSelect = jQuery(this);
			if (ThisSelect.val() == "users") {
				jQuery(".search-type-div,.main-search-div").removeClass("col-md-6").addClass("col-md-4");
				jQuery.when(jQuery(".user-filter-div").fadeIn(200).delay(600)).done(function() {
					jQuery(this).find("select").attr("name","user_filter");
				});
			}else {
				jQuery.when(jQuery(".user-filter-div").fadeOut(200).delay(300)).done(function() {
					jQuery(".search-type-div,.main-search-div").removeClass("col-md-4").addClass("col-md-6");
					jQuery(this).find("select").removeAttr("name");
				});
			}
		});
	}

	/* Page visits */

	if (jQuery(".activate-post-stats").length) {
		var meta_id = jQuery(".activate-post-stats").data("id");
		jQuery.post(askme_js.admin_url,{action:"askme_update_post_stats",post_id:(jQuery(".page-visits-post").length?meta_id:0),user_id:(jQuery(".page-visits-user").length?meta_id:0)});
	}
	
	/* Categories */
	
	if (jQuery(".home_categories").length) {
		jQuery(".home_categories").on("change",function () {
			var url = jQuery(this).val();
			if (url) {
				window.location = url;
			}
			return false;
		});
	}
	
	/* Mobile aside */
	
	if (jQuery('.mobile-aside').length) {
		jQuery('.mobile-aside li.menu-item-has-children').append('<span class="mobile-arrows"><i class="icon-angle-down"></i></span>');
		
		jQuery('.mobile-aside-close').on('touchstart click',function () {
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
			return false;
		});
		
		jQuery('.mobile-menu-click').not(".login-side-link").on('touchstart click',function () {
			jQuery('.mobile-menu-wrap').not(".mobile-login-wrap").addClass('mobile-aside-open');
			return false;
		});
		
		if (jQuery('.mobile-aside ul.menu_aside > li').length) {
			jQuery('.mobile-aside li.menu-item-has-children > a,.mobile-aside li.menu-item-has-children > .mobile-arrows').on("touchstart click",function(){
				jQuery(this).parent().find('ul:first').slideToggle(200);
				jQuery(this).parent().find('> .mobile-arrows').toggleClass('mobile-arrows-open');
				return false;
			});
		}
		
		jQuery('.mobile-aside-inner').mCustomScrollbar({axis:'y',mouseWheelPixels: 50,scrollInertia: 500});
	}
	
	/* Close */
	
	jQuery(document).keyup(function(event) {
		if (event.which == '27') {
			
			/* Panel pop */
			
			jQuery.when(jQuery(".panel-pop").fadeOut(200)).done(function() {
				jQuery(this).css({"top":"-100%","display":"none"});
				jQuery(".wrap-pop").remove();
			});
			
			/* Mobile menu */
			
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
			
			/* User notifications */
			
			jQuery(".user-login-area .user-notifications > div").slideUp(200);
			jQuery(".user-notifications-seen").removeClass("user-notifications-seen");
		}
	});
	
	/* Go up */
	
	jQuery(window).scroll(function () {
		var cssArea = (is_RTL == true?"left":"right");
		if(jQuery(this).scrollTop() > 100) {
			jQuery(".go-up").css(cssArea,"20px");
			jQuery(".ask-button").css(cssArea,(jQuery(".go-up").length?"70px":"20px"));
		}else {
			jQuery(".go-up").css(cssArea,"-60px");
			jQuery(".ask-button").css(cssArea,"20px");
		}
	});
	jQuery(".go-up").click(function(){
		jQuery("html,body").animate({scrollTop:0},500);
		return false;
	});
	
	/* Icon boxes */
	
	if (jQuery(".box_warp").length) {
		jQuery(".box_warp").each(function () {
			var box_warp = jQuery(this);
			var box_background = box_warp.attr("box_background");
			var box_color = box_warp.attr("box_color");
			var box_border = box_warp.attr("box_border");
			var box_border_width = box_warp.attr("box_border_width");
			var box_border_radius = box_warp.attr("box_border_radius");
			var box_background_hover = box_warp.attr("box_background_hover");
			var box_color_hover = box_warp.attr("box_color_hover");
			var box_border_hover = box_warp.attr("box_border_hover");
			
			box_warp.css({"background-color":box_background,"border-color":box_border,"color":box_color,"-moz-border-radius":box_border_radius+"px","-webkit-border-radius":box_border_radius+"px","border-radius":box_border_radius+"px"});
			
			if (box_border_width != "") {
				box_warp.css("border",box_border_width+"px solid "+box_border);
			}
			
			box_warp.find("a").not(".button").css({"color":box_color});
			
			box_warp.hover(function () {
				box_warp.css({"background-color":box_background_hover,"border-color":box_border_hover,"color":box_color_hover});
				box_warp.find("a").not(".button").css({"color":box_color_hover});
			},function () {
				box_warp.css({"background-color":box_background,"border-color":box_border,"color":box_color});
				box_warp.find("a").not(".button").css({"color":box_color});
			});
		});
	}
	
	if (jQuery(".box_icon").length) {
		jQuery(".box_icon").each(function () {
			var box_icon = jQuery(this);
			var icon_align = box_icon.find(".icon_i > span").attr("icon_align");
			var icon_size = box_icon.find(".icon_i > span").attr("icon_size");
			
			if (box_icon.find(".icon_i > span").hasClass("icon_soft_r") || box_icon.find(".icon_i > span").hasClass("icon_square") || box_icon.find(".icon_i > span").hasClass("icon_circle")) {
				box_icon.find(".icon_i > span").css({"height":icon_size+"px","width":icon_size+"px","font-size":icon_size/2+"px","line-height":icon_size+"px"});
				box_icon.find(".icon_i > span > span").css({"margin":0,"text-align":"center"}).parent().css({"line-height":icon_size+"px"});
			}else if (box_icon.find(".box_text h3 > span").hasClass("icon_soft_r") || box_icon.find(".box_text h3 > span").hasClass("icon_square") || box_icon.find(".box_text h3 > span").hasClass("icon_circle")) {
				if (icon_size > 80 && box_icon.find(".box_text h3 > span > span").length == 1) {
					var icon_size = 80;
				}
				box_icon.find(".box_text h3 > span").css({"height":icon_size+"px","width":icon_size+"px","line-height":icon_size+"px"});
			}else {
				box_icon.find(".icon_i > span i").css({"font-size":icon_size/2+"px"});
			}
			
			if (icon_align == "left") {
				box_icon.find(".icon_i").css({"display":"inherit"});
				if (box_icon.find(".icon_i > span").hasClass("icon_soft_r") || box_icon.find(".icon_i > span").hasClass("icon_square") || box_icon.find(".icon_i > span").hasClass("icon_circle")) {
					box_icon.find(".box_text").css({"padding-left":parseFloat(icon_size)+25+"px"});
				}else if (box_icon.find(".icon_i span[class^='icons']").length == 1) {
					box_icon.find(".box_text").css({"padding-left":41+"px"});
				}else {
					box_icon.find(".box_text").css({"padding-left":parseFloat(icon_size/2)+15+"px"});
				}
				
				box_icon.find(".icon_i > span").addClass("f_left");
			}else if (icon_align == "right") {
				box_icon.find(".icon_i").css({"display":"inherit"});
				
				if (box_icon.find(".icon_i > span").hasClass("icon_soft_r") || box_icon.find(".icon_i > span").hasClass("icon_square") || box_icon.find(".icon_i > span").hasClass("icon_circle")) {
					box_icon.find(".box_text").css({"padding-right":parseFloat(icon_size)+25+"px"});
				}else if (box_icon.find(".icon_i span[class^='icons']").length == 1) {
					box_icon.find(".box_text").css({"padding-right":41+"px"});
				}else {
					box_icon.find(".box_text").css({"padding-right":parseFloat(icon_size/2)+15+"px"});
				}
				
				box_icon.find(".icon_i > span").addClass("f_right");
			}else if (icon_align == "center") {
				box_icon.find(".icon_i").addClass("t_center");
			}
		});
	}
	
	if (jQuery(".box_icon").length) {
		jQuery(".box_icon").each(function() {
			var this_icon = jQuery(this);
			var span_bg = this_icon.find(".icon_i > span").attr("span_bg");
			if (span_bg != undefined) {
				this_icon.find(".icon_i > span").css({"background-color":span_bg});
			}else {
				var span_bg = this_icon.find(".box_text h3 > span").attr("span_bg");
				this_icon.find(".box_text h3 > span").css({"background-color":span_bg});
			}
			var i_color = this_icon.find(".icon_i > span i").attr("i_color");
			if (i_color != undefined) {
				this_icon.find(".icon_i > span i").css({"color":i_color});
			}
			var border_radius = this_icon.find(".icon_i > span").attr("border_radius");
			if (border_radius != undefined) {
				this_icon.find(".icon_i > span").css({"-moz-border-radius":border_radius+"px","-webkit-border-radius":border_radius+"px","border-radius":border_radius+"px"});
			}
			
			var border_color = this_icon.find(".icon_i > span").attr("border_color");
			if (border_color != undefined) {
				this_icon.find(".icon_i > span").css({"border-color":border_color});
				this_icon.find(".box_text h3 > span").css({"border-color":border_color});
			}else {
				var border_color = this_icon.find(".box_text h3 > span").attr("border_color");
				this_icon.find(".box_text h3 > span").css({"border-color":border_color});
			}
			var border_width = this_icon.find(".icon_i > span").attr("border_width");
			if (border_width != undefined) {
				this_icon.find(".icon_i > span").css({"border-width":border_width+"px","border-style":"solid"});
			}else {
				var border_width = this_icon.find(".box_text h3 > span").attr("border_width");
				this_icon.find(".box_text h3 > span").css({"border-width":border_width+"px","border-style":"solid"});
			}
		
			this_icon.hover(function () {
				var span_hover = this_icon.find(".icon_i > span").attr("span_hover");
				if (span_hover != undefined) {
					this_icon.find(".icon_i > span").css({"background-color":span_hover});
				}else {
					var span_hover = this_icon.find(".box_text h3 > span").attr("span_hover");
					this_icon.find(".box_text h3 > span").css({"background-color":span_hover});
				}
				var border_hover = this_icon.find(".icon_i > span").attr("border_hover");
				if (border_hover != undefined) {
					this_icon.find(".icon_i > span").css({"border-color":border_hover});
				}else {
					var border_hover = this_icon.find(".box_text h3 > span").attr("border_hover");
					this_icon.find(".box_text h3 > span").css({"border-color":border_hover});
				}
				var i_hover = this_icon.find(".icon_i > span i").attr("i_hover");
				if (i_hover != undefined) {
					this_icon.find(".icon_i > span i").css({"color":i_hover});
				}
				
				if (this_icon.find(".button").length) {
					var button_background_hover = this_icon.find(".button").attr("button_background_hover");
					var button_color_hover = this_icon.find(".button").attr("button_color_hover");
					var button_border_hover = this_icon.find(".button").attr("button_border_hover");
					this_icon.find(".button").css({"background-color":button_background_hover,"color":button_color_hover,"border-color":button_border_hover});
				}
			},function() {
				if (i_color != undefined) {
					this_icon.find(".icon_i > span i").css({"color":i_color});
				}
				var span_bg = this_icon.find(".icon_i > span").attr("span_bg");
				if (span_bg != undefined) {
					this_icon.find(".icon_i > span").css({"background-color":span_bg});
				}else {
					var span_bg = this_icon.find(".box_text h3 > span").attr("span_bg");
					this_icon.find(".box_text h3 > span").css({"background-color":span_bg});
				}
				var border_color = this_icon.find(".icon_i > span").attr("border_color");
				if (border_color != undefined) {
					this_icon.find(".icon_i > span").css({"border-color":border_color});
				}else {
					var border_color = this_icon.find(".box_text h3 > span").attr("border_color");
					this_icon.find(".box_text h3 > span").css({"border-color":border_color});
				}
				if (this_icon.find(".button").length) {
					var button_background = this_icon.find(".button").attr("button_background");
					var button_color = this_icon.find(".button").attr("button_color");
					var button_border = this_icon.find(".button").attr("button_border");
					this_icon.find(".button").css({"background-color":button_background,"color":button_color,"border-color":button_border});
				}
			});
			
		});
	}
	
	/* Icons */
	
	if (jQuery(".icon_i").length) {
		jQuery(".icon_i").each(function() {
			var this_icon = jQuery(this);
			if (!this_icon.parent().hasClass("box_icon") && !this_icon.parent().parent().hasClass("box_icon") && !this_icon.parent().parent().parent().hasClass("box_icon")) {
				var span_bg = this_icon.find("> span").attr("span_bg");
				var icon_align = this_icon.find("> span").attr("icon_align");
				var icon_size = this_icon.find("> span").attr("icon_size");
				var border_color = this_icon.find("> span").attr("border_color");
				var border_width = this_icon.find("> span").attr("border_width");
				var border_radius = this_icon.find("> span").attr("border_radius");
				var span_hover = this_icon.find("> span").attr("span_hover");
				var border_hover = this_icon.find("> span").attr("border_hover");
				var i_color = this_icon.find("> span i").attr("i_color");
				var i_hover = this_icon.find("> span i").attr("i_hover");
				
				if (this_icon.find("> span").hasClass("icon_soft_r") || this_icon.find("> span").hasClass("icon_square") || this_icon.find("> span").hasClass("icon_circle")) {
					this_icon.find("> span").css({"height":icon_size+"px","width":icon_size+"px","font-size":icon_size/2+"px","line-height":icon_size+"px"});
					this_icon.find("> span > span").css({"margin":0,"text-align":"center"});
				}else {
					this_icon.find("> span i").css({"font-size":icon_size/2+"px"});
				}
				
				if (icon_align == "left") {
					this_icon.addClass("f_left");
				}else if (icon_align == "right") {
					this_icon.addClass("f_right");
				}else if (icon_align == "center") {
					this_icon.addClass("t_center");
					this_icon.css("margin-bottom","15px");
				}
				
				if (this_icon.find("> span").hasClass("icon_soft_r") || this_icon.find("> span").hasClass("icon_square") || this_icon.find("> span").hasClass("icon_circle")) {
					this_icon.find("> span").css({"background-color":span_bg,"border-color":border_color,"border-width":border_width+"px","border-style":"solid","-moz-border-radius":border_radius+"px","-webkit-border-radius":border_radius+"px","border-radius":border_radius+"px"});
				}
				this_icon.find("> span i").css({"color":i_color});
			
				this_icon.hover(function () {
					if (this_icon.find("> span").hasClass("icon_soft_r") || this_icon.find("> span").hasClass("icon_square") || this_icon.find("> span").hasClass("icon_circle")) {
						this_icon.find("> span").css({"background-color":span_hover,"border-color":border_hover});
					}
					this_icon.find("> span i").css({"color":i_hover});
			
				},function() {
					if (this_icon.find("> span").hasClass("icon_soft_r") || this_icon.find("> span").hasClass("icon_square") || this_icon.find("> span").hasClass("icon_circle")) {
						this_icon.find("> span").css({"background-color":span_bg,"border-color":border_color});
					}
					this_icon.find("> span i").css({"color":i_color});
				});
			}
		});
	}
	
	/* Section */
	
	if (jQuery(".section-warp").length) {
		jQuery(".section-warp").each(function () {
			var section = jQuery(this);
			var section_background_color = section.attr("section_background_color");
			var section_background = section.attr("section_background");
			var section_background_size = section.attr("section_background_size");
			var section_color = section.attr("section_color");
			var section_color_a = section.attr("section_color_a");
			var section_padding_top = section.attr("section_padding_top");
			var section_padding_bottom = section.attr("section_padding_bottom");
			var section_margin_top = section.attr("section_margin_top");
			var section_margin_bottom = section.attr("section_margin_bottom");
			var section_border_top = section.attr("section_border_top");
			var section_border_bottom = section.attr("section_border_bottom");
			
			if (section_background != "" && section_background != undefined) {
				section.css({"background-image":"url("+section_background+")"});
			}
	
			section.css({"background-size":section_background_size,"background-color":section_background_color,"color":section_color,"padding-top":section_padding_top+"px","padding-bottom":section_padding_bottom+"px","margin-top":section_margin_top+"px","margin-bottom":section_margin_bottom+"px"});
			section.find("h1").css({"color":section_color});
			section.find("h2").css({"color":section_color});
			section.find("h3").css({"color":section_color});
			section.find("h4").css({"color":section_color});
			section.find("h5").css({"color":section_color});
			section.find("h6").css({"color":section_color});
			section.find("p").css({"color":section_color});
			section.find("a").not(".button").css({"color":section_color_a});
			if (section_border_top != "") {
				section.css({"border-top":"1px solid "+section_border_top});
			}
			if (section_border_bottom != "") {
				section.css({"border-bottom":"1px solid "+section_border_bottom});
			}
		});
	}
	
	/* Accordion & Toggle */
	
	if (jQuery(".accordion").length) {
		jQuery(".accordion").each(function(){
			if (jQuery(this).hasClass("toggle-accordion")) {
				jQuery(this).find(".accordion-toggle-open").addClass("active");
				jQuery(this).find(".accordion-toggle-open").next(".accordion-inner").show();
			}else {
				var what_active = jQuery(this).attr("what-active");
				if (what_active != undefined) {
					jQuery(this).find(".accordion-inner:nth-child("+what_active * 2+")").show();
					jQuery(this).find(".accordion-inner:nth-child("+what_active * 2+")").prev().addClass("active");
				}
			}
		});
		
		jQuery(".accordion .accordion-title").each(function(){
			//i_color
			var i_color = jQuery(this).parent().parent().attr("i_color");
			jQuery(this).parent().parent().find(".accordion-title i").css({"color":i_color});
			//i_click
			var i_click = jQuery(this).parent().parent().attr("i_click");
			jQuery(this).parent().parent().find(".accordion-title.active i").css({"color":i_click});
		
			jQuery(this).click(function() {
				if (jQuery(this).parent().parent().hasClass("toggle-accordion")) {
					jQuery(this).parent().parent().find("li:first .accordion-title").addClass("active");
					jQuery(this).toggleClass("active");
					jQuery(this).next(".accordion-inner").slideToggle();
				}else {
					if (jQuery(this).next().is(":hidden")) {
						jQuery(this).parent().parent().find(".accordion-title").removeClass("active").next().slideUp(200);
						jQuery(this).toggleClass("active").next().slideDown(200);
					}
				}
				if (jQuery(this).parent().parent().hasClass("acc-style-4")) {
					jQuery(this).parent().parent().find(".accordion-title.active").next().css({"border-bottom":"1px solid #DEDEDE"});
				}
				//i_color
				jQuery(this).parent().parent().find(".accordion-title i").css({"color":i_color});
				//i_click
				jQuery(this).parent().parent().find(".accordion-title.active i").css({"color":i_click});
				return false;
			});
		
		});
	}
	
	if (jQuery(".categories-toggle-accordion").length) {
		jQuery(".categories-toggle-accordion .accordion-title").each(function () {
			jQuery(this).find(" > a > i").click(function () {
				var categories = jQuery(this);
				categories.toggleClass("vbegy-minus");
				categories.parent().parent().next().slideToggle();
				return false;
			});
		});
	}
	
	/* Tabs */
	
	if (jQuery(".tab-inner-warp").length > 0) {
		jQuery("ul.tabs:not(.not-tabs)").tabss(".tab-inner-warp",{effect:"slide",fadeInSpeed:100});
	}
	
	if (jQuery("ul.tabs:not(.not-tabs) li").length) {
		jQuery("ul.tabs:not(.not-tabs) li").each(function(){
			//i_color
			var i_color = jQuery(this).parent().parent().attr("i_color");
			jQuery(this).find("a i").css({"color":i_color});
			//i_click
			var i_click = jQuery(this).parent().parent().attr("i_click");
			jQuery(this).find("a.current i").css({"color":i_click});
			
			jQuery(this).find("a").hover(function () {
				jQuery(this).find("i").css({"color":i_click});
			},function () {
				if (jQuery(this).hasClass("current")) {
					jQuery(this).find("i").css({"color":i_click});
				}else {
					jQuery(this).find("i").css({"color":i_color});
				}
			});
			
			if (!jQuery(this).parent().parent().hasClass("woocommerce-tabs")) {
				jQuery(this).click(function() {
					//i_color
					var i_color = jQuery(this).parent().parent().attr("i_color");
					jQuery(this).parent().find("a i").css({"color":i_color});
					//i_click
					var i_click = jQuery(this).parent().parent().attr("i_click");
					jQuery(this).find("a.current i").css({"color":i_click});
					return false;
				});
		
				var tab_width = jQuery(this).parent().parent().attr("tab_width");
				if (jQuery(this).parent().parent().hasClass("tabs-vertical")) {
					jQuery(this).parent().css({"width":tab_width+"px"});
					jQuery(this).parent().parent().find("div.tab-inner-warp").css({"margin-left":tab_width+"px"});
				}
			}
			
		});
	}
	
	/* Button */
	
	if (jQuery(".button").length) {
		jQuery(".button").each(function () {
			var button = jQuery(this);
			var button_background = button.attr("button_background");
			var button_background_hover = button.attr("button_background_hover");
			var button_color = button.attr("button_color");
			var button_color_hover = button.attr("button_color_hover");
			var button_border = button.attr("button_border");
			var button_border_hover = button.attr("button_border_hover");
			var button_border_width = button.attr("button_border_width");
			var button_border_radius = button.attr("button_border_radius");
			
			button.css({"background-color":button_background,"color":button_color,"border":button_border_width+"px solid "+button_border,"-moz-border-radius":button_border_radius+"px","-webkit-border-radius":button_border_radius+"px","border-radius":button_border_radius+"px"});
			
			button.hover(function () {
			button.css({"background-color":button_background_hover,"color":button_color_hover,"border-color":button_border_hover});
			},function () {
				button.css({"background-color":button_background,"color":button_color,"border":button_border_width+"px solid "+button_border,"-moz-border-radius":button_border_radius+"px","-webkit-border-radius":button_border_radius+"px","border-radius":button_border_radius+"px"});
			});
		});
	}
	
	/* Lists */
	
	if (jQuery(".ul_list").length) {
		jQuery(".ul_list").each(function () {
			var ul_list = jQuery(this);
			var list_background = ul_list.attr("list_background");
			var list_background_hover = ul_list.attr("list_background_hover");
			var list_color = ul_list.attr("list_color");
			var list_color_hover = ul_list.attr("list_color_hover");
			var list_border_radius = ul_list.attr("list_border_radius");
	
			if (ul_list.hasClass("ul_list_circle") || ul_list.hasClass("ul_list_square")) {
				ul_list.find("ul li i").css({"background-color":list_background,"-moz-border-radius":list_border_radius+"px","-webkit-border-radius":list_border_radius+"px","border-radius":list_border_radius+"px"});
				ul_list.find("ul li").hover(function () {
					jQuery(this).find("i").css({"background-color":list_background_hover});
				},function () {
					jQuery(this).find("i").css({"background-color":list_background});
				});
			}
			ul_list.find("ul li i").css({"color":list_color});
	
			ul_list.find("ul li").hover(function () {
				jQuery(this).find("i").css({"color":list_color_hover});
			},function () {
				jQuery(this).find("i").css({"color":list_color});
			});
			ul_list.find("i").each(function () {
				var ul_l = jQuery(this);
				var l_background = ul_l.attr("l_background");
				var l_background_hover = ul_l.attr("l_background_hover");
				var l_color = ul_l.attr("l_color");
				var l_color_hover = ul_l.attr("l_color_hover");
				var l_border_radius = ul_l.attr("l_border_radius");
				
				if (ul_l.hasClass("ul_l_circle") || ul_l.hasClass("ul_l_square")) {
					ul_l.css({"background-color":l_background,"-moz-border-radius":l_border_radius+"px","-webkit-border-radius":l_border_radius+"px","border-radius":l_border_radius+"px"});
					ul_l.parent().hover(function () {
						ul_l.css({"background-color":l_background_hover});
					},function () {
						ul_l.css({"background-color":l_background});
					});
				}
				
				ul_l.css({"color":l_color});
		
				ul_l.parent().hover(function () {
					ul_l.css({"color":l_color_hover});
				},function () {
					ul_l.css({"color":l_color});
				});
			});
		});
	}
	
	/* Quote */
	
	if (jQuery("blockquote").length) {
		jQuery("blockquote").each(function () {
			var blockquote = jQuery(this);
			var blockquote_background = blockquote.attr("blockquote_background");
			var blockquote_color = blockquote.attr("blockquote_color");
			var blockquote_border = blockquote.attr("blockquote_border");
			
			blockquote.css({"background-color":blockquote_background,"color":blockquote_color,"border-color":blockquote_border});
		});
	}
	
	/* Dropcap */
	
	if (jQuery(".dropcap").length) {
		jQuery(".dropcap").each(function () {
			var dropcap = jQuery(this);
			var dropcap_background = dropcap.attr("dropcap_background");
			var dropcap_color = dropcap.attr("dropcap_color");
			var dropcap_border_radius = dropcap.attr("dropcap_border_radius");
			
			if (dropcap_border_radius != "" && dropcap_border_radius != undefined) {
				dropcap.css({"-moz-border-radius":dropcap_border_radius+"px","-webkit-border-radius":dropcap_border_radius+"px","border-radius":dropcap_border_radius+"px"});
			}
			dropcap.css({"background-color":dropcap_background,"color":dropcap_color});
		});
	}
	
	/* Divider */
	
	if (jQuery(".divider").length) {
		jQuery(".divider").each(function () {
			var divider = jQuery(this);
			var divider_color = divider.attr("divider_color");
			
			divider.css({"border-bottom-color":divider_color});
		});
	}
	
	/* Progress Bar */
	
	if (jQuery(".progressbar-percent").length) {
		jQuery(".progressbar-percent").each(function(){
			var $this = jQuery(this);
			var percent = $this.attr("attr-percent");
			$this.bind("inview", function(event, isInView, visiblePartX, visiblePartY) {
				if (isInView) {
					$this.animate({ "width" : percent + "%"}, percent*20);
				}
			});
		});
	}
	
	/* Testimonial */
	
	if (jQuery(".testimonial-warp").length) {
		jQuery(".testimonial-warp").each(function () {
			var testimonial = jQuery(this);
			var testimonial_background = testimonial.attr("testimonial_background");
			var testimonial_color = testimonial.attr("testimonial_color");
			var testimonial_border = testimonial.attr("testimonial_border");
			var border_radius = testimonial.attr("border_radius");
			var client_color = testimonial.attr("client_color");
			var jop_color = testimonial.attr("jop_color");
			
			testimonial.find(".testimonial").css({"background-color":testimonial_background,"color":testimonial_color,"border-color":testimonial_border,"-moz-border-radius":border_radius+"px","-webkit-border-radius":border_radius+"px","border-radius":border_radius+"px"});
			testimonial.find(".testimonial a").css({"color":testimonial_color});
			testimonial.find(".testimonial-f-arrow").css({"border-top-color":testimonial_border});
			testimonial.find(".testimonial-l-arrow").css({"border-top-color":testimonial_background});
	
			testimonial.find(".testimonial-client > span").css({"color":client_color});
			testimonial.find(".testimonial-client > span > span").css({"color":jop_color});
		});
	}
	
	/* Callout */
	
	if (jQuery(".callout_warp").length) {
		jQuery(".callout_warp").each(function () {
			var callout_warp = jQuery(this);
			if (callout_warp.find(".button_right").length == 1) {
				callout_warp.find(".callout_inner").css("margin-right",parseFloat(callout_warp.find(".button_right").outerWidth())+25);
				var button_css_top = (((parseFloat(callout_warp.innerHeight()))/2))-parseFloat(callout_warp.find(".button_right").innerHeight())/2;
				callout_warp.find(".button_right").css("top",button_css_top);
			}
		});
	}
	
	/* Flex slider */
	
	if (jQuery(".blog_silder").length && jQuery()) {
		var flex_slider = jQuery(".blog_silder");
		flex_slider.flexslider({
			animation: "fade",//fade - slide
			animationLoop: true,
			slideshow: true,
			slideshowSpeed: 3000,
			animationSpeed: 800,
			pauseOnHover: true,
			pauseOnAction:true,
			controlNav: false,
			directionNav: true,
		});
	}
	
	if (jQuery(".flex-slider").length && jQuery()) {
		var flex_slider = jQuery(".flex-slider");
		flex_slider.flexslider({
			animation: "fade",//fade - slide
			animationLoop: true,
			slideshow: true,
			slideshowSpeed: 3000,
			animationSpeed: 800,
			pauseOnHover: true,
			pauseOnAction: true,
			controlNav: true,
			directionNav: true,
		});
	}
	
	/* Tipsy */
	
	jQuery(".tooltip-n").tipsy({fade:true,gravity:"s"});
	jQuery(".tooltip-s").tipsy({fade:true,gravity:"n"});
	jQuery(".tooltip-nw").tipsy({fade:true,gravity:"nw"});
	jQuery(".tooltip-ne").tipsy({fade:true,gravity:"ne"});
	jQuery(".tooltip-w").tipsy({fade:true,gravity:"w"});
	jQuery(".tooltip-e").tipsy({fade:true,gravity:"e"});
	jQuery(".tooltip-sw").tipsy({fade:true,gravity:"sw"});
	jQuery(".tooltip-se").tipsy({fade:true,gravity:"se"});
	
	/* Ask Question */
	
	if (jQuery(".question_tags,.post_tag").length) {
		jQuery('.question_tags,.post_tag').tag();
	}
	
	var question_poll = jQuery(".question_poll:checked").length;
	if (question_poll == 1) {
		jQuery(".poll_options").slideDown(500);
	}else {
		jQuery(".poll_options").slideUp(500);
	}
	
	if (jQuery(".question_poll").length) {
		jQuery(".question_poll").click(function () {
			var question_poll_c = jQuery(".question_poll:checked").length;
			if (question_poll_c == 1) {
				jQuery(".poll_options").slideDown(500);
			}else {
				jQuery(".poll_options").slideUp(500);
			}
		});
	}
	
	if (jQuery(".question_polls_item").length) {
		jQuery(".question_polls_item").sortable({placeholder: "ui-state-highlight"});
	}
	
	if (jQuery(".question_upload_item").length) {
		jQuery(".question_upload_item").sortable({placeholder: "ui-state-highlight"});
	}
	
	if (jQuery(".add_poll_button_js").length) {
		jQuery(".add_poll_button_js").click(function() {
			var poll_this = jQuery(this);
			var poll_options = poll_this.closest(".poll_options");
			var add_poll = poll_options.find(".question_poll_item > li").length;
			if (add_poll > 0) {
				var i_count = 0;
				while (i_count < add_poll) {
					if (poll_options.find(".question_poll_item > #poll_li_"+add_poll).length) {
						add_poll++;
					}
					i_count++;
				}
			}else {
				add_poll++;
			}
			jQuery(this).parent().parent().find('.question_poll_item').append('<li id="poll_li_'+add_poll+'"><div class="poll-li"><p><input id="ask['+add_poll+'][title]" class="ask" name="ask['+add_poll+'][title]" value="" type="text"></p><input id="ask['+add_poll+'][value]" name="ask['+add_poll+'][value]" value="" type="hidden"><input id="ask['+add_poll+'][id]" name="ask['+add_poll+'][id]" value="'+add_poll+'" type="hidden"><div class="del-poll-li"><i class="icon-remove"></i></div><div class="move-poll-li"><i class="icon-fullscreen"></i></div></div></li>');
			jQuery('#poll_li_'+add_poll).hide().fadeIn();
			jQuery(".del-poll-li").click(function() {
				jQuery(this).parent().parent().addClass('removered').fadeOut(function() {
					jQuery(this).remove();
				});
			});
			return false;
		});
	}
	
	if (jQuery(".del-poll-li").length) {
		jQuery(".del-poll-li").click(function() {
			jQuery(this).parent().parent().addClass('removered').fadeOut(function() {
				jQuery(this).remove();
			});
		});
	}
	
	if (jQuery(".fileinputs").length) {
		jQuery(".fileinputs input[type='file']").change(function () {
			var file_fake = jQuery(this);
			file_fake.parent().find("button").text(file_fake.val());
		});
	}
	
	/* Custom select */

	jQuery('.askme-custom-select').each(function () {
		jQuery(this).select2({
			width: '100%',
		});
	});
	
	/* Fake file */
	
	jQuery(document).on("change",".fileinputs input[type='file']",function () {
		var file_fake = jQuery(this);
		var file_value = file_fake.val();
		file_value = file_value.replace("C:\\fakepath\\","");
		file_fake.parent().find("button").text(file_value);
	});
	
	jQuery(document).on("click",".fakefile",function () {
		jQuery(this).parent().find("input[type='file']").trigger("click");
	});
	
	if (jQuery(".video_description_input,.video_description").length) {
		jQuery(".video_description").each(function () {
			var video_description = jQuery(this);
			var video_description_input = video_description.parent().find(".video_description_input");
			if (video_description_input.is(":checked")) {
				video_description.show(10);
			}else {
				video_description.hide(10);
			}
			
			video_description_input.click(function () {
				var video_description_input_c = jQuery(this);
				var video_description_c = video_description_input_c.parent().parent().find(".video_description");
				if (video_description_input_c.is(":checked")) {
					video_description_c.slideDown(300);
				}else {
					video_description_c.slideUp(300);
				}
			});
		});
	}
	
	if (jQuery(".video_answer_description").length) {
		jQuery(".video_answer_description").each(function () {
			var video_description = jQuery(this);
			var video_description_input = video_description.parent().find(".video_answer_description_input");
			if (video_description_input.is(":checked")) {
				video_description.show(10);
			}else {
				video_description.hide(10);
			}
			
			video_description_input.click(function () {
				var video_description_input_c = jQuery(this);
				var video_description_c = video_description_input_c.parent().parent().find(".video_answer_description");
				if (video_description_input_c.is(":checked")) {
					video_description_c.slideDown(300);
				}else {
					video_description_c.slideUp(300);
				}
			});
		});
	}
	
	if (jQuery(".ask-question-link").length) {
		jQuery(".ask-question-link").click(function () {
			jQuery(".panel-pop").animate({"top":"-100%"},10).hide();
			jQuery("#ask-question").show().animate({"top":"2%"},500);
			jQuery("html,body").animate({scrollTop:0},500);
			jQuery("body").prepend("<div class='wrap-pop'></div>");
			wrap_pop();
			return false;
		});
	}
	
	if (jQuery(".add-post-link").length) {
		jQuery(".add-post-link").click(function () {
			jQuery(".panel-pop").animate({"top":"-100%"},10).hide();
			jQuery("#add-post").show().animate({"top":"2%"},500);
			jQuery("html,body").animate({scrollTop:0},500);
			jQuery("body").prepend("<div class='wrap-pop'></div>");
			wrap_pop();
			return false;
		});
	}
	
	if (jQuery(".form_message,.message-reply a").length) {
		jQuery(".form_message,.message-reply a").click(function () {
			var user_id    = jQuery(this).attr("data-user-id");
			var message_id = jQuery(this).attr("data-id");
			if (message_id !== undefined && message_id !== false) {
				jQuery.ajax({
					url: askme_js.admin_url,
					type: "POST",
					data: { action : 'ask_message_reply',message_id : message_id },
					success:function(data) {
						jQuery("#send-message .the-title").val(data);
					}
				});
			}
			if (user_id !== undefined && user_id !== false) {
				if (jQuery(".message_user_id").length) {
					jQuery(".message_user_id").attr("value",user_id);
				}else {
					jQuery("#send-message .send-message").after('<input type="hidden" name="user_id" class="message_user_id" value="'+user_id+'">');
				}
			}
			
			jQuery(".panel-pop").animate({"top":"-100%"},10).hide();
			jQuery("#send-message").show().animate({"top":"2%"},500);
			jQuery("html,body").animate({scrollTop:0},500);
			jQuery("body").prepend("<div class='wrap-pop'></div>");
			wrap_pop();
			return false;
		});
	}
	
	if (jQuery(".message-delete a").length) {
		jQuery(".message-delete a").click(function () {
			if (confirm(askme_js.sure_delete_message)) {
				return true;
			}else {
				return false;
			}
		});
	}
	
	if (jQuery(".view-message").length) {
		jQuery(".view-message").click(function () {
			var view_message    = jQuery(this);
			var message_id      = view_message.attr("data-id");
			var message_show    = view_message.attr("data-show");
			var message_content = view_message.closest(".user-messages").find(".message-content");
			view_message.find(".message-open-close").removeClass("icon-minus").addClass("icon-plus");
			if (view_message.hasClass("view-message-open")) {
				message_content.slideUp(300);
				view_message.removeClass("view-message-open");
			}else {
				if (message_content.find(" > div").length) {
					message_content.slideDown(300);
					view_message.addClass("view-message-open").find(".message-open-close").removeClass("icon-plus").addClass("icon-minus");
				}else {
					view_message.addClass("view-message-open").find(".message_loader").addClass("message_loader_display");
					jQuery.ajax({
						url: askme_js.admin_url,
						type: "POST",
						data: { action : 'ask_message_view',message_id : message_id,message_show : message_show },
						success:function(data) {
							view_message.find(".message_loader").removeClass("message_loader_display");
							view_message.find(".message-open-close").removeClass("icon-plus").addClass("icon-minus");
							message_content.html(data).slideDown(300);
							view_message.find(".message-new").removeClass("message-new");
						}
					});
				}
			}
			return false;
		});
	}
	
	if (jQuery(".block_message").length) {
		jQuery(".block_message").click(function () {
			var block_message = jQuery(this);
			var user_id    = block_message.attr("data-id");
			block_message.hide();
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : (block_message.hasClass("unblock_message")?'ask_unblock_message':'ask_block_message'),user_id : user_id },
				success:function(data) {
					if (block_message.hasClass("unblock_message")) {
						block_message.removeClass("unblock_message").text(askme_js.block_message_text).show();
					}else {
						block_message.addClass("unblock_message").text(askme_js.unblock_message_text).show();
					}
					location.reload();
				}
			});
			return false;
		});
	}
	
	/* Block users */
	
	if (jQuery(".unblock-user-page,.block-user-page").length) {
		askme_block("unblock-user-page","block-user-page");
		askme_block("block-user-page","unblock-user-page");
		
		function askme_block(block,next_block) {
			jQuery(document).on("click","."+block,function () {
				var blocking_var = jQuery(this);
				var blocking_var_id = blocking_var.attr("data-rel");
				var block_nonce = blocking_var.data("nonce");
				var block_type = (block == "unblock-user-page"?"unblock":"block");
				var user_block_done = "user_block_done";
				blocking_var.hide();
				blocking_var.parent().addClass("user_block_active");
				blocking_var.parent().find(".user_block_loader").addClass("block_loader_show");
				jQuery.ajax({
					url: askme_js.admin_url,
					type: "POST",
					data: {action:'askme_block_user', block_type : block_type, block_nonce : block_nonce, user_id : blocking_var_id},
					success:function(result) {
						blocking_var.addClass(next_block).removeClass(block).attr("title",(block == "unblock-user-page"?askme_js.block_user:askme_js.unblock_user)).show().parent().removeClass("user_block_active");
						if (block == "unblock-user-page") {
							blocking_var.parent().removeClass(user_block_done).find(".block-value").text((block == "unblock-user-page"?askme_js.block_user:askme_js.unblock_user));
						}else {
							blocking_var.parent().addClass(user_block_done).find(".block-value").text((block == "unblock-user-page"?askme_js.block_user:askme_js.unblock_user));
						}
						blocking_var.parent().find(".user_block_loader").removeClass("block_loader_show");
					}
				});
				return false;
			});
		}
	}
	
	if (jQuery(".add_upload_button_js").length) {
		jQuery(".add_upload_button_js").click(function() {
			jQuery(this).parent().parent().find('.question_poll_item').append('<li id="poll_li_'+next_attachment+'"><div class="poll-li"><div class="fileinputs"><input type="file" class="file" name="attachment_m['+next_attachment+'][file_url]" id="attachment_m['+next_attachment+'][file_url]"><div class="fakefile"><button type="button" class="button small margin_0">'+askme_js.select_file+'</button><span><i class="icon-arrow-up"></i>'+askme_js.browse+'</span></div><div class="del-poll-li"><i class="icon-remove"></i></div><div class="move-poll-li"><i class="icon-fullscreen"></i></div></div></div></li>');
			jQuery(".fileinputs input[type='file']").change(function () {
				var file_fake = jQuery(this);
				file_fake.parent().find("button").text(file_fake.val());
			});
			jQuery(".fakefile").click(function () {
				jQuery(this).parent().find("input[type='file']").click();
			});
			jQuery('#poll_li_'+next_attachment).hide().fadeIn();
			next_attachment++;
			jQuery(".del-poll-li").click(function() {
				jQuery(this).parent().parent().parent().fadeOut(function() {
					jQuery(this).remove();
				});
			});
			return false;
		});
	}
	
	if (jQuery(".the-details").length) {
		jQuery("#wp-question-details-wrap").appendTo(".the-details");
		jQuery("#wp-post-details-wrap").appendTo(".the-details");
	}
	
	if (jQuery(".cat-ajax").length) {
		jQuery('.category-wrap').on('change','.cat-ajax',function() {
			var currentLevel = parseInt(jQuery(this).parent().parent().data('level'));
			ask_me_child_cats(jQuery(this),'ask-level-',currentLevel+1);
		});
	}

	if (jQuery(".add_media").length) {
		jQuery(".add_media").on("click",function (event) {
			event.preventDefault();
			wp.media.model.settings.post.id = 0;
		});
	}

	/* Cancel edit email */

	if (jQuery(".cancel-edit-email").length) {
		jQuery(document).on("click",".cancel-edit-email",function () {
			var edit_email = jQuery(this);
			var id = edit_email.data("id");
			var nonce = edit_email.data("nonce");
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : 'askme_cancel_edit_email',id : id,nonce : nonce },
				success:function(results) {
					jQuery(".alert-confirm-email").hide().remove();
				},
				error: function(errorThrown) {
					// Error
				}
			});
			return false;
		});
	}
	
	/* Datepicker */

	if (jQuery(".date-datepicker").length) {
		jQuery(".date-datepicker").datepicker({changeMonth:true,dateFormat:"yy-mm-dd"});
	}
	
	/* single question */
	
	if (jQuery(".share-inside").length) {
		jQuery(".share-inside").click(function () {
			if (jQuery(".share-inside-warp").hasClass("share-inside-show")) {
				jQuery(".share-inside-warp").slideUp("500");
				jQuery(".share-inside-warp").removeClass("share-inside-show");
			}else {
				jQuery(".share-inside-warp").slideDown("500");
				jQuery(".share-inside-warp").addClass("share-inside-show");
			}
		});
	}
	
	if (jQuery(".single-question.question").length > 0 && (jQuery(".question-edit").length > 0 || jQuery(".question-delete").length > 0 || jQuery(".question-follow").length > 0 || jQuery(".question-close").length > 0)) {
		jQuery(".single-question.question").hover(function () {
			jQuery(this).find(".edit-delete-follow-close").stop().slideDown(500);
		},function () {
			jQuery(this).find(".edit-delete-follow-close").slideUp(500);
		});
	}
	
	if (jQuery(".post-delete").length) {
		jQuery(".post-delete").click(function () {
			if (confirm(askme_js.sure_delete_post)) {
				return true;
			}else {
				return false;
			}
		});
	}
	
	if (jQuery(".comment-delete-link").length) {
		jQuery(".comment-delete-link").click(function () {
			var var_delete = (jQuery(".delete-answer").length?askme_js.sure_delete_answer:askme_js.sure_delete_comment);
			if (confirm(var_delete)) {
				return true;
			}else {
				return false;
			}
		});
	}
	
	if (jQuery(".question-follow a").length) {
		jQuery(".question-follow a").click(function () {
			question_follow = jQuery(this);
			if (jQuery(".edit-delete-follow-close-2").length > 0) {
				post_id = question_follow.parent().parent().parent().parent().parent().attr('id');
			}else {
				post_id = question_follow.parent().parent().parent().parent().attr('id');
			}
			post_id = post_id.replace("post-","");
			question_follow.hide();
			if (question_follow.hasClass("unfollow-question")) {
				jQuery.ajax({
					url: askme_js.admin_url,
					type: "POST",
					data: { action : 'question_unfollow', post_id : post_id },
					success:function(data) {
						question_follow.removeClass("unfollow-question");
						question_follow.find("i").addClass("icon-circle-arrow-up");
						question_follow.find("i").removeClass("icon-circle-arrow-down");
						question_follow.attr("original-title",askme_js.follow_question_attr);
						question_follow.text(askme_js.follow_question);
						question_follow.show();
					}
				});
			}else {
				jQuery.ajax({
					url: askme_js.admin_url,
					type: "POST",
					data: { action : 'question_follow', post_id : post_id },
					success:function(data) {
						question_follow.addClass("unfollow-question");
						question_follow.find("i").removeClass("icon-circle-arrow-up");
						question_follow.find("i").addClass("icon-circle-arrow-down");
						question_follow.attr("original-title",askme_js.unfollow_question_attr);
						question_follow.text(askme_js.unfollow_question);
						question_follow.show();
					}
				});
			}
			return false;
		});
	}
	
	if (jQuery(".question-close a").length) {
		jQuery(".question-close a").click(function () {
			question_close = jQuery(this);
			if (jQuery(".edit-delete-follow-close-2").length > 0) {
				post_id = question_close.parent().parent().parent().parent().parent().attr('id');
			}else {
				post_id = question_close.parent().parent().parent().parent().attr('id');
			}
			post_id = post_id.replace("post-","");
			question_close.hide();
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : 'question_close', post_id : post_id },
				success:function(data) {
					location.reload();
				}
			});
			return false;
		});
	}
	
	if (jQuery(".question-open a").length) {
		jQuery(".question-open a").click(function () {
			question_open = jQuery(this);
			if (jQuery(".edit-delete-follow-close-2").length > 0) {
				post_id = question_open.parent().parent().parent().parent().parent().attr('id');
			}else {
				post_id = question_open.parent().parent().parent().parent().attr('id');
			}
			post_id = post_id.replace("post-","");
			question_open.hide();
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : 'question_open', post_id : post_id },
				success:function(data) {
					location.reload();
				}
			});
			return false;
		});
	}
	
	if (jQuery(".question-delete a").length) {
		jQuery(".question-delete a").click(function () {
			if (confirm(askme_js.sure_delete)) {
				return true;
			}else {
				return false;
			}
		});
	}
	
	if (jQuery("li.comment").length) {
		jQuery(document).on("click",".best_answer_re",function() {
			var best_answer_re = jQuery(this);
			var nonce = best_answer_re.data("nonce");
			var comment_id = best_answer_re.parent().parent().attr('id');
			comment_id = comment_id.replace("comment-","");
			
			jQuery(".best_answer_re").hide();
			best_answer_re.parent().find(" > .loader_3").show();
			
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : 'best_answer_re', comment_id : comment_id, askme_best_answer_nonce : nonce },
				success:function(data) {
					best_answer_re.parent().parent().parent().removeClass("comment-best-answer");
					best_answer_re.parent().find(" > .loader_3").hide();
					best_answer_re.parent().find("div.commentform.question-answered").remove();
					jQuery(".comment-body .text").after('<a class="commentform best_answer_a question-report" data-nonce="'+askme_js.askme_best_answer_nonce+'" title="'+askme_js.choose_best_answer+'" href="#">'+askme_js.choose_best_answer+'</a>');
					best_answer_re.remove();
				}
			});
			return false;
		});
		
		jQuery(document).on("click",".best_answer_a",function() {
			var best_answer_a = jQuery(this);
			var nonce = best_answer_a.data("nonce");
			var comment_id = best_answer_a.parent().parent().attr('id');
			comment_id = comment_id.replace("comment-","");
			
			jQuery(".best_answer_a").hide();
			best_answer_a.parent().find(" > .loader_3").show();
			
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : 'best_answer', comment_id : comment_id, askme_best_answer_nonce : nonce },
				success:function(data) {
					best_answer_a.parent().parent().parent().addClass("comment-best-answer");
					best_answer_a.parent().find(".text").after('<div class="commentform question-answered question-answered-done"><i class="icon-ok"></i>'+askme_js.best_answer+'</div><div class="clearfix"></div><a class="commentform best_answer_re question-report" data-nonce="'+askme_js.askme_best_answer_nonce+'" title="'+askme_js.cancel_best_answer+'" href="#">'+askme_js.cancel_best_answer+'</a>');
					best_answer_a.parent().find(" > .loader_3").hide();
					best_answer_a.remove();
				}
			});
			return false;
		});
	}
	
	if (jQuery(".comment-best-answer").length > 0) {
		jQuery(".comment-best-answer").prependTo("ol.commentlist");
		jQuery(".comment-best-answer").hide;
	}
	
	if (jQuery("#respond").length) {
		jQuery(document).on("click",".askme-reply-link",function () {
			jQuery(".show-answer-form").remove();
			jQuery(".comment-form-hide,.comment-form-hide").show();
			var reply_link = jQuery(this);
			jQuery(".askme-cancel-link").remove();
			jQuery("html,body").animate({scrollTop: jQuery("#respond").offset().top-35},"slow");
			jQuery("#respond #comment_parent").val(reply_link.attr("data-id"));
			jQuery("#respond .form-submit").before('<div class="askme-cancel-link cancel-comment-reply"><a rel="nofollow" id="cancel-comment-reply-link" href="#respond">'+askme_js.cancel_reply+'</a></div>');
			return false;
		});
		
		jQuery(document).on("click",".askme-cancel-link a",function () {
			jQuery(".askme-cancel-link").remove();
			jQuery("#respond #comment_parent").val(0);
			return false;
		});
	}
	
	if (jQuery(".vote_not_user").length) {
		jQuery(".vote_not_user").on("click",function() {
			var this_vote_q = this;
			jQuery(this_vote_q).hide();
			jQuery(this_vote_q).parent().find(".loader_3").show();
			if (jQuery(this_vote_q).hasClass("single-question-vote-up") || jQuery(this_vote_q).hasClass("single-question-vote-down")) {
				jQuery(this).parent().parent().parent().parent().parent().find(".no_vote_more").hide(10).text(askme_js.no_vote_user).slideDown(300).delay(1200).hide(300);
			}else {
				jQuery(this).parent().parent().parent().parent().find(".no_vote_more").hide(10).text(askme_js.no_vote_user).slideDown(300).delay(1200).hide(300);
			}
			jQuery(this_vote_q).parent().find(".loader_3").hide();
			jQuery(this_vote_q).delay(500).show();
			return false;
		});
	}
	
	if (jQuery(".question_vote_up").length) {
		jQuery(".question_vote_up").each(function () {
			var this_vote_each = jQuery(this);
			if (this_vote_each.parent().find(".vote_allow").length) {
				this_vote_each.parent().find(".vote_allow").on("click",function() {
					var this_vote_q = this;
					var question_closest = jQuery(this_vote_q).closest(".question");
					var id = jQuery(this).attr('id');
					id = id.replace('question_vote_up-',"");
					
					jQuery(this_vote_q).hide();
					jQuery(this_vote_q).parent().find(".question_vote_down").hide();
			
					if (jQuery(this).hasClass("ask_yes-"+id)) {
						question_closest.find(".no_vote_more").hide(10).text(askme_js.no_vote_more).slideDown(300).delay(1200).hide(300);
						question_closest.find(".loader_3").hide();
						jQuery(this_vote_q).delay(500).show();
						jQuery(this_vote_q).parent().find(".question_vote_down").delay(500).show();
					}else {
						jQuery.ajax({
							url: askme_js.admin_url,
							type: "POST",
							data: { action : 'question_vote_up', id : id },
							success:function(data) {
								if (data.indexOf('no_vote_more') >= 0) {
									data = data.replace("no_vote_more","");
									question_closest.find(".no_vote_more").hide(10).text(askme_js.no_vote_more).slideDown(300).delay(1200).hide(300);
								}else {
									if (data > 0) {
										question_closest.find(".question_vote_result").removeClass("question_vote_red");
									}else if (data == 0) {
										question_closest.find(".question_vote_result").removeClass("question_vote_red");
									}else if (data < 0) {
										question_closest.find(".question_vote_result").addClass("question_vote_red");
									}
								}
								question_closest.find(".question_vote_result").html(data);
								question_closest.find(".loader_3").hide();
								jQuery(this_vote_q).delay(500).show();
								jQuery(this_vote_q).parent().find(".question_vote_down").delay(500).show();
							}
						});
					}
					return false;
				});
			}else if (this_vote_each.parent().find(".vote_not_allow")) {
				this_vote_each.parent().find(".vote_not_allow").on("click",function() {
					var this_vote_q = this;
					var question_closest = jQuery(this_vote_q).closest(".question");
					jQuery(this_vote_q).hide();
					jQuery(this_vote_q).parent().find(".loader_3").show();
					question_closest.find(".no_vote_more").hide(10).text(askme_js.no_vote_question).slideDown(300).delay(1200).hide(300);
					jQuery(this_vote_q).parent().find(".loader_3").hide();
					jQuery(this_vote_q).delay(500).show();
					return false;
				});
			}
		});
	}
	
	if (jQuery(".question_vote_down").length) {
		jQuery(".question_vote_down").each(function () {
			var this_vote_each = jQuery(this);
			if (this_vote_each.parent().find(".vote_allow").length) {
				this_vote_each.parent().find(".vote_allow").on("click",function() {
					var this_vote_q = this;
					var question_closest = jQuery(this_vote_q).closest(".question");
					var id = jQuery(this).attr('id');
					id = id.replace('question_vote_down-',"");
					jQuery(this_vote_q).hide();
					jQuery(this_vote_q).parent().find(".question_vote_up").hide();
			
					if (jQuery(this).hasClass("ask_yes-"+id)) {
						question_closest.find(".no_vote_more").hide(10).text(askme_js.no_vote_more).slideDown(300).delay(1200).hide(300);
						question_closest.find(".loader_3").hide();
						jQuery(this_vote_q).delay(500).show();
						jQuery(this_vote_q).parent().find(".question_vote_up").delay(500).show();
					}else {
						jQuery.ajax({
							url: askme_js.admin_url,
							type: "POST",
							data: { action : 'question_vote_down', id : id },
							success:function(data) {
								if (data.indexOf('no_vote_more') >= 0) {
									data = data.replace("no_vote_more","");
									question_closest.find(".no_vote_more").hide(10).text(askme_js.no_vote_more).slideDown(300).delay(1200).hide(300);
								}else {
									if (data > 0) {
										question_closest.find(".question_vote_result").removeClass("question_vote_red");
									}else if (data == 0) {
										question_closest.find(".question_vote_result").removeClass("question_vote_red");
									}else if (data < 0) {
										question_closest.find(".question_vote_result").addClass("question_vote_red");
									}
								}
								question_closest.find(".question_vote_result").html(data);
								question_closest.find(".loader_3").hide();
								jQuery(this_vote_q).delay(500).show();
								jQuery(this_vote_q).parent().find(".question_vote_up").delay(500).show();
							}
						});
					}
					return false;
				});
			}else if (this_vote_each.parent().find(".vote_not_allow")) {
				this_vote_each.parent().find(".vote_not_allow").on("click",function() {
					var this_vote_q = this;
					var question_closest = jQuery(this_vote_q).closest(".question");
					jQuery(this_vote_q).hide();
					jQuery(this_vote_q).parent().find(".loader_3").show();
					question_closest.find(".no_vote_more").hide(10).text(askme_js.no_vote_question).slideDown(300).delay(1200).hide(300);
					jQuery(this_vote_q).parent().find(".loader_3").hide();
					jQuery(this_vote_q).delay(500).show();
					return false;
				});
			}
		});
	}
	
	if (jQuery(".comment_vote_up").length) {
		jQuery(".comment_vote_up").each(function () {
			var this_vote_each = jQuery(this);
			if (this_vote_each.parent().find(".vote_allow").length) {
				this_vote_each.parent().find(".vote_allow").on("click",function() {
					var this_vote = jQuery(this);
					var id = this_vote.attr('id');
					id = id.replace('comment_vote_up-',"");
					
					this_vote.parent().hide();
					this_vote.parent().parent().find(".comment_vote_down").parent().hide();
					this_vote.closest(".comment").find(".loader_3").show();
			
					var post_id = this_vote.parent().parent().parent().parent().parent().parent().attr('rel');
					post_id = post_id.replace('posts-',"");
					if (this_vote.hasClass("ask_yes_comment-"+id)) {
						this_vote.closest(".comment").find(".no_vote_more").hide(10).text(askme_js.no_vote_more_answer).slideDown(300).delay(1200).hide(300);
						this_vote.closest(".comment").find(".loader_3").delay(300).hide(10);
						this_vote.parent().delay(300).show(1);
						this_vote.parent().parent().find(".comment_vote_down").parent().delay(300).show(1);
					}else {
						jQuery.ajax({
							url: askme_js.admin_url,
							type: "POST",
							data: { action : 'comment_vote_up', id : id, post_id : post_id },
							success:function(data) {
								if (data.indexOf('no_vote_more') >= 0) {
									data = data.replace("no_vote_more","");
									this_vote.closest(".comment").find(".no_vote_more").hide(10).text(askme_js.no_vote_more_answer).slideDown(300).delay(1200).hide(300);
								}else {
									if (data > 0) {
										jQuery("#comment-"+id).find(".question_vote_result").removeClass("question_vote_red");
									}else if (data == 0) {
										jQuery("#comment-"+id).find(".question_vote_result").removeClass("question_vote_red");
									}else if (data < 0) {
										jQuery("#comment-"+id).find(".question_vote_result").addClass("question_vote_red");
									}
								}
								jQuery("#comment-"+id).find(".question_vote_result").html(data);
								this_vote.closest(".comment").find(".loader_3").hide();
								this_vote.parent().delay(500).show();
								this_vote.parent().parent().find(".comment_vote_down").parent().delay(500).show();
							}
						});
					}
					return false;
				});
			}else if (this_vote_each.parent().find(".vote_not_allow")) {
				this_vote_each.parent().find(".vote_not_allow").on("click",function() {
					var this_vote_q = jQuery(this);
					this_vote_q.hide();
					this_vote_q.parent().find(".loader_3").show();
					this_vote_q.parent().parent().parent().parent().parent().find(".no_vote_more").hide(10).text(askme_js.no_vote_answer).slideDown(300).delay(1200).hide(300);
					this_vote_q.parent().find(".loader_3").hide();
					this_vote_q.delay(500).show();
					return false;
				});
			}
		});
	}
	
	if (jQuery(".comment_vote_down").length) {
		jQuery(".comment_vote_down").each(function () {
			var this_vote_each = jQuery(this);
			if (this_vote_each.parent().find(".vote_allow").length) {
				this_vote_each.parent().find(".vote_allow").on("click",function() {
					var this_vote = this;
					var id = jQuery(this).attr('id');
					id = id.replace('comment_vote_down-',"");
			
					jQuery(this_vote).parent().hide();
					jQuery(this_vote).parent().parent().find(".comment_vote_up").parent().hide();
					jQuery(this_vote).closest(".comment").find(".loader_3").show();
			
					var post_id = jQuery(this).parent().parent().parent().parent().parent().parent().attr('rel');
					post_id = post_id.replace('posts-',"");
					
					if (jQuery(this).hasClass("ask_yes_comment-"+id)) {
						jQuery(this_vote).closest(".comment").find(".no_vote_more").hide(10).text(askme_js.no_vote_more_answer).slideDown(300).delay(1200).hide(300);
						jQuery(this_vote).closest(".comment").find(".loader_3").delay(300).hide(10);
						jQuery(this_vote).parent().delay(300).show(1);
						jQuery(this_vote).parent().parent().find(".comment_vote_up").parent().delay(300).show(1);
					}else {
						jQuery.ajax({
							url: askme_js.admin_url,
							type: "POST",
							data: { action : 'comment_vote_down', id : id, post_id : post_id },
							success:function(data) {
								if (data.indexOf('no_vote_more') >= 0) {
									data = data.replace("no_vote_more","");
									jQuery(this_vote).closest(".comment").find(".no_vote_more").hide(10).text(askme_js.no_vote_more_answer).slideDown(300).delay(1200).hide(300);
								}else {
									if (data > 0) {
										jQuery("#comment-"+id).find(".question_vote_result").removeClass("question_vote_red");
									}else if (data == 0) {
										jQuery("#comment-"+id).find(".question_vote_result").removeClass("question_vote_red");
									}else if (data < 0) {
										jQuery("#comment-"+id).find(".question_vote_result").addClass("question_vote_red");
									}
								}
								jQuery("#comment-"+id).find(".question_vote_result").html(data);
								jQuery(this_vote).closest(".comment").find(".loader_3").hide();
								jQuery(this_vote).parent().delay(500).show();
								jQuery(this_vote).parent().parent().find(".comment_vote_up").parent().delay(500).show();
							}
						});
					}
					return false;
				});
			}else if (this_vote_each.parent().find(".vote_not_allow")) {
				this_vote_each.parent().find(".vote_not_allow").on("click",function() {
					var this_vote_q = this;
					jQuery(this_vote_q).hide();
					jQuery(this_vote_q).parent().find(".loader_3").show();
					jQuery(this).parent().parent().parent().parent().parent().find(".no_vote_more").hide(10).text(askme_js.no_vote_answer).slideDown(300).delay(1200).hide(300);
					jQuery(this_vote_q).parent().find(".loader_3").hide();
					jQuery(this_vote_q).delay(500).show();
					return false;
				});
			}
		});
	}
	
	if (jQuery(".report_q").length) {
		jQuery(".report_q").on("click",function() {
			report_q = jQuery(this);
			post_id = report_q.parent().attr("id");
			post_id = post_id.replace('post-',"");
			
			report_q.parent().find(".explain-reported").slideDown();
			report_q.parent().find(".cancel").click(function () {
				report_q.parent().find(".explain-reported").slideUp();
				return false;
			});
			
			report_q.parent().find(".report").click(function () {
				report = jQuery(this);
				var explain = report_q.parent().find(".explain-reported textarea");
				report_q.parent().find(".required-error").remove();
				if (explain.val() == '') {
					explain.after('<span class="required-error red">'+askme_js.ask_error_text+'</span>');
				}else {
					report.hide();
					report.parent().find(".loader_3").show();
					report.parent().find(".cancel").hide();
					report_q.parent().find(".required-error").remove();
					jQuery.ajax({
						url: askme_js.admin_url,
						type: "POST",
						data: { action : 'report_q', post_id : post_id, explain : explain.val() },
						success:function(data) {
							explain.val("");
							report.show();
							report.parent().find(".cancel").show();
							report_q.parent().find(".explain-reported").slideUp();
							report.parent().find(".loader_3").hide();
							report_q.delay(500).show();
						}
					});
				}
				return false;
			});
			return false;
		});
	}
	
	if (jQuery(".report_c").length) {
		jQuery(".report_c").on("click",function() {
			report_c = jQuery(this);
			comment_id = report_c.parent().parent().parent().parent().parent().attr("id");
			comment_id = comment_id.replace("li-comment-","");
			
			report_c.parent().parent().parent().find(".explain-reported").slideDown();
			report_c.parent().parent().parent().find(".cancel").click(function () {
				report_c.parent().parent().parent().find(".explain-reported").slideUp();
				return false;
			});
			
			report_c.parent().parent().parent().find(".report").click(function () {
				report = jQuery(this);
				var explain = report_c.parent().parent().parent().find(".explain-reported textarea");
				report_c.parent().parent().parent().find(".required-error").remove();
				if (explain.val() == '') {
					explain.after('<span class="required-error red">'+askme_js.ask_error_text+'</span>');
				}else {
					report.hide();
					report.parent().parent().parent().find(".explain-reported .loader_3").show();
					report.parent().parent().parent().find(".cancel").hide();
					report_c.parent().parent().parent().find(".required-error").remove();
					jQuery.ajax({
						url: askme_js.admin_url,
						type: "POST",
						data: { action : 'report_c', comment_id : comment_id, explain : explain.val() },
						success:function(data) {
							explain.val("");
							report.show();
							report.parent().parent().parent().find(".cancel").show();
							report_c.parent().parent().parent().find(".explain-reported").slideUp();
							report.parent().parent().parent().find(".explain-reported .loader_3").hide();
							report_c.delay(500).show();
						}
					});
				}
				return false;
			});
			return false;
		});
	}
	
	if (jQuery(".report_user").length) {
		jQuery(".report_user").on("click",function() {
			var report_user = jQuery(this);
			var user_id = report_user.attr("href");
			
			report_user.parent().parent().parent().find(".explain-reported").slideDown();
			report_user.parent().parent().parent().find(".cancel").click(function () {
				report_user.parent().parent().parent().find(".explain-reported").slideUp();
				return false;
			});
			
			report_user.parent().parent().parent().find(".report").click(function () {
				report = jQuery(this);
				var explain = report_user.parent().parent().parent().find(".explain-reported textarea");
				report_user.parent().parent().parent().find(".required-error").remove();
				if (explain.val() == '') {
					explain.after('<span class="required-error red">'+askme_js.ask_error_text+'</span>');
				}else {
					report.hide();
					report.parent().parent().parent().find(".explain-reported .loader_3").show();
					report.parent().parent().parent().find(".cancel").hide();
					report_user.parent().parent().parent().find(".required-error").remove();
					jQuery.ajax({
						url: askme_js.admin_url,
						type: "POST",
						data: { action : 'askme_report_user', user_id : user_id, explain : explain.val() },
						success:function(data) {
							explain.val("");
							report.show();
							report.parent().parent().parent().find(".cancel").show();
							report_user.parent().parent().parent().find(".explain-reported").slideUp();
							report.parent().parent().parent().find(".explain-reported .loader_3").hide();
							report_user.delay(500).show();
						}
					});
				}
				return false;
			});
			return false;
		});
	}
	
	if (jQuery(".poll_results").length) {
		jQuery(".poll_results").on("click",function() {
			jQuery(".poll_2").fadeOut(500);
			jQuery(".poll_1").delay(500).slideDown(500);
			return false;
		});
	}
	
	if (jQuery(".poll_polls").length) {
		jQuery(".poll_polls").on("click",function() {
			jQuery(".poll_1").fadeOut(500);
			jQuery(".poll_2").delay(500).slideDown(500);
			return false;
		});
	}
	
	if (jQuery(".question_poll_end").length) {
		jQuery(".question_poll_end input[type='radio']").on("click",function() {
			var question_poll = jQuery(this);
			var askme_form = question_poll.closest(".askme_form");
			
			question_poll.parent().parent().parent().find("input,label").hide();
			var poll_2 = question_poll.closest(".poll_2");
			askme_form.closest(".question_poll_end").find(".loader_3").show();
	
			var poll_id = question_poll.val();
			poll_id = poll_id.replace('poll_',"");
			
			var poll_title = question_poll.attr("data-rel");
			poll_title = poll_title.replace('poll_',"");
			
			var post_id = question_poll.closest("article.question").attr("id");
			post_id = post_id.replace('post-',"");
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : 'askme_question_poll', poll_id : poll_id, poll_title : poll_title, post_id : post_id },
				success:function(data) {
					if (data == "no_poll" || data == "must_login") {
						question_poll.parent().parent().parent().find("input,label").show();
						askme_form.closest(".question_poll_end").find(".alert-message.error").text((data == "must_login"?askme_js.must_login:askme_js.no_poll_more)).slideDown(200).delay(5000).slideUp(200);
						askme_form.closest(".question_poll_end").find(".loader_3").hide();
						askme_form.find(".ed_button").show();
					}else {
						var poll_main = poll_2.parent();
						poll_main.html(data);
						jQuery(".progressbar-percent").each(function(){
							var $this = jQuery(this);
							var percent = $this.attr("attr-percent");
							$this.bind("inview", function(event, isInView, visiblePartX, visiblePartY) {
								if (isInView) {
									$this.animate({ "width" : percent + "%"}, 700);
								}
							});
						});
					}
				}
			});
		});
	}
	
	if (jQuery(".ask_anonymously").length) {
		jQuery(".ask_anonymously").each(function () {
			var ask_anonymously = jQuery(this);
			if (ask_anonymously.is(":checked")) {
				ask_anonymously.parent().find(".ask_named").hide(10);
				ask_anonymously.parent().find(".ask_none").show(10);
			}else {
				ask_anonymously.parent().find(".ask_named").show(10);
				ask_anonymously.parent().find(".ask_none").hide(10);
			}
			
			ask_anonymously.click(function () {
				var ask_anonymously_c = jQuery(this);
				if (ask_anonymously_c.is(":checked")) {
					ask_anonymously_c.parent().find(".ask_named").hide(10);
					ask_anonymously_c.parent().find(".ask_none").show(10);
				}else {
					ask_anonymously_c.parent().find(".ask_named").show(10);
					ask_anonymously_c.parent().find(".ask_none").hide(10);
				}
			});
		});
	}
	
	if (jQuery(".paid-details").length) {
		jQuery(".paid-details").on("click",function() {
			jQuery(".paid-question-area").slideToggle(200);
			return false;
		});
	}
	
	if (jQuery(".pay-to-sticky").length) {
		jQuery(".pay-to-sticky").on("click",function() {
			jQuery(".pay-to-sticky-area").slideToggle(200);
			jQuery(this).toggleClass("pay-to-sticky-slide");
			return false;
		});
	}
	
	if (jQuery(".add_favorite").length) {
		jQuery(".add_favorite").click(function () {
			add_favorite = jQuery(this);
			post_id = add_favorite.parent().parent().parent().attr("id");
			post_id = post_id.replace('post-',"");
			
			add_favorite.hide();
			add_favorite.parent().find(".loader_2").show();
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : 'add_favorite', post_id : post_id },
				success:function(data) {
					location.reload();
				}
			});
			return false;
		});
	}
	
	if (jQuery(".remove_favorite").length) {
		jQuery(".remove_favorite").click(function () {
			remove_favorite = jQuery(this);
			if (remove_favorite.hasClass("question-remove")) {
				post_id = remove_favorite.parent().parent().attr("id");
			}else {
				post_id = remove_favorite.parent().parent().parent().attr("id");
			}
			post_id = post_id.replace('post-',"");
			
			remove_favorite.hide();
			remove_favorite.parent().find(".loader_2").show();
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: { action : 'remove_favorite', post_id : post_id },
				success:function(data) {
					location.reload();
				}
			});
			return false;
		});
	}
	
	if (jQuery(".user-profile").length) {
		jQuery(document).on("click",".following_not",function () {
			following_not = jQuery(this);
			following_not_id = following_not.attr("rel");
			following_nonce = following_not.attr("data-nonce");
			following_not.hide();
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: {action:'following_not_ajax',following_not_id:following_not_id,following_nonce:following_nonce},
				success:function(data) {
					jQuery(".followers span span").text(data);
					following_not.addClass("following_you").removeClass("following_not").text(askme_js.follow_question).show();
				}
			});
			return false;
		});
		
		jQuery(document).on("click",".following_you",function () {
			following_you = jQuery(this);
			following_you_id = following_you.attr("rel");
			following_nonce = following_you.attr("data-nonce");
			following_you.hide();
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: {action:'following_me_ajax',following_you_id:following_you_id,following_nonce:following_nonce},
				success:function(data) {
					jQuery(".followers span span").text(data);
					following_you.addClass("following_not").removeClass("following_you").text(askme_js.unfollow_question).show();
				}
			});
			return false;
		});
	}

	/* Payments */

	if (jQuery(".payment-methods a").length) {
		jQuery(".payment-tabs").on("click","a",function () {
			var payment = jQuery(this);
			var payment_hide = payment.attr("href");
			var payment_button = payment.parent().parent().find("a");
			var payment_wrap = payment.closest(".payment-wrap");
			if (payment_wrap.hasClass("payment-wrap-2")) {
				payment_button.removeClass("payment-style-activate");
				payment.addClass("payment-style-activate");
			}else {
				payment_button.addClass("button-default-2").addClass("btn__primary").removeClass("button-default-3").removeClass("btn__info");
				payment.addClass("button-default-3").addClass("btn__info").removeClass("button-default-2").removeClass("btn__primary");
			}
			payment_wrap.find(".payment-method").hide(10);
			payment_wrap.find(".payment-method[data-hide="+payment_hide+"]").slideDown(300);
			return false;
		});
	}

	/* Stripe */

	if (jQuery(".askme-stripe-payment").length && askme_js.publishable_key != "") {
		var stripe = Stripe(askme_js.publishable_key);

		function isInViewport(the_element) {
			var $window = jQuery(window);
			var viewPortTop = $window.scrollTop();
			var viewPortBottom = viewPortTop + $window.height();
			var elementTop = the_element.offset().top;
			var elementBottom = elementTop + the_element.outerHeight();
			return ((elementBottom <= viewPortBottom) && (elementTop >= viewPortTop));
		}

		function askme_stirpe_paypemt() {
			var $cards = jQuery('.askme-stripe-payment');

			if ($cards.length === 0) {
				return;
			}

			$cards.each(function () {
				var $form = jQuery(this).parents('form:first');
				var formId = $form.data('id');
				var elements = stripe.elements();
				var cardElement = elements.create('card', {
					hidePostalCode: true,
					classes: {
						base: 'askme-stripe-payment',
						empty: 'askme-stripe-payment-empty',
						focus: 'askme-stripe-payment-focus',
						complete: 'askme-stripe-payment-complete',
						invalid: 'askme-stripe-payment-error'
					},
					style: {
						base: {
							color: '#7c7f85',
							fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Oxygen-Sans", Ubuntu, Cantarell, "Helvetica Neue", sans-serif',
							fontSmoothing: 'antialiased',
							fontSize: '15px',
							'::placeholder': {
								color: '#7F8393'
							}
						},
						invalid: {
							color: '#7c7f85',
							iconColor: '#CC3434'
						}
					}
				});
				cardElement.mount('div.askme-stripe-payment[data-id="'+formId+'"]');
				cardElement.addEventListener('change', function (event) {
					var $form = jQuery(this).parents('form:first');
					if (event.error) {
						jQuery('.ask_error', $form).text(event.error.message).animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
					}
				});

				$form.on("submit",function () {
					jQuery('.load_span',$form).show();
					jQuery('input[type="submit"]',$form).prop('disabled', true).hide();
					jQuery('input[name="payment-method-id"]', $form).remove();
					jQuery('input[name="payment-intent-id"]', $form).remove();
					var payment_data = {};
					var card_name_input = jQuery('input[name="name"]', $form);
					if (card_name_input.length > 0) {
						var card_name = card_name_input.val();
						if (card_name != null && card_name != '') {
							payment_data.billing_details = {
								name: card_name
							};
						}
					}
					stripe.createPaymentMethod('card',cardElement,payment_data).then(function (payment_result) {
						if (payment_result.error) {
							jQuery('.load_span',$form).hide();
							jQuery('input[type="submit"]',$form).prop('disabled', false).show();
							jQuery('.ask_error', $form).text(payment_result.error.message).animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
							var the_element = jQuery('.askme-stripe-payment', $form);
							if (the_element && the_element.offset() && the_element.offset().top) {
								if (!isInViewport(the_element)) {
									jQuery('html, body').animate({scrollTop: the_element.offset().top - 100},1000);
								}
							}
							if (the_element) {
								the_element.fadeIn(500).fadeOut(500).fadeIn(500);
							}
						}else {
							if (typeof(payment_result) !== 'undefined' && payment_result.hasOwnProperty('paymentMethod') && payment_result.paymentMethod.hasOwnProperty('id')) {
								jQuery('<input>').attr({type: 'hidden',name: 'payment-method-id',value: payment_result.paymentMethod.id}).appendTo($form);
							}
							submit_ajax($form, cardElement);
						}
					});
					return false;
				});
			});
		}

		function submit_ajax($form, card) {
			jQuery.ajax({
				type: "POST",
				url: askme_js.admin_url,
				data: $form.serialize(),
				cache: false,
				dataType: "json",
				success: function (data) {
					if (data.error) {
						jQuery('.ask_error', $form).text(data.error).animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
						jQuery('.load_span',$form).hide();
						jQuery('input[type="submit"]',$form).prop('disabled', false).show();
					}else if (data.success) {
						var formId = $form.data('id');
						if (card != null) {
							card.clear();
						}
						jQuery('input[name="payment-method-id"]', $form).remove();
						jQuery('input[name="payment-intent-id"]', $form).remove();
						if (data.redirect) {
							setTimeout(function () {
								window.location = data.redirect;
							}, 1500);
						}
					}else if (typeof(data) !== 'undefined' && data.hasOwnProperty('confirm_card') && data.confirm_card == 1) {
						confirm_card_payment($form, card, data);
					}else if (typeof(data) !== 'undefined' && data.hasOwnProperty('resubmit_again') && data.resubmit_again == 1) {
						jQuery('.load_span',$form).show();
						jQuery('input[type="submit"]',$form).prop('disabled', true).hide();
						submit_ajax($form, card);
					}
				},error: function (jqXHR, textStatus, errorThrown) {
					// Error
				},complete: function (data) {
					// Done
				}
			});
		}

		function confirm_card_payment($form, card, data) {
			stripe.confirmCardPayment(data.client_secret).then(function (result) {
				if (result.error) {
					jQuery('.ask_error', $form).text((result.error.hasOwnProperty('message')?result.error.message:result.error)).animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
				}else {
					jQuery('input[name="payment-intent-id"]', $form).remove();
					if (typeof(result) !== 'undefined' && result.hasOwnProperty('paymentIntent') && result.paymentIntent.hasOwnProperty('id')) {
						jQuery('<input>').attr({type: 'hidden',name: 'payment-intent-id',value: result.paymentIntent.id}).appendTo($form);
					}
					jQuery('.load_span',$form).show();
					jQuery('input[type="submit"]',$form).prop('disabled', true).hide();
					submit_ajax($form, card);
				}
			});
		}

		askme_stirpe_paypemt();
	}
	
	/* Add Point */
	
	if (jQuery(".form-add-point a").length) {
		jQuery(".form-add-point a").click(function () {
			var point_a = jQuery(this);
			var input_add = jQuery("#input-add-point");
			var input_add_point = input_add.val();
			point_a.hide();
			point_a.parent().parent().parent().find(".loader_2").show();
			post_id = point_a.parent().parent().parent().parent().parent().attr("id");
			post_id = post_id.replace('post-',"");
			jQuery.ajax({
				url: askme_js.admin_url,
				type: "POST",
				data: {action:'askme_add_point',input_add_point:input_add_point,post_id:post_id},
				success:function(data) {
					point_a.parent().parent().parent().find(".no_vote_more").hide(10).text(data).slideDown(300).delay(1200).hide(300);
					point_a.show();
					point_a.parent().parent().parent().find(".loader_2").hide();
					input_add.val("");
				}
			});
			return false;
		});
	}
	
	/* Login panel */
	
	if (jQuery(".login-side-link").length) {
		jQuery('.login-side-link').on('touchstart click',function () {
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
			jQuery('.mobile-menu-wrap.mobile-login-wrap').addClass('mobile-aside-open');
			return false;
		});
	}
	
	if (jQuery(".login-panel-link").length) {
		jQuery(".login-panel-link").click(function () {
			if (jQuery(this).hasClass("header-top-active")) {
				jQuery(".login-panel").slideUp(500);
				jQuery(this).removeClass("header-top-active");
				jQuery(this).find("i").addClass("icon-user");
				jQuery(this).find("i").removeClass("icon-remove");
			}else {
				jQuery('.mobile-aside').removeClass('mobile-aside-open');
				jQuery(".login-panel").slideDown(500);
				jQuery(this).addClass("header-top-active");
				jQuery(this).find("i").removeClass("icon-user");
				jQuery(this).find("i").addClass("icon-remove");
			}
			return false;
		});
	}
	
	/* Login */
	
	if (jQuery(".login-form").length) {
		jQuery(".login-form").on("submit",function() {
			var thisform = jQuery(this);
			jQuery('.required-error',thisform).remove();
			jQuery('input[type="submit"]',thisform).hide();
			jQuery('.loader_2',thisform).show().css({"display":"block"});
			var fields = jQuery('.inputs',thisform);
			jQuery('.required-item',thisform).each(function () {
				var required = jQuery(this);
				if (required.val() == '') {
					required.after('<span class=required-error>'+askme_js.ask_error_text+'</span>');
					return false;
				}
			});
			
			if (jQuery('.ask_captcha',thisform).length > 0) {
				var ask_captcha = jQuery('.ask_captcha',thisform);
				var url = askme_js.v_get_template_directory_uri+"/captcha/captcha.php";
				var postStr = ask_captcha.attr("name")+"="+encodeURIComponent(ask_captcha.val());
				
				if (ask_captcha.val() == "") {
					ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_text+'</span>');
					jQuery('.loader_2',thisform).hide().css({"display":"none"});
					jQuery('input[type="submit"]',thisform).show();
					return false;
				}else if (ask_captcha.hasClass("captcha_answer")) {
					if (ask_captcha.val() != askme_js.captcha_answer) {
						ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_captcha+'</span>');
						jQuery('.loader_2',thisform).hide().css({"display":"none"});
						jQuery('input[type="submit"]',thisform).show();
						return false;
					}
				}else {
					message = "";
					jQuery.ajax({
						url:  url,
						type: "POST",
						data: postStr,
						async:false,
						success: function(data){
							message = data;
						}
					});
					if (message == "ask_captcha_0") {
						ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_captcha+'</span>');
						jQuery('.loader_2',thisform).hide().css({"display":"none"});
						jQuery('input[type="submit"]',thisform).show();
						return false;
					}
				}
			}
			
			var data = {
				action: 		'ask_ajax_login_process',
				log: 			jQuery('input[name=\"log\"]',thisform).val(),
				pwd: 			jQuery('input[name=\"pwd\"]',thisform).val(),
				redirect_to:	jQuery('input[name=\"redirect_to\"]',thisform).val()
			};
			jQuery.post(jQuery('input[name=\"ajax_url\"]',thisform).val(),data,function(response) {
				var result = jQuery.parseJSON(response);
				if (result.success != null && result.success == 1) {
					window.location = result.redirect;
				}else if (result.error) {
					jQuery(".ask_error",thisform).hide(10).slideDown(300).html('<strong>'+result.error+'</strong>').delay(3000).slideUp(300);
				}else {
					return true;
				}
				jQuery('.loader_2',thisform).hide().css({"display":"none"});
				jQuery('input[type="submit"]',thisform).show();
			});
			return false;
		});
	}
	
	/* Login */
	
	if (jQuery(".login-comments,.comment-reply-login,.login-popup").length) {
		jQuery(".login-comments,.comment-reply-login,.login-popup").click(function () {
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
			jQuery(".panel-pop").animate({"top":"-100%"},10).hide();
			jQuery("#login-comments").show().animate({"top":"2%"},500);
			jQuery("html,body").animate({scrollTop:0},500);
			jQuery("body").prepend("<div class='wrap-pop'></div>");
			wrap_pop();
			return false;
		});
	}
	
	/* Signup */
	
	if (jQuery(".signup,.login-links-r a").length) {
		jQuery(".signup,.login-links-r a").click(function () {
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
			jQuery(".panel-pop").animate({"top":"-100%"},10).hide();
			jQuery("#signup").show().animate({"top":"2%"},500);
			jQuery("html,body").animate({scrollTop:0},500);
			jQuery("body").prepend("<div class='wrap-pop'></div>");
			wrap_pop();
			return false;
		});
	}
	
	if (jQuery(".signup_form").length) {
		jQuery(".signup_form").on("submit",function () {
			var whatsubmit_s = true;
			var thisform = jQuery(this);
			jQuery('.required-error',thisform).remove();
			if (jQuery('.ask_captcha',thisform).length > 0) {
				var ask_captcha = jQuery('.ask_captcha',thisform).parent().find("input");
				var url = askme_js.v_get_template_directory_uri+"/captcha/captcha.php";
				var postStr = ask_captcha.attr("name")+"="+encodeURIComponent(ask_captcha.val());
				if (ask_captcha.val() == "") {
					ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_text+'</span>');
					whatsubmit_s = false;
				}else if (ask_captcha.hasClass("captcha_answer")) {
					if (ask_captcha.val() != askme_js.captcha_answer) {
						whatsubmit_s = false;
						ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_captcha+'</span>');
					}else {
						whatsubmit_s = true;
					}
				}else {
					message = "";
					jQuery.ajax({
						url:  url,
						type: "POST",
						data: postStr,
						async:false,
						success: function(data){
							message = data;
						}
					});
					if (message == "ask_captcha_0") {
						ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_captcha+'</span>');
						whatsubmit_s = false;
					}else {
						whatsubmit_s = true;
					}
				}
			}
			jQuery('.required-item',thisform).each(function () {
				var required = jQuery(this);
				if (required.val() == '') {
					if (required.parent().hasClass("styled-select")) {
						required.parent().after('<span class=required-error>'+askme_js.ask_error_text+'</span>');
					}else {
						required.after('<span class=required-error>'+askme_js.ask_error_text+'</span>');
					}
					whatsubmit_s = false;
				}
			});
			if(!whatsubmit_s){
				jQuery('.ask_error',thisform).hide(10).slideDown(300).html('<strong>'+askme_js.ask_error_empty+'</strong>').delay(1000).slideUp(300);
			}
			return whatsubmit_s;
		});
	}
	
	/* Lost password */
	
	if (jQuery(".login-password a").length) {
		jQuery(".login-password a").click(function () {
			jQuery('.mobile-aside').removeClass('mobile-aside-open');
			jQuery(".panel-pop").animate({"top":"-100%"},10).hide();
			jQuery("#lost-password").show().animate({"top":"2%"},500);
			jQuery("html,body").animate({scrollTop:0},500);
			jQuery("body").prepend("<div class='wrap-pop'></div>");
			wrap_pop();
			return false;
		});
	}
	
	if (jQuery(".ask-lost-password").length) {
		jQuery(".ask-lost-password").on("submit",function () {
			var whatsubmit_l = true;
			var thisform = jQuery(this);
			jQuery('.required-error',thisform).remove();
			jQuery('.required-item',thisform).each(function () {
				var required = jQuery(this);
				if (required.val() == '') {
					required.after('<span class=required-error>'+askme_js.ask_error_text+'</span>');
					whatsubmit_l = false;
				}
			});
			if(!whatsubmit_l){
				jQuery('.ask_error',thisform).hide(10).slideDown(300).html('<strong>'+askme_js.ask_error_empty+'</strong>').delay(1000).slideUp(300);
			}
			return whatsubmit_l;
		});
	}
	
	/* Comments & Answers */
	
	if (jQuery("#commentform").length) {
		jQuery("#commentform").on("submit",function () {
			var thisform = jQuery(this);
			jQuery('.required-error',thisform).remove();
			if (jQuery('.ask_captcha',thisform).length > 0) {
				var ask_captcha = jQuery('.ask_captcha',thisform).parent().find("input");
				var url = askme_js.v_get_template_directory_uri+"/captcha/captcha.php";
				var postStr = ask_captcha.attr("name")+"="+encodeURIComponent(ask_captcha.val());
				if (ask_captcha.val() == "") {
					ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_text+'</span>');
					return false;
				}else if (ask_captcha.hasClass("captcha_answer")) {
					if (ask_captcha.val() != askme_js.captcha_answer) {
						ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_captcha+'</span>');
						return false;
					}else {
						return true;
					}
				}else {
					message = "";
					jQuery.ajax({
						url:  url,
						type: "POST",
						data: postStr,
						async:false,
						success: function(data){
							message = data;
						}
					});
					if (message == "ask_captcha_0") {
						ask_captcha.parent().append('<span class="required-error required-error-c">'+askme_js.ask_error_captcha+'</span>');
						return false;
					}else {
						return true;
					}
				}
			}
		});
	}
	
	/* Panel pop */
	
	if (jQuery(".panel-pop h2 i").length) {
		jQuery(".panel-pop h2 i").click(function () {
			jQuery(this).parent().parent().css({"top":"-100%"},500).hide(10);
			jQuery("#send-message .the-title").val("");
			jQuery(".wrap-pop").remove();
		});
	}
	
	function wrap_pop() {
		jQuery(".wrap-pop").click(function () {
			jQuery(".panel-pop").css({"top":"-100%"},500).hide(10);
			jQuery("#send-message .the-title").val("");
			jQuery(this).remove();
		});
	}
	
	/* Select */
	
	if (jQuery(".widget select,select#calc_shipping_country,.woocommerce-sort-by select,.variations .value select").length) {
		jQuery(".widget:not(.signup-widget) select:not(.not-styled-select),select#calc_shipping_country,.woocommerce-sort-by select,.variations .value select").wrap('<div class="styled-select"></div>');
	}
	
	/* Widget */
	
	if (jQuery(".widget li.cat-item,.widget.widget_archive li").length) {
		jQuery(".widget li.cat-item,.widget.widget_archive li").each(function(){var e= jQuery(this).contents();e.length>1&&(e.eq(1).wrap('<span class="widget-span"></span>'),e.eq(1).each(function(){}))}).contents();jQuery(".widget li.cat-item .widget-span,.widget.widget_archive li .widget-span").each(function(){jQuery(this).html(jQuery(this).text().substring(2));jQuery(this).html(jQuery(this).text().replace(/\)/gi,""))});jQuery(".widget li.cat-item").length&&jQuery(".widget li.cat-item .widget-span");
	}
	
	/* Woocommerce */
	
	if (jQuery(".woocommerce").length > 0) {
		jQuery("#calc_shipping_state,#calc_shipping_postcode").parent().addClass("col-md-6").addClass("woocommerce-input");
		jQuery(".woocommerce .woocommerce-input").wrapAll('<div class="row"></div>');
		
		jQuery("ul.products li .product-details h3 a").each(function () {
			var shortlink = jQuery(this);
			var txt = shortlink.text();
			shortlink.html(trunc(txt,askme_js.products_excerpt_title));
		});
	}
	
	function trunc(str,n) {
		return str.substr(0,n-1);
	}
	
	if (jQuery(".cart_control").length) {
		jQuery(document).on('click','.cart_control',function() {
			if (jQuery(this).next('.cart_wrapper').hasClass('cart_wrapper_active')) {
				jQuery(this).next('.cart_wrapper').removeClass('cart_wrapper_active');
				jQuery(this).next('.cart_wrapper').slideUp();
			}else {
				jQuery(this).next('.cart_wrapper').slideDown();
				jQuery(this).next('.cart_wrapper').addClass('cart_wrapper_active');
			}
			return false;
		});
	}
	
	/* Notifications */
	
	if (jQuery(".notifications_control").length) {
		jQuery(document).on('click','.notifications_control',function() {
			if (jQuery(this).next('.notifications-wrapper').hasClass('notifications-wrapper-active')) {
				jQuery(this).next('.notifications-wrapper').removeClass('notifications-wrapper-active');
				jQuery(this).next('.notifications-wrapper').slideUp();
			}else {
				jQuery(this).next('.notifications-wrapper').slideDown();
				jQuery(this).next('.notifications-wrapper').addClass('notifications-wrapper-active');
				jQuery(".notifications_control .numofitems").text("0");
				jQuery.post(askme_js.admin_url,{action:"update_notifications"});
			}
			return false;
		});
	}
	
	/* Widget Menu jQuery */
	
	if (jQuery(".widget_menu_jquery").length) {
		jQuery(".widget_menu_jquery").onePageNav({
			currentClass : "current_page_item",
			changeHash : false,
			scrollSpeed : 750,
			scrollOffset : parseFloat(jQuery("#header").innerHeight())+60
		});
	}
	
	/* Lightbox */
	
	if (jQuery(".active-lightbox").length) {
		var lightboxArgs = {			
			animation_speed: "fast",
			overlay_gallery: true,
			autoplay_slideshow: false,
			slideshow: 5000, // light_rounded / dark_rounded / light_square / dark_square / facebook
			theme: "pp_default", 
			opacity: 0.8,
			show_title: false,
			social_tools: "",
			deeplinking: false,
			allow_resize: true, // Resize the photos bigger than viewport. true/false
			counter_separator_label: "/", // The separator for the gallery counter 1 "of" 2
			default_width: 940,
			default_height: 529
		};
			
		jQuery("a[href$=jpg], a[href$=JPG], a[href$=jpeg], a[href$=JPEG], a[href$=png], a[href$=gif], a[href$=bmp]:has(img)").prettyPhoto(lightboxArgs);
		jQuery("a[class^='prettyPhoto'], a[rel^='prettyPhoto']").prettyPhoto(lightboxArgs);
	}
	
	/* Page load */
	
	jQuery(window).on('load',function() {
		
		/* Loader */
		
		jQuery(".loader").fadeOut(500);
		
		/* Carousel */
		
		if (jQuery(".carousel-all").length) {
			jQuery(".carousel-all").each(function(){
				var $current = jQuery(this);
				var $prev = jQuery(this).find(".carousel-prev");
				var $next = jQuery(this).find(".carousel-next");
				var $effect = jQuery(this).attr("carousel_effect");
				var $auto = jQuery(this).attr("carousel_auto");
				var $responsive = jQuery(this).attr("carousel_responsive");
				var $max = jQuery(this).attr("what_col");
				var $pagination = jQuery(this).find(".carousel-pagination");
				
				if ($current.hasClass("testimonial-carousel")) {
					var $testimonial_width = $current.width();
					$current.find(".testimonial-warp").css("width",$testimonial_width)
				}
				
				if ($max == 1) {
					var $width = 940;
				}
				if ($max == 2) {
					var $width = 460;
				}
				if ($max == 3) {
					var $width = 300;
				}
				if ($max == 4) {
					var $width = 220;
				}
				if ($max == 5) {
					var $width = 220;
				}
				if ($max == 6) {
					var $width = 140;
				}
				
				jQuery(this).find(".slides").carouFredSel({
					circular: false,
					prev		 : $prev,
					next		 : $next,
					infinite	 : true,
					auto		 : ($auto == "true"?true:false),
					responsive	 : ($responsive == "true"?true:false),
					swipe: {onTouch:true},
					pagination   : $pagination,
					scroll	     : {
						easing   : "easeInOutCubic",
						duration : 600,
						fx: ($effect == "scroll"?"scroll":"")+($effect == "cover-fade"?"cover-fade":"")+($effect == "fade"?"fade":"")+($effect == "directscroll"?"directscroll":"")+($effect == "crossfade"?"crossfade":"")+($effect == "cover"?"cover":"")+($effect == "uncover"?"uncover":"")+($effect == "uncover-fade"?"uncover-fade":"")+($effect == "none"?"none":""),
					},
					items        : ($max == 6?6:"")+($max == 5?5:"")+($max == 4?4:"")+($max == 3?3:"")+($max == 2?2:"")+($max == 1?1:""),
				});
			});
		}
		
		if (jQuery(".bxslider").length) {
			jQuery(".bxslider").bxSlider({
				slideWidth: 200,
				minSlides: 4,
				maxSlides: 4,
				slideMargin: 30
			});
		}

		/* Editor */

		if (jQuery('.wp-editor-wrap').length) {
			jQuery('.wp-editor-wrap').each(function() {
				var editor_iframe = jQuery(this).find('iframe');
				if (editor_iframe.height() < 150) {
					editor_iframe.css({'height':'150px'});
				}
			});
		}
		
	});
	
	/* Widget Menu jQuery */
	
	if (!mobile_device) {
		jQuery(".with-sidebar-container").each(function () {
			var main_container = jQuery(this);
			var sticky_sidebar = main_container.parent().find(".sticky-sidebar");
			if (sticky_sidebar.length) {
				sticky_sidebar.theiaStickySidebar({
					"containerSelector"   : main_container,
					"additionalMarginTop" :  (jQuery("#wrap").hasClass("fixed-enabled")?120:(jQuery("body").hasClass("admin-bar")?50:40))
				});
			}
		});
	}

	jQuery(window).trigger('resize');
	jQuery(window).trigger('scroll');
	
	jQuery(".widget_menu.widget_menu_jquery").each(function () {
		var widget_menu_jquery = jQuery(this);
		var sidebar_w = widget_menu_jquery.parent().width();
		widget_menu_jquery.css({"width":sidebar_w});
	});
	
	jQuery(window).bind("resize", function () {
		if (jQuery(this).width() > 800) {
			jQuery(".widget_menu.widget_menu_jquery").each(function () {
				var widget_menu_jquery = jQuery(this);
				var sidebar_w = widget_menu_jquery.parent().width();
				widget_menu_jquery.css({"width":sidebar_w});
			});
		}
	});
	
	jQuery.fn.scrollBottom = function() {
		return jQuery(document).height() - this.scrollTop() - this.height();
	};
	
	var $widget_menu = jQuery(".widget_menu_jquery");
	var $window = jQuery(window);
	//var top = $widget_menu.parent().position().top;
	
	var header = parseFloat(jQuery("#header-top").outerHeight()+jQuery("#header").outerHeight()+jQuery(".breadcrumbs").outerHeight()+70);
	var footer = parseFloat(jQuery("#footer").outerHeight()+jQuery("#footer-bottom").outerHeight()+80);
	
	$window.bind("scroll resize", function() {
		var gap = $window.height() - $widget_menu.height()+40;
		var visibleHead = header - $window.scrollTop();
		var visibleFoot = footer - $window.scrollBottom();
		var scrollTop = $window.scrollTop();
		
		if (scrollTop < header) {
			$widget_menu.css({
				top: visibleHead + "px",
				bottom: "auto"
			});
		}else if (visibleFoot > $window.height() - $widget_menu.height()) {
			$widget_menu.css({
				top: "auto",
				bottom: visibleFoot + "px"
			});
		}else {
			if (jQuery("#wrap").hasClass("fixed-enabled")) {
				$widget_menu.css({
					top: parseFloat(jQuery("#header.fixed-nav").outerHeight()+40),
					bottom: "auto"
				});
			}else {
				$widget_menu.css({
					top: "40px",
					bottom: "auto"
				});
			}
		}
	}).scroll();
	
});
function ask_get_captcha(captcha_file,captcha_id) {
	var img = jQuery("#"+captcha_id).attr("src",captcha_file+'?'+Math.random());
}
function ask_me_child_cats(dropdown,result_div,level) {
	var cat         = dropdown.val();
	var results_div = result_div + level;
	var field_attr  = dropdown.attr('data-taxonomy');
	jQuery.ajax({
		type: 'post',
		url: askme_js.admin_url,
		data: {
			action: 'ask_me_child_cats',
			catID: cat,
			field_attr: field_attr
		},
		beforeSend: function() {
			dropdown.parent().parent().parent().next('.loader_2').show(10);
			dropdown.parent().parent().parent().addClass("no-load");
		},
		complete: function() {
			dropdown.parent().parent().parent().next('.loader_2').hide(10);
			dropdown.parent().parent().parent().removeClass("no-load");
		},
		success: function(html) {
			dropdown.parent().parent().nextAll().each(function() {
				jQuery(this).remove();
			});
			
			if (html != "") {
				dropdown.addClass('hasChild').parent().parent().parent().append('<span id="'+result_div+level+'" data-level="'+level+'"></span>');
				dropdown.parent().parent().parent().find('#'+results_div).html(html).slideDown('fast');
			}

			jQuery('#'+result_div+level+' .askme-custom-select').select2({
				width: '100%',
			});
		}
	});
}