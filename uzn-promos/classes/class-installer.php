<?php
/**
 * Install database tables
 */
global $uzn_promos_db_version;
$uzn_promos_db_version = '1.0';

function uzn_promos_install()
{
    global $wpdb;
    global $uzn_promos_db_version;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	$sql =
		"
		CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}uzn_promos` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(128) NOT NULL,
		  `description` text,
		  `image` varchar(250) DEFAULT NULL,
		  `permalink` varchar(128) NOT NULL,
		  `text_success` text,
		  `count_entries` int(11) NOT NULL DEFAULT '0',
		  `status` tinyint(1) NOT NULL DEFAULT '0',
		  `date_added` datetime NOT NULL,
		  `date_updated` datetime DEFAULT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `permalink` (`permalink`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";
	dbDelta($sql);

	$sql =
		"
		CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}uzn_promos_entries` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `promos_id` int(11) unsigned NOT NULL,
		  `user_id` bigint(20) unsigned NOT NULL,
		  `email` varchar(120) DEFAULT NULL,
		  `first_name` varchar(64) DEFAULT NULL,
		  `last_name` varchar(64) DEFAULT NULL,
		  `status` tinyint(1) NOT NULL DEFAULT '0',
		  `date_added` datetime NOT NULL,
		  `date_updated` datetime DEFAULT NULL,
		  PRIMARY KEY  (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
		";
	dbDelta($sql);

    add_option('uzn_promos_db_version', $uzn_promos_db_version);
}
register_activation_hook(UZN_PROMOS_PLUGIN_BASENAME, 'uzn_promos_install');

/**
 * Upgrade
 */
function uzn_promos_install_update() {
    $installed_ver = get_option('uzn_promos_db_version');
    if ($installed_ver != $uzn_promos_db_version) {
        $sql = "";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        update_option('uzn_promos_db_version', $uzn_promos_db_version);
    }
}

/**
 * Insert data (not used)
 */
function uzn_promos_install_data()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'uzn_promos';

    $wpdb->insert($table_name, array(
        'name' => 'Test',
        'permalink' => 'test',
        'status' => 1
    ));
}
// not used
// register_activation_hook(UZN_PROMOS_PLUGIN_BASENAME, 'uzn_promos_insert_data');

/**
 * Update (not used)
 */
function uzn_promos_update_db_check()
{
    global $uzn_promos_db_version;
    if (get_site_option('uzn_promos_db_version') != $uzn_promos_db_version) {
        uzn_promos_install();
    }
}
// not used
// add_action('plugins_loaded', 'uzn_promos_update_db_check');
