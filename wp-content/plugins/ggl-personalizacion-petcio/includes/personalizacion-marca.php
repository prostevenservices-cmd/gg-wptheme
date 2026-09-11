<?php
/**
 * Personalización de marca: teléfono real de la tienda y tamaño del logo.
 *
 * El tema Petcio muestra el teléfono en dos sitios que no siempre son
 * fáciles de editar sin tocar las opciones del tema/Elementor (el
 * teléfono del header viene de las opciones del tema, y el del footer
 * viene de un widget de texto de Elementor). Para no depender de eso,
 * este módulo reemplaza cualquier teléfono de "demo" que quede en el
 * HTML final por el teléfono real de Guau Guau Land, y agranda el logo
 * en header y footer.
 *
 * Para actualizar el teléfono en el futuro, basta con cambiar las
 * constantes GGL_BUSINESS_PHONE / GGL_BUSINESS_PHONE_TEL en
 * ggl-personalizacion-petcio.php (no hace falta tocar este archivo).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Números de teléfono "demo" del tema Petcio que deben sustituirse por
 * el teléfono real de la tienda, en sus distintos formatos (texto
 * visible y enlace tel:).
 */
function ggl_marca_reemplazos_telefono() {
	return array(
		// Teléfono del header (demo Petcio: 1300 655 896).
		'tel:1300 655 896' => 'tel:' . GGL_BUSINESS_PHONE_TEL,
		'1300 655 896'     => GGL_BUSINESS_PHONE,

		// Teléfono del footer (demo Petcio: (02) 6188 8062).
		'tel:(02)%206188%208062' => 'tel:' . GGL_BUSINESS_PHONE_TEL,
		'(02) 6188 8062'         => GGL_BUSINESS_PHONE,
	);
}

/**
 * Sustituye los teléfonos de demostración en todo el HTML de la
 * página, justo antes de enviarla al navegador.
 */
function ggl_marca_reemplazar_telefono_en_buffer( $html ) {
	if ( empty( $html ) ) {
		return $html;
	}

	$reemplazos = apply_filters( 'ggl_reemplazos_telefono', ggl_marca_reemplazos_telefono() );

	return strtr( $html, $reemplazos );
}

function ggl_marca_iniciar_buffer_telefono() {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_ajax() ) {
		return;
	}

	ob_start( 'ggl_marca_reemplazar_telefono_en_buffer' );
}
add_action( 'template_redirect', 'ggl_marca_iniciar_buffer_telefono', 0 );

/**
 * Agranda el logo del header y del footer (.wpbingoLogo img) al ancho
 * máximo definido en GGL_LOGO_MAX_WIDTH. Se imprime como CSS inline en
 * el <head> con !important porque el propio tema Petcio ya trae una
 * regla más específica (`.bwp-header .wpbingoLogo img { max-width:143px }`)
 * que, sin !important, seguiría ganando aunque nuestro <style> se
 * imprima después.
 */
function ggl_marca_imprimir_css_logo() {
	$ancho_maximo = esc_attr( GGL_LOGO_MAX_WIDTH );
	?>
	<style id="ggl-logo-tamano">
		.wpbingoLogo img {
			max-width: <?php echo $ancho_maximo; ?> !important;
			width: auto !important;
			height: auto !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'ggl_marca_imprimir_css_logo', 100 );
