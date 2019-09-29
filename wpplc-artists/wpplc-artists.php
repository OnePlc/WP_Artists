<?php
/**
 * Plugin main file.
 *
 * @package   OnePlace\Artists
 * @copyright 2019 OnePlace
 * @license   https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html GNU General Public License, version 2
 * @link      https://1plc.ch
 *
 * @wordpress-plugin
 * Plugin Name: Artist Plugin by OnePlace
 * Plugin URI:  https://1plc.ch/wordpress-plugins/artist
 * Description: Artist Plugin is for Creative Web Pages for all kinds of Artists with Portfolios.
 * Version:     0.1-dev
 * Author:      Nathanael Kammermann
 * Author URI:  https://wordpress.1plc.ch
 * License:     GNU General Public License, version 2
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * Text Domain: wpplc-artist
 */

// Some basic security
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Define global constants
define( 'WPPLC_ARTIST_VERSION', '0.1-dev' );
define( 'WPPLC_ARTIST_PLUGIN_MAIN_FILE', __FILE__ );
define( 'WPPLC_ARTIST_PLUGIN_MAIN_DIR', __DIR__ );

/**
 * Handles plugin activation.
 *
 * Throws an error if the plugin is activated on an older version than PHP 5.4.
 *
 * @access private
 *
 * @param bool $network_wide Whether to activate network-wide.
 */
function wpplcartist_activate_plugin( $network_wide ) {
    if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
        wp_die(
            esc_html__( 'Site Kit requires PHP version 5.4.', 'wpplc-artist' ),
            esc_html__( 'Error Activating', 'wpplc-artist' )
        );
    }
    /**
     * Multisite support
    if ( $network_wide ) {
        return;
    } **/
    //do_action( 'googlesitekit_activation', $network_wide );
}

register_activation_hook( __FILE__, 'wpplcartist_activate_plugin' );

if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
    require_once plugin_dir_path( __FILE__ ) . 'includes/Plugin.php';
}