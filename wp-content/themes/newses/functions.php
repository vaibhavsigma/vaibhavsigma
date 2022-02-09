<?php define( 'NEWSES_THEME_DIR', get_template_directory() . '/' );
	define( 'NEWSES_THEME_URI', get_template_directory_uri() . '/' );
	define( 'NEWSES_THEME_SETTINGS', 'newses-settings' );
	
	
	$newses_theme_path = get_template_directory() . '/inc/ansar/';

	require( $newses_theme_path . '/newses-custom-navwalker.php' );
	require( $newses_theme_path . '/default_menu_walker.php' );
	require( $newses_theme_path . '/font/font.php');
	require( $newses_theme_path . '/template-tags.php');
	require( $newses_theme_path . '/template-functions.php');
	require ( $newses_theme_path . '/custom-control/custom-control.php');
	require_once( trailingslashit( get_template_directory() ) . 'inc/ansar/customize-pro/class-customize.php' );

	$newses_theme_start = wp_get_theme();
	if ( 'Newses' == $newses_theme_start->name) {
	if ( is_admin() ) {
		require ($newses_theme_path . '/admin/getting-started.php');
	}
	}

	// Theme version.
	$newses_theme = wp_get_theme();
	define( 'NEWSES_THEME_VERSION', $newses_theme->get( 'Version' ) );
	define ( 'NEWSES_THEME_NAME', $newses_theme->get( 'Name' ) );

	/*
* Creating a function to create our CPT
*/
 
