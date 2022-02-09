<?php if(!function_exists('newslay_frontpage_trending_post_section')):

/**
*
* @since Newslay
*
*
*/
  function newslay_frontpage_trending_post_section()
    {

        if(is_front_page() || is_home())
        { 
         $number_of_posts = '4';
         $newsup_slider_category = newsup_get_option('select_recent_news_category');
         $newsup_all_posts_main = newsup_get_posts($number_of_posts, $newsup_slider_category);
        ?>

        <div class="col-md-3">
            <div class="trending-area mg-posts-sec-inner row">
                <div class="title mx-3">
                    <!-- mg-sec-title -->
                    <?php 
                    	$weekly_post_section_title = get_theme_mod('weekly_post_section_title', esc_html('Weekly Post','newslay'));
                    ?>
                    <h4><?php echo esc_html($weekly_post_section_title); ?></h4>
                </div>
                <!-- small-list-post -->
                <div class="small-list-post col-lg-12">
                    <ul>
                        <?php if ($newsup_all_posts_main->have_posts()) :
                        while ($newsup_all_posts_main->have_posts()) : $newsup_all_posts_main->the_post();
                        global $post;
                        $newsup_url = newsup_get_freatured_image_url($post->ID, 'newsup-slider-full'); ?>
                        <li class="small-post clearfix">
                            <div class="img-small-post">
                                <img src="<?php echo esc_url($newsup_url); ?>">
                            </div>
                            <div class="small-post-content">
                                <div class="mg-blog-category"> <?php newsup_post_categories(); ?> </div>
                                <div class="title_small_post">
                                    <h5> <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                                </div>
                            </div>
                        </li>
                        <?php
    endwhile;
endif;
wp_reset_postdata();
?>
                        
                    </ul>
                </div>
                <!-- // small-list-post -->
            </div>
        </div>
       <?php }
    }

endif;

add_action('newslay_action_front_page_trending_section', 'newslay_frontpage_trending_post_section', 30);

//Front Page Banner
if (!function_exists('newslay_front_page_banner_section')) :
    /**
     *
     * @since Newsbulk
     *
     */
    function newslay_front_page_banner_section()
    {
        if (is_front_page() || is_home()) {
        $newsup_enable_main_slider = newsup_get_option('show_main_news_section');
        $select_vertical_slider_news_category = newsup_get_option('select_vertical_slider_news_category');
        $vertical_slider_number_of_slides = newsup_get_option('vertical_slider_number_of_slides');
        $all_posts_vertical = newsup_get_posts($vertical_slider_number_of_slides, $select_vertical_slider_news_category);
        if ($newsup_enable_main_slider):  

            $main_banner_section_background_image = newsup_get_option('main_banner_section_background_image');
            $main_banner_section_background_image_url = wp_get_attachment_image_src($main_banner_section_background_image, 'full');
        if(!empty($main_banner_section_background_image)){ ?>
             <section class="mg-fea-area over" style="background-image:url('<?php echo esc_url($main_banner_section_background_image_url[0]); ?>');">
        <?php }else{ ?>
            <section class="mg-fea-area">
        <?php  } ?>
            <div class="overlay">
                <div class="container-fluid">
                    <div class="row">
                        <?php do_action('newslay_action_front_page_trending_section');?>
                        <div class="col-md-6 col-sm-6">
                            <div id="homemain"class="homemain owl-carousel mr-bot60 pd-r-10"> 
                                <?php newsup_get_block('list', 'banner'); ?>
                            </div>
                        </div> 
                        <?php do_action('newslay_action_banner_tabbed_posts');?>
                    </div>
                </div>
            </div>
        </section>
        <!--==/ Home Slider ==-->
        <?php endif; ?>
        <!-- end slider-section -->
        <?php }
    }
endif;
add_action('newslay_action_front_page_main_section_1', 'newslay_front_page_banner_section', 40);



//Banner Tabed Section
if (!function_exists('newslay_banner_tabbed_posts')):
    /**
     *
     * @since Newslay 1.0.0
     *
     */
    function newslay_banner_tabbed_posts()
    {
        
            $show_excerpt = 'false';
            $excerpt_length = '20';
            $number_of_posts = '4';

            $enable_categorised_tab = 'true';
            $latest_title = newsup_get_option('latest_tab_title');
            $popular_title = newsup_get_option('popular_tab_title');
            $categorised_title = newsup_get_option('trending_tab_title');
            $category = newsup_get_option('select_trending_tab_news_category');
            $tab_id = 'tan-main-banner-latest-trending-popular'
            ?>
            <div class="col-md-3 col-sm-6 top-right-area">
                    <div id="exTab2" >
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#<?php echo esc_attr($tab_id); ?>-recent"
                               aria-controls="<?php esc_attr_e('Recent', 'newslay'); ?>">
                               <i class="fa fa-clock-o"></i><?php echo esc_html($latest_title); ?>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#<?php echo esc_attr($tab_id); ?>-popular"
                               aria-controls="<?php esc_attr_e('Popular', 'newslay'); ?>">
                                <i class="fa fa-fire"></i> <?php echo esc_html($popular_title); ?>
                            </a>
                        </li>


                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#<?php echo esc_attr($tab_id); ?>-categorised"
                               aria-controls="<?php esc_attr_e('Categorised', 'newslay'); ?>">
                                <i class="fa fa-bolt"></i> <?php echo esc_html($categorised_title); ?>
                            </a>
                        </li>

                    </ul>
                <div class="tab-content">
                    <div id="<?php echo esc_attr($tab_id); ?>-recent" role="tabpanel" class="tab-pane active">
                        <?php
                        newsup_render_posts('recent', $show_excerpt, $excerpt_length, $number_of_posts);
                        ?>
                    </div>


                    <div id="<?php echo esc_attr($tab_id); ?>-popular" role="tabpanel" class="tab-pane">
                        <?php
                        newsup_render_posts('popular', $show_excerpt, $excerpt_length, $number_of_posts);
                        ?>
                    </div>

                    <?php if ($enable_categorised_tab == 'true'): ?>
                        <div id="<?php echo esc_attr($tab_id); ?>-categorised" role="tabpanel" class="tab-pane ">
                            <?php
                            newsup_render_posts('categorised', $show_excerpt, $excerpt_length, $number_of_posts, $category);
                            ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        <?php

    }
endif;

add_action('newslay_action_banner_tabbed_posts', 'newslay_banner_tabbed_posts', 10);