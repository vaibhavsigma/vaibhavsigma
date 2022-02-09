<?php

/**
 * Option Panel
 *
 * @package Newslay
 */


function newslay_customize_register($wp_customize) {

$newsup_default = newslay_get_default_theme_options();


//section title
$wp_customize->add_setting('weekly_post_section',
    array(
        'sanitize_callback' => 'sanitize_text_field',
    )
);

$wp_customize->add_control(
    new newsup_Section_Title(
        $wp_customize,
        'weekly_post_section',
        array(
            'label'             => esc_html__( 'Weekly Top Section', 'newslay' ),
            'section'           => 'frontpage_main_banner_section_settings',
            'priority'          => 40,
            'active_callback' => 'newsup_main_banner_section_status'
        )
    )
);


// Setting - Weekly top title settings.
$wp_customize->add_setting('weekly_post_section_title',
    array(
        'default' => $newsup_default['weekly_post_section_title'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'sanitize_text_field',
    )
);

$wp_customize->add_control('weekly_post_section_title',
    array(
        'label' => esc_html__('Section Title', 'newslay'),
        'section' => 'frontpage_main_banner_section_settings',
        'type' => 'text',
        'priority' => 50,

    )
);



// Setting - drop down category for slider.
$wp_customize->add_setting('select_weekly_news_category',
    array(
        'default' => $newsup_default['select_weekly_news_category'],
        'capability' => 'edit_theme_options',
        'sanitize_callback' => 'absint',
    )
);


$wp_customize->add_control(new Newsup_Dropdown_Taxonomies_Control($wp_customize, 'select_weekly_news_category',
    array(
        'label' => esc_html__('Category', 'newslay'),
        'description' => esc_html__('Select category for top weekly 4 Post', 'newslay'),
        'section' => 'frontpage_main_banner_section_settings',
        'type' => 'dropdown-taxonomies',
        'taxonomy' => 'category',
        'priority' => 60,
        'active_callback' => 'newsup_main_banner_section_status'
    )));
}
add_action('customize_register', 'newslay_customize_register');
