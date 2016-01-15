<?php
/*
Plugin Name: Promos
Description: Run promos, sweepstakes, giveaways and collect user data. Use custom HTML template. Save data into separate tables.
Plugin URI:
Author URI:
Author: RMAN UZN, 2015, radone@gmail.com
License:
Version: 1.0
*/

if (!defined('ABSPATH')) exit;

define('UZN_PROMOS_FILE', __FILE__);
define('UZN_PROMOS_PLUGIN_BASENAME', plugin_basename( UZN_PROMOS_FILE ));
define('UZN_PROMOS_PLUGIN_DIR_URL', plugin_dir_url(UZN_PROMOS_FILE));
define('UZN_PROMOS_PLUGIN_DIR_PATH', plugin_dir_path(UZN_PROMOS_FILE));

// functions that handle registration
// wp_ajax actions need to be defined on plugin main page
include_once(UZN_PROMOS_PLUGIN_DIR_PATH . '/pages/register/register_lib.php');
add_action('wp_ajax_fb_intialize_uzn_promos', 'fb_intialize_uzn_promos');
add_action('wp_ajax_nopriv_fb_intialize_uzn_promos', 'fb_intialize_uzn_promos');

include_once('classes/class-core.php');

if (is_admin()) {
	include_once('classes/class-installer.php');
	include_once('classes/class-admin.php');

	// /promos
	include_once('pages/promos/promos.php');
	include_once('pages/promos/promos_list_table.php');
	include_once('pages/promos/promos_form.php');

	// /promo_entries
	include_once('pages/promo_entries/promo_entries.php');
	include_once('pages/promo_entries/promo_entries_list_table.php');

} else {
	// public/front-end
	include_once('pages/register/register.php');
}

