<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'manage_ywrac_cart_posts_columns', 'ywrac_cart_posts_columns' );
add_filter( 'manage_ywrac_cart_posts_custom_column', 'ywrac_cart_posts_custom_column', 10, 2 );
add_filter( 'manage_edit-ywrac_cart_sortable_columns', 'ywrac_cart_sortable_columns', 10, 1 );
add_filter( 'parse_query', 'ywrac_cart_parse_query' );

if ( ! function_exists( 'ywrac_cart_parse_query' ) ) {
	function ywrac_cart_parse_query( $query ) {
		global $pagenow;
		$post_type = ( isset( $_GET['post_type'] ) ) ? $_GET['post_type'] : 'post';

		if ( $post_type == 'ywrac_cart' && $pagenow == 'edit.php' ) {
			$meta_query   = ! ! $query->get( 'meta_query' ) ? $query->get( 'meta_query' ) : array();
			$meta_query[] = array(
				'key'   => '_cart_status',
				'value' => 'abandoned',
			);
			$query->set( 'meta_query', $meta_query );
		}
	}
}

if ( ! function_exists( 'ywrac_cart_posts_columns' ) ) {
	function ywrac_cart_posts_columns( $columns ) {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'post_title'   => __( 'Info', 'yith-woocommerce-recover-abandoned-cart' ),
			'email'        => __( 'Email', 'yith-woocommerce-recover-abandoned-cart' ),
			'subtotal'     => __( 'Subtotal', 'yith-woocommerce-recover-abandoned-cart' ),
			'status'       => __( 'Status', 'yith-woocommerce-recover-abandoned-cart' ),
			'status_email' => __( 'Email sent', 'yith-woocommerce-recover-abandoned-cart' ),
			'last_update'  => __( 'Last update', 'yith-woocommerce-recover-abandoned-cart' ),
			'action'       => __( 'Action', 'yith-woocommerce-recover-abandoned-cart' ),
		);
		return $columns;
	}
}

if ( ! function_exists( 'ywrac_cart_sortable_columns' ) ) {
	function ywrac_cart_sortable_columns( $sortable_columns ) {
		$sortable_columns = array(
			'post_title'   => array( 'post_title', false ),
			'email'        => array( 'email', false ),
			'subtotal'     => array( 'email', false ),
			'status'       => array( 'status', false ),
			'status_email' => array( 'status_email', false ),
			'last_update'  => array( 'last_update', false ),
		);
		return $sortable_columns;
	}
}

if ( ! function_exists( 'ywrac_cart_posts_custom_column' ) ) {
	function ywrac_cart_posts_custom_column( $column_name, $post_id ) {
		$item = get_post( $post_id );

		switch ( $column_name ) {
			case 'post_title':
				echo $item->post_title;
				break;
			case 'email':
				$user_email = get_post_meta( $item->ID, '_user_email', true );
				echo $user_email;
				break;
			case 'status':
				$user_email = get_post_meta( $item->ID, '_cart_status', true );
				echo $user_email;
				break;
			case 'status_email':
				$email_sent   = get_post_meta( $item->ID, '_email_sent', true );
				$email_status = ( $email_sent != 'no' && $email_sent != '' ) ? $email_sent : __( 'Not sent', 'yith-woocommerce-recover-abandoned-cart' );
				echo '<span class="email_status" data-id="' . $item->ID . '">' . $email_status . '</span>';
				break;
			case 'subtotal':
				$cart_subtotal = wc_price( get_post_meta( $item->ID, '_cart_subtotal', true ) );
				echo $cart_subtotal;
				break;
			case 'last_update':
				$last_update    = $item->post_modified;
				$seconds_offset = get_option( 'gmt_offset' ) * 3600;
				$last_update    = date( 'Y-m-d h:i:sa', strtotime( $last_update ) + $seconds_offset );
				echo $last_update;
				break;
			case 'action':
				$button = '<input type="button" id="sendemail" class="ywrac_send_email button action"  value="' . __( 'Send email', 'yith-woocommerce-recover-abandoned-cart' ) . '" data-id="' . $item->ID . '">';
				echo $button;
			default:
				echo ''; // Show the whole array for troubleshooting purposes
		}

	}
}
