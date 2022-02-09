<?php
/*
Plugin Name: Drive Test - Google Drive Plugin
Plugin URI: 
Description: Drive helps you to access files from your Google Drive Account quickly and seamlessly.
Version: 1.0.0
Author: Mahipatsinh
Author URI: https://yudiz.com
License: GPL2

*/

/*/---- Ragister Post Type for Drive Test ----/*/
add_action( 'init', 'registerDriveTestCustomPostType' );
function registerDriveTestCustomPostType() {
  /*/---- create new posttype for Banner Slider ------/*/
  $projectLabels = array(
      'name'                => _x( 'Projects', 'Post Type General Name', 'Drive Test' ),
      'singular_name'       => _x( 'Project', 'Post Type Singular Name', 'Drive Test' ),
      'menu_name'           => __( 'Drive Bridge', 'Drive Test' ),
      'parent_item_colon'   => __( 'Parent Projects', 'Drive Test' ),
      'all_items'           => __( 'All Projects', 'Drive Test' ),
      'view_item'           => __( 'View Projects', 'Drive Test' ),
      'add_new_item'        => __( 'Add New Project', 'Drive Test' ),
      'add_new'             => __( 'Add New', 'Drive Test' ),
      'edit_item'           => __( 'Edit Project', 'Drive Test' ),
      'update_item'         => __( 'Update Project', 'Drive Test' ),
      'search_items'        => __( 'Search Projects', 'Drive Test' ),
      'not_found'           => __( 'Not Found', 'Drive Test' ),
      'not_found_in_trash'  => __( 'Not found in Trash', 'Drive Test' ),
  );
  // Set other options for Custom Post Type Projects
  $projectArgs = array(
      'label'               => __( 'Projects', 'Drive Test' ),
      'description'         => __( 'Projects', 'Drive Test' ),
      'labels'              => $projectLabels,
      'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields', 'revisions'  ),
      'taxonomies'          => array( 'genres' ),
      'hierarchical'        => false,
      'public'              => false,
      'show_ui'             => true,
      'show_in_menu'        => true,
      'show_in_rest'        => false,
      'show_in_nav_menus'   => true,
      'show_in_admin_bar'   => true,
      'menu_position'       => 5,
      'has_archive'         => false,
      'menu_icon'           => 'dashicons-cloud',
      'can_export'          => true,
      'exclude_from_search' => true,
      'publicly_queryable'  => true,
      'capability_type'     => 'post',
  );
  // Registering your Custom Post 
  register_post_type( 'ysdrive-projects', $projectArgs );
}

/*
 * Register a custom menu page.
 */
add_action( 'admin_menu', 'drive_test_register_my_custom_menu_page' );
function drive_test_register_my_custom_menu_page(){      
  add_submenu_page( 'edit.php?post_type=ysdrive-projects', 'Drive Set Up',  'Drive Set Up',  'manage_options', 'google-drive-setup-page', 'main_drive_custom_menu_pagefunc' );
  add_submenu_page( 'edit.php?post_type=ysdrive-projects', 'Google Drive',  'Check List',  'manage_options', 'google-drive-option-page', 'my_custom_options_pagefunc' );
}
 
/**
 * Display a custom menu page
 */
function main_drive_custom_menu_pagefunc() { 
    wp_enqueue_script( 'ys-custom-script', plugin_dir_url( __FILE__ ) . 'js/ysdrive-script.js' );
    require plugin_dir_path( __FILE__ ) . 'templates-part/setup-page.php'; 
}

//Add action link row action in enabled post type
add_filter( 'post_row_actions', 'drive_item_list_row_actions', 10, 2 );
add_filter( 'page_row_actions', 'drive_item_list_row_actions', 10, 2 );
function drive_item_list_row_actions( $actions, $post ) {
  //Check condition if post type enabled
  if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'ysdrive-projects' ) {
    global $wp;
    $actions['drive-edit'] = '<a href="edit.php?post_type='. $_GET['post_type'] .'&page=google-drive-menu-page&post_id='. $post->ID .'"> Edit Drive </a>';
  }
  return $actions;
} 

function my_custom_options_pagefunc() { 
  wp_enqueue_script( 'ys-custom-script', plugin_dir_url( __FILE__ ) . 'js/ysdrive-script.js' );
  require plugin_dir_path( __FILE__ ) . 'templates-part/listing-page.php'; 
}


