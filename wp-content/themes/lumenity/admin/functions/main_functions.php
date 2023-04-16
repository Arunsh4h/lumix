<?php
if (is_user_logged_in()) {
	define("is_user_logged_in", true);
} else {
	define("is_user_logged_in", false);
}
define("askme_theme_url_tf", "https://www.intself.com");
define("askme_meta", "vbegy");
define("askme_terms", "vbegy");
define("askme_author", "vbegy");
if (!defined("prefix_meta")) {
	define("prefix_meta", askme_meta . "_");
}
if (!defined("prefix_terms")) {
	define("prefix_terms", askme_terms . "_");
}
if (!defined("prefix_author")) {
	define("prefix_author", askme_author . "_");
}
/* Theme options */
if (!function_exists('vpanel_options')) :
	function vpanel_options($name, $default = false)
	{
		return askme_options($name, $default);
	}
endif;
if (!function_exists('askme_options')) :
	function askme_options($name, $default = false)
	{
		$options = get_option(askme_options);
		if (isset($options[$name])) {
			return $options[$name];
		}
		return $default;
	}
endif;
/* Lumeno */
add_action("init", "askme_init");
function askme_init()
{
	$get_theme_name = get_option("get_theme_name");
	if ($get_theme_name != "askme") {
		update_option("get_theme_name", "askme");
	}
}
/* Switch theme */
function askme_deactivate_theme($new_name, $new_theme, $old_theme)
{
	flush_rewrite_rules(true);
	$get_theme_name = get_option("get_theme_name");
	if ($get_theme_name != "") {
		update_option("old_theme_name", $get_theme_name);
		delete_option("get_theme_name");
	}
}
add_action('switch_theme', 'askme_deactivate_theme', 1, 3);
/* Delete theme */
function askme_deleted_theme($stylesheet, $deleted)
{
	flush_rewrite_rules(true);
	$get_theme_name = get_option("get_theme_name");
	if ($get_theme_name != "") {
		update_option("old_theme_name", $get_theme_name);
		delete_option("get_theme_name");
	}
}
add_action('deleted_theme', 'askme_deleted_theme', 1, 2);
/* excerpt */
define("excerpt_type", askme_options("excerpt_type"));
function excerpt($excerpt_length, $excerpt_type = excerpt_type)
{
	global $post;
	$excerpt_length = (isset($excerpt_length) && $excerpt_length != "" ? $excerpt_length : 5);
	$content = $post->post_content;
	if ($excerpt_type == "characters") {
		$number = mb_strlen(trim(strip_tags(trim($content))));
		$content = mb_substr($content, 0, $excerpt_length, "UTF-8") . ($excerpt_length > 0 && $number > 0 && $number > $excerpt_length ? ' ...' : '');
	} else {
		$words = explode(' ', $content, $excerpt_length + 1);
		if (count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words, '');
			$content = implode(' ', $words) . '...';
		endif;
	}
	$content = strip_tags($content);
	echo esc_attr($content);
}
/* excerpt_title */
function excerpt_title($excerpt_length, $excerpt_type = excerpt_type)
{
	global $post;
	$excerpt_length = (isset($excerpt_length) && $excerpt_length != "" ? $excerpt_length : 5);
	$title = $post->post_title;
	if ($excerpt_type == "characters") {
		$title = mb_substr($title, 0, $excerpt_length, "UTF-8");
	} else {
		$words = explode(' ', $title, $excerpt_length + 1);
		if (count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words, '');
			$title = implode(' ', $words);
		endif;
	}
	$title = strip_tags($title);
	echo esc_attr($title);
}
/* excerpt_any */
function excerpt_any($excerpt_length, $title, $excerpt_type = excerpt_type)
{
	$excerpt_length = (isset($excerpt_length) && $excerpt_length != "" ? $excerpt_length : 5);
	if ($excerpt_type == "characters") {
		$title = mb_substr($title, 0, $excerpt_length, "UTF-8");
	} else {
		$words = explode(' ', $title, $excerpt_length + 1);
		if (count($words) > $excerpt_length) :
			array_pop($words);
			array_push($words, '');
			$title = implode(' ', $words) . '...';
		endif;
		$title = strip_tags($title);
	}
	return $title;
}
/* Early Access fonts */
function askme_earlyaccess_fonts($value)
{
	$earlyaccess = array("Alef+Hebrew", "Amiri", "Dhurjati", "Dhyana", "Droid+Arabic+Kufi", "Droid+Arabic+Naskh", "Droid+Sans+Ethiopic", "Droid+Sans+Tamil", "Droid+Sans+Thai", "Droid+Serif+Thai", "Gidugu", "Gurajada", "Hanna", "Jeju+Gothic", "Jeju+Hallasan", "Jeju+Myeongjo", "Karla+Tamil+Inclined", "Karla+Tamil+Upright", "KoPub+Batang", "Lakki+Reddy", "Lao+Muang+Don", "Lao+Muang+Khong", "Lao+Sans+Pro", "Lateef", "Lohit+Bengali", "Lohit+Devanagari", "Lohit+Tamil", "Mallanna", "Mandali", "Myanmar+Sans+Pro", "NATS", "NTR", "Nanum+Brush+Script", "Nanum+Gothic", "Nanum+Gothic+Coding", "Nanum+Myeongjo", "Nanum+Pen+Script", "Noto+Kufi+Arabic", "Noto+Naskh+Arabic", "Noto+Nastaliq+Urdu+Draft", "Noto+Sans+Armenian", "Noto+Sans+Bengali", "Noto+Sans+Cherokee", "Noto+Sans+Devanagari", "Noto+Sans+Devanagari+UI", "Noto+Sans+Ethiopic", "Noto+Sans+Georgian", "Noto+Sans+Gujarati", "Noto+Sans+Gurmukhi", "Noto+Sans+Hebrew", "Noto+Sans+Japanese", "Noto+Sans+Kannada", "Noto+Sans+Khmer", "Noto+Sans+Kufi+Arabic", "Noto+Sans+Lao", "Noto+Sans+Lao+UI", "Noto+Sans+Malayalam", "Noto+Sans+Myanmar", "Noto+Sans+Osmanya", "Noto+Sans+Sinhala", "Noto+Sans+Tamil", "Noto+Sans+Tamil+UI", "Noto+Sans+Telugu", "Noto+Sans+Thai", "Noto+Sans+Thai+UI", "Noto+Serif+Armenian", "Noto+Serif+Georgian", "Noto+Serif+Khmer", "Noto+Serif+Lao", "Noto+Serif+Thai", "Open+Sans+Hebrew", "Open+Sans+Hebrew+Condensed", "Padauk", "Peddana", "Phetsarath", "Ponnala", "Ramabhadra", "Ravi+Prakash", "Scheherazade", "Souliyo", "Sree+Krushnadevaraya", "Suranna", "Suravaram", "Tenali+Ramakrishna", "Thabit", "Tharlon", "cwTeXFangSong", "cwTeXHei", "cwTeXKai", "cwTeXMing", "cwTeXYen");
	if (in_array($value, $earlyaccess)) {
		return "earlyaccess";
	}
}
/* add post-thumbnails */
add_theme_support('post-thumbnails');
/* Check URL or ID */
function askme_image_url_id($url_id)
{
	if (is_numeric($url_id)) {
		$image = wp_get_attachment_url($url_id);
	}

	if (!isset($image)) {
		if (is_array($url_id)) {
			if (isset($url_id['id']) && $url_id['id'] != '' && $url_id['id'] != 0) {
				$image = wp_get_attachment_url($url_id['id']);
			} else if (isset($url_id['url']) && $url_id['url'] != '') {
				$id    = askme_get_attachment_id($url_id['url']);
				$image = ($id ? wp_get_attachment_url($id) : '');
			}
			$image = (isset($image) && $image != '' ? $image : $url_id['url']);
		} else {
			if (isset($url_id) && $url_id != '') {
				$id    = askme_get_attachment_id($url_id);
				$image = ($id ? wp_get_attachment_url($id) : '');
			}
			$image = (isset($image) && $image != '' ? $image : $url_id);
		}
	}
	if (isset($image) && $image != "") {
		return $image;
	}
}
/* askme_resize_url */
function askme_resize_url($img_width_f, $img_height_f, $thumbs = "", $gif = false)
{
	global $post;
	if (empty($thumbs)) {
		$thumb = get_post_thumbnail_id((isset($post->ID) && $post->ID > 0 ? $post->ID : ""));
	} else {
		$thumb = $thumbs;
	}
	if ($thumb != "") {
		$full_image = wp_get_attachment_image_src($thumb, "full");
		$or_width = $full_image[1];
		$or_height = $full_image[2];
		$image = askme_resize($thumb, '', $img_width_f, $img_height_f, true, $gif);
		if (isset($image['url']) && $img_width_f / $or_width <= 2) {
			$last_image = $image['url'];
		} else {
			$last_image = "https://placehold.jp/" . $img_width_f . "x" . $img_height_f;
		}
		if (isset($last_image) && $last_image != "") {
			return $last_image;
		}
	} else {
		return vpanel_image();
	}
}
/* askme_resize_img */
function askme_resize_img($img_width_f, $img_height_f, $img_lightbox = "", $thumbs = "", $gif = false, $title = "")
{
	global $post;
	if (empty($thumbs)) {
		$thumb = get_post_thumbnail_id((isset($post->ID) && $post->ID > 0 ? $post->ID : ""));
	} else {
		$thumb = $thumbs;
	}
	$last_image = askme_resize_url($img_width_f, $img_height_f, $thumb, $gif);

	if ($thumb != "") {
		if ($img_lightbox == "lightbox") {
			$img_url = wp_get_attachment_url($thumb, "full");
		}
	} else {
		$img_url = vpanel_image();
	}

	if (isset($last_image) && $last_image != "") {
		return ($img_lightbox == "lightbox" ? "<a href='" . esc_url($img_url) . "'>" : "") . "<img alt='" . (isset($title) && $title != "" ? $title : get_the_title()) . "' width='" . $img_width_f . "' height='" . $img_height_f . "' src='" . $last_image . "'>" . ($img_lightbox == "lightbox" ? "</a>" : "");
	}
}
/* askme_resize_by_url */
function askme_resize_by_url($url, $img_width_f, $img_height_f, $gif = false)
{
	$image = askme_resize("", $url, $img_width_f, $img_height_f, true, $gif);
	if (isset($image['url'])) {
		$last_image = $image['url'];
	} else {
		$last_image = "https://placehold.jp/" . $img_width_f . "x" . $img_height_f;
	}
	return $last_image;
}
/* askme_resize_by_url_img */
function askme_resize_by_url_img($url, $img_width_f, $img_height_f, $gif = false, $title = "")
{
	$last_image = askme_resize_by_url($url, $img_width_f, $img_height_f, $gif, $title);
	if (isset($last_image) && $last_image != "") {
		return "<img alt='" . (isset($title) && $title != "" ? $title : get_the_title()) . "' width='" . $img_width_f . "' height='" . $img_height_f . "' src='" . $last_image . "'>";
	}
}
/* askme_resize_img_full */
function askme_resize_img_full($thumbnail_size, $title = "")
{
	$thumb = get_post_thumbnail_id();
	if ($thumb != "") {
		$img_url = wp_get_attachment_url($thumb, $thumbnail_size);
		$image = $img_url;
		return "<img alt='" . (isset($title) && $title != "" ? $title : get_the_title()) . "' src='" . $image . "'>";
	}
}
/* vpanel_image */
function vpanel_image()
{
	global $post;
	ob_start();
	ob_end_clean();
	if (isset($post->post_content)) {
		$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
		if (isset($matches[1][0])) {
			return $matches[1][0];
		} else {
			return false;
		}
	}
}
/* askme_general_typography */
function askme_general_typography($askme_general_typography, $askme_css)
{
	$custom_css = '';
	$general_typography = askme_options($askme_general_typography);
	if (
		(isset($general_typography["style"]) && $general_typography["style"] != "" && $general_typography["style"] != "default") ||
		(isset($general_typography["size"]) && $general_typography["size"] != "" && $general_typography["size"] != "default" && is_string($general_typography["size"])) ||
		(isset($general_typography["color"]) && $general_typography["color"] != "")
	) {
		$custom_css .= '
		' . $askme_css . ' {';
		if (isset($general_typography["size"]) && $general_typography["size"] != "" && $general_typography["size"] != "default" && is_string($general_typography["size"])) {
			$custom_css .= 'font-size: ' . $general_typography["size"] . ';';
		}
		if (isset($general_typography["color"]) && $general_typography["color"] != "") {
			$custom_css .= 'color: ' . $general_typography["color"] . ';';
		}
		if (isset($general_typography["style"]) && $general_typography["style"] != "default" && $general_typography["style"] != "Style") {
			if ($general_typography["style"] == "bold italic" || $general_typography["style"] == "bold") {
				$custom_css .= 'font-weight: bold;';
			}
			if ($general_typography["style"] == "normal") {
				$custom_css .= 'font-weight: normal;';
			}
			if ($general_typography["style"] == "italic" || $general_typography["style"] == "bold italic") {
				$custom_css .= 'font-style: italic;';
			}
		}
		$custom_css .= '}';
	}
	return $custom_css;
}
/* askme_general_color */
function askme_general_color($askme_general_color, $askme_css, $askme_type, $important = false)
{
	$custom_css = '';
	$important = ($important == true ? " !important" : "");
	$general_link_color = askme_options($askme_general_color);
	if (isset($general_link_color) && $general_link_color != "") {
		$custom_css .= '
		' . $askme_css . ' {
			' . $askme_type . ': ' . $general_link_color . $important . ';
		}';
	}
	return $custom_css;
}
/* askme_general_background */
function askme_general_background($askme_general_background, $full_screen_background, $askme_css)
{
	$custom_css = '';
	$general_image = askme_options($askme_general_background);
	$general_background_color = $general_image["color"];
	$general_background_img = $general_image["image"];
	$general_background_repeat = $general_image["repeat"];
	$general_background_position = $general_image["position"];
	$general_background_fixed = $general_image["attachment"];
	$general_full_screen_background = askme_options($full_screen_background);

	if ($general_full_screen_background == "on") {
		$custom_css .= $askme_css . ' {';
		if (!empty($background_color)) {
			$custom_css .= 'background-color: ' . $general_background_color . ';';
		}
		$custom_css .= 'background-image : url("' . $general_background_img . '") ;
			filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src="' . $general_background_img . '",sizingMethod="scale");
			-ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\'' . $general_background_img . '\',sizingMethod=\'scale\')";
			background-size: cover;
		}';
	} else {
		if (!empty($general_image)) {
			if ($general_full_screen_background != "on") {
				if ((isset($general_background_img) && $general_background_img != "") || isset($general_background_color) && $general_background_color != "") {
					$custom_css .= $askme_css . '{background:' . esc_attr($general_background_color) . (isset($general_background_img) && $general_background_img != "" ? ' url("' . esc_attr($general_background_img) . '") ' . esc_attr($general_background_repeat) . ' ' . esc_attr($general_background_fixed) . ' ' . esc_attr($general_background_position) : '') . ';}';
				}
			}
		}
	}
	return $custom_css;
}
/* formatMoney */
function formatMoney($number, $fractional = false)
{
	if ($fractional) {
		$number = sprintf('%.2f', $number);
	}
	while (true) {
		$replaced = preg_replace('/(-?\d+)(\d\d\d)/', '$1,$2', $number);
		if ($replaced != $number) {
			$number = $replaced;
		} else {
			break;
		}
	}
	return $number;
}
/* get_twitter_count */
function get_twitter_count($twitter_username)
{
	$count = get_transient('vpanel_twitter_followers');
	if ($count !== false) return $count;

	$count           = 0;
	$access_token    = get_option('vpanel_twitter_token');
	$consumer_key    = askme_options('twitter_consumer_key');
	$consumer_secret = askme_options('twitter_consumer_secret');
	if ($access_token == "") {
		$credentials = $consumer_key . ':' . $consumer_secret;
		$toSend 	 = base64_encode($credentials);

		$args = array(
			'method'      => 'POST',
			'httpversion' => '1.1',
			'blocking' 		=> true,
			'headers' 		=> array(
				'Authorization' => 'Basic ' . $toSend,
				'Content-Type' 	=> 'application/x-www-form-urlencoded;charset=UTF-8'
			),
			'body' 				=> array('grant_type' => 'client_credentials')
		);

		add_filter('https_ssl_verify', '__return_false');
		$response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);

		$keys = json_decode(wp_remote_retrieve_body($response));

		if (!empty($keys->access_token)) {
			update_option('vpanel_twitter_token', $keys->access_token);
			$access_token = $keys->access_token;
		}
	}

	$args = array(
		'httpversion' => '1.1',
		'blocking' 		=> true,
		'timeout'     => 10,
		'headers'     => array('Authorization' => "Bearer $access_token")
	);

	add_filter('https_ssl_verify', '__return_false');
	$api_url  = "https://api.twitter.com/1.1/users/show.json?screen_name=$twitter_username";

	$get_request = wp_remote_get($api_url, $args);
	$request = wp_remote_retrieve_body($get_request);
	$request = @json_decode($request, true);

	if (!empty($request['followers_count'])) {
		$count = $request['followers_count'];
	}
	set_transient('vpanel_twitter_followers', $count, 60 * 60 * 24);
	return $count;
}
/* vpanel_counter_facebook */
function vpanel_counter_facebook($page_id)
{
	$count = get_transient('vpanel_facebook_followers');
	if ($count !== false) return $count;
	$count = 0;
	$get_request = wp_remote_get("https://www.facebook.com/plugins/likebox.php?href=https://facebook.com/" . $page_id . "&show_faces=true&header=false&stream=false&show_border=false&locale=en_US", array('timeout' => 20));
	$the_request = wp_remote_retrieve_body($get_request);
	$pattern = '/_1drq[^>]+>(.*?)<\/div/s';
	preg_match($pattern, $the_request, $matches);
	if (!empty($matches[1])) {
		$number = strip_tags($matches[1]);
		preg_match('!\d+!', $number, $matches);
		if (!empty($matches[0])) {
			$count .= $matches[0];
			set_transient('vpanel_facebook_followers', $count, 60 * 60 * 24);
		}
	}
	return $count;
}
/* vpanel_counter_youtube */
function vpanel_counter_youtube($youtube, $return = 'count')
{
	$count = get_transient('vpanel_youtube_followers');
	$api_key = askme_options('google_api');
	if ($count !== false) return $count;
	$count = 0;
	$data = wp_remote_get('https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $youtube . '&key=' . $api_key);
	if (!is_wp_error($data)) {
		$json = json_decode($data['body'], true);
		$count = intval($json['items'][0]['statistics']['subscriberCount']);
		set_transient('vpanel_youtube_followers', $count, 3600);
	}
	return $count;
}
/* vpanel_twitter_tweets */
if (!function_exists('vpanel_twitter_tweets')) :
	function vpanel_twitter_tweets($username = '', $tweets_count = 3)
	{
		$twitter_data    = "";
		$access_token    = get_option('vpanel_twitter_token');
		$consumer_key    = askme_options('twitter_consumer_key');
		$consumer_secret = askme_options('twitter_consumer_secret');
		if ($access_token == "") {
			$credentials = $consumer_key . ':' . $consumer_secret;
			$toSend 	 = base64_encode($credentials);

			$args = array(
				'method'      => 'POST',
				'httpversion' => '1.1',
				'blocking' 		=> true,
				'headers' 		=> array(
					'Authorization' => 'Basic ' . $toSend,
					'Content-Type' 	=> 'application/x-www-form-urlencoded;charset=UTF-8'
				),
				'body' 				=> array('grant_type' => 'client_credentials')
			);

			add_filter('https_ssl_verify', '__return_false');
			$response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);

			$keys = json_decode(wp_remote_retrieve_body($response));

			if (!empty($keys->access_token)) {
				update_option('vpanel_twitter_token', $keys->access_token);
				$access_token = $keys->access_token;
			}
		}

		$args = array(
			'httpversion' => '1.1',
			'blocking'    => true,
			'headers'     => array(
				'Authorization' => "Bearer $access_token",
			)
		);

		add_filter('https_ssl_verify', '__return_false');

		$api_url = "https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=$username&count=$tweets_count";
		$response = wp_remote_get($api_url, $args);

		if (!is_wp_error($response)) {
			$twitter_data = json_decode(wp_remote_retrieve_body($response));
		}

		return $twitter_data;
	}
