<?php
/**
 * SEO básico y datos estructurados (Schema.org) para una tienda de
 * mascotas local, sin depender de un plugin SEO de pago.
 *
 * Si ya usas Yoast SEO o Rank Math, este módulo se desactiva
 * automáticamente para evitar datos estructurados duplicados.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Comprueba si ya hay un plugin de SEO conocido activo.
 */
function ggl_seo_plugin_activo() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'RankMath' ) || defined( 'AIOSEO_VERSION' );
}

/**
 * Imprime el JSON-LD de tipo "PetStore" (subtipo de LocalBusiness) en
 * el <head> de la home, con los datos definidos como constantes en el
 * archivo principal del plugin (GGL_BUSINESS_NAME, GGL_BUSINESS_PHONE,
 * GGL_BUSINESS_ADDRESS).
 */
function ggl_seo_schema_negocio() {
	if ( ggl_seo_plugin_activo() || ! is_front_page() ) {
		return;
	}

	$datos = array(
		'@context' => 'https://schema.org',
		'@type'    => 'PetStore',
		'name'     => GGL_BUSINESS_NAME,
		'url'      => home_url( '/' ),
	);

	if ( has_site_icon() ) {
		$datos['image'] = get_site_icon_url();
	}

	if ( GGL_BUSINESS_PHONE ) {
		$datos['telephone'] = GGL_BUSINESS_PHONE;
	}

	if ( GGL_BUSINESS_ADDRESS ) {
		$datos['address'] = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => GGL_BUSINESS_ADDRESS,
		);
	}

	$redes = apply_filters( 'ggl_seo_redes_sociales', array() );
	if ( ! empty( $redes ) ) {
		$datos['sameAs'] = array_values( $redes );
	}

	echo "\n<script type=\"application/ld+json\">" . wp_json_encode( $datos ) . "</script>\n";
}
add_action( 'wp_head', 'ggl_seo_schema_negocio' );

/**
 * Meta descripción automática (a partir del extracto o del contenido)
 * solo si no hay ningún plugin SEO instalado que ya la gestione.
 */
function ggl_seo_meta_descripcion() {
	if ( ggl_seo_plugin_activo() || is_admin() ) {
		return;
	}

	$descripcion = '';

	if ( is_singular() ) {
		global $post;
		if ( $post ) {
			$descripcion = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 35 );
		}
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$descripcion = term_description();
	} else {
		$descripcion = get_bloginfo( 'description' );
	}

	$descripcion = trim( wp_strip_all_tags( (string) $descripcion ) );

	if ( $descripcion ) {
		echo '<meta name="description" content="' . esc_attr( wp_trim_words( $descripcion, 35 ) ) . '" />' . "\n";
	}
}
add_action( 'wp_head', 'ggl_seo_meta_descripcion', 1 );

/**
 * Imagen Open Graph de respaldo (usa el icono del sitio o la primera
 * imagen del contenido) cuando no hay plugin SEO que ya la genere.
 */
function ggl_seo_og_imagen_respaldo() {
	if ( ggl_seo_plugin_activo() || is_admin() ) {
		return;
	}

	$imagen = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$imagen = get_the_post_thumbnail_url( get_the_ID(), 'large' );
	} elseif ( has_site_icon() ) {
		$imagen = get_site_icon_url();
	}

	if ( $imagen ) {
		echo '<meta property="og:image" content="' . esc_url( $imagen ) . '" />' . "\n";
		echo '<meta property="og:title" content="' . esc_attr( wp_get_document_title() ) . '" />' . "\n";
		echo '<meta property="og:type" content="website" />' . "\n";
	}
}
add_action( 'wp_head', 'ggl_seo_og_imagen_respaldo', 2 );