/*/added scritp on edit page/*/
add_action( 'admin_enqueue_scripts', 'ysdrive_include_script' );
function ysdrive_include_script() {
  if( isset($_GET['action']) && $_GET['action'] == 'edit' ) {
    // wp_register_style('wpb-jquery-ui-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/humanity/jquery-ui.css', false, null);
    // wp_enqueue_style('wpb-jquery-ui-style'); 
    wp_enqueue_script('jquery-ui-accordion');
    wp_enqueue_script( 'drive-script', plugin_dir_url( __FILE__ ) . 'js/ysdrive-script.js', array('jquery'), null, false );
  }
}

add_action("add_meta_boxes", "add_custom_meta_box");
function add_custom_meta_box() {
    add_meta_box("drive-bridge-options", "Drive Bridge Options", "drive_bridge_options_box", "ysdrive-projects", "advanced", "high", null);
    // add_meta_box("drive-bridge-checklist", "Drive Bridge Checklist", "drive_bridge_checklist_box", "ysdrive-projects", "advanced", "high", null);
}

function drive_bridge_options_box() {
  $post_folder = ($val = get_post_meta( get_the_ID(), 'posts_folder_id' ) )?$val[0]:'';
  // $current_api = get_ post_meta( get_the_ID(), 'drive_bridge_api_key' )[0]?:'';
  $current_api = get_option('drive_bridge_api_key')?:'';
  $current_folid = ( $val = get_post_meta( get_the_ID(), 'drive_bridge_folder_id' ) )?$val[0]:'';
  
  if( empty($current_api) || empty($current_folid) ){
	  $current_api = get_option('drive_bridge_api_key')?:'';
  	$current_folid = get_option('drive_bridge_folder_id')?:'';
    if( !empty($current_api) && !empty($current_folid) ) { 
      add_post_meta( get_the_ID(), 'drive_bridge_api_key', $current_api, true );
      add_post_meta( get_the_ID(), 'drive_bridge_folder_id', 	$current_folid, true );
    } else { ?>
      <P> <?php _e('Please complete plugin set up to use Drive Bridge Options.', 'Drive Bridge'); ?> </P>
    <?php }
  } else {
    if(isset($_GET['action']) && $_GET['action'] == 'edit' ) {
      add_filter('media_send_to_editor', 'media_to_editor', 10, 2);
      wp_nonce_field(basename(__FILE__), "meta-box-nonce");
      if(!empty($post_folder)){
        require plugin_dir_path( __FILE__ ) . 'templates-part/ysdrive-meta-box.php'; ?> 
        <script src="https://apis.google.com/js/api.js" type="text/javascript"></script>
        <input type="hidden" id="posts-folder-inner" value="<?php echo $post_folder; ?>" posts-id="<?php the_ID(); ?>" <?php if( empty($post_folder) ){ echo 'set-up="false"'; } ?> post-title="<?php the_title(); ?>" />
        <input type="hidden" id="api-key-inner" value="<?php echo $current_api; ?>" /> <input type="hidden" id="folder-id-inner" value="<?php echo $current_folid; ?>" />
      <?php } else { 
        require plugin_dir_path( __FILE__ ) . 'templates-part/connect-post.php'; ?> 
        <script src="https://apis.google.com/js/api.js" type="text/javascript"></script>
        <input type="hidden" id="posts-folder-inner"  posts-id="<?php echo get_the_ID(); ?>" post-title="<?php echo get_the_title(); ?>" />
        <input type="hidden" id="api-key-inner" value="<?php echo $current_api; ?>" /> <input type="hidden" id="folder-id-inner" value="<?php echo $current_folid; ?>" />
      <?php } ?>
    <?php } else { ?>
      <P> <?php _e('Create post to use Drive Bridge Options.', 'Drive Bridge'); ?> </P>
    <?php }     
  }
}

add_action('rest_api_init', function () {

  register_rest_route( 'drive-bridge', 'set-id',array(

      'methods'  => 'GET',
      'callback' => 'setPostFolderID',
      // 'permission_callback' => function() {
      //     return current_user_can('edit_posts');
      // }

  ));

  register_rest_route( 'drive-bridge', 'google-api',array(

    'methods'  => 'GET',
    'callback' => 'setGoogleConnect',
    // 'permission_callback' => function() {
    //     return current_user_can('edit_posts');
    // }

));

});

