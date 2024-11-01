<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements helper functions for YITH WooCommerce Recover Abandoned Cart
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 */

if ( ! function_exists( 'yith_ywrac_locate_template' ) ) {
	/**
	 * Locate the templates and return the path of the file found
	 *
	 * @param string $path
	 * @param array  $var
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_ywrac_locate_template( $path, $var = null ) {
		global $woocommerce;

		if ( function_exists( 'WC' ) ) {
			$woocommerce_base = WC()->template_path();
		} elseif ( defined( 'WC_TEMPLATE_PATH' ) ) {
			$woocommerce_base = WC_TEMPLATE_PATH;
		} else {
			$woocommerce_base = $woocommerce->plugin_path() . '/templates/';
		}

		$template_woocommerce_path = $woocommerce_base . $path;
		$template_path             = '/' . $path;
		$plugin_path               = YITH_YWRAC_DIR . 'templates/' . $path;

		$located = locate_template(
			array(
				$template_woocommerce_path, // Search in <theme>/woocommerce/
				$template_path,             // Search in <theme>/
				$plugin_path,                // Search in <plugin>/templates/
			)
		);

		if ( ! $located && file_exists( $plugin_path ) ) {
			return apply_filters( 'yith_ywrac_locate_template', $plugin_path, $path );
		}

		return apply_filters( 'yith_ywrac_locate_template', $located, $path );
	}
}

if ( ! function_exists( 'ywrac_get_timestamp' ) ) {
	function ywrac_get_timestamp() {
		$gtm = apply_filters( 'ywrac_get_timestamp_with_gtm', 0 );

		return current_time( 'timestamp', $gtm );
	}
}

if ( ! function_exists( 'yith_check_privacy_enabled' ) ) {

	/**
	 * Check if the tool for export and erase personal data are enabled
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function yith_check_privacy_enabled( $wc = false ) {
		global $wp_version;
		$enabled = $wc ? version_compare( WC()->version, '3.4.0', '>=' ) && version_compare( $wp_version, '4.9.5', '>' ) : version_compare( $wp_version, '4.9.5', '>' );
		return apply_filters( 'yith_check_privacy_enabled', $enabled, $wc );
	}
}

if ( ! function_exists( 'ywrac_get_cron_interval' ) ) {
	function ywrac_get_cron_interval() {
		return apply_filters( 'ywrac_cron_interval', 600 );
	}
}