endif;
/* Vpanel_Questions */
function Vpanel_Questions($questions_per_page = 5, $orderby = '', $display_date = '', $questions_excerpt = 0, $post_or_question = '', $excerpt_title = 5, $display_image = 'on', $display_author = '')
{
	global $post;
	$date_format = (askme_options("date_format") ? askme_options("date_format") : get_option("date_format"));
	$excerpt_title = ($excerpt_title != "" ? $excerpt_title : 5);
	$orderby_array = array();
	if ($orderby == "popular") {
		$orderby_array = array('orderby' => 'comment_count');
	} else if ($orderby == "random") {
		$orderby_array = array('orderby' => 'rand');
	}

	if ($orderby == "no_response") {
		add_filter('posts_where', 'ask_filter_where');
	}

	$block_users = askme_options("block_users");
	$author__not_in = array();
	if ($block_users == 1) {
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			$get_block_users = get_user_meta($user_id, "askme_block_users", true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__not_in = array("author__not_in" => $get_block_users);
			}
		}
	}

	$query = new WP_Query(array_merge($author__not_in, $orderby_array, array('post_type' => $post_or_question, 'ignore_sticky_posts' => 1, 'posts_per_page' => $questions_per_page, 'cache_results' => false, 'no_found_rows' => true)));
	if ($query->have_posts()) :
		echo "<ul class='related-posts'>";
		while ($query->have_posts()) : $query->the_post();
			if ($post_or_question == ask_questions_type) {
				$yes_private = ask_private($post->ID, $post->post_author, get_current_user_id());
			} else {
				$yes_private = 1;
			}
			if ($yes_private == 1) { ?>
<li class="related-item">
    <?php if (has_post_thumbnail() && $display_image == "on") { ?>
    <div class="author-img">
        <a href="<?php the_permalink(); ?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>"
            rel="bookmark">
            <?php echo askme_resize_img(60, 60); ?>
        </a>
    </div>
    <?php } ?>
    <div class="questions-div">
        <h3>
            <a href="<?php the_permalink(); ?>" title="<?php printf('%s', the_title_attribute('echo=0')); ?>"
                rel="bookmark">
                <?php if ($questions_excerpt == 0) { ?>
                <i class="icon-double-angle-right"></i>
                <?php }
								excerpt_title($excerpt_title); ?>
            </a>
        </h3>
        <?php if ($questions_excerpt != 0) { ?>
        <p><?php excerpt($questions_excerpt); ?></p>
        <?php }
						if ($display_date == "on") { ?>
        <div class="clear"></div><span
            <?php echo ($questions_excerpt == 0 ? "class='margin_t_5'" : "") ?>><?php the_time($date_format); ?></span>
        <?php }
						if ($display_author == "on") { ?>
        <div class="clear"></div>
        <span class="question-meta-author<?php echo ($questions_excerpt == 0 ? " margin_t_5" : "") ?>">
            <?php if ($post->post_author == 0) {
									$anonymously_user = get_post_meta($post->ID, 'anonymously_user', true);
									$anonymously_question = get_post_meta($post->ID, 'anonymously_question', true);
									if ($anonymously_question == 1 && $anonymously_user != "") {
										$question_username = esc_html__("Anonymous", "vbegy");
										$question_email = 0;
									} else {
										$question_username = get_post_meta($post->ID, 'question_username', true);
										$question_email = get_post_meta($post->ID, 'question_email', true);
										$question_username = ($question_username != "" ? $question_username : esc_html__("Anonymous", "vbegy"));
										$question_email = ($question_email != "" ? $question_email : 0);
									} ?>
            <i class="icon-user"></i><span><?php echo $question_username ?></span>
            <?php } else { ?>
            <a href="<?php echo vpanel_get_user_url($post->post_author) ?>"><i
                    class="icon-user"></i><?php echo get_the_author() ?></a>
            <?php do_action("askme_badge_widget_posts", $post->post_author);
								} ?>
        </span>
        <?php } ?>
    </div>
</li>
<?php }
		endwhile;
		echo "</ul>";
	endif;
	if ($orderby == "no_response") {
		remove_filter('posts_where', 'ask_filter_where');
	}
	wp_reset_postdata();
}
/* Vpanel_comments */
function Vpanel_comments($post_or_question = ask_questions_type, $comments_number = 5, $comment_excerpt = 30)
{

	$block_users = askme_options("block_users");
	$author__not_in = array();
	if ($block_users == 1) {
		$user_id = get_current_user_id();
		if ($user_id > 0) {
			$get_block_users = get_user_meta($user_id, "askme_block_users", true);
			if (is_array($get_block_users) && !empty($get_block_users)) {
				$author__not_in = array("post_author__not_in" => $get_block_users, "author__not_in" => $get_block_users);
			}
		}
	}

	$comments = get_comments(array_merge($author__not_in, array("post_type" => $post_or_question, "status" => "approve", "number" => $comments_number)));
	echo "<div class='widget_highest_points widget_comments'><ul>";
	foreach ($comments as $comment) {
		$your_avatar = get_the_author_meta(askme_avatar_name(), $comment->user_id);
		$user_profile_page = vpanel_get_user_url($comment->user_id);
		$yes_private = 1;
		$yes_private_answer = 1;
		if ($post_or_question == ask_questions_type) {
			$get_post = get_post($comment->comment_post_ID);
			$post_author = $get_post->post_author;
			$yes_private = ask_private($comment->comment_post_ID, $post_author, get_current_user_id());
			$yes_private_answer = ask_private_answer($comment->comment_ID, $comment->user_id, get_current_user_id(), $post_author);
		}
		if ($yes_private == 1 && $yes_private_answer == 1) { ?>
<li>
    <div class="author-img">
        <?php if ($comment->user_id != 0) { ?>
        <a href="<?php echo $user_profile_page ?>" original-title="<?php echo strip_tags($comment->comment_author); ?>"
            class="tooltip-n">
            <?php }
					echo askme_user_avatar($your_avatar, 65, 65, $comment->user_id, $comment->comment_author);
					if ($comment->user_id != 0) { ?>
        </a>
        <?php } ?>
    </div>
    <h6><a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo $comment->comment_ID; ?>"><?php echo strip_tags($comment->comment_author); ?>
            : <?php echo wp_trim_words($comment->comment_content, $comment_excerpt); ?></a></h6>
</li>
<?php }
	}
	echo "</ul></div>";
}
if (!function_exists('vbegy_comment')) {
	/* vbegy_comment */
	function vbegy_comment($comment, $args, $depth)
	{
		global $k;
		$k++;
		$GLOBALS['comment'] = $comment;
		$add_below = '';
		$comment_id = esc_attr($comment->comment_ID);
		$user_get_current_user_id = get_current_user_id();
		$can_delete_comment = askme_options("can_delete_comment");
		$can_edit_comment = askme_options("can_edit_comment");
		$can_edit_comment_after = askme_options("can_edit_comment_after");
		$can_edit_comment_after = (int)(isset($can_edit_comment_after) && $can_edit_comment_after > 0 ? $can_edit_comment_after : 0);
		if (version_compare(phpversion(), '5.3.0', '>')) {
			$time_now = strtotime(current_time('mysql'), date_create_from_format('Y-m-d H:i', current_time('mysql')));
		} else {
			list($year, $month, $day, $hour, $minute, $second) = sscanf(current_time('mysql'), '%04d-%02d-%02d %02d:%02d:%02d');
			$datetime = new DateTime("$year-$month-$day $hour:$minute:$second");
			$time_now = strtotime($datetime->format('r'));
		}
		$time_edit_comment = strtotime('+' . $can_edit_comment_after . ' hour', strtotime($comment->comment_date));
		$time_end = ($time_now - $time_edit_comment) / 60 / 60;
		$edit_comment = get_comment_meta($comment_id, "edit_comment", true);
		if (isset($k) && $k == askme_options("between_comments_position")) {
			$between_adv_type = askme_options("between_comments_adv_type");
			$between_adv_link = askme_options("between_comments_adv_link");
			$between_adv_code = askme_options("between_comments_adv_code");
			$between_adv_href = askme_options("between_comments_adv_href");
			$between_adv_img = askme_options("between_comments_adv_img");
			if (($between_adv_type == "display_code" && $between_adv_code != "") || ($between_adv_type == "custom_image" && $between_adv_img != "")) {
				echo '<li class="advertising advertising-answer">
	    			<div class="clearfix"></div>';
				if ($between_adv_type == "display_code") {
					echo do_shortcode(stripslashes($between_adv_code));
				} else {
					if ($between_adv_href != "") {
						echo '<a' . ($between_adv_link == "new_page" ? " target='_blank'" : "") . ' href="' . $between_adv_href . '">';
					}
					echo '<img alt="" src="' . $between_adv_img . '">';
					if ($between_adv_href != "") {
						echo '</a>';
					}
				}
				echo '<div class="clearfix"></div>
	    		</li><!-- End advertising -->';
			}
		}
		?>
<li <?php comment_class('comment'); ?> id="li-comment-<?php comment_ID(); ?>">
    <div id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
        <div class="avatar-img">
            <?php if ($comment->user_id != 0) {
						$vpanel_get_user_url = vpanel_get_user_url($comment->user_id, get_the_author_meta('nickname', $comment->user_id));
						if ($vpanel_get_user_url != "" && $vpanel_get_user_url != "vpanel_No_site") { ?>
            <a original-title="<?php echo strip_tags($comment->comment_author); ?>" class="tooltip-n"
                href="<?php echo esc_url($vpanel_get_user_url) ?>">
                <?php }
						echo askme_user_avatar(get_the_author_meta(askme_avatar_name(), $comment->user_id), 65, 65, $comment->user_id, $comment->comment_author);
						if ($vpanel_get_user_url != "" && $vpanel_get_user_url != "vpanel_No_site") { ?>
            </a>
            <?php }
					} else {
						$vpanel_get_user_url = ($comment->comment_author_url != "" ? $comment->comment_author_url : "vpanel_No_site");
						echo get_avatar($comment->comment_author_email, 65);
					} ?>
        </div>
        <div class="comment-text">
            <div class="author clearfix">
                <div class="comment-meta">
                    <div class="comment-author">
                        <?php if ($vpanel_get_user_url != "" && $vpanel_get_user_url != "vpanel_No_site") { ?>
                        <a href="<?php echo esc_url($vpanel_get_user_url) ?>">
                            <?php }
								$anonymously_user = get_comment_meta($comment_id, "anonymously_user", true);
								echo ($anonymously_user != "" ? esc_html__("Anonymous", "vbegy") : get_comment_author($comment_id));
								if ($vpanel_get_user_url != "" && $vpanel_get_user_url != "vpanel_No_site") { ?>
                        </a>
                        <?php }
								if ($comment->user_id != 0) {
									$verified_user = get_the_author_meta('verified_user', $comment->user_id);
									if ($verified_user == 1) {
										echo '<img class="verified_user tooltip-n" alt="' . __("Verified", "vbegy") . '" original-title="' . __("Verified", "vbegy") . '" src="' . get_template_directory_uri() . '/images/verified.png">';
									}
									echo vpanel_get_badge($comment->user_id);
								} ?>
                    </div>
                    <a href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo esc_attr($comment->comment_ID); ?>"
                        class="date"><i
                            class="fa fa-calendar"></i><?php printf(__('%1$s at %2$s', 'vbegy'), get_comment_date(), get_comment_time()) ?></a>
                </div>
                <div class="comment-reply">
                    <?php if (current_user_can('edit_comment', $comment_id) || ($can_edit_comment == 1 && $comment->user_id == get_current_user_id() && $comment->user_id != 0 && get_current_user_id() != 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after))) {
								echo "<a class='comment-edit-link edit-comment' href='" . esc_url(add_query_arg("comment_id", $comment_id, get_page_link(askme_options('edit_comment')))) . "'><i class='icon-pencil'></i>" . __("Edit", "vbegy") . "</a>";
							}
							if (($can_delete_comment == 1 && $comment->user_id == get_current_user_id() && $comment->user_id > 0 && get_current_user_id() > 0) || current_user_can('edit_comment', $comment_id) || is_super_admin(get_current_user_id())) {
								echo "<a class='comment-delete-link delete-comment' href='" . esc_url(add_query_arg(array('delete_comment' => $comment_id), get_permalink($comment->comment_post_ID))) . "'><i class='icon-trash'></i>" . __("Delete", "vbegy") . "</a>";
							}
							comment_reply_link(array_merge($args, array('reply_text' => '<i class="icon-reply"></i>' . __('Reply', 'vbegy'), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                </div>
            </div>
            <div class="text">
                <?php if ($edit_comment == "edited") { ?>
                <em><?php _e('This comment is edited.', 'vbegy') ?></em><br>
                <?php }
						if ($comment->comment_approved == '0') : ?>
                <em><?php _e('Your comment is awaiting moderation.', 'vbegy') ?></em><br>
                <?php endif;
						comment_text(); ?>
            </div>
        </div>
    </div>
    <?php
		}
	}
	/* Comment video */
	add_filter('wp_video_extensions', 'askme_video_extensions');
	function askme_video_extensions($exts)
	{
		$exts[] = 'mov';
		$exts[] = 'avi';
		$exts[] = 'wmv';
		return $exts;
	}
	/* askme_html_tags */
	function askme_html_tags($p_active = "")
	{
		global $allowedposttags, $allowedtags;
		$allowedposttags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
		$allowedposttags['a'] = array('href' => true, 'title' => true, 'target' => true, 'class' => true);
		$allowedposttags['br'] = array();
		$allowedposttags['div'] = array('class' => true);
		$allowedposttags['span'] = array('style' => true);
		$allowedtags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
		$allowedtags['a'] = array('href' => true, 'title' => true, 'target' => true, 'class' => true);
		$allowedtags['blockquote'] = array('class' => true, 'data-secret' => true, 'style' => true);
		$allowedtags['iframe'] = array('title' => true, 'width' => true, 'height' => true, 'src' => true, 'frameborder' => true, 'allow' => true, 'allowfullscreen' => true);
		$allowedtags['span'] = array('style' => true);
		$allowedtags['\\'] = array();
		$allowedtags['div'] = array('class' => true);
		$allowedtags['pre'] = array('class' => true, 'data-enlighter-language' => true);
		$array = array('hr', 'br', 'ul', 'ol', 'li', 'dl', 'dt', 'dd', 'table', 'td', 'tr', 'th', 'thead', 'tbody', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'cite', 'em', 'address', 'big', 'ins', 'sub', 'sup', 'tt', 'var');
		foreach ($array as $value) {
			$allowedtags[$value] = array();
		}
		if ($p_active == "yes") {
			$allowedtags['p'] = array('style' => true);
			$allowedposttags['p'] = array('style' => true);
		}
	}
	add_action('init', 'askme_html_tags', 10);
	/* askme_kses_stip */
	function askme_kses_stip($value, $ireplace = "", $p_active = "")
	{
		return wp_kses(stripslashes(($ireplace == "yes" ? str_ireplace(array("<br />", "<br>", "<br/>", "</p>"), "\r\n", $value) : $value)), askme_html_tags(($p_active == "yes" ? $p_active : "")));
	}
	/* askme_kses_stip_wpautop */
	function askme_kses_stip_wpautop($value, $ireplace = "", $p_active = "")
	{
		return wpautop(wp_kses(stripslashes((($ireplace == "yes" ? str_ireplace(array("<br />", "<br>", "<br/>", "</p>"), "\r\n", $value) : $value))), askme_html_tags(($p_active == "yes" ? $p_active : ""))));
	}
	/* Return video iframe */
	function askme_video_iframe($video_type, $video_id, $meta_type = "", $meta_name = "", $meta_id = 0)
	{
		if ($video_type == 'youtube') {
			preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $video_id, $matches);
			if (isset($matches[1])) {
				$video_id = $matches[1];
			}
			$type = "https://www.youtube.com/embed/" . $video_id;
		} else if ($video_type == 'vimeo') {
			preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/", $video_id, $matches);
			if (isset($matches[3])) {
				$video_id = $matches[3];
			}
			$type = "https://player.vimeo.com/video/" . $video_id;
		} else if ($video_type == 'daily') {
			preg_match("!^.+dailymotion\.com/(video|hub)/([^_]+)[^#]*(#video=([^_&]+))?|(dai\.ly/([^_]+))!", $video_id, $matches);
			if (isset($matches[2])) {
				$video_id = $matches[2];
			}
			$type = "https://www.dailymotion.com/embed/video/" . $video_id;
		} else if ($video_type == 'facebook') {
			preg_match("~(?:t\.\d+/)?(\d+)~i", $video_id, $matches);
			if (isset($matches[1])) {
				$video_id = $matches[1];
			}
			$type = "https://www.facebook.com/video/embed?video_id=" . $video_id;
			$type = "https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Ffacebook%2Fvideos%2F" . $video_id . "%2F&width=500&show_text=false&height=612&appId";
		} else if ($video_type == 'tiktok') {
			if (strpos($video_id, '/vm.tiktok.com/') !== false) {
				$response = wp_remote_get(esc_url($video_id));
				if (!is_wp_error($response) && isset($response['body'])) {
					$json = json_encode($response['body'], true);
					preg_match('/property="og:url" content="(.*?)}"/', stripslashes($json), $matchs);
					if (isset($matchs[1]) && $matchs[1] != "") {
						$video_id = explode("&amp;", $matchs[1]);
						if (isset($video_id[0]) && $video_id[0] != "") {
							$video_id = $video_id[0];
							$full_link = str_replace("?_r=1", "", $video_id);
							$explode = explode("/video/", $full_link);
							preg_match("~(?:t\.\d+/)?(\d+)~i", $full_link, $matches);
							if (isset($matches[1])) {
								$video_id = $matches[1];
							}
							if (isset($matches[1]) && isset($explode[1]) && $explode[1] != "" && $matches[1] != $explode[1]) {
								$video_id = $explode[1];
							}
							if (isset($full_link) && $full_link != "") {
								if ($meta_type == "options") {
									$get_option = get_option(askme_options);
									$get_option[$meta_name] = $full_link;
									update_option(askme_options, $get_option);
								} else if ($meta_type == "post_meta") {
									update_post_meta($meta_id, $meta_name, $full_link);
								} else if ($meta_type == "comment_meta") {
									update_comment_meta($meta_id, $meta_name, $full_link);
								}
							}
						}
					}
				}
			} else {
				$explode = explode("/video/", $video_id);
				preg_match("~(?:t\.\d+/)?(\d+)~i", $video_id, $matches);
				if (isset($matches[1])) {
					$video_id = $matches[1];
				}
				if (isset($matches[1]) && isset($explode[1]) && $explode[1] != "" && $matches[1] != $explode[1]) {
					$video_id = $explode[1];
				}
			}
			if (isset($video_id)) {
				$type = "https://www.tiktok.com/embed/" . $video_id;
			}
		}
		return (isset($type) ? $type : "");
	}
	/* vbegy_answer */
	if (!function_exists('vbegy_answer')) {
		function vbegy_answer($comment, $args, $depth)
		{
			global $post, $k;
			$k++;
			$GLOBALS['comment'] = $comment;
			$add_below = '';
			$comment_id = esc_attr($comment->comment_ID);
			$user_get_current_user_id = get_current_user_id();
			$yes_private_answer = ask_private_answer($comment_id, $comment->user_id, $user_get_current_user_id, $post->post_author);
			$comment_vote = get_comment_meta($comment_id, 'comment_vote', true);
			if (isset($comment_vote) && is_array($comment_vote) && isset($comment_vote["vote"])) {
				update_comment_meta($comment_id, 'comment_vote', $comment_vote["vote"]);
				$comment_vote = get_comment_meta($comment_id, 'comment_vote', true);
			} else if ($comment_vote == "") {
				update_comment_meta($comment_id, 'comment_vote', 0);
				$comment_vote = get_comment_meta($comment_id, 'comment_vote', true);
			}
			$active_best_answer = askme_options("active_best_answer");
			$the_best_answer = get_post_meta($post->ID, "the_best_answer", true);
			$best_answer_comment = get_comment_meta($comment_id, "best_answer_comment", true);
			$comment_best_answer = ($best_answer_comment == "best_answer_comment" || $the_best_answer == $comment_id ? "comment-best-answer" : "");
			$active_reports = askme_options("active_reports");
			$active_logged_reports = askme_options("active_logged_reports");
			$active_vote = askme_options("active_vote");
			$active_vote_unlogged = askme_options("active_vote_unlogged");
			$can_delete_comment = askme_options("can_delete_comment");
			$can_edit_comment = askme_options("can_edit_comment");
			$can_edit_comment_after = askme_options("can_edit_comment_after");
			$can_edit_comment_after = (int)(isset($can_edit_comment_after) && $can_edit_comment_after > 0 ? $can_edit_comment_after : 0);
			if (version_compare(phpversion(), '5.3.0', '>')) {
				$time_now = strtotime(current_time('mysql'), date_create_from_format('Y-m-d H:i', current_time('mysql')));
			} else {
				list($year, $month, $day, $hour, $minute, $second) = sscanf(current_time('mysql'), '%04d-%02d-%02d %02d:%02d:%02d');
				$datetime = new DateTime("$year-$month-$day $hour:$minute:$second");
				$time_now = strtotime($datetime->format('r'));
			}
			$time_edit_comment = strtotime('+' . $can_edit_comment_after . ' hour', strtotime($comment->comment_date));
			$time_end = ($time_now - $time_edit_comment) / 60 / 60;
			$edit_comment = get_comment_meta($comment_id, "edit_comment", true);
			if ($yes_private_answer != 1) { ?>
<li class="comment byuser comment ">
    <div class="comment-body clearfix" rel="post-<?php echo $post->ID ?>">
        <div class="comment-text">
            <div class="text">
                <p><?php _e("Sorry, this is a private answer.", "vbegy"); ?></p>
            </div>
        </div>
    </div>
    <?php } else { ?>
<li <?php comment_class('comment ' . $comment_best_answer); ?> id="li-comment-<?php comment_ID(); ?>">
    <div<?php echo ($best_answer_comment == "best_answer_comment" || $the_best_answer == $comment_id ? " itemprop='acceptedAnswer'" : " itemprop='suggestedAnswer'") ?>
        id="comment-<?php comment_ID(); ?>" class="comment-body clearfix" rel="post-<?php echo $post->ID ?>" itemscope
        itemtype="http://schema.org/Answer">
        <div class="avatar-img">
            <?php if ($comment->user_id != 0) {
						$vpanel_get_user_url = vpanel_get_user_url($comment->user_id, get_the_author_meta('nickname', $comment->user_id));
						if ($vpanel_get_user_url != "" && $vpanel_get_user_url != "vpanel_No_site") { ?>
            <a original-title="<?php echo strip_tags($comment->comment_author); ?>" class="tooltip-n"
                href="<?php echo esc_url($vpanel_get_user_url) ?>">
                <?php }
						echo askme_user_avatar(get_the_author_meta(askme_avatar_name(), $comment->user_id), 65, 65, $comment->user_id, $comment->comment_author);
						if ($vpanel_get_user_url != "" && $vpanel_get_user_url != "vpanel_No_site") { ?>
            </a>
            <?php }
					} else {
						$vpanel_get_user_url = ($comment->comment_author_url != "" ? $comment->comment_author_url : "vpanel_No_site");
						echo get_avatar($comment, 65);
					} ?>
        </div>
        <div class="comment-text">
            <div class="author clearfix">
                <div class="comment-author" itemprop="author" itemscope itemtype="http://schema.org/Person">
                    <?php if ($vpanel_get_user_url != "" && $vpanel_get_user_url != "vpanel_No_site") { ?>
                    <a itemprop="url" href="<?php echo esc_url($vpanel_get_user_url) ?>">
                        <?php }
							$anonymously_user = get_comment_meta($comment_id, "anonymously_user", true);
							echo '<span itemprop="name">' . ($anonymously_user != "" ? esc_html__("Anonymous", "vbegy") : get_comment_author($comment_id)) . '</span>';
							if ($vpanel_get_user_url != "" && $vpanel_get_user_url != "vpanel_No_site") { ?>
                    </a>
                    <?php }
							if ($comment->user_id != 0) {
								$verified_user = get_the_author_meta('verified_user', $comment->user_id);
								if ($verified_user == 1) {
									echo '<img class="verified_user tooltip-n" alt="' . __("Verified", "vbegy") . '" original-title="' . __("Verified", "vbegy") . '" src="' . get_template_directory_uri() . '/images/verified.png">';
								}
								echo vpanel_get_badge($comment->user_id);
							} ?>
                </div>
                <?php if ($active_vote == 1) {
							$show_dislike_answers = askme_options("show_dislike_answers"); ?>
                <div class="comment-vote">
                    <ul class="single-question-vote">
                        <?php if ((is_user_logged_in && $comment->user_id != $user_get_current_user_id) || (!is_user_logged_in && $active_vote_unlogged == 1)) { ?>
                        <li class="loader_3"></li>
                        <li><a href="#"
                                class="single-question-vote-up ask_vote_up comment_vote_up vote_allow<?php echo (isset($_COOKIE[askme_options("uniqid_cookie") . 'comment_vote' . $comment_id]) ? " " . $_COOKIE[askme_options("uniqid_cookie") . 'comment_vote' . $comment_id] . "-" . $comment_id : "") ?>"
                                title="<?php _e("Like", "vbegy"); ?>" id="comment_vote_up-<?php echo $comment_id ?>"><i
                                    class="icon-thumbs-up"></i></a></li>
                        <?php if ($show_dislike_answers != 1) { ?>
                        <li><a href="#"
                                class="single-question-vote-down ask_vote_down comment_vote_down vote_allow<?php echo (isset($_COOKIE[askme_options("uniqid_cookie") . 'comment_vote' . $comment_id]) ? " " . $_COOKIE[askme_options("uniqid_cookie") . 'comment_vote' . $comment_id] . "-" . $comment_id : "") ?>"
                                id="comment_vote_down-<?php echo $comment_id ?>"
                                title="<?php _e("Dislike", "vbegy"); ?>"><i class="icon-thumbs-down"></i></a></li>
                        <?php }
									} else { ?>
                        <li class="loader_3"></li>
                        <li><a href="#"
                                class="single-question-vote-up ask_vote_up comment_vote_up <?php echo (is_user_logged_in && $comment->user_id == $user_get_current_user_id ? "vote_not_allow" : "vote_not_user") ?>"
                                title="<?php _e("Like", "vbegy"); ?>"><i class="icon-thumbs-up"></i></a></li>
                        <?php if ($show_dislike_answers != 1) { ?>
                        <li><a href="#"
                                class="single-question-vote-down ask_vote_down comment_vote_down <?php echo (is_user_logged_in && $comment->user_id == $user_get_current_user_id ? "vote_not_allow" : "vote_not_user") ?>"
                                title="<?php _e("Dislike", "vbegy"); ?>"><i class="icon-thumbs-down"></i></a></li>
                        <?php }
									} ?>
                    </ul>
                </div>
                <span itemprop="upvoteCount"
                    class="question-vote-result question_vote_result<?php echo ($comment_vote < 0 ? " question_vote_red" : "") ?>"><?php echo ($comment_vote != "" ? $comment_vote : 0) ?></span>
                <?php } ?>
                <div class="comment-meta">
                    <?php $get_comment_date = get_comment_date("c", $comment_id);
							echo (is_single() ? '<span class="ask-hide" itemprop="dateCreated" datetime="' . $get_comment_date . '">' . $get_comment_date . '</span>' : ''); ?>
                    <a itemprop="url"
                        href="<?php echo get_permalink($comment->comment_post_ID); ?>#comment-<?php echo esc_attr($comment->comment_ID); ?>"
                        class="date"><span><i
                                class="fa fa-calendar"></i><?php printf(__('%1$s at %2$s', 'vbegy'), get_comment_date(), get_comment_time()) ?></span></a>
                </div>
                <div class="comment-reply">
                    <?php if (current_user_can('edit_comment', $comment_id) || ($can_edit_comment == 1 && $comment->user_id == $user_get_current_user_id && $comment->user_id != 0 && $user_get_current_user_id != 0 && ($can_edit_comment_after == 0 || $time_end <= $can_edit_comment_after))) {
								echo "<a class='comment-edit-link edit-comment' href='" . esc_url(add_query_arg("comment_id", $comment_id, get_page_link(askme_options('edit_comment')))) . "'><i class='icon-pencil'></i>" . __("Edit", "vbegy") . "</a>";
							}
							if (($can_delete_comment == 1 && $comment->user_id == $user_get_current_user_id && $comment->user_id > 0 && $user_get_current_user_id > 0) || current_user_can('edit_comment', $comment_id) || is_super_admin($user_get_current_user_id)) {
								echo "<a class='comment-delete-link delete-comment delete-answer' href='" . esc_url(add_query_arg(array('delete_comment' => $comment_id), get_permalink($comment->comment_post_ID))) . "'><i class='icon-trash'></i>" . __("Delete", "vbegy") . "</a>";
							}
							if ($active_reports == 1 && (is_user_logged_in || (!is_user_logged_in && $active_logged_reports != 1))) { ?>
                    <a class="question_r_l comment_l report_c" href="#"><i
                            class="icon-flag"></i><?php _e("Report", "vbegy") ?></a>
                    <?php }
							comment_reply_link(array_merge($args, array('reply_text' => '<i class="icon-reply"></i>' . __('Reply', 'vbegy'), 'login_text' => '<i class="icon-lock"></i>' . __('Log in to Reply', 'vbegy'), 'after' => '', 'depth' => $depth, 'max_depth' => $args['max_depth']))); ?>
                </div>
            </div>
            <div class="text">
                <?php if ($active_reports == 1 && (is_user_logged_in || (!is_user_logged_in && $active_logged_reports != 1))) { ?>
                <div class="explain-reported">
                    <h3><?php _e("Please briefly explain why you feel this answer should be reported.", "vbegy") ?></h3>
                    <textarea name="explain-reported"></textarea>
                    <div class="clearfix"></div>
                    <div class="loader_3"></div>
                    <div class="color button small report"><?php _e("Report", "vbegy") ?></div>
                    <div class="color button small dark_button cancel"><?php _e("Cancel", "vbegy") ?></div>
                </div><!-- End reported -->
                <?php }
						if ($edit_comment == "edited") { ?>
                <em class="comment-edited"><?php esc_html_e('This answer was edited.', 'vbegy') ?></em><br>
                <?php }
						if ($comment->comment_approved == '0') : ?>
                <em
                    class="comment-awaiting"><?php esc_html_e('Your answer is awaiting moderation.', 'vbegy') ?></em><br>
                <?php endif;
						$featured_image_question_answers = askme_options("featured_image_question_answers");
						if ($featured_image_question_answers == 1) {
							$featured_image = get_comment_meta($comment_id, 'featured_image', true);
							if ($featured_image != "") {
								$img_url = wp_get_attachment_url($featured_image, "full");
								if ($img_url != "") {
									$featured_image_answers_lightbox = askme_options("featured_image_answers_lightbox");
									$featured_image_answer_width = askme_options("featured_image_answer_width");
									$featured_image_answer_height = askme_options("featured_image_answer_height");
									$featured_image_answer_width = ($featured_image_answer_width != "" ? $featured_image_answer_width : 260);
									$featured_image_answer_height = ($featured_image_answer_height != "" ? $featured_image_answer_height : 185);
									$link_url = ($featured_image_answers_lightbox == 1 ? $img_url : get_permalink($comment->comment_post_ID) . "#comment-" . $comment->comment_ID);
									$last_image = askme_resize_img($featured_image_answer_width, $featured_image_answer_height, "", $featured_image);
									$featured_answer_position = askme_options("featured_answer_position");
									if ($featured_answer_position != "after" && isset($last_image) && $last_image != "") {
										echo "<div class='featured_image_answer'><a href='" . $link_url . "'>" . $last_image . "</a></div>
			    	        	    		<div class='clearfix'></div>";
									}
								}
							}
						}

						$answer_video = askme_options("answer_video");
						$video_answer_position = askme_options("video_answer_position");
						$video_answer_width = askme_options("video_answer_width");
						$video_answer_100 = askme_options("video_answer_100");
						$video_answer_height = askme_options("video_answer_height");
						$video_answer_description = get_comment_meta($comment_id, "video_answer_description", true);
						if ($answer_video == 1 && $video_answer_description == "on") {
							$video_answer_type = get_comment_meta($comment_id, "video_answer_type", true);
							$video_answer_id = get_comment_meta($comment_id, "video_answer_id", true);
							if ($video_answer_id != "") {
								$type = askme_video_iframe($video_answer_type, $video_answer_id, "comment_meta", "video_answer_id", $comment_id);
								$las_video = '<div class="question-video-loop answer-video' . ($video_answer_100 == 1 ? ' question-video-loop-100' : '') . ($video_answer_position == "after" ? ' question-video-loop-after' : '') . '"><iframe frameborder="0" allowfullscreen width="' . $video_answer_width . '" height="' . $video_answer_height . '" src="' . $type . '"></iframe></div>';

								if ($video_answer_position == "before" && $answer_video == 1 && isset($video_answer_id) && $video_answer_id != "" && $video_answer_description == "on") {
									echo ($las_video);
								}
							}
						} ?>
                <div itemprop="text"><?php comment_text(); ?></div>
                <div class="clearfix"></div>
                <?php if ($video_answer_position == "after" && $answer_video == 1 && isset($video_answer_id) && $video_answer_id != "" && $video_answer_description == "on") {
							echo ($las_video);
						} ?>
                <div class="clearfix"></div>
                <?php if ($featured_image_question_answers && isset($featured_answer_position) && $featured_answer_position == "after" && isset($img_url) && $img_url != "" && isset($last_image) && $last_image != "") {
							echo "<div class='featured_image_question featured_image_after'><a href='" . $link_url . "'>" . $last_image . "</a></div>
		    	        		<div class='clearfix'></div>";
						}

						$added_file = get_comment_meta($comment_id, 'added_file', true);
						if ($added_file != "") {
							echo "<div class='clearfix'></div><br><a href='" . wp_get_attachment_url($added_file) . "'>" . __("Attachment", "vbegy") . "</a>";
						}
						?>
            </div>
            <div class="clearfix"></div>
            <div class="loader_3"></div>
            <?php
					$user_best_answer = esc_attr(get_the_author_meta('user_best_answer', $user_get_current_user_id));
					if ($best_answer_comment == "best_answer_comment" || $the_best_answer == $comment_id) {
						echo '<div class="commentform question-answered question-answered-done"><i class="icon-ok"></i>' . __("Best answer", "vbegy") . '</div>
		    	        	<div class="clearfix"></div>';
						if (((is_user_logged_in && $user_get_current_user_id == $post->post_author && $active_best_answer == 1) || (isset($user_best_answer) && $user_best_answer == 1) || (is_super_admin($user_get_current_user_id))) && $the_best_answer != 0) {
							echo '<a class="commentform best_answer_re question-report" data-nonce="' . wp_create_nonce("askme_best_answer_nonce") . '" title="' . __("Cancel the best answer", "vbegy") . '" href="#">' . __("Cancel the best answer", "vbegy") . '</a>';
						}
					}
					if (((is_user_logged_in && $user_get_current_user_id == $post->post_author && $active_best_answer == 1) || (isset($user_best_answer) && $user_best_answer == 1) || (is_super_admin($user_get_current_user_id))) && ($the_best_answer == 0 || $the_best_answer == "")) { ?>
            <a class="commentform best_answer_a question-report"
                data-nonce="<?php echo wp_create_nonce("askme_best_answer_nonce") ?>"
                title="<?php _e("Select as best answer", "vbegy"); ?>"
                href="#"><?php _e("Select as best answer", "vbegy"); ?></a>
            <?php } ?>
            <div class="no_vote_more"></div>
            <?php do_action("askme_after_answer_action", $comment->user_id) ?>
        </div>
        </div>
        <?php
			}
			if (isset($k) && $k == askme_options("between_comments_position")) {
				$between_adv_type = askme_options("between_comments_adv_type");
				$between_adv_link = askme_options("between_comments_adv_link");
				$between_adv_code = askme_options("between_comments_adv_code");
				$between_adv_href = askme_options("between_comments_adv_href");
				$between_adv_img = askme_options("between_comments_adv_img");
				if (($between_adv_type == "display_code" && $between_adv_code != "") || ($between_adv_type == "custom_image" && $between_adv_img != "")) {
					echo '<li class="advertising advertising-answer">
					<div class="clearfix"></div>';
					if ($between_adv_type == "display_code") {
						echo do_shortcode(stripslashes($between_adv_code));
					} else {
						if ($between_adv_href != "") {
							echo '<a' . ($between_adv_link == "new_page" ? " target='_blank'" : "") . ' href="' . $between_adv_href . '">';
						}
						echo '<img alt="" src="' . $between_adv_img . '">';
						if ($between_adv_href != "") {
							echo '</a>';
						}
					}
					echo '<div class="clearfix"></div>
				</li><!-- End advertising -->';
				}
			}
		}
	}
	/* vpanel_pagination */
	if (!function_exists('vpanel_pagination')) {
		function vpanel_pagination($args = array(), $query = '')
		{
			global $wp_rewrite, $wp_query;
			do_action('vpanel_pagination_start');
			if ($query) {
				$wp_query = $query;
			} // End IF Statement
			/* If there's not more than one page,return nothing. */
			if (1 >= $wp_query->max_num_pages)
				return;
			/* Get the current page. */
			$current = askme_paged();
			$page_what = (get_query_var("paged") != "" ? "paged" : (get_query_var("page") != "" ? "page" : "paged"));
			/* Get the max number of pages. */
			$max_num_pages = intval($wp_query->max_num_pages);
			/* Set up some default arguments for the paginate_links() function. */
			$defaults = array(
				'base' => esc_url(add_query_arg($page_what, '%#%')),
				'format' => '',
				'total' => $max_num_pages,
				'current' => $current,
				'prev_next' => true,
				'prev_text' => '<i class="icon-angle-left"></i>',
				'next_text' => '<i class="icon-angle-right"></i>',
				'show_all' => false,
				'end_size' => 1,
				'mid_size' => 1,
				'add_fragment' => '',
				'type' => 'plain',
				'before' => '<div class="pagination">', // Begin vpanel_pagination() arguments.
				'after' => '</div>',
				'echo' => true,
			);
			/* Add the $base argument to the array if the user is using permalinks. */
			if ($wp_rewrite->using_permalinks())
				$defaults['base'] = user_trailingslashit(trailingslashit(get_pagenum_link()) . 'page/%#%');
			/* If we're on a search results page,we need to change this up a bit. */
			if (is_search()) {
				/* If we're in BuddyPress,use the default "unpretty" URL structure. */
				if (class_exists('BP_Core_User')) {
					$search_query = get_query_var('s');
					$paged = get_query_var('paged');
					$base = user_trailingslashit(home_url()) . '?s=' . $search_query . '&paged=%#%';
					$defaults['base'] = $base;
				} else {
					$search_permastruct = $wp_rewrite->get_search_permastruct();
					if (!empty($search_permastruct))
						$defaults['base'] = user_trailingslashit(trailingslashit(get_search_link()) . 'page/%#%');
				}
			}
			/* Merge the arguments input with the defaults. */
			$args = wp_parse_args($args, $defaults);
			/* Allow developers to overwrite the arguments with a filter. */
			$args = apply_filters('vpanel_pagination_args', $args);
			/* Don't allow the user to set this to an array. */
			if ('array' == $args['type'])
				$args['type'] = 'plain';
			/* Make sure raw querystrings are displayed at the end of the URL,if using pretty permalinks. */
			$pattern = '/\?(.*?)\//i';
			preg_match($pattern, $args['base'], $raw_querystring);
			if ($wp_rewrite->using_permalinks() && $raw_querystring)
				$raw_querystring[0] = str_replace('', '', $raw_querystring[0]);
			if (!empty($raw_querystring)) {
				@$args['base'] = str_replace($raw_querystring[0], '', $args['base']);
				@$args['base'] .= substr($raw_querystring[0], 0, -1);
			}
			/* Get the paginated links. */
			$page_links = paginate_links($args);
			/* Remove 'page/1' from the entire output since it's not needed. */
			$page_links = str_replace(array('&#038;paged=1\'', '/page/1\''), '\'', $page_links);
			/* Wrap the paginated links with the $before and $after elements. */
			$page_links = $args['before'] . $page_links . $args['after'];
			/* Allow devs to completely overwrite the output. */
			$page_links = apply_filters('vpanel_pagination', $page_links);
			do_action('vpanel_pagination_end');
			/* Return the paginated links for use in themes. */
			if ($args['echo'])
				echo $page_links;
			else
				return $page_links;
		}
	}
	/* askme_admin_bar_menu */
	add_action('admin_bar_menu', 'askme_admin_bar_menu', 70);
	function askme_admin_bar_menu($wp_admin_bar)
	{
		if (is_super_admin()) {
			$answers_count = get_all_comments_of_post_type(array(ask_questions_type, ask_asked_questions_type));
			if ($answers_count > 0) {
				$wp_admin_bar->add_node(array(
					'parent' => 0,
					'id' => 'answers',
					'title' => '<span class="ab-icon dashicons-before dashicons-format-chat"></span><span class=" count-' . $answers_count . '"><span class="">' . $answers_count . '</span></span>',
					'href' => admin_url('edit-comments.php?comment_status=all&answers=1')
				));
			}
		}
	}
	/* vpanel_admin_bar */
	function vpanel_admin_bar()
	{
		global $wp_admin_bar;
		if (is_super_admin()) {
			$count_questions_by_type = count_posts_by_type(array(ask_questions_type, ask_asked_questions_type), "draft");
			if ($count_questions_by_type > 0) {
				$wp_admin_bar->add_menu(array(
					'parent' => 0,
					'id' => 'questions_draft',
					'title' => '<span class="ab-icon dashicons-before dashicons-editor-help"></span><span class=" count-' . $count_questions_by_type . '"><span class="">' . $count_questions_by_type . '</span></span>',
					'href' => admin_url('edit.php?post_status=draft&post_type=' . ask_questions_type)
				));
			}
			if (is_singular(ask_asked_questions_type)) {
				global $post;
				if (isset($post->ID) && $post->ID > 0) {
					$wp_admin_bar->add_menu(array(
						'parent' => 0,
						'id' => 'edit_asked_question',
						'title' => '<span class="ab-icon dashicons-before dashicons-edit"></span>' . esc_html__("Edit Asked Question", "vbegy") . '</span></span>',
						'href' => admin_url('post.php?post=' . $post->ID . '&action=edit')
					));
				}
			}
			$count_posts_by_type = count_posts_by_type("post", "draft");
			if ($count_posts_by_type > 0) {
				$wp_admin_bar->add_menu(array(
					'parent' => 0,
					'id' => 'posts_draft',
					'title' => '<span class="ab-icon dashicons-before dashicons-media-text"></span><span class=" count-' . $count_posts_by_type . '"><span class="">' . $count_posts_by_type . '</span></span>',
					'href' => admin_url('edit.php?post_status=draft&post_type=post')
				));
			}
			$pay_ask = askme_options("pay_ask");
			$pay_to_sticky = askme_options("pay_to_sticky");
			$apply_filters = apply_filters("askme_new_payments_menu", false);
			if ($pay_ask == 1 || $pay_to_sticky == 1 || $apply_filters == true) {
				$new_payments = get_option("new_payments");
				$wp_admin_bar->add_menu(array(
					'parent' => 0,
					'id' => 'new_payments',
					'title' => '<span class="ab-icon dashicons-before dashicons-cart"></span><span class=" count-' . $new_payments . '"><span class="">' . $new_payments . '</span></span>',
					'href' => admin_url('admin.php?page=ask_payments')
				));
			}
			$count_messages_by_type = count_posts_by_type("message", "draft");
			if ($count_messages_by_type > 0) {
				$wp_admin_bar->add_menu(array(
					'parent' => 0,
					'id' => 'messages_draft',
					'title' => '<span class="ab-icon dashicons-before dashicons-email-alt"></span><span class=" count-' . $count_messages_by_type . '"><span class="">' . $count_messages_by_type . '</span></span>',
					'href' => admin_url('edit.php?post_status=draft&post_type=message')
				));
			}
			$count_user_under_review = count(get_users('&role=ask_under_review&blog_id=1'));
			if ($count_user_under_review > 0) {
				$wp_admin_bar->add_menu(array(
					'parent' => 0,
					'id' => 'user_under_review',
					'title' => '<span class="ab-icon dashicons-before dashicons-admin-users"></span><span class=" count-' . $count_user_under_review . '"><span class="">' . $count_user_under_review . '</span></span>',
					'href' => admin_url('users.php?role=ask_under_review')
				));
			}
			$support_activate = askme_updater()->is_active();
			if ($support_activate) {
				$wp_admin_bar->add_menu(array(
					'parent' => 0,
					'id' => 'vpanel_page',
					'title' => theme_name . ' Settings',
					'href' => admin_url('admin.php?page=options')
				));
			}
		}
	}
	add_action('wp_before_admin_bar_render', 'vpanel_admin_bar');
	/* Get post meta */
	function askme_post_meta($name, $type, $post_id = null, $default = false)
	{
		if (!$post_id) {
			$post_id = get_the_ID();
		}

		$value = get_post_meta($post_id, $name, true);

		if ('' !== $value && array() !== $value) {
			return $value;
		} else if ($default) {
			return $default;
		}

		return false;
	};
	/* breadcrumbs */
	function breadcrumbs($args = array())
	{
		$breadcrumbs_separator = askme_options("breadcrumbs_separator");
		$breadcrumbs_separator = ($breadcrumbs_separator != "" ? $breadcrumbs_separator : "/");
		$delimiter  = '<span class="crumbs-span">' . $breadcrumbs_separator . '</span>';
		$home       = __('Home', 'vbegy');
		$before     = '<h1>';
		$after      = '</h1>';
		$get_post_type = get_post_type();
		if (!is_home() && !is_front_page() || is_paged()) {
			if (is_page_template("template-users.php") || is_page_template("template-categories.php") || is_page_template("template-tags.php") || is_category() || is_tax(ask_question_category) || is_tax("product_cat") || is_tag() || is_tax(ask_question_tags) || is_tax("product_tag") || is_archive() || is_post_type_archive("product")) {
				$search_page         = askme_options('search_page');
				$live_search         = askme_options('live_search');
				$user_search         = askme_options('user_search');
				$user_filter         = askme_options('user_filter');
				$category_filter     = askme_options('category_filter');
				$cat_archives_search = askme_options('cat_archives_search');
				$cat_search          = askme_options('cat_search');
				$cat_filter          = askme_options('cat_filter');
				$child_category      = askme_options('child_category');
				$tag_archives_search = askme_options('tag_archives_search');
				$tag_search          = askme_options('tag_search');
				$tag_filter          = askme_options('tag_filter');
				$g_user_filter       = (isset($_GET["user_filter"]) && $_GET["user_filter"] != "" ? esc_html($_GET["user_filter"]) : "user_registered");
				$g_cat_filter        = (isset($_GET["cat_filter"]) && $_GET["cat_filter"] != "" ? esc_html($_GET["cat_filter"]) : "count");
				$g_tag_filter        = (isset($_GET["tag_filter"]) && $_GET["tag_filter"] != "" ? esc_html($_GET["tag_filter"]) : "count");
			}
			$breadcrumbs_6 = false;
			if ((is_page_template("template-users.php") && ($user_filter == 1 || $user_search == 1)) || (is_page_template("template-categories.php") && ($cat_filter == 1 || $cat_search == 1)) || (is_page_template("template-tags.php") && ($tag_filter == 1 || $tag_search == 1)) || ((is_tag() || is_tax(ask_question_tags) || is_tax("product_tag")) && $tag_archives_search == 1) || ((is_category() || is_tax(ask_question_category) || is_tax("product_cat") || is_archive() || is_post_type_archive(ask_questions_type) || is_post_type_archive("product")) && ($cat_archives_search == 1 || $category_filter == 1))) {
				$breadcrumbs_6 = true;
			}
			echo '<div class="breadcrumbs"><section class="container"><div class="row"><div class="' . ($breadcrumbs_6 == true ? "col-md-6" : "col-md-12") . '">';
			global $post, $wp_query;
			$item = array();
			$homeLink = home_url();
			if (is_search()) {
				echo $before . __("Search", "vbegy") . $after;
			} else if (is_page()) {
				echo $before . get_the_title() . $after;
			} else if (is_attachment()) {
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID);
				echo $before . get_the_title() . $after;
			} elseif (is_singular()) {
				$post = $wp_query->get_queried_object();
				$post_id = (int) $wp_query->get_queried_object_id();
				$post_type = $post->post_type;
				$post_type_object = get_post_type_object($post_type);
				if ('post' === $wp_query->post->post_type || ask_questions_type === $wp_query->post->post_type || ask_asked_questions_type === $wp_query->post->post_type || 'product' === $wp_query->post->post_type) {
					echo $before . get_the_title() . $after;
				}
				if ('page' !== $wp_query->post->post_type) {
					if (isset($args["singular_{$wp_query->post->post_type}_taxonomy"]) && is_taxonomy_hierarchical($args["singular_{$wp_query->post->post_type}_taxonomy"])) {
						$terms = wp_get_object_terms($post_id, $args["singular_{$wp_query->post->post_type}_taxonomy"]);
						echo array_merge($item, breadcrumbs_plus_get_term_parents($terms[0], $args["singular_{$wp_query->post->post_type}_taxonomy"]));
					} elseif (isset($args["singular_{$wp_query->post->post_type}_taxonomy"]))
						echo get_the_term_list($post_id, $args["singular_{$wp_query->post->post_type}_taxonomy"], '', ', ', '');
				}
			} else if (is_category() || is_tag() || is_tax()) {
				global $wp_query;
				$term = $wp_query->get_queried_object();
				$taxonomy = get_taxonomy($term->taxonomy);
				if ((is_taxonomy_hierarchical($term->taxonomy) && $term->parent) && $parents = breadcrumbs_plus_get_term_parents($term->parent, $term->taxonomy))
					$item = array_merge($item, $parents);
				echo $before . '' . single_cat_title('', false) . '' . $after;
			} elseif (is_day()) {
				echo $before . __('Daily Archives : ', 'vbegy') . get_the_time('d') . $after;
			} elseif (is_month()) {
				echo $before . __('Monthly Archives : ', 'vbegy') . get_the_time('F') . $after;
			} elseif (is_year()) {
				echo $before . __('Yearly Archives : ', 'vbegy') . get_the_time('Y') . $after;
			} elseif (is_single() && !is_attachment()) {
				if ($get_post_type != 'post' && $get_post_type != ask_questions_type && $get_post_type != ask_asked_questions_type && $get_post_type != 'product') {
					$post_type = get_post_type_object($get_post_type);
					$slug = $post_type->rewrite;
					echo $before . get_the_title() . $after;
				} else {
					$cat = get_the_category();
					if (isset($cat) && is_array($cat) && isset($cat[0])) {
						$cat = $cat[0];
					}
					echo $before . get_the_title() . $after;
				}
			} elseif (!is_single() && !is_page() && $get_post_type != 'post' && $get_post_type != ask_questions_type && $get_post_type != ask_asked_questions_type && $get_post_type != 'product') {
				if (is_author()) {
					$user_login = get_queried_object();
					if (isset($user_login) && is_object($user_login)) {
						$user_login = get_userdata(esc_attr($user_login->ID));
					}
					if (isset($user_login) && !is_object($user_login)) {
						$user_login = get_user_by('login', urldecode(get_query_var('author_name')));
					}
					if (isset($user_login) && !is_object($user_login)) {
						$user_login = get_user_by('slug', urldecode(get_query_var('author_name')));
					}
					echo $before . $user_login->display_name . $after;
				} else {
					$post_type = get_post_type_object($get_post_type);
					echo $before . (isset($post_type->labels->singular_name) ? $post_type->labels->singular_name : __("Error 404", "vbegy")) . $after;
				}
			} elseif (is_attachment()) {
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID);
				echo $before . get_the_title() . $after;
			} elseif (is_page() && !$post->post_parent) {
				echo $before . get_the_title() . $after;
			} elseif (is_page() && $post->post_parent) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
				echo $before . get_the_title() . $after;
			} elseif (is_search()) {
				echo $before . get_search_query() . $after;
			} elseif (is_tag()) {
				echo $before . single_tag_title('', false) . $after;
			} elseif (is_author()) {
				$user_login = get_queried_object();
				if (isset($user_login) && is_object($user_login)) {
					$user_login = get_userdata(esc_attr($user_login->ID));
				}
				if (isset($user_login) && !is_object($user_login)) {
					$user_login = get_user_by('login', urldecode(get_query_var('author_name')));
				}
				if (isset($user_login) && !is_object($user_login)) {
					$user_login = get_user_by('slug', urldecode(get_query_var('author_name')));
				}
				echo $before . $user_login->display_name . $after;
			} elseif (is_404()) {
				echo $before . __('Error 404 ', 'vbegy') . $after;
			} else if (is_archive()) {
				if (is_category() || is_tag() || is_tax()) {
					$term = $wp_query->get_queried_object();
					$taxonomy = get_taxonomy($term->taxonomy);
					if ((is_taxonomy_hierarchical($term->taxonomy) && $term->parent) && $parents = breadcrumbs_plus_get_term_parents($term->parent, $term->taxonomy))
						$item = array_merge($item, $parents);
					echo $before . $term->name . $after;
				} else if (function_exists('is_post_type_archive') && is_post_type_archive()) {
					$post_type_object = get_post_type_object(get_query_var('post_type'));
					echo $before . $post_type_object->labels->name . $after;
				} else if (is_date()) {
					if (is_day())
						echo $before . __('Archives for ', 'vbegy') . get_the_time('F j, Y') . $after;
					elseif (is_month())
						echo $before . __('Archives for ', 'vbegy') . single_month_title(' ', false) . $after;
					elseif (is_year())
						echo $before . __('Archives for ', 'vbegy') . get_the_time('Y') . $after;
				} else if (is_author()) {
					echo $before . __('Archives by: ', 'vbegy') . get_the_author_meta('display_name', $wp_query->post->post_author) . $after;
				}
			}
			$before     = '<span class="current">';
			$after      = '</span>';
			echo '<div class="clearfix"></div>
        <div class="crumbs">
        <a itemprop="breadcrumb" href="' . $homeLink . '">' . $home . '</a>' . $delimiter . ' ';
			if (is_search()) {
				echo $before . __("Search", "vbegy") . $after;
			} else if (is_category() || is_tag() || is_tax()) {
				global $wp_query;
				$term = $wp_query->get_queried_object();
				$taxonomy = get_taxonomy($term->taxonomy);
				if ((is_taxonomy_hierarchical($term->taxonomy) && $term->parent) && $parents = breadcrumbs_plus_get_term_parents($term->parent, $term->taxonomy))
					$item = array_merge($item, $parents);
				if (isset($term->term_id)) {
					echo ask_get_taxonomy_parents($term->term_id, $taxonomy->name, true, $delimiter, $term->term_id);
				}
				echo $before . '' . single_cat_title('', false) . '' . $after;
			} elseif (is_day()) {
				echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter . '';
				echo '<a href="' . get_month_link(get_the_time('Y'), get_the_time('m')) . '">' . get_the_time('F') . '</a>' . $delimiter . '';
				echo $before . get_the_time('d') . $after;
			} elseif (is_month()) {
				echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a>' . $delimiter . '';
				echo $before . get_the_time('F') . $after;
			} elseif (is_year()) {
				echo $before . get_the_time('Y') . $after;
			} elseif (is_single() && !is_attachment()) {
				if ($get_post_type != 'post') {
					$post_type = get_post_type_object($get_post_type);
					$slug = $post_type->rewrite;
					if ($get_post_type == ask_questions_type || $get_post_type == ask_asked_questions_type) {
						$question_category = wp_get_post_terms($post->ID, ask_question_category, array("fields" => "all"));
						if (isset($question_category[0])) {
							echo ask_get_taxonomy_parents($question_category[0]->term_id, ask_question_category, TRUE, $delimiter, $question_category[0]->term_id) .
								'<a href="' . get_term_link($question_category[0]->slug, ask_question_category) . '">' . $question_category[0]->name . '</a>' . $delimiter;
						}
					} else if ($get_post_type == 'product') {
						global $product;
						echo '<a href="' . get_post_type_archive_link("product") . '/">' . esc_html__("Shop", "vbegy") . '</a>' . $delimiter;
						echo $product->get_categories(', ', '');
						echo $delimiter;
					} else {
						echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a>' . $delimiter;
					}
					echo "" . $before . get_the_title() . $after;
				} else {
					$cat = get_the_category();
					$cat = (isset($cat[0]) ? $cat[0] : array());
					if (is_array($cat) && !empty($cat)) {
						echo get_category_parents($cat, true, ' ' . $delimiter . ' ');
					}
					echo $before . get_the_title() . $after;
				}
			} elseif (!is_single() && !is_page() && get_post_type() != 'post') {
				if (is_author()) {
					$user_login = get_queried_object();
					if (isset($user_login) && is_object($user_login)) {
						$user_login = get_userdata(esc_attr($user_login->ID));
					}
					if (isset($user_login) && !is_object($user_login)) {
						$user_login = get_user_by('login', urldecode(get_query_var('author_name')));
					}
					if (isset($user_login) && !is_object($user_login)) {
						$user_login = get_user_by('slug', urldecode(get_query_var('author_name')));
					}
					echo $before . $user_login->display_name . $after;
				} else {
					$post_type = get_post_type_object($get_post_type);
					echo $before . (isset($post_type->labels->singular_name) ? $post_type->labels->singular_name : __("Error 404", "vbegy")) . $after;
				}
			} elseif (is_attachment()) {
				$parent = get_post($post->post_parent);
				$cat = get_the_category($parent->ID);
				echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a>' . $delimiter . '';
				echo $before . get_the_title() . $after;
			} elseif (is_page() && !$post->post_parent) {
				echo $before . get_the_title() . $after;
			} elseif (is_page() && $post->post_parent) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
				echo $before . get_the_title() . $after;
			} elseif (is_search()) {
				echo $before . __('Search results for ', 'vbegy') . '"' . get_search_query() . '"' . $after;
			} elseif (is_tag()) {
				echo $before . __('Posts tagged ', 'vbegy') . '"' . single_tag_title('', false) . '"' . $after;
			} elseif (is_author()) {
				$user_login = get_queried_object();
				if (isset($user_login) && is_object($user_login)) {
					$user_login = get_userdata(esc_attr($user_login->ID));
				}
				if (isset($user_login) && !is_object($user_login)) {
					$user_login = get_user_by('login', urldecode(get_query_var('author_name')));
				}
				if (isset($user_login) && !is_object($user_login)) {
					$user_login = get_user_by('slug', urldecode(get_query_var('author_name')));
				}
				echo $before . $user_login->display_name . $after;
			} elseif (is_404()) {
				echo $before . __('Error 404 ', 'vbegy') . $after;
			}
			if (get_query_var('paged')) {
				if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo '';
				echo "<span class='crumbs-span'>/</span><span class='current'>" . __('Page', 'vbegy') . ' ' . get_query_var('paged') . "</span>";
				if (is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author()) echo '';
			}
			echo '</div></div>';
			if (!is_author() && (is_page_template("template-users.php") || is_page_template("template-categories.php") || is_page_template("template-tags.php") || is_tag() || is_category() || is_archive() || is_tax(ask_question_category) || is_tax(ask_question_tags) || is_post_type_archive(ask_questions_type) || is_tax("product_cat") || is_tax("product_tag") || is_post_type_archive("product"))) {
				echo '<div class="col-md-6">
		        <div class="search-form-breadcrumbs">
		        	<div class="row">';
				if (is_page_template("template-users.php") && $user_filter == 1) {
					echo '<div class="col-md-6' . (is_page_template("template-users.php") && $user_search == 1 ? "" : " col-md-right") . '">
			        	    	<form method="get" class="search-filter-form">
			        	    		<span class="styled-select user-filter">
			        	    		<select name="user_filter" onchange="this.form.submit()">
			        	    			<option value="user_registered" ' . selected($g_user_filter, "user_registered", false) . '>' . __("Register", "vbegy") . '</option>
			        	    			<option value="display_name" ' . selected($g_user_filter, "display_name", false) . '>' . __("Name", "vbegy") . '</option>
			        	    			<option value="ID" ' . selected($g_user_filter, "ID", false) . '>' . __("ID", "vbegy") . '</option>
			        	    			<option value="question_count" ' . selected($g_user_filter, "question_count", false) . '>' . __("Questions", "vbegy") . '</option>
			        	    			<option value="answers" ' . selected($g_user_filter, "answers", false) . '>' . __("Answers", "vbegy") . '</option>
			        	    			<option value="the_best_answer" ' . selected($g_user_filter, "the_best_answer", false) . '>' . __("Best Answers", "vbegy") . '</option>
			        	    			<option value="points" ' . selected($g_user_filter, "points", false) . '>' . __("Points", "vbegy") . '</option>
			        	    			<option value="post_count" ' . selected($g_user_filter, "post_count", false) . '>' . __("Posts", "vbegy") . '</option>
			        	    			<option value="comments" ' . selected($g_user_filter, "comments", false) . '>' . __("Comments", "vbegy") . '</option>
			        	    		</select>
			        	    		</span>
			        	    	</form>
			        		</div>';
				}
				if (is_page_template("template-tags.php") && $tag_filter == 1) {
					echo '<div class="col-md-6' . (is_page_template("template-tags.php") && $tag_search == 1 ? "" : " col-md-right") . '">
			        	    	<form method="get" class="search-filter-form">
			        	    		<span class="styled-select tag-filter">
			        	    		<select name="tag_filter" onchange="this.form.submit()">
			        	    			<option value="count" ' . selected($g_tag_filter, "count", false) . '>' . __("Popular", "vbegy") . '</option>
			        	    			<option value="name" ' . selected($g_tag_filter, "name", false) . '>' . __("Name", "vbegy") . '</option>
			        	    		</select>
			        	    		</span>
			        	    	</form>
			        		</div>';
				}
				if (is_page_template("template-categories.php") && $cat_filter == 1) {
					echo '<div class="col-md-6' . (is_page_template("template-categories.php") && $cat_search == 1 ? "" : " col-md-right") . '">
			        	    	<form method="get" class="search-filter-form">
			        	    		<span class="styled-select cat-filter">
			        	    		<select name="cat_filter" onchange="this.form.submit()">
			        	    			<option value="count" ' . selected($g_cat_filter, "count", false) . '>' . __("Popular", "vbegy") . '</option>
			        	    			<option value="name" ' . selected($g_cat_filter, "name", false) . '>' . __("Name", "vbegy") . '</option>
			        	    		</select>
			        	    		</span>
			        	    	</form>
			        		</div>';
				}
				if (!is_tag() && !is_tax(ask_question_tags) && !is_tax("product_tag") && ((is_category() || (!is_post_type_archive() && is_archive()) || is_tax(ask_question_category) || is_post_type_archive(ask_questions_type) || is_tax("product_cat") || is_post_type_archive("product")) && $category_filter == 1)) {
					$cats_search = 'category';
					if (is_tax(ask_question_category) || is_post_type_archive(ask_questions_type)) {
						$cats_search = ask_question_category;
					}
					if (is_tax("product_cat") || is_post_type_archive("product")) {
						$cats_search = 'product_cat';
					}
					$args = array(
						'parent'       => ($child_category == 1 ? 0 : ""),
						'orderby'      => 'name',
						'order'        => 'ASC',
						'hide_empty'   => 1,
						'hierarchical' => 1,
						'taxonomy'     => $cats_search,
						'pad_counts'   => false
					);
					$options_categories = get_categories($args);
					if ($child_category == 1 && isset($term->term_id)) {
						$children = get_terms(ask_question_category, array('parent' => $term->term_id, 'hide_empty' => 0));
						if (isset($children) && is_array($children) && !empty($children)) {
							$options_categories = $children;
						} else if (isset($term->parent) && $term->parent > 0) {
							$children = get_terms(ask_question_category, array('parent' => $term->parent, 'hide_empty' => 0));
							if (isset($children) && is_array($children) && !empty($children)) {
								$options_categories = $children;
							}
						}
					}
					if (isset($options_categories) && is_array($options_categories)) { ?>
        <div class="col-md-6<?php echo ($cat_archives_search == 1 ? "" : " col-md-right") ?> search-form">
            <div class="search-filter-form">
                <span class="styled-select cat-filter">
                    <?php $option_url = (is_tax(ask_question_category) || is_tax(ask_question_tags) || is_post_type_archive(ask_questions_type) ? get_post_type_archive_link(ask_questions_type) : (is_tax("product_cat") || is_tax("product_tag") || is_post_type_archive("product") ? get_post_type_archive_link("product") : "")) ?>
                    <select class="home_categories" data-taxonomy="<?php echo esc_attr($cats_search) ?>">
                        <option<?php echo (is_post_type_archive(ask_questions_type) ? ' selected="selected"' : '') ?>
                            value="<?php echo esc_url($option_url) ?>"><?php esc_html_e('All Categories', 'vbegy') ?>
                            </option>
                            <?php foreach ($options_categories as $category) {
												$option_url = get_term_link($category->slug, is_tax(ask_question_category) || is_tax(ask_question_tags) || is_post_type_archive(ask_questions_type) ? ask_question_category : (is_tax("product_tag") || is_tax("product_cat") || is_post_type_archive("product") ? "product_cat" : "category")); ?>
                            <option
                                <?php echo (is_category() || is_tax(ask_question_category) || is_tax("product_cat") || is_tax(ask_question_tags) || is_tax("product_tag") ? selected(esc_attr(get_query_var((is_category() ? 'cat' : 'term'))), (is_category() ? $category->term_id : $category->slug), false) : "") ?>
                                value="<?php echo esc_url($option_url) ?>"><?php echo esc_html($category->name) ?>
                            </option>
                            <?php } ?>
                    </select>
                </span>
            </div>
        </div><!-- End search-form -->
        <?php
					}
				}
				if ((is_page_template("template-users.php") && $user_search == 1) || (is_page_template("template-categories.php") && $cat_search == 1) || (is_page_template("template-tags.php") && $tag_search == 1) || ((is_tag() || is_tax(ask_question_tags) || is_tax("product_tag")) && $tag_archives_search == 1) || ((is_category() || (!is_post_type_archive() && is_archive()) || is_tax(ask_question_category) || is_post_type_archive(ask_questions_type) || is_tax("product_cat") || is_post_type_archive("product")) && $cat_archives_search == 1)) {
					$cats_search = $tags_search = 'posts';
					if (is_page_template("template-categories.php")) {
						$cats_tax = askme_post_meta('vbegy_cats_tax', 'type=radio', $post->ID);
						if ($cats_tax == ask_questions_type) {
							$cats_search = ask_question_category;
						} else if ($cats_tax == "product") {
							$cats_search = "product_cat";
						}
					}
					if (is_tax(ask_question_category) || is_post_type_archive(ask_questions_type)) {
						$cats_search = "questions";
					}
					if (is_tax("product_cat") || is_post_type_archive("product")) {
						$cats_search = "products";
					}

					if (is_page_template("template-tags.php")) {
						$tags_tax = askme_post_meta('vbegy_tags_tax', 'type=radio', $post->ID);
						if ($tags_tax == ask_questions_type) {
							$tags_search = ask_question_tags;
						} else if ($tags_tax == "product") {
							$tags_search = 'products';
						}
					}
					if (is_tax(ask_question_tags)) {
						$tags_search = 'questions';
					}
					if (is_tax("product_tag")) {
						$tags_search = 'products';
					}
					echo '<div class="col-md-6' . ((is_page_template("template-users.php") && $user_filter == 1) || (is_page_template("template-categories.php") && $cat_filter == 1) || (is_page_template("template-tags.php") && $tag_filter == 1) || ((is_category() || is_tax(ask_question_category) || is_tax("product_cat")) && $category_filter == 1) ? "" : " col-md-right") . '">
					        	<form method="get" action="' . esc_url((isset($search_page) && $search_page != "" ? get_page_link($search_page) : "")) . '" class="search-input-form">';
					if (isset($search_page) && $search_page != "") {
						echo '<input type="hidden" name="page_id" value="' . esc_attr($search_page) . '">';
					}
					echo '<input' . ($live_search == 1 ? " class='live-search breadcrumbs-live-search' autocomplete='off'" : "") . ' type="search" name="search" placeholder="' . __("Type to find...", "vbegy") . '">
					        		<button class="button-search"><i class="icon-search"></i></button>';
					if ($live_search == 1) {
						echo '<div class="search-results results-empty"></div>';
					}
					echo '<input type="hidden" name="search_type" class="search_type" value="' . (is_page_template("template-users.php") ? "users" : (is_page_template("template-tags.php") || is_tag() || is_tax(ask_question_tags) || is_tax("product_tag") ? $tags_search : $cats_search)) . '">
					        	</form>
				        	</div>';
				}
				echo '</div>
		        </div>
	        </div>';
			}
			echo '</div></section></div>';
		}
	}
	/* breadcrumbs_plus_get_term_parents */
	function breadcrumbs_plus_get_term_parents($parent_id = '', $taxonomy = '', $separator = '/')
	{
		$html = array();
		$parents = array();
		if (empty($parent_id) || empty($taxonomy))
			return $parents;
		while ($parent_id) {
			$parent = get_term($parent_id);
			$parents[] = '<a href="' . get_term_link($parent, $parent->taxonomy) . '" title="' . esc_attr($parent->name) . '">' . $parent->name . '</a>';
			$parent_id = $parent->parent;
		}
		if ($parents)
			$parents = array_reverse($parents);
		return $parents;
	}
	/* ask_get_taxonomy_parents */
	function ask_get_taxonomy_parents($id, $taxonomy = 'category', $link = false, $separator = '/', $main_id = '', $nicename = false, $visited = array())
	{
		$out = '';
		$parent = get_term($id, $taxonomy);

		if (is_wp_error($parent)) {
			return $parent;
		}
		if ($nicename) {
			$name = $parent->slug;
		} else {
			$name = (isset($parent->name) ? $parent->name : "");
		}

		if (isset($parent->parent) && ($parent->parent != $parent->term_id) && !in_array($parent->parent, $visited)) {
			$visited[] = $parent->parent;
			$get_taxonomy_parents = ask_get_taxonomy_parents($parent->parent, $parent->taxonomy, $link, $separator, '', $nicename, $visited);
			$out .= (is_string($get_taxonomy_parents) ? $get_taxonomy_parents : "");
		}
		if ($link) {
			if (isset($parent->term_id) && $parent->term_id != $main_id) {
				$out .= '<a href="' . esc_url(get_term_link($parent, $parent->taxonomy)) . '">' . $name . '</a>' . $separator;
			}
		} else {
			$out .= $name . $separator;
		}
		return $out;
	}
	/* askme_get_attachment_id */
	function askme_get_attachment_id($image_url)
	{
		global $wpdb;
		$pathinfo = pathinfo($image_url);
		$image_url = $pathinfo['filename'] . '.' . $pathinfo['extension'];
		if (strpos($image_url, esc_url(home_url('/'))) !== false && strpos($image_url, "themes/" . get_template() . "/image") === false) {
			$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid RLIKE '%s';", $image_url));
			if (isset($attachment[0]) && $attachment[0] != "") {
				return $attachment[0];
			}
		}
	}
	/* Get avatar link */
	function askme_get_user_avatar_link($args = array())
	{
		$defaults = array(
			'user_id'     => '',
			'your_avatar' => '',
			'size'        => '',
			'user_name'   => '',
		);

		$args = wp_parse_args($args, $defaults);

		$user_id     = $args['user_id'];
		$your_avatar = $args['your_avatar'];
		$size        = $args['size'];
		$user_name   = $args['user_name'];
		$user_meta_avatar = askme_avatar_name();
		$avatar = askme_user_avatar_link(get_the_author_meta($user_meta_avatar, $user_id), $size, $size, $user_id, $user_name);
		$avatar = apply_filters("askme_filter_avatar_image_link", $avatar, $user_meta_avatar, $your_avatar, $user_id);
		return $avatar;
	}
	/* askme_user_avatar */
	function askme_user_avatar_link($your_avatar, $img_width, $img_height, $user_id, $user_name, $user = "")
	{
		$user_meta_avatar = askme_avatar_name();
		if ($your_avatar && $user_id > 0) {
			$avatar = askme_get_user_avatar($your_avatar, $img_width, $img_height, $user_id);
		} else {
			$avatar = get_avatar_url((!empty($user) ? $user : $user_id), $img_width, "", $user_name);
		}
		$avatar = apply_filters("askme_filter_avatar_image", $avatar, $user_meta_avatar, $your_avatar, (isset($user_id) ? $user_id : 0));
		return $avatar;
	}
	/* askme_get_user_avatar */
	function askme_get_user_avatar($your_avatar, $img_width, $img_height, $user_id)
	{
		$avatar_num = false;
		if (isset($your_avatar) && $your_avatar != "" && is_numeric($your_avatar)) {
			$avatar_num = true;
		} else if ($your_avatar != "") {
			$get_attachment_id = askme_get_attachment_id($your_avatar);
			if (isset($get_attachment_id) && $get_attachment_id != "" && is_numeric($get_attachment_id)) {
				$avatar_num = true;
				$your_avatar = $get_attachment_id;
			}
		}

		if ($avatar_num == true) {
			$avatar = askme_resize_url($img_width, $img_height, $your_avatar);
		} else {
			$avatar = askme_resize_by_url($your_avatar, $img_width, $img_height);
		}
		return $avatar;
	}
	/* askme_user_avatar */
	function askme_user_avatar($your_avatar, $img_width, $img_height, $user_id, $user_name, $user = "", $itemprop = false)
	{
		return "<img" . ($itemprop == true ? " itemprop='image'" : "") . " class='avatar avatar-" . $img_width . " photo' alt='" . (isset($user_name) && $user_name != "" ? $user_name : "") . "' width='" . $img_width . "' height='" . $img_height . "' src='" . askme_user_avatar_link($your_avatar, $img_width, $img_height, $user_id, $user) . "'>";
	}
	/* askme_avatar */
	add_filter('get_avatar', 'askme_avatar', 1, 5);
	function askme_avatar($avatar, $id_or_email, $size, $default, $alt)
	{
		$user = false;
		if (is_numeric($id_or_email)) {
			$id = (int)$id_or_email;
			$user = get_user_by('id', $id);
		} elseif (is_object($id_or_email)) {
			if (!empty($id_or_email->user_id)) {
				$id = (int)$id_or_email->user_id;
				$user = get_user_by('id', $id);
			}
		} else {
			$user = get_user_by('email', $id_or_email);
		}
		if ($user && is_object($user)) {
			if ($user->data->ID > 0) {
				$your_avatar = get_the_author_meta(askme_avatar_name(), $user->data->ID);
				if ($your_avatar != "") {
					$avatar = askme_user_avatar($your_avatar, $size, $size, $user->data->ID, $alt);
				}
			}
		}
		return $avatar;
	}
	/* Count posts by user */
	if (!function_exists('askme_count_posts_by_user')) :
		function askme_count_posts_by_user($user_id, $post_type = null, $post_status = "publish", $category = 0, $date = 0)
		{
			$post_type = (is_array($post_type) ? $post_type : ($post_type != "" ? $post_type : "post"));
			$author = ($user_id > 0 ? array("author" => $user_id) : array());
			$tax = (is_array($category) && !empty($category) ? array("tax_query" => array(array("taxonomy" => (is_string($post_type) && $post_type == "post" ? "category" : ask_question_category), "field" => "id", "terms" => $category, 'operator' => 'IN'))) : array());
			$meta_query = ((is_string($post_type) && ($post_type == ask_questions_type || $post_type == ask_asked_questions_type)) || (is_array($post_type) && (in_array(ask_questions_type, $post_type) || in_array(ask_asked_questions_type, $post_type))) ? array("meta_query" => array("relation" => "OR", array("key" => "private_question", "compare" => "NOT EXISTS"), array("key" => "private_question", "compare" => "=", "value" => 0))) : array());
			$date_query = (is_array($date) && !empty($date) ? array("date_query" => array($date)) : array());
			$args = array(
				"post_type"   => $post_type,
				"post_status" => $post_status,
			);
			$args = array_merge($author, $tax, $meta_query, $date_query, $args);
			$the_query = new WP_Query($args);
			return $the_query->found_posts;
			wp_reset_postdata();
		}
	endif;
	/* count_posts_by_type */
	function count_posts_by_type($post_type = null, $post_status = "publish")
	{
		global $wpdb;
		$post_type = (is_array($post_type) ? $post_type : ($post_type != "" ? $post_type : "post"));
		$custom_post_type = "AND (";
		if (is_array($post_type)) {
			$key = 0;
			foreach ($post_type as $value) {
				if ($key != 0) {
					$custom_post_type .= " OR ";
				}
				$custom_post_type .= "$wpdb->posts.post_type = '$value'";
				$key++;
			}
		} else {
			$custom_post_type .= "$wpdb->posts.post_type = '$post_type'";
		}
		$custom_post_type .= ")";
		$where = "WHERE $wpdb->posts.post_status = '$post_status' " . $custom_post_type;
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts $where");
		return $count;
	}
	/* count_paid_question_by_type */
	function count_paid_question_by_type($user_id = "", $post_status = "publish")
	{
		global $wpdb;
		$where = "WHERE $wpdb->posts.post_type = '" . ask_questions_type . "' AND $wpdb->posts.post_status = '$post_status' AND post_author = $user_id AND ( ( $wpdb->postmeta.meta_key = '_paid_question' AND $wpdb->postmeta.meta_value = 'paid' ) )";
		$count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts INNER JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) $where");
		return $count;
	}
	/* count_asked_question_by_type */
	function count_asked_question_by_type($user_id = "", $asked = "=", $post_status = "publish")
	{
		$block_users = askme_options("block_users");
		$author__not_in = array();
		if ($block_users == 1) {
			if ($user_id > 0) {
				$get_block_users = get_user_meta($user_id, "askme_block_users", true);
				if (is_array($get_block_users) && !empty($get_block_users)) {
					$author__not_in = array("author__not_in" => $get_block_users);
				}
			}
		}
		$args = array(
			"post_type"     => ask_asked_questions_type,
			"post_status"   => $post_status,
			"comment_count" => array(
				"value"   => 0,
				"compare" => $asked,
			),
			"meta_query"    => array(
				array("key" => "user_id", "compare" => "=", "value" => $user_id),
			)
		);
		$the_query = new WP_Query(array_merge($author__not_in, $args));
		return $the_query->found_posts;
		wp_reset_postdata();
	}
	/* count_new_message */
	function count_new_message($user_id = "", $post_status = "publish")
	{
		global $wpdb;

		$count = $wpdb->get_var("SELECT COUNT(*) 
	FROM $wpdb->posts 
	
	LEFT JOIN $wpdb->postmeta AS mt1
	ON ($wpdb->posts.ID = mt1.post_id
	AND mt1.meta_key = 'delete_inbox_message' )
	
	LEFT JOIN $wpdb->postmeta AS mt2
	ON ($wpdb->posts.ID = mt2.post_id
	AND mt2.meta_key = 'message_user_id' )
	
	LEFT JOIN $wpdb->postmeta AS mt3
	ON ($wpdb->posts.ID = mt3.post_id
	AND mt3.meta_key = 'message_new' )
	
	WHERE 1=1 
	
	AND ( mt1.post_id IS NULL )
	AND ( mt2.meta_value = $user_id )
	AND ( mt3.meta_value = 1 )
	
	AND $wpdb->posts.post_type = 'message'
	AND $wpdb->posts.post_status = '$post_status'");

		return $count;
	}
	/* makeClickableLinks */
	function makeClickableLinks($text)
	{
		return make_clickable($text);
	}
	/* Get author group */
	function askme_get_user_group($user_info)
	{
		$user_group = (isset($user_info->roles) && is_array($user_info->roles) ? reset($user_info->roles) : (isset($user_info->caps) && is_array($user_info->caps) ? key($user_info->caps) : ""));
		return $user_group;
	}
	/* vpanel_get_countries */
	function vpanel_get_countries()
	{
		$countries = array(
			'AF' => __('Afghanistan', 'vbegy'),
			'AX' => __('Aland Islands', 'vbegy'),
			'AL' => __('Albania', 'vbegy'),
			'DZ' => __('Algeria', 'vbegy'),
			'AD' => __('Andorra', 'vbegy'),
			'AO' => __('Angola', 'vbegy'),
			'AI' => __('Anguilla', 'vbegy'),
			'AQ' => __('Antarctica', 'vbegy'),
			'AG' => __('Antigua and Barbuda', 'vbegy'),
			'AR' => __('Argentina', 'vbegy'),
			'AM' => __('Armenia', 'vbegy'),
			'AW' => __('Aruba', 'vbegy'),
			'AU' => __('Australia', 'vbegy'),
			'AT' => __('Austria', 'vbegy'),
			'AZ' => __('Azerbaijan', 'vbegy'),
			'BS' => __('Bahamas', 'vbegy'),
			'BH' => __('Bahrain', 'vbegy'),
			'BD' => __('Bangladesh', 'vbegy'),
			'BB' => __('Barbados', 'vbegy'),
			'BY' => __('Belarus', 'vbegy'),
			'BE' => __('Belgium', 'vbegy'),
			'PW' => __('Belau', 'vbegy'),
			'BZ' => __('Belize', 'vbegy'),
			'BJ' => __('Benin', 'vbegy'),
			'BM' => __('Bermuda', 'vbegy'),
			'BT' => __('Bhutan', 'vbegy'),
			'BO' => __('Bolivia', 'vbegy'),
			'BQ' => __('Bonaire, Saint Eustatius and Saba', 'vbegy'),
			'BA' => __('Bosnia and Herzegovina', 'vbegy'),
			'BW' => __('Botswana', 'vbegy'),
			'BV' => __('Bouvet Island', 'vbegy'),
			'BR' => __('Brazil', 'vbegy'),
			'IO' => __('British Indian Ocean Territory', 'vbegy'),
			'VG' => __('British Virgin Islands', 'vbegy'),
			'BN' => __('Brunei', 'vbegy'),
			'BG' => __('Bulgaria', 'vbegy'),
			'BF' => __('Burkina Faso', 'vbegy'),
			'BI' => __('Burundi', 'vbegy'),
			'KH' => __('Cambodia', 'vbegy'),
			'CM' => __('Cameroon', 'vbegy'),
			'CA' => __('Canada', 'vbegy'),
			'CV' => __('Cape Verde', 'vbegy'),
			'KY' => __('Cayman Islands', 'vbegy'),
			'CF' => __('Central African Republic', 'vbegy'),
			'TD' => __('Chad', 'vbegy'),
			'CL' => __('Chile', 'vbegy'),
			'CN' => __('China', 'vbegy'),
			'CX' => __('Christmas Island', 'vbegy'),
			'CC' => __('Cocos (Keeling) Islands', 'vbegy'),
			'CO' => __('Colombia', 'vbegy'),
			'KM' => __('Comoros', 'vbegy'),
			'CG' => __('Congo (Brazzaville)', 'vbegy'),
			'CD' => __('Congo (Kinshasa)', 'vbegy'),
			'CK' => __('Cook Islands', 'vbegy'),
			'CR' => __('Costa Rica', 'vbegy'),
			'HR' => __('Croatia', 'vbegy'),
			'CU' => __('Cuba', 'vbegy'),
			'CW' => __('Cura&Ccedil;ao', 'vbegy'),
			'CY' => __('Cyprus', 'vbegy'),
			'CZ' => __('Czech Republic', 'vbegy'),
			'DK' => __('Denmark', 'vbegy'),
			'DJ' => __('Djibouti', 'vbegy'),
			'DM' => __('Dominica', 'vbegy'),
			'DO' => __('Dominican Republic', 'vbegy'),
			'EC' => __('Ecuador', 'vbegy'),
			'EG' => __('Egypt', 'vbegy'),
			'SV' => __('El Salvador', 'vbegy'),
			'GQ' => __('Equatorial Guinea', 'vbegy'),
			'ER' => __('Eritrea', 'vbegy'),
			'EE' => __('Estonia', 'vbegy'),
			'ET' => __('Ethiopia', 'vbegy'),
			'FK' => __('Falkland Islands', 'vbegy'),
			'FO' => __('Faroe Islands', 'vbegy'),
			'FJ' => __('Fiji', 'vbegy'),
			'FI' => __('Finland', 'vbegy'),
			'FR' => __('France', 'vbegy'),
			'GF' => __('French Guiana', 'vbegy'),
			'PF' => __('French Polynesia', 'vbegy'),
			'TF' => __('French Southern Territories', 'vbegy'),
			'GA' => __('Gabon', 'vbegy'),
			'GM' => __('Gambia', 'vbegy'),
			'GE' => __('Georgia', 'vbegy'),
			'DE' => __('Germany', 'vbegy'),
			'GH' => __('Ghana', 'vbegy'),
			'GI' => __('Gibraltar', 'vbegy'),
			'GR' => __('Greece', 'vbegy'),
			'GL' => __('Greenland', 'vbegy'),
			'GD' => __('Grenada', 'vbegy'),
			'GP' => __('Guadeloupe', 'vbegy'),
			'GT' => __('Guatemala', 'vbegy'),
			'GG' => __('Guernsey', 'vbegy'),
			'GN' => __('Guinea', 'vbegy'),
			'GW' => __('Guinea-Bissau', 'vbegy'),
			'GY' => __('Guyana', 'vbegy'),
			'HT' => __('Haiti', 'vbegy'),
			'HM' => __('Heard Island and McDonald Islands', 'vbegy'),
			'HN' => __('Honduras', 'vbegy'),
			'HK' => __('Hong Kong', 'vbegy'),
			'HU' => __('Hungary', 'vbegy'),
			'IS' => __('Iceland', 'vbegy'),
			'IN' => __('India', 'vbegy'),
			'ID' => __('Indonesia', 'vbegy'),
			'IR' => __('Iran', 'vbegy'),
			'IQ' => __('Iraq', 'vbegy'),
			'IE' => __('Republic of Ireland', 'vbegy'),
			'IM' => __('Isle of Man', 'vbegy'),
			'IL' => __('Israel', 'vbegy'),
			'IT' => __('Italy', 'vbegy'),
			'CI' => __('Ivory Coast', 'vbegy'),
			'JM' => __('Jamaica', 'vbegy'),
			'JP' => __('Japan', 'vbegy'),
			'JE' => __('Jersey', 'vbegy'),
			'JO' => __('Jordan', 'vbegy'),
			'KZ' => __('Kazakhstan', 'vbegy'),
			'KE' => __('Kenya', 'vbegy'),
			'KI' => __('Kiribati', 'vbegy'),
			'KW' => __('Kuwait', 'vbegy'),
			'KG' => __('Kyrgyzstan', 'vbegy'),
			'LA' => __('Laos', 'vbegy'),
			'LV' => __('Latvia', 'vbegy'),
			'LB' => __('Lebanon', 'vbegy'),
			'LS' => __('Lesotho', 'vbegy'),
			'LR' => __('Liberia', 'vbegy'),
			'LY' => __('Libya', 'vbegy'),
			'LI' => __('Liechtenstein', 'vbegy'),
			'LT' => __('Lithuania', 'vbegy'),
			'LU' => __('Luxembourg', 'vbegy'),
			'MO' => __('Macao S.A.R., China', 'vbegy'),
			'MK' => __('Macedonia', 'vbegy'),
			'MG' => __('Madagascar', 'vbegy'),
			'MW' => __('Malawi', 'vbegy'),
			'MY' => __('Malaysia', 'vbegy'),
			'MV' => __('Maldives', 'vbegy'),
			'ML' => __('Mali', 'vbegy'),
			'MT' => __('Malta', 'vbegy'),
			'MH' => __('Marshall Islands', 'vbegy'),
			'MQ' => __('Martinique', 'vbegy'),
			'MR' => __('Mauritania', 'vbegy'),
			'MU' => __('Mauritius', 'vbegy'),
			'YT' => __('Mayotte', 'vbegy'),
			'MX' => __('Mexico', 'vbegy'),
			'FM' => __('Micronesia', 'vbegy'),
			'MD' => __('Moldova', 'vbegy'),
			'MC' => __('Monaco', 'vbegy'),
			'MN' => __('Mongolia', 'vbegy'),
			'ME' => __('Montenegro', 'vbegy'),
			'MS' => __('Montserrat', 'vbegy'),
			'MA' => __('Morocco', 'vbegy'),
			'MZ' => __('Mozambique', 'vbegy'),
			'MM' => __('Myanmar', 'vbegy'),
			'NA' => __('Namibia', 'vbegy'),
			'NR' => __('Nauru', 'vbegy'),
			'NP' => __('Nepal', 'vbegy'),
			'NL' => __('Netherlands', 'vbegy'),
			'AN' => __('Netherlands Antilles', 'vbegy'),
			'NC' => __('New Caledonia', 'vbegy'),
			'NZ' => __('New Zealand', 'vbegy'),
			'NI' => __('Nicaragua', 'vbegy'),
			'NE' => __('Niger', 'vbegy'),
			'NG' => __('Nigeria', 'vbegy'),
			'NU' => __('Niue', 'vbegy'),
			'NF' => __('Norfolk Island', 'vbegy'),
			'KP' => __('North Korea', 'vbegy'),
			'NO' => __('Norway', 'vbegy'),
			'OM' => __('Oman', 'vbegy'),
			'PK' => __('Pakistan', 'vbegy'),
			'PS' => __('Palestinian Territory', 'vbegy'),
			'PA' => __('Panama', 'vbegy'),
			'PG' => __('Papua New Guinea', 'vbegy'),
			'PY' => __('Paraguay', 'vbegy'),
			'PE' => __('Peru', 'vbegy'),
			'PH' => __('Philippines', 'vbegy'),
			'PN' => __('Pitcairn', 'vbegy'),
			'PL' => __('Poland', 'vbegy'),
			'PT' => __('Portugal', 'vbegy'),
			'QA' => __('Qatar', 'vbegy'),
			'RE' => __('Reunion', 'vbegy'),
			'RO' => __('Romania', 'vbegy'),
			'RU' => __('Russia', 'vbegy'),
			'RW' => __('Rwanda', 'vbegy'),
			'BL' => __('Saint Barth&eacute;lemy', 'vbegy'),
			'SH' => __('Saint Helena', 'vbegy'),
			'KN' => __('Saint Kitts and Nevis', 'vbegy'),
			'LC' => __('Saint Lucia', 'vbegy'),
			'MF' => __('Saint Martin (French part)', 'vbegy'),
			'SX' => __('Saint Martin (Dutch part)', 'vbegy'),
			'PM' => __('Saint Pierre and Miquelon', 'vbegy'),
			'VC' => __('Saint Vincent and the Grenadines', 'vbegy'),
			'SM' => __('San Marino', 'vbegy'),
			'ST' => __('S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'vbegy'),
			'SA' => __('Saudi Arabia', 'vbegy'),
			'SN' => __('Senegal', 'vbegy'),
			'RS' => __('Serbia', 'vbegy'),
			'SC' => __('Seychelles', 'vbegy'),
			'SL' => __('Sierra Leone', 'vbegy'),
			'SG' => __('Singapore', 'vbegy'),
			'SK' => __('Slovakia', 'vbegy'),
			'SI' => __('Slovenia', 'vbegy'),
			'SB' => __('Solomon Islands', 'vbegy'),
			'SO' => __('Somalia', 'vbegy'),
			'ZA' => __('South Africa', 'vbegy'),
			'GS' => __('South Georgia/Sandwich Islands', 'vbegy'),
			'KR' => __('South Korea', 'vbegy'),
			'SS' => __('South Sudan', 'vbegy'),
			'ES' => __('Spain', 'vbegy'),
			'LK' => __('Sri Lanka', 'vbegy'),
			'SD' => __('Sudan', 'vbegy'),
			'SR' => __('Suriname', 'vbegy'),
			'SJ' => __('Svalbard and Jan Mayen', 'vbegy'),
			'SZ' => __('Swaziland', 'vbegy'),
			'SE' => __('Sweden', 'vbegy'),
			'CH' => __('Switzerland', 'vbegy'),
			'SY' => __('Syria', 'vbegy'),
			'TW' => __('Taiwan', 'vbegy'),
			'TJ' => __('Tajikistan', 'vbegy'),
			'TZ' => __('Tanzania', 'vbegy'),
			'TH' => __('Thailand', 'vbegy'),
			'TL' => __('Timor-Leste', 'vbegy'),
			'TG' => __('Togo', 'vbegy'),
			'TK' => __('Tokelau', 'vbegy'),
			'TO' => __('Tonga', 'vbegy'),
			'TT' => __('Trinidad and Tobago', 'vbegy'),
			'TN' => __('Tunisia', 'vbegy'),
			'TR' => __('Turkey', 'vbegy'),
			'TM' => __('Turkmenistan', 'vbegy'),
			'TC' => __('Turks and Caicos Islands', 'vbegy'),
			'TV' => __('Tuvalu', 'vbegy'),
			'UG' => __('Uganda', 'vbegy'),
			'UA' => __('Ukraine', 'vbegy'),
			'AE' => __('United Arab Emirates', 'vbegy'),
			'GB' => __('United Kingdom (UK)', 'vbegy'),
			'US' => __('United States (US)', 'vbegy'),
			'UY' => __('Uruguay', 'vbegy'),
			'UZ' => __('Uzbekistan', 'vbegy'),
			'VU' => __('Vanuatu', 'vbegy'),
			'VA' => __('Vatican', 'vbegy'),
			'VE' => __('Venezuela', 'vbegy'),
			'VN' => __('Vietnam', 'vbegy'),
			'WF' => __('Wallis and Futuna', 'vbegy'),
			'EH' => __('Western Sahara', 'vbegy'),
			'WS' => __('Western Samoa', 'vbegy'),
			'YE' => __('Yemen', 'vbegy'),
			'ZM' => __('Zambia', 'vbegy'),
			'ZW' => __('Zimbabwe', 'vbegy')
		);
		asort($countries);
		return $countries;
	}
	/* Fetch options */
	function askme_parse_str($string)
	{
		if ('' == $string) {
			return false;
		}
		$result = array();
		$pairs  = explode('&', $string);
		foreach ($pairs as $key => $pair) {
			parse_str($pair, $params);
			$k = key($params);
			if (!isset($result[$k])) {
				$result += $params;
			} else {
				$result[$k] = askme_array_merge_distinct($result[$k], $params[$k]);
			}
		}

		return $result;
	}
	function askme_array_merge_distinct(array $array1, array $array2)
	{
		$merged = $array1;
		foreach ($array2 as $key => $value) {
			if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
				$merged[$key] = askme_array_merge_distinct($merged[$key], $value);
			} else if (is_numeric($key) && isset($merged[$key])) {
				$merged[] = $value;
			} else {
				$merged[$key] = $value;
			}
		}
		return $merged;
	}
	/* Update options */
	function askme_update_options()
	{
		$user_id = get_current_user_id();
		if (is_super_admin($user_id)) {
			$_POST['data'] = stripslashes($_POST['data']);
			$values = askme_parse_str($_POST['data']);
			if (!isset($values['saving_nonce']) || !wp_verify_nonce($values['saving_nonce'], 'saving_nonce')) {
				echo 3;
			} else {
				do_action("askme_update_options", $values);
				$setting_options = $values[askme_options];
				foreach ($setting_options as $key => $value) {
					if (isset($setting_options[$key]) && $setting_options[$key] == "on") {
						if (isset($setting_options["theme_pages"]) && $setting_options["theme_pages"] == "on") {
							if ($key == "theme_pages") {
								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('search', 'Page slug', 'vbegy'),
									'post_title'     => _x('Search', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$search_page = wp_insert_post($page_data);
								update_post_meta($search_page, '_wp_page_template', 'template-search.php');
								$setting_options["search_page"] = $search_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('add_post', 'Page slug', 'vbegy'),
									'post_title'     => _x('Add post', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$add_post = wp_insert_post($page_data);
								update_post_meta($add_post, '_wp_page_template', 'template-add_post.php');
								$setting_options["add_post_page"] = $add_post;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('add_question', 'Page slug', 'vbegy'),
									'post_title'     => _x('Add question', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$add_question = wp_insert_post($page_data);
								update_post_meta($add_question, '_wp_page_template', 'template-ask_question.php');
								$setting_options["add_question"] = $add_question;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('edit_question', 'Page slug', 'vbegy'),
									'post_title'     => _x('Edit question', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$edit_question = wp_insert_post($page_data);
								update_post_meta($edit_question, '_wp_page_template', 'template-edit_question.php');
								$setting_options["edit_question"] = $edit_question;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('login', 'Page slug', 'vbegy'),
									'post_title'     => _x('Login', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$login_register_page = wp_insert_post($page_data);
								update_post_meta($login_register_page, '_wp_page_template', 'template-login.php');
								$setting_options["login_register_page"] = $login_register_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('edit_profile', 'Page slug', 'vbegy'),
									'post_title'     => _x('Edit profile', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$user_edit_profile_page = wp_insert_post($page_data);
								update_post_meta($user_edit_profile_page, '_wp_page_template', 'template-edit_profile.php');
								$setting_options["user_edit_profile_page"] = $user_edit_profile_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('user_posts', 'Page slug', 'vbegy'),
									'post_title'     => _x('User posts', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$post_user_page = wp_insert_post($page_data);
								update_post_meta($post_user_page, '_wp_page_template', 'template-user_posts.php');
								$setting_options["post_user_page"] = $post_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('user_comments', 'Page slug', 'vbegy'),
									'post_title'     => _x('User comments', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$comment_user_page = wp_insert_post($page_data);
								update_post_meta($comment_user_page, '_wp_page_template', 'template-user_comments.php');
								$setting_options["comment_user_page"] = $comment_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('user_questions', 'Page slug', 'vbegy'),
									'post_title'     => _x('User questions', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$question_user_page = wp_insert_post($page_data);
								update_post_meta($question_user_page, '_wp_page_template', 'template-user_question.php');
								$setting_options["question_user_page"] = $question_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('user_polls', 'Page slug', 'vbegy'),
									'post_title'     => _x('User polls', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$polls_user_page = wp_insert_post($page_data);
								update_post_meta($polls_user_page, '_wp_page_template', 'template-user_polls.php');
								$setting_options["polls_user_page"] = $polls_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('user_asked_questions', 'Page slug', 'vbegy'),
									'post_title'     => _x('User asked questions', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$asked_question_user_page = wp_insert_post($page_data);
								update_post_meta($asked_question_user_page, '_wp_page_template', 'template-asked_question.php');
								$setting_options["asked_question_user_page"] = $asked_question_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('paid_questions', 'Page slug', 'vbegy'),
									'post_title'     => _x('Paid questions', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$paid_question_page = wp_insert_post($page_data);
								update_post_meta($paid_question_page, '_wp_page_template', 'template-user_paid_question.php');
								$setting_options["paid_question"] = $paid_question_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('user_best_answers', 'Page slug', 'vbegy'),
									'post_title'     => _x('User best answers', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$best_answer_user_page = wp_insert_post($page_data);
								update_post_meta($best_answer_user_page, '_wp_page_template', 'template-user_best_answer.php');
								$setting_options["best_answer_user_page"] = $best_answer_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('user_answers', 'Page slug', 'vbegy'),
									'post_title'     => _x('User answers', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$answer_user_page = wp_insert_post($page_data);
								update_post_meta($answer_user_page, '_wp_page_template', 'template-user_answer.php');
								$setting_options["answer_user_page"] = $answer_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('favorite_questions', 'Page slug', 'vbegy'),
									'post_title'     => _x('Favorite questions', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$favorite_user_page = wp_insert_post($page_data);
								update_post_meta($favorite_user_page, '_wp_page_template', 'template-user_favorite_questions.php');
								$setting_options["favorite_user_page"] = $favorite_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('followed_questions', 'Page slug', 'vbegy'),
									'post_title'     => _x('Followed questions', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$followed_user_page = wp_insert_post($page_data);
								update_post_meta($followed_user_page, '_wp_page_template', 'template-user_followed_questions.php');
								$setting_options["followed_user_page"] = $followed_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('user_points', 'Page slug', 'vbegy'),
									'post_title'     => _x('User points', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$point_user_page = wp_insert_post($page_data);
								update_post_meta($point_user_page, '_wp_page_template', 'template-user_points.php');
								$setting_options["point_user_page"] = $point_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('i_follow_users', 'Page slug', 'vbegy'),
									'post_title'     => _x('I follow users', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$i_follow_user_page = wp_insert_post($page_data);
								update_post_meta($i_follow_user_page, '_wp_page_template', 'template-i_follow.php');
								$setting_options["i_follow_user_page"] = $i_follow_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('blocking_users', 'Page slug', 'vbegy'),
									'post_title'     => _x('Blocking users', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$blocking_user_page = wp_insert_post($page_data);
								update_post_meta($blocking_user_page, '_wp_page_template', 'template-blocking.php');
								$setting_options["blocking_user_page"] = $blocking_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('followers_users', 'Page slug', 'vbegy'),
									'post_title'     => _x('Followers users', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$followers_user_page = wp_insert_post($page_data);
								update_post_meta($followers_user_page, '_wp_page_template', 'template-followers.php');
								$setting_options["followers_user_page"] = $followers_user_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('follow_questions', 'Page slug', 'vbegy'),
									'post_title'     => _x('Follow questions', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$follow_question_page = wp_insert_post($page_data);
								update_post_meta($follow_question_page, '_wp_page_template', 'template-question_follow.php');
								$setting_options["follow_question_page"] = $follow_question_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('follow_answers', 'Page slug', 'vbegy'),
									'post_title'     => _x('Follow answers', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$follow_answer_page = wp_insert_post($page_data);
								update_post_meta($follow_answer_page, '_wp_page_template', 'template-answer_follow.php');
								$setting_options["follow_answer_page"] = $follow_answer_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('follow_posts', 'Page slug', 'vbegy'),
									'post_title'     => _x('Follow posts', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$follow_post_page = wp_insert_post($page_data);
								update_post_meta($follow_post_page, '_wp_page_template', 'template-post_follow.php');
								$setting_options["follow_post_page"] = $follow_post_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('follow comments', 'Page slug', 'vbegy'),
									'post_title'     => _x('Follow comments', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$follow_comment_page = wp_insert_post($page_data);
								update_post_meta($follow_comment_page, '_wp_page_template', 'template-comment_follow.php');
								$setting_options["follow_comment_page"] = $follow_comment_page;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('edit_post', 'Page slug', 'vbegy'),
									'post_title'     => _x('Edit post', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$edit_post = wp_insert_post($page_data);
								update_post_meta($edit_post, '_wp_page_template', 'template-edit_post.php');
								$setting_options["edit_post"] = $edit_post;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('edit_comment', 'Page slug', 'vbegy'),
									'post_title'     => _x('Edit comment', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$edit_comment = wp_insert_post($page_data);
								update_post_meta($edit_comment, '_wp_page_template', 'template-edit_comment.php');
								$setting_options["edit_comment"] = $edit_comment;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('activity_log', 'Page slug', 'vbegy'),
									'post_title'     => _x('Activity log', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$activity_log = wp_insert_post($page_data);
								update_post_meta($activity_log, '_wp_page_template', 'template-activity_log.php');
								$setting_options["activity_log_page"] = $activity_log;

								$page_data = array(
									'post_status'    => 'publish',
									'post_type'      => 'page',
									'post_author'    => get_current_user_id(),
									'post_name'      => _x('notifications', 'Page slug', 'vbegy'),
									'post_title'     => _x('Notifications', 'Page title', 'vbegy'),
									'post_content'   => '',
									'post_parent'    => 0,
									'comment_status' => 'closed'
								);
								$notifications = wp_insert_post($page_data);
								update_post_meta($notifications, '_wp_page_template', 'template-notifications.php');
								$setting_options["notifications_page"] = $notifications;
								echo 3;
							}
						} else {
							$setting_options[$key] = 1;
						}
					} else {
						$setting_options[$key] = $value;
					}
				}
				unset($setting_options["theme_pages"]);
				/* Old emails */
				$mail_issue_fixed = get_option("askme_mail_issue_fixed");
				if ($mail_issue_fixed != "done" && (isset($setting_options['email_template']) && $setting_options['email_template'] != "") || (isset($setting_options['email_template_to']) && $setting_options['email_template_to'] != "")) {
					$parse = parse_url(get_site_url());
					$whitelist = array(
						'127.0.0.1',
						'::1'
					);
					if (in_array($_SERVER['REMOTE_ADDR'], $whitelist) || $parse['host'] == "intself.com") {
						$not_replace = true;
					}

					if (isset($setting_options['email_template']) && $setting_options['email_template'] != "" && !isset($not_replace)) {
						if (strpos($setting_options['email_template'], '@intself.com') !== false) {
							$setting_options['email_template'] = "no_reply@" . $parse['host'];
						}
					}
					if (isset($setting_options['email_template_to']) && $setting_options['email_template_to'] != "" && !isset($not_replace)) {
						if (strpos($setting_options['email_template_to'], '@intself.com') !== false || strpos($setting_options['email_template_to'], '2codethemes@') !== false || strpos($setting_options['email_template_to'], 'vbegy.info@') !== false) {
							$setting_options['email_template_to'] = get_bloginfo("admin_email");
						}
					}
					update_option("askme_mail_issue_fixed", "done");
				}
				update_option(askme_options, $setting_options);
				/* Badges */
				if (isset($values["badges"])) {
					update_option("badges", $values["badges"]);
				} else {
					delete_option("badges");
				}
				/* Coupons */
				if (isset($values["coupons"])) {
					update_option("coupons", $values["coupons"]);
				} else {
					delete_option("coupons");
				}
				/* Sidebars */
				if (isset($values["sidebars"])) {
					update_option("sidebars", $values["sidebars"]);
				} else {
					delete_option("sidebars");
				}
				$sidebars_widgets = get_option("sidebars_widgets");
				if (isset($values["sidebars"]) && is_array($values["sidebars"]) && !empty($values["sidebars"])) {
					foreach ($values["sidebars"] as $sidebar) {
						$key = array(sanitize_title(esc_html($sidebar["name"])) => array());
						if ((is_array($sidebars_widgets) && empty($sidebars_widgets)) || !is_array($sidebars_widgets) || $sidebars_widgets == "") {
							$sidebars_widgets = $key;
						} else if (is_array($sidebars_widgets) && !in_array($key, $sidebars_widgets)) {
							$sidebars_widgets = array_merge($sidebars_widgets, $key);
						}
					}
				}
				update_option("sidebars_widgets", $sidebars_widgets);
				$askme_registered_sidebars = array();
				foreach ($GLOBALS['wp_registered_sidebars'] as $sidebar) {
					$askme_registered_sidebars[$sidebar['id']] = $sidebar['name'];
				}
				update_option("askme_registered_sidebars", $askme_registered_sidebars);
				/* roles */
				global $wp_roles;
				$array = array("ask_question", "show_question", "show_post", "add_answer", "add_comment", "show_answer", "show_comment", "add_post", "send_message", "upload_files", "follow_question", "favorite_question");
				if (isset($values["roles"])) {
					$k = 0;
					foreach ($values["roles"] as $value_roles) {
						$k++;
						unset($wp_roles->roles[$value_roles["id"]]);
						add_role($value_roles["id"], $value_roles["group"], array('read' => false));
						$is_group = get_role($value_roles["id"]);
						foreach ($array as $value) {
							if (isset($value_roles[$value]) && $value_roles[$value] == "on") {
								$is_group->add_cap($value);
							} else {
								$is_group->remove_cap($value);
							}
						}
					}
					update_option("roles", $values["roles"]);
				} else {
					delete_option("roles");
				}
				/* roles_default */
				if (isset($values["roles_default"])) {
					update_option("roles_default", $values["roles_default"]);
					$old_roles = $wp_roles->roles;
					foreach ($old_roles as $key_r => $value_r) {
						$is_group = get_role($key_r);
						if (isset($values["roles_default"][$key_r]) && is_array($values["roles_default"][$key_r])) {
							$value_d = $values["roles_default"][$key_r];
							foreach ($array as $value) {
								if (isset($value_d[$value]) && $value_d[$value] == "on") {
									$is_group->add_cap($value);
								} else {
									$is_group->remove_cap($value);
								}
							}
						}
					}
				} else {
					delete_option("roles_default");
				}
				update_option("FlushRewriteRules", true);
			}
		}
		die(1);
	}
	add_action('wp_ajax_askme_update_options', 'askme_update_options');
	/* Import options */
	function askme_import_options()
	{
		$user_id = get_current_user_id();
		if (is_super_admin($user_id)) {
			$saving_nonce = (isset($_POST["saving_nonce"]) ? esc_html($_POST["saving_nonce"]) : "");
			if (!wp_verify_nonce($saving_nonce, 'saving_nonce')) {
				echo 3;
			} else {
				$values = $_POST['data'];
				if ($values != "") {
					$data = base64_decode($values);
					$data = json_decode($data, true);
					$array_options = array(askme_options, "coupons", "sidebars", "roles");
					delete_option("badges");
					foreach ($array_options as $option) {
						if (isset($data[$option])) {
							update_option($option, $data[$option]);
						} else {
							delete_option($option);
						}
					}
					echo 2;
					update_option("FlushRewriteRules", true);
					die();
				}
				update_option("FlushRewriteRules", true);
			}
		}
		die();
	}
	add_action('wp_ajax_askme_import_options', 'askme_import_options');
	/* Reset options */
	function askme_reset_options()
	{
		$user_id = get_current_user_id();
		if (is_super_admin($user_id)) {
			$saving_nonce = (isset($_POST["saving_nonce"]) ? esc_html($_POST["saving_nonce"]) : "");
			if (!wp_verify_nonce($saving_nonce, 'saving_nonce')) {
				echo 3;
			} else {
				$options = askme_optionsframework_options();
				foreach ($options as $option) {
					if (isset($option['id'])) {
						$option_std = $option['std'];
						$option_res[$option['id']] = $option['std'];
					}
				}
				update_option(askme_options, $option_res);
				update_option("FlushRewriteRules", true);
			}
		}
		die(1);
	}
	add_action('wp_ajax_askme_reset_options', 'askme_reset_options');
	/* delete_group */
	function delete_group()
	{
		$group_id = esc_attr($_POST["group_id"]);
		remove_role($group_id);
		die(1);
	}
	add_action('wp_ajax_delete_group', 'delete_group');
	add_action('wp_ajax_nopriv_delete_group', 'delete_group');
	/* vpanel_get_user_url */
	add_filter('author_link', 'ask_author_link', 10, 3);
	if (!function_exists('ask_author_link')) :
		function ask_author_link($link, $author_id)
		{
			global $wp_rewrite;
			$link = $wp_rewrite->get_author_permastruct();
			if (empty($link)) {
				$file = home_url('/');
				$link = $file . '?author=' . $author_id;
			} else {
				$user = get_userdata($author_id);
				//user_nicename - nickname
				if (isset($user->user_nicename)) {
					$link = str_replace('%author%', $user->user_nicename, $link);
					$link = home_url(user_trailingslashit($link));
				} else {
					return false;
				}
			}
			return $link;
		}
	endif;
	function vpanel_get_user_url($author_id, $author_nicename = '')
	{
		$auth_ID = (int) $author_id;
		return get_author_posts_url($auth_ID);
	}
	/* vpanel_get_badge */
	function vpanel_get_badge($author_id, $return = "")
	{
		$author_id = (int)$author_id;
		$badges_style = askme_options("badges_style");
		if ($badges_style == "by_groups") {
			if ($author_id > 0) {
				$badges_groups = askme_options("badges_groups");
				$badges_groups = (is_array($badges_groups) && !empty($badges_groups) ? $badges_groups : array());
				$user_info = get_userdata($author_id);
				$group_key = askme_get_user_group($user_info);
				if (isset($badges_groups) && is_array($badges_groups)) {
					global $wp_roles;
					$badges_groups = array_values($badges_groups);
					$badges_groups = (is_array($badges_groups) && !empty($badges_groups) ? $badges_groups : array());
					$found_key = array_search($group_key, array_column($badges_groups, 'badge_name'));
					$user_group = $wp_roles->roles[$group_key]["name"];
					return '<span class="badge-span" style="background-color: ' . $badges_groups[$found_key]["badge_color"] . '">' . $user_group . '</span>';
				}
			}
		} else if ($badges_style == "by_groups_points") {
			if ($author_id > 0) {
				$points = get_user_meta($author_id, "points", true);
				$badges_groups_points = askme_options("badges_groups_points");
				$badges_groups_points = (is_array($badges_groups_points) && !empty($badges_groups_points) ? $badges_groups_points : array());
				$points_badges = array_column($badges_groups_points, 'badge_points');
				if (is_array($points_badges) && !empty($points_badges) && is_array($badges_groups_points) && !empty($badges_groups_points)) {
					array_multisort($points_badges, SORT_ASC, $badges_groups_points);
				}
				$user_info = get_userdata($author_id);
				$group_key = askme_get_user_group($user_info);
				if (isset($badges_groups_points) && is_array($badges_groups_points)) {
					$badges_groups_points = array_values($badges_groups_points);
					$badges_groups_points = (is_array($badges_groups_points) && !empty($badges_groups_points) ? $badges_groups_points : array());
					foreach ($badges_groups_points as $badges_k => $badges_v) {
						if ($badges_v["badge_group"] == $group_key) {
							$badges_points[] = $badges_v;
						}
					}
					if (isset($badges_points) && is_array($badges_points)) {
						foreach ($badges_points as $key => $badge_point) {
							if ($points >= $badge_point["badge_points"]) {
								$last_key = $key;
							}
						}
					}
					if (isset($last_key)) {
						$badge_key = $last_key;
						if ($return == "color") {
							$badge_color = (isset($badges_points[$badge_key]["badge_color"]) ? $badges_points[$badge_key]["badge_color"] : "");
							return $badge_color;
						} else if ($return == "name") {
							$badge_name = (isset($badges_points[$badge_key]["badge_name"]) ? $badges_points[$badge_key]["badge_name"] : "");
							return $badge_name;
						} else {
							return '<span class="badge-span"' . (isset($badges_points[$badge_key]["badge_color"]) ? ' style="background-color: ' . $badges_points[$badge_key]["badge_color"] . '"' : "") . '>' . (isset($badges_points[$badge_key]["badge_name"]) ? $badges_points[$badge_key]["badge_name"] : "") . '</span>';
						}
					}
				}
			}
		} else if ($badges_style == "by_questions") {
			if ($author_id > 0) {
				$questions = askme_count_posts_by_user($author_id, array(ask_questions_type, ask_asked_questions_type), "publish");
				$badges = askme_options("badges_questions");
				$badges = (is_array($badges) && !empty($badges) ? $badges : array());
				if (is_array($badges) && !empty($badges)) {
					$questions_badges = array_column($badges, 'badge_questions');
					if (is_array($questions_badges) && !empty($questions_badges) && is_array($badges) && !empty($badges)) {
						array_multisort($questions_badges, SORT_ASC, $badges);
					}
					if (isset($badges) && is_array($badges)) {
						foreach ($badges as $badges_k => $badges_v) {
							$badges_questions[] = $badges_v["badge_questions"];
						}
						if (isset($badges_questions) && is_array($badges_questions)) {
							foreach ($badges_questions as $key => $badge_question) {
								if ($questions >= $badge_question) {
									$last_key = $key;
								}
							}
						}
						if (isset($last_key)) {
							$key = $last_key;
							if ($return == "color") {
								$badge_color = $badges[$key]["badge_color"];
								return $badge_color;
							} else if ($return == "name") {
								$badge_name = $badges[$key]["badge_name"];
								return $badge_name;
							} else {
								return '<span class="badge-span" style="background-color: ' . $badges[$key]["badge_color"] . '">' . $badges[$key]["badge_name"] . '</span>';
							}
						}
					}
				}
			}
		} else if ($badges_style == "by_answers") {
			if ($author_id > 0) {
				$answers = get_user_meta($author_id, "add_answer_all", true);
				$badges = askme_options("badges_answers");
				$badges = (is_array($badges) && !empty($badges) ? $badges : array());
				if (is_array($badges) && !empty($badges)) {
					$answers_badges = array_column($badges, 'badge_answers');
					if (is_array($answers_badges) && !empty($answers_badges) && is_array($badges) && !empty($badges)) {
						array_multisort($answers_badges, SORT_ASC, $badges);
					}
					if (isset($badges) && is_array($badges)) {
						foreach ($badges as $badges_k => $badges_v) {
							$badges_answers[] = $badges_v["badge_answers"];
						}
						if (isset($badges_answers) && is_array($badges_answers)) {
							foreach ($badges_answers as $key => $badge_answer) {
								if ($answers >= $badge_answer) {
									$last_key = $key;
								}
							}
						}
						if (isset($last_key)) {
							$key = $last_key;
							if ($return == "color") {
								$badge_color = $badges[$key]["badge_color"];
								return $badge_color;
							} else if ($return == "name") {
								$badge_name = $badges[$key]["badge_name"];
								return $badge_name;
							} else {
								return '<span class="badge-span" style="background-color: ' . $badges[$key]["badge_color"] . '">' . $badges[$key]["badge_name"] . '</span>';
							}
						}
					}
				}
			}
		} else {
			if ($author_id > 0) {
				$points = get_user_meta($author_id, "points", true);
				$badges = askme_options("badges");
				$badges = (is_array($badges) && !empty($badges) ? $badges : array());
				if (is_array($badges) && !empty($badges)) {
					$points_badges = array_column($badges, 'badge_points');
					if (is_array($points_badges) && !empty($points_badges) && is_array($badges) && !empty($badges)) {
						array_multisort($points_badges, SORT_ASC, $badges);
					}
					if (isset($badges) && is_array($badges)) {
						foreach ($badges as $badges_k => $badges_v) {
							$badges_points[] = $badges_v["badge_points"];
						}
						if (isset($badges_points) && is_array($badges_points)) {
							foreach ($badges_points as $key => $badge_point) {
								if ($points >= $badge_point) {
									$last_key = $key;
								}
							}
						}
						if (isset($last_key)) {
							$key = $last_key;
							if ($return == "color") {
								$badge_color = $badges[$key]["badge_color"];
								return $badge_color;
							} else if ($return == "name") {
								$badge_name = $badges[$key]["badge_name"];
								return $badge_name;
							} else {
								return '<span class="badge-span" style="background-color: ' . $badges[$key]["badge_color"] . '">' . $badges[$key]["badge_name"] . '</span>';
							}
						}
					}
				}
			}
		}
	}
	/* vpanel_sidebars */
	if (!is_admin()) {
		function vpanel_sidebars($return = 'sidebar_dir')
		{
			global $post;
			$sidebar_layout = $sidebar_class = "";
			$sidebar_width  = askme_options("sidebar_width");
			$sidebar_width  = (isset($sidebar_width) && $sidebar_width != "" ? $sidebar_width : "col-md-3");
			if (isset($sidebar_width) && $sidebar_width == "col-md-3") {
				$container_span = "col-md-9";
			} else {
				$container_span = "col-md-8";
			}
			$full_span       = "col-md-12";
			$page_right      = "page-right-sidebar";
			$page_left       = "page-left-sidebar";
			$page_full_width = "page-full-width";

			if (is_category()) {
				$tax_id = get_query_var('cat');
			} else if (is_tax("product_cat")) {
				$tax_id = get_term_by('slug', get_query_var('term'), "product_cat");
				$tax_id = $tax_id->term_id;
			} else if (is_tax(ask_question_category)) {
				$tax_id = get_term_by('slug', get_query_var('term'), ask_question_category);
				$tax_id = $tax_id->term_id;
			} else if (is_tax(ask_question_tags)) {
				$tax_id = get_term_by('slug', get_query_var('term'), ask_question_tags);
				$tax_id = $tax_id->term_id;
			}

			if (is_author()) {
				$author_sidebar_layout = askme_options('author_sidebar_layout');
			} else if (is_category() || is_tax("product_cat") || is_tax(ask_question_category) || is_tax(ask_question_tags)) {
				$cat_sidebar_layout = get_term_meta($tax_id, "vbegy_cat_sidebar_layout", true);
				$cat_sidebar_layout = ($cat_sidebar_layout != "" ? $cat_sidebar_layout : "default");
				if ($cat_sidebar_layout == "default") {
					if (is_tax("product_cat")) {
						$cat_sidebar_layout = askme_options("products_sidebar_layout");
					} else if (is_tax(ask_question_category) || is_tax(ask_question_tags)) {
						$cat_sidebar_layout = askme_options("questions_sidebar_layout");
					}
				}
			} else if (is_single() || is_page()) {
				$sidebar_post = askme_post_meta('vbegy_sidebar', 'radio', $post->ID);
				if ($sidebar_post == "" || $sidebar_post == "default") {
					$sidebar_post = askme_options("sidebar_layout");
				}
			} else {
				$sidebar_layout = askme_options('sidebar_layout');
			}

			if (is_author()) {
				if ($author_sidebar_layout == "" || $author_sidebar_layout == "default") {
					$author_sidebar_layout = askme_options("sidebar_layout");
				}
				if ($author_sidebar_layout == 'left') {
					$sidebar_dir = $page_left;
					$homepage_content_span = $container_span;
				} elseif ($author_sidebar_layout == 'full') {
					$sidebar_dir = $page_full_width;
					$homepage_content_span = $full_span;
				} else {
					$sidebar_dir = $page_right;
					$homepage_content_span = $container_span;
				}
			} else if (is_category() || is_tax("product_cat") || is_tax(ask_question_category) || is_tax(ask_question_tags)) {
				if ($cat_sidebar_layout == "" || $cat_sidebar_layout == "default") {
					$cat_sidebar_layout = askme_options("sidebar_layout");
				}
				if ($cat_sidebar_layout == 'left') {
					$sidebar_dir = $page_left;
					$homepage_content_span = $container_span;
				} elseif ($cat_sidebar_layout == 'full') {
					$sidebar_dir = $page_full_width;
					$homepage_content_span = $full_span;
				} else {
					$sidebar_dir = $page_right;
					$homepage_content_span = $container_span;
				}
			} else if (is_tax("product_tag") || is_post_type_archive("product")) {
				$products_layout = askme_options("products_sidebar_layout");
				if ($products_layout == 'left') {
					$sidebar_dir = $page_left;
					$homepage_content_span = $container_span;
				} elseif ($products_layout == 'full') {
					$sidebar_dir = $page_full_width;
					$homepage_content_span = $full_span;
				} else {
					$sidebar_dir = $page_right;
					$homepage_content_span = $container_span;
				}
			} else if (is_tax(ask_question_tags) || is_post_type_archive(ask_questions_type)) {
				$questions_layout = askme_options("questions_sidebar_layout");
				if ($questions_layout == 'left') {
					$sidebar_dir = $page_left;
					$homepage_content_span = $container_span;
				} elseif ($questions_layout == 'full') {
					$sidebar_dir = $page_full_width;
					$homepage_content_span = $full_span;
				} else {
					$sidebar_dir = $page_right;
					$homepage_content_span = $container_span;
				}
			} else if (is_single() || is_page()) {
				$sidebar_post = askme_post_meta('vbegy_sidebar', 'radio', $post->ID);
				$sidebar_dir = '';
				if (isset($sidebar_post) && $sidebar_post != "default" && $sidebar_post != "") {
					if ($sidebar_post == 'left') {
						$sidebar_dir = 'page-left-sidebar';
						$homepage_content_span = $container_span;
					} elseif ($sidebar_post == 'full') {
						$sidebar_dir = 'page-full-width';
						$homepage_content_span = $full_span;
					} else {
						$sidebar_dir = 'page-right-sidebar';
						$homepage_content_span = $container_span;
					}
				} else {
					$sidebar_layout_q = askme_options('questions_sidebar_layout');
					$sidebar_layout_p = askme_options('products_sidebar_layout');
					if ((is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) && $sidebar_layout_q != "default") {
						if ($sidebar_layout_q == 'left') {
							$sidebar_dir = 'page-left-sidebar';
							$homepage_content_span = $container_span;
						} elseif ($sidebar_layout_q == 'full') {
							$sidebar_dir = 'page-full-width';
							$homepage_content_span = $full_span;
						} else {
							$sidebar_dir = 'page-right-sidebar';
							$homepage_content_span = $container_span;
						}
					} else if (is_singular("product") && $sidebar_layout_p != "default") {
						if ($sidebar_layout_p == 'left') {
							$sidebar_dir = 'page-left-sidebar';
							$homepage_content_span = $container_span;
						} elseif ($sidebar_layout_p == 'full') {
							$sidebar_dir = 'page-full-width';
							$homepage_content_span = $full_span;
						} else {
							$sidebar_dir = 'page-right-sidebar';
							$homepage_content_span = $container_span;
						}
					} else {
						$sidebar_layout = askme_options('sidebar_layout');
						if ($sidebar_layout == 'left') {
							$sidebar_dir = 'page-left-sidebar';
							$homepage_content_span = $container_span;
						} elseif ($sidebar_layout == 'full') {
							$sidebar_dir = 'page-full-width';
							$homepage_content_span = $full_span;
						} else {
							$sidebar_dir = 'page-right-sidebar';
							$homepage_content_span = $container_span;
						}
					}
				}
			} else {
				if ((is_single() || is_page()) && $sidebar_post != "default" && $sidebar_post != "") {
					if ($sidebar_post == 'left') {
						$sidebar_dir = 'page-left-sidebar';
						$homepage_content_span = $container_span;
					} elseif ($sidebar_post == 'full') {
						$sidebar_dir = 'page-full-width';
						$homepage_content_span = $full_span;
					} else {
						$sidebar_dir = 'page-right-sidebar';
						$homepage_content_span = $container_span;
					}
				} else {
					if ((is_singular("product") && $sidebar_layout_p != "default" && $sidebar_layout_p != "")) {
						$sidebar_layout_p = askme_options('products_sidebar_layout');
						if ($sidebar_layout_p == 'left') {
							$sidebar_dir = 'page-left-sidebar';
							$homepage_content_span = $container_span;
						} elseif ($sidebar_layout_p == 'full') {
							$sidebar_dir = 'page-full-width';
							$homepage_content_span = $full_span;
						} else {
							$sidebar_dir = 'page-right-sidebar';
							$homepage_content_span = $container_span;
						}
					} else if (((is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) && $sidebar_layout_q != "default" && $sidebar_layout_q != "")) {
						$sidebar_layout_q = askme_options('questions_sidebar_layout');
						if ($sidebar_layout_q == 'left') {
							$sidebar_dir = 'page-left-sidebar';
							$homepage_content_span = $container_span;
						} elseif ($sidebar_layout_q == 'full') {
							$sidebar_dir = 'page-full-width';
							$homepage_content_span = $full_span;
						} else {
							$sidebar_dir = 'page-right-sidebar';
							$homepage_content_span = $container_span;
						}
					} else {
						$sidebar_layout = askme_options('sidebar_layout');
						if ($sidebar_layout == 'left') {
							$sidebar_dir = 'page-left-sidebar';
							$homepage_content_span = $container_span;
						} elseif ($sidebar_layout == 'full') {
							$sidebar_dir = 'page-full-width';
							$homepage_content_span = $full_span;
						} else {
							$sidebar_dir = 'page-right-sidebar';
							$homepage_content_span = $container_span;
						}
					}
				}
			}
			$sidebar_dir = apply_filters("askme_sidebar_dir", $sidebar_dir);

			if ($return == "sidebar_dir") {
				return $sidebar_dir;
			} else if ($return == "sidebar_class") {
				return $sidebar_class;
			} else if ($return == "sidebar_where") {
				if ($sidebar_dir == $page_full_width) {
					$sidebar_where = 'full';
				} else {
					$sidebar_where = 'sidebar';
				}
				$sidebar_where = apply_filters("askme_sidebar_where", $sidebar_where);
				return $sidebar_where;
			} else {
				return $homepage_content_span;
			}
		}
	}
	/* askme_notifications */
	function askme_notifications_activities($user_id = "", $another_user_id = "", $username = "", $post_id = "", $comment_id = "", $text = "", $type = "notifications", $more_text = "", $type_of_item = "")
	{
		$active_notifications = askme_options("active_notifications");
		$active_activity_log = askme_options("active_activity_log");
		if (($type == "notifications" && $active_notifications == 1) || ($type == "activities" && $active_activity_log == 1)) {
			/* Number of my types */
			$_types = get_user_meta($user_id, $user_id . "_" . $type, true);
			if ($_types == "") {
				$_types = 0;
			}
			$_types++;
			update_user_meta($user_id, $user_id . "_" . $type, $_types);

			$array = array(
				"date_years"      => date_i18n('Y/m/d', current_time('timestamp')),
				"date_hours"      => date_i18n('g:i a', current_time('timestamp')),
				"time"            => current_time('timestamp'),
				"user_id"         => $user_id,
				"another_user_id" => $another_user_id,
				"post_id"         => $post_id,
				"comment_id"      => $comment_id,
				"text"            => $text,
				"username"        => $username,
				"more_text"       => $more_text,
				"type_of_item"    => $type_of_item,
				"number_of_item"  => $_types,
				"new"             => 1
			);

			add_user_meta($user_id, $user_id . "_" . $type . "_" . $_types, $array);

			/* New */
			$_new_types = get_user_meta($user_id, $user_id . "_new_" . $type, true);
			if ($_new_types == "") {
				$_new_types = 0;
			}
			$_new_types++;
			$update = update_user_meta($user_id, $user_id . '_new_' . $type, $_new_types);
			do_action("askme_action_notifications_activities", $_types, $user_id, $another_user_id, $username, $post_id, $comment_id, $text, $type, $more_text, $type_of_item, true, $array);
		}
	}
	/* Get notification and activity result */
	function askme_notification_activity_result($post, $type = "notification", $admin = "")
	{
		$post =
			$another_user_id = $array["another_user_id"];
		$username = $array["username"];
		$post_id = $array["post_id"];
		$comment_id = $array["comment_id"];
		$more_text = $array["more_text"];
		$type_of_item = $array["type_of_item"];
		$new = $array["new"];

		$type_result = array();
		$type_result["text"] = $post->post_title;
		$type_result["user_id"] = $post->post_author;
		$date_format = askme_options("date_format");
		$date_format = ($date_format ? $date_format : get_option("date_format"));
		$time_format = askme_options("time_format");
		$time_format = ($time_format ? $time_format : get_option("time_format"));
		$type_result["time"] = sprintf(esc_html__('%1$s at %2$s', 'vbegy'), get_the_time($date_format, $post->ID), get_the_time($time_format, $post->ID));
		$type_result["another_user_id"] = $another_user_id;
		if ($username != "") {
			$type_result["username"] = $username;
		}
		if ($post_id != "") {
			$type_result["post_id"] = $post_id;
		}
		if ($comment_id != "") {
			$type_result["comment_id"] = $comment_id;
		}
		if ($more_text != "") {
			$type_result["more_text"] = $more_text;
		}
		if ($type_of_item != "") {
			$type_result["type_of_item"] = $type_of_item;
		}
		if ($new != "") {
			$type_result["new"] = $new;
		}
		if ($admin != "admin" && $type == "notification" && is_page_template("template-notifications.php")) {
			$num = get_user_meta($user_id, $user_id . '_new_notifications', true);
			$num--;
			update_user_meta($user_id, $user_id . '_new_notifications', ($num > 0 ? $num : 0));
		}
		return $type_result;
	}
	/* delete_question_post */
	function delete_question_post()
	{
		$data_id = (int)$_POST["data_id"];
		$data_div = esc_attr($_POST["data_div"]);
		$get_post = get_post($data_id);
		$post_author = $get_post->post_author;
		$post_type = $get_post->post_type;
		$anonymously_user = get_post_meta($data_id, "anonymously_user", true);
		if ($post_author > 0 || $anonymously_user > 0) {
			askme_notifications_activities(($post_author > 0 ? $post_author : $anonymously_user), "", "", "", "", "delete_" . ($post_type == ask_questions_type || $post_type == ask_asked_questions_type ? ask_questions_type : "post"), "notifications", $data_div, ($post_type == ask_questions_type || $post_type == ask_asked_questions_type ? ask_questions_type : ""));
		}
		wp_delete_post($data_id, true);
		die(1);
	}
	add_action('wp_ajax_delete_question_post', 'delete_question_post');
	add_action('wp_ajax_nopriv_delete_question_post', 'delete_question_post');
	/* delete_comment_answer */
	function delete_comment_answer()
	{
		$data_id = (int)$_POST["data_id"];
		$data_div = esc_attr($_POST["data_div"]);
		$comment_type = get_comment_meta($data_id, 'comment_type', "question", true);
		$get_comment = get_comment($data_id);
		$anonymously_user = get_comment_meta($data_id, 'anonymously_user', true);
		if ($get_comment->user_id > 0 || $anonymously_user > 0) {
			askme_notifications_activities(($get_comment->user_id > 0 ? $get_comment->user_id : $anonymously_user), "", "", "", "", "delete_" . ($comment_type == "question" ? "answer" : "comment"), "notifications", $data_div, ($comment_type == "question" ? "answer" : "comment"));
		}
		wp_delete_comment($data_id, true);
		die();
	}
	add_action('wp_ajax_delete_comment_answer', 'delete_comment_answer');
	add_action('wp_ajax_nopriv_delete_comment_answer', 'delete_comment_answer');
	/* HTML tags */
	if (!function_exists('ask_html_tags')) :
		function ask_html_tags($p_active = "")
		{
			global $allowedposttags, $allowedtags;
			$allowedtags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
			$allowedposttags['img'] = array('alt' => true, 'class' => true, 'id' => true, 'title' => true, 'src' => true);
			$allowedtags['a'] = array('href' => true, 'title' => true, 'target' => true);
			$allowedposttags['a'] = array('href' => true, 'title' => true, 'target' => true);
			$allowedtags['br'] = array();
			$allowedposttags['br'] = array();
			if ($p_active == "yes") {
				$allowedtags['p'] = array();
				$allowedposttags['p'] = array();
			}
		}
	endif;
	add_action('init', 'ask_html_tags', 10);
	/* Kses stip */
	if (!function_exists('ask_kses_stip')) :
		function ask_kses_stip($value, $ireplace = "", $p_active = "")
		{
			return wp_kses(stripslashes(($ireplace == "yes" ? str_ireplace(array("<br />", "<br>", "<br/>", "</p>"), "\r\n", $value) : $value)), ask_html_tags(($p_active == "yes" ? $p_active : "")));
		}
	endif;
	/* Kses stip wpautop */
	if (!function_exists('ask_kses_stip_wpautop')) :
		function ask_kses_stip_wpautop($value, $ireplace = "", $p_active = "")
		{
			return wpautop(wp_kses(stripslashes((($ireplace == "yes" ? str_ireplace(array("<br />", "<br>", "<br/>", "</p>"), "\r\n", $value) : $value))), ask_html_tags(($p_active == "yes" ? $p_active : ""))));
		}
	endif;
	/* Notifications */
	function askme_count_new_notifications($user_id)
	{
		$num = get_user_meta($user_id, $user_id . '_new_notifications', true);
		$num = ($num == "" ? 0 : $num);
		return $num;
	}
	function askme_notifications($user_id)
	{
		$notifications_number = askme_options("notifications_number");
		$_notifications = get_user_meta($user_id, $user_id . "_notifications", true);

		for ($notifications = 1; $notifications <= $_notifications; $notifications++) {
			$notification_one[] = get_user_meta($user_id, $user_id . "_notifications_" . $notifications);
		}
		if (isset($notification_one) and is_array($notification_one)) {
			$notification = array_reverse($notification_one);
			$end = (sizeof($notification) < $notifications_number) ? sizeof($notification) : $notifications_number;
			echo "<div><ul>";
			for ($i = 0; $i < $end; ++$i) {
				$notification_result = $notification[$i][0];
				if (isset($notification_result["text"])) {
					echo "<li>";
					if (!empty($notification_result["another_user_id"])) {
						$vpanel_get_user_url = vpanel_get_user_url($notification_result["another_user_id"]);
						$display_name = get_the_author_meta('display_name', $notification_result["another_user_id"]);
					}

					if ((($notification_result["text"] == "add_question_user" || $notification_result["text"] == "add_question" || $notification_result["text"] == "add_post" || $notification_result["text"] == "poll_question") && empty($notification_result["username"]) && isset($notification_result["another_user_id"]) && $notification_result["another_user_id"] == 0) || (!empty($notification_result["another_user_id"]) || !empty($notification_result["username"])) && $notification_result["text"] != "admin_add_points" && $notification_result["text"] != "admin_remove_points") {

						if ((($notification_result["text"] == "add_question_user" || $notification_result["text"] == "add_question" || $notification_result["text"] == "add_post" || $notification_result["text"] == "poll_question") && isset($notification_result["another_user_id"]) && $notification_result["another_user_id"] == 0) || (isset($display_name) && $display_name != "")) {
							if (!empty($notification_result["another_user_id"])) { ?>
        <a href="<?php echo esc_url($vpanel_get_user_url) ?>"><?php echo esc_html($display_name); ?></a>
        <?php }
							if (!empty($notification_result["username"])) {
								echo esc_attr($notification_result["username"]) . " ";
							}
							if (($notification_result["text"] == "add_question_user" || $notification_result["text"] == "add_question" || $notification_result["text"] == "add_post") && empty($notification_result["username"]) && isset($notification_result["another_user_id"]) && $notification_result["another_user_id"] == 0) {
								echo esc_html__("Anonymous", "vbegy") . " ";
							}
							if (($notification_result["text"] == "poll_question") && empty($notification_result["username"]) && isset($notification_result["another_user_id"]) && $notification_result["another_user_id"] == 0) {
								echo esc_html__("A non-registered user", "vbegy") . " ";
							}
							esc_html_e("has", "vbegy");
						} else if (!empty($notification_result["username"])) {
							echo esc_attr($notification_result["username"]) . " ";
						} else {
							echo esc_html__("Deleted user", "vbegy") . " -";
						}
					}

					if (!empty($notification_result["post_id"])) {
						$get_the_permalink = get_the_permalink($notification_result["post_id"]);
						$get_post_status = get_post_status($notification_result["post_id"]);
					}
					if (!empty($notification_result["comment_id"])) {
						$get_comment = get_comment($notification_result["comment_id"]);
					}
					if (!empty($notification_result["post_id"]) && !empty($notification_result["comment_id"]) && $get_post_status != "trash" && isset($get_comment) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") { ?>
        <a
            href="<?php echo esc_url($get_the_permalink . (isset($notification_result["comment_id"]) ? "#comment-" . $notification_result["comment_id"] : "")) ?>">
            <?php }
					if (!empty($notification_result["post_id"]) && empty($notification_result["comment_id"]) && $get_post_status != "trash" && isset($get_the_permalink) && $get_the_permalink != "") { ?>
            <a href="<?php echo esc_url($get_the_permalink) ?>">
                <?php }
						echo " ";
						if ($notification_result["text"] == "add_question_user") {
							_e("been asked you a question.", "vbegy");
						} else if ($notification_result["text"] == "poll_question") {
							_e("polled at your question", "vbegy");
						} else if ($notification_result["text"] == "gift_site") {
							_e("Gift of the site.", "vbegy");
						} else if ($notification_result["text"] == "admin_add_points") {
							_e("The administrator added points for you.", "vbegy");
						} else if ($notification_result["text"] == "admin_remove_points") {
							_e("The administrator removed points from you.", "vbegy");
						} else if ($notification_result["text"] == "question_vote_up") {
							_e("voted up your question.", "vbegy");
						} else if ($notification_result["text"] == "question_vote_down") {
							_e("voted down your question.", "vbegy");
						} else if ($notification_result["text"] == "answer_vote_up") {
							_e("voted up you answer.", "vbegy");
						} else if ($notification_result["text"] == "answer_vote_down") {
							_e("voted down you answer.", "vbegy");
						} else if ($notification_result["text"] == "user_follow") {
							_e("followed you.", "vbegy");
						} else if ($notification_result["text"] == "user_unfollow") {
							_e("unfollowed you.", "vbegy");
						} else if ($notification_result["text"] == "point_back") {
							_e("Your point back because the best answer selected.", "vbegy");
						} else if ($notification_result["text"] == "select_best_answer") {
							_e("chosen your answer best answer.", "vbegy");
						} else if ($notification_result["text"] == "point_removed") {
							_e("Your point removed because the best answer removed.", "vbegy");
						} else if ($notification_result["text"] == "cancel_best_answer") {
							_e("canceled your answer best answer.", "vbegy");
						} else if ($notification_result["text"] == "answer_asked_question") {
							_e("answered at your asked question.", "vbegy");
						} else if ($notification_result["text"] == "answer_question") {
							_e("answered your question.", "vbegy");
						} else if ($notification_result["text"] == "answer_question_follow") {
							_e("answered your question you follow.", "vbegy");
						} else if ($notification_result["text"] == "reply_answer") {
							_e("replied to your answer.", "vbegy");
						} else if ($notification_result["text"] == "add_question") {
							_e("added a new question.", "vbegy");
						} else if ($notification_result["text"] == "add_post") {
							_e("added a new post.", "vbegy");
						} else if ($notification_result["text"] == "question_favorites") {
							_e("added your question at favorites.", "vbegy");
						} else if ($notification_result["text"] == "question_remove_favorites") {
							_e("removed your question from favorites.", "vbegy");
						} else if ($notification_result["text"] == "follow_question") {
							_e("followed your question.", "vbegy");
						} else if ($notification_result["text"] == "unfollow_question") {
							_e("unfollowed your question.", "vbegy");
						} else if ($notification_result["text"] == "approved_answer") {
							_e("The administrator added your answer.", "vbegy");
						} else if ($notification_result["text"] == "approved_comment") {
							_e("The administrator added your comment.", "vbegy");
						} else if ($notification_result["text"] == "approved_question") {
							_e("The administrator added your question.", "vbegy");
						} else if ($notification_result["text"] == "approved_post") {
							_e("The administrator added your post.", "vbegy");
						} else if ($notification_result["text"] == "add_message_user") {
							echo "<a href='" . esc_url(get_page_link(askme_options('messages_page'))) . "'>" . __("sent a message for you.", "vbegy") . "</a>";
						} else if ($notification_result["text"] == "seen_message") {
							_e("seen your message.", "vbegy");
						} else if ($notification_result["text"] == "action_comment") {
							echo sprintf(__("The administrator %s your %s.", "vbegy"), $notification_result["more_text"], (isset($notification_result["type_of_item"]) && $notification_result["type_of_item"] == "answer" ? __("answer", "vbegy") : __("comment", "vbegy")));
						} else if ($notification_result["text"] == "action_post") {
							echo sprintf(__("The administrator %s your %s.", "vbegy"), $notification_result["more_text"], (isset($notification_result["type_of_item"]) && ($notification_result["type_of_item"] == ask_questions_type || $notification_result["type_of_item"] == ask_asked_questions_type) ? __("question", "vbegy") : __("post", "vbegy")));
						} else if ($notification_result["text"] == "delete_reason") {
							echo sprintf(__("The administrator reason : %s.", "vbegy"), $notification_result["more_text"]);
						} else if ($notification_result["text"] == "delete_question" || $notification_result["text"] == "delete_post") {
							echo sprintf(__("The administrator deleted your %s.", "vbegy"), (isset($notification_result["type_of_item"]) && ($notification_result["type_of_item"] == ask_questions_type || $notification_result["type_of_item"] == ask_asked_questions_type) ? __("question", "vbegy") : __("post", "vbegy")));
						} else if ($notification_result["text"] == "delete_answer" || $notification_result["text"] == "delete_comment") {
							echo sprintf(__("The administrator deleted your %s.", "vbegy"), (isset($notification_result["type_of_item"]) && $notification_result["type_of_item"] == "answer" ? __("answer", "vbegy") : __("comment", "vbegy")));
						}
						if ((!empty($notification_result["post_id"]) && !empty($notification_result["comment_id"]) && $get_post_status != "trash" && isset($get_comment) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") || (!empty($notification_result["post_id"]) && empty($notification_result["comment_id"]) && $get_post_status != "trash" && isset($get_the_permalink) && $get_the_permalink != "")) { ?>
            </a>
            <?php }
						if (!empty($notification_result["post_id"]) && !empty($notification_result["comment_id"])) {
							if (isset($get_comment) && $get_comment->comment_approved == "spam") {
								echo " " . __('( Spam )', 'vbegy');
							} else if ($get_post_status == "trash" || (isset($get_comment) && $get_comment->comment_approved == "trash")) {
								echo " " . __('( Trashed )', 'vbegy');
							} else if (empty($get_comment)) {
								echo " " . __('( Deleted )', 'vbegy');
							}
							if ($notification_result["text"] == "delete_reason") {
								echo " - " . (isset($notification_result["type_of_item"]) && $notification_result["type_of_item"] == "answer" ? __("answer", "vbegy") : __("comment", "vbegy"));
							}
						}
						if (!empty($notification_result["post_id"]) && empty($notification_result["comment_id"])) {
							if ($get_post_status == "trash") {
								echo " " . __('( Trashed )', 'vbegy');
							} else if (empty($get_the_permalink)) {
								echo " " . __('( Deleted )', 'vbegy');
							}
						}
						if (!empty($notification_result["more_text"]) && $notification_result["text"] != "action_post" && $notification_result["text"] != "action_comment" && $notification_result["text"] != "delete_reason") {
							echo " - " . esc_attr($notification_result["more_text"]) . ".";
						}
						echo "</li>";
					}
				}
				echo "</div></ul>
		<a href='" . get_page_link(askme_options('notifications_page')) . "'>" . __("Show all notifications.", "vbegy") . "</a>";
			} else {
				echo "<p class='no-notifications'>" . __("There are no notifications yet.", "vbegy") . "</p>";
			}
		}
		/* ask_custom_widget_search */
		function ask_custom_widget_search($form)
		{
			$live_search  = askme_options("live_search");
			$search_page  = askme_options('search_page');
			$search_type  = (isset($_GET["search_type"]) && $_GET["search_type"] != "" ? esc_attr($_GET["search_type"]) : askme_options("default_search"));
			$search_attrs = askme_options("search_attrs");
			if (isset($search_attrs) && is_array($search_attrs) && !empty($search_attrs)) {
				$k_count = 0;
				foreach ($search_attrs as $key => $value) {
					if (isset($value) && isset($value["value"]) && $value["value"] == $key) {
						$k_count++;
						$count_search_attrs = $k_count;
					}
				}
			}
			$form = '<form role="search" method="get" class="search-form" action="' . esc_url((isset($search_page) && $search_page != "" ? get_page_link($search_page) : "")) . '">';
			if (isset($search_page) && $search_page != "") {
				$form .= '<input type="hidden" name="page_id" value="' . esc_attr($search_page) . '">';
			}
			$form .= '<label>
			<input type="search" class="search-field' . ($live_search == 1 ? " live-search" : "") . '"' . ($live_search == 1 ? " autocomplete='off'" : "") . ' placeholder="' . esc_html__("Search ...", "vbegy") . '" value="' . (get_query_var('search') != "" ? esc_attr(get_query_var('search')) : esc_attr(get_query_var('s'))) . '" name="search">';
			if ($live_search == 1) {
				$form .= '<div class="loader_2 search_loader"></div>
				<div class="search-results results-empty"></div>';
			}
			$form .= '</label>
		<select name="search_type" class="search_type">';
			if (isset($count_search_attrs) && $count_search_attrs > 1) {
				$form .= '<option value="-1">' . esc_html__("Select kind of search", "vbegy") . '</option>';
			}
			foreach ($search_attrs as $key => $value) {
				if ($key == "questions" && isset($search_attrs["questions"]) && isset($search_attrs["questions"]["value"]) && $search_attrs["questions"]["value"] == "questions") {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), "questions", false) . ' value="questions">' . esc_html__("Questions", "vbegy") . '</option>';
				} else if ($key == "answers" && isset($search_attrs["answers"]) && isset($search_attrs["answers"]["value"]) && $search_attrs["answers"]["value"] == "answers") {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), "answers", false) . ' value="answers">' . esc_html__("Answers", "vbegy") . '</option>';
				} else if ($key == ask_question_category && isset($search_attrs[ask_question_category]) && isset($search_attrs[ask_question_category]["value"]) && $search_attrs[ask_question_category]["value"] == ask_question_category) {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), ask_question_category, false) . ' value="' . ask_question_category . '">' . esc_html__("Question categories", "vbegy") . '</option>';
				} else if ($key == ask_question_tags && isset($search_attrs[ask_question_tags]) && isset($search_attrs[ask_question_tags]["value"]) && $search_attrs[ask_question_tags]["value"] == ask_question_tags) {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), ask_question_tags, false) . ' value=ask_question_tags>' . esc_html__("Question tags", "vbegy") . '</option>';
				} else if ($key == "posts" && isset($search_attrs["posts"]) && isset($search_attrs["posts"]["value"]) && $search_attrs["posts"]["value"] == "posts") {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), "posts", false) . ' value="posts">' . esc_html__("Posts", "vbegy") . '</option>';
				} else if ($key == "comments" && isset($search_attrs["comments"]) && isset($search_attrs["comments"]["value"]) && $search_attrs["comments"]["value"] == "comments") {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), "comments", false) . ' value="comments">' . esc_html__("Comments", "vbegy") . '</option>';
				} else if ($key == "category" && isset($search_attrs["category"]) && isset($search_attrs["category"]["value"]) && $search_attrs["category"]["value"] == "category") {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), "category", false) . ' value="category">' . esc_html__("Post categories", "vbegy") . '</option>';
				} else if ($key == "post_tag" && isset($search_attrs["post_tag"]) && isset($search_attrs["post_tag"]["value"]) && $search_attrs["post_tag"]["value"] == "post_tag") {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), "post_tag", false) . ' value="post_tag">' . esc_html__("Post tags", "vbegy") . '</option>';
				} else if ($key == "users" && isset($search_attrs["users"]) && isset($search_attrs["users"]["value"]) && $search_attrs["users"]["value"] == "users") {
					$form .= '<option ' . selected((isset($search_type) && $search_type != "" ? $search_type : ""), "users", false) . ' value="users">' . esc_html__("Users", "vbegy") . '</option>';
				}
			}
			$form .= '</select>
		<input type="submit" class="search-submit" value="' . esc_html__("Search", "vbegy") . '">
		' . apply_filters("askme_search_filter_in_form", false) . '
	</form>';
			return $form;
		}
		add_filter('get_search_form', 'ask_custom_widget_search', 100);
		function ask_author($user_id, $sort)
		{
			$your_avatar = get_the_author_meta(askme_avatar_name(), $user_id);
			$country = get_the_author_meta('country', $user_id);
			$url = get_the_author_meta('url', $user_id);
			$twitter = get_the_author_meta('twitter', $user_id);
			$facebook = get_the_author_meta('facebook', $user_id);
			$tiktok = get_the_author_meta('tiktok', $user_id);
			$linkedin = get_the_author_meta('linkedin', $user_id);
			$follow_email = get_the_author_meta('follow_email', $user_id);
			$youtube = get_the_author_meta('youtube', $user_id);
			$pinterest = get_the_author_meta('pinterest', $user_id);
			$instagram = get_the_author_meta('instagram', $user_id);
			$author_description = get_the_author_meta("description", $user_id);
			$points_u = get_the_author_meta('points', $user_id);
			$the_best_answer_u = get_the_author_meta('the_best_answer', $user_id);
			$display_name = get_the_author_meta('display_name', $user_id);
			$user_email = get_the_author_meta('user_email', $user_id);
			$verified_user = get_the_author_meta('verified_user', $user_id);
			$count_question = askme_count_posts_by_user($user_id, array(ask_questions_type, ask_asked_questions_type), "publish");
			$out = '<div class="about-author clearfix">
		<div class="author-image">
		<a href="' . vpanel_get_user_url($user_id) . '" original-title="' . $display_name . '" class="tooltip-n">
			' . askme_user_avatar($your_avatar, 65, 65, $user_id, $display_name) . '	
		</a>
		</div>
		<div class="author-bio">
			<h4><a href="' . vpanel_get_user_url($user_id) . '">' . $display_name . '</a>' . ($verified_user == 1 ? '<img class="verified_user tooltip-n" alt="' . __("Verified", "vbegy") . '" original-title="' . __("Verified", "vbegy") . '" src="' . get_template_directory_uri() . '/images/verified.png">' : '') . vpanel_get_badge($user_id) . '</h4>';
			if ($sort == "points") {
				$out .= '<span class="user_count"><i class="icon-heart"></i>' . ($points_u != "" ? $points_u : "0") . ' ' . __("Points", "vbegy") . '</span><br>';
			} else if ($sort == "the_best_answer") {
				$out .= '<span class="user_count"><i class="icon-asterisk"></i>' . ($the_best_answer_u != "" ? $the_best_answer_u : "0") . ' ' . __("Best answers", "vbegy") . '</span><br>';
			} else if ($sort == "question_count" || $sort == "post_count" || $sort == "answers" || $sort == "comments") {
				if ($sort == "question_count") {
					$out .= '<span class="user_count"><i class="icon-question-sign"></i>' . $count_question . ' ' . __("Questions", "vbegy") . '</span>';
				} else if ($sort == "answers") {
					$out .= '<span class="user_count"><i class="icon-comment"></i>' . count(get_comments(array("post_type" => array(ask_questions_type, ask_asked_questions_type), "status" => "approve", "user_id" => $user_id))) . ' ' . __("Answers", "vbegy") . '</span>';
				} else if ($sort == "comments") {
					$out .= '<span class="user_count"><i class="icon-comments"></i>' . count(get_comments(array("post_type" => "post", "status" => "approve", "user_id" => $user_id))) . ' ' . __("Comments", "vbegy") . '</span>';
				} else {
					$out .= '<span class="user_count"><i class="icon-file-alt"></i>' . askme_count_posts_by_user($user_id, "post", "publish") . ' ' . __("Posts", "vbegy") . '</span>';
				}
			}
			$out .= '<div class="clearfix"></div>
			' . $author_description . '
			<div class="clearfix"></div>
			<br>';
			if ($facebook || $tiktok || $twitter || $linkedin || $follow_email || $youtube || $pinterest || $instagram) {
				$out .= '<span class="user-follow-me">' . __("Follow Me", "vbegy") . '</span>
				<div class="social_icons social_icons_display">';
				if ($facebook) {
					$out .= '<a href="' . $facebook . '" original-title="' . __("Facebook", "vbegy") . '" class="tooltip-n">
							<span class="icon_i">
								<span class="icon_square" icon_size="30" span_bg="#3b5997" span_hover="#2f3239">
									<i class="social_icon-facebook"></i>
								</span>
							</span>
						</a>';
				}
				if ($twitter) {
					$out .= '<a href="' . $twitter . '" original-title="' . __("Twitter", "vbegy") . '" class="tooltip-n">
							<span class="icon_i">
								<span class="icon_square" icon_size="30" span_bg="#00baf0" span_hover="#2f3239">
									<i class="social_icon-twitter"></i>
								</span>
							</span>
						</a>';
				}
				if ($tiktok) {
					$out .= '<a href="' . $tiktok . '" original-title="' . __("TikTok", "vbegy") . '" class="tooltip-n">
							<span class="icon_i">
								<span class="icon_square" icon_size="30" span_bg="#3b5997" span_hover="#2f3239">
									<i class="fab fa-tiktok"></i>
								</span>
							</span>
						</a>';
				}
				if ($linkedin) {
					$out .= '<a href="' . $linkedin . '" original-title="' . __("Linkedin", "vbegy") . '" class="tooltip-n">
							<span class="icon_i">
								<span class="icon_square" icon_size="30" span_bg="#006599" span_hover="#2f3239">
									<i class="social_icon-linkedin"></i>
								</span>
							</span>
						</a>';
				}
				if ($youtube) {
					$out .= '<a href="' . $youtube . '" original-title="' . __("Youtube", "vbegy") . '" class="tooltip-n">
							<span class="icon_i">
								<span class="icon_square" icon_size="30" span_bg="#ef4e41" span_hover="#2f3239">
									<i class="social_icon-youtube"></i>
								</span>
							</span>
						</a>';
				}
				if ($pinterest) {
					$out .= '<a href="' . $pinterest . '" original-title="' . __("Pinterest", "vbegy") . '" class="tooltip-n">
							<span class="icon_i">
								<span class="icon_square" icon_size="30" span_bg="#e13138" span_hover="#2f3239">
									<i class="social_icon-pinterest"></i>
								</span>
							</span>
						</a>';
				}
				if ($instagram) {
					$out .= '<a href="' . $instagram . '" original-title="' . __("Instagram", "vbegy") . '" class="tooltip-n">
							<span class="icon_i">
								<span class="icon_square" icon_size="30" span_bg="#548bb6" span_hover="#2f3239">
									<i class="social_icon-instagram"></i>
								</span>
							</span>
						</a>';
				}
				if ($follow_email) {
					$out .= '<a href="mailto:' . $user_email . '" original-title="' . __("Email", "vbegy") . '" class="tooltip-n">
							<span class="icon_i">
								<span class="icon_square" icon_size="30" span_bg="#000" span_hover="#2f3239">
									<i class="social_icon-email"></i>
								</span>
							</span>
						</a>';
				}
				$out .= '</div>';
			}
			$out .= '</div>
	</div>';
			return $out;
		}
		/* ask_count_number */
		function ask_count_number($input)
		{
			$input = number_format((int)$input);
			$input_count = substr_count($input, ',');
			if ($input_count != '0') {
				if ($input_count == '1') {
					return (int)substr($input, 0, -4) . 'k';
				} else if ($input_count == '2') {
					return (int)substr($input, 0, -8) . 'mil';
				} else if ($input_count == '3') {
					return (int)substr($input, 0, -12) . 'bil';
				} else {
					return;
				}
			} else {
				return $input;
			}
		}
		/* ask_live_search */
		function ask_live_search()
		{
			global $post;
			$search_type          = (isset($_POST["search_type"]) ? esc_attr($_POST["search_type"]) : "");
			$search_type          = (isset($search_type) && $search_type != "" ? $search_type : askme_options("default_search"));
			$search_value         = esc_js(wp_unslash(sanitize_text_field($_POST["search_value"])));
			$search_result_number = askme_options("search_result_number");
			$search_page          = askme_options("search_page");
			$k_search             = 0;
			if ($search_value != "") {
				echo "<div class='result-div'>
			<ul>";
				if ($search_type == "answers" || $search_type == "comments") {
					$not_get_result_page = true;
					include locate_template("includes/comments.php");
				} else if ($search_type == "users") {
					$not_get_result_page = true;
					include locate_template("includes/users.php");
				} else if ($search_type == ask_question_category || $search_type == "product_cat" || $search_type == "category") {
					$not_get_result_page = true;
					include locate_template("includes/categories.php");
				} else if ($search_type == ask_question_tags || $search_type == "product_tag" || $search_type == "post_tag") {
					$not_get_result_page = true;
					include locate_template("includes/tags.php");
				} else {
					if ($search_type == "posts") {
						$post_type_array = array('post');
					} else if ($search_type == "products") {
						$post_type_array = array('product');
					} else {
						$search_type = "questions";
						$post_type_array = array(ask_questions_type);
					}

					$block_users = askme_options("block_users");
					$author__not_in = array();
					if ($block_users == 1) {
						$get_current_user_id = get_current_user_id();
						if ($get_current_user_id > 0) {
							$get_block_users = get_user_meta($get_current_user_id, "askme_block_users", true);
							if (is_array($get_block_users) && !empty($get_block_users)) {
								$author__not_in = array("author__not_in" => $get_block_users);
							}
						}
					}

					$args = array_merge($author__not_in, array('s' => $search_value, 'post_status' => 'publish', 'post_type' => $post_type_array));
					$args = apply_filters("askme_filter_live_search_query", $args, $search_value, $post_type_array);
					$search_query = new wp_query($args);
					if ($search_query->have_posts()) :
						while ($search_query->have_posts()) : $search_query->the_post();
							$k_search++;
							if ($search_result_number >= $k_search) {
								echo "<li>";
								if ($search_type == "products") {
									echo '<a class="get-results" href="' . get_permalink($post->ID) . '">
											' . askme_resize_img(20, 20) . '
										</a>';
								}
								echo "<a href='" . get_permalink($post->ID) . "'>" . str_ireplace($search_value, "<strong>" . $search_value . "</strong>", get_the_title($post->ID)) . "</a>
								</li>";
							} else {
								echo "<li><a href='" . esc_url(add_query_arg(array("search" => $search_value, "search_type" => $search_type), (isset($search_page) && $search_page != "" ? get_page_link($search_page) : ""))) . "'>" . __("View all results.", "vbegy") . "</a></li>";
								exit;
							}
						endwhile;
					else :
						echo "<li class='no-search-result'>" . __("No results found.", "vbegy") . "</li>";
					endif;
					wp_reset_postdata();
				}
				echo "</ul>
		</div>";
			}
			die();
		}
		add_action('wp_ajax_ask_live_search', 'ask_live_search');
		add_action('wp_ajax_nopriv_ask_live_search', 'ask_live_search');
		/* ask_private */
		function ask_private($post_id, $first_user, $second_user)
		{
			global $post;
			$get_private_question = get_post_meta($post_id, "private_question", true);
			$user_id = get_post_meta($post_id, "user_id", true);
			$user_is_comment = get_post_meta($post_id, "user_is_comment", true);
			$anonymously_user = get_post_meta($post_id, "anonymously_user", true);
			$question_category = wp_get_post_terms($post_id, ask_question_category, array("fields" => "all"));

			if (isset($question_category) && is_array($question_category) && isset($question_category[0])) {
				$askme_private = get_term_meta($question_category[0]->term_id, "vbegy_private", true);
				$askme_private = (isset($askme_private) ? $askme_private : "");
				$yes_private = 0;
				if (isset($question_category[0]) && $askme_private == "on") {
					if (isset($authordata->ID) && $authordata->ID > 0 && $authordata->ID == $user_get_current_user_id) {
						$yes_private = 1;
					}
				} else if (isset($question_category[0]) && $askme_private != "on") {
					$yes_private = 1;
				}
			} else {
				$yes_private = 1;
			}

			if (isset($question_category) && is_array($question_category) && empty($question_category[0])) {
				$yes_private = 1;
			}

			if ($get_private_question == 1) {
				$yes_private = 0;
				if (isset($first_user) && $first_user > 0 && $first_user == $second_user) {
					$yes_private = 1;
				}
			}

			if ($get_private_question == 1 || $get_private_question == "on" || ($user_id != "" && $user_is_comment != true)) {
				$yes_private = 0;
				if ((isset($first_user) && $first_user > 0 && $first_user == $second_user) || ($user_id > 0 && $user_id == $second_user) || ($anonymously_user > 0 && $anonymously_user == $second_user)) {
					$yes_private = 1;
				}
			}

			if (is_super_admin($second_user)) {
				$yes_private = 1;
			}
			return $yes_private;
		}
		/* ask_private_answer */
		function ask_private_answer($comment_id, $first_user, $second_user, $post_author)
		{
			$yes_private_answer = 0;
			$private_answer = askme_options("private_answer");
			$get_private_answer = get_comment_meta($comment_id, 'private_answer', true);

			if ($private_answer == 1) {
				$private_answer_user = askme_options("private_answer_user");
				if (($get_private_answer == 1 && $private_answer_user == 1 && $second_user == $post_author && $post_author > 0) || (($get_private_answer == 1 && isset($first_user) && $first_user > 0 && $first_user == $second_user) || $get_private_answer != 1)) {
					$yes_private_answer = 1;
				}
			} else {
				$yes_private_answer = 1;
			}

			if (is_super_admin($second_user)) {
				$yes_private_answer = 1;
			}
			return $yes_private_answer;
		}
		/* ask_option_images */
		function ask_option_images($value_id = '', $value_width = '', $value_height = '', $value_options = '', $val = '', $value_class = '', $option_name = '', $name_id = '', $data_attr = '', $add_value_id = '')
		{
			$output = '';
			$name = $option_name . ($add_value_id != 'no' ? '[' . $value_id . ']' : '');
			$width = (isset($value_width) && $value_width != "" ? " width='" . $value_width . "' style='box-sizing: border-box;-moz-box-sizing: border-box;-weblit-box-sizing: border-box;'" : "");
			$height = (isset($value_height) && $value_height != "" ? " height='" . $value_height . "' style='box-sizing: border-box;-moz-box-sizing: border-box;-weblit-box-sizing: border-box;'" : "");
			foreach ($value_options as $key => $option) {
				$selected = '';
				if ($val != '' && ($val == $key)) {
					$selected = ' of-radio-img-selected';
				}
				$output .= '<div class="of-radio-img-label">' . esc_html($key) . '</div>';
				$output .= '<input type="radio" data-attr="' . esc_attr($data_attr) . '" class="of-radio-img-radio" value="' . esc_attr($key) . '" ' . ($name_id != "no" ? ' id="' . esc_attr($value_id . '_' . $key) . '" name="' . esc_attr($name) . '"' : '') . ' ' . checked($val, $key, false) . '>';
				$output .= '<img' . $width . $height . ' src="' . esc_url($option) . '" alt="' . $option . '" class="of-radio-img-img vpanel-radio-img-img' . (isset($value_class) ? " " . esc_attr($value_class) : '') . '' . $selected . '" ' . ($name_id != "no" ? 'onclick="document.getElementById(\'' . esc_attr($value_id . '_' . $key) . '\').checked=true;"' : '') . '>';
			}
			return $output;
		}
		/* ask_option_sliderui */
		function ask_option_sliderui($value_min = '', $value_max = '', $value_step = '', $value_edit = '', $val = '', $value_id = '', $option_name = '', $element = '', $bracket = '', $widget = '')
		{
			$output = $min = $max = $step = $edit = '';

			if (!isset($value_min)) {
				$min  = '0';
			} else {
				$min = $value_min;
			}
			if (!isset($value_max)) {
				$max  = $min + 1;
			} else {
				$max = $value_max;
			}
			if (!isset($value_step)) {
				$step  = '1';
			} else {
				$step = $value_step;
			}

			if (!isset($value_edit)) {
				$edit  = ' readonly="readonly"';
			} else {
				$edit  = '';
			}

			if ($val == '') $val = $min;

			//values
			$data = 'data-id="' . (isset($element) && $element != "" ? $element : $value_id) . '" data-val="' . $val . '" data-min="' . $min . '" data-max="' . $max . '" data-step="' . $step . '"';

			//html output
			$output .= '<input type="text" name="' . esc_attr((isset($widget) && $widget == "widget" ? $option_name : $option_name . ($bracket != 'remove_it' ? '[' : '') . $value_id . ']')) . '" id="' . (isset($element) && $element != "" ? $element : $value_id) . '" value="' . $val . '" class="mini" ' . $edit . ' />';
			$output .= '<div id="' . (isset($element) && $element != "" ? $element : $value_id) . '-slider" class="v_sliderui" ' . $data . '></div>';
			return $output;
		}
		/* ask_filter_where */
		function ask_filter_where($where = '')
		{
			$where .= " AND comment_count = 0";
			return $where;
		}
		/* ask_filter_where_more */
		function ask_filter_where_more($where = '')
		{
			$where .= " AND comment_count > 0";
			return $where;
		}
		/* ask_me_not_show_questions */
		add_action('wp', 'ask_me_not_show_questions');
		function ask_me_not_show_questions()
		{
			global $post;
			if (is_singular(ask_questions_type) || is_singular(ask_asked_questions_type)) {
				$private_question_content = askme_options("private_question_content");
				$user_get_current_user_id = get_current_user_id();
				$yes_private = ask_private($post->ID, $post->post_author, $user_get_current_user_id);
				if (!is_super_admin($user_get_current_user_id) && $yes_private != 1 && $private_question_content != 1) {
					global $wp_query;
					$wp_query->set_404();
					status_header(404);
				}
			}
		}
		/* Get comment reply link */
		add_filter("comment_reply_link", "askme_comment_reply_link", 1, 3);
		if (!function_exists('askme_comment_reply_link')) :
			function askme_comment_reply_link($link, $args, $comment)
			{
				$comment_editor = askme_options("comment_editor");
				if ($comment_editor == 1) {
					$link = '<a rel="nofollow" class="comment-reply-link askme-reply-link" href="#respond" data-id="' . $comment->comment_ID . '" aria-label="' . esc_attr(sprintf($args['reply_to_text'], $comment->comment_author)) . '"><i class="icon-reply"></i>' . esc_html__("Reply", "vbegy") . '</a>';
				}
				return $link;
			}
		endif;
		/* Login URL */
		$redirect_wp_admin_unlogged = askme_options("redirect_wp_admin_unlogged");
		if ($redirect_wp_admin_unlogged == 1) {
			add_filter('login_url', 'askme_login_url', 10, 1);
		}
		if (!function_exists('askme_login_url')) :
			function askme_login_url()
			{
				return get_page_link(askme_options('login_register_page'));
			}
		endif;
		/* Login redirect */
		add_action('init', 'askme_login_redirect', 10, 1);
		if (!function_exists('askme_login_redirect')) :
			function askme_login_redirect()
			{
				$redirect_wp_admin_unlogged = askme_options("redirect_wp_admin_unlogged");
				if ($redirect_wp_admin_unlogged == 1 && isset($GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php' && (!isset($_GET['action']) || (isset($_GET['action']) && $_GET['action'] == 'login'))) {
					wp_safe_redirect(get_page_link(askme_options('login_register_page')));
					exit;
				}
				if (is_admin() && !ask_is_ajax() && is_user_logged_in()) {
					$redirect_wp_admin = askme_options("redirect_wp_admin");
					if ($redirect_wp_admin == 1) {
						$redirect_groups = askme_options("redirect_groups");
						$user_id = get_current_user_id();
						$user_info = get_userdata($user_id);
						$user_group = askme_get_user_group($user_info);
						if (is_array($redirect_groups) && isset($redirect_groups[$user_group]) && $redirect_groups[$user_group] == 1) {
							wp_safe_redirect(home_url());
							exit;
						}
					}
				}
			}
		endif;
		/* Top bar wordpress */
		add_filter('show_admin_bar', 'askme_disable_admin_bar', 20, 1);
		if (!function_exists('askme_disable_admin_bar')) :
			function askme_disable_admin_bar($show_admin_bar)
			{
				$top_bar_wordpress = askme_options("top_bar_wordpress");
				if ($top_bar_wordpress == 1 && !(current_user_can('administrator'))) {
					$top_bar_groups = askme_options("top_bar_groups");
					$user_id = get_current_user_id();
					$user_info = get_userdata($user_id);
					$user_group = askme_get_user_group($user_info);
					if (is_array($top_bar_groups) && isset($top_bar_groups[$user_group]) && $top_bar_groups[$user_group] == 1) {
						$show_admin_bar = false;
					}
				}
				return $show_admin_bar;
			}
		endif;
		/* Mention */
		add_filter("the_content", "askme_mention");
		add_filter("comment_text", "askme_mention");
		if (!function_exists('askme_mention')) :
			function askme_mention($content)
			{
				$active_mention = askme_options("active_mention");
				if ($active_mention == 1) {
					if (preg_match_all('/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i', $content, $matches_email)) {
						if (isset($matches[0])) {
						}
					} else if (preg_match_all('/@[\w\s+]+/', $content, $matches)) {
						if (isset($matches[0])) {
							foreach ($matches[0] as $key => $match) {
								$first_match = str_ireplace("@", "", $match);
								$middle_match = preg_split("/[\s,]+/", $first_match);
								$match = trim((isset($middle_match[0]) && $middle_match[0] !== "" ? $middle_match[0] : "") . " " . (isset($middle_match[1]) && $middle_match[1] !== "" ? $middle_match[1] : ""));
								$last_match = trim(preg_replace('/\s+/', ' ', $match));
								if ($last_match != "") {
									$first_name = (isset($middle_match[0]) && $middle_match[0] !== "" ? $middle_match[0] : $last_match);
									add_action('pre_user_query', 'askme_custom_search_mention');
									$args = array(
										'meta_query' => array('relation' => 'OR', array("key" => "first_name", "value" => $first_name, "compare" => "RLIKE")),
										'orderby'    => "user_registered",
										'order'      => "ASC",
										'search'     => '*' . $last_match . '*',
										'number'     => 1,
										'fields'     => 'ID',
									);
									$query = new WP_User_Query($args);
									$query = $query->get_results();
									if (isset($query[0])) {
										$user_id = $query[0];
										$content = str_ireplace('@' . $last_match, '<a target="_blank" href="' . vpanel_get_user_url($user_id) . '">' . get_the_author_meta('display_name', $user_id) . '</a>', $content);
									} else {
										$args = array(
											'meta_query' => array('relation' => 'OR', array("key" => "first_name", "value" => $first_name, "compare" => "RLIKE")),
											'orderby'    => "user_registered",
											'order'      => "ASC",
											'search'     => '*' . $first_name . '*',
											'number'     => 1,
											'fields'     => 'ID',
										);
										$query = new WP_User_Query($args);
										$query = $query->get_results();
										if (isset($query[0])) {
											$user_id = $query[0];
											$content = str_ireplace('@' . $first_name, '<a target="_blank" href="' . vpanel_get_user_url($user_id) . '">' . get_the_author_meta('display_name', $user_id) . '</a>', $content);
										}
									}
									remove_action('pre_user_query', 'askme_custom_search_mention');
								}
							}
						}
					}
				}
				return $content;
			}
		endif;
		/* Custom search for mention */
		if (!function_exists('askme_custom_search_mention')) :
			function askme_custom_search_mention($user_query)
			{
				global $wpdb;
				$search_value = esc_js($user_query->query_vars);
				if (is_array($search_value) && isset($search_value['search'])) {
					$search_value = str_replace("*", "", $search_value['search']);
				}
				$search_value = apply_filters("askme_search_value_filter", $search_value);
				$user_query->query_where .= " 
		OR (ID IN (SELECT user_id FROM $wpdb->usermeta
		WHERE (($wpdb->usermeta.meta_key='nickname' OR $wpdb->usermeta.meta_key='first_name' OR $wpdb->usermeta.meta_key='last_name')
			AND ($wpdb->usermeta.meta_value LIKE '" . $search_value . "' OR $wpdb->usermeta.meta_value RLIKE '" . $search_value . "'))
		)
		OR ($wpdb->users.ID LIKE '" . $search_value . "' OR $wpdb->users.ID RLIKE '" . $search_value . "') 
		OR ($wpdb->users.display_name LIKE '" . $search_value . "' OR $wpdb->users.display_name RLIKE '" . $search_value . "') 
		OR ($wpdb->users.user_login LIKE '" . $search_value . "' OR $wpdb->users.user_login RLIKE '" . $search_value . "') 
		OR ($wpdb->users.user_nicename LIKE '" . $search_value . "' OR $wpdb->users.user_nicename RLIKE '" . $search_value . "'))";
			}
		endif;
		/* Captcha */
		if (!function_exists('askme_captcha')) :
			function askme_add_captcha($the_captcha, $type, $rand, $comment = "")
			{
				$out = "";
				$captcha_users = askme_options("captcha_users");
				$captcha_style = askme_options("captcha_style");
				$captcha_question = askme_options("captcha_question");
				$captcha_answer = askme_options("captcha_answer");
				$show_captcha_answer = askme_options("show_captcha_answer");
				if ($the_captcha == 1 && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
					$out .= "<span class='clearfix'></span>
				<" . ($captcha_style == "google_recaptcha" ? "div" : "p") . " class='ask_captcha_p'>
				<label for='ask_captcha-" . $rand . "' class='required'>" . __("Captcha", "vbegy") . "<span>*</span></label>";
					if ($captcha_style == "google_recaptcha") {
						$out .= "<div class='g-recaptcha' data-sitekey='" . askme_options("site_key_recaptcha") . "'></div><br>";
					} else if ($captcha_style == "question_answer") {
						$out .= "<input size='10' id='ask_captcha-" . $rand . "' name='ask_captcha' class='ask_captcha captcha_answer' value='' type='text'>
				<span class='question_poll ask_captcha_span'>" . $captcha_question . ($show_captcha_answer == 1 ? " ( " . $captcha_answer . " )" : "") . "</span>";
					} else {
						$out .= "<input size='10' id='ask_captcha-" . $rand . "' name='ask_captcha' class='ask_captcha' value='' type='text'><img class='ask_captcha_img' src='" . add_query_arg(array("captcha_type" => $type), get_template_directory_uri() . "/captcha/create_image.php") . "' alt='" . __("Captcha", "vbegy") . "' title='" . __("Click here to update the captcha", "vbegy") . "' onclick=";
						$out .= '"javascript:ask_get_captcha';
						$out .= "('" . add_query_arg(array("captcha_type" => $type), get_template_directory_uri() . "/captcha/create_image.php") . "', 'ask_captcha_img_" . $rand . "');";
						$out .= '"';
						$out .= " id='ask_captcha_img_" . $rand . "'>
				<span class='question_poll ask_captcha_span'>" . __("Click on image to update the captcha .", "vbegy") . "</span>";
					}
					$out .= "</" . ($captcha_style == "google_recaptcha" ? "div" : "p") . ">";
				}
				return $out;
			}
		endif;
		/* Check captcha */
		if (!function_exists('askme_check_captcha')) :
			function askme_check_captcha($the_captcha, $type, $posted, $errors)
			{
				$captcha_users = askme_options("captcha_users");
				$captcha_style = askme_options("captcha_style");
				$captcha_question = askme_options("captcha_question");
				$captcha_answer = askme_options("captcha_answer");
				$show_captcha_answer = askme_options("show_captcha_answer");
				$the_captcha = (!isset($_POST['mobile']) && !isset($_GET['mobile']) ? $the_captcha : 0);
				if ($the_captcha == 1 && ($captcha_users == "both" || ($captcha_users == "unlogged" && !is_user_logged_in()))) {
					if ($captcha_style == "google_recaptcha") {
						if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
							$secretKey = askme_options("secret_key_recaptcha");
							$data = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretKey . '&response=' . $_POST['g-recaptcha-response']);
							if (is_wp_error($data)) {
								$errors->add('required-captcha-error', esc_html__('Robot verification failed, Please try again.', 'vbegy'));
							} else {
								$json = json_decode($data['body'], true);
							}
							if ((isset($json["success"]) && $json["success"] == true) || (isset($json["error-codes"]) && isset($json["error-codes"][0]) && $json["error-codes"][0] == "timeout-or-duplicate")) {
								//success
							} else {
								$errors->add('required-captcha-error', esc_html__('Robot verification failed, Please try again.', 'vbegy'));
							}
						} else {
							$errors->add('required-captcha-error', esc_html__('Please check on the reCAPTCHA box.', 'vbegy'));
						}
					} else {
						if (empty($posted["ask_captcha"])) {
							$errors->add('required-captcha', esc_html__("There are required fields (captcha).", "vbegy"));
						} else if ($captcha_style == "question_answer") {
							if ($captcha_answer != $posted["ask_captcha"]) {
								$errors->add('required-captcha-error', esc_html__('The captcha is incorrect, Please try again.', 'vbegy'));
							}
						} else {
							if (!session_id()) session_start();
							if (isset($_SESSION["askme_code_captcha_" . $type]) && $_SESSION["askme_code_captcha_" . $type] != $posted["ask_captcha"]) {
								$errors->add('required-captcha-error', esc_html__('The captcha is incorrect, Please try again.', 'vbegy'));
							}
						}
					}
				}
				return $errors;
			}
		endif;
		/* Get user id */
		function askme_get_user_object($user_ID = 0)
		{
			if (is_author()) {
				$user_login = get_queried_object();
				if (isset($user_login) && is_object($user_login)) {
					$user_login = get_userdata(esc_attr($user_login->ID));
				}
				if (isset($user_login) && !is_object($user_login)) {
					$user_login = get_user_by('login', urldecode(get_query_var('author_name')));
				}
				if (isset($user_login) && !is_object($user_login)) {
					$user_login = get_user_by('slug', urldecode(get_query_var('author_name')));
				}
			} else {
				$user_login = get_userdata((int)(is_user_logged_in && empty($_GET['u']) ? $user_ID : (isset($_GET['u']) ? $_GET['u'] : 0)));
			}
			if (isset($user_login) && is_object($user_login)) {
				return $user_login;
			}
		}
		/* Get post stats */
		function askme_get_post_stats($post_id = 0, $user_id = 0)
		{
			$meta_id = ($user_id > 0 ? $user_id : $post_id);
			$post_meta_stats = askme_get_meta_stats();
			$cache_post_stats = askme_options("cache_post_stats");
			if ($cache_post_stats == 1) {
				$post_stats = get_transient($post_meta_stats . $meta_id);
				$post_stats = ($post_stats !== false ? $post_stats : askme_get_meta($post_meta_stats, $post_id, 0, 0, $user_id));
			} else {
				$post_stats = askme_get_meta($post_meta_stats, $post_id, 0, 0, $user_id);
			}
			return (int)$post_stats;
		}
		/* Post stats name */
		function askme_get_meta_stats()
		{
			$post_meta_stats = askme_options("post_meta_stats");
			$post_meta_stats = apply_filters("askme_post_meta_stats", $post_meta_stats);
			$post_meta_stats = ($post_meta_stats != "" ? $post_meta_stats : "post_stats");
			return $post_meta_stats;
		}
		/* Get avatar name */
		function askme_avatar_name()
		{
			$avatar = askme_options("user_meta_avatar");
			$avatar = apply_filters("askme_user_meta_avatar", $avatar);
			$avatar = ($avatar != "" ? $avatar : "you_avatar");
			return $avatar;
		}
		/* Get points for badges page */
		function askme_get_points()
		{
			$mobile_rewarded_adv   = askme_options("mobile_rewarded_adv");
			$points_rewarded       = askme_options("points_rewarded");
			$point_add_question    = askme_options("point_add_question");
			$point_best_answer     = askme_options("point_best_answer");
			$point_rating_question = askme_options("point_rating_question");
			$point_poll_question   = askme_options("point_poll_question");
			$point_add_comment     = askme_options("point_add_comment");
			$point_rating_answer   = askme_options("point_rating_answer");
			$point_following_me    = askme_options("point_following_me");
			$point_new_user        = askme_options("point_new_user");
			$point_add_post        = askme_options("point_add_post");
			$points_array          = array(
				"point_add_question"    => array("points" => $point_add_question),
				"point_best_answer"     => array("points" => $point_best_answer),
				"point_rating_question" => array("points" => $point_rating_question),
				"point_poll_question"   => array("points" => $point_poll_question),
				"point_add_comment"     => array("points" => $point_add_comment),
				"point_rating_answer"   => array("points" => $point_rating_answer),
				"point_following_me"    => array("points" => $point_following_me),
				"point_add_post"        => array("points" => $point_add_post),
				"point_new_user"        => array("points" => $point_new_user),
				"point_new_user"        => array("points" => $point_new_user),
				"points_rewarded"       => array("points" => ($mobile_rewarded_adv == "on" ? $points_rewarded : 0)),
			);
			$points_array = apply_filters("askme_filter_points_array", $points_array);
			$points = array_column($points_array, 'points');
			if (is_array($points) && !empty($points) && is_array($points_array) && !empty($points_array)) {
				array_multisort($points, SORT_DESC, $points_array);
			}
			return $points_array;
		}
		function askme_get_points_name($key)
		{
			if ($key == "point_add_question") {
				$output = esc_html__("For adding a new question", "vbegy");
			} else if ($key == "point_best_answer") {
				$output = esc_html__("When your answer has been chosen as the best answer", "vbegy");
			} else if ($key == "point_rating_question") {
				$output = esc_html__("Your question gets a vote", "vbegy");
			} else if ($key == "point_poll_question") {
				$output = esc_html__("For choosing a poll on the question.", "vbegy");
			} else if ($key == "point_add_comment") {
				$output = esc_html__("For adding an answer", "vbegy");
			} else if ($key == "point_rating_answer") {
				$output = esc_html__("Your answer gets a vote", "vbegy");
			} else if ($key == "point_following_me") {
				$output = esc_html__("Each time when a user follows you", "vbegy");
			} else if ($key == "point_add_post") {
				$output = esc_html__("For adding a new post", "vbegy");
			} else if ($key == "point_new_user") {
				$output = esc_html__("For Signing up", "vbegy");
			} else if ($key == "points_rewarded") {
				$output = esc_html__("For the rewarded adv", "vbegy");
			} else if (isset($value["text"]) && $value["text"] != "") {
				$output = esc_html($value["text"]);
			}
			if (isset($output)) {
				return $output;
			}
		}
		/* Show notifications */
		function askme_show_notifications($notification_result)
		{
			$out = '';
			if (!empty($notification_result["another_user_id"])) {
				$vpanel_get_user_url = vpanel_get_user_url($notification_result["another_user_id"]);
				$display_name = get_the_author_meta('display_name', $notification_result["another_user_id"]);
			}

			if (isset($notification_result["text"]) && ((($notification_result["text"] == "add_question_user" || $notification_result["text"] == "add_question" || $notification_result["text"] == "poll_question") && empty($notification_result["username"]) && isset($notification_result["another_user_id"]) && $notification_result["another_user_id"] == 0) || (!empty($notification_result["another_user_id"]) || !empty($notification_result["username"])) && $notification_result["text"] != "admin_add_points" && $notification_result["text"] != "admin_remove_points")) {

				if ((($notification_result["text"] == "add_question_user" || $notification_result["text"] == "add_question" || $notification_result["text"] == "poll_question") && isset($notification_result["another_user_id"]) && $notification_result["another_user_id"] == 0) || (isset($display_name) && $display_name != "")) {
					if (!empty($notification_result["another_user_id"])) {
						$out .= '<a href="' . esc_url($vpanel_get_user_url) . '">' . esc_html($display_name) . '</a>' . " ";
					}
					if (!empty($notification_result["username"])) {
						$out .= esc_attr($notification_result["username"]) . " ";
					}
					if (($notification_result["text"] == "add_question_user" || $notification_result["text"] == "add_question") && empty($notification_result["username"]) && isset($notification_result["another_user_id"]) && $notification_result["another_user_id"] == 0) {
						$out .= esc_html__("Anonymous", "vbegy") . " ";
					}
					if (($notification_result["text"] == "poll_question") && empty($notification_result["username"]) && isset($notification_result["another_user_id"]) && $notification_result["another_user_id"] == 0) {
						$out .= esc_html__("A non-registered user", "vbegy") . " ";
					}
					$out .= esc_html__("has", "vbegy");
				} else if (!empty($notification_result["username"])) {
					$out .= esc_attr($notification_result["username"]) . " ";
				} else {
					$out .= esc_html__("Deleted user", "vbegy") . " -";
				}
			}
			if (!empty($notification_result["post_id"])) {
				$get_the_permalink = get_the_permalink($notification_result["post_id"]);
				$get_post_status = get_post_status($notification_result["post_id"]);
			}
			if (!empty($notification_result["comment_id"])) {
				$get_comment = get_comment($notification_result["comment_id"]);
			}
			if (!empty($notification_result["post_id"]) && !empty($notification_result["comment_id"]) && $get_post_status != "trash" && isset($get_comment) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") {
				$out .= '<a href="' . esc_url($get_the_permalink . (isset($notification_result["comment_id"]) ? "#comment-" . $notification_result["comment_id"] : "")) . '">';
			}
			if (!empty($notification_result["post_id"]) && empty($notification_result["comment_id"]) && $get_post_status != "trash" && isset($get_the_permalink) && $get_the_permalink != "") {
				$out .= '<a href="' . esc_url($get_the_permalink) . '">';
			}
			$out .= " ";
			if (isset($notification_result["text"])) {
				if ($notification_result["text"] == "add_question_user") {
					$out .= esc_html__("been asked you a question.", "vbegy");
				} else if ($notification_result["text"] == "poll_question") {
					$out .= esc_html__("polled at your question", "vbegy");
				} else if ($notification_result["text"] == "gift_site") {
					$out .= esc_html__("Gift of the site.", "vbegy");
				} else if ($notification_result["text"] == "admin_add_points") {
					$out .= esc_html__("The administrator added points for you.", "vbegy");
				} else if ($notification_result["text"] == "admin_remove_points") {
					$out .= esc_html__("The administrator removed points from you.", "vbegy");
				} else if ($notification_result["text"] == "question_vote_up") {
					$out .= esc_html__("voted up your question.", "vbegy");
				} else if ($notification_result["text"] == "question_vote_down") {
					$out .= esc_html__("voted down your question.", "vbegy");
				} else if ($notification_result["text"] == "answer_vote_up") {
					$out .= esc_html__("voted up you answer.", "vbegy");
				} else if ($notification_result["text"] == "answer_vote_down") {
					$out .= esc_html__("voted down you answer.", "vbegy");
				} else if ($notification_result["text"] == "user_follow") {
					$out .= esc_html__("followed you.", "vbegy");
				} else if ($notification_result["text"] == "user_unfollow") {
					$out .= esc_html__("unfollowed you.", "vbegy");
				} else if ($notification_result["text"] == "point_back") {
					$out .= esc_html__("Your point back because the best answer selected.", "vbegy");
				} else if ($notification_result["text"] == "select_best_answer") {
					$out .= esc_html__("chosen your answer best answer.", "vbegy");
				} else if ($notification_result["text"] == "point_removed") {
					$out .= esc_html__("Your point removed because the best answer removed.", "vbegy");
				} else if ($notification_result["text"] == "cancel_best_answer") {
					$out .= esc_html__("canceled your answer best answer.", "vbegy");
				} else if ($notification_result["text"] == "answer_asked_question") {
					$out .= esc_html__("answered at your asked question.", "vbegy");
				} else if ($notification_result["text"] == "answer_question") {
					$out .= esc_html__("answered your question.", "vbegy");
				} else if ($notification_result["text"] == "answer_question_follow") {
					$out .= esc_html__("answered your question you follow.", "vbegy");
				} else if ($notification_result["text"] == "reply_answer") {
					$out .= esc_html__("replied to your answer.", "vbegy");
				} else if ($notification_result["text"] == "add_question") {
					$out .= esc_html__("added a new question.", "vbegy");
				} else if ($notification_result["text"] == "add_post") {
					$out .= esc_html__("added a new post.", "vbegy");
				} else if ($notification_result["text"] == "question_favorites") {
					$out .= esc_html__("added your question at favorites.", "vbegy");
				} else if ($notification_result["text"] == "question_remove_favorites") {
					$out .= esc_html__("removed your question from favorites.", "vbegy");
				} else if ($notification_result["text"] == "follow_question") {
					$out .= esc_html__("followed your question.", "vbegy");
				} else if ($notification_result["text"] == "unfollow_question") {
					$out .= esc_html__("unfollowed your question.", "vbegy");
				} else if ($notification_result["text"] == "approved_answer") {
					$out .= esc_html__("The administrator added your answer.", "vbegy");
				} else if ($notification_result["text"] == "approved_comment") {
					$out .= esc_html__("The administrator added your comment.", "vbegy");
				} else if ($notification_result["text"] == "approved_question") {
					$out .= esc_html__("The administrator added your question.", "vbegy");
				} else if ($notification_result["text"] == "approved_post") {
					$out .= esc_html__("The administrator added your post.", "vbegy");
				} else if ($notification_result["text"] == "add_message_user") {
					$out .= "<a href='" . esc_url(get_page_link(askme_options('messages_page'))) . "'>" . __("sent a message for you.", "vbegy") . "</a>";
				} else if ($notification_result["text"] == "seen_message") {
					$out .= esc_html__("seen your message.", "vbegy");
				} else if ($notification_result["text"] == "action_comment") {
					$out .= sprintf(__("The administrator %s your %s.", "vbegy"), $notification_result["more_text"], (isset($notification_result["type_of_item"]) && $notification_result["type_of_item"] == "answer" ? __("answer", "vbegy") : __("comment", "vbegy")));
				} else if ($notification_result["text"] == "action_post") {
					$out .= sprintf(__("The administrator %s your %s.", "vbegy"), $notification_result["more_text"], (isset($notification_result["type_of_item"]) && ($notification_result["type_of_item"] == ask_questions_type || $notification_result["type_of_item"] == ask_asked_questions_type) ? __("question", "vbegy") : __("post", "vbegy")));
				} else if ($notification_result["text"] == "delete_reason") {
					$out .= sprintf(__("The administrator reason : %s.", "vbegy"), $notification_result["more_text"]);
				} else if ($notification_result["text"] == "delete_question" || $notification_result["text"] == "delete_post") {
					$out .= sprintf(__("The administrator deleted your %s.", "vbegy"), (isset($notification_result["type_of_item"]) && ($notification_result["type_of_item"] == ask_questions_type || $notification_result["type_of_item"] == ask_asked_questions_type) ? __("question", "vbegy") : __("post", "vbegy")));
				} else if ($notification_result["text"] == "delete_answer" || $notification_result["text"] == "delete_comment") {
					$out .= sprintf(__("The administrator deleted your %s.", "vbegy"), (isset($notification_result["type_of_item"]) && $notification_result["type_of_item"] == "answer" ? __("answer", "vbegy") : __("comment", "vbegy")));
				}
			}
			if ((!empty($notification_result["post_id"]) && !empty($notification_result["comment_id"]) && $get_post_status != "trash" && isset($get_comment) && $get_comment->comment_approved != "spam" && $get_comment->comment_approved != "trash") || (!empty($notification_result["post_id"]) && empty($notification_result["comment_id"]) && $get_post_status != "trash" && isset($get_the_permalink) && $get_the_permalink != "")) {
				$out .= '</a>';
			}
			if (!empty($notification_result["post_id"]) && !empty($notification_result["comment_id"])) {
				if (isset($get_comment) && $get_comment->comment_approved == "spam") {
					$out .= " " . __('( Spam )', 'vbegy');
				} else if ($get_post_status == "trash" || (isset($get_comment) && $get_comment->comment_approved == "trash")) {
					$out .= " " . __('( Trashed )', 'vbegy');
				} else if (empty($get_comment)) {
					$out .= " " . __('( Deleted )', 'vbegy');
				}
				if ($notification_result["text"] == "delete_reason") {
					$out .= " - " . (isset($notification_result["type_of_item"]) && $notification_result["type_of_item"] == "answer" ? __("answer", "vbegy") : __("comment", "vbegy"));
				}
			}
			if (!empty($notification_result["post_id"]) && empty($notification_result["comment_id"])) {
				if ($get_post_status == "trash") {
					$out .= " " . __('( Trashed )', 'vbegy');
				} else if (empty($get_the_permalink)) {
					$out .= " " . __('( Deleted )', 'vbegy');
				}
			}
			if (!empty($notification_result["more_text"]) && $notification_result["text"] != "action_post" && $notification_result["text"] != "action_comment" && $notification_result["text"] != "delete_reason") {
				$out .= " - " . esc_attr($notification_result["more_text"]) . ".";
			}
			return $out;
		}
		/* Random token */
		if (!function_exists('askme_token')) :
			function askme_token($length)
			{
				$token = "";
				$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
				$codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
				$codeAlphabet .= "0123456789";
				$max = strlen($codeAlphabet);
				for ($i = 0; $i < $length; $i++) {
					$token .= $codeAlphabet[random_int(0, $max - 1)];
				}
				return $token;
			}
		endif;
		/* Home tab setting */
		function askme_home_setting($askme_home_tabs, $the_page_id)
		{
			$question_bump = askme_options("question_bump");
			$active_points = askme_options("active_points");
			include locate_template("includes/slugs.php");
			if (isset($askme_home_tabs) && is_array($askme_home_tabs)) {
				$i_count = -1;
				while ($i_count < count($askme_home_tabs)) {
					$array_values_tabs = array_values($askme_home_tabs);
					if ((isset($array_values_tabs[$i_count]["value"]) && $array_values_tabs[$i_count]["value"] != "" && $array_values_tabs[$i_count]["value"] != "0") || (isset($array_values_tabs[$i_count]["cat"]) && $array_values_tabs[$i_count]["cat"] == "yes")) {
						$get_i = $i_count;
						if (isset($array_values_tabs[$i_count]["cat"]) && $array_values_tabs[$i_count]["cat"] == "yes") {
							$home_tabs_keys = array_keys($askme_home_tabs);
							$first_one = $askme_home_tabs[$home_tabs_keys[$i_count]]["value"];
							$get_term = get_term_by('id', $first_one, ask_question_category);
							$first_one = (isset($get_term->slug) ? $get_term->slug : $first_one);
							if ($first_one == 0 || $first_one === "q-0") {
								$first_one = "all";
							}
							$get_i = "none";
						}
						break;
					}
					$i_count++;
				}

				if (isset($get_i) && $get_i !== "none") {
					$array_keys_tabs = array_keys($askme_home_tabs);
					$first_one = $array_keys_tabs[$get_i];
					if ($first_one == "recent-questions") {
						$first_one = $recent_questions_slug;
					} else if ($first_one == "most-answers") {
						$first_one = $most_answers_slug;
					} else if ($first_one == "answers") {
						$first_one = $answers_slug;
					} else if ($first_one == "no-answers") {
						$first_one = $no_answers_slug;
					} else if ($first_one == "most-visit") {
						$first_one = $most_visit_slug;
					} else if ($first_one == "most-vote") {
						$first_one = $most_vote_slug;
					} else if ($first_one == "recent-posts") {
						$first_one = $recent_posts_slug;
					} else if ($question_bump == 1 && $active_points == 1 && $first_one == "question-bump") {
						$first_one = $question_bump_slug;
					}
					do_action("askme_home_page_tabs", $first_one);
				}

				if (isset($_GET["tabs"]) && $_GET["tabs"] != "") {
					$first_one = esc_html($_GET["tabs"]);
				}
			}

			return (isset($first_one) && $first_one != "" ? $first_one : "");
		}
		/* Home tabs */
		if (!function_exists('askme_home_tabs')) :
			function askme_home_tabs($askme_home_tabs, $first_one, $page_template = "", $the_page_id = 0)
			{ ?>
            <div class="wrap-tabs">
                <div class="menu-tabs">
                    <ul class="menu flex menu-tabs-desktop">
                        <?php askme_home_tab_list($askme_home_tabs, $first_one, $page_template, "li", $the_page_id); ?>
                    </ul>
                </div><!-- End menu-tabs -->
            </div><!-- End wrap-tabs -->
            <?php }
		endif;
		if (!function_exists('askme_home_tab_list')) :
			function askme_home_tab_list($askme_home_tabs, $first_one, $page_template = "", $list_child = "li", $the_page_id = 0)
			{
				$question_bump = askme_options("question_bump");
				$active_points = askme_options("active_points");
				include locate_template("includes/slugs.php");
				$last_url = array();
				foreach ($askme_home_tabs as $key => $value) {
					if ((isset($askme_home_tabs[$key]["sort"]) && isset($askme_home_tabs[$key]["value"]) && $askme_home_tabs[$key]["value"] != "" && $askme_home_tabs[$key]["value"] != "0") || (isset($askme_home_tabs[$key]["value"]) && isset($askme_home_tabs[$key]["cat"]))) {
						if (isset($askme_home_tabs[$key]["value"]) && isset($askme_home_tabs[$key]["cat"])) {
							if ($askme_home_tabs[$key]["value"] > 0) {
								$get_tax = get_term_by('id', $askme_home_tabs[$key]["value"], ask_question_category);
								if (isset($get_tax->term_id) && $get_tax->term_id > 0) {
									$last_url[$key]["link"] = $get_tax->slug;
									$category_icon = get_term_meta($askme_home_tabs[$key]["value"], prefix_terms . "category_icon", true);
									if ($category_icon != "") {
										$last_url[$key]["class"] = $category_icon;
									}
								} else {
									$last_url[$key]["link"] = "all";
								}
							} else {
								$last_url[$key]["link"] = "all";
							}
						} else {
							if ($key == "recent-questions") {
								$last_url[$key]["link"] = $recent_questions_slug;
								$last_url[$key]["link-2"] = $key;
								$last_url[$key]["class"] = "icon-book-open";
							} else if ($key == "most-answers") {
								$last_url[$key]["link"] = $most_answers_slug;
								$last_url[$key]["link-2"] = $key;
								$last_url[$key]["class"] = "icon-chat";
							} else if ($key == "answers") {
								$last_url[$key]["link"] = $answers_slug;
								$last_url[$key]["link-2"] = $key;
								$last_url[$key]["class"] = "icon-comment";
							} else if ($key == "no-answers") {
								$last_url[$key]["link"] = $no_answers_slug;
								$last_url[$key]["link-2"] = $key;
								$last_url[$key]["class"] = "icon-traffic-cone";
							} else if ($key == "most-visit") {
								$last_url[$key]["link"] = $most_visit_slug;
								$last_url[$key]["link-2"] = $key;
								$last_url[$key]["class"] = "icon-eye";
							} else if ($key == "most-vote") {
								$last_url[$key]["link"] = $most_vote_slug;
								$last_url[$key]["link-2"] = $key;
								$last_url[$key]["class"] = "icon-chart-bar";
							} else if ($key == "recent-posts") {
								$last_url[$key]["link"] = $recent_posts_slug;
								$last_url[$key]["link-2"] = $key;
								$last_url[$key]["class"] = "icon-newspaper";
							} else if ($question_bump == 1 && $active_points == 1 && $key == "question-bump") {
								$last_url[$key]["link"] = $question_bump_slug;
								$last_url[$key]["link-2"] = $key;
								$last_url[$key]["class"] = "icon-heart";
							}
							$last_url = apply_filters("askme_filter_home_page_tabs_last_url", (isset($last_url) ? $last_url : $key), $key);
							do_action("askme_home_page_tabs_list", $key, (isset($last_url) ? $last_url : $key));
						}
					}
				}
				if (isset($last_url) && is_array($last_url) && !empty($last_url)) {
					foreach ($last_url as $last_key => $last_value) {
						$get_url = esc_url(add_query_arg("tabs", esc_attr($last_value["link"]), ($page_template != "" ? get_the_permalink($page_template) : home_url('/'))));
						if ($list_child == "li") { ?>
<li class="tab">
    <a<?php echo (isset($first_one) && $first_one != "" && $first_one == $last_value["link"] ? " class='current'" : "") ?>
        href="<?php echo esc_url($get_url) ?>">
        <?php } else { ?>
        <option<?php echo (isset($first_one) && $first_one != "" && $first_one == $last_value["link"] ? " selected='selected'" : "") ?>
            value="<?php echo esc_url($get_url) ?>">
            <?php }
						if (isset($last_value["link"]) && (isset($askme_home_tabs[$last_value["link"]]["sort"]) || (isset($last_value["link-2"]) && isset($askme_home_tabs[$last_value["link-2"]]["sort"])))) {
							if ($last_value["link"] == $recent_questions_slug) {
								esc_html_e("Recent Questions", "vbegy");
							} else if ($last_value["link"] == $most_answers_slug) {
								esc_html_e("Most Answered", "vbegy");
							} else if ($last_value["link"] == $answers_slug) {
								esc_html_e("Answers", "vbegy");
							} else if ($last_value["link"] == $no_answers_slug) {
								esc_html_e("No Answers", "vbegy");
							} else if ($last_value["link"] == $most_visit_slug) {
								esc_html_e("Most Visited", "vbegy");
							} else if ($last_value["link"] == $most_vote_slug) {
								esc_html_e("Most Voted", "vbegy");
							} else if ($last_value["link"] == $recent_posts_slug) {
								esc_html_e("Recent Posts", "vbegy");
							} else if ($question_bump == 1 && $active_points == 1 && $last_value["link"] == $question_bump_slug) {
								esc_html_e("Bump Question", "vbegy");
							}
							do_action("askme_home_page_tabs_text", $last_value["link"]);
						} else if (isset($askme_home_tabs[$last_key]["value"])) {
							if (is_numeric($askme_home_tabs[$last_key]["value"]) && $askme_home_tabs[$last_key]["value"] > 0) {
								$get_tax = get_term_by('id', $askme_home_tabs[$last_key]["value"], ask_question_category);
								if (isset($get_tax->term_id) && $get_tax->term_id > 0) {
									echo esc_attr($get_tax->name);
								}
							} else {
								esc_html_e("Show All Categories", "vbegy");
							}
						}
						if ($list_child == "li") { ?>
            </a>
</li>
<?php } else { ?>
</option>
<?php }
					}
				}
			}
		endif;
		/* Clean HTML codes */
		add_filter('comment_text', 'askme_remove_html_codes', 0);
		if (!function_exists('askme_remove_html_codes')) :
			function askme_remove_html_codes($comment_text)
			{
				$comment_text = do_shortcode($comment_text);
				add_filter('embed_oembed_discover', '__return_false', 999);
				$comment_text = $GLOBALS['wp_embed']->autoembed($comment_text);
				remove_filter('embed_oembed_discover', '__return_false', 999);
				return $comment_text;
			}
		endif;
		/* Get comments by post */
		function get_all_comments_of_post_type($post_type = null)
		{
			global $wpdb;
			$post_type = (is_array($post_type) ? $post_type : ($post_type != "" ? $post_type : "post"));
			$custom_post_type = "AND (";
			if (is_array($post_type)) {
				$key = 0;
				foreach ($post_type as $value) {
					if ($key != 0) {
						$custom_post_type .= " OR ";
					}
					$custom_post_type .= "post_type = '$value'";
					$key++;
				}
			} else {
				$custom_post_type .= "post_type = '$post_type'";
			}
			$custom_post_type .= ")";
			$cc = $wpdb->get_var("SELECT COUNT(comment_ID)
		FROM $wpdb->comments
		WHERE comment_post_ID in (
		SELECT ID 
		FROM $wpdb->posts 
		WHERE post_status = 'publish'
		" . $custom_post_type . " )
		AND comment_approved = '1'
	");
			return $cc;
		}
		/* Paged */
		function askme_paged()
		{
			if (get_query_var("paged") != "") {
				$paged = (int)get_query_var("paged");
			} else if (get_query_var("page") != "") {
				$paged = (int)get_query_var("page");
			}
			if (get_query_var("paged") > get_query_var("page") && get_query_var("paged") > 0) {
				$paged = (int)get_query_var("paged");
			}
			if (get_query_var("page") > get_query_var("paged") && get_query_var("page") > 0) {
				$paged = (int)get_query_var("page");
			}
			if (!isset($paged) || (isset($paged) && $paged <= 1)) {
				$paged = 1;
			}
			return $paged;
		}
		/* Delete posts */
		function askme_delete_posts_nonce($post, $data = array())
		{
			$askme_delete_nonce = (isset($data["askme_delete_nonce"]) ? esc_html($data["askme_delete_nonce"]) : "");
			if (wp_verify_nonce($askme_delete_nonce, 'askme_delete_nonce')) {
				askme_delete_posts($post, $data);
				return "done";
			}
		}
		function askme_delete_posts($post, $data = array())
		{
			$post_author = $post->post_author;
			$post_type = $post->post_type;
			if ($post_author > 0) {
				askme_notifications_activities($post_author, "", "", "", "", "delete_question", "activities", "", ($post_type == ask_questions_type || $post_type == ask_asked_questions_type ? ask_questions_type : ""));
			}
			$delete_trush = askme_options("delete_" . $post_type);
			wp_delete_post($post->ID, ($delete_trush == "trash" ? false : true));
		} ?>