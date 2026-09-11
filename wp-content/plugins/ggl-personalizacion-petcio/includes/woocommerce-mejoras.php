<?php
/**
 * Mejoras de WooCommerce que sustituyen funciones típicas de
 * Elementor PRO / Theme Builder (barra de envío gratis, barra fija de
 * compra en móvil, insignias de producto) usando hooks nativos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1) Insignia "Nuevo" en productos publicados hace menos de N días,
 *    además de la insignia "Oferta" que ya trae WooCommerce.
 */
function ggl_wc_insignia_nuevo() {
	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$dias_como_nuevo = apply_filters( 'ggl_dias_producto_nuevo', 30 );
	$fecha_creacion   = $product->get_date_created();

	if ( ! $fecha_creacion ) {
		return;
	}

	$dias_transcurridos = ( time() - $fecha_creacion->getTimestamp() ) / DAY_IN_SECONDS;

	if ( $dias_transcurridos <= $dias_como_nuevo ) {
		echo '<span class="ggl-insignia ggl-insignia--nuevo">' . esc_html__( 'Nuevo', 'ggl-personalizacion' ) . '</span>';
	}
}
add_action( 'woocommerce_before_shop_loop_item_title', 'ggl_wc_insignia_nuevo', 9 );
add_action( 'woocommerce_before_single_product_summary', 'ggl_wc_insignia_nuevo', 9 );

/**
 * 2) Barra de progreso hacia el envío gratis, en el carrito y en el
 *    mini-carrito. Umbral configurable con la constante
 *    GGL_FREE_SHIPPING_THRESHOLD (definida en el archivo principal).
 */
function ggl_wc_barra_envio_gratis() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}

	$umbral    = (float) apply_filters( 'ggl_umbral_envio_gratis', GGL_FREE_SHIPPING_THRESHOLD );
	$subtotal  = (float) WC()->cart->get_subtotal();
	$restante  = $umbral - $subtotal;
	$porcentaje = $umbral > 0 ? min( 100, ( $subtotal / $umbral ) * 100 ) : 100;

	if ( $umbral <= 0 ) {
		return;
	}

	echo '<div class="ggl-barra-envio">';
	if ( $restante > 0 ) {
		printf(
			/* translators: %s: importe restante para envío gratis, ya formateado con el símbolo de moneda. */
			'<p class="ggl-barra-envio__texto">' . esc_html__( '¡Añade %s más y tu envío es gratis!', 'ggl-personalizacion' ) . '</p>',
			wc_price( $restante )
		);
	} else {
		echo '<p class="ggl-barra-envio__texto">' . esc_html__( '¡Genial! Tu pedido tiene envío gratis.', 'ggl-personalizacion' ) . '</p>';
	}
	echo '<div class="ggl-barra-envio__pista"><span class="ggl-barra-envio__relleno" style="width:' . esc_attr( $porcentaje ) . '%;"></span></div>';
	echo '</div>';
}
add_action( 'woocommerce_before_cart_table', 'ggl_wc_barra_envio_gratis' );
add_action( 'woocommerce_before_mini_cart_contents', 'ggl_wc_barra_envio_gratis' );

/**
 * 3) Barra fija de "Añadir al carrito" en móvil dentro de la página de
 *    producto individual (equivalente al "Sticky Add To Cart" de los
 *    builders de pago). Se oculta automáticamente en escritorio vía CSS.
 */
function ggl_wc_barra_fija_compra() {
	if ( ! is_product() ) {
		return;
	}

	global $product;

	if ( ! $product instanceof WC_Product ) {
		return;
	}
	?>
	<div class="ggl-barra-fija-compra" id="ggl-barra-fija-compra">
		<div class="ggl-barra-fija-compra__info">
			<?php echo wp_kses_post( $product->get_image( 'thumbnail' ) ); ?>
			<div>
				<p class="ggl-barra-fija-compra__titulo"><?php echo esc_html( $product->get_name() ); ?></p>
				<p class="ggl-barra-fija-compra__precio"><?php echo wp_kses_post( $product->get_price_html() ); ?></p>
			</div>
		</div>
		<a href="#" class="ggl-barra-fija-compra__boton" data-scroll-a="add_to_cart_form"><?php esc_html_e( 'Comprar', 'ggl-personalizacion' ); ?></a>
	</div>
	<?php
}
add_action( 'wp_footer', 'ggl_wc_barra_fija_compra' );

