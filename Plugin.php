<?php
/*
Plugin Name: Custom Plugin
Description: Plugin personalizado que convierte a mayúsculas las palabras de una base de datos en un post.
*/

// Acción para ejecutar al activar el plugin
register_activation_hook(__FILE__, 'custom_plugin_activate');

function custom_plugin_activate() {

    create_custom_database();
}

// Función para crear la base de datos
function create_custom_database() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'custom_words';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        word varchar(50) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);

   //Aqui añadimos las palabras a la base de datos
    $wpdb->insert($table_name, array('word' => 'hola'));
    $wpdb->insert($table_name, array('word' => 'buenas'));
}

// Acción para ejecutar al publicar un post
add_action('publish_post', 'custom_plugin_process_post');

function custom_plugin_process_post($post_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'custom_words';

    // Obtener palabras de la base de datos
    $words = $wpdb->get_col("SELECT word FROM $table_name");

    if ($words) {
        // Obtener contenido del post
        $post_content = get_post_field('post_content', $post_id);

        // Reemplazar las palabras en el contenido con versiones en mayúsculas
        $post_content_upper = str_ireplace($words, array_map('strtoupper', $words), $post_content);

        // Actualizar el contenido del post
        wp_update_post(array('ID' => $post_id, 'post_content' => $post_content_upper));

        // Obtener título del post
        $post_title = get_post_field('post_title', $post_id);

        // Reemplazar las palabras en el título con versiones en mayúsculas
        $post_title_upper = str_ireplace($words, array_map('strtoupper', $words), $post_title);

        // Actualizar el título del post
        wp_update_post(array('ID' => $post_id, 'post_title' => $post_title_upper));
    }
}
?>
