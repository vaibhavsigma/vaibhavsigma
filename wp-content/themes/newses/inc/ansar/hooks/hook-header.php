<?php
if (!function_exists('newses_header_section')) :
/**
 *  Slider
 *
 * @since newses
 *
 */
function newses_header_section()
{
?>
<div class="mg-head-detail d-none d-md-block">
      <div class="container">
        <div class="row align-items-center">
            <?php
            $header_social_icon_enable = esc_attr(get_theme_mod('header_social_icon_enable','true'));
            $newses_header_fb_link = get_theme_mod('newses_header_fb_link');
            $newses_header_fb_target = esc_attr(get_theme_mod('newses_header_fb_target','true'));
            $newses_header_twt_link = get_theme_mod('newses_header_twt_link');
            $newses_header_twt_target = esc_attr(get_theme_mod('newses_header_twt_target','true'));
            $newses_header_lnkd_link = get_theme_mod('newses_header_lnkd_link');
            $newses_header_lnkd_target = esc_attr(get_theme_mod('newses_header_lnkd_target','true'));
            $newses_header_insta_link = get_theme_mod('newses_header_insta_link');
            $newses_insta_insta_target = esc_attr(get_theme_mod('newses_insta_insta_target','true'));
            $newses_header_youtube_link = get_theme_mod('newses_header_youtube_link');
            $newses_header_youtube_target = esc_attr(get_theme_mod('newses_header_youtube_target','true'));
            $newses_header_pintrest_link = get_theme_mod('newses_header_pintrest_link');
            $newses_header_pintrest_target = esc_attr(get_theme_mod('newses_header_pintrest_target','true'));  
            $newses_header_telegram_link = get_theme_mod('newses_header_telegram_link');
            $newses_header_telegram_target = esc_attr(get_theme_mod('newses_header_telegram_target','true'));
            ?>
            <div class="col-md-6 col-xs-12 col-sm-6">
                <ul class="info-left">
                    <?php newses_date_display_type(); ?>
                </ul>
            </div>
            <?php 
            if($header_social_icon_enable == true)
            {
            ?>
            <div class="col-md-6 col-xs-12">
                <ul class="mg-social info-right">
                    
                      <?php if($newses_header_fb_link !=''){?>
                      <li><span class="icon-soci facebook"><a <?php if($newses_header_fb_target) { ?> target="_blank" <?php } ?>href="<?php echo esc_url($newses_header_fb_link); ?>"><i class="fa fa-facebook"></i></a></span> </li>
                      <?php } if($newses_header_twt_link !=''){ ?>
                      <li><span class="icon-soci twitter"><a <?php if($newses_header_twt_target) { ?>target="_blank" <?php } ?>href="<?php echo esc_url($newses_header_twt_link);?>"><i class="fa fa-twitter"></i></a></span></li>
                      <?php } if($newses_header_lnkd_link !=''){ ?>
                      <li><span class="icon-soci linkedin"><a <?php if($newses_header_lnkd_target) { ?>target="_blank" <?php } ?> href="<?php echo esc_url($newses_header_lnkd_link); ?>"><i class="fa fa-linkedin"></i></a></span></li>
                      <?php } 
                      if($newses_header_insta_link !=''){ ?>
                      <li><span class="icon-soci instagram"><a <?php if($newses_insta_insta_target) { ?>target="_blank" <?php } ?> href="<?php echo esc_url($newses_header_insta_link); ?>"><i class="fa fa-instagram"></i></a></span></li>
                      <?php }
                      if($newses_header_youtube_link !=''){ ?>
                      <li><span class="icon-soci youtube"><a <?php if($newses_header_youtube_target) { ?>target="_blank" <?php } ?> href="<?php echo esc_url($newses_header_youtube_link); ?>"><i class="fa fa-youtube"></i></a></span></li>
                      <?php }  if($newses_header_pintrest_link !=''){ ?>
                      <li><span class="icon-soci pinterest"><a <?php if($newses_header_pintrest_target) { ?>target="_blank" <?php } ?> href="<?php echo esc_url($newses_header_pintrest_link); ?>"><i class="fa fa-pinterest-p"></i></a></span></li>
                      <?php }  if($newses_header_telegram_link !=''){ ?>
                      <li><span class="icon-soci telegram"><a <?php if($newses_header_telegram_target) { ?>target="_blank" <?php } ?> href="<?php echo esc_url($newses_header_telegram_link); ?>"><i class="fa fa-telegram"></i></a></span></li>
                      <?php } ?>
                </ul>
            </div>
            <?php }?>
        </div>
    </div>
</div>
<?php 
}
endif;
add_action('newses_action_header_section', 'newses_header_section', 5);