jQuery(document).ready( function($) {
	
	/* Fix the counter */
	if (jQuery('.fix-comments').length) {
		jQuery(".fix-comments").click(function () {
	    	var fix_comments = jQuery(this);
	    	var post_id = fix_comments.data("post");
	    	jQuery.post(admin_ajax.ajax_a,"action=askme_confirm_fix_comments&post_id="+post_id,function (result) {
		    	location.reload();
	    	});
			return false;
		});
	}

	if (jQuery('input.vpanel_save').length) {
		jQuery("input.vpanel_save").click(function() {
			var typingTimer;
			jQuery("#ajax-saving").fadeIn("slow");
			jQuery("#loading").show();
			if (jQuery(".wp-editor-wrap.tmce-active").length && typeof(tinyMCE) != 'undefined' && tinyMCE !== undefined && tinyMCE !== false) {
				tinyMCE.triggerSave();
			}
			
			var $data = jQuery('#main_options_form').serialize();
			jQuery('#main_options_form').find('input[type=checkbox]').each(function() {
				if ( typeof $( this ).attr( 'name' ) !== "undefined" ) {
					var chkVal = $( this ).is( ':checked' ) ? $( this ).val() : "0";
					$data += "&" + $( this ).attr( 'name' ) + "=" + chkVal;
				}
			});
			var import_setting = jQuery("#import_setting").val();
			if (import_setting != "") {
				var saving_nonce = jQuery("#saving_nonce").val();
				jQuery.ajax({
					type: "POST",
					url: admin_ajax.ajax_a,
					data: {
						action: "askme_import_options",
						saving_nonce: saving_nonce,
						data: import_setting
					},
					success: function (results) {
						jQuery(".vpanel_save").blur();
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function () {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#loading").hide();
						},500);
						jQuery("#import_setting").val("");
						if (results == 3) {
							jQuery("#ajax-load").fadeIn("slow");
							clearTimeout(typingTimer);
							typingTimer = setTimeout(function () {
								jQuery("#ajax-load").fadeOut("slow");
								location.reload();
							},3000);
						}else {
							location.reload();
						}
					},error: function (jqXHR, textStatus, errorThrown) {
						// Error
					},complete: function () {
						// Done
					}
				});
			}else {
				jQuery.ajax({
					type: "POST",
					url: admin_ajax.ajax_a,
					data: {
						action: "askme_update_options",
						data: $data
					},
					cache: false,
					dataType: "json",
					success: function (results) {
						jQuery(".vpanel_save").blur();
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function () {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#loading").hide();
						},200);
						if (results == 3) {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#ajax-load").fadeIn("slow");
							clearTimeout(typingTimer);
							typingTimer = setTimeout(function () {
								jQuery("#ajax-load").fadeOut("slow");
								location.reload();
							},3000);
						}
					},error: function (jqXHR, textStatus, errorThrown) {
						// Error
					},complete: function (results) {
						results = (typeof(results) !== 'undefined' && results.hasOwnProperty('responseJSON')?results.responseJSON:results);
						jQuery(".vpanel_save").blur();
						clearTimeout(typingTimer);
						typingTimer = setTimeout(function () {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#loading").hide();
						},200);
						if (results == 3) {
							jQuery("#ajax-saving").fadeOut("slow");
							jQuery("#ajax-load").fadeIn("slow");
							clearTimeout(typingTimer);
							typingTimer = setTimeout(function () {
								jQuery("#ajax-load").fadeOut("slow");
								location.reload();
							},3000);
						}
					}
				});
			}
			return false;
		});
	}
	
	jQuery("#reset_c").click(function() {
		var answer = confirm(admin_ajax.confirm_reset);
		if (answer) {
			jQuery("#ajax-reset").fadeIn("slow");
			var typingTimer;
			var saving_nonce = jQuery("#saving_nonce").val();
			var defaults = "&action=askme_reset_options&saving_nonce="+saving_nonce;
			jQuery.post(admin_ajax.ajax_a,defaults,function (results) {
				jQuery("#reset_c").blur();
				if (results == 3) {
					jQuery("#ajax-reset").fadeOut("slow");
					jQuery("#ajax-load").fadeIn("slow");
					clearTimeout(typingTimer);
					typingTimer = setTimeout(function () {
						jQuery("#ajax-load").fadeOut("slow");
						location.reload();
					},3000);
				}else {
	    			setTimeout(function() {
	    				jQuery("#ajax-reset").fadeOut("slow");
	    				location.reload();
	    			},200);
	    		}
			});
		}
		return false;
	});
	
	jQuery(".delete-question-post,.delete-comment-answer").click(function() {
		var answer = confirm(admin_ajax.confirm_delete);
		if (answer) {
			var this_event = jQuery(this);
			var data_id = this_event.attr("data-id");
			var data_action = this_event.attr("data-action");
			var data_location = this_event.attr("data-location");
			var data_div = this_event.attr("data-div-id");
			jQuery.post(admin_ajax.ajax_a,"data_id="+data_id+"&data_div="+jQuery("#"+data_div).val()+"&action="+data_action,function (data) {
				window.location = data_location;
			});
		}
		return false;
	});
	
	if (jQuery('.sort-sections').length) {
		jQuery('.sort-sections').each(function () {
			if (!jQuery(this).hasClass("not-sort") && !jQuery(this).hasClass("sort-sections-with")) {
				jQuery(this).sortable({placeholder: "ui-state-highlight",handle: ".widget-head",cancel: ".builder-toggle-open,.builder-toggle-close,.builder_clone,.del-builder-item"});
			}
		});
	}
	
	/* Delete reports */
	jQuery(".reports-delete").click(function () {
		var answer = confirm(admin_ajax.confirm_reports);
		if (answer) {
			var reports_delete = jQuery(this);
			var reports_delete_id = reports_delete.attr("attr");
			if (reports_delete.hasClass("reports-answers")) {
				jQuery.post(admin_ajax.ajax_a,"action=reports_answers_delete&reports_delete_id="+reports_delete_id,function (result) {
					reports_delete.parent().parent().addClass('removered').fadeOut(function() {
						jQuery(this).remove();
						if (jQuery(".reports-table-items .reports-table-item").length == 0) {
							jQuery(".reports-table-items").html("<p>There are no reports yet</p>");
						}
						if (jQuery(".ask-reports-items .ask-reports").length == 0) {
							jQuery(".ask-reports-items").html("<p>There are no reports yet</p>");
						}
					});
				});
			}else if (reports_delete.hasClass("reports-users")) {
				jQuery.post(admin_ajax.ajax_a,"action=reports_users_delete&reports_delete_id="+reports_delete_id,function (result) {
					reports_delete.parent().parent().addClass('removered').fadeOut(function() {
						jQuery(this).remove();
						if (jQuery(".reports-table-items .reports-table-item").length == 0) {
							jQuery(".reports-table-items").html("<p>There are no reports yet</p>");
						}
						if (jQuery(".ask-reports-items .ask-reports").length == 0) {
							jQuery(".ask-reports-items").html("<p>There are no reports yet</p>");
						}
					});
				});
			}else {
				jQuery.post(admin_ajax.ajax_a,"action=reports_delete&reports_delete_id="+reports_delete_id,function (result) {
					reports_delete.parent().parent().addClass('removered').fadeOut(function() {
						jQuery(this).remove();
						if (jQuery(".reports-table-items .reports-table-item").length == 0) {
							jQuery(".reports-table-items").html("<p>There are no reports yet</p>");
						}
						if (jQuery(".ask-reports-items .ask-reports").length == 0) {
							jQuery(".ask-reports-items").html("<p>There are no reports yet</p>");
						}
					});
				});
			}
		}
		return false;
	});
	
	/* View reports */
	jQuery(".reports-view").click(function () {
		var reports_view = jQuery(this);
		var reports_view_attr = "#reports-"+reports_view.attr("attr");
		jQuery(reports_view_attr).slideDown();
		
		jQuery("body").prepend("<div class='reports-hidden'></div>");
		wrap_pop();
		var count_report_new = jQuery(".wp-submenu-head .count_lasts").text();
		var count_report_new_last = count_report_new-1;
		if (reports_view.hasClass("reports-answers")) {
			jQuery.post(admin_ajax.ajax_a,"action=reports_answers_view&reports_view_id="+reports_view.attr("attr"),function (result) {
				reports_view.parent().find(".reports-new").hide();
				
				var count_report_answer_new = jQuery(".count_report_answer_new").text();
				var count_report_answer_new_last = count_report_answer_new-1;
				if (count_report_new > 0 && reports_view.parent().find(".reports-new").length > 0) {
					jQuery(".count_lasts").text(count_report_new_last);
					jQuery(".count_lasts").removeClass("count-"+count_report_new).addClass("count-"+count_report_new_last).parent().removeClass("count-"+count_report_new).addClass("count-"+count_report_new_last);
					if (count_report_new_last == 0) {
						jQuery(".count_lasts").removeClass("count-"+count_report_new_last).removeClass("awaiting-mod").parent().removeClass("count-"+count_report_new).removeClass("awaiting-mod");
					}
					
					jQuery(".count_report_answer_new").text(count_report_answer_new_last);
					jQuery(".count_report_answer_new").removeClass("count-"+count_report_answer_new).addClass("count-"+count_report_answer_new_last).parent().removeClass("count-"+count_report_answer_new).addClass("count-"+count_report_answer_new_last);
					if (count_report_answer_new_last == 0) {
						jQuery(".count_lasts").removeClass("count-"+count_report_answer_new_last).removeClass("awaiting-mod").parent().removeClass("count-"+count_report_answer_new_last).removeClass("awaiting-mod");
					}
				}
			});
		}else if (reports_view.hasClass("reports-users")) {
			jQuery.post(admin_ajax.ajax_a,"action=reports_users_view&reports_view_id="+reports_view.attr("attr"),function (result) {
				reports_view.parent().find(".reports-new").hide();
				
				var count_report_user_new = jQuery(".count_report_user_new").text();
				var count_report_user_new_last = count_report_user_new-1;
				if (count_report_new > 0 && reports_view.parent().find(".reports-new").length > 0) {
					jQuery(".count_lasts").text(count_report_new_last);
					jQuery(".count_lasts").removeClass("count-"+count_report_new).addClass("count-"+count_report_new_last).parent().removeClass("count-"+count_report_new).addClass("count-"+count_report_new_last);
					if (count_report_new_last == 0) {
						jQuery(".count_lasts").removeClass("count-"+count_report_new_last).removeClass("awaiting-mod").parent().removeClass("count-"+count_report_new).removeClass("awaiting-mod");
					}
					
					jQuery(".count_report_user_new").text(count_report_user_new_last);
					jQuery(".count_report_user_new").removeClass("count-"+count_report_user_new).addClass("count-"+count_report_user_new_last).parent().removeClass("count-"+count_report_user_new).addClass("count-"+count_report_user_new_last);
					if (count_report_user_new_last == 0) {
						jQuery(".count_lasts").removeClass("count-"+count_report_user_new_last).removeClass("awaiting-mod").parent().removeClass("count-"+count_report_user_new_last).removeClass("awaiting-mod");
					}
				}
			});
		}else {
			jQuery.post(admin_ajax.ajax_a,"action=reports_view&reports_view_id="+reports_view.attr("attr"),function (result) {
				reports_view.parent().find(".reports-new").hide();
				
				var count_report_question_new = jQuery(".count_report_question_new").text();
				var count_report_question_new_last = count_report_question_new-1;
				if (count_report_new > 0 && reports_view.parent().find(".reports-new").length > 0) {
					jQuery(".count_lasts").text(count_report_new_last);
					jQuery(".count_lasts").removeClass("count-"+count_report_new).addClass("count-"+count_report_new_last).parent().removeClass("count-"+count_report_new).addClass("count-"+count_report_new_last);
					if (count_report_new_last == 0) {
						jQuery(".count_lasts").removeClass("count-"+count_report_new_last).removeClass("awaiting-mod").parent().removeClass("count-"+count_report_new).removeClass("awaiting-mod");
					}
					
					jQuery(".count_report_question_new").text(count_report_question_new_last);
					jQuery(".count_report_question_new").removeClass("count-"+count_report_question_new).addClass("count-"+count_report_question_new_last).parent().removeClass("count-"+count_report_question_new).addClass("count-"+count_report_question_new_last);
					if (count_report_question_new_last == 0) {
						jQuery(".count_lasts").removeClass("count-"+count_report_question_new_last).removeClass("awaiting-mod").parent().removeClass("count-"+count_report_question_new_last).removeClass("awaiting-mod");
					}
				}
			});
		}
		return false;
	});
	
	/* Close reports */
	jQuery(".reports-close").click(function () {
		jQuery(".reports-pop").animate({"top":"-50%"},500).hide(function () {
			jQuery(this).animate({"top":"-50%"},500);
		});
		jQuery(".reports-hidden").remove();
		return false;
	});
	
	/* Function pop */
	function wrap_pop() {
		jQuery(".reports-hidden").click(function () {
			jQuery(".reports-pop").slideUp();
			jQuery(this).remove();
		});
	}
	
	/* Publishing action post */
	jQuery("#publishing-action #publish").click(function () {
		/*
		var return_f = false;
		var post_ID = jQuery(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().find("#post_ID").val();
		jQuery.post(admin_ajax.ajax_a,"action=publishing_action_post&post_ID="+post_ID,function (result) {
			alert(result);
			return_f = true;
			setTimeout(function(){
				var return_f = true;
				alert(return_f);
			},200);
		});
		alert(return_f);
		return return_f;
		*/
	});
	
	jQuery(".delete-this-attachment").click(function () {
		var answer = confirm(admin_ajax.confirm_delete_attachment);
		if (answer) {
			var delete_attachment = jQuery(this);
			var attachment_id = delete_attachment.attr("href");
			var post_id = jQuery("#post_ID").val();
			var single_attachment = "No";
			if (delete_attachment.hasClass("single-attachment")) {
				single_attachment = "Yes";
			}
			jQuery.post(admin_ajax.ajax_a,"action=confirm_delete_attachment&attachment_id="+attachment_id+"&post_id="+post_id+"&single_attachment="+single_attachment,function (result) {
				delete_attachment.parent().fadeOut(function() {
					jQuery(this).remove();
				});
			});
		}
		return false;
	});
	
	jQuery('.tooltip_n').tipsy({gravity: 'n'});
	jQuery('.tooltip_s').tipsy({gravity: 's'});
	
	jQuery('.wp-color-picker').wpColorPicker();
	
});