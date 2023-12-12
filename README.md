# PluginExamen

# Descripcion

Creación de un plugin que convierta en mayuscula las palabras de una base de datos en un post.

# Explicacion del codigo



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

  Lo primero seria crear una funcion que cree la base de datos donde vamos a guardar las palabras que queremos poner en mayusculas

     $wpdb->insert($table_name, array('word' => 'hola'));
    $wpdb->insert($table_name, array('word' => 'buenas'));

  Lo siguente seria insertar en la base de datos las palabras que queremos cambiar

    add_action('publish_post', 'custom_plugin_process_post');

  Hacemos que se ejecute al publicar un post

       $words = $wpdb->get_col("SELECT word FROM $table_name");
  Ahora hacemso el select de todas las palabras y als metemos en la variable words


    if ($words) {
    
 Obtener contenido del post
        
        $post_content = get_post_field('post_content', $post_id);

Reemplazar las palabras en el contenido con versiones en mayúsculas

       $post_content_upper = str_ireplace($words, array_map('strtoupper', $words), $post_content);

Actualizar el contenido del post
        
        wp_update_post(array('ID' => $post_id, 'post_content' => $post_content_upper));

Obtener título del post
        
        $post_title = get_post_field('post_title', $post_id);

Reemplazar las palabras en el título con versiones en mayúsculas

        $post_title_upper = str_ireplace($words, array_map('strtoupper', $words), $post_title);

 Actualizar el título del post
 
        wp_update_post(array('ID' => $post_id, 'post_title' => $post_title_upper));
    }
