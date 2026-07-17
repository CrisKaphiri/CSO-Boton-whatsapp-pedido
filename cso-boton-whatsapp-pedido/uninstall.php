<?php
/**
 * Se ejecuta automáticamente cuando alguien desinstala (elimina) el plugin
 * desde el panel de Plugins de WordPress. Limpia las opciones guardadas
 * en la base de datos para no dejar datos huérfanos.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'csowsp_phone' );
delete_option( 'csowsp_button_text' );
delete_option( 'csowsp_message_template' );
delete_option( 'csowsp_company_name' );

if ( is_multisite() ) {
    global $wpdb;
    $site_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );

    foreach ( $site_ids as $site_id ) {
        switch_to_blog( $site_id );
        delete_option( 'csowsp_phone' );
        delete_option( 'csowsp_button_text' );
        delete_option( 'csowsp_message_template' );
        delete_option( 'csowsp_company_name' );
        restore_current_blog();
    }
}
