<?php
/* Image button */
add_action('init','ask_me_editor_buttons');
function ask_me_editor_buttons() {
	$custom_upload_image = apply_filters("ask_me_custom_upload_image",true);
	if ($custom_upload_image == true) {
	    add_filter("mce_external_plugins","ask_me_external_buttons");
	    add_filter("mce_buttons","ask_me_register_buttons");
	}
}
function ask_me_external_buttons($plugin_array) {
    $plugin_array['ASKME'] = get_template_directory_uri().'/editor/image.js';
    return $plugin_array;
}
function ask_me_register_buttons($buttons) {
    array_push($buttons,"custom_image_class");
    return $buttons;
}
add_action('wp_ajax_ask_me_editor_upload_image','ask_me_editor_upload_image');
add_action('wp_ajax_nopriv_ask_me_editor_upload_image','ask_me_editor_upload_image');
function ask_me_editor_upload_image() {
	$result = array();
	if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) :
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		require_once(ABSPATH . 'wp-admin/includes/file.php');
		$file = array(
			'name'	   => $_FILES['image']['name']['file'],
			'type'	   => $_FILES['image']['type']['file'],
			'tmp_name' => $_FILES['image']['tmp_name']['file'],
			'error'	   => $_FILES['image']['error']['file'],
			'size'	   => $_FILES['image']['size']['file']
		);
		$types = array("image/jpeg","image/bmp","image/jpg","image/png","image/gif","image/tiff","image/ico");
		if (!in_array($_FILES['image']['type'],$types)) :
			$result["success"] = 0;
			$result["error"] = esc_html__("Attachment Error! Please upload image only.","ask_me");
		endif;
		
		$attachment = wp_handle_upload($file,array('test_form' => false),current_time('mysql'));
		if (isset($attachment['error'])) :
			$result["success"] = 0;
			$result["error"] = esc_html__("Attachment Error: ","ask_me") . $attachment['error'];
		endif;

		if (isset($attachment['type']) && isset($attachment['file'])) :
			$user_id = get_current_user_id();
			$attachment_data = array(
				'post_mime_type' => $attachment['type'],
				'post_title'	 => preg_replace('/\.[^.]+$/','',basename($attachment['file'])),
				'post_content'   => '',
				'post_status'	 => 'inherit',
				'post_author'    => ($user_id > 0?$user_id:0),
			);
			$attachment_id = wp_insert_attachment($attachment_data,$attachment['file'],0);
			$attachment_metadata = wp_generate_attachment_metadata($attachment_id,$attachment['file']);
			wp_update_attachment_metadata($attachment_id,$attachment_metadata);
		endif;
		if (isset($attachment["url"]) && $attachment["url"] != "") {
			$result["success"] = $attachment["url"];
		}
	endif;
	echo json_encode($result);
	die();
}?>