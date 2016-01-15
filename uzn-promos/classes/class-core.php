<?php
/**
 * Translation
 */
function uzn_promos_languages()
{
	load_plugin_textdomain('uzn_promos', false, dirname(UZN_PROMOS_PLUGIN_BASENAME));
}
add_action('init', 'uzn_promos_languages');
