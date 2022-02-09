<?php
/**
 * Plugin Name: my-react-app
 * Plugin URI: a url
 * Description: A react application
 * Version: 0.1
 * Text Domain: my-react-app
 * Author: Julien Bras
 * Author URI: https://sidoine.org
 */

// First register resources with init 
function my_react_app_init() {
    $path = "/frontend/build/static";
    if(getenv('WP_ENV')=="development") {
        $path = "/frontend/build/static";
    }
    wp_register_script("my_react_app_js", plugins_url($path."/js/main.js", __FILE__), array(), "1.0", false);
    wp_register_style("my_react_app_css", plugins_url($path."/css/main.css", __FILE__), array(), "1.0", "all");
}

add_action( 'init', 'my_react_app_init' );

// Function for the short code that call React app
function my_react_app() {
    wp_enqueue_script("my_react_app_js", '1.0', true);
    wp_enqueue_style("my_react_app_css");
    return "<div id=\"my_react_app\"></div>";
}

add_shortcode('my_react_app', 'my_react_app');
