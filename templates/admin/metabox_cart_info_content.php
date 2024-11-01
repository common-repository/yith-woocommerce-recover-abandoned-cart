<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Content metabox template
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 */
$seconds_offset = get_option( 'gmt_offset' ) * 3600;
$last_update    = date( 'Y-m-d h:i:sa', strtotime( $last_update ) + $seconds_offset );
?>
<table class="yith-ywrac-info-cart" cellspacing="20">
	<tbody>
		<tr>
			<th><?php esc_html_e( 'Cart Status:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><span class="<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status ); ?></span></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'Cart Last Update:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo esc_html( $last_update ); ?></td>
		</tr>

		<?php if ( $user ) : ?>
		<tr>
			<th><?php esc_html_e( 'User:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo( esc_html( $user->display_name ) ); ?></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'User email:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo '<a href="mailto:' . esc_attr( $user->user_email ) . '">' . esc_html( $user->user_email ) . '</a>'; ?></td>
		</tr>
		<?php endif; ?>



	</tbody>
</table>
