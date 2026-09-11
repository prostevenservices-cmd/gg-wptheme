<?php
/**
 * Plugin Name:       GGL – Personalización Petcio (sin Elementor Pro)
 * Plugin URI:        https://guauguauland.com
 * Description:       Snippets/plugin a medida para personalizar el tema Petcio (WooCommerce) de guauguauland.com sin depender de la suscripción anual de Elementor Pro. Incluye shortcodes de secciones (slider, testimonios, categorías, contador de ofertas), mejoras de WooCommerce (barra de envío gratis, insignia "Nuevo", campo "nombre de mi mascota", barra fija de compra en móvil), SEO/Schema básico y limpieza de rendimiento/seguridad.
 * Version:           1.0.0
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce
 * Author:            Guau Guau Land
 * Text Domain:       ggl-personalizacion
 *
 * Este plugin sustituye funcionalidades típicas de Elementor Pro (Slides,
 * Nested Carousel, Countdown, Theme Builder para WooCommerce) usando
 * shortcodes y hooks nativos de WordPress/WooCommerce, para poder seguir
 * usando Elementor GRATIS (el widget "Shortcode" está disponible en la
 * versión gratuita) sin pagar la licencia Pro.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Salir si se accede directamente al archivo.
}

define( 'GGL_PERSONALIZACION_VERSION', '1.0.0' );
define( 'GGL_PERSONALIZACION_DIR', plugin_dir_path( __FILE__ ) );
define( 'GGL_PERSONALIZACION_URL', plugin_dir_url( __FILE__ ) );

/**
 * Ajustes rápidos del negocio (editar aquí una sola vez).
 * Se usan en el schema SEO y en la barra de envío gratis.
 */
if ( ! defined( 'GGL_BUSINESS_NAME' ) ) {
	define( 'GGL_BUSINESS_NAME', 'Guau Guau Land' );
}
if ( ! defined( 'GGL_BUSINESS_PHONE' ) ) {
	define( 'GGL_BUSINESS_PHONE', '+1 (787) 918-5519' ); // Texto visible del teléfono.
}
if ( ! defined( 'GGL_BUSINESS_PHONE_TEL' ) ) {
	define( 'GGL_BUSINESS_PHONE_TEL', '+17879185519' ); // Formato para el enlace tel: (solo dígitos y +).
}
if ( ! defined( 'GGL_BUSINESS_ADDRESS' ) ) {
	define( 'GGL_BUSINESS_ADDRESS', '' );
}
if ( ! defined( 'GGL_FREE_SHIPPING_THRESHOLD' ) ) {
	define( 'GGL_FREE_SHIPPING_THRESHOLD', 50 ); // Monto para envío gratis.
}
if ( ! defined( 'GGL_LOGO_MAX_WIDTH' ) ) {
	define( 'GGL_LOGO_MAX_WIDTH', '200px' ); // Ancho máximo del logo del header/footer.
}

/**
 * Carga los módulos del plugin.
 * Cada archivo está protegido individualmente con ABSPATH y puede
 * copiarse también como snippet independiente en WPCode/Code Snippets.
 */
require_once GGL_PERSONALIZACION_DIR . 'includes/shortcodes-secciones.php';
require_once GGL_PERSONALIZACION_DIR . 'includes/woocommerce-mejoras.php';
require_once GGL_PERSONALIZACION_DIR . 'includes/optimizacion-seguridad.php';
require_once GGL_PERSONALIZACION_DIR . 'includes/seo-schema.php';
require_once GGL_PERSONALIZACION_DIR . 'includes/personalizacion-marca.php';

/**
 * Encola los assets (CSS/JS) propios del plugin solo en el front-end.
 */