function custom_post_type() {
 
	// Set UI labels for Custom Post Type
		$labels = array(
			'name'                => _x( 'Movies', 'Post Type General Name', 'demo-pro' ),
			'singular_name'       => _x( 'Movie', 'Post Type Singular Name', 'demo-pro' ),
			'menu_name'           => __( 'Movies', 'demo-pro' ),
			'parent_item_colon'   => __( 'Parent Movie', 'demo-pro' ),
			'all_items'           => __( 'All Movies', 'demo-pro' ),
			'view_item'           => __( 'View Movie', 'demo-pro' ),
			'add_new_item'        => __( 'Add New Movie', 'demo-pro' ),
			'add_new'             => __( 'Add New', 'demo-pro' ),
			'edit_item'           => __( 'Edit Movie', 'demo-pro' ),
			'update_item'         => __( 'Update Movie', 'demo-pro' ),
			'search_items'        => __( 'Search Movie', 'demo-pro' ),
			'not_found'           => __( 'Not Found', 'demo-pro' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'demo-pro' ),
		);
		 
	// Set other options for Custom Post Type
		 
		$args = array(
			'label'               => __( 'movies', 'demo-pro' ),
			'description'         => __( 'Movie news and reviews', 'demo-pro' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			'taxonomies'          => array( 'genres' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/ 
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest' 		  => true,	 
		);
		 
		// Registering your Custom Post Type
		register_post_type( 'movies', $args );
		
		$labels = array(
			'name' => _x( 'Topics', 'taxonomy general name' ),
			'singular_name' => _x( 'Topic', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Topics' ),
			'popular_items' => __( 'Popular Topics' ),
			'all_items' => __( 'All Topics' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit Topic' ), 
			'update_item' => __( 'Update Topic' ),
			'add_new_item' => __( 'Add New Topic' ),
			'new_item_name' => __( 'New Topic Name' ),
			'separate_items_with_commas' => __( 'Separate topics with commas' ),
			'add_or_remove_items' => __( 'Add or remove topics' ),
			'choose_from_most_used' => __( 'Choose from the most used topics' ),
			'menu_name' => __( 'Topics' ),
		  ); 
		 
		// Now register the non-hierarchical taxonomy like tag		 
		  register_taxonomy('topics','movies',array(
			'hierarchical' => false,
			'labels' => $labels,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'topic' ),
		  ));	

		  // Set UI labels for Custom Post Type
		  $labels = array(
			'name'                => _x( 'Products 1', 'Post Type General Name', 'demo-pro' ),
			'singular_name'       => _x( 'Product 2', 'Post Type Singular Name', 'demo-pro' ),
			'menu_name'           => __( 'Products 3', 'demo-pro' ),
			'parent_item_colon'   => __( 'Parent Product 4', 'demo-pro' ),
			'all_items'           => __( 'All Products 5', 'demo-pro' ),
			'view_item'           => __( 'View Product 6', 'demo-pro' ),
			'add_new_item'        => __( 'Add New Product 7', 'demo-pro' ),
			'add_new'             => __( 'Add New 8', 'demo-pro' ),
			'edit_item'           => __( 'Edit Product 9', 'demo-pro' ),
			'update_item'         => __( 'Update Product 10', 'demo-pro' ),
			'search_items'        => __( 'Search Product 11', 'demo-pro' ),
			'not_found'           => __( 'Not Found 12', 'demo-pro' ),
			'not_found_in_trash'  => __( 'Not found in Trash 13', 'demo-pro' ),
		);
		 
	// Set other options for Custom Post Type
		 
		$args = array(
			'label'               => __( 'Products 14', 'demo-pro' ),
			'description'         => __( 'Products 15', 'demo-pro' ),
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			'taxonomies'          => array( 'product-cat' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/ 
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 30,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest' 		  => true,
	 
		);
		 
		// Registering your Custom Post Type
		register_post_type( 'products', $args );
	
		$labels = array(
			'name' => _x( 'categories', 'taxonomy general name' ),
			'singular_name' => _x( 'category', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search categories' ),
			'popular_items' => __( 'Popular categories' ),
			'all_items' => __( 'All categories' ),
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => __( 'Edit category' ), 
			'update_item' => __( 'Update category' ),
			'add_new_item' => __( 'Add New category' ),
			'new_item_name' => __( 'New category Name' ),
			'separate_items_with_commas' => __( 'Separate categories with commas' ),
			'add_or_remove_items' => __( 'Add or remove categories' ),
			'choose_from_most_used' => __( 'Choose from the most used categories' ),
			'menu_name' => __( 'categories' ),
		  ); 
		 
		// Now register the non-hierarchical taxonomy like tag		 
		  register_taxonomy('product-cat','products',array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'show_in_rest' => true,
			'show_admin_column' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'product-cat' ),
		  ));	
	}
	 
	/* Hook into the 'init' action so that the function
	* Containing our post type registration is not 
	* unnecessarily executed. 
	*/
	 
	add_action( 'init', 'custom_post_type', 0 );
	/*-----------------------------------------------------------------------------------*/
	/*	Enqueue scripts and styles.
	/*-----------------------------------------------------------------------------------*/
	require( $newses_theme_path .'/enqueue.php');
	/* ----------------------------------------------------------------------------------- */
	/* Customizer */
	/* ----------------------------------------------------------------------------------- */
	require( $newses_theme_path . '/customize/customizer.php');

	/* ----------------------------------------------------------------------------------- */
	/* Customizer */
	/* ----------------------------------------------------------------------------------- */

	require( $newses_theme_path  . '/hooks/hooks-init.php');
	
	require_once( trailingslashit( get_template_directory() ) . 'inc/ansar/customize-pro/class-customize.php' );

if ( ! function_exists( 'newses_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function newses_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on newses, use a find and replace
	 * to change 'newses' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'newses', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/* Add theme support for gutenberg block */
	add_theme_support( 'align-wide' );

	// Add theme support for Responsive Videos.
	add_theme_support( 'jetpack-responsive-videos' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary menu', 'newses' ),
        'footer' => __( 'Footer menu', 'newses' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'newses_custom_background_args', array(
		'default-color' => '#eee',
		'default-image' => '',
	) ) );

    // Set up the woocommerce feature.
    add_theme_support( 'woocommerce');

     // Woocommerce Gallery Support
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

    // Added theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );
	
	//Custom logo
	add_theme_support(
    'custom-logo',
    array(
        'unlink-homepage-logo' => true, // Add Here!
    	)
	);
	
	// custom header Support
			$args = array(
			'default-image'	=>  get_template_directory_uri() .'/images/head-back.jpg',
			'width'			=> '1600',
			'height'		=> '600',
			'flex-height'		=> false,
			'flex-width'		=> false,
			'header-text'		=> true,
			'default-text-color'	=> 'fff',
            'wp-head-callback' => 'newses_site_info_style',

		);
		add_theme_support( 'custom-header', $args );
	

}
endif;
add_action( 'after_setup_theme', 'newses_setup' );


	function newses_the_custom_logo() {
	
		if ( function_exists( 'the_custom_logo' ) ) {
			the_custom_logo();
		}

	}

	add_filter('get_custom_logo','newses_logo_class');


	function newses_logo_class($html)
	{
	$html = str_replace('custom-logo-link', 'navbar-brand', $html);
	return $html;
	}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function newses_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'newses_content_width', 640 );
}
add_action( 'after_setup_theme', 'newses_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function newses_widgets_init() {
	
	$newses_footer_column_layout = esc_attr(get_theme_mod('newses_footer_column_layout',3));
	
	$newses_footer_column_layout = 12 / $newses_footer_column_layout;
	
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Widget Area', 'newses' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="mg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="mg-wid-title"><h6><span class="bg">',
		'after_title'   => '</span></h6></div>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Front-page Content Section', 'newses'),
		'id'            => 'front-page-content',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="newses-front-page-content-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h6>',
		'after_title'   => '</h6>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Front-page Sidebar Section', 'newses'),
		'id'            => 'front-page-sidebar',
		'description'   => '',
		'before_widget' => '<div id="%1$s" class="mg-widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="mg-wid-title"><h6><span class="bg">',
		'after_title'   => '</span></h6></div>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer Widget Area', 'newses' ),
		'id'            => 'footer_widget_area',
		'description'   => '',
		'before_widget' => '<div class="col-md-'.$newses_footer_column_layout.' col-sm-6"><div id="%1$s" class="mg-widget %2$s">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<h6>',
		'after_title'   => '</h6>',
	) );

}
add_action( 'widgets_init', 'newses_widgets_init' );

