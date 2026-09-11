/**
 * JS vanilla (sin dependencias) para los shortcodes y mejoras de
 * GGL – Personalización Petcio.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		iniciarHeroSliders();
		iniciarCountdowns();
		iniciarBarraFijaCompra();
	} );

	/**
	 * Slider del hero ([ggl_hero_slider]): autoplay + flechas + puntos.
	 */
	function iniciarHeroSliders() {
		var sliders = document.querySelectorAll( '.ggl-hero-slider' );

		sliders.forEach( function ( slider ) {
			var slides = slider.querySelectorAll( '.ggl-hero-slider__slide' );
			var puntos = slider.querySelectorAll( '.ggl-hero-slider__punto' );
			var indiceActual = 0;
			var intervalo;

			if ( slides.length < 2 ) {
				return;
			}

			function mostrarSlide( indice ) {
				slides[ indiceActual ].classList.remove( 'is-activa' );
				if ( puntos[ indiceActual ] ) {
					puntos[ indiceActual ].classList.remove( 'is-activo' );
				}

				indiceActual = ( indice + slides.length ) % slides.length;

				slides[ indiceActual ].classList.add( 'is-activa' );
				if ( puntos[ indiceActual ] ) {
					puntos[ indiceActual ].classList.add( 'is-activo' );
				}
			}

			function siguiente() {
				mostrarSlide( indiceActual + 1 );
			}

			function anterior() {
				mostrarSlide( indiceActual - 1 );
			}

			function reiniciarAutoplay() {
				clearInterval( intervalo );
				intervalo = setInterval( siguiente, 6000 );
			}

			var botonSiguiente = slider.querySelector( '.ggl-hero-slider__flecha--next' );
			var botonAnterior  = slider.querySelector( '.ggl-hero-slider__flecha--prev' );

			if ( botonSiguiente ) {
				botonSiguiente.addEventListener( 'click', function () {
					siguiente();
					reiniciarAutoplay();
				} );
			}

			if ( botonAnterior ) {
				botonAnterior.addEventListener( 'click', function () {
					anterior();
					reiniciarAutoplay();
				} );
			}

			puntos.forEach( function ( punto, indice ) {
				punto.addEventListener( 'click', function () {
					mostrarSlide( indice );
					reiniciarAutoplay();
				} );
			} );

			reiniciarAutoplay();
		} );
	}

	/**
	 * Countdown ([ggl_countdown]): calcula días/horas/min/seg restantes
	 * hasta la fecha límite indicada en el atributo data-fecha-limite
	 * (timestamp en milisegundos).
	 */
	function iniciarCountdowns() {
		var countdowns = document.querySelectorAll( '.ggl-countdown' );

		countdowns.forEach( function ( countdown ) {
			var fechaLimite = parseInt( countdown.getAttribute( 'data-fecha-limite' ), 10 );

			if ( ! fechaLimite ) {
				return;
			}

			var numDias    = countdown.querySelector( '[data-unidad="dias"]' );
			var numHoras   = countdown.querySelector( '[data-unidad="horas"]' );
			var numMinutos = countdown.querySelector( '[data-unidad="minutos"]' );
			var numSegundos = countdown.querySelector( '[data-unidad="segundos"]' );

			function actualizar() {
				var restante = fechaLimite - Date.now();

				if ( restante <= 0 ) {
					countdown.style.display = 'none';
					clearInterval( temporizador );
					return;
				}

				var dias    = Math.floor( restante / ( 1000 * 60 * 60 * 24 ) );
				var horas   = Math.floor( ( restante / ( 1000 * 60 * 60 ) ) % 24 );
				var minutos = Math.floor( ( restante / ( 1000 * 60 ) ) % 60 );
				var segundos = Math.floor( ( restante / 1000 ) % 60 );

				if ( numDias ) {
					numDias.textContent = String( dias ).padStart( 2, '0' );
				}
				if ( numHoras ) {
					numHoras.textContent = String( horas ).padStart( 2, '0' );
				}
				if ( numMinutos ) {
					numMinutos.textContent = String( minutos ).padStart( 2, '0' );
				}
				if ( numSegundos ) {
					numSegundos.textContent = String( segundos ).padStart( 2, '0' );
				}
			}

			actualizar();
			var temporizador = setInterval( actualizar, 1000 );
		} );
	}

	/**
	 * Barra fija de "Comprar" en móvil dentro de producto individual:
	 * aparece al hacer scroll pasado el formulario de compra y el
	 * botón hace scroll suave de vuelta al formulario original.
	 */
	function iniciarBarraFijaCompra() {
		var barra = document.getElementById( 'ggl-barra-fija-compra' );

		if ( ! barra ) {
			return;
		}

		var formularioCompra = document.querySelector( 'form.cart' ) || document.querySelector( '.summary' );

		if ( ! formularioCompra ) {
			return;
		}

		window.addEventListener(
			'scroll',
			function () {
				var posicionFormulario = formularioCompra.getBoundingClientRect().bottom;

				if ( posicionFormulario < 0 ) {
					barra.classList.add( 'is-visible' );
				} else {
					barra.classList.remove( 'is-visible' );
				}
			},
			{ passive: true }
		);

		var botonComprar = barra.querySelector( '.ggl-barra-fija-compra__boton' );

		if ( botonComprar ) {
			botonComprar.addEventListener( 'click', function ( evento ) {
				evento.preventDefault();
				formularioCompra.scrollIntoView( { behavior: 'smooth', block: 'center' } );
			} );
		}
	}
} )();
