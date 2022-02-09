<?php
/*
 Plugin Name: WP Contact Form 7 DB Handler
 Plugin URI:
 description: This plugin is created to display Contact Form 7 submitted data from database.
 Version: 1.2
 Author: Yudiz Solutions Pvt. Ltd.
 Author URI: https://www.yudiz.com/
 License: 
*/

/*/----- register custom posttype for ys_cfdbh ------/*/
add_action( 'init', 'ys_cfdbh_custom_postyype' );
function ys_cfdbh_custom_postyype() {
  $ys_cfdbhLabels = array(
    'name' => _x( 'ys_cfdbh', 'Post Type General Name'),
    'singular_name' => _x( 'ys_cfdbh', 'Post Type Singular Name'),
    'menu_name' => __( 'ys_cfdbh'),
    'parent_item_colon' => __( 'Parent ys_cfdbh'),
    'all_items' => __( 'All ys_cfdbh'),
    'view_item' => __( 'View ys_cfdbh'),
    'add_new_item' => __( 'Add New ys_cfdbh'),
    'add_new' => __( 'Add New ys_cfdbh'),
    'edit_item' => __( 'Edit ys_cfdbh'),
    'update_item' => __( 'Update ys_cfdbh'),
    'search_items' => __( 'Search ys_cfdbh'),
    'not_found' => __( 'Not Found'),
    'not_found_in_trash' => __( 'Not found in Trash'),
  );
  $ys_cfdbhArgs = array(
    'description' => __( 'New ys_cfdbh'),
    'labels' => $ys_cfdbhLabels,
    'supports' => array( 'title', ),
    'hierarchical' => false,
    'public' => false,
    'show_ui' => true,
    'show_in_rest' => false,
    'show_in_menu' => false,
    'menu_icon' => 'dashicons-products',
    'show_in_nav_menus' => false,
    'show_in_admin_bar' => false,
    'menu_position' => 5,
    'can_export' => true,
    'has_archive' => false,
    'exclude_from_search' => false,
    'publicly_queryable' => true,
    'capability_type' => 'post',
    'capabilities' => array('create_posts' => 'do_not_allow',),
  );  
  register_post_type( 'ys_cfdbh', $ys_cfdbhArgs );
}

/*/ cf7dbh download file function /*/
add_action( 'init', 'ys_cfdbh_init');
function ys_cfdbh_init(){
    if( is_admin() ){
        require_once 'include/download-file-class.php';
        $csv = new expoert_ys_cfdbh_CSV();
        if( isset($_REQUEST['ys_cfdbh_csv']) &&  sanitize_text_field( $_REQUEST['ys_cfdbh_csv'] ) == true  && isset( $_REQUEST['ys_cfdbh_nonce'] ) ) {
            $nonce  = filter_input( INPUT_GET, 'ys_cfdbh_nonce', FILTER_SANITIZE_STRING );
            if ( ! wp_verify_nonce( $nonce, 'ys_cfdbh_nonce' ) ) wp_die('Invalid nonce...!');
            $csv->download_ys_cfdbh_csv_file();
        }
    }
}


/*/ unset the key with empty value /*/
add_filter( 'ys_cfdbh_before_save_data', function ( array $form_data ): array {
    foreach ( $form_data as $key => $value ) {
        if ( null === $value || '' === $value ) {
            unset( $form_data[ $key ] );
        }
    }
    return $form_data;
} );

