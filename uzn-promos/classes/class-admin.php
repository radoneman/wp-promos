<?php
/**
 * Register menus/pages and callback functions
 */
function uzn_promos_admin_menu()
{
	add_menu_page(__('Promos', 'uzn_promos'), __('Promos', 'uzn_promos'), 'activate_plugins', 'promos', 'uzn_promos_pages_promos');
	add_submenu_page('promos', __('All Promos', 'uzn_promos'), __('All Promos', 'uzn_promos'), 'activate_plugins', 'promos', 'uzn_promos_pages_promos');
	add_submenu_page('promos', __('Add new', 'uzn_promos'), __('Add new', 'uzn_promos'), 'activate_plugins', 'promos_form', 'uzn_promos_pages_promos_form');
	add_submenu_page(null, __('Promo Entries', 'uzn_promos'), __('Promo Entries', 'uzn_promos'), 'activate_plugins', 'promo_entries', 'uzn_promos_pages_promo_entries');
}

add_action('admin_menu', 'uzn_promos_admin_menu');
