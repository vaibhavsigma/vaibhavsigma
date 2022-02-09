<?php
/**
 * Theme functions and definitions
 *
 * @package Newslay
 */
if ( ! function_exists( 'newslay_enqueue_styles' ) ) :
	/**
	 * @since 0.1
	 */
	function newslay_enqueue_styles() {
		wp_enqueue_style( 'newsup-style-parent', get_template_directory_uri() . '/style.css' );
		wp_enqueue_style( 'newslay-style', get_stylesheet_directory_uri() . '/style.css', array( 'newsup-style-parent' ), '1.0' );
		wp_enqueue_style('bootstrap', get_template_directory_uri() . '/css/bootstrap.css');
		wp_dequeue_style( 'newsup-default',get_template_directory_uri() .'/css/colors/default.css');
		wp_enqueue_style( 'newslay-default-css', get_stylesheet_directory_uri()."/css/colors/default.css" );
		if(is_rtl()){
		wp_enqueue_style( 'newsup_style_rtl', trailingslashit( get_template_directory_uri() ) . 'style-rtl.css' );
	    }
		
	}

endif;
add_action( 'wp_enqueue_scripts', 'newslay_enqueue_styles', 9999 );

function newslay_theme_setup() {

//Load text domain for translation-ready
load_theme_textdomain('newslay', get_stylesheet_directory() . '/languages');

require( get_stylesheet_directory() . '/hooks/hooks.php' );
require( get_stylesheet_directory() . '/customizer-default.php' );
require( get_stylesheet_directory() . '/frontpage-options.php' );
require( get_stylesheet_directory() . '/hooks/hook-header-section.php' );


// custom header Support
			$args = array(
			'default-image'		=>  get_stylesheet_directory_uri() .'/images/head-back.jpg',
			'width'			=> '1600',
			'height'		=> '600',
			'flex-height'		=> false,
			'flex-width'		=> false,
			'header-text'		=> true,
			'default-text-color'	=> '#143745'
		);
		add_theme_support( 'custom-header', $args );
} 
add_action( 'after_setup_theme', 'newslay_theme_setup' );


/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function newslay_partial_customize_register($wp_customize) {

	if (isset($wp_customize->selective_refresh)) {	
		$wp_customize->selective_refresh->add_partial('weekly_post_section_title', array(
				'selector'        => '.trending-area .title h4',
				'render_callback' => 'newsup_customize_partial_weekly_post_section_title',
		));

		$wp_customize->selective_refresh->add_partial('select_weekly_news_category', array(
				'selector'        => '.small-list-post',
				'render_callback' => 'newsup_customize_partial_select_weekly_news_category',
		));
	}

    $default = newsup_get_default_theme_options();

    $selective_refresh = isset( $wp_customize->selective_refresh ) ? 'postMessage' : 'refresh';
}
add_action('customize_register', 'newslay_partial_customize_register');

function newslay_customize_partial_weekly_post_section_title() {
	return get_theme_mod( 'weekly_post_section_title' );
}

function newslay_customize_partial_select_weekly_news_category() {
	return get_theme_mod( 'select_weekly_news_category' );
}