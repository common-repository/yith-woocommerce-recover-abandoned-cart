<?php
/*
Plugin Name: YITH WooCommerce Recover Abandoned Cart
Description: <code><strong>YITH WooCommerce Recover Abandoned Cart</strong></code> reminds users who did not complete the checkout, so you can recover this lost sale. Recovering abandoned carts increase dramatically the conversion rate of your e-commerce shop. It's perfect if you want to maximise profit. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
Version: 1.4.6
Author: YITH
Author URI: https://yithemes.com/
Text Domain: yith-woocommerce-recover-abandoned-cart
Domain Path: /languages/
WC requires at least: 3.8.0
WC tested up to: 4.6.0
*/

/*
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}


if ( ! defined( 'YITH_YWRAC_DIR' ) ) {
	define( 'YITH_YWRAC_DIR', plugin_dir_path( __FILE__ ) );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWRAC_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWRAC_DIR . 'plugin-fw/init.php';
	yit_maybe_plugin_fw_loader( YITH_YWRAC_DIR );
}



// This version can't be activate if premium version is active  ________________________________________
if ( defined( 'YITH_YWRAC_PREMIUM' ) ) {
	function yith_ywrac_install_free_admin_notice() {
		?>
		<div class="error">
			<p><?php _ex( 'You can\'t activate the free version of YITH WooCommerce Recover Abandoned Cart while you are using the premium one.', 'do not translate plugin name', 'yith-woocommerce-recover-abandoned-cart' ); ?></p>
		</div>
		<?php
	}

	add_action( 'admin_notices', 'yith_ywrac_install_free_admin_notice' );

	deactivate_plugins( plugin_basename( __FILE__ ) );
	return;
}


// Registration hook  ________________________________________
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( ! function_exists( 'yith_ywrac_install_woocommerce_admin_notice' ) ) {
	function yith_ywrac_install_woocommerce_admin_notice() {
		?>
		<div class="error">
			<p><?php _ex( 'YITH WooCommerce Recover Abandoned Cart is enabled but not effective. It requires WooCommerce in order to work.', 'do not translate plugin name', 'yith-woocommerce-recover-abandoned-cart' ); ?></p>
		</div>
		<?php
	}
}

// Define constants ________________________________________
if ( defined( 'YITH_YWRAC_VERSION' ) ) {
	return;
} else {
	define( 'YITH_YWRAC_VERSION', '1.4.6' );
}

if ( ! defined( 'YITH_YWRAC_FREE_INIT' ) ) {
	define( 'YITH_YWRAC_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWRAC_INIT' ) ) {
	define( 'YITH_YWRAC_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWRAC_FILE' ) ) {
	define( 'YITH_YWRAC_FILE', __FILE__ );
}


if ( ! defined( 'YITH_YWRAC_URL' ) ) {
	define( 'YITH_YWRAC_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWRAC_ASSETS_URL' ) ) {
	define( 'YITH_YWRAC_ASSETS_URL', YITH_YWRAC_URL . 'assets' );
}

if ( ! defined( 'YITH_YWRAC_TEMPLATE_PATH' ) ) {
	define( 'YITH_YWRAC_TEMPLATE_PATH', YITH_YWRAC_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWRAC_INC' ) ) {
	define( 'YITH_YWRAC_INC', YITH_YWRAC_DIR . '/includes/' );
}

if ( ! defined( 'YITH_YWRAC_SLUG' ) ) {
	define( 'YITH_YWRAC_SLUG', 'yith-woocommerce-recover-abandoned-cart' );
}

if ( ! defined( 'YITH_YWRAC_SUFFIX' ) ) {
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	define( 'YITH_YWRAC_SUFFIX', $suffix );
}


if ( ! function_exists( 'yith_ywrac_install' ) ) {
	function yith_ywrac_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywrac_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_ywrac_init' );
		}
	}

	add_action( 'plugins_loaded', 'yith_ywrac_install', 11 );
}


function yith_ywrac_constructor() {

	// Woocommerce installation check _________________________
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_ywrac_install_woocommerce_admin_notice' );
		return;
	}

	// Load YWSL text domain ___________________________________
	load_plugin_textdomain( 'yith-woocommerce-recover-abandoned-cart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
	}

	require_once YITH_YWRAC_INC . 'functions.yith-wc-abandoned-cart.php';
	require_once YITH_YWRAC_INC . 'admin/class-wp-carts-list-table.php';
	require_once YITH_YWRAC_INC . 'class-yith-wc-abandoned-cart.php';
	require_once YITH_YWRAC_INC . 'class-yith-wc-abandoned-cart-helper.php';
	require_once YITH_YWRAC_INC . 'class-yith-wc-abandoned-cart-admin.php';
	require_once YITH_YWRAC_INC . 'class.yith-wc-abandoned-cart-privacy.php';
	require_once YITH_YWRAC_INC . 'admin/class-yith-wc-abandoned-cart-metaboxes.php';
	require_once YITH_YWRAC_INC . 'admin/hooks.manage-ywrac-cart-list-table.php';

	YITH_WC_Recover_Abandoned_Cart();
	YITH_WC_RAC_Metaboxes();

	if ( is_admin() ) {
		YITH_WC_Recover_Abandoned_Cart_Admin();
	}

}
add_action( 'yith_ywrac_init', 'yith_ywrac_constructor' );
