<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.nicolabavaro.it
 * @since             1.0.0
 * @package           Ultimate_File_Sharing
 *
 * @wordpress-plugin
 * Plugin Name:       Ultimate File Sharing
 * Plugin URI:        https://www.nicolabavaro.it
 * Description:       Questo plugin permette la condivisione di files verso utenti registrati o gruppi di utenti registrati. Personalizzazione per Donata.
 * Version:           1.0.0
 * Author:            Nicola Bavaro
 * Author URI:        https://www.informaticabattistini.it
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ultimate-file-sharing
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ultimate-file-sharing-activator.php
 */
function activate_Ultimate_File_Sharing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-file-sharing-activator.php';
    Ultimate_File_Sharing_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ultimate-file-sharing-deactivator.php
 */
function deactivate_Ultimate_File_Sharing() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-file-sharing-deactivator.php';
    Ultimate_File_Sharing_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Ultimate_File_Sharing' );
register_deactivation_hook( __FILE__, 'deactivate_Ultimate_File_Sharing' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ultimate-file-sharing.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Ultimate_File_Sharing() {

	$plugin = new Ultimate_File_Sharing();
	$plugin->run();

}
run_Ultimate_File_Sharing();
