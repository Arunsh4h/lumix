<?php
$twitter_icon_f = askme_options("twitter_icon_f");
$facebook_icon_f = askme_options("facebook_icon_f");
$tiktok_icon_f = askme_options("tiktok_icon_f");
$youtube_icon_f = askme_options("youtube_icon_f");
$skype_icon_f = askme_options("skype_icon_f");
$flickr_icon_f = askme_options("flickr_icon_f");
$instagram_icon_f = askme_options("instagram_icon_f");
$linkedin_icon_f = askme_options("linkedin_icon_f");
$rss_icon_f = askme_options("rss_icon_f");
$tooltip = (isset($footer_social)?"tooltip-n":"tooltip-s");
?>
<ul>
	<?php if ($twitter_icon_f) {?>
	<li class="twitter"><a target="_blank" original-title="<?php _e("Twitter","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="<?php echo $twitter_icon_f?>"><i class="social_icon-twitter font17"></i></a></li>
	<?php }
	if ($facebook_icon_f) {?>
		<li class="facebook"><a target="_blank" original-title="<?php _e("Facebook","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="<?php echo $facebook_icon_f?>"><i class="social_icon-facebook font17"></i></a></li>
	<?php }
	if ($tiktok_icon_f) {?>
		<li class="tiktok"><a target="_blank" original-title="<?php _e("TikTok","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="<?php echo $tiktok_icon_f?>"><i class="fab fa-tiktok font17"></i></a></li>
	<?php }
	if ($youtube_icon_f) {?>
		<li class="youtube"><a target="_blank" original-title="<?php _e("Youtube","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="<?php echo $youtube_icon_f?>"><i class="social_icon-youtube font17"></i></a></li>
	<?php }
	if ($skype_icon_f) {?>
		<li class="skype"><a target="_blank" original-title="<?php _e("Skype","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="skype:<?php echo $skype_icon_f?>?call"><i class="social_icon-skype font17"></i></a></li>
	<?php }
	if ($flickr_icon_f) {?>
		<li class="flickr"><a target="_blank" original-title="<?php _e("Flickr","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="<?php echo $flickr_icon_f?>"><i class="social_icon-flickr font17"></i></a></li>
	<?php }
	if ($instagram_icon_f) {?>
		<li class="instagram"><a target="_blank" original-title="<?php _e("Instagram","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="<?php echo $instagram_icon_f?>"><i class="social_icon-instagram font17"></i></a></li>
	<?php }
	if ($linkedin_icon_f) {?>
		<li class="linkedin"><a target="_blank" original-title="<?php _e("Linkedin","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="<?php echo $linkedin_icon_f?>"><i class="social_icon-linkedin font17"></i></a></li>
	<?php }
	if ($rss_icon_f == 1) {?>
		<li class="rss"><a original-title="<?php _e("Rss","vbegy")?>" class="<?php echo esc_attr($tooltip)?>" href="<?php echo (askme_options("rss_icon_f_other") != ""?askme_options("rss_icon_f_other"):bloginfo('rss2_url'));?>"><i class="social_icon-rss font17"></i></a></li>
	<?php }?>
</ul>