function ggl_personalizacion_enqueue_assets() {
	wp_enqueue_style(
		'ggl-personalizacion',
		GGL_PERSONALIZACION_URL . 'assets/css/personalizacion.css',
		array(),
		GGL_PERSONALIZACION_VERSION
	);

	wp_enqueue_script(
		'ggl-personalizacion',
		GGL_PERSONALIZACION_URL . 'assets/js/personalizacion.js',
		array(),
		GGL_PERSONALIZACION_VERSION,
		true
	);

	wp_localize_script(
		'ggl-personalizacion',
		'gglPersonalizacion',
		array(
			'moneda' => get_woocommerce_currency_symbol(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'ggl_personalizacion_enqueue_assets' );

/**
 * Registra los Custom Post Types usados por los shortcodes de secciones
 * (testimonios y slides del hero) para poder gestionarlos desde el
 * escritorio de WordPress sin necesidad de Elementor Pro/Theme Builder.
 */
function ggl_personalizacion_registrar_cpts() {
	register_post_type(
		'ggl_testimonio',
		array(
			'labels'       => array(
				'name'          => __( 'Testimonios', 'ggl-personalizacion' ),
				'singular_name' => __( 'Testimonio', 'ggl-personalizacion' ),
				'add_new_item'  => __( 'Añadir testimonio', 'ggl-personalizacion' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-format-quote',
			'supports'     => array( 'title', 'editor', 'thumbnail' ),
		)
	);

	register_post_type(
		'ggl_slide',
		array(
			'labels'       => array(
				'name'          => __( 'Slides del Hero', 'ggl-personalizacion' ),
				'singular_name' => __( 'Slide', 'ggl-personalizacion' ),
				'add_new_item'  => __( 'Añadir slide', 'ggl-personalizacion' ),
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-images-alt2',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
		)
	);
}
add_action( 'init', 'ggl_personalizacion_registrar_cpts' );

/**
 * Añade los metabox sencillos (botón + enlace) para los slides del hero.
 */
function ggl_personalizacion_metabox_slide() {
	add_meta_box(
		'ggl_slide_datos',
		__( 'Datos del botón', 'ggl-personalizacion' ),
		'ggl_personalizacion_metabox_slide_html',
		'ggl_slide',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'ggl_personalizacion_metabox_slide' );

function ggl_personalizacion_metabox_slide_html( $post ) {
	wp_nonce_field( 'ggl_slide_guardar', 'ggl_slide_nonce' );
	$texto_boton = get_post_meta( $post->ID, '_ggl_boton_texto', true );
	$url_boton   = get_post_meta( $post->ID, '_ggl_boton_url', true );
	?>
	<p>
		<label for="ggl_boton_texto"><?php esc_html_e( 'Texto del botón', 'ggl-personalizacion' ); ?></label><br />
		<input type="text" id="ggl_boton_texto" name="ggl_boton_texto" class="widefat" value="<?php echo esc_attr( $texto_boton ); ?>" placeholder="Comprar ahora" />
	</p>
	<p>
		<label for="ggl_boton_url"><?php esc_html_e( 'URL del botón', 'ggl-personalizacion' ); ?></label><br />
		<input type="text" id="ggl_boton_url" name="ggl_boton_url" class="widefat" value="<?php echo esc_attr( $url_boton ); ?>" placeholder="https://guauguauland.com/tienda" />
	</p>
	<?php
}

function ggl_personalizacion_guardar_metabox_slide( $post_id ) {
	if ( ! isset( $_POST['ggl_slide_nonce'] ) || ! wp_verify_nonce( $_POST['ggl_slide_nonce'], 'ggl_slide_guardar' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['ggl_boton_texto'] ) ) {
		update_post_meta( $post_id, '_ggl_boton_texto', sanitize_text_field( $_POST['ggl_boton_texto'] ) );
	}
	if ( isset( $_POST['ggl_boton_url'] ) ) {
		update_post_meta( $post_id, '_ggl_boton_url', sanitize_text_field( $_POST['ggl_boton_url'] ) );
	}
}
add_action( 'save_post_ggl_slide', 'ggl_personalizacion_guardar_metabox_slide' );

/**
 * Activación: crea las páginas/avisos necesarios (placeholder por si se
 * quiere añadir alguna tarea de instalación en el futuro).
 */
function ggl_personalizacion_activar() {
	ggl_personalizacion_registrar_cpts();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ggl_personalizacion_activar' );
