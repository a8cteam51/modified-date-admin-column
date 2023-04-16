<?php

defined( 'ABSPATH' ) || exit;

use WPcomSpecialProjects\MDAC\Admin;

/**
 * Returns the plugin's admin class instance.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  Plugin
 */
function wpcomsp_mdac_get_admin_instance(): Admin {
	return Admin::init();
}

/**
 * Returns the plugin's slug.
 *
 * @since   1.0.0
 * @version 1.0.0
 *
 * @return  string
 */
function wpcomsp_mdac_get_plugin_slug(): string {
	return sanitize_key( WPCOMSP_MDAC_METADATA['TextDomain'] );
}
