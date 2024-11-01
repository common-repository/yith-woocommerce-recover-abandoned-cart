<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Content metabox template
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 */

?>
<table class="yith-ywrac-info-cart" cellspacing="20">
	<tbody>
		<tr>
			<th><?php esc_html_e( 'Email sent:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td class="ywrac_email_status"><?php echo esc_html( $email_sent ); ?></td>
		</tr>

		<tr>
			<th><?php esc_html_e( 'Email action:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<td><?php echo '<input type="button" id="sendemail" class="ywrac_send_email button action"  value="' . esc_html( __( 'Send email', 'yith-woocommerce-recover-abandoned-cart' ) ) . '" data-id="' . esc_html( $cart_id ) . '">'; ?></td>
		</tr>
	</tbody>
</table>
