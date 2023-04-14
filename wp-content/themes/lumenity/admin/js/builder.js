jQuery(function() {
    jQuery(window).on("load",function() {
        if (jQuery(".upload_image_button.upload_image_button_m").length) {
            var custom_uploader;
            jQuery('.upload_image_button.upload_image_button_m').click(function(e) {
                var image_var = jQuery(this);
                e.preventDefault();
                if (custom_uploader) {
                    custom_uploader.open();
                    return;
                }
                //Extend the wp.media object
                custom_uploader = wp.media.frames.file_frame = wp.media({
                    title: builder_ajax.choose_image,
                    button: {
                        text: builder_ajax.choose_image
                    },
                    multiple: true
                });
                custom_uploader.on('select', function() {
                    var selection = custom_uploader.state().get('selection');
                    selection.map( function( attachment ) {
                        attachment = attachment.toJSON();
                        if (jQuery("#"+image_var.attr("data-id")+"-item-"+attachment.id).length == 0) {
                            jQuery("#"+image_var.attr("data-id")).append("<li id='"+image_var.attr("data-id")+"-item-"+attachment.id+"' class='multi-images'>\
                                <div class='multi-image'>\
                                    <img alt='"+attachment.url+"' src='"+attachment.url+"'><input type='hidden' name='"+image_var.attr("data-name")+"[]' value='"+attachment.id+"'>\
                                    <div class='image-overlay'></div>\
                                    <div class='image-media-bar'>\
                                        <a class='image-edit-media' title='"+builder_ajax.edit_image+"' href='post.php?post="+attachment.id+"&amp;action=edit' target='_blank'>\
                                            <span class='dashicons dashicons-edit'></span>\
                                        </a>\
                                        <a href='#' class='image-remove-media' title='"+builder_ajax.remove_image+"'>\
                                            <span class='dashicons dashicons-no-alt'></span>\
                                        </a>\
                                    </div>\
                                </div>\
                            </li>");
                        }
                    });
                });
                custom_uploader.open();
            });
        }
        
        jQuery(document).on("click",".image-remove-media",function () {
            jQuery(this).parent().parent().parent().addClass('removered').fadeOut(function() {
                jQuery(this).remove();
            });
            return false;
        });
        jQuery(document).on('mouseup',".builder_select",function () {
            jQuery(this).select();
        });
    });
    
    function uploaded_image() {
        jQuery(".adv-label").each(function () {
            var adv_label = jQuery(this);
            if (jQuery("input[type='radio']:checked",adv_label).val() == "custom_image") {
                jQuery(".image-url",adv_label.parent()).show(10);
                jQuery(".adv-url",adv_label.parent()).show(10);
                jQuery(".adv-code",adv_label.parent()).hide(10);
            }else if (jQuery("input[type='radio']:checked",adv_label).val() == "display_code") {
                jQuery(".image-url",adv_label.parent()).hide(10);
                jQuery(".adv-url",adv_label.parent()).hide(10);
                jQuery(".adv-code",adv_label.parent()).show(10);
            }
            jQuery("input[type='radio']",adv_label).click(function () {
                if (jQuery(this).val() == "custom_image") {
                    jQuery(".image-url",jQuery(this).parent().parent()).slideDown(500);
                    jQuery(".adv-url",jQuery(this).parent().parent()).slideDown(500);
                    jQuery(".adv-code",jQuery(this).parent().parent()).slideUp(500);
                }else if (jQuery(this).val() == "display_code") {
                    jQuery(".image-url",jQuery(this).parent().parent()).slideUp(500);
                    jQuery(".adv-url",jQuery(this).parent().parent()).slideUp(500);
                    jQuery(".adv-code",jQuery(this).parent().parent()).slideDown(500);
                }
            });
        });
    }
    
    jQuery(document).on("click","#expand-all .expand-all2",function () {
        jQuery(".widget-content").slideUp(300);
        jQuery(".builder-toggle-close").css("display","none");
        jQuery(".builder-toggle-open").css("display","block");
        jQuery(".expand-all").css("display","block");
        jQuery(".expand-all2").css("display","none");
    });
    
    jQuery("#add_badge").click(function() {
        var badge_name = jQuery('#badge_name').val();
        var badge_points = jQuery('#badge_points').val();
        var badge_color = jQuery('#badge_color').val();
        var intRegex = /^\d+$/;
        if (badge_name == "") {
            alert("Please write the name !");
        }else if (badge_points == "") {
            alert("Please write the points !");
        }else if (!intRegex.test(badge_points)) {
            alert("Sorry not number !");
        }else if (badge_color == "") {
            alert("Please write the color !");
        }else {
            var badges_list = jQuery("#badges_list > li").length;
            badges_list++;
            jQuery('#badges_list').append('<li class="badges_last"><a class="del-builder-item del-badge-item">x</a><div class="widget-head">'+badge_name+'</div><div class="widget-content"><h4 class="heading">Badge name</h4><input name="badges['+badges_list+'][badge_name]" type="text" value="'+badge_name+'"><div class="clear"></div><h4 class="heading">Badge points</h4><input name="badges['+badges_list+'][badge_points]" type="text" value="'+badge_points+'"><div class="clear"></div><h4 class="heading">Badge color</h4><input class="of-color badge_color" name="badges['+badges_list+'][badge_color]" type="text" value="'+badge_color+'"><div class="clear"></div></div></li>');
            jQuery('.badges_last .badge_color').wpColorPicker();
            jQuery('#badge_name').val("");
            jQuery('#badge_points').val("");
            jQuery('#badge_color').val("");
            jQuery('.badges_last').removeClass('badges_last');
        }
    });
    
    /* Add a new element */
    jQuery("#optionsframework .add_element").on("click",function () {
        var add_element = jQuery(this);
        if (!add_element.hasClass("not_add_element")) {
            var ask_theme_var = builder_ajax.ask_theme;
            var data_id     = add_element.attr("data-id");
            var data_add_to = add_element.parent().find(".all_elements ul").attr("data-to");
            var data_id_name = "["+data_id+"]";
            var data_add_to_name = "["+data_add_to+"]";
            
            if (add_element.hasClass("no_theme_options")) {
                var ask_theme_var = "";
                var data_id_name = data_id;
                var data_add_to_name = data_add_to;
            }
            
            var data_title  = add_element.attr("data-title");
            if (data_add_to !== undefined && data_add_to !== false) {
                var add_element_j = jQuery("#"+data_add_to+" li").length;
                add_element_j++;
                var data_add_to_id = data_add_to;
            }else {
                var add_element_j = add_element.parent().find("."+data_id+"_j").attr("data-js");
                var data_add_to_id = data_id;
            }
            
            var element_id = "elements_"+data_add_to_id+"_"+add_element_j;
            add_element.parent().find(".all_elements ul li").clone().attr("id",element_id).appendTo('#'+data_add_to_id);
            jQuery("html,body").animate({scrollTop: jQuery("#"+element_id).offset().top-35},"slow");
            
            if (data_title !== undefined && data_title !== false) {
                jQuery("#"+element_id+" .del-builder-item,#"+element_id+" a.widget-handle").wrapAll("<div class='widget-head' />");
                jQuery("#"+element_id+" > div:not(.widget-content)").prepend(jQuery(add_element.parent().find(".all_elements ul li input[data-title='"+data_title+"']")).val());
            }
            
            jQuery("#"+element_id+" .section,#"+element_id+" .wrap_class").each(function () {
                var this_each = jQuery(this);
                var this_attr = this_each.attr("data-attr");
                if (!this_each.hasClass("wrap_class")) {
                    if (data_add_to !== undefined && data_add_to !== false) {
                        var last_id = ask_theme_var+"_"+data_add_to+"_"+add_element_j+"_"+this_attr;
                    }else {
                        var last_id = ask_theme_var+"_"+data_id+"_"+add_element_j+"_"+this_attr;
                    }
                }

                var condition = this_each.attr("data-condition");
                if (condition !== undefined && condition !== false) {
                    this_each.attr("data-condition",condition.split("[%id%]").join(ask_theme_var+"_"+data_add_to_id+"_"+add_element_j+"_"));
                }

                if (!this_each.hasClass("wrap_class")) {
                    this_each.attr("data-id",last_id).attr("id","section-"+last_id);
                    
                    if (this_each.find("div.v_slidersui").length) {
                        this_each.find("div.v_slidersui").attr("id",last_id+"-slider").attr("data-id",last_id).addClass('v_sliderui').removeClass('v_slidersui');
                    }
                }
            });
            
            jQuery("#"+element_id+" .widget-content select").each(function () {
                var this_each = jQuery(this);
                var this_attr = (this_each.hasClass("check-parent-class")?this_each.parent().attr("data-attr"):this_each.attr("data-attr"));
                if (data_add_to !== undefined && data_add_to !== false) {
                    var last_id   = ask_theme_var+"_"+data_add_to+"_"+add_element_j+"_"+this_attr;
                    var last_name = ask_theme_var+data_add_to_name+"["+add_element_j+"]["+this_attr+"]";
                }else {
                    var last_id   = ask_theme_var+"_"+data_id+"_"+add_element_j+"_"+this_attr;
                    var last_name = ask_theme_var+data_id_name+"["+add_element_j+"]["+this_attr+"]";
                }
                this_each.attr("name",last_name).attr("id",last_id);
            });
            
            jQuery("#"+element_id+" .widget-content input,#"+element_id+" .widget-content textarea").each(function () {
                var this_each = jQuery(this);
                var this_attr = this_each.attr("data-attr");
                if (data_add_to !== undefined && data_add_to !== false) {
                    var last_id   = ask_theme_var+"_"+data_add_to+"_"+add_element_j+"_"+this_attr;
                    var last_name = ask_theme_var+data_add_to_name+"["+add_element_j+"]["+this_attr+"]";
                }else {
                    var last_id   = ask_theme_var+"_"+data_id+"_"+add_element_j+"_"+this_attr;
                    var last_name = ask_theme_var+data_id_name+"["+add_element_j+"]["+this_attr+"]";
                }
                this_each.attr("id",last_id).not('[type="button"]').attr("name",last_name);
                if (this_each.is('[type="hidden"]')) {
                    this_each.val(add_element_j);
                }
                if (this_each.is('[type="radio"]')) {
                    this_each.attr("id",last_id+"_"+this_each.attr("value")).next("label").attr("for",last_id+"_"+this_each.attr("value"));
                }
                if (this_each.is('[type="checkbox"]')) {
                    this_each.closest(".switch").attr("for",last_id).find(" > label").attr("for",last_id);
                }
                if (this_each.is('[data-type="uniq_id"]')) {
                    this_each.val(add_element_j);
                }
            });
            
            update_form_elements(jQuery('#'+element_id),'#'+element_id);
            
            if (data_add_to !== undefined && data_add_to !== false) {
                jQuery("#"+element_id).append('<input name="'+ask_theme_var+'['+data_add_to+']['+add_element_j+'][getthe]" value="'+data_add_to+'" type="hidden">');
            }
            if (!add_element.parent().find(".all_elements ul li input").is(':radio') && !add_element.parent().find(".all_elements ul li input").is(':checkbox') && !add_element.parent().find(".all_elements ul li input.upload_image_button,.upload_image_button_m")) {
                add_element.parent().find(".all_elements ul li input").val("");
            }
            if (!add_element.parent().find(".all_elements ul li textarea")) {
                add_element.parent().find(".all_elements ul li textarea").val("");
            }
            add_element_j++;
            add_element.parent().find("."+data_id+"_j").attr("data-js",add_element_j);
            
            jQuery('#'+element_id+' .of-colors').wpColorPicker();
            var attr_js = jQuery('#'+element_id+' .builder-datepicker').data('js');
            jQuery('#'+element_id+' .builder-datepicker').removeClass("builder-datepicker").removeClass("hasDatepicker").addClass("of-datepicker").datepicker((attr_js !== undefined && attr_js !== false?attr_js:{}));
            jQuery("#"+element_id).closest("ul").removeClass("sort-sections-empty");
        }
    });
    
    jQuery("#add_coupon").click(function() {
        var coupon_name = jQuery('#coupon_name').val();
        var coupon_type = jQuery('#coupon_type').val();
        var coupon_amount = jQuery('#coupon_amount').val();
        var coupon_date = jQuery('#coupon_date').val();
        var intRegex = /^\d+$/;
        if (coupon_name == "") {
            alert("Please write the name !");
        }else if (coupon_type == "") {
            alert("Please write the coupon type !");
        }else if (coupon_amount == "") {
            alert("Please write the amount !");
        }else if (!intRegex.test(coupon_amount)) {
            alert("Sorry not number !");
            jQuery('#coupon_amount').val("");
        }else {
            var coupons_list = jQuery("#coupons_list > li").length;
            coupons_list++;
            jQuery('#coupons_list').append('<li class="coupons_last"><a class="del-builder-item del-coupon-item">x</a><div class="widget-content"><h4 class="heading">Coupon name</h4><input name="coupons['+coupons_list+'][coupon_name]" type="text" value="'+coupon_name+'" class="coupon_name"><div class="clear"></div><h4 class="heading">Discount type</h4><div class="styled-select"><select class="coupon_type" name="coupons['+coupons_list+'][coupon_type]"><option value="discount"'+(coupon_type == "discount"?" selected='selected'":"")+'>Discount</option><option value="percent"'+(coupon_type == "percent"?" selected='selected'":"")+'>% Percent</option></select></div><div class="clear"></div><h4 class="heading">Amount</h4><input name="coupons['+coupons_list+'][coupon_amount]" class="coupon_amount" type="text" value="'+coupon_amount+'"><div class="clear"></div><h4 class="heading">Expiry date</h4><input name="coupons['+coupons_list+'][coupon_date]" class="of-datepicker coupon_date" type="text" value="'+coupon_date+'"><div class="clear"></div></div></li>');
            jQuery('.coupons_last .of-datepicker').datepicker();
            jQuery('#coupon_name').val("");
            jQuery('#coupon_amount').val("");
            jQuery('#coupon_date').val("");
            jQuery('.coupons_last').removeClass('coupons_last');
        }
    });
    
    jQuery("#sidebar_add").click(function() {
        var sidebar_name = jQuery('#sidebar_name').val();
        if (sidebar_name != "" ) {
            if( sidebar_name.length > 0){
                jQuery('#sidebars_list').append('<li><div class="widget-head">'+sidebar_name+' <input name="sidebars[]" type="hidden" value="'+sidebar_name+'"><a class="del-builder-item del-sidebar-item">x</a></div></li>');
            }
        }else {
            alert("Please write the name !");
        }
        jQuery('#sidebar_name').val("");
    });
    
    jQuery("#role_add").click(function() {
        var role_name = jQuery('#role_name').val();
        if (role_name != "" ) {
            if( role_name.length > 0){
                jQuery('#roles_list').append('<li><div class="widget-head">'+role_name+'<a class="del-builder-item del-role-item">x</a></div><div class="widget-content"><div class="widget-content-div"><label for="roles['+ roles_j +'][group]">Type here the group name .</label><input id="roles['+ roles_j +'][group]" type="text" name="roles['+ roles_j +'][group]" value="'+role_name+'"><input type="hidden" class="group_id" name="roles['+ roles_j +'][id]" value="group_'+ roles_j +'"><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][ask_question]"><input id="roles['+ roles_j +'][ask_question]" type="checkbox" checked name="roles['+ roles_j +'][ask_question]"><label for="roles['+ roles_j +'][ask_question]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][ask_question]">Select ON to can add a question.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][show_question]"><input id="roles['+ roles_j +'][show_question]" type="checkbox" checked name="roles['+ roles_j +'][show_question]"><label for="roles['+ roles_j +'][show_question]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][show_question]">Select ON to can view questions.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][show_post]"><input id="roles['+ roles_j +'][show_post]" type="checkbox" checked name="roles['+ roles_j +'][show_post]"><label for="roles['+ roles_j +'][show_post]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][show_post]">Select ON to can view posts.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][add_answer]"><input id="roles['+ roles_j +'][add_answer]" type="checkbox" checked name="roles['+ roles_j +'][add_answer]"><label for="roles['+ roles_j +'][add_answer]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][add_answer]">Select ON to can add an answer.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][show_answer]"><input id="roles['+ roles_j +'][show_answer]" type="checkbox" checked name="roles['+ roles_j +'][show_answer]"><label for="roles['+ roles_j +'][show_answer]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][show_answer]">Select ON to can view answers.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][add_post]"><input id="roles['+ roles_j +'][add_post]" type="checkbox" checked name="roles['+ roles_j +'][add_post]"><label for="roles['+ roles_j +'][add_post]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][add_post]">Select ON to can add a post.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][add_comment]"><input id="roles['+ roles_j +'][add_comment]" type="checkbox" checked name="roles['+ roles_j +'][add_comment]"><label for="roles['+ roles_j +'][add_comment]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][add_comment]">Select ON to can add an answer.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][show_comment]"><input id="roles['+ roles_j +'][show_comment]" type="checkbox" checked name="roles['+ roles_j +'][show_comment]"><label for="roles['+ roles_j +'][show_comment]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][show_comment]">Select ON to can view comments.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][send_message]"><input id="roles['+ roles_j +'][send_message]" type="checkbox" checked name="roles['+ roles_j +'][send_message]"><label for="roles['+ roles_j +'][send_message]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][send_message]">Select ON to can send a message.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][upload_files]"><input id="roles['+ roles_j +'][upload_files]" type="checkbox" checked name="roles['+ roles_j +'][upload_files]"><label for="roles['+ roles_j +'][upload_files]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][upload_files]">Select ON to can upload files.</label><div class="clearfix"></div><label class="switch" for="roles['+ roles_j +'][follow_question]"><input id="roles['+ roles_j +'][follow_question]" type="checkbox" checked name="roles['+ roles_j +'][follow_question]"><label for="roles['+ roles_j +'][follow_question]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][follow_question]">Select ON to can follow a question.</label><div class="clearfix"></div> <label class="switch" for="roles['+ roles_j +'][favorite_question]"><input id="roles['+ roles_j +'][favorite_question]" type="checkbox" checked name="roles['+ roles_j +'][favorite_question]"><label for="roles['+ roles_j +'][favorite_question]" data-on="'+builder_ajax.builder_on+'" data-off="'+builder_ajax.builder_off+'"></label></label><label for="roles['+ roles_j +'][favorite_question]">Select ON to can add a question at favorite.</label><div class="clearfix"></div></div></div></li>');
                roles_j ++ ;
            }
        }else {
            alert("Please write the name !");
        }
        jQuery('#role_name').val("");

    });
    
    var categories_select = jQuery('#categories_select').html();
    
    jQuery(document).on("click",".del-builder-item",function() {
        if (jQuery(this).closest("#section-vbegy_home_tabs").length) {
            jQuery(this).parent().parent().parent().addClass('removered').fadeOut(function() {
                jQuery(this).remove();
            });
        }else if (jQuery(this).hasClass("del-element-item")) {
            jQuery(this).parent().parent().parent().parent().addClass('removered').fadeOut(function() {
                jQuery(this).remove();
            });
        }else if (jQuery(this).hasClass("del-sidebar-item")) {
            jQuery(this).parent().parent().addClass('removered').fadeOut(function() {
                jQuery(this).remove();
            });
        }else if (jQuery(this).hasClass("del-role-item")) {
            var group = jQuery(this);
            roles_j = roles_j-1;
            var answer = confirm("If you press will delete group !");
            if (answer) {
                var group_id = jQuery(this).parent().parent().find(".group_id").val();
                var defaults = "group_id="+group_id+"&action=delete_group";
                jQuery.post(builder_ajax.ajax_a,defaults,function (data) {
                    group.parent().parent().addClass('removered').fadeOut(function() {
                        jQuery(this).remove();
                    });
                });
            }
        }else {
            jQuery(this).parent().addClass('removered').fadeOut(function() {
                jQuery(this).remove();
            });
        }
        return false;
    });
    
    uploaded_image();
    
    jQuery( "#question_poll_item" ).sortable({placeholder: "ui-state-highlight"});
    
    jQuery("#upload_add_ask").click(function() {
        jQuery('#question_poll_item').append('<li id="listItem_'+ nextCell +'" class="ui-state-default"><div class="widget-content option-item"><div class="rwmb-input"><input id="ask['+ nextCell +'][title]" class="ask" name="ask['+ nextCell +'][title]" value="" type="text"><input id="ask['+ nextCell +'][value]" name="ask['+ nextCell +'][value]" value="" type="hidden"><input id="ask['+ nextCell +'][id]" name="ask['+ nextCell +'][id]" value="'+ nextCell +'" type="hidden"><a class="del-cat">x</a></div></div></li>');
        nextCell ++ ;
        return false;
    });
    
    jQuery(document).on("click",".del-cat",function() {
        jQuery(this).parent().parent().addClass('removered').fadeOut(function() {
            jQuery(this).remove();
        });
    });

    var question_poll = jQuery("#vpanel_question_poll:checked").length;
    if (question_poll == 1) {
        jQuery(".vpanel_poll_options").slideDown(500);
    }else {
        jQuery(".vpanel_poll_options").slideUp(500);
    }
    
    jQuery("#vpanel_question_poll").click(function() {
        var vpanel_question_poll = jQuery("#vpanel_question_poll:checked").length;
        if (vpanel_question_poll == 1) {
            jQuery(".vpanel_poll_options").slideDown(500);
        }else {
            jQuery(".vpanel_poll_options").slideUp(500);
        }
    });
    
    /* Add new category */
    
    jQuery(".add-item.add-item-2.add-item-6:not(.add-item-7)").on("click",function () {
        var add_item = jQuery(this);
        var add_item_parent = add_item.parent();
        var addto = jQuery(this).data("addto");
        var add_to_jquery = add_item_parent.find(".category_tabs > ul");
        var item_name = jQuery(this).data("name");
        var select_val = add_item_parent.find("select").val();
        var select_val_array = '['+select_val+']';
        if (addto !== undefined && addto !== false) {
            add_to_jquery = jQuery("#"+addto);
            var number_id = add_to_jquery.find(" > li").length;
            number_id++;
            if (number_id > 0) {
                var i_count = 0;
                while (i_count < number_id) {
                    if (add_to_jquery.find(" > li.category_tabs_cat_"+number_id).length) {
                        number_id++;
                    }
                    i_count++;
                }
            }else {
                number_id++;
            }
            item_name = addto+"["+number_id+"][cat]";
            select_val_array = '';
        }
        var item_id = jQuery(this).data("id");
        var select_text = add_item_parent.find("select option:selected").text();
        
        if (add_to_jquery.find("#"+item_id+'_'+select_val).length) {
            add_to_jquery.find("#"+item_id+'_'+select_val).addClass("removered").slideUp(function() {
                jQuery(this).slideDown().removeClass("removered");
            });
        }else {
            add_to_jquery.append('<li id="'+item_id+'_'+select_val+'" class="ui-state-default'+(number_id !== undefined && number_id !== false?" category_tabs_cat_"+number_id:"")+'"><div class="widget-head ui-sortable-handle"><span>'+select_text+'</span></div><input name="'+item_name+select_val_array+'" value="'+select_val+'" type="hidden"><a class="del-builder-item"><span class="dashicons dashicons-trash"></span></a></li>');
        }
    });

    /* Add a custom addition */
    jQuery(".add-item.add-item-2.add-item-6.add-item-7").on("click",function () {
        var add_item = jQuery(this);
        var item_id = jQuery(this).data("id");
        var item_type = jQuery(this).data("type");
        var addto = jQuery(this).data("addto");
        var toadd = jQuery(this).data("toadd");
        var item_name = jQuery(this).data("name");
        var select_val = add_item.parent().find("select").val();
        var select_text = add_item.parent().find("select option:selected").text();
        
        if (jQuery("#"+item_id+'_'+select_val).length) {
            jQuery("#"+item_id+'_'+select_val).addClass("removered").slideUp(function() {
                jQuery(this).slideDown().removeClass("removered");
            });
        }else {
            jQuery("#"+addto+"-ul").append('<li class="additions-li" id="'+item_id+'_'+select_val+'"><div class="widget-head"><'+(toadd == 'yes'?'label':'span')+'>'+select_text+'</'+(toadd == 'yes'?'label':'span')+'>'+(toadd == 'yes'?'':'</div>')+(toadd == 'yes'?'<input name="'+item_name+'['+item_type+'-'+select_val+']['+item_type+']" value="yes" type="hidden">':'')+'<input name="'+item_name+'['+item_type+'-'+select_val+']'+(toadd == 'yes'?'[value]':'')+'" value="'+select_val+'" type="hidden"><div><a class="widget-handle"><span class="dashicons dashicons-editor-justify"></span></a><a class="'+(toadd == 'yes'?'del-cat-item ':'')+'del-builder-item"><span class="dashicons dashicons-trash"></span></a></div>'+(toadd == 'yes'?'</div>':'')+'</li>');
        }
    });
    
    function askme_admin_add_file(event, selector) {
        
        var frame,
        $el = jQuery(this),
        askme_admin_upload,
        askme_admin_selector = selector;
        
        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( askme_admin_upload ) {
            askme_admin_upload.open();
        } else {
            // Create the media frame.
            askme_admin_upload = wp.media.frames.askme_admin_upload = wp.media({
                // Set the title of the modal.
                title: $el.data('choose'),
                // Customize the submit button.
                button: {
                    // Set the text of the button.
                    text: $el.data('update'),
                    // Tell the button not to close the modal, since we're
                    // going to refresh the page when the image is selected.
                    close: false
                }
            });

            // When an image is selected, run a callback.
            askme_admin_upload.on( 'select', function() {
                // Grab the selected attachment.
                var attachment = askme_admin_upload.state().get('selection').first();
                var attachment_attr = attachment.toJSON();
                var attr_width = askme_admin_selector.find('input[type="button"]').attr("data-width");
                var attr_height = askme_admin_selector.find('input[type="button"]').attr("data-height");
                askme_admin_upload.close();
                askme_admin_selector.find('.upload').val(attachment.attributes.url).change();
                
                if (attr_height !== undefined && attr_height !== false) {
                    jQuery('#'+attr_height).val(attachment_attr.height);
                }
                if (attr_width !== undefined && attr_width !== false) {
                    jQuery('#'+attr_width).val(attachment_attr.width);
                }
                
                if (askme_admin_selector.hasClass("upload-button-2")) {
                    askme_admin_selector.parent().find('.upload').val(attachment.attributes.url);
                    askme_admin_selector.parent().find('.image_id').val(attachment.attributes.id);
                }else if (askme_admin_selector.find(".image_id")) {
                    askme_admin_selector.find('.image_id').val(attachment.attributes.id);
                }
                if (attachment.attributes.type == 'image') {
                    askme_admin_selector.find('.screenshot').empty().hide().append('<img src="' + attachment.attributes.url + '"><a class="remove-image">'+builder_ajax.remove_image+'</a>').slideDown('fast');
                }
                askme_admin_selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(builder_ajax.remove_image);
                askme_admin_selector.find('.vpanel-background-properties').slideDown();
                askme_admin_selector.find('.remove-image, .remove-file').on('click', function() {
                    askme_admin_remove_file(jQuery(this).parent().parent());
                });
            });

        }

        // Finally, open the modal.
        askme_admin_upload.open();
    }

    function askme_admin_remove_file(selector) {
        selector.find('.remove-image').hide();
        selector.find('.upload,.image_id').val('');
        selector.find('.vpane-background-properties').hide();
        selector.find('.screenshot').animate({
            opacity: 'hide',
            height: 'hide'
        }, 200, function() {
            selector.find('.screenshot').addClass('hide');
        });
        selector.find('.remove-file').unbind().addClass('upload-button').removeClass('remove-file').val(builder_ajax.upload_image).change();
        if ( jQuery('.section-upload .upload-notice').length ) {
            jQuery('.upload-button').remove();
        }
        selector.find('.upload-button').on('click', function(event) {
            askme_admin_add_file(event,jQuery(this).parent().parent());
        });
    }

    var update_form_elements = function(container,element = "") {
    
        container.find('.vpanel-form-text .vpanel-form-control,.vpanel-form-textarea .vpanel-form-control').keyup(function() {
            vpanel_form_change.call(this, jQuery(this).val());
        });
        
        container.find('.vpanel-form-select .vpanel-form-control,.vpanel-form-radio .vpanel-form-control,.vpanel-form-images .vpanel-form-control,.vpanel-form-checkbox .vpanel-form-control,.vpanel-form-multicheck_3 .vpanel-form-control').change(function() {
            var ct = jQuery(this);
            var checked = [];
            if (ct.attr('type') == "checkbox") {
                if (ct.prop('checked')) {
                    checked.push(ct.val());
                }
                checked = checked == "on" ? checked : false;
            }else {
                checked = jQuery(this).val();
            }
            vpanel_form_change.call(this, checked);
        });
        
        container.find('.section-multicheck_sort,.section-multicheck').each(function() {
            var pt = jQuery(this),
                    lastChecked = null;
        
            jQuery(this).find('.vpanel-form-control').on('change', function(e) {
                var checked = [];
                pt.find('.vpanel-form-control').each(function() {
                    var ct = jQuery(this);
                    if (ct.prop('checked')) {
                        checked.push(ct.val());
                    }
                });
                checked = (checked.length > 0 ? checked : "no");
                vpanel_form_change.call(this, checked);
            });
        
            jQuery(this).find('li > .widget-head > label').on('click', function(e) {
                var t = jQuery(this).find('.vpanel-form-control');
                if (!lastChecked) {
                    lastChecked = t;
                    return;
                }
        
                if (e.shiftKey) {
                    var curStart = t.parents('li').index(),
                            curEnd = lastChecked.parents('li').index(),
                            startIndex = Math.min(curStart, curEnd),
                            endIndex = Math.max(curStart, curEnd) + 1,
                            i;
        
                    for (i = startIndex; i < endIndex; i++) {
                        if (t.parents('li').index() != i) {
                            pt.find('li').eq(i).find('.vpanel-form-control').prop('checked', lastChecked.prop('checked'));
                        }
                    }
                }
                lastChecked = t;
            });
        });
        
        jQuery(".vpanel-form-elements").find(element+' .vpanel-form-text .vpanel-form-control,'+element+' .vpanel-form-textarea .vpanel-form-control').each(function() {
            vpanel_form_change.call(this, jQuery(this).val());
        });
        
        jQuery(".vpanel-form-elements").find(element+' .vpanel-form-select .vpanel-form-control,'+element+' .vpanel-form-select_category .vpanel-form-control,'+element+' .vpanel-form-radio .vpanel-form-control,'+element+' .vpanel-form-images .vpanel-form-control,'+element+' .vpanel-form-checkbox .vpanel-form-control').each(function() {
            var ct = jQuery(this);
            var checked = [];
            if (ct.attr('type') == "checkbox") {
                if (ct.prop('checked')) {
                    checked.push(ct.val());
                }
                checked = checked == "on" ? checked : false;
            }else {
                checked = jQuery(this).val();
            }
            vpanel_form_change.call(this, checked);
        });
        
        jQuery(".vpanel-form-elements").find(element+' .section-multicheck_sort,'+element+' .section-multicheck').each(function() {
            var pt = jQuery(this),
                    lastChecked = null;
        
            var checked = [];
            pt.find('.vpanel-form-control').each(function() {
                var ct = jQuery(this);
                if (ct.prop('checked')) {
                    checked.push(ct.val());
                }
            });
            checked = (checked.length > 0 ? checked : "no");
            vpanel_form_change.call(this, checked);
        });
        
        /* Image Options */
        container.find('.vpanel-radio-img-img').each(function () {
            var radio_img = jQuery(this);
            radio_img.parent().find('.vpanel-radio-img-label,.vpanel-radio-img-radio').hide();
            radio_img.show().on("click",function() {
                var radio_img = jQuery(this);
                radio_img.parent().parent().find('.vpanel-radio-img-img').removeClass('vpanel-radio-img-selected');
                radio_img.addClass('vpanel-radio-img-selected');
                radio_img.parent().find(".vpanel-radio-img-radio").click().attr('checked','checked');
                vpanel_form_change.call(this, radio_img.attr("value"));
            });
        });
        
        /* form element: colorpicker */
        container.find('.vpanel-form-color .vpanel-color,.vpanel-form-typography .vpanel-color,.vpanel-form-background .vpanel-color').each(function() {
           var t = this,
                is_set = jQuery(t).hasClass('wp-color-picker');

           if (is_set || jQuery(t).closest("#available-widgets").length) {
                return;
           }
           jQuery(t).wpColorPicker();
        });
        
        /* form element: datepicker */
        container.find('.vpanel-form-date .vpanel-date,.site-form-date .site-date').each(function() {
           var t = this,
                is_set = jQuery(t).hasClass('hasDatepicker');

           if (is_set || jQuery(t).closest("#available-widgets").length) {
                return;
           }
           jQuery(t).datepicker(jQuery(t).data('js'));
        });
        
        /* form element: sort sections */
        container.find('.section .sort-sections').each(function() {
           var t = this;

            if (!jQuery(t).hasClass("not-sort") && !jQuery(t).hasClass("sort-sections-with")) {
                jQuery(t).sortable({
                    placeholder: "ui-state-highlight",
                    handle: ".widget-head,.widget-handle",
                    cancel: ".builder-toggle-open,.builder-toggle-close,.builder_clone,.del-builder-item,.switch,.not-sort .widget-handle,.not-sort .del-builder-item"
                });
            }
        });
        
        /* form element: multicheck */
        container.find('.vpanel-form-multicheck_category .widget-switch').each(function() {
            var t = this,
                is_set = jQuery(t).hasClass('widget-switch-already'),
                checkbox_attr = jQuery(t).find(" > input").attr("id"),
                checkbox_for = "";

            if (is_set) {
                return;
            }

            if (checkbox_attr !== undefined && checkbox_attr !== false) {
                checkbox_for = " for='"+checkbox_attr+"'";
            }

            jQuery(t).addClass("widget-switch-already").attr("for",checkbox_attr).find(" > input").after("<label"+checkbox_for+" data-on='ON' data-off='OFF'></label>");
        });

        /* form element: sliderui */
        container.find('.vpanel-form-sliderui .v_sliderui,.vpanel-form-slider .v_sliderui').each(function() {
           var t = this,
               sId = "#" + jQuery(t).data('id'),
                to,
                d = {
                   range : "min",
                   value : parseInt(jQuery(t).data('val')),
                   min   : parseInt(jQuery(t).data('min')),
                   max   : parseInt(jQuery(t).data('max')),
                   step  : parseInt(jQuery(t).data('step')),
                   slide: function(e, ui) {
                        if(typeof to != 'undefined') {
                           clearTimeout(to);
                        }
                        jQuery(sId).val( ui.value );
                        to = setTimeout(function() {
                           vpanel_form_change.call(t, ui.value);
                        }, 400);
                   }
                },
                i;

           for(i in d) {
                if(typeof jQuery(t).data(i) != 'undefined') {
                   d[i] = jQuery(t).data(i);
                }
           }

           jQuery(t).slider(d);
        });
        
        /* form element: upload */
        container.find('.vpanel-form-upload .form-upload-images,.vpanel-form-background .form-upload-images').each(function() {
            var t = this;
            jQuery(t).find('.remove-image,.remove-file').on('click', function() {
                askme_admin_remove_file(jQuery(this).parent().parent());
            });
        
            jQuery(t).find('.upload-button').on("click", function( event ) {
                askme_admin_add_file(event,jQuery(this).parent().parent());
            });
            
            jQuery(t).find('.upload-button-2').on( "click", function( event ) {
                askme_admin_add_file(event,jQuery(this).parent().parent());
            });
        });

        /* form element: code */
        container.find('.vpanel-code-editor').each(function() {

           var t = this,
                id = jQuery(t).attr('id'),
                name = jQuery(t).data('name'),
                mode = jQuery(t).data('mode'),
                theme = jQuery(t).data('theme'),
                el_holder = jQuery(t).siblings('textarea[name="'+name+'"]');

           if(!el_holder.length) {
                el_holder = jQuery('<textarea>').attr({name:name}).addClass('vpanel-form-control').hide();
                jQuery(t).before(el_holder);
           }

           var askme_editor = askme.edit(id);
           askme_editor.setTheme('askme/theme/'+ theme);
           askme_editor.getSession().setMode('askme/mode/'+ mode);

           askme_editor.on('change', function(ev, editor) {
                var v = editor.getValue();
                el_holder.text(v);
                vpanel_form_change.call(t, v);
           });

        });
        
        /* fix wp_editor on ajax call */
        container.find('.vpanel-form-wp-editor').each(function() {

           var t = jQuery(this),
                p = t.parents('.vpanel-form-block'),
                p_is_hidden = p.hasClass('vpanel-hide'),
                id = t.find('.wp-editor-area').attr('id'),
                preloaded_wp_editor_id = 'vpanel_preloaded_editor_id';

           if ( typeof id == 'undefined'
                || typeof quicktags == 'undefined'
                || typeof tinyMCEPreInit != 'object'
                || typeof tinyMCEPreInit.mceInit != 'object'
                || ! tinyMCEPreInit.mceInit.hasOwnProperty(preloaded_wp_editor_id) ) {
                return;
           }

           p.removeClass('vpanel-hide');

           var mceinit_params = jQuery.extend(true, {}, tinyMCEPreInit.mceInit[preloaded_wp_editor_id]);

           mceinit_params.selector = '#'+id;
           mceinit_params.resize = true;
           mceinit_params.toolbar1 = mceinit_params.toolbar1.replace( /(fullscreen)/g, '' );
           mceinit_params.toolbar2 = mceinit_params.toolbar2.replace( /(wp_help)/g, '' );

           if (mceinit_params.hasOwnProperty('body_class')) {
                mceinit_params.body_class = mceinit_params.body_class.replace(preloaded_wp_editor_id, id);
           }

           tinyMCE.init(mceinit_params);
           tinyMCE.execCommand('mceAddEditor', false, id);
           quicktags({id : id});

           if (p_is_hidden) {
                p.addClass('vpanel-hide');
           }

        });
            
        return container;

    };
        
    jQuery(document).ajaxSuccess(function(e, xhr, settings) {
        var getParameterByName = function(name, url) {
                name = name.replace(/[\[\]]/g, "\\$&");
                var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                   results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, " "));
           },
           action = getParameterByName( 'action', settings.data );

        if (action == 'save-widget') {

           var widget_id = getParameterByName( 'widget-id', settings.data ),
                sidebar = getParameterByName( 'sidebar', settings.data );

           jQuery('#'+ sidebar +' .widget').each(function() {

                var t = jQuery(this),
                   id = t.attr('id');

                if (id.indexOf(widget_id) != -1) {
                   update_form_elements(t);
                }

           });

        }

    });

    /* Condition */
    var objectValues = function(obj) {
        var arr = [],
                o;
    
        for (o in obj) {
            arr.push(obj[o]);
        }
        return arr;
    };
    
    get_form_data = function(container) {
        var form_data = {},
                i;
    
        container.find('.section,.wrap_class,.options-group').each(function() {
            var t = jQuery(this),
                    id = t.attr('data-id'),
                    type = t.attr('data-type'),
                    fc = t.find('.vpanel-form-control');
            if (fc.length == 0) {
                return;
            }
    
            form_data[id] = {};
            var checked = [];
    
            if (fc.length > 1) {
                fc.each(function() {
                    var it = jQuery(this),
                            i = it.attr('value');
    
                    if (typeof i != 'undefined') {
                        if (type != 'checkbox' || it.prop('checked')) {
                            if (it.prop('checked')) {
                                checked.push(it.val());
                                form_data[id] = checked;
                            }else {
                                form_data[id][i] = it.val();
                            }
                        }
                    }
                });
            }else if (fc.length == 1) {
                if (type == 'checkbox') {
                    if (fc.prop('checked')) {
                        checked.push(fc.val());
                    }
                    checked = (checked == "on" || checked === 1 ? checked : false);
                    form_data[id] = checked;
                }else {
                    form_data[id] = fc.val();
                }
                if (type == 'wp_editor' && typeof(tinyMCE) != 'undefined') {
                    var ed = tinyMCE.editors[fc.attr('id')];
                    if (typeof ed != 'undefined') {
                        form_data[id] = ed.getContent();
                    }
                }
            }
        });
        return form_data;
    }

    vpanel_form_change = function(value) {
        //set_data_changed();
        var el = jQuery(this),
                el_form_block = el.parents('.section,.wrap_class,.options-group');
    
        if (!el_form_block.length) {
            return;
        }
        var id = el_form_block.attr('data-id');
        if (typeof id == 'undefined') {
            return;
        }
        var el_container = el.parents('#optionsframework');
        if (el.parents('.widget-content').length) {
            el_container = el.parents('.widget-content');
        }
        var form_data = get_form_data(el_container);
        el_container.find('.section,.wrap_class,.options-group').each(function() {
    
            var t = jQuery(this),
                    condition = t.attr('data-condition'),
                    operator = t.attr('data-operator');
    
            if (typeof condition == 'undefined') {
                return;
            }
    
            if (typeof operator == 'undefined' || ['and', 'or'].indexOf(operator) == -1) {
                operator = 'and';
            }
    
            var bool_arr = [],
                    cond_arr = condition.split('),'),
                    i;
    
            for (i in cond_arr) {
                if (cond_arr[i].slice(-1) != ')') {
                    cond_arr[i] += ')';
                }
    
                var m = cond_arr[i].match(/^([a-z0-9_]+)\:(not|is|has|has_not)\(([a-z0-9-_\,]+)\)$/i),
                        m_bool = false;
                
                if (m != null) {
                    var m_id = m[1],
                            m_op = m[2],
                            m_val = m[3];
                    if (!form_data.hasOwnProperty(m_id)) {
                        form_data[m_id] = '';
                    }
                    
                    if (['is', 'not'].indexOf(m_op) != -1) {
                        if (m_val == "empty" && (m_op == 'not' || m_op == 'is')) {
                            if (m_op == 'not') {
                                m_bool = (form_data[m_id] != "");
                            }else {
                                m_bool = (form_data[m_id] == "");
                            }
                        }else {
                            m_bool = (form_data[m_id] == m_val);
                            if (m_op == 'not') {
                                m_bool = !m_bool;
                            }
                        }
                    }else if (['has', 'has_not'].indexOf(m_op) != -1) {
                        if (typeof form_data[m_id] == 'string') {
                            form_data[m_id] = form_data[m_id].split(',');
                        }else if (typeof form_data[m_id] != 'object') {
                            form_data[m_id] = [];
                        }else if (!(form_data[m_id] instanceof Array)) {
                            form_data[m_id] = [];
                        }
                        m_val = m_val.split(',');
                        var j, k = [];
                        for (j in m_val) {
                            if (m_val != 0) {
                                k.push(form_data[m_id].indexOf(m_val[j]) != -1);
                            }
                        }
                        m_bool = (k.indexOf(false) == -1);
                        if (m_op == 'has_not') {
                            m_bool = !m_bool;
                        }
                    }
                }
                bool_arr.push(m_bool);
            }
    
            var is_hidden = false;
            if (operator == 'or') {
                is_hidden = (bool_arr.indexOf(true) == -1);
            }else {
                is_hidden = (bool_arr.indexOf(false) != -1);
            }
    
            if (is_hidden) {
                t.animate({
                    opacity: 'hide',
                    height: 'hide'
                }, 200, function() {
                    t.addClass('hide');
                });
            }else {
                t.animate({
                    opacity: 'show',
                    height: 'show'
                }, 200, function() {
                    t.removeClass('hide');
                });
            }
        });
    }

    update_form_elements(jQuery('body'));
    
});