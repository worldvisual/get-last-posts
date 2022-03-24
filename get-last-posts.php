<?php

/**
 * Plugin Name: Retorna Últimos Posts
 * Plugin URI: https://github.com/worldvisual/get-last-posts
 * Description: WordPress plugin to return the latest posts from another WP blog.
 * Version: 1.0.0
 * Author: Samuel Almeida
 * Author URI: https://github.com/worldvisual
 */

/**
 * Shortcode example
 * [get_last_posts quantity="3" url="https://example.com"]
 */

function get_post_styles()
{
	wp_register_style('foo-styles',  plugin_dir_url(__FILE__) . 'css/custom.css');
	wp_enqueue_style('foo-styles');
}
add_action('wp_enqueue_scripts', 'get_post_styles');

// Add Shortcode
function get_last_posts($atts)
{

	// Attributes
	$atts = shortcode_atts(
		array(
			'quantity' => '',
			'url' => '',
		),
		$atts
	);

	$url = $atts['url'] . "/wp-json/wp/v2/posts?per_page=" . $atts['quantity'] . "&_embed";

	$options  = array(
		'http' =>
		array(
			'ignore_errors' => true,
		),
		"ssl" => array(
			"verify_peer" => false,
			"verify_peer_name" => false,
		),
	);
	$context  = stream_context_create($options);
	$response = file_get_contents($url, false, $context);
	$response = json_decode($response, true);


	$html = '<div class="get_last_posts">';

	foreach ($response as $value) {

		$html .= '

		<div class="get_last_post">
		<div class="get_last_image">
		<a href="' . $value['link'] . '" target="_blank">
		<span>
		<img src="' . $value['_embedded']['wp:featuredmedia'][0]['source_url'] . '" alt="" title="' . $value['title']['rendered'] . '" height="auto" width="auto">
		</span>
		</a>
		</div>
		<div class="get_last_title">
		<a href="' . $value['link'] . '" target="_blank">
		<h2>' . $value['title']['rendered'] . '</h2>
		</a>
		</div>
		</div>';
	}

	$html .= '</div>';

	return $html;
}
add_shortcode('get_last_posts', 'get_last_posts');