# CSO - Botón WhatsApp Pedido

Plugin de WordPress/WooCommerce que agrega un botón (shortcode) para que el cliente envíe por WhatsApp el resumen de su pedido — productos, número y total — leído directamente desde los datos reales del pedido en WooCommerce.

## Descripción

Muchas tiendas que coordinan pagos por transferencia necesitan que el cliente avise por WhatsApp después de comprar, adjuntando el detalle del pedido. Este plugin arma ese mensaje automáticamente, **en el servidor**, usando el objeto `WC_Order` de WooCommerce.

El botón solo aparece en la página real de "Pedido recibido" del pedido correspondiente, verificando la *key* de seguridad de WooCommerce — en cualquier otra página, simplemente no se muestra nada.

## Características

- **Verificación real del pedido**: usa `is_wc_endpoint_url()`, el ID de pedido de la URL, y la *order key* de WooCommerce — la misma validación que usa WooCommerce internamente.
- **Mensaje configurable** con comodines que se reemplazan automáticamente: `{nombre_empresa}`, `{productos}`, `{numero_pedido}`, `{total}`.
- **Número de WhatsApp y nombre de empresa** configurables desde el panel, sin tocar código.
- **Sin JavaScript**: el link de WhatsApp se genera ya armado desde PHP.
- **Estilo de botón incluido**, pensado para integrarse con un botón ya diseñado en un builder visual como Elementor.
- Verificación de dependencia de WooCommerce, con aviso en el admin si no está activo.
- Menú de administración compartido con otros plugins CSO (bajo un mismo "CSO" en el sidebar).
- Limpieza automática de datos al desinstalar (`uninstall.php`).

## Shortcode disponible

| Shortcode | Descripción |
|---|---|
| `[cso_boton_whatsapp_pedido]` | Muestra el botón, solo en la página de pedido recibido válida. |

## Requisitos

- WordPress 6.0+
- WooCommerce activo (obligatorio)
- PHP 7.4+
- Elementor u otro constructor de páginas: **opcional**, solo necesario si quieres insertar el shortcode manualmente con un widget visual.

## Instalación

1. Descarga el `.zip` desde este repositorio (o clónalo y comprime la carpeta `cso-boton-whatsapp-pedido/`).
2. En tu WordPress, ve a **Plugins → Añadir nuevo → Subir plugin**.
3. Selecciona el `.zip` y haz clic en **Instalar ahora**.
4. Activa el plugin.
5. Ve a **CSO → Botón WhatsApp** para configurar el número de destino, el nombre de tu empresa y el mensaje.
6. Inserta el shortcode `[cso_boton_whatsapp_pedido]` en tu página de "Pedido recibido".

## Capturas de pantalla

> _Agrega aquí capturas del panel de administración y del botón renderizado en la página de pedido recibido._

## Estructura del repositorio

```
cso-boton-whatsapp-pedido/
├── cso-boton-whatsapp-pedido.php   # Lógica principal del plugin
├── uninstall.php                    # Limpieza de datos al desinstalar
└── readme.txt                       # Formato estándar de WordPress.org
```

## Changelog

Ver [`readme.txt`](./cso-boton-whatsapp-pedido/readme.txt) para el historial completo de versiones.

## Licencia

GPLv2 or later — ver [LICENSE](./LICENSE).

## Autor

Cristóbal Sánchez Orellana