/*/ callee the fucntion before sending an email /*/
add_action( 'wpcf7_before_send_mail', 'ys_cfdbh_before_send_mail' );
function ys_cfdbh_before_send_mail( $form_tag ) {
  $upload_dir    = wp_upload_dir();
  $ys_cfdbh_dirname = $upload_dir['basedir'].'/ys_cfdbh_uploads';
  if ( ! file_exists( $ys_cfdbh_dirname ) ) {
    wp_mkdir_p( $ys_cfdbh_dirname );
  } 
  $time_now = time();
  
  $form = WPCF7_Submission::get_instance();
  if ( $form ) :

    $black_list   = array('_wpcf7', '_wpcf7_version', '_wpcf7_locale', '_wpcf7_unit_tag',
    '_wpcf7_is_ajax_call','ys_cfdbh_name', '_wpcf7_container_post','_wpcf7cf_hidden_group_fields',
    '_wpcf7cf_hidden_groups', '_wpcf7cf_visible_groups', '_wpcf7cf_options','g-recaptcha-response');

    $data           = $form->get_posted_data();
    $files          = $form->uploaded_files();
    $uploaded_files = array();
    
    /* ys_cfdbh action can be execute action before the data i ssave . */
    $rm_underscore  = apply_filters('ys_cfdbh_remove_underscore_data', true); 

    foreach ($files as $file_key => $file) {
      array_push($uploaded_files, $file_key);
      $uploaded_files[$file_key] = $time_now.'-'.basename($file[0]);
      copy($file[0], $ys_cfdbh_dirname.'/'.$time_now.'-'.basename($file[0]));
    }

    /*/ store collected form data into array /*/
    $form_data   = array();
    foreach ($data as $key => $d) :
      $matches = array();
      $d = empty( $d ) ? ' ' : $d;
      if( $rm_underscore ) preg_match('/^_.*$/m', $key, $matches);
      if ( !in_array($key, $black_list ) && !in_array($key, $uploaded_files ) && empty( $matches[0] ) ) {
        /* chnage special character and escape sequence characters. */
        $tmpD = $d;
        if( ! is_array($d) ) {
          $bl   = array('\"',"\'",'/','\\','"',"'");
          $wl   = array('&quot;','&#039;','&#047;', '&#092;','&quot;','&#039;');
          $tmpD = str_replace($bl, $wl, $tmpD );
        }
        $form_data[$key] = $tmpD;
      }
      if ( array_key_exists($key, $uploaded_files ) ) {
        $form_data[$key.'ys_cfdbh_file'] = $uploaded_files[$key];
      }
    endforeach;

    /* ys_cfdbh before save data. */
    $form_data = apply_filters('ys_cfdbh_before_save_data', $form_data);

    /* ys_cfdbh action can be execute before the data is save . */
    do_action( 'ys_cfdbh_before_save', $form_data );
    $form_post_id = $form_tag->id();
    $form_value   = serialize( $form_data );
    $form_date    = current_time('Y-m-d H:i:s');

    /* ys_cfdbh savedCPT new post. */
    $formPostArgs = array(
      'post_type'     => 'ys_cfdbh',
      'post_title'    => wp_strip_all_tags( "Custom post" ),
      'post_content'  => $form_value,
      'post_status'   => 'unread',      
      'post_parent'   => $form_post_id,
    );
    $insert_id = wp_insert_post( $formPostArgs, true);

    /*/ Count strioes into post meta forn make count sortable /*/
    $count_key = 'ys_cfdbh_send_in_count';
    $count = get_post_meta($form_post_id, $count_key, true);
    if($count==''){
        delete_post_meta($form_post_id, $count_key);
        add_post_meta($form_post_id, $count_key, '1');
    }else{
        $count++;
        update_post_meta($form_post_id, $count_key, $count);
    }
    /* ys_cfdbh after save post data do action with given post id. */
    do_action( 'ys_cfdbh_after_save_data', $insert_id );

  endif;
}

/*/ create Admin Menu /*/
add_action( 'admin_menu', 'ys_cfdbh_menu' );  
function ys_cfdbh_menu(){    
  $page_title = 'ContactForm DBH';   
  $menu_title = 'Contact Forms DBH';
  $capability = 'manage_options';
  $menu_slug  = 'contact-form-dbh';
  $function   = 'ys_cfdbh_show_admin_page';
  $icon_url   = 'dashicons-schedule';
  $position   = 40;
  add_menu_page( $page_title, $menu_title,  $capability,  $menu_slug,  $function,  $icon_url,  $position );
}

