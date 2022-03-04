<?php
define( 'PRIME_RATES_LEADCHECK_LOAN_APPLICATIONS_PLUGIN_URL', PRIME_RATES_LOAN_APPLICATIONS_PLUGIN_URL.'pr-leadcheck-offers-recall'.DIRECTORY_SEPARATOR);
define( 'PRIME_RATES_LEADCHECK_LOAN_APPLICATIONS_CSS_URL', PRIME_RATES_LEADCHECK_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR );
define( 'PRIME_RATES_LEADCHECK_LOAN_APPLICATIONS_JS_URL', PRIME_RATES_LEADCHECK_LOAN_APPLICATIONS_PLUGIN_URL.'static'.DIRECTORY_SEPARATOR.'js'.DIRECTORY_SEPARATOR );

function prime_rates_leadcheck_loan_applications_enqueue() {
	global $post;
	if(has_shortcode( $post->post_content, 'apply-for-loans')  && (strpos($_SERVER['REQUEST_URI'],'personal-loans/access-offers') !== false || strpos($_SERVER['REQUEST_URI'],'access-offers') !== false || @$_GET['leadCheckID'] != '')){
	  wp_enqueue_script('prime_rates_loan_applications_leadcheck', PRIME_RATES_LEADCHECK_LOAN_APPLICATIONS_JS_URL.'main.5198abe0.js', array(), 0,false );
	  if(strpos($_SERVER["HTTP_HOST"],"primerates.com") !== false){
		wp_enqueue_style('prime_rates_leadcheck_css',get_stylesheet_directory_uri().'/css/LeadCheck/leadcheck-primerates.css');
		wp_enqueue_style('prime_rates_leadcheck_transition_css',get_stylesheet_directory_uri().'/css/LeadCheck/transition-primerates.css');
	  }
	  if(strpos($_SERVER["HTTP_HOST"],"apply.headwaysales.com") !== false || strpos($_SERVER["HTTP_HOST"],"pr.acornfinance.com") !== false || strpos($_SERVER["HTTP_HOST"],"mykukun.com") !== false || strpos($_SERVER["HTTP_HOST"],"codepen.io") !== false || strpos($_SERVER["HTTP_HOST"],"retainfinance.com") !== false){
		wp_enqueue_style('headway_leadcheck_css',get_stylesheet_directory_uri().'/css/LeadCheck/leadcheck-headway.css');
		wp_enqueue_style('headway_leadcheck_transition_css',get_stylesheet_directory_uri().'/css/LeadCheck/transition-headway.css');
	  }
	  if(strpos($_SERVER["HTTP_HOST"],"loans.zalea.com") !== false){
		wp_enqueue_style('zalea_leadcheck_css',get_stylesheet_directory_uri().'/css/LeadCheck/leadcheck-zalea.css');
		wp_enqueue_style('zalea_leadcheck_transition_css',get_stylesheet_directory_uri().'/css/LeadCheck/transition-zalea.css');
	  }
	  
	  // Bank specific css loading
		if(isset($versionConfigData['domains']) && !empty($versionConfigData['domains'])){
			foreach($versionConfigData['domains'] as $host){
				if(isset($host['hosts']) && is_array($host['hosts'])){
					foreach($host['hosts'] as $host_url){
						if($_SERVER['HTTP_HOST'] == $host_url['host']){
							wp_enqueue_style($host['slug'].'_breadcrumb_css',get_stylesheet_directory_uri().'/css/Breadcrumb/'.$host['breadcrumb_css']);
							wp_enqueue_style($host['slug'].'_breadcrumb_transition_css',get_stylesheet_directory_uri().'/css/'.$host['breadcrumb_transition_css']);
						}
					}
				}
			}
		}
	}
}


add_action( 'wp_enqueue_scripts', 'prime_rates_leadcheck_loan_applications_enqueue', 50);
