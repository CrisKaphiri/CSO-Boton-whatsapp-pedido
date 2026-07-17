<?php
/**
 * Plugin Name: CSO - Botón WhatsApp Pedido
 * Description: Agrega un shortcode con un botón que envía por WhatsApp un resumen del pedido (productos, número y total), leído directamente desde los datos reales del pedido en WooCommerce — solo visible en la página de "Pedido recibido" del pedido correspondiente.
 * Version: 2.2.2
 * Author: Cristóbal Sánchez Orellana
 * Text Domain: cso-boton-whatsapp
 * Requires Plugins: woocommerce
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // No acceso directo.
}

class CSO_Boton_Whatsapp_Pedido {

    const OPTION_PHONE        = 'csowsp_phone';
    const OPTION_BTN_TEXT     = 'csowsp_button_text';
    const OPTION_MESSAGE      = 'csowsp_message_template';
    const OPTION_COMPANY_NAME = 'csowsp_company_name';
    const SETTINGS_SLUG   = 'cso-whatsapp-pedido';
    const CSO_PARENT_SLUG = 'cso-panel';
    const SHORTCODE       = 'cso_boton_whatsapp_pedido';

    public function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
    }

    public function init_plugin() {
        add_action( 'admin_notices', array( $this, 'maybe_show_woocommerce_notice' ) );

        if ( ! $this->woocommerce_is_active() ) {
            return;
        }

        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_shortcode( self::SHORTCODE, array( $this, 'render_shortcode' ) );
    }

    private function woocommerce_is_active() {
        return class_exists( 'WooCommerce' );
    }

    public function maybe_show_woocommerce_notice() {
        if ( $this->woocommerce_is_active() ) {
            return;
        }
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }
        echo '<div class="notice notice-error"><p><strong>CSO - Botón WhatsApp Pedido</strong> necesita que <strong>WooCommerce</strong> esté instalado y activo para funcionar.</p></div>';
    }

    /* =========================================================
     *  VALORES POR DEFECTO
     * ========================================================= */
    private function get_default_message() {
        return "Hola *{nombre_empresa}*, quiero comprar los siguientes juegos:\n*======= ⭒Juegos Comprados⭒ =======*\n{productos}\n*======= ⭒Detalle del Pedido⭒ =======*\nNúmero de Pedido: {numero_pedido}\nTotal: {total}\nAdjuntaré mi comprobante de transferencia.\n¡Gracias!";
    }

    private function get_company_name() {
        return get_option( self::OPTION_COMPANY_NAME, '' );
    }

    private function get_phone() {
        return get_option( self::OPTION_PHONE, '' );
    }

    private function get_button_text() {
        return get_option( self::OPTION_BTN_TEXT, 'Enviar Pedido por Whatsapp' );
    }

    private function get_message_template() {
        $msg = get_option( self::OPTION_MESSAGE, '' );
        return ( $msg !== '' ) ? $msg : $this->get_default_message();
    }

    /**
     * Obtiene el pedido actual SOLO si estamos realmente en su página de
     * "pedido recibido" y la key de seguridad coincide (misma validación
     * que usa WooCommerce internamente). Si no, devuelve null.
     */
    private function get_current_order_if_valid() {
        if ( ! function_exists( 'is_wc_endpoint_url' ) || ! is_wc_endpoint_url( 'order-received' ) ) {
            return null;
        }

        $order_id = absint( get_query_var( 'order-received' ) );
        if ( $order_id <= 0 ) {
            return null;
        }

        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return null;
        }

        $submitted_key = isset( $_GET['key'] ) ? wc_clean( wp_unslash( $_GET['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

        if ( ! hash_equals( $order->get_order_key(), $submitted_key ) ) {
            return null;
        }

        return $order;
    }

    /* =========================================================
     *  SHORTCODE: link estilo botón, armado 100% en el servidor
     * ========================================================= */
    public function render_shortcode( $atts ) {
        $order = $this->get_current_order_if_valid();

        // Fuera de una página de pedido válida, el shortcode no muestra nada.
        if ( ! $order ) {
            return '';
        }

        $phone = $this->get_phone();
        if ( ! $phone ) {
            return ''; // Sin número configurado, no mostramos un botón que no puede funcionar.
        }

        $products_text = '';
        foreach ( $order->get_items() as $item ) {
            $quantity = $item->get_quantity();
            $name     = $item->get_name();
            $products_text .= $quantity . 'x - ' . $name . "\n";
        }

        $order_number = $order->get_order_number();
        $total        = html_entity_decode( wp_strip_all_tags( $order->get_formatted_order_total() ), ENT_QUOTES, 'UTF-8' );

        $template = $this->get_message_template();
        $message  = str_replace(
            array( '{productos}', '{numero_pedido}', '{total}', '{nombre_empresa}' ),
            array( trim( $products_text ), $order_number, $total, $this->get_company_name() ),
            $template
        );

        $url     = 'https://wa.me/' . rawurlencode( $phone ) . '?text=' . rawurlencode( $message );
        $btn_txt = $this->get_button_text();

        ob_start();
        ?>
        <a href="<?php echo esc_attr( $url ); ?>" target="_blank" rel="noopener noreferrer" class="cso-wsp-btn"><?php echo esc_html( $btn_txt ); ?></a>
        <style>
            a.cso-wsp-btn,
            a.cso-wsp-btn:link,
            a.cso-wsp-btn:visited {
                display: inline-block;
                background-color: #25D366;
                color: #181818 !important;
                border: none;
                border-radius: 25px;
                padding: 10px 24px;
                font-family: 'Lexend Deca', sans-serif;
                font-size: 16px;
                font-weight: 500;
                text-decoration: none;
                cursor: pointer;
                transition: background-color 0.2s ease;
            }
            a.cso-wsp-btn:hover,
            a.cso-wsp-btn:focus,
            a.cso-wsp-btn:active {
                background-color: #2BF777;
                color: #181818 !important;
            }
        </style>
        <?php
        return ob_get_clean();
    }

    /* =========================================================
     *  PANEL DE ADMINISTRACIÓN
     * ========================================================= */
    public function add_settings_page() {
        // Si ningún otro plugin "CSO" ya creó el menú padre, lo creamos nosotros.
        // Así este plugin funciona igual de bien solo, o junto a otros plugins CSO.
        if ( ! isset( $GLOBALS['admin_page_hooks'][ self::CSO_PARENT_SLUG ] ) ) {
            add_menu_page(
                'CSO',
                'CSO',
                'manage_woocommerce',
                self::CSO_PARENT_SLUG,
                array( $this, 'render_settings_page' ),
                'dashicons-store',
                56
            );
        }

        add_submenu_page(
            self::CSO_PARENT_SLUG,
            'Botón WhatsApp',
            'Botón WhatsApp',
            'manage_woocommerce',
            self::SETTINGS_SLUG,
            array( $this, 'render_settings_page' )
        );

        // WordPress agrega automáticamente un submenú "fantasma" que duplica
        // el nombre del menú padre la primera vez que se registra un submenú.
        // Lo quitamos aquí (después de agregar el submenú, que es cuando WP
        // realmente lo crea). Si otro plugin CSO ya lo quitó antes, esta
        // llamada simplemente no hace nada.
        remove_submenu_page( self::CSO_PARENT_SLUG, self::CSO_PARENT_SLUG );
    }

    public function register_settings() {
        register_setting( self::SETTINGS_SLUG, self::OPTION_PHONE, array(
            'sanitize_callback' => array( $this, 'sanitize_phone' ),
        ) );
        register_setting( self::SETTINGS_SLUG, self::OPTION_BTN_TEXT, array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
        register_setting( self::SETTINGS_SLUG, self::OPTION_MESSAGE, array(
            'sanitize_callback' => 'sanitize_textarea_field',
        ) );
        register_setting( self::SETTINGS_SLUG, self::OPTION_COMPANY_NAME, array(
            'sanitize_callback' => 'sanitize_text_field',
        ) );
    }

    public function sanitize_phone( $input ) {
        return preg_replace( '/\D/', '', (string) $input );
    }

    public function render_settings_page() {
        $phone        = $this->get_phone();
        $btn_txt      = $this->get_button_text();
        $message      = $this->get_message_template();
        $company_name = $this->get_company_name();
        ?>
        <div class="wrap">
            <h1>Botón WhatsApp Pedido</h1>
            <p><strong>⚠️ Este botón solo funciona en la página de "Pedido recibido" de WooCommerce.</strong> En cualquier otra página, el shortcode no muestra nada.</p>
            <p>Configura aquí el número de destino y el texto del mensaje que se envía por WhatsApp al presionar el botón.</p>

            <form method="post" action="options.php">
                <?php settings_fields( self::SETTINGS_SLUG ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">Nombre de la empresa</th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( self::OPTION_COMPANY_NAME ); ?>"
                                value="<?php echo esc_attr( $company_name ); ?>" class="regular-text"
                                placeholder="Ej: Mi Tienda">
                            <p class="description">Se usa donde el mensaje tenga el comodín <code>{nombre_empresa}</code>.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Número de WhatsApp</th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( self::OPTION_PHONE ); ?>"
                                value="<?php echo esc_attr( $phone ); ?>" class="regular-text"
                                placeholder="56912345678">
                            <p class="description">Con código de país, solo números (sin +, espacios ni guiones). Ejemplo: <code>56912345678</code>.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Texto del botón</th>
                        <td>
                            <input type="text" name="<?php echo esc_attr( self::OPTION_BTN_TEXT ); ?>"
                                value="<?php echo esc_attr( $btn_txt ); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Mensaje de WhatsApp</th>
                        <td>
                            <textarea name="<?php echo esc_attr( self::OPTION_MESSAGE ); ?>" rows="10" class="large-text code"><?php echo esc_textarea( $message ); ?></textarea>
                            <p class="description">
                                Puedes usar estos comodines, se reemplazan automáticamente con los datos reales del pedido:<br>
                                <code>{nombre_empresa}</code> — el nombre configurado arriba.<br>
                                <code>{productos}</code> — lista de productos comprados (cantidad + nombre).<br>
                                <code>{numero_pedido}</code> — número de la orden.<br>
                                <code>{total}</code> — total del pedido, ya formateado con el símbolo de moneda.
                            </p>
                        </td>
                    </tr>
                </table>

                <h2>Shortcode</h2>
                <p>Inserta este shortcode donde quieras mostrar el botón (por ejemplo, con el widget de "Shortcode" de Elementor en tu página de pedido recibido). En cualquier otra página no se mostrará nada.</p>
                <p><code>[<?php echo self::SHORTCODE; ?>]</code></p>

                <?php submit_button( 'Guardar cambios' ); ?>
            </form>
        </div>
        <?php
    }
}

new CSO_Boton_Whatsapp_Pedido();
