function ask_me_upload_file(inp,editor) {
	var input = inp.get(0);
	var data = new FormData();
	data.append('image[file]',input.files[0]);
	data.append('action','ask_me_editor_upload_image');
	var editor_element = jQuery(editor.getElement()).parent();
	var editor_wrap = editor_element.closest(".wp-editor-wrap");
	editor_wrap.append('<div class="load_span"><div class="loader_2 search_loader"></div></div>');
	editor_wrap.find(".load_span").show(10);

	jQuery.ajax({
		url: (typeof(askme_js) !== 'undefined'?askme_js.admin_url:admin_ajax.ajax_a),
		type: 'POST',
		data: data,
		dataType: "JSON",
		processData: false,
		contentType: false,
		success: function(result,textStatus,jqXHR) {
			if (result.success == 0) {
				if (editor_wrap.find(".ask_error").length == 0) {
					editor_wrap.prepend('<div class="ask_error"><span><p>'+result.error+'</p></span></div>');
				}else {
					editor_wrap.find(".ask_error .required-error"),html(result.error);
				}
				editor_wrap.find(".ask_error").animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
			}else {
				editor.insertContent('<img class="content-img" src="'+result.success+'"/>');
			}
			editor_wrap.find(".load_span,.ask_error").hide(10);
		},
		error: function(jqXHR,textStatus,errorThrown) {
			if (jqXHR.responseText) {
				errors = JSON.parse(jqXHR.responseText).errors;
				if (editor_wrap.find(".ask_error").length == 0) {
					editor_wrap.prepend('<div class="ask_error"><span><p>'+(typeof(askme_js) !== 'undefined'?askme_js.error_uploading_image:admin_ajax.error_uploading_image)+'</p></span></div>');
				}else {
					editor_wrap.find(".ask_error .required-error"),html((typeof(askme_js) !== 'undefined'?askme_js.error_uploading_image:admin_ajax.error_uploading_image));
				}
				editor_wrap.find(".ask_error").animate({opacity: 'show' , height: 'show'}, 400).delay(5000).animate({opacity: 'hide' , height: 'hide'}, 400);
			}
		}
	});
}

(function() {
	if (typeof(askme_js) !== 'undefined' || typeof(admin_ajax) !== 'undefined') {
		tinymce.create('tinymce.plugins.ASKME',{
			init : function(editor,url) {
				var editor_element = jQuery(editor.getElement()).parent();
				if (editor_element.find(".tinymce-uploader").length == 0) {
					var inp = jQuery('<input class="tinymce-uploader" type="file" name="pic" accept="image/*" style="display:none">');
				}
				editor_element.append(inp);

				editor.addButton('custom_image_class',{
					title : (typeof(askme_js) !== 'undefined'?askme_js.insert_image:admin_ajax.insert_image),
					cmd : 'custom_image_class',
					image : url+'/image.png',
				});

				inp.on("change",function(e) {
					ask_me_upload_file(jQuery(this),editor);
				});

				editor.addCommand('custom_image_class',function() {
					inp.click();
				});
			},
		});
		tinymce.PluginManager.add('ASKME',tinymce.plugins.ASKME);
	}
})();