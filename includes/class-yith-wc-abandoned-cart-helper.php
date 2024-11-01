<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Recover Abandoned Cart Counters
 *
 * @class   YITH_WC_Recover_Abandoned_Cart_Helper
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Recover_Abandoned_Cart_Helper' ) ) {

	class YITH_WC_Recover_Abandoned_Cart_Helper {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Recover_Abandoned_Cart_Helper
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Recover_Abandoned_Cart_Helper
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'set_cron' ), 20 );
			add_filter( 'cron_schedules', array( $this, 'cron_schedule' ), 50 );
			add_action( 'update_option_ywrac_cron_time', array( $this, 'destroy_schedule' ) );
			add_action( 'update_option_ywrac_cron_time_type', array( $this, 'destroy_schedule' ) );

		}

		/**
		 * Destroy the schedule
		 *
		 * Called when ywrac_cron_time and ywrac_cron_time_type are update from settings panel
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function destroy_schedule() {
			wp_clear_scheduled_hook( 'ywrac_cron' );
			$this->set_cron();
		}

		/**
		 * Cron Schedule
		 *
		 * Add new schedules to WordPress
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function cron_schedule( $schedules ) {

				$interval = ywrac_get_cron_interval();

				$schedules['ywrac_gap'] = array(
					'interval' => $interval,
					'display'  => _x( 'YITH WooCommerce Recover Abandoned Cart Cron', 'do not translate plugin name', 'yith-woocommerce-recover-abandoned-cart' ),
				);

				return $schedules;
		}

		/**
		 * Set Cron
		 *
		 * Set ywrac_cron action each ywrac_gap schedule
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function set_cron() {
			if ( ! wp_next_scheduled( 'ywrac_cron' ) ) {
				$recurrence = apply_filters( 'ywrac_recurrence', 'ywrac_gap' );
				wp_schedule_event( ywrac_get_timestamp(), $recurrence, 'ywrac_cron' );
			}
		}



	}
}

/**
 * Unique access to instance of YITH_WC_Recover_Abandoned_Cart_Helper class
 *
 * @return \YITH_WC_Recover_Abandoned_Cart_Helper
 */
function YITH_WC_Recover_Abandoned_Cart_Helper() {
	return YITH_WC_Recover_Abandoned_Cart_Helper::get_instance();
}




