<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://mitango.app
 * @since             1.0.0
 * @package           Linepay
 *
 * @wordpress-plugin
 * Plugin Name:       Linepay Woocommerce
 * Plugin URI:        https://mitango.app
 * Description:       Linepay plugin for Woocommerce
 * Version:           1.0.0
 * Author:            COQUARD Cyrille
 * Author URI:        https://mitango.app
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       linepay
 * Domain Path:       /languages
 */
use Mitango\LinepayWoocommerce\Plugin;
use Mitango\LinepayWoocommerce\Dependencies\League\Container\Container;

function linepay_woocommerce_init() {
    if (! defined('LINEPAY_WOOCOMMERCE_PLUGIN_ROOT')) {
        define( 'LINEPAY_WOOCOMMERCE_PLUGIN_ROOT', __DIR__ . DIRECTORY_SEPARATOR );
    }
    require_once __DIR__ . '/vendor/autoload.php';
    $plugin = new Plugin( new Container());
    $plugin->load();
}

add_action( 'plugins_loaded', 'linepay_woocommerce_init' );