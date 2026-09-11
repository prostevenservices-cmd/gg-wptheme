<?php
/**
 * Personalización de marca: teléfono, logo (header y footer) y
 * copyright reales de la tienda, sin depender de poder editar la
 * plantilla de Elementor del footer (post #25112) directamente.
 *
 * El tema Petcio muestra el teléfono/logo/copyright en sitios que no
 * siempre son fáciles de editar sin tocar las opciones del tema o la
 * plantilla de Elementor del footer. Para no depender de eso, este
 * módulo reemplaza cualquier contenido de "demo" que quede en el HTML
 * final por el contenido real de Guau Guau Land, y agranda el logo en
 * header y footer.
 *
 * Para actualizar el teléfono/logo/redes en el futuro, basta con
 * cambiar las constantes GGL_BUSINESS_PHONE, GGL_BUSINESS_PHONE_TEL,
 * GGL_LOGO_URL, GGL_LOGO_MAX_WIDTH, GGL_SOCIAL_* en
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
 * Otros textos/recursos de demostración del footer de Petcio (plantilla
 * Elementor #25112) que también se sustituyen en el HTML final, sin
 * necesidad de editar la plantilla en Elementor:
 *   - El logo "petcio" (imagen) del footer, por el logo real de la tienda.
 *   - El texto de copyright "© 2024 – Petcio."
 */
function ggl_marca_reemplazos_footer() {
	return array(
		// Logo de demostración del footer (imagen con el texto "petcio").
		'https://guauguauland.com/wp-content/uploads/2023/11/logo.png'          => GGL_LOGO_URL,
		'https://guauguauland.com/wp-content/uploads/2023/11/logo-300x82.png'   => GGL_LOGO_URL,

		// Copyright de demostración. En el HTML de origen el guion va como
		// entidad "&#8211;" (sin resolver) y el símbolo © es un carácter
		// UTF-8 literal, tal cual lo genera el widget de texto de Elementor.
		'© 2024 &#8211; Petcio. All Rights Reserved.' => '© ' . gmdate( 'Y' ) . ' ' . esc_html( GGL_BUSINESS_NAME ) . '. Todos los derechos reservados.',
	);
}

/**
 * Sustituye los teléfonos y demás textos/recursos de demostración en
 * todo el HTML de la página, justo antes de enviarla al navegador.
 */
function ggl_marca_reemplazar_en_buffer( $html ) {
	if ( empty( $html ) ) {
		return $html;
	}

	$reemplazos = array_merge( ggl_marca_reemplazos_telefono(), ggl_marca_reemplazos_footer() );
	$reemplazos = apply_filters( 'ggl_reemplazos_marca', $reemplazos );

	return strtr( $html, $reemplazos );
}

function ggl_marca_iniciar_buffer() {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_ajax() ) {
		return;
	}

	ob_start( 'ggl_marca_reemplazar_en_buffer' );
}
add_action( 'template_redirect', 'ggl_marca_iniciar_buffer', 0 );

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
		/* Logo del footer (widget de imagen elementor-element-e3618ba dentro
		   de la plantilla Elementor #25112). Si algún día se recrea ese
		   widget en Elementor, el ID puede cambiar: en ese caso vuelve a
		   inspeccionar el nuevo `data-id` del widget del logo en el footer. */
		#bwp-footer .elementor-element-e3618ba img {
			max-width: <?php echo $ancho_maximo; ?> !important;
			width: auto !important;
			height: auto !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'ggl_marca_imprimir_css_logo', 100 );
