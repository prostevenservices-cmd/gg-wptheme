# Ejemplos de uso de los shortcodes

Puedes pegar cualquiera de estos shortcodes:

- Dentro de un widget **"Shortcode"** de Elementor (versión gratuita).
- Dentro de un bloque **"Shortcode"** del editor de bloques de WordPress.
- Directamente en el contenido de una página/entrada.

## `[ggl_hero_slider]`

```
[ggl_hero_slider]
[ggl_hero_slider cantidad="4" altura="600"]
```

Antes de usarlo, crea contenido en **Slides del Hero** (menú del
escritorio) con: título, extracto (texto corto), imagen destacada y, si
quieres, el texto/URL del botón en el metabox "Datos del botón".

## `[ggl_testimonios]`

```
[ggl_testimonios]
[ggl_testimonios cantidad="6"]
```

Crea cada testimonio en **Testimonios** (menú del escritorio):
título = nombre del cliente, contenido = la cita/opinión, imagen
destacada = foto (opcional).

## `[ggl_categorias_mascotas]`

```
[ggl_categorias_mascotas]
[ggl_categorias_mascotas categorias="perro,gato,ave,pez"]
[ggl_categorias_mascotas cantidad="4"]
```

Usa los slugs reales de tus categorías de producto de WooCommerce
(**Productos → Categorías**). Asigna una imagen a cada categoría desde esa
misma pantalla para que se vea igual que la demo.

## `[ggl_iconos_beneficios]`

```
[ggl_iconos_beneficios]

[ggl_iconos_beneficios items="Envío gratis|Compras superiores a $30.000;Cambios sin costo|Dentro de los primeros 30 días;Pago seguro|Todas las tarjetas y Webpay"]
```

Formato del atributo `items`: cada beneficio separado por `;`, y dentro
de cada beneficio, `Título|Subtítulo`.

## `[ggl_countdown]`

```
[ggl_countdown fecha="2026-12-31 23:59:59" texto="Oferta de fin de año" link="https://guauguauland.com/tienda"]
```

- `fecha`: formato `AAAA-MM-DD HH:MM:SS`, en la zona horaria configurada en
  **Ajustes → General** de WordPress.
- `texto`: título que aparece arriba del reloj.
- `link` (opcional): si lo indicas, se muestra un botón "Comprar ahora".
