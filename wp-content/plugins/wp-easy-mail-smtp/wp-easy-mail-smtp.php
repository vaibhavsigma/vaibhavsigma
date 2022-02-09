<?php
/*
Plugin Name: WP Easy Mail SMTP
Plugin URI: 
description: Make mail sending easy using SMTP in Wordpress
Version: 1.1
Author: Yudiz Solutions Pvt. Ltd.
Author URI: https://www.yudiz.com
License: 
*/
?>
<?php
define( 'WEMS_MAIL_SMTP', __FILE__ );
define( 'WEMS_MAIL_SMTP_DIR', plugin_dir_path( __FILE__ ) );
define( 'WEMS_MAIL_SMTP_URL', plugin_dir_url( WEMS_MAIL_SMTP_DIR ) . basename( dirname( __FILE__ ) ) . '/' );
define( 'WEMS_MAIL_SMTP_BASENAME', plugin_basename( WEMS_MAIL_SMTP ) );
require_once(WEMS_MAIL_SMTP_DIR.'init/easy-mail-smtp-functions.php');