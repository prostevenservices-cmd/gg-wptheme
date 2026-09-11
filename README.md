# gg-wptheme — Personalización Petcio para Guau Guau Land

Snippets / plugin a medida para personalizar el tema **Petcio** (WooCommerce)
en **[guauguauland.com](https://guauguauland.com)** sin pagar la suscripción
anual de **Elementor Pro**.

Sitio de referencia del tema (demo oficial): https://petcio.wpbingosite.com

## ¿Qué incluye este repositorio?

Un plugin ligero, propio y 100% editable llamado **GGL – Personalización
Petcio**, ubicado en:

```
wp-content/plugins/ggl-personalizacion-petcio/
├── ggl-personalizacion-petcio.php   # Archivo principal (constantes + carga de módulos)
├── includes/
│   ├── shortcodes-secciones.php     # Slider, testimonios, categorías, beneficios, countdown
│   ├── woocommerce-mejoras.php      # Envío gratis, insignia "Nuevo", barra fija móvil, campo mascota...
│   ├── optimizacion-seguridad.php   # Limpieza de head, emojis, XML-RPC, login, etc.
│   ├── seo-schema.php               # Schema.org PetStore + meta description/OG de respaldo
│   └── personalizacion-marca.php    # Teléfono real de la tienda (reemplaza el de demo) + tamaño del logo
└── assets/
    ├── css/personalizacion.css
    └── js/personalizacion.js
```

Todo el código sustituye funcionalidades que en Elementor solo están
disponibles en la versión **Pro** (Slides, Nested Carousel, Countdown,
Theme Builder para WooCommerce), usando **shortcodes y hooks nativos** de
WordPress/WooCommerce. Así puedes seguir usando **Elementor gratuito** (el
widget "Shortcode" está disponible en la versión libre) para maquetar tus
páginas sin pagar la licencia anual.

## Instalación (dos formas, elige la que prefieras)

### Opción A — Como plugin (recomendado)

1. Comprime la carpeta `wp-content/plugins/ggl-personalizacion-petcio/` en un
   `.zip`.
2. En tu WordPress: **Plugins → Añadir nuevo → Subir plugin** y sube el zip.
3. Activa el plugin **GGL – Personalización Petcio**.
4. Revisa las constantes al principio de `ggl-personalizacion-petcio.php`
   (`GGL_BUSINESS_PHONE`, `GGL_BUSINESS_PHONE_TEL`, `GGL_BUSINESS_ADDRESS`,
   `GGL_FREE_SHIPPING_THRESHOLD`, `GGL_LOGO_MAX_WIDTH`) y ajústalas a los
   datos reales de la tienda. Actualmente el teléfono está configurado como
   `+1 (787) 918-5519` y el logo con `max-width: 200px`.

### Opción B — Con el plugin gratuito "WPCode" (Code Snippets)

Si prefieres no subir un plugin nuevo, puedes copiar el contenido de cada
archivo de `includes/` como un snippet independiente:

1. Instala el plugin gratuito **WPCode** (o **Code Snippets**).
2. Crea un snippet nuevo de tipo **PHP** por cada archivo de `includes/`
   (pega el contenido **sin** la primera línea `<?php`, WPCode ya la añade).
3. Crea un snippet de tipo **CSS** con el contenido de
   `assets/css/personalizacion.css`.
4. Crea un snippet de tipo **JS** (inserción en el `footer`) con el
   contenido de `assets/js/personalizacion.js`.
5. Activa (`Active`) los 6 snippets.

Más detalle paso a paso en [`docs/GUIA-INSTALACION.md`](docs/GUIA-INSTALACION.md).

## Shortcodes disponibles

| Shortcode | Qué hace | Sustituye a (Elementor Pro) |
|---|---|---|
| `[ggl_hero_slider]` | Slider a pantalla completa con las "Slides del Hero" (nuevo tipo de contenido en el escritorio de WP) | Widget "Slides" |
| `[ggl_testimonios]` | Carrusel de testimonios con el nuevo tipo de contenido "Testimonios" | Widget "Nested Carousel" / "Testimonial" |
| `[ggl_categorias_mascotas]` | Grid con las categorías de producto de WooCommerce (Perro, Gato, Ave, Pez...) | Widget de productos con Theme Builder |
| `[ggl_iconos_beneficios]` | Franja "Envío gratis / Devolución 30 días / Pago seguro" | Icon List / Icon Box (esto ya existe en Elementor gratis, se incluye por comodidad) |
| `[ggl_countdown fecha="2026-12-31 23:59:59" texto="Oferta de verano"]` | Banner con cuenta atrás | Widget "Countdown" |

Más ejemplos de uso en [`docs/EJEMPLOS-SHORTCODES.md`](docs/EJEMPLOS-SHORTCODES.md).

## Teléfono y logo

El tema Petcio muestra un teléfono de demostración en el header
(`1300 655 896`) y otro en el footer (`(02) 6188 8062`), ambos definidos en
las opciones del tema/Elementor, no en código. El módulo
`personalizacion-marca.php` reemplaza automáticamente ambos números (texto
visible y enlace `tel:`) por el teléfono real `+1 (787) 918-5519` en todo el
HTML de cada página, y aumenta el logo (`.wpbingoLogo img`, usado en el
header y en las versiones móvil/escritorio) a `max-width: 200px`.

Para cambiar el teléfono o el tamaño del logo en el futuro, edita
únicamente las constantes `GGL_BUSINESS_PHONE`, `GGL_BUSINESS_PHONE_TEL` y
`GGL_LOGO_MAX_WIDTH` al principio de `ggl-personalizacion-petcio.php` (o el
snippet PHP equivalente si usaste la Opción B).

## Footer (plantilla de Elementor bloqueada)

El footer de Petcio es una plantilla de Elementor global (post `#25112`)
que no siempre se puede editar desde el front-end (Elementor no muestra el
botón "Editar con Elementor" sobre el header/footer porque no forman parte
del contenido de la página que se está visitando). Para editarla
directamente:

1. Entra al escritorio de WordPress.
2. Ve a la URL `https://guauguauland.com/wp-admin/post.php?post=25112&action=edit`.
3. Si aparece el botón azul **"Editar con Elementor"**, haz clic para abrir
   el editor visual y modificar los enlaces de "Shop / Customer Services /
   About Us" (que en la demo apuntan a `#`) con las URLs reales de tu tienda.

Mientras tanto, este plugin ya corrige automáticamente (sin tocar
Elementor) las partes del footer que sí se pueden resolver por código:

- **Logo del footer**: sustituye la imagen de demostración ("petcio") por
  el logo real definido en `GGL_LOGO_URL`, con el tamaño de
  `GGL_LOGO_MAX_WIDTH`.
- **Copyright**: cambia "© 2024 – Petcio. All Rights Reserved." por
  "© [año actual] Guau Guau Land. Todos los derechos reservados.".
- **Redes sociales**: registra el shortcode `[social_link]` (que la
  plantilla ya usa pero se mostraba como texto literal porque no existía)
  y muestra iconos de Facebook/Instagram/WhatsApp usando las URLs de
  `GGL_SOCIAL_FACEBOOK`, `GGL_SOCIAL_INSTAGRAM` y `GGL_SOCIAL_WHATSAPP`
  (por defecto en `#`; edítalas con tus perfiles reales para que se
  muestren los iconos).

## Botón "Book Grooming" (reemplaza "All Categories")

El módulo `boton-reservar-cita.php` oculta el widget "All Categories" del
header de escritorio (el menú desplegable de categorías de producto) y
coloca en su lugar un botón simple, sin dropdown, con el texto
**"Book Grooming"**, texto blanco y fondo del color de acento del tema,
que enlaza directo a `GGL_URL_RESERVAR_CITA` (por defecto
`https://guauguauland.com/reservar-cita/`).

## Mejoras de WooCommerce incluidas

- Insignia **"Nuevo"** automática en productos publicados hace menos de 30 días.
- **Barra de progreso de envío gratis** en el carrito y mini-carrito.
- **Barra fija de "Comprar"** en móvil dentro de la página de producto.
- Campo de checkout **"Nombre de tu mascota"** (se guarda en el pedido, email y escritorio).
- Productos relacionados limitados a 4 en 4 columnas.

## Rendimiento, seguridad y SEO

- Desactiva los emojis nativos de WordPress (menos peticiones).
- Limpia el `<head>` de enlaces innecesarios (RSD, wlwmanifest, versión de WP...).
- Desactiva pingbacks de XML-RPC y oculta el header `X-Pingback`.
- Bloquea la enumeración de usuarios vía `?author=1`.
- Limita los intentos de login fallidos (5 intentos / 15 min por IP).
- Restringe el editor de archivos de temas/plugins a superadmins.
- Datos estructurados **Schema.org `PetStore`** + meta description/Open Graph
  de respaldo (se desactiva automáticamente si ya usas Yoast SEO, Rank Math
  o All in One SEO, para no duplicar).

Ver el detalle completo y la equivalencia con Elementor Pro en
[`docs/MAPEO-ELEMENTOR-PRO-GRATIS.md`](docs/MAPEO-ELEMENTOR-PRO-GRATIS.md).

## Notas importantes

- Este código está pensado para instalarse **además** de Petcio + WooCommerce
  + Elementor (gratis), no los sustituye.
- Los textos de demostración (teléfono, horarios, textos del hero, etc.) que
  ves en la demo de Petcio y que aún aparecen en guauguauland.com se editan
  directamente desde el **Personalizador de WordPress** o **Elementor**, no
  requieren código: solo hay que sustituir el contenido de ejemplo por el
  real de la tienda.
- Antes de activar en producción, pruébalo primero en un entorno de
  *staging* o en local (por ejemplo con LocalWP o un subdominio de pruebas).
