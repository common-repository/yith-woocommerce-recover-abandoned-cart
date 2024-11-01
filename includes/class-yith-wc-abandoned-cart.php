<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Recover Abandoned Cart
 *
 * @class   YITH_WC_Recover_Abandoned_Cart
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Recover_Abandoned_Cart' ) ) {

	class YITH_WC_Recover_Abandoned_Cart {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Recover_Abandoned_Cart
		 */
		protected static $instance;

		/**
		 * Post type name
		 *
		 * @var \YITH_WC_Recover_Abandoned_Cart
		 */
		public $post_type_name = 'ywrac_cart';

		/**
		 * Cut Off time
		 *
		 * @var \YITH_WC_Recover_Abandoned_Cart
		 */
		public $cutoff = 60;


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Recover_Abandoned_Cart
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
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );

			$this->cutoff = get_option( 'ywrac_cut_off_time' ) * 60;
			add_action( 'init', array( $this, 'add_post_type' ), 10 );
			YITH_WC_Recover_Abandoned_Cart_Helper();
			add_action( 'woocommerce_cart_updated', array( $this, 'cart_updated' ) );
			add_action( 'ywrac_cron', array( $this, 'update_carts' ) );
			yith_check_privacy_enabled() && YITH_WC_Recover_Abandoned_Cart_Privacy();
		}

		/**
		 * Load YIT Plugin Framework
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Emanuela Castorina
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}


		/**
		 * Register the custom post type ywrac_cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function add_post_type() {

				$labels = array(
					'name'               => _x( 'Abandoned Cart', 'Post Type General Name', 'yith-woocommerce-recover-abandoned-cart' ),
					'singular_name'      => _x( 'Abandoned Cart', 'Post Type Singular Name', 'yith-woocommerce-recover-abandoned-cart' ),
					'menu_name'          => __( 'Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
					'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-recover-abandoned-cart' ),
					'all_items'          => __( 'All Abandoned Carts', 'yith-woocommerce-recover-abandoned-cart' ),
					'view_item'          => __( 'View Abandoned Carts', 'yith-woocommerce-recover-abandoned-cart' ),
					'add_new_item'       => __( 'Add New Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
					'add_new'            => __( 'Add New Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
					'edit_item'          => __( 'Edit Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
					'update_item'        => __( 'Update Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
					'search_items'       => __( 'Search Abandoned Cart', 'yith-woocommerce-recover-abandoned-cart' ),
					'not_found'          => __( 'Not found', 'yith-woocommerce-recover-abandoned-cart' ),
					'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-recover-abandoned-cart' ),
				);

				$args = array(
					'label'               => __( 'Carts', 'yith-woocommerce-recover-abandoned-cart' ),
					'description'         => '',
					'labels'              => $labels,
					'supports'            => array( 'title' ),
					'hierarchical'        => false,
					'public'              => false,
					'show_ui'             => true,
					'show_in_menu'        => false,
					'exclude_from_search' => true,
					'capability_type'     => 'post',
					'capabilities'        => array( 'create_posts' => false ),
					'map_meta_cap'        => true,
				);

				register_post_type( $this->post_type_name, $args );

		}

		/**
		 * Update the entry on db
		 *
		 * when the user update the cart update the entry on db of the current cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function cart_updated() {
			if ( is_admin() ) {
				return;
			}

			if ( is_user_logged_in() ) {
				$user_id           = get_current_user_id();
				$user_details      = get_userdata( $user_id );
				$user_email        = $user_details->user_email;
				$has_previous_cart = $this->has_previous_cart( $user_id );

				if ( ! $has_previous_cart ) {
					$post = array(
						'post_content' => '',
						'post_status'  => 'publish',
						'post_title'   => $user_details->display_name,
						'post_type'    => $this->post_type_name,
					);

					$post_id = wp_insert_post( $post );

					update_post_meta( $post_id, '_user_id', $user_id );
					update_post_meta( $post_id, '_user_email', $user_email );
					update_post_meta( $post_id, '_email_sent', 'no' );

				} else {
					$post_id      = $has_previous_cart->ID;
					$post_updated = array(
						'ID'        => $post_id,
						'post_date' => $has_previous_cart->post_date,
						'post_type' => $this->post_type_name,
					);

					wp_update_post( $post_updated );

				}

				update_post_meta( $post_id, '_cart_status', 'open' );
				update_post_meta( $post_id, '_cart_content', $this->get_item_cart() );

				$subtotal = ( get_option( 'woocommerce_tax_display_cart' ) == 'excl' ) ? WC()->cart->subtotal_ex_tax : WC()->cart->subtotal;
				update_post_meta( $post_id, '_cart_subtotal', $subtotal );
			}

		}

		/**
		 * Check if a user has a previous cart in database
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function has_previous_cart( $user_id ) {
			$args = array(
				'post_type'   => $this->post_type_name,
				'meta_key'    => '_user_id',
				'meta_value'  => $user_id,
				'post_status' => 'publish',
			);

			$r = get_posts( $args );
			if ( empty( $r ) ) {
				return false;
			} else {
				return $r[0];
			}
		}

		/**
		 * Return an array with the content of cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function get_item_cart() {

			$cart = maybe_serialize( array( 'cart' => WC()->session->get( 'cart' ) ) );

			return $cart;
		}

		/**
		 * Called when a cart is updated
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function update_carts() {
			$start_to_date = (int) ( time() - $this->cutoff );
			$args          = array(
				'post_type'   => $this->post_type_name,
				'post_status' => 'publish',
				'meta_value'  => 'open',
				'meta_key'    => '_cart_status',
				'date_query'  => array(
					array(
						'column' => 'post_modified_gmt',
						'before' => date( 'Y-m-d H:i:s', $start_to_date ),
					),
				),
			);
			$p             = get_posts( $args );
			if ( ! empty( $p ) ) {
				foreach ( $p as $post ) {
					$this->update_status( $post );
				}
			}
		}

		/**
		 * Update the status of a cart
		 *
		 * @since  1.0.0
		 * @author Emanuela Castorina
		 */
		public function update_status( $cart ) {
			$current_status = get_post_meta( $cart->ID, '_cart_status', true );
			$post_modified  = strtotime( $cart->post_modified );
			$current_time   = ywrac_get_timestamp();

			if ( ( $current_time - $post_modified ) > $this->cutoff ) {
				if ( $current_status == 'open' ) {
					update_post_meta( $cart->ID, '_cart_status', 'abandoned' );
				}
			}
		}


	}


}

/**
 * Unique access to instance of YITH_WC_Recover_Abandoned_Cart class
 *
 * @return \YITH_WC_Recover_Abandoned_Cart
 */
function YITH_WC_Recover_Abandoned_Cart() {
	return YITH_WC_Recover_Abandoned_Cart::get_instance();
}

