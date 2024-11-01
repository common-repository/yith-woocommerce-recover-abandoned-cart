<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Content metabox template
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author  YITH
 */
if ( empty( $cart_content['cart'] ) ) {
	return;
}
?>
<table class="shop_table cart" id="yith-ywrac-table-list" cellspacing="0">
	<thead>
	<tr>
		<th class="product-thumbnail"><?php esc_html_e( 'Thumbnail', 'ywraq' ); ?></th>
		<th class="product-name"><?php esc_html_e( 'Product', 'ywraq' ); ?></th>
		<th class="product-single"><?php esc_html_e( 'Product Price', 'ywraq' ); ?></th>
		<th class="product-quantity"><?php esc_html_e( 'Quantity', 'ywraq' ); ?></th>
		<th class="product-subtotal"><?php esc_html_e( 'Total', 'ywraq' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach ( $cart_content['cart'] as $key => $raq ) :
		$_product = wc_get_product( ( isset( $raq['variation_id'] ) && $raq['variation_id'] != '' ) ? $raq['variation_id'] : $raq['product_id'] );
		?>
		<tr class="cart_item">
			<td class="product-thumbnail">
				<?php
				$thumbnail = $_product->get_image();

				if ( ! $_product->is_visible() ) {
					echo $thumbnail; //phpcs:ignore
				} else {
					printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );  //phpcs:ignore
				}
				?>
			</td>

			<td class="product-name">
				<a href="<?php echo esc_url( $_product->get_permalink() ); ?>"><?php echo wp_kses_post( $_product->get_title() ); ?></a>
				<?php
				// Meta data
				$item_data = array();

				// Variation data
				if ( isset( $raq['variation_id'] ) && isset( $raq['variation'] ) && ! empty( $raq['variation_id'] ) && is_array( $raq['variations'] ) ) {
					foreach ( $raq['variations'] as $name => $value ) {
						$label = '';
						if ( '' === $value ) {
							continue;
						}
						$taxonomy = wc_attribute_taxonomy_name( str_replace( 'pa_', '', urldecode( $name ) ) );

						// If this is a term slug, get the term's nice name
						if ( taxonomy_exists( $taxonomy ) ) {
							$term = get_term_by( 'slug', $value, $taxonomy );
							if ( ! is_wp_error( $term ) && $term && $term->name ) {
								$value = $term->name;
							}
							$label = wc_attribute_label( $taxonomy );

						} else {

							if ( strpos( $name, 'attribute_' ) !== false ) {
								$custom_att = str_replace( 'attribute_', '', $name );

								if ( $custom_att != '' ) {
									$label = wc_attribute_label( $custom_att );
								} else {
									$label = $name;
								}
							}
						}

						$item_data[] = array(
							'key'   => $label,
							'value' => $value,
						);
					}
				}

				// Output flat or in list format
				if ( sizeof( $item_data ) > 0 ) {
					foreach ( $item_data as $data ) {
						echo esc_html( $data['key'] ) . ': ' . wp_kses_post( $data['value'] ) . "\n";
					}
				}
				?>
			</td>
			<td class="product-price">
				<?php
				echo wp_kses_post( $_product->get_price_html() );
				?>
			</td>

			<td class="product-quantity">
				<?php echo esc_html( $raq['quantity'] ); ?>
			</td>

			<td class="product-subtotal">
				<?php
				echo wp_kses_post( wc_price( $_product->get_price() * $raq['quantity'] ) );
				?>
			</td>
		</tr>

	<?php endforeach ?>

	</tbody>
</table>
