<?php
global $post,$blog_style,$vbegy_sidebar_all;
$vbegy_what_post = askme_post_meta('vbegy_what_post','select',$post->ID);
$video_mp4 = askme_post_meta('vbegy_video_mp4','text',$post->ID);
$video_m4v = askme_post_meta('vbegy_video_m4v','text',$post->ID);
$video_webm = askme_post_meta('vbegy_video_webm','text',$post->ID);
$video_ogv = askme_post_meta('vbegy_video_ogv','text',$post->ID);
$video_wmv = askme_post_meta('vbegy_video_wmv','text',$post->ID);
$video_flv = askme_post_meta('vbegy_video_flv','text',$post->ID);
$video_image = askme_post_meta('vbegy_video_image','upload',$post->ID);
$video_mp4 = (isset($video_mp4) && $video_mp4 != ""?" mp4='".$video_mp4."'":"");
$video_m4v = (isset($video_m4v) && $video_m4v != ""?" m4v='".$video_m4v."'":"");
$video_webm = (isset($video_webm) && $video_webm != ""?" webm='".$video_webm."'":"");
$video_ogv = (isset($video_ogv) && $video_ogv != ""?" ogv='".$video_ogv."'":"");
$video_wmv = (isset($video_wmv) && $video_wmv != ""?" wmv='".$video_wmv."'":"");
$video_flv = (isset($video_flv) && $video_flv != ""?" flv='".$video_flv."'":"");
$video_image = (isset($video_image) && $video_image != ""?" poster='".$video_image."'":"");
$featured_image = askme_options("featured_image");
if ($vbegy_what_post == "image" || $vbegy_what_post == "video" || $vbegy_what_post == "lightbox") {
	if ($vbegy_sidebar_all == "full") {
		if ($vbegy_what_post == "image" || $vbegy_what_post == "lightbox") {
			if (has_post_thumbnail()) {
				$show_featured_image = 1;
				if ($featured_image == 1 && is_singular()) {
					$show_featured_image = 0;
				}
				if ($show_featured_image == 1) {
					if ($blog_style == "blog_2") {
						if ($vbegy_what_post == "lightbox") {
							echo askme_resize_img(250,160,$img_lightbox = "lightbox");
						}else {
							echo askme_resize_img(250,160);
						}
					}else {
						if ($vbegy_what_post == "lightbox") {
							echo askme_resize_img(1098,590,$img_lightbox = "lightbox");
						}else {
							echo askme_resize_img(1098,590);
						}
					}
				}
			}
		}else if ($vbegy_what_post == "video") {
			if ($video_type == "html5") {
				echo do_shortcode('[video'.$video_mp4.$video_m4v.$video_webm.$video_ogv.$video_wmv.$video_flv.$video_image.']');
	    	}else {
		    	echo '<iframe height="600" src="'.$type.'"></iframe>';
	    	}
		}
	}else {
		if ($vbegy_what_post == "image" || $vbegy_what_post == "lightbox") {
			if (has_post_thumbnail()) {
				$show_featured_image = 1;
				if ($featured_image == 1 && is_singular()) {
					$show_featured_image = 0;
				}
				if ($show_featured_image == 1) {
					if ($blog_style == "blog_2") {
						if ($vbegy_what_post == "lightbox") {
							echo askme_resize_img(250,190,$img_lightbox = "lightbox");
						}else {
							echo askme_resize_img(250,190);
						}
					}else {
						if ($vbegy_what_post == "lightbox") {
							echo askme_resize_img(806,440,$img_lightbox = "lightbox");
						}else {
							echo askme_resize_img(806,440);
						}
					}
				}
			}
		}else if ($vbegy_what_post == "video") {
			if ($video_type == "html5") {
				echo do_shortcode('[video'.$video_mp4.$video_m4v.$video_webm.$video_ogv.$video_wmv.$video_flv.$video_image.']');
			}else {
	    		echo '<iframe height="450" src="'.$type.'"></iframe>';
	    	}
		}
	}
}else if ($vbegy_what_post == "google" || $vbegy_what_post == "slideshow") {
	if ($vbegy_what_post == "google") {
		echo $vbegy_google;
	}else if ($vbegy_what_post == "slideshow") {
		if ($vbegy_sidebar_all == "full") {
	    	if ($blog_style == "blog_2") {
	    		$img_width = 250;
	    		$img_height = 160;
	    	}else {
	    		$img_width = 1098;
	    		$img_height = 590;
	    	}
	    }else {
	    	if ($blog_style == "blog_2") {
	    	    $img_width = 250;
	    		$img_height = 190;
	    	}else {
	    		$img_width = 806;
	    		$img_height = 440;
	    	}
	    }
		if ($vbegy_slideshow_type == "custom_slide") {
			$slideshow_post = get_post_meta($post->ID,"vbegy_slideshow_post",true);
			if (isset($slideshow_post) && is_array($slideshow_post)) {?>
			    <div class="flexslider blog_silder margin_b_20 post-img">
			    	<ul class="slides">
				    	<?php foreach ($slideshow_post as $key_slide => $value_slide) {
			    			if (isset($value_slide['image_url']['id']) && (int)$value_slide['image_url']['id'] != "") {
				    		    $src = wp_get_attachment_image_src($value_slide['image_url']['id'],'full');
				    		    $src = $src[0];
				    		    if (isset($src) && $src != "") {
			    		    	    $src = askme_resize_by_url_img(esc_url($src),$img_width,$img_height,"",get_the_title($value_slide['image_url']['id']));?>
					    		    <li>
						    		    <?php if ($value_slide['slide_link'] != "") {echo "<a class='slide_link' href='".esc_url($value_slide['slide_link'])."'>";}
							    	        echo ($src);
						    	        if ($value_slide['slide_link'] != "") {echo "</a>";}?>
					    	        </li>
					    		<?php }
				    		}
			    		}?>
			    	</ul>
			    </div>
			<?php }
		}else if ($vbegy_slideshow_type == "upload_images") {
			$upload_images = get_post_meta($post->ID,"vbegy_upload_images",true);
			if (isset($upload_images) && is_array($upload_images)) {?>
			    <div class="flexslider blog_silder margin_b_20 post-img">
			    	<ul class="slides">
				    	<?php foreach ($upload_images as $att) {
				    	    $src = wp_get_attachment_image_src($att,'full');
				    	    if (isset($src[0])) {
				    	    	$src = $src[0];?>
				    		    <li>
				    	    	    <?php $src = askme_resize_by_url_img(esc_url($src),$img_width,$img_height,"",get_the_title($att));
				    	    	    echo ($src);?>
				    	        </li>
				    	    <?php }
				    	}?>
				    </ul>
			    </div>
			<?php }
		}
	}
}else {
	if (has_post_thumbnail()) {
		$show_featured_image = 1;
		if ($featured_image == 1 && is_singular()) {
			$show_featured_image = 0;
		}
		if ($show_featured_image == 1) {
			if ($vbegy_sidebar_all == "full") {
				if ($blog_style == "blog_2") {
					echo askme_resize_img(250,160);
				}else {
					echo askme_resize_img(1098,590);
				}
			}else {
				if ($blog_style == "blog_2") {
					echo askme_resize_img(250,190);
				}else {
					echo askme_resize_img(806,440);
				}
			}
		}
	}
}
?>