<?php echo "<div class='header-notifications'>";
	$num = askme_count_new_notifications($user_id);
	echo '<a href="'.get_page_link(askme_options('notifications_page')).'" class="notifications_control"><i class="fa fa-bell"></i>
		<span class="numofitems">'.$num.'</span>
	</a>
	<div class="notifications-wrapper">';
		askme_notifications($user_id);
	echo '</div>
</div>';