/**
 * 4) Campo personalizado en el checkout: "Nombre de tu mascota" 🐾.
 *    Muy útil para una tienda de mascotas: se guarda en el pedido y se
 *    muestra en el email de confirmación y en el escritorio de pedidos.
 */
function ggl_wc_agregar_campo_mascota( $checkout ) {
	echo '<div id="ggl_nombre_mascota_wrapper" class="ggl-campo-mascota">';
	woocommerce_form_field(
		'ggl_nombre_mascota',
		array(
			'type'        => 'text',
			'class'       => array( 'form-row-wide' ),
			'label'       => __( 'Nombre de tu mascota 🐾 (opcional)', 'ggl-personalizacion' ),
			'placeholder' => __( 'Ej: Firulais', 'ggl-personalizacion' ),
			'required'    => false,
		),
		$checkout->get_value( 'ggl_nombre_mascota' )
	);
	echo '</div>';
}
add_action( 'woocommerce_after_order_notes', 'ggl_wc_agregar_campo_mascota' );

function ggl_wc_guardar_campo_mascota( $order_id ) {
	if ( isset( $_POST['ggl_nombre_mascota'] ) && ! empty( $_POST['ggl_nombre_mascota'] ) ) {
		update_post_meta( $order_id, '_ggl_nombre_mascota', sanitize_text_field( $_POST['ggl_nombre_mascota'] ) );
	}
}
add_action( 'woocommerce_checkout_update_order_meta', 'ggl_wc_guardar_campo_mascota' );

function ggl_wc_mostrar_campo_mascota_admin( $order ) {
	$nombre_mascota = get_post_meta( $order->get_id(), '_ggl_nombre_mascota', true );
	if ( $nombre_mascota ) {
		echo '<p><strong>' . esc_html__( 'Nombre de la mascota:', 'ggl-personalizacion' ) . '</strong> ' . esc_html( $nombre_mascota ) . '</p>';
	}
}
add_action( 'woocommerce_admin_order_data_after_billing_address', 'ggl_wc_mostrar_campo_mascota_admin' );

function ggl_wc_mostrar_campo_mascota_email( $order, $sent_to_admin, $plain_text ) {
	$nombre_mascota = get_post_meta( $order->get_id(), '_ggl_nombre_mascota', true );
	if ( $nombre_mascota ) {
		echo '<p><strong>' . esc_html__( 'Nombre de la mascota:', 'ggl-personalizacion' ) . '</strong> ' . esc_html( $nombre_mascota ) . '</p>';
	}
}
add_action( 'woocommerce_email_order_meta', 'ggl_wc_mostrar_campo_mascota_email', 10, 3 );

/**
 * 5) Limita los "productos relacionados" a 4, en 4 columnas, para que
 *    coincida con el diseño de grid habitual de Petcio (evita depender
 *    del widget de productos relacionados de Elementor Pro).
 */
function ggl_wc_ajustar_productos_relacionados( $args ) {
	$args['posts_per_page'] = 4;
	$args['columns']        = 4;
	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'ggl_wc_ajustar_productos_relacionados' );

/**
 * 6) Texto personalizado del botón "Añadir al carrito" en el listado
 *    de productos (opcional). Comenta esta función si prefieres el
 *    texto original de WooCommerce/Petcio.
 */
function ggl_wc_texto_boton_anadir_carrito( $texto, $product ) {
	if ( $product->is_type( 'simple' ) && $product->is_in_stock() ) {
		return __( 'Añadir al carrito', 'ggl-personalizacion' );
	}
	return $texto;
}
add_filter( 'woocommerce_product_add_to_cart_text', 'ggl_wc_texto_boton_anadir_carrito', 10, 2 );