/*/ Admin Menu ys_cfdbh Page Function /*/
function  ys_cfdbh_show_admin_page(){ 

  /*/ Check IF contact form 7 plug in is activate or not /*/
  if ( ! class_exists('WPCF7_ContactForm') ) {
    wp_die( 'Please Install and activate the <a href="https://wordpress.org/plugins/contact-form-7/" target="_blank">contact form 7</a> plugin to continue.' );
  }
  
  /*/ add defult admin page script with fontawesome /*/
  wp_enqueue_style( 'ys-cfdbh-style', plugin_dir_url( __FILE__ ) . 'css/ys-cfdbh-style.css' );
  wp_enqueue_style( 'fontawesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), '4.7.0');  

  /*/ check page condition that whaich page is called like form inner for details /*/
  $post_parent  = empty($_GET['post_parent']) ? 0 : (int) sanitize_text_field( $_GET['post_parent'] );
  $response_id  = empty($_GET['ys_cfdbh_post_id']) ? 0 : (int) sanitize_text_field( $_GET['ys_cfdbh_post_id'] );

  /*/ if post_parent avalaible than is a form inner page  /*/
  if( !empty($post_parent) ){ 
    wp_enqueue_script( 'ys-cf7dbh-script', plugin_dir_url( __FILE__ ) . 'js/ys-cf7dbh-script.js' );
    require_once 'include/form-inner-page-class.php'; 
    new ys_cfdbh_Sub_Page();
    return;
  }
  
  /*/ if ys_cfdbh_post_id avalaible than it's a form details page  /*/
  if( !empty($response_id) ){
    require_once 'include/form-details-page-class.php'; 
    new ys_cfdbh_Form_Details();
    return;
  }

  /*/ if no condition found than show main page foum title with count  /*/
  require_once 'include/show-form-count-page-class.php'; 
  $ListTable = new ys_cfdbh_Main_List_Table();
  $ListTable->prepare_items();  ?>
  <div class="wrap">
    <div id="icon-users" class="icon32"></div>
    <script type="text/javascript"></script>
    <h2><?php _e( 'Contact Forms List', 'contact-form-ys-cfdb7' ); ?></h2>
    <?php $ListTable->display(); ?>
  </div>
  <?php
}

/*/ Ajex Function for the update hide columns on page /*/
add_action('wp_ajax_ys_cfdbh_update_view_list', 'ys_cfdbh_update_view_list');
function ys_cfdbh_update_view_list() {
  /*/ check request /*/
  if( isset( $_POST['conatctformId'] ) && isset( $_POST['Datalist'] ) && isset( $_POST['conatctformId'] ) ) :
    $columns = sanitize_text_field( $_POST['Datalist'] );
    $columns =  explode(',', $columns  );
    $conatctformId = sanitize_text_field( $_POST['conatctformId'] );
    /*/ Update the idin conatct form post meta tabel /*/
    if( update_post_meta( $conatctformId, 'show_form_columns', $columns ) ) :  
      echo esc_html( __( 'Updated the view columns list successfuly ...', 'contact-form-ys-cfdb7' ) );
    else :
      echo esc_html( __( '!Unable to update the view columns list.', 'contact-form-ys-cfdb7' ) );
    endif;

    if( isset( $_POST['newcolumnsnames'] ) && !empty( $_POST['newcolumnsnames'] ) ) :
      $newcolumnsnames = sanitize_text_field( $_POST['newcolumnsnames'] );
      $newcolumnsnames = urldecode( $newcolumnsnames );
      $chunks = array_chunk(preg_split('/(=|&)/', $newcolumnsnames ), 2);
      $result = array_combine(array_column($chunks, 0), array_column($chunks, 1) );
      update_post_meta( $conatctformId, 'ys_cfdbh_columns_names', $result );
    endif;

  else:
    echo esc_html( __( '!Request is Not Valid.', 'contact-form-ys-cfdb7' ) );
  endif;
  exit();
}