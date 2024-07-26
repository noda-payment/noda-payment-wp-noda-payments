<?php

declare(strict_types=1);

/**
 * Plugin Information
 *
 * @package           Nodapay/Button
 *
 * Plugin Name:       Noda.live Payment Button
 * Plugin URI:        https://noda.live
 * Description:       Effortlessly accept and manage user payments using the reliable and secure Noda payment gateway solution.
 * Version:           1.1.1
 * Requires at least: 5.3.0
 * Requires PHP:      7.0.3
 * Author:            Noda.Live
 * Author URI:        https://noda.live
 * Developer:         Noda Dev
 * Developer URI:     https://woo.noda.live
 * Text Domain:       noda
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$basename   = plugin_basename( __FILE__ );
$autoloader = trailingslashit( __DIR__ ) . 'vendor/autoload.php';

// Autoload.
if ( file_exists( $autoloader ) ) {
	require_once $autoloader;
}

// Initialise plugin.
$noda_button_plugin = new NodaPay\Button\Instance(
	$basename,
	new \NodaPay\Button\AdminNotice(),
	new \NodaPay\Button\Verificator(),
	new \NodaPay\Button\NodapayButton(),
	new \NodaPay\Button\NodapaySettings(),
	new \NodaPay\Button\NodapayAppApi(),
	new \NodaPay\Button\NodaPaymentNotification()
);

$noda_button_plugin->initButton();
$noda_button_plugin->initAdminSettings();
$noda_button_plugin->initWPApi();

// Activation hook.
register_activation_hook( __FILE__, [ $noda_button_plugin, 'activate' ] );

// Deactivation hook.
register_deactivation_hook( __FILE__, [ $noda_button_plugin, 'deactivate' ] );

// Uninstall hook.
register_uninstall_hook( __FILE__, [ \NodaPay\Button\Instance::class, 'uninstall' ] );

// Load the payment gateway.
add_action( 'plugins_loaded', [ $noda_button_plugin, 'init' ], 100, 0 );
