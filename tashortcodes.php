<?php
/**
 *
 * @package   TAShortcodes
 * @author    Alain Sanchez <luka.ghost@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.linkedin.com/in/mrbrazzi/
 * @copyright 2014 Alain Sanchez
 *
 * @wordpress-plugin
 * Plugin Name:       TA Shortcodes
 * Plugin URI:       http://www.todoapuestas.org
 * Description:       A set of useful shortcodes for todoapuestar.org sites's.
 * Version:           1.0.0
 * Author:       Alain Sanchez
 * Author URI:       http://www.linkedin.com/in/mrbrazzi/
 * Text Domain:       tashortcodes
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-tashortcodes.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'TAShortcodes', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TAShortcodes', 'deactivate' ) );

/*
 *
 */
add_action( 'plugins_loaded', array( 'TAShortcodes', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-tashortcodes-admin.php' );
	add_action( 'plugins_loaded', array( 'TAShortcodes_Admin', 'get_instance' ) );

}

//TAShortcodes::get_instance()->remote_sync();