//Editor Styling 
add_editor_style( array( 'css/editor-style.css') );


add_filter('wp_nav_menu_items', 'newses_add_home_link', 1, 2);
function newses_add_home_link($items, $args){
    if( $args->theme_location == 'primary' ){
        $item = '<li class="active home"><a class="nav-link homebtn" title="Home" href="'. esc_url( home_url() ) .'">' . "<span class='fa fa-home'></span>" . '</a></li>';
        $items = $item . $items;
    }
    return $items;
}

if ( ! function_exists( 'wp_body_open' ) ) {

	/**
	 * Shim for wp_body_open, ensuring backward compatibility with versions of WordPress older than 5.2.
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
}

add_filter('ys_cfdbh_before_save_data', 'add_user_agent_details', 4, 1);
function add_user_agent_details($classes){
global
    $is_iphone, // iPhone Safari
    $is_chrome, // Google Chrome
    $is_safari, // Safari
    $is_NS4, // Netscape 4
    $is_opera, // Opera
    $is_macIE, // Mac Internet Explorer
    $is_winIE, // Windows Internet Explorer
    $is_gecko, // FireFox
    $is_lynx, // Lynx (web browser)
    $is_IE, // Internet Explorer
    $is_edge, // Microsoft Edge
    $is_apache, // Apache HTTP Server
    $is_IIS, // Microsoft Internet Information Services (IIS)
    $is_iis7, // Microsoft Internet Information Services (IIS) v7.x
    $is_nginx; // Nginx web server

    // Browser
    if($is_chrome) $classes['Browser'] = 'chrome';  
    elseif($is_safari) $classes['Browser'] = 'safari';
    elseif($is_NS4) $classes['Browser'] = 'netscape';
    elseif($is_opera) $classes['Browser'] = 'opera';
    elseif (strstr($httpAgent, 'opera mini')) $classes['Browser'] = 'opera-mini';
    elseif (strstr($httpAgent, 'opera mobi')) $classes['Browser'] = 'opera-mobi';
    elseif($is_macIE) $classes['Browser'] = 'mac-ie';
    elseif($is_winIE) $classes['Browser'] = 'win-ie';
    elseif($is_gecko) $classes['Browser'] = 'firefox';
    elseif($is_IE) $classes['Browser'] = 'ie';
    elseif($is_edge) $classes['Browser'] = 'microsoft-edge';
    elseif($is_lynx) $classes['Browser'] = 'lynx-browser';
	
	$u_agent = $_SERVER['HTTP_USER_AGENT'];
	// Device
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os_platform    = "Unknown OS Platform";
    $os_array       = array('/windows phone 8/i'    =>  'Windows Phone 8',
                            '/windows phone os 7/i' =>  'Windows Phone 7',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile');
    $found = false;
    $device = '';
    foreach ($os_array as $regex => $value) 
    { 
        if($found)
         break;
        else if (preg_match($regex, $user_agent)) 
        {
            $os_platform    =   $value;
            $device = !preg_match('/(windows|mac|linux|ubuntu)/i',$os_platform)
                      ?'MOBILE':(preg_match('/phone/i', $os_platform)?'MOBILE':'SYSTEM');
        }
    }

	// (D) MANY OTHERS...
	if( is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "windows")) ){ $classes['Device'] = 'windows'; }
	elseif( is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "android")) ){ $classes['Device'] = 'android'; }
	elseif( is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "mobile")) ){ $classes['Device'] = 'mobile'; }
	elseif( is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "iphone")) ){ $classes['Device'] = 'iphone'; }
	elseif( is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "ipad")) ){ $classes['Device'] = 'ipad'; }
	elseif( is_numeric(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), "tablet")) ){ $classes['Device'] = 'tablet'; }
	else{ $classes['Device'] = 'Desktop'; }

	
	//OS
	$classes['OS'] = $os_platform;

	if (getenv('HTTP_CLIENT_IP'))
        $classes['IP'] = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $classes['IP'] = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $classes['IP'] = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $classes['IP'] = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $classes['IP'] = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $classes['IP'] = getenv('REMOTE_ADDR');
    else
        $classes['IP'] = 'UNKNOWN';
	
	
	return $classes;
}

add_action('wpcf7_before_send_mail', 'save_form' );
function save_form( $wpcf7 ) {
	$data = array();
	$data = add_user_agent_details($data);
    $mail = $wpcf7->prop('mail');
	$body = '[us-browser]';	$mail['body'] = str_replace($body, $data['Browser'], $mail['body']);
	$body = '[us-os]';	$mail['body'] = str_replace($body, $data['OS'], $mail['body']);
	$body = '[us-device]';	$mail['body'] = str_replace($body, $data['Device'], $mail['body']);
	$body = '[us-ip]';	$mail['body'] = str_replace($body, $data['IP'], $mail['body']);
	// Save the email body
    $wpcf7->set_properties(array("mail" => $mail,));
    return $wpcf7;
}
