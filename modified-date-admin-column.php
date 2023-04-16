<?php
/**
 * The Team51 Plugin MDAC bootstrap file.
 *
 * @since       1.0.0
 * @version     1.0.0
 * @author      WordPress.com Special Projects
 * @license     GPL-3.0-or-later
 *
 * @noinspection    ALL
 *
 * @wordpress-plugin
 * Plugin Name:             Modified Date Admin Column
 * Plugin URI:              https://github.com/a8cteam51/modified-date-admin-column
 * Description:             Adds a modified date column to the admin post list. Also adds the author display name to published and modified columns.
 * Version:                 1.0.0
 * Requires at least:       6.2
 * Tested up to:            6.2
 * Requires PHP:            8.0
 * Author:                  WordPress.com Special Projects
 * Author URI:              https://wpspecialprojects.wordpress.com
 * License:                 GPL v3 or later
 * License URI:             https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:             wpcomsp-mdac
 * Domain Path:             /languages
 **/

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
function_exists( 'get_plugin_data' ) || require_once ABSPATH . 'wp-admin/includes/plugin.php';
define( 'WPCOMSP_MDAC_METADATA', get_plugin_data( __FILE__, false, false ) );

define( 'WPCOMSP_MDAC_BASENAME', plugin_basename( __FILE__ ) );
define( 'WPCOMSP_MDAC_PATH', plugin_dir_path( __FILE__ ) );

// Load plugin translations so they are available even for the error admin notices.
add_action(
	'init',
	static function() {
		load_plugin_textdomain(
			WPCOMSP_MDAC_METADATA['TextDomain'],
			false,
			dirname( WPCOMSP_MDAC_BASENAME ) . WPCOMSP_MDAC_METADATA['DomainPath']
		);
	}
);

// Load the autoloader.
if ( ! is_file( WPCOMSP_MDAC_PATH . '/vendor/autoload.php' ) ) {
	add_action(
		'admin_notices',
		static function() {
			$message      = __( 'It seems like <strong>Modified Date Admin Column</strong> is corrupted. Please reinstall!', 'wpcomsp-mdac' );
			$html_message = wp_sprintf( '<div class="error notice wpcomsp-mdac-error">%s</div>', wpautop( $message ) );
			echo wp_kses_post( $html_message );
		}
	);
	return;
}
require_once WPCOMSP_MDAC_PATH . '/vendor/autoload.php';

// Initialize the plugin if system requirements check out.
$wpcomsp_mdac_requirements = validate_plugin_requirements( WPCOMSP_MDAC_BASENAME );
define( 'WPCOMSP_MDAC_REQUIREMENTS', $wpcomsp_mdac_requirements );

if ( $wpcomsp_mdac_requirements instanceof WP_Error ) {
	add_action(
		'admin_notices',
		static function() use ( $wpcomsp_mdac_requirements ) {
			$html_message = wp_sprintf( '<div class="error notice wpcomsp-mdac-error">%s</div>', $wpcomsp_mdac_requirements->get_error_message() );
			echo wp_kses_post( $html_message );
		}
	);
} else {
	require_once WPCOMSP_MDAC_PATH . 'functions.php';
	add_action( 'admin_init', array( wpcomsp_mdac_get_admin_instance(), 'define_hooks' ) );
}
