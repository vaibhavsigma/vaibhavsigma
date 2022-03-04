<?php
define( 'PRIME_RATES_AFFILIATE_LOAN_APPLICATIONS_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'affiliate-app'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_AFFILIATE_LOAN_APPLICATIONS_CSS_URL', PRIME_RATES_AFFILIATE_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_AFFILIATE_LOAN_APPLICATIONS_JS_URL', PRIME_RATES_AFFILIATE_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );


function prime_rates_affiliate_loan_applications_enqueue() {
	global $post;
	if(has_shortcode( $post->post_content, 'apply-for-loans') && ($_GET['affiliateid'] == 'intercom' || $_GET['affiliateid'] == '426464' || $_GET['affiliateid'] == 'CreditSoup' || $_POST['affiliateid'] == 'intercom' || $_POST['affiliateid'] == '426464' || $_POST['affiliateid'] == 'CreditSoup')){
	  wp_enqueue_script('prime_rates_loan_applications', PRIME_RATES_AFFILIATE_LOAN_APPLICATIONS_JS_URL.'main.0517963f.js', array(), 0,false );
	  wp_enqueue_style('prime_rates_loan_applications_affiliate_css',get_stylesheet_directory_uri().'/css/AffiliateApp/affiliateapp-primerate.css');
	  wp_enqueue_style('prime_rates_loan_applications_transition_css',get_stylesheet_directory_uri().'/css/transition-primerates.css');
	} 
}

add_action( 'wp_enqueue_scripts', 'prime_rates_affiliate_loan_applications_enqueue' );
