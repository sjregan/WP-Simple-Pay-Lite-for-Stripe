<?php
/**
 * Plugin Name: WP Simple Pay Lite for Stripe
 * Plugin URI: http://wpsimplepay.com
 * Description: Add highly optimized Stripe checkout form overlays to your site in a few simple steps.
 * Version: 1.4.0
 * Author: Phil Derksen
 * Author URI: http://philderksen.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/pderksen/WP-Stripe-Checkout
 * Text Domain: sc
 * Domain Path: /languages/
 *
 * @package   SC
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @link      http://wpsimplepay.com
 * @copyright 2014-2015 Phil Derksen
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin requirements.
$stripe_checkout_requires = array(
	'wp'  => '3.9.0',
	'php' => '5.3.0',
	'ext' => array( 'curl', 'mbstring' )
);
// Define constants.
$stripe_checkout_constants = array(
	'SC_REQUIRES'         => serialize( $stripe_checkout_requires ),
	'SC_MAIN_FILE'        => __FILE__,
	'SC_DIR_PATH'         => plugin_dir_path( __FILE__ ),
	'SC_DIR_URL'          => plugin_dir_url( __FILE__ ) ,
	'SC_WEBSITE_BASE_URL' => 'http://wpsimplepay.com/',
);
foreach( $stripe_checkout_constants as $constant => $value ) {
	if ( ! defined( $constant ) ) {
		define( $constant, $value );
	}
}

// Check plugin requirements.
include_once 'stripe-checkout-requirements.php';
$stripe_checkout_requirements = new Stripe_Checkout_Requirements( $stripe_checkout_requires );
if ( $stripe_checkout_requirements->pass() === false ) {

	$stripe_checkout_fails = $stripe_checkout_requirements->failures();

	if ( isset( $stripe_checkout_fails['wp'] ) || isset( $stripe_checkout_fails['php']) ) {

		// Display an admin notice if running old WordPress or PHP.
		function stripe_checkout_plugin_requirements() {
			$required = unserialize( SC_REQUIRES );
			global $wp_version;
			echo '<div class="error"><p>' . sprintf( __( 'Stripe Checkout requires PHP %1$s and WordPress %2$s to function properly. PHP version found: %3$s. WordPress installed version: %4$s. Please upgrade to meet the minimum requirements.', 'sc' ), $required['php'], $required['wp'], PHP_VERSION, $wp_version ) . '</p></div>';
		}
		add_action( 'admin_notices', 'stripe_checkout_plugin_requirements' );
	}

	if ( ! isset( $stripe_checkout_fails['ext'] ) ) {

		// Display a notice if extensions are not found.
		function stripe_checkout_plugin_extensions() {
			$required = unserialize( SC_REQUIRES );
			$extensions = '<code>' . implode( ', ', $required['ext'] ) . '</code>';
			echo '<div class="error"><p>' . sprintf( __( 'Stripe Checkout requires the following PHP extensions to work: %s. Please make sure they are installed or contact your host.', 'sc' ), $extensions ) . '</p></div>';
		}
		add_action( 'admin_notices', 'stripe_checkout_plugin_extensions' );
	}

	// Halt the rest of the plugin execution if PHP check fails or extensions not found.
	if ( isset( $stripe_checkout_fails['php'] ) || isset( $stripe_checkout_fails['ext'] ) ) {
		return;
	}

}

// Load the plugin.
require_once SC_DIR_PATH . 'classes/class-stripe-checkout.php';

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( SC_MAIN_FILE, array( 'Stripe_Checkout', 'activate' ) );

// Set up global holding the base class instance so we can easily use it throughout
global $base_class;
$base_class = Stripe_Checkout::get_instance();
