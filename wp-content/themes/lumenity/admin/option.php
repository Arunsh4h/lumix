<?php
/* Save default options */
$askme_admin_options = new askme_admin_options;
$default_options = $askme_admin_options->get_default_values();
if (!get_option(askme_options)) {
	add_option(askme_options,$default_options);
}
/* Theme old version */
if (askme_theme_version >= 6.4) {
	if (get_option("askme_old_version_done") == "") {
		update_option("askme_old_version",get_option(askme_options));
		update_option("askme_old_version_done",true);
	}
}?>