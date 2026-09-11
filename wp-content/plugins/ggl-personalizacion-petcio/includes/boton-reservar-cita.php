<?php
/**
 * Reemplaza el botón "All Categories" (widget de menú vertical de
 * categorías, en el header de escritorio) por un botón de llamada a la
 * acción "Book Grooming" que enlaza directo a la página de reservas,
 * sin dropdown ni submenú.
 *
 * El widget original vive dentro de `.header-vertical-categories`, en
 * el header de escritorio de Petcio. En vez de editarlo desde el
 * escritorio de WordPress (Apariencia → Widgets), se oculta por CSS y
 * se inserta un botón nuevo justo en su lugar, así seguimos sin tocar
 * la configuración del tema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Inserta el botón "Book Grooming" justo antes del widget de
 * categorías, usando como ancla la etiqueta de apertura del contenedor
 * `.header-vertical-categories` (aparece una sola vez en el header de
 * escritorio, así que es un punto de anclaje estable).
 */
function ggl_reservar_insertar_boton( $html ) {
	if ( empty( $html ) ) {
		return $html;
	}

	$boton = sprintf(
		'<a href="%s" class="ggl-boton-reservar">%s</a>',
		esc_url( GGL_URL_RESERVAR_CITA ),
		esc_html__( 'Book Grooming', 'ggl-personalizacion' )
	);

	return str_replace(
		'<div class="header-vertical-categories vertical-spacing-2">',
		$boton . '<div class="header-vertical-categories vertical-spacing-2">',
		$html
	);
}

function ggl_reservar_iniciar_buffer() {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_ajax() ) {
		return;
	}

	ob_start( 'ggl_reservar_insertar_boton' );
}
add_action( 'template_redirect', 'ggl_reservar_iniciar_buffer', 0 );

/**
 * Oculta el widget original de categorías y da estilo al botón nuevo.
 * El widget original queda oculto (display:none con !important) en vez
 * de eliminado del HTML, así el botón nuevo puede insertarse justo en
 * su lugar sin tener que tocar el markup interno de la lista de
 * categorías (que cambia según cuántas categorías de producto tengas).
 */
function ggl_reservar_imprimir_css() {
	?>
	<style id="ggl-boton-reservar-css">
		.header-vertical-categories {
			display: none !important;
		}
		.ggl-boton-reservar {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			height: 44px;
			padding: 0 22px;
			margin-right: 10px;
			background: var( --theme-color, #30b7c5 );
			color: #fff !important;
			font-weight: 700;
			text-decoration: none !important;
			border-radius: 6px;
			white-space: nowrap;
		}
		.ggl-boton-reservar:hover {
			background: #279aa6;
			color: #fff !important;
		}
	</style>
	<?php
}
add_action( 'wp_head', 'ggl_reservar_imprimir_css', 100 );
