=== CSO - Botón WhatsApp Pedido ===
Contributors: cristobalsanchezorellana
Tags: woocommerce, whatsapp, pedidos, checkout, botón
Requires at least: 6.0
Tested up to: 6.6
Requires PHP: 7.4
Requires Plugins: woocommerce
Stable tag: 2.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Agrega un botón/shortcode que envía por WhatsApp un resumen del pedido (productos, número y total), leído directamente desde los datos reales del pedido en WooCommerce.

== Description ==

CSO - Botón WhatsApp Pedido resuelve un flujo muy común en tiendas que coordinan pagos por transferencia: después de comprar, el cliente necesita avisar por WhatsApp con el detalle de su pedido para que lo procesen manualmente.

En vez de que el cliente escriba el mensaje a mano (o de depender de JavaScript leyendo el HTML de la pantalla, algo frágil), este plugin arma el mensaje **en el servidor**, usando los datos reales del pedido de WooCommerce (`WC_Order`), y genera un botón/link listo para abrir WhatsApp con el mensaje precargado.

**Características principales**

* El botón solo se muestra en la página real de "Pedido recibido" del pedido correspondiente (verificado con la key de seguridad de WooCommerce) — en cualquier otra página, el shortcode no muestra nada.
* Mensaje totalmente configurable, con comodines que se reemplazan automáticamente: `{nombre_empresa}`, `{productos}`, `{numero_pedido}`, `{total}`.
* Número de WhatsApp y nombre de la empresa configurables desde el panel, sin tocar código.
* Estilo del botón incluido (color, radio, tipografía), pensado para integrarse con un botón ya diseñado en un builder visual como Elementor.
* No depende de JavaScript para funcionar: el link de WhatsApp ya viene armado desde el servidor.

**Shortcode disponible**

* `[cso_boton_whatsapp_pedido]` — muestra el botón, solo en la página de pedido recibido válida.

**Requisitos**

* WooCommerce activo (obligatorio).
* Elementor u otro constructor de páginas es opcional — solo se necesita si quieres insertar el shortcode manualmente con un widget visual.

**Nota sobre el menú de administración**

Este plugin registra su panel bajo un menú "CSO" en el admin de WordPress. Si también tienes instalado el plugin "CSO - Descuentos por Lote", ambos comparten el mismo menú padre.

== Installation ==

1. Descarga el `.zip` del plugin.
2. En tu WordPress, ve a Plugins → Añadir nuevo → Subir plugin.
3. Selecciona el `.zip` y haz clic en "Instalar ahora".
4. Activa el plugin.
5. Ve a **CSO → Botón WhatsApp** para configurar el número de destino, el nombre de tu empresa y el mensaje.
6. Inserta el shortcode `[cso_boton_whatsapp_pedido]` en tu página de "Pedido recibido" (por ejemplo, con el widget de Shortcode de Elementor).

== Frequently Asked Questions ==

= ¿Por qué no veo el botón al insertar el shortcode en una página cualquiera? =

Es el comportamiento esperado: el botón solo se genera cuando la página es realmente la de "pedido recibido" de un pedido válido (verificado con su key de seguridad). En cualquier otra página, el shortcode no devuelve nada.

= ¿Qué pasa si no configuro el número de WhatsApp? =

El shortcode tampoco muestra el botón — evita mostrar un botón que no podría funcionar.

= ¿Necesito Elementor? =

No, es obligatorio solo WooCommerce. Elementor (u otro builder) es opcional, solo para insertar el shortcode visualmente donde quieras.

== Changelog ==

= 2.2.2 =
* El campo de "Nombre de la empresa" ahora viene vacío por defecto (antes traía un valor de ejemplo precargado).
* Aviso de "solo funciona en la página de pedido recibido" ahora se destaca al principio del panel.

= 2.2.1 =
* Corrección del submenú "fantasma" duplicado en el menú CSO compartido.

= 2.2.0 =
* El menú de administración ahora es compartido con otros plugins CSO bajo un mismo menú padre "CSO".
* Nuevo campo de "Nombre de la empresa", con comodín `{nombre_empresa}` en el mensaje.
* Altura del botón ajustada (padding vertical reducido).

= 2.1.0 =
* Corrección de color de texto del botón (normal y hover) para que no sea sobrescrito por estilos del tema.
* Corrección del signo de moneda mostrado como entidad HTML (`&#36;`) en vez de `$`.
* Corrección de saltos de línea y asteriscos perdidos en el mensaje, causados por el escapado de la URL.

= 2.0.0 =
* Cambio de arquitectura: el mensaje ahora se arma con los datos reales del pedido en el servidor (WooCommerce), en vez de leer el HTML de la página con JavaScript.
* El botón ahora es un link directo a WhatsApp, sin necesidad de JavaScript.

= 1.0.0 =
* Versión inicial: botón con JavaScript que lee el DOM de la página para armar el mensaje de WhatsApp.