//rest api callback functions with validations
function setPostFolderID($request) {    
  $post_id = $request['post-id']?:false;
  $folder_id = $request['folder-id']?:false;
  $checklist_id = $request['checklist-id']?:false;
  if($post_id != false && $folder_id != false ) {
    delete_post_meta($post_id, 'posts_folder_id');
    add_post_meta( $post_id, 'posts_folder_id', $folder_id, true );
    $data['status'] = "200";
    $data['msg'] = 'Post id updated sucessfully!';
    $response = new WP_REST_Response($data);
    $response->set_status(200);
  } else if($post_id != false && $checklist_id != false ) {
    delete_post_meta($post_id, 'drive_bridge_checklist_id');
    add_post_meta( $post_id, 'drive_bridge_checklist_id', $checklist_id, true );
    $data['status'] = "200";
    $data['msg'] = 'Checklist id updated sucessfully!';
    $response = new WP_REST_Response($data);
    $response->set_status(200);
  } else {
    $data['status'] = "400";
    $data['msg'] = 'Not a Valid Request!';
    $response = new WP_REST_Response($data);
    $response->set_status(400);
  }  
  return $response;
}

function setGoogleConnect($request)
{
  print_r ($_POST);
  $data = array(
    "first_name" => "First name",
    "last_name" => "last name",
    "email"=>"email@gmail.com",
    "addresses" => array (
        "address1" => "some address",
        "city" => "city",
        "country" => "CA",
        "first_name" =>  "Mother",
        "last_name" =>  "Lastnameson",
        "phone" => "555-1212",
        "province" => "ON",
        "zip" => "123 ABC"
    )
  );
  $url = 'https://script.google.com/macros/s/AKfycbydxCYHMK-2OfASvnQ-MsU17oDCuxRMohsVWrf-H_h4Bv1I7uw80ELFkA/exec';
  $data_string = json_encode($data);
  $ch=curl_init($url);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, array("customer"=>$data_string));
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER,
      array(
          'Content-Type:application/json',
          'Content-Length: ' . strlen($data_string)
      )
  );

  $result = curl_exec($ch);
  curl_close($ch);
  
  // handle/format response; 
  
    $data['status'] = "400";
    $data['msg'] = 'Not a Valid Request!';
    $data['info'] = $result;
    $response = new WP_REST_Response($data);
    $response->set_status(400);  
  return $response;
}

//Ajax for checllist Page
add_action( 'wp_ajax_ysdriveUpdateCheckList', 'ysdriveUpdateCheckList' );
function ysdriveUpdateCheckList(){
  $listArr = array();
  $pattern = '/^item-[0-9]{1,3}$/i';
  foreach ( $_POST as $key => $value) {
    if(preg_match($pattern, $key)){
      $listArr[] = sanitize_text_field( $value );
    }
  }
  if( count($listArr) < 1 ) { echo "Please Provide the check list points."; exit(); }
  if( count($listArr) > 50 ) { echo "check list points limit is riched please remove some points."; exit(); }
  $option_name = 'ysdrive_check_list';
  $new_value = $listArr;
  if ( get_option( $option_name ) !== false ) { // The option already exists, so update it.
      update_option( $option_name, $new_value );
      echo "Check list points updated successfully.";
  } else {  // The option hasn't been created yet, so add it with $autoload set to 'no'.
      $deprecated = null;
      $autoload = 'no';
      add_option( $option_name, $new_value, $deprecated, $autoload );
      echo "Check list points added successfully.";
  }
  exit();
}

//add content Upload Button
function add_media_button() {
  global $pagenow;

  if ($pagenow == 'post.php'){
    global $typenow;
    if ( "ysdrive-projects" == $typenow){
      printf( '<a href="%s" class="button ysdrive-button ysdrive-custom-button" id="ysdrive-content">' . '<span class="wp-media-buttons-icon dashicons dashicons-cloud"></span><span> %s </span>' . '</a>', '#', __( 'Upload Content', 'textdomain' ) );
      echo '<span class="ysdrive-spinner" style="display:none;"><img src="'.get_home_url().'/wp-admin/images/spinner.gif" /></span>';
    } 
 }
 
}
add_action( 'media_buttons', 'add_media_button' );