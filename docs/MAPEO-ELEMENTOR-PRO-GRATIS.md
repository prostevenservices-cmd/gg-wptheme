# Mapeo: funciones de Elementor Pro → alternativa gratuita usada aquí

La demo de Petcio (https://petcio.wpbingosite.com) está construida
combinando Elementor con algunos widgets que **solo existen en la versión
Pro**. Esta tabla muestra qué widget de pago se sustituye por qué
funcionalidad de este repositorio.

| Sección de la demo | Widget de Elementor Pro que suele usarse | Alternativa gratuita en este repo |
|---|---|---|
| Slider de portada (imagen grande + texto + botón) | **Slides** | `[ggl_hero_slider]` + tipo de contenido "Slides del Hero" |
| Carrusel de testimonios | **Nested Carousel** / Testimonial Carousel | `[ggl_testimonios]` + tipo de contenido "Testimonios" |
| Carrusel de productos por categoría (Bird/Cat/Dog/Fish) | Carrusel de productos con Theme Builder | `[ggl_categorias_mascotas]` (grid de categorías WooCommerce) + el propio *loop* de productos de WooCommerce/Elementor gratis |
| Banner de oferta con cuenta atrás | **Countdown** | `[ggl_countdown fecha="..." texto="..."]` |
| Franja "Free shipping / 30 Days Return / Secure Payment" | Icon List (ya en gratis) o Price List (Pro) | `[ggl_iconos_beneficios]` (por si prefieres editar el texto vía shortcode en vez de duplicar widgets) |
| Plantillas de encabezado/pie de página distintas por tipo de página | **Theme Builder** | Se resuelve con las opciones nativas del *Customizer* de Petcio + menús/widgets de WordPress (no requiere Pro para un encabezado/pie único en todo el sitio) |
| Plantilla de página de producto individual personalizada | **Theme Builder** (Single Product) | Hooks de WooCommerce en `woocommerce-mejoras.php` (insignias, barra fija de compra, campo de mascota) en vez de rediseñar toda la plantilla |
| Barra fija de "Añadir al carrito" en móvil | Sticky (Pro) + Theme Builder | `ggl_wc_barra_fija_compra()` (CSS + JS propios) |
| Barra de progreso de envío gratis | Requiere Pro + WooCommerce Builder o plugin de pago | `ggl_wc_barra_envio_gratis()` |

## ¿Cuándo SÍ necesitarías Elementor Pro?

Sé honesto contigo mismo sobre estos casos, en los que un snippet no es
suficiente y sí conviene evaluar Elementor Pro (o una alternativa gratuita
como **Spectra**, **Kadence Blocks** o el propio **Site Editor** de
WordPress si el tema es compatible con bloques):

- Necesitas un editor visual "Theme Builder" completo con arrastrar y
  soltar para encabezado/pie/plantillas de producto, sin tocar código en
  absoluto.
- Formularios avanzados con lógica condicional compleja (Elementor Forms Pro).
- Popups avanzados con múltiples condiciones de disparo.

Para el caso de **guauguauland.com**, con un catálogo de productos de
mascotas estándar, los shortcodes y hooks de este repositorio cubren el
95% de lo que muestra la demo de Petcio sin coste de licencia.
