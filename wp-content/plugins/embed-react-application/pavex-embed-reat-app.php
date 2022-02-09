<?php
/**
	Plugin Name: Embed React app
	Description: Embedding a React Application to WordPress post
	Version: 1.0.1
	Plugin URI: https://www.publicalbum.org/blog/embedding-react-app-wordpress-post
	Author: pavex@ines.cz
	Author URI: https://www.publicalbum.org/
	License: GPLv2
	Text Domain: pavex-embed-react-app
*/


class Pavex_embed_react_app {


	public function __construct()
	{
		add_shortcode('reactapp', array($this, 'shortcode'));
	}





	public function shortcode($attrs)
	{
		if (count($attrs) < 2) {
			return NULL;
		}
		foreach ($attrs as $key => $attr) {
			if ($key === 'id') {
				$id = $attr;
			}
			elseif (preg_match('/\.js$/i', $attr)) {
				wp_enqueue_script("react-app-$key", $attr);
			}
			elseif (preg_match('/\.css$/i', $attr)) {
				wp_enqueue_style("react-app-$key", $attr);
			}
		}
		return sprintf("<div id=\"%s\" class=\"reactapp\"></div>", isset($id) ? $id : 'root');
	}


}


add_action('init', function() {
	new Pavex_embed_react_app();
});
