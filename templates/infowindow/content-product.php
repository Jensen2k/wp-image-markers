<?php
/**
 * Template part for displaying content product, support for WooCommerce Plugin
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WPIM
 * @since 1.0
 * @version 1.0
 */
$product_id = $infowindow->get_content();

if ( !$product_id ) {
	return;
}

if ( $post = get_post( $product_id ) ):

	setup_postdata( $GLOBALS['post'] = & $post );

	global $product;

	/**
	 *  Ensure visibility
	 */
	if ( empty( $product ) || !$product->is_visible() ) {
		return;
	}
	?>
	
	<div <?php post_class(); ?>>

		<?php
		/**
		 * woocommerce_before_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item' );

		/**
		 * woocommerce_before_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - 10
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item_title' );

		/**
		 * woocommerce_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );

		/**
		 * woocommerce_after_shop_loop_item_title hook.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );

		/**
		 * woocommerce_after_shop_loop_item hook.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		do_action( 'woocommerce_after_shop_loop_item' );
		?>
	</div>
	<?php
	wp_reset_postdata();
endif;