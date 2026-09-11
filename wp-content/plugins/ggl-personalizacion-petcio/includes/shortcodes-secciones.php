<?php
/**
 * Shortcodes de secciones de la home / landing.
 *
 * Estos shortcodes reemplazan widgets exclusivos de Elementor PRO
 * (Slides, Nested Carousel, Countdown, Price List) usando HTML/CSS/JS
 * propios. Se insertan en cualquier página con el widget "Shortcode" de
 * Elementor GRATIS o directamente en el editor de bloques de WordPress.
 *
 * Uso rápido:
 *   [ggl_hero_slider]                     -> slider con los "Slides del Hero" (CPT ggl_slide)
 *   [ggl_testimonios]                     -> carrusel con los "Testimonios" (CPT ggl_testimonio)
 *   [ggl_categorias_mascotas]             -> grid de categorías de producto de WooCommerce
 *   [ggl_categorias_mascotas categorias="perro,gato,ave,pez"]
 *   [ggl_iconos_beneficios]               -> franja "Envío gratis / 30 días / Pago seguro"
 *   [ggl_countdown fecha="2026-12-31 23:59:59" texto="Oferta de verano"]
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * [ggl_hero_slider]
 * Slider a pantalla completa basado en el CPT "ggl_slide".
 */
function ggl_shortcode_hero_slider( $atts ) {
	$atts = shortcode_atts(
		array(
			'cantidad' => -1,
			'altura'   => '520', // px, se puede sobreescribir por CSS.
		),
		$atts,
		'ggl_hero_slider'
	);

	$slides = new WP_Query(
		array(
			'post_type'      => 'ggl_slide',
			'posts_per_page' => intval( $atts['cantidad'] ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'post_status'    => 'publish',
		)
	);

	if ( ! $slides->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="ggl-hero-slider" style="--ggl-hero-altura: <?php echo esc_attr( $atts['altura'] ); ?>px;">
		<div class="ggl-hero-slider__track">
			<?php
			$indice = 0;
			while ( $slides->have_posts() ) :
				$slides->the_post();
				$indice++;
				$imagen_url  = get_the_post_thumbnail_url( get_the_ID(), 'full' );
				$boton_texto = get_post_meta( get_the_ID(), '_ggl_boton_texto', true );
				$boton_url   = get_post_meta( get_the_ID(), '_ggl_boton_url', true );
				?>
				<div class="ggl-hero-slider__slide <?php echo 1 === $indice ? 'is-activa' : ''; ?>"
					<?php if ( $imagen_url ) : ?>
						style="background-image:url('<?php echo esc_url( $imagen_url ); ?>');"
					<?php endif; ?>
				>
					<div class="ggl-hero-slider__contenido">
						<h2 class="ggl-hero-slider__titulo"><?php the_title(); ?></h2>
						<div class="ggl-hero-slider__texto"><?php the_excerpt(); ?></div>
						<?php if ( $boton_texto && $boton_url ) : ?>
							<a class="ggl-hero-slider__boton" href="<?php echo esc_url( $boton_url ); ?>"><?php echo esc_html( $boton_texto ); ?></a>
						<?php endif; ?>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
		<?php if ( $slides->post_count > 1 ) : ?>
			<button class="ggl-hero-slider__flecha ggl-hero-slider__flecha--prev" aria-label="Anterior">&#8249;</button>
			<button class="ggl-hero-slider__flecha ggl-hero-slider__flecha--next" aria-label="Siguiente">&#8250;</button>
			<div class="ggl-hero-slider__puntos">
				<?php for ( $i = 0; $i < $slides->post_count; $i++ ) : ?>
					<button class="ggl-hero-slider__punto <?php echo 0 === $i ? 'is-activo' : ''; ?>" data-indice="<?php echo esc_attr( $i ); ?>" aria-label="Ir al slide <?php echo esc_attr( $i + 1 ); ?>"></button>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'ggl_hero_slider', 'ggl_shortcode_hero_slider' );

/**
 * [ggl_testimonios]
 * Carrusel de testimonios basado en el CPT "ggl_testimonio".
 */
function ggl_shortcode_testimonios( $atts ) {
	$atts = shortcode_atts(
		array(
			'cantidad' => 9,
		),
		$atts,
		'ggl_testimonios'
	);

	$testimonios = new WP_Query(
		array(
			'post_type'      => 'ggl_testimonio',
			'posts_per_page' => intval( $atts['cantidad'] ),
			'post_status'    => 'publish',
		)
	);

	if ( ! $testimonios->have_posts() ) {
		return '';
	}

	ob_start();
	?>
	<div class="ggl-testimonios">
		<div class="ggl-testimonios__track">
			<?php
			while ( $testimonios->have_posts() ) :
				$testimonios->the_post();
				?>
				<blockquote class="ggl-testimonios__item">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="ggl-testimonios__avatar"><?php the_post_thumbnail( 'thumbnail' ); ?></div>
					<?php endif; ?>
					<p class="ggl-testimonios__texto">&ldquo;<?php echo esc_html( wp_strip_all_tags( get_the_content() ) ); ?>&rdquo;</p>
					<cite class="ggl-testimonios__autor"><?php the_title(); ?></cite>
				</blockquote>
			<?php endwhile; ?>
		</div>
	</div>
	<?php
	wp_reset_postdata();

	return ob_get_clean();
}
add_shortcode( 'ggl_testimonios', 'ggl_shortcode_testimonios' );

/**
 * [ggl_categorias_mascotas]
 * Grid de categorías de producto de WooCommerce (Perro, Gato, Ave, Pez, ...).
 */
function ggl_shortcode_categorias_mascotas( $atts ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return '';
	}

	$atts = shortcode_atts(
		array(
			'categorias' => '', // slugs separados por coma; vacío = todas las categorías padre.
			'cantidad'   => 8,
		),
		$atts,
		'ggl_categorias_mascotas'
	);

	$args = array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => true,
		'parent'     => 0,
		'number'     => intval( $atts['cantidad'] ),
	);

	if ( ! empty( $atts['categorias'] ) ) {
		$args['slug'] = array_map( 'trim', explode( ',', $atts['categorias'] ) );
		unset( $args['parent'] );
	}

	$categorias = get_terms( $args );

	if ( empty( $categorias ) || is_wp_error( $categorias ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="ggl-categorias-grid">
		<?php foreach ( $categorias as $categoria ) : ?>
			<?php
			$id_imagen  = get_term_meta( $categoria->term_id, 'thumbnail_id', true );
			$url_imagen = $id_imagen ? wp_get_attachment_image_url( $id_imagen, 'medium' ) : '';
			?>
			<a class="ggl-categorias-grid__item" href="<?php echo esc_url( get_term_link( $categoria ) ); ?>">
				<?php if ( $url_imagen ) : ?>
					<span class="ggl-categorias-grid__imagen" style="background-image:url('<?php echo esc_url( $url_imagen ); ?>');"></span>
				<?php endif; ?>
				<span class="ggl-categorias-grid__nombre"><?php echo esc_html( $categoria->name ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ggl_categorias_mascotas', 'ggl_shortcode_categorias_mascotas' );

/**
 * [ggl_iconos_beneficios]
 * Franja de iconos "Envío gratis / Devolución 30 días / Pago seguro",
 * como la que muestra la demo de Petcio, pero editable por shortcode.
 */
function ggl_shortcode_iconos_beneficios( $atts ) {
	$atts = shortcode_atts(
		array(
			'items' => 'Envío gratis|O te devolvemos el envío*;Devolución 30 días|Si el producto tiene problemas;Pago seguro|100% de pago seguro',
		),
		$atts,
		'ggl_iconos_beneficios'
	);

	$items = array_filter( array_map( 'trim', explode( ';', $atts['items'] ) ) );

	if ( empty( $items ) ) {
		return '';
	}

	ob_start();
	?>
	<div class="ggl-beneficios">
		<?php foreach ( $items as $item ) : ?>
			<?php
			$partes    = array_map( 'trim', explode( '|', $item, 2 ) );
			$titulo    = isset( $partes[0] ) ? $partes[0] : '';
			$subtitulo = isset( $partes[1] ) ? $partes[1] : '';
			?>
			<div class="ggl-beneficios__item">
				<span class="ggl-beneficios__icono" aria-hidden="true">&#10003;</span>
				<div>
					<strong class="ggl-beneficios__titulo"><?php echo esc_html( $titulo ); ?></strong>
					<?php if ( $subtitulo ) : ?>
						<span class="ggl-beneficios__subtitulo"><?php echo esc_html( $subtitulo ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ggl_iconos_beneficios', 'ggl_shortcode_iconos_beneficios' );

/**
 * [ggl_countdown fecha="2026-12-31 23:59:59" texto="Oferta de verano"]
 * Banner de cuenta atrás para promociones (equivalente al widget
 * "Countdown" de Elementor PRO). El cálculo se hace en JS
 * (assets/js/personalizacion.js) para que funcione con caché de página.
 */
function ggl_shortcode_countdown( $atts ) {
	$atts = shortcode_atts(
		array(
			'fecha' => '',
			'texto' => __( 'Oferta especial por tiempo limitado', 'ggl-personalizacion' ),
			'link'  => '',
		),
		$atts,
		'ggl_countdown'
	);

	if ( empty( $atts['fecha'] ) ) {
		return '';
	}

	$timestamp = strtotime( $atts['fecha'] );
	if ( ! $timestamp ) {
		return '';
	}

	ob_start();
	?>
	<div class="ggl-countdown" data-fecha-limite="<?php echo esc_attr( $timestamp * 1000 ); ?>">
		<p class="ggl-countdown__texto"><?php echo esc_html( $atts['texto'] ); ?></p>
		<div class="ggl-countdown__reloj">
			<div class="ggl-countdown__bloque"><span class="ggl-countdown__numero" data-unidad="dias">00</span><span class="ggl-countdown__etiqueta">Días</span></div>
			<div class="ggl-countdown__bloque"><span class="ggl-countdown__numero" data-unidad="horas">00</span><span class="ggl-countdown__etiqueta">Horas</span></div>
			<div class="ggl-countdown__bloque"><span class="ggl-countdown__numero" data-unidad="minutos">00</span><span class="ggl-countdown__etiqueta">Min</span></div>
			<div class="ggl-countdown__bloque"><span class="ggl-countdown__numero" data-unidad="segundos">00</span><span class="ggl-countdown__etiqueta">Seg</span></div>
		</div>
		<?php if ( ! empty( $atts['link'] ) ) : ?>
			<a class="ggl-countdown__boton" href="<?php echo esc_url( $atts['link'] ); ?>"><?php esc_html_e( 'Comprar ahora', 'ggl-personalizacion' ); ?></a>
		<?php endif; ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'ggl_countdown', 'ggl_shortcode_countdown' );
