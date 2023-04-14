/**
 * Custom scripts needed for the colorpicker, image button selectors,
 * and navigation tabs.
 */

jQuery(document).ready(function($) {

	// Loads the color pickers
	$('.of-color').wpColorPicker();
	
	$('.of-datepicker').datepicker();

	if ($('.of-typography-face').length) {
		$('.of-typography-face').fontselect();
	}
	
	$("#your-profile .form-table td").addClass("rwmb-input");

	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');
	});
	
	$('.section .sort-sections').each(function() {
	   var t = this;

		//if (!$(t).hasClass("not-sort") && !$(t).hasClass("sort-sections-with")) {
			$(t).sortable({placeholder: "ui-state-highlight",handle: ".widget-head,.widget-handle",cancel: ".builder-toggle-open,.builder-toggle-close,.builder_clone,.del-builder-item"});
		//}
	});
	
	$('.v_sliderui').each(function() {
		
		var obj   = $(this);
		var sId   = "#" + obj.data('id');
		var val   = parseInt(obj.data('val'));
		var min   = parseInt(obj.data('min'));
		var max   = parseInt(obj.data('max'));
		var step  = parseInt(obj.data('step'));
		
		//slider init
		obj.slider({
			value: val,
			min: min,
			max: max,
			step: step,
			range: "min",
			slide: function( event, ui ) {
				$(sId).val( ui.value );
			}
		});
		
	});

	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	// Loads tabbed sections if they exist
	if ( $('.optionsframework-content .nav-tab-wrapper').length > 0 ) {
		options_framework_tabs();
	}

	function options_framework_tabs() {
		
		// Hides all the .group sections to start
		jQuery('.group').hide();

		// Find if a selected tab is saved in localStorage
		var ask_v = '';
		if ( typeof(localStorage) != 'undefined' ) {
			ask_v = localStorage.getItem("ask_v");
		}
		
		jQuery('.vpanel-loading').hide();
		// If active tab is saved and exists, load it's .group
		if (ask_v != '' && jQuery(ask_v).length && !jQuery(ask_v + '-tab').hasClass('hide') ) {
			jQuery(ask_v).animate({
				opacity: 'show',
				height: 'show'
			}, 200, function() {
				jQuery(this).removeClass('hide');
			});
			jQuery(ask_v + '-tab').addClass('nav-tab-active');
		}else {
			jQuery('.group:not(.hide):first-child').animate({
				opacity: 'show',
				height: 'show'
			}, 200, function() {
				jQuery(this).removeClass('hide');
			});
			jQuery('.optionsframework-content .nav-tab-wrapper a:not(.hide):first-child').addClass('nav-tab-active');
		}
		// Bind tabs clicks
		jQuery('.optionsframework-content .nav-tab-wrapper a').click(function(evt) {

			evt.preventDefault();

			// Remove active class from all tabs
			jQuery('.optionsframework-content .nav-tab-wrapper a').removeClass('nav-tab-active');

			jQuery(this).addClass('nav-tab-active').blur();

			var group = jQuery(this).attr('href');

			if (typeof(localStorage) != 'undefined' ) {
				localStorage.setItem("ask_v", jQuery(this).attr('href') );
			}

			var ask_tab_value = '';
			if (typeof(localStorage) != 'undefined') {
				ask_tab_value = localStorage.getItem('ask_tab_value');
			}

			if (ask_tab_value != '' && jQuery(group+" .ask_tabs a[href='"+ask_tab_value+"']").length) {
				//
			}else {
				jQuery(group+" .ask_tabs li:first-child a").click();
			}

			jQuery('.group').hide();
			jQuery(group).animate({
				opacity: 'show',
				height: 'show'
			}, 200, function() {
				jQuery(this).removeClass('hide');
			});

			// Editor height sometimes needs adjustment when unhidden
			jQuery('.wp-editor-wrap').each(function() {
				var editor_iframe = jQuery(this).find('iframe');
				if ( editor_iframe.height() < 30 ) {
					editor_iframe.css({'height':'auto'});
				}
			});

		});
		
		// ask tabs
		var ask_tab_value = '';
		if (typeof(localStorage) != 'undefined') {
			ask_tab_value = localStorage.getItem('ask_tab_value');
		}
		
		if (ask_tab_value != '' && jQuery(".ask_tabs a[href='"+ask_tab_value+"']").length) {
			jQuery(".ask_tabs a[href='"+ask_tab_value+"']").parent().parent().parent().find(".head-group").hide(10).parent().hide(10);
			if (jQuery(".ask_tabs a[href='"+ask_tab_value+"']").parent().parent().parent().find(ask_tab_value).length) {
				jQuery(".ask_tabs a[href='"+ask_tab_value+"']").addClass("ask_active").parent().parent().parent().find(ask_tab_value).show(10).slideDown(300);
			}else {
				jQuery(ask_v).find(".ask_tabs > li:first-child a").addClass("ask_active").click();
				jQuery(jQuery(ask_v).find(".ask_tabs > li:first-child a").attr("href")).show(10).slideDown(300);
			}
		}else {
			jQuery(ask_v).find(".ask_tabs > li:first-child a").addClass("ask_active").click();
		}
		
		jQuery('.ask_tabs a').click(function(evt) {
			evt.preventDefault();
			jQuery(this).parent().parent().parent().find(".head-group").hide(10).parent().hide(10);
			jQuery(this).parent().parent().find(".ask_active").removeClass("ask_active");
			jQuery(this).addClass("ask_active").parent().parent().parent().find(jQuery(this).attr('href')).show(10).slideDown(300);
			if (typeof(localStorage) != 'undefined') {
				localStorage.setItem('ask_tab_value', jQuery(this).attr('href'));
			}
		});
		
		jQuery(".ask_tabs").each(function () {
			var data_std = jQuery(this).attr("data-std");
			if (data_std != jQuery(".ask_tabs a[href='"+ask_tab_value+"']").parent().parent().attr("data-std")) {
				jQuery(".ask_tabs a[href='"+data_std+"']").addClass("ask_active");
				jQuery(data_std).show(10).slideDown(300);
			}
		});
	}

});