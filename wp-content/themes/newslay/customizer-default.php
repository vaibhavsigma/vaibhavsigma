<?php
/**
 * Default theme options.
 *
 * @package Newslay
 */

if (!function_exists('newslay_get_default_theme_options')):

/**
 * Get default theme options
 *
 * @since 1.0.0
 *
 * @return array Default theme options.
 */
function newslay_get_default_theme_options() {

    $defaults = array();

    $defaults['weekly_post_section_title'] = __('Weekly Top', 'newslay');
    $defaults['select_weekly_news_category'] = 0;

	return $defaults;

}
endif;