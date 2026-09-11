# Guía de instalación paso a paso

## Requisitos

- WordPress con el tema **Petcio** y **WooCommerce** activos (ya los tienes en guauguauland.com).
- Elementor en su versión **gratuita** (no es necesario Elementor Pro).
- Acceso de administrador al escritorio de WordPress.

## Opción A — Instalar como plugin (recomendado)

Esta es la forma más ordenada y fácil de mantener/actualizar en el tiempo.

1. Descarga o clona este repositorio.
2. Comprime **solo** la carpeta
   `wp-content/plugins/ggl-personalizacion-petcio/` en un archivo `.zip`
   llamado, por ejemplo, `ggl-personalizacion-petcio.zip`.
   - Importante: el `.zip` debe contener directamente la carpeta
     `ggl-personalizacion-petcio` (no una carpeta contenedora extra).
3. En el escritorio de WordPress ve a **Plugins → Añadir nuevo → Subir
   plugin**.
4. Selecciona el `.zip` y pulsa **Instalar ahora**.
5. Pulsa **Activar plugin**.
6. Ve a **Apariencia → Editor de archivos de tema** (o mediante FTP/hosting)
   y abre `wp-content/plugins/ggl-personalizacion-petcio/ggl-personalizacion-petcio.php`
   para editar, si quieres, estas constantes con los datos reales del
   negocio:

   ```php
   define( 'GGL_BUSINESS_NAME', 'Guau Guau Land' );
   define( 'GGL_BUSINESS_PHONE', '+1 (787) 918-5519' );      // Texto visible
   define( 'GGL_BUSINESS_PHONE_TEL', '+17879185519' );       // Para el enlace tel: (solo dígitos y +)
   define( 'GGL_BUSINESS_ADDRESS', 'Tu dirección aquí' );
   define( 'GGL_FREE_SHIPPING_THRESHOLD', 50 );
   define( 'GGL_LOGO_MAX_WIDTH', '200px' );                   // Tamaño máximo del logo
   ```

7. En el menú lateral del escritorio aparecerán dos nuevos tipos de
   contenido: **Testimonios** y **Slides del Hero**. Añade ahí tus
   testimonios reales y las imágenes/textos del slider de portada.
8. En cualquier página (o en la portada con Elementor gratis), añade el
   widget **"Shortcode"** de Elementor y pega, por ejemplo:

   ```
   [ggl_hero_slider]
   [ggl_iconos_beneficios]
   [ggl_categorias_mascotas]
   [ggl_testimonios]
   ```

## Opción B — Instalar con WPCode (sin subir un plugin nuevo)

Si prefieres no instalar un plugin adicional y gestionar todo desde
**Code Snippets/WPCode** (gratuito):

1. Instala y activa el plugin **WPCode** (o **Code Snippets**) desde
   **Plugins → Añadir nuevo**.
2. Ve a **Code Snippets → Añadir nuevo**.
3. Crea un snippet por cada archivo dentro de `includes/`:
   - Tipo: **PHP Snippet**.
   - Pega el contenido del archivo **quitando la primera línea** `<?php`
     (WPCode ya ejecuta el snippet como PHP).
   - Ubicación de ejecución (*Insertion*): **Run Everywhere** (o
     *Frontend only* si el snippet solo afecta al front-end, por ejemplo
     `shortcodes-secciones.php`).
   - Guarda y marca el interruptor como **Active**.
4. Repite para los 5 archivos: `shortcodes-secciones.php`,
   `woocommerce-mejoras.php`, `optimizacion-seguridad.php`,
   `seo-schema.php`, `personalizacion-marca.php`.
5. Añade también las constantes del inicio de
   `ggl-personalizacion-petcio.php` (`GGL_BUSINESS_NAME`, etc.) en un
   snippet PHP propio que se ejecute **antes** que los demás (ordénalo
   primero en la lista, o usa la opción de prioridad de WPCode).
6. Crea un snippet de tipo **CSS Snippet** con el contenido de
   `assets/css/personalizacion.css`.
7. Crea un snippet de tipo **JS Snippet**, con ubicación **Footer**, con el
   contenido de `assets/js/personalizacion.js`.
8. Los tipos de contenido "Testimonios" y "Slides del Hero" se registran
   automáticamente al activar el snippet PHP correspondiente
   (`ggl-personalizacion-petcio.php`), igual que con la Opción A.

## Verificación después de instalar

- Ve al frontend de `guauguauland.com` y comprueba que no aparecen errores
  PHP visibles (avisos blancos con texto de error).
- Revisa la consola del navegador (F12 → Console) por si hay errores de
  JavaScript.
- Comprueba que el teléfono del header ("Call Us on") y el del footer
  ("Contact our customer happiness team") ya muestran
  `+1 (787) 918-5519` y que el enlace del teléfono (click en móvil) marca
  ese mismo número.
- Comprueba que el logo se ve más grande (hasta 200px de ancho) tanto en
  el header de escritorio como en el de móvil.
- Añade un producto reciente y confirma que aparece la insignia **"Nuevo"**.
- Añade productos al carrito por debajo del umbral de envío gratis y
  comprueba que aparece la barra de progreso.
- Desde el móvil, entra a una página de producto y haz scroll: debe
  aparecer la barra fija de "Comprar" en la parte inferior.

## Rollback (desactivar todo)

- **Opción A**: ve a **Plugins** y desactiva/elimina **GGL – Personalización
  Petcio**.
- **Opción B**: ve a **Code Snippets** y desactiva (o borra) los 6 snippets
  creados.

En ambos casos el sitio vuelve exactamente al estado anterior, ya que este
código no modifica archivos del tema ni de WooCommerce.
