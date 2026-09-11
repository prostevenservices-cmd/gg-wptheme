<?php
/**
 * Limpieza de rendimiento y seguridad básica.
 *
 * Nada de esto sustituye un plugin de seguridad/cache dedicado, pero
 * cubre las mejoras más habituales sin instalar plugins adicionales
 * (y sin pagar nada).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1) Desactiva los emojis nativos de WordPress (script + estilos
 *    inline en cada página), que no se usan casi nunca y añaden
 *    peticiones extra.
 */
function ggl_desactivar_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'ggl_quitar_emojis_tinymce' );
}
add_action( 'init', 'ggl_desactivar_emojis' );

function ggl_quitar_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	}
	return array();
}

/**
 * 2) Limpia el <head> de enlaces que no aportan nada al SEO y exponen
 *    información innecesaria (versión de WP, RSD, wlwmanifest, shortlink).
 */
function ggl_limpiar_head() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
}
add_action( 'init', 'ggl_limpiar_head' );

add_filter(
	'the_generator',
	function () {
		return '';
	}
);

/**
 * 3) Desactiva los métodos de XML-RPC más usados en ataques de fuerza
 *    bruta (pingback), sin apagar XML-RPC por completo (por si se usa
 *    la app móvil de WordPress o Jetpack).
 */
function ggl_desactivar_pingbacks( $metodos ) {
	unset( $metodos['pingback.ping'] );
	unset( $metodos['pingback.extensions.getPingbacks'] );
	return $metodos;
}
add_filter( 'xmlrpc_methods', 'ggl_desactivar_pingbacks' );

function ggl_quitar_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'ggl_quitar_pingback_header' );

/**
 * 4) Evita la enumeración de usuarios vía ?author=1 (redirige a la home).
 */
function ggl_bloquear_enumeracion_usuarios() {
	if ( is_admin() ) {
		return;
	}
	if ( isset( $_GET['author'] ) && ! empty( $_GET['author'] ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'ggl_bloquear_enumeracion_usuarios' );

/**
 * 5) Bloqueo simple de intentos de login (rate limiting con transients).
 *    Tras 5 intentos fallidos en 15 minutos desde la misma IP, se
 *    bloquea el login durante 15 minutos. No sustituye a un plugin de
 *    seguridad completo, pero frena bots básicos sin coste alguno.
 */
function ggl_login_obtener_ip() {
	return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0.0.0.0';
}

function ggl_login_bloqueado( $user, $username, $password ) {
	$ip    = ggl_login_obtener_ip();
	$clave = 'ggl_login_bloqueo_' . md5( $ip );

	if ( get_transient( $clave ) ) {
		return new WP_Error( 'ggl_demasiados_intentos', __( '<strong>Error:</strong> demasiados intentos fallidos. Inténtalo de nuevo en 15 minutos.', 'ggl-personalizacion' ) );
	}

	return $user;
}
add_filter( 'authenticate', 'ggl_login_bloqueado', 30, 3 );

function ggl_login_registrar_fallo( $username ) {
	$ip           = ggl_login_obtener_ip();
	$clave_conteo = 'ggl_login_intentos_' . md5( $ip );
	$clave_bloqueo = 'ggl_login_bloqueo_' . md5( $ip );

	$intentos = (int) get_transient( $clave_conteo );
	$intentos++;

	set_transient( $clave_conteo, $intentos, 15 * MINUTE_IN_SECONDS );

	if ( $intentos >= 5 ) {
		set_transient( $clave_bloqueo, true, 15 * MINUTE_IN_SECONDS );
	}
}
add_action( 'wp_login_failed', 'ggl_login_registrar_fallo' );

/**
 * 6) Restringe la edición de temas/plugins desde el escritorio de
 *    WordPress a los superadministradores (alternativa suave a la
 *    constante DISALLOW_FILE_EDIT, que se define normalmente en
 *    wp-config.php).
 */
function ggl_restringir_editor_archivos( $caps, $cap ) {
	if ( in_array( $cap, array( 'edit_themes', 'edit_plugins', 'edit_files' ), true ) && ! is_super_admin() ) {
		$caps[] = 'do_not_allow';
	}
	return $caps;
}
add_filter( 'map_meta_cap', 'ggl_restringir_editor_archivos', 10, 2 );
